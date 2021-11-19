<?php

namespace App\Imports;

use App\Models\BatchJob;
use App\Models\LongTermStorageFees;
use App\Services\ImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class QueueLongTermStorageFees implements ToModel, WithHeadingRow, ShouldQueue, WithChunkReading, WithBatchInserts, WithCalculatedFormulas, WithEvents, WithValidation
{
    use Importable, RegistersEventListeners, RemembersRowNumber;

    public $rows = 0;
    private $userID;
    private $batchID;
    private $inputReportDate;

    public function __construct(
        $userID,
        $batchID,
        $inputReportDate
    ) {
        $this->userID = $userID;
        $this->batchID = $batchID;
        $this->inputReportDate = $inputReportDate;
    }

    public function model(array $row)
    {
        ++$this->rows;

        return new LongTermStorageFees([
            'account' => $row['account'],
            'snapshot_date' => $row['snapshot_date'],
            'sku' => $row['sku'],
            'fnsku' => $row['fnsku'],
            'asin' => $row['asin'],
            'product_name' => $row['product_name'],
            'supplier_type' => $row['supplier_type'],
            'supplier' => $row['supplier'],
            'condition' => $row['condition'],
            'qty_charged_12_mo_long_term_storage_fee' => $row['qty_charged_12_mo_long_term_storage_fee'],
            'per_unit_volume' => $row['per_unit_volume'],
            'currency' => $row['currency'],
            '12_mo_long_terms_storage_fee' => $row['12_mo_long_terms_storage_fee'],
            'hkd' => $row['hkd'],
            'hkd_rate' => $row['hkd_rate'],
            'qty_charged_6_mo_long_term_storage_fee' => $row['qty_charged_6_mo_long_term_storage_fee'],
            '6_mo_long_terms_storage_fee' => $row['6_mo_long_terms_storage_fee'],
            'volume_unit' => $row['volume_unit'],
            'country' => $row['country'],
            'enrolled_in_small_and_light' => $row['enrolled_in_small_and_light'],
            'upload_id' => $this->batchID,
            'report_date' => $this->inputReportDate,
            'active' => 1,
            'created_at' => date('Y-m-d h:i:s'),
            'created_by' => $this->userID,
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_by' => $this->userID,
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function getRowCount(): int
    {
        return LongTermStorageFees::where('upload_id', $this->batchID)
            ->where('active', 1)
            ->count();
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                DB::beginTransaction();
                try {
                    LongTermStorageFees::where('report_date', $this->inputReportDate)
                        ->where('upload_id', '<', $this->batchID)
                        ->where('active', '=', 1)
                        ->cursor()
                        ->chunk(1000, function ($item) {
                            $item->update(['active' => 0]);
                        });

                    BatchJob::where('id', $this->batchID)->update(
                        [
                            'status' => 'completed',
                            'total_count' => $this->getRowCount()
                        ]
                    );

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();

                    \Log::channel('daily_queue_import')
                        ->info("[QueueLongTermStorageFees.errors]" . $e);
                }
            },
            ImportFailed::class => function (ImportFailed $event) {
                DB::beginTransaction();
                try {
                    BatchJob::where('id', $this->batchID)
                        ->update(
                            [
                                'status' => 'failed',
                                'total_count' => $this->getRowCount(),
                                'exit_message' => $event->getException(),
                                'user_error_msg' => (new ImportService)->getUserErrorMsg($event->getException())
                            ]
                        );

                    LongTermStorageFees::where('report_date', $this->inputReportDate)
                        ->where('upload_id', '=', $this->batchID)
                        ->where('active', '=', 1)
                        ->cursor()
                        ->chunk(1000, function ($item) {
                            $item->delete();
                        });

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();

                    \Log::channel('daily_queue_import')
                        ->info("[QueueLongTermStorageFees.errors]" . $e);
                }

                foreach ($event->getException() as $failure) {
                    \Log::channel('daily_queue_import')
                        ->info("[QueueLongTermStorageFees.errors]" . $failure);
                }
            },
        ];
    }

    public function rules(): array
    {
        return [];
//        return [
//            'account' => ['bail', 'nullable', 'string', 'max:50'],
//            'snapshot_date' => ['bail', 'nullable', 'string', 'max:50'],
//            'sku' => ['bail', 'nullable', 'string', 'max:100'],
//            'fnsku' => ['bail', 'nullable', 'string', 'max:100'],
//            'asin' => ['bail', 'nullable', 'string', 'max:100'],
//            'product_name' => ['bail', 'nullable', 'string', 'max:255'],
//            'supplier_type' => ['bail', 'nullable', 'string', 'max:50'],
//            'supplier' => ['bail', 'nullable', 'string', 'max:50'],
//            'condition' => ['bail', 'nullable', 'string', 'max:50'],
//            'qty_charged_12_mo_long_term_storage_fee' => ['bail', 'nullable', 'numeric', 'max:50'],//
//            'per_unit_volume' => ['bail', 'nullable', 'numeric', 'between:0,9999999999.9999'],
//            'currency' => ['bail', 'nullable', 'string', 'max:3'],
//            '12_mo_long_terms_storage_fee' => ['bail', 'nullable', 'numeric', 'between:0,99999.99'],
//            'hkd' => ['bail', 'nullable', 'numeric', 'between:0,99999.99'],
//            'hkd_rate' => ['bail', 'nullable', 'numeric', 'between:0,99999.99'],
//            'qty_charged_6_mo_long_term_storage_fee' => ['bail', 'nullable', 'numeric', 'between:0,99999.99'],
//            '6_mo_long_terms_storage_fee' => ['bail', 'nullable', 'numeric', 'between:0,99999.99'],
//            'volume_unit' => ['bail', 'nullable', 'string', 'max:50'],
//            'country' => ['bail', 'nullable', 'string', 'max:2'],
//            'enrolled_in_small_and_light' => ['bail', 'nullable', 'string', 'max:50'],
//
//            '*.account' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.snapshot_date' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.sku' => ['bail', 'nullable', 'string', 'max:100'],
//            '*.fnsku' => ['bail', 'nullable', 'string', 'max:100'],
//            '*.asin' => ['bail', 'nullable', 'string', 'max:100'],
//            '*.product_name' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.supplier_type' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.supplier' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.condition' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.qty_charged_12_mo_long_term_storage_fee' => ['bail', 'nullable', 'numeric', 'max:50'],
//            ' *.per_unit_volume' => ['bail', 'nullable', 'numeric', 'between:0,9999999999.9999'],
//            ' *.currency' => ['bail', 'nullable', 'string', 'max:3'],
//            ' * .12_mo_long_terms_storage_fee' => ['bail', 'nullable', 'numeric', 'between:0,99999.99'],
//            ' *.hkd' => ['bail', 'nullable', 'numeric', 'between:0,99999.99'],
//            ' *.hkd_rate' => ['bail', 'nullable', 'numeric', 'between:0,99999.99'],
//            ' *.qty_charged_6_mo_long_term_storage_fee' => ['bail', 'nullable', 'numeric', 'between:0,99999.99'],
//            ' * .6_mo_long_terms_storage_fee' => ['bail', 'nullable', 'numeric', 'between:0,99999.99'],
//            ' *.volume_unit' => ['bail', 'nullable', 'string', 'max:50'],
//            ' *.country' => ['bail', 'nullable', 'string', 'max:2'],
//            ' *.enrolled_in_small_and_light' => ['bail', 'nullable', 'string', 'max:50'],
//        ];
    }

    public function prepareForValidation(array $row): array
    {
        $row['snapshot_date'] = $row['snapshot_date'] ? (new ImportService)->transformDate($row['snapshot_date'])
            : null;

        return $row;
    }
}

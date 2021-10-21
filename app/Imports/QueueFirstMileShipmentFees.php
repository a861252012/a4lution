<?php

namespace App\Imports;

use App\Models\BatchJobs;
use App\Models\FirstMileShipmentFees;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class QueueFirstMileShipmentFees implements ToModel, WithHeadingRow, ShouldQueue, WithChunkReading, WithBatchInserts, WithCalculatedFormulas, WithEvents, withValidation
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
    )
    {
        $this->userID = $userID;
        $this->batchID = $batchID;
        $this->inputReportDate = $inputReportDate;
    }

    public function model(array $row)
    {
        ++$this->rows;

        return new FirstMileShipmentFees([
            'client_code' => $row['client_code'],
            'ids_sku' => $row['ids_sku'],
            'title' => $row['title'],
            'asin' => $row['asin'],
            'fnsku' => $row['fnsku'],
            'external_id' => $row['external_id'],
            'condition' => $row['condition'],
            'who_will_prep' => $row['who_will_prep'],
            'prep_type' => $row['prep_type'],
            'who_will_label' => $row['who_will_label'],
            'shipped' => $row['shipped'],
            'fba_shipment' => $row['fba_shipment'],
            'shipment_type' => $row['shipment_type'],
            'date' => $row['date'],
            'account' => $row['account'],
            'ship_from' => $row['ship_from'],
            'first_mile' => $row['first_mile'],
            'last_mile_est_orig' => $row['last_mile_apc_original_currency_estimated'],
            'last_mile_act_orig' => $row['last_mile_apc_original_currency_actual'],
            'shipment_remark' => $row['shipment_remark'],
            'currency_last_mile' => $row['currency_last_mile'],
            'exchange_rate' => $row['exchange_rate'],
            'total' => $row['total'],
            'is_payment_settled' => $row['is_payment_settled'],
            'remark' => $row['remark'],
            'report_date' => $this->inputReportDate,
            'active' => 1,
            'upload_id' => $this->batchID,
            'created_at' => date('Y-m-d h:i:s'),
            'created_by' => $this->userID,
            'fulfillment_center' => $row['account'] ? substr($row['account'], -2) : null
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function getRowCount(): int
    {
        return FirstMileShipmentFees::where('upload_id', $this->batchID)
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
                    FirstMileShipmentFees::where('report_date', $this->inputReportDate)
                        ->where('upload_id', '<', $this->batchID)
                        ->where('active', '=', 1)
                        ->cursor()
                        ->each(function ($item) {
                            $item->update(['active' => 0]);
                        });

                    BatchJobs::where('id', $this->batchID)->update(
                        [
                            'status' => 'completed',
                            'total_count' => $this->getRowCount()
                        ]
                    );

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();

                    \Log::channel('daily_queue_import')
                        ->info("[QueueFirstMileShipmentFees.errors]" . $e);
                }
            },
            ImportFailed::class => function (ImportFailed $event) {
                DB::beginTransaction();
                try {
                    BatchJobs::where('id', $this->batchID)
                        ->update(
                            [
                                'status' => 'failed',
                                'total_count' => $this->getRowCount(),
                                'exit_message' => $event->getException()
                            ]
                        );

                    FirstMileShipmentFees::where('report_date', $this->inputReportDate)
                        ->where('upload_id', '=', $this->batchID)
                        ->where('active', '=', 1)
                        ->cursor()
                        ->each(function ($item) {
                            $item->delete();
                        });

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();

                    \Log::channel('daily_queue_import')
                        ->info("[QueueFirstMileShipmentFees.errors]" . $e);
                }

                foreach ($event->getException() as $failure) {
                    \Log::channel('daily_queue_import')
                        ->info("[QueueFirstMileShipmentFees.errors]" . $failure);
                }
            },
        ];
    }

    public function rules(): array
    {
        return [
//            'client_code' => ['bail', 'nullable', 'string', 'max:50'],
//            'ids_sku' => ['bail', 'nullable', 'string', 'max:255'],
//            'title' => ['bail', 'nullable', 'string', 'max:255'],
//            'asin' => ['bail', 'nullable', 'string', 'max:255'],
//            'fnsku' => ['bail', 'nullable', 'string', 'max:255'],
//            'external_id' => ['bail', 'nullable', 'string', 'max:255'],
//            'condition' => ['bail', 'nullable', 'string', 'max:255'],
//            'who_will_prep' => ['bail', 'nullable', 'string', 'max:255'],
//            'prep_type' => ['bail', 'nullable', 'string', 'max:255'],
//            'who_will_label' => ['bail', 'nullable', 'string', 'max:255'],
//            'shipped' => ['bail', 'nullable', 'numeric', 'max:9999999999'],
//            'fba_shipment' => ['bail', 'nullable', 'string', 'max:255'],
//            'shipment_type' => ['bail', 'nullable', 'string', 'max:255'],
//            'date' => ['bail', 'nullable', 'date'],
//            'account' => ['bail', 'nullable', 'string', 'max:255'],
//            'ship_from' => ['bail', 'nullable', 'string', 'max:255'],
//            'first_mile' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//數字前面加上美金符號 type不確定//
//            'last_mile_est_orig' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//數字前面加上美金符號 type不確定
//            'last_mile_act_orig' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//數字前面加上美金符號 type不確定
//            'shipment_remark' => ['bail', 'nullable', 'string', 'max:255'],
//            'currency_last_mile' => ['bail', 'nullable', 'string', 'max:255'],
//            'exchange_rate' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//數字前面加上美金符號 type不確定
//            'total' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//數字前面加上美金符號 type不確定
//            'is_payment_settled' => ['bail', 'nullable', 'string', 'max:255'],
//            'remark' => ['bail', 'nullable', 'string', 'max:255'],
//
//            '*.client_code' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.ids_sku' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.title' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.asin' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.fnsku' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.external_id' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.condition' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.who_will_prep' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.prep_type' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.who_will_label' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.shipped' => ['bail', 'nullable', 'numeric', 'max:9999999999'],
//            '*.fba_shipment' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.shipment_type' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.date' => ['bail', 'nullable', 'date'],
//            '*.account' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.ship_from' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.first_mile' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//數字前面加上美金符號 type不確定//
//            '*.last_mile_est_orig' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//數字前面加上美金符號 type不確定
//            '*.last_mile_act_orig' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//數字前面加上美金符號 type不確定
//            '*.shipment_remark' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.currency_last_mile' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.exchange_rate' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//數字前面加上美金符號 type不確定
//            '*.total' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//數字前面加上美金符號 type不確定
//            '*.is_payment_settled' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.remark' => ['bail', 'nullable', 'string', 'max:255'],
        ];
    }

    public function prepareForValidation(array $row): array
    {
        $row['date'] = $row['date'] ? $this->transformDate($row['date']) : null;

        return $row;
    }

    /**
     * Transform a date value into a Carbon object.
     *
     * @param $value
     * @return string
     */
    public function transformDate($value): string
    {
        try {
            return Carbon::instance(Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        }
    }
}

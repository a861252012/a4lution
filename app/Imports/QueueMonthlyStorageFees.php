<?php

namespace App\Imports;

use App\Models\BatchJob;
use App\Models\MonthlyStorageFees;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\ImportFailed;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Services\ImportService;

class QueueMonthlyStorageFees implements ToModel, WithHeadingRow, ShouldQueue, WithChunkReading, WithBatchInserts, WithCalculatedFormulas, WithEvents, WithValidation
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

        return new MonthlyStorageFees([
            'account' => $row['account'],
            'asin' => $row['asin'],
            'fnsku' => $row['fnsku'],
            'product_name' => $row['product_name'],
            'fulfilment_center' => $row['fulfilment_center'],
            'country_code' => $row['country_code'],
            'supplier_type' => $row['supplier_type'],
            'supplier' => $row['supplier'],
            'longest_side' => $row['longest_side'],
            'median_side' => $row['median_side'],
            'shortest_side' => $row['shortest_side'],
            'measurement_units' => $row['measurement_units'],
            'weight' => $row['weight'],
            'weight_units' => $row['weight_units'],
            'item_volume' => $row['item_volume'],
            'volume_units' => $row['volume_units'],
            'product_size_tier' => $row['product_size_tier'],
            'average_quantity_on_hand' => $row['average_quantity_on_hand'],
            'average_quantity_pending_removal' => $row['average_quantity_pending_removal'],
            'total_item_volume_est' => $row['total_item_volume_est'],
            'month_of_charge' => $row['month_of_charge'],
            'storage_rate' => $row['storage_rate'],
            'currency' => $row['currency'],
            'hkd' => $row['hkd'],
            'monthly_storage_fee_est' => $row['monthly_storage_fee_est'],
            'hkd_rate' => $row['hkd_rate'],
            'dangerous_goods_storage_type' => $row['dangerous_goods_storage_type'],
            'category' => $row['category'],
            'eligible_for_discount' => $row['eligible_for_discount'],
            'qualified_for_discount' => $row['qualified_for_discount'],
            'total_incentive_fee_amount' => $row['total_incentive_fee_amount'],
            'breakdown_incentive_fee_amount' => $row['breakdown_incentive_fee_amount'],
            'average_quantity_customer_orders' => $row['average_quantity_customer_orders'],
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
        return MonthlyStorageFees::where('upload_id', $this->batchID)
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
                    MonthlyStorageFees::where('report_date', $this->inputReportDate)
                        ->where('active', '=', 1)
                        ->where('upload_id', '<', $this->batchID)
                        ->cursor()
                        ->chunk(1000, function ($items) {
                            $items->update(['active' => 0]);
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
                        ->info("[QueueMonthlyStorageFees.errors]" . $e);
                }
            },
            ImportFailed::class => function (ImportFailed $event) {
                DB::beginTransaction();
                try {
                    BatchJob::where('id', $this->batchID)->update(
                        [
                            'status' => 'failed',
                            'total_count' => $this->getRowCount(),
                            'exit_message' => $event->getException(),
                            'user_error_msg' => (new ImportService)->getUserErrorMsg($event->getException())
                        ]
                    );

                    MonthlyStorageFees::where('report_date', $this->inputReportDate)
                        ->where('active', '=', 1)
                        ->where('upload_id', '=', $this->batchID)
                        ->cursor()
                        ->chunk(1000, function ($item) {
                            $item->delete();
                        });

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();

                    \Log::channel('daily_queue_import')
                        ->info("[QueueMonthlyStorageFees.errors]" . $e);
                }

                foreach ($event->getException() as $failure) {
                    \Log::channel('daily_queue_import')
                        ->info("[QueueMonthlyStorageFees.errors]" . $failure);
                }
            },
        ];
    }

    public function rules(): array
    {
        return [];
//        return [
//            'account' => ['bail', 'nullable', 'string', 'max:50'],//
//            'asin' => ['bail', 'nullable', 'string', 'max:50'],//
//            'fnsku' => ['bail', 'nullable', 'string', 'max:50'],//
//            'product_name' => ['bail', 'nullable', 'string', 'max:255'],//
//            'fulfilment_center' => ['bail', 'nullable', 'string', 'max:50'],//
//            'country_code' => ['bail', 'nullable', 'string', 'max:50'],//
//            'supplier_type' => ['bail', 'nullable', 'string', 'max:50'],//
//            'supplier' => ['bail', 'nullable', 'string', 'max:50'],//
//            'longest_side' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            'median_side' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            'shortest_side' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            'measurement_units' => ['bail', 'nullable', 'string', 'max:50'],//
//            'weight' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            'weight_units' => ['bail', 'nullable', 'string', 'max:50'],/////
//            'item_volume' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            'volume_units' => ['bail', 'nullable', 'string', 'max:50'],//
//            'product_size_tier' => ['bail', 'nullable', 'string', 'max:50'],//
//            'average_quantity_on_hand' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            'average_quantity_pending_removal' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            'total_item_volume_est' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            'month_of_charge' => ['bail', 'nullable', 'date'],//
//            'storage_rate' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            'currency' => ['bail', 'nullable', 'string', 'max:3'],
//            'hkd' => ['bail', 'nullable', 'numeric', 'max:99999999'],
//            'monthly_storage_fee_est' => ['bail', 'nullable', 'numeric', 'max:99999999'],
//            'hkd_rate' => ['bail', 'nullable', 'numeric', 'max:99999999'],
//            'dangerous_goods_storage_type' => ['bail', 'nullable', 'string', 'max:50'],
////            'category' => ['bail', 'nullable'],
//            'eligible_for_discount' => ['bail', 'nullable', 'string', 'max:50'],
////            'qualified_for_discount' => ['bail', 'nullable', 'string', 'max:50'],
////            'total_incentive_fee_amount' => ['bail', 'nullable', 'string', 'max:50'],
////            'breakdown_incentive_fee_amount' => ['bail', 'nullable', 'string', 'max:50'],
//            'average_quantity_customer_orders' => ['bail', 'nullable', 'numeric', 'max:99999999'],
//
//            '*.account' => ['bail', 'nullable', 'string', 'max:50'],//
//            '*.asin' => ['bail', 'nullable', 'string', 'max:50'],//
//            '*.fnsku' => ['bail', 'nullable', 'string', 'max:50'],//
//            '*.product_name' => ['bail', 'nullable', 'string', 'max:255'],//
//            '*.fulfilment_center' => ['bail', 'nullable', 'string', 'max:50'],//
//            '*.country_code' => ['bail', 'nullable', 'string', 'max:50'],//
//            '*.supplier_type' => ['bail', 'nullable', 'string', 'max:50'],//
//            '*.supplier' => ['bail', 'nullable', 'string', 'max:50'],//
//            '*.longest_side' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            '*.median_side' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            '*.shortest_side' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            '*.measurement_units' => ['bail', 'nullable', 'string', 'max:50'],//
//            '*.weight' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            '*.weight_units' => ['bail', 'nullable', 'string', 'max:50'],//////
//            '*.item_volume' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            '*.volume_units' => ['bail', 'nullable', 'string', 'max:50'],//
//            '*.product_size_tier' => ['bail', 'nullable', 'string', 'max:50'],//
//            '*.average_quantity_on_hand' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            '*.average_quantity_pending_removal' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            '*.total_item_volume_est' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            '*.month_of_charge' => ['bail', 'nullable', 'date'],//
//            '*.storage_rate' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            '*.currency' => ['bail', 'nullable', 'string', 'max:3'],
//            '*.hkd' => ['bail', 'nullable', 'numeric', 'max:99999999'],
//            '*.monthly_storage_fee_est' => ['bail', 'nullable', 'numeric', 'max:99999999'],
//            '*.hkd_rate' => ['bail', 'nullable', 'numeric', 'max:99999999'],
//            '*.dangerous_goods_storage_type' => ['bail', 'nullable', 'string', 'max:50'],
////            '*.category' => ['bail', 'nullable'],
//            '*.eligible_for_discount' => ['bail', 'nullable', 'string', 'max:50'],
////            '*.qualified_for_discount' => ['bail', 'nullable', 'string', 'max:50'],
////            '*.total_incentive_fee_amount' => ['bail', 'nullable', 'string', 'max:50'],
////            '*.breakdown_incentive_fee_amount' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.average_quantity_customer_orders' => ['bail', 'nullable', 'numeric', 'max:99999999'],
//        ];
    }

    public function prepareForValidation(array $row): array
    {
        $row['month_of_charge'] = $row['month_of_charge'] ? (new ImportService)->transformDate($row['month_of_charge'])
            : null;

        return $row;
    }
}

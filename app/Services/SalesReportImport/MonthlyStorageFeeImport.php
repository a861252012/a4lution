<?php

namespace App\Services\SalesReportImport;

use Carbon\Carbon;
use App\Models\BatchJob;
use App\Services\ImportService;
use App\Models\MonthlyStorageFee;
use Illuminate\Support\Facades\DB;
use App\Constants\BatchJobConstant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class MonthlyStorageFeeImport implements ImportInterface
{
    public function import(LazyCollection $collection, int $batchId, Carbon $reportDate, int $userId)
    {
        $batchJob = BatchJob::findOrFail($batchId);

        foreach ($collection->chunk(1000) as $fees) {

            DB::beginTransaction();

            try {
                $data = [];
                foreach ($fees as $fee) {
                    if (isset($fee['account'])) {
                        $data[] = [
                            'account' => $fee['account'],
                            'asin' => $fee['asin'],
                            'fnsku' => $fee['fnsku'],
                            'product_name' => $fee['product_name'],
                            'fulfilment_center' => $fee['fulfilment_center'],
                            'country_code' => $fee['country_code'],
                            'supplier_type' => $fee['supplier_type'],
                            'supplier' => $fee['supplier'],
                            'longest_side' => $fee['longest_side'],
                            'median_side' => $fee['median_side'],
                            'shortest_side' => $fee['shortest_side'],
                            'measurement_units' => $fee['measurement_units'],
                            'weight' => $fee['weight'],
                            'weight_units' => $fee['weight_units'],
                            'item_volume' => $fee['item_volume'],
                            'volume_units' => $fee['volume_units'],
                            'product_size_tier' => $fee['product_size_tier'],
                            'average_quantity_on_hand' => $fee['average_quantity_on_hand'],
                            'average_quantity_pending_removal' => $fee['average_quantity_pending_removal'],
                            'total_item_volume_est' => $fee['total_item_volume_est'],
                            'month_of_charge' => $fee['month_of_charge'],
                            'storage_rate' => $fee['storage_rate'],
                            'currency' => $fee['currency'],
                            'hkd' => $fee['hkd'],
                            'monthly_storage_fee_est' => $fee['monthly_storage_fee_est'],
                            'hkd_rate' => $fee['hkd_rate'],
                            // 'dangerous_goods_storage_type' => $fee['dangerous_goods_storage_type'],
                            'category' => $fee['category'],
                            'eligible_for_discount' => $fee['eligible_for_discount'],
                            'qualified_for_discount' => $fee['qualified_for_discount'],
                            'total_incentive_fee_amount' => $fee['total_incentive_fee_amount'],
                            'breakdown_incentive_fee_amount' => $fee['breakdown_incentive_fee_amount'],
                            'average_quantity_customer_orders' => $fee['average_quantity_customer_orders'],
                            'upload_id' => $batchId,
                            'report_date' => $reportDate->toDateString(),
                            'active' => 1,
                            'created_at' => date('Y-m-d h:i:s'),
                            'created_by' => $userId,
                            'updated_at' => date('Y-m-d h:i:s'),
                            'updated_by' => $userId,
                        ];
                    }
                }

                MonthlyStorageFee::insert($data);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();

                Log::channel('daily_queue_import')
                    ->error("[A4lutionSalesReport.errors]" . $e);

                $batchJob->monthlyStorageFees()->update(['active' => 0]);

                $batchJob->update([
                    'status' => BatchJobConstant::STATUS_FAILED,
                    'total_count' => 0,
                    'exit_message' => $e->getMessage(),
                    'user_error_msg' => (new ImportService)->getUserErrorMsg($e->getMessage())
                ]);

                return;
            }

        }

        MonthlyStorageFee::where('report_date', $reportDate->toDateString())
            ->where('upload_id', '!=', $batchId)
            ->active()
            ->update(['active' => 0]);

        $batchJob->update([
            'status' => BatchJobConstant::STATUS_COMPLETED,
            'total_count' => MonthlyStorageFee::where('upload_id', $batchId)->active()->count(),
        ]);
    }
}

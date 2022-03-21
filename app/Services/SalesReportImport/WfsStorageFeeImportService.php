<?php

namespace App\Services\SalesReportImport;

use App\Services\SalesReportImport\ImportInterface;

class WfsStorageFeeImportService implements ImportInterface
{
    public function import(LazyCollection $collection, int $batchId, Carbon $reportDate, int $userId)
    {
        $batchJob = BatchJob::findOrFail($batchId);

        foreach ($collection->chunk(1000) as $items) {
            DB::beginTransaction();

            try {

                $data = [];
                foreach ($items as $item) {
                    if (isset($item['partner_gtin'])) {
                        $data[] = [
                            'partner_gtin' => $item['partner_gtin'],
                            'vendor_sku' => $item['vendor_sku'],
                            'supplier_type' => $item['supplier_type'],
                            'supplier' => $item['supplier'],
                            'walmart_item_id' => $item['walmart_item_id'],
                            'item_name' => $item['item_name'],
                            'length' => $item['length'],
                            'width' => $item['width'],
                            'height' => $item['height'],
                            'volume' => $item['volume'],
                            'weight' => $item['weight'],
                            // 'standard_daily_storage_cost_per_unit_off_peak_aged_under_365_days' => $item['shipmentnumber'],
                            // 'peak_daily_storage_cost_per_unit_aged_over_30_days' => $item['warehousecountry'],
                            // 'long_term_daily_storage_cost_per_unit_aged_over_365_days' => $item['shipmentstate'],
                            'average_units_on_hand' => $item['average_units_on_hand'],
                            'ending_units_on_hand' => $item['ending_units_on_hand'],
                            'storage_fee_for_selected_time_period' => $item['storage_fee_for_selected_time_period'],
                            'hkd_rate' => $item['hkd_rate'],
                            'storage_fee_hkd' => $item['storage_fee_hkd'],
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

                ReturnHelperCharge::insert($data);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();

                Log::channel('daily_queue_import')
                    ->error("[A4lutionSalesReport.errors]" . $e);

                $batchJob->returnHelperCharges()->update(['active' => 0]);

                $batchJob->update([
                    'status' => BatchJobConstant::STATUS_FAILED,
                    'total_count' => 0,
                    'exit_message' => $e->getMessage(),
                    'user_error_msg' => (new ImportService)->getUserErrorMsg($e->getMessage())
                ]);

                return;
            }
        }

        ReturnHelperCharge::where('report_date', $reportDate->toDateString())
                ->where('upload_id', '!=', $batchId)
                ->active()
                ->update(['active' => 0]);

        $batchJob->update([
            'status' => BatchJobConstant::STATUS_COMPLETED,
            'total_count' => ReturnHelperCharge::where('upload_id', $batchId)->active()->count(),
        ]);
    }
}

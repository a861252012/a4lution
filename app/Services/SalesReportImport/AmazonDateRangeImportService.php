<?php

namespace App\Services\SalesReportImport;

use Carbon\Carbon;
use App\Models\BatchJob;
use App\Services\ImportService;
use Illuminate\Support\Facades\DB;
use App\Constants\BatchJobConstant;
use Illuminate\Support\Facades\Log;
use App\Models\AmazonDateRangeReport;
use Illuminate\Support\LazyCollection;
use App\Services\SalesReportImport\ImportInterface;

class AmazonDateRangeImportService implements ImportInterface
{
    public function import(LazyCollection $collection, int $batchId, Carbon $reportDate, int $userId)
    {
        $batchJob = BatchJob::findOrFail($batchId);

        foreach ($collection->chunk(1000) as $dateRanges) {

            DB::beginTransaction();

            try {
                $data = [];
                foreach ($dateRanges as $dateRange) {
                    if (isset($dateRange['account'])) {

                        $data[] = [
                            'account' => $dateRange['account'],
                            'country' => $dateRange['country'],
                            'paid_date' => $dateRange['paid_date'],
                            'shipped_date' => $dateRange['shipped_date'],
                            'settlement_id' => $dateRange['settlement_id'],
                            'type' => $dateRange['type'],
                            'description' => $dateRange['description'],
                            'order_id' => $dateRange['order_id'],
                            'order_type' => $dateRange['order_type'],
                            'msku' => $dateRange['msku'],
                            'asin' => $dateRange['asin'],
                            'product_name' => $dateRange['product_name'],
                            'sku' => $dateRange['sku'],
                            'supplier_type' => $dateRange['supplier_type'],
                            'supplier' => $dateRange['supplier'],
                            'marketplace' => $dateRange['marketplace'],
                            'fulfillment' => $dateRange['fulfillment'],
                            'quantity' => $dateRange['quantity'],
                            'currency' => $dateRange['currency'],
                            'product_sales' => $dateRange['product_sales'],
                            'shipping_credits' => $dateRange['shipping_credits'],
                            'gift_wrap_credits' => $dateRange['gift_wrap_credits'],
                            'promotional_rebates' => $dateRange['promotional_rebates'],
                            'cost_of_point' => $dateRange['cost_of_point'],
                            'tax' => $dateRange['tax'],
                            'marketplace_withheld_tax' => $dateRange['marketplace_withheld_tax'],
                            'selling_fees' => $dateRange['selling_fees'],
                            'fba_fees' => $dateRange['fba_fees'],
                            'other_transaction_fees' => $dateRange['other_transaction_fees'],
                            'other' => $dateRange['other'],
                            'amazon_total' => $dateRange['amazon_total'],
                            'hkd_rate' => $dateRange['hkd_rate'],
                            'amazon_total_hkd' => $dateRange['amazon_total_hkd'],
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

                AmazonDateRangeReport::insert($data);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
    
                Log::channel('daily_queue_import')
                    ->error("[A4lutionSalesReport.errors]" . $e);

                $batchJob->amazonDateRangeReports()->update(['active' => 0]);
    
                $batchJob->update([
                    'status' => BatchJobConstant::STATUS_FAILED,
                    'total_count' => 0,
                    'exit_message' => $e->getMessage(),
                    'user_error_msg' => (new ImportService)->getUserErrorMsg($e->getMessage())
                ]);

                return;
            }
        }

        AmazonDateRangeReport::where('report_date', $reportDate->toDateString())
            ->where('upload_id', '!=', $batchId)
            ->active()
            ->update(['active' => 0]);

        $batchJob->update([
            'status' => BatchJobConstant::STATUS_COMPLETED,
            'total_count' => AmazonDateRangeReport::where('upload_id', $batchId)->active()->count(),
        ]);
    }
}

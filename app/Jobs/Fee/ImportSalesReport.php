<?php

namespace App\Jobs\Fee;

use App\Models\BatchJob;
use Illuminate\Support\Str;
use App\Models\PlatformAdFee;
use Illuminate\Bus\Queueable;
use InvalidArgumentException;
use App\Services\ImportService;
use App\Models\MonthlyStorageFee;
use App\Models\LongTermStorageFee;
use App\Support\SimpleExcelReader;
use Illuminate\Support\Facades\DB;
use App\Constants\BatchJobConstant;
use Illuminate\Support\Facades\Log;
use App\Models\AmazonDateRangeReport;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\LazyCollection;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\SalesReportImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Symfony\Component\HttpFoundation\Response;

class ImportSalesReport implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    private $userId;
    private $path;
    private $fileName;
    private $reportDate;
    private $batchIds;

    public function __construct($userId, $path, $fileName, $reportDate, $batchIds)
    {
        $this->userId = $userId;
        $this->path = $path;
        $this->fileName = $fileName;
        $this->reportDate = $reportDate;
        $this->batchIds = $batchIds;
    }

    public function handle()
    {
        $file = $this->path . '/' . $this->fileName;

        $sheetNames = SalesReportImportService::sheets();

        $excel = SimpleExcelReader::create($file, 'xlsx');
        $reader = $excel->getReader();
        $reader->open($file);

        foreach ($reader->getSheetIterator() as $sheet) {
            $sheetName = Str::slug($sheet->getName(), '_');
            
            // dump($sheetName);
            if (in_array($sheetName, $sheetNames)) {

                if (! method_exists($this, $method = 'import' . Str::studly($sheetName))) {
                    throw new InvalidArgumentException("Unsupported import [{$sheetName}] sheet.");
                }

                // 資料匯入
                $this->{$method}(
                    $excel->headersToSnakeCase()->getRowsBySheet($sheet),
                );
            }
        }

        // TODO: 刪除本地檔案
    }
    
    private function importAmzAds(LazyCollection $collection)
    {
        $this->importAdFee($collection);
    }

    private function importEbayAds(LazyCollection $collection)
    {
        $this->importAdFee($collection);
    }

    private function importWalmartAds(LazyCollection $collection)
    {
        $this->importAdFee($collection);
    }

    private function importLazadaAds(LazyCollection $collection)
    {
        $this->importAdFee($collection);
    }

    private function importShopeeAds(LazyCollection $collection)
    {
        $this->importAdFee($collection);
    }

    private function importAdFee(LazyCollection $collection)
    {
        $batchId = $this->batchIds[BatchJobConstant::FEE_TYPE_PLATFORM_AD_FEES];
        $batchJob = BatchJob::findOrFail($batchId);

        foreach ($collection->chunk(1000) as $adFees) {
            DB::beginTransaction();

            try {

                $data = [];
                foreach ($adFees as $adFee) {
                    if (isset($adFee['client_code'])) {
                        $data[] = [
                            'client_code' => $adFee['client_code'],
                            'client_type' => $adFee['client_type'],
                            'platform' => $adFee['platform'],
                            'account' => $adFee['account'],
                            'campagin_type' => $adFee['campagin_type'],
                            'campagin' => $adFee['campagin'],
                            'currency' => $adFee['currency'],
                            'Impressions' => $adFee['impressions'],
                            'clicks' => $adFee['clicks'],
                            'ctr' => $adFee['ctr'],
                            'spendings' => $adFee['spendings'],
                            'spendings_hkd' => $adFee['spendings_hkd'],
                            'cpc' => $adFee['cpc'],
                            'sales_qty' => $adFee['sales_qty'],
                            'sales_amount' => $adFee['sales_amount'],
                            'sales_amount_hkd' => $adFee['sales_amount_hkd'],
                            'acos' => $adFee['acos'],
                            'exchange_rate' => $adFee['exchange_rate'],
                            'upload_id' => $batchId,
                            'report_date' => $this->reportDate,
                            'active' => 1,
                            'created_at' => date('Y-m-d h:i:s'),
                            'created_by' => $this->userId,
                            'updated_at' => date('Y-m-d h:i:s'),
                            'updated_by' => $this->userId,
                        ];
                    }
                }

                PlatformAdFee::insert($data);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();

                Log::channel('daily_queue_import')
                    ->error("[A4lutionSalesReport.errors]" . $e);

                $batchJob->platformAdFees()->update(['active' => 0]);

                $batchJob->update([
                    'status' => BatchJobConstant::STATUS_FAILED,
                    'total_count' => 0,
                    'exit_message' => $e->getMessage(),
                    'user_error_msg' => (new ImportService)->getUserErrorMsg($e->getMessage())
                ]);

                return;
            }
        }

        PlatformAdFee::where('report_date', $this->reportDate)
                ->where('upload_id', '!=', $batchId)
                ->active()
                ->update(['active' => 0]);

        $batchJob->update([
            'status' => BatchJobConstant::STATUS_COMPLETED,
            'total_count' => PlatformAdFee::where('upload_id', $batchId)->active()->count(),
        ]);
    }

    private function importDateRange(LazyCollection $collection)
    {
        
        $batchId = $this->batchIds[BatchJobConstant::FEE_TYPE_AMAZON_DATE_RANGE];
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
                            'report_date' => $this->reportDate,
                            'active' => 1,
                            'created_at' => date('Y-m-d h:i:s'),
                            'created_by' => $this->userId,
                            'updated_at' => date('Y-m-d h:i:s'),
                            'updated_by' => $this->userId,
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

        AmazonDateRangeReport::where('report_date', $this->reportDate)
            ->where('upload_id', '!=', $batchId)
            ->active()
            ->update(['active' => 0]);

        $batchJob->update([
            'status' => BatchJobConstant::STATUS_COMPLETED,
            'total_count' => AmazonDateRangeReport::where('upload_id', $batchId)->active()->count(),
        ]);
    }

    private function importMonthlyStorageFees(LazyCollection $collection)
    {
        $batchId = $this->batchIds[BatchJobConstant::FEE_TYPE_MONTHLY_STORAGE_FEES];
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
                            'dangerous_goods_storage_type' => $fee['dangerous_goods_storage_type'],
                            'category' => $fee['category'],
                            'eligible_for_discount' => $fee['eligible_for_discount'],
                            'qualified_for_discount' => $fee['qualified_for_discount'],
                            'total_incentive_fee_amount' => $fee['total_incentive_fee_amount'],
                            'breakdown_incentive_fee_amount' => $fee['breakdown_incentive_fee_amount'],
                            'average_quantity_customer_orders' => $fee['average_quantity_customer_orders'],
                            'upload_id' => $batchId,
                            'report_date' => $this->reportDate,
                            'active' => 1,
                            'created_at' => date('Y-m-d h:i:s'),
                            'created_by' => $this->userId,
                            'updated_at' => date('Y-m-d h:i:s'),
                            'updated_by' => $this->userId,
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

        MonthlyStorageFee::where('report_date', $this->reportDate)
            ->where('upload_id', '!=', $batchId)
            ->active()
            ->update(['active' => 0]);

        $batchJob->update([
            'status' => BatchJobConstant::STATUS_COMPLETED,
            'total_count' => MonthlyStorageFee::where('upload_id', $batchId)->active()->count(),
        ]);
    }

    private function importLongTermStorageFeeCharge(LazyCollection $collection)
    {
        $batchId = $this->batchIds[BatchJobConstant::FEE_TYPE_LONG_TERM_STORAGE_FEES];
        $batchJob = BatchJob::findOrFail($batchId);

        foreach ($collection->chunk(1000) as $fees) {

            DB::beginTransaction();

            try {
                $data = [];
                foreach ($fees as $fee) {
                    if (isset($fee['account'])) {
                        $data[] = [
                            'account' => $fee['account'],
                            'snapshot_date' => $fee['snapshot_date'],
                            'sku' => $fee['sku'],
                            'fnsku' => $fee['fnsku'],
                            'asin' => $fee['asin'],
                            'product_name' => $fee['product_name'],
                            'supplier_type' => $fee['supplier_type'],
                            'supplier' => $fee['supplier'],
                            'condition' => $fee['condition'],
                            'qty_charged_12_mo_long_term_storage_fee' => $fee['qty_charged_12_mo_long_term_storage_fee'],
                            'per_unit_volume' => $fee['per_unit_volume'],
                            'currency' => $fee['currency'],
                            '12_mo_long_terms_storage_fee' => $fee['12_mo_long_terms_storage_fee'],
                            'hkd' => $fee['hkd'],
                            'hkd_rate' => $fee['hkd_rate'],
                            'qty_charged_6_mo_long_term_storage_fee' => $fee['qty_charged_6_mo_long_term_storage_fee'],
                            '6_mo_long_terms_storage_fee' => $fee['6_mo_long_terms_storage_fee'],
                            'volume_unit' => $fee['volume_unit'],
                            'country' => $fee['country'],
                            'enrolled_in_small_and_light' => $fee['enrolled_in_small_and_light'],
                            'upload_id' => $batchId,
                            'report_date' => $this->reportDate,
                            'active' => 1,
                            'created_at' => date('Y-m-d h:i:s'),
                            'created_by' => $this->userId,
                            'updated_at' => date('Y-m-d h:i:s'),
                            'updated_by' => $this->userId,
                        ];
                    }
                }

                LongTermStorageFee::insert($data);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();

                Log::channel('daily_queue_import')
                    ->error("[A4lutionSalesReport.errors]" . $e);

                $batchJob->longTermStorageFees()->update(['active' => 0]);

                $batchJob->update([
                    'status' => BatchJobConstant::STATUS_FAILED,
                    'total_count' => 0,
                    'exit_message' => $e->getMessage(),
                    'user_error_msg' => (new ImportService)->getUserErrorMsg($e->getMessage())
                ]);

                return;
            }
        }
        

        LongTermStorageFee::where('report_date', $this->reportDate)
            ->where('upload_id', '!=', $batchId)
            ->active()
            ->update(['active' => 0]);

        $batchJob->update([
            'status' => BatchJobConstant::STATUS_COMPLETED,
            'total_count' => LongTermStorageFee::where('upload_id', $batchId)->active()->count(),
        ]);
    }
}
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

        DB::beginTransaction();

        try {
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

            
            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            BatchJob::whereIn('id', $this->batchIds)
                ->update([
                    'status' => BatchJobConstant::STATUS_FAILED,
                    'total_count' => $this->getRowCount(),
                    'exit_message' => $e->getException(),
                    'user_error_msg' => (new ImportService)->getUserErrorMsg($e->getException())
                ]);

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Error');

            Log::channel('daily_queue_import')
                ->error("[A4lutionSalesReport.errors]" . $e);
        }


        // TODO: 刪除本地檔案
    }
    
    public function importAmzAds(LazyCollection $collection)
    {
        $this->importAdFee($collection);
    }

    public function importEbayAds(LazyCollection $collection)
    {
        $this->importAdFee($collection);
    }

    public function importWalmartAds(LazyCollection $collection)
    {
        $this->importAdFee($collection);
    }

    public function importLazadaAds(LazyCollection $collection)
    {
        $this->importAdFee($collection);
    }

    public function importShopeeAds(LazyCollection $collection)
    {
        $this->importAdFee($collection);
    }

    public function importAdFee(LazyCollection $collection)
    {
        $batchId = $this->batchIds[BatchJobConstant::FEE_TYPE_PLATFORM_AD_FEES];

        $collection
            ->chunk(10000)
            ->each(function ($rows) use ($batchId) {

                $data = [];
                foreach ($rows as $row) {
                    if (isset($row['client_code'])) {
                        $data[] = [
                            'client_code' => $row['client_code'],
                            'client_type' => $row['client_type'],
                            'platform' => $row['platform'],
                            'account' => $row['account'],
                            'campagin_type' => $row['campagin_type'],
                            'campagin' => $row['campagin'],
                            'currency' => $row['currency'],
                            'Impressions' => $row['impressions'],
                            'clicks' => $row['clicks'],
                            'ctr' => $row['ctr'],
                            'spendings' => $row['spendings'],
                            'spendings_hkd' => $row['spendings_hkd'],
                            'cpc' => $row['cpc'],
                            'sales_qty' => $row['sales_qty'],
                            'sales_amount' => $row['sales_amount'],
                            'sales_amount_hkd' => $row['sales_amount_hkd'],
                            'acos' => $row['acos'],
                            'exchange_rate' => $row['exchange_rate'],
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
            });

            PlatformAdFee::where('report_date', $this->reportDate)
                ->where('upload_id', '!=', $batchId)
                ->active()
                ->update(['active' => 0]);

            BatchJob::findOrFail($batchId)->update([
                'status' => BatchJobConstant::STATUS_COMPLETED,
                'total_count' => $collection->count()
            ]);
    }

    public function importDateRange(LazyCollection $collection)
    {
        $batchId = $this->batchIds[BatchJobConstant::FEE_TYPE_AMAZON_DATE_RANGE];

        $collection
            ->chunk(10000)
            ->each(function ($rows) use ($batchId) {

                $data = [];
                foreach ($rows as $row) {
                    if (isset($row['account'])) {
                        $data[] = [
                            'account' => $row['account'],
                            'country' => $row['country'],
                            'paid_date' => $row['paid_date'],
                            'shipped_date' => $row['shipped_date'],
                            'settlement_id' => $row['settlement_id'],
                            'type' => $row['type'],
                            'description' => $row['description'],
                            'order_id' => $row['order_id'],
                            'order_type' => $row['order_type'],
                            'msku' => $row['msku'],
                            'asin' => $row['asin'],
                            'product_name' => $row['product_name'],
                            'sku' => $row['sku'],
                            'supplier_type' => $row['supplier_type'],
                            'supplier' => $row['supplier'],
                            'marketplace' => $row['marketplace'],
                            'fulfillment' => $row['fulfillment'],
                            'quantity' => $row['quantity'],
                            'currency' => $row['currency'],
                            'product_sales' => $row['product_sales'],
                            'shipping_credits' => $row['shipping_credits'],
                            'gift_wrap_credits' => $row['gift_wrap_credits'],
                            'promotional_rebates' => $row['promotional_rebates'],
                            'cost_of_point' => $row['cost_of_point'],
                            'tax' => $row['tax'],
                            'marketplace_withheld_tax' => $row['marketplace_withheld_tax'],
                            'selling_fees' => $row['selling_fees'],
                            'fba_fees' => $row['fba_fees'],
                            'other_transaction_fees' => $row['other_transaction_fees'],
                            'other' => $row['other'],
                            'amazon_total' => $row['amazon_total'],
                            'hkd_rate' => $row['hkd_rate'],
                            'amazon_total_hkd' => $row['amazon_total_hkd'],
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
            });

        AmazonDateRangeReport::where('report_date', $this->reportDate)
            ->where('upload_id', '!=', $batchId)
            ->active()
            ->update(['active' => 0]);

        BatchJob::findOrFail($batchId)->update([
            'status' => BatchJobConstant::STATUS_COMPLETED,
            'total_count' => $collection->count()
        ]);
    }

    public function importMonthlyStorageFees(LazyCollection $collection)
    {
        $batchId = $this->batchIds[BatchJobConstant::FEE_TYPE_MONTHLY_STORAGE_FEES];

        $collection
            ->chunk(10000)
            ->each(function ($rows) use ($batchId) {

                $data = [];
                foreach ($rows as $row) {
                    dd($row);
                    if (isset($row['account'])) {
                        $data[] = [
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
            });

        MonthlyStorageFee::where('report_date', $this->reportDate)
            ->where('upload_id', '!=', $batchId)
            ->active()
            ->update(['active' => 0]);

        BatchJob::findOrFail($batchId)->update([
            'status' => BatchJobConstant::STATUS_COMPLETED,
            'total_count' => $collection->count()
        ]);
    }

    public function importLongTermStorageFeeCharge(LazyCollection $collection)
    {
        $batchId = $this->batchIds[BatchJobConstant::FEE_TYPE_LONG_TERM_STORAGE_FEES];

        $collection
            ->chunk(10000)
            ->each(function ($rows) use ($batchId) {

                $data = [];
                foreach ($rows as $row) {
                    if (isset($row['account'])) {
                        $data[] = [
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
            });

        LongTermStorageFee::where('report_date', $this->reportDate)
            ->where('upload_id', '!=', $batchId)
            ->active()
            ->update(['active' => 0]);

        BatchJob::findOrFail($batchId)->update([
            'status' => BatchJobConstant::STATUS_COMPLETED,
            'total_count' => $collection->count()
        ]);
    }
}
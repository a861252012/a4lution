<?php

namespace App\Services\SalesReportImport;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\BatchJob;
use Illuminate\Support\Str;
use App\Models\OrderProduct;
use App\Services\ImportService;
use App\Models\OrderSkuCostDetail;
use Illuminate\Support\Facades\DB;
use App\Constants\BatchJobConstant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use App\Services\SalesReportImport\ImportInterface;

class OrderImportService implements ImportInterface
{
    public function import(LazyCollection $collection, int $batchId, Carbon $reportDateCarbon, int $userId)
    {
        $reportDate = $reportDateCarbon->format('Ymd');
        $reportDateYm = $reportDateCarbon->format('Ym');

        $batchJob = BatchJob::findOrFail($batchId);

        // 舊資料 active => 0
        Order::where('correlation_id', 'like', "{$reportDateYm}%")->active()->update(['active' => 0]);
        OrderProduct::where('correlation_id', 'like', "{$reportDateYm}%")->active()->update(['active' => 0]);
        OrderSkuCostDetail::where('correlation_id', 'like', "{$reportDateYm}%")->active()->update(['active' => 0]);

        foreach ($collection->groupBy('package_id')->chunk(500) as $packageIdWithOrders) {

            $orderData = [];
            $orderProductData = [];
            $orderSkuCostData = [];

            DB::beginTransaction();

            try {
                foreach ($packageIdWithOrders as $packageId => $orders) {

                    // 建立 Order
                    $main = $orders->first();

                    $warehouse_name = trim(Str::between($main['warehouse'], '[', ']'));
                    $warehouse_code = trim(Str::before($main['warehouse'], '['));

                    $sm_code = trim(Str::before($main['shipping_method'], '['));

                    $orderData[] = [
                        'correlation_id' => $reportDate,
                        'platform' => $main['platform'],
                        'order_code' => $main['package_id'],
                        'reference_no' => $main['erp_order_id'],
                        'seller_id' => trim($main['acc_nick_name']),
                        'sm_code' => $sm_code,
                        'add_time' => Carbon::parse($main['audit_date'])->toDateTimeString(),
                        'order_paydate' => Carbon::parse($main['paid_date'])->toDateTimeString(),
                        'order_status' => 8,
                        'ship_time' => Carbon::parse($main['shipped_date'])->toDateTimeString(),
                        'tracking_number' => $main['tracking'],
                        'so_weight' => $main['product_weight'],
                        'platform_user_name' => $main['acc_name'],
                        'platform_ref_no' => $main['site_order_id'],
                        'warehouse_name' => $warehouse_name,
                        'warehouse_code' => $warehouse_code,
                        'created_at' => now(),
                        'order_type' => $main['order_type'],
                        'package_type' => $main['package_type'],
                        'active' => 1,
                    ];

                    foreach (
                        $orders->groupBy( fn ($order) => $order['erp_order_id'] . $order['sku'] ) 
                        as $orders
                    ) {

                        $order = $orders->first();
                        $ordersCollection = collect($orders);

                        $other_fee_sum =  (float) $ordersCollection->sum('other_fee_original_currency');
                        $marketplace_tax_sum = (float) $ordersCollection->sum('marketplace_tax_original_currency');
                        $cost_of_point_sum = (float) $ordersCollection->sum('cost_of_point_original_currency');
                        $exclusives_referral_fee_sum = (float) $ordersCollection->sum('exclusives_referral_fee_original_currency');
                        $qty_sum = $ordersCollection->sum('qty');

                        $orderProductData[] = [
                            'correlation_id' => $reportDate,
                            'order_code' => $main['package_id'],
                            'sku' => $order['sku'],
                            'weight' => $order['product_weight'],
                            'supplier_type' => $order['supplier_type'],
                            'supplier' => $order['supplier'],
                            'currency_code' => $order['original_currency'],
                            'sales_amount' => $ordersCollection->sum('order_price_original_currency'),
                            'paypal_fee' => $ordersCollection->sum('paypal_fee_original_currency'),
                            'transaction_fee' => $ordersCollection->sum('transaction_fee_original_currency'),
                            'fba_fee' => $ordersCollection->sum('fba_fee_original_currency'),
                            'first_mile_shipping_fee' => $ordersCollection->sum('first_mile_shipping_fee_original_currency'),
                            'first_mile_tariff' => $ordersCollection->sum('first_mile_tariff_original_currency'),
                            'last_mile_shipping_fee' => $ordersCollection->sum('last_mile_shipping_fee_original_currency'),
                            'other_fee' => $other_fee_sum,
                            'purchase_shipping_fee' => $ordersCollection->sum('purchase_shipping_fee_original_currency'),
                            'product_cost' => $ordersCollection->sum('product_cost_original_currency'),
                            'marketplace_tax' => $marketplace_tax_sum,
                            'cost_of_point' => $cost_of_point_sum,
                            'exclusives_referral_fee' => $exclusives_referral_fee_sum,
                            'gross_profit' => $order['gross_profit_original_currency'],
                            'other_transaction' => $other_fee_sum + $marketplace_tax_sum + $cost_of_point_sum + $exclusives_referral_fee_sum,
                            'unit_sold' => $qty_sum,
                            'created_at' => now(),
                            'created_by' => $userId,
                            'updated_at' => now(),
                            'updated_by' => $userId,
                            'active' => 1,
                            'promotion_discount_rate' => 0,
                            'promotion_amount' => 0,
                        ];
                        
                        $orderSkuCostData[] = [
                            'correlation_id' => $reportDate,
                            'platform' => $order['platform'],
                            'sm_code' => trim(Str::before($order['shipping_method'], '[')),
                            'op_platform_sales_sku' => $order['platform_sku'],
                            'product_barcode' => $order['sku'],
                            'reference_no' => $order['package_id'],
                            'site_id' => $order['site'],
                            'seller_id' => $order['acc_nick_name'],
                            'currency_code_org' => $order['original_currency'],
                            'order_total_amount_org' => $ordersCollection->sum('order_price_original_currency'),
                            'order_platform_type' => $order['order_type'],
                            'platform_reference_no' => trim($order['erp_order_id']),
                            'product_title' => $order['product_name'],
                            'quantity' => $qty_sum,
                            'product_barcode' => $order['sku'],
                            'currency_code' => $order['original_currency'],
                            'currency_rate' => 1,
                            'created_at' => now(),
                            'active' => 1,
                        ];
                    }

                }
                
                Order::insert($orderData);
                OrderProduct::insert($orderProductData);
                OrderSkuCostDetail::insert($orderSkuCostData);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();

                Log::channel('daily_queue_import')
                    ->error("[A4lutionSalesReport.errors]" . $e);

                // 刪除已匯入資料
                Order::where('correlation_id', $reportDate)->active()->delete();
                OrderProduct::where('correlation_id', $reportDate)->active()->delete();
                OrderSkuCostDetail::where('correlation_id', $reportDate)->active()->delete();  

                // 還原舊資料
                Order::where('correlation_id', 'like', "{$reportDateYm}%")->inactive()->update(['active' => 1]);
                OrderProduct::where('correlation_id', 'like', "{$reportDateYm}%")->inactive()->update(['active' => 1]);
                OrderSkuCostDetail::where('correlation_id', 'like', "{$reportDateYm}%")->inactive()->update(['active' => 1]);       

                $batchJob->update([
                    'status' => BatchJobConstant::STATUS_FAILED,
                    'total_count' => 0,
                    'exit_message' => $e->getMessage(),
                    'user_error_msg' => (new ImportService)->getUserErrorMsg($e->getMessage())
                ]);

                return;
            }
        }

        // 刪除舊資料
        Order::where('correlation_id', 'like', "{$reportDateYm}%")->inactive()->delete();
        OrderProduct::where('correlation_id', 'like', "{$reportDateYm}%")->inactive()->delete();
        OrderSkuCostDetail::where('correlation_id', 'like', "{$reportDateYm}%")->inactive()->delete(); 

        $batchJob->update([
            'status' => BatchJobConstant::STATUS_COMPLETED,
            'total_count' => Order::where('correlation_id', $reportDate)->active()->count(),
        ]);
    }
}

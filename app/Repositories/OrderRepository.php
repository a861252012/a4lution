<?php

namespace App\Repositories;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Order);
    }

    public function insertData(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Order::insert($data);
        });
    }

    public function getTableColumns()
    {
        return Schema::getColumnListing((new Order)->getTable());
    }

    //if isCrafter,then get a4 account fees,if not, then get client account fees
    public function getReportFees(
        string $reportDate,
        string $clientCode,
        bool $isAvolution
    ) {
        return $this->model
            ->selectRaw(
                'SUM((order_products.last_mile_shipping_fee + 
                ((order_products.first_mile_tariff + order_products.first_mile_shipping_fee) 
                / order_sku_cost_details.currency_rate)) * exchange_rates.exchange_rate) AS "shipping_fee_hkd",
                sum((order_products.transaction_fee + order_products.other_transaction) * exchange_rates.exchange_rate)
                AS "platform_fee_hkd",
                SUM(order_products.fba_fee * exchange_rates.exchange_rate) AS "FBA_fees_hkd"'
            )
            ->join('order_products', function ($join) {
                $join->on('order_products.order_code', '=', 'orders.order_code')
                    ->where('order_products.active', 1);
            })
            ->join('order_sku_cost_details', function ($join) {
                $join->on('order_products.order_code', '=', 'order_sku_cost_details.reference_no')
                    ->on('order_products.sku', '=', 'order_sku_cost_details.product_barcode');
            })
            ->leftJoin('exchange_rates', function ($join) {
                $join->on('order_sku_cost_details.currency_code_org', '=', 'exchange_rates.base_currency')
                    ->where('exchange_rates.active', 1)
                    ->where(
                        DB::raw("DATE_FORMAT(orders.ship_time, '%Y%m')"),
                        '=',
                        DB::raw("DATE_FORMAT(exchange_rates.quoted_date, '%Y%m')")
                    );
            })
            ->whereRaw("DATE_FORMAT(orders.ship_time, '%Y%m') = ?", date("Ym", strtotime($reportDate)))
            ->where('order_products.supplier', $clientCode)
            ->when($isAvolution, function ($q) {
                return $q->whereIn('orders.seller_id', function ($query) {
                    $query->from('seller_accounts')
                        ->selectRaw('DISTINCT erp_nick_name')
                        ->where('is_a4_account', 1)
                        ->where('active', 1)
                        ->get();
                });
            }, function ($q) {
                return $q->whereNotIn('orders.seller_id', function ($query) {
                    $query->from('seller_accounts')
                        ->selectRaw('DISTINCT erp_nick_name')
                        ->where('is_a4_account', 1)
                        ->where('active', 1)
                        ->get();
                });
            })
            ->groupBy('order_products.supplier')
            ->first();
    }

    //if $isAvolution,then get a4 account resend,if not, then get client account resend
    public function getAccountResend(
        string $reportDate,
        string $clientCode,
        bool $isAvolution
    ): float {
        return (float)$this->model
            ->selectRaw('(order_sku_cost_details.product_amount_org * exchange_rates.exchange_rate)
             AS "total_sales_hkd"')
            ->join('order_products', function ($join) {
                $join->on('order_products.order_code', '=', 'orders.order_code')
                    ->where('order_products.active', 1);
            })
            ->join('order_sku_cost_details', function ($join) {
                $join->on('order_products.order_code', '=', 'order_sku_cost_details.reference_no')
                    ->on('order_products.sku', '=', 'order_sku_cost_details.product_barcode');
            })
            ->leftJoin('exchange_rates', function ($join) {
                $join->on('order_sku_cost_details.currency_code_org', '=', 'exchange_rates.base_currency')
                    ->where('exchange_rates.active', 1)
                    ->where(
                        DB::raw("DATE_FORMAT(orders.ship_time, '%Y%m')"),
                        '=',
                        DB::raw("DATE_FORMAT(exchange_rates.quoted_date, '%Y%M')")
                    );
            })
            ->whereRaw("DATE_FORMAT(orders.ship_time, '%Y%m') = ?", date("Ym", strtotime($reportDate)))
            ->where('order_products.supplier', $clientCode)
            ->where('orders.seller_id', $clientCode)
            ->where('order_sku_cost_details.order_platform_type', 'resend')
            ->when($isAvolution, function ($q) {
                return $q->whereIn('orders.seller_id', function ($query) {
                    $query->from('seller_accounts')
                        ->selectRaw('DISTINCT erp_nick_name')
                        ->where('is_a4_account', 1)
                        ->where('active', 1)
                        ->get();
                });
            }, function ($q) {
                return $q->whereNotIn('orders.seller_id', function ($query) {
                    $query->from('seller_accounts')
                        ->selectRaw('DISTINCT erp_nick_name')
                        ->where('is_a4_account', 1)
                        ->where('active', 1)
                        ->get();
                });
            })
            ->value('total_sales_hkd');
    }

    public function getAccountFbaStorageFee(
        string $reportDate,
        string $clientCode,
        bool $isAvolution
    ) {
        $sql = "SELECT
    SUM(x.storage_fee_hkd) as storage_fee_hkd_sum
FROM
    (SELECT
        (m.`monthly_storage_fee_est` * r.exchange_rate) AS 'storage_fee_hkd'
    FROM
        monthly_storage_fees m
    LEFT JOIN exchange_rates r ON m.report_date = r.quoted_date
        AND m.currency = r.base_currency
        AND r.active = 1
    WHERE
        m.supplier = '{$clientCode}'
            AND m.report_date = '{$reportDate}'
            AND m.active = 1 
            AND m.account ";

        $isAvolution ? $sql .= '' : $sql .= 'NOT';

        $sql .= " IN (SELECT DISTINCT asinking_account_name
         FROM seller_accounts
        WHERE is_a4_account = 1 AND active=1)

         UNION ALL SELECT
        (t.12_mo_long_terms_storage_fee * r.exchange_rate) AS 'storage_fee_hkd'
    FROM
        long_term_storage_fees t
    LEFT JOIN exchange_rates r ON t.report_date = r.quoted_date
        AND t.currency = r.base_currency
        AND r.active = 1
    WHERE
        t.supplier = '{$clientCode}'
            AND t.report_date = '{$reportDate}'
            AND t.active = 1 
            AND t.account ";

        $isAvolution ? $sql .= '' : $sql .= 'NOT';

        $sql .= " IN (
            SELECT DISTINCT asinking_account_name
             FROM seller_accounts 
            WHERE is_a4_account = 1 AND active = 1)) x ";

        return DB::select($sql);
    }

    public function getTotalSalesOrders(
        string $reportDate,
        string $clientCode
    ): int {
        return (int)$this->model
            ->selectRaw("COUNT(DISTINCT orders.reference_no) as total_sales_orders")
            ->leftJoin('order_products', function ($join) {
                $join->on('orders.order_code', '=', 'order_products.order_code')
                    ->where('order_products.active', 1);
            })
            ->whereRaw("DATE_FORMAT(orders.ship_time, '%Y%m') = ?", date("Ym", strtotime($reportDate)))
            ->where('order_products.supplier', $clientCode)
            ->whereNotNull('orders.platform_ref_no')
            ->value('total_sales_orders');
    }

    public function getSumOfSalesAmount(
        string $reportDate,
        string $clientCode
    ): float {
        return (float)$this->model
            ->selectRaw("SUM(order_sku_cost_details.order_total_amount_org * exchange_rates.exchange_rate) 
            AS 'total_sales_hkd'")
            ->leftJoin('order_products', function ($join) {
                $join->on('orders.order_code', '=', 'order_products.order_code')
                    ->where('order_products.active', 1);
            })
            ->leftJoin('order_sku_cost_details', function ($join) {
                $join->on('order_products.order_code', '=', 'order_sku_cost_details.reference_no')
                    ->on('order_products.sku', '=', 'order_sku_cost_details.product_barcode');
            })
            ->leftJoin('exchange_rates', function ($join) {
                $join->on('order_sku_cost_details.currency_code_org', '=', 'exchange_rates.base_currency')
                    ->where(
                        DB::raw("DATE_FORMAT(orders.ship_time, '%Y%m')"),
                        DB::raw("DATE_FORMAT(exchange_rates.quoted_date, '%Y%m')")
                    )
                    ->where('exchange_rates.active', 1);
            })
            ->whereRaw("DATE_FORMAT(orders.ship_time, '%Y%m') = ?", date("Ym", strtotime($reportDate)))
            ->where('order_products.supplier', $clientCode)
            ->groupBy('order_products.supplier')
            ->value('total_sales_hkd');
    }

    public function getOrderDetail(array $request)
    {
        return $this->model->select(
            'orders.sm_code',
            'orders.seller_id',
            'orders.tracking_number',
            'orders.add_time',
            'orders.order_paydate',
            'orders.ship_time',
            'orders.order_code',
            DB::raw("CONCAT(orders.warehouse_code,'[',orders.warehouse_name,']') as warehouse"),
            'order_sku_cost_details.product_title',
            'order_sku_cost_details.op_platform_sales_sku',
            'order_sku_cost_details.asin_or_item',
            'order_sku_cost_details.quantity',
            'order_sku_cost_details.currency_code_org',
            'order_products.id as product_id',
            'order_products.weight',
            'order_products.promotion_discount_rate',
            'order_products.promotion_amount',
            'order_products.purchase_shipping_fee',
            'order_products.product_cost',
            'order_products.first_mile_shipping_fee',
            'order_products.first_mile_tariff',
            'order_products.last_mile_shipping_fee',
            'order_products.paypal_fee',
            'order_products.transaction_fee',
            'order_products.fba_fee',
            'order_products.promotion_amount',
            'order_products.promotion_discount_rate',
            'order_products.other_transaction',
        )
            ->join('order_sku_cost_details', 'order_sku_cost_details.reference_no', '=', 'orders.order_code')
            ->join('order_products', 'order_products.order_code', '=', 'orders.order_code')
            ->when($request['acc_nick_name'], fn ($q) => $q->where('orders.seller_id', $request['acc_nick_name']))
            ->when($request['erp_order_id'], fn ($q) => $q->where('orders.reference_no', $request['erp_order_id']))
            ->when($request['package_id'], fn ($q) => $q->where('orders.order_code', $request['package_id']))
            ->when($request['sku'], fn ($q) => $q->where('order_products.sku', $request['sku']))
            ->when($request['shipped_date'], function ($q, $reportDate) {
                return $q->whereBetween(
                    'orders.ship_time',
                    [
                        Carbon::parse($reportDate)->startOfDay()->toDateTimeString(),
                        Carbon::parse($reportDate)->endOfDay()->toDateTimeString()
                    ]
                );
            })
            ->where('order_products.active', 1)
            ->first()
            ->toArray();
    }

    public function getTotalUnitSold(
        string $reportDate,
        string $clientCode
    ): int {
        return (int)$this->model
            ->selectRaw("SUM(order_sku_cost_details.quantity) AS 'qty'")
            ->join('order_products', function ($join) {
                $join->on('order_products.order_code', '=', 'orders.order_code')
                    ->where('order_products.active', 1);
            })
            ->join('order_sku_cost_details', function ($join) {
                $join->on('order_sku_cost_details.reference_no', '=', 'order_products.order_code')
                    ->on('order_sku_cost_details.product_barcode', '=', 'order_products.sku');
            })
            ->join('exchange_rates', function ($join) {
                $join->on('order_sku_cost_details.currency_code_org', '=', 'exchange_rates.base_currency')
                    ->where('exchange_rates.active', 1)
                    ->where(
                        DB::raw("DATE_FORMAT(orders.ship_time, '%Y%m')"),
                        '=',
                        DB::raw("DATE_FORMAT(exchange_rates.quoted_date, '%Y%m')")
                    );
            })
            ->whereRaw("DATE_FORMAT(orders.ship_time, '%Y%m') = ?", date("Ym", strtotime($reportDate)))
            ->where('order_products.supplier', $clientCode)
            ->whereNotNull('orders.platform_ref_no')
            ->value('qty');
    }

    public function getSalesOrder(
        string $reportDate,
        string $clientCode,
        bool $isAvolution
    ) {
        return $this->model->query()
            ->selectRaw('COUNT(DISTINCT orders.reference_no) as sales_order_count')
            ->join('order_products', function ($join) {
                $join->on('order_products.order_code', '=', 'orders.order_code')
                    ->where('order_products.active', 1);
            })
            ->whereRaw("DATE_FORMAT(orders.ship_time,'%Y%m') = ?", date("Ym", strtotime($reportDate)))
            ->where('order_products.supplier', $clientCode)
            ->whereNotNull('orders.platform_ref_no')
            ->when($isAvolution, function ($q) {
                return $q->whereIn('orders.seller_id', function ($query) {
                    $query->selectRaw('DISTINCT erp_nick_name')
                        ->from('seller_accounts')
                        ->where('is_a4_account', 1)
                        ->where('active', 1);
                });
            }, function ($q) {
                return $q->whereNotIn('orders.seller_id', function ($query) {
                    $query->selectRaw('DISTINCT erp_nick_name')
                        ->from('seller_accounts')
                        ->where('is_a4_account', 1)
                        ->where('active', 1);
                });
            })->value('sales_order_count');
    }

    public function getSalesAmount(
        string $reportDate,
        string $clientCode,
        bool $isAvolution
    ): float {
        return (float)$this->model
            ->selectRaw("sum(order_sku_cost_details.order_total_amount_org * exchange_rates.exchange_rate ) AS 
            'total_sales_hkd'")
            ->join('order_products', function ($join) {
                $join->on('order_products.order_code', '=', 'orders.order_code')
                    ->where('order_products.active', 1);
            })
            ->join('order_sku_cost_details', function ($join) {
                $join->on('order_products.order_code', '=', 'order_sku_cost_details.reference_no')
                    ->on('order_products.sku', '=', 'order_sku_cost_details.product_barcode');
            })
            ->leftJoin('exchange_rates', function ($join) {
                $join->on('order_sku_cost_details.currency_code_org', '=', 'exchange_rates.base_currency')
                    ->where('exchange_rates.active', 1)
                    ->where(
                        DB::raw("DATE_FORMAT(orders.ship_time,'%Y%m')"),
                        DB::raw("DATE_FORMAT(exchange_rates.quoted_date,'%Y%m')")
                    );
            })
            ->whereRaw("DATE_FORMAT(orders.ship_time,'%Y%m') = ?", date("Ym", strtotime($reportDate)))
            ->where('order_products.supplier', $clientCode)
            ->when($isAvolution, function ($q) {
                return $q->whereIn('orders.seller_id', function ($query) {
                    $query->selectRaw('DISTINCT erp_nick_name')
                        ->from('seller_accounts')
                        ->where('is_a4_account', 1)
                        ->where('active', 1);
                });
            }, function ($q) {
                return $q->whereNotIn('orders.seller_id', function ($query) {
                    $query->selectRaw('DISTINCT erp_nick_name')
                        ->from('seller_accounts')
                        ->where('is_a4_account', 1)
                        ->where('active', 1);
                });
            })->groupBy('order_products.supplier')
            ->value('total_sales_hkd');
    }
}

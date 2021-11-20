<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Boolean;

class OrderRepository
{
    protected $orders;

    public function __construct()
    {
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
    public function getReportFees(string $reportDate, string $clientCode, string $supplierCode, bool $isCrafter)
    {
        $reportDate = date("Ym", strtotime($reportDate));

        $sql = "SELECT
    SUM((p.last_mile_shipping_fee + ((p.first_mile_tariff + p.first_mile_shipping_fee) / d.currency_rate)) * r.exchange_rate) AS 'shipping_fee_hkd',
    SUM(p.transaction_fee * r.exchange_rate) AS 'platform_fee_hkd',
    SUM(p.fba_fee * r.exchange_rate) AS 'FBA_fees_hkd',
    SUM(p.other_transaction * r.exchange_rate) AS 'other_transaction_fees_hkd'
FROM
    orders o
        LEFT JOIN
    order_products p ON p.active = 1
    AND o.order_code = p.order_code
        LEFT JOIN
    order_sku_cost_details d ON p.order_code = d.reference_no
    AND p.sku = d.product_barcode
        LEFT JOIN
    exchange_rates r ON d.currency_code_org = r.base_currency
    AND DATE_FORMAT(o.ship_time, '%Y%m') = DATE_FORMAT(r.quoted_date, '%Y%m')
WHERE
    DATE_FORMAT(o.ship_time, '%Y%m') = {$reportDate}
    AND p.supplier = '{$clientCode}'";

        $isCrafter ? $sql .= " AND o.seller_id = '{$supplierCode}'" : $sql .= "AND o.seller_id != '{$supplierCode}'";

        $sql .= " GROUP BY p.supplier";

        return DB::select($sql);
    }

    //if isCrafter,then get a4 account refund,if not, then get client account refund
    public function getAccountRefund(string $reportDate, string $clientCode, string $supplierName, bool $isCrafter)
    {
        $reportDate = date("Ym", strtotime($reportDate));

        $sql = "SELECT
    SUM(abs(a.amount_refund) * r.exchange_rate) AS 'refund_amount_hkd'
FROM
    rma_refund_list a
        LEFT JOIN
    exchange_rates r ON a.currency = r.base_currency
        AND DATE_FORMAT(a.create_date, '%Y%M') = DATE_FORMAT(r.quoted_date, '%Y%M')
WHERE
    a.pc_name = '{$clientCode}'
        AND DATE_FORMAT(a.create_date, '%Y%m') = {$reportDate}
        AND a.shipping_method !='AMAZONFBA'";

        $isCrafter ? $sql .= " AND a.user_account_name = '{$supplierName}'" : $sql .= " AND a.user_account_name != '{$supplierName}'";

        return DB::select($sql);
    }

    //if isCrafter,then get a4 account resend,if not, then get client account resend
    public function getAccountResend(string $reportDate, string $clientCode, string $supplierName, bool $isCrafter)
    {
        $reportDate = date("Ym", strtotime($reportDate));

        $sql = "SELECT
    (d.product_amount_org * r.exchange_rate) AS 'total_sales_hkd'
FROM
    orders o
        LEFT JOIN
    order_products p ON p.active = 1
        AND o.order_code = p.order_code
        LEFT JOIN
    order_sku_cost_details d ON p.order_code = d.reference_no
        AND p.sku = d.product_barcode
        LEFT JOIN
    exchange_rates r ON d.currency_code_org = r.base_currency
        AND DATE_FORMAT(o.ship_time, '%Y%m') = DATE_FORMAT(r.quoted_date, '%Y%m')
WHERE
    DATE_FORMAT(o.ship_time, '%Y%m') = {$reportDate}
        AND p.supplier = '{$clientCode}'
        AND d.order_platform_type = 'resend'";

        $isCrafter ? $sql .= "AND o.seller_id = '{$supplierName}'" : $sql .= "AND o.seller_id != '{$supplierName}'";

        return DB::select($sql);
    }

    //if isCrafter,then get a4 account refund,if not, then get client account AmzTotal
    public function getAccountAmzTotal(string $reportDate, string $clientCode, string $supplierName, bool $isCrafter)
    {
        $reportDate = date("Ym", strtotime($reportDate));

        $sql = "SELECT
    SUM(abs(d.amazon_total) * r.exchange_rate) AS 'amazon_total_hkd'
FROM
    amazon_date_range_report d
        LEFT JOIN
    exchange_rates r ON d.currency = r.base_currency
        AND DATE_FORMAT(d.report_date, '%Y%M') = DATE_FORMAT(r.quoted_date, '%Y%M')
WHERE
    d.`type` = 'Refund'
        AND d.fulfillment = 'Amazon'
        AND d.supplier = '{$clientCode}'
        AND DATE_FORMAT(d.report_date, '%Y%m') = '{$reportDate}'
        AND d.active = 1";

        $isCrafter ? $sql .= " AND d.account = '{$supplierName}'" : $sql .= " AND d.account != '{$supplierName}'";

        return DB::select($sql);
    }

    public function getAccountAds(string $reportDate, string $clientCode, string $supplierName, bool $isCrafter)
    {
        $sql = "SELECT
    SUM((p.spendings * r.exchange_rate)) AS 'ad'
FROM
    platform_ad_fees p
        LEFT JOIN
    exchange_rates r ON p.report_date = r.quoted_date
        AND p.currency = r.base_currency
WHERE
    p.active = 1 AND p.client_code = '{$clientCode}'
        AND p.report_date = '{$reportDate}'
        AND p.active = 1";

        $isCrafter ? $sql .= " AND p.account = '{$supplierName}'" : $sql .= " AND p.account != '{$supplierName}'";

        $sql .= " GROUP BY p.client_code";

        return DB::select($sql);
    }

    public function getAccountFbaStorageFee(string $reportDate, string $clientCode, string $supplierName, bool $isCrafter)
    {
        $sql = "SELECT
    SUM(x.storage_fee_hkd) as storage_fee_hkd_sum
FROM
    (SELECT
        (m.`monthly_storage_fee_est` * r.exchange_rate) AS 'storage_fee_hkd'
    FROM
        monthly_storage_fees m
    LEFT JOIN exchange_rates r ON m.report_date = r.quoted_date
        AND m.currency = r.base_currency
    WHERE
        m.supplier = '{$clientCode}'
            AND m.report_date = '{$reportDate}'
            AND m.active = 1 ";

        $isCrafter ? $sql .= " AND m.account = '{$supplierName}'" : $sql .= " AND m.account != '{$supplierName}'";

        $sql .= " UNION ALL SELECT
        (t.12_mo_long_terms_storage_fee * r.exchange_rate) AS 'storage_fee_hkd'
    FROM
        long_term_storage_fees t
    LEFT JOIN exchange_rates r ON t.report_date = r.quoted_date
        AND t.currency = r.base_currency
    WHERE
        t.supplier = '{$clientCode}'
            AND t.report_date = '{$reportDate}'
            AND t.active = 1 ";

        $isCrafter ? $sql .= " AND t.account = '{$supplierName}') x" : $sql .= " AND t.account != '{$supplierName}') x";

        return DB::select($sql);
    }

    public function getTotalSalesOrders(string $reportDate, string $clientCode)
    {
        $reportDate = date("Ym", strtotime($reportDate));

        $sql = "SELECT
    COUNT(DISTINCT o.reference_no) as total_sales_orders
FROM
    orders o
        LEFT JOIN
    order_products p ON p.active = 1
    AND o.order_code = p.order_code
WHERE
    DATE_FORMAT(o.ship_time, '%Y%m') = {$reportDate}
    AND p.supplier = '{$clientCode}'
    AND o.platform_ref_no IS NOT NULL";

        return DB::select($sql);
    }

    public function getSumOfSalesAmount(string $reportDate, string $clientCode)
    {
        $reportDate = date("Ym", strtotime($reportDate));

        $sql = "SELECT
    SUM(d.order_total_amount_org * r.exchange_rate) AS 'total_sales_hkd'
FROM
    orders o
        LEFT JOIN
    order_products p ON p.active = 1
        AND o.order_code = p.order_code
        LEFT JOIN
    order_sku_cost_details d ON p.order_code = d.reference_no
        AND p.sku = d.product_barcode
        LEFT JOIN
    exchange_rates r ON d.currency_code_org = r.base_currency
        AND DATE_FORMAT(o.ship_time, '%Y%m') = DATE_FORMAT(r.quoted_date, '%Y%m')
WHERE
    DATE_FORMAT(o.ship_time, '%Y%m') = '{$reportDate}'
        AND p.supplier = '{$clientCode}'
GROUP BY p.supplier";

        return DB::select($sql);
    }
}

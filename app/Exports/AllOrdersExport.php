<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Throwable;

class AllOrdersExport implements
    WithTitle,
    FromQuery,
    WithHeadings,
    withMapping,
    WithStrictNullComparison
{
    private string $reportDate;
    private string $clientCode;

    public function __construct(
        string $reportDate,
        string $clientCode
    ) {
        $this->reportDate = $reportDate;
        $this->clientCode = $clientCode;
    }

    public function title(): string
    {
        return 'All Orders';
    }

    public function failed(Throwable $exception): void
    {
        \Log::channel('daily_queue_export')
            ->info('AllOrdersExport')
            ->info($exception);
    }

    public function query()
    {
        return Order::query()
            ->select(
                DB::raw("orders.reference_no AS order_number"),
                DB::raw("orders.seller_id AS account"),
                DB::raw("d.platform AS marketplace"),
                DB::raw("d.site_id AS region"),
                DB::raw("d.order_total_amount_org AS original_sales_amount"),
                DB::raw("d.currency_code_org AS currency"),
                DB::raw("r.exchange_rate AS exchange_rate"),
                DB::raw("ROUND(d.order_total_amount_org * r.exchange_rate, 4) AS total_sales_HKD"),
                DB::raw("ROUND((p.last_mile_shipping_fee * r.exchange_rate ), 4) AS shipping_fee_HKD"),
                DB::raw("ROUND((p.transaction_fee+p.paypal_fee) * r.exchange_rate, 4) AS platform_fee_HKD"),
                DB::raw("ROUND(p.fba_fee * r.exchange_rate, 4) AS FBA_fees_HKD"),
                DB::raw("ROUND((p.other_transaction) * r.exchange_rate, 4) AS other_trans_fee_HKD"),
                DB::raw("orders.order_paydate AS order_date"),
                DB::raw("orders.ship_time AS ship_date"),
                DB::raw("orders.sm_code AS shipping_method"),
                DB::raw("d.order_platform_type AS transaction_type"),
                DB::raw("d.product_title AS item_name"),
                DB::raw("d.quantity AS qty"),
                DB::raw("d.product_barcode AS sku"),
            )
            ->leftJoin('order_products as p', function ($join) {
                $join->on('p.order_code', '=', 'orders.order_code');
                $join->on('p.active', '=', DB::raw("1"));
            })
            ->leftJoin('order_sku_cost_details as d', function ($join) {
                $join->on('p.order_code', '=', 'd.reference_no');
                $join->on('p.sku', '=', 'd.product_barcode');
            })
            ->leftJoin('exchange_rates as r', function ($join) {
                $join->on('d.currency_code_org', '=', 'r.base_currency');
                $join->on(
                    DB::raw("DATE_FORMAT(orders.ship_time, '%Y%m')"),
                    '=',
                    DB::raw("DATE_FORMAT(r.quoted_date, '%Y%m')")
                );
                $join->where('r.active', 1);
            })
            ->where(
                DB::raw("DATE_FORMAT(orders.ship_time,'%Y%m')"),
                $this->reportDate
            )
            ->where('p.supplier', '=', $this->clientCode)
            ->whereNotNull('orders.platform_ref_no');
    }

    public function headings(): array
    {
        return [
            'Order number',
            'Account',
            'Marketplace',
            'Original Sales Amount',
            'Region',
            'Currency',
            'Exchange Rate',
            'Total Sales (HKD)',
            'Shipping Fee (HKD)',
            'Platform Fee (HKD)',
            'FBAFees(HKD)',
            'OtherTransactionFees(HKD)',
            'Order Date',
            'Ship Date',
            'Shipping Method',
            'Transaction Type',
            'Item Name',
            'Qty',
            'sku',
        ];
    }

    public function map($row): array
    {
        return [
            [
                $row->order_number,
                $row->account,
                $row->marketplace,
                $row->original_sales_amount,
                $row->region,
                $row->currency,
                $row->exchange_rate,
                $row->total_sales_HKD,
                $row->shipping_fee_HKD,
                $row->platform_fee_HKD,
                $row->FBA_fees_HKD,
                $row->other_trans_fee_HKD,
                $row->order_date,
                $row->ship_date,
                $row->shipping_method,
                $row->transaction_type,
                $row->item_name,
                $row->qty,
                $row->sku,
            ]
        ];
    }
}

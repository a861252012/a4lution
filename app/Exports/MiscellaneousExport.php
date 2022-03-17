<?php

namespace App\Exports;

use App\Models\AmazonDateRangeReport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Throwable;

class MiscellaneousExport implements
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
        return 'Miscellaneous';
    }

    public function failed(Throwable $exception): void
    {
        \Log::channel('daily_queue_export')
            ->info('MiscellaneousExport')
            ->info($exception);
    }

    public function query()
    {
        return AmazonDateRangeReport::query()
            ->select(
                "amazon_date_range_report.account",
                "amazon_date_range_report.country",
                "amazon_date_range_report.paid_date",
                DB::raw("amazon_date_range_report.shipped_date AS dispatch_date"),
                DB::raw("amazon_date_range_report.settlement_id AS transaction_ID"),
                DB::raw("amazon_date_range_report.type AS transaction_type"),
                DB::raw("amazon_date_range_report.description AS description"),
                "amazon_date_range_report.order_id",
                DB::raw("amazon_date_range_report.order_type AS type"),
                "amazon_date_range_report.msku",
                "amazon_date_range_report.asin",
                DB::raw("amazon_date_range_report.product_name AS item_name"),
                "amazon_date_range_report.sku",
                DB::raw("amazon_date_range_report.supplier_type AS business_type"),
                DB::raw("amazon_date_range_report.supplier AS brand"),
                "amazon_date_range_report.marketplace",
                "amazon_date_range_report.fulfillment",
                "amazon_date_range_report.quantity",
                "amazon_date_range_report.currency",
                DB::raw("amazon_date_range_report.product_sales AS price"),
                DB::raw("amazon_date_range_report.shipping_credits AS shipping_charge_back"),
                DB::raw("amazon_date_range_report.gift_wrap_credits AS gift_wrap_charge_back"),
                "amazon_date_range_report.promotional_rebates",
                "amazon_date_range_report.cost_of_point",
                "amazon_date_range_report.tax",
                "amazon_date_range_report.marketplace_withheld_tax",
                DB::raw("amazon_date_range_report.selling_fees AS platform_fee"),
                "amazon_date_range_report.fba_fees",
                "amazon_date_range_report.other_transaction_fees",
                "amazon_date_range_report.other",
                "amazon_date_range_report.amazon_total",
                DB::raw("amazon_date_range_report.amazon_total AS total_amount"),
                DB::raw("(amazon_date_range_report.amazon_total * exchange_rates.exchange_rate ) AS total_amount_HKD"),
                "exchange_rates.exchange_rate",
            )
            ->leftJoin('exchange_rates', function ($join) {
                $join->on('amazon_date_range_report.currency', '=', 'exchange_rates.base_currency');
                $join->on('amazon_date_range_report.report_date', '=', 'exchange_rates.quoted_date');
                $join->where('exchange_rates.active', 1);
            })
            ->where('amazon_date_range_report.active', 1)
            ->where('amazon_date_range_report.supplier', $this->clientCode)
            ->where('amazon_date_range_report.report_date', $this->reportDate)
            ->whereNotIn(
                'amazon_date_range_report.type',
                [
                    'Refund',
                    'Order',
                    'Debt',
                    'Other FBA Inventory Fee',
                    'Transfer',
                    'Service Fee',
                    'Liquidations'
                ]
            );
    }

    public function headings(): array
    {
        return [
            'Account',
            'Country',
            'Paid Date',
            'Dispatch Date',
            'Transaction ID',
            'Transaction Type',
            'Description',
            'Order ID',
            'Type',
            'MSKU',
            'ASIN',
            'Item Name',
            'sku',
            'Business Type',
            'Brand',
            'Marketplace',
            'Fulfillment Method',
            'Qty',
            'Currency',
            'Price',
            'ShippingChargeback',
            'GiftWrapChargeback',
            'Promotion Rebate',
            'Points',
            'Sale Tax',
            'Marketplace TAX',
            'Platform Fee',
            'FBA fees',
            'Other Transaction Fee',
            'Other Fee',
            'Total Amount',
            'Total Amount (HKD)',
            'Exchange Rate',
        ];
    }

    public function map($row): array
    {
        return [
            [
                $row->account,
                $row->country,
                $row->paid_date,
                $row->dispatch_date,
                $row->transaction_ID,
                $row->transaction_type,
                $row->description,
                $row->order_id,
                $row->type,
                $row->msku,
                $row->asin,
                $row->item_name,
                $row->sku,
                $row->business_type,
                $row->brand,
                $row->marketplace,
                $row->fulfillment,
                $row->quantity,
                $row->currency,
                $row->price,
                $row->shipping_charge_back,
                $row->gift_wrap_charge_back,
                $row->promotional_rebates,
                $row->cost_of_point,
                $row->tax,
                $row->marketplace_withheld_tax,
                $row->platform_fee,
                $row->fba_fees,
                $row->other_transaction_fees,
                $row->other,
                $row->amazon_total,
                $row->total_amount_HKD,
                $row->exchange_rate,
            ]
        ];
    }
}

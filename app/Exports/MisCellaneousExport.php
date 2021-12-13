<?php

namespace App\Exports;

use App\Models\AmazonDateRangeReport;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Throwable;

class MisCellaneousExport implements WithTitle, FromQuery, WithHeadings, withMapping, WithStrictNullComparison
{
    private $reportDate;
    private $clientCode;
    private $insertInvoiceID;

    public function __construct(
        string $reportDate,
        string $clientCode,
        int    $insertInvoiceID
    ) {
        $this->reportDate = $reportDate;
        $this->clientCode = $clientCode;
        $this->insertInvoiceID = $insertInvoiceID;
    }

    public function title(): string
    {
        return 'MisCellaneous';
    }

    public function failed(Throwable $exception): void
    {
        $invoice = Invoice::findOrFail($this->insertInvoiceID);
        $invoice->doc_status = "deleted";
        $invoice->save();

        \Log::channel('daily_queue_export')
            ->info('MisCellaneousExport')
            ->info($exception);
    }

    public function query()
    {
        return AmazonDateRangeReport::query()
            ->from('amazon_date_range_report as a')
            ->select(
                "a.account",
                "a.country",
                "a.paid_date",
                DB::raw("a.shipped_date AS dispatch_date"),
                DB::raw("a.settlement_id AS transaction_ID"),
                DB::raw("a.type AS transaction_type"),
                DB::raw("a.description AS description"),
                "a.order_id",
                DB::raw("a.order_type AS type"),
                "a.msku",
                "a.asin",
                DB::raw("a.product_name AS item_name"),
                "a.sku",
                DB::raw("a.supplier_type AS business_type"),
                DB::raw("a.supplier AS brand"),
                "a.marketplace",
                "a.fulfillment",
                "a.quantity",
                "a.currency",
                DB::raw("a.product_sales AS price"),
                DB::raw("a.shipping_credits AS shipping_charge_back"),
                DB::raw("a.gift_wrap_credits AS gift_wrap_charge_back"),
                "a.promotional_rebates",
                "a.cost_of_point",
                "a.tax",
                "a.marketplace_withheld_tax",
                DB::raw("a.selling_fees AS platform_fee"),
                "a.fba_fees",
                "a.other_transaction_fees",
                "a.other",
                "a.amazon_total",
                DB::raw("a.amazon_total AS total_amount"),
                DB::raw("(a.amazon_total * r.exchange_rate ) AS total_amount_HKD"),
                "r.exchange_rate",
            )
            ->leftJoin('exchange_rates as r', function ($join) {
                $join->on('a.currency', '=', 'r.base_currency');
                $join->on('a.report_date', '=', 'r.quoted_date');
                $join->where('r.active', 1);
            })
            ->where('a.active', 1)
            ->where('a.supplier', $this->clientCode)
            ->where('a.report_date', $this->reportDate)
            ->whereIn('a.type', ['FBA Customer Return Fee', 'Adjustment', 'other']);
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

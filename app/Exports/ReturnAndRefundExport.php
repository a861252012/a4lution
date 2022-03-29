<?php

namespace App\Exports;

use App\Models\AmazonDateRangeReport;
use App\Models\RmaRefundList;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Throwable;

class ReturnAndRefundExport implements
    WithTitle,
    FromCollection,
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
        return 'Return And Refund';
    }

    public function failed(Throwable $exception): void
    {
        \Log::channel('daily_queue_export')
            ->info('ReturnAndRefundExport')
            ->info($exception);
    }

    public function collection()
    {
        $amzQuery = AmazonDateRangeReport::from('amazon_date_range_report as d')
            ->select(
                DB::raw("'.' AS create_date"),
                DB::raw("d.order_id AS reference_number"),
                DB::raw("'' AS warehouse_order_number"),
                DB::raw("'' AS refund_reason"),
                DB::raw("'' AS platform_refund_reason"),
                DB::raw("'' AS paypal_refund_number"),
                DB::raw("'' AS refund_remark"),
                DB::raw("d.account AS account_name"),
                DB::raw("d.account AS account_short_name"),
                DB::raw("'' AS paid_date"),
                DB::raw("'' AS ship_date"),
                "d.country",
                DB::raw("d.marketplace AS region"),
                DB::raw("'' AS warehouse"),
                DB::raw("'' AS shipping_method"),
                DB::raw("'' AS cs_remark"),
                "sku",
                DB::raw("d.product_name AS item_name"),
                DB::raw("'' AS refund_qty"),
                DB::raw("d.supplier_type AS client_type"),
                DB::raw("d.supplier AS client_name"),
                DB::raw("'' AS paypal_transaction_number"),
                DB::raw("'' AS refund_type"),
                DB::raw("ABS(d.amazon_total) AS refund_amount"),
                DB::raw("(d.product_sales + d.tax) AS transaction_amount"),
                DB::raw("'' AS sale_amount"),
                "d.currency",
                DB::raw("ABS(d.amazon_total * r.exchange_rate) AS refund_amount_hkd"),
                DB::raw("'' AS buyer_id"),
                DB::raw("'' AS refund_date"),
                DB::raw("'已退款' AS refund_status"),
                DB::raw("'FBA退款' AS 'refund_method'"),
            )
            ->leftJoin('exchange_rates as r', function ($join) {
                $join->on('d.currency', '=', 'r.base_currency');
                $join->on(
                    DB::raw("DATE_FORMAT(d.report_date, '%Y%M')"),
                    '=',
                    DB::raw("DATE_FORMAT(r.quoted_date, '%Y%M')")
                );
                $join->where('r.active', 1);
            })
            ->wherein('d.type', ['Chargeback Refund', 'Refund'])
            ->where('d.fulfillment', 'Amazon')
            ->where('d.supplier', $this->clientCode)
            ->where('d.active', 1)
            ->where(DB::raw("DATE_FORMAT(d.report_date, '%Y%m')"), $this->reportDate);

        return RmaRefundList::from('rma_refund_list as a')
            ->select(
                "a.create_date",
                DB::raw("a.ref_no AS reference_number"),
                DB::raw("a.warehouse_ref_no AS warehouse_order_number"),
                DB::raw("a.reason AS refund_reason"),
                DB::raw("'' AS platform_refund_reason"),
                DB::raw("a.pay_ref_id AS paypal_refund_number"),
                DB::raw("a.note AS refund_remark"),
                DB::raw("a.user_account AS account_name"),
                DB::raw("a.user_account_name AS account_short_name"),
                "a.paid_date",
                DB::raw("a.warehouse_ship_date AS ship_date"),
                "a.country",
                DB::raw("a.site AS region"),
                DB::raw("a.warehous_id AS warehouse"),
                "a.shipping_method",
                DB::raw("a.customer_service_note AS cs_remark"),
                DB::raw("a.product_sku AS sku"),
                DB::raw("a.product_title AS item_name"),
                DB::raw("a.qty AS refund_qty"),
                DB::raw("a.pc_like AS client_type"),
                DB::raw("a.pc_name AS client_name"),
                DB::raw("a.pay_ref_id AS paypal_transaction_number"),
                DB::raw("a.refund_step AS refund_type"),
                DB::raw("ABS(a.amount_refund) AS refund_amount"),
                DB::raw("a.amount_paid AS transaction_amount"),
                DB::raw("a.amount_order AS sale_amount"),
                "a.currency",
                DB::raw("ABS(a.amount_refund * r.exchange_rate) AS refund_amount_hkd"),
                "a.buyer_id",
                "a.refund_date",
                DB::raw("a.status AS refund_status"),
                DB::raw("a.refund_step AS refund_method"),
            )
            ->leftJoin('exchange_rates as r', function ($join) {
                $join->on('a.currency', '=', 'r.base_currency');
                $join->on(
                    DB::raw("DATE_FORMAT(a.create_date, '%Y%M')"),
                    '=',
                    DB::raw("DATE_FORMAT(r.quoted_date, '%Y%M')")
                );
                $join->where('r.active', 1);
            })
            ->where(DB::raw("DATE_FORMAT(a.create_date,'%Y%m')"), $this->reportDate)
            ->where('a.product_sku', 'like', "{$this->clientCode}-%")
            ->where('a.shipping_method', '!=', 'AMAZONFBA')
            ->whereRaw("length(a.warehouse_ship_date) > ?", 0)
            ->unionAll($amzQuery)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Create Date',
            'Reference Number',
            'Warehouse Order Number',
            'Refund Reason',
            'Platform Refund Reason',
            'PayPal Refund Number',
            'Refund Remark',
            'Account Name',
            'Account Short Name',
            'Paid Date',
            'Ship Date',
            'Country',
            'Region',
            'Warehouse',
            'Shipping Method',
            'CS Remark',
            'SKU',
            'Item Name',
            'Refund Qty',
            'Client Type',
            'Client Name',
            'Paypal Transaction Number',
            'Refund Type',
            'Refund Amount',
            'Transaction Amount',
            'Sale Amount',
            'Currency',
            'Refund Amount (HKD)',
            'Buyer ID',
            'Refund Date',
            'Refund Status',
            'Refund Method',
        ];
    }

    public function map($row): array
    {
        return [
            [
                $row->create_date,
                $row->reference_number,
                $row->warehouse_order_number,
                $row->refund_reason,
                $row->platform_refund_reason,
                $row->paypal_refund_number,
                $row->refund_remark,
                $row->account_name,
                $row->account_short_name,
                $row->paid_date,
                $row->ship_date,
                $row->country,
                $row->region,
                $row->warehouse,
                $row->shipping_method,
                $row->cs_remark,
                $row->sku,
                $row->item_name,
                $row->refund_qty,
                $row->client_type,
                $row->client_name,
                $row->paypal_transaction_number,
                $row->refund_type,
                $row->refund_amount,
                $row->transaction_amount,
                $row->sale_amount,
                $row->currency,
                $row->refund_amount_hkd,
                $row->buyer_id,
                $row->refund_date,
                $row->refund_status,
                $row->refund_method,
            ]
        ];
    }
}

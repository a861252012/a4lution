<?php

namespace App\Exports;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Throwable;

class StorageFeeExport implements
    WithTitle,
    FromQuery,
    WithHeadings,
    withMapping,
    WithStrictNullComparison,
    WithEvents
{
    use RegistersEventListeners;

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
        return 'Storage Fee';
    }

    public function failed(Throwable $exception): void
    {
        \Log::channel('daily_queue_export')
            ->info('StorageFeeExport')
            ->info($exception);
    }

    public function query(): Builder
    {
        $firstQuery = DB::query()
            ->from("monthly_storage_fees as m")
            ->select(
                DB::raw("'1' AS order_seq"),
                DB::raw("m.account AS account"),
                DB::raw(" m.asin AS 'ASIN'"),
                "m.fnsku",
                "m.product_name",
                "m.fulfilment_center",
                "m.country_code",
                DB::raw("m.supplier_type AS type"),
                DB::raw("m.supplier AS client"),
                "m.longest_side",
                "m.median_side",
                DB::raw("m.shortest_side"),
                "m.measurement_units",
                "m.weight",
                "m.weight_units",
                "m.item_volume",
                "m.volume_units",
                "m.product_size_tier",
                "m.average_quantity_on_hand",
                "m.average_quantity_pending_removal",
                "m.total_item_volume_est",
                "m.month_of_charge",
                "m.storage_rate",
                "m.currency",
                "r.exchange_rate",
                "m.dangerous_goods_storage_type",
                "m.category",
                "m.eligible_for_discount",
                "m.qualified_for_discount",
                DB::raw("'Monthly Storage Fee' AS storage_fee_type"),
                DB::raw("m.monthly_storage_fee_est AS storage_fee"),
                DB::raw("(m.monthly_storage_fee_est * r.exchange_rate) AS storage_fee_hkd")
            )->leftJoin('exchange_rates as r', function ($join) {
                $join->on('m.report_date', '=', 'r.quoted_date')
                    ->on('m.currency', '=', 'r.base_currency')
                    ->where('r.active', 1);
            })
            ->where('m.supplier', $this->clientCode)
            ->where('m.report_date', $this->reportDate)
            ->where('m.active', 1);

        $secQuery = DB::query()
            ->from("long_term_storage_fees as t")
            ->select(
                DB::raw("'2' AS order_seq"),
                "t.account",
                DB::raw("t.asin AS 'ASIN'"),
                "t.fnsku",
                "t.product_name",
                DB::raw("'' AS fulfilment_center"),
                DB::raw("t.country AS country_code"),
                DB::raw("t.supplier_type AS type"),
                DB::raw("t.supplier AS client"),
                DB::raw("'' AS longest_side"),
                DB::raw("'' AS median_side"),
                DB::raw("'' AS shortest_side"),
                DB::raw("'' AS measurement_units"),
                DB::raw("'' AS weight"),
                DB::raw("'' AS weight_units"),
                DB::raw("'' AS item_volume"),
                DB::raw("'' AS volume_units"),
                DB::raw("'' AS product_size_tier"),
                DB::raw("'' AS average_quantity_on_hand"),
                DB::raw("'' AS average_quantity_pending_removal"),
                DB::raw("'' AS total_item_volume_est"),
                DB::raw("t.snapshot_date AS month_of_charge"),
                DB::raw("'' AS storage_rate"),
                "t.currency",
                "r.exchange_rate",
                DB::raw("'' AS dangerous_goods_storage_type"),
                DB::raw("'' AS category"),
                DB::raw("'' AS eligible_for_discount"),
                DB::raw("'' AS qualified_for_discount"),
                DB::raw("'' AS storage_fee_type"),
                DB::raw("t.12_mo_long_terms_storage_fee AS storage_fee"),
                DB::raw("(t.12_mo_long_terms_storage_fee * r.exchange_rate) AS storage_fee_hkd")
            )->leftJoin('exchange_rates as r', function ($join) {
                $join->on('t.report_date', '=', 'r.quoted_date')
                    ->on('t.currency', '=', 'r.base_currency')
                    ->where('r.active', 1);
            })
            ->where('t.supplier', $this->clientCode)
            ->where('t.report_date', $this->reportDate)
            ->where('t.active', 1);


        $thirdQuery = DB::query()
            ->from("wfs_storage_fees as w")
            ->selectRaw("
            '3' AS order_seq,
            '' AS 'account',
            w.walmart_item_id AS 'ASIN',
            w.vendor_sku AS 'fnsku',
            w.item_name AS 'product_name',
            '' AS 'fulfilment_center',
            '' AS 'country_code',
            w.supplier_type AS 'type',
            w.supplier AS 'client',
            '' AS 'longest_side',
            '' AS 'median_side',
            '' AS 'shortest_side',
            '' AS 'measurement_units',
            '' AS 'weight',
            '' AS 'weight_units',
            '' AS 'item_volume',
            '' AS 'volume_units',
            '' AS 'product_size_tier',
            '' AS 'average_quantity_on_hand',
            '' AS 'average_quantity_pending_removal',
            '' AS 'total_item_volume_est',
            w.report_date AS 'month_of_charge',
            '' AS 'storage_rate',
            'USD' AS 'currency',
            r.exchange_rate AS 'exchange_rate',
            '' AS 'dangerous_goods_storage_type',
            '' AS 'category',
            '' AS 'eligible_for_discount',
            '' AS 'qualified_for_discount',
            'Walmart Storage Fee' AS 'storage_fee_type',
            w.storage_fee_for_selected_time_period AS 'storage_fee',
            (w.storage_fee_for_selected_time_period * r.exchange_rate) AS 'storage_fee_hkd'
            ")->leftJoin('exchange_rates as r', function ($join) {
                $join->on('w.report_date', '=', 'r.quoted_date')
                    ->where('r.base_currency', 'USD')
                    ->where('r.active', 1);
            })->where('w.supplier', $this->clientCode)
            ->where('w.report_date', $this->reportDate)
            ->where('w.active', 1);

        $subQuery = $firstQuery->unionAll($secQuery)
            ->unionAll($thirdQuery);


        return DB::table(DB::raw("({$subQuery->toSql()}) as x"))
            ->orderByRaw('x.order_seq ,x.account, x.asin')
            ->mergeBindings($subQuery);
    }

    public function headings(): array
    {
        return [
            'Account',
            'ASIN',
            'fnsku',
            'product-name',
            'Fulfilment center',
            'Country code',
            'Type',
            'Client',
            'Longest side',
            'Median side',
            'Shortest side',
            'Measurement units',
            'weight',
            'Weight units',
            'Item volume',
            'Volume units',
            'Product size tier',
            'Average quantity on hand',
            'Average quantity pending removal',
            'Total item volume (est)',
            'Month of charge',
            'Storage rate',
            'currency',
            'Exchange Rate',
            'dangerous-goods-storage-type',
            'category',
            'eligible-for-discount',
            'qualified-for-discount',
            'Storage Fee Type',
            'Storage Fee',
            'Storage fee (HKD)'
        ];
    }

    public function map($row): array
    {
        return [
            [
                $row->account,
                $row->ASIN,
                $row->fnsku,
                $row->product_name,
                $row->fulfilment_center,
                $row->country_code,
                $row->type,
                $row->client,
                $row->longest_side,
                $row->median_side,
                $row->shortest_side,
                $row->measurement_units,
                $row->weight,
                $row->weight_units,
                $row->item_volume,
                $row->volume_units,
                $row->product_size_tier,
                $row->average_quantity_on_hand,
                $row->average_quantity_pending_removal,
                $row->total_item_volume_est,
                $row->month_of_charge,
                $row->storage_rate,
                $row->currency,
                $row->exchange_rate,
                $row->dangerous_goods_storage_type,
                $row->category,
                $row->eligible_for_discount,
                $row->qualified_for_discount,
                $row->storage_fee_type,
                $row->storage_fee,
                $row->storage_fee_hkd
            ]
        ];
    }
}

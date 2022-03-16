<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\FirstMileShipmentFee;
use Throwable;

class FBADateExport implements
    WithTitle,
    FromQuery,
    WithMapping,
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

    public function query()
    {
        return FirstMileShipmentFee::select(
            DB::raw("fulfillment_center as 'country'"),
            DB::raw("fba_shipment as 'shipment_id'"),
            DB::raw("COUNT(DISTINCT ids_sku) AS 'sku'"),
            DB::raw("SUM(shipped) as 'shipped_qty'")
        )
            ->where('active', 1)
            ->where('report_date', $this->reportDate)
            ->where('client_code', $this->clientCode)
            ->groupBy(['fulfillment_center', 'fba_shipment']);
    }

    public function failed(Throwable $exception): void
    {
        \Log::channel('daily_queue_export')
            ->info('FBADateExport')
            ->info($exception);
    }

    public function map($row): array
    {
        return [
            [
                $row->combine,
                $row->shipped_qty,
            ]
        ];
    }

    public function prepareRows($rows)
    {
        foreach ($rows as $v) {
            $v->combine .= "Country: {$v->country}";
            $v->combine .= " Shipment ID: {$v->shipment_id}";
            $v->combine .= " SKU: {$v->sku}";
            $v->combine .= " Shipped Qty: {$v->shipped_qty}";

            $v->shipped_qty = "$ {$v->shipped_qty}";
        }

        return $rows;
    }

    public function title(): string
    {
        return 'FBA Date';
    }
}


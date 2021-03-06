<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Models\PlatformAdFee;
use Throwable;

class ADSPromotionExport implements
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
        return 'ADS and Promotion';
    }

    public function failed(Throwable $exception): void
    {
        \Log::channel('daily_queue_export')
            ->info('ADSPromotionExport')
            ->info($exception);
    }

    public function query()
    {
        return PlatformAdFee::query()
            ->select(
                "platform_ad_fees.platform",
                "platform_ad_fees.account",
                "platform_ad_fees.campagin_type",
                "platform_ad_fees.campagin",
                "platform_ad_fees.currency",
                "platform_ad_fees.Impressions",
                "platform_ad_fees.clicks",
                "platform_ad_fees.ctr",
                "platform_ad_fees.spendings",
                DB::raw("(platform_ad_fees.spendings * r.exchange_rate) AS spendings_hkd"),
                "platform_ad_fees.cpc",
                "platform_ad_fees.sales_qty",
                "platform_ad_fees.sales_amount",
                DB::raw("(platform_ad_fees.sales_amount * r.exchange_rate) AS sales_amount_hkd"),
                "platform_ad_fees.acos",
                "platform_ad_fees.exchange_rate"
            )
            ->leftJoin('exchange_rates as r', function ($join) {
                $join->on('platform_ad_fees.report_date', '=', 'r.quoted_date');
                $join->on('platform_ad_fees.currency', '=', 'r.base_currency');
                $join->where('r.active', 1);
            })
            ->where('platform_ad_fees.active', 1)
            ->where('platform_ad_fees.client_code', $this->clientCode)
            ->where('platform_ad_fees.report_date', $this->reportDate);
    }

    public function headings(): array
    {
        return [
            'Platform',
            'Account',
            'Campagin Type',
            'Campagin',
            'Currency',
            'Impressions',
            'Clicks',
            'CTR',
            'Spendings',
            'Spendings (HKD)',
            'CPC',
            'Sales Qty',
            'Sales Amount',
            'Sales Amount (HKD)',
            'ACOS',
            'Exchange Rate'
        ];
    }

    public function map($row): array
    {
        return [
            [
                $row->platform,
                $row->account,
                $row->campagin_type,
                $row->campagin,
                $row->currency,
                $row->Impressions,
                $row->clicks,
                $row->ctr,
                $row->spendings,
                $row->spendings_hkd,
                $row->cpc,
                $row->sales_qty,
                $row->sales_amount,
                $row->sales_amount_hkd,
                $row->acos,
                $row->exchange_rate,
            ]
        ];
    }
}

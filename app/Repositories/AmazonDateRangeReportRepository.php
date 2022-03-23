<?php

namespace App\Repositories;

use App\Models\AmazonDateRangeReport;
use Illuminate\Support\Facades\DB;

class AmazonDateRangeReportRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new AmazonDateRangeReport);
    }

    public function getAccountMarketingAndPromotion(
        string $reportDate,
        string $clientCode,
        array $sellerAccount,
        bool   $isAvolution
    ): float {
        return(float)$this->model
            ->selectRaw("SUM(amazon_date_range_report.amazon_total * exchange_rates.exchange_rate) AS 'promotion'")
            ->leftJoin('exchange_rates', function ($join) {
                $join->on('amazon_date_range_report.report_date', 'exchange_rates.quoted_date')
                    ->on('amazon_date_range_report.currency', 'exchange_rates.base_currency')
                    ->where('exchange_rates.active', 1);
            })
            ->where('amazon_date_range_report.report_date', $reportDate)
            ->where('amazon_date_range_report.supplier', $clientCode)
            ->where('amazon_date_range_report.active', 1)
            ->when($isAvolution, function ($q) use ($sellerAccount) {
                return $q->whereIn('amazon_date_range_report.account', $sellerAccount);
            }, function ($q) use ($sellerAccount) {
                return $q->whereNotIn('amazon_date_range_report.account', $sellerAccount);
            })
            ->whereIn(
                'amazon_date_range_report.type',
                [
                    'Early Reviewer Program',
                    'Lightning Deal Fee',
                    'Coupons'
                ]
            )
            ->value('promotion');
    }

    public function getAccountMiscellaneous(
        string $reportDate,
        string $clientCode,
        array $sellerAccount,
        bool   $isAvolution
    ) {
        return $this->model
            ->selectRaw("SUM(amazon_date_range_report.amazon_total * exchange_rates.exchange_rate) AS 'Miscellaneous'")
            ->leftJoin('exchange_rates', function ($join) {
                $join->on('amazon_date_range_report.report_date', 'exchange_rates.quoted_date');
                $join->on('amazon_date_range_report.currency', 'exchange_rates.base_currency');
                $join->where('exchange_rates.active', 1);
            })
            ->where('amazon_date_range_report.report_date', $reportDate)
            ->where('amazon_date_range_report.supplier', $clientCode)
            ->when($isAvolution, function ($q) use ($sellerAccount) {
                return $q->whereIn('amazon_date_range_report.account', $sellerAccount);
            }, function ($q) use ($sellerAccount) {
                return $q->whereNotIn('amazon_date_range_report.account', $sellerAccount);
            })
            ->where('amazon_date_range_report.active', 1)
            ->whereNotIn(
                'amazon_date_range_report.type',
                [
                    'Order',
                    'Refund',
                    'Service Fee',
                    'Transfer',
                    'Other FBA Inventory Fee',
                    'Coupons'
                ]
            )
            ->first();
    }

    public function getTotalAmount(
        string $reportDate,
        string $clientCode,
        array  $sellerAccount,
        bool   $isAvolution
    ): float {
        return (float)$this->model
            ->selectRaw(
                'SUM(ABS(amazon_date_range_report.amazon_total) * exchange_rates.exchange_rate) AS "amazon_total_hkd"'
            )
            ->leftJoin('exchange_rates', function ($join) {
                $join->on('amazon_date_range_report.currency', '=', 'exchange_rates.base_currency')
                    ->where('exchange_rates.active', 1)
                    ->where(
                        DB::raw("DATE_FORMAT(amazon_date_range_report.report_date, '%Y%M')"),
                        '=',
                        DB::raw("DATE_FORMAT(exchange_rates.quoted_date, '%Y%M')")
                    );
            })
            ->where('amazon_date_range_report.active', 1)
            ->where('amazon_date_range_report.type', 'Refund')
            ->where('amazon_date_range_report.fulfillment', 'Amazon')
            ->where('amazon_date_range_report.supplier', $clientCode)
            ->whereRaw(
                "DATE_FORMAT(amazon_date_range_report.report_date, '%Y%m') = ?",
                date("Ym", strtotime($reportDate))
            )
            ->when($isAvolution, function ($q) use ($sellerAccount) {
                return $q->whereIn('amazon_date_range_report.account', $sellerAccount);
            }, function ($q) use ($sellerAccount) {
                return $q->whereNotIn('amazon_date_range_report.account', $sellerAccount);
            })
            ->value('amazon_total_hkd');
    }
}

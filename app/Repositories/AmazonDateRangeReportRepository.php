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
            ->when($isAvolution, function ($q) {
                return $q->whereIn('amazon_date_range_report.account', function ($q) {
                    $q->from('seller_accounts')
                        ->selectRaw('DISTINCT asinking_account_name')
                        ->where('is_a4_account', 1)
                        ->where('active', 1)
                        ->get();
                });
            }, function ($q) {
                return $q->whereNotIn('amazon_date_range_report.account', function ($q) {
                    $q->from('seller_accounts')
                        ->selectRaw('DISTINCT asinking_account_name')
                        ->where('is_a4_account', 1)
                        ->where('active', 1)
                        ->get();
                });
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
            ->when($isAvolution, function ($q) {
                return $q->whereIn('amazon_date_range_report.account', function ($q) {
                    $q->from('seller_accounts')
                        ->selectRaw('DISTINCT asinking_account_name')
                        ->where('is_a4_account', 1)
                        ->where('active', 1)
                        ->get();
                });
            }, function ($q) {
                return $q->whereNotIn('amazon_date_range_report.account', function ($q) {
                    $q->from('seller_accounts')
                        ->selectRaw('DISTINCT asinking_account_name')
                        ->where('is_a4_account', 1)
                        ->where('active', 1)
                        ->get();
                });
            })
            ->where('amazon_date_range_report.active', 1)
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
            )
            ->first();
    }

    public function getTotalAmount(
        string $reportDate,
        string $clientCode,
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
            ->when($isAvolution, function ($q) {
                return $q->whereIn('amazon_date_range_report.account', function ($query) {
                    $query->from('seller_accounts')
                        ->selectRaw('DISTINCT asinking_account_name')
                        ->where('is_a4_account', 1)
                        ->where('active', 1)
                        ->get();
                });
            }, function ($q) {
                return $q->whereNotIn('amazon_date_range_report.account', function ($query) {
                    $query->from('seller_accounts')
                        ->selectRaw('DISTINCT asinking_account_name')
                        ->where('is_a4_account', 1)
                        ->where('active', 1)
                        ->get();
                });
            })
            ->value('amazon_total_hkd');
    }
}

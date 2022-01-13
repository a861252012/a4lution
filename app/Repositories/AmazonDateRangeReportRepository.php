<?php

namespace App\Repositories;

use App\Models\AmazonDateRangeReport;

class AmazonDateRangeReportRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new AmazonDateRangeReport);
    }

    public function getAccountMarketingAndPromotion(
        string $reportDate,
        string $clientCode,
        string $supplierName,
        bool   $isCrafter
    ) {
        return $this->model
            ->query()
            ->selectRaw("SUM(amazon_date_range_report.amazon_total * exchange_rates.exchange_rate) AS 'Miscellaneous'")
            ->leftJoin('exchange_rates', function ($join) {
                $join->on('amazon_date_range_report.report_date', 'exchange_rates.quoted_date');
                $join->on('amazon_date_range_report.currency', 'exchange_rates.base_currency');
                $join->where('exchange_rates.active', 1);
            })
            ->where('amazon_date_range_report.report_date', $reportDate)
            ->where('amazon_date_range_report.supplier', $clientCode)
            ->when($isCrafter, function ($q) use ($supplierName) {
                return $q->where('amazon_date_range_report.account', $supplierName);
            }, function ($q) use ($supplierName) {
                return $q->where('amazon_date_range_report.account', '!=', $supplierName);
            })
            ->where('amazon_date_range_report.active', 1)
            ->whereNotIn(
                'amazon_date_range_report.type',
                [
                    'Early Reviewer Program',
                    'Lightning Deal Fee',
                    'Coupons'
                ]
            )
            ->get();
    }

    public function getAccountMiscellaneous(
        string $reportDate,
        string $clientCode,
        string $supplierName,
        bool   $isCrafter
    ) {
        return $this->model
            ->query()
            ->selectRaw("SUM(amazon_date_range_report.amazon_total * exchange_rates.exchange_rate) AS 'Miscellaneous'")
            ->leftJoin('exchange_rates', function ($join) {
                $join->on('amazon_date_range_report.report_date', 'exchange_rates.quoted_date');
                $join->on('amazon_date_range_report.currency', 'exchange_rates.base_currency');
                $join->where('exchange_rates.active', 1);
            })
            ->where('amazon_date_range_report.report_date', $reportDate)
            ->where('amazon_date_range_report.supplier', $clientCode)
            ->when($isCrafter, function ($q) use ($supplierName) {
                return $q->where('amazon_date_range_report.account', $supplierName);
            }, function ($q) use ($supplierName) {
                return $q->where('amazon_date_range_report.account', '!=', $supplierName);
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
            ->get();
    }
}

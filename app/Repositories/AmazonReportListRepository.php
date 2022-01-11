<?php


namespace App\Repositories;

use App\Models\AmazonDateRangeReport;
use App\Models\AmazonReportList;
use Illuminate\Support\Facades\DB;

class AmazonReportListRepository extends BaseRepository
{
    protected $amazonReportList;

    public function __construct()
    {
        parent::__construct(new AmazonReportList);
    }

    public function getAccountMarketingAndPromotion(string $reportDate, string $clientCode, string $supplierName, bool $isCrafter)
    {
        $sql = "SELECT
    SUM(a.amazon_total * r.exchange_rate) AS 'Miscellaneous'
FROM
    amazon_date_range_report a
        LEFT JOIN
    exchange_rates r ON a.report_date = r.quoted_date
        AND a.currency = r.base_currency
        AND r.active = 1
WHERE
    a.report_date = '{$reportDate}'
        AND a.supplier = '{$clientCode}'
        AND a.active=1
        AND a.`type` IN ('Early Reviewer Program' , 'Lightning Deal Fee', 'Coupons')";

        $isCrafter ? $sql .= "AND a.account = '{$supplierName}'" : $sql .= "AND a.account != '{$supplierName}'";

        return DB::select($sql);
    }

    public function getAccountMiscellaneous(
        string $reportDate,
        string $clientCode,
        string $supplierName,
        bool   $isCrafter
    ) {
        return AmazonDateRangeReport::query()
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
            ->whereNotIn('amazon_date_range_report.type', ['Order', 'Refund'])
            ->get();
    }
}

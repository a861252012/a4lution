<?php


namespace App\Repositories;

use App\Models\AmazonReportList;
use Illuminate\Support\Facades\DB;

class AmazonReportListRepository
{
    protected $amazonReportList;

    public function __construct()
    {
    }

    public function insertData(array $data)
    {
        return DB::transaction(function () use ($data) {
            return AmazonReportList::insert($data);
        });
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
WHERE
    a.report_date = '{$reportDate}'
        AND a.supplier = '{$clientCode}'
        AND a.active=1
        AND a.`type` IN ('Early Reviewer Program' , 'Lightning Deal Fee', 'Coupons')";

        $isCrafter ? $sql .= "AND a.account = '{$supplierName}'" : $sql .= "AND a.account != '{$supplierName}'";

        return DB::select($sql);
    }

    public function getAccountMiscellaneous(string $reportDate, string $clientCode, string $supplierName, bool $isCrafter)
    {
        $sql = "SELECT
    SUM(a.amazon_total * r.exchange_rate) AS 'Miscellaneous'
FROM
    amazon_date_range_report a
        LEFT JOIN
    exchange_rates r ON a.report_date = r.quoted_date
        AND a.currency = r.base_currency
WHERE
    a.report_date = '{$reportDate}'
        AND a.supplier = '{$clientCode}'
        AND a.`type` IN ('FBA Customer Return Fee','Adjustment','other')
        AND a.active = 1";

        $isCrafter ? $sql .= " AND a.account = '{$supplierName}'" : $sql .= " AND a.account != '{$supplierName}'";

        return DB::select($sql);
    }
}

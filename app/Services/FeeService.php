<?php

namespace App\Services;

use App\Models\BillingStatement;
use App\Constants\ImportTitleConstant;
use Maatwebsite\Excel\HeadingRowImport;
use App\Repositories\ExchangeRateRepository;
use Symfony\Component\HttpFoundation\Response;

class FeeService
{
    // 在上傳檔案前做驗證
    public function validate($reportDate)
    {
        // 1. exchange rate
        $exchangeRate = (new ExchangeRateRepository)->getByQuotedDate($reportDate->toDateString());

        if ($exchangeRate->isEmpty()) {
            abort(Response::HTTP_FORBIDDEN, 'Currency Exchange Rate Not Found Error');
        }

        // 2. 查詢該月是否已結算,如已結算則不得再更改
        $hasMonthlyBilling = BillingStatement::query()
            ->active()
            ->where('report_date', $reportDate->toDateString())
            ->exists();

        if ($hasMonthlyBilling) {
            abort(
                Response::HTTP_FORBIDDEN, 
                "The {$reportDate->format('Y-m')} sales summary was generated.
                    Please delete the sales summary and reupload it."
            );
        }
    }

    public function checkExcelHeader($file, $feeType)
    {
        $headings = (new HeadingRowImport)->toCollection($file) ?
            (new HeadingRowImport)->toCollection($file)->collapse()->collapse()->filter() : null;

        if (!$headings) {
            abort(Response::HTTP_FORBIDDEN, "Title unmatched");
        }

        switch ($feeType) {
            case 'platform_ad_fees':
                $diff = $headings->diff(ImportTitleConstant::PLATFORM_AD) ?? null;
                break;
            case 'amazon_date_range':
                $diff = $headings->diff(ImportTitleConstant::AMZ_DATE_RANGE) ?? null;
                break;
            case 'long_term_storage_fees':
                $diff = $headings->diff(ImportTitleConstant::LONG_TERM) ?? null;
                break;
            case 'monthly_storage_fees':
                $diff = $headings->diff(ImportTitleConstant::MONTHLY_STORAGE) ?? null;
                break;
            case 'first_mile_shipment_fees':
                $diff = $headings->diff(ImportTitleConstant::FIRST_MILE_SHIPMENT) ?? null;
                break;

            default:
                $diff = null;
                break;
        }

        if ($diff->isNotEmpty()) {
            abort(Response::HTTP_FORBIDDEN, "Title : [{$diff->implode(', ')}] unmatched");
        }
    }
}

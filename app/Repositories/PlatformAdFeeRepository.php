<?php

namespace App\Repositories;

use App\Models\PlatformAdFee;

class PlatformAdFeeRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new PlatformAdFee());
    }

    public function getAccountAd(
        string $reportDate,
        string $clientCode,
        bool   $isAvolution
    ): float {
        return (float)$this->model
            ->selectRaw("SUM((platform_ad_fees.spendings * exchange_rates.exchange_rate)) AS 'ad'")
            ->leftJoin('exchange_rates', function ($join) {
                $join->on('platform_ad_fees.report_date', '=', 'exchange_rates.quoted_date')
                    ->on('platform_ad_fees.currency', '=', 'exchange_rates.base_currency')
                    ->where('exchange_rates.active', 1);
            })
            ->where('platform_ad_fees.active', 1)
            ->where('platform_ad_fees.client_code', $clientCode)
            ->where('platform_ad_fees.report_date', $reportDate)
            ->when($isAvolution, function ($q) {
                return $q->whereIn('platform_ad_fees.account', function ($query) {
                    $query->from('seller_accounts')
                        ->selectRaw('DISTINCT asinking_account_name')
                        ->where('is_a4_account', 1)
                        ->where('active', 1)
                        ->get();
                });
            }, function ($q) {
                return $q->whereNotIn('platform_ad_fees.account', function ($query) {
                    $query->from('seller_accounts')
                        ->selectRaw('DISTINCT asinking_account_name')
                        ->where('is_a4_account', 1)
                        ->where('active', 1)
                        ->get();
                });
            })
            ->groupBy('platform_ad_fees.client_code')
            ->value('ad');
    }
}

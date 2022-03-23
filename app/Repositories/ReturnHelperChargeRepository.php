<?php

namespace App\Repositories;

use App\Models\ReturnHelperCharge;

class ReturnHelperChargeRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new ReturnHelperCharge);
    }

    public function getSumOfAmount(
        string $reportDate,
        string $clientCode
    ): float {
        return (float)$this->model
            ->selectRaw("sum(abs(return_helper_charges.amount) * exchange_rates.exchange_rate) AS 'amount_hkd'")
            ->leftJoin('exchange_rates', function ($join) {
                $join->on('return_helper_charges.report_date', '=', 'exchange_rates.quoted_date')
                    ->on('return_helper_charges.currency_code', '=', 'exchange_rates.base_currency')
                    ->where('exchange_rates.active', 1);
            })
            ->where('return_helper_charges.active', 1)
            ->where('return_helper_charges.report_date', $reportDate)
            ->where('return_helper_charges.supplier', $clientCode)
            ->value('amount_hkd');
    }
}

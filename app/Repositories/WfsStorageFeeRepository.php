<?php

namespace App\Repositories;

use App\Models\WfsStorageFee;

class WfsStorageFeeRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new WfsStorageFee);
    }

    public function getStorageFee(
        string $reportDate,
        string $clientCode
    ): float {
        return (float)$this->model
            ->selectRaw('sum(wfs_storage_fees.storage_fee_for_selected_time_period * exchange_rates.exchange_rate) 
            AS storage_fee')
            ->join('exchange_rates', function ($join) {
                $join->on('wfs_storage_fees.report_date', '=', 'exchange_rates.quoted_date')
                    ->where('exchange_rates.base_currency', 'USD')
                    ->where('exchange_rates.active', 1);
            })
            ->where('wfs_storage_fees.supplier', $clientCode)
            ->where('wfs_storage_fees.report_date', $reportDate)
            ->where('wfs_storage_fees.active', 1)
            ->value('storage_fee');
    }
}

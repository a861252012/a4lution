<?php

namespace App\Repositories;

use App\Models\WfsStorageFee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    public function getSearchResult(
        string $reportDate = null,
        string $supplier = null
    ) {
        return $this->model
            ->select(
                'wfs_storage_fees.report_date',
                'wfs_storage_fees.supplier',
                'wfs_storage_fees.vendor_sku',
                'wfs_storage_fees.storage_fee_for_selected_time_period',
                DB::raw('round(wfs_storage_fees.storage_fee_for_selected_time_period * exchange_rates.exchange_rate, 4)
                as storage_fee_hkd'),
                'wfs_storage_fees.created_at',
            )
            ->join('exchange_rates', function ($join) {
                $join->on('wfs_storage_fees.report_date', '=', 'exchange_rates.quoted_date')
                    ->on('exchange_rates.base_currency', 'wfs_storage_fees.currency_code')
                    ->where('exchange_rates.active', 1);
            })
            ->when(
                $reportDate,
                fn ($q) => $q->where(
                    'wfs_storage_fees.report_date',
                    Carbon::parse($reportDate)->firstOfMonth()->toDateString()
                )
            )
            ->when($supplier, fn ($q) => $q->where('wfs_storage_fees.supplier', $supplier))
            ->where('wfs_storage_fees.active', 1)
            ->paginate();
    }
}

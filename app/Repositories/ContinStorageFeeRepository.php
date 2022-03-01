<?php

namespace App\Repositories;

use App\Models\ContinStorageFee;
use Illuminate\Support\Facades\DB;

class ContinStorageFeeRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new ContinStorageFee);
    }

    public function getAccountRefund(
        string $reportDate,
        string $clientCode
    ): float {
        $subQuery = DB::query()->from("contin_storage_fees")
            ->selectRaw("(contin_storage_fees.amount * exchange_rates.exchange_rate) AS 'storage_fee_hkd'")
            ->leftJoin('exchange_rates', function ($join) {
                $join->on('contin_storage_fees.report_date', '=', 'exchange_rates.quoted_date')
                    ->on('contin_storage_fees.currency', '=', 'exchange_rates.base_currency');
            })
            ->where('contin_storage_fees.supplier', $clientCode)
            ->where('contin_storage_fees.report_date', $reportDate)
            ->where('contin_storage_fees.active', 1)
            ->where('exchange_rates.active', 1);

        return (float)$this->model->selectRaw('SUM(x.storage_fee_hkd) as storage_fee_hkd')
            ->from(DB::raw(' ( ' . $subQuery->toSql() . ' ) AS x'))
            ->mergeBindings($subQuery)
            ->value('storage_fee_hkd');
    }
}

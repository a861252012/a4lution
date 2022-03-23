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
            ->selectRaw('sum(abs(amount)) as sum')
            ->active()
            ->where('report_date', $reportDate)
            ->where('supplier', $clientCode)
            ->value('sum');
    }
}

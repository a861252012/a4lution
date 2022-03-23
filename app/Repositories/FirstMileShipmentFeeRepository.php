<?php


namespace App\Repositories;

use App\Models\FirstMileShipmentFee;

class FirstMileShipmentFeeRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new FirstMileShipmentFee);
    }

    public function getSumOfTotalValue(
        string $reportDate,
        string $clientCode
    ): float {
        return (float)$this->model
            ->selectRaw('sum(total) as total')
            ->active()
            ->where('report_date', $reportDate)
            ->where('client_code', $clientCode)
            ->value('total');
    }
}

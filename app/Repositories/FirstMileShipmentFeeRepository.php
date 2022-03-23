<?php


namespace App\Repositories;

use App\Models\FirstMileShipmentFee;
use Illuminate\Support\Facades\DB;

class FirstMileShipmentFeeRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new FirstMileShipmentFee);
    }

    public function getSumOfAmountValue(
        string $reportDate,
        string $clientCode
    ): float {
        return DB::query()->fromSub(function ($query) use ($clientCode, $reportDate) {
            $query->from('first_mile_shipment_fees')
                ->selectRaw('total AS unit_price')
                ->where('report_date', $reportDate)
                ->where('client_code', $clientCode)
                ->where('active', 1)
                ->groupBy(['fulfillment_center', 'fba_shipment']);
        }, 'x')->selectRaw('SUM(x.unit_price) as sum')->value('sum');
    }
}

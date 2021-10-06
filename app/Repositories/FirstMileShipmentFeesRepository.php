<?php


namespace App\Repositories;

use App\Models\FirstMileShipmentFees;
use Illuminate\Support\Facades\DB;

class FirstMileShipmentFeesRepository
{

    public function __construct()
    {
    }

    public function getFbaStorageFeeInvoice(string $reportDate, string $clientCode)
    {
        $sql = "SELECT
    f.fba_shipment, f.total
FROM
    first_mile_shipment_fees f
WHERE
    f.active = 1
        AND f.report_date = '{$reportDate}'
        AND f.client_code = '{$clientCode}'
GROUP BY f.fulfillment_center , f.fba_shipment";

        return DB::select($sql);
    }
}

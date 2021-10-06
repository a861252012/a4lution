<?php

namespace App\Imports;

use App\Models\FirstMileShipmentFees;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class FirstMileShipmentFeesImport implements ToModel, WithHeadingRow, WithBatchInserts
{
    private $rows = 0;

    public function model(array $row)
    {
        ++$this->rows;

        return new FirstMileShipmentFees([
            'client_code' => $row['client_code'],
            'ids_sku' => $row['ids_sku'],
            'title' => $row['title'],
            'asin' => $row['asin'],
            'fnsku' => $row['fnsku'],
            'external_id' => $row['external_id'],
            'condition' => $row['condition'],
            'who_will_prep' => $row['who_will_prep'],
            'prep_type' => $row['prep_type'],
            'who_will_label' => $row['who_will_label'],
            'shipped' => $row['shipped'],
            'fba_shipment' => $row['fba_shipment'],
            'shipment_type' => $row['shipment_type'],
            'date' => $row['date'],
            'account' => $row['account'],
            'ship_from' => $row['ship_from'],
            'first_mile' => $row['first_mile'],
            'last_mile_est_orig' => $row['last_mile_apc_original_currency_estimated'],
            'last_mile_act_orig' => $row['last_mile_apc_original_currency_actual'],
            'shipment_remark' => $row['shipment_remark'],
            'currency_last_mile' => $row['currency_last_mile'],
            'exchange_rate' => $row['exchange_rate'],
            'total' => $row['total'],
            'is_payment_settled' => $row['is_payment_settled'],
            'remark' => $row['remark'],
//            'upload_id' => 1,//TODO
            'report_date' => date('Y-m-d'),
            'active' => 1,
//            'created_at' => Carbon::now(),
            'created_at' => date('Y-m-d h:i:s'),
            'created_by' => 2,//TODO
//            'updated_at' => Carbon::now(),
//            'updated_at' => date('Y-m-d h:i:s'),
//            'updated_by' => 2,
            'fulfillment_center' => $row['account'] ? substr($row['account'], -2) : null
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }
}

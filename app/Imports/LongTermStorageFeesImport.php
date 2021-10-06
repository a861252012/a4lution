<?php

namespace App\Imports;

use App\Models\AmazonDateRangeReport;
use App\Models\LongTermStorageFees;
use App\Models\PlatformAdFees;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class LongTermStorageFeesImport implements ToModel, WithHeadingRow, WithBatchInserts, WithCalculatedFormulas
{

    private $rows = 0;

    public function model(array $row)
    {
        ++$this->rows;

        return new LongTermStorageFees([
            'account' => $row['account'],
            'snapshot_date' => $row['snapshot_date'],
            'sku' => $row['sku'],
            'fnsku' => $row['fnsku'],
            'asin' => $row['asin'],
            'product_name' => $row['product_name'],
            'supplier_type' => $row['supplier_type'],
            'supplier' => $row['supplier'],
            'condition' => $row['condition'],
            'qty_charged_12_mo_long_term_storage_fee' => $row['qty_charged_12_mo_long_term_storage_fee'],
            'per_unit_volume' => $row['per_unit_volume'],
            'currency' => $row['currency'],
            '12_mo_long_terms_storage_fee' => $row['12_mo_long_terms_storage_fee'],
            'hkd' => $row['hkd'],
            'hkd_rate' => $row['hkd_rate'],
            'qty_charged_6_mo_long_term_storage_fee' => $row['qty_charged_6_mo_long_term_storage_fee'],
            '6_mo_long_terms_storage_fee' => $row['6_mo_long_terms_storage_fee'],
            'volume_unit' => $row['volume_unit'],
            'country' => $row['country'],
            'enrolled_in_small_and_light' => $row['enrolled_in_small_and_light'],
            'upload_id' => 1,//TODO
            'report_date' => date('Y-m-d'),
            'active' => 1,
            'created_at' => date('Y-m-d h:i:s'),
            'created_by' => 2,//TODO
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_by' => 2,//TODO
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

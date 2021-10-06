<?php

namespace App\Imports;

use App\Models\MonthlyStorageFees;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class MonthlyStorageFeesImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithCalculatedFormulas, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    private $rows = 0;

    public function model(array $row)
    {
        ++$this->rows;

        return new MonthlyStorageFees([
            'account' => $row['account'],
            'asin' => $row['asin'],
            'fnsku' => $row['fnsku'],
            'product_name' => $row['product_name'],
            'fulfilment_center' => $row['fulfilment_center'],
            'country_code' => $row['country_code'],
            'supplier_type' => $row['supplier_type'],
            'supplier' => $row['supplier'],
            'longest_side' => $row['longest_side'],
            'median_side' => $row['median_side'],
            'shortest_side' => $row['shortest_side'],
            'measurement_units' => $row['measurement_units'],
            'weight' => $row['weight'],
            'weight_units' => $row['weight_units'],
            'item_volume' => $row['item_volume'],
            'volume_units' => $row['volume_units'],
            'product_size_tier' => $row['product_size_tier'],
            'average_quantity_on_hand' => $row['average_quantity_on_hand'],
            'average_quantity_pending_removal' => $row['average_quantity_pending_removal'],
            'total_item_volume_est' => $row['total_item_volume_est'],
            'month_of_charge' => $row['month_of_charge'],
            'storage_rate' => $row['storage_rate'],
            'currency' => $row['currency'],
            'hkd' => $row['hkd'],
            'monthly_storage_fee_est' => $row['monthly_storage_fee_est'],
            'hkd_rate' => $row['hkd_rate'],
            'dangerous_goods_storage_type' => $row['dangerous_goods_storage_type'],
            'category' => $row['category'],
            'eligible_for_discount' => $row['eligible_for_discount'],
            'qualified_for_discount' => $row['qualified_for_discount'],
            'total_incentive_fee_amount' => $row['total_incentive_fee_amount'],
            'breakdown_incentive_fee_amount' => $row['breakdown_incentive_fee_amount'],
            'average_quantity_customer_orders' => $row['average_quantity_customer_orders'],
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

    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        // Handle the failures how you'd like.
        \Log::channel('daily_queue_import')
            ->info("[daily_queue_import.MonthlyStorageFees]" . $failures);
    }

    public function rules(): array
    {
        return [
            'account' => ['bail', 'nullable', 'string', 'max:50'],//
            'asin' => ['bail', 'nullable', 'string', 'max:50'],//
            'fnsku' => ['bail', 'nullable', 'string', 'max:50'],//
            'product_name' => ['bail', 'nullable', 'string', 'max:255'],//
            'fulfilment_center' => ['bail', 'nullable', 'string', 'max:50'],//
            'country_code' => ['bail', 'nullable', 'string', 'max:50'],//
            'supplier_type' => ['bail', 'nullable', 'string', 'max:50'],//
            'supplier' => ['bail', 'nullable', 'string', 'max:50'],//
            'longest_side' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
            'median_side' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
            'shortest_side' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
            'measurement_units' => ['bail', 'nullable', 'string', 'max:50'],//
            'weight' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
            'weight_units' => ['bail', 'nullable', 'string', 'max:50'],/////
            'item_volume' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
            'volume_units' => ['bail', 'nullable', 'string', 'max:50'],//
            'product_size_tier' => ['bail', 'nullable', 'string', 'max:50'],//
            'average_quantity_on_hand' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
            'average_quantity_pending_removal' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
            'total_item_volume_est' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            'month_of_charge' => ['bail', 'nullable', 'string', 'max:50'],//
            'storage_rate' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            'currency' => ['bail', 'nullable', 'string', 'max:3'],
//            'hkd' => ['bail', 'nullable', 'numeric', 'max:99999999'],
//            'monthly_storage_fee_est' => ['bail', 'nullable', 'numeric', 'max:99999999'],
//            'hkd_rate' => ['bail', 'nullable', 'numeric', 'max:99999999'],
//            'dangerous_goods_storage_type' => ['bail', 'nullable', 'string', 'max:50'],
//            'category' => ['bail', 'nullable', 'string', 'max:50'],
//            'eligible_for_discount' => ['bail', 'nullable', 'string', 'max:50'],
//            'qualified_for_discount' => ['bail', 'nullable', 'string', 'max:50'],
//            'total_incentive_fee_amount' => ['bail', 'nullable', 'string', 'max:50'],
//            'breakdown_incentive_fee_amount' => ['bail', 'nullable', 'string', 'max:50'],
//            'average_quantity_customer_orders' => ['bail', 'nullable', 'numeric', 'max:99999999'],

            '*.account' => ['bail', 'nullable', 'string', 'max:50'],//
            '*.asin' => ['bail', 'nullable', 'string', 'max:50'],//
            '*.fnsku' => ['bail', 'nullable', 'string', 'max:50'],//
            '*.product_name' => ['bail', 'nullable', 'string', 'max:255'],//
            '*.fulfilment_center' => ['bail', 'nullable', 'string', 'max:50'],//
            '*.country_code' => ['bail', 'nullable', 'string', 'max:50'],//
            '*.supplier_type' => ['bail', 'nullable', 'string', 'max:50'],//
            '*.supplier' => ['bail', 'nullable', 'string', 'max:50'],//
            '*.longest_side' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
            '*.median_side' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
            '*.shortest_side' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
            '*.measurement_units' => ['bail', 'nullable', 'string', 'max:50'],//
            '*.weight' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
            '*.weight_units' => ['bail', 'nullable', 'string', 'max:50'],//////
            '*.item_volume' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
            '*.volume_units' => ['bail', 'nullable', 'string', 'max:50'],//
            '*.product_size_tier' => ['bail', 'nullable', 'string', 'max:50'],//
            '*.average_quantity_on_hand' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
            '*.average_quantity_pending_removal' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
            '*.total_item_volume_est' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            '*.month_of_charge' => ['bail', 'nullable', 'string', 'max:50'],//
            '*.storage_rate' => ['bail', 'nullable', 'numeric', 'max:99999999'],//
//            '*.currency' => ['bail', 'nullable', 'string', 'max:3'],
//            '*.hkd' => ['bail', 'nullable', 'numeric', 'max:99999999'],
//            '*.monthly_storage_fee_est' => ['bail', 'nullable', 'numeric', 'max:99999999'],
//            '*.hkd_rate' => ['bail', 'nullable', 'numeric', 'max:99999999'],
//            '*.dangerous_goods_storage_type' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.category' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.eligible_for_discount' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.qualified_for_discount' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.total_incentive_fee_amount' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.breakdown_incentive_fee_amount' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.average_quantity_customer_orders' => ['bail', 'nullable', 'numeric', 'max:99999999'],
        ];
    }

}

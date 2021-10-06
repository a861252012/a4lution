<?php

namespace App\Imports;

use App\Models\AmazonDateRangeReport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;

class AmazonDateRangeImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithCalculatedFormulas, WithValidation, SkipsOnFailure, SkipsOnError
{
    use Importable, RemembersRowNumber, SkipsErrors;

    private $rows = 0;

    public function model(array $row)
    {
        ++$this->rows;

        return new AmazonDateRangeReport([
            'account' => $row['account'],
            'country' => $row['country'],
            'paid_date' => $row['paid_date'],
            'shipped_date' => $row['shipped_date'],
            'settlement_id' => $row['settlement_id'],
            'type' => $row['type'],
            'description' => $row['description'],
            'order_id' => $row['order_id'],
            'order_type' => $row['order_type'],
            'msku' => $row['msku'],
            'asin' => $row['asin'],
            'product_name' => $row['product_name'],
            'sku' => $row['sku'],
            'supplier_type' => $row['supplier_type'],
            'supplier' => $row['supplier'],
            'marketplace' => $row['marketplace'],
            'fulfillment' => $row['fulfillment'],
            'quantity' => $row['quantity'],
            'currency' => $row['currency'],
            'product_sales' => $row['product_sales'],
            'shipping_credits' => $row['shipping_credits'],
            'gift_wrap_credits' => $row['gift_wrap_credits'],
            'promotional_rebates' => $row['promotional_rebates'],
            'cost_of_point' => $row['cost_of_point'],
            'tax' => $row['tax'],
            'marketplace_withheld_tax' => $row['marketplace_withheld_tax'],
            'selling_fees' => $row['selling_fees'],
            'fba_fees' => $row['fba_fees'],
            'other_transaction_fees' => $row['other_transaction_fees'],
            'other' => $row['other'],
            'amazon_total' => $row['amazon_total'],
            'hkd_rate' => $row['hkd_rate'],
            'amazon_total_hkd' => $row['amazon_total_hkd'],
            'upload_id' => 1,
            'report_date' => date('Y-m-d'),
            'active' => 1,
//            'created_at' => Carbon::now(),
            'created_at' => date('Y-m-d h:i:s'),
            'created_by' => 2,
//            'updated_at' => Carbon::now(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_by' => 2,
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        // Handle the failures how you'd like.
        \Log::channel('daily_queue_import')
            ->info("[daily_queue_import.onFailure]" . serialize($failures));
    }

    public function rules(): array
    {
        return [
            'account' => ['bail', 'nullable', 'string', 'max:50'],
            'country' => ['bail', 'nullable', 'string', 'max:50'],
            'paid_date' => ['bail', 'nullable', 'date'],
            'shipped_date' => ['bail', 'nullable', 'date'],
            'settlement_id' => ['bail', 'nullable', 'numeric', 'max:999999999999'],//DB是varchar(50),但資料type為數字
            'type' => ['bail', 'nullable', 'string', 'max:50'],
            'description' => ['bail', 'nullable', 'string', 'max:255'],
            'order_id' => ['bail', 'nullable', 'string', 'max:100'],
            'order_type' => ['bail', 'nullable', 'string', 'max:50'],
            'msku' => ['bail', 'nullable', 'string', 'max:50'],
            'asin' => ['bail', 'nullable', 'string', 'max:50'],
            'product_name' => ['bail', 'nullable', 'string', 'max:255'],
            'sku' => ['bail', 'nullable', 'string', 'max:50'],
            'supplier_type' => ['bail', 'nullable', 'string', 'max:50'],
            'supplier' => ['bail', 'nullable', 'string', 'max:50'],
            'marketplace' => ['bail', 'nullable', 'string', 'max:50'],
            'fulfillment' => ['bail', 'nullable', 'string', 'max:50'],
            'quantity' => ['bail', 'nullable', 'numeric', 'max:99999'],
            'currency' => ['bail', 'nullable', 'string', 'max:3'],
            'product_sales' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'shipping_credits' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'gift_wrap_credits' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'promotional_rebates' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'cost_of_point' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'tax' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'marketplace_withheld_tax' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'selling_fees' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'fba_fees' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'other_transaction_fees' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'other' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'amazon_total' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'hkd_rate' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            'amazon_total_hkd' => ['bail', 'nullable', 'numeric', 'max:999999999999'],

            '*.account' => ['bail', 'nullable', 'string', 'max:50'],
            '*.country' => ['bail', 'nullable', 'string', 'max:50'],
            '*.paid_date' => ['bail', 'nullable', 'date'],
            '*.shipped_date' => ['bail', 'nullable', 'date'],
            '*.settlement_id' => ['bail', 'nullable', 'numeric', 'max:999999999999'],//DB是varchar(50),但資料type為數字
            '*.type' => ['bail', 'nullable', 'string', 'max:50'],
            '*.description' => ['bail', 'nullable', 'string', 'max:255'],
            '*.order_id' => ['bail', 'nullable', 'string', 'max:100'],
            '*.order_type' => ['bail', 'nullable', 'string', 'max:50'],
            '*.msku' => ['bail', 'nullable', 'string', 'max:50'],
            '*.asin' => ['bail', 'nullable', 'string', 'max:50'],
            '*.product_name' => ['bail', 'nullable', 'string', 'max:255'],
            '*.sku' => ['bail', 'nullable', 'string', 'max:50'],
            '*.supplier_type' => ['bail', 'nullable', 'string', 'max:50'],
            '*.supplier' => ['bail', 'nullable', 'string', 'max:50'],
            '*.marketplace' => ['bail', 'nullable', 'string', 'max:50'],
            '*.fulfillment' => ['bail', 'nullable', 'string', 'max:50'],
            '*.quantity' => ['bail', 'nullable', 'numeric', 'max:99999'],
            '*.currency' => ['bail', 'nullable', 'string', 'max:3'],
            '*.product_sales' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.shipping_credits' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.gift_wrap_credits' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.promotional_rebates' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.cost_of_point' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.tax' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.marketplace_withheld_tax' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.selling_fees' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.fba_fees' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.other_transaction_fees' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.other' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.amazon_total' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.hkd_rate' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
            '*.amazon_total_hkd' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
        ];
    }

    public function prepareForValidation($data, $index)
    {
        // Handle the failures how you'd like.
//        \Log::channel('daily_queue_import')
//            ->info("[daily_queue_import.prepareForValidation]" . implode(',', $data));
        return $data;
    }
}

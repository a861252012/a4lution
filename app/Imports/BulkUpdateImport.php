<?php

namespace App\Imports;

use App\Models\OrderBulkUpdate;
use App\Models\OrderProduct;
use App\Repositories\OrderSkuCostDetailRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BulkUpdateImport implements
    ToCollection,
    WithChunkReading,
    WithHeadingRow,
    WithBatchInserts,
    ShouldQueue
{
    use Importable, RegistersEventListeners;

    private int $userID;
    private string $batchJobID;

    public function __construct(
        $userID,
        $batchJobID
    ) {
        $this->userID = $userID;
        $this->batchJobID = $batchJobID;
    }

    public function collection(Collection $row)
    {
        $dateTime = now()->format('YmdHisu');

        $row->each(function ($item, $key) use ($dateTime) {
            Log::channel('order_bulk_update_import')
                ->info("[batch_job_id:{$dateTime} key:{$key}] " . $item);

            $bulkUpdateID = OrderBulkUpdate::insertGetId($this->formBulkUpdateData($item));

            $executionStatus = 'FAILURE';

            //if order_code and sku match condition,then update OrderProduct and record as success
            if (app(OrderSkuCostDetailRepository::class)->checkIfExist($item['erp_order_id'], $item['sku'])) {
                $executionStatus = 'SUCCESS';

                OrderProduct::where(
                    [
                        'order_code' => $item['erp_order_id'],
                        'sku' => $item['sku']
                    ]
                )->update($this->formOrderProductData($item));
            }

            //update OrderBulkUpdate table by id
            $BulkOrder = OrderBulkUpdate::find($bulkUpdateID);
            $BulkOrder->execution_status = $executionStatus;
            ($executionStatus === 'FAILURE') ? $BulkOrder->exit_message = 'No records match this find criteria' : null;
            $BulkOrder->save();
        });
    }

    public function formBulkUpdateData(Collection $collection): array
    {
        return $collection->merge(
            [
                'batch_job_id' => $this->batchJobID,
                'execution_status' => 'PENDING',
                'platform_order_id' => $collection['erp_order_id'],
                'product_sku' => $collection['sku'],
                'created_at' => date('Y-m-d h:i:s'),
                'created_by' => $this->userID,
            ]
        )->only(
            [
                'order_price_original_currency',
                'paypal_fee_original_currency',
                'transaction_fee_original_currency',
                'fba_fee_original_currency',
                'first_mile_shipping_fee_original_currency',
                'first_mile_tariff_original_currency',
                'last_mile_shipping_fee_original_currency',
                'other_fee_original_currency',
                'purchase_shipping_fee_original_currency',
                'product_cost_original_currency',
                'marketplace_tax_original_currency',
                'cost_of_point_original_currency',
                'exclusives_referral_fee_original_currency',
                'batch_job_id',
                'execution_status',
                'platform_order_id',
                'product_sku',
                'created_at',
                'created_by',
            ]
        )->all();
    }

    public function formOrderProductData($row): array
    {
        //other_transaction = 上傳數據Other Fee + marketpalce tax + cost of point + Exclusives Referral Fee
        return [
            'sales_amount' => $row['order_price_original_currency'],
            'paypal_fee' => $row['paypal_fee_original_currency'],
            'transaction_fee' => $row['transaction_fee_original_currency'],
            'fba_fee' => $row['fba_fee_original_currency'],
            'first_mile_shipping_fee' => $row['first_mile_shipping_fee_original_currency'],
            'first_mile_tariff' => $row['first_mile_tariff_original_currency'],
            'last_mile_shipping_fee' => $row['last_mile_shipping_fee_original_currency'],
            'other_fee' => $row['other_fee_original_currency'],
            'purchase_shipping_fee' => $row['purchase_shipping_fee_original_currency'],
            'product_cost' => $row['product_cost_original_currency'],
            'marketplace_tax' => $row['marketplace_tax_original_currency'],
            'cost_of_point' => $row['cost_of_point_original_currency'],
            'exclusives_referral_fee' => $row['exclusives_referral_fee_original_currency'],
            'other_transaction' => (float)$row['other_fee_original_currency'] +
                (float)$row['cost_of_point_original_currency'] + (float)$row['marketplace_tax_original_currency'] +
                (float)$row['exclusives_referral_fee_original_currency'],
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    //The 2nd row will now be used as heading row
    public function headingRow(): int
    {
        return 2;
    }
}

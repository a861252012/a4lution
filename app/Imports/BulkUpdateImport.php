<?php

namespace App\Imports;

use App\Models\OrderBulkUpdate;
use App\Models\OrderProduct;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
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

    public function __construct($userID)
    {
        $this->userID = $userID;
    }

    public function collection(Collection $row)
    {
        $dateTime = now()->format('YmdHisu');

        $row->each(function ($item, $key) use ($dateTime) {
            $bulkUpdateID = OrderBulkUpdate::insertGetId(
                $item->merge(
                    [
                        'batch_job_id' => $dateTime,
                        'execution_status' => 'PENDING',
                        'site_order_id' => $item['order_id'],
                        'site_product_sku' => $item['sku'],
                        'created_at' => date('Y-m-d h:i:s'),
                        'created_by' => $this->userID,
                    ]
                )->except(['order_id', 'sku'])->all()
            );

            $executionStatus = 'FAILURE';

            if (OrderProduct::where(['order_code' => $item['order_id'], 'sku' => $item['sku']])->exists()) {
                $executionStatus = 'SUCCESS';

                OrderProduct::where(
                    [
                        'order_code' => $item['order_id'],
                        'sku' => $item['sku']
                    ]
                )->update($this->formOrderProductData($item));
            }

            $BulkOrder = OrderBulkUpdate::find($bulkUpdateID);
            $BulkOrder->execution_status = $executionStatus;
            ($executionStatus === 'FAILURE') ? $BulkOrder->exit_message = 'No records match this find criteria' : null;
            $BulkOrder->save();
        });
    }

    public function formOrderProductData($row): array
    {
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
}

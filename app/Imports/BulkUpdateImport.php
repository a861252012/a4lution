<?php

namespace App\Imports;

use App\Models\OrderBulkUpdate;
use App\Models\OrderProduct;
use App\Models\SystemChangeLog;
use App\Repositories\OrderSkuCostDetailRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BulkUpdateImport implements
    ToCollection,
    WithChunkReading,
    WithHeadingRow,
    ShouldQueue
{
    use Importable;

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
            $getOrderProduct = app(OrderSkuCostDetailRepository::class)->getProductId(
                preg_replace('/\s+/', '', $item['erp_order_id']),
                preg_replace('/\s+/', '', $item['sku'])
            );

            if ($getOrderProduct) {
                $executionStatus = 'SUCCESS';

                $updateData = $this->formOrderProductData($item);

                $originalOrderProduct = OrderProduct::selectRaw(collect($updateData)->keys()->implode(','))
                    ->find($getOrderProduct->id)->toArray();

                OrderProduct::where('id', $getOrderProduct->id)->update($updateData);

                $diff = array_diff($updateData, $originalOrderProduct);

                collect($diff)->each(fn ($item, $key) => SystemChangeLog::insert(
                    [
                        'menu_path' => '/orders/bulkUpdate/index',
                        'event_type' => 'U',
                        'table_name' => 'order_products',
                        'reference_id' => $getOrderProduct->id,
                        'field_name' => $key,
                        'original_value' => $originalOrderProduct[$key],
                        'new_value' => $item,
                        'created_by' => $this->userID,
                        'created_at' => date('Y-m-d h:i:s')
                    ]
                ));
            }

            //update OrderBulkUpdate table by id
            $BulkOrder = OrderBulkUpdate::find($bulkUpdateID);
            $BulkOrder->execution_status = $executionStatus;
            ($getOrderProduct) ? $BulkOrder->order_product_id = $getOrderProduct->id : null;
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
        $otherTransaction = (float)$row['other_fee_original_currency'] +
            (float)$row['cost_of_point_original_currency'] + (float)$row['marketplace_tax_original_currency'] +
            (float)$row['exclusives_referral_fee_original_currency'];

        //other_transaction = 上傳數據Other Fee + marketpalce tax + cost of point + Exclusives Referral Fee
        return [
            'sales_amount' => round($row['order_price_original_currency'], 4),
            'paypal_fee' => round($row['paypal_fee_original_currency'], 4),
            'transaction_fee' => round($row['transaction_fee_original_currency'], 4),
            'fba_fee' => round($row['fba_fee_original_currency'], 4),
            'first_mile_shipping_fee' => round($row['first_mile_shipping_fee_original_currency'], 4),
            'first_mile_tariff' => round($row['first_mile_tariff_original_currency'], 4),
            'last_mile_shipping_fee' => round($row['last_mile_shipping_fee_original_currency'], 4),
            'other_fee' => round($row['other_fee_original_currency'], 4),
            'purchase_shipping_fee' => round($row['purchase_shipping_fee_original_currency'], 4),
            'product_cost' => round($row['product_cost_original_currency'], 4),
            'marketplace_tax' => round($row['marketplace_tax_original_currency'], 4),
            'cost_of_point' => round($row['cost_of_point_original_currency'], 4),
            'exclusives_referral_fee' => round($row['exclusives_referral_fee_original_currency'], 4),
            'other_transaction' => round($otherTransaction, 4),
        ];
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

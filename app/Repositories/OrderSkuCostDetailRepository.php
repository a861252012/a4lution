<?php

namespace App\Repositories;

use App\Models\OrderSkuCostDetail;
use Illuminate\Support\Facades\DB;

class OrderSkuCostDetailRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new OrderSkuCostDetail);
    }

    public function insertData(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->model->insert($data);
        });
    }

    public function getSkuDetail(string $referenceNo, string $barcode)
    {
        return $this->model->select(
            'currency_code_org',
            'order_total_amount_org',
            'platform_cost_org',
            'first_carrier_freight',
            'tariff_fee',
            'shipping_fee_org',
            'other_fee_org',
        )
            ->where('reference_no', '=', $referenceNo)
            ->where('product_barcode', '=', $barcode)
            ->first();
    }

    public function checkIfSkuDetailDuplicated(string $productBarcode, string $referenceNo): bool
    {
        return $this->model->where('product_barcode', $productBarcode)
            ->where('reference_no', $referenceNo)
            ->exists();
    }

    public function getProductId($orderId = '', $sku = '')
    {
        return $this->model->select('order_products.id')
            ->join('order_products', function ($join) {
                $join->on('order_products.order_code', '=', 'order_sku_cost_details.reference_no')
                    ->on('order_products.sku', '=', 'order_sku_cost_details.product_barcode');
            })
            ->where('order_sku_cost_details.platform_reference_no', $orderId)
            ->where('order_sku_cost_details.product_barcode', $sku)
            ->first();
    }
}

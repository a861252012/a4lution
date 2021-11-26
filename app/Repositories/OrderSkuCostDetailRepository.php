<?php


namespace App\Repositories;


use App\Models\OrderSkuCostDetail;
use Illuminate\Support\Facades\DB;

class OrderSkuCostDetailRepository
{
    protected $orderSkuCostDetail;

    public function __construct(OrderSkuCostDetail $orderSkuCostDetail)
    {
        $this->orderSkuCostDetail = $orderSkuCostDetail;
    }

    public function insertData(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->orderSkuCostDetail->insert($data);
        });
    }

    public function getSkuDetail(string $referenceNo, string $barcode)
    {
        return $this->orderSkuCostDetail->select(
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
        return $this->orderSkuCostDetail->where('product_barcode', $productBarcode)
            ->where('reference_no', $referenceNo)
            ->exists();
    }
}

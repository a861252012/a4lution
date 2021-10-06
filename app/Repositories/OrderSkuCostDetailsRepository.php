<?php


namespace App\Repositories;


use App\Models\orderSkuCostDetails;
use Illuminate\Support\Facades\DB;

class OrderSkuCostDetailsRepository
{
    protected $orderSkuCostDetails;

    public function __construct(OrderSkuCostDetails $orderSkuCostDetails)
    {
        $this->orderSkuCostDetails = $orderSkuCostDetails;
    }

    public function insertData(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->orderSkuCostDetails->insert($data);
        });
    }

    public function getSkuDetail(string $referenceNo, string $barcode)
    {
        return $this->orderSkuCostDetails->select(
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
        return $this->orderSkuCostDetails->where('product_barcode', $productBarcode)
            ->where('reference_no', $referenceNo)
            ->exists();
    }
}

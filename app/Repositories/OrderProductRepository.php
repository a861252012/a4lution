<?php


namespace App\Repositories;

use App\Models\OrderProduct;

class OrderProductRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new OrderProduct);
    }

    public function updateData(array $data, string $orderCode, string $sku)
    {
        return $this->model->where('order_code', '=', $orderCode)
            ->where('sku', '=', $sku)
            ->update($data);
    }

    public function getFitOrder(
        string $supplier,
        string $shipDate
    ) {
        return $this->model
            ->select(
                'order_products.id',
                'order_products.sku',
                'order_products.sales_amount AS selling_price',
                'orders.order_code',
                'commission_sku_settings.currency',
                'commission_sku_settings.threshold',
                'commission_sku_settings.basic_rate',
                'commission_sku_settings.upper_bound_rate'
            )
            ->join('orders', function ($join) {
                $join->on('orders.order_code', '=', 'order_products.order_code');
                $join->where('order_products.order_code', 1);
            })
            ->leftJoin('commission_sku_settings', function ($join) {
                $join->on('commission_sku_settings.sku', '=', 'order_products.sku');
            })
            ->where('order_products.supplier', $supplier)
            ->whereRaw("DATE_FORMAT(orders.ship_time, '%Y%m')", date("Ym", strtotime($shipDate)))
            ->get();
    }

    public function checkUnmatchedRecord(
        string $supplier,
        string $shipDate
    ) {
        return $this->model
            ->select(
                'order_products.id',
                'order_products.sku',
                'order_products.sales_amount AS selling_price',
                'orders.order_code',
                'commission_sku_settings.currency',
                'commission_sku_settings.threshold',
                'commission_sku_settings.basic_rate',
                'commission_sku_settings.upper_bound_rate'
            )
            ->join('orders', function ($join) {
                $join->on('orders.order_code', '=', 'order_products.order_code');
                $join->where('order_products.order_code', 1);
            })
            ->leftJoin('commission_sku_settings', function ($join) {
                $join->on('commission_sku_settings.sku', '=', 'order_products.sku');
            })
            ->where('order_products.supplier', $supplier)
            ->whereRaw("DATE_FORMAT(orders.ship_time, '%Y%m')", date("Ym", strtotime($shipDate)))
            ->whereNotNull('commission_sku_settings.sku')
            ->get();
    }

    public function getMaxDiscountRate(
        string $supplier,
        string $shipDate
    ): float {
        return (float)$this->model->selectRaw('MAX(order_products.promotion_discount_rate) as max_promotion_rate')
            ->join('orders', function ($join) {
                $join->on('orders.order_code', '=', 'order_products.order_code')
                    ->where('order_products.active', 1);
            })
            ->whereRaw("DATE_FORMAT(orders.ship_time, '%Y%m') = ?", date("Ym", strtotime($shipDate)))
            ->where('order_products.supplier', $supplier)
            ->value('max_promotion_rate');
    }

    public function getSkuAvolutionCommission(
        string $supplier,
        string $shipDate
    ): float {
        return (float)$this->model
            ->selectRaw("SUM(IFNULL(order_products.sku_commission_amount, 0)) as sku_commission_amount")
            ->join('orders', function ($join) {
                $join->on('orders.order_code', '=', 'order_products.order_code')
                    ->where('order_products.active', 1);
            })
            ->where('order_products.supplier', $supplier)
            ->whereRaw("DATE_FORMAT(orders.ship_time, '%Y%m') = ?", date("Ym", strtotime($shipDate)))
            ->value('sku_commission_amount');
    }
}

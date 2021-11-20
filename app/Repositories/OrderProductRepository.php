<?php


namespace App\Repositories;

use App\Models\OrderProduct;
use Illuminate\Support\Facades\DB;

//use Illuminate\Support\Facades\Schema;

class OrderProductRepository
{
    public function __construct()
    {
    }

    public function insertData(array $data)
    {
        return DB::transaction(function () use ($data) {
            return OrderProduct::insert($data);
        });
    }

    public function updateData(array $data, string $orderCode, string $sku)
    {
        return OrderProduct::where('order_code', '=', $orderCode)
            ->where('sku', '=', $sku)
            ->update($data);
    }

    public function countReportColumns(string $referenceNo, string $product_Barcode): array
    {
        $sql = "SELECT SUM(IF(t.amount_description = 'FBAPerUnitFulfillmentFee',
              t.amount,
              0) + IF(t.amount_description = 'ShippingChargeback',
                      t.amount,
                      0)) AS fba_fee,
       SUM(IF(t.amount_description = 'MarketplaceFacilitatorTax-Principal',
              t.amount,
              0) + IF(t.amount_description = 'MarketplaceFacilitatorTax-Shipping',
                      t.amount,
                      0)) AS marketplace_tax,
       SUM(IF(t.amount_description = 'PointsGranted',
              t.amount,
              0))         AS cost_of_point,
       SUM(IF(t.amount_description = 'AmazonExclusivesFee',
              t.amount,
              0))         AS exclusives_referral_fee
FROM amazon_report_list t
         INNER JOIN
     order_sku_cost_details d ON d.op_platform_sales_sku = t.sku
         AND d.platform_reference_no = t.order_id
         AND d.seller_id = t.user_account
WHERE t.amount_description IN (
                               'FBAPerUnitFulfillmentFee', 'ShippingChargeback',
                               'MarketplaceFacilitatorTax-Principal',
                               'MarketplaceFacilitatorTax-Shipping',
                               'PointsGranted',
                               'AmazonExclusivesFee')
  AND d.reference_no = '{$referenceNo}'
  AND d.product_barcode = '{$product_Barcode}'";

        return DB::select($sql);
    }

    public function countPromotionAmount(string $referenceNo, string $productBarcode): array
    {
        $sql = "SELECT
    SUM(IF(t.amount_type = 'ItemPrice',
        t.amount,
        0)) AS 'principal',
    SUM(IF(t.amount_type = 'Promotion',
        t.amount,
        0)) AS 'promotion_amount'
FROM
    amazon_report_list t
        INNER JOIN
    order_sku_cost_details d ON d.op_platform_sales_sku = t.sku
        AND d.platform_reference_no = t.order_id
        AND d.seller_id = t.user_account
WHERE
    (t.amount_type = 'Promotion'
        OR (t.amount_type = 'ItemPrice'
        AND t.amount_description = 'Principal'))
        AND d.reference_no = '{$referenceNo}'
        AND d.product_barcode = '{$productBarcode}'";

        return DB::select($sql);
    }

    public function getFitOrder(string $supplier, string $shipDate)
    {
        $shipDate = date("Ym", strtotime($shipDate));

        $sql = "SELECT
    p.id,
    o.order_code,
    p.sku,
    p.sales_amount AS 'selling_price',
    s.currency,
    s.threshold,
    s.basic_rate,
    s.upper_bound_rate
FROM
    order_products p
        INNER JOIN
    orders o ON o.order_code = p.order_code
        AND p.active = 1
        LEFT JOIN
    commission_sku_settings s ON p.sku = s.sku
WHERE
    p.supplier = '{$supplier}'
        AND DATE_FORMAT(o.ship_time, '%Y%m') = '{$shipDate}'";

        return DB::select($sql);
    }

    public function checkUnmatchedRecord(string $supplier, string $shipDate)
    {
        $sql = "SELECT
    p.id,
    o.order_code,
    p.sku,
    p.sales_amount AS 'selling_price',
    s.currency,
    s.threshold,
    s.basic_rate,
    s.upper_bound_rate
FROM
    order_products p
        INNER JOIN
    orders o ON o.order_code = p.order_code
        AND p.active = 1
        LEFT JOIN
    commission_sku_settings s ON p.sku = s.sku
WHERE
    p.supplier = '{$supplier}'
        AND DATE_FORMAT(o.ship_time, '%Y%m') = '{$shipDate}'
        AND s.sku is null";

        return DB::select($sql);
    }

    public function getMaxDiscountRate(string $supplier, string $shipDate)
    {
        $shipDate = date("Ym", strtotime($shipDate));

        $sql = "SELECT
    MAX(p.promotion_discount_rate)
FROM
    order_products p
        INNER JOIN
    orders o ON o.order_code = p.order_code
        AND p.active = 1
WHERE
    DATE_FORMAT(o.ship_time, '%Y%m') = '{$shipDate}'
        AND p.supplier = '{$supplier}'";

        return DB::select($sql);
    }

    public function getSkuAvolutionCommission(string $supplier, string $shipDate)
    {
        $shipDate = date("Ym", strtotime($shipDate));

        $sql = "SELECT
    SUM(IFNULL(p.sku_commission_amount, 0)) as sum_sku_commission_amount
FROM
    order_products p
        INNER JOIN
    orders o ON o.order_code = p.order_code
        AND p.active = 1
WHERE
    p.supplier = '{$supplier}'
        AND DATE_FORMAT(o.ship_time, '%Y%m') = {$shipDate}";

        return DB::select($sql)[0]->sum_sku_commission_amount;
    }
}

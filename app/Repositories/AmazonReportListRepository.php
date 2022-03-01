<?php

namespace App\Repositories;

use App\Models\AmazonReportList;

class AmazonReportListRepository extends BaseRepository
{
    protected AmazonReportList $amazonReportList;

    public function __construct()
    {
        parent::__construct(new AmazonReportList);
    }

    public function countPromotionAmount(
        string $referenceNo,
        string $productBarcode
    ) {
        return $this->model
            ->selectRaw(
                "SUM(IF(amazon_report_list.amount_type = 'ItemPrice', amazon_report_list.amount, 0)) 
            AS 'principal',
            SUM(IF(amazon_report_list.amount_type = 'Promotion',amazon_report_list.amount,0)) AS 'promotion_amount'"
            )
            ->join('order_sku_cost_details', function ($join) {
                $join->on('order_sku_cost_details.op_platform_sales_sku', '=', 'amazon_report_list.sku')
                    ->on('order_sku_cost_details.platform_reference_no', '=', 'amazon_report_list.order_id')
                    ->on('order_sku_cost_details.seller_id', '=', 'amazon_report_list.user_account');
            })
            ->whereRaw("(amazon_report_list.amount_type = 'Promotion' OR (amazon_report_list.amount_type = 'ItemPrice'
            AND amazon_report_list.amount_description = 'Principal'))")
            ->where('order_sku_cost_details.reference_no', $referenceNo)
            ->where('order_sku_cost_details.product_barcode', $productBarcode)
            ->get();
    }

    public function countReportColumns(
        string $referenceNo,
        string $productBarcode
    ) {
        return $this->model
            ->selectRaw(
                "SUM(IF(amazon_report_list.amount_description = 'FBAPerUnitFulfillmentFee',amazon_report_list.amount,0)
                 + IF(amazon_report_list.amount_description = 'ShippingChargeback', amazon_report_list.amount,0)) 
                 AS fba_fee,
       SUM(IF(amazon_report_list.amount_description = 'MarketplaceFacilitatorTax-Principal',
              amazon_report_list.amount,0) + 
              IF(amazon_report_list.amount_description = 'MarketplaceFacilitatorTax-Shipping',
                      amazon_report_list.amount,0)) AS marketplace_tax,
       SUM(IF(amazon_report_list.amount_description = 'PointsGranted',amazon_report_list.amount,0))
        AS cost_of_point,
       SUM(IF(amazon_report_list.amount_description = 'AmazonExclusivesFee',amazon_report_list.amount,0))
        AS exclusives_referral_fee"
            )
            ->join('order_sku_cost_details', function ($join) {
                $join->on('order_sku_cost_details.op_platform_sales_sku', '=', 'amazon_report_list.sku')
                    ->on('order_sku_cost_details.platform_reference_no', '=', 'amazon_report_list.order_id')
                    ->on('order_sku_cost_details.seller_id', '=', 'amazon_report_list.user_account');
            })
            ->whereIn(
                'amazon_report_list.amount_description',
                [
                    'FBAPerUnitFulfillmentFee',
                    'ShippingChargeback',
                    'MarketplaceFacilitatorTax-Principal',
                    'MarketplaceFacilitatorTax-Shipping',
                    'PointsGranted',
                    'AmazonExclusivesFee'
                ]
            )
            ->where('order_sku_cost_details.reference_no', $referenceNo)
            ->where('order_sku_cost_details.product_barcode', $productBarcode)
            ->get();
    }
}

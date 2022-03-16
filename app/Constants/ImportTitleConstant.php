<?php
/**
 * import excel時驗證的title
 */

namespace App\Constants;

class ImportTitleConstant
{
    //platform_ad_fees
    const PLATFORM_AD = [
        "client_code",
        "client_type",
        "platform",
        "account",
        "campagin_type",
        "campagin",
        "currency",
        "impressions",
        "clicks",
        "ctr",
        "spendings",
        "spendings_hkd",
        "cpc",
        "sales_qty",
        "sales_amount",
        "sales_amount_hkd",
        "acos",
        "exchange_rate",
        "country"
    ];

    //amazon_date_range_report
    const AMZ_DATE_RANGE = [
        "account",
        "country",
        "paid_date",
        "shipped_date",
        "settlement_id",
        "type",
        "description",
        "order_id",
        "order_type",
        "msku",
        "asin",
        "product_name",
        "sku",
        "supplier_type",
        "supplier",
        "marketplace",
        "fulfillment",
        "quantity",
        "currency",
        "product_sales",
        "shipping_credits",
        "gift_wrap_credits",
        "promotional_rebates",
        "cost_of_point",
        "tax",
        "marketplace_withheld_tax",
        "selling_fees",
        "fba_fees",
        "other_transaction_fees",
        "other",
        "amazon_total",
        "hkd_rate",
        "amazon_total_hkd"
    ];

    //long_term_storage_fees
    const LONG_TERM = [
        "account",
        "snapshot_date",
        "sku",
        "fnsku",
        "asin",
        "product_name",
        "supplier_type",
        "supplier",
        "condition",
        "qty_charged_12_mo_long_term_storage_fee",
        "per_unit_volume",
        "currency",
        "12_mo_long_terms_storage_fee",
        "hkd",
        "hkd_rate",
        "qty_charged_6_mo_long_term_storage_fee",
        "6_mo_long_terms_storage_fee",
        "volume_unit",
        "country",
        "enrolled_in_small_and_light"
    ];

    //monthly_storage_fees
    const MONTHLY_STORAGE = [
        "account",
        "asin",
        "fnsku",
        "product_name",
        "fulfilment_center",
        "country_code",
        "supplier_type",
        "supplier",
        "longest_side",
        "median_side",
        "shortest_side",
        "measurement_units",
        "weight",
        "weight_units",
        "item_volume",
        "volume_units",
        "product_size_tier",
        "average_quantity_on_hand",
        "average_quantity_pending_removal",
        "total_item_volume_est",
        "month_of_charge",
        "storage_rate",
        "currency",
        "monthly_storage_fee_est",
        "hkd",
        "hkd_rate",
        // "dangerous_goods_storage_type",
        "category",
        "eligible_for_discount",
        "qualified_for_discount",
        "total_incentive_fee_amount",
        "breakdown_incentive_fee_amount",
        "average_quantity_customer_orders"
    ];

    //first_mile_shipment_fees
    const FIRST_MILE_SHIPMENT = [
        "client_code",
        "ids_sku",
        "title",
        "asin",
        "fnsku",
        "external_id",
        "condition",
        "who_will_prep",
        "prep_type",
        "who_will_label",
        "shipped",
        "fba_shipment",
        "shipment_type",
        "date",
        "account",
        "ship_from",
        "first_mile",
        "last_mile_apc_original_currency_estimated",
        "last_mile_apc_original_currency_actual",
        "shipment_remark",
        "currency_last_mile",
        "exchange_rate",
        "total",
        "is_payment_settled",
        "remark",
    ];

    // ERP Orders
    const ERP_ORDERS = [
        "platform",
        "acc_nick_name",
        "acc_name",
        "site",
        "erp_order_id",
        "package_type",
        "order_type",
        "paid_date",
        "shipped_date",
        "audit_date",
        "platform_sku",
        "item_idasin",
        "product_name",
        "sku",
        "supplier_type",
        "supplier",
        "warehouse",
        "site_order_id",
        "package_id",
        "qty",
        "shipping_method",
        "tracking",
        "product_weight",
        "original_currency",
        "order_price_original_currency",
        "paypal_fee_original_currency",
        "transaction_fee_original_currency",
        "fba_fee_original_currency",
        "first_mile_shipping_fee_original_currency",
        "first_mile_tariff_original_currency",
        "last_mile_shipping_fee_original_currency",
        "other_fee_original_currency",
        "purchase_shipping_fee_original_currency",
        "product_cost_original_currency",
        "marketplace_tax_original_currency",
        "cost_of_point_original_currency",
        "exclusives_referral_fee_original_currency",
        "country",
        "note",
        "gross_profit_original_currency",
        "gross_margin",
        "hkd",
        "hkd_rate",
        "order_price_hkd",
        "paypal_fee_hkd",
        "transaction_fee_hkd",
        "fba_fee_hkd",
        "first_mile_shipping_fee_hkd",
        "first_mile_tariff_hkd",
        "last_mile_shipping_fee_hkd",
        "other_fee_hkd",
        "purchase_shipping_fee_hkd",
        "product_cost_hkd",
        "marketplace_tax_hkd",
        "cost_of_point_hkd",
        "exclusives_referral_fee_hkd",
        "gross_profit_hkd",
        "gross_margin",
        "order_quantity_statistics",
    ];
}
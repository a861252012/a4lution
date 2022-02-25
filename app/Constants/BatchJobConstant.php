<?php

namespace App\Constants;

class BatchJobConstant
{
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    const FEE_TYPE_SALES_REPORT = 'a4lution_sales_reports';
    const FEE_TYPE_PLATFORM_AD_FEES = 'platform_ad_fees';
    const FEE_TYPE_AMAZON_DATE_RANGE = 'amazon_date_range';
    const FEE_TYPE_LONG_TERM_STORAGE_FEES = 'long_term_storage_fees';
    const FEE_TYPE_MONTHLY_STORAGE_FEES = 'monthly_storage_fees';
    const FEE_TYPE_FIRST_MILE_SHIPMENT_FEES = 'first_mile_shipment_fees';

    const IMPORT_TYPE_ERP_ORDERS = 'erp_orders'; // For 「A4lution Sales Reports」
    const IMPORT_TYPE_CONTIN_STORAGE_FEE = 'contin_storage_fee'; // For 「A4lution Sales Reports」

    public static function mapFeeType(): array
    {
        return [
            self::FEE_TYPE_SALES_REPORT => 'A4lution Sales Report',
            self::FEE_TYPE_PLATFORM_AD_FEES => 'Platform Advertisement Fee',
            self::FEE_TYPE_AMAZON_DATE_RANGE => 'Amazon Date Range Report',
            self::FEE_TYPE_LONG_TERM_STORAGE_FEES => 'FBA Long Term Storage Fee',
            self::FEE_TYPE_MONTHLY_STORAGE_FEES => 'FBA Monthly Storage Fee',
            self::FEE_TYPE_FIRST_MILE_SHIPMENT_FEES => 'First Mile Shipment Fee',
        ];
    }

    public static function mapStatus(): array
    {
        return [
            'completed' => 'Completed',
            'processing' => 'Processing',
            'failed' => 'Error',
        ];
    }
}

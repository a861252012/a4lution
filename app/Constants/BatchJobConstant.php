<?php

namespace App\Constants;

class BatchJobConstant
{
    public static function mapFeeType(): array
    {
        return [
            'a4lution_sales_reports' => 'A4lution Sales Report',
            'platform_ad_fees' => 'Platform Advertisement Fee',
            'amazon_date_range' => 'Amazon Date Range Report',
            'long_term_storage_fees' => 'FBA Long Term Storage Fee',
            'monthly_storage_fees' => 'FBA Monthly Storage Fee',
            'first_mile_shipment_fees' => 'First Mile Shipment Fee',
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

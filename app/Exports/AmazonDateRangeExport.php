<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;

class AmazonDateRangeExport implements FromArray
{
    use Exportable;

    public function array(): array
    {
        return [
            ['Account', 'Country', 'Paid Date', 'Shipped Date', 'Settlement ID', 'Type', 'Description', 'Order ID',
                'Order Type', 'MSKU', 'ASIN', 'Product Name', 'SKU', 'Supplier Type', 'Supplier', 'Marketplace',
                'Fulfillment', 'Quantity', 'Currency', 'Product Sales', 'Shipping Credits', 'Gift Wrap Credits',
                'Promotional Rebates', 'Cost of Point', 'Tax', 'Marketplace Withheld Tax', 'Selling Fees', 'FBA fees'
                , 'Other Transaction Fees', 'Other', 'Amazon Total', '', 'HKD Rate', 'Amazon Total (HKD)']
        ];
    }
}

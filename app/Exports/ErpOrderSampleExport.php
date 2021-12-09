<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;

class ErpOrderSampleExport implements FromArray
{
    use Exportable;

    public function array(): array
    {
        return [
            [
                'ORDER ID',
                'SKU',
                'Order Price (Original Currency)',
                'PayPal Fee (Original Currency)',
                'Transaction Fee (Original Currency)',
                'FBA Fee (Original Currency)',
                'First Mile Shipping Fee (Original Currency)',
                'First Mile Tariff (Original Currency)',
                'Last Mile Shipping Fee (Original Currency)',
                'Other Fee (Original Currency)',
                'Purchase Shipping Fee (Original Currency)',
                'Product Cost (Original Currency)',
                'Marketplace Tax (Original Currency)',
                'Cost of Point (Original Currency)',
                'Exclusives Referral Fee (Original Currency)'
            ]
        ];
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;

class MonthlyStorageFeesExport implements FromArray
{
    use Exportable;

    public function array(): array
    {
        return [
            ['Account', 'ASIN', 'fnsku', 'product-name', 'Fulfilment center', 'Country code', 'Supplier Type',
                'Supplier', 'Longest side', 'Median side', 'Shortest side', 'Measurement units', 'weight',
                'Weight units', 'Item volume', 'Volume units', 'Product size tier', 'Average quantity on hand',
                'Average quantity pending removal', 'Total item volume (est.)', 'Month of charge', 'Storage rate',
                'currency', 'Monthly storage fee (est.)', 'HKD', 'HKD Rate', 'category',
                'eligible-for-discount', 'qualified-for-discount', 'total-incentive-fee-amount',
                'breakdown-incentive-fee-amount', 'average-quantity-customer-orders']
        ];
    }
}

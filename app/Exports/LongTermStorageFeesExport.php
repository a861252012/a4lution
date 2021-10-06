<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;

class LongTermStorageFeesExport implements FromArray
{
    use Exportable;

    public function array(): array
    {
        return [
            ['Account', 'snapshot-date', 'sku', 'fnsku', 'asin', 'product-name', 'Supplier Type', 'Supplier',
                'condition', 'qty-charged-12-mo-long-term-storage-fee', 'per-unit-volume', 'currency',
                '12-mo-long-terms-storage-fee', 'HKD', 'HKD Rate', 'qty-charged-6-mo-long-term-storage-fee',
                '6-mo-long-terms-storage-fee', 'volume-unit', 'country', 'enrolled-in-small-and-light'
            ]
        ];
    }
}

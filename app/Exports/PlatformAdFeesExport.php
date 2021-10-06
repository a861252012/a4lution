<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Excel;

class PlatformAdFeesExport implements FromArray
{
    use Exportable;

    public function array(): array
    {
        return [
            ['Client Code', 'Client Type', 'Platform', 'Account', 'Campagin Type', 'Campagin', 'Currency'
                , 'Impressions', 'Clicks', 'CTR', 'Spendings', 'Spendings (HKD)', 'CPC', 'Sales Qty', 'Sales Amount',
                'Sales Amount(HKD)', 'ACOS', 'Exchange Rate'
            ]
        ];
    }
}

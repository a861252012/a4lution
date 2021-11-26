<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;

class FirstMileShipmentFeeExport implements FromArray
{
    use Exportable;

    public function array(): array
    {
        return [
            [
                'Client Code',
                'IDS SKU',
                'Title',
                'ASIN',
                'FNSKU',
                'external-id',
                'Condition',
                'Who will prep?',
                'Prep Type',
                'Who will label?',
                'Shipped',
                'FBA shipment',
                'Shipment Type',
                'Date',
                'Account',
                'Ship From',
                'First Mile',
                'Last Mile (APC) -Original Currency （ESTIMATED）',
                'Last Mile (APC) -Original Currency （ACTUAL）',
                'Shipment Remark',
                'Currency (Last Mile)',
                'Exchange Rate',
                'Total',
                'Is Payment Settled',
                'Remark',
            ]
        ];
    }
}

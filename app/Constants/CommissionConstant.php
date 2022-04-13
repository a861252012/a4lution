<?php

namespace App\Constants;

class CommissionConstant
{
    const CALCULATION_TYPE_SKU = 1;
    const CALCULATION_TYPE_TIER = 2;
    const CALCULATION_TYPE_BASIC_RATE = 3;

    public static function map(): array
    {
        return [
            self::CALCULATION_TYPE_SKU => 'SKU',
            self::CALCULATION_TYPE_TIER => 'Tiered Rate',
            self::CALCULATION_TYPE_BASIC_RATE => 'Basic Rate',
        ];
    }
}

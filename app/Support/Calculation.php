<?php

namespace App\Support;

class Calculation
{
    //取浮點數到小數點後的特定位(不四捨五入)
    public function numberFormatPrecision(float $num, int $precision)
    {
        $precision = pow(10, $precision);
        return intval(($num * $precision)) / $precision;
    }
}
<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportService
{

    public function getUserErrorMsg($msg): string
    {
        return Str::contains($msg, 'Failed to parse time string') ? 'Date Format Error' : 'Internet Error';
    }

    public function transformDate($value, $format = 'Y-m-d H:i:s'): string
    {
        try {
            return Carbon::instance(Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return Carbon::parse($value)->format($format);
        }
    }
}
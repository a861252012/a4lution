<?php

namespace App\Repositories;

use App\Models\ExchangeRates;
use Illuminate\Support\Facades\DB;

class ExchangeRatesRepository
{
    public function __construct()
    {
    }

    public function getAllCurrency()
    {
        return ExchangeRates::selectRaw('DISTINCT base_currency')
            ->where('active', 1)
            ->pluck('base_currency');
    }
}

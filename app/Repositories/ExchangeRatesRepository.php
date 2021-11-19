<?php

namespace App\Repositories;

use App\Models\ExchangeRates;
use App\Support\LaravelLoggerUtil;
use Illuminate\Database\Eloquent\Collection;

class ExchangeRatesRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new ExchangeRates);
    }

    public function getAllCurrency()
    {
        return ExchangeRates::selectRaw('DISTINCT base_currency')
            ->where('active', 1)
            ->pluck('base_currency');
    }

    /**
     * @param string $date
     * @return ExchangeRate[]|Collection
     */
    public function getByQuotedDate(string $date): ?Collection
    {
        try {
            $exchangeRates = $this->model
                ->where('quoted_date', $date)
                ->active()
                ->get();

        } catch (\Throwable $e) {
            LaravelLoggerUtil::loggerException($e);
            $exchangeRates = Collection::make();
        }

        return $exchangeRates;
    }
}

<?php

namespace App\Repositories;

use App\Models\ExchangeRates;
use App\Support\LaravelLoggerUtil;
use Illuminate\Support\Facades\DB;
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

    // TODO: do scope [active=1] in model

    /**
     * @param string $date
     * @return Model[]|Collection
     */
    public function getByQuotedDate($date): ?Collection
    {
        try {
            $models = $this->model
                ->where('quoted_date', $date)
                ->where('active', 1)
                ->get();

        } catch (\Throwable $e) {
            LaravelLoggerUtil::loggerException($e);
            $models = Collection::make();
        }

        return $models;
    }
}

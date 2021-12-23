<?php

namespace App\Repositories;

use App\Constants\Currency;
use App\Models\ExchangeRate;
use App\Support\LaravelLoggerUtil;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ExchangeRateRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new ExchangeRate);
    }

    public function getAllCurrency()
    {
        return ExchangeRate::selectRaw('DISTINCT base_currency')
            ->active()
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

    public function getNewestActiveRate($orderBy, $quotedDate = null): ?Collection
    {
        try {
            $exchangeRates = $this->model
                ->when($quotedDate, fn ($q) => $q->where('quoted_date', Carbon::parse($quotedDate)->format('Y-m-d')))
                ->whereIn('base_currency', Currency::EXCHANGE_RATE)
                ->active()
                ->orderBy($orderBy, 'desc')
                ->take(18)
                ->get();
        } catch (\Throwable $e) {
            LaravelLoggerUtil::loggerException($e);
            $exchangeRates = Collection::make();
        }

        return $exchangeRates;
    }

    public function getHistoryRateByDateRange($currency, $startDate, $endDate): ?Collection
    {
        try {
            $exchangeRates = ExchangeRate::from('exchange_rates')
                ->leftJoin('users', function ($join) {
                    $join->on('users.id', '=', 'exchange_rates.updated_by');
                })
                ->select(
                    'exchange_rates.quoted_date',
                    'exchange_rates.base_currency',
                    'exchange_rates.quote_currency',
                    'exchange_rates.exchange_rate',
                    'exchange_rates.created_at',
                    'exchange_rates.updated_at',
                    'exchange_rates.active',
                    'users.user_name',
                )
                ->whereBetween(
                    'exchange_rates.created_at',
                    [
                        Carbon::parse($startDate)->startOfDay()->toDateTimeString(),
                        Carbon::parse($endDate)->endOfDay()->toDateTimeString()
                    ]
                )
                ->where('exchange_rates.base_currency', $currency)
                ->orderBy('exchange_rates.quoted_date', 'desc')
                ->get();
        } catch (\Throwable $e) {
            LaravelLoggerUtil::loggerException($e);
            $exchangeRates = Collection::make();
        }

        return $exchangeRates;
    }
}

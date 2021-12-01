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

    public function getNewestActiveRate($orderBy, $quotedDate = null): ?Collection
    {
        try {
            $exchangeRates = $this->model
                ->when($quotedDate, fn($q) => $q->where('quoted_date', Carbon::parse($quotedDate)->format('Y-m-d')))
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

    public function getSpecificRateByDateRange($currency, $startDate, $endDate): ?Collection
    {
        try {
            $exchangeRates = ExchangeRate::from('exchange_rates as e')
                ->join('users as u', 'u.id', '=', 'e.updated_by')
                ->select(
                    'e.quoted_date',
                    'e.base_currency',
                    'e.quote_currency',
                    'e.exchange_rate',
                    'e.created_at',
                    'e.updated_at',
                    'e.active',
                    'u.user_name',
                )
                ->whereBetween(
                    'e.created_at',
                    [
                        Carbon::parse($startDate)->startOfDay()->toDateTimeString(),
                        Carbon::parse($endDate)->endOfDay()->toDateTimeString()
                    ]
                )
                ->where('e.base_currency', $currency)
                ->orderBy('e.updated_at', 'desc')
                ->take(18)
                ->get();
        } catch (\Throwable $e) {
            LaravelLoggerUtil::loggerException($e);
            $exchangeRates = Collection::make();
        }

        return $exchangeRates;
    }
}

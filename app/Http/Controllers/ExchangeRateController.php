<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Repositories\ExchangeRateRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ExchangeRateController extends Controller
{
    public function index()
    {
        $data['lists'] = (new ExchangeRateRepository)->getNewestActiveRate('quoted_date')->map(function ($item) {
            $item->quoted_date = Carbon::parse($item->quoted_date)->format('F-Y');
            return $item;
        });

        return view('exchangeRate.index', $data);
    }

    public function create(Request $request): JsonResponse
    {
        $req = $request->except('_token');
        $date = Carbon::parse($req['quoted_date'])->format('Y-m-d');

        try {
            ExchangeRate::where('quoted_date', $date)->update(['active' => 0]);

            $data = collect($req['exchange_rate'])->map(fn($key, $val) => [
                'quoted_date' => $date,
                'base_currency' => $val,
                'quote_currency' => $req['quote_currency'],
                'exchange_rate' => $req['exchange_rate'][$val],
                'active' => 1,
                'created_at' => now()->toDateTimeString(),
                'created_by' => Auth::id(),
                'updated_at' => now()->toDateTimeString(),
                'updated_by' => Auth::id()
            ])->values()->all();

            ExchangeRate::insert($data);

            return response()->json(
                [
                    'status' => Response::HTTP_OK,
                    'msg' => 'OK'
                ]
            );
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'msg' => 'Update Rate Failed'
                ]
            );
        }
    }

    public function show(Request $request): JsonResponse
    {
        return response()->json(
            [
                'data' => (new ExchangeRateRepository)->getNewestActiveRate(
                    'updated_at',
                    $request->route('date')
                ),
                'status' => Response::HTTP_OK,
                'msg' => 'success'
            ]
        );
    }

    public function getExchangeRate(Request $request): JsonResponse
    {
        return response()->json(
            [
                'data' => (new ExchangeRateRepository)->getSpecificRateByDateRange(
                    $request->currency,
                    $request->startDate,
                    $request->endDate,
                ),
                'status' => Response::HTTP_OK,
                'msg' => 'success'
            ]
        );
    }
}

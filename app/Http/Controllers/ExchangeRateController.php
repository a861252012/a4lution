<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Repositories\ExchangeRateRepository;
use App\Repositories\SellerAccountRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ExchangeRateController extends Controller
{
    private ExchangeRateRepository $exchangeRateRepository;
    private SellerAccountRepository $sellerAccountRepo;
    private const QUOTE_CURRENCY = 'HKD';

    public function __construct(
        ExchangeRateRepository $exchangeRateRepository,
        SellerAccountRepository $sellerAccountRepository
    ) {
        $this->exchangeRateRepository = $exchangeRateRepository;
        $this->sellerAccountRepo = $sellerAccountRepository;
    }

    public function index(Request $request)
    {
        return view(
            'exchangeRate.index',
            [
                'lists' => $this->exchangeRateRepository->getNewestActiveRate(
                    'quoted_date',
                    $request->quoted_date
                )
            ]
        );
    }

    public function ajaxCreate(Request $request): JsonResponse
    {
        $date = Carbon::parse($request->quoted_date)->format('Y-m-d');

        try {
            ExchangeRate::where('quoted_date', $date)->update(['active' => 0]);

            $data = collect($request->exchange_rate)->map(fn ($val, $currency) => [
                'quoted_date' => $date,
                'base_currency' => $currency,
                'quote_currency' => self::QUOTE_CURRENCY,
                'exchange_rate' => $val,
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

    public function ajaxShow(Request $request): JsonResponse
    {
        return response()->json(
            [
                'data' => $this->exchangeRateRepository->getNewestActiveRate(
                    'updated_at',
                    $request->route('date')
                ),
                'status' => Response::HTTP_OK,
                'msg' => 'success'
            ]
        );
    }

    public function ajaxGetExchangeRate(Request $request): JsonResponse
    {
        return response()->json(
            [
                'data' => $this->exchangeRateRepository->getHistoryRateByDateRange(
                    $request->currency,
                    $request->startDate,
                    $request->endDate,
                ),
                'status' => Response::HTTP_OK,
                'msg' => 'success'
            ]
        );
    }

    public function sellerAccountView(Request $request)
    {
        return view(
            'exchangeRate.sellerAccount',
            [
                'lists' => $this->sellerAccountRepo->searchSellerAccountView(
                    $request->platform,
                    $request->is_a4_account
                )
            ]
        );
    }
}

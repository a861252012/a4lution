<?php

namespace App\Http\Controllers;

use App\Http\Requests\Billing\AjaxCreateRequest;
use App\Http\Requests\Billing\AjaxUpdateRequest;
use App\Models\Statement;
use App\Repositories\CustomerRepository;
use App\Repositories\ExchangeRateRepository;
use App\Repositories\StatementRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class BillingController extends Controller
{
    private CustomerRepository $customerRepo;
    private StatementRepository $statementRepo;

    public function __construct(
        CustomerRepository  $customerRepo,
        StatementRepository $statementRepo
    ) {
        $this->customerRepo = $customerRepo;
        $this->statementRepo = $statementRepo;
    }

    public function monthlyFeeTransactionView(Request $request)
    {
        $clientCodeList = $this->customerRepo->getAllClientCode(false);
        $currencyList = app(ExchangeRateRepository::class)->getAllCurrency();

        $lists = empty(count($request->all()))
            ? []
            : $this->statementRepo->getMonthlyFeeTransactionView(
                $request->billing_month,
                $request->client_code
            );

        return view('billing.monthlyFeeTransaction', compact('lists', 'clientCodeList', 'currencyList'));
    }

    public function ajaxGetMonthlyFee(Request $request): JsonResponse
    {
        $customer = $this->customerRepo->findByClientCode($request->client_code);

        return response()->json(
            [
                'status' => Response::HTTP_OK,
                'msg' => 'success',
                'data' => [
                    'monthly_fee' => sprintf(
                        "%s_%s",
                        optional($customer)->monthly_fee,
                        optional($customer)->currency
                    ),
                    'paid_amount' => optional($customer)->monthly_fee,
                    'currency' => optional($customer)->currency
                ]
            ]
        );
    }

    public function ajaxCreate(AjaxCreateRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            //如有重複資料則軟刪除
            if ($this->statementRepo->isDuplicated($request->client_code, $request->billing_month)) {
                $this->statementRepo->softDeleteDuplicated($request->client_code, $request->billing_month);
            }

            Statement::create($request->all());

            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Created Failed');
        }

        return response()->json(
            [
                'status' => Response::HTTP_OK,
                'msg' => 'success'
            ]
        );
    }

    public function ajaxDelete(Request $request): JsonResponse
    {
        try {
            $statement = Statement::findOrFail($request->id);
            $statement->active = 0;
            $statement->save();
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'delete failed');
        }

        return response()->json(
            [
                'msg' => 'deleted',
                'status' => Response::HTTP_OK,
            ]
        );
    }

    public function ajaxGetEditData(Request $request): JsonResponse
    {
        //dd($this->statementRepo->find($request->id));
        return response()->json(
            [
                'msg' => 'success',
                'status' => Response::HTTP_OK,
                'data' => $this->statementRepo->find($request->id)->toArray(),
            ]
        );
    }

    public function ajaxUpdate(AjaxUpdateRequest $request): JsonResponse
    {
        try {
            $this->statementRepo->update($request->id, $request->except(['_token', '_method']));
        } catch (\Throwable $e) {
            Log::error($e);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Updated Failed');
        }

        return response()->json(
            [
                'msg' => 'success',
                'status' => Response::HTTP_OK,
            ]
        );
    }
}

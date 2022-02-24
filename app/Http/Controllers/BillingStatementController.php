<?php

namespace App\Http\Controllers;

use App\Services\BillingStatementService;
use App\Http\Requests\BillingStatement\AjaxStoreRequest;

class BillingStatementController extends Controller
{
    private BillingStatementService $billingStatementService;

    public function __construct(BillingStatementService $billingStatementService)
    {
        $this->billingStatementService = $billingStatementService;
    }

    public function ajaxStore(AjaxStoreRequest $request)
    {
        $this->billingStatementService->create($request);
    }
}

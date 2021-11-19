<?php

namespace App\Http\Controllers;

use App\Services\BillingStatementService;
use App\Http\Requests\BillingStatement\AjaxStoreRequest;

class BillingStatementController extends Controller
{
    private $billingStatementsService;

    public function __construct(BillingStatementService $billingStatementsService)
    {
        $this->billingStatementsService = $billingStatementsService;
    }

    public function ajaxStore(AjaxStoreRequest $request)
    {
        $this->billingStatementsService->create($request);
    }
}

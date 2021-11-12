<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BillingStatementsService;

class BillingStatementController extends Controller
{
    private $billingStatementsService;

    public function __construct(BillingStatementsService $billingStatementsService)
    {
        $this->billingStatementsService = $billingStatementsService;
    }

    // TODO: create request
    public function ajaxStore(Request $request)
    {
        $this->billingStatementsService->create($request);
    }
}

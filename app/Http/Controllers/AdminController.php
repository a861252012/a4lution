<?php

namespace App\Http\Controllers;

use App\Models\BillingStatements;
use App\Repositories\BillingStatementRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\EmployeeCommission;
use App\Services\AdminService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    private $billingStatementRepository;
    private $adminService;

    public function __construct(
        BillingStatementRepository $billingStatementRepository,
        AdminService               $adminService
    )
    {
        $this->billingStatementRepository = $billingStatementRepository;
        $this->adminService = $adminService;
    }

    public function approvalAdminView()
    {
        return view('admin.approvalAdmin');
    }

    public function batchApprove(Request $request): \Illuminate\Http\JsonResponse
    {
        $date = Carbon::parse($request->route('date'))->format('Y-m-01');

        try {
            BillingStatements::where('active', 1)
                ->where('report_date', $date)
                ->update([
                    'cutoff_time' => Carbon::now()->copy()->toDateTimeString()
                ]);

            Artisan::call('calculate_commission', [
                '--date' => $date,
            ]);

            return response()->json(
                [
                    'status' => 200,
                    'msg' => 'success'
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 999,
                    'msg' => 'error',
                ]
            );
        }
    }

    public function revokeApprove(Request $request): \Illuminate\Http\JsonResponse
    {
        $date = Carbon::parse($request->route('date'))->format('Y-m-01');

        if (!Auth::user()->isManager()) {
            return response()->json(
                [
                    'status' => 400,
                    'msg' => 'Permission Denied'
                ]
            );
        }

        try {
            $res = $this->adminService->revokeApprove($date);

            if ($res === 400) {
                return response()->json(
                    [
                        'status' => 500,
                        'msg' => 'API ERROR'
                    ]
                );
            }

            return response()->json(
                [
                    'status' => 200,
                    'msg' => 'success'
                ]
            );
        } catch (\Exception $e) {
            Log::error("revokeApprove error: {$e}");

            return response()->json(
                [
                    'status' => 500,
                    'msg' => 'error',
                ]
            );
        }
    }


}

<?php

namespace App\Http\Controllers;

use App\Models\BillingStatements;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\AdminService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    private $adminService;

    public function __construct(
        AdminService $adminService
    )
    {
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

    public function revokeApprove(Request $request)
    {
        $date = Carbon::parse($request->route('date'))->format('Y-m-01');

        if (!Auth::user()->isManager()) {
            abort(response()->json('Unauthorized', 401));
        }

        try {
            $res = $this->adminService->revokeApprove($date);

            if ($res === 500) {
                abort(response()->json('API ERROR', 500));
            }

            return response()->json(
                [
                    'status' => 200,
                    'msg' => 'success'
                ]
            );
        } catch (\Exception $e) {
            Log::error("revokeApprove error: {$e}");
            abort(response()->json('API ERROR', 500));
        }
    }
}

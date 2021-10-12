<?php

namespace App\Http\Controllers;

use App\Models\BillingStatements;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\EmployeeCommission;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{

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
}

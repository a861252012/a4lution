<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\EmployeeCommission;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{

    public function commissionPayView(Request $request)
    {
        $data = $request->all();
        $data['lists'] = [];

        if (count($request->all())) {
            $query = EmployeeCommission::from('employee_commissions as a')
                ->leftJoin('users as u', function ($join) {
                    $join->on('a.employee_user_id', '=', 'u.id');
                })
                ->join('employee_commission_entries as r', function ($join) {
                    $join->on('a.id', '=', 'r.employee_commissions_id');
                    $join->where('r.active', 1);
                })
                ->select(
                    'u.id',
                    'u.user_name',
                    'a.role_name',
                    'u.company_type',
                    'u.region',
                    'a.currency',
                    'a.customer_qty',
                    'a.report_date',
                    'a.total_billed_commissions_amount',
                    DB::RAW("SUM(CASE WHEN r.billing_statement_id IS NOT NULL THEN 1 ELSE 0 END) AS 'billed_qty'"),
                    DB::RAW("IFNULL(a.extra_monthly_fee_amount, 0) AS 'extra_monthly_fee'"),
                    DB::RAW("IFNULL(a.extra_ops_commission_amount, 0) AS 'extra_ops_commission'"),
                    DB::RAW("SUM(IFNULL(r.monthly_fee, 0) + IFNULL(r.cross_sales, 0) + IFNULL(r.ops_commission, 0))
                    AS 'total_employee_sharing'"),
                )
                ->where('a.active', 1);

            if ($request->input('report_date')) {
                $reportDate = Carbon::parse($request->input('report_date'))->format('Y-m-01');

                $query->where('a.report_date', $reportDate);
            }

            if ($request->input('user_name')) {
                $query->where('u.user_name', $request->input('user_name'));
            }

            $data['lists'] = $query->groupBy(['u.user_name', 'a.report_date'])
                ->paginate(15);
        }

        return view('employee.commissionPay', $data);
    }

    public function commissionDetail(Request $request): \Illuminate\Http\JsonResponse
    {
        $userID = $request->route('userID');
        $date = $request->route('date');

        $query = EmployeeCommission::query()
            ->from('employee_commissions as a')
            ->join('employee_commission_entries as c', function ($join) {
                $join->on('a.id', '=', 'c.employee_commissions_id');
                $join->where('c.active', 1);
            })
            ->join('billing_statements as b', function ($join) {
                $join->on('c.billing_statement_id', '=', 'b.id');
                $join->where('b.active', 1);
            })
            ->select(
                'c.client_code',
                'c.contract_length',
                'b.created_at',
                'b.avolution_commission',
                'c.monthly_fee',
                'c.cross_sales',
                'c.ops_commission',
            )
            ->where('a.active', 1);

        if ($date) {
            $query->where('a.report_date', $date);
        }

        if ($userID) {
            $query->where('a.employee_user_id', $userID);
        }

        $data = $query->get();

        //加上當下時間以便後續寫入DB
//        $test = collect($detail)->map(function ($item) {
////            $item['created_at'] = Carbon::parse($item['created_at'])->toDateTimeString();
//            $item['created_at'] = Carbon::parse($item['created_at'])->format('Y-m-d');
//
//            return $item;
//        });
//
//        $multiplied = collect($detail)->map(function ($item, $key) {
//            return Carbon::parse($item['created_at'])->format('Y-m-d');
//        });

        $filtered = collect($data)->map(function ($item, $key) {
            $item['created_at'] = Carbon::parse($item['created_at'])->toDateTimeString();
            return $item;
        });

        return response()->json(['data' => $filtered]);
    }
}

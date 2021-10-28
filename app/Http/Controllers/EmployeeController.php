<?php

namespace App\Http\Controllers;

use App\Resources\Employee\DetailResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\EmployeeCommission;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{

    public function commissionPayView()
    {
        $data = request()->all();
        $data['lists'] = [];

        if (count(request()->all())) {
            $data['lists'] = EmployeeCommission::from('employee_commissions as a')
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
                    DB::RAW("DATE_FORMAT(a.report_date,'%b-%Y') AS report_date"),
                    'a.total_billed_commissions_amount',
                    DB::RAW("SUM(CASE WHEN r.billing_statement_id IS NOT NULL THEN 1 ELSE 0 END) AS 'billed_qty'"),
                    DB::RAW("IFNULL(a.extra_monthly_fee_amount, 0) AS 'extra_monthly_fee'"),
                    DB::RAW("IFNULL(a.extra_ops_commission_amount, 0) AS 'extra_ops_commission'"),
                    DB::RAW("SUM(IFNULL(r.monthly_fee, 0) + IFNULL(r.cross_sales, 0) + IFNULL(r.ops_commission, 0))
                    AS 'total_employee_sharing'"),
                )
                ->where('a.active', 1)
                ->when(request()->input('report_date'), function ($query) {
                    $reportDate = Carbon::parse(request()->input('report_date'))->format('Y-m-01');
                    return $query->where('a.report_date', $reportDate);
                })
                ->when(request()->input('client_code'), function ($query) {
                    return $query->where('r.client_code', request()->input('client_code'));
                })
                ->when(request()->input('user_name'), function ($query) {
                    return $query->where('u.user_name', request()->input('user_name'));
                })
                ->groupBy(['u.user_name', 'a.report_date'])
                ->paginate(15);
        }

        return view('employee.commissionPay', $data);
    }

    public function commissionDetail(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $userID = $request->route('userID');
        $date = Carbon::parse($request->route('date'))->format('Y-m-d');
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

        return DetailResource::collection($data);
    }
}

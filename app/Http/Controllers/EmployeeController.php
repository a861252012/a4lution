<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use App\Models\EmployeeCommission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class EmployeeController extends Controller
{

    public function commissionPayView(Request $request)
    {
        $data['lists'] = [];

//        if (count($request->all())) {

        //table layer 1
        $data['lists'] = EmployeeCommission::from('employee_commissions as a')
            ->leftJoin('users as u', function ($join) {
                $join->on('a.employee_user_id', '=', 'u.id');
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
                'a.extra_monthly_fee_amount',
                'a.extra_ops_commission_amount',
                'a.total_billed_commissions_amount',
            )
            ->where('a.active', 1)
            ->where('a.report_date', '2021-08-01')//TODO
//            ->where('u.user_name', 'billy') //TODO
            ->paginate(15);

        //table layer 2
//        foreach ($data['lists'] as $key => $item) {
//            $data['lists'][$key]['details'] = EmployeeCommission::from('employee_commissions as a')
//                ->join('employee_commission_entries as c', 'a.id', '=', 'c.employee_commissions_id')
//                ->join('billing_statements as b', 'c.billing_statement_id', '=', 'b.id')
//                ->select(
//                    'c.client_code',
//                    'c.contract_length',
//                    'b.created_at',
//                    'b.avolution_commission',
//                    'c.monthly_fee',
//                    'c.cross_sales',
//                    'c.ops_commission',
//                )
//                ->where('a.report_date', '2021-08-01')//TODO
//                ->where('a.employee_user_id', $item->id)
//                ->get()
//                ->toArray();
//        }

//        $layer2 = EmployeeCommission::from('employee_commissions as a')
//            ->join('employee_commission_entries as c', 'a.id', '=', 'c.employee_commissions_id')
//            ->join('billing_statements as b', ' c.billing_statement_id', '=', 'b.id ')
//            ->select(
//                'c.client_code',
//                'c.contract_length',
//                'b.created_at',
//                'b.avolution_commission',
//                'c.monthly_fee',
//                'c.cross_sales',
//                ' c.ops_commission',
//            )
//            ->where('a.report_date', '2021-08-01')
//            ->where('a.employee_user_id', 11)
//            ->get();

        return view('employee.commissionPay', $data);
    }

    public function commissionDetail(Request $request): \Illuminate\Http\JsonResponse
    {
        $userID = $request->route('userID');
        $date = $request->route('date');

        $query = EmployeeCommission::query()
            ->from('employee_commissions as a')
            ->join('employee_commission_entries as c', 'a.id', '=', 'c.employee_commissions_id')
            ->join('billing_statements as b', 'c.billing_statement_id', '=', 'b.id')
            ->select(
                'c.client_code',
                'c.contract_length',
                'b.created_at',
                'b.avolution_commission',
                'c.monthly_fee',
                'c.cross_sales',
                'c.ops_commission',
            );

//            ->where('a.report_date', '2021-08-01')//TODO
//            ->where('a.employee_user_id', 2)
//            ->get();
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
//            $item['created_at'] = '2021-01-01';
            return $item;
        });

        return response()->json(['data' => $filtered]);
    }
}

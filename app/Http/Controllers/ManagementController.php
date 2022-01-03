<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\EmployeeMonthlyFeeRule;
use Illuminate\Http\Request;

class ManagementController extends Controller
{
    public function __construct()
    {
    }

    public function monthlyFeeView(Request $request)
    {
        $data['lists'] = [];

        if (count($request->all())) {
            $data['lists'] = Customer::query()
                ->when($request->client_code, function ($q, $clientCode) {
                    return $q->where('client_code', $clientCode);
                })
                ->when($request->status_type !== 'all', function ($q) {
                    return $q->where('active', request('status_type'));
                })
                ->paginate(50);

//            $data['lists'] = EmployeeMonthlyFeeRule::query()
//                ->with('roles')
//                ->when($request->client_code !== 'all', function ($q, $clientCode) {
//                    return $q->where('client_code', $clientCode);
//                })
//                ->when($request->status_type !== 'all', function ($q) {
//                    return $q->where('active', request('status_type'));
//                })
//                ->orderByDesc('client_code')
//                ->orderByDesc('role_id')
//                ->orderByDesc('updated_at')
//                ->paginate(50);
        }

        return view('management.monthlyFee', $data);
    }

    public function ajaxEmployeeRule(Request $request)
    {
        $list = EmployeeMonthlyFeeRule::query()
            ->with('roles')
            ->when($request->route('clientCode'), function ($q, $clientCode) {
                return $q->where('client_code', $clientCode);
            })
            ->where('active', 1)
            ->orderByDesc('client_code')
            ->orderByDesc('role_id')
            ->orderByDesc('updated_at')
            ->get()
            ->toArray();

        return response()->json(
            [
                'status' => 200,
                'msg' => 'success',
                'data' => $list
            ]
        );
    }
}

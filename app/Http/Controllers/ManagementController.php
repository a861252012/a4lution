<?php

namespace App\Http\Controllers;

use App\Constants\RoleConstant;
use App\Models\Customer;
use App\Models\EmployeeMonthlyFeeRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ManagementController extends Controller
{
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
        }

        return view('management.monthlyFee', $data);
    }

    public function ajaxEmployeeRule(Request $request): JsonResponse
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
                'status' => Response::HTTP_OK,
                'msg' => 'success',
                'data' => $list
            ]
        );
    }

    public function ajaxEditView(Request $request)
    {
        $clientCode = $request->clientCode;
        $role = $this->getRole();

        return view('management.edit', compact('clientCode', 'role'));
    }

    private function getRole(): array
    {
        return [
            'sales' => RoleConstant::SALES,
            'operation' => RoleConstant::OPERATION,
            'account_service' => RoleConstant::ACCOUNT_SERVICE
        ];
    }

    public function ajaxCreateSetting(Request $request)
    {
        $req = $request->except('_token');
        $roleCollection = collect($this->getRole());

        DB::beginTransaction();
        try {
            EmployeeMonthlyFeeRule::whereIn('role_id', $roleCollection->values())
                ->where('client_code', $request->clientCode)
                ->active()
                ->update(['active' => 0]);

            $formattedSetting = $roleCollection->map(fn ($roleID, $roleName) => [
                'client_code' => $request->clientCode,
                'role_id' => $roleID,
                'is_tiered_rate' => $req['is_tiered_rate'][$roleID],
                'rate_base' => $req['rate_base'][$roleID] / 100,
                'rate' => $req['rate'][$roleID] / 100,
                'currency' => 'HKD',
                'threshold' => $req['threshold'][$roleID],
                'tier_1_first_year' => $req['tier_1_first_year'][$roleID] / 100,
                'tier_2_first_year' => $req['tier_2_first_year'][$roleID] / 100,
                'tier_1_over_a_year' => $req['tier_1_over_a_year'][$roleID] / 100,
                'tier_2_over_a_year' => $req['tier_2_over_a_year'][$roleID] / 100,
                'active' => 1,
                'created_at' => now()->toDateTimeString(),
                'created_by' => Auth::id(),
                'updated_at' => now()->toDateTimeString(),
                'updated_by' => Auth::id()
            ])->toArray();

            EmployeeMonthlyFeeRule::insert($formattedSetting);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Updated Failed');
        }

        return response()->json(
            [
                'status' => Response::HTTP_OK,
                'msg' => 'success',
            ]
        );
    }
}

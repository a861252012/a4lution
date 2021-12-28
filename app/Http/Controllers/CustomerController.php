<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use App\Constants\Commission;
use App\Models\CommissionSetting;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Customer\IndexRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Customer\AjaxUpdateRequest;

class CustomerController extends Controller
{
    public function index(IndexRequest $request)
    {
        
        $query = [
            'client_code' => $request->client_code ?? null,
            'active' => $request->active ?? null,
            'sales_region' => $request->sales_region ?? null,
        ];

        if (empty($request->all())) {
            $customers = [];
            return view('customer.index', compact('customers', 'query'));
        }

        $customers = Customer::query()
            ->with('salesReps', 'accountServices', 'updater')
            ->when($request->client_code, fn($q) => $q->where('client_code', $request->client_code))
            ->when(isset($request->active), fn($q) => $q->where('active', $request->active))
            ->when($request->sales_region, fn($q) => $q->where('sales_region', $request->sales_region))
            ->oldest('client_code')
            ->paginate();

        return view('customer.index', compact('customers', 'query'));
    }

    public function ajaxEdit(string $client_code)
    {
        $customer = Customer::query()
            ->with('users.roles', 'commission')
            ->where('client_code', $client_code)
            ->first();

        $callback = function($user) {
            return [
                'id' => $user->id,
                'user_name' => $user->user_name,
                'role_id' => optional($user->roles->first())->id,
                'role_desc' => optional($user->roles->first())->role_desc,
            ];
        };

        $selectedUsers = $customer->users->map($callback);
        $unSelectedUsers = User::with('roles')->get()
            ->diff($customer->users)
            ->map($callback);

        return view('customer.edit', compact('customer', 'selectedUsers', 'unSelectedUsers'));
    }

    public function ajaxUpdate(AjaxUpdateRequest $request, string $client_code)
    {
        // dd($request->all());
        // 更新 customer
        $result = Customer::find($client_code)
            ->update([
                'client_code' => $request->client_code,
                'company_name' => $request->company_name,
                'contact_person' => $request->company_contact,
                'address1' => $request->street1,
                'address2' => $request->street2,
                'city' => $request->city,
                'district' => $request->district,
                'zip' => $request->zip,
                'country' => $request->country,
                'sales_region' => $request->sales_region,
                'contract_date' => $request->contract_date,
                'active' => $request->active,
            ]);

        if (!$result) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Updated Failed');
        }

        // 更新 customer_relations
        $customerRelationsData = [];
        if ($request->staff_members) {
            foreach (explode('|', $request->staff_members) as $user_id) {
                $customerRelationsData[$user_id] = [
                    'role_id' => $customerRelationsData[$user_id] = User::find($user_id)->roles->first()->id,
                    'active' => 1,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ];
            }
        }

        $customer = Customer::find($client_code);
        $customer->customerRelation()->update(['active' => 0]);
        $customer->users()->syncWithoutDetaching($customerRelationsData);

        // 更新 commission_settings
        $commissionData = collect([
            'calculation_type' => $request->calculation_type ?? Commission::CALCULATION_TYPE_BASIC_RATE,
            'basic_rate' => $request->basic_rate ? $request->basic_rate/100 : '',
            'promotion_threshold' => $request->promotion_threshold,
            'tier_promotion' => $request->tier_promotion,
            'tier_1_threshold' => $request->tier_1_threshold,
            'tier_1_amount' => $request->tier_1_amount,
            'tier_1_rate' => $request->tier_1_rate  ? $request->tier_1_rate/100 : '',
            'tier_2_threshold' => $request->tier_2_threshold,
            'tier_2_amount' => $request->tier_2_amount,
            'tier_2_rate' => $request->tier_2_rate  ? $request->tier_2_rate/100 : '',
            'tier_3_threshold' => $request->tier_3_threshold,
            'tier_3_amount' => $request->tier_3_amount,
            'tier_3_rate' => $request->tier_3_rate  ? $request->tier_3_rate/100 : '',
            'tier_4_threshold' => $request->tier_4_threshold,
            'tier_4_amount' => $request->tier_4_amount,
            'tier_4_rate' => $request->tier_4_rate  ? $request->tier_4_rate/100 : '',
            'tier_top_amount' => $request->tier_top_amount,
            'tier_top_rate' => $request->tier_top_rate  ? $request->tier_top_rate/100 : '',
            'promotion_threshold' => $request->percentage_of_promotion ? (100 - $request->percentage_of_promotion)/100 : '',
            'tier_promotion' => $request->tier_promotion ? $request->tier_promotion/100 : '',
        ])->filter()->toArray();

        CommissionSetting::updateOrCreate(
            ['client_code' => $customer->client_code, 'active' => 1],
            $commissionData,
        );

    }
}

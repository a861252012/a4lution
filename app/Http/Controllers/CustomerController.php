<?php

namespace App\Http\Controllers;

use App\Constants\Commission;
use App\Models\User;
use App\Models\Customer;
use App\Http\Requests\Customer\IndexRequest;
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
            ->with('salesReps')
            ->when($request->client_code, fn($q) => $q->where('client_code', $request->client_code))
            ->when($request->active, fn($q) => $q->where('active', $request->active))
            ->when($request->sales_region, fn($q) => $q->where('sales_region', $request->sales_region))
            ->oldest('client_code')
            ->paginate();

        return view('customer.index', compact('customers', 'query'));
    }

    public function ajaxEdit(string $client_code)
    {
        $customer = Customer::query()
            ->with('salesReps', 'commission')
            ->where('client_code', $client_code)
            ->first();

        // dd($customer);

        $selectedSalesReps = $customer->salesReps->pluck('user_name', 'id')->toArray();
        $unSelectedSalesReps = array_diff_key(
            User::all()->pluck('user_name', 'id')->toArray(), 
            $selectedSalesReps
        );

        return view('customer.edit', compact('customer', 'selectedSalesReps', 'unSelectedSalesReps'));
    }

    public function ajaxUpdate(AjaxUpdateRequest $request, string $client_code)
    {
        // dd(request()->all());

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
            // abort();
        }

        // 更新 customer_relations
        $customerRelationsData = [];
        if ($request->sales_reps) {
            foreach (explode('|', $request->sales_reps) as $user_id) {
                $customerRelationsData[$user_id] = [
                    'role_id' => $customerRelationsData[$user_id] = User::find($user_id)->roles->first->id
                ];
            }
        }
        $customer = Customer::find($client_code);
        $customer->salesReps()->sync($customerRelationsData);

        // 更新 commission_settings
        $commissionData = collect([
            'calculate_type' => $request->calculate_type ?? Commission::CALCULATE_TYPE_BASIC_RATE,
            'basic_rate' => $request->basic_rate,
            'promotion_threshold' => $request->promotion_threshold,
            'tier_promotion' => $request->tier_promotion,
            'tier_1_threshold' => $request->tier_1_threshold,
            'tier_1_amount' => $request->tier_1_amount,
            'tier_1_rate' => $request->tier_1_rate,
            'tier_2_threshold' => $request->tier_2_threshold,
            'tier_2_amount' => $request->tier_2_amount,
            'tier_2_rate' => $request->tier_2_rate,
            'tier_3_threshold' => $request->tier_3_threshold,
            'tier_3_amount' => $request->tier_3_amount,
            'tier_3_rate' => $request->tier_3_rate,
            'tier_4_threshold' => $request->tier_4_threshold,
            'tier_4_amount' => $request->tier_4_amount,
            'tier_4_rate' => $request->tier_4_rate,
            'tier_top_amount' => $request->tier_top_amount,
            'tier_top_rate' => $request->tier_top_rate,
        ])->filter()->toArray();

        if ($customer->commission()->exists()) {
            $customer->commission()->update($commissionData);
        } else {
            $customer->commission()->create($commissionData);
        }

    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use App\Http\Requests\Customer\IndexRequest;
use App\Http\Requests\Customer\AjaxEditRequest;

class CustomerController extends Controller
{
    public function index(IndexRequest $request)
    {
        $query = [
            'client_code' => $request->client_code ?? null,
            'active' => $request->active ?? null,
            'sales_region' => $request->sales_region ?? null,
        ];

        $customers = Customer::query()
            ->with('salesReps')
            ->when($request->client_code, fn($q) => $q->where('client_code', $request->client_code))
            ->when($request->active, fn($q) => $q->where('active', $request->active))
            ->when($request->sales_region, fn($q) => $q->where('sales_region', $request->sales_region))
            ->oldest('client_code')
            ->paginate();

        return view('customer.index', compact('customers', 'query'));
    }

    public function ajaxEdit(AjaxEditRequest $request)
    {
        $customer = Customer::query()
            ->with('salesReps', 'commission')
            ->where('client_code', $request->client_code)
            ->first();

        // dd($customer);

        $selectedSalesReps = $customer->salesReps->pluck('user_name', 'id')->toArray();
        $unSelectedSalesReps = array_diff_key(
            User::all()->pluck('user_name', 'id')->toArray(), 
            $selectedSalesReps
        );

        return view('customer.edit', compact('customer', 'selectedSalesReps', 'unSelectedSalesReps'));
    }

    public function ajaxUpdate()
    {
        dd(request()->all());
    }
}

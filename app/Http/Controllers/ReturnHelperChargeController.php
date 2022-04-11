<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ReturnHelperCharge;
use Illuminate\Support\Facades\DB;

class ReturnHelperChargeController
{
    public function index()
    {
        $reportDate = request('report_date') ?? now()->subMonth()->format('Y-m');
        $reportDateCarbon = Carbon::parse($reportDate);
        
        $returnHelperCharges = empty(request()->all()) 
            ? [] 
            : ReturnHelperCharge::query()
                ->select([
                    'report_date',
                    'supplier',
                    'transaction_number',
                    'transaction_name',
                    'amount',
                    DB::raw('(return_helper_charges.amount * exchange_rates.exchange_rate) AS amount_hkd'),
                    'return_helper_charges.created_at'
                ])
                ->leftJoin('exchange_rates', function ($q) {
                    $q->whereColumn('return_helper_charges.report_date', 'exchange_rates.quoted_date')
                        ->whereColumn('return_helper_charges.currency_code', 'exchange_rates.base_currency')
                        ->where('exchange_rates.active', 1);
                })
                ->when(request('supplier'), fn($q, $supplier) => $q->where('return_helper_charges.supplier', $supplier))
                ->whereBetween('return_helper_charges.report_date', [
                    $reportDateCarbon->startOfMonth()->toDateString(), 
                    $reportDateCarbon->endOfMonth()->toDateString()
                ])
                ->active()
                ->oldest('report_date')
                ->oldest('transaction_number')
                ->oldest('supplier')
                ->paginate();

        return view('fee.returnHelperCharge.index', compact('returnHelperCharges', 'reportDate'));
    }
}

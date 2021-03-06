<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ContinStorageFee;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContinStorage\AjaxUpdateRequest;

class ContinStorageController extends Controller
{
    public function index()
    {
        $reportDate = request('report_date') ?? now()->subMonth()->format('Y-m');
        $reportDateCarbon = Carbon::parse($reportDate);
        
        $continStorageFees = empty(request()->all()) 
            ? [] 
            : ContinStorageFee::query()
                ->when(request('supplier'), fn($q, $supplier) => $q->where('supplier', $supplier))
                ->whereBetween('report_date', [
                    $reportDateCarbon->startOfMonth()->toDateString(), 
                    $reportDateCarbon->endOfMonth()->toDateString()
                ])
                ->active()
                ->orderBy('report_date')
                ->orderBy('transaction_no')
                ->orderBy('supplier')
                ->paginate();

        return view('fee.continStorage.index', compact('continStorageFees', 'reportDate'));
    }

    public function ajaxUpdate(AjaxUpdateRequest $request)
    {
        ContinStorageFee::findOrFail($request->id)
            ->update([
                $request->col => $request->value,
                'is_revised' => 1,
            ]);
    }
}
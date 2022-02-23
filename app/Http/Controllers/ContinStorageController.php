<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ContinStorageFee;
use App\Http\Controllers\Controller;

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
                ->paginate();

        return view('fee.continStorage.index', compact('continStorageFees', 'reportDate'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Imports\SkuCommissionImport;
use App\Models\CommissionSkuSetting;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\SkuCommission\IndexRequest;
use App\Http\Requests\SkuCommission\ajaxUploadStoreRequest;

class SkuCommissionController extends Controller
{
    public function index(IndexRequest $request)
    {
        $query = [
            'client_code' => $request->client_code ?? null,
            'sku' => $request->sku ?? null,
        ];

        $skuCommissions = empty($request->all()) 
            ? [] 
            : CommissionSkuSetting::query()
                ->with('updater')
                ->when($request->client_code, fn($q) => $q->where('client_code', $request->client_code))
                ->when($request->sku, fn($q) => $q->where('sku', $request->sku))
                ->active()
                ->oldest('client_code')
                ->oldest('site')
                ->oldest('sku')
                ->paginate();

        return view('skuCommission.index', compact('skuCommissions', 'query'));
    }

    public function ajaxUpload()
    {
        return view('skuCommission.upload');
    }

    public function ajaxUploadStore(ajaxUploadStoreRequest $request)
    {
        $clientCode = $request->client_code;
        $skuCommissionImport = new SkuCommissionImport($clientCode);

        // 檢查 excel title 排序是否正確
        $titles = ['Site','Currency','SKU','Threshold','Basic Rate (<Threshold)','Higher Rate (>=Threshold)'];
        $fileTitles = $skuCommissionImport->toArray($request->file('file'))[0][0];

        foreach ($titles as $index => $title) {
            if ($fileTitles[$index] <> $title) {
                abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'File title columns incorrect!');
            }
        }

        if (CommissionSkuSetting::whereClientCode($clientCode)->active()->exists()) {
            CommissionSkuSetting::whereClientCode($clientCode)
                ->active()
                ->update(['active' => 0]);
        }

        Excel::import(
            $skuCommissionImport,
            $request->file('file')
        );
    }
}
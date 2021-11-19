<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Console\Commands\OrderDataSync;
use App\Repositories\OrderProductsRepository;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    public function test()
    {
        $orderProductRepository = new OrderProductsRepository();

        dump($orderProductRepository->getSkuAvolutionCommission('S53A', 202108));
        $res = round($orderProductRepository->getSkuAvolutionCommission('S53A', 202108), 2) ?? 0;
        dd($res);
        try {
            $data = Customer::select('client_code')
                ->where('active', 1)
                ->pluck('client_code');

            return response()->json(
                [
                    'status' => 200,
                    'msg' => 'success',
                    'data' => $data
                ]
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(
                [
                    'status' => 999,
                    'msg' => 'error',
                    'data' => []
                ]
            );
        }
    }

    public function orderSync()
    {
//        OrderDataSync::dispatch()->onQueue('processing');
        $this->dispatch(new OrderDataSync);
    }


    public function getTest(array $fees, array $keys = []): float
    {

//        $rankScore = collect($reward)->map(function ($item, $key) {
//            return collect($item)->sum();
//        })->sum();

        $feesCollection = collect($fees);

        if ($keys) {
            $feesCollection = collect($fees)->only($keys);
        }

//        return $feesCollection->map(function ($val) {
//            return (float)$val;
//        })->sum();
        return $feesCollection->map(function ($val) {
            return (float)$val;
        })->sum();

//        $sum = 0;

//        foreach ($billings as $val) {
//            $sum += (float)$val;
//        }
//        return $sum;
//
//        $whiteLists = array_intersect_key($fees, array_flip($keys));
//        ->map(function ($item) {
//            $item['created_at'] = date('Y-m-d h:i:s');
//
//            return $item;
//        })->toArray();
//
//        $sumOfWhiteLists = 0;
//
//        //TODO
//        foreach ($whiteLists as $v) {
//            $sumOfWhiteLists += $v;
//        }
//
//        return (float)$sumOfWhiteLists;
    }
}

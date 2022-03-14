<?php

namespace App\Services\SalesReportImport;

use Carbon\Carbon;
use App\Models\BatchJob;
use App\Services\ImportService;
use Illuminate\Support\Facades\DB;
use App\Constants\BatchJobConstant;
use Illuminate\Support\Facades\Log;
use App\Models\FirstMileShipmentFee;
use Illuminate\Support\LazyCollection;

class FirstMileShipmentFeeImportService
{
    public function import(LazyCollection $collection, int $batchId, Carbon $reportDate, int $userId)
    {
        $batchJob = BatchJob::findOrFail($batchId);

        $collection = $collection->skip(1); // skip header
        foreach ($collection->chunk(1000) as $fees) {

            DB::beginTransaction();

            try {
                $data = [];
                foreach ($fees as $fee) {
                    if (isset($fee['client_code'])) {
                        $data[] = [
                            'client_code' => $fee['client_code'],
                            'ids_sku' => $fee['ids_sku'],
                            'title' => $fee['title'],
                            'asin' => $fee['asin'],
                            'fnsku' => $fee['fnsku'],
                            'external_id' => $fee['external_id'],
                            'condition' => $fee['condition'],
                            'who_will_prep' => $fee['who_will_prep'],
                            'prep_type' => $fee['prep_type'],
                            'who_will_label' => $fee['who_will_label'],
                            'shipped' => $fee['shipped'],
                            'fba_shipment' => $fee['fba_shipment'],
                            'shipment_type' => $fee['shipment_type'],
                            'date' => $fee['date'],
                            'account' => $fee['account'],
                            'ship_from' => $fee['ship_from'],
                            'first_mile' => $fee['first_mile'],
                            'last_mile_est_orig' => $fee['last_mile_apc_original_currency_estimated'],
                            'last_mile_act_orig' => $fee['last_mile_apc_original_currency_actual'],
                            'shipment_remark' => $fee['shipment_remark'],
                            'currency_last_mile' => $fee['currency_last_mile'],
                            'exchange_rate' => $fee['exchange_rate'],
                            'total' => $fee['total'],
                            'is_payment_settled' => $fee['is_payment_settled'],
                            'remark' => $fee['remark'],
                            'upload_id' => $batchId,
                            'report_date' => $reportDate->toDateString(),
                            'active' => 1,
                            'created_at' => date('Y-m-d h:i:s'),
                            'created_by' => $userId,
                            // 'updated_at' => date('Y-m-d h:i:s'),
                            // 'updated_by' => $userId,
                        ];
                    }
                }

                FirstMileShipmentFee::insert($data);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();

                Log::channel('daily_queue_import')
                    ->error("[A4lutionSalesReport.errors]" . $e);

                $batchJob->firstMileShipmentFees()->update(['active' => 0]);

                $batchJob->update([
                    'status' => BatchJobConstant::STATUS_FAILED,
                    'total_count' => 0,
                    'exit_message' => $e->getMessage(),
                    'user_error_msg' => (new ImportService)->getUserErrorMsg($e->getMessage())
                ]);

                return;
            }
        }
        

        FirstMileShipmentFee::where('report_date', $reportDate->toDateString())
            ->where('upload_id', '!=', $batchId)
            ->active()
            ->update(['active' => 0]);

        $batchJob->update([
            'status' => BatchJobConstant::STATUS_COMPLETED,
            'total_count' => FirstMileShipmentFee::where('upload_id', $batchId)->active()->count(),
        ]);
    }
}

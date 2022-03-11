<?php

namespace App\Services\SalesReportImport;

use Carbon\Carbon;
use App\Models\BatchJob;
use App\Services\ImportService;
use App\Models\LongTermStorageFee;
use Illuminate\Support\Facades\DB;
use App\Constants\BatchJobConstant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class LongTermStorageFeeImport
{
    public function import(LazyCollection $collection, int $batchId, Carbon $reportDate, int $userId)
    {
        $batchJob = BatchJob::findOrFail($batchId);

        foreach ($collection->chunk(1000) as $fees) {

            DB::beginTransaction();

            try {
                $data = [];
                foreach ($fees as $fee) {
                    if (isset($fee['account'])) {
                        $data[] = [
                            'account' => $fee['account'],
                            'snapshot_date' => $fee['snapshot_date'],
                            'sku' => $fee['sku'],
                            'fnsku' => $fee['fnsku'],
                            'asin' => $fee['asin'],
                            'product_name' => $fee['product_name'],
                            'supplier_type' => $fee['supplier_type'],
                            'supplier' => $fee['supplier'],
                            'condition' => $fee['condition'],
                            'qty_charged_12_mo_long_term_storage_fee' => $fee['qty_charged_12_mo_long_term_storage_fee'],
                            'per_unit_volume' => $fee['per_unit_volume'],
                            'currency' => $fee['currency'],
                            '12_mo_long_terms_storage_fee' => $fee['12_mo_long_terms_storage_fee'],
                            'hkd' => $fee['hkd'],
                            'hkd_rate' => $fee['hkd_rate'],
                            'qty_charged_6_mo_long_term_storage_fee' => $fee['qty_charged_6_mo_long_term_storage_fee'],
                            '6_mo_long_terms_storage_fee' => $fee['6_mo_long_terms_storage_fee'],
                            'volume_unit' => $fee['volume_unit'],
                            'country' => $fee['country'],
                            'enrolled_in_small_and_light' => $fee['enrolled_in_small_and_light'],
                            'upload_id' => $batchId,
                            'report_date' => $reportDate->toDateString(),
                            'active' => 1,
                            'created_at' => date('Y-m-d h:i:s'),
                            'created_by' => $userId,
                            'updated_at' => date('Y-m-d h:i:s'),
                            'updated_by' => $userId,
                        ];
                    }
                }

                LongTermStorageFee::insert($data);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();

                Log::channel('daily_queue_import')
                    ->error("[A4lutionSalesReport.errors]" . $e);

                $batchJob->longTermStorageFees()->update(['active' => 0]);

                $batchJob->update([
                    'status' => BatchJobConstant::STATUS_FAILED,
                    'total_count' => 0,
                    'exit_message' => $e->getMessage(),
                    'user_error_msg' => (new ImportService)->getUserErrorMsg($e->getMessage())
                ]);

                return;
            }
        }
        

        LongTermStorageFee::where('report_date', $reportDate->toDateString())
            ->where('upload_id', '!=', $batchId)
            ->active()
            ->update(['active' => 0]);

        $batchJob->update([
            'status' => BatchJobConstant::STATUS_COMPLETED,
            'total_count' => LongTermStorageFee::where('upload_id', $batchId)->active()->count(),
        ]);
    }
}

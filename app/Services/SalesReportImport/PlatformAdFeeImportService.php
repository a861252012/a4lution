<?php

namespace App\Services\SalesReportImport;

use Carbon\Carbon;
use App\Models\BatchJob;
use App\Models\PlatformAdFee;
use App\Services\ImportService;
use Illuminate\Support\Facades\DB;
use App\Constants\BatchJobConstant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use App\Services\SalesReportImport\ImportInterface;

class PlatformAdFeeImportService implements ImportInterface
{
    public function import(LazyCollection $collection, int $batchId, Carbon $reportDate, int $userId)
    {
        $batchJob = BatchJob::findOrFail($batchId);

        foreach ($collection->chunk(1000) as $adFees) {
            DB::beginTransaction();

            try {

                $data = [];
                foreach ($adFees as $adFee) {
                    if (isset($adFee['client_code'])) {
                        $data[] = [
                            'client_code' => $adFee['client_code'],
                            'client_type' => $adFee['client_type'],
                            'platform' => $adFee['platform'],
                            'account' => $adFee['account'],
                            'campagin_type' => $adFee['campagin_type'],
                            'campagin' => $adFee['campagin'],
                            'currency' => $adFee['currency'],
                            'Impressions' => $adFee['impressions'],
                            'clicks' => $adFee['clicks'],
                            'ctr' => $adFee['ctr'],
                            'spendings' => $adFee['spendings'],
                            'spendings_hkd' => $adFee['spendings_hkd'],
                            'cpc' => $adFee['cpc'],
                            'sales_qty' => $adFee['sales_qty'],
                            'sales_amount' => $adFee['sales_amount'],
                            'sales_amount_hkd' => $adFee['sales_amount_hkd'],
                            'acos' => $adFee['acos'],
                            'exchange_rate' => $adFee['exchange_rate'],
                            'country' => $adFee['country'],
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

                PlatformAdFee::insert($data);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();

                Log::channel('daily_queue_import')
                    ->error("[A4lutionSalesReport.errors]" . $e);

                $batchJob->platformAdFees()->update(['active' => 0]);

                $batchJob->update([
                    'status' => BatchJobConstant::STATUS_FAILED,
                    'total_count' => 0,
                    'exit_message' => $e->getMessage(),
                    'user_error_msg' => (new ImportService)->getUserErrorMsg($e->getMessage())
                ]);

                return;
            }
        }

        PlatformAdFee::where('report_date', $reportDate->toDateString())
                ->where('upload_id', '!=', $batchId)
                ->active()
                ->update(['active' => 0]);

        $batchJob->update([
            'status' => BatchJobConstant::STATUS_COMPLETED,
            'total_count' => PlatformAdFee::where('upload_id', $batchId)->active()->count(),
        ]);
    }
}

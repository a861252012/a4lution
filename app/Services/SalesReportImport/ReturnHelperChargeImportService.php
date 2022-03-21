<?php

namespace App\Services\SalesReportImport;

use Carbon\Carbon;
use App\Models\BatchJob;
use App\Services\ImportService;
use App\Models\ReturnHelperCharge;
use Illuminate\Support\Facades\DB;
use App\Constants\BatchJobConstant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use App\Services\SalesReportImport\ImportInterface;

class ReturnHelperChargeImportService implements ImportInterface
{
    public function import(LazyCollection $collection, int $batchId, Carbon $reportDate, int $userId)
    {
        $batchJob = BatchJob::findOrFail($batchId);

        foreach ($collection->chunk(1000) as $items) {
            DB::beginTransaction();

            try {

                $data = [];
                foreach ($items as $item) {
                    if (isset($item['returnrequestnumber'])) {
                        $data[] = [
                            'return_request_number' => $item['returnrequestnumber'],
                            'transaction_type_code' => $item['transactiontypecode'],
                            'transaction_name' => $item['transactionname'],
                            'currency_code' => $item['currencycode'],
                            'amount' => $item['amount'],
                            'rate' => $item['rate'],
                            'supplier' => $item['supplier'],
                            'notes' => $item['notes'],
                            'transaction_create_on' => $item['transactioncreateon'],
                            'transaction_number' => $item['transactionnumber'],
                            'rma' => $item['rma'],
                            'shipment_number' => $item['shipmentnumber'],
                            'warehouse_country' => $item['warehousecountry'],
                            'shipment_state' => $item['shipmentstate'],
                            'shipment_postal_code' => $item['shipmentpostalcode'],
                            'shipment_receive_date' => $item['shipmentreceivedate'],
                            'tracking_number' => $item['trackingnumber'],
                            'actual_weight' => $item['actualweight'],
                            'actual_length' => $item['actuallength'],
                            'actual_width' => $item['actualwidth'],
                            'actual_height' => $item['actualheight'],
                            'weight' => $item['weight'],
                            'lenth' => $item['lenth'],
                            'width' => $item['width'],
                            'height' => $item['height'],
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

                ReturnHelperCharge::insert($data);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();

                Log::channel('daily_queue_import')
                    ->error("[A4lutionSalesReport.errors]" . $e);

                $batchJob->returnHelperCharges()->update(['active' => 0]);

                $batchJob->update([
                    'status' => BatchJobConstant::STATUS_FAILED,
                    'total_count' => 0,
                    'exit_message' => $e->getMessage(),
                    'user_error_msg' => (new ImportService)->getUserErrorMsg($e->getMessage())
                ]);

                return;
            }
        }

        ReturnHelperCharge::where('report_date', $reportDate->toDateString())
                ->where('upload_id', '!=', $batchId)
                ->active()
                ->update(['active' => 0]);

        $batchJob->update([
            'status' => BatchJobConstant::STATUS_COMPLETED,
            'total_count' => ReturnHelperCharge::where('upload_id', $batchId)->active()->count(),
        ]);
    }
}

<?php

namespace App\Imports\Fee;

use App\Models\BatchJob;
use App\Models\FirstMileShipmentFee;
use App\Services\ImportService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;

class FirstMileShipmentFeesImport implements
    ToModel,
    WithHeadingRow,
    ShouldQueue,
    WithChunkReading,
    WithBatchInserts,
    WithCalculatedFormulas,
    WithEvents,
    withValidation
{
    use Importable,
        RegistersEventListeners,
        RemembersRowNumber;

    public $rows = 0;
    private $userID;
    private $batchID;
    private $inputReportDate;

    public function __construct(
        $userID,
        $batchID,
        $inputReportDate
    ) {
        $this->userID = $userID;
        $this->batchID = $batchID;
        $this->inputReportDate = $inputReportDate;
    }

    public function model(array $row)
    {
        ++$this->rows;

        return new FirstMileShipmentFee([
            'client_code' => $row['client_code'],
            'ids_sku' => $row['ids_sku'],
            'title' => $row['title'],
            'asin' => $row['asin'],
            'fnsku' => $row['fnsku'],
            'external_id' => $row['external_id'],
            'condition' => $row['condition'],
            'who_will_prep' => $row['who_will_prep'],
            'prep_type' => $row['prep_type'],
            'who_will_label' => $row['who_will_label'],
            'shipped' => $row['shipped'],
            'fba_shipment' => $row['fba_shipment'],
            'shipment_type' => $row['shipment_type'],
            'date' => $row['date'],
            'account' => $row['account'],
            'ship_from' => $row['ship_from'],
            'first_mile' => $row['first_mile'],
            'last_mile_est_orig' => $row['last_mile_apc_original_currency_estimated'],
            'last_mile_act_orig' => $row['last_mile_apc_original_currency_actual'],
            'shipment_remark' => $row['shipment_remark'],
            'currency_last_mile' => $row['currency_last_mile'],
            'exchange_rate' => $row['exchange_rate'],
            'total' => $row['total'],
            'is_payment_settled' => $row['is_payment_settled'],
            'remark' => $row['remark'],
            'report_date' => $this->inputReportDate,
            'active' => 1,
            'upload_id' => $this->batchID,
            'created_at' => date('Y-m-d h:i:s'),
            'created_by' => $this->userID,
            'fulfillment_center' => $row['account'] ? substr($row['account'], -2) : null
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                DB::beginTransaction();
                try {
                    FirstMileShipmentFee::where('report_date', $this->inputReportDate)
                        ->where('upload_id', '<', $this->batchID)
                        ->where('active', 1)
                        ->cursor()
                        ->each(function ($item) {
                            $item->update(['active' => 0]);
                        });

                    BatchJob::where('id', $this->batchID)->update(
                        [
                            'status' => 'completed',
                            'total_count' => $this->getRowCount()
                        ]
                    );

                    DB::commit();
                } catch (Exception $e) {
                    DB::rollback();

                    Log::channel('daily_queue_import')
                        ->info("[QueueFirstMileShipmentFees.errors]" . $e);
                }
            },
            ImportFailed::class => function (ImportFailed $event) {
                DB::beginTransaction();
                try {
                    BatchJob::where('id', $this->batchID)
                        ->update(
                            [
                                'status' => 'failed',
                                'total_count' => $this->getRowCount(),
                                'exit_message' => $event->getException(),
                                'user_error_msg' => (new ImportService)->transformDate($event->getException()),
                            ]
                        );

                    FirstMileShipmentFee::where('report_date', $this->inputReportDate)
                        ->where('upload_id', $this->batchID)
                        ->where('active', 1)
                        ->cursor()
                        ->each(function ($item) {
                            $item->delete();
                        });

                    DB::commit();
                } catch (Exception $e) {
                    DB::rollback();

                    Log::channel('daily_queue_import')
                        ->info("[QueueFirstMileShipmentFees.errors]" . $e);
                }

                foreach ($event->getException() as $failure) {
                    Log::channel('daily_queue_import')
                        ->info("[QueueFirstMileShipmentFees.errors]" . $failure);
                }
            },
        ];
    }

    public function getRowCount(): int
    {
        return FirstMileShipmentFee::where('upload_id', $this->batchID)
            ->where('active', 1)
            ->count();
    }

    public function rules(): array
    {
        return [
//            'client_code' => ['bail', 'nullable', 'string', 'max:50'],
//            'ids_sku' => ['bail', 'nullable', 'string', 'max:255'],
//            'title' => ['bail', 'nullable', 'string', 'max:255'],
//            'asin' => ['bail', 'nullable', 'string', 'max:255'],
//            'fnsku' => ['bail', 'nullable', 'string', 'max:255'],
//            'external_id' => ['bail', 'nullable', 'string', 'max:255'],
//            'condition' => ['bail', 'nullable', 'string', 'max:255'],
//            'who_will_prep' => ['bail', 'nullable', 'string', 'max:255'],
//            'prep_type' => ['bail', 'nullable', 'string', 'max:255'],
//            'who_will_label' => ['bail', 'nullable', 'string', 'max:255'],
//            'shipped' => ['bail', 'nullable', 'numeric', 'max:9999999999'],
//            'fba_shipment' => ['bail', 'nullable', 'string', 'max:255'],
//            'shipment_type' => ['bail', 'nullable', 'string', 'max:255'],
//            'date' => ['bail', 'nullable', 'date'],
//            'account' => ['bail', 'nullable', 'string', 'max:255'],
//            'ship_from' => ['bail', 'nullable', 'string', 'max:255'],
//            'first_mile' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//?????????????????????????????? type?????????//
//            'last_mile_est_orig' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//?????????????????????????????? type?????????
//            'last_mile_act_orig' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//?????????????????????????????? type?????????
//            'shipment_remark' => ['bail', 'nullable', 'string', 'max:255'],
//            'currency_last_mile' => ['bail', 'nullable', 'string', 'max:255'],
//            'exchange_rate' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//?????????????????????????????? type?????????
//            'total' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//?????????????????????????????? type?????????
//            'is_payment_settled' => ['bail', 'nullable', 'string', 'max:255'],
//            'remark' => ['bail', 'nullable', 'string', 'max:255'],
//
//            '*.client_code' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.ids_sku' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.title' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.asin' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.fnsku' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.external_id' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.condition' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.who_will_prep' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.prep_type' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.who_will_label' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.shipped' => ['bail', 'nullable', 'numeric', 'max:9999999999'],
//            '*.fba_shipment' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.shipment_type' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.date' => ['bail', 'nullable', 'date'],
//            '*.account' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.ship_from' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.first_mile' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//?????????????????????????????? type?????????//
//            '*.last_mile_est_orig' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//?????????????????????????????? type?????????
//            '*.last_mile_act_orig' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//?????????????????????????????? type?????????
//            '*.shipment_remark' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.currency_last_mile' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.exchange_rate' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//?????????????????????????????? type?????????
//            '*.total' => ['bail', 'nullable', 'numeric', 'max:9999999999'],//?????????????????????????????? type?????????
//            '*.is_payment_settled' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.remark' => ['bail', 'nullable', 'string', 'max:255'],
        ];
    }

    public function prepareForValidation(array $row): array
    {
        $row['date'] = $row['date'] ? (new ImportService)->transformDate($row['date']) : null;

        return $row;
    }
}

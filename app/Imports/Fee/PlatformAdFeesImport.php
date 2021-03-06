<?php

namespace App\Imports\Fee;

use App\Models\BatchJob;
use App\Models\PlatformAdFee;
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
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;

class PlatformAdFeesImport implements
    ToModel,
    WithChunkReading,
    ShouldQueue,
    WithHeadingRow,
    WithBatchInserts,
    WithEvents,
    WithValidation
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

        return new PlatformAdFee([
            'client_code' => $row['client_code'],
            'client_type' => $row['client_type'],
            'platform' => $row['platform'],
            'account' => $row['account'],
            'campagin_type' => $row['campagin_type'],
            'campagin' => $row['campagin'],
            'currency' => $row['currency'],
            'Impressions' => $row['impressions'],
            'clicks' => $row['clicks'],
            'ctr' => $row['ctr'],
            'spendings' => $row['spendings'],
            'spendings_hkd' => $row['spendings_hkd'],
            'cpc' => $row['cpc'],
            'sales_qty' => $row['sales_qty'],
            'sales_amount' => $row['sales_amount'],
            'sales_amount_hkd' => $row['sales_amount_hkd'],
            'acos' => $row['acos'],
            'exchange_rate' => $row['exchange_rate'],
            'upload_id' => $this->batchID,
            'report_date' => $this->inputReportDate,
            'active' => 1,
            'created_at' => date('Y-m-d h:i:s'),
            'created_by' => $this->userID,
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_by' => $this->userID,
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
                    PlatformAdFee::where('report_date', $this->inputReportDate)
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
                        ->info("[QueuePlatformAdFees.errors]" . $e);
                }
            },
            ImportFailed::class => function (ImportFailed $event) {
                DB::beginTransaction();
                try {
                    BatchJob::where('id', $this->batchID)->update(
                        [
                            'status' => 'failed',
                            'total_count' => $this->getRowCount(),
                            'exit_message' => $event->getException(),
                            'user_error_msg' => (new ImportService)->getUserErrorMsg($event->getException())
                        ]
                    );

                    PlatformAdFee::where('report_date', $this->inputReportDate)
                        ->where('active', 1)
                        ->where('upload_id', $this->batchID)
                        ->cursor()
                        ->each(function ($item) {
                            $item->delete();
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
                        ->info("[QueuePlatformAdFees.errors]" . $e);
                }

                foreach ($event->getException() as $failure) {
                    Log::channel('daily_queue_import')
                        ->info("[QueuePlatformAdFees.errors]" . $failure);
                }
            },
        ];
    }

    public function getRowCount(): int
    {
        return PlatformAdFee::where('upload_id', $this->batchID)
            ->where('active', 1)
            ->count();
    }

    public function rules(): array
    {
        return [];
//        return [
//            'client_code' => ['bail', 'nullable', 'string', 'max:50'],
//            'client_type' => ['bail', 'nullable', 'string', 'max:50'],
//            'platform' => ['bail', 'nullable', 'string', 'max:100'],
//            'account' => ['bail', 'nullable', 'string', 'max:100'],
//            'campagin_type' => ['bail', 'nullable', 'string', 'max:100'],
//            'campagin' => ['bail', 'nullable', 'string', 'max:255'],
//            'currency' => ['bail', 'nullable', 'string', 'max:50'],
//            'Impressions' => ['bail', 'nullable', 'numeric', 'max:9999999999'],
//            'clicks' => ['bail', 'nullable', 'numeric', 'max:9999999999'],
//            'ctr' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'spendings' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'spendings_hkd' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'cpc' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'sales_qty' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'sales_amount' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'sales_amount_hkd' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'acos' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'exchange_rate' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//
//            '*.client_code' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.client_type' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.platform' => ['bail', 'nullable', 'string', 'max:100'],
//            '*.account' => ['bail', 'nullable', 'string', 'max:100'],
//            '*.campagin_type' => ['bail', 'nullable', 'string', 'max:100'],
//            '*.campagin' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.currency' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.Impressions' => ['bail', 'nullable', 'numeric', 'max:9999999999'],
//            '*.clicks' => ['bail', 'nullable', 'numeric', 'max:9999999999'],
//            '*.ctr' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.spendings' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.spendings_hkd' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.cpc' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.sales_qty' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.sales_amount' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.sales_amount_hkd' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.acos' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.exchange_rate' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//        ];
    }
}

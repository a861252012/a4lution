<?php

namespace App\Imports;

use App\Models\AmazonDateRangeReport;
use App\Models\BatchJob;
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

class QueueAmazonDateRangeImport implements
    ToModel,
    WithChunkReading,
    ShouldQueue,
    WithHeadingRow,
    WithCalculatedFormulas,
    WithBatchInserts,
    WithValidation,
    WithEvents
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

        return new AmazonDateRangeReport([
            'account' => $row['account'],
            'country' => $row['country'],
            'paid_date' => $row['paid_date'],
            'shipped_date' => $row['shipped_date'],
            'settlement_id' => $row['settlement_id'],
            'type' => $row['type'],
            'description' => $row['description'],
            'order_id' => $row['order_id'],
            'order_type' => $row['order_type'],
            'msku' => $row['msku'],
            'asin' => $row['asin'],
            'product_name' => $row['product_name'],
            'sku' => $row['sku'],
            'supplier_type' => $row['supplier_type'],
            'supplier' => $row['supplier'],
            'marketplace' => $row['marketplace'],
            'fulfillment' => $row['fulfillment'],
            'quantity' => $row['quantity'],
            'currency' => $row['currency'],
            'product_sales' => $row['product_sales'],
            'shipping_credits' => $row['shipping_credits'],
            'gift_wrap_credits' => $row['gift_wrap_credits'],
            'promotional_rebates' => $row['promotional_rebates'],
            'cost_of_point' => $row['cost_of_point'],
            'tax' => $row['tax'],
            'marketplace_withheld_tax' => $row['marketplace_withheld_tax'],
            'selling_fees' => $row['selling_fees'],
            'fba_fees' => $row['fba_fees'],
            'other_transaction_fees' => $row['other_transaction_fees'],
            'other' => $row['other'],
            'amazon_total' => $row['amazon_total'],
            'hkd_rate' => $row['hkd_rate'],
            'amazon_total_hkd' => $row['amazon_total_hkd'],
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
                    AmazonDateRangeReport::where('report_date', $this->inputReportDate)
                        ->where('upload_id', '<', $this->batchID)
                        ->where('active', '=', 1)
                        ->cursor()
                        ->chunk(1000, function ($item) {
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
                        ->info("[QueueAmazonDateRangeImport.errors]" . $e);
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
                                'user_error_msg' => (new ImportService)->getUserErrorMsg($event->getException())
                            ]
                        );

                    AmazonDateRangeReport::where('report_date', $this->inputReportDate)
                        ->where('upload_id', '=', $this->batchID)
                        ->where('active', '=', 1)
                        ->cursor()
                        ->chunk(1000, function ($item) {
                            $item->delete();
                        });

                    DB::commit();
                } catch (Exception $e) {
                    DB::rollback();

                    Log::channel('daily_queue_import')
                        ->info("[QueueAmazonDateRangeImport.errors]" . $e);
                }

                foreach ($event->getException() as $failure) {
                    Log::channel('daily_queue_import')
                        ->info("[QueueAmazonDateRangeImport.errors]" . $failure);
                }
            },
        ];
    }

    public function getRowCount(): int
    {
        return AmazonDateRangeReport::where('upload_id', $this->batchID)
            ->where('active', 1)
            ->count();
    }

    public function rules(): array
    {
        return [];
//        return [
//            'account' => ['bail', 'nullable', 'string', 'max:50'],
//            'country' => ['bail', 'nullable', 'string', 'max:50'],
//            'paid_date' => ['bail', 'nullable', 'date'],
//            'shipped_date' => ['bail', 'nullable', 'date'],
//            'settlement_id' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'type' => ['bail', 'nullable', 'string', 'max:50'],
//            'description' => ['bail', 'nullable', 'string', 'max:255'],
//            'order_id' => ['bail', 'nullable', 'string', 'max:100'],
//            'order_type' => ['bail', 'nullable', 'string', 'max:50'],
//            'msku' => ['bail', 'nullable', 'string', 'max:50'],
//            'asin' => ['bail', 'nullable', 'string', 'max:50'],
//            'product_name' => ['bail', 'nullable', 'string', 'max:255'],
//            'sku' => ['bail', 'nullable', 'string', 'max:50'],
//            'supplier_type' => ['bail', 'nullable', 'string', 'max:50'],
//            'supplier' => ['bail', 'nullable', 'string', 'max:50'],
//            'marketplace' => ['bail', 'nullable', 'string', 'max:50'],
//            'fulfillment' => ['bail', 'nullable', 'string', 'max:50'],
//            'quantity' => ['bail', 'nullable', 'numeric', 'max:99999'],
//            'currency' => ['bail', 'nullable', 'string', 'max:3'],
//            'product_sales' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'shipping_credits' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'gift_wrap_credits' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'promotional_rebates' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'cost_of_point' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'tax' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'marketplace_withheld_tax' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'selling_fees' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'fba_fees' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'other_transaction_fees' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'other' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'amazon_total' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'hkd_rate' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            'amazon_total_hkd' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//
//            '*.account' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.country' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.paid_date' => ['bail', 'nullable', 'date'],
//            '*.shipped_date' => ['bail', 'nullable', 'date'],
//            '*.settlement_id' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.type' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.description' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.order_id' => ['bail', 'nullable', 'string', 'max:100'],
//            '*.order_type' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.msku' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.asin' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.product_name' => ['bail', 'nullable', 'string', 'max:255'],
//            '*.sku' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.supplier_type' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.supplier' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.marketplace' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.fulfillment' => ['bail', 'nullable', 'string', 'max:50'],
//            '*.quantity' => ['bail', 'nullable', 'numeric', 'max:99999'],
//            '*.currency' => ['bail', 'nullable', 'string', 'max:3'],
//            '*.product_sales' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.shipping_credits' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.gift_wrap_credits' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.promotional_rebates' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.cost_of_point' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.tax' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.marketplace_withheld_tax' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.selling_fees' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.fba_fees' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.other_transaction_fees' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.other' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.amazon_total' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.hkd_rate' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//            '*.amazon_total_hkd' => ['bail', 'nullable', 'numeric', 'max:999999999999'],
//        ];
    }

    public function prepareForValidation(array $row): array
    {
        $row['paid_date'] = $row['paid_date'] ? (new ImportService)->transformDate($row['paid_date']) : null;

        return $row;
    }
}

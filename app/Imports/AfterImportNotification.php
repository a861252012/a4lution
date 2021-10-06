<?php

//namespace App\Imports;
//
//use App\Models\BatchJobs;
//use Illuminate\Bus\Queueable;
//use Illuminate\Contracts\Queue\ShouldQueue;
//use Illuminate\Queue\SerializesModels;
//use Maatwebsite\Excel\Concerns\WithBatchInserts;
//use Maatwebsite\Excel\Concerns\WithChunkReading;
//use Maatwebsite\Excel\Concerns\WithHeadingRow;
//use Maatwebsite\Excel\Concerns\Importable;
//use Maatwebsite\Excel\Concerns\RegistersEventListeners;
//use Maatwebsite\Excel\Events\AfterImport;
//use Maatwebsite\Excel\Events\ImportFailed;
//use Maatwebsite\Excel\Imports\HeadingRowFormatter;
//
//class ImportHasFailedNotification implements WithChunkReading, ShouldQueue, WithHeadingRow, WithBatchInserts
//{
//    use Queueable, SerializesModels, Importable, RegistersEventListeners;
//
//    private $rows = 0;
//    private $batchID;
//
//    public function __construct(
//        $batchID
//    )
//    {
//        $this->batchID = $batchID;
//    }
//
//    public function updateBatchStatus()
//    {
//        \Log::channel('daily_refund_sync')
//            ->info("[test2]" . $this->batchID);
//
//        BatchJobs::where('id', '=', $this->batchID)->update(['status' => 'completed', 'total_count' => '666']);
//    }
//
//    public function batchSize(): int
//    {
//        return 1000;
//    }
//
//    public function getRowCount(): int
//    {
//        return $this->rows;
//    }
//
//    public function chunkSize(): int
//    {
//        return 1000;
//    }
//
//}

namespace App\Imports;

use App\Models\BatchJobs;
use App\Models\FirstMileShipmentFees;
use App\Models\LongTermStorageFees;
use App\Models\MonthlyStorageFees;
use App\Models\PlatformAdFees;
use App\Models\AmazonDateRangeReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Reader;

class AfterImportNotification implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $import;
    private $batchID;

    public function __construct(
        $import,
        $batchID
    )
    {
        $this->import = $import;
        $this->batchID = $batchID;
    }

    public function handle()
    {

        switch ($this->import) {
            case "Platform Advertisement Fee":
                $count = PlatformAdFees::where('upload_id', $this->batchID)->count();

                break;
            case "Amazon Date Range Report":
                $count = AmazonDateRangeReport::where('upload_id', $this->batchID)->count();

                break;
            case "FBA Long Term Storage Fee":
                $count = LongTermStorageFees::where('upload_id', $this->batchID)->count();

                break;
            case "FBA Monthly Storage Fee":
                $count = MonthlyStorageFees::where('upload_id', $this->batchID)->count();

                break;
            case "First Mile Shipment Fee":
                $count = FirstMileShipmentFees::where('upload_id', $this->batchID)->count();

                break;
            default:
                $count = 0;
        }

        BatchJobs::where('id', $this->batchID)->update(
            [
                'status' => 'completed',
                'total_count' => $count
            ]
        );
        
    }
}

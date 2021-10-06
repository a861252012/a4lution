<?php

namespace App\Jobs;

use App\Imports\AmazonDateRangeImport;
use App\Imports\FirstMileShipmentFeesImport;
use App\Imports\LongTermStorageFeesImport;
use App\Imports\MonthlyStorageFeesImport;
use App\Models\BatchJobs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Imports\PlatformAdFeesImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const BATCH_STATUS = 'processing';

    protected $userID;
    protected $feeType;
    protected $fileName;
    protected $reportDate;
    protected $fileData;

    public function __construct(
        object $fileData,
        int $userID,
        string $feeType,
        string $fileName,
        string $reportDate
    )
    {
        $this->fileData = $fileData;
        $this->userID = $userID;
        $this->feeType = $feeType;
        $this->fileName = $fileName;
        $this->reportDate = $reportDate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        dump('Row count: ' . $import->getRowCount());
        //insert batch
        $batchJobsArray = [
            'user_id' => $this->userID,
            'fee_type' => $this->feeType,
            'file_name' => $this->fileName,
            'report_date' => $this->reportDate,
            'total_count' => 1,
            'status' => self::BATCH_STATUS,
            'created_at' => date('Y-m-d h:i:s'),
        ];

        BatchJobs::insert($batchJobsArray);

        switch ($this->feeType) {
            case "Platform Advertisement Fee":
                $import = new PlatformAdFeesImport;

                break;
            case "Amazon Date Range Report":
                $import = new AmazonDateRangeImport;

//                $res = Excel::import(new AmazonDateRangeImport, $this->fileData);
                break;
            case "FBA Long Term Storage Fee":
                $import = new LongTermStorageFeesImport;

//                $res = Excel::import(new LongTermStorageFeesImport, $this->fileData);
                break;
            case "FBA Monthly Storage Fee":
                $import = new MonthlyStorageFeesImport;

//                $res = Excel::import(new MonthlyStorageFeesImport, $this->fileData);
                break;
            case "First Mile Shipment Fee":
                $import = new FirstMileShipmentFeesImport;

//                $res = Excel::import(new FirstMileShipmentFeesImport, $this->fileData);
                break;
            default:
        }
        $res = Excel::import($import, $this->fileData);

        dump('Row count: ' . $import->getRowCount());
        dd($res);


//        $this->batchJobs->create($batchJobsArray);

//        return Users::create([
//            'user_name' => 'ted' . rand(1, 100),
//            'email' => 'ted.lin@contin-global.com',
//            'actor_type' => 3,
//            'full_name' => 'ted',
//            'company_name' => 'a4lution',
//            'phone_number' => '0912345678',
//            'address' => 'address',
//            'active' => 1,
////            'created_at' => date('Y-m-d H:i:s'),
//            'created_by' => 999999999,
////            'updated_at' => date('Y-m-d H:i:s'),
//            'updated_by' => 999999999,
//            'password' => Hash::make('secret'),
//        ]);
    }
}

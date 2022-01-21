<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\BatchJob;
use Illuminate\Support\Str;
use App\Models\BillingStatement;
use App\Support\SimpleExcelReader;
use App\Constants\BatchJobConstant;
use Illuminate\Support\Facades\Auth;
use App\Constants\ImportTitleConstant;
use Illuminate\Support\Facades\Storage;
use App\Jobs\Fee\ImportSalesReport;
use Symfony\Component\HttpFoundation\Response;

class SalesReportImportService
{
    private $sheetsHeader = [
        // 'erp_orders' => ImportTitleConstant::,
        'amz_ads' => ImportTitleConstant::PLATFORM_AD,
        'ebay_ads' => ImportTitleConstant::PLATFORM_AD,
        'walmart_ads' => ImportTitleConstant::PLATFORM_AD,
        'lazada_ads' => ImportTitleConstant::PLATFORM_AD,
        'shopee_ads' => ImportTitleConstant::PLATFORM_AD,
        'date_range' => ImportTitleConstant::AMZ_DATE_RANGE,
        'monthly_storage_fees' => ImportTitleConstant::MONTHLY_STORAGE,
        'long_term_storage_fee_charge' => ImportTitleConstant::LONG_TERM,
    ];

    private $feeTypes = [
        BatchJobConstant::FEE_TYPE_PLATFORM_AD_FEES,
        BatchJobConstant::FEE_TYPE_AMAZON_DATE_RANGE,
        BatchJobConstant::FEE_TYPE_LONG_TERM_STORAGE_FEES,
        BatchJobConstant::FEE_TYPE_MONTHLY_STORAGE_FEES,
    ];

    public $file;
    public $reportDate;
    

    public function __construct($file, $reportDate)
    {
        $this->file = $file;
        $this->reportDate = $reportDate;
    }


    public function import()
    {
        $this->checkSheetTitle();

        $batchIds = $this->createBatchJobsAndGetBatchIds();

        $fileName = $this->uploadFile();

        ImportSalesReport::dispatchSync(Auth::id(), storage_path('sales_report'), $fileName, $this->reportDate, $batchIds);
        // ImportSalesReport::dispatch()->onQueue('queue_excel');
        
    }

    private function checkSheetTitle(){
        $excel = SimpleExcelReader::create($this->file, 'xlsx');
        $reader = $excel->getReader();
        $reader->open($this->file);

        foreach ($reader->getSheetIterator() as $sheet) {
            $sheetName = Str::slug($sheet->getName(), '_');

            if (in_array($sheetName, self::sheets())) {
                $header = $excel->headersToSnakeCase()->getHeadersBySheet($sheet);

                if (!$header) {
                    abort(Response::HTTP_FORBIDDEN, "Sheet: [{$sheetName}] Title unmatched");
                }

                $diff = collect($header)->diff($this->sheetsHeader[$sheetName]);
                if ( $diff->isNotEmpty() ) {
                    abort(Response::HTTP_FORBIDDEN, "Sheet: [{$sheetName}] Title : [{$diff->implode(', ')}] unmatched");
                }
            }
        }
    }

    private function createBatchJobsAndGetBatchIds()
    {
        $batchJobs = [];
        foreach ($this->feeTypes as $feeType) {

            $batchJobs[$feeType] = BatchJob::create([
                'user_id' => Auth::id(),
                'fee_type' => $feeType,
                'file_name' => $this->file->getClientOriginalName(),
                'report_date' => $this->reportDate,
                'total_count' => 0,
                'status' => BatchJobConstant::STATUS_PROCESSING,
                'created_at' => now(),
            ]);
        }

        return collect($batchJobs)->map(fn($batchJob) => $batchJob->id)->toArray();
    }

    private function uploadFile()
    {
        // 上傳至 aws s3
        $fileName = date('Him'). '-' .$this->file->getClientOriginalName();
        $s3Path = sprintf(
            'a4lution-sales-report/%s/%s',
            date('Ymd'),
            $fileName,
        );

        // TODO: 筆記: 如果 $file 沒使用 ->get() 不會轉換成 contents，因此無法自己設定檔名，系統會自己建一個亂碼檔案名
        // Storage::disk('s3-a4lution-import')->put($path, $file); // 系統建一個亂碼檔案名
        Storage::disk('s3-a4lution-import')->put($s3Path, $this->file->get()); // 依據我設定的 path 建立檔案名

        // 存在本地端供 Queue 處理
        $this->file->move(storage_path('sales_report'), $fileName);

        return $fileName;
    }

    public static function sheets()
    {
        return array_keys(self::sheetsWithHeader);
    }
}

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
    // 每個分頁對應的表頭欄位
    private static $sheetsHeader = [
        'erp_orders' => ImportTitleConstant::ERP_ORDERS,
        'amz_ads' => ImportTitleConstant::PLATFORM_AD,
        'ebay_ads' => ImportTitleConstant::PLATFORM_AD,
        'walmart_ads' => ImportTitleConstant::PLATFORM_AD,
        'lazada_ads' => ImportTitleConstant::PLATFORM_AD,
        'shopee_ads' => ImportTitleConstant::PLATFORM_AD,
        'date_range' => ImportTitleConstant::AMZ_DATE_RANGE,
        'monthly_storage_fees' => ImportTitleConstant::MONTHLY_STORAGE,
        'long_term_storage_fee_charge' => ImportTitleConstant::LONG_TERM,
        'contin_storage_fee' => ImportTitleConstant::CONTIN_STORAGE,
        
    ];

    // 每個分頁對應的 Fee Type
    private $sheetsFeeType = [
        'erp_orders' => BatchJobConstant::IMPORT_TYPE_ERP_ORDERS,
        'amz_ads' => BatchJobConstant::FEE_TYPE_PLATFORM_AD_FEES,
        'ebay_ads' => BatchJobConstant::FEE_TYPE_PLATFORM_AD_FEES,
        'walmart_ads' => BatchJobConstant::FEE_TYPE_PLATFORM_AD_FEES,
        'lazada_ads' => BatchJobConstant::FEE_TYPE_PLATFORM_AD_FEES,
        'shopee_ads' => BatchJobConstant::FEE_TYPE_PLATFORM_AD_FEES,
        'date_range' => BatchJobConstant::FEE_TYPE_AMAZON_DATE_RANGE,
        'monthly_storage_fees' => BatchJobConstant::FEE_TYPE_MONTHLY_STORAGE_FEES,
        'long_term_storage_fee_charge' => BatchJobConstant::FEE_TYPE_LONG_TERM_STORAGE_FEES,
        'contin_storage_fee' => BatchJobConstant::IMPORT_TYPE_CONTIN_STORAGE_FEE,
    ];

    public $file;
    public $reportDate;

    public function __construct($file, Carbon $reportDate)
    {
        $this->file = $file;
        $this->reportDate = $reportDate;
    }


    public function import()
    {
        $this->checkSheetsTitle();

        $batchIds = $this->createBatchJobsAndGetBatchIds();

        $fileName = $this->uploadFile();

        // ImportSalesReport::dispatchSync(auth()->id(), storage_path('sales_report'), $fileName, $this->reportDate, $batchIds);
        ImportSalesReport::dispatch(
            auth()->id(), 
            storage_path('sales_report'), 
            $fileName, 
            $this->reportDate, 
            $batchIds
        )->onQueue('queue_excel');
        
    }

    private function checkSheetsTitle(){
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

                $diff = collect($header)->filter()->diff(self::$sheetsHeader[$sheetName]);
                if ( $diff->isNotEmpty() ) {
                    abort(Response::HTTP_FORBIDDEN, "Sheet: [{$sheetName}] Title : [{$diff->implode(', ')}] unmatched");
                }
            }
        }
    }

    private function createBatchJobsAndGetBatchIds()
    {
        $batchJobs = [];

        $excel = SimpleExcelReader::create($this->file, 'xlsx');
        $reader = $excel->getReader();
        $reader->open($this->file);

        foreach ($reader->getSheetIterator() as $sheet) {
            $sheetName = Str::slug($sheet->getName(), '_');
            

            if ( ! in_array($sheetName, self::sheets()) ) {
                continue;
            }

            $feeType = $this->sheetsFeeType[$sheetName];
            if ( isset($batchJobs[$feeType]) ) {
                continue;
            }

            $batchJobs[$feeType] = BatchJob::create([
                'user_id' => Auth::id(),
                'fee_type' => $feeType,
                'file_name' => $this->file->getClientOriginalName(),
                'report_date' => $this->reportDate->toDateString(),
                'total_count' => 0,
                'status' => BatchJobConstant::STATUS_PROCESSING,
                'created_at' => now(),
            ])->id;
        }

        return $batchJobs;
    }

    private function uploadFile()
    {
        // 上傳至 aws s3
        $fileName = date('YmdHim'). '-' .$this->file->getClientOriginalName();
        $s3Path = sprintf(
            'a4lution-sales-report/%s/%s/%s',
            app()->environment('production') ? 'prod' : 'dev',
            date('Ymd'),
            $fileName,
        );

        Storage::disk('s3-a4lution-import')->put($s3Path, $this->file->get());

        // 存在本地端供 Queue 處理
        $this->file->move(storage_path('sales_report'), $fileName);

        return $fileName;
    }

    public static function sheets()
    {
        return array_keys(self::$sheetsHeader);
    }
}

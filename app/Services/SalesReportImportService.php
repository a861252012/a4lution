<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\BatchJob;
use Illuminate\Support\Str;
use App\Support\SimpleExcelReader;
use App\Constants\BatchJobConstant;
use Illuminate\Support\Facades\Auth;
use App\Constants\ImportTitleConstant;
use Illuminate\Support\Facades\Storage;
use App\Jobs\Fee\ImportSalesReport;
use Symfony\Component\HttpFoundation\Response;

class SalesReportImportService
{
    const SHEET_ERP_ORDERS = 'erp_orders';
    const SHEET_AMZ_ADS = 'amz_ads';
    const SHEET_EBAY_ADS = 'ebay_ads';
    const SHEET_WALMART_ADS = 'walmart_ads';
    const SHEET_LAZADA_ADS = 'lazada_ads';
    const SHEET_SHOPEE_ADS = 'shopee_ads';
    const SHEET_DATE_RANGE = 'date_range';
    const SHEET_MONTHLY_STORAGE_FEES = 'monthly_storage_fees';
    const SHEET_LONG_TERM_STORAGE_FEE_CHARGE = 'long_term_storage_fee_charge';
    const SHEET_FIRST_MILE_SHIPMENT_FEE = 'fba';
    const SHEET_CONTIN_STORAGE_FEE = 'contin_storage_fee';

    // 每個分頁對應的表頭欄位
    private static $sheetsHeader = [
        self::SHEET_ERP_ORDERS => ImportTitleConstant::ERP_ORDERS,
        self::SHEET_AMZ_ADS => ImportTitleConstant::PLATFORM_AD,
        self::SHEET_EBAY_ADS => ImportTitleConstant::PLATFORM_AD,
        self::SHEET_WALMART_ADS => ImportTitleConstant::PLATFORM_AD,
        self::SHEET_LAZADA_ADS => ImportTitleConstant::PLATFORM_AD,
        self::SHEET_SHOPEE_ADS => ImportTitleConstant::PLATFORM_AD,
        self::SHEET_DATE_RANGE => ImportTitleConstant::AMZ_DATE_RANGE,
        self::SHEET_MONTHLY_STORAGE_FEES => ImportTitleConstant::MONTHLY_STORAGE,
        self::SHEET_LONG_TERM_STORAGE_FEE_CHARGE => ImportTitleConstant::LONG_TERM,
        self::SHEET_FIRST_MILE_SHIPMENT_FEE => ImportTitleConstant::FIRST_MILE_SHIPMENT,
        self::SHEET_CONTIN_STORAGE_FEE => 0,
        
    ];

    // 每個分頁對應的 Fee Type
    private $sheetsFeeType = [
        self::SHEET_ERP_ORDERS => BatchJobConstant::IMPORT_TYPE_ERP_ORDERS,
        self::SHEET_AMZ_ADS => BatchJobConstant::FEE_TYPE_PLATFORM_AD_FEES,
        self::SHEET_EBAY_ADS => BatchJobConstant::FEE_TYPE_PLATFORM_AD_FEES,
        self::SHEET_WALMART_ADS => BatchJobConstant::FEE_TYPE_PLATFORM_AD_FEES,
        self::SHEET_LAZADA_ADS => BatchJobConstant::FEE_TYPE_PLATFORM_AD_FEES,
        self::SHEET_SHOPEE_ADS => BatchJobConstant::FEE_TYPE_PLATFORM_AD_FEES,
        self::SHEET_DATE_RANGE => BatchJobConstant::FEE_TYPE_AMAZON_DATE_RANGE,
        self::SHEET_MONTHLY_STORAGE_FEES => BatchJobConstant::FEE_TYPE_MONTHLY_STORAGE_FEES,
        self::SHEET_LONG_TERM_STORAGE_FEE_CHARGE => BatchJobConstant::FEE_TYPE_LONG_TERM_STORAGE_FEES,
        self::SHEET_FIRST_MILE_SHIPMENT_FEE => BatchJobConstant::FEE_TYPE_FIRST_MILE_SHIPMENT_FEES,
        self::SHEET_CONTIN_STORAGE_FEE => BatchJobConstant::IMPORT_TYPE_CONTIN_STORAGE_FEE,
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
        $this->checkSheetsContent();

        $batchIds = $this->createBatchJobsAndGetBatchIds();

        $fileName = $this->uploadFile();

        ImportSalesReport::dispatch(
            auth()->id(), 
            storage_path('sales_report'), 
            $fileName, 
            $this->reportDate, 
            $batchIds
        )->onQueue('queue_excel');
        
    }

    private function checkSheetsContent(){
        $excel = SimpleExcelReader::create($this->file, 'xlsx');
        $reader = $excel->getReader();
        $reader->open($this->file);

        foreach ($reader->getSheetIterator() as $sheet) {
            $sheetName = Str::slug($sheet->getName(), '_');

            if (in_array($sheetName, self::sheets())) {

                // 檢查表頭
                $header = $excel->headersToSnakeCase()->getHeadersBySheet($sheet);

                if (!$header) {
                    abort(Response::HTTP_FORBIDDEN, "Sheet [{$sheetName}]: no Title!");
                }

                // 不比對，用index行列獲取內容
                if (!self::$sheetsHeader[$sheetName]) {
                    continue;
                }

                // 如果 header 是中文，會產生全部空字串內容
                if (collect($header)->filter()->isEmpty()) {
                    abort(Response::HTTP_FORBIDDEN, "Sheet [{$sheetName}]: no Title!");
                }

                $diff = collect(self::$sheetsHeader[$sheetName])->diff($header);
                if ( $diff->isNotEmpty() ) {
                    abort(Response::HTTP_FORBIDDEN, "Sheet [{$sheetName}]: Title [{$diff->implode(', ')}] unmatched");
                }


                // 檢查分頁 ERP Orders 的 Shipped Date 欄位是否等於 Report Date
                if ($sheetName == self::SHEET_ERP_ORDERS) {
                    
                    // 比對 Shipped Date
                    $sheet = $excel->headersToSnakeCase()->getRowsBySheet($sheet);
                    $firstRow = $sheet->first();
                    $lastRow = $sheet->last();

                    if (Carbon::parse($firstRow['shipped_date'])->format('Ym') <> $this->reportDate->format('Ym')
                        || Carbon::parse($lastRow['shipped_date'])->format('Ym') <> $this->reportDate->format('Ym')) {
                        abort(
                            Response::HTTP_FORBIDDEN, 
                            "Partial orders were not shipped in {$this->reportDate->format('M Y')}.
                            Review the data set details for the \"Shipped Date\" of [ERP orders] you are importing and make sure your upload file matches the {$this->reportDate->format('M Y')}.
                            Please revise the file and upload again."
                        );
                    }

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

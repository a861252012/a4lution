<?php

namespace App\Jobs\Fee;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\BatchJob;
use Illuminate\Support\Str;
use App\Models\OrderProduct;
use App\Models\PlatformAdFee;
use Illuminate\Bus\Queueable;
use InvalidArgumentException;
use App\Services\ImportService;
use App\Models\ContinStorageFee;
use App\Models\MonthlyStorageFee;
use App\Models\LongTermStorageFee;
use App\Models\OrderSkuCostDetail;
use App\Support\SimpleExcelReader;
use Illuminate\Support\Facades\DB;
use App\Constants\BatchJobConstant;
use Illuminate\Support\Facades\Log;
use App\Models\AmazonDateRangeReport;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\LazyCollection;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\SalesReportImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Services\SalesReportImport\AdFeeImportService;
use App\Services\SalesReportImport\OrderImportService;
use App\Services\SalesReportImport\MonthlyStorageFeeImport;
use App\Services\SalesReportImport\LongTermStorageFeeImport;
use App\Services\SalesReportImport\PlatformAdFeeImportService;
use App\Services\SalesReportImport\AmazonDateRangeImportService;
use App\Services\SalesReportImport\ContinStorageFeeImportService;

class ImportSalesReport implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    private $userId;
    private $path;
    private $fileName;
    private $reportDate;
    private $batchIds;
    private $adFeeCollection;

    private $sheetsByIndexRow = [
        SalesReportImportService::SHEET_CONTIN_STORAGE_FEE,
    ];

    public function __construct($userId, $path, $fileName, Carbon $reportDate, $batchIds)
    {
        $this->userId = $userId;
        $this->path = $path;
        $this->fileName = $fileName;
        $this->reportDate = $reportDate;
        $this->batchIds = $batchIds;
        $this->adFeeCollection = LazyCollection::make();
    }

    public function handle()
    {
        $file = $this->path . '/' . $this->fileName;

        $sheetNames = SalesReportImportService::sheets();

        $excel = SimpleExcelReader::create($file, 'xlsx');
        $reader = $excel->getReader();
        $reader->open($file);

        foreach ($reader->getSheetIterator() as $sheet) {
            $sheetName = Str::slug($sheet->getName(), '_');
            
            if (in_array($sheetName, $sheetNames)) {

                if (! method_exists($this, $method = 'import' . Str::studly($sheetName))) {
                    throw new InvalidArgumentException("Unsupported import [{$sheetName}] sheet.");
                }

                // 資料匯入
                $this->{$method}(
                    in_array($sheetName, $this->sheetsByIndexRow)
                        ? $excel->noHeaderRow()->getRowsBySheet($sheet)
                        : $excel->withHeaderRow()->headersToSnakeCase()->getRowsBySheet($sheet)
                );
                
            }
        }

        // Ad Fee 匯入
        if ($this->adFeeCollection->isNotEmpty()) {
            $this->importAdFee();
        }

        unlink($file);
    }
    
    private function importAmzAds(LazyCollection $collection)
    {
        $this->mergeAdFee($collection);
    }

    private function importEbayAds(LazyCollection $collection)
    {
        $this->mergeAdFee($collection);
    }

    private function importWalmartAds(LazyCollection $collection)
    {
        $this->mergeAdFee($collection);
    }

    private function importLazadaAds(LazyCollection $collection)
    {
        $this->mergeAdFee($collection);
    }

    private function importShopeeAds(LazyCollection $collection)
    {
        $this->mergeAdFee($collection);
    }

    private function mergeAdFee(LazyCollection $collection)
    {
        $this->adFeeCollection = $this->adFeeCollection->merge($collection->all());
    }

    private function importAdFee()
    {
        (new PlatformAdFeeImportService)->import(
            $this->adFeeCollection,
            $this->batchIds[BatchJobConstant::FEE_TYPE_PLATFORM_AD_FEES],
            $this->reportDate,
            $this->userId
        );
        
    }

    private function importDateRange(LazyCollection $collection)
    {
        (new AmazonDateRangeImportService)->import(
            $collection,
            $this->batchIds[BatchJobConstant::FEE_TYPE_AMAZON_DATE_RANGE],
            $this->reportDate,
            $this->userId
        );
        
    }

    private function importMonthlyStorageFees(LazyCollection $collection)
    {
        (new MonthlyStorageFeeImport)->import(
            $collection,
            $this->batchIds[BatchJobConstant::FEE_TYPE_MONTHLY_STORAGE_FEES],
            $this->reportDate,
            $this->userId
        );
        
    }

    private function importLongTermStorageFeeCharge(LazyCollection $collection)
    {
        (new LongTermStorageFeeImport)->import(
            $collection,
            $this->batchIds[BatchJobConstant::FEE_TYPE_LONG_TERM_STORAGE_FEES],
            $this->reportDate,
            $this->userId
        );
    }

    private function importErpOrders(LazyCollection $collection)
    {
        (new OrderImportService)->import(
            $collection,
            $this->batchIds[BatchJobConstant::IMPORT_TYPE_ERP_ORDERS],
            $this->reportDate,
            $this->userId
        );
    }

    private function importContinStorageFee(LazyCollection $collection)
    {
        (new ContinStorageFeeImportService)->import(
            $collection,
            $this->batchIds[BatchJobConstant::IMPORT_TYPE_CONTIN_STORAGE_FEE],
            $this->reportDate,
            $this->userId
        );
    }
}
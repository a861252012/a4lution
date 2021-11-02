<?php

namespace App\Http\Controllers;

use App\Imports\QueueAmazonDateRangeImport;
use App\Imports\QueueFirstMileShipmentFees;
use App\Imports\QueueLongTermStorageFees;
use App\Imports\QueueMonthlyStorageFees;
use App\Imports\QueuePlatformAdFees;
use App\Exports\PlatformAdFeesExport;
use App\Exports\AmazonDateRangeExport;
use App\Exports\LongTermStorageFeesExport;
use App\Exports\MonthlyStorageFeesExport;
use App\Exports\FirstMileShipmentFeesExport;
use App\Models\AmazonDateRangeReport;
use App\Models\FirstMileShipmentFees;
use App\Models\LongTermStorageFees;
use App\Models\PlatformAdFees;
use App\Models\BatchJobs;
use App\Models\MonthlyStorageFees;
use App\Models\BillingStatements;
use App\Models\ExtraordinaryItems;
use App\Repositories\CustomersRepository;
use App\Repositories\ExchangeRatesRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Maatwebsite\Excel\HeadingRowImport;
use App\Constants\ImportTitle;

class FeeController extends Controller
{
    const BATCH_STATUS = 'processing';

    private $batchJobs;
    private $amazonDateRangeReport;
    private $platformAdFees;
    private $monthlyStorageFees;
    private $longTermStorageFees;
    private $firstMileShipmentFees;
    private $billingStatements;
    private $customersRepository;
    private $exchangeRatesRepository;

    public function __construct(
        BatchJobs               $batchJobs,
        AmazonDateRangeReport   $amazonDateRangeReport,
        PlatformAdFees          $platformAdFees,
        MonthlyStorageFees      $monthlyStorageFees,
        LongTermStorageFees     $longTermStorageFees,
        FirstMileShipmentFees   $firstMileShipmentFees,
        BillingStatements       $billingStatements,
        CustomersRepository     $customersRepository,
        ExchangeRatesRepository $exchangeRatesRepository
    ) {
        $this->batchJobs = $batchJobs;
        $this->amazonDateRangeReport = $amazonDateRangeReport;
        $this->platformAdFees = $platformAdFees;
        $this->monthlyStorageFees = $monthlyStorageFees;
        $this->longTermStorageFees = $longTermStorageFees;
        $this->firstMileShipmentFees = $firstMileShipmentFees;
        $this->billingStatements = $billingStatements;
        $this->customersRepository = $customersRepository;
        $this->exchangeRatesRepository = $exchangeRatesRepository;
    }

    public function uploadView(Request $request)
    {
        $createdAt = $request->input('search_date') ?? date('Y-m-d h:i:s');
        $createdFrom = date('Y-m-d 00:00:00', strtotime($createdAt));
        $createdTo = date('Y-m-d 23:59:59', strtotime($createdAt));
        $feeType = $request->input('fee_type') ?? null;
        $reportDate = $request->input('report_date') ?? date('Y-m');
        $reportDateFrom = $reportDate ? date('Y-m-01', strtotime($reportDate)) : date('Y-m-01');
        $reportDateTo = $reportDate ? date('Y-m-31', strtotime($reportDate)) : date('Y-m-31');
        $status = $request->input('status_type') ?? null;

        $query = $this->batchJobs->select(
            'batch_jobs.user_id',
            'batch_jobs.report_date',
            'batch_jobs.fee_type',
            'batch_jobs.file_name',
            'batch_jobs.status',
            'batch_jobs.created_at',
            'batch_jobs.user_error_msg',
            'users.user_name'
        )
            ->join('users', 'users.id', '=', 'batch_jobs.user_id')
            ->whereBetween('batch_jobs.created_at', [$createdFrom, $createdTo])
            ->whereBetween('batch_jobs.report_date', [$reportDateFrom, $reportDateTo]);

        if ($request->input('status')) {
            $query->where('batch_jobs.status', '=', $status);
        }

        if ($request->input('fee_type')) {
            $query->where('batch_jobs.fee_type', '=', $feeType);
        }

        $lists = $query->orderby('batch_jobs.id', 'desc')->paginate(50)->appends(request()->query());

        return view('fee.upload', compact('lists', 'reportDate', 'feeType', 'status', 'createdAt'));
    }

    public function uploadFile(Request $request)
    {
        $fileData = $request->file('file');

        $feeType = $request->input('inline_fee_type');

        $fileName = $fileData->getClientOriginalName();

        $inputReportDate = date('Y-m-d', strtotime($request->input('inline_report_date')));

        $reportDate = $inputReportDate ? date('Y-m-d', strtotime($inputReportDate)) : date('Y-m-d');

        $currentDateTime = Carbon::now()->timezone(env('TIME_ZONE_A'))->toDateTimeString();

        $batchJobsArray = [
            'user_id' => Auth::id(),
            'fee_type' => $feeType,
            'file_name' => $fileName,
            'report_date' => $reportDate,
            'total_count' => 0,
            'status' => self::BATCH_STATUS,
            'created_at' => $currentDateTime,
        ];

        $insertBatchID = BatchJobs::insertGetId($batchJobsArray);

        //查詢該月是否已結算,如已結算則不得再更改
        $haveMonthlyReport = $this->billingStatements->where('active', 1)
            ->where('report_date', $inputReportDate)
            ->count();

        if ($haveMonthlyReport) {
            BatchJobs::where('id', $insertBatchID)->update(
                [
                    'status' => 'failed',
                    'exit_message' => 'The selected report date (year-month) was closed',
                ]
            );

            return false;
        }

        switch ($feeType) {
            case "platform_ad_fees":
                $import = new QueuePlatformAdFees(Auth::id(), $insertBatchID, $inputReportDate);

                break;
            case "amazon_date_range":
                $import = new QueueAmazonDateRangeImport(Auth::id(), $insertBatchID, $inputReportDate);

                break;
            case "long_term_storage_fees":
                $import = new QueueLongTermStorageFees(Auth::id(), $insertBatchID, $inputReportDate);

                break;
            case "monthly_storage_fees":
                $import = new QueueMonthlyStorageFees(Auth::id(), $insertBatchID, $inputReportDate);

                break;
            case "first_mile_shipment_fees":
                $import = new QueueFirstMileShipmentFees(Auth::id(), $insertBatchID, $inputReportDate);
                break;
            default:
                $import = null;
        }

        if ($import) {
            Excel::queueImport($import, $fileData)->allOnQueue('queue_excel');
        }
    }

    public function platformAdsView(Request $request)
    {
        $data['reportDate'] = $request->input('report_date') ?? date('Y-m');
        $reportDateFrom = $data['reportDate'] ? date('Y-m-01', strtotime($data['reportDate'])) : date('Y-m-01');
        $reportDateTo = $data['reportDate'] ? date('Y-m-31', strtotime($data['reportDate'])) : date('Y-m-31');
        $data['supplier'] = $request->input('supplier') ?? null;
        $data['platform'] = $request->input('platform') ?? null;

        //調整report_date格式
        $formattedReportDate = DB::raw("date_format(report_date,'%M-%Y') as report_date");

        $query = $this->platformAdFees->select(
            $formattedReportDate,
            'client_code as supplier',
            'platform',
            'account',
            'campagin_type',
            'campagin',
            'Impressions',
            'currency',
            'clicks',
            'ctr',
            'spendings',
            'spendings_hkd',
            'sales_qty',
            'sales_amount',
            'sales_amount_hkd'
        )
            ->where('active', 1)
            ->whereBetween('report_date', [$reportDateFrom, $reportDateTo]);

        if ($data['supplier']) {
            $query->where('client_code', '=', $data['supplier']);
        }

        if ($data['platform']) {
            $query->where('platform', '=', $data['platform']);
        }

        $data['lists'] = $query->orderby('report_date', 'desc')
            ->paginate(100)
            ->appends($request->query());

        return view('fee.platformAdsView', ['data' => $data]);
    }

    public function amzDaterangeView(Request $request)
    {
        $data['reportDate'] = $request->input('report_date') ?? date('Y-m');
        $reportDateFrom = $data['reportDate'] ? date('Y-m-01', strtotime($data['reportDate'])) : date('Y-m-01');
        $reportDateTo = $data['reportDate'] ? date('Y-m-31', strtotime($data['reportDate'])) : date('Y-m-31');
        $data['orderID'] = $request->input('order_id') ?? null;
        $data['sku'] = $request->input('sku') ?? null;
        $data['supplier'] = $request->input('supplier') ?? null;

        //調整report_date格式
        $formattedReportDate = DB::raw("date_format(report_date,'%M-%Y') as report_date");

        $query = $this->amazonDateRangeReport->select(
            $formattedReportDate,
            'account',
            'order_id',
            'sku',
            'supplier',
            'quantity',
            'currency',
            'product_sales',
            'shipping_credits',
            'gift_wrap_credits',
            'promotional_rebates',
            'cost_of_point',
            'tax',
            'marketplace_withheld_tax',
            'selling_fees',
            'fba_fees',
            'other_transaction_fees',
            'other',
            'amazon_total'
        )
            ->where('active', 1)
            ->whereBetween('report_date', [$reportDateFrom, $reportDateTo]);

        if ($data['orderID']) {
            $query->where('order_id', '=', $data['orderID']);
        }

        if ($data['sku']) {
            $query->where('sku', '=', $data['sku']);
        }

        if ($data['supplier']) {
            $query->where('supplier', '=', $data['supplier']);
        }

        $data['lists'] = $query->orderby('report_date', 'desc')
            ->paginate(100)
            ->appends($request->query());

        return view('fee.amzDaterangeView', ['data' => $data]);
    }

    public function monthlyStorageView(Request $request)
    {
        $data['reportDate'] = $request->input('report_date') ?? date('Y-m');
        $reportDateFrom = $data['reportDate'] ? date('Y-m-01', strtotime($data['reportDate'])) : date('Y-m-01');
        $reportDateTo = $data['reportDate'] ? date('Y-m-31', strtotime($data['reportDate'])) : date('Y-m-31');
        $data['supplier'] = $request->input('supplier') ?? null;

        //調整report_date格式
        $formattedReportDate = DB::raw("date_format(report_date,'%M-%Y') as report_date");

        $query = $this->monthlyStorageFees->select(
            $formattedReportDate,
            'asin',
            'fnsku',
            'fulfilment_center',
            'HKD',
            'country_code',
            'supplier',
            'weight',
            'month_of_charge',
            'storage_rate',
            'currency',
            'monthly_storage_fee_est',
            'hkd_rate'
        )
            ->where('active', 1)
            ->whereBetween('report_date', [$reportDateFrom, $reportDateTo]);

        if ($data['supplier']) {
            $query->where('supplier', '=', $data['supplier']);
        }

        $data['lists'] = $query->orderby('report_date', 'desc')
            ->paginate(100)
            ->appends($request->query());

        return view('fee.monthlyStorageView', ['data' => $data]);
    }

    public function longTermStorageView(Request $request)
    {
        $data['reportDate'] = $request->input('report_date') ?? date('Y-m');
        $reportDateFrom = $data['reportDate'] ? date('Y-m-01', strtotime($data['reportDate'])) : date('Y-m-01');
        $reportDateTo = $data['reportDate'] ? date('Y-m-31', strtotime($data['reportDate'])) : date('Y-m-31');
        $data['supplier'] = $request->input('supplier') ?? null;
        $data['sku'] = $request->input('sku') ?? null;

        //調整report_date格式
        $formattedReportDate = DB::raw("date_format(report_date,'%M-%Y') as report_date");

        $query = $this->longTermStorageFees->select(
            $formattedReportDate,
            'snapshot_date',
            'sku',
            'fnsku',
            'asin',
            'supplier',
            'condition',
            'currency',
            '12_mo_long_terms_storage_fee as fee',
            'hkd',
            'hkd_rate',
            'country'
        )
            ->where('active', 1)
            ->whereBetween('report_date', [$reportDateFrom, $reportDateTo]);

        if ($data['supplier']) {
            $query->where('supplier', '=', $data['supplier']);
        }

        if ($data['sku']) {
            $query->where('sku', '=', $data['sku']);
        }

        $data['lists'] = $query->orderby('report_date', 'desc')
            ->paginate(100)
            ->appends($request->query());

        return view('fee.longTermStorageFees', ['data' => $data]);
    }

    public function firstMileShipmentView(Request $request)
    {
        $data['reportDate'] = $request->input('report_date') ?? date('Y-m');
        $reportDateFrom = $data['reportDate'] ? date('Y-m-01', strtotime($data['reportDate'])) : date('Y-m-01');
        $reportDateTo = $data['reportDate'] ? date('Y-m-31', strtotime($data['reportDate'])) : date('Y-m-31');
        $data['clientCode'] = $request->input('client_code') ?? null;
        $data['fbaShipment'] = $request->input('fba_shipment') ?? null;
        $data['idsSku'] = $request->input('ids_sku') ?? null;
        $data['account'] = $request->input('account') ?? null;

        //調整report_date格式
        $formattedReportDate = DB::raw("date_format(report_date,'%M-%Y') as report_date");

        $query = $this->firstMileShipmentFees->select(
            $formattedReportDate,
            'client_code',
            'ids_sku',
            'asin',
            'shipped',
            'fba_shipment',
            'shipment_type',
            'date',
            'account',
            'ship_from',
            'first_mile',
            'last_mile_est_orig',
            'last_mile_act_orig',
            'shipment_remark',
            'currency_last_mile',
            'total'
        )
            ->where('active', 1)
            ->whereBetween('report_date', [$reportDateFrom, $reportDateTo]);

        if ($data['clientCode']) {
            $query->where('client_code', '=', $data['clientCode']);
        }

        if ($data['fbaShipment']) {
            $query->where('fba_shipment', '=', $data['fbaShipment']);
        }

        if ($data['idsSku']) {
            $query->where('ids_sku', '=', $data['idsSku']);
        }

        if ($data['account']) {
            $query->where('account', '=', $data['account']);
        }

        $data['lists'] = $query->orderby('ids_sku')
            ->paginate(100)
            ->appends($request->query());

        return view('fee.firstMileShipmentView', ['data' => $data]);
    }

    public function exportSampleFile()
    {
        $type = request()->route('export_type');

        switch ($type) {
            case "platform_ad_fees":
                $fileName = 'SampleFile_AdFee.xlsx';

                return (new PlatformAdFeesExport)->download($fileName, \Maatwebsite\Excel\Excel::XLSX);
            case "amazon_date_range":
                $fileName = 'SampleFile_DateRange.xlsx';

                return (new AmazonDateRangeExport)->download($fileName, \Maatwebsite\Excel\Excel::XLSX);
            case "long_term_storage_fees":
                $fileName = 'SampleFile_LongTermStorageFee.xlsx';

                return (new LongTermStorageFeesExport)->download($fileName, \Maatwebsite\Excel\Excel::XLSX);
            case "monthly_storage_fees":
                $fileName = 'SampleFile_MonthlyStorageFee.xlsx';

                return (new MonthlyStorageFeesExport)->download($fileName, \Maatwebsite\Excel\Excel::XLSX);
            case "first_mile_shipment_fees":
                $fileName = 'SampleFile_FirstMileShipmentFee.xlsx';

                return (new FirstMileShipmentFeesExport)->download($fileName, \Maatwebsite\Excel\Excel::XLSX);
        }
    }

    public function extraordinaryItem(Request $request)
    {
        $data['clientCode'] = $request->input('client_code') ?? null;
        $data['reportDate'] = $request->input('report_date') ?? null;

        $query = ExtraordinaryItems::query()
            ->select(
                'id',
                'client_code',
                'report_date',
                'item_name',
                'description',
                'currency_code',
                'receivable_amount',
                'payable_amount',
                'item_amount',
                'created_at',
                'created_by',
                'updated_at',
                'updated_by'
            )
            ->where('active', 1);

        if ($data['clientCode']) {
            $query->where('client_code', $data['clientCode']);
        }

        if ($data['reportDate']) {
            $dateFrom = date('Y-m-d 00:00:00', strtotime($data['reportDate']));
            $dateTo = date('Y-m-d 23:59:59', strtotime($data['reportDate']));

            $query->whereBetween('report_date', [$dateFrom, $dateTo]);
        }

        $data['lists'] = $query->orderBy('id', 'desc')
            ->paginate(50);

        return view('fee.extraordinaryItem', $data);
    }

    public function deleteExtraordinaryItem(Request $request): JsonResponse
    {
        $id = $request->route('id');

        try {
            $item = ExtraordinaryItems::findOrFail($id);

            $item->active = 0;
            $item->updated_at = date('Y-m-d h:i:s');
            $item->updated_by = Auth::id();

            $item->save();

            return response()->json(['status' => 200, 'msg' => "DELETED!"]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 404, 'msg' => "WRONG ID!"]);
        }
    }

    public function getClientCodeList(): JsonResponse
    {
        try {
            $data = $this->customersRepository->getAllClientCode();

            return response()->json(
                [
                    'status' => 200,
                    'msg' => 'success',
                    'data' => $data
                ]
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(
                [
                    'status' => 999,
                    'msg' => 'error',
                    'data' => []
                ]
            );
        }
    }

    public function getAllCurrency(): JsonResponse
    {
        try {
            $data = $this->exchangeRatesRepository->getAllCurrency();

            return response()->json(
                [
                    'status' => 200,
                    'msg' => 'success',
                    'data' => $data
                ]
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(
                [
                    'status' => 999,
                    'msg' => 'error',
                    'data' => []
                ]
            );
        }
    }

    public function createExtraordinaryItem(Request $request): JsonResponse
    {
        $datetime = date('Y-m-d h:i:s');
        $userID = Auth::id();
        $data = $request->only(
            'report_date',
            'client_code',
            'currency_code',
            'item_name',
            'description',
            'receivable_amount',
            'payable_amount',
            'item_amount'
        );
        $data['report_date'] = Carbon::parse($data['report_date'])->format('Y-m-d');
        $data['created_at'] = $datetime;
        $data['created_by'] = $userID;
        $data['updated_at'] = $datetime;
        $data['updated_by'] = $userID;
        $data['active'] = 1;

        try {
            ExtraordinaryItems::insert($data);

            return response()->json(
                [
                    'status' => 200,
                    'msg' => 'success'
                ]
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(
                [
                    'status' => 999,
                    'msg' => 'error',
                ]
            );
        }
    }

    public function updateExtraordinaryDetail(): JsonResponse
    {
        $id = request()->route('id');
        $data = request()->except(['_token', '_method']);
        $data['updated_by'] = Auth::id();
        $data['report_date'] = Carbon::parse($data['report_date'])->format('Y-m-d');

        try {
            ExtraordinaryItems::where('id', $id)->update($data);

            return response()->json(
                [
                    'status' => 200,
                    'msg' => 'success'
                ]
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(
                [
                    'status' => 999,
                    'msg' => 'error',
                ]
            );
        }
    }

    public function extraordinaryCreate(Request $request): JsonResponse
    {
        $userID = Auth::id();
        $data = $request->only(
            'report_date',
            'client_code',
            'currency_code',
            'item_name',
            'description',
            'receivable_amount',
            'payable_amount',
            'item_amount'
        );

        $data['report_date'] = Carbon::parse($data['report_date'])->format('Y-m-d');
        $data['created_at'] = date('Y-m-d h:i:s');
        $data['created_by'] = $userID;
        $data['updated_at'] = date('Y-m-d h:i:s');
        $data['updated_by'] = $userID;
        $data['active'] = 1;

        try {
            ExtraordinaryItems::insert($data);

            return response()->json(
                [
                    'status' => 200,
                    'msg' => 'success'
                ]
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(
                [
                    'status' => 999,
                    'msg' => 'error',
                ]
            );
        }
    }

    public function preValidation(Request $request): JsonResponse
    {
        //check if monthly report exist
        $formattedDate = date('Ym', strtotime($request->route('date')));
        $hasMonthlyBilling = $this->billingStatements
            ->active()
            ->where(DB::raw("DATE_FORMAT(report_date,'%Y%m')"), $formattedDate)
            ->count();

        if ($hasMonthlyBilling) {
            return response()->json(
                [
                    'status' => 403,
                    'msg' => "The selected report date {$formattedDate} was closed"
                ]
            );
        }

        //validate excel title
        $headings = (new HeadingRowImport)->toCollection($request->file('file')) ?
            (new HeadingRowImport)->toCollection($request->file('file'))->collapse()->collapse()->filter() : null;

        if (!$headings) {
            return response()->json(
                [
                    'status' => 403,
                    'msg' => "Title unmatched"
                ]
            );
        }

        switch ($request->route('type')) {
            case 'platform_ad_fees':
                $diff = $headings->diff(ImportTitle::PLATFORM_AD) ?? null;
                break;
            case 'amazon_date_range':
                $diff = $headings->diff(ImportTitle::AMZ_DATE_RANGE) ?? null;
                break;
            case 'long_term_storage_fees':
                $diff = $headings->diff(ImportTitle::LONG_TERM) ?? null;
                break;
            case 'monthly_storage_fees':
                $diff = $headings->diff(ImportTitle::MONTHLY_STORAGE) ?? null;
                break;
            case 'first_mile_shipment_fees':
                $diff = $headings->diff(ImportTitle::FIRST_MILE_SHIPMENT) ?? null;
                break;

            default:
                $diff = null;
                break;
        }

        if ($diff->isNotEmpty()) {
            return response()->json(
                [
                    'status' => 403,
                    'msg' => "Title : {$diff->implode(', ')} unmatched"
                ]
            );
        }

        return response()->json(
            [
                'status' => 200,
                'msg' => "success"
            ]
        );
    }
}

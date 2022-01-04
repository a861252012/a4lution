<?php

namespace App\Http\Controllers;

use App\Constants\ImportTitle;
use App\Exports\AmazonDateRangeExport;
use App\Exports\FirstMileShipmentFeeExport;
use App\Exports\LongTermStorageFeesExport;
use App\Exports\MonthlyStorageFeesExport;
use App\Exports\PlatformAdFeesExport;
use App\Imports\QueueAmazonDateRangeImport;
use App\Imports\QueueFirstMileShipmentFees;
use App\Imports\QueueLongTermStorageFees;
use App\Imports\QueueMonthlyStorageFees;
use App\Imports\QueuePlatformAdFees;
use App\Models\AmazonDateRangeReport;
use App\Models\BatchJob;
use App\Models\BillingStatement;
use App\Models\ExtraordinaryItem;
use App\Models\FirstMileShipmentFee;
use App\Models\LongTermStorageFee;
use App\Models\MonthlyStorageFee;
use App\Models\PlatformAdFee;
use App\Repositories\CustomerRepository;
use App\Repositories\ExchangeRateRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Symfony\Component\HttpFoundation\Response;

class FeeController extends Controller
{
    const BATCH_STATUS = 'processing';

    private $batchJob;
    private $amazonDateRangeReport;
    private $platformAdFee;
    private $monthlyStorageFee;
    private $longTermStorageFee;
    private $firstMileShipmentFee;
    private $billingStatement;
    private $customerRepository;
    private $exchangeRateRepository;

    public function __construct(
        BatchJob               $batchJob,
        AmazonDateRangeReport  $amazonDateRangeReport,
        PlatformAdFee          $platformAdFee,
        MonthlyStorageFee      $monthlyStorageFee,
        LongTermStorageFee     $longTermStorageFee,
        FirstMileShipmentFee   $firstMileShipmentFee,
        BillingStatement       $billingStatement,
        CustomerRepository     $customerRepository,
        ExchangeRateRepository $exchangeRateRepository
    ) {
        $this->batchJob = $batchJob;
        $this->amazonDateRangeReport = $amazonDateRangeReport;
        $this->platformAdFee = $platformAdFee;
        $this->monthlyStorageFee = $monthlyStorageFee;
        $this->longTermStorageFee = $longTermStorageFee;
        $this->firstMileShipmentFee = $firstMileShipmentFee;
        $this->billingStatement = $billingStatement;
        $this->customerRepository = $customerRepository;
        $this->exchangeRateRepository = $exchangeRateRepository;
    }

    public function uploadView(Request $request)
    {
        $data['lists'] = $this->batchJob->query()
        ->select(
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
            ->when($request->status, fn ($q) => $q->where('batch_jobs.status', $request->status))
            ->when($request->fee_type, fn ($q) => $q->where('batch_jobs.fee_type', $request->fee_type))
            ->when(
                $request->search_date,
                fn ($q) => $q->whereBetween(
                    'batch_jobs.created_at',
                    [
                        Carbon::parse($request->search_date)->startOfDay()->toDateTimeString(),
                        Carbon::parse($request->search_date)->endOfDay()->toDateTimeString(),
                    ]
                )
            )
            ->when(
                $request->report_date,
                fn ($q) => $q->whereBetween(
                    'batch_jobs.report_date',
                    [
                        Carbon::parse($request->report_date)->startOfMonth()->toDateTimeString(),
                        Carbon::parse($request->report_date)->endOfMonth()->toDateTimeString(),
                    ]
                )
            )
            ->orderby('batch_jobs.id', 'desc')->paginate(50)->appends(request()->query());

        return view('fee.upload', $data);
    }

    public function uploadFile(Request $request)
    {
        $fileData = $request->file('file');
        $feeType = $request->inline_fee_type;
        $inputReportDate = date('Y-m-d', strtotime($request->inline_report_date));
        $reportDate = $inputReportDate ? date('Y-m-d', strtotime($inputReportDate)) : date('Y-m-d');

        $insertBatchID = BatchJob::insertGetId([
            'user_id' => Auth::id(),
            'fee_type' => $feeType,
            'file_name' => $fileData->getClientOriginalName(),
            'report_date' => $reportDate,
            'total_count' => 0,
            'status' => self::BATCH_STATUS,
            'created_at' => now()->timezone((config('services.timezone.taipei')))->toDateTimeString(),
        ]);

        //查詢該月是否已結算,如已結算則不得再更改
        $haveMonthlyReport = $this->billingStatement
            ->active()
            ->where('report_date', $inputReportDate)
            ->count();

        if ($haveMonthlyReport) {
            BatchJob::where('id', $insertBatchID)->update(
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
        $data['reportDate'] = $request->report_date ?? date('Y-m');
        $reportDateFrom = $data['reportDate'] ? date('Y-m-01', strtotime($data['reportDate'])) : date('Y-m-01');
        $reportDateTo = $data['reportDate'] ? date('Y-m-31', strtotime($data['reportDate'])) : date('Y-m-31');
        $data['supplier'] = $request->supplier ?? null;
        $data['platform'] = $request->platform ?? null;

        //調整report_date格式
        $formattedReportDate = DB::raw("date_format(report_date,'%M-%Y') as report_date");

        $query = $this->platformAdFee->select(
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
        $data['reportDate'] = $request->report_date ?? date('Y-m');
        $reportDateFrom = $data['reportDate'] ? date('Y-m-01', strtotime($data['reportDate'])) : date('Y-m-01');
        $reportDateTo = $data['reportDate'] ? date('Y-m-31', strtotime($data['reportDate'])) : date('Y-m-31');
        $data['orderID'] = $request->order_id ?? null;
        $data['sku'] = $request->sku ?? null;
        $data['supplier'] = $request->supplier ?? null;

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
        $data['reportDate'] = $request->report_date ?? date('Y-m');
        $reportDateFrom = $data['reportDate'] ? date('Y-m-01', strtotime($data['reportDate'])) : date('Y-m-01');
        $reportDateTo = $data['reportDate'] ? date('Y-m-31', strtotime($data['reportDate'])) : date('Y-m-31');
        $data['supplier'] = $request->supplier ?? null;

        //調整report_date格式
        $formattedReportDate = DB::raw("date_format(report_date,'%M-%Y') as report_date");

        $query = $this->monthlyStorageFee->select(
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
            $query->where('supplier', $data['supplier']);
        }

        $data['lists'] = $query->orderby('report_date', 'desc')
            ->paginate(100)
            ->appends($request->query());

        return view('fee.monthlyStorageView', ['data' => $data]);
    }

    public function longTermStorageView(Request $request)
    {
        $data['reportDate'] = $request->report_date ?? date('Y-m');
        $reportDateFrom = $data['reportDate'] ? date('Y-m-01', strtotime($data['reportDate'])) : date('Y-m-01');
        $reportDateTo = $data['reportDate'] ? date('Y-m-31', strtotime($data['reportDate'])) : date('Y-m-31');
        $data['supplier'] = $request->supplier ?? null;
        $data['sku'] = $request->sku ?? null;

        //調整report_date格式
        $formattedReportDate = DB::raw("date_format(report_date,'%M-%Y') as report_date");

        $query = $this->longTermStorageFee->select(
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
        $data['reportDate'] = $request->report_date ?? date('Y-m');
        $reportDateFrom = $data['reportDate'] ? date('Y-m-01', strtotime($data['reportDate'])) : date('Y-m-01');
        $reportDateTo = $data['reportDate'] ? date('Y-m-31', strtotime($data['reportDate'])) : date('Y-m-31');
        $data['clientCode'] = $request->client_code ?? null;
        $data['fbaShipment'] = $request->fba_shipment ?? null;
        $data['idsSku'] = $request->ids_sku ?? null;
        $data['account'] = $request->account ?? null;

        //調整report_date格式
        $formattedReportDate = DB::raw("date_format(report_date,'%M-%Y') as report_date");

        $query = $this->firstMileShipmentFee->select(
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

                return (new FirstMileShipmentFeeExport)->download($fileName, \Maatwebsite\Excel\Excel::XLSX);
        }
    }

    public function extraordinaryItem(Request $request)
    {
        $data['clientCode'] = $request->client_code ?? null;
        $data['reportDate'] = $request->report_date ?? null;

        $data['lists'] = ExtraordinaryItem::from('extraordinary_items as e')
            ->leftJoin('users as u', function ($join) {
                $join->on('u.id', '=', 'e.updated_by');
                $join->where('u.active', 1);
            })
            ->select(
                'e.id',
                'e.client_code',
                DB::raw("date_format(e.report_date,'%b-%Y') as report_date"),
                'e.item_name',
                'e.description',
                'e.currency_code',
                'e.receivable_amount',
                'e.payable_amount',
                'e.item_amount',
                'e.created_at',
                'e.created_by',
                'e.updated_at',
                'u.full_name as updated_by'
            )
            ->withoutGlobalScope('isActive')
            ->where('e.active', 1)
            ->when($data['clientCode'], function ($q, $clientCode) {
                return $q->where('e.client_code', $clientCode);
            })
            ->when($data['reportDate'], function ($q, $reportDate) {
                return $q->whereBetween(
                    'e.report_date',
                    [
                        Carbon::parse($reportDate)->format('Y-m-d 00:00:00'),
                        Carbon::parse($reportDate)->format('Y-m-d 23:59:59')
                    ]
                );
            })
            ->orderBy('e.id', 'desc')
            ->paginate(50);

        return view('fee.extraordinaryItem', $data);
    }

    public function deleteExtraordinaryItem(Request $request): JsonResponse
    {
        $id = $request->route('id');

        try {
            $item = ExtraordinaryItem::findOrFail($id);

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
            $data = $this->customerRepository->getAllClientCode();

            return response()->json(
                [
                    'status' => 200,
                    'msg' => 'success',
                    'data' => $data
                ]
            );
        } catch (Exception $e) {
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
            $data = $this->exchangeRateRepository->getAllCurrency();

            return response()->json(
                [
                    'status' => 200,
                    'msg' => 'success',
                    'data' => $data
                ]
            );
        } catch (Exception $e) {
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

        try {
            ExtraordinaryItem::create($data);

            return response()->json(
                [
                    'status' => 200,
                    'msg' => 'success'
                ]
            );
        } catch (Exception $e) {
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
        $data['report_date'] = Carbon::parse($data['report_date'])->format('Y-m-d');

        try {
            ExtraordinaryItem::where('id', $id)->update($data);

            return response()->json(
                [
                    'status' => 200,
                    'msg' => 'success'
                ]
            );
        } catch (Exception $e) {
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

        try {
            ExtraordinaryItem::create($data);

            return response()->json(
                [
                    'status' => 200,
                    'msg' => 'success'
                ]
            );
        } catch (Exception $e) {
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
        $dateObject = Carbon::parse($request->route('date'));

        //exchange rate validation
        $exchangeRate = (new ExchangeRateRepository)->getByQuotedDate($dateObject->startOfMonth()->toDateString());

        if ($exchangeRate->isEmpty()) {
            return response()->json(
                [
                    'status' => Response::HTTP_FORBIDDEN,
                    'msg' => "Currency Exchange Rate Not Found Error"
                ]
            );
        }

        //check if monthly report exist
        $hasMonthlyBilling = $this->billingStatement
            ->active()
            ->whereRaw(
                "DATE_FORMAT(report_date,'%Y%m') = ?",
                [$dateObject->format('Ym')]
            )
            ->count();

        if ($hasMonthlyBilling) {
            $formattedDate = $dateObject->format('Y-m');

            return response()->json(
                [
                    'status' => Response::HTTP_FORBIDDEN,
                    'msg' => "The {$formattedDate} sales summary was generated.
                            Please delete the sales summary and reupload it."
                ]
            );
        }

        //validate excel title
        $headings = (new HeadingRowImport)->toCollection($request->file('file')) ?
            (new HeadingRowImport)->toCollection($request->file('file'))->collapse()->collapse()->filter() : null;

        if (!$headings) {
            return response()->json(
                [
                    'status' => Response::HTTP_FORBIDDEN,
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
                    'status' => Response::HTTP_FORBIDDEN,
                    'msg' => "Title : {$diff->implode(', ')} unmatched"
                ]
            );
        }

        return response()->json(
            [
                'status' => Response::HTTP_OK,
                'msg' => "success"
            ]
        );
    }
}
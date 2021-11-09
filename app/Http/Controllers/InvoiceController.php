<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\Roles;
use App\Models\Orders;
use App\Models\Invoices;
use App\Models\Customers;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use App\Jobs\UploadFileToAWS;
use App\Models\ExchangeRates;
use App\Models\OrderProducts;
use App\Models\RmaRefundList;
use App\Exports\FBADateExport;
use App\Exports\InvoiceExport;
use App\Models\PlatformAdFees;
use App\Models\RoleAssignment;
use App\Jobs\Invoice\SetSaveDir;
use App\Services\InvoiceService;
use App\Models\BillingStatements;
use App\Models\CustomerRelations;
use Illuminate\Http\JsonResponse;
use App\Models\CommissionSettings;
use Illuminate\Support\Facades\DB;
use App\Exports\SalesExpenseExport;
use App\Jobs\Invoice\CreateZipToS3;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\AmazonDateRangeReport;
use App\Models\FirstMileShipmentFees;
use Illuminate\Filesystem\Filesystem;
use App\Repositories\OrdersRepository;
use App\Jobs\Invoice\ExportInvoicePDFs;
use App\Repositories\InvoiceRepository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use App\Jobs\Invoice\ExportInvoiceExcel;
use App\Repositories\OrderProductsRepository;
use App\Repositories\AmazonReportListRepository;
use App\Repositories\BillingStatementRepository;
use App\Repositories\FirstMileShipmentFeesRepository;

class InvoiceController extends Controller
{
    private $invoices;
    private $customerRelations;
    private $roleAssignment;
    private $roles;
    private $exchangeRates;
    private $commissionSettings;
    private $billingStatements;
    private $customers;
    private $ordersRepository;
    private $orderProductRepository;
    private $amazonReportListRepository;
    private $firstMileShipmentFeesRepository;
    private $billingStatementRepository;
    private $firstMileShipmentFees;
    private $invoiceService;
    private const MANAGER_ROLE_NAME = 'manager';

    public function __construct(
        Invoices                        $invoices,
        CustomerRelations               $customerRelations,
        Customers                       $customers,
        RoleAssignment                  $roleAssignment,
        Roles                           $roles,
        ExchangeRates                   $exchangeRates,
        CommissionSettings              $commissionSettings,
        BillingStatements               $billingStatements,
        OrdersRepository                $ordersRepository,
        OrderProductsRepository         $orderProductRepository,
        AmazonReportListRepository      $amazonReportListRepository,
        FirstMileShipmentFeesRepository $firstMileShipmentFeesRepository,
        FirstMileShipmentFees           $firstMileShipmentFees,
        InvoiceService                  $invoiceService,
        BillingStatementRepository      $billingStatementRepository
    ) {
        $this->invoices = $invoices;
        $this->customerRelations = $customerRelations;
        $this->roleAssignment = $roleAssignment;
        $this->roles = $roles;
        $this->exchangeRates = $exchangeRates;
        $this->commissionSettings = $commissionSettings;
        $this->billingStatements = $billingStatements;
        $this->customers = $customers;
        $this->ordersRepository = $ordersRepository;
        $this->orderProductRepository = $orderProductRepository;
        $this->amazonReportListRepository = $amazonReportListRepository;
        $this->firstMileShipmentFeesRepository = $firstMileShipmentFeesRepository;
        $this->billingStatementRepository = $billingStatementRepository;
        $this->firstMileShipmentFees = $firstMileShipmentFees;
        $this->invoiceService = $invoiceService;
    }

    public function getAvolutionCommission(string $clientCode, string $shipDate, float $tieredParam, array $commissionRate)
    {
        switch ($commissionRate['type']) {
            case 'sku':
                $orderProductRepository = new OrderProductsRepository();

                return $orderProductRepository->getSkuAvolutionCommission($clientCode, $shipDate) ?: 0;
            case 'promotion':
                return $commissionRate['value'];
            case 'tiered':
                return $tieredParam * $commissionRate['value'];
        }
    }


    public function getTieredInfo(string $clientCode, float $totalSalesAmount): array
    {
        $setting = CommissionSettings::where('client_code', $clientCode)->first();

        if (!empty($setting) & $totalSalesAmount >= $setting->tier_1_threshold) {
            $newLevel = 1;
            for ($i = 1; $i <= 4; $i++) {
                $key = "tier_{$i}_threshold";
                $val = $setting->$key;
                if ($totalSalesAmount >= $val) {
                    $newLevel = $i;
                }
            }
            //如有amount則先取amount
            $amountKey = "tier_{$newLevel}_amount";
            if (!empty((float)$setting->$amountKey)) {
                return ['type' => 'tiered', 'value' => $setting->$amountKey, 'status' => 'success'];
            }

            $rateKey = "tier_{$newLevel}_rate";

            return ['type' => 'tiered', 'value' => $setting->$rateKey, 'status' => 'success'];
        }

        return ['type' => 'tiered', 'value' => $setting->basic_rate, 'status' => 'success'];
    }

    public function getCommissionRate(string $clientCode, string $reportDate, float $totalSalesAmount)
    {
        $commissionSettings = new CommissionSettings();
        $orderProductRepository = new OrderProductsRepository();

        $settings = $commissionSettings->where('client_code', $clientCode)->first();

        if ($settings->is_sku_level_commission === 'T') {
            //check unmatched record
            $haveUnmatchedRecord = $orderProductRepository->checkUnmatchedRecord($clientCode, $reportDate);
            if (!empty($haveUnmatchedRecord)) {
                return [
                    'msg' => 'SKU-level commissions list need to match up with SKUs',
                    'status' => 'error',
                ];
            }

            $orders = $orderProductRepository->getFitOrder($clientCode, $reportDate);
            if ($orders) {
                foreach ($orders as $item) {
                    $thisOrder = OrderProducts::find($item->id);

                    $thisOrder->sku_commission_rate = $this->getSkuCommissionRate(
                        $item,
                        (float)$item->selling_price,
                        (float)$item->threshold
                    );
                    $thisOrder->sku_commission_amount = (float)$item->selling_price * (float)$thisOrder->sku_commission_rate;
                    $thisOrder->sku_commission_computed_at = date('Y-m-d h:i:s');
                    $thisOrder->save();
                }
                return ['type' => 'sku', 'value' => '0', 'status' => 'success'];
            }
        }

        //check if commission rate type is promotion
        $maxDiscountRate = $orderProductRepository->getMaxDiscountRate($clientCode, $reportDate);

        if ((float)$settings->promotion_threshold >= (float)$maxDiscountRate) {
            return ['type' => 'promotion', 'value' => $settings->tier_promotion, 'status' => 'success'];
        }

        //check if commission rate type is tiered
        if ($settings->tier === 'T') {
            return $this->getTieredInfo($clientCode, $totalSalesAmount);
        }
        return ['type' => 'tiered', 'value' => $settings->basic_rate, 'status' => 'success'];
    }

    public function getSumValue(array $fees): float
    {
        $sum = 0;
        foreach ($fees as $v) {
            $sum += (float)$v;
        }

        return (float)$sum;
    }

    public function listView(Request $request)
    {
        $data['clientCode'] = $request->input('client_code') ?? null;
        $data['reportDate'] = $request->input('report_date') ?? null;
        $data['status'] = $request->input('status') ?? null;
        $data['lists'] = [];

        //取得登入用戶的對應 client_code列表
        $roleID = $this->roleAssignment->select('role_id')
            ->where('user_id', Auth::id())
            ->where('active', 1)
            ->pluck('role_id');

        $managerRoleID = $this->roles->select('id')
            ->where('role_name', self::MANAGER_ROLE_NAME)
            ->where('active', 1)
            ->pluck('id');

        if ($roleID == $managerRoleID) {
            $data['client_code_lists'] = $this->customerRelations
                ->select('customer_relations.client_code')
                ->distinct('customer_relations.client_code')
                ->pluck('client_code');
        } else {
            $data['client_code_lists'] = $this->customerRelations
                ->select('customer_relations.client_code')
                ->distinct('customer_relations.client_code')
                ->join('users', 'users.id', '=', 'customer_relations.user_id')
                ->where('customer_relations.active', 1)
                ->where('users.id', Auth::id())
                ->pluck('client_code');
        }

        if (count($request->all())) {
            $formattedReportDate = DB::raw("date_format(report_date,'%M-%Y') AS report_date");

            $query = $this->invoices->select(
                'id',
                'client_code',
                $formattedReportDate,
                'opex_invoice_no',
                'doc_file_name',
                'doc_status',
                'doc_storage_token',
                'created_at'
            );

            if ($data['clientCode']) {
                $query->where('client_code', $data['clientCode']);
            }

            if ($data['reportDate']) {
                $query->where('report_date', date('Y-m-01', strtotime($data['reportDate'])));
            }

            if ($data['status']) {
                $query->where('doc_status', $data['status']);
            }
            $data['lists'] = $query->orderBy('id', 'desc')->paginate(100);
        }

        return view('invoice/list', $data);
    }

    public function downloadFile(Request $request)
    {
        $token = $request->route('token') ?? null;

        if (!$token) {
            return back()->with('message', 'failed to download');
        }

        $fileName = sprintf(
            '%s.zip',
            $this->invoices->where('doc_storage_token', $token)->value('doc_file_name')
        );

        $headers = [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        return \Response::make(
            Storage::disk('s3')->get("invoices/{$token}.zip"), 
            200, 
            $headers
        );

    }

    public function issueView(Request $request)
    {
        $data['sel_client_code'] = $request->input('sel_client_code') ?? null;
        $data['report_date'] = $request->input('report_date') ?? null;
        $data['status'] = $request->input('status') ?? null;
        $data['lists'] = [];

        //取得登入用戶的對應 client_code列表
        $roleID = $this->roleAssignment->select('role_id')
            ->where('user_id', Auth::id())
            ->where('active', 1)
            ->pluck('role_id');

        $managerRoleID = $this->roles->select('id')
            ->where('role_name', self::MANAGER_ROLE_NAME)
            ->where('active', 1)
            ->pluck('id');

        if ($roleID == $managerRoleID) {
            $data['client_code_lists'] = $this->customerRelations
                ->select('customer_relations.client_code')
                ->distinct('customer_relations.client_code')
                ->pluck('client_code');
        } else {
            $data['client_code_lists'] = $this->customerRelations
                ->select('customer_relations.client_code')
                ->distinct('customer_relations.client_code')
                ->join('users', 'users.id', '=', 'customer_relations.user_id')
                ->where('customer_relations.active', 1)
                ->where('users.id', Auth::id())
                ->pluck('client_code');
        }

        if (count($request->all())) {
            $formattedShipDate = DB::raw("date_format(report_date,'%b-%Y') as 'report_date'");
            $query = $this->billingStatements->select(
                'id',
                'client_code',
                'avolution_commission',
                'commission_type',
                'total_sales_orders',
                'total_sales_amount',
                'total_expenses',
                $formattedShipDate
            )->where('active', 1);

            if ($data['sel_client_code']) {
                $query->where('client_code', $data['sel_client_code']);
            }

            if ($data['report_date']) {
                $query->where('report_date', date('Y-m-01', strtotime($data['report_date'])));
            }
            $data['lists'] = $query->paginate();
        }

        return view('invoice/issue', $data);
    }

    public function createBill(Request $request): JsonResponse
    {
        $data = collect($request)->only($this->billingStatementRepository->getTableColumns());

        $data->put('report_date', date('Y-m-d', strtotime($data['report_date'])));
        $data->put('created_at', date('Y-m-d h:i:s'));
        $data->put('created_by', Auth::id());
        $data->put('active', 1);
        $data->put('commission_type', 'manual');

        try {
            $this->billingStatementRepository->create($data->all());
        } catch (QueryException $exception) {
            return response()->json(
                [
                    'msg' => $exception->errorInfo,
                    'status' => 500,
                ],
                500
            );
        }

        return response()->json(['msg' => 'success', 'status' => 200]);
    }

    public function reportValidation(): JsonResponse
    {
        $res = $this->invoiceService->reportValidation(
            request()->route('date'),
            request()->route('clientCode')
        );

        return response()->json(
            [
                'msg' => $res['msg'],
                'status' => $res['status'],
            ]
        );
    }

    public function editView(Request $request)
    {
        $billingStatementId = $request->billing_statement_id;
        $data['clientCode'] = $request->client_code ?? null;
        $data['reportDate'] = $request->report_date ?? null;
        $data['status'] = $request->status ?? null;

        $reportDate = Carbon::parse($data['reportDate']);

        $data['formattedStartDate'] = $reportDate->format('jS F Y');
        $data['formattedEndDate'] = $reportDate->endOfMonth()->format('jS F Y');
        $data['formattedReportDate'] = $reportDate->endOfMonth()->format('F Y');
        $data['currentDate'] = date("m/d/Y");
        $data['nextMonthDate'] = date("m/d/Y", strtotime('+30 days', strtotime($data['currentDate'])));

        // TODO: create repo
        $data['billingStatement'] = $this->billingStatements->find($billingStatementId);

//        Client Contact : customers.contact_person
        $data['customerInfo'] = $this->customers
            ->select(
                'contact_person',
                'company_name',
                'address1',
                'address2',
                'city',
                'district',
                'zip',
                'country'
            )
            ->where('client_code', $data['clientCode'])
            ->first()
            ->toArray();

        //打api取 SupplierName
        $getSupplierCode = $this->customers->where('client_code', $data['clientCode'])
            ->value('supplier_code');

        $getSupplierName = $this->sendERPRequest(
            env("ERP_WMS_URL"),
            'getSupplierInfo',
            ["supplierCode" => $getSupplierCode],
            "",
            "",
            100
        );

        $data['supplierName'] = $getSupplierName['data']['supplierName'] ?? '';

        return view('invoice/edit', $data);
    }

    // TODO: add Request
    public function ajaxExport(Request $request)
    {
        $data = $request->all();

        $data['report_date'] = Carbon::parse($request->step_report_date)->format('Y-m-d');

        $data['issue_date'] = isset($data['issue_date']) 
            ? date("Y-m-d", strtotime($data['issue_date'])) 
            : date("Y-m-d");

        $data['due_date'] = isset($data['due_date']) 
            ? date("Y-m-d", strtotime($data['due_date'])) 
            : date('Y-m-d', strtotime('+30 days'));

        $formattedIssueDate = date("ymd", strtotime($data['issue_date']));
        $formattedSupplier = str_replace(' ', '_', ($data['supplier_name']));

        $data['opex_invoice_no'] = sprintf('INV-%d%s_1', $formattedIssueDate, $formattedSupplier);
        $data['fba_shipment_invoice_no'] = sprintf('INV-%d%s_FBA', $formattedIssueDate, $formattedSupplier);
        $data['credit_note_no'] = sprintf('CR-%d%s_1', $formattedIssueDate, $formattedSupplier);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = Auth::id();
        $data['created_by'] = Auth::id();
        $data['active'] = 1;
        $data['doc_status'] = "processing";
        $data['doc_file_name'] = sprintf(
            '%s_invoice_%s%d',
            $data['client_code'],
            date("Fy", strtotime($data['report_date'])),
            date('YmdHis')
        );

        $data['approved_at'] = null;
        $data['approved_by'] = null;

        unset($data['_token']);
        unset($data['step_report_date']);

        $data['doc_storage_token'] = $this->genDocStorageToken();

        $invoice = Invoices::create($data);
        $invoiceID = $invoice->id;

        $batch = \Bus::batch([
            [
                new SetSaveDir($invoiceID),
                new ExportInvoiceExcel($invoice),
                new ExportInvoicePDFs($invoice),
                new CreateZipToS3($invoice),
            ],
        ])->then(function (Batch $batch) use ($invoiceID) {
            (new InvoiceRepository)->update($invoiceID, ['doc_status' => 'active']);

        })->catch(function (Batch $batch, \Throwable $e) use ($invoiceID) {
            (new InvoiceRepository)->update($invoiceID, ['doc_status' => 'failed']);

        })->finally(function (Batch $batch) {
            // TODO: 建立排程刪除舊資料(Local)
        })->dispatch();

    }

    public function genDocStorageToken(): string
    {
        return sprintf(
            '%s_%d',
            str_shuffle(uniqid()),
            (int)(microtime(true) * 1000)
        );
    }

    public function getSkuCommissionRate(object $item, float $sellingPrice, float $threshold)
    {
        if ($sellingPrice > $threshold) {
            return $item->upper_bound_rate;
        }

        return $item->basic_rate;
    }

    public function deleteIssue(Request $request)
    {
        $condition = $request->route('condition') ?? null;//could be report_date or id,depend on type
        $type = $request->route('type');

        if ($type === 'byID' && $condition) {
            $billingStatements = $this->billingStatements->find($condition);
            if ($billingStatements) {
                $billingStatements->active = 0;
                $billingStatements->deleted_at = date('Y-m-d h:i:s');
                $billingStatements->deleted_by = Auth::id();
                $billingStatements->save();

                return response()->json(['msg' => 'deleted', 'status' => 'success', 'icon' => 'success']);
            }
        }

        if ($type === 'byDate' && $condition) {
            $reportDate = date("Y-m-d", strtotime($condition));

            $this->billingStatements->where('report_date', $reportDate)
                ->update(
                    [
                        'active' => 0,
                        'deleted_at' => date('Y-m-d h:i:s'),
                        'deleted_by' => Auth::id()
                    ]
                );
        }
    }

    public function deleteInvoice(Request $request): JsonResponse
    {
        $id = $request->route('id');

        if (!$id) {
            return response()->json(['msg' => 'wrong ID', 'status' => 'error', 'icon' => 'error']);
        }

        $invoice = $this->invoices->findOrFail($id);

        if (!$invoice) {
            return response()->json(['msg' => 'wrong ID', 'status' => 'error', 'icon' => 'error']);
        }

        $invoice->active = 0;
        $invoice->updated_by = Auth::id();
        $invoice->doc_status = 'deleted';
        $invoice->save();

        return response()->json(['msg' => 'deleted', 'status' => 'success', 'icon' => 'success']);
    }
}

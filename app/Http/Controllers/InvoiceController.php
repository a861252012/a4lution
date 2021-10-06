<?php

namespace App\Http\Controllers;

use App\Jobs\UploadFileToAWS;
use App\Models\AmazonDateRangeReport;
use App\Models\Orders;
use App\Models\PlatformAdFees;
use App\Models\RmaRefundList;
use DateTime;
use Illuminate\Http\Request;
use App\Models\Invoices;
use App\Models\Roles;
use App\Models\CustomerRelations;
use App\Models\RoleAssignment;
use App\Models\BillingStatements;
use App\Models\Customers;
use App\Models\ExchangeRates;
use App\Models\CommissionSettings;
use App\Models\OrderProducts;
use App\Models\FirstMileShipmentFees;
use App\Repositories\OrdersRepository;
use App\Repositories\OrderProductsRepository;
use App\Repositories\AmazonReportListRepository;
use App\Repositories\FirstMileShipmentFeesRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\InvoiceExport;
use App\Exports\SalesExpenseExport;
use App\Exports\FBADateExport;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

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
    private $firstMileShipmentFees;
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
        FirstMileShipmentFees           $firstMileShipmentFees
    )
    {
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
        $this->firstMileShipmentFees = $firstMileShipmentFees;
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

    public function getAccountMiscellaneous(string $reportDate, string $clientCode, string $supplierName, bool $isCrafter)
    {
        $sql = "SELECT
    SUM(a.amazon_total * r.exchange_rate) AS 'Miscellaneous'
FROM
    amazon_date_range_report a
        LEFT JOIN
    exchange_rates r ON a.report_date = r.quoted_date
        AND a.currency = r.base_currency
WHERE
    a.report_date = '{$reportDate}'
        AND a.supplier = '{$clientCode}'
        AND a.`type` IN ('FBA Customer Return Fee','Adjustment','other')
        AND a.active = 1";

        $isCrafter ? $sql .= " AND a.account = '{$supplierName}'" : $sql .= " AND a.account != '{$supplierName}'";

        return DB::select($sql);
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

        $format = '%s.xlsx';

        $docFileName = sprintf(
            $format,
            $this->invoices->where('doc_storage_token', $token)->value('doc_file_name')
        );

        $headers = [
            'Content-Type' => 'application/xlsx',
            'Content-Disposition' => 'attachment; filename="' . $docFileName . '"',
        ];

        return \Response::make(Storage::disk('s3')->get($token), 200, $headers);

//        return (new InvoiceExport)->download($fileName, \Maatwebsite\Excel\Excel::XLSX);//working fine

//        dd(Excel::store(new InvoiceExport('S53A', '2021-08-01'), $fileName, 's3', \Maatwebsite\Excel\Excel::XLSX));

//        (new InvoiceExport)->queue($fileName);
//        return back()->withSuccess('Export started!');
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
                'total_sales_orders',
                'total_sales_amount',
                'total_expenses',
                'sales_gp',
                'avolution_commission',
                'sales_tax_handling',
                'sales_credit',
                'opex_invoice',
                'fba_storage_fee_invoice',
                'final_credit',
                $formattedShipDate
            )->where('active', 1);

            if ($data['sel_client_code']) {
                $query->where('client_code', $data['sel_client_code']);
            }

            if ($data['report_date']) {
                $query->where('report_date', date('Y-m-01', strtotime($data['report_date'])));
            }
            $data['lists'] = $query->paginate(100);
        }

        return view('invoice/issue', $data);
    }

    public function checkIfReportExist()
    {
        $reportDate = request()->input('report_date');
        $clientCode = request()->input('client_code');

        $formattedDate = date('Ym', strtotime($reportDate));

        $formattedReportDate = DB::raw("DATE_FORMAT(report_date,'%Y%m')");

        $hasMonthlyBilling = $this->billingStatements->where("active", 1)
            ->where($formattedReportDate, $formattedDate)
            ->where("client_code", $clientCode)
            ->count();

        if ($hasMonthlyBilling) {
            return response()->json(['status' => 'failed', 'icon' => 'error']);
        }

        return response()->json(['status' => 'success', 'icon' => 'success']);
    }

    public function editView(Request $request)
    {
        $data['clientCode'] = $request->input('client_code') ?? null;
        $data['report_date'] = $request->input('report_date') ?? null;
        $data['status'] = $request->input('status') ?? null;
        $data['lists'] = [];

        $data['formattedStartDate'] = date('jS F Y', strtotime($data['report_date']));
        $data['formattedEndDate'] = date('jS F Y', strtotime(date("Y-m-t", strtotime($data['report_date']))));
        $data['formattedReportDate'] = date('F Y', strtotime(date("Y-m-t", strtotime($data['report_date']))));
        $data['currentDate'] = date("m/d/Y");
        $data['nextMonthDate'] = date("m/d/Y", strtotime('+30 days', strtotime($data['currentDate'])));

        if (count($request->all())) {
            $query = $this->billingStatements->where('active', 1);

            if ($data['clientCode']) {
                $query->where('client_code', $data['clientCode']);
            }

            if ($data['report_date']) {
                $reportDate = DateTime::createFromFormat("M-Y", $data['report_date']);
                $formattedReportDate = $reportDate->format('Y-m-01');
                $query->where('report_date', $formattedReportDate);
            }
            $data['lists'] = $query->first();
        }

//        Client Contact : customers.contact_person
        $data['customerInfo'] = $this->customers->select(
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
//            ->where('client_code', 'C101A')//TODO need to be modified
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

    public function runReport(Request $request)
    {
        $this->dispatchNow(new UploadFileToAWS($request->all(), Auth::id(), (bool)$request->route('store')));
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

    public function deleteInvoice(Request $request): \Illuminate\Http\JsonResponse
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

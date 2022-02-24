<?php

namespace App\Http\Controllers;

use App\Constants\CommissionConstant;
use App\Http\Requests\Invoice\EditRequest;
use App\Jobs\Invoice\CreateZipToS3;
use App\Jobs\Invoice\ExportInvoiceExcel;
use App\Jobs\Invoice\ExportInvoicePDFs;
use App\Jobs\Invoice\SetSaveDir;
use App\Models\BillingStatement;
use App\Models\CommissionSetting;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\OrderProduct;
use App\Repositories\BillingStatementRepository;
use App\Repositories\CustomerRelationRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\OrderProductRepository;
use App\Services\InvoiceService;
use App\Support\ERPRequester;
use Carbon\Carbon;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class InvoiceController extends Controller
{
    private const GET_SUPPLIER_INFO = 'getSupplierInfo';
    private Invoice $invoice;
    private CustomerRelationRepository $customerRelationRepo;
    private BillingStatement $billingStatement;
    private Customer $customer;
    private InvoiceService $invoiceService;
    private BillingStatementRepository $billingStatementRepository;
    private InvoiceRepository $invoiceRepository;

    public function __construct(
        Invoice                    $invoice,
        CustomerRelationRepository $customerRelationRepo,
        Customer                   $customer,
        BillingStatement           $billingStatement,
        InvoiceService             $invoiceService,
        BillingStatementRepository $billingStatementRepository,
        InvoiceRepository          $invoiceRepository
    ) {
        $this->invoice = $invoice;
        $this->customerRelationRepo = $customerRelationRepo;
        $this->billingStatement = $billingStatement;
        $this->customer = $customer;
        $this->invoiceService = $invoiceService;
        $this->billingStatementRepository = $billingStatementRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    public function getAvolutionCommission(
        string $clientCode,
        string $shipDate,
        float  $tieredParam,
        array  $commissionRate
    ) {
        switch ($commissionRate['type']) {
            case 'sku':
                $orderProductRepository = new OrderProductRepository();

                return $orderProductRepository->getSkuAvolutionCommission($clientCode, $shipDate) ?: 0;
            case 'promotion':
                return $commissionRate['value'];
            case 'tiered':
                return $tieredParam * $commissionRate['value'];
        }
    }

    public function getCommissionRate(string $clientCode, string $reportDate, float $totalSalesAmount)
    {
        $commissionSetting = new CommissionSetting();
        $orderProductRepository = new OrderProductRepository();

        $settings = $commissionSetting->where('client_code', $clientCode)->first();

        if ($settings->calculation_type === CommissionConstant::CALCULATION_TYPE_SKU) {
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
                    $thisOrder = OrderProduct::find($item->id);

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
        if ($settings->calculation_type === CommissionConstant::CALCULATION_TYPE_TIER) {
            return $this->getTieredInfo($clientCode, $totalSalesAmount);
        }
        return ['type' => 'tiered', 'value' => $settings->basic_rate, 'status' => 'success'];
    }

    public function getSkuCommissionRate(object $item, float $sellingPrice, float $threshold)
    {
        if ($sellingPrice > $threshold) {
            return $item->upper_bound_rate;
        }

        return $item->basic_rate;
    }

    public function getTieredInfo(string $clientCode, float $totalSalesAmount): array
    {
        $setting = CommissionSetting::where('client_code', $clientCode)->first();

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
        $lists = empty(count($request->all()))
            ? []
            : $this->invoiceRepository->getListViewData(
                $request->client_code,
                $request->status,
                $request->report_date
            );

        //取得登入用戶的對應 client_code列表
        $clientCodeList = $this->customerRelationRepo->getClientCodeList();

        return view('invoice/list', compact('lists', 'clientCodeList'));
    }

    public function downloadFile(Request $request)
    {
        $token = $request->route('token') ?? null;

        if (!$token) {
            return back()->with('message', 'failed to download');
        }

        $fileName = sprintf(
            '%s.zip',
            $this->invoice->where('doc_storage_token', $token)->value('doc_file_name')
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
        $lists = empty(count($request->all()))
            ? []
            : $this->billingStatementRepository->getIssueViewData(
                $request->sel_client_code,
                $request->report_date
            );

        //取得登入用戶的對應 client_code列表
        $clientCodeList = $this->customerRelationRepo->getClientCodeList();

        return view('invoice/issue', compact('lists', 'clientCodeList'));
    }

    public function createBill(Request $request): JsonResponse
    {
        $data = collect($request)->only($this->billingStatementRepository->getTableColumns());

        $data->put('report_date', date('Y-m-d', strtotime($data['report_date'])));
        $data->put('commission_type', 'manual');

        try {
            $this->billingStatementRepository->create($data->all());
        } catch (QueryException $exception) {
            \Log::error($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'create failed');
        }

        return response()->json(
            [
                'msg' => 'success',
                'status' => Response::HTTP_OK
            ]
        );
    }

    public function reportValidation(Request $request): JsonResponse
    {
        $res = $this->invoiceService->reportValidation(
            $request->date,
            $request->clientCode
        );

        return response()->json(
            [
                'msg' => $res['msg'],
                'status' => $res['status'],
            ]
        );
    }

    public function editView(EditRequest $request)
    {
        $clientCode = $request->client_code;
        $reportDate = Carbon::parse($request->report_date);

        $formattedStartDate = $reportDate->format('jS F Y');
        $formattedEndDate = $reportDate->endOfMonth()->format('jS F Y');
        $formattedReportDate = $reportDate->endOfMonth()->format('F Y');
        $currentDate = date("m/d/Y");
        $nextMonthDate = date("m/d/Y", strtotime('+30 days', strtotime($currentDate)));

        $billingStatement = $this->billingStatement->find($request->billing_statement_id);

        $customerInfo = $this->customer
            ->select(
                'contact_person',
                'company_name',
                'address1',
                'address2',
                'city',
                'district',
                'zip',
                'country',
                'supplier_code'
            )
            ->where('client_code', $clientCode)
            ->first();

        $getSupplierName = app(ERPRequester::class)->send(
            config('services.erp.wmsUrl'),
            self::GET_SUPPLIER_INFO,
            ["supplierCode" => $customerInfo->supplier_code],
        );

        $supplierName = data_get($getSupplierName, 'data.supplierName', '');

        return view(
            'invoice/edit',
            compact(
                'clientCode',
                'formattedStartDate',
                'formattedEndDate',
                'formattedReportDate',
                'currentDate',
                'nextMonthDate',
                'billingStatement',
                'customerInfo',
                'supplierName'
            )
        );
    }

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
        $data['doc_status'] = "processing";
        $data['doc_file_name'] = sprintf(
            '%s_invoice_%s%d',
            $data['client_code'],
            date("Fy", strtotime($data['report_date'])),
            date('YmdHis')
        );

        unset($data['_token']);
        unset($data['step_report_date']);

        $data['doc_storage_token'] = $this->genDocStorageToken();

        $invoice = Invoice::create($data);
        $invoiceID = $invoice->id;

        \Bus::batch([
            [
                new SetSaveDir($invoiceID),
                new ExportInvoiceExcel($invoice),
                new ExportInvoicePDFs($invoice),
                new CreateZipToS3($invoice),
            ],
        ])->then(function (Batch $batch) use ($invoiceID) {
            (new InvoiceRepository)->update($invoiceID, ['doc_status' => 'active']);
        })->catch(function (Batch $batch, Throwable $e) use ($invoiceID) {
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

    public function deleteIssue(Request $request)
    {
        $condition = $request->condition;//could be report_date or id,depend on type

        if ($request->type === 'byID' && $condition) {
            $billingStatement = $this->billingStatement->find($condition);
            if ($billingStatement) {
                $billingStatement->active = 0;
                $billingStatement->deleted_at = date('Y-m-d h:i:s');
                $billingStatement->deleted_by = \Auth::id();
                $billingStatement->save();

                return response()->json(
                    [
                        'msg' => 'deleted',
                        'status' => Response::HTTP_OK,
                        'icon' => 'success'
                    ]
                );
            }
        }

        if ($request->type === 'byDate' && $condition) {
            $reportDate = date("Y-m-d", strtotime($condition));

            $this->billingStatement->where('report_date', $reportDate)
                ->update(
                    [
                        'active' => 0,
                        'deleted_at' => date('Y-m-d h:i:s'),
                        'deleted_by' => \Auth::id()
                    ]
                );
        }
    }

    public function deleteInvoice(Request $request): JsonResponse
    {
        try {
            $invoice = $this->invoice->findOrFail($request->id);

            $invoice->active = 0;
            $invoice->doc_status = 'deleted';
            $invoice->save();
        } catch (ModelNotFoundException $e) {
            \Log::error($e);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'delete failed');
        }

        return response()->json(
            [
                'msg' => 'deleted',
                'status' => Response::HTTP_OK,
                'icon' => 'success'
            ]
        );
    }
}

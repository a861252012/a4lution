<?php

namespace App\Http\Controllers;

use App\Jobs\Invoice\CreateZipToS3;
use App\Jobs\Invoice\ExportInvoiceExcel;
use App\Jobs\Invoice\ExportInvoicePDFs;
use App\Jobs\Invoice\SetSaveDir;
use App\Models\BillingStatement;
use App\Models\Invoice;
use App\Repositories\BillingStatementRepository;
use App\Repositories\CustomerRelationRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\CustomerRepository;
use App\Services\InvoiceService;
use App\Support\ERPRequester;
use Carbon\Carbon;
use Illuminate\Bus\Batch;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class InvoiceController extends Controller
{
    private const GET_SUPPLIER_INFO = 'getSupplierInfo';
    private Invoice $invoice;
    private CustomerRelationRepository $customerRelationRepo;
    private BillingStatement $billingStatement;
    private BillingStatementRepository $billingStatementRepo;
    private InvoiceService $invoiceService;
    private InvoiceRepository $invoiceRepo;
    private CustomerRepository $customerRepo;

    public function __construct(
        Invoice                    $invoice,
        CustomerRelationRepository $customerRelationRepo,
        BillingStatement           $billingStatement,
        InvoiceService             $invoiceService,
        BillingStatementRepository $billingStatementRepo,
        InvoiceRepository $invoiceRepo,
        CustomerRepository $customerRepo
    ) {
        $this->invoice = $invoice;
        $this->customerRelationRepo = $customerRelationRepo;
        $this->billingStatement = $billingStatement;
        $this->billingStatementRepo = $billingStatementRepo;
        $this->invoiceService = $invoiceService;
        $this->invoiceRepo = $invoiceRepo;
        $this->customerRepo = $customerRepo;
    }

    public function listView(Request $request)
    {
        $lists = empty(count($request->all()))
            ? []
            : $this->invoiceRepo->getListViewData(
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
        $token = $request->route('token');

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
            Response::HTTP_OK,
            $headers
        );
    }

    public function issueView(Request $request)
    {
        $lists = empty(count($request->all()))
            ? []
            : $this->billingStatementRepo->getIssueViewData(
                $request->sel_client_code,
                $request->report_date
            );

        //取得登入用戶的對應 client_code列表
        $clientCodeList = $this->customerRelationRepo->getClientCodeList();

        return view('invoice/issue', compact('lists', 'clientCodeList'));
    }

    public function createBill(Request $request): JsonResponse
    {
        $data = collect($request)->only($this->billingStatementRepo->getTableColumns());

        $data->put('report_date', date('Y-m-d', strtotime($data['report_date'])));
        $data->put('created_at', date('Y-m-d h:i:s'));
        $data->put('created_by', Auth::id());
        $data->put('active', 1);
        $data->put('commission_type', 'manual');

        try {
            $this->billingStatementRepo->create($data->all());
        } catch (QueryException $exception) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'created failed');
        }

        return response()->json(['msg' => 'success', 'status' => Response::HTTP_OK]);
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

    // TODO: add Request

    public function editView(Request $request)
    {
        $data['clientCode'] = $request->client_code ?? null;
        $data['reportDate'] = $request->report_date ?? null;
        $data['status'] = $request->status ?? null;

        $reportDate = Carbon::parse($data['reportDate']);

        $data['formattedStartDate'] = $reportDate->format('jS F Y');
        $data['formattedEndDate'] = $reportDate->endOfMonth()->format('jS F Y');
        $data['formattedReportDate'] = $reportDate->endOfMonth()->format('F Y');
        $data['currentDate'] = date("m/d/Y");
        $data['nextMonthDate'] = date("m/d/Y", strtotime('+30 days', strtotime($data['currentDate'])));

        $data['billingStatement'] = $this->billingStatementRepo->find($request->billing_statement_id);

        //Client Contact : customers.contact_person
        $supplierCode = $this->customerRepo->findByClientCode($data['clientCode']);

        if (optional($supplierCode)->supplier_code) {
            //打api取 SupplierName
            $getSupplierName = app(ERPRequester::class)->send(
                config('services.erp.wmsUrl'),
                self::GET_SUPPLIER_INFO,
                [
                    "supplierCode" => $supplierCode->supplier_code
                ]
            );
        }

        $data['supplierName'] = $getSupplierName['data']['supplierName'] ?? '';

        $data['customerInfo'] = collect($supplierCode)->toArray();

        return view('invoice/edit', $data);
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
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = Auth::id();
        $data['created_by'] = Auth::id();
        $data['active'] = 1;
        $data['doc_status'] = "processing";
        $data['doc_file_name'] = sprintf(
            '%s_invoice_%s_%d',
            $data['client_code'],
            date("Fy", strtotime($data['report_date'])),
            date('YmdHis')
        );

        $data['approved_at'] = null;
        $data['approved_by'] = null;

        unset($data['_token']);
        unset($data['step_report_date']);

        $data['doc_storage_token'] = $this->genDocStorageToken();

        $invoice = Invoice::create($data);
        $invoiceID = $invoice->id;

        $batch = Bus::batch([
            [
                new SetSaveDir($invoiceID),
                new ExportInvoiceExcel($invoice),
                new ExportInvoicePDFs($invoice),
                new CreateZipToS3($invoice),
            ],
        ])->then(function (Batch $batch) use ($invoiceID) {
            $this->invoiceRepo->update($invoiceID, ['doc_status' => 'active']);
        })->catch(function (Batch $batch, Throwable $e) use ($invoiceID) {
            $this->invoiceRepo->update($invoiceID, ['doc_status' => 'failed']);
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
        $condition = $request->route('condition') ?? null;//could be report_date or id,depend on type
        $type = $request->route('type');

        if ($type === 'byID' && $condition) {
            $billingStatement = $this->billingStatement->find($condition);
            if ($billingStatement) {
                $billingStatement->active = 0;
                $billingStatement->deleted_at = date('Y-m-d h:i:s');
                $billingStatement->deleted_by = Auth::id();
                $billingStatement->save();

                return response()->json(['msg' => 'deleted', 'status' => 'success', 'icon' => 'success']);
            }
        }

        if ($type === 'byDate' && $condition) {
            $reportDate = date("Y-m-d", strtotime($condition));

            $this->billingStatement->where('report_date', $reportDate)
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

        $invoice = $this->invoice->findOrFail($id);

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

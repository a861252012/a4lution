<?php

namespace App\Http\Controllers;

use App\Jobs\Invoice\CreateZipToS3;
use App\Jobs\Invoice\ExportInvoiceExcel;
use App\Jobs\Invoice\ExportInvoicePDFs;
use App\Jobs\Invoice\SetSaveDir;
use App\Models\BillingStatement;
use App\Models\Customer;
use App\Models\Invoice;
use App\Repositories\BillingStatementRepository;
use App\Repositories\CustomerRelationRepository;
use App\Repositories\InvoiceRepository;
use App\Services\InvoiceService;
use App\Support\ERPRequester;
use App\Http\Requests\Invoice\EditRequest;
use Carbon\Carbon;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function listView(Request $request)
    {
        $data['lists'] = empty(count($request->all()))
            ? []
            : $this->invoiceRepository->getListViewData(
                $request->client_code,
                $request->status,
                $request->report_date
            );

        //取得登入用戶的對應 client_code列表
        $data['client_code_lists'] = $this->customerRelationRepo->getClientCodeList();

        return view('invoice/list', $data);
    }

    public function downloadFile(Request $request)
    {
        $token = data_get($request, 'token');

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
            \Storage::disk('s3')->get("invoices/{$token}.zip"),
            Response::HTTP_OK,
            $headers
        );
    }

    public function issueView(Request $request)
    {
        $data['lists'] = empty(count($request->all()))
            ? []
            : $this->billingStatementRepository->getIssueViewData(
                $request->sel_client_code,
                $request->report_date
            );

        //取得登入用戶的對應 client_code列表
        $data['client_code_lists'] = $this->customerRelationRepo->getClientCodeList();

        return view('invoice/issue', $data);
    }

    public function createBill(Request $request): JsonResponse
    {
        $data = collect($request)->only($this->billingStatementRepository->getTableColumns());

        $data->put('report_date', date('Y-m-d', strtotime($data['report_date'])));
        $data->put('commission_type', 'manual');

        try {
            $this->billingStatementRepository->create($data->all());
        } catch (QueryException $exception) {
            return response()->json(
                [
                    'msg' => $exception->errorInfo,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return response()->json(
            [
                'msg' => 'success',
                'status' => Response::HTTP_OK
            ]
        );
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

    public function editView(EditRequest $request)
    {
        $data['clientCode'] = data_get($request, 'client_code');
        $data['reportDate'] = data_get($request, 'report_date');
        $data['status'] = data_get($request, 'status');

        $reportDate = Carbon::parse($data['reportDate']);

        $data['formattedStartDate'] = $reportDate->format('jS F Y');
        $data['formattedEndDate'] = $reportDate->endOfMonth()->format('jS F Y');
        $data['formattedReportDate'] = $reportDate->endOfMonth()->format('F Y');
        $data['currentDate'] = date("m/d/Y");
        $data['nextMonthDate'] = date("m/d/Y", strtotime('+30 days', strtotime($data['currentDate'])));

        $data['billingStatement'] = $this->billingStatement->find($request->billing_statement_id);

        $data['customerInfo'] = $this->customer
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
        $getSupplierCode = $this->customer->where('client_code', $data['clientCode'])->value('supplier_code');

        $getSupplierName = app(ERPRequester::class)->send(
            config('services.erp.wmsUrl'),
            self::GET_SUPPLIER_INFO,
            ["supplierCode" => $getSupplierCode],
        );

        $data['supplierName'] = data_get($getSupplierName, 'data.supplierName', '');

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
            return response()->json(
                [
                    'msg' => 'wrong ID',
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'icon' => 'error'
                ]
            );
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

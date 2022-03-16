<?php

namespace App\Exports;

use App\Models\BillingStatement;
use App\Models\User;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\BeforeSheet;
use Throwable;

class PaymentExport implements
    WithTitle,
    WithEvents
{
    use RegistersEventListeners;

    private string $reportDate;
    private string $clientCode;
    private int $insertInvoiceID;
    private int $insertBillingID;

    public function __construct(
        string $reportDate,
        string $clientCode,
        int    $insertInvoiceID,
        int    $insertBillingID
    ) {
        $this->reportDate = $reportDate;
        $this->clientCode = $clientCode;
        $this->insertInvoiceID = $insertInvoiceID;
        $this->insertBillingID = $insertBillingID;
    }

    public function title(): string
    {
        return 'Payment';
    }

    public function failed(Throwable $exception): void
    {
        \Log::channel('daily_queue_export')
            ->info('PaymentExport')
            ->info($exception);
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $formattedDate = date('d-M-y', strtotime($this->reportDate));
                $invoice = Invoice::find($this->insertInvoiceID);
                $billing = BillingStatement::find($this->insertBillingID);

                if (!$invoice) {
                    Log::error("can't find invoice by using id: {$this->insertInvoiceID}");
                }

                $user = User::find($invoice->created_by);

                if (!$user) {
                    Log::error("can't find user by using id: {$invoice->created_by}");
                }

                $event->sheet->SetCellValue("A7", "Date (日期)");
                $event->sheet->SetCellValue("C7", $formattedDate);

                $event->sheet->SetCellValue("A9", "Applied by (申請人)");
                $event->sheet->SetCellValue("C9", $user->full_name);


                $event->sheet->SetCellValue("A11", "Company");
                $event->sheet->SetCellValue("C11", $user->company_name);

                $event->sheet->SetCellValue("A15", "Name of Beneficiary (收款單位)");
                $event->sheet->SetCellValue("C15", $invoice->supplier_name);

                $sumOfAmount = $billing->opex_invoice + $billing->fba_storage_fee_invoice + $billing->sales_credit;

                $event->sheet->SetCellValue("A17", "Amount (金額)");
                $event->sheet->SetCellValue("C17", "$  {$sumOfAmount}");

                $event->sheet->SetCellValue("A19", "Due Date (到期日期)");

                $formattedDueDate = date('d M Y', strtotime($invoice->due_date));

                $event->sheet->SetCellValue("C19", $formattedDueDate);

                //提款原因
                $event->sheet->SetCellValue("A21", "Purpose (提款原因):");
                $event->sheet->SetCellValue("C21", $this->getWithdrawalPurpose($billing->total_sales_amount));

                $event->sheet->SetCellValue("C22", 'Document Reference');
                $event->sheet->SetCellValue("D22", 'Type');
                $event->sheet->SetCellValue("E22", 'Description');
                $event->sheet->SetCellValue("F22", 'Document Amount (HKD)');

                $event->sheet->SetCellValue("C23", $invoice->opex_invoice_no);
                $event->sheet->SetCellValue("D23", 'Invoice');
                $event->sheet->SetCellValue("E23", 'OPEX');
                $event->sheet->SetCellValue("F23", "$  {$billing->opex_invoice}");

                $event->sheet->SetCellValue("C24", $invoice->fba_shipment_invoice_no);
                $event->sheet->SetCellValue("D24", 'Invoice');
                $event->sheet->SetCellValue("E24", 'FBA Shipment');
                $event->sheet->SetCellValue("F24", "$  {$billing->fba_storage_fee_invoice}");

                $event->sheet->SetCellValue("C25", $this->getCreditNote($invoice->credit_note_no));
                $event->sheet->SetCellValue("D25", 'Credit Note');
                $event->sheet->SetCellValue("E25", 'Sales Credit');
                $event->sheet->SetCellValue("F25", "$  {$billing->sales_credit}");
                $event->sheet->SetCellValue("F26", "$  {$sumOfAmount}");


                $event->sheet->SetCellValue("C28", 'DBS Bank (Hong Kong) Limited');
                $event->sheet->SetCellValue("C29", $sumOfAmount);
                $event->sheet->SetCellValue("C30", "Account Name : " . $invoice->supplier_name);


                $event->sheet->SetCellValue("A34", 'Cheque / Cash / TT');
                $event->sheet->SetCellValue("C34", 'Online Transfer');

                $event->sheet->SetCellValue("A37", 'Requested by');
                $event->sheet->SetCellValue("C37", $user->full_name);

                $event->sheet->SetCellValue("A40", 'Prepared by');
                $event->sheet->SetCellValue("C40", $user->full_name);

                $event->sheet->SetCellValue("A43", 'Approved by');
            }
        ];
    }

    private function getWithdrawalPurpose($totalSalesAmount): string
    {
        $format = "Sales HKD %u; %u Orders";

        return sprintf($format, $totalSalesAmount, round($totalSalesAmount));
    }

    private function getCreditNote($invoiceCreditNoteNo): string
    {
        $format = "Credit Note # %s";

        return sprintf($format, $invoiceCreditNoteNo);
    }
}

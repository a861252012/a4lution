<?php

namespace App\Exports;

use App\Models\BillingStatements;
use App\Models\Invoices;
use App\Models\Users;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Excel;
use Throwable;

class CreditNoteExport implements WithTitle, WithEvents
{
    use RegistersEventListeners;

    private $reportDate;
    private $clientCode;
    private $insertInvoiceID;
    private $insertBillingID;

    public function __construct(
        string $reportDate,
        string $clientCode,
        int    $insertInvoiceID,
        int    $insertBillingID
    )
    {
        $this->reportDate = $reportDate;
        $this->clientCode = $clientCode;
        $this->insertInvoiceID = $insertInvoiceID;
        $this->insertBillingID = $insertBillingID;
    }

    public function title(): string
    {
        return 'Credit Note';
    }

    public function failed(Throwable $exception): void
    {
        $invoice = Invoices::findOrFail($this->insertInvoiceID);
        $invoice->doc_status = "deleted";
        $invoice->save();

        \Log::channel('daily_queue_export')
            ->info('StorageFeeExport')
            ->info($exception);
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {

                $invoice = Invoices::findOrFail($this->insertInvoiceID);

                $billing = BillingStatements::findOrFail($this->insertBillingID);

                $event->sheet->SetCellValue("E5", "Credit Note");
                $event->sheet->SetCellValue("D8", "Details");

                $event->sheet->SetCellValue("B9", 'TO');
                $event->sheet->SetCellValue("B10", $invoice->client_contact);
                $event->sheet->SetCellValue("B11", $invoice->client_company);
                $event->sheet->SetCellValue("B12", $invoice->client_address1);

                $streetAddress = $invoice->client_address2 . "," . $invoice->client_district;
                $cityAddress = $invoice->client_city . "," . $invoice->client_company;

                $event->sheet->SetCellValue("B13", $streetAddress);
                $event->sheet->SetCellValue("B14", $cityAddress);

                $event->sheet->SetCellValue("D9", 'Credit Note:');
                $event->sheet->SetCellValue("E9", $invoice->credit_note_no);

                $event->sheet->SetCellValue("D10", 'Issue Date:');
                $event->sheet->SetCellValue("E10", date('d-M-y', strtotime($this->reportDate)));

                $event->sheet->SetCellValue("B16", 'Item');
                $event->sheet->SetCellValue("C16", 'Description');
                $event->sheet->SetCellValue("F16", 'Amount');

                //item.A
                $event->sheet->SetCellValue("B17", 'A');

                $formattedStartDate = date('jS M Y', strtotime($this->reportDate));

                $endOfDate = date("Y-m-t", strtotime($this->reportDate));
                $formattedEndDate = date('jS M Y', strtotime($endOfDate));

                $desc = sprintf("Sales for the period of %s to %s", $formattedStartDate, $formattedEndDate);
                $event->sheet->SetCellValue("C17", $desc);
                $event->sheet->SetCellValue("F17", "HKD  {$billing->total_sales_amount}");

                //item.C
                $formattedStartDate = date('jS M Y', strtotime($this->reportDate));

                $endOfDate = date("Y-m-t", strtotime($this->reportDate));
                $formattedEndDate = date('jS M Y', strtotime($endOfDate));
                $itemFormatOfB = "Cost of Refund Cases - Refund Amount for the period of %s to %s";

                $desc = sprintf($itemFormatOfB, $formattedStartDate, $formattedEndDate);

                $event->sheet->SetCellValue("B19", 'C');
                $event->sheet->SetCellValue("C19", $desc);
                $event->sheet->SetCellValue("F19", "-HKD  {$billing->a4_account_refund_and_resend}");

                $total = (float)$billing->a4_account_refund_and_resend + (float)$billing->total_sales_amount;
                $event->sheet->SetCellValue("B20", 'Total');
                $event->sheet->SetCellValue("F20", "HKD  {$total}");
            }
        ];
    }
}

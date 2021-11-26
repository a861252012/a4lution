<?php

namespace App\Exports;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Excel;
use App\Models\BillingStatement;
use Throwable;

class SalesExpenseExport implements WithTitle, WithHeadings, WithEvents
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
        return 'Sales and Expense Summary';
    }

    public function failed(Throwable $exception): void
    {
        $invoice = Invoice::findOrFail($this->insertInvoiceID);
        $invoice->doc_status = "deleted";
        $invoice->save();

        \Log::channel('daily_queue_export')
            ->info("[SalesExpenseExport]" . $exception);
    }

    public function headings(): array
    {
        $formattedStartDate = date('jS M Y', strtotime($this->reportDate));
        $formattedEndDate = date('jS M Y', strtotime(date("Y-m-t", strtotime($this->reportDate))));
        $formattedDate = date('M-Y', strtotime(date("Y-m-t", strtotime($this->reportDate))));

        $msg = "Monthly Sales & OPEX Summary in HKD (for the period of {$formattedStartDate} to {$formattedEndDate})";

        return [
            ['', $msg],
            ['', $this->clientCode],
            ['', 'Sales Overview', $formattedDate],
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $formattedStartDate = date('jS M Y', strtotime($this->reportDate));
                $formattedEndDate = date('jS M Y', strtotime(date("Y-m-t", strtotime($this->reportDate))));

                $msg = "Monthly Sales & OPEX Summary in HKD ";
                $msg .= " (for the period of {$formattedStartDate} to {$formattedEndDate})";

                $billing = BillingStatement::findOrFail($this->insertBillingID);

                //A4lution Account Sales Overview
                $event->sheet->SetCellValue("B1", $msg);
                $event->sheet->SetCellValue("B2", $this->clientCode);
                $event->sheet->SetCellValue("B3", "Sales Overview");
                $event->sheet->SetCellValue("B4", "Total Sales Orders");
                $event->sheet->SetCellValue("C4", $billing->total_sales_orders);
                $event->sheet->SetCellValue("B5", "Total Sales Amount");
                $event->sheet->SetCellValue("C5", "$  " . (int)$billing->total_sales_amount);
                $event->sheet->SetCellValue("B6", "Total Expenses");
                $event->sheet->SetCellValue("C6", "$  " . (int)$billing->total_expenses);
                $event->sheet->SetCellValue("B7", "Sales GP");
                $event->sheet->SetCellValue("C7", "$  " . (int)$billing->sales_gp);

                //A4lution Account Expenses Breakdown
                $event->sheet->SetCellValue("B9", "Expenses Breakdown");
                $event->sheet->SetCellValue("B10", "  - Logistics Fee");
                $event->sheet->SetCellValue("C10", "$  " . (int)$billing->a4_account_logistics_fee);
                $event->sheet->SetCellValue("B11", "  - FBA Fee");
                $event->sheet->SetCellValue("C11", "$  " . (int)$billing->a4_account_fba_fee);
                $event->sheet->SetCellValue("B12", "  - FBA storage Fee");
                $event->sheet->SetCellValue("C12", "$  " . (int)$billing->a4_account_fba_storage_fee);
                $event->sheet->SetCellValue("B13", "  - Platform Fee");
                $event->sheet->SetCellValue("C13", "$  " . (int)$billing->a4_account_platform_fee);
                $event->sheet->SetCellValue("B14", "  - Refund and Resend");
                $event->sheet->SetCellValue("C14", "$  " . (int)$billing->a4_account_refund_and_resend);
                $event->sheet->SetCellValue("B15", "  - Miscellaneous");
                $event->sheet->SetCellValue("C15", "$  " . (int)$billing->a4_account_miscellaneous);

                $event->sheet->SetCellValue("B17", "Marketing Fee");
                $event->sheet->SetCellValue("B18", "  - Advertisement");
                $event->sheet->SetCellValue("C18", "$  " . (int)$billing->a4_account_advertisement);
                $event->sheet->SetCellValue("B19", "  - Marketing and Promotion");
                $event->sheet->SetCellValue("C19", "$  " . (int)$billing->a4_account_marketing_and_promotion);

                //Client Account Expenses Breakdown
                $event->sheet->SetCellValue("B21", "Expenses Breakdown");
                $event->sheet->SetCellValue("B22", "  - Logistics Fee");
                $event->sheet->SetCellValue("C22","$  " . (int)$billing->client_account_logistics_fee);
                $event->sheet->SetCellValue("B23", "  - FBA Fee");
                $event->sheet->SetCellValue("C23", "$  " . (int)$billing->client_account_fba_fee);
                $event->sheet->SetCellValue("B24", "  - FBA storage Fee");
                $event->sheet->SetCellValue("C24", "$  " . (int)$billing->client_account_fba_storage_fee);
                $event->sheet->SetCellValue("B25", "  - Platform Fee");
                $event->sheet->SetCellValue("C25", "$  " . (int)$billing->client_account_platform_fee);
                $event->sheet->SetCellValue("B26", "  - Refund and Return");
                $event->sheet->SetCellValue("C26", "$  " . (int)$billing->client_account_refund_and_resend);
                $event->sheet->SetCellValue("B27", "  - Miscellaneous");
                $event->sheet->SetCellValue("C27", "$  " . (int)$billing->client_account_miscellaneous);
                $event->sheet->SetCellValue("B29", "Marketing Fee");
                $event->sheet->SetCellValue("B30", "  - Advertisement");
                $event->sheet->SetCellValue("C30", "$  " . (int)$billing->client_account_advertisement);
                $event->sheet->SetCellValue("B31", "  - Marketing and Promotion");
                $event->sheet->SetCellValue("C31", "$  " . (int)$billing->client_account_marketing_and_promotion);

                //Avolution Commission and Sales Tax Handling
                $event->sheet->SetCellValue("B33", "Avolution Commission");
                $event->sheet->SetCellValue("C33", "$  " . (int)$billing->avolution_commission);
                $event->sheet->SetCellValue("B34", "Sales Tax Handling");
                $event->sheet->SetCellValue("C34", "$  0");
                $event->sheet->SetCellValue("B35", "Extraordinary item");
                $event->sheet->SetCellValue("C35", "$  " . (int)$billing->extraordinary_item);

                //Summary
                $event->sheet->SetCellValue("B37", "Sales Credit");
                $event->sheet->SetCellValue("C37", "$  " . (int)$billing->sales_credit);
                $event->sheet->SetCellValue("B38", "OPEX Invoice");
                $event->sheet->SetCellValue("C38", "$  " . (int)$billing->opex_invoice);
                $event->sheet->SetCellValue("B39", "FBA & Storage Fee Invoice");
                $event->sheet->SetCellValue("C39", "$  " . (int)$billing->fba_storage_fee_invoice);
                $event->sheet->SetCellValue("B40", "Final Credit");
                $event->sheet->SetCellValue("C40", "$  " . (int)$billing->final_credit);

                $event->sheet->SetCellValue("A10", "A4lution Account");

                $event->sheet->SetCellValue("A22", "Client Account");
            },
        ];
    }
}

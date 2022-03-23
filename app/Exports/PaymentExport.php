<?php

namespace App\Exports;

use App\Models\BillingStatement;
use App\Models\User;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Throwable;

class PaymentExport implements
    WithTitle,
    WithEvents,
    WithColumnWidths
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

    public function columnWidths(): array
    {
        $cols['A'] = 21;
        $cols['C'] = 39;
        $cols['D'] = 12;
        $cols['E'] = 14;
        $cols['F'] = 13;

        return $cols;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $formattedDate = date('d-F-y', strtotime($this->reportDate));
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
                $event->sheet->SetCellValue("C17", "HK  " . number_format($sumOfAmount, 2));

                $event->sheet->SetCellValue("A19", "Due Date (到期日期)");

                $formattedDueDate = date('d F Y', strtotime($invoice->due_date));

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
                $event->sheet->SetCellValue("F23", "$  " . number_format($billing->opex_invoice, 2));

                $event->sheet->SetCellValue("C24", $invoice->fba_shipment_invoice_no);
                $event->sheet->SetCellValue("D24", 'Invoice');
                $event->sheet->SetCellValue("E24", 'FBA Shipment');
                $event->sheet->SetCellValue("F24", "$  " . number_format($billing->fba_storage_fee_invoice, 2));

                $event->sheet->SetCellValue("C25", $this->getCreditNote($invoice->credit_note_no));
                $event->sheet->SetCellValue("D25", 'Credit Note');
                $event->sheet->SetCellValue("E25", 'Sales Credit');
                $event->sheet->SetCellValue("F25", "$  " . number_format($billing->sales_credit, 2));
                $event->sheet->SetCellValue("F26", "$  " . number_format($sumOfAmount, 2));


                $event->sheet->SetCellValue("C28", 'DBS Bank (Hong Kong) Limited');
                $event->sheet->SetCellValue("C29", "ACCOUNT NO.");
                $event->sheet->SetCellValue("C30", "Account Name : " . $invoice->supplier_name);


                $event->sheet->SetCellValue("A34", 'Cheque / Cash / TT');
                $event->sheet->SetCellValue("C34", 'Online Transfer');

                $event->sheet->SetCellValue("A37", 'Requested by');
                $event->sheet->SetCellValue("C37", $user->full_name);

                $event->sheet->SetCellValue("A40", 'Prepared by');
                $event->sheet->SetCellValue("C40", $user->full_name);

                $event->sheet->SetCellValue("A43", 'Approved by');
            },
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // 自動換行
                $sheet->getDelegate()->getStyle('A1:Z43')->getAlignment()->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ]
                ])->setWrapText(true);

                //TODO A4lution Logo

                //A3:F3
                $sheet->getDelegate()->getStyle('A3:F3')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
                        ]
                    ]
                ]);

                // C7:F7
                $sheet->getDelegate()->getStyle('C7:F7')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
                        ]
                    ]
                ]);

                // C9:F9
                $sheet->getDelegate()->getStyle('C9:F9')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
                        ]
                    ]
                ]);

                // C11:F11
                $sheet->getDelegate()->getStyle('C11:F11')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
                        ]
                    ]
                ]);

                // C15:F15
                $sheet->getDelegate()->getStyle('C15:F15')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
                        ],
                    ]
                ]);

                // C17:D17
                $sheet->getDelegate()->getStyle('C17:D17')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
                        ]
                    ]
                ]);

                // C19:D19
                $sheet->getDelegate()->getStyle('C19:D19')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
                        ]
                    ]
                ]);

                // C22:F25
                $sheet->getDelegate()->getStyle('C22:F25')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ],
                    ]
                ]);

                // D26:F26
                $sheet->getDelegate()->getStyle('D26:F26')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ],
                    ]
                ]);

                // C27
                $sheet->getDelegate()->getStyle('C27')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
                        ]
                    ]
                ]);

                // C28
                $sheet->getDelegate()->getStyle('C28')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
                        ]
                    ]
                ]);

                // C29
                $sheet->getDelegate()->getStyle('C29')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
                        ]
                    ]
                ]);

                // C30
                $sheet->getDelegate()->getStyle('C30')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
                        ]
                    ]
                ]);

                // C31
                $sheet->getDelegate()->getStyle('C31')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
                        ]
                    ]
                ]);

                // C34
                $sheet->mergeCells('C34:D34');

                // C34
                $sheet->getDelegate()->getStyle('C34')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
                        ]
                    ]
                ]);

                // C37:D37
                $sheet->getDelegate()->getStyle('C37:D37')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
                        ]
                    ]
                ]);

                // C40:D40
                $sheet->getDelegate()->getStyle('C40:D40')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
                        ]
                    ]
                ]);

                // C43
                $sheet->getDelegate()->getStyle('C43')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
                        ]
                    ]
                ]);
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

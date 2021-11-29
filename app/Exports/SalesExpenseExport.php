<?php

namespace App\Exports;

use Throwable;
use App\Models\Invoice;
use App\Models\BillingStatement;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class SalesExpenseExport implements WithTitle, WithHeadings, WithEvents, WithColumnWidths
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

    public function columnWidths(): array
    {
        $cols['A'] = 12;
        $cols['B'] = 36;
        foreach (range('C', 'Y') as $col) {
            $cols[$col] = 22;
        }

        return $cols;
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
                $event->sheet->SetCellValue("C5", (int)$billing->total_sales_amount);
                $event->sheet->SetCellValue("B6", "Total Expenses");
                $event->sheet->SetCellValue("C6", (int)$billing->total_expenses);
                $event->sheet->SetCellValue("B7", "Sales GP");
                $event->sheet->SetCellValue("C7", (int)$billing->sales_gp);

                //A4lution Account Expenses Breakdown
                $event->sheet->SetCellValue("B9", "Expenses Breakdown");
                $event->sheet->SetCellValue("A10", "A4lution Account");
                $event->sheet->SetCellValue("B10", "  - Logistics Fee");
                $event->sheet->SetCellValue("C10", (int)$billing->a4_account_logistics_fee);
                $event->sheet->SetCellValue("B11", "  - FBA Fee");
                $event->sheet->SetCellValue("C11", (int)$billing->a4_account_fba_fee);
                $event->sheet->SetCellValue("B12", "  - FBA storage Fee");
                $event->sheet->SetCellValue("C12", (int)$billing->a4_account_fba_storage_fee);
                $event->sheet->SetCellValue("B13", "  - Platform Fee");
                $event->sheet->SetCellValue("C13", (int)$billing->a4_account_platform_fee);
                $event->sheet->SetCellValue("B14", "  - Refund and Resend");
                $event->sheet->SetCellValue("C14", (int)$billing->a4_account_refund_and_resend);
                $event->sheet->SetCellValue("B15", "  - Miscellaneous");
                $event->sheet->SetCellValue("C15", (int)$billing->a4_account_miscellaneous);

                $event->sheet->SetCellValue("B17", "Marketing Fee");
                $event->sheet->SetCellValue("B18", "  - Advertisement");
                $event->sheet->SetCellValue("C18", (int)$billing->a4_account_advertisement);
                $event->sheet->SetCellValue("B19", "  - Marketing and Promotion");
                $event->sheet->SetCellValue("C19", (int)$billing->a4_account_marketing_and_promotion);

                //Client Account Expenses Breakdown
                $event->sheet->SetCellValue("B21", "Expenses Breakdown");
                $event->sheet->SetCellValue("A22", "Client Account");
                $event->sheet->SetCellValue("B22", "  - Logistics Fee");
                $event->sheet->SetCellValue("C22",(int)$billing->client_account_logistics_fee);
                $event->sheet->SetCellValue("B23", "  - FBA Fee");
                $event->sheet->SetCellValue("C23", (int)$billing->client_account_fba_fee);
                $event->sheet->SetCellValue("B24", "  - FBA storage Fee");
                $event->sheet->SetCellValue("C24", (int)$billing->client_account_fba_storage_fee);
                $event->sheet->SetCellValue("B25", "  - Platform Fee");
                $event->sheet->SetCellValue("C25", (int)$billing->client_account_platform_fee);
                $event->sheet->SetCellValue("B26", "  - Refund and Return");
                $event->sheet->SetCellValue("C26", (int)$billing->client_account_refund_and_resend);
                $event->sheet->SetCellValue("B27", "  - Miscellaneous");
                $event->sheet->SetCellValue("C27", (int)$billing->client_account_miscellaneous);
                $event->sheet->SetCellValue("B29", "Marketing Fee");
                $event->sheet->SetCellValue("B30", "  - Advertisement");
                $event->sheet->SetCellValue("C30", (int)$billing->client_account_advertisement);
                $event->sheet->SetCellValue("B31", "  - Marketing and Promotion");
                $event->sheet->SetCellValue("C31", (int)$billing->client_account_marketing_and_promotion);

                //Avolution Commission and Sales Tax Handling
                $event->sheet->SetCellValue("B33", "Avolution Commission");
                $event->sheet->SetCellValue("C33", (int)$billing->avolution_commission);
                $event->sheet->SetCellValue("B34", "Sales Tax Handling");
                $event->sheet->SetCellValue("C34", 0);
                $event->sheet->SetCellValue("B35", "Extraordinary item");
                $event->sheet->SetCellValue("C35", (int)$billing->extraordinary_item);

                //Summary
                $event->sheet->SetCellValue("B37", "Summary");
                $event->sheet->SetCellValue("B38", "Sales Credit");
                $event->sheet->SetCellValue("C38", (int)$billing->sales_credit);
                $event->sheet->SetCellValue("B39", "OPEX Invoice");
                $event->sheet->SetCellValue("C39", (int)$billing->opex_invoice);
                $event->sheet->SetCellValue("B40", "FBA & Storage Fee Invoice");
                $event->sheet->SetCellValue("C40", (int)$billing->fba_storage_fee_invoice);
                $event->sheet->SetCellValue("B41", "Final Credit");
                $event->sheet->SetCellValue("C41", (int)$billing->final_credit);
            },
            AfterSheet::class => function(AfterSheet $event) {

                $sheet = $event->sheet;

                // 自動換行
                $sheet->getDelegate()->getStyle('A1:Z42')->getAlignment()->setWrapText(true);

                // 凍結視窗
                $sheet->freezePane('C4');

                // B1
                $sheet->getDelegate()->getStyle('B1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11
                    ],
                ]);

                // B2
                $sheet->getDelegate()->getStyle('B2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'underline' => true,
                        'size' => 12
                    ],
                ]);

                // B3
                $sheet->getDelegate()->getStyle('B3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'underline' => true,
                        'size' => 11,
                        'color' => [
                            'argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE,
                        ]
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => '203764'
                        ]
                    ]
                ]);

                // C3
                $sheet->getDelegate()->getStyle('C3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => [
                            'argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE,
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => '203764'
                        ]
                    ]
                ]);

                // B4:C7
                $sheet->getDelegate()->getStyle('B4:C7')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // C5:C7
                $sheet->getDelegate()->getStyle('C5:C7')->applyFromArray([
                    'numberFormat' => [
                        'formatCode' => '_($* #,##0_);_($* (#,##0);_($* "-"??_);_(@_)'
                    ]
                ]);

                #########################
                ### A4lution Account ####
                #########################

                // B9:C15
                $sheet->getDelegate()->getStyle('B9:C19')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ]
                ]);

                // B9
                $sheet->getDelegate()->getStyle('B9')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'underline' => true,
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFC000'
                        ]
                    ]
                ]);

                // C9
                $sheet->getDelegate()->getStyle('C9')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFC000'
                        ]
                    ]
                ]);

                // A10
                $sheet->mergeCells('A10:A19');
                $sheet->getDelegate()->getStyle('A10')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFC000'
                        ]
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ]
                ]);

                // B10:B16
                $sheet->getDelegate()->getStyle('B10:C16')->applyFromArray([
                    'font' => [
                        'size' => 9,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFF2CC'
                        ]
                    ]
                ]);

                // C10:C16
                $sheet->getDelegate()->getStyle('C10:C16')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFF2CC'
                        ]
                    ],
                    'numberFormat' => [
                        'formatCode' => '_($* #,##0_);_($* (#,##0);_($* "-"??_);_(@_)'
                    ]
                ]);

                // B17
                $sheet->getDelegate()->getStyle('B17')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'underline' => true,
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFD966'
                        ]
                    ]
                ]);

                // C17
                $sheet->getDelegate()->getStyle('C17')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFD966'
                        ]
                    ]
                ]);

                // B18:B19
                $sheet->getDelegate()->getStyle('B18:B19')->applyFromArray([
                    'font' => [
                        'size' => 9,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFE699'
                        ]
                    ]
                ]);

                // C18:C19
                $sheet->getDelegate()->getStyle('C18:C19')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFE699'
                        ]
                    ],
                    'numberFormat' => [
                        'formatCode' => '_($* #,##0_);_($* (#,##0);_($* "-"??_);_(@_)'
                    ]
                ]);

                #######################
                ### Client Account ####
                #######################

                // B21:B31
                $sheet->getDelegate()->getStyle('B21:C31')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ]
                ]);

                // B21
                $sheet->getDelegate()->getStyle('B21')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'underline' => true,
                        'size' => 11,
                        'color' => [
                            'argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE,
                        ]
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => '4472C4'
                        ]
                    ]
                ]);

                // C21
                $sheet->getDelegate()->getStyle('C21')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => '4472C4'
                        ]
                    ]
                ]);

                // A22
                $sheet->mergeCells('A22:A31');
                $sheet->getDelegate()->getStyle('A22')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => [
                            'argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE,
                        ]
                    ],
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => '4472C4'
                        ]
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ]
                ]);

                // B22:B28
                $sheet->getDelegate()->getStyle('B22:B28')->applyFromArray([
                    'font' => [
                        'size' => 9,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'DDEBF7'
                        ]
                    ]
                ]);

                // C22:C28
                $sheet->getDelegate()->getStyle('C22:C28')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'DDEBF7'
                        ]
                    ],
                    'numberFormat' => [
                        'formatCode' => '_($* #,##0_);_($* (#,##0);_($* "-"??_);_(@_)'
                    ]
                ]);

                // B29
                $sheet->getDelegate()->getStyle('B29')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'underline' => true,
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => '9BC2E6'
                        ]
                    ]
                ]);

                // C29
                $sheet->getDelegate()->getStyle('C29')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => '9BC2E6'
                        ]
                    ]
                ]);

                // B30:B31
                $sheet->getDelegate()->getStyle('B30:B31')->applyFromArray([
                    'font' => [
                        'size' => 9,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'BDD7EE'
                        ]
                    ]
                ]);

                // C30:C31
                $sheet->getDelegate()->getStyle('C30:C31')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'BDD7EE'
                        ]
                    ],
                    'numberFormat' => [
                        'formatCode' => '_($* #,##0_);_($* (#,##0);_($* "-"??_);_(@_)'
                    ]
                ]);

                #############################
                ### Avolution Commission ####
                #############################

                // B33:C35
                $sheet->getDelegate()->getStyle('B33:C35')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'numberFormat' => [
                        'formatCode' => '_($* #,##0_);_($* (#,##0);_($* "-"??_);_(@_)'
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ]
                ]);

                ################
                ### Summary ####
                ################

                // B37
                $sheet->getDelegate()->getStyle('B37')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'underline' => true,
                        'size' => 14,
                        'color' => [
                            'argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE,
                        ]
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => '203764'
                        ]
                    ]
                ]);

                // C37
                $sheet->getDelegate()->getStyle('C37')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => '203764'
                        ]
                    ]
                ]);

                // B38:B41
                $sheet->getDelegate()->getStyle('B38:B41')->applyFromArray([
                    'font' => [
                        'size' => 12,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ]
                ]);

                // C38:C41
                $sheet->getDelegate()->getStyle('C38:C41')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'numberFormat' => [
                        'formatCode' => '_($* #,##0_);_($* (#,##0);_($* "-"??_);_(@_)'
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ]
                ]);

            },
        ];
    }
}

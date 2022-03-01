<?php

namespace App\Exports;

use App\Repositories\ContinStorageFeeRepository;
use Throwable;
use App\Models\Invoice;
use App\Models\BillingStatement;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class SalesExpenseExport implements
    WithTitle,
    WithHeadings,
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

                $continStorageFee = app(ContinStorageFeeRepository::class)->getAccountRefund(
                    $this->reportDate,
                    $this->clientCode
                );

                $msg = "Monthly Sales & OPEX Summary in HKD ";
                $msg .= " (for the period of {$formattedStartDate} to {$formattedEndDate})";

                $billing = BillingStatement::findOrFail($this->insertBillingID);

                //A4lution Account Sales Overview
                $event->sheet->SetCellValue("A10", 'A4lution Account');
                $event->sheet->SetCellValue("B1", $msg);
                $event->sheet->SetCellValue("B2", $this->clientCode);
                $event->sheet->SetCellValue("B3", "Sales Overview");
                $event->sheet->SetCellValue("B4", "Total Sales Orders");
                $event->sheet->SetCellValue("C4", $billing->total_sales_orders);

                $event->sheet->SetCellValue("B5", "Total Unit Sold");
                $event->sheet->SetCellValue("C5", round($billing->total_unit_sold));
                $event->sheet->SetCellValue("B6", "Total Sales Amount");
                $event->sheet->SetCellValue("C6", round($billing->total_sales_amount));
                $event->sheet->SetCellValue("B7", "Total Expenses");
                $event->sheet->SetCellValue("C7", round($billing->total_expenses));
                $event->sheet->SetCellValue("B8", "Sales GP");
                $event->sheet->SetCellValue("C8", round($billing->sales_gp));

                //A4lution Sales
                $event->sheet->SetCellValue("B10", "Sales");
                $event->sheet->SetCellValue("B11", "Sales Orders");
                $event->sheet->SetCellValue("C11", round($billing->a4_account_sales_orders));
                $event->sheet->SetCellValue("B12", "Sales Amount");
                $event->sheet->SetCellValue("C12", round($billing->a4_account_sales_amount));

                //A4lution Account Expenses Breakdown
                $event->sheet->SetCellValue("B13", "Expenses Breakdown");
                $event->sheet->SetCellValue("A11", "A4lution Account");
                $event->sheet->SetCellValue("B14", "  - Logistics Fee");
                $event->sheet->SetCellValue("C14", round($billing->a4_account_logistics_fee));
                $event->sheet->SetCellValue("B15", "  - FBA Fee");
                $event->sheet->SetCellValue("C15", round($billing->a4_account_fba_fee));
                $event->sheet->SetCellValue("B16", "  - FBA storage Fee");
                $event->sheet->SetCellValue("C16", round($billing->a4_account_fba_storage_fee));
                $event->sheet->SetCellValue("B17", "  - Platform Fee");
                $event->sheet->SetCellValue("C17", round($billing->a4_account_platform_fee));
                $event->sheet->SetCellValue("B18", "  - Refund and Resend");
                $event->sheet->SetCellValue("C18", round($billing->a4_account_refund_and_resend));

                $event->sheet->SetCellValue("B19", "  - Miscellaneous");
                $event->sheet->SetCellValue("C19", round($billing->a4_account_miscellaneous));

                $event->sheet->SetCellValue("B21", "Marketing Fee");
                $event->sheet->SetCellValue("B22", "  - Advertisement");
                $event->sheet->SetCellValue("C22", round($billing->a4_account_advertisement));
                $event->sheet->SetCellValue("B23", "  - Marketing and Promotion");
                $event->sheet->SetCellValue("C23", round($billing->a4_account_marketing_and_promotion));

                //Client Sales
                $event->sheet->SetCellValue("A25", "Client Account");
                $event->sheet->SetCellValue("B25", "Sales");
                $event->sheet->SetCellValue("B26", "Sales Orders");
                $event->sheet->SetCellValue("C26", round($billing->client_account_sales_orders));
                $event->sheet->SetCellValue("B27", "Sales Amount");
                $event->sheet->SetCellValue("C27", round($billing->client_account_sales_amount));

                //Client Account Expenses Breakdown
                $event->sheet->SetCellValue("B28", "Expenses Breakdown");
                $event->sheet->SetCellValue("A29", "Client Account");
                $event->sheet->SetCellValue("B29", "  - Logistics Fee");
                $event->sheet->SetCellValue("C29", round($billing->client_account_logistics_fee));

                $event->sheet->SetCellValue("B30", "  - FBA Fee");
                $event->sheet->SetCellValue("C30", round($billing->client_account_fba_fee));
                $event->sheet->SetCellValue("B31", "  - FBA storage Fee");
                $event->sheet->SetCellValue("C31", round($billing->client_account_fba_storage_fee));
                $event->sheet->SetCellValue("B32", "  - Contin storage Fee");
                $event->sheet->SetCellValue("C32", round($continStorageFee));

                $event->sheet->SetCellValue("B33", "  - Platform Fee");
                $event->sheet->SetCellValue("C33", round($billing->client_account_platform_fee));
                $event->sheet->SetCellValue("B34", "  - Refund and Resend");
                $event->sheet->SetCellValue("C34", round($billing->client_account_refund_and_resend));
                $event->sheet->SetCellValue("B35", "  - Miscellaneous");
                $event->sheet->SetCellValue("C35", round($billing->client_account_miscellaneous));

                $event->sheet->SetCellValue("B36", "Marketing Fee");
                $event->sheet->SetCellValue("B37", "  - Advertisement");
                $event->sheet->SetCellValue("C37", round($billing->client_account_advertisement));
                $event->sheet->SetCellValue("B38", "  - Marketing and Promotion");
                $event->sheet->SetCellValue("C38", round($billing->client_account_marketing_and_promotion));

                //Avolution Commission and Sales Tax Handling
                $event->sheet->SetCellValue("B40", "Avolution Commission");
                $event->sheet->SetCellValue("C40", round($billing->avolution_commission));
                $event->sheet->SetCellValue("B41", "Sales Tax Handling");
                $event->sheet->SetCellValue("C41", 0);
                $event->sheet->SetCellValue("B42", "Extraordinary item");
                $event->sheet->SetCellValue("C42", round($billing->extraordinary_item));

                //Summary
                $event->sheet->SetCellValue("B44", "Summary");
                $event->sheet->SetCellValue("B45", "Sales Credit");
                $event->sheet->SetCellValue("C45", round($billing->sales_credit));
                $event->sheet->SetCellValue("B46", "OPEX Invoice");
                $event->sheet->SetCellValue("C46", round($billing->opex_invoice));
                $event->sheet->SetCellValue("B47", "FBA & Storage Fee Invoice");
                $event->sheet->SetCellValue("C47", round($billing->fba_storage_fee_invoice));
                $event->sheet->SetCellValue("B48", "Final Credit");
                $event->sheet->SetCellValue("C48", round($billing->final_credit));
            },
            AfterSheet::class => function (AfterSheet $event) {
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

                // B4:C8
                $sheet->getDelegate()->getStyle('B4:C8')->applyFromArray([
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

                // C6:C8
                $sheet->getDelegate()->getStyle('C6:C8')->applyFromArray([
                    'numberFormat' => [
                        'formatCode' => '_($* #,##0_);_($* (#,##0);_($* "-"??_);_(@_)'
                    ]
                ]);

                #########################
                ### A4lution Account ####
                #########################

                // B10:C23
                $sheet->getDelegate()->getStyle('B10:C23')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ]
                ]);

                // B10:C10
                $sheet->getDelegate()->getStyle('B10:C10')->applyFromArray([
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

                // A10
                $event->sheet->SetCellValue("A10", "A4lution Account");
                $sheet->mergeCells('A10:A23');
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

                // B11:B23
                $sheet->getDelegate()->getStyle('B11:B23')->applyFromArray([
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

                // C11
                $sheet->getDelegate()->getStyle('C11')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFF2CC'
                        ]
                    ]
                ]);

                // C12:C20
                $sheet->getDelegate()->getStyle('C12:C20')->applyFromArray([
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

                // B13:C13
                $sheet->getDelegate()->getStyle('B13:C13')->applyFromArray([
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

                // B21:C21
                $sheet->getDelegate()->getStyle('B21:C21')->applyFromArray([
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

                // B22:B23
                $sheet->getDelegate()->getStyle('B22:B23')->applyFromArray([
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

                // C22:C23
                $sheet->getDelegate()->getStyle('C22:C23')->applyFromArray([
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

                // B25:C38
                $sheet->getDelegate()->getStyle('B25:C38')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ]
                ]);

                // B25:C25
                $sheet->getDelegate()->getStyle('B25:C25')->applyFromArray([
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

                // A22
                $sheet->mergeCells('A25:A38');
                $sheet->getDelegate()->getStyle('A25')->applyFromArray([
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

                // B26:B35
                $sheet->getDelegate()->getStyle('B26:B35')->applyFromArray([
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

                // B28
                $sheet->getDelegate()->getStyle('B28')->applyFromArray([
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

                // C26
                $sheet->getDelegate()->getStyle('C26')->applyFromArray([
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
                ]);


                // C27:C35
                $sheet->getDelegate()->getStyle('C27:C35')->applyFromArray([
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

                // C28
                $sheet->getDelegate()->getStyle('C28')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => '4472C4'
                        ]
                    ]
                ]);

                // B36
                $sheet->getDelegate()->getStyle('B36')->applyFromArray([
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

                // C36
                $sheet->getDelegate()->getStyle('C36')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => '9BC2E6'
                        ]
                    ]
                ]);

                // B37:B38
                $sheet->getDelegate()->getStyle('B37:B38')->applyFromArray([
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

                // C37:C38
                $sheet->getDelegate()->getStyle('C37:C38')->applyFromArray([
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

                // B40:C42
                $sheet->getDelegate()->getStyle('B40:C42')->applyFromArray([
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

                // B44:C48
                $sheet->getDelegate()->getStyle('B44:C48')->applyFromArray([
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

                // B44
                $sheet->getDelegate()->getStyle('B44')->applyFromArray([
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

                // C44
                $sheet->getDelegate()->getStyle('C44')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => '203764'
                        ]
                    ]
                ]);
            },
        ];
    }
}

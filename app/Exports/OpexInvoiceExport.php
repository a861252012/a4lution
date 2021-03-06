<?php

namespace App\Exports;

use App\Models\BillingStatement;
use App\Models\Invoice;
use App\Support\Calculation;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Throwable;

class OpexInvoiceExport implements
    WithTitle,
    WithEvents,
    WithColumnWidths,
    WithDrawings
{
    use RegistersEventListeners;

    private string $reportDate;
    private string $clientCode;
    private int $insertInvoiceID;
    private int $insertBillingID;
    private $user;

    public function __construct(
        string $reportDate,
        string $clientCode,
        int    $insertInvoiceID,
        int    $insertBillingID,
               $user
    ) {
        $this->reportDate = $reportDate;
        $this->clientCode = $clientCode;
        $this->insertInvoiceID = $insertInvoiceID;
        $this->insertBillingID = $insertBillingID;
        $this->user = $user;
    }

    public function title(): string
    {
        return 'OPEX Invoice';
    }

    public function failed(Throwable $exception): void
    {
        \Log::channel('daily_queue_export')
            ->info('OpexInvoiceExport')
            ->info($exception);
    }

    public function drawings(): Drawing
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setPath(public_path('pictures/A4lution_logo.jpg'));
        $drawing->setCoordinates('A1');

        return $drawing;
    }

    public function columnWidths(): array
    {
        $cols['A'] = 12;
        $cols['B'] = 16;
        $cols['C'] = 54;
        $cols['D'] = 16;
        $cols['E'] = 13;
        $cols['F'] = 15;

        return $cols;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $invoice = Invoice::find($this->insertInvoiceID);

                $billing = BillingStatement::find($this->insertBillingID);

                $event->sheet->SetCellValue("E5", "OPEX Invoice");
                $event->sheet->SetCellValue("D8", "Details");

                $event->sheet->SetCellValue("B9", 'TO');
                $event->sheet->SetCellValue("B10", $invoice->client_contact);
                $event->sheet->SetCellValue("B11", $invoice->client_company);
                $event->sheet->SetCellValue("B12", $invoice->client_address1);

                $streetAddress = $invoice->client_address2 . "," . $invoice->client_district;
                $cityAddress = $invoice->client_city . "," . $invoice->client_company;

                $event->sheet->SetCellValue("B13", $streetAddress);
                $event->sheet->SetCellValue("B14", $cityAddress);

                $event->sheet->SetCellValue("D9", 'Invoice:');
                $event->sheet->SetCellValue("E9", $invoice->opex_invoice_no);

                $event->sheet->SetCellValue("D10", 'Issue Date:');
                $event->sheet->SetCellValue("E10", $invoice->issue_date->format('d-M-y'));

                $event->sheet->SetCellValue("D11", 'Due Date:');
                $event->sheet->SetCellValue("E11", $invoice->due_date->format('d-M-y'));

                $formattedStartDate = date('jS M Y', strtotime($this->reportDate));
                $endOfDate = date("Y-m-t", strtotime($this->reportDate));
                $formattedEndDate = date('jS M Y', strtotime($endOfDate));

                $descWithDate = sprintf(
                    "Description  (for the period of %s to %s)",
                    $formattedStartDate,
                    $formattedEndDate
                );

                $event->sheet->SetCellValue("B16", 'Item');
                $event->sheet->SetCellValue("C16", $descWithDate);
                $event->sheet->SetCellValue("D16", "Unit Price");
                $event->sheet->SetCellValue("E16", "Quantity");
                $event->sheet->SetCellValue("F16", 'Amount');

                //item.A
                $logisticFee = (float)$billing->a4_account_logistics_fee +
                    (float)$billing->client_account_logistics_fee;
                if ($billing->client_code === 'G73A') {
                    $logisticFee = (float)$billing->a4_account_logistics_fee;
                }
                $event->sheet->SetCellValue("B17", 'A');
                $event->sheet->SetCellValue("C17", 'Logistic fee');
                $event->sheet->SetCellValue("D17", "HKD  " . $logisticFee);
                $event->sheet->SetCellValue("E17", 1);
                $event->sheet->SetCellValue("F17", "HKD  " . $logisticFee);


                //item.B
                $event->sheet->SetCellValue("B19", 'B');
                $event->sheet->SetCellValue("C19", 'Platform fee');
                $event->sheet->SetCellValue("D19", "HKD  " . $billing->a4_account_platform_fee);
                $event->sheet->SetCellValue("E19", 1);
                $event->sheet->SetCellValue("F19", "HKD  " . $billing->a4_account_platform_fee);

                //item.C
                $event->sheet->SetCellValue("B21", 'C');
                $event->sheet->SetCellValue("C21", 'FBA fee');
                $event->sheet->SetCellValue("D21", "HKD  " . $billing->a4_account_fba_fee);
                $event->sheet->SetCellValue("E21", 1);
                $event->sheet->SetCellValue("F21", "HKD  " . $billing->a4_account_fba_fee);

                //item.D
                $event->sheet->SetCellValue("B23", 'D');
                $event->sheet->SetCellValue("C23", 'FBA Storage Fee');
                $event->sheet->SetCellValue("D23", "HKD  " . $billing->a4_account_fba_storage_fee);
                $event->sheet->SetCellValue("E23", 1);
                $event->sheet->SetCellValue("F23", "HKD  " . $billing->a4_account_fba_storage_fee);

                //item.E
                $UnitPriceKeys = [
                    'a4_account_advertisement',
                    'a4_account_marketing_and_promotion'
                ];

                $sumOfUnitPrice = $this->getSumValue($billing, $UnitPriceKeys);

                $event->sheet->SetCellValue("B25", 'E');
                $event->sheet->SetCellValue("C25", 'Marketing Fee');
                $event->sheet->SetCellValue("D25", "HKD  " . $sumOfUnitPrice);
                $event->sheet->SetCellValue("E25", 1);
                $event->sheet->SetCellValue("F25", "HKD  " . $sumOfUnitPrice);

                //item.F
                $event->sheet->SetCellValue("B27", 'F');
                $event->sheet->SetCellValue("C27", 'Sales Tax Handling');
                $event->sheet->SetCellValue("D27", "HKD  " . $billing->sales_tax_handling);
                $event->sheet->SetCellValue("E27", 1);
                $event->sheet->SetCellValue("F27", "HKD  " . $billing->sales_tax_handling);

                //item.G
                $event->sheet->SetCellValue("B29", 'G');
                $event->sheet->SetCellValue("C29", 'Miscellaneous');
                $event->sheet->SetCellValue("D29", "HKD  " . $billing->a4_account_miscellaneous);
                $event->sheet->SetCellValue("E29", 1);
                $event->sheet->SetCellValue("F29", "HKD  " . $billing->a4_account_miscellaneous);

                //item.H
                $event->sheet->SetCellValue("B31", 'H');
                $event->sheet->SetCellValue("C31", 'Extraordinary item');
                $event->sheet->SetCellValue("D31", "HKD  " . $billing->extraordinary_item);
                $event->sheet->SetCellValue("E31", 1);
                $event->sheet->SetCellValue("F31", "HKD  " . $billing->extraordinary_item);

                //item.I
                $event->sheet->SetCellValue("B33", 'I');
                $event->sheet->SetCellValue("C33", 'A4lution Commission');
                $event->sheet->SetCellValue("D33", "HKD  " . $billing->avolution_commission);
                $event->sheet->SetCellValue("E33", 1);
                $event->sheet->SetCellValue("F33", "HKD  " . $billing->avolution_commission);

                //item Total
                $totalKeys = [
                    'a4_account_logistics_fee',
                    'a4_account_platform_fee',
                    'a4_account_fba_fee',
                    'a4_account_fba_storage_fee',
                    'a4_account_advertisement',
                    'a4_account_marketing_and_promotion',
                    'a4_account_miscellaneous',
                    'client_account_logistics_fee',
                    'sales_tax_handling',
                    'avolution_commission',
                    'extraordinary_item'
                ];

                if ($billing->client_code === 'G73A') {
                    $totalKeys = collect($totalKeys)
                        ->filter(fn($value, $key) => $value !== 'client_account_logistics_fee')
                        ->all();
                }

                $total = $this->getSumValue($billing, $totalKeys);

                $event->sheet->SetCellValue("B35", 'Total');
                $event->sheet->SetCellValue("F35", "HKD  {$total}");

                $email = $this->user->email;
                if (isset($this->user->payment_checker_email)) {
                    $email = sprintf(
                        '%s, and %s',
                        $this->user->payment_checker_email,
                        $this->user->email
                    );
                }

                //footer
                $event->sheet->SetCellValue("B44", 'Payment Method:');
                $event->sheet->SetCellValue(
                    "B46",
                    "By Transfer to the following HSBC account & send copy to {$email}"
                );
                $event->sheet->SetCellValue("B47", '     a) Beneficiary Name: A4lution Limited');
                $event->sheet->SetCellValue(
                    "B48",
                    '     b) Beneficiary Bank: THE HONGKONG AND SHANGHAI BANKING CORPORATION LTD'
                );
                $event->sheet->SetCellValue("B49", '     c) Swift code: HSBCHKHHHKH');
                $event->sheet->SetCellValue("B50", '     d) Account no.: 004-747-095693-838');
                $event->sheet->SetCellValue("B51", '2) Payment Term: within 10 working days from the date of Invoice');
            },
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // E5
                $sheet->getDelegate()->getStyle('E5')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 20,
                    ]
                ]);

                // B16:F16
                $sheet->getDelegate()->getStyle('B16:F16')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        ]
                    ]
                ]);

                // B35:F35
                $sheet->getDelegate()->getStyle('B35:F35')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        ]
                    ]
                ]);
            }
        ];
    }

    public function getSumValue(?object $fees, array $keys = []): float
    {
        $feesCollection = collect($fees);

        if ($keys) {
            $feesCollection = collect($fees)->only($keys);
        }

        return $feesCollection->map(fn ($val) => app(Calculation::class)->numberFormatPrecision($val, 4))->sum();
    }
}

<?php

namespace App\Exports;

use App\Models\FirstMileShipmentFee;
use App\Models\Invoice;
use App\Models\ReturnHelperCharge;
use App\Repositories\ContinStorageFeeRepository;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Throwable;

class FBAFirstMileShipmentFeesExport implements
    WithTitle,
    WithEvents,
    WithColumnWidths,
    WithDrawings
{
    use RegistersEventListeners;

    private string $reportDate;
    private string $clientCode;
    private int $insertInvoiceID;
    private $user;
    private int $serialNumber = 1;
    private int $firstMileCol = 19;
    private int $descNum = 20;
    private int $totalBarCol = 18;

    public function __construct(
        string $reportDate,
        string $clientCode,
        int    $insertInvoiceID,
               $user
    ) {
        $this->reportDate = $reportDate;
        $this->clientCode = $clientCode;
        $this->insertInvoiceID = $insertInvoiceID;
        $this->user = $user;
    }

    public function title(): string
    {
        return 'FBA First Mile Shipment Fee';
    }

    public function failed(Throwable $exception): void
    {
        \Log::channel('daily_queue_export')
            ->info('PaymentExport')
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
        $cols['C'] = 75;
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

                $event->sheet->SetCellValue("E5", "INVOICE");
                $event->sheet->SetCellValue("D8", "DETAILS");

                $event->sheet->SetCellValue("B9", 'TO');
                $event->sheet->SetCellValue("B10", $invoice->client_contact);
                $event->sheet->SetCellValue("B11", $invoice->client_company);
                $event->sheet->SetCellValue("B12", $invoice->client_address1);

                $streetAddress = $invoice->client_address2 . "," . $invoice->client_district;
                $cityAddress = $invoice->client_city . "," . $invoice->client_company;

                $event->sheet->SetCellValue("B13", $streetAddress);
                $event->sheet->SetCellValue("B14", $cityAddress);

                $event->sheet->SetCellValue("D9", 'Invoice Number:');
                $event->sheet->SetCellValue("E9", $invoice->fba_shipment_invoice_no);

                $event->sheet->SetCellValue("D10", 'Issue Date:');
                $event->sheet->SetCellValue("E10", $invoice->issue_date->format('d-M-y'));

                $event->sheet->SetCellValue("D11", 'Payment Terms:');
                $event->sheet->SetCellValue("E11", $invoice->payment_terms);

                //first_mile_shipment_fees data list
                $formattedStartDate = date('jS M Y', strtotime($this->reportDate));
                $endOfDate = date("Y-m-t", strtotime($this->reportDate));
                $formattedEndDate = date('jS M Y', strtotime($endOfDate));

                $descWithDate = sprintf(
                    "Item Description (for the period of %s to %s)",
                    $formattedStartDate,
                    $formattedEndDate
                );

                $event->sheet->SetCellValue("B15", 'NO');
                $event->sheet->SetCellValue("C15", $descWithDate);
                $event->sheet->SetCellValue("D15", 'Unit Price');
                $event->sheet->SetCellValue("E15", 'Quantity');
                $event->sheet->SetCellValue("F15", 'Amount');

                //1.)   Contin Storage Fee :  (一筆加總的數值)
                $continStorageFee = app(ContinStorageFeeRepository::class)->getContinStorageFee(
                    $this->reportDate,
                    $this->clientCode
                );
                $event->sheet->SetCellValue("B16", $this->serialNumber);
                $event->sheet->SetCellValue("C16", "Contin Storage Fee");
                $event->sheet->SetCellValue("F16", "HKD  {$continStorageFee}");

                $totalValue = $continStorageFee;

                $averageCbmUsage =  number_format($continStorageFee / 300, 2);

                $event->sheet->SetCellValue("C17", "Average CBM Usage: {$averageCbmUsage}");
                $event->sheet->SetCellValue("D17", "$  " . $continStorageFee);
                $event->sheet->SetCellValue("E17", 1);

                // 2.)  Contin 寄FBA的頭程費用 : 依據shipment
                $lists = FirstMileShipmentFee::selectRaw("
                    fulfillment_center as 'country',
                    fba_shipment as 'shipment_id',
                    COUNT(DISTINCT ids_sku) AS 'sku',
                    SUM(shipped) as 'shipped_qty',
                    total as 'unit_price'")
                    ->where('active', 1)
                    ->where('report_date', $this->reportDate)
                    ->where('client_code', $this->clientCode)
                    ->groupBy(['fulfillment_center', 'fba_shipment'])
                    ->get();

                if (count($lists) > 0) {
                    foreach ($lists as $k => $item) {
                        $this->serialNumber++;
                        $itemDesc = sprintf(
                            "Country:%s, Shipment ID:%s, SKU:%d, Shipped Qty:%d",
                            $item->country,
                            $item->shipment_id,
                            $item->sku,
                            $item->shipped_qty,
                        );

                        $this->firstMileCol = 19 + $k * 3;//record start from B19
                        $this->descNum = $this->firstMileCol + 1;
                        $event->sheet->SetCellValue("B{$this->firstMileCol}", $this->serialNumber);
                        $event->sheet->SetCellValue(
                            "C{$this->firstMileCol}",
                            'FBA shipment Fee from Continental HK warehouse to Amazon FBA warehouse:'
                        );
                        $event->sheet->SetCellValue("C{$this->descNum}", $itemDesc);
                        $event->sheet->SetCellValue(
                            "D{$this->descNum}",
                            "$ " .  number_format((float)$item->unit_price, 2)
                        );
                        $event->sheet->SetCellValue("E{$this->descNum}", "1");
                        $event->sheet->SetCellValue(
                            "F{$this->firstMileCol}",
                            "HKD  " . number_format((float)$item->unit_price, 2)
                        );

                        $totalValue +=  (float)$item->unit_price;
                    }
                }

                // 3.)  Return Helper : 逐筆列出
                $returnHelperList = ReturnHelperCharge::selectRaw("
                return_helper_charges.notes,
                ABS(return_helper_charges.amount * exchange_rates.exchange_rate) AS 'amount_hkd'")
                    ->leftJoin('exchange_rates', function ($join) {
                        $join->on('return_helper_charges.report_date', '=', 'exchange_rates.quoted_date')
                            ->on('return_helper_charges.currency_code', '=', 'exchange_rates.base_currency')
                            ->where('exchange_rates.active', 1);
                    })
                    ->where('return_helper_charges.supplier', $this->clientCode)
                    ->where('return_helper_charges.report_date', $this->reportDate)
                    ->where('return_helper_charges.active', 1)
                    ->get();

                if (count($returnHelperList) > 0) {
                    foreach ($returnHelperList as $k => $item) {
                        $col = $this->firstMileCol + 3 + $k * 3;
                        if ($this->firstMileCol === 19) {
                            $col = $this->firstMileCol + $k * 3;
                        }

                        $this->descNum = $col + 1;
                        $this->serialNumber++;

                        $event->sheet->SetCellValue("B{$col}", $this->serialNumber);
                        $event->sheet->SetCellValue("C{$col}", 'Return Helper Charges');
                        $event->sheet->SetCellValue("C{$this->descNum}", "{$item->notes}");
                        $event->sheet->SetCellValue(
                            "D{$this->descNum}",
                            "$ " . number_format((float)$item->amount_hkd, 2)
                        );
                        $event->sheet->SetCellValue("E{$this->descNum}", "1");
                        $event->sheet->SetCellValue("F{$col}", "HKD  " . number_format((float)$item->amount_hkd, 2));

                        $totalValue += (float)$item->amount_hkd;
                    }
                }

                $this->totalBarCol = ($this->descNum !== 20) ?  $this->descNum + 1 : 18;
                $event->sheet->SetCellValue("B{$this->totalBarCol}", 'Total');
                $event->sheet->SetCellValue("F{$this->totalBarCol}", "HKD  " . number_format((float)$totalValue, 2));

                //footer
                $footerCol = $this->totalBarCol + 5;

                $email = $this->user->email;
                if (isset($this->user->payment_checker_email)) {
                    $email = sprintf(
                        '%s, and %s',
                        $this->user->payment_checker_email,
                        $this->user->email
                    );
                }

                $event->sheet->SetCellValue("B" . ($footerCol + 1), 'Payment Method:');
                $event->sheet->SetCellValue(
                    "B" . ($footerCol + 2),
                    "By Transfer to the following HSBC account & send copy to  {$email}"
                );
                $event->sheet->SetCellValue("B" . ($footerCol + 3), '  a) Beneficiary Name: A4LUTION LIMITED');
                $event->sheet->SetCellValue(
                    "B" . ($footerCol + 4),
                    '  b) Beneficiary Bank: THE HONGKONG AND SHANGHAI BANKING CORPORATION LTD'
                );
                $event->sheet->SetCellValue("B" . ($footerCol + 5), '  c) Swift Code: HSBCHKHHHKH');
                $event->sheet->SetCellValue("B" . ($footerCol + 6), '  d) Account No.: 004-747-095693-838');
                $event->sheet->SetCellValue("B" . ($footerCol + 7), '  e) FPS registered email: info@a4lution.com');
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

                // B15:F15
                $sheet->getDelegate()->getStyle('B15:F15')->applyFromArray([
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

                //total
                $sheet->getDelegate()->getStyle("B$this->totalBarCol:F$this->totalBarCol")->applyFromArray([
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
}

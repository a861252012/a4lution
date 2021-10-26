<?php

namespace App\Exports;

use App\Models\BillingStatements;
use App\Models\Invoices;
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

class OpexInvoiceExport implements WithTitle, WithEvents
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
        return 'OPEX Invoice';
    }

    public function failed(Throwable $exception): void
    {
        $invoice = Invoices::findOrFail($this->insertInvoiceID);
        $invoice->doc_status = "deleted";
        $invoice->save();

        \Log::channel('daily_queue_export')
            ->info('OpexInvoiceExport')
            ->info($exception);
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $invoice = Invoices::findOrFail($this->insertInvoiceID);

                $billing = BillingStatements::findOrFail($this->insertBillingID);

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
                $event->sheet->SetCellValue("E9", $invoice->credit_note_no);

                $event->sheet->SetCellValue("D10", 'Issue Date:');
                $event->sheet->SetCellValue("E10", date('d-M-y', strtotime($this->reportDate)));

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
                $event->sheet->SetCellValue("D19", "HKD  " . (float)$billing->a4_account_platform_fee);
                $event->sheet->SetCellValue("E19", 1);
                $event->sheet->SetCellValue("F19", "HKD  " . (float)$billing->a4_account_platform_fee);

                //item.C
                $event->sheet->SetCellValue("B21", 'C');
                $event->sheet->SetCellValue("C21", 'FBA fee');
                $event->sheet->SetCellValue("D21", "HKD  " . (float)$billing->a4_account_fba_fee);
                $event->sheet->SetCellValue("E21", 1);
                $event->sheet->SetCellValue("F21", "HKD  " . (float)$billing->a4_account_fba_fee);

                //item.D
                $event->sheet->SetCellValue("B23", 'D');
                $event->sheet->SetCellValue("C23", 'FBA Storage Fee');
                $event->sheet->SetCellValue("D23", "HKD  " . (float)$billing->a4_account_fba_storage_fee);
                $event->sheet->SetCellValue("E23", 1);
                $event->sheet->SetCellValue("F23", "HKD  " . (float)$billing->a4_account_fba_storage_fee);

                //item.E
                $UnitPriceKeys = [
                    'a4_account_advertisement',
                    'a4_account_marketing_and_promotion'
                ];
                $sumOfUnitPrice = $this->getTotalVal($billing, $UnitPriceKeys);

                $event->sheet->SetCellValue("B25", 'E');
                $event->sheet->SetCellValue("C25", 'Marketing Fee');
                $event->sheet->SetCellValue("D25", "HKD  " . $sumOfUnitPrice);
                $event->sheet->SetCellValue("E25", 1);
                $event->sheet->SetCellValue("F25", "HKD  " . $sumOfUnitPrice);

                //item.F
                $event->sheet->SetCellValue("B27", 'F');
                $event->sheet->SetCellValue("C27", 'Sales Tax Handling');
                $event->sheet->SetCellValue("D27", "HKD  " . (float)$billing->sales_tax_handling);
                $event->sheet->SetCellValue("E27", 1);
                $event->sheet->SetCellValue("F27", "HKD  " . (float)$billing->sales_tax_handling);

                //item.G
                $event->sheet->SetCellValue("B29", 'G');
                $event->sheet->SetCellValue("C29", 'Miscellaneous');
                $event->sheet->SetCellValue("D29", "HKD  " . (float)$billing->a4_account_miscellaneous);
                $event->sheet->SetCellValue("E29", 1);
                $event->sheet->SetCellValue("F29", "HKD  " . (float)$billing->a4_account_miscellaneous);

                //item.H
                $event->sheet->SetCellValue("B31", 'H');
                $event->sheet->SetCellValue("C31", 'Extraordinary item');
                $event->sheet->SetCellValue("D31", "HKD  " . (float)$billing->extraordinary_item);
                $event->sheet->SetCellValue("E31", 1);
                $event->sheet->SetCellValue("F31", "HKD  " . (float)$billing->extraordinary_item);

                //item.I
                $event->sheet->SetCellValue("B33", 'I');
                $event->sheet->SetCellValue("C33", 'A4lution Commission');
                $event->sheet->SetCellValue("D33", "HKD  " . (float)$billing->avolution_commission);
                $event->sheet->SetCellValue("E33", 1);
                $event->sheet->SetCellValue("F33", "HKD  " . (float)$billing->avolution_commission);

                //item Total
                $totalKeys = [
                    'a4_account_logistics_fee',
                    'client_account_logistics_fee',
                    'a4_account_platform_fee',
                    'a4_account_fba_fee',
                    'a4_account_fba_storage_fee',
                    'a4_account_advertisement',
                    'a4_account_marketing_and_promotion',
                    'sales_tax_handling',
                    'a4_account_miscellaneous',
                    'avolution_commission',
                    'extraordinary_item'
                ];

                if ($billing->client_code === 'G73A') {
                    $totalKeys = collect($totalKeys)->forget('client_account_logistics_fee')->all();
                }

                $event->sheet->SetCellValue("B35", 'Total');
                $event->sheet->SetCellValue("F35", "HKD  " . $this->getTotalVal($billing, $totalKeys));

                //footer
                $event->sheet->SetCellValue("B44", 'Payment Method:');
                $event->sheet->SetCellValue("B46", 'By Transfer to the following HSBC account & send copy to sammi.chan@a4lution.com and billy.kwan@a4lution.com');
                $event->sheet->SetCellValue("B47", '     a) Beneficiary Name: A4lution Limited');
                $event->sheet->SetCellValue("B48", '     b) Beneficiary Bank: THE HONGKONG AND SHANGHAI BANKING CORPORATION LTD');
                $event->sheet->SetCellValue("B49", '     c) Swift code: HSBCHKHHHKH');
                $event->sheet->SetCellValue("B50", '     d) Account no.: 004-747-095693-838');
                $event->sheet->SetCellValue("B51", '2) Payment Term: within 10 working days from the date of Invoice');

                //TODO 公司印章和公司聯絡資訊的圖片(共兩張) 待補上
            }
        ];
    }

    private function getTotalVal(object $billing, array $keys): float
    {
        $billings = collect($billing)->only($keys);

        $sum = 0;
        foreach ($billings as $val) {
            $sum += (float)$val;
        }
        return $sum;
    }
}


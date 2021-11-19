<?php

namespace App\Exports;

use App\Models\FirstMileShipmentFees;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Excel;
use Throwable;

class FBAFirstMileShipmentFeesExport implements WithTitle, WithEvents
{
    use RegistersEventListeners;

    private $reportDate;
    private $clientCode;
    private $insertInvoiceID;

    public function __construct(
        string $reportDate,
        string $clientCode,
        int    $insertInvoiceID
    )
    {
        $this->reportDate = $reportDate;
        $this->clientCode = $clientCode;
        $this->insertInvoiceID = $insertInvoiceID;
    }

    public function title(): string
    {
        return 'FBA First Mile Shipment Fee';
    }

    public function failed(Throwable $exception): void
    {
        $invoice = Invoice::findOrFail($this->insertInvoiceID);
        $invoice->doc_status = "deleted";
        $invoice->save();

        \Log::channel('daily_queue_export')
            ->info('PaymentExport')
            ->info($exception);
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $invoice = Invoice::findOrFail($this->insertInvoiceID);

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

                $lists = FirstMileShipmentFees::select(
                    DB::raw("fulfillment_center as 'country'"),
                    DB::raw("fba_shipment as 'shipment_id'"),
                    DB::raw("COUNT(DISTINCT ids_sku) AS 'sku'"),
                    DB::raw("SUM(shipped) as 'shipped_qty'"),
                    DB::raw("ROUND(first_mile, 2) as 'unit_price'"),
                )
                    ->where('active', 1)
                    ->where('report_date', $this->reportDate)
                    ->where('client_code', $this->clientCode)
                    ->groupBy(['fulfillment_center', 'fba_shipment'])
                    ->get();

                if (count($lists) > 0) {
                    $totalAmount = 0;
                    foreach ($lists as $k => $item) {
                        $itemDesc = sprintf(
                            "Country:%s, Shipment ID:%s, SKU:%d, Shipped Qty:%d",
                            $item->country,
                            $item->shipment_id,
                            $item->sku,
                            $item->shipped_qty,
                        );

                        $colNum = 16 + $k * 3;
                        $descNum = $colNum + 1;
                        $event->sheet->SetCellValue("B{$colNum}", $k + 1);//16
                        $event->sheet->SetCellValue("C{$colNum}", 'FBA shipment Fee from Continental HK warehouse to Amazon FBA warehouse:');
                        $event->sheet->SetCellValue("C{$descNum}", $itemDesc);
                        $event->sheet->SetCellValue("D{$descNum}", "$ {$item->unit_price}");
                        $event->sheet->SetCellValue("E{$descNum}", "1");
                        $event->sheet->SetCellValue("F{$colNum}", "HKD  {$item->unit_price}");

                        $totalAmount += $item->unit_price;
                    }

                    $event->sheet->SetCellValue("B" . ($descNum + 4), 'Total');
                    $event->sheet->SetCellValue("F" . ($descNum + 4), "HKD  {$totalAmount}");
                }

                //footer
                $descNum = isset($descNum) ? ($descNum + 10) : 15;
                $event->sheet->SetCellValue("B" . ($descNum + 10), 'Payment Method:');
                $event->sheet->SetCellValue("B" . ($descNum + 11), 'By Transfer to the following HSBC account & send copy to sammi.chan@a4lution.com and billy.kwan@a4lution.com');
                $event->sheet->SetCellValue("B" . ($descNum + 12), '  a) Beneficiary Name: A4LUTION LIMITED');
                $event->sheet->SetCellValue("B" . ($descNum + 13), '  b) Beneficiary Bank: THE HONGKONG AND SHANGHAI BANKING CORPORATION LTD');
                $event->sheet->SetCellValue("B" . ($descNum + 14), '  c) Swift Code: HSBCHKHHHKH');
                $event->sheet->SetCellValue("B" . ($descNum + 15), '  d) Account No.: 004-747-095693-838');
            }
        ];
    }
}

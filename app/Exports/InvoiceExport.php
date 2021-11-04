<?php

namespace App\Exports;

use App\Models\BillingStatements;
use App\Models\FirstMileShipmentFees;
use App\Models\Invoices;
use App\Models\Users;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Excel;
use App\Exports\SalesExpenseExport;
use App\Exports\PaymentExport;
use App\Exports\OpexInvoiceExport;
use App\Exports\FBAFirstMileShipmentFeesExport;
use App\Exports\FBADateExport;
use App\Exports\ADSPromotionExport;
use App\Exports\ReturnAndRefundExport;
use App\Exports\MisCellaneousExport;
use App\Exports\StorageFeeExport;
use Throwable;

class InvoiceExport implements WithMultipleSheets, ShouldQueue, WithEvents
{
    use RegistersEventListeners;

    private $clientCode;
    private $reportDate;
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

    public function failed(Throwable $exception): void
    {
        $invoice = Invoices::findOrFail($this->insertInvoiceID);
        $invoice->doc_status = "failed_bye";
        $invoice->save();

        \Log::channel('daily_queue_export')
            ->info("[InvoiceExport]" . $exception);
    }

    public function sheets(): array
    {
        $formatYmDate = date("Ym", strtotime($this->reportDate));

        $sheets[0] = new SalesExpenseExport(
            $this->reportDate,
            $this->clientCode,
            $this->insertInvoiceID,
            $this->insertBillingID
        );
        $sheets[1] = new PaymentExport(
            $this->reportDate,
            $this->clientCode,
            $this->insertInvoiceID,
            $this->insertBillingID
        );
        $sheets[2] = new OpexInvoiceExport(
            $this->reportDate,
            $this->clientCode,
            $this->insertInvoiceID,
            $this->insertBillingID
        );
        $sheets[3] = new CreditNoteExport(
            $this->reportDate,
            $this->clientCode,
            $this->insertInvoiceID,
            $this->insertBillingID
        );
        $sheets[4] = new FBAFirstMileShipmentFeesExport(
            $this->reportDate,
            $this->clientCode,
            $this->insertInvoiceID
        );
        $sheets[5] = new FBADateExport($this->reportDate, $this->clientCode, $this->insertInvoiceID);
        $sheets[6] = new AllOrdersExport($formatYmDate, $this->clientCode, $this->insertInvoiceID);
        $sheets[7] = new ADSPromotionExport($this->reportDate, $this->clientCode, $this->insertInvoiceID);
        $sheets[8] = new ReturnAndRefundExport($formatYmDate, $this->clientCode, $this->insertInvoiceID);
        $sheets[9] = new MisCellaneousExport($this->reportDate, $this->clientCode, $this->insertInvoiceID);
        $sheets[10] = new StorageFeeExport($this->reportDate, $this->clientCode, $this->insertInvoiceID);
        $sheets[11] = new ExtraordinaryItemExport($this->reportDate, $this->clientCode, $this->insertInvoiceID);

        return $sheets;
    }

//    public function registerEvents(): array
//    {
//        return [
//            AfterSheet::class => function (AfterSheet $event) {
//                $invoice = Invoices::findOrFail($this->insertInvoiceID);
//                $invoice->doc_status = "active!";
//                $invoice->save();
//            }
//        ];
//    }
}

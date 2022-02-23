<?php

namespace App\Exports;

use App\Models\Invoice;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Throwable;

class InvoiceExport implements
    WithMultipleSheets,
    WithEvents
{
    use RegistersEventListeners;

    private string $clientCode;
    private string $reportDate;
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

    public function failed(Throwable $exception): void
    {
        $invoice = Invoice::findOrFail($this->insertInvoiceID);
        $invoice->doc_status = "failed_bye";
        $invoice->save();

        Log::channel('daily_queue_export')
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
}

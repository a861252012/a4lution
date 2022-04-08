<?php

namespace App\Exports;

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

    public function failed(Throwable $exception): void
    {
        \Log::channel('daily_queue_export')
            ->info("[InvoiceExport]" . $exception);
    }

    public function sheets(): array
    {
        $formatYmDate = date("Ym", strtotime($this->reportDate));

        $sheets[0] = new SalesExpenseExport(
            $this->reportDate,
            $this->clientCode,
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
            $this->insertInvoiceID,
            $this->user
        );
        $sheets[5] = new FBADataExport($this->reportDate, $this->clientCode);
        $sheets[6] = new AllOrdersExport($formatYmDate, $this->clientCode);
        $sheets[7] = new ADSPromotionExport($this->reportDate, $this->clientCode);
        $sheets[8] = new ReturnAndRefundExport($formatYmDate, $this->clientCode);
        $sheets[9] = new MiscellaneousExport($this->reportDate, $this->clientCode);
        $sheets[10] = new StorageFeeExport($this->reportDate, $this->clientCode);
        $sheets[11] = new ExtraordinaryItemExport($this->reportDate, $this->clientCode);

        return $sheets;
    }
}

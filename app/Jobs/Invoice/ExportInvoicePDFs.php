<?php

namespace App\Jobs\Invoice;

use App\Models\Invoices;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use App\Models\FirstMileShipmentFees;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportInvoicePDFs extends BaseInvoiceJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $invoice;

    public function __construct(
        Invoices $invoice
    )
    {
        $this->invoice = $invoice;
    }

    public function handle()
    {
        $saveDir = $this->getSaveDir($this->invoice->id);
        $invoice = $this->invoice->load('billingStatement');

        $pdf = \PDF::loadView('invoice.pdf.creditNote', compact('invoice'))
            ->save($saveDir . 'credit-note.pdf');

        $pdf = \PDF::loadView('invoice.pdf.opexInvoice', compact('invoice'))
            ->save($saveDir . 'opex-invoice.pdf');

        // TODO: create Repo
        $firstMileShipmentFees = FirstMileShipmentFees::query()
            ->select(
                DB::raw("fulfillment_center as 'country'"),
                DB::raw("fba_shipment as 'shipment_id'"),
                DB::raw("COUNT(DISTINCT ids_sku) AS 'sku'"),
                DB::raw("SUM(shipped) as 'shipped_qty'"),
                DB::raw("ROUND(first_mile, 2) as 'unit_price'"),
            )
            ->where('active', 1)
            ->where('report_date', $invoice->report_date)
            ->where('client_code', $invoice->client_code)
            ->groupBy(['fulfillment_center', 'fba_shipment'])
            ->get();

        $pdf = \PDF::loadView('invoice.pdf.fbaFirstMileShipmentFee', compact('invoice', 'firstMileShipmentFees'))
            ->save($saveDir . 'fba-first-mile-shipment-fee.pdf');
    }
}

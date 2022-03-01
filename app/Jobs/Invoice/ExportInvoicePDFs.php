<?php

namespace App\Jobs\Invoice;

use App\Models\Invoice;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use App\Models\FirstMileShipmentFee;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportInvoicePDFs extends BaseInvoiceJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $invoice;

    public function __construct(
        Invoice $invoice
    )
    {
        $this->invoice = $invoice;
    }

    public function handle()
    {
        $saveDir = $this->getSaveDir($this->invoice->id);
        $invoice = $this->invoice->load('billingStatement');

        // create credit-note
        $fileName = sprintf("%s %s Credit Note.pdf",
            $invoice->client_code,
            $invoice->credit_note_no,
        );
        $pdf = \PDF::loadView('invoice.pdf.creditNote', compact('invoice'))
            ->save($saveDir . $fileName);


        // create opex-invoice
        $fileName = sprintf("%s INV-%s%s_1 OPEX Invoice.pdf",
            $invoice->client_code,
            $invoice->issue_date->format('ymd'),
            str_replace(' ', '_', $invoice->supplier_name)
        );
        $fileName = sprintf("%s %s OPEX Invoice.pdf",
            $invoice->client_code,
            $invoice->opex_invoice_no,
        );

        $pdf = \PDF::loadView('invoice.pdf.opexInvoice', compact('invoice'))
            ->save($saveDir . $fileName);

        // create fba-first-mile-shipment-fee.pdf
        $fileName = sprintf("%s %s&ReturnHelperInvoice.pdf",
            $invoice->client_code,
            $invoice->fba_shipment_invoice_no,
        );
        $firstMileShipmentFees = FirstMileShipmentFee::query()
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
            ->save($saveDir . $fileName);
    }
}

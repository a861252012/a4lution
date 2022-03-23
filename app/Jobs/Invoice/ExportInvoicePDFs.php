<?php

namespace App\Jobs\Invoice;

use App\Models\Invoice;
use App\Models\ReturnHelperCharge;
use App\Repositories\ContinStorageFeeRepository;
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
    use Batchable,
        Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    private Invoice $invoice;

    public function __construct(
        Invoice $invoice
    ) {
        $this->invoice = $invoice;
    }

    public function handle()
    {
        $saveDir = $this->getSaveDir($this->invoice->id);
        $invoice = $this->invoice->load('billingStatement');

        // create credit-note
        $fileName = sprintf(
            "%s %s Credit Note.pdf",
            $invoice->client_code,
            $invoice->credit_note_no,
        );
        \PDF::loadView('invoice.pdf.creditNote', compact('invoice'))
            ->save($saveDir . $fileName);


        // create opex-invoice
        $fileName = sprintf(
            "%s INV-%s%s_1 OPEX Invoice.pdf",
            $invoice->client_code,
            $invoice->issue_date->format('ymd'),
            str_replace(' ', '_', $invoice->supplier_name)
        );
        $fileName = sprintf(
            "%s %s OPEX Invoice.pdf",
            $invoice->client_code,
            $invoice->opex_invoice_no,
        );

        \PDF::loadView('invoice.pdf.opexInvoice', compact('invoice'))
            ->save($saveDir . $fileName);

        // create fba-first-mile-shipment-fee.pdf
        $fileName = sprintf(
            "%s %s&ReturnHelperInvoice.pdf",
            $invoice->client_code,
            $invoice->fba_shipment_invoice_no,
        );

        //1.)   Contin Storage Fee :  (一筆加總的數值)
        $continStorageFee = app(ContinStorageFeeRepository::class)->getContinStorageFee(
            $invoice->report_date,
            $invoice->client_code
        );

        // 2.)  Contin 寄FBA的頭程費用 : 依據shipment
        $firstMileShipmentFees = FirstMileShipmentFee::selectRaw("
                    fulfillment_center as 'country',
                    fba_shipment as 'shipment_id',
                    COUNT(DISTINCT ids_sku) AS 'sku',
                    SUM(shipped) as 'shipped_qty',
                    ROUND(total, 2) as 'unit_price'")
            ->where('active', 1)
            ->where('report_date', $invoice->report_date)
            ->where('client_code', $invoice->client_code)
            ->groupBy(['fulfillment_center', 'fba_shipment'])
            ->get();

        // 3.)  Return Helper : 逐筆列出
        $returnHelperList = ReturnHelperCharge::selectRaw("
                return_helper_charges.notes,
                (return_helper_charges.amount * exchange_rates.exchange_rate) AS 'amount_hkd'")
            ->leftJoin('exchange_rates', function ($join) {
                $join->on('return_helper_charges.report_date', '=', 'exchange_rates.quoted_date')
                    ->on('return_helper_charges.currency_code', '=', 'exchange_rates.base_currency')
                    ->where('exchange_rates.active', 1);
            })
            ->where('return_helper_charges.supplier', $invoice->client_code)
            ->where('return_helper_charges.report_date', $invoice->report_date)
            ->where('return_helper_charges.active', 1)
            ->get();

        \PDF::loadView('invoice.pdf.fbaFirstMileShipmentFee', compact(
            'invoice',
            'continStorageFee',
            'firstMileShipmentFees',
            'returnHelperList'
        ))->save($saveDir . $fileName);
    }
}

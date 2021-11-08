<?php

namespace App\Jobs\Invoice;

use App\Models\Invoices;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Exports\InvoiceExport;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportInvoiceExcel extends BaseInvoiceJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $invoice;
    private $saveDir;

    public function __construct(
        Invoices $invoice
    )
    {
        $this->invoice = $invoice;
    }

    public function handle()
    {   
        \Excel::store(
            new InvoiceExport(
                $this->invoice->report_date->format('Y-m-d'),
                $this->invoice->client_code,
                $this->invoice->id,
                $this->invoice->billing_statement_id,
            ),
            sprintf(
                "%s/%s.xlsx", 
                $this->invoice->id ,
                $this->invoice->doc_file_name
            ),
            'invoice-export',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }
}

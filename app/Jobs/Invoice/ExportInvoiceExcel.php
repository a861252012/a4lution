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
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ExportInvoiceExcel implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $invoice;
    private $saveDir;

    public function __construct(
        Invoices $invoice,
        string $saveDir
    )
    {
        $this->invoice = $invoice;
        $this->saveDir = $saveDir;
    }

    public function handle()
    {
        \Config::set('filesystems.disks.invoice-export.root', $this->saveDir);

        \Excel::store(
            new InvoiceExport(
                $this->invoice->client_code,
                $this->invoice->report_date,
                $this->invoice->id,
                $this->invoice->billing_statement_id,
            ),
            $this->invoice->doc_file_name . ".xlsx",
            'invoice-export',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }
}

<?php

namespace App\Jobs\Invoice;

use App\Exports\InvoiceExport;
use App\Models\Invoice;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExportInvoiceExcel extends BaseInvoiceJob implements ShouldQueue
{
    use Batchable,
        Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    private $invoice;
    private $saveDir;
    private $user;

    public function __construct(
        Invoice $invoice,
        $user
    ) {
        $this->invoice = $invoice;
        $this->user = $user;
    }

    public function handle()
    {
        \Excel::store(
            new InvoiceExport(
                $this->invoice->report_date->format('Y-m-d'),
                $this->invoice->client_code,
                $this->invoice->id,
                $this->invoice->billing_statement_id,
                $this->user
            ),
            // [資料夾(id)/檔案名稱]
            sprintf(
                "%s/%s_AVO format_sales report %s.xlsx",
                $this->invoice->id,
                $this->invoice->supplier_name,
                $this->invoice->report_date->format('M Y'),
            ),
            'invoice-export',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }
}

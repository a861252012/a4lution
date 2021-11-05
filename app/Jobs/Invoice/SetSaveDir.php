<?php

namespace App\Jobs\Invoice;

use App\Models\Invoices;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Exports\InvoiceExport;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SetSaveDir implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $invoiceID;

    public function __construct(
        string $invoiceID
    )
    {
        $this->invoiceID = $invoiceID;
    }

    public function handle()
    {
        // 建立儲存目錄
        $saveDir = storage_path("invoice-export/{$this->invoiceID}/");
        (new Filesystem)->ensureDirectoryExists($saveDir);

        // 更新 disk 默認儲存位置，提供後續 job 使用
        \Config::set('filesystems.disks.invoice-export.root', $saveDir);
    }
}

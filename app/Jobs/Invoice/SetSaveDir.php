<?php

namespace App\Jobs\Invoice;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SetSaveDir extends BaseInvoiceJob implements ShouldQueue
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
        (new Filesystem)->ensureDirectoryExists(
            $this->getSaveDir($this->invoiceID)
        );
    }
}

<?php

namespace App\Jobs\Invoice;

use Zip;
use ZipArchive;
use App\Models\Invoice;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateZipToS3 extends BaseInvoiceJob implements ShouldQueue
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
        $fileName = $this->invoice->doc_storage_token . '.zip';

        // 壓縮檔案
        $zip = new ZipArchive;
        if ($zip->open($saveDir . $fileName, ZipArchive::CREATE) === true) {
            foreach (\File::files($saveDir) as $name) {
                $zip->addFile(
                    $name,
                    basename($name)
                );
            }
            $zip->close();
        }

        // 上傳至 aws s3
        $content = Storage::disk('invoice-export')->get("{$this->invoice->id}/{$fileName}");
        Storage::disk('s3')->put("invoices/{$fileName}", $content);
    }
}

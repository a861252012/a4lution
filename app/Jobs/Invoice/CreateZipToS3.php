<?php

namespace App\Jobs\Invoice;

use Zip;
use ZipArchive;
use App\Models\Invoices;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class CreateZipToS3 implements ShouldQueue
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
        $fileName = $this->invoice->doc_storage_token . '.zip';

        $zip = new ZipArchive;
        if ($zip->open($this->saveDir . $fileName, ZipArchive::CREATE) === TRUE) {
            foreach (\File::files($this->saveDir) as $name) {
                $zip->addFile(
                    $name,
                    basename($name)
                );
            }
            $zip->close();
        }

        // 上傳檔案至 s3
        $content = \Storage::disk('invoice-export')->get($this->invoice->id . '/' . $fileName);
        \Storage::disk('s3')->put("invoice-export/{$fileName}", $content);
    }
}

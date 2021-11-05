<?php

namespace App\Jobs\Invoice;

use Zip;
use ZipArchive;
use App\Models\Invoices;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Symfony\Component\HttpFoundation\Response;

class CreateZipToS3 implements ShouldQueue
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
        $saveDir = Storage::disk('invoice-export')->getAdapter()->getPathPrefix();

        $fileName = $this->invoice->doc_storage_token . '.zip';

        $zip = new ZipArchive;
        if ($zip->open($saveDir . $fileName, ZipArchive::CREATE) === TRUE) {
            foreach (\File::files($saveDir) as $name) {
                $zip->addFile(
                    $name,
                    basename($name)
                );
            }
            $zip->close();
        }

        $content = Storage::disk('invoice-export')->get($fileName);
        Storage::disk('s3')->put("invoices/{$fileName}", $content);
    }
}

<?php

namespace App\Jobs\Invoice;

use Illuminate\Support\Facades\Storage;

abstract class BaseInvoiceJob
{
    public function getSaveDir(int $id): string
    {
        return sprintf(
            "/%s/%s/",
            Storage::disk('invoice-export')->getAdapter()->getPathPrefix(),
            $id
        );
    }
}

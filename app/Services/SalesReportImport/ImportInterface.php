<?php

namespace App\Services\SalesReportImport;

use Carbon\Carbon;
use Illuminate\Support\LazyCollection;

interface ImportInterface
{
    public function import(LazyCollection $collection, int $batchId, Carbon $reportDate, int $userId);
}

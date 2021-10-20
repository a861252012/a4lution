<?php

namespace App\Repositories;

use App\Models\Invoices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoicesRepository
{
    protected $invoices;

    public function __construct(Invoices $invoices)
    {
        $this->invoices = $invoices;
    }

    public function insertData(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->invoices->insert($data);
        });
    }

    public function updateDataByDate(string $date, array $data): int
    {
        try {
            return $this->invoices
                ->active()
                ->where('report_date', $date)
                ->update($data);
        } catch (\Exception $e) {
            Log::error("invoices update error: {$e}");
            return -1;
        }
    }
}

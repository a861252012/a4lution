<?php

namespace App\Repositories;

use App\Models\BillingStatements;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingStatementRepository
{
    protected $billingStatements;

    public function __construct(BillingStatements $billingStatements)
    {
        $this->billingStatements = $billingStatements;
    }

    public function create(array $data)
    {
        return $this->billingStatements->create($data);
    }

    public function updateByDate(string $date, array $data): int
    {
        try {
            return $this->billingStatements
                ->active()
                ->where('report_date', $date)
                ->update($data);
        } catch (\Exception $e) {
            Log::error("billingStatements update error: {$e}");
            return -1;
        }
    }
}

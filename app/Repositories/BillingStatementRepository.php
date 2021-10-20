<?php

namespace App\Repositories;

use App\Models\BillingStatements;
use Illuminate\Support\Facades\DB;

class BillingStatementRepository
{
    protected $billingStatements;

    public function __construct(BillingStatements $billingStatements)
    {
        $this->billingStatements = $billingStatements;
    }

    public function insertData(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->billingStatements->insert($data);
        });
    }

    public function updateDataByDate(string $date, array $data)
    {
        return DB::transaction(function () use ($date, $data) {
            return $this->billingStatements
                ->active()
                ->where('report_date', $date)
                ->update($data);
        });
    }
}

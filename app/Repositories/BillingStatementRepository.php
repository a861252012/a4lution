<?php

namespace App\Repositories;

use App\Models\BillingStatements;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BillingStatementRepository
{
    protected $billingStatements;

    public function __construct(BillingStatements $billingStatements)
    {
        $this->billingStatements = $billingStatements;
    }

    public function getTableColumns(): array
    {
        return Schema::getColumnListing($this->billingStatements->getTable());
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

    //檢核若該月份已結算員工commission則提示訊息(需Revoke Approval)
    public function checkIfSettled(string $date): int
    {
        return $this->billingStatements
            ->active()
            ->where('report_date', $date)
            ->whereNotNull('cutoff_time')
            ->count();
    }

    //檢核若該月份已結算員工commission則提示訊息(需Revoke Approval)
    public function checkIfDuplicated(string $date, string $clientCode): int
    {
        return $this->billingStatements
            ->active()
            ->where('report_date', $date)
            ->where("client_code", $clientCode)
            ->count();
    }
}

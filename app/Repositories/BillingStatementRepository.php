<?php

namespace App\Repositories;

use App\Models\BillingStatement;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BillingStatementRepository
{
    protected $billingStatement;

    public function __construct(BillingStatement $billingStatement)
    {
        $this->billingStatement = $billingStatement;
    }

    public function getTableColumns(): array
    {
        return Schema::getColumnListing($this->billingStatement->getTable());
    }

    public function create(array $data)
    {
        return $this->billingStatement->create($data);
    }

    public function updateByDate(string $date, array $data): int
    {
        try {
            return $this->billingStatement
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
        return $this->billingStatement
            ->active()
            ->where('report_date', $date)
            ->whereNotNull('cutoff_time')
            ->count();
    }

    //檢核若該月份已結算員工commission則提示訊息(需Revoke Approval)
    public function checkIfDuplicated(string $date, string $clientCode): int
    {
        return $this->billingStatement
            ->active()
            ->where('report_date', $date)
            ->where("client_code", $clientCode)
            ->count();
    }
}

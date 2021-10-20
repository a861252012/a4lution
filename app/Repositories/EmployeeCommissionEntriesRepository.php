<?php

namespace App\Repositories;

use App\Models\EmployeeCommissionEntries;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeCommissionEntriesRepository
{
    protected $employeeCommissionEntries;

    public function __construct(EmployeeCommissionEntries $employeeCommissionEntries)
    {
        $this->employeeCommissionEntries = $employeeCommissionEntries;
    }

    public function insertData(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->employeeCommissionEntries->insert($data);
        });
    }

    public function updateDataByDate(string $date, array $data): int
    {
        try {
            return $this->employeeCommissionEntries
                ->active()
                ->where('report_date', $date)
                ->update($data);
        } catch (\Exception $e) {
            Log::error("billingStatements update error: {$e}");
            return -1;
        }
    }
}

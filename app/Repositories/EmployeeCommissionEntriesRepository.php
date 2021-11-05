<?php

namespace App\Repositories;

use App\Models\EmployeeCommissionEntries;
use Illuminate\Support\Facades\Log;

class EmployeeCommissionEntriesRepository
{
    protected $employeeCommissionEntries;

    public function __construct(EmployeeCommissionEntries $employeeCommissionEntries)
    {
        $this->employeeCommissionEntries = $employeeCommissionEntries;
    }

    public function create(array $data)
    {
        return $this->employeeCommissionEntries->create($data);
    }

    public function updateByDate(string $date, array $data): int
    {
        try {
            return $this->employeeCommissionEntries
                ->active()
                ->where('report_date', $date)
                ->update($data);
        } catch (\Exception $e) {
            Log::error("EmployeeCommissionEntries update error: {$e}");
            return -1;
        }
    }
}

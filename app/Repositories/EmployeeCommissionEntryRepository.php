<?php

namespace App\Repositories;

use App\Models\EmployeeCommissionEntry;
use Illuminate\Support\Facades\Log;

class EmployeeCommissionEntryRepository
{
    protected $employeeCommissionEntry;

    public function __construct(EmployeeCommissionEntry $employeeCommissionEntry)
    {
        $this->employeeCommissionEntry = $employeeCommissionEntry;
    }

    public function create(array $data)
    {
        return $this->employeeCommissionEntry->create($data);
    }

    public function updateByDate(string $date, array $data): int
    {
        try {
            return $this->employeeCommissionEntry
                ->active()
                ->where('report_date', $date)
                ->update($data);
        } catch (\Exception $e) {
            Log::error("EmployeeCommissionEntry update error: {$e}");
            return -1;
        }
    }

    public function updateByEmployeeID(array $id, array $data): int
    {
        try {
            return $this->employeeCommissionEntry
                ->active()
                ->whereIn('employee_commissions_id', $id)
                ->update($data);
        } catch (\Exception $e) {
            Log::error("EmployeeCommissionEntry update error: {$e}");
            return -1;
        }
    }
}

<?php

namespace App\Repositories;

use App\Models\EmployeeCommission;
use Illuminate\Support\Facades\Log;

class EmployeeCommissionRepository
{
    protected $employeeCommission;

    public function __construct(EmployeeCommission $employeeCommission)
    {
        $this->employeeCommission = $employeeCommission;
    }

    public function create(array $data)
    {
        return $this->employeeCommission->create($data);
    }

    public function updateByDate(string $date, array $data): int
    {
        try {
            return $this->employeeCommission
                ->active()
                ->where('report_date', $date)
                ->update($data);
        } catch (\Exception $e) {
            Log::error("EmployeeCommission update error: {$e}");
            return -1;
        }
    }
}

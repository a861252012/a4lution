<?php

namespace App\Repositories;

use App\Models\EmployeeCommission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeCommissionRepository
{
    protected $employeeCommission;

    public function __construct(EmployeeCommission $employeeCommission)
    {
        $this->employeeCommission = $employeeCommission;
    }

    public function insertData(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->employeeCommission->insert($data);
        });
    }

    public function updateDataByDate(string $date, array $data): int
    {
        try {
            return $this->employeeCommission
                ->active()
                ->where('report_date', $date)
                ->update($data);
        } catch (\Exception $e) {
            Log::error("billingStatements update error: {$e}");
            return -1;
        }
    }
}

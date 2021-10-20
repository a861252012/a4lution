<?php

namespace App\Services;

use App\Repositories\BillingStatementRepository;
use App\Repositories\InvoicesRepository;
use App\Repositories\EmployeeCommissionRepository;
use App\Repositories\EmployeeCommissionEntriesRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminService
{
    private $billingStatementRepository;
    private $invoicesRepository;
    private $employeeCommissionRepository;
    private $employeeCommissionEntriesRepository;

    public function __construct(
        BillingStatementRepository          $billingStatementRepository,
        InvoicesRepository                  $invoicesRepository,
        EmployeeCommissionRepository        $employeeCommissionRepository,
        EmployeeCommissionEntriesRepository $employeeCommissionEntriesRepository
    )
    {
        $this->billingStatementRepository = $billingStatementRepository;
        $this->invoicesRepository = $invoicesRepository;
        $this->employeeCommissionRepository = $employeeCommissionRepository;
        $this->employeeCommissionEntriesRepository = $employeeCommissionEntriesRepository;
    }

    public function revokeApprove(string $date)
    {
        $softDeleteParams = [
            'active' => 0,
            'deleted_at' => date('Y-m-d h:i:s'),
            'deleted_by' => Auth::id(),
        ];

        DB::beginTransaction();
        try {

            $updateBilling = $this->billingStatementRepository->updateByDate(
                $date,
                $softDeleteParams
            );
            if ($updateBilling === -1) {
                DB::rollback();
                return 500;
            }

            $invoiceData = [
                'active' => 0,
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_by' => Auth::id(),
                'doc_status' => 'deleted',
            ];

            $updateInvoice = $this->invoicesRepository->updateByDate(
                $date,
                $invoiceData
            );

            if ($updateInvoice === -1) {
                DB::rollback();
                return 500;
            }

            $updateEmployee = $this->employeeCommissionRepository->updateByDate(
                $date,
                $softDeleteParams
            );

            if ($updateEmployee === -1) {
                DB::rollback();
                return 500;
            }

            $updateEmployeeEntries = $this->employeeCommissionEntriesRepository->updateDataByDate(
                $date,
                $softDeleteParams
            );

            if ($updateEmployeeEntries === -1) {
                DB::rollback();
                return 500;
            }
            DB::commit();

            return 200;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("revokeApprove update error: {$e}");
        }
    }
}

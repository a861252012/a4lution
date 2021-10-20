<?php

namespace App\Services;

use App\Repositories\BillingStatementRepository;
use App\Repositories\InvoicesRepository;
use App\Repositories\EmployeeCommissionRepository;
use App\Repositories\EmployeeCommissionEntriesRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminService
{
    private $billingStatementRepository;
    private $invoicesRepository;
    private $employeeCommissionRepository;
    private $employeeCommissionEntriesRepositor;

    public function __construct(
        BillingStatementRepository          $billingStatementRepository,
        InvoicesRepository                  $invoicesRepository,
        EmployeeCommissionRepository        $employeeCommissionRepository,
        EmployeeCommissionEntriesRepository $employeeCommissionEntriesRepositor
    )
    {
        $this->billingStatementRepository = $billingStatementRepository;
        $this->invoicesRepository = $invoicesRepository;
        $this->employeeCommissionRepository = $employeeCommissionRepository;
        $this->employeeCommissionEntriesRepositor = $employeeCommissionEntriesRepositor;
    }

    public function revokeApprove(string $date)
    {
        $softDeleteParams = [
            'active' => 0,
            'deleted_at' => date('Y-m-d h:i:s'),
            'deleted_by' => Auth::id(),
        ];

        $updateBilling = $this->billingStatementRepository->updateDataByDate(
            $date,
            $softDeleteParams
        );
        if ($updateBilling === -1) {
            return 400;
        }

        $invoiceData = [
            'active' => 0,
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_by' => Auth::id(),
            'doc_status' => 'deleted',
        ];

        $updateInvoice = $this->invoicesRepository->updateDataByDate(
            $date,
            $invoiceData
        );

        if ($updateInvoice === -1) {
            return 400;
        }

        $updateEmployee = $this->employeeCommissionRepository->updateDataByDate(
            $date,
            $softDeleteParams
        );

        if ($updateEmployee === -1) {
            return 400;
        }

        $updateEmployeeEntries = $this->employeeCommissionEntriesRepositor->updateDataByDate(
            $date,
            $softDeleteParams
        );

        if ($updateEmployeeEntries === -1) {
            return 400;
        }

        return 200;
    }

}

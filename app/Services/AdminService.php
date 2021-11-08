<?php

namespace App\Services;

use App\Repositories\BillingStatementRepository;
use App\Repositories\InvoicesRepository;
use App\Repositories\EmployeeCommissionRepository;
use App\Repositories\EmployeeCommissionEntriesRepository;
use Carbon\Carbon;
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
    ) {
        $this->billingStatementRepository = $billingStatementRepository;
        $this->invoicesRepository = $invoicesRepository;
        $this->employeeCommissionRepository = $employeeCommissionRepository;
        $this->employeeCommissionEntriesRepository = $employeeCommissionEntriesRepository;
    }

    public function revokeApprove(string $date)
    {
        $date = Carbon::parse($date)->format('Y-m-01');

        if (!Auth::user()->isManager()) {
            return response()->json(
                [
                    'status' => 401,
                    'msg' => 'Unauthorized'
                ]
            );
        }

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

            abort_if($updateBilling === -1, 500);

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

            abort_if($updateInvoice === -1, 500);

            $idArray = $this->employeeCommissionRepository->getIDByDate(
                $date
            );

            $updateEmployee = $this->employeeCommissionRepository->updateByDate(
                $date,
                $softDeleteParams
            );

            abort_if($updateEmployee === -1, 500);

            if ($idArray) {
                $updateEmployeeEntries = $this->employeeCommissionEntriesRepository->updateByEmployeeID(
                    $idArray,
                    $softDeleteParams
                );

                abort_if($updateEmployeeEntries === -1, 500);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("revokeApprove update error: {$e}");

            return response()->json(
                [
                    'status' => 500,
                    'msg' => 'Error'
                ],
                500
            );
        }

        return response()->json(
            [
                'status' => 200,
                'msg' => 'success'
            ]
        );
    }
}

<?php

namespace App\Services;

use App\Repositories\BillingStatementRepository;
use App\Repositories\InvoiceRepository;
use Symfony\Component\HttpFoundation\Response;

class InvoiceService
{
    private $billingStatementRepository;
    private $invoiceRepository;

    public function __construct(
        BillingStatementRepository $billingStatementRepository,
        InvoiceRepository         $invoiceRepository
    ) {
        $this->billingStatementRepository = $billingStatementRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    public function reportValidation(string $date, string $clientCode): array
    {
        $reportDateTime = date('Y-m-01', strtotime($date));

        $formattedReportDate = date('F-Y', strtotime($date));

        //檢核若該月份已結算員工commission則提示訊息(需Revoke Approval)
        if ($this->billingStatementRepository->checkIfSettled($reportDateTime)) {
            return [
                'status' => Response::HTTP_FORBIDDEN,
                'msg' => $formattedReportDate . ' employee commission was generated. 
                    To recalculate employee commission, go to the "Approval Admin" and click on "Revoke Approval"',
            ];
        }

        //檢核若已出invoice則提示訊息(需先刪除相關聯的invoices)
        if ($this->invoiceRepository->checkIfDuplicated($reportDateTime, $clientCode)) {
            return [
                'status' => Response::HTTP_FORBIDDEN,
                'msg' => 'The record are referenced by other invoice(s), please delete all the references first.',
            ];
        }

        //檢核是否重複
        if ($this->billingStatementRepository->checkIfDuplicated($reportDateTime, $clientCode)) {
            return [
                'status' => Response::HTTP_ACCEPTED,
                'msg' => 'Duplicate entry with the same client code and report date',
            ];
        }

        return [
            'status' => Response::HTTP_OK,
            'msg' => 'success',
        ];
    }
}
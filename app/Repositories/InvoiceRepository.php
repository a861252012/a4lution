<?php

namespace App\Repositories;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Invoice);
    }

    public function create(array $data): ?Invoice
    {
        return $this->model->create($data);
    }

    public function updateByDate(string $date, array $data): int
    {
        try {
            return $this->model
                ->active()
                ->where('report_date', $date)
                ->update($data);
        } catch (\Exception $e) {
            Log::error("invoices update error: {$e}");
            return -1;
        }
    }

    //檢核若已出invoice則提示訊息(需先刪除相關聯的invoices)
    public function checkIfDuplicated(string $date, string $clientCode): int
    {
        return $this->model
            ->active()
            ->where('report_date', $date)
            ->where("client_code", $clientCode)
            ->count();
    }

    public function getListViewData(
        string $clientCode = null,
        string $status = null,
        string $reportDate = null
    ) {
        return $this->model
            ->select(
                DB::raw("date_format(report_date,'%M-%Y') AS report_date"),
                'id',
                'billing_statement_id',
                'client_code',
                'opex_invoice_no',
                'doc_file_name',
                'doc_status',
                'doc_storage_token',
                'created_at'
            )
            ->active()
            ->when($clientCode, fn ($q) => $q->where('client_code', $clientCode))
            ->when($status, fn ($q) => $q->where('doc_status', $status))
            ->when(
                $reportDate,
                fn ($q) => $q->where(
                    'report_date',
                    Carbon::parse($reportDate)->startOfMonth()->toDateString()
                )
            )
            ->orderByDesc('id')
            ->paginate(100);
    }
}

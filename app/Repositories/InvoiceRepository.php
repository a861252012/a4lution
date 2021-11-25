<?php

namespace App\Repositories;

use App\Models\Invoice;
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
}

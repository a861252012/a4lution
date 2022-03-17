<?php

namespace App\Repositories;

use App\Models\BillingStatement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BillingStatementRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new BillingStatement);
    }

    public function getTableColumns(): array
    {
        return Schema::getColumnListing($this->model->getTable());
    }

    public function updateByDate(string $date, array $data): int
    {
        try {
            return $this->model
                ->active()
                ->where('report_date', $date)
                ->update($data);
        } catch (\Exception $e) {
            Log::error("billingStatements update error: {$e}");
            return -1;
        }
    }

    //檢核若該月份已結算員工commission則提示訊息(需Revoke Approval)
    public function checkIfSettled(string $date): int
    {
        return $this->model
            ->active()
            ->where('report_date', $date)
            ->whereNotNull('cutoff_time')
            ->count();
    }

    //檢核若該月份已結算員工commission則提示訊息(需Revoke Approval)
    public function checkIfDuplicated(string $date, string $clientCode): int
    {
        return $this->model
            ->active()
            ->where('report_date', $date)
            ->where("client_code", $clientCode)
            ->count();
    }

    public function getIssueViewData(
        string $clientCode = null,
        string $reportDate = null
    ) {
        return $this->model
            ->select(
                'id',
                'client_code',
                'avolution_commission',
                'commission_type',
                'total_sales_orders',
                'total_sales_amount',
                'created_at',
                DB::raw("date_format(report_date,'%b-%Y') as 'report_date'")
            )->active()
            ->when($clientCode, fn ($q) => $q->where('client_code', $clientCode))
            ->when(
                $reportDate,
                fn ($q) => $q->where(
                    'report_date',
                    Carbon::parse($reportDate)->startOfMonth()->toDateString()
                )
            )
            ->paginate(100);
    }
}

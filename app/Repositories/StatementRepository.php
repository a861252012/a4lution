<?php

namespace App\Repositories;

use App\Models\Statement;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StatementRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Statement);
    }

    public function getMonthlyFeeTransactionView(
        string $billingMonth = null,
        string $clientCode = null
    ): LengthAwarePaginator {
        return $this->model->join('users', 'users.id', '=', 'statements.created_by')
            ->select(
                'statements.id',
                'statements.client_code',
                'statements.currency',
                'statements.billing_month',
                'statements.deposit_date as paid_date',
                'statements.amount as paid_amount',
                'statements.created_at',
                'statements.created_by',
                'users.user_name'
            )
            ->when(
                $billingMonth,
                fn ($q) => $q->where(
                    'statements.billing_month',
                    Carbon::parse($billingMonth)->format('Ym')
                )
            )
            ->when(
                $clientCode,
                fn ($q) => $q->where(
                    'statements.client_code',
                    $clientCode
                )
            )
            ->where('statements.active', 1)
            ->orderByDesc('statements.billing_month')
            ->orderBy('statements.client_code')
            ->paginate(50);
    }

    public function isDuplicated(
        string $clientCode,
        string $billingMonth
    ): bool {
        return $this->model->where('client_code', $clientCode)
            ->where('billing_month', $billingMonth)
            ->exists();
    }

    public function softDeleteDuplicated(
        string $clientCode,
        string $billingMonth
    ): bool {
        return $this->model->where('client_code', $clientCode)
            ->where('billing_month', $billingMonth)
            ->update(['active' => 0]);
    }
}

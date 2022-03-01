<?php

namespace App\Repositories;

use App\Models\RmaRefundList;
use Illuminate\Support\Facades\DB;

class RmaRefundListRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new RmaRefundList);
    }

    public function getAccountRefund(
        string $reportDate,
        string $clientCode,
        bool   $isAvolution
    ): float {
        return (float)$this->model
            ->selectRaw("SUM(ABS(rma_refund_list.amount_refund) * exchange_rates.exchange_rate) AS 'refund_amount_hkd'")
            ->leftJoin('exchange_rates', function ($join) {
                $join->on('rma_refund_list.currency', '=', 'exchange_rates.base_currency')
                    ->where('exchange_rates.active', 1)
                    ->where(
                        DB::raw("DATE_FORMAT(rma_refund_list.create_date, '%Y%M')"),
                        '=',
                        DB::raw("DATE_FORMAT(exchange_rates.quoted_date, '%Y%M')")
                    );
            })
            ->where('rma_refund_list.pc_name', $clientCode)
            ->whereRaw("DATE_FORMAT(rma_refund_list.create_date, '%Y%m') = ?", date("Ym", strtotime($reportDate)))
            ->where('rma_refund_list.shipping_method', '!=', 'AMAZONFBA')
            ->when($isAvolution, function ($q) {
                return $q->whereIn('rma_refund_list.user_account_name', function ($query) {
                    $query->from('seller_accounts')
                        ->selectRaw('DISTINCT asinking_account_name')
                        ->where('is_a4_account', 1)
                        ->where('active', 1)
                        ->get();
                });
            }, function ($q) {
                return $q->whereNotIn('rma_refund_list.user_account_name', function ($query) {
                    $query->from('seller_accounts')
                        ->selectRaw('DISTINCT asinking_account_name')
                        ->where('is_a4_account', 1)
                        ->where('active', 1)
                        ->get();
                });
            })
            ->value('refund_amount_hkd');
    }
}

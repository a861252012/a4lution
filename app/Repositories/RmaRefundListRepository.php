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
        array $sellerAccount,
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
            ->where('rma_refund_list.product_sku', 'like', "{$clientCode}-%")
            ->whereRaw("DATE_FORMAT(rma_refund_list.create_date, '%Y%m') = ?", date("Ym", strtotime($reportDate)))
            ->whereRaw("length(rma_refund_list.warehouse_ship_date) > ?", 0)
            ->where('rma_refund_list.shipping_method', '!=', 'AMAZONFBA')
            ->when($isAvolution, function ($q) use ($sellerAccount) {
                return $q->whereIn('rma_refund_list.user_account_name', $sellerAccount);
            }, function ($q) use ($sellerAccount) {
                return $q->whereNotIn('rma_refund_list.user_account_name', $sellerAccount);
            })
            ->value('refund_amount_hkd');
    }
}

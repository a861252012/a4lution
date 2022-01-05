<?php

namespace App\Console\Commands;

use App\Jobs\Order\SyncOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class OrderDataSync extends Command
{
    private const GET_ORDER = 'getOrders';
    private const GET_PRODUCT_BY_SKU = 'getProductBySku';
    private const GET_ORDER_DETAIL = 'getOrderCostDetailSku';
    private const AMZ_REPORT = 'amazonReportList';
    private const LOG_CHANNEL = 'daily_order_sync';
    private const PAGE_SIZE = 500;

    protected $signature = 'order_data_sync {startDate? : Y-m-d} {endDate? : Y-m-d}';
    protected $description = 'order_data_sync';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $startDate = $this->argument('startDate') ? Carbon::parse($this->argument('startDate')) :
            now()->copy()->subMonth()->startOfMonth();

        $endDate = $this->argument('endDate') ? Carbon::parse($this->argument('endDate')) :
            now()->copy()->subMonth()->endOfMonth();
        $diffDay = $endDate->diffInDays($startDate);

        if ($diffDay < 0) {
            $this->error('wrong date range');
            return false;
        }

        for ($i = 0; $i <= $diffDay; $i++) {
            $startDateTime = $startDate->copy()->addDays($i)->startOfDay()->toDateTimeString();
            $endDateTime = $startDate->copy()->addDays($i)->endOfDay()->toDateTimeString();
            $correlationID = (int)Carbon::parse($startDateTime)->format('Ymd');

            SyncOrder::dispatch(
                $startDateTime,
                $endDateTime,
                $correlationID
            )->allOnQueue('order_sync')->allOnConnection('redis');
        }
    }
}

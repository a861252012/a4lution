<?php

namespace App\Console\Commands;

use App\Models\RmaRefundList;
use App\Repositories\RmaRefundListRepository;
use App\Support\ERPRequester;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EbRefundDataSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eb_refund_data_sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'eb_refund_data_sync';

    private const EB_SERVICE_NAME = 'rmaRefundList';
    private const LOG_CHANNEL = 'daily_refund_sync';
    private RmaRefundListRepository $rmaRefundListRepository;
    private ERPRequester $erpRequest;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        RmaRefundListRepository $rmaRefundListRepository,
        ERPRequester            $ERPRequest
    )
    {
        parent::__construct();
        $this->rmaRefundListRepository = $rmaRefundListRepository;
        $this->erpRequest = $ERPRequest;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //預設排程時間為UTC+8早上五點整,故以UTC時間為準 取前一天的退款資料
        $startDateTime = gmdate('Y-m-d 00:00:00');
        $endDateTime = gmdate('Y-m-d 23:59:59');

        $pageSize = 200;

        $res = $this->erpRequest->send(
            config('services.erp.ebUrl'),
            self::EB_SERVICE_NAME,
            [
                'create_date_form' => $startDateTime,
                'create_date_to' => $endDateTime,
                "page" => 1,
                "pageSize" => $pageSize
            ],
            self::LOG_CHANNEL
        );

        //加上當下時間以便後續寫入DB
        $res['data'] = collect($res['data'])->map(function ($item) {
            $item['created_at'] = date('Y-m-d h:i:s');

            return $item;
        })->toArray();

        $total = (int)$res['total'];

        $totalPage = (int)ceil($total / $pageSize);

        $restData = array();

        \Log::channel('daily_refund_sync')
            ->info("[daily_refund_sync.count]" . $total);

        //如果回傳成功且資料不止一頁
        if ($totalPage > 1 && $res['code'] == "200") {
            for ($i = 2; $i <= $totalPage; $i++) {
                $content = $this->erpRequest->send(
                    config('services.erp.ebUrl'),
                    self::EB_SERVICE_NAME,
                    [
                        'create_date_form' => $startDateTime,
                        'create_date_to' => $endDateTime,
                        "page" => $i,
                        "pageSize" => $pageSize
                    ],
                    self::LOG_CHANNEL
                )['data'];
                foreach ($content as $v) {
                    $v = array_merge($v, ['created_at' => date('Y-m-d h:i:s')]);
                    array_push($restData, $v);
                }
            }
        }

        if (!empty($restData)) {
            $data = array_merge($res['data'], $restData);
        } else {
            $data = $res['data'];
        }

        DB::beginTransaction();
        try {
            $chunkData = array_chunk($data, 2);
            if (isset($chunkData) && !empty($chunkData)) {
                foreach ($chunkData as $item) {
                    RmaRefundList::insert($item);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            \Log::channel('daily_refund_sync')
                ->info("[daily_refund_sync.insertRefundList]" . $e);
            DB::rollBack();
            return false;
        }
    }
}

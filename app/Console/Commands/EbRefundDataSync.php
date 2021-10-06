<?php

namespace App\Console\Commands;

use App\Models\RmaRefundList;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use App\Repositories\RmaRefundListRepository;
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

    private const EB_ACCOUNT = 'IT2';
    private const EB_PWD = 'AbAO@12';
    private const EB_SERVICE_NAME = 'rmaRefundList';
    private $headers = [
        "Content-type: text/xml;charset=\"utf-8\"",
        'Accept: text/xml',
        'Cache-Control: no-cache',
        'Pragma: no-cache'
    ];
    /**
     * @var RmaRefundListRepository
     */
    private $rmaRefundListRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RmaRefundListRepository $rmaRefundListRepository)
    {
        parent::__construct();
        $this->rmaRefundListRepository = $rmaRefundListRepository;
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

        $res = $this->sendERPRequest($startDateTime, $endDateTime, $pageSize);

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
                $content = $this->sendERPRequest($startDateTime, $endDateTime, $pageSize, $i)['data'];

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

    private function genXML(string $paramsJson, string $userName, string $userPass, string $serviceName): string
    {
        return <<< EOF
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.example.org/Ec/">
  <SOAP-ENV:Body>
    <ns1:callService>
      <paramsJson>$paramsJson</paramsJson>
      <userName>$userName</userName>
      <userPass>$userPass</userPass>
      <service>$serviceName</service>
    </ns1:callService>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
EOF;
    }

    private function analyzeSOAP(string $soapForm)
    {
        // converting
        $soapForm = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $soapForm);
        $soapForm = str_replace("SOAP-ENV:", "", $soapForm);
        $soapForm = str_replace("<ns1:callServiceResponse>", "", $soapForm);
        $soapForm = str_replace("</ns1:callServiceResponse>", "", $soapForm);

        // converting to XML
        $parser = simplexml_load_string($soapForm);

        // get response
        return $parser->Body->response->__toString();
    }

    private function formatParams(string $startDateTime, string $endDateTime, int $page = 1, int $pageSize = 100)
    {
        return json_encode([
            'create_date_form' => $startDateTime,
            'create_date_to' => $endDateTime,
            'page' => $page,
            'pageSize' => $pageSize,
        ]);
    }

    private function sendERPRequest(string $startDateTime, string $endDateTime, int $pageSize = 100, int $page = 1): array
    {
        $jsonParams = $this->formatParams($startDateTime, $endDateTime, $page, $pageSize);

        \Log::channel('daily_refund_sync')
            ->info("[daily_refund_sync.reqJSON]" . $jsonParams);

        $ebSoapRequest = $this->genXML(
            $jsonParams,
            self::EB_ACCOUNT,
            self::EB_PWD,
            self::EB_SERVICE_NAME
        );

        $client = new Client();

        $res = $client->request(
            'POST',
            env("ERP_EB_URL"),
            [
                'headers' => $this->headers,
                'body' => $ebSoapRequest
            ]
        )->getBody()->getContents();

        $analyzedRes = json_decode($this->analyzeSOAP($res), true);

        \Log::channel('daily_refund_sync')
            ->info("[daily_refund_sync.resJSON]" . json_encode($analyzedRes));

        return $analyzedRes;
    }
}

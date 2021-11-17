<?php

namespace App\Console\Commands;

use App\Repositories\AmazonReportListRepository;
use App\Repositories\OrderProductsRepository;
use App\Repositories\OrderSkuCostDetailsRepository;
use App\Repositories\OrdersRepository;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderDataSync extends Command
{
    private const GET_ORDER = 'getOrders';
    private const GET_PRODUCT_BY_SKU = 'getProductBySku';
    private const GET_ORDER_DETAIL = 'getOrderCostDetailSku';
    private const AMZ_REPORT = 'amazonReportList';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order_data_sync {date? : Y-m-d}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'order_data_sync';
    private OrdersRepository $ordersRepository;
    private OrderProductsRepository $orderProductsRepository;
    private OrderSkuCostDetailsRepository $orderSkuCostDetailsRepository;
    private AmazonReportListRepository $amazonReportListRepository;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        OrdersRepository              $ordersRepository,
        OrderProductsRepository       $orderProductsRepository,
        OrderSkuCostDetailsRepository $orderSkuCostDetailsRepository,
        AmazonReportListRepository    $amazonReportListRepository
    ) {
        parent::__construct();
        $this->ordersRepository = $ordersRepository;
        $this->orderProductsRepository = $orderProductsRepository;
        $this->orderSkuCostDetailsRepository = $orderSkuCostDetailsRepository;
        $this->amazonReportListRepository = $amazonReportListRepository;
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $startDateTime = $this->argument('date') ? Carbon::parse($this->argument('date'))
            ->format('Y-m-d 00:00:00') : now()->subDay()->format('Y-m-d 00:00:00');

        $endDateTime = $this->argument('date') ? Carbon::parse($this->argument('date'))
            ->format('Y-m-d 23:59:59') : now()->subDay()->format('Y-m-d 23:59:59');

        $pageSize = 500;
        $orderCostParamsArr = array();//儲存請求getOrderCostDetailSku的參數.
        $orderProductParamsArr = array();//儲存sku和訂單編號以便後續update order_products剩餘欄位.
        $ordersData = array();//儲存要 insert orders 的訂單資訊
        $getAVOSellerID = array();//儲存當日不重複的AVO seller_id
        $res = $this->sendERPRequest(
            config('services.erp.wmsUrl'),
            self::GET_ORDER,
            [],
            $startDateTime,
            $endDateTime,
            $pageSize
        );
        $ordersWhiteList = $this->ordersRepository->getTableColumns();

        if (!$res['data']) {
            return false;
        }

        DB::beginTransaction();
        foreach ($res['data'] as $v) {
            //逐一透過商品sku取得商品詳細內容
            foreach ($v['productList'] as $productListItem) {
                $getProductInfo = $this->sendERPRequest(
                    config('services.erp.wmsUrl'),
                    self::GET_PRODUCT_BY_SKU,
                    [
                        'productSku' => $productListItem['sku']
                    ]
                );

                //如果回傳的 procutCategoryName1 是 AVO,則儲存產品資訊到 order_products
                if ($getProductInfo['code'] === 500002) {
                    sleep(60);
                }

                //回傳值可能為空
                if (!isset($getProductInfo['data']) || empty($getProductInfo['data'])) {
                    Log::channel('daily_order_sync')
                        ->info("[daily_order_sync.getProductInfo]" . json_encode($getProductInfo));

                    continue;
                }

                if (str_contains($getProductInfo['data'][0]['defaultSupplierCode'], 'AVO')) {
                    $productSkuArray = array();

                    //組建要寫入order_products的資訊 start
                    $productSkuArray['sku'] = $productListItem['sku'];
                    $productSkuArray['order_code'] = $v['order_code'];
                    $productSkuArray['weight'] = $productListItem['weight'];
                    $productSkuArray['active'] = 1;
                    $productSkuArray['supplier_type'] = $getProductInfo['data'][0]['procutCategoryName1'];
                    $productSkuArray['supplier'] = $getProductInfo['data'][0]['procutCategoryName2'];

                    //TODO
                    $this->orderProductsRepository->insertData($productSkuArray);

                    //組建要request 給 getOrderCostDetailSku的參數
                    $orderCostParams = [
                        'productSku' => $productSkuArray['sku'],
                        'orderCode' => $productSkuArray['order_code']
                    ];

                    $orderProductParams = [
                        'order_code' => $productSkuArray['order_code'],
                        'sku' => $productSkuArray['sku']
                    ];

                    //組建要request 給 getOrderCostDetailSku的參數
                    array_push($orderCostParamsArr, $orderCostParams);

                    //組建要update order_products剩餘欄位的sku和訂單編號
                    array_push($orderProductParamsArr, $orderProductParams);

                    //儲存當日AVO seller_id
                    array_push($getAVOSellerID, $v['seller_id']);

                    unset($productSkuArray);
                }
            }
            //for loop end here
            $v = array_intersect_key($v, array_flip($ordersWhiteList));//只留下 orders table的欄位

            $v['platform_ref_no'] = $v['platform_ref_no'][0] ?? null;
            $v['created_at'] = date('Y-m-d h:i:s');

            array_push($ordersData, $v);
        }

        $total = (int)$res['count'];

        $totalPage = (int)ceil($total / $pageSize);

        $restOrders = array();

        Log::channel('daily_order_sync')
            ->info("[daily_order_sync.getOrders.count]" . $total);

        //如果回傳成功且資料不止一頁
        if ($totalPage > 1 && $res['message'] == "Success") {
            sleep(1);//避免請求太頻繁被擋

            for ($i = 2; $i <= $totalPage; $i++) {
                $content = $this->sendERPRequest(
                    config('services.erp.wmsUrl'),
                    self::GET_ORDER,
                    [],
                    $startDateTime,
                    $endDateTime,
                    $pageSize,
                    $i
                )['data'];

                foreach ($content as $v) {
                    //如果回傳的procutCategoryName1 是 AVO,則儲存產品資訊到 order_products
                    foreach ($v['productList'] as $productListItem) {
                        $getProductInfos = $this->sendERPRequest(
                            config('services.erp.wmsUrl'),
                            self::GET_PRODUCT_BY_SKU,
                            ['productSku' => $productListItem['sku']]
                        );

                        //如果回傳的 procutCategoryName1 是 AVO,則儲存產品資訊到 order_products
                        if ($getProductInfos['code'] === 500002) {
                            sleep(60);
                        }

                        //回傳值可能為空
                        if (!isset($getProductInfos['data']) || empty($getProductInfos['data'])) {
                            Log::channel('daily_order_sync')
                                ->info("[daily_order_sync.getProductInfos]" . json_encode($getProductInfos));

                            continue;
                        }

                        if (str_contains($getProductInfos['data'][0]['defaultSupplierCode'], 'AVO')) {
                            $productSkuArr = array();
                            //組建要寫入order_products的資訊
                            $productSkuArr['sku'] = $productListItem['sku'];
                            $productSkuArr['order_code'] = $v['order_code'];
                            $productSkuArr['weight'] = $productListItem['weight'];
                            $productSkuArr['active'] = 1;
                            $productSkuArr['supplier_type'] = $getProductInfos['data'][0]['procutCategoryName1'];
                            $productSkuArr['supplier'] = $getProductInfos['data'][0]['procutCategoryName2'];

                            $this->orderProductsRepository->insertData($productSkuArr);

                            //組建要request 給 getOrderCostDetailSku的參數
                            $orderCostParams = [
                                'productSku' => $productSkuArr['sku'],
                                'orderCode' => $productSkuArr['order_code']
                            ];

                            //組建要update order_products剩餘欄位的sku和訂單編號
                            $orderProductParams = [
                                'order_code' => $productSkuArr['order_code'],
                                'sku' => $productSkuArr['sku']
                            ];

                            unset($productSkuArr);

                            array_push($orderCostParamsArr, $orderCostParams);

                            array_push($orderProductParamsArr, $orderProductParams);

                            //儲存當日AVO seller_id
                            array_push($getAVOSellerID, $v['seller_id']);
                        }
                    }
                    $v = array_intersect_key($v, array_flip($ordersWhiteList));

                    $v['platform_ref_no'] = $v['platform_ref_no'][0] ?? null;
                    $v['created_at'] = date('Y-m-d h:i:s');

                    array_push($restOrders, $v);

                    unset($productSkuParams);
                }

                //TODO 確切限制比數以及秒數待確認
                sleep(1);
            }
        }

        //取得 費用/SKU維度的訂單費用和成本明细,並寫入DB
        //TODO 可一次傳最多1000筆 sku
        if (!empty($orderCostParamsArr)) {
            //將回傳的駝峰參數轉成蛇形式 再insert DB
            $costDetailArray = array();

            foreach ($orderCostParamsArr as $v) {
                $getCostDetail = $this->sendERPRequest(
                    config('services.erp.wmsUrl'),
                    self::GET_ORDER_DETAIL,
                    $v
                );

                if ($getCostDetail['data']) {
                    $tempCostDetailArr = array();
                    foreach ($getCostDetail['data'][0] as $k => $val) {
                        $kNew = $this->camelToSnakeCase($k);
                        $tempCostDetailArr[$kNew] = $val;
                        $tempCostDetailArr['created_at'] = date('Y-m-d h:i:s');
                    }

                    array_push($costDetailArray, $tempCostDetailArr);

                    unset($tempCostDetailArr);
                }
            }

            foreach ($costDetailArray as $items) {
                $isDuplicated = $this->orderSkuCostDetailsRepository->checkIfSkuDetailDuplicated(
                    $items['product_barcode'],
                    $items['reference_no']
                );

                if ($isDuplicated) {
                    Log::channel('daily_order_sync')
                        ->info("[daily_order_sync.isDuplicated]" . json_encode($items));
                } else {
                    $insertCostDetail = $this->orderSkuCostDetailsRepository->insertData($items);

                    if (!$insertCostDetail) {
                        Log::channel('daily_order_sync')
                            ->info("[daily_order_sync.insertCostDetailFailed]");
                        DB::rollBack();

                        return false;
                    }
                }
            }
        }

        if (!empty($restOrders)) {
            $ordersData = array_merge($ordersData, $restOrders);
        }

        //分批insert訂單到orders
        foreach ($ordersData as $item) {
            $insertOrdersList = $this->ordersRepository->insertData($item);

            if (!$insertOrdersList) {
                Log::channel('daily_order_sync')
                    ->info("[daily_order_sync.insertOrdersList]" . $insertOrdersList);
                DB::rollBack();

                return false;
            }
        }

        //如有當日的AVO seller_id,則作為參數request amazonReportList 以獲取獲取结算報告列表
        if (!empty($getAVOSellerID)) {
            $getAVOSellerID = array_filter(array_unique($getAVOSellerID));

            $page = 1;

            $amazonList = array();

            foreach ($getAVOSellerID as $item) {
                $getAmazonReportParams = [
                    "userAccount" => $item,
                    "shipTimeFrom" => $startDateTime,
                    "shipTimeTo" => $endDateTime,
                    "page" => $page,
                    "pageSize" => $pageSize
                ];

                $amazonReportList = $this->sendERPRequest(
                    config('services.erp.ebUrl'),
                    self::AMZ_REPORT,
                    $getAmazonReportParams
                );

                if (!empty($amazonReportList['data'])) {
                    foreach ($amazonReportList['data'] as $list) {
                        $list['created_at'] = date('Y-m-d h:i:s');

                        array_push($amazonList, $list);
                    }

                    $amazonTotalPage = (int)ceil((int)$amazonReportList['totalCount'] / $pageSize);

                    if ($amazonTotalPage > 1) {
                        for ($x = 2; $x <= $amazonTotalPage; $x++) {
                            $getAmazonReportParam = [
                                "userAccount" => $item,
                                "shipTimeFrom" => $startDateTime,
                                "shipTimeTo" => $endDateTime,
                                "page" => $x,
                                "pageSize" => $pageSize
                            ];

                            //TODO
                            if ($item) {
                                $amazonReportList = $this->sendERPRequest(
                                    config('services.erp.ebUrl'),
                                    self::AMZ_REPORT,
                                    $getAmazonReportParam
                                );

                                foreach ($amazonReportList['data'] as $lists) {
                                    $lists['created_at'] = date('Y-m-d h:i:s');

                                    array_push($amazonList, $lists);
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!empty($amazonList)) {
            foreach ($amazonList as $items) {
                $insertAmazonList = $this->amazonReportListRepository->insertData($items);

                if (!$insertAmazonList) {
                    Log::channel('daily_order_sync')
                        ->info("[daily_order_sync.insertAmazonList]" . $insertAmazonList);
                    DB::rollBack();

                    return false;
                }
            }
        }

        //TODO 作法待優化
        //補齊 order_products其餘的欄位
        if (!empty($orderProductParamsArr)) {
            foreach ($orderProductParamsArr as $v) {
                $item = $this->orderSkuCostDetailsRepository->getSkuDetail($v['order_code'], $v['sku']);

                $countCol = $this->orderProductsRepository->countReportColumns($v['order_code'], $v['sku']);

                $v['sales_amount'] = 0;
                $v['fba_fee'] = (float)abs($countCol[0]->fba_fee) ?? 0;
                $v['marketplace_tax'] = (float)abs($countCol[0]->marketplace_tax) ?? 0;
                $v['cost_of_point'] = (float)abs($countCol[0]->cost_of_point) ?? 0;
                $v['exclusives_referral_fee'] = (float)abs($countCol[0]->exclusives_referral_fee) ?? 0;
                if (!empty($item)) {
                    $v['currency_code'] = $item['currency_code_org'];
                    $v['sales_amount'] = $item['order_total_amount_org'] ?? 0;
                    $v['transaction_fee'] = $item['platform_cost_org'];
                    $v['first_mile_shipping_fee'] = $item['first_carrier_freight'];
                    $v['first_mile_tariff'] = $item['tariff_fee'];
                    $v['last_mile_shipping_fee'] = $item['shipping_fee_org'];
                    $v['paypal_fee'] = $item['payment_platform_fee_org'];
                    $v['other_fee'] = $item['other_fee_org'] - $v['marketplace_tax'] - $v['cost_of_point']
                        - $v['exclusives_referral_fee'];
                    $v['other_transaction'] = $item['other_fee_org'];
                }

                $getPromotion = $this->orderProductsRepository->countPromotionAmount(
                    $v['order_code'],
                    $v['sku']
                );

                $v['promotion_amount'] = $getPromotion[0]->promotion_amount ? abs($getPromotion[0]->promotion_amount)
                    : 0;

                $principal = $getPromotion[0]->principal ? abs($getPromotion[0]->principal) : 0;

                $v['promotion_discount_rate'] = $this->getPromotionDiscount(
                    (float)$v['promotion_amount'],
                    (float)$principal
                );

                $res = $this->orderProductsRepository->updateData($v, $v['order_code'], $v['sku']);

                if (!$res) {
                    Log::channel('daily_order_sync')
                        ->info("[daily_order_sync.updateOrderProduct]" . $res)
                        ->info("[order_code:{$v['order_code']}, sku:{$v['sku']}");
                    DB::rollBack();

                    return false;
                }
            }
        }
        DB::commit();

        Log::channel('daily_order_sync')
            ->info("[daily_order_sync.endTime]" . date("Y-m-d H:i:s"));
    }

    private function sendERPRequest(
        string $url,
        string $serviceName,
        array  $customParam = [],
        string $startDateTime = "",
        string $endDateTime = "",
        int    $pageSize = 100,
        int    $page = 1
    ) {
        $jsonParams = $customParam ? json_encode($customParam) : $this->formatParams(
            $startDateTime,
            $endDateTime,
            $page,
            $pageSize
        );

        Log::channel('daily_order_sync')
            ->info("[daily_order_sync.{$serviceName}.reqJSON]" . $jsonParams);

        $ebSoapRequest = $this->genXML(
            $jsonParams,
            config('services.erp.ebAccount'),
            config('services.erp.ebPwd'),
            $serviceName
        );

        $client = new Client();

        $res = $client->request(
            'POST',
            $url,
            [
                'body' => $ebSoapRequest
            ]
        )->getBody()->getContents();

        $analyzedRes = json_decode($this->analyzeSOAP($res), true);

        Log::channel('daily_order_sync')
            ->info("[daily_order_sync.{$serviceName}.resJSON]" . json_encode($analyzedRes));

        return $analyzedRes;
    }

    private function formatParams(string $startDateTime, string $endDateTime, int $page = 1, int $pageSize = 100)
    {
        return json_encode([
            'shipDateFor' => $startDateTime,
            'shipDateTo' => $endDateTime,
            "pagination" => [
                "page" => $page, "pageSize" => $pageSize
            ]
        ]);
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

    private function analyzeSOAP(string $soapForm): string
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

    public function camelToSnakeCase(string $string): string
    {
        return strtolower(
            preg_replace(
                '/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/',
                '_',
                $string
            )
        );
    }

    //取至小數點第二位

    public function getPromotionDiscount(float $promotionAmount, float $principal): float
    {
        if ($promotionAmount == 0 || $principal == 0) {
            return 0;
        }
        return $this->getFloatVal(($principal - $promotionAmount) / $principal);
    }

    public function getFloatVal(float $num): float
    {
        return substr(sprintf(" % .3f", $num), 0, -1);
    }
}

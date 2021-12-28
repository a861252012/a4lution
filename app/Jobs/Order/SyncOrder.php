<?php

namespace App\Jobs\Order;

use App\Repositories\AmazonReportListRepository;
use App\Repositories\OrderProductRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderSkuCostDetailRepository;
use App\Support\ERPRequester;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const GET_ORDER = 'getOrders';
    private const GET_PRODUCT_BY_SKU = 'getProductBySku';
    private const GET_ORDER_DETAIL = 'getOrderCostDetailSku';
    private const AMZ_REPORT = 'amazonReportList';
    private const LOG_CHANNEL = 'daily_order_sync';
    private const PAGE_SIZE = 500;
    private string $startDateTime;
    private string $endDateTime;
    private int $correlationID;

    public function __construct(
        string $startDateTime,
        string $endDateTime,
        int    $correlationID
    ) {
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;
        $this->correlationID = $correlationID;
    }

    public function handle()
    {
        $orderCostParamsArr = [];//儲存請求getOrderCostDetailSku的參數.
        $orderProductParamsArr = [];//儲存sku和訂單編號以便後續update order_products剩餘欄位.
        $ordersData = [];//儲存要 insert orders 的訂單資訊
        $getAVOSellerID = [];//儲存當日不重複的AVO seller_id

        //紀錄執行時間
        Log::channel('daily_order_sync')
            ->info('[order_sync.start (UTC)]' . now()->toDateTimeString());

        $res = app(ERPRequester::class)->send(
            config('services.erp.wmsUrl'),
            self::GET_ORDER,
            [
                'shipDateFor' => $this->startDateTime,
                'shipDateTo' => $this->endDateTime,
                'pagination' => [
                    'page' => 1,
                    'pageSize' => self::PAGE_SIZE,
                ],
            ],
            self::LOG_CHANNEL
        );

        $ordersWhiteList = app(OrderRepository::class)->getTableColumns();

        if (!$res['data']) {
            return false;
        }

        DB::beginTransaction();
        foreach ($res['data'] as $v) {
            //逐一透過商品sku取得商品詳細內容
            foreach ($v['productList'] as $productListItem) {
                $getProductInfo = app(ERPRequester::class)->send(
                    config('services.erp.wmsUrl'),
                    self::GET_PRODUCT_BY_SKU,
                    ['productSku' => $productListItem['sku']],
                    self::LOG_CHANNEL
                );

                //如果回傳的 procutCategoryName1 是 AVO,則儲存產品資訊到 order_products
                if ($getProductInfo['code'] === 500002) {
                    sleep(60);
                }

                //回傳值可能為空
                if (empty($getProductInfo['data'])) {
                    Log::channel('daily_order_sync')
                        ->info('[order_sync.getProductInfo]' . json_encode($getProductInfo));

                    continue;
                }

                if (str_contains($getProductInfo['data'][0]['defaultSupplierCode'], 'AVO')) {
                    $productSkuArray = [];

                    //組建要寫入order_products的資訊 start
                    $productSkuArray['sku'] = $productListItem['sku'];
                    $productSkuArray['order_code'] = $v['order_code'];
                    $productSkuArray['weight'] = $productListItem['weight'];
                    $productSkuArray['active'] = 1;
                    $productSkuArray['correlation_id'] = $this->correlationID;
                    $productSkuArray['supplier_type'] = $getProductInfo['data'][0]['procutCategoryName1'];
                    $productSkuArray['supplier'] = $getProductInfo['data'][0]['procutCategoryName2'];

                    //TODO
                    app(OrderProductRepository::class)->create($productSkuArray);

                    //組建要request 給 getOrderCostDetailSku的參數
                    $orderCostParams = [
                        'productSku' => $productSkuArray['sku'],
                        'orderCode' => $productSkuArray['order_code'],
                    ];

                    $orderProductParams = [
                        'order_code' => $productSkuArray['order_code'],
                        'sku' => $productSkuArray['sku'],
                    ];

                    //組建要request 給 getOrderCostDetailSku的參數
                    $orderCostParamsArr[] = $orderCostParams;

                    //組建要update order_products剩餘欄位的sku和訂單編號
                    $orderProductParamsArr[] = $orderProductParams;

                    //儲存當日AVO seller_id
                    $getAVOSellerID[] = $v['seller_id'];

                    unset($productSkuArray);
                }
            }
            //for loop end here
            $v = array_intersect_key($v, array_flip($ordersWhiteList));//只留下 orders table的欄位

            $v['platform_ref_no'] = $v['platform_ref_no'][0] ?? null;
            $v['created_at'] = now()->toDateTimeString();
            $v['correlation_id'] = $this->correlationID;

            $ordersData[] = $v;
        }

        $total = (int)$res['count'];

        $totalPage = (int)ceil($total / self::PAGE_SIZE);

        $restOrders = [];

        Log::channel('daily_order_sync')->info('[order_sync.getOrders.count]' . $total);

        //如果回傳成功且資料不止一頁
        if ($totalPage > 1 && $res['message'] === 'Success') {
            for ($i = 2; $i <= $totalPage; $i++) {
                $content = app(ERPRequester::class)->send(
                    config('services.erp.wmsUrl'),
                    self::GET_ORDER,
                    [
                        'shipDateFor' => $this->startDateTime,
                        'shipDateTo' => $this->endDateTime,
                        'pagination' => [
                            'page' => $i,
                            'pageSize' => self::PAGE_SIZE,
                        ],
                    ],
                    self::LOG_CHANNEL
                )['data'];

                foreach ($content as $v) {
                    //如果回傳的procutCategoryName1 是 AVO,則儲存產品資訊到 order_products
                    foreach ($v['productList'] as $productListItem) {
                        $getProductInfos = app(ERPRequester::class)->send(
                            config('services.erp.wmsUrl'),
                            self::GET_PRODUCT_BY_SKU,
                            ['productSku' => $productListItem['sku']],
                            self::LOG_CHANNEL
                        );

                        //如果回傳的 procutCategoryName1 是 AVO,則儲存產品資訊到 order_products
                        if ($getProductInfos['code'] === 500002) {
                            sleep(60);
                        }

                        //回傳值可能為空
                        if (empty($getProductInfos['data'])) {
                            Log::channel('daily_order_sync')
                                ->info('[order_sync.getProductInfos]' . json_encode($getProductInfos));
                            continue;
                        }

                        if (str_contains($getProductInfos['data'][0]['defaultSupplierCode'], 'AVO')) {
                            $productSkuArr = [];
                            //組建要寫入order_products的資訊
                            $productSkuArr['sku'] = $productListItem['sku'];
                            $productSkuArr['order_code'] = $v['order_code'];
                            $productSkuArr['weight'] = $productListItem['weight'];
                            $productSkuArr['active'] = 1;
                            $productSkuArr['correlation_id'] = $this->correlationID;
                            $productSkuArr['supplier_type'] = $getProductInfos['data'][0]['procutCategoryName1'];
                            $productSkuArr['supplier'] = $getProductInfos['data'][0]['procutCategoryName2'];

                            app(OrderProductRepository::class)->create($productSkuArr);

                            //組建要request 給 getOrderCostDetailSku的參數
                            $orderCostParams = [
                                'productSku' => $productSkuArr['sku'],
                                'orderCode' => $productSkuArr['order_code'],
                            ];

                            //組建要update order_products剩餘欄位的sku和訂單編號
                            $orderProductParams = [
                                'order_code' => $productSkuArr['order_code'],
                                'sku' => $productSkuArr['sku'],
                            ];

                            unset($productSkuArr);

                            $orderCostParamsArr[] = $orderCostParams;

                            $orderProductParamsArr[] = $orderProductParams;

                            //儲存當日AVO seller_id
                            $getAVOSellerID[] = $v['seller_id'];
                        }
                    }
                    $v = array_intersect_key($v, array_flip($ordersWhiteList));

                    $v['platform_ref_no'] = $v['platform_ref_no'][0] ?? null;
                    $v['created_at'] = now()->toDateTimeString();
                    $v['correlation_id'] = $this->correlationID;

                    $restOrders[] = $v;

                    unset($productSkuParams);
                }
            }
        }

        //取得 費用/SKU維度的訂單費用和成本明细,並寫入DB
        //TODO 可一次傳最多1000筆 sku
        if (!empty($orderCostParamsArr)) {
            //將回傳的駝峰參數轉成蛇形式 再insert DB
            $costDetailArray = [];

            foreach ($orderCostParamsArr as $v) {
                $getCostDetail = app(ERPRequester::class)->send(
                    config('services.erp.wmsUrl'),
                    self::GET_ORDER_DETAIL,
                    $v,
                    self::LOG_CHANNEL
                );

                if ($getCostDetail['data']) {
                    $tempCostDetailArr = [];
                    foreach ($getCostDetail['data'][0] as $k => $val) {
                        $kNew = $this->camelToSnakeCase($k);
                        $tempCostDetailArr[$kNew] = $val;
                        $tempCostDetailArr['created_at'] = now()->toDateTimeString();
                        $tempCostDetailArr['correlation_id'] = $this->correlationID;
                    }

                    $costDetailArray[] = $tempCostDetailArr;

                    unset($tempCostDetailArr);
                }
            }

            foreach ($costDetailArray as $items) {
                $isDuplicated = app(OrderSkuCostDetailRepository::class)->checkIfSkuDetailDuplicated(
                    $items['product_barcode'],
                    $items['reference_no']
                );

                if ($isDuplicated) {
                    Log::channel('daily_order_sync')
                        ->info('[order_sync.isDuplicated]' . json_encode($items));
                } else {
                    $insertCostDetail = app(OrderSkuCostDetailRepository::class)->create($items);

                    if (!$insertCostDetail) {
                        Log::channel('daily_order_sync')
                            ->info('[order_sync.insertCostDetailFailed]');
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
            $insertOrdersList = app(OrderRepository::class)->create($item);

            if (!$insertOrdersList) {
                Log::channel('daily_order_sync')
                    ->info('[order_sync.insertOrdersList]' . $insertOrdersList);
                DB::rollBack();

                return false;
            }
        }

        //如有當日的AVO seller_id,則作為參數request amazonReportList 以獲取獲取结算報告列表
        if (!empty($getAVOSellerID)) {
            $getAVOSellerID = array_filter(array_unique($getAVOSellerID));

            $amazonList = [];

            foreach ($getAVOSellerID as $item) {
                $getAmazonReportParams = [
                    'userAccount' => $item,
                    'shipTimeFrom' => $this->startDateTime,
                    'shipTimeTo' => $this->endDateTime,
                    'page' => 1,
                    'pageSize' => self::PAGE_SIZE,
                ];

                $amazonReportList = app(ERPRequester::class)->send(
                    config('services.erp.ebUrl'),
                    self::AMZ_REPORT,
                    $getAmazonReportParams,
                    self::LOG_CHANNEL
                );

                if (!empty($amazonReportList['data'])) {
                    foreach ($amazonReportList['data'] as $list) {
                        $list['created_at'] = now()->toDateTimeString();
                        $list['correlation_id'] = $this->correlationID;

                        $amazonList[] = $list;
                    }

                    $amazonTotalPage = (int)ceil((int)$amazonReportList['totalCount'] / self::PAGE_SIZE);

                    if ($amazonTotalPage > 1) {
                        for ($x = 2; $x <= $amazonTotalPage; $x++) {
                            $getAmazonReportParam = [
                                'userAccount' => $item,
                                'shipTimeFrom' => $this->startDateTime,
                                'shipTimeTo' => $this->endDateTime,
                                'page' => $x,
                                'pageSize' => self::PAGE_SIZE,
                            ];

                            if ($item) {
                                $amazonReportList = app(ERPRequester::class)->send(
                                    config('services.erp.ebUrl'),
                                    self::AMZ_REPORT,
                                    $getAmazonReportParam,
                                    self::LOG_CHANNEL
                                );

                                foreach ($amazonReportList['data'] as $lists) {
                                    $lists['created_at'] = now()->toDateTimeString();
                                    $lists['correlation_id'] = $this->correlationID;

                                    $amazonList[] = $lists;
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!empty($amazonList)) {
            foreach ($amazonList as $items) {
                $insertAmazonList = app(AmazonReportListRepository::class)->create($items);

                if (!$insertAmazonList) {
                    Log::channel('daily_order_sync')
                        ->info('[order_sync.insertAmazonList]' . $insertAmazonList);
                    DB::rollBack();

                    return false;
                }
            }
        }

        //TODO 作法待優化
        //補齊 order_products其餘的欄位
        if (!empty($orderProductParamsArr)) {
            foreach ($orderProductParamsArr as $v) {
                $item = app(OrderSkuCostDetailRepository::class)->getSkuDetail($v['order_code'], $v['sku']);

                $countCol = app(OrderProductRepository::class)->countReportColumns($v['order_code'], $v['sku']);

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

                $getPromotion = app(OrderProductRepository::class)->countPromotionAmount(
                    $v['order_code'],
                    $v['sku']
                );

                $v['promotion_amount'] = $getPromotion[0]->promotion_amount ?
                    abs($getPromotion[0]->promotion_amount) : 0;

                $principal = $getPromotion[0]->principal ? abs($getPromotion[0]->principal) : 0;

                $v['promotion_discount_rate'] = $this->getPromotionDiscount(
                    (float)$v['promotion_amount'],
                    (float)$principal
                );

                $res = app(OrderProductRepository::class)->updateData($v, $v['order_code'], $v['sku']);

                if (!$res) {
                    Log::channel('daily_order_sync')
                        ->info("[order_sync.updateOrderProduct.order_code:{$v['order_code']}, sku:{$v['sku']}");
                    DB::rollBack();

                    return false;
                }
            }
        }
        DB::commit();

        Log::channel('daily_order_sync')
            ->info('[order_sync.endTime (UTC)]' . now()->toDateTimeString());
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
        return ($promotionAmount == 0 || $principal == 0) ? 0 :
            $this->getFloatVal(($principal - $promotionAmount) / $principal);
    }

    public function getFloatVal(float $num): float
    {
        return substr(sprintf(' % .3f', $num), 0, -1);
    }
}

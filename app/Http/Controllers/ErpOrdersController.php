<?php

namespace App\Http\Controllers;

use App\Models\BillingStatement;
use App\Models\ExchangeRate;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\RmaRefundList;
use App\Models\SystemChangeLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ErpOrdersController extends Controller
{
    private $rmaRefundList;
    private $order;
    private $exchangeRate;
    private $systemChangeLog;
    private $billingStatement;
    private $orderProduct;

    public function __construct(
        RmaRefundList    $rmaRefundList,
        Order            $order,
        ExchangeRate     $exchangeRate,
        SystemChangeLog  $systemChangeLog,
        BillingStatement $billingStatement,
        OrderProduct     $orderProduct
    ) {
        $this->rmaRefundList = $rmaRefundList;
        $this->order = $order;
        $this->exchangeRate = $exchangeRate;
        $this->systemChangeLog = $systemChangeLog;
        $this->billingStatement = $billingStatement;
        $this->orderProduct = $orderProduct;
    }

    public function refundSearchView(Request $request)
    {
        $data['shipped_date'] = $request->input('shipped_date') ?? null;
        $data['erp_order_ID'] = $request->input('erp_order_ID') ?? null;
        $data['sku'] = $request->input('sku') ?? null;
        $data['warehouse_order_id'] = $request->input('warehouse_order_id') ?? null;
        $data['supplier'] = $request->input('supplier') ?? null;

        $query = $this->rmaRefundList->select(
            'create_date',
            'warehouse_ref_no AS warehouse_order_id',
            'ref_no AS refund_order_id',
            'refrence_no_platform AS erp_order_id',
            'warehouse_ship_date AS shipped_date',
            'product_sku AS sku',
            'pc_name AS supplier',
            'amount_refund AS refund_price',
            'amount_paid AS transaction_amount',
            'amount_order AS sales_vloume'
        );

        if ($data['shipped_date']) {
            $shippedDateFrom = date('Y-m-d 00:00:00', strtotime($data['shipped_date']));

            $shippedDateTo = date('Y-m-d 23:59:59', strtotime($data['shipped_date']));

            $query->whereBetween('warehouse_ship_date', [$shippedDateFrom, $shippedDateTo]);
        }

        if ($data['erp_order_ID']) {
            $query->where('refrence_no_platform', '=', $data['erp_order_ID']);
        }

        if ($data['sku']) {
            $query->where('product_sku', '=', $data['sku']);
        }

        if ($data['warehouse_order_id']) {
            $query->where('warehouse_ref_no', '=', $data['warehouse_order_id']);
        }

        if ($data['supplier']) {
            $query->where('pc_name', '=', $data['supplier']);
        }

        $data['lists'] = $query->paginate(100)
            ->appends($request->query());

        return view('erpOrders.refundSearch', ['data' => $data]);
    }

    public function ordersSearchView(Request $request)
    {
        $data['erp_order_id'] = $request->erp_order_id;
        $data['shipped_date_from'] = $request->shipped_date_from;
        $data['shipped_date_to'] = $request->shipped_date_to;
        $data['sku'] = $request->sku;
        $data['supplier'] = $request->supplier;

        $data['lists'] = $this->order::from('orders as o')
            ->join('order_products as p', function ($join) {
                $join->on('p.order_code', '=', 'o.order_code')
                    ->where('p.active', '=', 1);
            })
            ->join('order_sku_cost_details as d', function ($join) {
                $join->on('p.order_code', '=', 'd.reference_no');
                $join->on('p.sku', '=', 'd.product_barcode');
            })
            ->select(
                'd.currency_code_org',
                'o.platform',
                'o.seller_id AS acc_nick_name',
                'o.platform_user_name AS acc_name',
                'o.order_type',
                'o.reference_no AS erp_order_id',
                'p.sku',
                'p.supplier',
                'p.id as product_id',
                'o.order_code AS package_id',
                'd.site_id',
                'p.sales_amount AS order_price',
                DB::raw("date_format(o.ship_time,'%Y-%m-%d') as 'shipped_date'"),
                DB::raw("CONCAT(o.warehouse_code,'[',o.warehouse_name,']') AS 'warehouse'"),
            )
            ->when($request->erp_order_id, fn ($q) => $q->where('o.reference_no', $request->erp_order_id))
            ->when($request->sku, fn ($q) => $q->where('p.sku', $request->sku))
            ->when($request->supplier, fn ($q) => $q->where('p.supplier', $request->supplier))
            ->when(
                $request->shipped_date_from && $request->shipped_date_to,
                fn ($q) => $q->whereBetween(
                    'o.ship_time',
                    [
                    Carbon::parse($request->shipped_date_from)->startOfDay()->toDateTimeString(),
                    Carbon::parse($request->shipped_date_to)->endOfDay()->toDateTimeString(),
                    ]
                )
            )
            ->paginate(100)
            ->appends($request->query());

        return view('erpOrders.ordersSearch', ['data' => $data]);
    }

    public function editOrders(Request $request)
    {
        $data['platform'] = $request->input('platform') ?? null;
        $data['acc_nick_name'] = $request->input('acc_nick_name') ?? null;
        $data['acc_name'] = $request->input('acc_name') ?? null;
        $data['site_id'] = $request->input('site_id') ?? null;
        $data['shipped_date'] = $request->input('shipped_date') ?? null;
        $data['package_id'] = $request->input('package_id') ?? null;
        $data['erp_order_id'] = $request->input('erp_order_id') ?? null;
        $data['sku'] = $request->input('sku') ?? null;
        $data['order_price'] = $request->input('order_price') ?? null;
        $data['supplier'] = $request->input('supplier') ?? null;
        $data['warehouse'] = $request->input('warehouse') ?? null;

        $query = $this->order::from('orders as o')
            ->join('order_sku_cost_details as d', 'd.reference_no', '=', 'o.order_code')
            ->join('order_products as p', 'p.order_code', '=', 'o.order_code')
            ->select(
                'o.sm_code',
                'o.seller_id',
                'o.tracking_number',
                'o.add_time',
                'o.order_paydate',
                'o.ship_time',
                'd.product_title',
                'd.op_platform_sales_sku',
                'd.asin_or_item',
                'd.quantity',
                'p.id as product_id',
                'p.weight',
                'p.promotion_discount_rate',
                'p.promotion_amount',
                'p.purchase_shipping_fee',
                'p.product_cost',
                'p.first_mile_shipping_fee',
                'p.first_mile_tariff',
                'p.last_mile_shipping_fee',
                'p.paypal_fee',
                'p.transaction_fee',
                'p.fba_fee',
                'p.promotion_amount',
                'p.promotion_discount_rate',
                'p.other_transaction',
                'd.currency_code_org'
            )
            ->where('p.active', 1);

        if ($data['acc_nick_name']) {
            $query->where('o.seller_id', $data['acc_nick_name']);
        }

        if ($data['erp_order_id']) {
            $query->where('o.reference_no', $data['erp_order_id']);
        }

        if ($data['package_id']) {
            $query->where('o.order_code', $data['package_id']);
        }

        if ($data['sku']) {
            $query->where('p.sku', $data['sku']);
        }

        if ($data['shipped_date']) {
            $shippedDateFrom = date('Y-m-d 00:00:00', strtotime($data['shipped_date']));
            $shippedDateTo = date('Y-m-d 23:59:59', strtotime($data['shipped_date']));

            $query->whereBetween('o.ship_time', [$shippedDateFrom, $shippedDateTo]);
        }

        $data['lists'] = $query->first()->toArray();

        $data['sys_logs'] = $this->getChangeLog($data['lists']['product_id']);

        //取得匯率
        $formattedQuotedDate = DB::raw("date_format(quoted_date,'%Y%m')");

        $formattedShippedDate = date("Ym", strtotime($data['shipped_date']));

        $data['exchange_rate'] = $this->exchangeRate->select('base_currency', 'exchange_rate')
            ->active()
            ->wherein('base_currency', [$data['lists']['currency_code_org'], 'RMB'])
            ->where($formattedQuotedDate, $formattedShippedDate)
            ->pluck('exchange_rate', 'base_currency')
            ->toArray();

        //匯率換算
        $data['lists']['order_price_hkd'] = $data['order_price'] ? $this->getHkdRate(
            $data['exchange_rate'][$data['lists']['currency_code_org']],
            $data['order_price']
        ) : 0;
        $data['lists']['purchase_shipping_fee_hkd'] = $data['lists']['purchase_shipping_fee'] ? $this->getHkdRate(
            $data['exchange_rate']['RMB'],
            $data['lists']['purchase_shipping_fee']
        ) : 0;
        $data['lists']['product_cost_hkd'] = $data['lists']['product_cost'] ? $this->getHkdRate(
            $data['exchange_rate']['RMB'],
            $data['lists']['product_cost']
        ) : 0;
        $data['lists']['first_mile_shipping_fee_hkd'] = $data['lists']['first_mile_shipping_fee'] ? $this->getHkdRate(
            $data['exchange_rate'][$data['lists']['currency_code_org']],
            $data['lists']['first_mile_shipping_fee']
        ) : 0;
        $data['lists']['first_mile_tariff_hkd'] = $data['lists']['first_mile_tariff'] ? $this->getHkdRate(
            $data['exchange_rate'][$data['lists']['currency_code_org']],
            $data['lists']['first_mile_tariff']
        ) : 0;
        $data['lists']['last_mile_shipping_fee_hkd'] = $data['lists']['last_mile_shipping_fee'] ? $this->getHkdRate(
            $data['exchange_rate'][$data['lists']['currency_code_org']],
            $data['lists']['last_mile_shipping_fee']
        ) : 0;
        $data['lists']['paypal_fee_hkd'] = $data['lists']['paypal_fee'] ? $this->getHkdRate(
            $data['exchange_rate'][$data['lists']['currency_code_org']],
            $data['lists']['paypal_fee']
        ) : 0;
        $data['lists']['transaction_fee_hkd'] = $data['lists']['transaction_fee'] ? $this->getHkdRate(
            $data['exchange_rate'][$data['lists']['currency_code_org']],
            $data['lists']['transaction_fee']
        ) : 0;
        $data['lists']['fba_fee_hkd'] = $data['lists']['fba_fee'] ? $this->getHkdRate(
            $data['exchange_rate'][$data['lists']['currency_code_org']],
            $data['lists']['fba_fee']
        ) : 0;
        $data['lists']['other_transaction_hkd'] = $data['lists']['other_transaction'] ? $this->getHkdRate(
            $data['exchange_rate'][$data['lists']['currency_code_org']],
            $data['lists']['other_transaction']
        ) : 0;

        $data['lists']['gross_profit'] = $this->getGrossProfit($data['lists']);

        return view('erpOrders/ordersEdit', $data);
    }

    //取得港幣匯率(取至小數點第二位)
    public function getHkdRate(float $rate, float $num): float
    {
        return substr(sprintf(" % .3f", $num * $rate), 0, -1);
    }

    //取得港幣匯率(取至小數點第二位)
    public function getGrossProfit(array $lists): float
    {
        $keys = [
            'order_price_hkd',
            'purchase_shipping_fee_hkd',
            'product_cost_hkd',
            'first_mile_shipping_fee_hkd',
            'first_mile_tariff_hkd',
            'last_mile_shipping_fee_hkd',
            'paypal_fee_hkd',
            'transaction_fee_hkd',
            'fba_fee_hkd',
            'other_transaction_hkd',
        ];

        $sum = $lists[$keys[0]];
        unset($keys[0]);
        $keys = array_values($keys);

        foreach ($keys as $v) {
            $sum -= $lists[$v];
        }

        return $sum;
    }

    public function getChangeLog($productID)
    {
        return $this->systemChangeLog->from('system_changelogs as s')
            ->select(
                's.field_name',
                's.original_value',
                's.new_value',
                's.created_at',
                'u.user_name'
            )
            ->join('users as u', 's.created_by', '=', 'u.id')//TODO
            ->where('s.reference_id', $productID)
            ->where('s.created_by', Auth::id())
            ->orderBy('s.created_at', 'desc')
            ->get();
    }

    public function editOrderDetail(): \Illuminate\Http\JsonResponse
    {
        $productID = request()->route('id');
        $inputs = request()->except(['product_id']);
        $modifiedColumn = $inputs ? implode(',', array_keys($inputs)) : null;

        DB::beginTransaction();
        try {
            //get original order_products value
            $oldValues = $this->orderProduct->selectRaw($modifiedColumn)->find($productID)->toArray();
            $UpdatedData = array_diff($inputs, $oldValues);

            //update order_product
            $this->orderProduct->where('id', $productID)->update($UpdatedData);
            //record on log
            foreach ($UpdatedData as $k => $v) {
                $this->systemChangeLog->insert(
                    [
                        'menu_path' => '/orders/edit',
                        'event_type' => 'U',
                        'table_name' => 'order_products',
                        'reference_id' => $productID,
                        'field_name' => $k,
                        'original_value' => $oldValues[$k],
                        'new_value' => $v,
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d h:i:s')
                    ]
                );
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json(['msg' => 'failed!', 'status' => 'failed']);
        }
        return response()->json(['msg' => 'UPDATED!', 'status' => 'success', 'icon' => 'success']);
    }

    public function checkEditQualification(): \Illuminate\Http\JsonResponse
    {
        $supplier = request()->input('supplier');

        $formattedDate = date('Ym', strtotime(request()->input('shipped_date')));

        $formattedReportDate = DB::raw("DATE_FORMAT(report_date,'%Y%m')");

        $hasMonthlyBilling = $this->billingStatement->where("active", 1)
            ->where($formattedReportDate, $formattedDate)
            ->where("client_code", $supplier)
            ->count();

        $msg = "Order information is 'frozen' once a monthly billing statement is created. You can view order through the front end. However can not adjust it, except by contacting sales to delete the billing statement.";

        if ($hasMonthlyBilling) {
            return response()->json(['msg' => $msg, 'status' => 'failed', 'icon' => 'error']);
        }

        return response()->json(['msg' => 'UPDATED!', 'status' => 'success', 'icon' => 'success']);
    }

    public function checkRate(): \Illuminate\Http\JsonResponse
    {
        $shippedDate = request()->input('shipped_date') ?? null;
        $currency = request()->input('currency') ?? null;

        $msg = 'Currency Exchange Rate Not Found Error';

        if (!$shippedDate || !$currency) {
            return response()->json(['msg' => $msg, 'status' => 'failed']);
        }

        //取得匯率
        $formattedQuotedDate = DB::raw("date_format(quoted_date,'%Y%m')");

        $formattedShippedDate = date("Ym", strtotime($shippedDate));

        $exchangeRate = $this->exchangeRate->select('base_currency', 'exchange_rate')
            ->active()
            ->wherein('base_currency', [$currency, 'RMB'])
            ->where($formattedQuotedDate, $formattedShippedDate)
            ->count();

        if ($exchangeRate !== 2) {
            return response()->json(['msg' => $msg, 'status' => 'failed']);
        }
        return response()->json(['status' => 'success']);
    }
}

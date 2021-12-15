<?php

namespace App\Http\Controllers;

use App\Exports\ErpOrderSampleExport;
use App\Http\Requests\ErpOrder\BulkUpdateRequest;
use App\Imports\BulkUpdateImport;
use App\Models\BillingStatement;
use App\Models\ExchangeRate;
use App\Models\Order;
use App\Models\OrderBulkUpdate;
use App\Models\OrderProduct;
use App\Models\RmaRefundList;
use App\Models\SystemChangeLog;
use App\Repositories\OrderRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class ErpOrdersController extends Controller
{
    private const FILE_EXPECTED_HEADER = 'platform';
    private RmaRefundList $rmaRefundList;
    private Order $order;
    private ExchangeRate $exchangeRate;
    private SystemChangeLog $systemChangeLog;
    private BillingStatement $billingStatement;
    private OrderProduct $orderProduct;

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
        $data['shipped_date'] = $request->shipped_date ?? null;
        $data['erp_order_ID'] = $request->erp_order_ID ?? null;
        $data['sku'] = $request->sku ?? null;
        $data['warehouse_order_id'] = $request->warehouse_order_id ?? null;
        $data['supplier'] = $request->supplier ?? null;

        $data['lists'] = $this->rmaRefundList->select(
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
        )
            ->when(
                $request->shipped_date,
                fn ($q, $shippedDate) => $q->whereBetween(
                    'warehouse_ship_date',
                    [
                        Carbon::parse($shippedDate)->startOfDay()->toDateTimeString(),
                        Carbon::parse($shippedDate)->endOfDay()->toDateTimeString(),
                    ]
                )
            )
            ->when($request->erp_order_ID, fn ($q) => $q->where('refrence_no_platform', $request->erp_order_ID))
            ->when($request->sku, fn ($q) => $q->where('product_sku', $request->sku))
            ->when($request->warehouse_order_id, fn ($q) => $q->where('warehouse_ref_no', $request->warehouse_order_id))
            ->when($request->supplier, fn ($q) => $q->where('pc_name', $request->supplier))
            ->paginate(100)
            ->appends($request->query());

        return view('erpOrders.refundSearch', ['data' => $data]);
    }

    public function ordersSearchView(Request $request)
    {
        $data['lists'] = [];
        $data['erp_order_id'] = $request->erp_order_id;
        $data['shipped_date_from'] = $request->shipped_date_from;
        $data['shipped_date_to'] = $request->shipped_date_to;
        $data['sku'] = $request->sku;
        $data['supplier'] = $request->supplier;

        if (count($request->all())) {
            $data['lists'] = $this->order->join('order_products', function ($join) {
                $join->on('order_products.order_code', '=', 'orders.order_code')
                    ->where('order_products.active', 1);
            })
                ->join('order_sku_cost_details', function ($join) {
                    $join->on('order_products.order_code', '=', 'order_sku_cost_details.reference_no');
                    $join->on('order_products.sku', '=', 'order_sku_cost_details.product_barcode');
                })
                ->select(
                    'orders.platform',
                    'orders.seller_id AS acc_nick_name',
                    'orders.platform_user_name AS acc_name',
                    'orders.order_type',
                    'orders.reference_no AS erp_order_id',
                    'orders.order_code AS package_id',
                    DB::raw("date_format(orders.ship_time,'%Y-%m-%d') as 'shipped_date'"),
                    DB::raw("CONCAT(orders.warehouse_code,'[',orders.warehouse_name,']') AS 'warehouse'"),
                    'order_products.sku',
                    'order_products.supplier',
                    'order_products.id as product_id',
                    'order_products.sales_amount AS order_price',
                    'order_products.updated_at',
                    'order_sku_cost_details.site_id',
                    'order_sku_cost_details.currency_code_org'
                )
                ->when($request->erp_order_id, fn ($q) => $q->where('orders.reference_no', $request->erp_order_id))
                ->when($request->sku, fn ($q) => $q->where('order_products.sku', $request->sku))
                ->when($request->supplier, fn ($q) => $q->where('order_products.supplier', $request->supplier))
                ->when(
                    $request->shipped_date_from && $request->shipped_date_to,
                    fn ($q) => $q->whereBetween(
                        'orders.ship_time',
                        [
                            Carbon::parse($request->shipped_date_from)->startOfDay()->toDateTimeString(),
                            Carbon::parse($request->shipped_date_to)->endOfDay()->toDateTimeString(),
                        ]
                    )
                )
                ->paginate(100)
                ->appends($request->query());
        }

        return view('erpOrders.ordersSearch', ['data' => $data]);
    }

    public function editOrders(Request $request)
    {
        $data['lists'] = [];
        $data['platform'] = $request->platform ?? null;
        $data['acc_nick_name'] = $request->acc_nick_name ?? null;
        $data['acc_name'] = $request->acc_name ?? null;
        $data['site_id'] = $request->site_id ?? null;
        $data['shipped_date'] = $request->shipped_date ?? null;
        $data['package_id'] = $request->package_id ?? null;
        $data['erp_order_id'] = $request->erp_order_id ?? null;
        $data['sku'] = $request->sku ?? null;
        $data['order_price'] = $request->order_price ?? null;
        $data['supplier'] = $request->supplier ?? null;
        $data['warehouse'] = $request->warehouse ?? null;

        if (count(request()->all())) {
            $data['lists'] = app(OrderRepository::class)->getOrderDetail($data);
        }

        $data['sys_logs'] = $this->getChangeLog($data['lists']['product_id']);

        //取得匯率
        $data['exchange_rate'] = $this->exchangeRate->select('base_currency', 'exchange_rate')
            ->active()
            ->wherein('base_currency', [$data['lists']['currency_code_org'], 'RMB'])
            ->where(
                DB::raw("date_format(quoted_date,'%Y%m')"),
                Carbon::parse($data['shipped_date'])->format('Ym')
            )
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

    //取得港幣匯率(取至小數點第二位)

    public function getHkdRate(float $rate, float $num): float
    {
        return substr(sprintf(" % .3f", $num * $rate), 0, -1);
    }

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

    public function editOrderDetail(): JsonResponse
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
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json(['msg' => 'failed!', 'status' => 'failed']);
        }
        return response()->json(['msg' => 'UPDATED!', 'status' => 'success', 'icon' => 'success']);
    }

    public function checkEditQualification(): JsonResponse
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

    public function checkRate(): JsonResponse
    {
        $shippedDate = request()->input('shipped_date') ?? null;
        $currency = request()->input('currency') ?? null;

        $msg = 'Currency Exchange Rate Not Found Error';

        if (!$shippedDate || !$currency) {
            return response()->json(['msg' => $msg, 'status' => 'failed']);
        }

        //取得匯率
        $exchangeRate = $this->exchangeRate->select('base_currency', 'exchange_rate')
            ->active()
            ->wherein('base_currency', [$currency, 'RMB'])
            ->where(
                DB::raw("date_format(quoted_date,'%Y%m')"),
                Carbon::parse($shippedDate)->format('Ym')
            )
            ->count();

        if ($exchangeRate !== 2) {
            return response()->json(['msg' => $msg, 'status' => 'failed']);
        }
        return response()->json(['status' => 'success']);
    }

    public function bulkUpdateView(Request $request)
    {
        $data['lists'] = [];
        if (count($request->all())) {
            $data['lists'] = OrderBulkUpdate::from('order_bulk_updates as o')
                ->join('users as u', 'u.id', '=', 'o.created_by')
                ->select('u.user_name', 'o.*')
                ->when($request->order_id, fn ($q) => $q->where('o.platform_order_id', $request->order_id))
                ->when($request->status_type, fn ($q) => $q->where('o.execution_status', $request->status_type))
                ->when(
                    $request->upload_date,
                    fn ($q) => $q->whereBetween(
                        'o.created_at',
                        [
                            Carbon::parse($request->upload_date)->startOfDay()->toDateTimeString(),
                            Carbon::parse($request->upload_date)->endOfDay()->toDateTimeString(),
                        ]
                    )
                )
                ->orderBy('o.id', 'desc')
                ->paginate(50);
        }

        return view('erpOrders.bulkUpdate', $data);
    }

    public function bulkUpdate(BulkUpdateRequest $request)
    {
        Excel::queueImport(
            new BulkUpdateImport(
                Auth::id(),
                now()->format('YmdHisu')
            ),
            $request->file('file')
        )->allOnQueue('queue_excel')->delay(1);
    }

    public function exportSample()
    {
        return (new ErpOrderSampleExport)->download('bulkUpdateSampleFile.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function ajaxValidateFileHeadingRow(BulkUpdateRequest $request): JsonResponse
    {
        //validate excel title
        $headings = (new HeadingRowImport(2))->toCollection($request->file('file')) ?
            (new HeadingRowImport(2))->toCollection($request->file('file'))->collapse()->collapse() : null;

        if (!$headings->isEmpty()) {
            if ($headings[0] === self::FILE_EXPECTED_HEADER) {
                return response()->json(
                    [
                        'status' => 200,
                        'msg' => "OK"
                    ]
                );
            }
        }

        return response()->json(
            [
                'status' => 200,
                'msg' => "The second row in the table is the 'EN' field name of the table.
                            Starting from the third row is the data in the table ."
            ]
        );
    }
}

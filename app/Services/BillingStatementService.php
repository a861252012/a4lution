<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\OrderProduct;
use App\Support\ERPRequester;
use App\Models\CommissionSetting;
use App\Models\ExtraordinaryItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Repositories\OrderRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\ExchangeRateRepository;
use App\Repositories\OrderProductRepository;
use App\Repositories\AmazonReportListRepository;
use App\Repositories\BillingStatementRepository;
use App\Repositories\CommissionSettingRepository;
use App\Repositories\FirstMileShipmentFeeRepository;
use App\Http\Requests\BillingStatement\AjaxStoreRequest;

class BillingStatementService
{
    private $billingStatementRepo;

    public function __construct(BillingStatementRepository $billingStatementRepo)
    {
        $this->billingStatementRepo = $billingStatementRepo;
    }

    // TODO: do all repository
    public function create(AjaxStoreRequest $request)
    {
        $reportDate = Carbon::parse($request->report_date)->format('Y-m-d');
        $clientCode = $request->client_code;

        //4-1 Commission Rate
        $exchangeRate = (new ExchangeRateRepository)->getByQuotedDate($reportDate);
        if ($exchangeRate->isEmpty()) {
            Log::error('uploadFileToS3_failed: exchangeRate is empty');
            return;
        }


        if (!(new CommissionSettingRepository)->findByClientCode($clientCode)) {
            Log::error('uploadFileToS3_failed: commissionSetting is empty');
            return;
        }

        //4-2 Expenses Breakdown start

        //getReportFees
        $supplierCode = (new CustomerRepository)->findByClientCode($clientCode)->supplier_code;

        $getSupplierName = app(ERPRequester::class)->send(
            config('services.erp.wmsUrl'),
            'getSupplierInfo',
            ["supplierCode" => $supplierCode]
        );

        $supplierName = $getSupplierName['data']['supplierName'] ?? null;

        $orderRepository = new OrderRepository();
        $clientReportFees = $orderRepository->getReportFees(
            $reportDate,
            $clientCode,
            $supplierName,
            true
        );

        $fees['clientShippingFeeHKD'] = $clientReportFees ? $clientReportFees[0]->shipping_fee_hkd : 0;
        $clientPlatformFeeHKD = $clientReportFees ? $clientReportFees[0]->platform_fee_hkd : 0;
        $fees['clientFBAFeesHKD'] = $clientReportFees ? $clientReportFees[0]->FBA_fees_hkd : 0;
        $fees['clientOtherTransactionFeesHKD'] = $clientReportFees ? $clientReportFees[0]->other_transaction_fees_hkd
            : 0;
        $fees['clientPlatformFeeHKD'] = (float)$clientPlatformFeeHKD + (float)$fees['clientOtherTransactionFeesHKD'];

        $a4ReportFees = $orderRepository->getReportFees(
            $reportDate,
            $clientCode,
            $supplierName,
            false
        );

        $fees['a4ShippingFeeHKD'] = $a4ReportFees ? $a4ReportFees[0]->shipping_fee_hkd : 0;
        $a4PlatformFeeHKD = $a4ReportFees ? $a4ReportFees[0]->platform_fee_hkd : 0;
        $fees['a4FBAFeesHKD'] = $a4ReportFees ? $a4ReportFees[0]->FBA_fees_hkd : 0;
        $fees['a4OtherTransactionFeesHKD'] = $a4ReportFees ? $a4ReportFees[0]->other_transaction_fees_hkd : 0;
        $fees['a4PlatformFeeHKD'] = (float)$a4PlatformFeeHKD + (float)$fees['a4OtherTransactionFeesHKD'];

        //getAccountRefund
        $clientRefundFees = $orderRepository->getAccountRefund(
            $reportDate,
            $clientCode,
            $supplierName,
            true
        );

        $clientRefundFees = $clientRefundFees ? $clientRefundFees[0]->refund_amount_hkd : 0;

        $a4RefundFees = $orderRepository->getAccountRefund(
            $reportDate,
            $clientCode,
            $supplierName,
            false
        );

        $a4RefundFees = $a4RefundFees ? $a4RefundFees[0]->refund_amount_hkd : 0;

        //getAccountResend
        $clientAccountResend = $orderRepository->getAccountResend(
            $reportDate,
            $clientCode,
            $supplierName,
            true
        );

        $clientAccountResend = $clientAccountResend ? $clientAccountResend[0]->total_sales_hkd : 0;

        $a4AccountResend = $orderRepository->getAccountResend(
            $reportDate,
            $clientCode,
            $supplierName,
            false
        );

        $a4AccountResend = $a4AccountResend ? $a4AccountResend[0]->total_sales_hkd : 0;

        $clientAccountAmazonTotal = $orderRepository->getAccountAmzTotal(
            $reportDate,
            $clientCode,
            $supplierName,
            true
        );

        $clientAccountAmazonTotal = $clientAccountAmazonTotal ?
            $clientAccountAmazonTotal[0]->amazon_total_hkd : 0;

        $a4AccountAmazonTotal = $orderRepository->getAccountAmzTotal(
            $reportDate,
            $clientCode,
            $supplierName,
            false
        );

        $a4AccountAmazonTotal = $a4AccountAmazonTotal ? $a4AccountAmazonTotal[0]->amazon_total_hkd : 0;

        $fees['a4_account_refund_and_resend'] = abs((float)$a4RefundFees + (float)$a4AccountResend + (float)$a4AccountAmazonTotal);

        $fees['client_account_refund_and_resend'] = abs((float)$clientAccountAmazonTotal + (float)$clientAccountResend + (float)$clientRefundFees);

        //getAccountMiscellaneous
        $amazonReportListRepository = new AmazonReportListRepository();

        $clientAccountMiscellaneous = $amazonReportListRepository->getAccountMiscellaneous(
            $reportDate,
            $clientCode,
            $supplierName,
            true
        );

        $fees['clientAccountMiscellaneous'] = $clientAccountMiscellaneous
            ? -1 * abs($clientAccountMiscellaneous[0]->Miscellaneous) : 0;

        $a4AccountMiscellaneous = $amazonReportListRepository->getAccountMiscellaneous(
            $reportDate,
            $clientCode,
            $supplierName,
            false
        );

        $fees['a4AccountMiscellaneous'] = $a4AccountMiscellaneous ?
            -1 * abs($a4AccountMiscellaneous[0]->Miscellaneous) : 0;

        //getAccountAds
        $clientAccountAds = $orderRepository->getAccountAds(
            $reportDate,
            $clientCode,
            $supplierName,
            true
        );

        $fees['clientAccountAds'] = $clientAccountAds ? $clientAccountAds[0]->ad : 0;

        $a4AccountAds = $orderRepository->getAccountAds(
            $reportDate,
            $clientCode,
            $supplierName,
            false
        );

        $fees['a4AccountAds'] = $a4AccountAds ? $a4AccountAds[0]->ad : 0;

        //getAccountMarketingAndPromotion
        $clientAccountMarketingAndPromotion = $amazonReportListRepository->getAccountMarketingAndPromotion(
            $reportDate,
            $clientCode,
            $supplierName,
            true
        );

        $fees['a4AccountMarketingAndPromotion'] = $clientAccountMarketingAndPromotion ?
            $clientAccountMarketingAndPromotion[0]->Miscellaneous : 0;

        $a4AccountMarketingAndPromotion = $amazonReportListRepository->getAccountMarketingAndPromotion(
            $reportDate,
            $clientCode,
            $supplierName,
            false
        );

        $fees['clientAccountMarketingAndPromotion'] = $a4AccountMarketingAndPromotion ?
            $a4AccountMarketingAndPromotion[0]->Miscellaneous : 0;

        //getAccountFbaStorageFee
        $clientAccountFbaStorageFee = $orderRepository->getAccountFbaStorageFee(
            $reportDate,
            $clientCode,
            $supplierName,
            true
        );

        $fees['clientAccountFbaStorageFee'] = $clientAccountFbaStorageFee ? $clientAccountFbaStorageFee[0]->storage_fee_hkd_sum : 0;

        $a4AccountFbaStorageFee = $orderRepository->getAccountFbaStorageFee(
            $reportDate,
            $clientCode,
            $supplierName,
            false
        );

        $fees['a4AccountFbaStorageFee'] = $a4AccountFbaStorageFee ? $a4AccountFbaStorageFee[0]->storage_fee_hkd_sum : 0;

        //4-2 Expenses Breakdown end

        //4-3 prepare data for step3,for insert billing statement
        $billingItems['sales_tax_handling'] = 0;
        $billingItems['report_date'] = $reportDate;
        $billingItems['client_code'] = $clientCode;

        $totalSalesOrders = $orderRepository->getTotalSalesOrders(
            $reportDate,
            $clientCode
        );

        $billingItems['total_sales_orders'] = $totalSalesOrders ? $totalSalesOrders[0]->total_sales_orders : 0;

        $sumOfSalesAmount = $orderRepository->getSumOfSalesAmount(
            $reportDate,
            $clientCode
        );

        $billingItems['total_sales_amount'] = $sumOfSalesAmount ? round($sumOfSalesAmount[0]->total_sales_hkd, 2) : 0;

        $fees['extraordinary_item'] = ExtraordinaryItem::where('report_date', $reportDate)
            ->where('client_code', $clientCode)
            ->groupBy('client_code', 'report_date')
            ->get()
            ->sum('item_amount');

        $billingItems['extraordinary_item'] = $fees['extraordinary_item'] ?
            round(-1 * abs($fees['extraordinary_item']), 2) : 0;

        $totalExpensesKeys = [
            "clientShippingFeeHKD",
            "a4ShippingFeeHKD",
            "clientFBAFeesHKD",
            "a4FBAFeesHKD",
            "clientAccountFbaStorageFee",
            "a4AccountFbaStorageFee",
            "clientPlatformFeeHKD",
            "a4PlatformFeeHKD",
            "client_account_refund_and_resend",
            "a4_account_refund_and_resend",
            "clientAccountMiscellaneous",
            "a4AccountMiscellaneous",
        ];

        $billingItems['total_expenses'] = $this->getSumValue($fees, $totalExpensesKeys);
        $billingItems['sales_gp'] = $billingItems['total_sales_amount'] - $billingItems['total_expenses'];
        $billingItems['sales_credit'] = round(
            $billingItems['total_sales_amount'] - $fees['a4_account_refund_and_resend'],
            2
        );

        //4-1 Commission Rate

        //get comission setting
        $totalSalesAmount = $billingItems['sales_credit'] - $clientRefundFees - $clientAccountResend;
        $commissionRate = $this->getCommissionRate(
            $clientCode,
            $reportDate,
            (float)$totalSalesAmount
        );

        $tieredParam = $billingItems['total_sales_amount'] - $fees['a4_account_refund_and_resend']
            - $fees['client_account_refund_and_resend'];
        $billingItems['commission_type'] = $commissionRate['type'] ?? null;
        $billingItems['avolution_commission'] = $this->getAvolutionCommission(
            $clientCode,
            $reportDate,
            $tieredParam,
            $commissionRate
        );

        //final_credit
        $billingItems['created_at'] = date('Y-m-d h:i:s');
        $billingItems['created_by'] = Auth::id();
        $billingItems['active'] = 1;

        $firstMileShipmentFeeRepository = new FirstMileShipmentFeeRepository();

        $getFbaStorageFeeInvoices = $firstMileShipmentFeeRepository->getFbaStorageFeeInvoice(
            $reportDate,
            $clientCode
        );

        $billingItems['fba_storage_fee_invoice'] = round(
            collect($getFbaStorageFeeInvoices)->sum('total'),
            2
        );

        $billingItems['a4_account_logistics_fee'] = round($fees['a4ShippingFeeHKD'], 2);
        $billingItems['client_account_logistics_fee'] = round($fees['clientShippingFeeHKD'], 2);
        $billingItems['a4_account_fba_fee'] = round($fees['a4FBAFeesHKD'], 2);
        $billingItems['client_account_fba_fee'] = round($fees['clientFBAFeesHKD'], 2);
        $billingItems['a4_account_fba_storage_fee'] = round($fees['a4AccountFbaStorageFee'], 2);
        $billingItems['client_account_fba_storage_fee'] = round($fees['clientAccountFbaStorageFee'], 2);
        $billingItems['a4_account_platform_fee'] = round($fees['a4PlatformFeeHKD'], 2);
        $billingItems['client_account_platform_fee'] = round($fees['clientPlatformFeeHKD'], 2);
        $billingItems['a4_account_refund_and_resend'] = round($fees['a4_account_refund_and_resend'], 2);
        $billingItems['client_account_refund_and_resend'] = round($fees['client_account_refund_and_resend'], 2);
        $billingItems['a4_account_miscellaneous'] = round($fees['a4AccountMiscellaneous'], 2);
        $billingItems['client_account_miscellaneous'] = round($fees['clientAccountMiscellaneous'], 2);
        $billingItems['a4_account_advertisement'] = round($fees['a4AccountAds'], 2);
        $billingItems['client_account_advertisement'] = round($fees['clientAccountAds'], 2);
        $billingItems['client_account_marketing_and_promotion'] = round($fees['clientAccountMarketingAndPromotion'], 2);
        $billingItems['a4_account_marketing_and_promotion'] = round($fees['a4AccountMarketingAndPromotion'], 2);

        //count opexInvoice value
        $opexInvoiceKeys = [
            'a4_account_logistics_fee',
            'client_account_logistics_fee',
            'a4_account_platform_fee',
            'a4_account_fba_fee',
            'a4_account_fba_storage_fee',
            'a4_account_advertisement',
            'a4_account_marketing_and_promotion',
            'sales_tax_handling',
            'a4_account_miscellaneous',
            'avolution_commission',
            'extraordinary_item'
        ];

        if ($billingItems['client_code'] === 'G73A') {
            $opexInvoiceKeys = collect($opexInvoiceKeys)->forget('client_account_logistics_fee')->all();
        }

        $billingItems['opex_invoice'] = $this->getSumValue($billingItems, $opexInvoiceKeys);

        $billingItems['final_credit'] = round(
            $billingItems['sales_credit'] - $billingItems['opex_invoice']
            - $billingItems['a4_account_fba_storage_fee'] - $billingItems['a4_account_fba_fee'],
            2
        );

        $this->billingStatementRepo->create($billingItems);
    }

    public function getSumValue(array $fees, array $keys = []): float
    {
        $feesCollection = collect($fees);

        if ($keys) {
            $feesCollection = collect($fees)->only($keys);
        }

        return $feesCollection->map(fn ($val) => round($val, 2))->sum();
    }

    public function getCommissionRate(string $clientCode, string $reportDate, float $totalSalesAmount)
    {
        $commissionSetting = new CommissionSetting();
        $orderProductRepository = new OrderProductRepository();

        $settings = $commissionSetting->where('client_code', $clientCode)->first();

        if ($settings->is_sku_level_commission === 'T') {
            //check unmatched record
            $haveUnmatchedRecord = $orderProductRepository->checkUnmatchedRecord($clientCode, $reportDate);
            if (!empty($haveUnmatchedRecord)) {
                return [
                    'msg' => 'SKU-level commissions list need to match up with SKUs',
                    'status' => 'error',
                ];
            }

            $orders = $orderProductRepository->getFitOrder($clientCode, $reportDate);
            if ($orders) {
                foreach ($orders as $item) {
                    $thisOrder = OrderProduct::find($item->id);

                    $thisOrder->sku_commission_rate = $this->getSkuCommissionRate(
                        $item,
                        (float)$item->selling_price,
                        (float)$item->threshold
                    );
                    $thisOrder->sku_commission_amount = (float)$item->selling_price * (float)$thisOrder->sku_commission_rate;
                    $thisOrder->sku_commission_computed_at = date('Y-m-d h:i:s');
                    $thisOrder->save();
                }
                return ['type' => 'sku', 'value' => '0', 'status' => 'success'];
            }
        }

        //check if commission rate type is promotion
        $maxDiscountRate = $orderProductRepository->getMaxDiscountRate($clientCode, $reportDate);

        if ((float)$settings->promotion_threshold >= (float)$maxDiscountRate) {
            return ['type' => 'promotion', 'value' => $settings->tier_promotion, 'status' => 'success'];
        }

        //check if commission rate type is tiered
        if ($settings->tier === 'T') {
            return $this->getTieredInfo($clientCode, $totalSalesAmount);
        }
        return ['type' => 'tiered', 'value' => $settings->basic_rate, 'status' => 'success'];
    }

    public function getAvolutionCommission(
        string $clientCode,
        string $shipDate,
        float  $tieredParam,
        array  $commissionRate
    ) {
        switch ($commissionRate['type']) {
            case 'sku':
                $orderProductRepository = new OrderProductRepository();

                return round($orderProductRepository->getSkuAvolutionCommission($clientCode, $shipDate), 2);
            case 'promotion':
                return $commissionRate['value'];
            case 'tiered':
                return round($tieredParam * $commissionRate['value'], 2);
        }
    }

    public function getSkuCommissionRate(object $item, float $sellingPrice, float $threshold)
    {
        if ($sellingPrice > $threshold) {
            return $item->upper_bound_rate;
        }

        return $item->basic_rate;
    }

    public function getTieredInfo(string $clientCode, float $totalSalesAmount): array
    {
        $setting = CommissionSetting::where('client_code', $clientCode)->first();

        if (!empty($setting) & $totalSalesAmount >= $setting->tier_1_threshold) {
            $newLevel = 1;
            for ($i = 1; $i <= 4; $i++) {
                $key = "tier_{$i}_threshold";
                $val = $setting->$key;
                if ($totalSalesAmount >= $val) {
                    $newLevel = $i;
                }
            }
            //如有amount則先取amount
            $amountKey = "tier_{$newLevel}_amount";
            if (!empty((float)$setting->$amountKey)) {
                return ['type' => 'tiered', 'value' => $setting->$amountKey, 'status' => 'success'];
            }

            $rateKey = "tier_{$newLevel}_rate";

            return ['type' => 'tiered', 'value' => $setting->$rateKey, 'status' => 'success'];
        }

        return ['type' => 'tiered', 'value' => $setting->basic_rate, 'status' => 'success'];
    }
}

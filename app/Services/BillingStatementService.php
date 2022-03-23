<?php

namespace App\Services;

use App\Constants\CommissionConstant;
use App\Http\Requests\BillingStatement\AjaxStoreRequest;
use App\Models\CommissionSetting;
use App\Repositories\AmazonDateRangeReportRepository;
use App\Repositories\BillingStatementRepository;
use App\Repositories\ContinStorageFeeRepository;
use App\Repositories\ExtraordinaryRepository;
use App\Repositories\FirstMileShipmentFeeRepository;
use App\Repositories\OrderProductRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PlatformAdFeeRepository;
use App\Repositories\ReturnHelperChargeRepository;
use App\Repositories\RmaRefundListRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\SellerAccountRepository;
use App\Repositories\WfsStorageFeeRepository;
use App\Support\Calculation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BillingStatementService
{
    private BillingStatementRepository $billingStatementRepo;
    private AmazonDateRangeReportRepository $amzDateRangeRepo;
    private OrderProductRepository $orderProductRepo;
    private OrderRepository $orderRepo;
    private Calculation $calculation;

    public function __construct(
        BillingStatementRepository      $billingStatementRepo,
        AmazonDateRangeReportRepository $amzDateRangeRepo,
        OrderProductRepository          $orderProductRepo,
        OrderRepository                 $orderRepo,
        Calculation                     $calculation
    ) {
        $this->billingStatementRepo = $billingStatementRepo;
        $this->amzDateRangeRepo = $amzDateRangeRepo;
        $this->orderProductRepo = $orderProductRepo;
        $this->orderRepo = $orderRepo;
        $this->calculation = $calculation;
    }

    public function create(AjaxStoreRequest $request)
    {
        $reportDate = Carbon::parse($request->report_date)->format('Y-m-d');
        $clientCode = $request->client_code;
        $sellerAccount = app(SellerAccountRepository::class)->getSellerAccount();

        //getReportFees
        $clientReportFees = $this->orderRepo->getReportFees(
            $reportDate,
            $clientCode,
            $sellerAccount,
            false
        );

        $fees['clientLogisticFeeHKD'] = (float)optional($clientReportFees)->logistics_fee_hkd;
        $fees['clientPlatformFeeHKD'] =  (float)optional($clientReportFees)->platform_fee_hkd;
        $fees['clientFBAFeesHKD'] = (float)optional($clientReportFees)->FBA_fees_hkd;

        $a4ReportFees = $this->orderRepo->getReportFees(
            $reportDate,
            $clientCode,
            $sellerAccount,
            true
        );

        $fees['a4LogisticFeeHKD'] = (float)optional($a4ReportFees)->logistics_fee_hkd;
        $fees['a4PlatformFeeHKD'] = (float)optional($a4ReportFees)->platform_fee_hkd;
        $fees['a4FBAFeesHKD'] = (float)optional($a4ReportFees)->FBA_fees_hkd;

        //getAccountRefund
        $clientRefundFees = abs(app(RmaRefundListRepository::class)->getAccountRefund(
            $reportDate,
            $clientCode,
            $sellerAccount,
            false
        ));

        $a4RefundFees = abs(app(RmaRefundListRepository::class)->getAccountRefund(
            $reportDate,
            $clientCode,
            $sellerAccount,
            true
        ));

        //getAccountResend
        $clientAccountResend = abs($this->orderRepo->getAccountResend(
            $reportDate,
            $clientCode,
            $sellerAccount,
            false
        ));

        $a4AccountResend = abs($this->orderRepo->getAccountResend(
            $reportDate,
            $clientCode,
            $sellerAccount,
            true
        ));

        $clientAmazonTotal = abs($this->amzDateRangeRepo->getTotalAmount(
            $reportDate,
            $clientCode,
            $sellerAccount,
            false
        ));

        $a4AccountAmazonTotal = abs($this->amzDateRangeRepo->getTotalAmount(
            $reportDate,
            $clientCode,
            $sellerAccount,
            true
        ));

        $fees['a4_account_refund_and_resend'] = $a4AccountAmazonTotal + $a4RefundFees + $a4AccountResend;

        $fees['client_account_refund_and_resend'] = $clientAmazonTotal + $clientRefundFees + $clientAccountResend;

        //getAccountMiscellaneous
        $fees['clientAccountMiscellaneous'] = -1 * abs($this->amzDateRangeRepo->getAccountMiscellaneous(
            $reportDate,
            $clientCode,
            $sellerAccount,
            false
        )->Miscellaneous);

        $fees['a4AccountMiscellaneous'] = -1 * abs($this->amzDateRangeRepo->getAccountMiscellaneous(
            $reportDate,
            $clientCode,
            $sellerAccount,
            true
        )->Miscellaneous);

        //getAccountAds
        $fees['clientAccountAds'] = app(PlatformAdFeeRepository::class)->getAccountAd(
            $reportDate,
            $clientCode,
            $sellerAccount,
            false
        );

        $fees['a4AccountAds'] = app(PlatformAdFeeRepository::class)->getAccountAd(
            $reportDate,
            $clientCode,
            $sellerAccount,
            true
        );

        //getAccountMarketingAndPromotion
        $fees['clientAccountMarketingAndPromotion'] = abs($this->amzDateRangeRepo->getAccountMarketingAndPromotion(
            $reportDate,
            $clientCode,
            $sellerAccount,
            false
        ));

        $fees['a4AccountMarketingAndPromotion'] = abs($this->amzDateRangeRepo->getAccountMarketingAndPromotion(
            $reportDate,
            $clientCode,
            $sellerAccount,
            true
        ));

        //getAccountFbaStorageFee
        $storageFeeHKD = app(WfsStorageFeeRepository::class)->getStorageFee($reportDate, $clientCode);

        $fees['clientAccountFbaStorageFee'] = (float)optional($this->orderRepo->getAccountFbaStorageFee(
            $reportDate,
            $clientCode,
            $sellerAccount,
            false
        )[0])->storage_fee_hkd_sum;

        $fees['a4AccountFbaStorageFee'] = (float)optional($this->orderRepo->getAccountFbaStorageFee(
            $reportDate,
            $clientCode,
            $sellerAccount,
            true
        )[0])->storage_fee_hkd_sum + $storageFeeHKD;

        //4-2 Expenses Breakdown end

        //4-3 prepare data for step3,for insert billing statement
        $billingItems['sales_tax_handling'] = 0;
        $billingItems['report_date'] = $reportDate;
        $billingItems['client_code'] = $clientCode;

        $billingItems['total_sales_orders'] = $this->orderRepo->getTotalSalesOrders(
            $reportDate,
            $clientCode
        );

        $billingItems['total_sales_amount'] =  $this->calculation->numberFormatPrecision(
            $this->orderRepo->getSumOfSalesAmount($reportDate, $clientCode),
            4
        );

        $billingItems['total_unit_sold'] = $this->orderRepo->getTotalUnitSold(
            $reportDate,
            $clientCode
        );

        $fees['extraordinary_item'] = app(ExtraordinaryRepository::class)->getExtraordinaryItem(
            $reportDate,
            $clientCode
        );

        $billingItems['extraordinary_item'] = $this->calculation->numberFormatPrecision(
            -1 * abs($fees['extraordinary_item']),
            4
        );

        $billingItems['a4_account_sales_orders'] = $this->orderRepo->getSalesOrder(
            $reportDate,
            $clientCode,
            $sellerAccount,
            true
        );

        $billingItems['client_account_sales_orders'] = $this->orderRepo->getSalesOrder(
            $reportDate,
            $clientCode,
            $sellerAccount,
            false
        );

        $billingItems['a4_account_sales_amount'] = $this->calculation->numberFormatPrecision(
            $this->orderRepo->getSalesAmount(
                $reportDate,
                $clientCode,
                $sellerAccount,
                true
            ),
            4
        );

        $billingItems['client_account_sales_amount'] = $this->calculation->numberFormatPrecision(
            $this->orderRepo->getSalesAmount(
                $reportDate,
                $clientCode,
                $sellerAccount,
                false
            ),
            4
        );

        $billingItems['a4_account_logistics_fee'] =  $this->calculation->numberFormatPrecision(
            $fees['a4LogisticFeeHKD'],
            4
        );
        $billingItems['client_account_logistics_fee'] = $this->calculation->numberFormatPrecision(
            $fees['clientLogisticFeeHKD'],
            4
        );
        $billingItems['a4_account_fba_fee'] = $this->calculation->numberFormatPrecision($fees['a4FBAFeesHKD'], 4);
        $billingItems['client_account_fba_fee'] = $this->calculation->numberFormatPrecision(
            $fees['clientFBAFeesHKD'],
            4
        );
        $billingItems['a4_account_fba_storage_fee'] = $this->calculation->numberFormatPrecision(
            $fees['a4AccountFbaStorageFee'],
            4
        );
        $billingItems['client_account_fba_storage_fee'] = $this->calculation->numberFormatPrecision(
            $fees['clientAccountFbaStorageFee'],
            4
        );
        $billingItems['a4_account_platform_fee'] = $this->calculation->numberFormatPrecision(
            $fees['a4PlatformFeeHKD'],
            4
        );
        $billingItems['client_account_platform_fee'] = $this->calculation->numberFormatPrecision(
            $fees['clientPlatformFeeHKD'],
            4
        );
        $billingItems['a4_account_refund_and_resend'] = $this->calculation->numberFormatPrecision(
            $fees['a4_account_refund_and_resend'],
            4
        );
        $billingItems['client_account_refund_and_resend'] = $this->calculation->numberFormatPrecision(
            $fees['client_account_refund_and_resend'],
            4
        );
        $billingItems['a4_account_miscellaneous'] = $this->calculation->numberFormatPrecision(
            $fees['a4AccountMiscellaneous'],
            4
        );
        $billingItems['client_account_miscellaneous'] = $this->calculation->numberFormatPrecision(
            $fees['clientAccountMiscellaneous'],
            4
        );
        $billingItems['a4_account_advertisement'] = $this->calculation->numberFormatPrecision($fees['a4AccountAds'], 4);
        $billingItems['client_account_advertisement'] = $this->calculation->numberFormatPrecision(
            $fees['clientAccountAds'],
            4
        );
        $billingItems['client_account_marketing_and_promotion'] = $this->calculation->numberFormatPrecision(
            $fees['clientAccountMarketingAndPromotion'],
            4
        );
        $billingItems['a4_account_marketing_and_promotion'] = $this->calculation->numberFormatPrecision(
            $fees['a4AccountMarketingAndPromotion'],
            4
        );

        $billingItems['sales_credit'] = $this->calculation->numberFormatPrecision(
            $billingItems['a4_account_sales_amount'] - $billingItems['a4_account_refund_and_resend'],
            4
        );

        $totalExpensesKeys = [
            "clientLogisticFeeHKD",
            "a4LogisticFeeHKD",
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

        //4-1 Commission Rate

        //get comission setting
        $totalSalesAmount = $billingItems['sales_credit'] - $clientRefundFees - $clientAccountResend;
        $commissionRate = $this->getCommissionRate(
            $clientCode,
            $reportDate,
            (float)$totalSalesAmount
        );

        $totalRefundAndResend =  $fees['a4_account_refund_and_resend'] + $fees['client_account_refund_and_resend'];

        $billingItems['commission_type'] = $commissionRate['type'];
        $fees['avolution_commission'] = $this->getAvolutionCommission(
            $clientCode,
            $reportDate,
            $billingItems['total_sales_amount'],
            $totalRefundAndResend,
            $commissionRate
        );

        $billingItems['avolution_commission'] = $this->calculation->numberFormatPrecision(
            $fees['avolution_commission'],
            4
        );

        //final_credit
        $billingItems['created_at'] = date('Y-m-d h:i:s');
        $billingItems['created_by'] = Auth::id();
        $billingItems['active'] = 1;

        $firstMileTotalVal = $this->calculation->numberFormatPrecision(
            app(FirstMileShipmentFeeRepository::class)->getSumOfAmountValue($reportDate, $clientCode),
            4
        );

        $returnHelperTotalVal = $this->calculation->numberFormatPrecision(
            app(ReturnHelperChargeRepository::class)->getSumOfAmount($reportDate, $clientCode),
            4
        );

        $continSumOfAmount = $this->calculation->numberFormatPrecision(
            app(ContinStorageFeeRepository::class)->getSumOfAmount($reportDate, $clientCode),
            4
        );

        $billingItems['fba_storage_fee_invoice'] = $firstMileTotalVal + $returnHelperTotalVal + $continSumOfAmount;

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

        $billingItems['final_credit'] = $this->calculation->numberFormatPrecision(
            $billingItems['sales_credit'] - $billingItems['opex_invoice'] - $billingItems['fba_storage_fee_invoice'],
            4
        );

        $this->billingStatementRepo->create($billingItems);
    }

    public function getSumValue(array $fees, array $keys = []): float
    {
        $feesCollection = collect($fees);

        if ($keys) {
            $feesCollection = collect($fees)->only($keys);
        }

        return $feesCollection->map(fn ($val) => $this->calculation->numberFormatPrecision($val, 4))->sum();
    }

    public function getCommissionRate(
        string $clientCode,
        string $reportDate,
        float $totalSalesAmount
    ): array {
        $settings = CommissionSetting::where('client_code', $clientCode)->first();

        if ($settings->calculation_type === CommissionConstant::CALCULATION_TYPE_SKU) {
            //check unmatched record
            $haveUnmatchedRecord = $this->orderProductRepo->checkUnmatchedRecord($clientCode, $reportDate);
            if (!empty($haveUnmatchedRecord)) {
                return [
                    'msg' => 'SKU-level commissions list need to match up with SKUs',
                    'status' => 'error',
                ];
            }

            $orders = $this->orderProductRepo->getFitOrder($clientCode, $reportDate);
            if ($orders) {
                foreach ($orders as $item) {
                    $thisOrder = $this->orderProductRepo->find($item->id);

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
        if ($this->isPromotion((float)$settings->promotion_threshold, $clientCode, $reportDate)) {
            return ['type' => 'promotion', 'value' => $settings->tier_promotion, 'status' => 'success'];
        }

        //check if commission rate type is tiered
        if ($settings->calculation_type === CommissionConstant::CALCULATION_TYPE_TIER) {
            return $this->getTieredInfo($clientCode, $totalSalesAmount);
        }
        return ['type' => 'base_rate', 'value' => $settings->basic_rate, 'status' => 'success'];
    }

    public function getAvolutionCommission(
        string $clientCode,
        string $shipDate,
        float  $totalSalesAmount,
        float  $totalRefundAndResend,
        array  $commissionRate
    ) {
        switch ($commissionRate['type']) {
            case 'sku':
                return $this->orderProductRepo->getSkuAvolutionCommission($clientCode, $shipDate);
            case 'tier_amount':
                return $commissionRate['value'];
            default:
                $param = ($this->isDeductRefund($clientCode)) ? $totalSalesAmount - $totalRefundAndResend :
                    $totalSalesAmount;

                return $param * $commissionRate['value'];
        }
    }

    public function getSkuCommissionRate(
        object $item,
        float $sellingPrice,
        float $threshold
    ) {
        if ($sellingPrice > $threshold) {
            return $item->upper_bound_rate;
        }

        return $item->basic_rate;
    }

    public function getTieredInfo(
        string $clientCode,
        float $totalSalesAmount
    ): array {
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
                return ['type' => 'tier_amount', 'value' => $setting->$amountKey, 'status' => 'success'];
            }

            $rateKey = "tier_{$newLevel}_rate";

            return ['type' => 'tier_rate', 'value' => $setting->$rateKey, 'status' => 'success'];
        }

        return ['type' => 'base_rate', 'value' => $setting->basic_rate, 'status' => 'success'];
    }

    protected function isDeductRefund(string $clientCode): bool
    {
        return (bool)optional(
            app(CustomerRepository::class)->findByClientCode($clientCode)
        )->commission_deduct_refund_cxl_order;
    }

    protected function isPromotion(
        float $promotionThreshold,
        string $clientCode,
        string $reportDate
    ): bool {
        $maxDiscountRate = $this->orderProductRepo->getMaxDiscountRate($clientCode, $reportDate);

        return (($promotionThreshold > 0) && ($promotionThreshold >= $maxDiscountRate));
    }
}

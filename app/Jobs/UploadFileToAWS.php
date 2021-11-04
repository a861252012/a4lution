<?php

namespace App\Jobs;

use App\Exports\InvoiceExport;
use App\Models\ExchangeRates;
use App\Models\Invoices;
use App\Models\CommissionSettings;
use App\Models\OrderProducts;
use App\Models\BillingStatements;
use App\Models\Customers;
use App\Models\ExtraordinaryItems;
use App\Repositories\OrderProductsRepository;
use App\Repositories\OrdersRepository;
use App\Repositories\AmazonReportListRepository;
use App\Repositories\FirstMileShipmentFeesRepository;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class UploadFileToAWS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

//    const BATCH_STATUS = 'processing';
    protected $request;
    protected $userID;
    protected $store;

    private const EB_ACCOUNT = 'IT2';
    private const EB_PWD = 'AbAO@12';

    public function __construct(
        array $request,
        int   $userID,
        bool  $store
    )
    {
        $this->request = $request;
        $this->userID = $userID;
        $this->store = $store;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->request;
        $data['report_date'] = date("Y-m-01", strtotime($data['step_report_date']));

        if ($this->store) {
            $data['issue_date'] = isset($data['issue_date']) ?
                date("Y-m-d", strtotime($data['issue_date'])) : date("Y-m-d");

            $data['due_date'] = isset($data['due_date']) ?
                date("Y-m-d", strtotime($data['due_date'])) : date('Y-m-d', strtotime('+30 days'));

            $formattedIssueDate = date("ymd", strtotime($data['issue_date']));
            $formattedSupplier = str_replace(' ', '_', ($data['supplier_name']));

            $data['opex_invoice_no'] = sprintf('INV-%d%s_1', $formattedIssueDate, $formattedSupplier);
            $data['fba_shipment_invoice_no'] = sprintf('INV-%d%s_FBA', $formattedIssueDate, $formattedSupplier);
            $data['credit_note_no'] = sprintf('CR-%d%s_1', $formattedIssueDate, $formattedSupplier);
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $data['updated_by'] = $this->userID;
            $data['created_by'] = $this->userID;
            $data['active'] = 1;
            $data['doc_status'] = "processing";
            $data['doc_file_name'] = sprintf(
                '%s_invoice_%s%d',
                $data['client_code'],
                date("Fy", strtotime($data['report_date'])),
                date('YmdHis')
            );

            $data['approved_at'] = null;
            $data['approved_by'] = null;

            unset($data['_token']);
            unset($data['step_report_date']);

            $insertInvoiceID = Invoices::insertGetId($data);
        }

        //4-1 Commission Rate
        $exchangeRate = ExchangeRates::where('quoted_date', $data['report_date'])
            ->where('active', 1)
            ->get();

        //check if exchange rate exist
        if (!$exchangeRate) {
            Log::error('uploadFileToS3_failed: exchangeRate is empty');
        }

        $commissionSettings = CommissionSettings::where('client_code', $data['client_code'])
            ->exists();

        if (!$commissionSettings) {
            Log::error('uploadFileToS3_failed: commissionSettings is empty');
            return;
        }
        //4-2 Expenses Breakdown start

        //getReportFees
        $supplierCode = Customers::where('client_code', $data['client_code'])->value('supplier_code');

        if (!$supplierCode) {
            Log::error("uploadFileToS3_failed: the supplierCode of {$data['client_code']} is empty");
        }

        $getSupplierName = $this->sendERPRequest(
            env("ERP_WMS_URL"),
            'getSupplierInfo',
            ["supplierCode" => $supplierCode]
        );

        $supplierName = $getSupplierName['data']['supplierName'] ?? null;

        $orderRepository = new OrdersRepository();
        $clientReportFees = $orderRepository->getReportFees(
            $data['report_date'],
            $data['client_code'],
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
            $data['report_date'],
            $data['client_code'],
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
            $data['report_date'],
            $data['client_code'],
            $supplierName,
            true
        );

        $clientRefundFees = $clientRefundFees ? $clientRefundFees[0]->refund_amount_hkd : 0;

        $a4RefundFees = $orderRepository->getAccountRefund(
            $data['report_date'],
            $data['client_code'],
            $supplierName,
            false
        );

        $a4RefundFees = $a4RefundFees ? $a4RefundFees[0]->refund_amount_hkd : 0;

        //getAccountResend
        $clientAccountResend = $orderRepository->getAccountResend(
            $data['report_date'],
            $data['client_code'],
            $supplierName,
            true
        );

        $clientAccountResend = $clientAccountResend ? $clientAccountResend[0]->total_sales_hkd : 0;

        $a4AccountResend = $orderRepository->getAccountResend(
            $data['report_date'],
            $data['client_code'],
            $supplierName,
            false
        );

        $a4AccountResend = $a4AccountResend ? $a4AccountResend[0]->total_sales_hkd : 0;

        $clientAccountAmazonTotal = $orderRepository->getAccountAmzTotal(
            $data['report_date'],
            $data['client_code'],
            $supplierName,
            true
        );

        $clientAccountAmazonTotal = $clientAccountAmazonTotal ?
            $clientAccountAmazonTotal[0]->amazon_total_hkd : 0;

        $a4AccountAmazonTotal = $orderRepository->getAccountAmzTotal(
            $data['report_date'],
            $data['client_code'],
            $supplierName,
            false
        );

        $a4AccountAmazonTotal = $a4AccountAmazonTotal ? $a4AccountAmazonTotal[0]->amazon_total_hkd : 0;

        $fees['a4_account_refund_and_resend'] = abs((float)$a4RefundFees + (float)$a4AccountResend + (float)$a4AccountAmazonTotal);

        $fees['client_account_refund_and_resend'] = abs((float)$clientAccountAmazonTotal + (float)$clientAccountResend + (float)$clientRefundFees);

        //getAccountMiscellaneous
        $amazonReportListRepository = new AmazonReportListRepository();

        $clientAccountMiscellaneous = $amazonReportListRepository->getAccountMiscellaneous(
            $data['report_date'],
            $data['client_code'],
            $supplierName,
            true
        );

        $fees['clientAccountMiscellaneous'] = $clientAccountMiscellaneous
            ? -1 * abs($clientAccountMiscellaneous[0]->Miscellaneous) : 0;

        $a4AccountMiscellaneous = $amazonReportListRepository->getAccountMiscellaneous(
            $data['report_date'],
            $data['client_code'],
            $supplierName,
            false
        );

        $fees['a4AccountMiscellaneous'] = $a4AccountMiscellaneous ?
            -1 * abs($a4AccountMiscellaneous[0]->Miscellaneous) : 0;

        //getAccountAds
        $clientAccountAds = $orderRepository->getAccountAds(
            $data['report_date'],
            $data['client_code'],
            $supplierName,
            true
        );

        $fees['clientAccountAds'] = $clientAccountAds ? $clientAccountAds[0]->ad : 0;

        $a4AccountAds = $orderRepository->getAccountAds(
            $data['report_date'],
            $data['client_code'],
            $supplierName,
            false
        );

        $fees['a4AccountAds'] = $a4AccountAds ? $a4AccountAds[0]->ad : 0;

        //getAccountMarketingAndPromotion
        $clientAccountMarketingAndPromotion = $amazonReportListRepository->getAccountMarketingAndPromotion(
            $data['report_date'],
            $data['client_code'],
            $supplierName,
            true
        );

        $fees['a4AccountMarketingAndPromotion'] = $clientAccountMarketingAndPromotion ?
            $clientAccountMarketingAndPromotion[0]->Miscellaneous : 0;

        $a4AccountMarketingAndPromotion = $amazonReportListRepository->getAccountMarketingAndPromotion(
            $data['report_date'],
            $data['client_code'],
            $supplierName,
            false
        );

        $fees['clientAccountMarketingAndPromotion'] = $a4AccountMarketingAndPromotion ?
            $a4AccountMarketingAndPromotion[0]->Miscellaneous : 0;

        //getAccountFbaStorageFee
        $clientAccountFbaStorageFee = $orderRepository->getAccountFbaStorageFee(
            $data['report_date'],
            $data['client_code'],
            $supplierName,
            true
        );

        $fees['clientAccountFbaStorageFee'] = $clientAccountFbaStorageFee ? $clientAccountFbaStorageFee[0]->storage_fee_hkd_sum : 0;

        $a4AccountFbaStorageFee = $orderRepository->getAccountFbaStorageFee(
            $data['report_date'],
            $data['client_code'],
            $supplierName,
            false
        );

        $fees['a4AccountFbaStorageFee'] = $a4AccountFbaStorageFee ? $a4AccountFbaStorageFee[0]->storage_fee_hkd_sum : 0;

        //4-2 Expenses Breakdown end

        //4-3 prepare data for step3,for insert billing statement
        $billingItems['sales_tax_handling'] = 0;
        $billingItems['report_date'] = $data['report_date'];
        $billingItems['client_code'] = $data['client_code'];

        $totalSalesOrders = $orderRepository->getTotalSalesOrders(
            $data['report_date'],
            $data['client_code']
        );

        $billingItems['total_sales_orders'] = $totalSalesOrders ? $totalSalesOrders[0]->total_sales_orders : 0;

        $sumOfSalesAmount = $orderRepository->getSumOfSalesAmount(
            $data['report_date'],
            $data['client_code']
        );

        $billingItems['total_sales_amount'] = $sumOfSalesAmount ? round($sumOfSalesAmount[0]->total_sales_hkd, 2) : 0;

        $fees['extraordinary_item'] = ExtraordinaryItems::where('active', 1)
            ->where('report_date', $data['report_date'])
            ->where('client_code', $data['client_code'])
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
            $data['client_code'],
            $data['report_date'],
            (float)$totalSalesAmount
        );

        $tieredParam = $billingItems['total_sales_amount'] - $fees['a4_account_refund_and_resend']
            - $fees['client_account_refund_and_resend'];
        $billingItems['commission_type'] = $commissionRate['type'] ?? null;
        $billingItems['avolution_commission'] = $this->getAvolutionCommission(
            $data['client_code'],
            $data['report_date'],
            $tieredParam,
            $commissionRate
        );

        //final_credit
        $billingItems['created_at'] = date('Y-m-d h:i:s');
        $billingItems['created_by'] = $this->userID;
        $billingItems['active'] = 1;

        $firstMileShipmentFeesRepository = new FirstMileShipmentFeesRepository();

        $getFbaStorageFeeInvoices = $firstMileShipmentFeesRepository->getFbaStorageFeeInvoice(
            $data['report_date'],
            $data['client_code']
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

        $billingInsertID = BillingStatements::insertGetId($billingItems);

        $invoices = new Invoices();

        $uniqueFileName = $this->genDocStorageToken();

        if (isset($insertInvoiceID)) {
            $invoices->where('id', $insertInvoiceID)
                ->update(
                    ['doc_storage_token' => $uniqueFileName]
                );
        }

        if ($this->store && isset($insertInvoiceID)) {
            Excel::store(
                new InvoiceExport($data['report_date'], $data['client_code'], $insertInvoiceID, $billingInsertID),
                $uniqueFileName,
                's3',
                \Maatwebsite\Excel\Excel::XLSX
            );
        }
    }

    public function getSumValue(array $fees, array $keys = []): float
    {
        $feesCollection = collect($fees);

        if ($keys) {
            $feesCollection = collect($fees)->only($keys);
        }

        return $feesCollection->map(function ($val) {
            return round($val, 2);
        })->sum();
    }

    public function getCommissionRate(string $clientCode, string $reportDate, float $totalSalesAmount)
    {
        $commissionSettings = new CommissionSettings();
        $orderProductRepository = new OrderProductsRepository();

        $settings = $commissionSettings->where('client_code', $clientCode)->first();

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
                    $thisOrder = OrderProducts::find($item->id);

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

    public function getSkuCommissionRate(object $item, float $sellingPrice, float $threshold)
    {
        if ($sellingPrice > $threshold) {
            return $item->upper_bound_rate;
        }

        return $item->basic_rate;
    }

    public function getTieredInfo(string $clientCode, float $totalSalesAmount): array
    {
        $setting = CommissionSettings::where('client_code', $clientCode)->first();

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

    public function getAvolutionCommission(
        string $clientCode,
        string $shipDate,
        float  $tieredParam,
        array  $commissionRate
    )
    {
        switch ($commissionRate['type']) {
            case 'sku':
                $orderProductRepository = new OrderProductsRepository();

                return round($orderProductRepository->getSkuAvolutionCommission($clientCode, $shipDate), 2);
            case 'promotion':
                return $commissionRate['value'];
            case 'tiered':
                return round($tieredParam * $commissionRate['value'], 2);
        }
    }

    private function sendERPRequest(
        string $url,
        string $serviceName,
        array  $customParam = []
    ): array
    {
        $ebSoapRequest = $this->genXML(
            json_encode($customParam),
            self::EB_ACCOUNT,
            self::EB_PWD,
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

        return json_decode($this->analyzeSOAP($res), true);
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

    public function genDocStorageToken(): string
    {
        $microTime = (int)(microtime(true) * 1000);
        $uniqID = str_shuffle(uniqid());
        return sprintf(
            '%s_%d.xlsx',
            $uniqID,
            $microTime
        );
    }
}

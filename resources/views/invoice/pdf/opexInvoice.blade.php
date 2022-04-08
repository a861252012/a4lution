<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>A4lution Credit Note</title>
</head>
<style>
    body {
        size: A4;
        margin:0;
        padding:0;
        font-size: 10px;
    }
    table {
        width: 100%;
        border: none;
        border-collapse: collapse;
    }
    .col { word-wrap: break-word; }

    /* header */
    .header .logo-img img { width: 200px; }
    .header .title {
        font-size: 25px;
        font-weight: bold;
        padding-bottom: 20px;
    }
    .header .col-1 { width: 50%; }
    .header .col-2 { width: 20%; }
    .header .col-3 { width: 30%; }

    /* content */
    .content { margin-top: 15px; }
    .content thead th {
        text-align: left;
    }
    .content .col-1 { width: 10%; }
    .content .col-2 { width: 50%; }
    .content .col-3 { width: 15%; }
    .content .col-4 { width: 10%; }
    .content .col-5 { width: 15%; }
    .content tbody .col-1, .col-2,
    .content tfoot .col-1 {
        text-align: left;
    }
    .content tbody .col-3, .col-4, .col-5,
    .content tfoot .col-5 {
        text-align: right;
    }
    .content tbody tr td {
        padding: 5px 0 5px 0;
    }
    .content tbody tr:first-child td {
        border-top: 1px solid;
    }
    .content tbody tr:last-child td {
        border-bottom: 1px solid;
    }
    .content tfoot tr td {
        padding-top: 15px;
    }
    .content .total { font-weight: bold; }

    /* payment-info */
    .payment-info { padding-top: 60px; }
    .payment-info ul {
        list-style: none;
        padding-left: 0;
        color: rgb(77, 75, 75);
        border-top: 1px solid;
        border-bottom: 1px solid
    }
    .payment-info .email {
        font-weight: bold;
        color: black;
        font-style: italic;
    }
    .payment-info ul li:first-child { padding-bottom: 15px; }
    .payment-info ul li:last-child { padding-bottom: 50px; }

    /* footer */
    .footer { padding-top: 20px; }
    .footer ul { list-style: none; }
    .footer .info {
        padding-left: 50px;
        text-align: center;
        font-size: 12px;
    }
    .footer .info .website-url {
        padding-bottom: 15px;
    }
    .footer .img { width: 180px; }
    .footer .img img { width: 100%; }
</style>
<body>
<div class="header">
    <table>
        <tr>
            <td class="col col-1 logo-img">
                <img src="{{ "data:image/jpg;base64, " . base64_encode(file_get_contents(public_path('pictures/A4lution_logo.jpg'))) }}"
                     alt="A4lution_logo">
            </td>
            <td class="col col-2"></td>
            <td class="col col-3"></td>
        </tr>
        <tr>
            <td class="col col-1"></td>
            <td class="col col-2"></td>
            <td class="col col-3 title">OPEX Invoice</td>
        </tr>
        <tr>
            <td class="col col-1"></td>
            <td class="col col-2"></td>
            <td class="col col-3"></td>
        </tr>
        <tr>
            <td class="col col-1"></td>
            <td class="col col-2">Details</td>
            <td class="col col-3"></td>
        </tr>
        <tr>
            <td class="col col-1">TO</td>
            <td class="col col-2">Credit Note:</td>
            <td class="col col-3">{{ $invoice->credit_note_no }}</td>
        </tr>
        <tr>
            <td class="col col-1">{{ $invoice->client_contact }}</td>
            <td class="col col-2">Issue Date:</td>
            <td class="col col-3">{{ $invoice->issue_date->format('d-M-y') }}</td>
        </tr>
        <tr>
            <td class="col col-1"></td>
            <td class="col col-2">Due Date:</td>
            <td class="col col-3">{{ $invoice->due_date }}</td>
        </tr>
        <tr>
            <td class="col col-1">{{ $invoice->client_company }}</td>
            <td class="col col-2"></td>
            <td class="col col-3"></td>
        </tr>
        <tr>
            <td class="col col-1">{{ $invoice->client_address1 . ',' }}</td>
            <td class="col col-2"></td>
            <td class="col col-3"></td>
        </tr>
        <tr>
            <td class="col col-1">{{ $invoice->client_address2 }}</td>
            <td class="col col-2"></td>
            <td class="col col-3"></td>
        </tr>
        <tr>
            <td class="col col-1">{{ $invoice->client_city . ',' . $invoice->client_country }}</td>
            <td class="col col-2"></td>
            <td class="col col-3"></td>
        </tr>
    </table>
</div>
<div class="content">
    <table>
        <thead>
        <tr>
            <th class="col col-1">Item</th>
            <th class="col col-2">
                {{sprintf("Description  (for the period of %s to %s)",
                    $invoice->report_date->format('jS M Y'),
                    $invoice->report_date->endOfMonth()->format('jS M Y')); }}
            </th>
            <th class="col col-3">Unit Price</th>
            <th class="col col-4">Quantity</th>
            <th class="col col-5">Amount</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            @php
                $logisticFee = ($invoice->billingStatement->client_code === 'G73A')
                    ? $invoice->billingStatement->a4_account_logistics_fee
                    : $invoice->billingStatement->a4_account_logistics_fee + $invoice->billingStatement->client_account_logistics_fee;
            @endphp
            <td class="col col-1">A</td>
            <td class="col col-2">Logistic fee</td>
            <td class="col col-3">HKD {{ number_format($logisticFee, 2) }}</td>
            <td class="col col-4">1</td>
            <td class="col col-5">HKD {{ number_format($logisticFee, 2) }}</td>
        </tr>
        <tr>
            <td class="col col-1">B</td>
            <td class="col col-2">Platform fee</td>
            <td class="col col-3">HKD {{ number_format($invoice->billingStatement->a4_account_platform_fee, 2)  }}</td>
            <td class="col col-4">1</td>
            <td class="col col-5">HKD {{ number_format($invoice->billingStatement->a4_account_platform_fee, 2) }}</td>
        </tr>
        <tr>
            <td class="col col-1">C</td>
            <td class="col col-2">FBA fee</td>
            <td class="col col-3">HKD {{ number_format($invoice->billingStatement->a4_account_fba_fee, 2) }}</td>
            <td class="col col-4">1</td>
            <td class="col col-5">HKD {{ number_format($invoice->billingStatement->a4_account_fba_fee, 2) }}</td>
        </tr>
        <tr>
            <td class="col col-1">D</td>
            <td class="col col-2">FBA Storage Fee</td>
            <td class="col col-3">
                HKD {{ number_format($invoice->billingStatement->a4_account_fba_storage_fee, 2) }}</td>
            <td class="col col-4">1</td>
            <td class="col col-5">
                HKD {{ number_format($invoice->billingStatement->a4_account_fba_storage_fee, 2) }}</td>
        </tr>
        <tr>
            @php
                $marketingFee = $invoice->billingStatement->a4_account_advertisement + $invoice->billingStatement->a4_account_marketing_and_promotion;
            @endphp
            <td class="col col-1">E</td>
            <td class="col col-2">Marketing Fee</td>
            <td class="col col-3">HKD {{ number_format($marketingFee, 2) }}</td>
            <td class="col col-4">1</td>
            <td class="col col-5">HKD {{ number_format($marketingFee, 2) }}</td>
        </tr>
        <tr>
            <td class="col col-1">F</td>
            <td class="col col-2">Sales Tax Handling</td>
            <td class="col col-3">
                HKD {{ number_format($invoice->billingStatement->a4_account_sales_tax_handling, 2) }}</td>
            <td class="col col-4">1</td>
            <td class="col col-5">
                HKD {{ number_format($invoice->billingStatement->a4_account_sales_tax_handling, 2) }}</td>
        </tr>
        <tr>
            <td class="col col-1">G</td>
            <td class="col col-2">Miscellaneous</td>
            <td class="col col-3">HKD {{ number_format($invoice->billingStatement->a4_account_miscellaneous, 2) }}</td>
            <td class="col col-4">1</td>
            <td class="col col-5">HKD {{ number_format($invoice->billingStatement->a4_account_miscellaneous, 2) }}</td>
        </tr>
        <tr>
            <td class="col col-1">H</td>
            <td class="col col-2">Extraordinary item</td>
            <td class="col col-3">HKD {{ number_format($invoice->billingStatement->extraordinary_item, 2) }}</td>
            <td class="col col-4">1</td>
            <td class="col col-5">HKD {{ number_format($invoice->billingStatement->extraordinary_item, 2) }}</td>
        </tr>
        <tr>
            <td class="col col-1">I</td>
            <td class="col col-2">A4lution Commission</td>
            <td class="col col-3">HKD {{ number_format($invoice->billingStatement->avolution_commission, 2) }}</td>
            <td class="col col-4">1</td>
            <td class="col col-5">HKD {{ number_format($invoice->billingStatement->avolution_commission, 2) }}</td>
        </tr>
        </tbody>
        <tfoot>
        @php
            $total = collect($invoice->billingStatement)
                ->only([
                    'a4_account_platform_fee',
                    'a4_account_fba_fee',
                    'a4_account_fba_storage_fee',
                    'a4_account_advertisement',
                    'a4_account_marketing_and_promotion',
                    'a4_account_miscellaneous',
                    'sales_tax_handling',
                    'avolution_commission',
                    'extraordinary_item'
                ])
                ->sum() + $logisticFee;
        @endphp
        <tr>
            <td class="col col-1 total">Total</td>
            <td class="col col-2"></td>
            <td class="col col-3"></td>
            <td class="col col-4"></td>
            <td class="col col-5">HKD {{ number_format($total, 2) }}</td>
        </tr>
        </tfoot>
    </table>
</div>
<div class="payment-info">
    <ul>
        <li>Payment Method:</li>
        <li>1) By Transfer to the following HSBC account & send copy to <span
                    class="email">sammi.chan@a4lution.com</span> and <span class="email">billy.kwan@a4lution.com</span>
        </li>
        <li>&nbsp;&nbsp;&nbsp;&nbsp;a) Beneficiary Name: A4lution Limited</li>
        <li>&nbsp;&nbsp;&nbsp;&nbsp;b) Beneficiary Bank: THE HONGKONG AND SHANGHAI BANKING CORPORATION LTD</li>
        <li>&nbsp;&nbsp;&nbsp;&nbsp;c) Swift code: HSBCHKHHHKH</li>
        <li>&nbsp;&nbsp;&nbsp;&nbsp;d) Account no.: 004-747-095693-838</li>
        <li>2) Payment Term: within 10 working days from the date of Invoice</li>
    </ul>
</div>
<div class="footer">
    <table>
        <tr>
            <td class="info">
                <ul>
                    <li>A4lution Limited</li>
                    <li><a href="mailto:info@a4lution.com">info@a4lution.com</a></li>
                    <li class="website-url"><a href="http://www.a4lution.com">http://www.a4lution.com</a></li>
                    <li>Mail box no. 621-08, Unit 621, 6/F, Building 19W,</li>
                    <li>No. 19 Science Park West Avenue,</li>
                    <li>Hong Kong Science Park, Pak Shek Kok, N.T., Hong Kong</li>
                </ul>
            </td>
            <td class="img">
                <img src="{{ "data:image/png;base64, " . base64_encode(file_get_contents(public_path('pictures/A4lution_signature.jpg'))) }}"
                     alt="A4lution_logo">
            </td>
        </tr>
    </table>
</div>
</body>
</html>
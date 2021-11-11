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
        font-size: 30px;
        font-weight: bold;
        padding-bottom: 20px;
    }
    .header .col-1 { width: 65%; }
    .header .col-2 { width: 10%; }
    .header .col-3 { width: 25%; }

    /* content */
    .content { margin-top: 15px; }
    .content thead th {
        text-align: left;
    }
    .content .col-1 { width: 20%; }
    .content .col-2 { width: 65%; }
    .content .col-3 { width: 15%; }
    .content tbody .col-1, .col-2,
    .content tfoot .col-1 {
        text-align: left;
    }
    .content tbody .col-3,
    .content tfoot .col-3 {
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

    /* footer */
    .footer { padding-top: 200px; }
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
                    <img src="{{ "data:image/jpg;base64, " . base64_encode(file_get_contents(public_path('pictures/A4lution_logo.jpg'))) }}" alt="A4lution_logo">
                </td>
                <td class="col col-2"></td>
                <td class="col col-3"></td>
            </tr>
            <tr>
                <td class="col col-1"></td>
                <td class="col col-2"></td>
                <td class="col col-3 title">Credit Note</td>
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
                    <th class="col col-2">Description</th>
                    <th class="col col-3">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="col col-1">A</td>
                    <td class="col col-2">
                        {{ sprintf("Sales for the period of %s to %s", 
                            $invoice->report_date->format('jS M Y'), 
                            $invoice->report_date->endOfMonth()->format('jS M Y')); }}
                    </td>
                    <td class="col col-3">HKD  {{ number_format($invoice->billingStatement->total_sales_amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="col col-1">C</td>
                    <td class="col col-2">
                        {{ sprintf(
                            "Cost of Refund Cases - Refund Amount for the period of %s to %s", 
                            $invoice->report_date->format('jS M Y'), 
                            $invoice->report_date->endOfMonth()->format('jS M Y')); }}
                    </td>
                    <td class="col col-3">-HKD  {{ number_format($invoice->billingStatement->a4_account_refund_and_resend, 2) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td class="col col-1 total">Total</td>
                    <td class="col col-2"></td>
                    <td class="col col-3">HKD {{ number_format((float) -$invoice->billingStatement->a4_account_refund_and_resend + (float) $invoice->billingStatement->total_sales_amount, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="footer">
        <table>
            <tr>
                <td class="info">
                    <ul>
                        <li>A4lution Limited</li>
                        <li><a href="mailto:info@a4lution.com">info@a4lution.com</a></li>
                        <li class="website-url"><a href="http://www.a4lution.com">http://www.a4lution.com</a>    </li>
                        <li>Mail box no. 621-08, Unit 621, 6/F, Building 19W,</li>
                        <li>No. 19 Science Park West Avenue,</li>
                        <li>Hong Kong Science Park, Pak Shek Kok, N.T., Hong Kong</li>
                    </ul>
                </td>
                <td class="img">
                    <img src="{{ "data:image/png;base64, " . base64_encode(file_get_contents(public_path('pictures/A4lution_signature.jpg'))) }}" alt="A4lution_logo">
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
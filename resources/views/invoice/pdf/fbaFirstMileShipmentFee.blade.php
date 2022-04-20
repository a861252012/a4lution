<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>A4lution FFBA First Mile Shipment Fee</title>
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
                <img src="{{ "data:image/jpg;base64, " . base64_encode(file_get_contents(public_path('pictures/A4lution_logo.jpg'))) }}" alt="A4lution_logo">
            </td>
            <td class="col col-2"></td>
            <td class="col col-3"></td>
        </tr>
        <tr>
            <td class="col col-1"></td>
            <td class="col col-2"></td>
            <td class="col col-3 title">Invoice</td>
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
            <td class="col col-2">Invoice Number:</td>
            <td class="col col-3">{{ $invoice->fba_shipment_invoice_no }}</td>
        </tr>
        <tr>
            <td class="col col-1">{{ $invoice->client_contact }}</td>
            <td class="col col-2">Issue Date:</td>
            <td class="col col-3">{{ $invoice->issue_date->format('d-M-y') }}</td>
        </tr>
        <tr>
            <td class="col col-1">{{ $invoice->client_company }}</td>
            <td class="col col-2">Payment Terms:</td>
            <td class="col col-3">{{ $invoice->payment_terms }}  days net</td>
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
            <th class="col col-1">NO.</th>
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
        @php($total = 0)
        @php($loopCount = 1)

        {{-- 1. Contin Storage Fee start --}}
        @php($total += round($continStorageFee, 2))
        @php($averageCbmUsage = number_format($continStorageFee / 300, 2))
        <tr>
            <td class="col col-1">{{ $loopCount }}</td>
            <td class="col col-2">Contin Storage Fee</td>
            <td class="col col-3"></td>
            <td class="col col-4"></td>
            <td class="col col-5">HKD  {{ number_format($continStorageFee, 2) }}</td>
        </tr>
        <tr>
            <td class="col col-1"></td>
            <td class="col col-2">
                {{ "Average CBM Usage: {$averageCbmUsage}" }}
            </td>
            <td class="col col-3">$ {{ number_format($continStorageFee, 2) }}</td>
            <td class="col col-4">1</td>
            <td class="col col-5"></td>
        </tr>
        {{-- 1. Contin Storage Fee end --}}

        {{-- 2. Contin 寄FBA的頭程費用 start --}}
        @forelse ($firstMileShipmentFees as $firstMileShipmentFee)
            @php($total += (float)$firstMileShipmentFee->unit_price)
            @if($loop->last)
                @php($loopCount = $loop->count + 1)
            @endif
            <tr>
                <td class="col col-1">{{  $loop->iteration + 1 }}</td>
                <td class="col col-2">FBA shipment Fee from Continental HK warehouse to Amazon FBA warehouse:</td>
                <td class="col col-3"></td>
                <td class="col col-4"></td>
                <td class="col col-5">HKD  {{ number_format($firstMileShipmentFee->unit_price, 2) }}</td>
            </tr>
            <tr>
                <td class="col col-1"></td>
                <td class="col col-2">
                    {{ sprintf(
                        "Country:%s, Shipment ID:%s, SKU:%d, Shipped Qty:%d",
                        $firstMileShipmentFee->country,
                        $firstMileShipmentFee->shipment_id,
                        $firstMileShipmentFee->sku,
                        $firstMileShipmentFee->shipped_qty,
                    ) }}
                </td>
                <td class="col col-3">$ {{ number_format($firstMileShipmentFee->unit_price, 2) }}</td>
                <td class="col col-4">1</td>
                <td class="col col-5"></td>
            </tr>
        @empty
        @endforelse
        {{-- 2. Contin 寄FBA的頭程費用 end --}}

        {{-- 3. Contin 寄FBA的頭程費用 start --}}
        @forelse ($returnHelperList as $returnHelperItem)
            @php($total += (float)$returnHelperItem->amount_hkd)
            <tr>
                <td class="col col-1">{{ $loopCount + $loop->iteration }}</td>
                <td class="col col-2">Return Helper Charges</td>
                <td class="col col-3"></td>
                <td class="col col-4"></td>
                <td class="col col-5">HKD  {{ number_format($returnHelperItem->amount_hkd, 2) }}</td>
            </tr>
            <tr>
                <td class="col col-1"></td>
                <td class="col col-2">{{ $returnHelperItem->notes }}</td>
                <td class="col col-3">$ {{ number_format($returnHelperItem->amount_hkd, 2) }}</td>
                <td class="col col-4">1</td>
                <td class="col col-5"></td>
            </tr>
        @empty
        @endforelse
        {{-- 3. Contin 寄FBA的頭程費用 end --}}

        </tbody>
        <tfoot>
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
        <li>1) By Transfer to the following HSBC account & send copy to
            @isset($user->payment_checker_email)
                <span class="email">{{ $user->payment_checker_email }}</span>,and
            @endisset
            <span class="email">{{ $user->email }}</span>
        </li>
        <li>&nbsp;&nbsp;&nbsp;&nbsp;a) Beneficiary Name: A4lution Limited</li>
        <li>&nbsp;&nbsp;&nbsp;&nbsp;b) Beneficiary Bank: THE HONGKONG AND SHANGHAI BANKING CORPORATION LTD</li>
        <li>&nbsp;&nbsp;&nbsp;&nbsp;c) Swift code: HSBCHKHHHKH</li>
        <li>&nbsp;&nbsp;&nbsp;&nbsp;d) Account no.: 004-747-095693-838</li>
        <li>&nbsp;&nbsp;&nbsp;&nbsp;e) FPS registered email: info@a4lution.com</li>
    </ul>
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
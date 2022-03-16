<!-- colorbox html part start -->
<div class="container">
    <input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}">

    @if(count($errors) > 0 )
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <ul class="p-0 m-0" style="list-style: none;">
                @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{--   ORDER SUMMARY  --}}
    <div class="row">
        <div class="col">
            <div class="text-warning"><strong>Order Summary</strong></div>
        </div>
    </div>
    <hr class="my-2">
    <div class="row">
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Package Id:</label>{{ $lists['order_code'] }}
        </div>

        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Erp Order Id:</label>{{ $erp_order_id }}
        </div>

        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Platform:</label>{{ $platform }}
        </div>
    </div>

    <div class="row">
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Acc Nick Name:</label>{{ $lists['seller_id'] }}
        </div>

        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Acc Name:</label>{{ $acc_name }}
        </div>

        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Site:</label>{{ $site_id }}
        </div>
    </div>

    <div class="row">
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Warehouse:</label>{{ $lists['warehouse'] }}
        </div>

        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Shipping Method:</label>
            {{ $lists['sm_code'] }}
        </div>

        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1"
                   style="font-weight:bold;">Tracking:</label>{{ $lists['tracking_number'] }}
        </div>
    </div>

    <div class="row">
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Audit Date:</label>{{ $lists['add_time'] }}
        </div>

        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Paid
                Date:</label>{{ $lists['order_paydate'] }}
        </div>

        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1 inline_shipped_date" style="font-weight:bold;"
                   data-attr="{{ $shipped_date }}">Shipped Date:</label>{{ $shipped_date }}
        </div>
    </div>

    {{-- PRODUCT DETAILS --}}
    <div class="row">
        <div class="col">
            <div class="text-warning"><strong>Product Details</strong></div>
        </div>
    </div>
    <hr class="my-2">

    <div class="row">
        <div class="col-12 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Product
                Name:</label>{{ $lists['product_title'] }}
        </div>
    </div>

    <div class="row">
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Platform Sku:
            </label>{{ $lists['op_platform_sales_sku'] }}
        </div>
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1"
                   style="font-weight:bold;">Item Id(ASIN):</label>{{ $lists['asin_or_item'] }}
        </div>
        <div class="col-4 form-group _fz-1" id="supplier" data-label="{{ $supplier }}">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Supplier:</label>{{ $supplier }}
        </div>
    </div>

    <div class="row">
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Sku:</label>{{ $sku }}
        </div>
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Qty:</label>{{ $lists['quantity'] }}
        </div>
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Product
                Weight:</label>{{ $lists['weight'] }}
        </div>
    </div>


    {{--  PRODUCT PROFIT DETAILS --}}
    <div class="row">
        <div class="col">
            <div class="text-warning"><strong>Product Profit Details</strong></div>
        </div>
    </div>
    <hr class="my-2">

    <div class="row">
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Order Original
                Ccurrency:</label>{{ $lists['currency_code_org'] }}
        </div>
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">HKD Rate:</label>
            {{ $exchange_rate[$lists['currency_code_org']] }}
        </div>
    </div>

    <div class="row">
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Erp Original Currency:</label>{{'RMB'}}
        </div>
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">HKD Rate:</label>
            {{ $exchange_rate['RMB'] }}
        </div>
    </div>

    <div class="row">
        <div class="col-6 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">(AMAZON)Promotion Metadata Definition
                Value:</label>
            {{ $lists['promotion_amount'] }}
        </div>
        <div class="col-6 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Percentage Discount:</label>
            {{ $lists['promotion_discount_rate'] }}
        </div>
    </div>

    <hr class="my-2">

    {{-- EDIT BUTTON --}}
    <div class="row justify-content-center align-items-center">
        <div class="col">
            <button class="btn btn-primary _fz-1" type="button" id="edit_btn" data-attr="{{$lists['product_id']}}">Edit
            </button>
        </div>
    </div>

    {{-- table --}}
    <table class="table table-sm _table">
        <thead>
        <tr class="bg-primary">
            <th scope="col"></th>
            <th scope="col"></th>
            <th class="text-white" scope="col"><strong>{{ $lists['currency_code_org'] }}</strong></th>
            <th class="text-white" scope="col"><strong>HKD</strong></th>
        </tr>
        </thead>

        <tbody>

        <tr class="table-success">
            <th scope="row"><strong>Order Price</strong></th>
            <td>Order Price</td>
            <td>{{ $order_price }}</td>
            <td>{{ $lists['order_price_hkd'] }}</td>
        </tr>

        {{--  PRODUCT COST  --}}
        <tr class="table-info">
            <th rowspan="2"><strong>Product Cost</strong></th>
            <td>Purchase Shipping Fee</td>
            <td>{{ $lists['purchase_shipping_fee'] }}</td>
            <td>{{ $lists['purchase_shipping_fee_hkd'] }}</td>
        </tr>
        <tr class="table-info">
            <th scope="row">Product Cost</th>
            <td>{{ $lists['product_cost'] }}</td>
            <td>{{ $lists['product_cost_hkd'] }}</td>
        </tr>

        {{-- SKIPPING FEE --}}
        <tr>
            <th rowspan="3"><strong>Shipping Fee</strong></th>
            <th>First Mile Shipping Fee</th>
            <td><input type="text" class="editable" name="first_mile_shipping_fee"
                value="{{ $lists['first_mile_shipping_fee'] }}"
                readonly></td>
            <td>{{ $lists['first_mile_shipping_fee_hkd'] }}</td>
        </tr>
        <tr>
            <th scope="row">First Mile Tariff</th>
            <td><input type="text" class="editable" name="first_mile_tariff" value="{{ $lists['first_mile_tariff'] }}"
                readonly></td>
            <td>{{ $lists['first_mile_tariff_hkd'] }}</td>
        </tr>
        <tr>
            <th scope="row">Last Mile Shipping Fee</th>
            <td><input type="text" class="editable" name="last_mile_shipping_fee"
                value="{{ $lists['last_mile_shipping_fee'] }}"
                readonly></td>
            <td>{{ $lists['last_mile_shipping_fee_hkd'] }}</td>
        </tr>

        {{-- PLATFORM FEE --}}
        <tr class="table-info">
            <th rowspan="4"><strong>Platform Fee</strong></th>
            <th>Other Fee</th>
            <td><input type="text" name="other_fee" class="editable" value="{{ $lists['other_fee'] }}"
                readonly></td>
            <td>{{ $lists['other_fee_hkd'] }}</td>
        </tr>
        <tr class="table-info">
            <th scope="row">Marketplace Tax</th>
            <td><input type="text" name="marketplace_tax" class="editable" value="{{ $lists['marketplace_tax'] }}"
                readonly></td>
            <td>{{ $lists['marketplace_tax_hkd'] }}</td>
        </tr>
        <tr class="table-info">
            <th scope="row">Cost Of Point</th>
            <td><input type="text" name="cost_of_point" class="editable" value="{{ $lists['cost_of_point'] }}"
                readonly></td>
            <td>{{ $lists['cost_of_point_hkd'] }}</td>
        </tr>
        <tr class="table-info">
            <th scope="row">Exclusives Referral Fee</th>
            <td><input type="text" name="exclusives_referral_fee" class="editable" value="{{ $lists['exclusives_referral_fee'] }}"
                readonly></td>
            <td>{{ $lists['exclusives_referral_fee_hkd'] }}</td>
        </tr>

        {{-- FBA FEE --}}
        <tr>
            <th scope="row"><strong>Fba Fee</strong></th>
            <td>Fba Fee</td>
            <td><input type="text" name="fba_fee" class="editable" value="{{ $lists['fba_fee'] }}" readonly></td>
            <td>{{ $lists['fba_fee_hkd'] }}</td>
        </tr>

        {{-- OTHER TRANSACTION FEE --}}
        <tr class="table-info">
            <th><strong>Other Transaction</strong></th>
            <td>Other Transaction</td>
            <td><input type="text" name="other_transaction" class="editable" value="{{ $lists['other_transaction'] }}"
                readonly>
            </td>
            <td>{{ $lists['other_transaction_hkd'] }}</td>
        </tr>
        </tbody>
    </table>

    {{-- Button --}}
    <div class="row justify-content-center align-items-center">
        <div class="col-3">
            <button class="btn btn-primary _fz-1" type="button" id="inline_submit">Submit</button>
        </div>
        <div class="col-3">
            <button class="btn btn-primary _fz-1" type="button" id="cancel_btn">Cancel</button>
        </div>
    </div>

    <hr class="my-2">
    {{-- table --}}
    <div class="row">
        <div class="col">
            <div class="text-warning"><strong>Change Log</strong></div>
        </div>
    </div>
    <hr class="my-2">
    <table class="table table-sm _table table-striped">
        <thead>
        <tr class="bg-primary">
            <th class="text-white" scope="col"><strong>{{'Field Name'}}</strong></th>
            <th class="text-white" scope="col"><strong>{{'Original Value'}}</strong></th>
            <th class="text-white" scope="col"><strong>{{'New Value'}}</strong></th>
            <th class="text-white" scope="col"><strong>{{'Date'}}</strong></th>
            <th class="text-white" scope="col"><strong>{{'User'}}</strong></th>
        </tr>
        </thead>
        <tbody>

        @foreach ($sys_logs as $item)
            <tr>
                <th rowspan="row">{{ $item->field_name }}</th>
                <td>{{ $item->original_value }}</td>
                <td>{{ $item->new_value }}</td>
                <td>{{ \Carbon\Carbon::parse($item->created_at)->setTimezone(config('services.timezone.taipei'))}}</td>
                <td>{{ $item->user_name }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<!-- colorbox html part end -->

<!-- sweetalert JS -->
<script src="{{ asset('js') }}/sweetalert.min.js"></script>
<script src="{{ asset('argon') }}/vendor/jquery/dist/jquery-3.1.0.js"></script>

<!-- jquery colorbox JS -->
<script src="{{ asset('argon') }}/vendor/colorbox/js/jquery.colorbox.js"></script>。

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx"
        crossorigin="anonymous"></script>
<script src="{{ asset('argon') }}/vendor/js-cookie/js.cookie.js"></script>
<!-- Optional JS -->
<script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.min.js"></script>
<script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.extension.js"></script>

@stack('js')

<!-- Argon CSS -->
<link type="text/css" href="{{ asset('argon') }}/vendor/colorbox/css/colorbox.css" rel="stylesheet">
@stack('css')

@push('js')
    <script type="text/javascript">
        $(function () {
            $(document).on("click", "button#cancel_btn", function () {
                $.colorbox.close();
            });
        });
    </script>
@endpush

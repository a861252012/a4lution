<!-- colorbox html part start -->
<div class="container">
    {{--                                            {{ dd(get_defined_vars()) }}--}}
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
            <label class="form-control-label _fz-1" style="font-weight:bold;">Package Id:</label>{{$package_id}}
        </div>

        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Erp Order Id:</label>{{$erp_order_id}}
        </div>

        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Platform:</label>{{$platform}}
        </div>
    </div>

    <div class="row">
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Acc Nick Name:</label>{{$acc_nick_name}}
        </div>

        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Acc Name:</label>{{$acc_name}}
        </div>

        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Site:</label>{{$site_id}}
        </div>
    </div>

    <div class="row">
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Warehouse:</label>{{$warehouse}}
        </div>

        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Shipping Method:</label>{{$lists['sm_code']}}
        </div>

        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1"
                   style="font-weight:bold;">Tracking:</label>{{$lists['tracking_number']}}
        </div>
    </div>

    <div class="row">
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Audit Date:</label>{{$lists['add_time']}}
        </div>

        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Paid Date:</label>{{$lists['order_paydate']}}
        </div>

        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1 inline_shipped_date" style="font-weight:bold;" 
                data-attr="{{$shipped_date}}">Shipped Date:</label>{{$shipped_date}}
        </div>
    </div>

    {{--   PRODUCT DETAILS     --}}
    <div class="row">
        <div class="col">
            <div class="text-warning"><strong>Product Details</strong></div>
        </div>
    </div>
    <hr class="my-2">

    <div class="row">
        <div class="col-12 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Product Name:</label>{{$lists['product_title']}}
        </div>
    </div>

    <div class="row">
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Platform Sku:
            </label>{{$lists['op_platform_sales_sku']}}
        </div>
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1"
                   style="font-weight:bold;">Item Id(ASIN):</label>{{$lists['asin_or_item']}}
        </div>
        <div class="col-4 form-group _fz-1" id="supplier" data-label="{{$supplier}}">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Supplier:</label>{{$supplier}}
        </div>
    </div>

    <div class="row">
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Sku:</label>{{$sku}}
        </div>
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Qty:</label>{{$lists['quantity']}}
        </div>
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Product Weight:</label>{{$lists['weight']}}
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
                Ccurrency:</label>{{$lists['currency_code_org']}}
        </div>
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">HKD Rate:</label>
            {{$exchange_rate[$lists['currency_code_org']]}}
        </div>
    </div>

    <div class="row">
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Erp Original Currency:</label>{{'RMB'}}
        </div>
        <div class="col-4 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">HKD Rate:</label>
            {{$exchange_rate['RMB']}}
        </div>
    </div>

    <div class="row">
        <div class="col-6 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">(AMAZON)Promotion Metadata Definition Value:</label>
            {{$lists['promotion_amount']}}
        </div>
        <div class="col-6 form-group _fz-1">
            <label class="form-control-label _fz-1" style="font-weight:bold;">Percentage Discount:</label>
            {{$lists['promotion_discount_rate']}}
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
            <th class="text-white" scope="col"><strong>RMB</strong></th>
            <th class="text-white" scope="col"><strong>{{$lists['currency_code_org']}}</strong></th>
            <th class="text-white" scope="col"><strong>HKD</strong></th>
        </tr>
        </thead>

        <tbody>

        <tr class="table-success">
            <th scope="row"><strong>Order Price</strong></th>
            <td>Order Price</td>
            <td></td>
            <td>{{$order_price}}</td>
            <td>{{$lists['order_price_hkd']}}</td>
        </tr>

        {{--  PRODUCT COST  --}}
        <tr class="table-info">
            <th rowspan="2"><strong>Product Cost</strong></th>
            <td>Purchase Shipping Fee</td>
            <td>{{$lists['purchase_shipping_fee']}}</td>
            <td></td>
            <td>{{$lists['purchase_shipping_fee_hkd']}}</td>
        </tr>
        <tr class="table-info">
            <th scope="row">Product Cost</th>
            <td>{{$lists['product_cost']}}</td>
            <td></td>
            <td>{{$lists['product_cost_hkd']}}</td>
        </tr>

        {{-- SKIPPING FEE --}}
        <tr>
            <th rowspan="3"><strong>Shipping Fee</strong></th>
            <th>First Mile Shipping Fee</th>
            <td></td>
            <td><input type="text" class="editable" name="first_mile_shipping_fee"
                       value="{{$lists['first_mile_shipping_fee']}}"
                       readonly></td>
            <td>{{$lists['first_mile_shipping_fee_hkd']}}</td>
        </tr>
        <tr>
            <th scope="row">First Mile Tariff</th>
            <td></td>
            <td><input type="text" class="editable" name="first_mile_tariff" value="{{$lists['first_mile_tariff']}}"
                       readonly></td>
            <td>{{$lists['first_mile_tariff_hkd']}}</td>
        </tr>
        <tr>
            <th scope="row">Last Mile Shipping Fee</th>
            <td></td>
            <td><input type="text" class="editable" name="last_mile_shipping_fee"
                       value="{{$lists['last_mile_shipping_fee']}}"
                       readonly></td>
            <td>{{$lists['last_mile_shipping_fee_hkd']}}</td>
        </tr>

        {{-- PLATFORM FEE --}}
        <tr class="table-info">
            <th rowspan="2"><strong>Platform Fee</strong></th>
            <th>Paypal Fee</th>
            <td></td>
            <td><input type="text" name="paypal_fee" class="editable" value="{{$lists['paypal_fee']}}"
                       readonly></td>
            <td>{{$lists['paypal_fee_hkd']}}</td>
        </tr>
        <tr class="table-info">
            <th scope="row">Transaction Fee</th>
            <td></td>
            <td><input type="text" name="transaction_fee" class="editable" value="{{$lists['transaction_fee']}}"
                       readonly></td>
            <td>{{$lists['transaction_fee_hkd']}}</td>
        </tr>

        {{-- FBA FEE --}}
        <tr>
            <th scope="row"><strong>Fba Fee</strong></th>
            <td>Fba Fee</td>
            <td></td>
            <td><input type="text" name="fba_fee" class="editable" value="{{$lists['fba_fee']}}" readonly></td>
            <td>{{$lists['fba_fee_hkd']}}</td>
        </tr>

        {{-- OTHER TRANSACTION FEE --}}
        <tr class="table-info">
            <th rowspan="4"><strong>Other Transaction</strong></th>
            <td>Other Transaction</td>
            <td></td>
            <td><input type="text" name="other_transaction" class="editable" value="{{$lists['other_transaction']}}"
                       readonly>
            </td>
            <td>{{$lists['other_transaction_hkd']}}</td>
        </tr>
        <tr class="table-info">
            {{--            <th rowspan="row">MARKETPLACE TAX</th>--}}
            {{--            <td></td>--}}
            {{--            <td><input type="text" name="marketplace_tax" class="editable" value="{{$lists['marketplace_tax']}}"--}}
            {{--                       readonly></td>--}}
            {{--            <td>{{$lists['marketplace_tax_hkd']}}</td>--}}
        </tr>
        <tr class="table-info">
            {{--            <th rowspan="row">COST OF POINT</th>--}}
            {{--            <td></td>--}}
            {{--            <td><input type="text" name="cost_of_point" class="editable" value="{{$lists['cost_of_point']}}" readonly>--}}
            {{--            </td>--}}
            {{--            <td>{{$lists['cost_of_point_hkd']}}</td>--}}
        </tr>

        <tr class="table-info">
            {{--            <th rowspan="row">EXCLUSIVES REFERRAL FEE</th>--}}
            {{--            <td></td>--}}
            {{--            <td><input type="text" name="exclusives_referral_fee" class="editable"--}}
            {{--                       value="{{$lists['exclusives_referral_fee']}}"--}}
            {{--                       readonly></td>--}}
            {{--            <td>{{$lists['exclusives_referral_fee_hkd']}}</td>--}}
        </tr>
        {{-- GROSS PROFIT --}}
        <tr class="table-dark">
            <th rowspan="row"></th>
            <td></td>
            <td></td>
            <td><strong>Gross Profit</strong></td>
            <td><strong>{{$lists['gross_profit']}}</strong></td>
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
                <th rowspan="row">{{$item->field_name}}</th>
                <td>{{$item->original_value}}</td>
                <td>{{$item->new_value}}</td>
                <td>{{$item->created_at}}</td>
                <td>{{$item->user_name}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>


</div>
{{--        </div>--}}
{{--    </div>--}}
<!-- colorbox html part end -->

<!-- sweetalert JS -->
<script src="{{ asset('js') }}/sweetalert.min.js"></script>

<script src="{{ asset('argon') }}/vendor/jquery/dist/jquery-3.1.0.js"></script>

{{--<script src="{{ asset('argon') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>--}}
{{--<script src="{{ asset('argon') }}/vendor/js-cookie/js.cookie.js"></script>--}}
{{--<script src="{{ asset('argon') }}/vendor/jquery.scrollbar/jquery.scrollbar.min.js"></script>--}}
{{--<script src="{{ asset('argon') }}/vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js"></script>--}}
{{--<script src="{{ asset('argon') }}/vendor/lavalamp/js/jquery.lavalamp.min.js"></script>--}}

<!-- Optional JS -->
{{--<script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.min.js"></script>--}}
{{--<script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.extension.js"></script>--}}

<!-- jquery colorbox JS -->
<script src="{{ asset('argon') }}/vendor/colorbox/js/jquery.colorbox.js"></script>。

{{--<script src="https://code.jquery.com/jquery-3.1.0.js"></script>--}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx"
        crossorigin="anonymous"></script>
<script src="{{ asset('argon') }}/vendor/js-cookie/js.cookie.js"></script>
<!-- Optional JS -->
<script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.min.js"></script>
<script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.extension.js"></script>

{{--<script src="http://code.jquery.com/jquery-1.9.1.js"></script>--}}
@stack('js')

<!-- Argon CSS -->
<link type="text/css" href="{{ asset('argon') }}/vendor/colorbox/css/colorbox.css" rel="stylesheet">
@stack('css')

@push('js')
    <script type="text/javascript">
        $(function () {
            $(document).on("click", "button#cancel_btn", function () {
                $.colorbox.close();
                // return false;
            });
        });

        // $(document).bind('cbox_complete', function () {
        //     $('button#inline_submit').hide();
        //
        //     $(document).on("click", "button#cancel_btn", function () {
        //         // $.colorbox.close();
        //         // parent.$.colorbox.close();
        //
        //         $('#cboxOverlay').remove();
        //         $('#colorbox').remove();
        //
        //         // return false;
        //     });
        //
        //     //edit function
        //     $(document).on("click", "button#edit_btn", function () {
        //         let shipped_date = $("div .inline_shipped_date").attr("data-attr");
        //         let _token = $('meta[name="csrf-token"]').attr('content');
        //         let supplier = $('div#supplier').attr('data-label');
        //
        //         $.ajax({
        //             url: origin + '/orders/checkEditQualification',
        //             data: {shipped_date: shipped_date, _token: _token, supplier: supplier},
        //             type: 'post',
        //             success: function (res) {
        //                 if (res.status !== 'failed') {
        //                     swal({
        //                         icon: 'success',
        //                         text: 'editable now'
        //                     });
        //                     $('input[type=text]').attr('readonly', false);
        //                     $('button#inline_submit').show();
        //                 } else {
        //                     swal({
        //                         icon: 'error',
        //                         text: res.msg
        //                     });
        //                     $('button#inline_submit').hide();
        //                 }
        //             }
        //         });
        //     });
        //
        //     //submit function
        //     $(document).on("click", "button#inline_submit", function () {
        //         let data = {};
        //
        //         data.first_mile_shipping_fee = $("input[name='first_mile_shipping_fee']").val();
        //         data.first_mile_tariff = $("input[name='first_mile_tariff']").val();
        //         data.last_mile_shipping_fee = $("input[name='last_mile_shipping_fee']").val();
        //         data.paypal_fee = $("input[name='paypal_fee']").val();
        //         data.transaction_fee = $("input[name='transaction_fee']").val();
        //         data.fba_fee = $("input[name='fba_fee']").val();
        //         data.other_fee = $("input[name='other_fee']").val();
        //         data.marketplace_tax = $("input[name='marketplace_tax']").val();
        //         data.cost_of_point = $("input[name='cost_of_point']").val();
        //         data.exclusives_referral_fee = $("input[name='exclusives_referral_fee']").val();
        //         data.product_id = $("button#edit_btn").attr('data-attr');
        //
        //         $.ajaxSetup({
        //             headers: {
        //                 'X-CSRF-TOKEN': $('#csrf_token').val()
        //             }
        //         });
        //
        //         $.ajax({
        //             url: origin + '/orders/edit/orderDetail',
        //             data: data,
        //             type: 'post',
        //             success: function (res) {
        //                 if (res.status === 'failed') {
        //                     swal({
        //                         icon: 'error',
        //                         text: 'update failed'
        //                     });
        //                 } else {
        //                     swal({
        //                         icon: res.status,
        //                         text: res.msg
        //                     });
        //                     $('input[type=text]').attr('readonly', false);
        //                 }
        //             }, error: function (error) {
        //                 swal({
        //                     icon: 'error',
        //                     text: error
        //                 });
        //             }
        //         });
        //     });
        // });

    </script>
@endpush
@push('css')
    <style>
        /*input[type=text] {*/
        /*    background: transparent;*/
        /*    border: none;*/
        /*    border-bottom: 1px solid #000000;*/
        /*}*/
    </style>
@endpush

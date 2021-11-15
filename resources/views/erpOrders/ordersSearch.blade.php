@extends('layouts.app', [
    'parentSection' => 'erp-orders',
    'elementName' => 'ORDER-SEARCH'
])

@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('ERP ORDERS') }}
            @endslot
            <li class="breadcrumb-item"><a href="{{ route('erpOrder.view') }}">{{ __('ERP ORDERS') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('ORDER SEARCH') }}</li>
        @endcomponent
    @endcomponent

    {{--@section('content')--}}
    {{--    @include('forms.header')--}}

    {{--    <div class="container-fluid mt--6">--}}
    <div class="wrapper wrapper-content animated">
        <!-- Table -->
        <div class="row">
            <div class="col">
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        {{--                        <h3 class="mb-0">Datatable</h3>--}}
                        {{--                        <div class="row input-daterange datepicker align-items-center">--}}
                        <div>
                            <form method="GET" action="/orders/search" role="form" onsubmit="return validateForm()"
                                  class="form">
                                <div class="row">
                                    {{-- ACC NICK NAME --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="acc_nick_name">ACC NICK NAME</label>
                                            <input class="form-control" name="acc_nick_name" id="acc_nick_name"
                                                   type="text" placeholder="ACC NICK NAME"
                                                   value="{{$data['acc_nick_name'] ?? ''}}">
                                        </div>
                                    </div>

                                    {{-- ERP ORDER ID --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="erp_order_id">ERP ORDER ID</label>
                                            <input class="form-control" name="erp_order_id" id="erp_order_id"
                                                   type="text" placeholder="ERP ORDER ID"
                                                   value="{{$data['erp_order_id'] ?? ''}}">
                                        </div>
                                    </div>

                                    {{-- SHIPPED DATE --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="shipped_date">SHIPPED DATE</label>
                                            <input class="form-control" name="shipped_date" id="shipped_date"
                                                   type="text"
                                                   placeholder="SHIPPED DATE" value="{{$data['shipped_date'] ?? ''}}">
                                        </div>
                                    </div>

                                    {{-- PACKAGE ID --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="package_id">PACKAGE ID</label>
                                            <input class="form-control" name="package_id" id="package_id" type="text"
                                                   placeholder="PACKAGE ID" value="{{$data['package_id'] ?? ''}}">
                                        </div>
                                    </div>

                                    {{-- SKU --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="sku">SKU</label>
                                            <input class="form-control" name="sku" id="sku" type="text"
                                                   placeholder="SKU" value="{{$data['sku'] ?? ''}}">
                                        </div>
                                    </div>

                                    {{-- SEARCH --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <label class="form-control-label" for="submit_btn"></label>
                                        <div class="form-group">
                                            <button class="form-control btn btn-primary" id="submit_btn" type="submit"
                                                    style="margin-top: 6px;">SEARCH
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- data table --}}
                    <div class="table-responsive py-4">
                        <table class="table table-flush">
                            <thead class="thead-light">
                            <tr>
                                <th>DETAILS</th>
                                <th>PLATFORM</th>
                                <th>ACC NICK NAME</th>
                                <th>ACC NAME</th>
                                <th>SITE</th>
                                <th>SHIPPED DATE</th>
                                <th>PACKAGE ID</th>
                                <th>ERP ORDER ID</th>
                                <th>SKU</th>
                                <th>ORDER PRICE</th>
                                <th>SUPPLIER</th>
                                <th>WAREHOUSE</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($data['lists'] as $item)
                                <tr>
                                    {{-- edit --}}
                                    <td>
                                        <a class="ajax_btn form-control btn">
                                            <div>
                                                <i class="ni ni-settings"></i>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="platform">{{ $item->platform }}</td>
                                    <td class="acc_nick_name">{{ $item->acc_nick_name }}</td>
                                    <td class="acc_name">{{ $item->acc_name }}</td>
                                    <td class="site_id">{{ $item->site_id }}</td>
                                    <td class="shipped_date">{{ $item->shipped_date }}</td>
                                    <td class="package_id">{{ $item->package_id }}</td>
                                    <td class="erp_order_id">{{ $item->erp_order_id }}</td>
                                    <td class="sku">{{ $item->sku }}</td>
                                    <td class="order_price">{{ $item->order_price }}</td>
                                    <td class="supplier">{{ $item->supplier }}</td>
                                    <td class="warehouse">{{ $item->warehouse }}</td>
                                    <input class="hidden" type="hidden" value="{{$item->currency_code_org}}">
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-center" style='margin-top: 20px;'>
                            {!! $data['lists']->links() !!}
                        </div>

                    </div>

                </div>
            </div>
        </div>
        <!-- Footer -->
        {{--        @include('layouts.footers.auth')--}}
    </div>
@endsection

@push('js')
    <script src="{{ asset('argon') }}/vendor/select2/dist/js/select2.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <script type="text/javascript">
        $(function () {

            let shippedDate = $('#shipped_date').val();

            $('#shipped_date').datepicker({
                format: 'yyyy-mm-dd',//日期時間格式
                ignoreReadonly: false,  //禁止使用者輸入 啟用唯讀
                autoclose: true
            });

            $('#report_date').datepicker('update', shippedDate);

            $("a.ajax_btn").click(function () {
                let data = [];

                data['_token'] = $('meta[name="csrf-token"]').attr('content');
                data['platform'] = $(this).parent().parent().find('[class="platform"]').text();
                data['acc_nick_name'] = $(this).parent().parent().find('[class="acc_nick_name"]').text();
                data['acc_name'] = $(this).parent().parent().find('[class="acc_name"]').text();
                data['site_id'] = $(this).parent().parent().find('[class="site_id"]').text();
                data['shipped_date'] = $(this).parent().parent().find('[class="shipped_date"]').text();
                data['package_id'] = $(this).parent().parent().find('[class="package_id"]').text();
                data['erp_order_id'] = $(this).parent().parent().find('[class="erp_order_id"]').text();
                data['sku'] = $(this).parent().parent().find('[class="sku"]').text();
                data['order_price'] = $(this).parent().parent().find('[class="order_price"]').text();
                data['supplier'] = $(this).parent().parent().find('[class="supplier"]').text();
                data['warehouse'] = $(this).parent().parent().find('[class="warehouse"]').text();
                data['product_id'] = $(this).attr("data-attr");
                data['base_currency'] = $(this).parent().parent().find('input[type="hidden"]').val();

                let keep = true;
                $.ajax({
                    async: false,
                    url: origin + '/orders/checkRate',
                    data: {shipped_date: data['shipped_date'], _token: data['_token'], currency: data['base_currency']},
                    type: 'post',
                    success: function (res) {
                        if (res.status === 'failed') {
                            swal({
                                icon: 'error',
                                text: res.msg
                            });
                            keep = false;
                        }
                    }
                });

                if (!keep) {
                    return false;
                }

                $.colorbox({
                    iframe: false,
                    href: origin + '/orders/edit',
                    width: "60%",
                    height: "100%",
                    returnFocus: false,
                    data: {
                        _token: data['_token'],
                        platform: data['platform'],
                        acc_nick_name: data['acc_nick_name'],
                        acc_name: data['acc_name'],
                        site_id: data['site_id'],
                        shipped_date: data['shipped_date'],
                        package_id: data['package_id'],
                        erp_order_id: data['erp_order_id'],
                        sku: data['sku'],
                        order_price: data['order_price'],
                        supplier: data['supplier'],
                        warehouse: data['warehouse'],
                    },
                    onComplete: function () {
                        $("button#cancel_btn").unbind("click");
                        $("button#edit_btn").unbind("click");
                        $("button#inline_submit").unbind("click");

                        $('button#inline_submit').hide();

                        $('input.editable').attr("style", "background: transparent; border: none;")
                        $("button#cancel_btn").click(function () {
                            $('#cboxOverlay').remove();
                            $('#colorbox').remove();
                        });

                        //edit function
                        $("button#edit_btn").click(function () {
                            let shipped_date = $("div .inline_shipped_date").attr("data-attr");
                            let _token = $('meta[name="csrf-token"]').attr('content');
                            let supplier = $('div#supplier').attr('data-label');

                            $.ajax({
                                url: origin + '/orders/checkEditQualification',
                                data: {shipped_date: shipped_date, _token: _token, supplier: supplier},
                                type: 'post',
                                success: function (res) {
                                    if (res.status !== 'failed') {
                                        swal({
                                            icon: 'success',
                                            text: 'editable now'
                                        });
                                        $('input.editable').attr('readonly', false);
                                        $('input.editable').attr("style", "background:transparent;")
                                        $('button#inline_submit').show();

                                    } else {
                                        swal({
                                            icon: 'error',
                                            text: res.msg
                                        });
                                        $('button#inline_submit').hide();
                                    }
                                }
                            });
                        });

                        //submit function
                        $("button#inline_submit").click(function () {
                            let id = $("button#edit_btn").attr('data-attr');
                            let data = {};
                            data.first_mile_shipping_fee = $("input[name='first_mile_shipping_fee']").val();
                            data.first_mile_tariff = $("input[name='first_mile_tariff']").val();
                            data.last_mile_shipping_fee = $("input[name='last_mile_shipping_fee']").val();
                            data.paypal_fee = $("input[name='paypal_fee']").val();
                            data.transaction_fee = $("input[name='transaction_fee']").val();
                            data.fba_fee = $("input[name='fba_fee']").val();
                            data.other_transaction = $("input[name='other_transaction']").val();
                            // data.product_id = $("button#edit_btn").attr('data-attr');

                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('#csrf_token').val()
                                }
                            });

                            $.ajax({
                                url: origin + '/orders/orderDetail/' + id,
                                data: data,
                                type: 'put',
                                success: function (res) {
                                    if (res.status === 'failed') {
                                        swal({
                                            icon: 'error',
                                            text: 'update failed'
                                        });
                                    } else {
                                        swal({
                                            icon: res.status,
                                            text: res.msg
                                        });
                                        $('input[type=text]').attr('readonly', false);
                                    }
                                }, error: function (error) {
                                    swal({
                                        icon: 'error',
                                        text: error
                                    });
                                }
                            });
                        });
                    }
                });
                // $.colorbox.resize();
            });
        });

        function validateForm() {
            let accNickName = $('#acc_nick_name').val();
            let erpOrderId = $('#erp_order_id').val();
            let shippedDate = $('#shipped_date').val();
            let packageID = $('#package_id').val();
            let sku = $('#sku').val();
            if (!accNickName && !erpOrderId && !shippedDate && !packageID && !sku) {
                swal({
                    icon: "warning",
                    text: "must have at least one search condition"
                });
                return false;
            }
        }
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

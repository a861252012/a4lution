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

    <div class="wrapper wrapper-content">
        <!-- Table -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                <form method="GET" action="{{route(('erpOrder.view'))}}" role="form"
                      onsubmit="return validateForm()">
                    <div class="row">

                        {{-- ERP ORDER ID --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="erp_order_id">Erp Order Id</label>
                                <input class="form-control _fz-1" name="erp_order_id" id="erp_order_id"
                                       type="text" placeholder="ERP ORDER ID"
                                       value="{{ request()->input('erp_order_id') }}">
                            </div>
                        </div>

                        {{-- SHIPPED DATE --}}
                        <div class="col-lg-4 col-md-12 col-sm-12">
                            <label class="form-control-label _fz-1" for="shipped_date_from">Shipped Date</label>
                            <div class="input-group input-daterange mb-0">
                                <input class="form-control _fz-1" name="shipped_date_from" id="shipped_date_from"
                                       type="text" placeholder="SHIPPED DATE FROM"
                                       value="{{ request()->input('shipped_date_from') }}">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-secondary">to</span>
                                </div>
                                <input class="form-control _fz-1" name="shipped_date_to" id="shipped_date_to"
                                       type="text" placeholder="SHIPPED DATE TO"
                                       value="{{ request()->input('shipped_date_to') }}">
                            </div>
                        </div>

                        {{-- SKU --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="sku">Sku</label>
                                <input class="form-control _fz-1" name="sku" id="sku" type="text"
                                       placeholder="SKU" value="{{ request()->input('sku') }}">
                            </div>
                        </div>

                        {{-- Supplier --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="supplier">Supplier</label>
                                <input class="form-control _fz-1" name="supplier" id="supplier" type="text"
                                       placeholder="SUPPLIER" value="{{ request()->input('supplier') }}">
                            </div>
                        </div>

                        {{-- SEARCH --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <label class="form-control-label _fz-1" for="submit_btn"></label>
                            <div class="form-group mb-0">
                                <button class="form-control _fz-1 btn btn-primary" id="submit_btn" type="submit"
                                        style="margin-top: 6px;">SEARCH
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            {{-- data table --}}
            <div class="table-responsive">
                <table class="table table-sm _table">
                    <thead class="thead-light">
                    <tr>
                        <th>Details</th>
                        <th>Platform</th>
                        <th>Acc Name</th>
                        <th>Site</th>
                        <th>Shipped Date</th>
                        <th>Erp Order Id</th>
                        <th>Sku</th>
                        <th>Order Price</th>
                        <th>Supplier</th>
                        <th>Last Updated</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($data['lists'] as $item)
                        <tr>
                            {{-- edit  button --}}
                            <td>
                                <a class="ajax_btn btn p-0">
                                    <i class="ni ni-settings"></i>
                                </a>
                            </td>
                            <td class="platform">{{ $item->platform }}</td>
                            <td class="acc_name">{{ $item->acc_name }}</td>
                            <td class="site_id">{{ $item->site_id }}</td>
                            <td class="shipped_date">{{ $item->shipped_date }}</td>
                            <td class="erp_order_id">{{ $item->erp_order_id }}</td>
                            <td class="sku">{{ $item->sku }}</td>
                            <td class="order_price">{{ $item->order_price }}</td>
                            <td class="supplier">{{ $item->supplier }}</td>
                            <td class="updated_at">{{ \Carbon\Carbon::parse($item->updated_at)
                                ->setTimezone(config('services.timezone.taipei')) }}</td>
                            <input class="hidden" type="hidden" value="{{ $item->currency_code_org }}">
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                @if($data['lists'] && $data['lists']->lastPage() > 1)
                    <div class="d-flex justify-content-center" style='margin-top: 20px;'>
                        {{ $data['lists']->appends($_GET)->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(function () {
            $('.input-daterange input').each(function () {
                $(this).datepicker({
                    format: 'yyyy-mm-dd',//日期時間格式
                    ignoreReadonly: false,  //禁止使用者輸入 啟用唯讀
                    autoclose: true
                });
            });

            $("a.ajax_btn").click(function () {
                let data = [];

                data['_token'] = $('meta[name="csrf-token"]').attr('content');
                data['platform'] = $(this).parent().parent().find('[class="platform"]').text();
                data['acc_name'] = $(this).parent().parent().find('[class="acc_name"]').text();
                data['site_id'] = $(this).parent().parent().find('[class="site_id"]').text();
                data['shipped_date'] = $(this).parent().parent().find('[class="shipped_date"]').text();
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
                        acc_name: data['acc_name'],
                        site_id: data['site_id'],
                        shipped_date: data['shipped_date'],
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
                            data.first_mile_shipping_fee = parseFloat($("input[name='first_mile_shipping_fee']").val());
                            data.first_mile_tariff = parseFloat($("input[name='first_mile_tariff']").val());
                            data.last_mile_shipping_fee = parseFloat($("input[name='last_mile_shipping_fee']").val());
                            data.paypal_fee = parseFloat($("input[name='paypal_fee']").val());
                            data.transaction_fee = parseFloat($("input[name='transaction_fee']").val());
                            data.fba_fee = parseFloat($("input[name='fba_fee']").val());
                            data.other_fee = parseFloat($("input[name='other_fee']").val());
                            data.marketplace_tax = parseFloat($("input[name='marketplace_tax']").val());
                            data.cost_of_point = parseFloat($("input[name='cost_of_point']").val());
                            data.exclusives_referral_fee = parseFloat($("input[name='exclusives_referral_fee']").val());
                            // data.other_transaction = parseFloat($("input[name='other_transaction']").val());
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
            });
        });

        function validateForm() {
            let erpOrderId = $('#erp_order_id').val();
            let shippedDateFrom = $('#shipped_date_from').val();
            let shippedDateTo = $('#shipped_date_to').val();
            let sku = $('#sku').val();
            if (!erpOrderId && !(shippedDateFrom && shippedDateTo) && !sku) {
                swal({
                    icon: "warning",
                    text: "must have at least one search condition"
                });
                return false;
            }
        }
    </script>
@endpush
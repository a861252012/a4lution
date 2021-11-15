@extends('layouts.app', [
    'parentSection' => 'erp-orders',
    'elementName' => 'refund'
])

@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('ERP ORDERS') }}
            @endslot
            <li class="breadcrumb-item"><a href="{{ route('refundOrder.view') }}">{{ __('ERP ORDERS') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('REFUND') }}</li>
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
                            <form method="GET" action="/refund/search" onsubmit="return validateForm()" role="form"
                                  class="form">

                                <div class="row">

                                    {{-- ERP ORDER ID --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="erp_order_ID">ERP ORDER ID</label>
                                            <input class="form-control" name="erp_order_ID" id="erp_order_ID"
                                                   type="text" placeholder="ERP ORDER ID"
                                                   value="{{$data['erp_order_ID'] ?? ''}}">
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

                                    {{-- SHIPPED DATE --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="shipped_date">SHIPPED DATE</label>
                                            <input class="form-control" name="shipped_date" id="shipped_date"
                                                   type="text"
                                                   placeholder="SHIPPED DATE" value="{{$data['shipped_date'] ?? ''}}"
                                            >
                                        </div>
                                    </div>

                                    {{-- WAREHOUSE ORDER ID --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="warehouse_order_id">WAREHOUSE ORDER
                                                ID</label>
                                            <input class="form-control" name="warehouse_order_id" type="text"
                                                   id="warehouse_order_id" placeholder="WAREHOUSE ORDER ID"
                                                   value="{{$data['warehouse_order_id'] ?? ''}}">
                                        </div>
                                    </div>

                                    {{-- SUPPLIER --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="supplier">SUPPLIER</label>
                                            <input class="form-control" name="supplier" id="supplier" type="text"
                                                   placeholder="SUPPLIER" value="{{$data['supplier'] ?? ''}}">
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
                                <th>CREATE DATE</th>
                                <th>WAREHOUSE ORDER ID</th>
                                <th>REFUND ORDER ID</th>
                                <th>ERP ORDER ID</th>
                                <th>SHIPPED DATE</th>
                                <th>SKU</th>
                                <th>SUPPLIER</th>
                                <th>REFUND PRICE</th>
                                <th>TRANSACTION AMOUNT</th>
                                <th>SALES VOLUME</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($data['lists'] as $item)
                                <tr>
                                    <td>{{ $item->create_date }}</td>
                                    <td>{{ $item->warehouse_order_id }}</td>
                                    <td>{{ $item->refund_order_id }}</td>
                                    <td>{{ $item->erp_order_id }}</td>
                                    <td>{{ $item->shipped_date }}</td>
                                    <td>{{ $item->sku }}</td>
                                    <td>{{ $item->supplier }}</td>
                                    <td>{{ $item->refund_price }}</td>
                                    <td>{{ $item->transaction_amount }}</td>
                                    <td>{{ $item->sales_vloume }}</td>
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
        });

        function validateForm() {
            let erpOrderID = $('#erp_order_ID').val();
            let sku = $('#sku').val();
            let shippedDate = $('#shipped_date').val();
            let warehouseOrderID = $('#warehouse_order_id').val();
            let supplier = $('#supplier').val();
            if (!erpOrderID && !sku && !shippedDate && !warehouseOrderID && !supplier) {
                swal({
                    icon: "warning",
                    text: "must have at least one search condition"
                });
                return false;
            }
        }
    </script>
@endpush

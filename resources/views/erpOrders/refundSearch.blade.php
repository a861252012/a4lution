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

    <div class="wrapper wrapper-content">
        <!-- Table -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                <div>
                    <form method="GET" action="{{ route('refundOrder.view') }}" onsubmit="return validateForm()"
                          role="form">
                        <div class="row">

                            {{-- ERP ORDER ID --}}
                            <div class="col-lg-2 col-md-6 col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="form-control-label _fz-1" for="erp_order_ID">Erp Order Id</label>
                                    <input class="form-control _fz-1" name="erp_order_ID" id="erp_order_ID"
                                           type="text" placeholder="ERP ORDER ID"
                                           value="{{ request()->input('erp_order_ID') }}">
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

                            {{-- SHIPPED DATE RANGE --}}
                            <div class="col-lg-3 col-md-12 col-sm-12">
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

                            {{-- WAREHOUSE ORDER ID --}}
                            <div class="col-lg-2 col-md-6 col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="form-control-label _fz-1" for="warehouse_order_id">Warehouse Order Id</label>
                                    <input class="form-control _fz-1" name="warehouse_order_id" type="text"
                                           id="warehouse_order_id" placeholder="WAREHOUSE ORDER ID"
                                           value="{{ request()->input('warehouse_order_id') }}">
                                </div>
                            </div>

                            {{-- SUPPLIER --}}
                            <div class="col-lg-1 col-md-6 col-sm-6">
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
                                            style="margin-top: 6px;">Search
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            {{-- data table --}}
            <div class="table-responsive">
                <table class="table table-sm _table">
                    <thead class="thead-light">
                    <tr>
                        <th>Create Date</th>
                        <th>Warehouse Order Id</th>
                        <th>Refund Order Id</th>
                        <th>Erp Order Id</th>
                        <th>Shipped Date</th>
                        <th>Sku</th>
                        <th>Supplier</th>
                        <th>Refund Price</th>
                        <th>Transaction Amount</th>
                        <th>Sales Volume</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($data['lists'] as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->create_date)
                                    ->setTimezone(config('services.timezone.taipei')) }}</td>
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
            let shippedDateFrom = $('#shipped_date_from').val();
            let shippedDateTo = $('#shipped_date_to').val();
            let warehouseOrderID = $('#warehouse_order_id').val();
            let supplier = $('#supplier').val();
            if (!erpOrderID && !sku && !(shippedDateFrom && shippedDateTo) && !warehouseOrderID && !supplier) {
                swal({
                    icon: "warning",
                    text: "must have at least one search condition"
                });
                return false;
            }
        }
    </script>
@endpush

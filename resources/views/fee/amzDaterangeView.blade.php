{{--@extends('layouts.app', [--}}
{{--    'title' => __('User Profile'),--}}
{{--    'navClass' => 'bg-default',--}}
{{--    'parentSection' => 'laravel',--}}
{{--    'elementName' => 'profile'--}}
{{--])--}}

@extends('layouts.app', [
    'parentSection' => 'fee',
    'elementName' => 'amz-date-range'
])

@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('FEE') }}
            @endslot
            <li class="breadcrumb-item"><a href="{{ route('page.index', 'components') }}">{{ __('FEE') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('AMZDATERANGE') }}</li>
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
                            <form method="GET" action="/fee/amzdaterange" role="form" class="form">

                                <div class="row">

                                    {{-- REPORT DATE --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="report_date">REPORT DATE</label>
                                            <input class="form-control" name="report_date" id="report_date" type="text"
                                                   placeholder="report date" value="{{$data['reportDate']}}" readonly>
                                        </div>
                                    </div>

                                    {{-- ORDER ID --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="order_id">ORDER ID</label>
                                            <input class="form-control" name="order_id" id="order_id"
                                                   value="{{$data['orderID']}}" placeholder="ORDER ID" type="text">
                                        </div>
                                    </div>

                                    {{-- SKU --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="sku">SKU</label>
                                            <input class="form-control" name="sku" id="sku" placeholder="SKU"
                                                   value="{{$data['sku']}}" type="text">
                                        </div>
                                    </div>

                                    {{-- SUPPLIER --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="supplier">SUPPLIER</label>
                                            <input class="form-control" name="supplier" id="supplier"
                                                   placeholder="SUPPLIER" value="{{$data['supplier']}}" type="text">
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
                                <th>REPORT DATE</th>
                                <th>ACCOUNT</th>
                                <th>ORDER ID</th>
                                <th>SKU</th>
                                <th>SUPPLIER</th>
                                <th>QUANTITY</th>
                                <th>CURRENCY</th>
                                <th>PRODUCT SALES</th>
                                <th>SHIPPING CREDITS</th>
                                <th>GIFT WRAP CREDITS</th>
                                <th>PROMOTIONAL REBATES</th>
                                <th>COST OF POINT</th>
                                <th>TAX</th>
                                <th>SELLING FEES</th>
                                <th>FBA FEES</th>
                                <th>OTHER TRAN</th>
                                <th>OTHER</th>
                                <th>AMAZON TOTAL</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($data['lists'] as $item)
                                <tr>
                                    <td>{{ $item->report_date }}</td>
                                    <td>{{ $item->account }}</td>
                                    <td>{{ $item->order_id }}</td>
                                    <td>{{ $item->sku }}</td>
                                    <td>{{ $item->supplier }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->currency }}</td>
                                    <td>{{ $item->product_sales }}</td>
                                    <td>{{ $item->shipping_credits }}</td>
                                    <td>{{ $item->gift_wrap_credits }}</td>
                                    <td>{{ $item->promotional_rebates }}</td>
                                    <td>{{ $item->cost_of_point }}</td>
                                    <td>{{ $item->tax }}</td>
                                    <td>{{ $item->selling_fees }}</td>
                                    <td>{{ $item->fba_fees }}</td>
                                    <td>{{ $item->other_transaction_fees }}</td>
                                    <td>{{ $item->other }}</td>
                                    <td>{{ $item->amazon_total }}</td>
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

            let reportDate = $('#report_date').val();

            $('#report_date').datepicker({
                format: 'yyyy-mm',//日期時間格式
                viewMode: "months",
                minViewMode: "months",
                ignoreReadonly: true,  //禁止使用者輸入 啟用唯讀
                autoclose: true
            });

            // $('#report_date').datepicker('update', new Date());
            $('#report_date').datepicker('update', reportDate);
        });
    </script>
@endpush

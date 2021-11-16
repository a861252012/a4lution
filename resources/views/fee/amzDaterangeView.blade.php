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
            <li class="breadcrumb-item"><a href="{{ route('fee.amzDateRange.view') }}">{{ __('FEE') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('AMZ DATE RANGE') }}</li>
        @endcomponent
    @endcomponent

    <div class="wrapper wrapper-content">
        <!-- Table -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                <form method="GET" action="/fee/amzdaterange" role="form" class="form">
                    <div class="row">

                        {{-- REPORT DATE --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="report_date">Report Date</label>
                                <input class="form-control _fz-1" name="report_date" id="report_date" type="text"
                                       placeholder="Report Date" value="{{$data['reportDate']}}" readonly>
                            </div>
                        </div>

                        {{-- ORDER ID --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="order_id">Order ID</label>
                                <input class="form-control _fz-1" name="order_id" id="order_id"
                                       value="{{$data['orderID']}}" placeholder="Order ID" type="text">
                            </div>
                        </div>

                        {{-- SKU --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="sku">Sku</label>
                                <input class="form-control _fz-1" name="sku" id="sku" placeholder="Sku"
                                       value="{{$data['sku']}}" type="text">
                            </div>
                        </div>

                        {{-- SUPPLIER --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="supplier">Supplier</label>
                                <input class="form-control _fz-1" name="supplier" id="supplier"
                                       placeholder="Supplier" value="{{$data['supplier']}}" type="text">
                            </div>
                        </div>

                        {{-- SEARCH --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <label class="form-control-label" for="submit_btn"></label>
                            <div class="form-group mb-0">
                                <button class="form-control btn btn-primary _btn _fz-1" id="submit_btn" type="submit"
                                        style="margin-top: 6px;">Search
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
                        <th>Report Date</th>
                        <th>Account</th>
                        <th>Order ID</th>
                        <th>Sku</th>
                        <th>Supplier</th>
                        <th>Quantitiy</th>
                        <th>Currency</th>
                        <th>Product Sales</th>
                        <th>Shipping Credits</th>
                        <th>Gift Wrap Credits</th>
                        <th>Promotional Rebates</th>
                        <th>Cost Of Point</th>
                        <th>Tax</th>
                        <th>Selling Fees</th>
                        <th>Fba Fees</th>
                        <th>Other Tran</th>
                        <th>Other</th>
                        <th>Amazon Total</th>
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
    <script src="{{ asset('argon') }}/vendor/select2/dist/js/select2.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <script type="text/javascript">
        $(function () {
            $('#report_date').datepicker({
                format: 'yyyy-mm',//日期時間格式
                viewMode: "months",
                minViewMode: "months",
                ignoreReadonly: true,  //禁止使用者輸入 啟用唯讀
                autoclose: true
            });

            $('#report_date').datepicker('update', $('#report_date').val());
        });
    </script>
@endpush

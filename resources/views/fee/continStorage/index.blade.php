@extends('layouts.app', [
    'parentSection' => 'fee',
    'elementName' => 'first-mile-shipment'
])

@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('FEE') }}
            @endslot
            <li class="breadcrumb-item"><a href="{{ route('fee.continStorage.index') }}">{{ __('FEE') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('CONTIN STORAGE') }}</li>
        @endcomponent
    @endcomponent

    <div class="wrapper wrapper-content">
        <!-- Table -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                <form method="GET" action="{{ route('fee.continStorage.index') }}" role="form" class="form">
                    <div class="row">

                        {{-- REPORT DATE --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="report_date">Report Date</label>
                                <input class="form-control _fz-1" name="report_date" id="report_date"
                                    type="text" placeholder="report date"
                                    value="{{ $reportDate }}" readonly>
                            </div>
                        </div>

                        {{-- SUPPLIER --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="supplier">Supplier</label>
                                <input class="form-control _fz-1" name="supplier" id="supplier"
                                    type="text" placeholder="Supplier"
                                    value="{{ request('supplier') }}">
                            </div>
                        </div>

                        {{-- SEARCH --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <label class="form-control-label" for="submit_btn"></label>
                            <div class="form-group mb-0">
                                <button class="form-control btn _btn btn-primary _fz-1" id="submit_btn" type="submit"
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
                        <th>Transaction No</th>
                        <th>Billing Period</th>
                        <th>Warehouse</th>
                        <th>Supplier</th>
                        <th>Transaction Date</th>
                        <th>Volume</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                        <th>Currency</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($continStorageFees as $fee)
                        <tr>
                            <td>{{ $fee->report_date->format('F-Y') }}</td>
                            <td>{{ $fee->transaction_no }}</td>
                            <td>{{ $fee->billing_period }}</td>
                            <td>{{ $fee->warehouse_code }}</td>
                            <td>{{ $fee->supplier }}</td>
                            <td>{{ $fee->transaction_datetime }}</td>
                            <td>{{ $fee->volume }}</td>
                            <td>{{ $fee->quantity }}</td>
                            <td>{{ $fee->amount }}</td>
                            <td>{{ $fee->currency }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                @if($continStorageFees && $continStorageFees->lastPage() > 1)
                    <div class="d-flex justify-content-center" style='margin-top: 20px;'>
                        {{ $continStorageFees->appends($_GET)->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('argon') }}/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <script type="text/javascript">
        $(function () {
            $('#report_date').datepicker({
                format: 'yyyy-mm', //日期時間格式
                viewMode: "months",
                minViewMode: "months",
                ignoreReadonly: true,  //禁止使用者輸入 啟用唯讀
                autoclose: true
            });

            $('#report_date').datepicker('update', $('#report_date').val());
        });
    </script>
@endpush

{{--@extends('layouts.app', [--}}
{{--    'title' => __('User Profile'),--}}
{{--    'navClass' => 'bg-default',--}}
{{--    'parentSection' => 'laravel',--}}
{{--    'elementName' => 'profile'--}}
{{--])--}}

@extends('layouts.app', [
    'parentSection' => 'fee',
    'elementName' => 'platform-ads-view'
])

@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('FEE') }}
            @endslot
            <li class="breadcrumb-item"><a href="{{ route('page.index', 'components') }}">{{ __('FEE') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('PLATFORMADSVIEW') }}</li>
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
                            <form method="GET" action="/fee/platformads" role="form" class="form">

                                <div class="row">

                                    {{-- REPORT DATE --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="report_date">REPORT DATE</label>
                                            <input class="form-control" name="report_date" id="report_date" type="text"
                                                   placeholder="report date" value="{{$data['reportDate'] ?? ''}}"
                                                   readonly>
                                        </div>
                                    </div>

                                    {{-- SUPPLIER --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="supplier">SUPPLIER</label>
                                            <input class="form-control" name="supplier" id="supplier"
                                                   placeholder="SUPPLIER" type="text"
                                                   value="{{$data['supplier'] ?? ''}}">
                                        </div>
                                    </div>

                                    {{-- PLATFORM --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="platform">PLATFORM</label>
                                            <input class="form-control" name="platform" id="platform" type="text"
                                                   value="{{$data['platform'] ?? ''}}" placeholder="PLATFORM">
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
                                <th>SUPPLIER</th>
                                <th>PLATFORM</th>
                                <th>ACCOUNT</th>
                                <th>CAMPAGIN TYPE</th>
                                <th>CAMPAGIN</th>
                                <th>IMPRESSIONS</th>
                                <th>CURRENCY</th>
                                <th>CLICKS</th>
                                <th>CTR</th>
                                <th>SPENDINGS</th>
                                <th>SPENDINGS HKD</th>
                                <th>SALES QTY</th>
                                <th>SALES AMOUNT</th>
                                <th>SALES AMOUNT HKD</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($data['lists'] as $item)
                                <tr>
                                    <td>{{ $item->report_date }}</td>
                                    <td>{{ $item->supplier }}</td>
                                    <td>{{ $item->platform }}</td>
                                    <td>{{ $item->account }}</td>
                                    <td>{{ $item->campagin_type }}</td>
                                    <td>{{ $item->campagin }}</td>
                                    <td>{{ $item->Impressions }}</td>
                                    <td>{{ $item->currency }}</td>
                                    <td>{{ $item->clicks }}</td>
                                    <td>{{ $item->ctr }}</td>
                                    <td>{{ $item->spendings }}</td>
                                    <td>{{ $item->spendings_hkd }}</td>
                                    <td>{{ $item->sales_qty }}</td>
                                    <td>{{ $item->sales_amount }}</td>
                                    <td>{{ $item->sales_amount_hkd }}</td>
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

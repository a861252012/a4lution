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
            <li class="breadcrumb-item"><a href="{{ route('fee.platformAds.view') }}">{{ __('FEE') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('PLATFORM ADS') }}</li>
        @endcomponent
    @endcomponent

    <div class="wrapper wrapper-content">
        <!-- Table -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                <form method="GET" action="/fee/platformads" role="form" class="form">
                    <div class="row">
                        {{-- REPORT DATE --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="report_date">Report Date</label>
                                <input class="form-control _fz-1" name="report_date" id="report_date" type="text"
                                       placeholder="report date" value="{{ request('report_date') }}" readonly>
                            </div>
                        </div>

                        {{-- SUPPLIER --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="supplier">Supplier</label>
                                <input class="form-control _fz-1" name="supplier" id="supplier"
                                       placeholder="supplier" type="text" value="{{ request('supplier') }}">
                            </div>
                        </div>

                        {{-- PLATFORM --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="platform">Platform</label>
                                <input class="form-control _fz-1" name="platform" id="platform" type="text"
                                       value="{{ request('platform') }}" placeholder="platform">
                            </div>
                        </div>

                        {{-- SEARCH --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <label class="form-control-label _fz-1" for="submit_btn"></label>
                            <div class="form-group mb-0">
                                <button class="form-control btn btn-primary _fz-1 _btn" id="submit_btn" type="submit"
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
                        <th>Supplier</th>
                        <th>Platform</th>
                        <th>Account</th>
                        <th>Campaign Type</th>
                        <th>Campaign</th>
                        <th>Impressions</th>
                        <th>Currency</th>
                        <th>Clicks</th>
                        <th>Ctr</th>
                        <th>Spendings</th>
                        <th>Spendings HKD</th>
                        <th>Sales Qty</th>
                        <th>Sales Amount</th>
                        <th>Sales Amount HKD</th>
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

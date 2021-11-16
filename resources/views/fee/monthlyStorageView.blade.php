@extends('layouts.app', [
    'parentSection' => 'fee',
    'elementName' => 'monthly-storage'
])

@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('FEE') }}
            @endslot
            <li class="breadcrumb-item"><a href="{{ route('fee.monthlyStorage.view') }}">{{ __('FEE') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('MONTHLY STORAGE') }}</li>
        @endcomponent
    @endcomponent

    <div class="wrapper wrapper-content">
        <!-- Table -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                <form method="GET" action="/fee/monthlystorage" role="form" class="form">
                    <div class="row">

                        {{-- REPORT DATE --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="report_date">Report Date</label>
                                <input class="form-control _fz-1" name="report_date" id="report_date" type="text"
                                       placeholder="report date" value="{{$data['reportDate']}}"
                                       readonly>
                            </div>
                        </div>

                        {{-- SUPPLIER --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="Supplier">Supplier</label>
                                <input class="form-control _fz-1" name="supplier" id="supplier" type="text"
                                       placeholder="Supplier" value="{{$data['supplier'] ?? ''}}">
                            </div>
                        </div>

                        {{-- SEARCH --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <label class="form-control-label _fz-1" for="submit_btn"></label>
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
                        <th>Asin</th>
                        <th>Fnsku</th>
                        <th>Fulfilment Center</th>
                        <th>Country Code</th>
                        <th>Supplier</th>
                        <th>Weight</th>
                        <th>Month Of Change</th>
                        <th>Storage Rate</th>
                        <th>Currency</th>
                        <th>Monthly Storage Fee(est)</th>
                        <th>HKD</th>
                        <th>HKD Rate</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($data['lists'] as $item)
                        <tr>
                            <td>{{ $item->report_date }}</td>
                            <td>{{ $item->asin }}</td>
                            <td>{{ $item->fnsku }}</td>
                            <td>{{ $item->fulfilment_center }}</td>
                            <td>{{ $item->country_code }}</td>
                            <td>{{ $item->supplier }}</td>
                            <td>{{ $item->weight }}</td>
                            <td>{{ $item->month_of_charge }}</td>
                            <td>{{ $item->storage_rate }}</td>
                            <td>{{ $item->currency }}</td>
                            <td>{{ $item->monthly_storage_fee_est }}</td>
                            <td>{{ $item->HKD }}</td>
                            <td>{{ $item->hkd_rate }}</td>
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

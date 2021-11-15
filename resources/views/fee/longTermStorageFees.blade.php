@extends('layouts.app', [
    'parentSection' => 'fee',
    'elementName' => 'long-term-storage-fees'
])

@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('FEE') }}
            @endslot
            <li class="breadcrumb-item"><a href="{{ route('fee.longTermStorage.view') }}">{{ __('FEE') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('LONG TERM STORAGE FEES') }}</li>
        @endcomponent
    @endcomponent

    <div class="wrapper wrapper-content">
        <!-- Table -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                <form method="GET" action="/fee/longtermstorage" role="form" class="form">
                    <div class="row">

                        {{-- REPORT DATE --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="report_date">Report Date</label>
                                <input class="form-control _fz-1" name="report_date" id="report_date" type="text"
                                       placeholder="Report date" value="{{$data['reportDate'] ?? ''}}"
                                       readonly>
                            </div>
                        </div>

                        {{-- SUPPLIER --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="supplier">Supplier</label>
                                <input class="form-control _fz-1" name="supplier" id="supplier" type="text"
                                       placeholder="Supplier" value="{{$data['supplier'] ?? ''}}">
                            </div>
                        </div>

                        {{-- SKU --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="sku">Sku</label>
                                <input class="form-control _fz-1" name="sku" id="sku" type="text"
                                       placeholder="Sku" value="{{$data['sku'] ?? ''}}">
                            </div>
                        </div>

                        {{-- SEARCH --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <label class="form-control-label _fz-1" for="submit_btn"></label>
                            <div class="form-group mb-0">
                                <button class="form-control btn _btn btn-primary _fz-1" id="submit_btn"
                                        type="submit" style="margin-top: 6px;">Search
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
                        <th>Snapshot-Date</th>
                        <th>Sku</th>
                        <th>Fnsku</th>
                        <th>Asin</th>
                        <th>Supplier</th>
                        <th>Condition</th>
                        <th>Currency</th>
                        <th>12-Mo-Long-Terms-Storage-Fee</th>
                        <th>HKD</th>
                        <th>HKD Rate</th>
                        <th>Country</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($data['lists'] as $item)
                        <tr>
                            <td>{{ $item->report_date }}</td>
                            <td>{{ $item->snapshot_date }}</td>
                            <td>{{ $item->sku }}</td>
                            <td>{{ $item->fnsku }}</td>
                            <td>{{ $item->asin }}</td>
                            <td>{{ $item->supplier }}</td>
                            <td>{{ $item->condition }}</td>
                            <td>{{ $item->currency }}</td>
                            <td>{{ $item->fee }}</td>
                            <td>{{ $item->hkd }}</td>
                            <td>{{ $item->hkd_rate }}</td>
                            <td>{{ $item->country }}</td>
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

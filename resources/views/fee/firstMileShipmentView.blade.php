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
            <li class="breadcrumb-item"><a href="{{ route('page.index', 'components') }}">{{ __('FEE') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('FIRSTMILESHIPMENT') }}</li>
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
                            <form method="GET" action="/fee/firstmileshipment" role="form" class="form">

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

                                    {{-- CLIENT CODE --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="fba_shipment">CLIENT CODE</label>
                                            <input class="form-control" name="client_code" id="client_code"
                                                   type="text" placeholder="CLIENT CODE"
                                                   value="{{$data['clientCode'] ?? ''}}">
                                        </div>
                                    </div>

                                    {{-- FBA SHIPMENT --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="fba_shipment">FBA SHIPMENT</label>
                                            <input class="form-control" name="fba_shipment" id="fba_shipment"
                                                   type="text" placeholder="FBA SHIPMENT"
                                                   value="{{$data['fbaShipment'] ?? ''}}">
                                        </div>
                                    </div>

                                    {{-- IDS SKU --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="ids_sku">IDS SKU</label>
                                            <input class="form-control" name="ids_sku" id="ids_sku"
                                                   type="text" placeholder="IDS SKU" value="{{$data['idsSku'] ?? ''}}">
                                        </div>
                                    </div>

                                    {{-- ACCOUNT --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="account">ACCOUNT</label>
                                            <input class="form-control" name="account" id="account"
                                                   type="text" placeholder="ACCOUNT" value="{{$data['account'] ?? ''}}">
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
                                <th>CLIENT CODE</th>
                                <th>IDS SKU</th>
                                <th>ASIN</th>
                                <th>SHIPPED</th>
                                <th>FBA SHIPMENT</th>
                                <th>SHIPMENT TYPE</th>
                                <th>DATE</th>
                                <th>ACCOUNT</th>
                                <th>SHIP FROM</th>
                                <th>FIRST MILE</th>
                                <th>LAST MILE(EST)</th>
                                <th>LAST MILE(ACT)</th>
                                <th>SHIPMENT</th>
                                <th>CURRENCY</th>
                                <th>TOTAL</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($data['lists'] as $item)
                                <tr>
                                    <td>{{ $item->report_date }}</td>
                                    <td>{{ $item->client_code }}</td>
                                    <td>{{ $item->ids_sku }}</td>
                                    <td>{{ $item->asin }}</td>
                                    <td>{{ $item->shipped }}</td>
                                    <td>{{ $item->fba_shipment }}</td>
                                    <td>{{ $item->shipment_type }}</td>
                                    <td>{{ $item->date }}</td>
                                    <td>{{ $item->account }}</td>
                                    <td>{{ $item->ship_from }}</td>
                                    <td>{{ $item->first_mile }}</td>
                                    <td>{{ $item->last_mile_est_orig != null ? '$ ' . $item->last_mile_est_orig : '' }}</td>
                                    <td>{{ $item->last_mile_act_orig }}</td>
                                    <td>{{ $item->shipment_remark }}</td>
                                    <td>{{ $item->currency_last_mile }}</td>
                                    <td>{{ $item->total != null ? '$ ' . $item->total : '' }}</td>
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

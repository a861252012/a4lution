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
            <li class="breadcrumb-item"><a href="{{ route('fee.firstMileShipment.view') }}">{{ __('FEE') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('FIRST MILE SHIPMENT') }}</li>
        @endcomponent
    @endcomponent

    <div class="wrapper wrapper-content">
        <!-- Table -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                <form method="GET" action="/fee/firstmileshipment" role="form" class="form">
                    <div class="row">

                        {{-- REPORT DATE --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="report_date">Report Date</label>
                                <input class="form-control _fz-1" name="report_date" id="report_date"
                                       type="text" placeholder="report date"
                                       value="{{$data['reportDate'] ?? ''}}" readonly>
                            </div>
                        </div>

                        {{-- CLIENT CODE --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="fba_shipment">
                                    Client Code</label>
                                <input class="form-control _fz-1" name="client_code" id="client_code"
                                       type="text" placeholder="Client Code"
                                       value="{{$data['clientCode'] ?? ''}}">
                            </div>
                        </div>

                        {{-- FBA SHIPMENT --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="fba_shipment">Fba Shipment</label>
                                <input class="form-control _fz-1" name="fba_shipment" id="fba_shipment"
                                       type="text" placeholder="Fba Shipment"
                                       value="{{$data['fbaShipment'] ?? ''}}">
                            </div>
                        </div>

                        {{-- IDS SKU --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="ids_sku">Ids Sku</label>
                                <input class="form-control _fz-1" name="ids_sku" id="ids_sku"
                                       type="text" placeholder="Ids Sku" value="{{$data['idsSku'] ?? ''}}">
                            </div>
                        </div>

                        {{-- ACCOUNT --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="account">Account</label>
                                <input class="form-control _fz-1" name="account" id="account"
                                       type="text" placeholder="Account" value="{{$data['account'] ?? ''}}">
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
                        <th>Client Code</th>
                        <th>Ids Sku</th>
                        <th>Asin</th>
                        <th>Shipped</th>
                        <th>Fba Shipment</th>
                        <th>Shipment Type</th>
                        <th>Date</th>
                        <th>Account</th>
                        <th>Ship From</th>
                        <th>First Mile</th>
                        <th>Last Mile(est)</th>
                        <th>Last Mile(act)</th>
                        <th>Shipment</th>
                        <th>Currency</th>
                        <th>Total</th>
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

@extends('layouts.app', [
    'parentSection' => 'System Setting',
    'elementName' => 'Exchange Rate'
])

@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('System Setting') }}
            @endslot

            <li class="breadcrumb-item"><a href="{{ route('fee.upload.view') }}">{{ __('System Setting') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Exchange Rate') }}</li>
        @endcomponent
    @endcomponent

    <div class="wrapper wrapper-content">
        <!-- Table -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                <div class="row">
                    <div class="col-lg-9 col-md-9 col-sm-9 text-left" style="margin-top: 6px;">
                        <div class="h2 text-muted mb-0">
                            Currencies
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-3 text-right">
                        <a class="btn btn-success" href="#inline_content" id="create_btn">
                            <i class="ni ni-fat-add"></i>
                            Add Currency Exchange
                        </a>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm _table" id="datatable-basic">
                    <thead class="thead-light">
                    <tr>
                        <th>Quoted Date</th>
                        <th>Base Currency</th>
                        <th>Quote Currency</th>
                        <th>Exchange Rate</th>
                        <th>Last Updated</th>
                        <th class="col col-1 text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($lists as $item)
                        <tr>
                            <td>{{ $item->quoted_date }}</td>
                            <td>{{ $item->base_currency }}</td>
                            <td>{{ $item->quote_currency }}</td>
                            <td>{{ $item->exchange_rate }}</td>
                            <td>{{ $item->updated_at }}</td>
                            <td>
                                <div class="col-lg-8 col-md-3 col-sm-3 text-right">
                                    <a class="btn btn-success _fz-1 text-white historical_rate_btn"
                                       data-attr="{{$item->base_currency}}">
                                        Historical Exchange Rates
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                {{--                @if($lists && $lists->lastPage() > 1)--}}
                {{--                    <div class="d-flex justify-content-center" style='margin-top: 20px;'>--}}
                {{--                        {{ $lists->appends($_GET)->links() }}--}}
                {{--                    </div>--}}
                {{--                @endif--}}

            </div>
        </div>
    </div>

    <!-- colorbox create form part start -->
    <div style='display:none'>
        <div class="container" id='inline_content'>
            <form id="create_form" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-3">
                        <label class="form-control-label required" for="quoted_date">Quoted Date</label>
                    </div>
                    <div class="col-3">
                        <input type="text" name="quoted_date" class="form-control" id="quoted_date" required>
                    </div>
                </div>

                <hr class="my-2">

                <div class="row">
                    <div class="col">
                        <h2>Currency I Have</h2>
                    </div>
                </div>

                <hr class="my-2">

                <div class="row">
                    <div class="col-3">
                        <label class="form-control-label" for="base_currency">Base Currency</label>
                    </div>

                    <div class="col-3">
                        <input type="text" name="quote_currency" class="form-control" value="HKD" id="base_currency"
                               readonly required>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <h2 class="required">Currency I Want</h2>
                    </div>
                </div>

                <hr class="my-2">

                <!-- TABLE HEADER -->
                <div class="row">
                    <div class="col-4 form-group">Quote Currency</div>
                    <div class="col-4 form-group">Current Amount</div>
                    <div class="col-4 form-group" id="historical_date">Historical Rates(2021-11-04)</div>
                </div>

                <!-- RMB -->
                <div class="row">
                    <div class="col-4 form-group text-right">
                        <label class="form-control-label" for="RMB">RMB</label>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" min="0" name="exchange_rate[RMB]" class="form-control" id="RMB" required>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" class="form-control" name="old_val" id="RMB_old_val" disabled>
                    </div>
                </div>

                <!-- AUD -->
                <div class="row">
                    <div class="col-4 form-group text-right">
                        <label class="form-control-label" for="AUD">AUD</label>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" min="0" name="exchange_rate[AUD]" class="form-control" id="AUD" required>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" class="form-control" name="old_val" id="AUD_old_val" disabled>
                    </div>
                </div>

                <!-- CAD -->
                <div class="row">
                    <div class="col-4 form-group text-right">
                        <label class="form-control-label" for="CAD">CAD</label>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" min="0" name="exchange_rate[CAD]" class="form-control" id="CAD" required>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" class="form-control" name="old_val" id="CAD_old_val" disabled>
                    </div>
                </div>

                <!-- EUR -->
                <div class="row">
                    <div class="col-4 form-group text-right">
                        <label class="form-control-label" for="EUR">EUR</label>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" min="0" name="exchange_rate[EUR]" class="form-control" id="EUR" required>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" class="form-control" name="old_val" id="EUR_old_val" disabled>
                    </div>
                </div>

                <!-- GBP -->
                <div class="row">
                    <div class="col-4 form-group text-right">
                        <label class="form-control-label" for="GBP">GBP</label>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" min="0" name="exchange_rate[GBP]" class="form-control" id="GBP" required>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" class="form-control" name="old_val" id="GBP_old_val" disabled>
                    </div>
                </div>

                <!-- JPY -->
                <div class="row">
                    <div class="col-4 form-group text-right">
                        <label class="form-control-label" for="JPY">JPY</label>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" min="0" name="exchange_rate[JPY]" class="form-control" id="JPY" required>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" class="form-control" name="old_val" id="JPY_old_val" disabled>
                    </div>
                </div>

                <!-- KRW -->
                <div class="row">
                    <div class="col-4 form-group text-right">
                        <label class="form-control-label" for="KRW">KRW</label>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" min="0" name="exchange_rate[KRW]" class="form-control" id="KRW" required>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" class="form-control" name="old_val" id="KRW_old_val" disabled>
                    </div>
                </div>

                <!-- MYR -->
                <div class="row">
                    <div class="col-4 form-group text-right">
                        <label class="form-control-label" for="MYR">MYR</label>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" min="0" name="exchange_rate[MYR]" class="form-control" id="MYR" required>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" class="form-control" name="old_val" id="MYR_old_val" disabled>
                    </div>
                </div>

                <!-- NZD -->
                <div class="row">
                    <div class="col-4 form-group text-right">
                        <label class="form-control-label" for="NZD">NZD</label>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" min="0" name="exchange_rate[NZD]" class="form-control" id="NZD" required>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" class="form-control" name="old_val" id="NZD_old_val" disabled>
                    </div>
                </div>

                <!-- SGD -->
                <div class="row">
                    <div class="col-4 form-group text-right">
                        <label class="form-control-label" for="SGD">SGD</label>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" min="0" name="exchange_rate[SGD]" class="form-control" id="SGD" required>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" class="form-control" name="old_val" id="SGD_old_val" disabled>
                    </div>
                </div>

                <!-- THB -->
                <div class="row">
                    <div class="col-4 form-group text-right">
                        <label class="form-control-label" for="THB">THB</label>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" min="0" name="exchange_rate[THB]" class="form-control" id="THB" required>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" class="form-control" name="old_val" id="THB_old_val" disabled>
                    </div>
                </div>

                <!-- TWD -->
                <div class="row">
                    <div class="col-4 form-group text-right">
                        <label class="form-control-label" for="TWD">TWD</label>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" min="0" name="exchange_rate[TWD]" class="form-control" id="TWD" required>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" class="form-control" name="old_val" id="TWD_old_val" disabled>
                    </div>
                </div>

                <!-- USD -->
                <div class="row">
                    <div class="col-4 form-group text-right">
                        <label class="form-control-label" for="USD">USD</label>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" min="0" name="exchange_rate[USD]" class="form-control" id="USD" required>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" class="form-control" name="old_val" id="USD_old_val" disabled>
                    </div>
                </div>

                <!-- MXN -->
                <div class="row">
                    <div class="col-4 form-group text-right">
                        <label class="form-control-label" for="MXN">MXN</label>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" min="0" name="exchange_rate[MXN]" class="form-control" id="MXN" required>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" class="form-control" name="old_val" id="MXN_old_val" disabled>
                    </div>
                </div>

                <!-- PLN -->
                <div class="row">
                    <div class="col-4 form-group text-right">
                        <label class="form-control-label" for="PLN">PLN</label>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" min="0" name="exchange_rate[PLN]" class="form-control" id="PLN" required>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" class="form-control" name="old_val" id="PLN_old_val" disabled>
                    </div>
                </div>

                <!-- IDR -->
                <div class="row">
                    <div class="col-4 form-group text-right">
                        <label class="form-control-label" for="IDR">IDR</label>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" min="0" name="exchange_rate[IDR]" class="form-control" id="IDR" required>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" class="form-control" name="old_val" id="IDR_old_val" disabled>
                    </div>
                </div>

                <!-- PHP -->
                <div class="row">
                    <div class="col-4 form-group text-right">
                        <label class="form-control-label" for="PHP">PHP</label>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" min="0" name="exchange_rate[PHP]" class="form-control" id="PHP" required>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" class="form-control" name="old_val" id="PHP_old_val" disabled>
                    </div>
                </div>

                <!-- VND -->
                <div class="row">
                    <div class="col-4 form-group text-right">
                        <label class="form-control-label" for="VND">VND</label>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" min="0" name="exchange_rate[VND]" class="form-control" id="VND" required>
                    </div>
                    <div class="col-4 form-group">
                        <input type="number" class="form-control" name="old_val" id="VND_old_val" disabled>
                    </div>
                </div>


                <!-- Save Cancel button-->
                <div class="row justify-content-center align-items-center">
                    <div class="col-3">
                        <button class="btn btn-primary" type="submit" id="inline_submit">Save</button>
                    </div>

                    <div class="col-3">
                        <button class="btn btn-primary" type="button" id="cancel_btn">Cancel</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <!-- colorbox create form part end -->

    <!-- colorbox historical rate part start -->
    <div style='display:none'>
        <div class="container" id='historical_rate_cbx'>
            <form enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col">
                        <h2>Historical Exchange Rates</h2>
                    </div>
                </div>

                <hr class="my-2">

                <div class="row">

                    <div class="col-2 form-group">
                        <label class="form-control-label">Open Time</label>
                    </div>

                    <div class="col-3">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                </div>
                                <input class="form-control" placeholder="Start Date" type="text" id="historicalStart">
                            </div>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                </div>
                                <input class="form-control" placeholder="End Date" type="text" id="historicalEnd">
                            </div>
                        </div>
                    </div>

                    <div class="col-4 text-right">
                        <button class="btn btn-success" type="button" id="search_btn">Search</button>
                        <button class="btn btn-primary" type="button" id="close_btn">Close</button>
                    </div>
                </div>


                {{-- table --}}
                <table class="table table-sm _table">
                    <thead>
                    <tr class="bg-primary">
                        <th class="text-white" scope="col">Quoted Date</th>
                        <th class="text-white" scope="col">Base Currency</th>
                        <th class="text-white" scope="col">Quote Currency</th>
                        <th class="text-white" scope="col">Exchange Rate</th>
                        <th class="text-white" scope="col">Open Time</th>
                        <th class="text-white" scope="col">Close Time</th>
                        <th class="text-white" scope="col">Updated By</th>
                    </tr>
                    </thead>

                    <tbody id="historicalBody">
                    </tbody>

                    {{--                    <tbody>--}}
                    {{--                    @forelse ($lists as $item)--}}
                    {{--                        <td class="platform">{{ $item->client_code ?? '' }}</td>--}}
                    {{--                        <td class="acc_nick_name">{{ $item->report_date->format('F-Y') ?? '' }}</td>--}}
                    {{--                        <td class="acc_name">{{ $item->opex_invoice_no ?? '' }}</td>--}}
                    {{--                        <td class="file_name">{{ $item->doc_file_name ?? '' }}</td>--}}
                    {{--                        <td class="shipped_date">{{ $item->doc_status ?? '' }}</td>--}}
                    {{--                        <td class="package_id">{{ $item->created_at ?? '' }}</td>--}}
                    {{--                        <td>--}}
                    {{--                            <button class="btn btn-icon btn-primary download_file btn-sm _fz-1"--}}
                    {{--                                    type="button" @if($item->doc_status !="active") {{ 'disabled' }} @endif>--}}
                    {{--                                <span class="btn-inner--icon"><i class="ni ni-cloud-download-95"></i></span>--}}
                    {{--                                <span class="btn-inner--text">Download</span>--}}
                    {{--                            </button>--}}
                    {{--                            <button class="btn btn-danger delete_btn btn-sm _fz-1" type="button"--}}
                    {{--                                    data-id="{{$item->id}}">--}}
                    {{--                                <span class="btn-inner--text">Delete</span>--}}
                    {{--                            </button>--}}
                    {{--                        </td>--}}

                    {{--                        <input name="file_token" type="hidden" value="{{$item->doc_storage_token ?? ''}}">--}}
                    {{--                        </tr>--}}
                    {{--                    @empty--}}
                    {{--                    @endforelse--}}
                    {{--                    </tbody>--}}
                </table>
            </form>
        </div>
    </div>
    <!-- colorbox historical rate part end -->
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
    <script src="https://momentjs.com/downloads/moment.min.js"></script>
    {{--TODO download cdn plugin--}}
    <script type="text/javascript">
        $(function () {
            $('#quoted_date').datepicker({
                format: 'yyyy-mm',//日期時間格式
                viewMode: "months",
                minViewMode: "months",
                defaultDate: new Date(),
                ignoreReadonly: true,  //禁止使用者輸入 啟用唯讀
                autoclose: true
            });

            $('#create_btn').click(function () {
                $.colorbox({
                    href: "#inline_content",
                    inline: true,
                    width: "65%",
                    height: "80%",
                    closeButton: true,
                    onComplete: function () {
                        //datepicker changeDate event
                        $('#quoted_date').datepicker('update', new Date()).on('changeDate', function (e) {
                            getLastUpdateExchangeRate();
                        });

                        getLastUpdateExchangeRate();

                        //cancel button event
                        $('#cancel_btn').click(function () {
                            $.colorbox.close();
                            return false;
                        });

                        //submit button event
                        $('#inline_submit').click(function () {

                            $("#create_form").validate({});//表格欄位驗證

                            let ajaxFormOption = {
                                type: "POST", //提交方式
                                url: origin + "/management/exchangeRate/create",
                                success: function (res) { //提交成功的回撥函式
                                    let icon = 'success';
                                    if (res.status !== 200) {
                                        icon = 'error';
                                    }
                                    swal({
                                        icon: icon,
                                        text: res.msg
                                    }).then(function (isConfirm) {
                                        if (isConfirm) {
                                            $.colorbox.close();
                                        }
                                    });
                                }, error: function (e) {
                                    swal({
                                        icon: 'error',
                                        text: e
                                    });
                                }
                            };

                            $("#create_form").ajaxForm(ajaxFormOption);
                        });
                    }
                });
            });

            $('.historical_rate_btn').click(function () {
                let currency = $(this).attr('data-attr');

                $.colorbox({
                    href: "#historical_rate_cbx",
                    inline: true,
                    width: "70%",
                    height: "80%",
                    closeButton: true,
                    onComplete: function () {
                        //init datepicker
                        $('.input-group input').each(function () {
                            $(this).datepicker({
                                format: 'yyyy-mm-dd',//日期時間格式
                                defaultDate: new Date(),
                                ignoreReadonly: true,  //禁止使用者輸入 啟用唯讀
                                autoclose: true
                            });
                        });

                        //cancel button event
                        $('#close_btn').click(function () {
                            $.colorbox.close();
                            return false;
                        });

                        $('#search_btn').click({currency: currency}, function (e) {
                            let historicalStart = $('#historicalStart').val();
                            let historicalEnd = $('#historicalEnd').val();

                            if (!historicalStart || !historicalEnd) {
                                swal({
                                    icon: 'error',
                                    text: 'Date Range Can Not Be Empty'
                                });
                                return;
                            }

                            getHistoricalRate(historicalStart, historicalEnd, e.data.currency);
                        });

                    },
                    onClosed: function () {
                        $('#historicalBody').html('');
                        $('#historicalStart').val('');
                        $('#historicalEnd').val('');
                    }
                });
            });
        });

        function getLastUpdateExchangeRate() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: origin + '/management/exchangeRate/' + $('#quoted_date').val(),
                type: 'get',
                success: function (res) {
                    let date = (res.data.length) ? moment(new Date(Date.parse(res.data[0].created_at)))
                        .format('YYYY-MM-DD') : '0000-00-00';

                    $("#historical_date").text('Historical Rates (' + date + ')');

                    (res.data.length === 0) ?
                        $('#create_form').find('[name="old_val"]').val('') :
                        $.each(res.data, function (key, val) {
                            $('#' + val.base_currency + '_old_val').val(val.exchange_rate);
                        });

                }, error: function (e) {
                    console.log(e);
                }
            });
        }

        function getHistoricalRate(startDate, endDate, currency) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: origin + '/management/exchangeRate/' + currency + '/' + startDate + '/' + endDate,
                type: 'get',
                success: function (res) {
                    let html = '';

                    res.data.forEach((item) => {
                        let closeTime = (item.active) ? '-' : item.updated_at;

                        html += '<tr>';
                        html += '<td>' + item.quoted_date + '</td>';
                        html += '<td>' + item.base_currency + '</td>';
                        html += '<td>' + item.quote_currency + '</td>';
                        html += '<td>' + item.exchange_rate + '</td>';
                        html += '<td>' + item.created_at + '</td>';
                        html += '<td>' + closeTime + '</td>';
                        html += '<td>' + item.user_name + '</td>';
                        html += '</tr>';
                    })

                    $('#historicalBody').html(html);
                }, error: function (e) {
                    console.log(e);
                    swal({
                        icon: 'error',
                        text: 'Api Error'
                    });
                }
            });
        }
    </script>
@endpush

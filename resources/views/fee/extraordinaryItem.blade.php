@extends('layouts.app', [
    'parentSection' => 'expense-claim',
    'elementName' => 'extraordinary-item'
])

@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('EXPENSE CLAIM') }}
            @endslot
            <li class="breadcrumb-item"><a href="{{ route('page.index', 'components') }}">{{ __('EXPENSE CLAIM') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('EXTRAORDINARY ITEM') }}</li>
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
                            <form method="GET" action="/fee/extraordinaryitem" role="form" class="form">

                                <div class="row">

                                    {{-- CLIENT CODE --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="client_code">CLIENT CODE</label>
                                            <input class="form-control" name="client_code" id="client_code" type="text"
                                                   placeholder="CLIENT CODE" value="{{$clientCode ?? ''}}">
                                        </div>
                                    </div>

                                    {{-- REPORT DATE RANGE--}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="report_date">REPORT DATE
                                                RANGE</label>
                                            <input class="form-control" name="report_date" id="report_date"
                                                   type="text" placeholder="REPORT DATE"
                                                   value="{{$reportDate ?? ''}}">
                                        </div>
                                    </div>

                                    {{-- SUBMIT --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <label class="form-control-label" for="submit_btn"></label>
                                        <div class="form-group">
                                            <button class="form-control btn btn-primary" id="submit_btn" type="submit"
                                                    style="margin-top: 6px;">SEARCH
                                            </button>
                                        </div>
                                    </div>

                                    {{-- COMPOSE --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <label class="form-control-label" for="compose_btn"></label>
                                        <div class="form-group">
                                            <button class="form-control btn btn-outline-default" id="compose_btn"
                                                    type="button" style="margin-top: 6px;"
                                                    href="#inline_compose_content">
                                                COMPOSE
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
                                <th>ACTIONS</th>
                                <th>#</th>
                                <th>CLIENT CODE</th>
                                <th>REPORT DATE</th>
                                <th>ITEM NAME</th>
                                <th>DESCRIPTIONS</th>
                                <th>CURRENCY</th>
                                <th>RECEIVABLE $</th>
                                <th>PAYABLE $</th>
                                <th>TOTAL AMOUNT $</th>
                                <th>CREATED BY</th>
                                <th>CREATED AT</th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse ($lists as $item)
                                <tr>
                                    <td>
                                        {{-- copy button --}}
                                        <button type="button" class="btn btn-sm btn-neutral" data-attr="copy"
                                                href="#inline_compose_content">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 fill="currentColor" class="bi bi-files" viewBox="0 0 16 16">
                                                <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"/>
                                            </svg>
                                        </button>

                                        {{--                                        <button class="form-control btn btn-outline-default" id="compose_btn"--}}
                                        {{--                                                type="button" style="margin-top: 6px;"--}}
                                        {{--                                                href="#inline_compose_content">--}}

                                        {{-- delete button --}}
                                        <button type="button" class="btn btn-sm btn-neutral" data-attr="delete"
                                                data-label="{{$item->id}}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                                <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"></path>
                                            </svg>
                                        </button>

                                        {{-- edit button --}}
                                        <button type="button" class="btn btn-sm btn-neutral" data-attr="edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                <path fill-rule="evenodd"
                                                      d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                            </svg>
                                        </button>
                                    </td>

                                    <td data-label="id">{{ $item->id }}</td>
                                    <td data-label="client_code">{{ $item->client_code }}</td>
                                    <td data-label="report_date">{{ $item->report_date }}</td>
                                    <td data-label="item_name">{{ $item->item_name }}</td>
                                    <td data-label="description">{{ $item->description }}</td>
                                    <td data-label="currency_code">{{ $item->currency_code }}</td>
                                    <td data-label="receivable_amount">{{ $item->receivable_amount }}</td>
                                    <td data-label="payable_amount">{{ $item->payable_amount }}</td>
                                    <td data-label="total">{{ $item->item_amount }}</td>
                                    <td data-label="created_by">{{ $item->created_by }}</td>
                                    <td data-label="created_at">{{ $item->created_at }}</td>

                                    <input type="hidden" name="updated_at" value="{{ $item->updated_at }}">
                                    <input type="hidden" name="updated_by" value="{{ $item->updated_by }}">
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-center" style='margin-top: 20px;'>
                            {!! $lists->links() !!}
                        </div>

                    </div>

                </div>
            </div>
        </div>
        <!-- Footer -->
        {{--        @include('layouts.footers.auth')--}}
    </div>

    <!-- compose colorbox html part start -->
    <div style='display:none'>
        <div class="container" id='inline_compose_content'>
            <div class="row">
                <div class="col form-group">
                    <strong id="cbx_title">{{'NEW EXTRAORDINARY ITEM'}}</strong>
                </div>
            </div>

            {{--            <form action="/fee/extraordinaryitem" method="post" id="inline_form" enctype="multipart/form-data">--}}
            <form id="inline_form" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-4 form-group">
                        <label class="form-control-label"># ID</label>
                    </div>
                    <div class="col-4 form-group" id="inline_id">{{$lists['id'] ?? ''}}</div>
                </div>

                <!-- REPORT DATE -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="inline_report_date">REPORT DATE</label>
                    </div>
                    <div class="col-4 form-group">
                        <input class="form-control" id="inline_report_date" placeholder="report date" type="text"
                               name="report_date" required>
                    </div>
                </div>

                <!-- CLIENT CODE-->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="inline_client_code">CLIENT CODE</label>
                    </div>
                    <div class="col-5 form-group">
                        <select class="form-control" data-toggle="select" name="client_code" id="inline_client_code">
                        </select>
                    </div>
                </div>

                <!-- CURRENCY-->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="inline_currency">CURRENCY</label>
                    </div>
                    <div class="col-3 form-group">
                        <select class="form-control" data-toggle="select" name="currency_code" id="inline_currency">
                        </select>
                    </div>
                </div>

                <!-- ITEM NAME -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="inline_item_name">ITEM NAME</label>
                    </div>
                    <div class="col-4 form-group">
                        <input class="form-control" id="inline_item_name" placeholder="item name" type="text"
                               name="item_name">
                    </div>
                </div>

                <!-- DESCRIPTION -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="inline_description">DESCRIPTION</label>
                    </div>
                    <div class="col-4 form-group">
                        <input class="form-control" id="inline_description" placeholder="DESCRIPTION" type="text"
                               name="description" required>
                    </div>
                </div>

                <!-- RECEIVABLE -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="inline_receivable">RECEIVABLE $</label>
                    </div>
                    <div class="col-4 form-group">
                        <input class="form-control" id="inline_receivable" placeholder="RECEIVABLE" type="number"
                               name="receivable_amount" max="99999999.99" step="0.01" required>
                    </div>
                </div>

                <!-- PAYABLE -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="inline_payable">PAYABLE $</label>
                    </div>
                    <div class="col-4 form-group">
                        <input class="form-control" id="inline_payable" placeholder="PAYABLE" type="number"
                               name="payable_amount" max="99999999.99" step="0.01" required>
                    </div>
                </div>

                <!-- TOTAL AMOUNT -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="inline_total">TOTAL AMOUNT $</label>
                    </div>

                    <div class="col-4 form-group">
                        <input class="form-control" id="inline_total" placeholder="TOTAL AMOUNT" type="number"
                               name="item_amount" max="99999999.99" step="0.01" required>
                    </div>
                </div>

                <!-- LAST UPDATED AT -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label">LAST UPDATED AT</label>
                    </div>
                    <div class="col-4 form-group" id="last_updated_at"></div>
                </div>

                <!-- LAST UPDATED BY -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label">LAST UPDATED BY</label>
                    </div>
                    <div class="col-4 form-group" id="last_updated_by"></div>
                </div>

                <!-- submit cancel button -->
                <div class="row justify-content-center align-items-center">
                    <div class="col-3">
                        <button class="btn btn-primary" type="button" id="cancel_btn">Cancel</button>
                    </div>
                    <div class="col-3">
                        <button class="btn btn-primary" type="submit" id="inline_submit">Submit</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <!-- compose colorbox html part end -->
@endsection

@push('js')
    {{--    <script src="{{ asset('argon') }}/vendor/select2/dist/js/select2.min.js"></script>--}}
    {{--    <script src="{{ asset('argon') }}/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>--}}

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

            $('#report_date').datepicker('update', reportDate);

            $("#compose_btn").on('click', function () {
                $.colorbox({
                    href: "#inline_compose_content",
                    inline: true,
                    width: "45%",
                    height: "80%",
                    closeButton: true,
                    onComplete: function () {

                        $("#cbx_title").text("NEW EXTRAORDINARY ITEM");

                        let _token = $('meta[name="csrf-token"]').attr('content');

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': _token
                            }
                        });

                        //get client code list
                        $.ajax({
                            url: origin + '/fee/clientCodeList',
                            type: 'get',
                            success: function (res) {
                                let clientOption = "";

                                $.each(res.data, function (key, value) {
                                    clientOption += '<option value=' + value + '>' + value + '</option>';

                                    $("#inline_client_code").html(clientOption);
                                });

                            }, error: function (e) {
                                console.log(e);
                            }
                        });

                        //get currency list
                        $.ajax({
                            url: origin + '/fee/allCurrency',
                            type: 'get',
                            success: function (res) {
                                let currencyOption = "";

                                $.each(res.data, function (key, value) {
                                    currencyOption += '<option value=' + value + '>' + value + '</option>';

                                    $("#inline_currency").html(currencyOption);
                                });
                            }, error: function (e) {
                                console.log(e);
                            }
                        });

                        //clean form
                        $(':input', '#inline_form').val('');

                        //cancel
                        $("#cancel_btn").on('click', function () {
                            $.colorbox.close();
                        });

                        //submit
                        $("#inline_submit").on('click', function () {
                            let ajaxFormOption = {
                                type: "post", //提交方式
                                url: origin + "/fee/extraordinaryitem",
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

                            $("#inline_form").ajaxForm(ajaxFormOption);
                        });

                    }
                });

                $('#inline_report_date').datepicker({
                    format: 'yyyy-mm',//日期時間格式
                    viewMode: "months",
                    minViewMode: "months",
                    ignoreReadonly: true,  //禁止使用者輸入 啟用唯讀
                    autoclose: true
                });

                $('#inline_report_date').datepicker('update', reportDate);
            });

            $("button[data-attr='delete']").on('click', function () {
                let id = $(this).attr('data-label');

                swal({
                    title: "Are you sure?",
                    icon: 'warning',
                    buttons: true,
                    buttons: ["No,Cancel Plx!", "Yes,Delete it!"]
                })
                    .then(function (isConfirm) {
                        if (isConfirm) {
                            deleteExtraordinaryItem(id);
                        }
                    });
            });

            //copy_btn test start
            $("button[data-attr='copy']").on('click', function () {
                let data = {};

                data._token = $('meta[name="csrf-token"]').attr('content');
                data.reportDate = $(this).parent().parent().find('td[data-label="report_date"]').text();
                data.clientCode = $(this).parent().parent().find('td[data-label="client_code"]').text();
                data.currency = $(this).parent().parent().find('td[data-label="currency_code"]').text();
                data.itemName = $(this).parent().parent().find('td[data-label="item_name"]').text();
                data.desc = $(this).parent().parent().find('td[data-label="description"]').text();
                data.receivableAmount = $(this).parent().parent().find('td[data-label="receivable_amount"]').text();
                data.payableAmount = $(this).parent().parent().find('td[data-label="payable_amount"]').text();
                data.totalAmount = $(this).parent().parent().find('td[data-label="total"]').text();

                $.colorbox({
                    href: "#inline_compose_content",
                    inline: true,
                    width: "45%",
                    height: "80%",
                    closeButton: true,
                    onComplete: function () {
                        $("#cbx_title").text("NEW EXTRAORDINARY ITEM");

                        let _token = $('meta[name="csrf-token"]').attr('content');

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': _token
                            }
                        });

                        //get client code list
                        $.ajax({
                            url: origin + '/fee/clientCodeList',
                            type: 'get',
                            success: function (res) {
                                let clientOption = "";

                                $.each(res.data, function (key, value) {
                                    clientOption += '<option value=' + value + '>' + value + '</option>';
                                });

                                $("#inline_client_code").html(clientOption);

                                $("#inline_client_code").val(data.clientCode);
                            }, error: function (e) {
                                console.log(e);
                            }
                        });

                        //get currency list
                        $.ajax({
                            url: origin + '/fee/allCurrency',
                            type: 'get',
                            success: function (res) {
                                let currencyOption = "";

                                console.log(res);
                                $.each(res.data, function (key, value) {
                                    currencyOption += '<option value=' + value + '>' + value + '</option>';
                                });

                                $("#inline_currency").html(currencyOption);
                                $("#inline_currency").val(data.currency);

                            }, error: function (e) {
                                console.log(e);
                            }
                        });

                        //cancel
                        $("#cancel_btn").on('click', function () {
                            $.colorbox.close();
                        });


                        $('#inline_report_date').datepicker({
                            format: 'yyyy-mm',//日期時間格式
                            viewMode: "months",
                            minViewMode: "months",
                            ignoreReadonly: true,  //禁止使用者輸入 啟用唯讀
                            autoclose: true
                        });

                        $("#inline_report_date").val(data.reportDate);

                        $('#inline_report_date').datepicker('update', data.reportDate);

                        $("#inline_item_name").val(data.itemName);
                        $("#inline_description").val(data.desc);
                        $("#inline_receivable").val(data.receivableAmount);
                        $("#inline_payable").val(data.payableAmount);
                        $("#inline_total").val(data.totalAmount);

                        //submit
                        $("#inline_submit").on('click', function () {

                            let ajaxFormOption = {
                                type: "post", //提交方式
                                url: origin + "/fee/extraordinaryitem/createItem",
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

                            // $("#inline_compose_content").ajaxForm(ajaxFormOption);
                            $("#inline_form").ajaxForm(ajaxFormOption);
                        });

                    }
                });
            });

            $("button[data-attr='edit']").on('click', function () {
                let data = {};

                data._token = $('meta[name="csrf-token"]').attr('content');
                data.id = $(this).parent().parent().find('td[data-label="id"]').text();
                data.reportDate = $(this).parent().parent().find('td[data-label="report_date"]').text();
                data.clientCode = $(this).parent().parent().find('td[data-label="client_code"]').text();
                data.currency = $(this).parent().parent().find('td[data-label="currency_code"]').text();
                data.itemName = $(this).parent().parent().find('td[data-label="item_name"]').text();
                data.desc = $(this).parent().parent().find('td[data-label="description"]').text();
                data.receivableAmount = $(this).parent().parent().find('td[data-label="receivable_amount"]').text();
                data.payableAmount = $(this).parent().parent().find('td[data-label="payable_amount"]').text();
                data.totalAmount = $(this).parent().parent().find('td[data-label="total"]').text();
                data.updatedAt = $(this).parent().parent().find('input[name="updated_at"]').val();
                data.updatedBy = $(this).parent().parent().find('input[name="updated_by"]').val();

                $.colorbox({
                    href: "#inline_compose_content",
                    inline: true,
                    width: "45%",
                    height: "80%",
                    closeButton: true,
                    onComplete: function () {
                        //get client code list
                        $.ajax({
                            url: origin + '/fee/clientCodeList',
                            type: 'get',
                            success: function (res) {
                                let clientOption = "";

                                $.each(res.data, function (key, value) {
                                    clientOption += '<option value=' + value + '>' + value + '</option>';
                                });
                                $("#inline_client_code").html(clientOption);
                                $("#inline_client_code").val(data.clientCode);
                            }, error: function (e) {
                                console.log(e);
                            }
                        });

                        //get currency list
                        $.ajax({
                            url: origin + '/fee/allCurrency',
                            type: 'get',
                            success: function (res) {
                                let currencyOption = "";

                                $.each(res.data, function (key, value) {
                                    currencyOption += '<option value=' + value + '>' + value + '</option>';
                                });

                                $("#inline_currency").html(currencyOption);
                                $("#inline_currency").val(data.currency);
                            }, error: function (e) {
                                console.log(e);
                            }
                        });

                        //cancel
                        $("#cancel_btn").on('click', function () {
                            $.colorbox.close();
                        });

                        $('#inline_report_date').datepicker({
                            format: 'yyyy-mm',//日期時間格式
                            viewMode: "months",
                            minViewMode: "months",
                            ignoreReadonly: true,  //禁止使用者輸入 啟用唯讀
                            autoclose: true
                        });

                        $("#inline_report_date").val(data.reportDate);

                        $('#inline_report_date').datepicker('update', data.reportDate);

                        $("#inline_id").val(data.id);
                        $("#inline_client_code").val(data.clientCode);
                        $("#inline_currency").val(data.currency);
                        $("#inline_item_name").val(data.itemName);
                        $("#inline_description").val(data.desc);
                        $("#inline_receivable").val(data.receivableAmount);
                        $("#inline_payable").val(data.payableAmount);
                        $("#inline_total").val(data.totalAmount);
                        $("#last_updated_at").val(data.updatedAt);
                        $("#last_updated_by").val(data.updatedBy);
                        $("#cbx_title").text("EDIT EXTRAORDINARY ITEMS");

                        //submit
                        $("#inline_submit").on('click', function () {

                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });

                            let ajaxFormOption = {
                                type: "POST",
                                data: {'_method': 'PUT'},
                                url: origin + "/fee/extraordinaryitem/detail/" + data.id,
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
                                            // $('#cboxOverlay').remove();
                                            // $('#colorbox').remove();
                                            $.colorbox.close();
                                            // parent.jQuery.colorbox.close()
                                            // console.log('x');

                                        }
                                    });
                                }, error: function (e) {
                                    swal({
                                        icon: 'error',
                                        text: e
                                    });
                                }
                            };

                            $("#inline_form").ajaxForm(ajaxFormOption);
                        });
                    }
                });
            });
        });

        function deleteExtraordinaryItem(id) {
            let _token = $('meta[name="csrf-token"]').attr('content');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': _token
                }
            });

            $.ajax({
                url: origin + '/fee/extraordinaryitem/' + id,
                type: 'delete',
                success: function (res) {
                    let icon = 'success';
                    if (res.status !== 200) {
                        icon = 'error';
                    }
                    swal({
                        icon: icon,
                        text: res.msg
                    });
                }, error: function (e) {
                    swal({
                        icon: 'error',
                        text: e
                    });
                }
            });
        }
    </script>
@endpush

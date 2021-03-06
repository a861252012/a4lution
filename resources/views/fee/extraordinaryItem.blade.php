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
            <li class="breadcrumb-item"><a href="{{ route('fee.extraordinaryItem.view') }}">{{ __('EXPENSE CLAIM') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('EXTRAORDINARY ITEM') }}</li>
        @endcomponent
    @endcomponent

    <div class="wrapper wrapper-content">
        <!-- Table -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                <form method="GET" action="/fee/extraordinaryitem" role="form" class="form">
                    <div class="row">
                        {{-- CLIENT CODE --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="client_code">Client Code</label>
                                <input class="form-control _fz-1" name="client_code" id="client_code"
                                    type="text" placeholder="Client Code" value="{{$clientCode ?? ''}}">
                            </div>
                        </div>

                        {{-- REPORT DATE RANGE--}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="report_date">Report Date Range</label>
                                <input class="form-control _fz-1" name="report_date" id="report_date"
                                       type="text" placeholder="Report Date" value="{{$reportDate ?? ''}}">
                            </div>
                        </div>

                        {{-- SUBMIT --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="submit_btn"></label>
                                <button class="form-control btn _btn btn-primary _fz-1" id="submit_btn" type="submit"
                                        style="margin-top: 6px;">Search
                                </button>
                            </div>
                        </div>

                        {{-- COMPOSE --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="compose_btn"></label>
                                <button class="form-control btn _btn btn-outline-default _fz-1" id="compose_btn"
                                        type="button" style="margin-top: 6px;" href="#inline_compose_content">Compose
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
                        <th>Actions</th>
                        <th>#</th>
                        <th>Client Code</th>
                        <th>Report Date</th>
                        <th>Item Name</th>
                        <th>Descriptions</th>
                        <th>Currency</th>
                        <th>Receivable $</th>
                        <th>Payable $</th>
                        <th>Total Amount $</th>
                        <th>Created By</th>
                        <th>Created At</th>
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
                @if($lists && $lists->lastPage() > 1)
                    <div class="d-flex justify-content-center" style='margin-top: 20px;'>
                        {{ $lists->appends($_GET)->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- compose colorbox html part start -->
    <div style='display:none'>
        <div class="container" id='inline_compose_content'>
            <div class="row">
                <div class="col form-group">
                    <strong id="cbx_title">{{'New Extraordinary Item'}}</strong>
                </div>
            </div>

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
                        <label class="form-control-label" for="inline_report_date">Report Date</label>
                    </div>
                    <div class="col-4 form-group">
                        <input class="form-control" id="inline_report_date" placeholder="Report Date" type="text"
                               name="report_date" required>
                    </div>
                </div>

                <!-- CLIENT CODE-->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="inline_client_code">Client Code</label>
                    </div>
                    <div class="col-5 form-group">
                        <select class="form-control" data-toggle="select" name="client_code" id="inline_client_code">
                        </select>
                    </div>
                </div>

                <!-- CURRENCY-->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="inline_currency">Currency</label>
                    </div>
                    <div class="col-3 form-group">
                        <select class="form-control" data-toggle="select" name="currency_code" id="inline_currency">
                        </select>
                    </div>
                </div>

                <!-- ITEM NAME -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="inline_item_name">Item Name</label>
                    </div>
                    <div class="col-4 form-group">
                        <input class="form-control" id="inline_item_name" placeholder="Item Name" type="text"
                               name="item_name">
                    </div>
                </div>

                <!-- DESCRIPTION -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="inline_description">Description</label>
                    </div>
                    <div class="col-4 form-group">
                        <input class="form-control" id="inline_description" placeholder="Description" type="text"
                               name="description" required>
                    </div>
                </div>

                <!-- RECEIVABLE -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="inline_receivable">Receivable $</label>
                    </div>
                    <div class="col-4 form-group">
                        <input class="form-control" id="inline_receivable" placeholder="Receivable" type="number"
                               name="receivable_amount" max="99999999.99" step="0.01" required>
                    </div>
                </div>

                <!-- PAYABLE -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="inline_payable">Payable $</label>
                    </div>
                    <div class="col-4 form-group">
                        <input class="form-control" id="inline_payable" placeholder="Payable" type="number"
                               name="payable_amount" max="99999999.99" step="0.01" required>
                    </div>
                </div>

                <!-- TOTAL AMOUNT -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="inline_total">Total Amount $</label>
                    </div>

                    <div class="col-4 form-group">
                        <input class="form-control" id="inline_total" placeholder="Total Amount" type="number"
                               name="item_amount" max="99999999.99" step="0.01" required>
                    </div>
                </div>

                <!-- LAST UPDATED AT -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label">Last Updated At</label>
                    </div>
                    <div class="col-4 form-group" id="last_updated_at"></div>
                </div>

                <!-- LAST UPDATED BY -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label">Last Updated By</label>
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
    <script type="text/javascript">
        $(function () {
            let reportDate = $('#report_date').val();

            $('#report_date').datepicker({
                format: 'yyyy-mm',//??????????????????
                viewMode: "months",
                minViewMode: "months",
                ignoreReadonly: true,  //????????????????????? ????????????
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

                        $("#cbx_title").text("New Extraordinary Item");
                        $("#inline_id").text('');
                        $("#last_updated_at").text('');
                        $("#last_updated_by").text('');

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
                                type: "post", //????????????
                                url: origin + "/fee/extraordinaryitem",
                                success: function (res) { //???????????????????????????
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
                    format: 'yyyy-mm',//??????????????????
                    viewMode: "months",
                    minViewMode: "months",
                    ignoreReadonly: true,  //????????????????????? ????????????
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
                        $("#cbx_title").text("New Extraordinary Item");
                        $("#inline_id").text('');

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
                            format: 'yyyy-mm',//??????????????????
                            viewMode: "months",
                            minViewMode: "months",
                            ignoreReadonly: true,  //????????????????????? ????????????
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
                                type: "post", //????????????
                                url: origin + "/fee/extraordinaryitem/createItem",
                                success: function (res) { //???????????????????????????
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
                            format: 'yyyy-mm',//??????????????????
                            viewMode: "months",
                            minViewMode: "months",
                            ignoreReadonly: true,  //????????????????????? ????????????
                            autoclose: true
                        });

                        $("#inline_report_date").val(data.reportDate);

                        $('#inline_report_date').datepicker('update', data.reportDate);

                        $("#inline_id").text(data.id);
                        $("#inline_client_code").val(data.clientCode);
                        $("#inline_currency").val(data.currency);
                        $("#inline_item_name").val(data.itemName);
                        $("#inline_description").val(data.desc);
                        $("#inline_receivable").val(data.receivableAmount);
                        $("#inline_payable").val(data.payableAmount);
                        $("#inline_total").val(data.totalAmount);
                        $("#last_updated_at").text(data.updatedAt);
                        $("#last_updated_by").text(data.updatedBy);
                        $("#cbx_title").text("Edit Extraordinary Items");

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
                                success: function (res) { //???????????????????????????
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

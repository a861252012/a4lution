@extends('layouts.app', [
    'parentSection' => 'INVOICE',
    'elementName' => 'ISSUE'
])
@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('INVOICE') }}
            @endslot
            <li class="breadcrumb-item"><a href="{{ route('page.index', 'components') }}">{{ __('INVOICE') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('ISSUE') }}</li>
        @endcomponent
    @endcomponent

    <div class="wrapper wrapper-content animated">
        <!-- Table -->
        <div class="row">
            <div class="col">
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}">

                        <form method="GET" action="/invoice/issue" role="form" class="form">
                            <div class="row">
                                {{-- CLIENT CODE --}}
                                <div class="col-2 col-lg-2  col-sm-2">
                                    <div class="form-group">
                                        <label class="form-control-label" for="sel_client_code">Client Code</label>
                                        <select class="form-control" data-toggle="select" name="sel_client_code"
                                                id="sel_client_code">
                                            @forelse ($client_code_lists as $item)
                                                <option value="{{$item}}" @if($sel_client_code == $item) {{ 'selected' }} @endif>{{$item}}</option>
                                            @empty
                                                <option value="">{{'NONE'}}</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>

                                {{-- REPORT DATE --}}
                                <div class="col-2 col-lg-2  col-sm-2">
                                    <div class="form-group">
                                        <label class="form-control-label" for="search_report_date">Report Date</label>
                                        <input class="form-control" name="report_date" id="search_report_date"
                                               type="text" placeholder="REPORT DATE" value="{{$report_date ?? ''}}">
                                    </div>
                                </div>

                                {{-- SEARCH --}}
                                <div class="col-2 col-lg-2  col-sm-2">
                                    <label class="form-control-label" for="submit_btn"></label>
                                    <div class="form-group">
                                        <button class="form-control btn btn-primary" id="submit_btn" type="submit"
                                                style="margin-top: 6px;">Search
                                        </button>
                                    </div>
                                </div>

                                {{-- GENERATE SUMMARY --}}
                                <div class="col-2 col-lg-2  col-sm-2">
                                    <label class="form-control-label" for="gen_sales_btn"></label>
                                    <div class="form-group">
                                        <button class="form-control btn btn-primary" id="gen_sales_btn"
                                                type="button" style="margin-top: 6px;">Generate Sales Summary
                                        </button>
                                    </div>
                                </div>

                                {{-- CREATE SUMMARY --}}
                                <div class="col-2 col-lg-2  col-sm-2">
                                    <label class="form-control-label" for="create_sales_btn"></label>
                                    <div class="form-group">
                                        <a id="create_sales_btn" href="#inline_content">
                                            <div class="form-control btn btn-success" type="button"
                                                 style="margin-top: 6px;">Create Sales Summary
                                            </div>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>

                    {{-- data table --}}
                    <div class="table-responsive py-4">
                        <table class="table table-flush">
                            <thead class="thead-light">
                            <tr>
                                <th>REPORT DATE</th>
                                <th>CLIENT CODE</th>
                                <th>AVOLUTION COMMISSION</th>
                                <th>COMMISSION TYPE</th>
                                <th>TOTAL SALES ORDERS</th>
                                <th>TOTAL SALES AMOUNT</th>
                                <th>TOTAL EXPENSE</th>
                                <th>ACTION</th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse ($lists as $item)
                                <tr>
                                    <input type="hidden" name="bill_state_id" value="{{ $item->id }}">
                                    <td class="report_date">{{ $item->report_date ?? '' }}</td>
                                    <td class="client_code">{{ $item->client_code ?? '' }}</td>
                                    <td class="avolution_commission">{{ $item->avolution_commission ?? '' }}</td>
                                    <td class="commission_type">{{ $item->commission_type ?? '' }}</td>
                                    <td class="total_sales_orders">{{ $item->total_sales_orders ?? '' }}</td>
                                    <td class="total_sales_amount">{{ $item->total_sales_amount ?? '' }}</td>
                                    <td class="total_expenses">{{ $item->total_expenses ?? '' }}</td>
                                    <td>
                                        <button class="btn btn-primary issue_btn btn-sm" type="button"
                                        @if($item->commission_type === "manual") {{ 'disabled' }} @endif>
                                            <span class="btn-inner--text">ISSUE INVOICE</span>
                                        </button>
                                        <button class="btn btn-danger delete_btn btn-sm" type="button">
                                            <span class="btn-inner--text">DELETE</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-center" style='margin-top: 20px;'>
                            @if($lists)
                                {!! $lists->links() !!}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        {{--        @include('layouts.footers.auth')--}}
    </div>

    <!-- colorbox html part start -->
    <div style='display:none'>
        <div class="container" id="inline_content">

            {{--  DATE --}}
            <div class="row">
                <div class="col">
                    <strong>
                        Create Sales Summary
                    </strong>
                </div>
            </div>

            <br>

            <form id="cbx_form" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    {{-- CLIENT CODE --}}
                    <div class="col-2 col-lg-2 col-sm-2 text-right">
                        <label class="form-control-label required" for="cbx_client_code">Client Code</label>
                    </div>

                    <div class="col-2">
                        <select class="form-control" data-toggle="select" name="sel_client_code"
                                id="cbx_client_code">
                            @forelse ($client_code_lists as $item)
                                <option value="{{$item}}" @if($sel_client_code == $item) {{ 'selected' }} @endif>
                                    {{$item}}
                                </option>
                            @empty
                                <option value="">{{'NONE'}}</option>
                            @endforelse
                        </select>
                    </div>

                    {{-- REPORT DATE --}}
                    <div class="col-2 col-lg-2  col-sm-2 text-right">
                        <label class="form-control-label required" for="inline_report_date">Report Date</label>
                    </div>

                    <div class="col-2">
                        <input class="form-control" name="report_date" id="inline_report_date"
                               type="text" placeholder="REPORT DATE" value="{{$report_date ?? ''}}" required>
                    </div>
                </div>

                <br>

                {{-- Sales OverView--}}
                <div class="row">
                    <div class="col">
                        <h2>
                            <u>Sales OverView</u>
                        </h2>
                    </div>
                </div>

                {{-- Total Sales Orders --}}
                <div class="row">
                    <div class="col-4">
                        - Total Sales Orders
                    </div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="total_sales_orders" type="number" min="0" max="9999999999">
                        </label>
                    </div>
                </div>

                {{-- Total Sales Amount --}}
                <div class="row">
                    <div class="col-4">
                        - Total Sales Amount
                    </div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="total_sales_amount" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                </div>

                {{-- Total Expenses --}}
                <div class="row">
                    <div class="col-4">
                        - Total Expenses
                    </div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="total_expenses" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                </div>

                {{-- Sales GP --}}
                <div class="row">
                    <div class="col-4">
                        - Sales GP
                    </div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="sales_gp" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                </div>

                <br>

                {{-- Expenses Breakdown headers --}}
                <div class="row">
                    <div class="col-4">
                        <h3>
                            <u>Expenses Breakdown</u>
                        </h3>
                    </div>
                    <div class="col-4">
                        <h3>
                            <u>A4lution Account</u>
                        </h3>
                    </div>
                    <div class="col-4">
                        <h3>
                            <u>Client Account</u>
                        </h3>
                    </div>
                </div>

                {{-- Logistics Fee --}}
                <div class="row">
                    <div class="col-4">- Logistics Fee</div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="a4_account_logistics_fee" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="client_account_logistics_fee" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                </div>

                {{--FBA Fee --}}
                <div class="row">
                    <div class="col-4">- FBA Fee</div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="a4_account_fba_fee" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="client_account_fba_fee" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                </div>

                {{-- FBA storage Fee --}}
                <div class="row">
                    <div class="col-4">- FBA storage Fee</div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="a4_account_fba_storage_fee" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="client_account_fba_storage_fee" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                </div>

                {{-- Platform Fee --}}
                <div class="row">
                    <div class="col-4">- Platform Fee</div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="a4_account_platform_fee" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="client_account_platform_fee" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                </div>

                {{-- Refund and Resend --}}
                <div class="row">
                    <div class="col-4">- Refund and Resend</div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="a4_account_refund_and_resend" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="client_account_refund_and_resend" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                </div>

                {{-- Miscellaneous --}}
                <div class="row">
                    <div class="col-4">- Miscellaneous</div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="a4_account_miscellaneous" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="client_account_miscellaneous" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                </div>

                {{-- Marketing Fee--}}
                <div class="row">
                    <div class="col">
                        <h2>
                            <u>Marketing Fee</u>
                        </h2>
                    </div>
                </div>

                {{-- Advertisement --}}
                <div class="row">
                    <div class="col-4">- Advertisement</div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="a4_account_advertisement" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="client_account_advertisement" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                </div>

                {{-- Marketing and Promotion --}}
                <div class="row">
                    <div class="col-4">- Marketing and Promotion</div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="a4_account_marketing_and_promotion" type="number"
                                   step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                    <div class="col-4">
                        <label>
                            <input class="decoration" name="client_account_marketing_and_promotion" type="number"
                                   step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                </div>

                <br>

                {{-- Avolution Commission --}}
                <div class="row">
                    <div class="col-4 ">
                        <h3 class="required">Avolution Commission</h3>
                    </div>

                    <div class="col-4">
                        <label>
                            <input class="decoration" name="avolution_commission" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99" required>
                        </label>
                    </div>
                </div>

                {{-- Sales Tax Handling --}}
                <div class="row">
                    <div class="col-4">
                        <h3>Sales Tax Handling</h3>
                    </div>

                    <div class="col-4">
                        <label>
                            <input class="decoration" name="sales_tax_handling" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                </div>

                {{--  Extraordinary item-comm. Fee rebate --}}
                <div class="row">
                    <div class="col-4">
                        <h3>Extraordinary item-comm. Fee rebate</h3>
                    </div>

                    <div class="col-4">
                        <label>
                            <input class="decoration" name="extraordinary_item" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                </div>

                <br>

                {{--  Sales Overview --}}
                <div class="row">
                    <div class="col">
                        <h2>
                            <u>Sales OverView</u>
                        </h2>
                    </div>
                </div>

                {{--  Sales Credit --}}
                <div class="row">
                    <div class="col-4">
                        <h3>{!! "&emsp;" !!}Sales Credit</h3>
                    </div>

                    <div class="col-4">
                        <label>
                            <input class="decoration" name="sales_credit" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                </div>

                {{--  OPEX Invoice --}}
                <div class="row">
                    <div class="col-4">
                        <h3>{!! "&emsp;" !!}OPEX Invoice</h3>
                    </div>

                    <div class="col-4">
                        <label>
                            <input class="decoration" name="opex_invoice" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                </div>

                {{-- Batch Shipment & Storage Fee --}}
                <div class="row">
                    <div class="col-4">
                        <h3>{!! "&emsp;" !!}Batch Shipment & Storage Fee Return Invoice</h3>
                    </div>

                    <div class="col-4">
                        <label>
                            <input class="decoration" name="fba_storage_fee_invoice" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                </div>

                {{-- Final Credit  --}}
                <div class="row">
                    <div class="col-4">
                        <h3>{!! "&emsp;" !!}Final Credit</h3>
                    </div>

                    <div class="col-4">
                        <label>
                            <input class="decoration" name="final_credit" type="number" step="0.01"
                                   min="-99999999.99" max="99999999.99">
                        </label>
                    </div>
                </div>

                <br>

                {{-- Button --}}
                <div class="row justify-content-center align-items-center">
                    <div class="col-3">
                        <button class="btn btn-primary" type="submit" id="cbx_submit">Submit</button>
                    </div>
                    <div class="col-3">
                        <button class="btn btn-primary" type="button" id="cbx_cancel_btn">Cancel</button>
                    </div>
                </div>

            </form>

        </div>
        <!-- colorbox html part end -->
        @endsection

        @push('js')
            <script type="text/javascript">
                $(function () {
                    $('#inline_report_date').datepicker({
                        format: 'yyyy-mm',//日期時間格式
                        viewMode: "months",
                        minViewMode: "months",
                        ignoreReadonly: false,  //禁止使用者輸入 啟用唯讀
                        autoclose: true
                    });

                    $("#cbx_form").submit(function (e) {
                        e.preventDefault();

                        let reportDate = $('#inline_report_date').val();
                        let clientCode = $('#sel_client_code').find(":selected").val();

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        let ajaxFormOption = {
                            type: "post", //提交方式
                            data: {client_code: clientCode}, //提交方式
                            url: origin + "/invoice/createBill",
                            success: function (res) { //提交成功的回撥函式
                                console.log(res);
                                console.log('form');
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
                                console.log('error');
                                console.log(e);
                                swal({
                                    icon: 'error',
                                    text: e
                                });
                            }
                        };

                        $.ajax({
                            url: origin + '/invoice/validation/' + reportDate + '/' + clientCode,
                            type: 'get',
                            success: function (res) {
                                if (res.status === 200) {
                                    $("#cbx_form").ajaxSubmit(ajaxFormOption);
                                } else if (res.status === 202) {
                                    swal({
                                        title: "Are you sure ?",
                                        text: (res.msg),
                                        icon: 'warning',
                                        buttons: true,
                                        buttons: ["No,Cancel Plx!", "Yes,Delete it!"]
                                    })
                                        .then(function (isConfirm) {
                                            if (isConfirm) {
                                                deleteIssue(reportDate);
                                                $("#cbx_form").ajaxSubmit(ajaxFormOption);
                                            }
                                        });
                                } else {
                                    swal({
                                        icon: 'warning',
                                        text: res.msg
                                    });
                                    return false;
                                }
                            }
                        });
                    });

                    $('#cbx_cancel_btn').click(function () {
                        $.colorbox.close();
                    });

                    $('#search_report_date').datepicker({
                        format: 'yyyy-mm',//日期時間格式
                        viewMode: "months",
                        minViewMode: "months",
                        ignoreReadonly: false,  //禁止使用者輸入 啟用唯讀
                        autoclose: true
                    });

                    $('button.issue_btn').click(function () {
                        let data = [];
                        data['report_date'] = $(this).parent().parent().find('[class="report_date"]').text();
                        data['client_code'] = $(this).parent().parent().find('[class="client_code"]').text();
                        data['_token'] = $('meta[name="csrf-token"]').attr('content');


                        $.colorbox({
                            iframe: false,
                            // preloading: false,
                            href: origin + '/invoice/edit',
                            width: "80%",
                            height: "80%",
                            returnFocus: false,
                            data: {
                                _token: data['_token'],
                                report_date: data['report_date'],
                                client_code: data['client_code'],
                            },
                            onComplete: function () {
                                //binding jquery.steps plugin
                                $('#steps-nav').steps({
                                    doneClass: "",
                                });

                                $("button#edit_btn").unbind("click");
                                $("button#inline_submit").unbind("click");

                                $('#cancel_btn').click(function () {
                                    console.log('x');
                                    $.colorbox.close();
                                });

                                // prepare Options Object
                                let options = {
                                    url: '/invoice/runReport/1',
                                    responseType: 'blob', // important
                                    type: 'POST',
                                    success: function (res) {
                                        let msg = "Your file(s) are being processed.";
                                        msg += "Please check back later.";
                                        msg += "Go to your invoice list to get status information for all of your reports";

                                        swal({
                                            text: msg,
                                            icon: 'success',
                                        })
                                            .then(function (isConfirm) {
                                                if (isConfirm) {
                                                    $('#cboxOverlay').remove();
                                                    $('#colorbox').remove();
                                                    $.colorbox.close();
                                                }
                                            });

                                    }, error: function (e) {
                                        swal({
                                            icon: "error",
                                            text: e
                                        });
                                    }
                                };

                                // pass options to ajaxForm
                                $('#step_form').ajaxForm(options);
                            }.bind(this)
                        });
                    });

                    $('button.delete_btn').click(function () {
                        let _token = $('meta[name="csrf-token"]').attr('content');
                        let id = $(this).parent().parent().find('[name="bill_state_id"]').val();
                        console.log(id);

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': _token
                            }
                        });

                        $.ajax({
                            url: origin + '/invoice/issue/byID/' + id,
                            type: 'delete',
                            success: function (res) {
                                swal({
                                    icon: res.icon,
                                    text: res.msg
                                });
                            }, error: function (e) {
                                swal({
                                    icon: 'error',
                                    text: e
                                });
                            }
                        });
                    });
                });

                $('button#gen_sales_btn').click(function () {
                    let reportDate = $('#search_report_date').val();
                    let clientCode = $("#sel_client_code option:selected").val();
                    let _token = $('meta[name="csrf-token"]').attr('content');

                    if (!reportDate || !clientCode) {
                        swal({
                            icon: "error",
                            text: "report date and client code can't be empty"
                        });
                        return;
                    }

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': _token
                        }
                    });

                    $.ajax({
                        url: origin + '/invoice/validation/' + reportDate + '/' + clientCode,
                        type: 'get',
                        success: function (res) {
                            if (res.status === 200) {
                                swal({
                                    icon: 'success',
                                    text: 'processing'
                                });
                                ajaxRunBillingStatement();

                            } else if (res.status === 202) {
                                swal({
                                    title: "Are you sure ?",
                                    text: (res.msg),
                                    icon: 'warning',
                                    buttons: true,
                                    buttons: ["No,Cancel Plx!", "Yes,Delete it!"]
                                })
                                    .then(function (isConfirm) {
                                        if (isConfirm) {

                                            swal({
                                                text: "processing!",
                                                icon: "success",
                                                button: "OK",
                                            });

                                            deleteIssue(reportDate);
                                            ajaxRunBillingStatement();
                                        }
                                    });
                            } else {
                                swal({
                                    icon: 'warning',
                                    text: res.msg
                                });
                                return false;
                            }
                        }
                    });
                });

                $("#create_sales_btn").colorbox({inline: true, width: "60%", height: "80%", closeButton: true});

                function ajaxRunBillingStatement() {
                    let reportDate = $('#search_report_date').val();
                    let clientCode = $("#sel_client_code option:selected").val();
                    let _token = $('meta[name="csrf-token"]').attr('content');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': _token
                        }
                    });

                    $.ajax({
                        url: origin + '/invoice/runReport/0',
                        type: 'post',
                        data: {step_report_date: reportDate, client_code: clientCode},
                        success: function (res) {
                            console.log(res);
                        }
                    });
                }

                function deleteIssue(reportDate) {
                    let _token = $('meta[name="csrf-token"]').attr('content');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': _token
                        }
                    });

                    $.ajax({
                        url: origin + '/invoice/issue/byDate/' + reportDate,
                        type: 'delete',
                        success: function (res) {
                            console.log(res);
                        }, error: function (e) {
                            console.log(e);
                            swal({
                                icon: 'error',
                                text: e
                            });
                        }
                    });
                }

            </script>
        @endpush

        @push('css')
            <style>
                input[class="decoration"] {
                    outline: 0;
                    border-width: 0 0 2px;
                }
            </style>
    @endpush

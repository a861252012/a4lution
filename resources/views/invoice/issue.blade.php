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
            <li class="breadcrumb-item"><a href="{{ route('invoice.issue.view') }}">{{ __('INVOICE') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('ISSUE') }}</li>
        @endcomponent
    @endcomponent

    <div class="wrapper wrapper-content">
        <!-- Card -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                @csrf

                <form method="GET" action="{{ route('invoice.issue.view') }}" role="form" class="form">
                    <div class="row">
                        {{-- CLIENT CODE --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="sel_client_code">Client Code</label>
                                <select class="form-control _fz-1" data-toggle="select" name="sel_client_code"
                                        id="sel_client_code">
                                    <option value={{''}} @if(request('client_code') === 'all'){{ 'selected' }} @endif>
                                        {{'all'}}
                                    </option>
                                    @forelse ($clientCodeList as $item)
                                        <option value="{{$item}}" @if(request('sel_client_code') == $item)
                                            {{ 'selected' }} @endif>{{$item}}</option>
                                    @empty
                                        <option value="">{{'NONE'}}</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        {{-- REPORT DATE --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="search_report_date">Report Date</label>
                                <input class="form-control _fz-1" name="report_date" id="search_report_date"
                                       type="text" placeholder="REPORT DATE" value="{{ request('report_date') }}">
                            </div>
                        </div>

                        {{-- SEARCH --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <label class="form-control-label" for="submit_btn"></label>
                            <div class="form-group mb-0">
                                <button class="form-control _fz-1 btn _btn btn-primary" id="submit_btn" type="submit"
                                        style="margin-top: 6px;">Search
                                </button>
                            </div>
                        </div>

                        {{-- GENERATE SUMMARY --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <label class="form-control-label" for="create_sales_btn"></label>
                            <div class="form-group mb-0">
                                <button class="form-control _fz-1 btn _btn btn-primary" id="create_sales_btn"
                                        type="button" style="margin-top: 6px;">Generate Sales Summary
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
                        <th>Avolution Commission</th>
                        <th>Commission Type</th>
                        <th>Total Sales Orders</th>
                        <th>Total Sales Amount</th>
                        <th>Total Expense</th>
                        <th>Action</th>
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
                                <button class="btn btn-primary issue_btn btn-sm _fz-1" type="button"
                                        billing-statement-id="{{ $item->id }}"
                                @if($item->commission_type === "manual") {{ 'disabled' }} @endif>
                                    <span class="btn-inner--text">Issue Invoice</span>
                                </button>
                                <button class="btn btn-danger delete_btn btn-sm _fz-1" type="button">
                                    <span class="btn-inner--text">Delete</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>

            </div>
            {{-- Pagination --}}
            @if($lists && $lists->lastPage() > 1)
                <div class="d-flex justify-content-center" style='margin-top: 20px;'>
                    {{ $lists->appends($_GET)->links() }}
                </div>
            @endif
        </div>
        <!-- ./Card -->
    </div>
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
                data['billing_statement_id'] = $(this).attr('billing-statement-id');

                $.colorbox({
                    iframe: false,
                    href: origin + '/invoice/edit',
                    width: "70%",
                    height: "70%",
                    returnFocus: false,
                    data: {
                        _token: data['_token'],
                        report_date: data['report_date'],
                        client_code: data['client_code'],
                        billing_statement_id: data['billing_statement_id'],
                    },
                    onComplete: function () {
                        //binding jquery.steps plugin
                        $('#steps-nav').steps({
                            doneClass: "",
                        });

                        $("button#inline_submit").unbind("click");

                        $('#cancel_btn').click(function () {
                            $('#cboxOverlay').remove();
                            $('#colorbox').remove();
                        });

                        // prepare Options Object
                        let options = {
                            url: '/ajax/invoice/export',
                            responseType: 'blob', // important
                            type: 'POST',
                            beforeSend: function () {
                                $('#cboxOverlay').remove();
                                $('#colorbox').remove();
                                $.colorbox.close();
                            }, success: function () {
                                let msg = "Your file(s) are being processed.";
                                msg += "Please check back later.";
                                msg += "Go to your invoice list to get status information for all of your reports";

                                swal({
                                    text: msg,
                                    icon: 'success',
                                }).then(function (isConfirm) {
                                    if (isConfirm) {
                                        $.colorbox.close();
                                    }
                                });

                            }, error: function (e) {
                                // 顯示 Validate Error
                                let errors = [];
                                $.each(JSON.parse(e.responseText).errors, function (col, msg) {
                                    errors.push(msg.toString());
                                });

                                swal({
                                    icon: 'error',
                                    text: errors.join("\n")
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
                        }).then(function (isConfirm) {
                            if (isConfirm) {
                                $.colorbox.close();
                            }
                        });
                    }, error: function (e) {
                        // 顯示 Validate Error
                        let errors = [];
                        $.each(JSON.parse(e.responseText).errors, function (col, msg) {
                            errors.push(msg.toString());
                        });

                        swal({
                            icon: 'error',
                            text: errors.join("\n")
                        });
                    }
                });
            });
        });

        $('button#create_sales_btn').click(function () {
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
                url: origin + '/ajax/billing-statements',
                type: 'post',
                data: {
                    report_date: reportDate,
                    client_code: clientCode
                },
                success: function () {
                    swal({
                        text: "Generate Summary Complete!",
                        icon: "success",
                        button: "OK",
                    });

                    location.reload();
                }, error: function (e) {
                    // 顯示 Validate Error
                    let errors = [];
                    $.each(JSON.parse(e.responseText).errors, function (col, msg) {
                        errors.push(msg.toString());
                    });

                    swal({
                        icon: 'error',
                        text: errors.join("\n")
                    });
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
                    // 顯示 Validate Error
                    let errors = [];
                    $.each(JSON.parse(e.responseText).errors, function (col, msg) {
                        errors.push(msg.toString());
                    });

                    swal({
                        icon: 'error',
                        text: errors.join("\n")
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

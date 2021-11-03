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
                            <input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}">

                            <form method="GET" action="/invoice/issue" role="form" class="form">
                                <div class="row">
                                    {{-- CLIENT CODE --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="sel_client_code">CLIENT CODE</label>
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
                                            <label class="form-control-label" for="erp_order_id">REPORT DATE</label>
                                            <input class="form-control" name="report_date" id="report_date"
                                                   type="text" placeholder="REPORT DATE"
                                                   value="{{$report_date ?? ''}}">
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

                                    {{-- SUMMARY --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <label class="form-control-label" for="gen_sales_btn"></label>
                                        <div class="form-group">
                                            <button class="form-control btn btn-primary" id="gen_sales_btn"
                                                    type="button" style="margin-top: 6px;">GENERATE SALES SUMMARY
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
                                <th>TOTAL SALES ORDERS</th>
                                <th>TOTAL SALES AMOUNT</th>
                                <th>TOTAL EXPENSES</th>
                                <th>SALES GP</th>
                                <th>AVOLUTION COMMISSION</th>
                                <th>SALES TAX HANDLING</th>
                                <th>SALES CREDIT</th>
                                <th>OPEX INVOICE</th>
                                <th>FBA&STORAGE FEE INVOICE</th>
                                <th>FINAL CREDIT</th>
                                <th>ACTION</th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse ($lists as $item)
                                <tr>
                                    <input type="hidden" name="bill_state_id" value="{{ $item->id }}">
                                    <td class="report_date">{{ $item->report_date ?? '' }}</td>
                                    <td class="client_code">{{ $item->client_code ?? '' }}</td>
                                    <td class="total_sales_orders">{{ $item->total_sales_orders ?? '' }}</td>
                                    <td class="total_sales_amount">{{ $item->total_sales_amount ?? '' }}</td>
                                    <td class="total_expenses">{{ $item->total_expenses ?? '' }}</td>
                                    <td class="sales_gp">{{ $item->sales_gp ?? '' }}</td>
                                    <td class="avolution_commission">{{ $item->avolution_commission ?? '' }}</td>
                                    <td class="sales_tax_handling">{{ $item->sales_tax_handling ?? '' }}</td>
                                    <td class="sales_credit">{{ $item->sales_credit ?? '' }}</td>
                                    <td class="opex_invoice">{{ $item->opex_invoice ?? '' }}</td>
                                    <td class="fba_storage_fee_invoice">{{ $item->fba_storage_fee_invoice ?? '' }}</td>
                                    <td class="final_credit">{{ $item->final_credit ?? '' }}</td>
                                    <td>
                                        <button class="btn btn-primary issue_btn btn-sm" type="button" billing-statement-id="{{ $item->id }}">
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
@endsection

@push('js')
    {{--    <script src="{{ asset('argon') }}/vendor/select2/dist/js/select2.min.js"></script>--}}
    {{--    <script src="{{ asset('argon') }}/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>--}}

    <script type="text/javascript">
        $(function () {
            $('#report_date').datepicker({
                format: 'yyyy-mm',//日期時間格式
                viewMode: "months",
                minViewMode: "months",
                ignoreReadonly: false,  //禁止使用者輸入 啟用唯讀
                autoclose: true
            });

            // $('#report_date').datepicker('update', reportDate);

            $('button.issue_btn').click(function () {
                let data = [];
                data['report_date'] = $(this).parent().parent().find('[class="report_date"]').text();
                data['client_code'] = $(this).parent().parent().find('[class="client_code"]').text();
                data['_token'] = $('meta[name="csrf-token"]').attr('content');
                data['billing_statement_id'] = $(this).attr('billing-statement-id');

                $.colorbox({
                    iframe: false,
                    // preloading: false,
                    href: origin + '/invoice/edit',
                    width: "80%",
                    height: "100%",
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

                        // $('#cancel_btn').unbind("click");
                        $("button#edit_btn").unbind("click");
                        $("button#inline_submit").unbind("click");
                        // $("button#cancel_btn").off('click');
                        // $("button#edit_btn").off('click');
                        // $("button#inline_submit").off('click');
                        // $('button#inline_submit').hide();

                        $('#cancel_btn').click(function () {
                            console.log('x');
                            $('#cboxOverlay').remove();
                            $('#colorbox').remove();
                        });

                        // $('#cancel_btn').one("click", function () {
                        //     console.log('x');
                        //     $('#cboxOverlay').remove();
                        //     $('#colorbox').remove();
                        //     $.colorbox.close();
                        // });

                        // $("#button#cancel_btn").one( "click", function() {
                        //     console.log("cancel_btn");
                        //     $('#cboxOverlay').remove();
                        //     $('#colorbox').remove();
                        // });

                        // $("p").one("click",function(){
                        //     $(this).animate({fontSize:"+=6px"});
                        // });

                        // prepare Options Object
                        let options = {
                            url: '/ajax/invoice/export',
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
                // $.colorbox.resize();
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
            let reportDate = $('#report_date').val();
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
                url: origin + '/invoice/checkIfReportExist',
                type: 'post',
                data: {report_date: reportDate, client_code: clientCode},
                success: function (res) {
                    if (res.status !== 'failed') {
                        swal({
                            icon: 'success',
                            text: 'processing'
                        });
                        ajaxRunBillingStatement();

                    } else {
                        swal({
                            title: "Are you sure?",
                            text: ("Duplicate entry with the Client Code and Report!"),
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
                            // else {
                            //     // $('#cboxOverlay').remove();
                            //     // $('#colorbox').remove();
                            //     $.colorbox.close();
                            // }
                        });
                    }
                }
            });
        });

        function ajaxRunBillingStatement() {
            let reportDate = $('#report_date').val();
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
                success: function (res) {
                    console.log(res);
                    swal({
                        text: "Generate Summary Complete!",
                        icon: "success",
                        button: "OK",
                    });
                    location.reload();
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

        // function validateForm() {
        //     let accNickName = $('#acc_nick_name').val();
        //     let erpOrderId = $('#erp_order_id').val();
        //     let shippedDate = $('#shipped_date').val();
        //     let packageID = $('#package_id').val();
        //     let sku = $('#sku').val();
        //     if (!accNickName && !erpOrderId && !shippedDate && !packageID && !sku) {
        //         swal({
        //             icon: "warning",
        //             text: "must have at least one search condition"
        //         });
        //         return false;
        //     }
        // }
    </script>
@endpush

@push('css')
    {{--    <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css"/>--}}

    <style>
        /*input[type=text] {*/
        /*    background: transparent;*/
        /*    border: none;*/
        /*    border-bottom: 1px solid #000000;*/
        /*}*/
    </style>
@endpush

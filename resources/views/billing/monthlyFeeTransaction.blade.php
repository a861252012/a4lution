@extends('layouts.app')

@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('Billing') }}
            @endslot

            <li class="breadcrumb-item"><a href="{{ route('monthly_fee_transaction.view') }}">{{ __('Billing') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Monthly Fee Transactions') }}</li>
        @endcomponent
    @endcomponent

    <div class="wrapper wrapper-content">
        <!-- Table -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                <form method="GET" action="{{ route('monthly_fee_transaction.view') }}" role="form" class="form">
                    <div class="row">

                        {{-- CLIENT CODE --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="client_code">Client Code</label>
                                <select class="form-control _fz-1" data-toggle="select" name="client_code"
                                        id="client_code">
                                    <option value={{''}} @if(request('client_code') === 'all'){{ 'selected' }} @endif>
                                        {{'all'}}
                                    </option>
                                    @forelse ($clientCodeList as $item)
                                        <option value="{{$item}}" @if(request('client_code') == $item)
                                            {{ 'selected' }} @endif>{{$item}}</option>
                                    @empty
                                        <option value="">{{'NONE'}}</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        {{-- Billing Month --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="billing_month">Billing Month</label>
                                <input class="form-control _fz-1" name="billing_month" id="billing_month" type="text"
                                       placeholder="Billing Month" value="{{ request('billing_month') }}">
                            </div>
                        </div>

                        {{-- Search --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <label class="form-control-label _fz-1" for="submit_btn"></label>
                            <div class="form-group mb-0">
                                <button class="form-control btn btn-primary _fz-1 _btn" id="submit_btn"
                                        type="submit" style="margin-top: 6px;">Search
                                </button>
                            </div>
                        </div>

                        {{-- Create --}}
                        <div class="col-lg-2 col-md-6 col-sm-6 text-right">
                            <label class="form-control-label _fz-1" for="create_btn"></label>
                            <div class="form-group mb-0">
                                <a id="create_btn" class="form-control btn btn-primary" href="#inline_content"
                                   style="margin-top: 6px;">
                                    <span class="_fz-1">Create</span>
                                </a>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-sm _table" id="datatable-basic">
                    <thead class="thead-light">
                    <tr>
                        <th>Billing Month</th>
                        <th>Client Code</th>
                        <th>Currency</th>
                        <th>Paid Date</th>
                        <th>Paid Amount</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($lists as $item)
                        <tr>
                            <td>{{ $item->formatted_billing_month }}</td>
                            <td>{{ $item->client_code }}</td>
                            <td>{{ $item->currency }}</td>
                            <td>{{ $item->paid_date }}</td>
                            <td>{{ $item->paid_amount }}</td>
                            <td>{{ $item->user_name }}</td>
                            <td>{{ $item->created_at }}</td>
                            <td class="py-1">
                                <div class="dropdown">
                                    <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                       data-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow" style="">
                                        <a class="dropdown-item _edit-btn" href="#inline_content"
                                           statement-id="{{ $item->id }}">Edit</a>
                                        <a class="dropdown-item _delete-btn" statement-id="{{ $item->id }}">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
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

    <!-- colorbox html part start -->
    <div style='display:none'>
        <div class="container" id='inline_content'>
            <form enctype="multipart/form-data" id="cbx_form">

                @csrf
                {{-- CLIENT CODE --}}
                <div class="row py-3">
                    <div class="col-3 form-group">
                        <label class="form-control-label _required-before" for="cbx_client_code">
                            Client Code
                        </label>
                    </div>
                    <div class="col-3 form-group">
                        <select class="form-control _fz-1" name="client_code" id="cbx_client_code" required>
                            @forelse ($clientCodeList as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @empty
                                <option value="">{{ 'NONE' }}</option>
                            @endforelse
                        </select>
                    </div>
                </div>

                <!-- MONTHLY FEE -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="cbx_monthly_fee">Monthly Fee</label>
                    </div>

                    <div class="col-4 form-group">
                        <input class="form-control" id="cbx_monthly_fee" placeholder="monthly fee" type="text"
                               name="amount_description">
                    </div>
                </div>

                <!-- BILLING MONTH -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label _required-before" for="cbx_billing_month">
                            Billing Month
                        </label>
                    </div>
                    <div class="col-4 form-group">
                        <input class="form-control" name="billing_month" id="cbx_billing_month" required>
                    </div>
                </div>

                <!-- PAID AMOUNT -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label _required-before" for="cbx_paid_amount">Paid Amount</label>
                    </div>

                    <div class="col-4 form-group">
                        <input class="form-control" id="cbx_paid_amount" placeholder="Paid Amount" type="number"
                               step="0.01" name="amount" min="0" required>
                    </div>
                </div>

                <!-- CURRENCY  -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label _required-before" for="cbx_currency">Currency</label>
                    </div>
                    <div class="col-3 form-group">
                        <select class="form-control _fz-1" name="currency" id="cbx_currency" required>
                            @forelse ($currencyList as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @empty
                                <option value="">{{ 'NONE' }}</option>
                            @endforelse
                        </select>
                    </div>
                </div>

                <!-- PAID DATE -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label _required-before" for="cbx_paid_date">Paid Date</label>
                    </div>

                    <div class="col-4 form-group">
                        <input class="form-control" id="cbx_paid_date" placeholder="Paid Date"
                               type="text" name="deposit_date" required>
                    </div>
                </div>

                <!-- REMARKS -->
                <div class="row">
                    <div class="col-12">
                        <h3>Remarks</h3>
                    </div>

                    <div class="col-12">
                        <input class="form-control" id="cbx_remark" name="remarks"
                               style="outline:0;border-width: 0 0 2px;" type="text">
                    </div>
                </div>

                <!-- BUTTONS -->
                <div class="row justify-content-center align-items-center py-5">
                    <div class="col-3">
                        <button class="btn btn-primary" type="submit" id="cbx_submit">Submit</button>
                    </div>

                    <div class="col-3">
                        <button class="btn btn-primary" type="button" id="cbx_cancel_btn">Cancel</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <!-- colorbox html part end -->
@endsection

@push('js')
    <script type="text/javascript">
        $(function () {
            $('#billing_month').datepicker({
                format: 'yyyy-mm',//日期時間格式
                viewMode: "months",
                minViewMode: "months",
                ignoreReadonly: true,  //禁止使用者輸入 啟用唯讀
                autoclose: true
            });

            $('#cbx_billing_month').datepicker({
                format: 'yyyymm',//日期時間格式
                viewMode: "months",
                minViewMode: "months",
                ignoreReadonly: true,  //禁止使用者輸入 啟用唯讀
                autoclose: true
            });

            $('#cbx_paid_date').datepicker({
                format: 'yyyy-mm-dd',//日期時間格式
                ignoreReadonly: false,  //禁止使用者輸入 啟用唯讀
                autoclose: true
            });

            $('#cbx_cancel_btn').click(function () {
                $.colorbox.close();
                return false;
            });

            $('#cbx_client_code').on('change', function () {
                ajaxGetMonthlyFee($(this).val());
            });

            //create button event
            $('#create_btn').colorbox({
                inline: true,
                width: "40%",
                height: "70%",
                closeButton: true,
                onComplete: function () {
                    resetInput();

                    //get default monthly fee value
                    ajaxGetMonthlyFee($('#cbx_client_code').val());

                    //submit
                    $("#cbx_submit").unbind("click");

                    $('#cbx_submit').on('click', function () {
                        let ajaxFormOption = {
                            type: 'post',
                            url: origin + "/ajax/billing/monthly-fee-transaction",
                            success: function (res) {
                                ajaxSuccessHandling(res.msg);
                            }, error: function (e) {
                                ajaxErrorHandling(e);
                            }
                        };

                        $('#cbx_form').ajaxForm(ajaxFormOption);
                    });
                }
            });

            //edit button event
            $('._edit-btn').on('click', function () {
                let statementID = $(this).attr('statement-id');

                $.colorbox({
                    href: '#inline_content',
                    inline: true,
                    width: "45%",
                    height: "75%",
                    closeButton: true,
                    onComplete: function () {
                        getEditData(statementID);

                        //submit
                        $("#cbx_submit").unbind("click");

                        $('#cbx_submit').on('click', function () {
                            $('#cbx_form').append('<input id="put_method" type="hidden" name="_method" value="put">');

                            let ajaxFormOption = {
                                type: 'post',
                                url: origin + '/ajax/billing/monthly-fee-transaction/' + statementID,
                                success: function (res) {
                                    ajaxSuccessHandling(res.msg);
                                    $('#put_method').remove();
                                }, error: function (e) {
                                    ajaxErrorHandling(e);
                                }
                            };

                            $('#cbx_form').ajaxForm(ajaxFormOption);
                        });
                    }
                });
            });

            //delete button event
            $('._delete-btn').on('click', function () {
                let statementID = $(this).attr('statement-id');

                swal({
                    title: 'Are you sure ?',
                    text: ('DELETE'),
                    icon: 'warning',
                    buttons: ['No,Cancel', 'Yes,Delete it !']
                }).then(function (isConfirm) {
                    if (isConfirm) {
                        deleteByID(statementID)
                    }
                });
            });

            // 確保input輸入的值符合格式
            $('#cbx_paid_amount').on('input', function () {
                $(this).val(
                    setTwoDecimal($(this).val())
                );
            });
        });


        function ajaxGetMonthlyFee(clientCode) {
            ajaxHeader();

            $.ajax({
                url: origin + '/ajax/billing/monthly-fee/' + clientCode,
                type: 'get',
                success: function (res) {
                    $('#cbx_monthly_fee').val(res.data.monthly_fee);
                    $('#cbx_paid_amount').val(res.data.paid_amount);
                    $('#cbx_currency').val(res.data.currency);
                }, error: function (e) {
                    ajaxErrorHandling(e);
                }
            });
        }

        // 限制 input 只能輸入小數點後兩位
        function setTwoDecimal(num) {
            if (num.indexOf(".") !== 0) {
                num = num.replace(/[^\d.]/g, "");  // 清除'數字'和 '.' 以外的字元
                num = num.replace(/\.{2,}/g, "."); // 只保留第一個 '.' 清除多餘的
                num = num.replace(".", "$#$").replace(/\./g, "").replace("$#$", ".");
                num = num.replace(/^(\-)*(\d+)\.(\d\d).*$/, '$1$2.$3'); // 只能輸入兩個小數
                if (num.indexOf(".") < 0 && num !== '') { // 以上已經過濾，此處控制的是如果沒有小數點，首位不能為類似於 01、02的金額
                    num = parseFloat(num);
                }
            } else {
                num = '';
            }

            return num;
        }

        function deleteByID(statementID) {
            ajaxHeader();

            $.ajax({
                url: origin + '/ajax/billing/monthly-fee/' + statementID,
                type: 'delete',
                success: function (res) {
                    ajaxSuccessHandling(res.msg);
                }, error: function (e) {
                    ajaxErrorHandling(e);
                }
            });
        }

        function getEditData(statementID) {
            ajaxHeader();

            $.ajax({
                url: origin + '/ajax/billing/monthly-fee-transaction/' + statementID,
                type: 'get',
                success: function (res) {
                    setEditData(res.data);
                }, error: function (e) {
                    ajaxErrorHandling(e);
                }
            });
        }

        function updateByID(statementID) {
            ajaxHeader();

            $.ajax({
                url: origin + '/ajax/billing/monthly-fee/' + statementID,
                type: 'delete',
                success: function (res) {
                    ajaxSuccessHandling(res.msg);
                }, error: function (e) {
                    ajaxErrorHandling(e);
                }
            });
        }

        function setEditData(data) {
            $('#cbx_client_code').val(data.client_code).attr('disabled', true).attr('required', false);
            $('#cbx_monthly_fee').val(data.amount_description);
            $('#cbx_billing_month').val(data.billing_month).attr('disabled', true).attr('required', false);
            $('#cbx_paid_amount').val(data.amount);
            $('#cbx_currency').val(data.currency);
            $('#cbx_paid_date').val(data.deposit_date);
            $('#cbx_remark').val(data.remarks);
        }

        function ajaxErrorHandling(errorMsg) {
            let errors = [];

            $.each(JSON.parse(errorMsg.responseText).errors, function (col, msg) {
                errors.push(msg.toString());
            });

            swal({
                icon: 'error',
                text: errors.join("\n")
            });
        }

        function ajaxSuccessHandling(msg) {
            swal({
                icon: 'success',
                text: msg
            }).then(function (isConfirm) {
                if (isConfirm) {
                    $.colorbox.close();
                }
            });
        }

        function ajaxHeader() {
            return $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }

        function resetInput() {
            $('#cbx_billing_month').val('').attr('disabled', false);
            $('#cbx_paid_amount').val('');
            $('#cbx_paid_date').val('');
            $('#cbx_remark').val('');
            $('#cbx_client_code').attr('disabled', false);
        }
    </script>
@endpush

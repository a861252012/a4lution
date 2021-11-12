@extends('layouts.app', [
    'parentSection' => 'ADMIN',
    'elementName' => 'APPROVAL-ADMIN'
])

@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('ADMIN') }}
            @endslot
            <li class="breadcrumb-item"><a href="{{ route('page.index', 'components') }}">{{ __('ADMIN') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('APPROVAL ADMIN') }}</li>
        @endcomponent
    @endcomponent

    <div class="wrapper wrapper-content animated">
        <!-- Table -->
        <div class="card py-2">
            <!-- Card header -->
            <div class="card-header">
                <div class="row">

                    {{-- REPORT DATE --}}
                    <div class="col-lg-2 col-md-6 col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-control-label _fz-1" for="report_date">Report Date</label>
                            <input class="form-control _fz-1" name="report_date" id="report_date"
                                   type="text" placeholder="Report Date" value="{{$report_date ?? ''}}">
                        </div>
                    </div>

                    {{-- BATCH APPROVE --}}
                    <div class="col-lg-2 col-md-6 col-sm-6">
                        <label class="form-control-label" for="batch_approve_btn"></label>
                        <div class="form-group mb-0">
                            <button class="form-control btn _btn btn-primary _fz-1" id="batch_approve_btn"
                                    type="button" style="margin-top: 0.45rem;">Batch Approve
                            </button>
                        </div>
                    </div>

                    {{-- REVOKE APPROVAL --}}
                    <div class="col-lg-2 col-md-6 col-sm-6">
                        <label class="form-control-label" for="revoke_approval_btn"></label>
                        <div class="form-group mb-0">
                            <button class="form-control btn _btn btn-default _fz-1" id="revoke_approval_btn"
                                    type="button" style="margin-top: 0.45rem;">Revoke Approval
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')

    <script type="text/javascript">
        $(function () {
            $('#report_date').datepicker({
                format: 'yyyy-mm',//日期時間格式
                viewMode: "months",
                minViewMode: "months",
                ignoreReadonly: false,  //禁止使用者輸入 啟用唯讀
                autoclose: true
            });

            $('#batch_approve_btn').click(function () {
                let reportDate = $('#report_date').val();
                let _token = $('meta[name="csrf-token"]').attr('content');

                if (!reportDate) {
                    swal({
                        icon: "error",
                        text: "Report Date Can't Be Empty"
                    });
                    return;
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': _token
                    }
                });

                $.ajax({
                    url: origin + '/admin/approvaladmin/batch/' + reportDate,
                    type: 'put',
                    success: function (res) {
                        if (res.status !== 200) {
                            swal({
                                icon: 'error',
                                text: 'Error'
                            });
                        } else {
                            swal({
                                icon: 'success',
                                text: 'Updated'
                            });
                        }
                    },
                    error: function (e) {
                        swal({
                            icon: 'error',
                            text: e
                        });
                    }
                });
            });

            $('#revoke_approval_btn').click(function () {
                let reportDate = $('#report_date').val();
                let _token = $('meta[name="csrf-token"]').attr('content');

                if (!reportDate) {
                    swal({
                        icon: "error",
                        text: "report date can't be empty"
                    });
                    return;
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': _token
                    }
                });

                $.ajax({
                    url: origin + '/admin/approvaladmin/revoke/' + reportDate,
                    type: 'put',
                    success: function (res) {
                        if (res.status !== 200) {
                            swal({
                                icon: 'error',
                                text: res.msg
                            });
                        } else {
                            swal({
                                icon: 'success',
                                text: 'Updated'
                            });
                        }
                    },
                    error: function () {
                        swal({
                            icon: 'error',
                            text: 'Api Error'
                        });
                    }
                });
            });

        });
    </script>
@endpush


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

    {{--@section('content')--}}
    {{--    @include('forms.header')--}}

    <div class="wrapper wrapper-content animated">
        <!-- Table -->
        <div class="row">
            <div class="col">
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <div>
                            <div class="row">

                                {{-- REPORT DATE --}}
                                <div class="col-2 col-lg-2  col-sm-2">
                                    <div class="form-group">
                                        <label class="form-control-label" for="report_date">REPORT DATE</label>
                                        <input class="form-control" name="report_date" id="report_date"
                                               type="text" placeholder="REPORT DATE" value="{{$report_date ?? ''}}">
                                    </div>
                                </div>

                                {{-- BATCH APPROVE --}}
                                <div class="col-2 col-lg-2 col-sm-2">
                                    <label class="form-control-label" for="batch_approve_btn"></label>
                                    <div class="form-group">
                                        <button class="form-control btn btn-primary" id="batch_approve_btn"
                                                type="button" style="margin-top: 0.45rem;">BATCH APPROVE
                                        </button>
                                    </div>
                                </div>

                                {{-- REVOKE APPROVAL --}}
                                <div class="col-2 col-lg-2 col-sm-2">
                                    <label class="form-control-label" for="revoke_approval_btn"></label>
                                    <div class="form-group">
                                        <button class="form-control btn btn-default" id="revoke_approval_btn"
                                                type="button" style="margin-top: 0.45rem;">REVOKE APPROVAL
                                        </button>
                                    </div>
                                </div>

                            </div>
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
                    url: origin + '/admin/approvaladmin/' + reportDate,
                    type: 'put',
                    success: function (res) {
                        if (res.status !== 200) {
                            swal({
                                icon: 'error',
                                text: 'ERROR'
                            });
                        } else {
                            swal({
                                icon: 'success',
                                text: 'UPDATED'
                            });
                        }
                    }
                });
            });

        });
    </script>
@endpush


{{--@extends('layouts.app', [--}}
{{--    'title' => __('User Profile'),--}}
{{--    'navClass' => 'bg-default',--}}
{{--    'parentSection' => 'laravel',--}}
{{--    'elementName' => 'profile'--}}
{{--])--}}

@extends('layouts.app', [
    'parentSection' => 'fee',
    'elementName' => 'upload'
])

@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('FEE') }}
            @endslot

            <li class="breadcrumb-item"><a href="{{ route('page.index', 'components') }}">{{ __('FEE') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('UPLOAD') }}</li>
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
                            <form method="GET" action="/fee/upload" role="form" class="form">

                                <div class="row">
                                    <div class="col-2 col-lg-2 col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="input_date">DATE</label>
                                            <input class="form-control" name="search_date" id="input_date"
                                                   placeholder="date"
                                                   type="text" value="{{ $createdAt }}">
                                        </div>
                                    </div>
                                    {{--                                    {{ dd(get_defined_vars()) }}--}}

                                    <div class=" col-2 col-lg-2 col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="select_fee_type">FEE TYPE</label>
                                            <select class="form-control" data-toggle="select" name="fee_type"
                                                    id="select_fee_type">
                                                <option value="">all</option>
                                                <option value="platform_ad_fees"
                                                @if($feeType == 'platform_ad_fees') {{ 'selected' }} @endif>
                                                    Platform Advertisement Fee
                                                </option>
                                                <option value="amazon_date_range"
                                                @if($feeType == 'amazon_date_range') {{ 'selected' }} @endif>
                                                    Amazon Date Range Report
                                                </option>
                                                <option value="long_term_storage_fees"
                                                @if($feeType == 'long_term_storage_fees') {{ 'selected' }} @endif>
                                                    FBA Long Term Storage Fee
                                                </option>
                                                <option value="monthly_storage_fees"
                                                @if($feeType == 'monthly_storage_fees') {{ 'selected' }} @endif>
                                                    FBA Monthly Storage Fee
                                                </option>
                                                <option value="first_mile_shipment_fees"
                                                @if($feeType == 'first_mile_shipment_fees') {{ 'selected' }} @endif>
                                                    First Mile Shipment Fee
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-2 col-lg-2 col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="select_status">STATUS</label>
                                            <select class="form-control" data-toggle="select" name="status_type"
                                                    id="select_status">
                                                <option value="">all</option>
                                                <option value="completed" @if($status == 'completed') {{ 'selected' }} @endif>
                                                    Completed
                                                </option>
                                                <option value="processing" @if($status == 'processing') {{ 'selected' }} @endif>
                                                    Processing
                                                </option>
                                                <option value="failed" @if($status == 'failed') {{ 'selected' }} @endif>
                                                    Error
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-2 col-lg-2 col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="report_date">REPORT
                                                DATE</label>
                                            <input class="form-control" name="report_date" id="report_date"
                                                   value="{{$reportDate}}" placeholder="report date" type="text">
                                        </div>
                                    </div>

                                    <div class="col-2 col-lg-2 col-sm-2">
                                        <label class="form-control-label" for="submit_btn"></label>
                                        <div class="form-group">
                                            <button class="form-control btn btn-primary" id="submit_btn"
                                                    type="submit"
                                                    style="margin-top: 6px;">SEARCH
                                            </button>
                                        </div>
                                    </div>

                                    {{--                                    <div class="col-2 col-lg-2 col-sm-2 text-right">--}}
                                    {{--                                        <label class="form-control-label" for="inline_href"></label>--}}
                                    {{--                                        <div class="form-group">--}}
                                    {{--                                            <a id="upload_btn" class="form-control btn btn-primary" id="inline_href"--}}
                                    {{--                                               href="#inline_content" style="margin-top: 6px;">--}}
                                    {{--                                                <div>--}}
                                    {{--                                                    <i class="ni ni-cloud-upload-96"></i>--}}
                                    {{--                                                    <span>UPLOAD</span>--}}
                                    {{--                                                </div>--}}
                                    {{--                                            </a>--}}
                                    {{--                                        </div>--}}
                                    {{--                                    </div>--}}

                                    <div class="col-2 col-lg-2 col-sm-2 text-right">
                                        <label class="form-control-label" for="upload_btn"></label>
                                        <div class="form-group">
                                            <a id="upload_btn" class="form-control btn btn-primary"
                                               href="#inline_content" style="margin-top: 6px;">
                                                <div>
                                                    <i class="ni ni-cloud-upload-96"></i>
                                                    <span>UPLOAD</span>
                                                </div>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive py-4">
                        <table class="table table-flush" id="datatable-basic">
                            <thead class="thead-light">
                            <tr>
                                <th>DATE</th>
                                <th>USER</th>
                                <th>Report DATE</th>
                                <th>FEE TYPE</th>
                                <th>FILE NAME</th>
                                <th>STATUS</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($lists as $item)
                                <tr>
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->user_name }}</td>
                                    <td>{{ $item->report_date }}</td>
                                    <td>{{ $item->fee_type }}</td>
                                    <td>{{ $item->file_name }}</td>
                                    <td>{{ $item->status }}</td>
                                </tr>
                            @endforeach
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

    <!-- colorbox html part start -->
    <div style='display:none'>
        {{--        <div id='inline_content'>--}}
        <div class="container" id='inline_content'>

            <!-- Data Import/Platform Ads -->
            {{--            <form action="/fee/upload/file" method="post" id="inline_form" enctype="multipart/form-data">--}}
            <form enctype="multipart/form-data">
                {{--            <form enctype="multipart/form-data">--}}

                <div class="row">
                    <div class="col-4 form-group">
                        <label class="form-control-label">Data Import</label>
                    </div>
                    <div class="col-8 form-group">
                        <div class="input-group-btn">
                            <a class="btn btn-info genxls" id="downloadSampleFile">Sample File</a>
                        </div>
                    </div>
                </div>

                <!-- REPORT DATE -->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="inline_report_date">REPORT DATE</label>
                    </div>

                    <div class="col-4 form-group">
                        <input class="form-control" id="inline_report_date" placeholder="report date" type="text"
                               name="inline_report_date" readonly>
                    </div>
                </div>

                <!-- FEE TYPE-->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="inline_select_fee_type">FEE TYPE</label>
                    </div>
                    <div class="col-5 form-group">
                        <select class="form-control" data-toggle="select" name="inline_fee_type"
                                id="inline_select_fee_type">
                            <option value="platform_ad_fees">Platform Advertisement Fee</option>
                            <option value="amazon_date_range">Amazon Date Range Report</option>
                            <option value="long_term_storage_fees">FBA Long Term Storage Fee</option>
                            <option value="monthly_storage_fees">FBA Monthly Storage Fee</option>
                            <option value="first_mile_shipment_fees">First Mile Shipment Fee</option>
                        </select>
                    </div>
                </div>

                <!-- FILE INPUT-->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label">File Input</label>
                    </div>

                    <div class="col-8 form-group">
                        <div class="dropzone dropzone-single mb-3" data-toggle="dropzone">
                            <div class="fallback">
                                <div class="custom-file">
                                    {{--                                    <input type="file" class="custom-file-input" id="projectCoverUploads" required>--}}
                                    <input type="file" name="file" class="form-control" id="inline_file" required>
                                    <div class="required">Maximum size: 30 MB</div>
                                    {{--                                    <label class="custom-file-label" for="projectCoverUploads">Choose file</label>--}}
                                </div>
                            </div>
                            {{--                            <div class="dz-preview dz-preview-single">--}}
                            {{--                                <div class="dz-preview-cover">--}}
                            {{--                                    <img class="dz-preview-img" data-dz-thumbnail>--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center align-items-center">
                    <div class="col-3">
                        <button class="btn btn-primary" type="button" id="inline_submit">Submit</button>
                    </div>

                    <div class="col-3">
                        <button class="btn btn-primary" type="button" id="cancel_btn">Cancel</button>
                    </div>
                </div>

            </form>
        </div>
        {{--        </div>--}}
    </div>
    <!-- colorbox html part end -->
@endsection

@push('js')
    {{--    <script src="{{ asset('argon') }}/vendor/select2/dist/js/select2.min.js"></script>--}}
    {{--    <script src="{{ asset('argon') }}/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>--}}

    {{--    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>--}}

    {{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"--}}
    {{--            integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn"--}}
    {{--            crossorigin="anonymous"></script>--}}

    {{--    <script src="{{ asset('argon') }}/vendor/nouislider/distribute/nouislider.min.js"></script>--}}
    {{--    <script src="{{ asset('argon') }}/vendor/quill/dist/quill.min.js"></script>--}}
    {{--    <script src="{{ asset('argon') }}/vendor/dropzone/dist/min/dropzone.min.js"></script>--}}
    {{--    <script src="{{ asset('argon') }}/vendor/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>--}}

    {{--    <link href="{{asset("vendor/kartik-v/bootstrap-fileinput/css/fileinput.css")}}" rel="stylesheet">--}}
    {{--    <script src="{{asset("vendor/kartik-v/bootstrap-fileinput/js/fileinput.js")}}"></script>--}}

    <script type="text/javascript">
        $(function () {
            let inputDate = $('#input_date').val();
            let reportDate = $('#report_date').val();

            $('#input_date').datepicker({
                format: 'yyyy-mm-dd',//日期時間格式
                ignoreReadonly: true, //禁止使用者輸入 啟用唯讀
                autoclose: true
            });

            $('#report_date').datepicker({
                format: 'yyyy-mm',//日期時間格式
                viewMode: "months",
                minViewMode: "months",
                ignoreReadonly: true,  //禁止使用者輸入 啟用唯讀
                autoclose: true
            });

            $('#inline_report_date').datepicker({
                format: 'yyyy-mm',//日期時間格式
                viewMode: "months",
                minViewMode: "months",
                ignoreReadonly: true,  //禁止使用者輸入 啟用唯讀
                autoclose: true
            });

            $('#input_date').datepicker('update', inputDate);
            $('#report_date').datepicker('update', reportDate);
            $('#inline_report_date').datepicker('update', new Date());

            $("#upload_btn").colorbox({inline: true, width: "40%", height: "70%", closeButton: true});
            // $("#upload_btn").colorbox({inline: true, width: "auto", height: "auto", closeButton: true});

            $('#cancel_btn').click(function () {
                $.colorbox.close();
                return false;
            });

            $('#inline_submit').click(function () {
                let _token = $('meta[name="csrf-token"]').attr('content');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': _token
                    }
                });

                // Append data
                // let fd = new FormData();
                let origin = window.location.origin;
                let inlineReportDate = $('#inline_report_date').val();

                //檢查副檔名
                if ($.trim($("#inline_file").val()) === '') {
                    swal({
                        icon: "error",
                        text: "file can't be empty"
                    });
                    return false;
                }

                let file = $('#inline_file')[0].files[0];
                let fileType = file.name.slice((file.name.lastIndexOf(".") - 1 >>> 0) + 2);

                if (fileType !== 'xlsx') {
                    swal({
                        icon: "error",
                        text: "wrong file type"
                    });
                    return false;
                }

                //call api to check if monthly report exist
                $.ajax({
                    url: origin + '/fee/checkIfReportExist/' + inlineReportDate,
                    type: 'get',
                    async: false,
                    success: function (res) {
                        if (res.status === "failed") {
                            console.log('failed');
                            swal({
                                icon: "error",
                                text: "The selected report date " + inlineReportDate + " was closed"
                            })
                            return false;
                        } else {
                            console.log('secAjax');
                            secAjax();
                        }
                    }
                });
            });

            //check file size
            $('input[type=file]').change(e => {
                if (e.currentTarget.files.length > 0) {
                    if ((e.currentTarget.files[0].size / 1024 / 1024) > 30) {
                        $('#inline_submit').prop('disabled', true)
                    } else {
                        $('#inline_submit').prop('disabled', false)
                    }
                }
            })

            //export file
            $('#downloadSampleFile').click(function () {
                let inlineSelectType = $('#inline_select_fee_type :selected').val();

                window.location.href = origin + '/fee/export/' + inlineSelectType;
            });
        });

        function secAjax() {
            swal({
                icon: "success",
                text: "file uploaded"
            })
                .then(function (isConfirm) {
                    if (isConfirm) {
                        $.colorbox.close();
                    }
                });
            let fd = new FormData();
            let origin = window.location.origin;

            fd.append('file', $('#inline_file')[0].files[0]);
            fd.append('inline_report_date', $('#inline_report_date').val());
            fd.append('inline_fee_type', $('#inline_select_fee_type :selected').val());

            $.ajax({
                url: origin + '/fee/upload/file',
                type: 'post',
                processData: false,
                contentType: false,
                data: fd,
                success: function () {
                    console.log('success');
                }
            });
        }
    </script>
@endpush

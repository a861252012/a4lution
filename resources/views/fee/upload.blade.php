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

            <li class="breadcrumb-item"><a href="{{ route('fee.upload.view') }}">{{ __('FEE') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('UPLOAD') }}</li>
        @endcomponent
    @endcomponent

    <div class="wrapper wrapper-content">
        <!-- Table -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                <form method="GET" action="{{ route('fee.upload.view') }}" role="form" class="form">
                    <div class="row">

                        {{-- Created At --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="input_date">Created At</label>
                                <input class="form-control _fz-1" name="search_date" id="input_date"
                                       placeholder="date" type="text" value="{{  request('search_date') }}">
                            </div>
                        </div>

                        {{-- Fee Type --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="select_fee_type">Fee Type</label>
                                <select class="form-control _fz-1" data-toggle="select" name="fee_type"
                                        id="select_fee_type">
                                    <option value="">all</option>
                                    <option value="platform_ad_fees"
                                    @if(request('fee_type') == 'platform_ad_fees') {{ 'selected' }} @endif>
                                        Platform Advertisement Fee
                                    </option>
                                    <option value="amazon_date_range"
                                    @if(request('fee_type') == 'amazon_date_range') {{ 'selected' }} @endif>
                                        Amazon Date Range Report
                                    </option>
                                    <option value="long_term_storage_fees"
                                    @if(request('fee_type') == 'long_term_storage_fees') {{ 'selected' }} @endif>
                                        FBA Long Term Storage Fee
                                    </option>
                                    <option value="monthly_storage_fees"
                                    @if(request('fee_type') == 'monthly_storage_fees') {{ 'selected' }} @endif>
                                        FBA Monthly Storage Fee
                                    </option>
                                    <option value="first_mile_shipment_fees"
                                    @if(request('fee_type') == 'first_mile_shipment_fees') {{ 'selected' }} @endif>
                                        First Mile Shipment Fee
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="select_status">Status</label>
                                <select class="form-control _fz-1" data-toggle="select" name="status_type"
                                        id="select_status">
                                    <option value="">all</option>
                                    <option value="completed" @if(request('status_type') == 'completed')
                                        {{ 'selected' }} @endif>Completed
                                    </option>
                                    <option value="processing" @if(request('status_type') == 'processing')
                                        {{ 'selected' }} @endif>Processing
                                    </option>
                                    <option value="failed" @if(request('status_type') == 'failed')
                                        {{ 'selected' }} @endif>Error
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- Report Date --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="report_date">Report Date</label>
                                <input class="form-control _fz-1" name="report_date" id="report_date"
                                       value="{{ request('report_date') }}" placeholder="report date" type="text">
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

                        {{-- Upload --}}
                        <div class="col-lg-2 col-md-6 col-sm-6 text-right">
                            <label class="form-control-label _fz-1" for="upload_btn"></label>
                            <div class="form-group mb-0">
                                <a id="upload_btn" class="form-control btn btn-primary"
                                   href="#inline_content" style="margin-top: 6px;">
                                    <div>
                                        <i class="ni ni-cloud-upload-96"></i>
                                        <span class="_fz-1">Upload</span>
                                    </div>
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
                        <th>Created At</th>
                        <th>User</th>
                        <th>Report Date</th>
                        <th>Fee Type</th>
                        <th>File Name</th>
                        <th>Status</th>
                        <th>Error Msg</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($lists as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)
                                                    ->setTimezone(config('services.timezone.taipei')) }}</td>
                            <td>{{ $item->user_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->report_date)
                                                    ->setTimezone(config('services.timezone.taipei'))->format('F-Y') }}</td>
                            <td>{{ $item->fee_type }}</td>
                            <td>{{ $item->file_name }}</td>
                            <td>{{ $item->status }}</td>
                            <td>{{ $item->user_error_msg }}</td>
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

            <!-- Data Import/Platform Ads -->
            <form enctype="multipart/form-data">

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
                        <label class="form-control-label" for="inline_report_date">Report Date</label>
                    </div>

                    <div class="col-4 form-group">
                        <input class="form-control" id="inline_report_date" placeholder="report date" type="text"
                               name="inline_report_date" readonly>
                    </div>
                </div>

                <!-- FEE TYPE-->
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label" for="inline_select_fee_type">Fee Type</label>
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
                                    <input type="file" name="file" class="form-control" id="inline_file" required>
                                    <div class="required">Maximum size: 30 MB</div>
                                </div>
                            </div>
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
    </div>
    <!-- colorbox html part end -->
@endsection

@push('js')
    <script type="text/javascript">
        $(function () {
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

            $('#report_date').datepicker('update', reportDate);
            $('#inline_report_date').datepicker('update', new Date());

            $("#upload_btn").colorbox({inline: true, width: "40%", height: "70%", closeButton: true});

            $('#cancel_btn').click(function () {
                $.colorbox.close();
                return false;
            });

            $('#inline_submit').click(function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                let date = $('#inline_report_date').val();
                let type = $('#inline_select_fee_type :selected').val();

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

                let data = new FormData();
                data.append('file', file);

                if (fileType !== 'xlsx') {
                    swal({
                        icon: "error",
                        text: "wrong file type"
                    });
                    return false;
                }

                //暫時禁止用戶再次上傳檔案
                $('#inline_submit').prop('disabled', true);

                swal({
                    icon: "success",
                    text: "processing"
                })
                    .then(function (isConfirm) {
                        if (isConfirm) {
                            $.colorbox.close();
                        }
                    });
                //call api to check if monthly report exist and validate title
                $.ajax({
                    url: window.location.origin + '/fee/preValidation/' + date + '/' + type,
                    type: 'post',
                    processData: false,
                    contentType: false,
                    data: data,
                    success: function (res) {
                        if (res.status !== 200) {
                            //如驗證失敗則可再次上傳
                            $('#inline_submit').prop('disabled', false);

                            swal({
                                icon: "error",
                                text: res.msg
                            })
                            return false;
                        }

                        uploadAjax(file, date, type);
                    }, error: function (e) {
                        console.log(e);
                        swal({
                            icon: 'error',
                            text: 'upload error'
                        });
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

        function uploadAjax(file, date, type) {
            let data = new FormData();

            data.append('file', file);
            data.append('inline_report_date', date);
            data.append('inline_fee_type', type);

            $.ajax({
                url: window.location.origin + '/fee/upload/file',
                type: 'post',
                processData: false,
                contentType: false,
                data: data,
                success: function () {
                    //如上傳成功則可再次上傳
                    $('#inline_submit').prop('disabled', false);
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

                    //如上傳失敗則可再次上傳
                    $('#inline_submit').prop('disabled', false);
                }
            });
        }
    </script>
@endpush

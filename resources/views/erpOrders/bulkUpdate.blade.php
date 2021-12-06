@extends('layouts.app', [
    'parentSection' => 'erp-orders',
    'elementName' => 'bulk-upload'
])

@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('ERP ORDERS') }}
            @endslot

            <li class="breadcrumb-item"><a href="{{ route('erpOrder.view') }}">{{ __('ERP ORDERS') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('BULK UPLOAD') }}</li>
        @endcomponent
    @endcomponent

    <div class="wrapper wrapper-content">
        <!-- Table -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                <form method="GET" action="/fee/upload" role="form" class="form">
                    <div class="row">

                        {{-- Upload Date --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="upload_date">Upload Date</label>
                                <input class="form-control _fz-1" name="upload_date" id="upload_date"
                                       placeholder="Upload Date" type="text">
                            </div>
                        </div>

                        {{-- Order ID --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="order_id">Order ID</label>
                                <input class="form-control _fz-1" name="order_id" id="order_id"
                                       placeholder="Order ID" type="text">
                            </div>
                        </div>

                        {{-- Execution Status --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="execution_status">Execution Status</label>
                                <select class="form-control _fz-1" data-toggle="select" name="status_type"
                                        id="execution_status">
                                    <option value="">all</option>
                                    <option value="completed">Completed</option>
                                    <option value="Pending">Pending</option>
                                    <option value="failed">Error</option>
                                </select>
                            </div>
                        </div>

                        {{-- Report Date --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="report_date">Report Date</label>
                                <input class="form-control _fz-1" name="report_date" id="report_date"
                                       placeholder="report date" type="text">
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
                                        <span class="_fz-1">Bulk Upload</span>
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
                        <th>Batch ID</th>
                        <th>Order ID</th>
                        <th>Sku</th>
                        <th>Execution Status</th>
                        <th>Exit Message Sku</th>
                        <th>Created At</th>
                        <th>Created By</th>
                    </tr>
                    </thead>
                    <tbody>
                    {{--                    @foreach ($lists as $item)--}}
                    {{--                        <tr>--}}
                    {{--                            <td>{{ $item->created_at }}</td>--}}
                    {{--                            <td>{{ $item->user_name }}</td>--}}
                    {{--                            <td>{{ $item->report_date }}</td>--}}
                    {{--                            <td>{{ $item->fee_type }}</td>--}}
                    {{--                            <td>{{ $item->file_name }}</td>--}}
                    {{--                            <td>{{ $item->status }}</td>--}}
                    {{--                            <td>{{ $item->user_error_msg }}</td>--}}
                    {{--                        </tr>--}}
                    {{--                    @endforeach--}}
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

            <!-- Data Import - Bulk Update -->
            <form enctype="multipart/form-data">
                <div class="row">
                    <div class="col-4 form-group">
                        <label class="form-control-label">Bulk Update - ERP Orders</label>
                    </div>
                    <div class="col-8 form-group">
                        <div class="input-group-btn">
                            <button class="btn btn-success text-white" id="downloadSampleFile">Sample File</button>
                        </div>
                    </div>
                </div>

                <!-- FILE INPUT-->
                <div class="row">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">File Input</span>
                        </div>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="inputGroupFile01">
                            <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
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

            // $("#upload_btn").colorbox({inline: true, width: "40%", height: "50%", closeButton: true});
            $("#upload_btn").colorbox({inline: true, width: "40%", height: "40%", closeButton: true});

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
                window.location.href = origin + '/orders/exportSample';
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
                    console.log('success');
                }
            });
        }
    </script>
@endpush

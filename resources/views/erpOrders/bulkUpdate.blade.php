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
                <form method="GET" action="{{ route('bulkUpdate.view') }}" role="form" class="form">
                    <div class="row">

                        {{-- Upload Date --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="upload_date">Upload Date</label>
                                <input class="form-control _fz-1" name="upload_date" id="upload_date"
                                       placeholder="Upload Date" value="{{ Request()->get('upload_date') }}"
                                       type="text">
                            </div>
                        </div>

                        {{-- Order ID --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="order_id">Order ID</label>
                                <input class="form-control _fz-1" name="order_id" id="order_id"
                                       placeholder="Order ID" value="{{ Request()->get('order_id') }}" type="text">
                            </div>
                        </div>

                        {{-- Execution Status --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="execution_status">Execution Status</label>
                                <select class="form-control _fz-1" data-toggle="select" name="status_type"
                                        id="execution_status">
                                    <option value="">all</option>
                                    <option value="SUCCESS" @if(Request()->get('status_type') == 'SUCCESS')
                                        {{ 'selected' }} @endif>Success
                                    </option>
                                    <option value="PENDING" @if(Request()->get('status_type') == 'PENDING')
                                        {{ 'selected' }} @endif>
                                        Pending
                                    </option>
                                    <option value="FAILURE" @if(Request()->get('status_type') == 'FAILURE')
                                        {{ 'selected' }} @endif>
                                        Failure
                                    </option>
                                </select>
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
                        <th>Exit Message</th>
                        <th>Created At</th>
                        <th>Created By</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($lists as $item)
                        <tr>
                            <td>{{ $item->batch_job_id }}</td>
                            <td>{{ $item->platform_order_id }}</td>
                            <td>{{ $item->product_sku }}</td>
                            <td>{{ $item->execution_status }}</td>
                            <td>{{ $item->exit_message }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->setTimezone(config('services.timezone.taipei'))}}</td>
                            <td>{{ $item->user_name }}</td>
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

    {{-- colorbox html part start --}}
    <div style='display:none'>
        <div class="container" id='inline_content'>

            {{-- Data Import - Bulk Update --}}
            <form enctype="multipart/form-data">
                <div class="row">
                    <div class="col-4 form-group">
                        <label class="form-control-label">Bulk Update - ERP Orders</label>
                    </div>
                    <div class="col-8 form-group">
                        <div class="input-group-btn">
                            <a class="btn btn-success text-white" href="{{ route('orders.sample.download') }}" download>
                                Sample File</a>
                        </div>
                    </div>
                </div>

                {{-- FILE INPUT --}}
                <div class="row">
                    <div class="col-3 form-group">
                        <label class="form-control-label">File Input</label>
                    </div>

                    <div class="col-8 form-group">
                        <div class="dropzone dropzone-single mb-3" data-toggle="dropzone">
                            <div class="fallback">
                                <div class="custom-file">
                                    <input type="file" name="file" class="form-control" id="bulkUploadFile" required>
                                    <div class="required">Maximum size: 5 MB</div>
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
    {{-- colorbox html part end --}}
@endsection

@push('js')
    <script type="text/javascript">
        $(function () {
            $('#upload_date').datepicker({
                format: 'yyyy-mm-dd',//日期時間格式
                ignoreReadonly: true, //禁止使用者輸入 啟用唯讀
                autoclose: true
            });

            $("#upload_btn").colorbox({inline: true, width: "40%", height: "50%", closeButton: true});

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

                if ($.trim($("#bulkUploadFile").val()) === '') {
                    swal({
                        icon: "error",
                        text: "file can't be empty"
                    });
                    return false;
                }

                let data = new FormData();
                let file = $('#bulkUploadFile')[0].files[0];
                let fileType = file.name.slice((file.name.lastIndexOf(".") - 1 >>> 0) + 2);

                data.append('file', file);

                //檢查副檔名
                if (fileType !== 'xlsx') {
                    swal({
                        icon: "error",
                        text: "wrong file type"
                    });
                    return false;
                }

                $('#inline_submit').prop('disabled', true)

                swal({
                    icon: 'success',
                    text: 'Processing'
                }).then(function (isConfirm) {
                    if (isConfirm) {
                        $.colorbox.close();
                    }
                });

                $.ajax({
                    url: window.location.origin + '/orders/bulkUpdate',
                    type: 'post',
                    processData: false,
                    contentType: false,
                    data: data,
                    success: function () {
                        $('#inline_submit').prop('disabled', false)
                    }, error: function (error) {
                        swal({
                            icon: 'error',
                            text: error
                        });
                        $('#inline_submit').prop('disabled', false)

                    }
                });
            });

            //check file size
            $('input[type=file]').change(e => {
                if (e.currentTarget.files.length > 0) {
                    if ((e.currentTarget.files[0].size / 1024 / 1024) > 5) {
                        $('#inline_submit').prop('disabled', true)
                    } else {
                        $('#inline_submit').prop('disabled', false)
                    }
                }
            })

        });
    </script>
@endpush

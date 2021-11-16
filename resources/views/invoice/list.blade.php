@extends('layouts.app', [
    'parentSection' => 'INVOICE',
    'elementName' => 'LIST'
])
@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('INVOICE') }}
            @endslot
            <li class="breadcrumb-item"><a href="{{ route('invoice.list.view') }}">{{ __('INVOICE') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('LIST') }}</li>
        @endcomponent
    @endcomponent

    @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    @endif
    <div class="wrapper wrapper-content">
        <!-- Table -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                <div>
                    <form method="GET" action="/invoice/list" role="form" class="form">
                        <div class="row">
                            {{-- CLIENT CODE --}}
                            <div class="col-lg-2 col-md-6 col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="form-control-label _fz-1" for="client_code">Client Code</label>
                                    <select class="form-control _fz-1" data-toggle="select" name="client_code"
                                            id="client_code">
                                        @forelse ($client_code_lists as $item)
                                            <option value="{{$item}}" @if($clientCode == $item) {{ 'selected' }} @endif>
                                                {{$item}}</option>
                                        @empty
                                            <option value="">{{'NONE'}}</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>

                            {{-- REPORT DATE --}}
                            <div class="col-lg-2 col-md-6 col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="form-control-label _fz-1" for="erp_order_id">Report Date</label>
                                    <input class="form-control _fz-1" name="report_date" id="report_date"
                                            type="text" placeholder="Report Date"
                                            value="{{$reportDate ?? ''}}">
                                </div>
                            </div>

                            {{-- STATUS --}}
                            <div class="col-lg-2 col-md-6 col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="form-control-label _fz-1" for="status">Status</label>
                                    <select class="form-control _fz-1" data-toggle="select" name="status"
                                            id="status">
                                        <option value="">all</option>
                                        <option value="processing" @if($status == 'processing') {{ 'selected' }} @endif>
                                            processing
                                        </option>
                                        <option value="deleted" @if($status == 'deleted') {{ 'selected' }} @endif>
                                            deleted
                                        </option>
                                        <option value="active" @if($status == 'active') {{ 'selected' }} @endif>
                                            active
                                        </option>
                                    </select>
                                </div>
                            </div>

                            {{-- FIND --}}
                            <div class="col-lg-2 col-md-6 col-sm-6">
                                <label class="form-control-label _fz-1" for="submit_btn"></label>
                                <div class="form-group mb-0">
                                    <button class="form-control _fz-1 btn btn-primary" id="submit_btn" type="submit"
                                            style="margin-top: 6px;">Find
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            {{-- data table --}}
            <div class="table-responsive">
                <table class="table table-sm _table">
                    <thead class="thead-light">
                    <tr>
                        <th>Client Code</th>
                        <th>Report Date</th>
                        <th>Invoice No.</th>
                        <th>File Name</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse ($lists as $item)
                        <td class="platform">{{ $item->client_code ?? '' }}</td>
                        <td class="acc_nick_name">{{ $item->report_date ?? '' }}</td>
                        <td class="acc_name">{{ $item->opex_invoice_no ?? '' }}</td>
                        <td class="file_name">{{ $item->doc_file_name ?? '' }}</td>
                        <td class="shipped_date">{{ $item->doc_status ?? '' }}</td>
                        <td class="package_id">{{ $item->created_at ?? '' }}</td>
                        <td>
                            <button class="btn btn-icon btn-primary download_file btn-sm _fz-1"
                                    type="button" @if($item->doc_status !="active") {{ 'disabled' }} @endif>
                                <span class="btn-inner--icon"><i class="ni ni-cloud-download-95"></i></span>
                                <span class="btn-inner--text">Download</span>
                            </button>
                            <button class="btn btn-danger delete_btn btn-sm _fz-1" type="button"
                                    data-id="{{$item->id}}">
                                <span class="btn-inner--text">Delete</span>
                            </button>
                        </td>

                        <input name="file_token" type="hidden" value="{{$item->doc_storage_token ?? ''}}">
                        </tr>
                    @empty
                    @endforelse
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
@endsection

@push('js')
    <script src="{{ asset('argon') }}/vendor/select2/dist/js/select2.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <script type="text/javascript">
        $(function () {
            let reportDate = $('#report_date').val();

            $('#report_date').datepicker({
                format: 'yyyy-mm',//日期時間格式
                viewMode: "months",
                minViewMode: "months",
                ignoreReadonly: true,  //禁止使用者輸入 啟用唯讀
                autoclose: true
            });

            $('button.download_file').click(function () {
                // let inlineSelectType = $('#inline_select_fee_type :selected').val();
                // window.location(url);
                let token = $(this).parent().parent().find('input[name="file_token"]').val();

                window.location.href = origin + '/invoice/download/' + token;
            });

            $('button.delete_btn').click(function () {
                let invoiceID = $(this).data("id");

                swal({
                    title: "Are you sure?",
                    text: ("DELETE"),
                    icon: 'warning',
                    buttons: true,
                    buttons: ["No,Cancel", "Yes,Delete it!"]
                })
                    .then(function (isConfirm) {
                        if (isConfirm) {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });

                            $.ajax({
                                url: origin + '/invoice/' + invoiceID,
                                type: 'delete',
                                success: function (res) {
                                    swal({
                                        icon: res.icon,
                                        text: res.msg
                                    });
                                }, error: function (error) {
                                    swal({
                                        icon: 'error',
                                        text: error
                                    });
                                }
                            });
                        }
                    });
            });
        });
    </script>
@endpush

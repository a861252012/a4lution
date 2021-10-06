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
            <li class="breadcrumb-item"><a href="{{ route('page.index', 'components') }}">{{ __('INVOICE') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('LIST') }}</li>
        @endcomponent
    @endcomponent

    {{--@section('content')--}}
    {{--    @include('forms.header')--}}

    {{--    <div class="container-fluid mt--6">--}}
    @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    @endif
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
                            <form method="GET" action="/invoice/list" role="form" class="form">
                                <div class="row">
                                    {{-- CLIENT CODE --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="client_code">CLIENT CODE</label>
                                            <select class="form-control" data-toggle="select" name="client_code"
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
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="erp_order_id">REPORT DATE</label>
                                            <input class="form-control" name="report_date" id="report_date"
                                                   type="text" placeholder="REPORT DATE"
                                                   value="{{$reportDate ?? ''}}">
                                        </div>
                                    </div>

                                    {{-- STATUS --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="status">STATUS</label>
                                            <select class="form-control" data-toggle="select" name="status"
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
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <label class="form-control-label" for="submit_btn"></label>
                                        <div class="form-group">
                                            <button class="form-control btn btn-primary" id="submit_btn" type="submit"
                                                    style="margin-top: 6px;">FIND
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
                                <th>CLIENT CODE</th>
                                <th>REPORT DATE</th>
                                <th>INVOICE NO.</th>
                                <th>FILE NAME</th>
                                <th>STATUS</th>
                                <th>CREATED DATE</th>
                                <th>ACTION</th>
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
                                {{--                                <td>--}}
                                {{--                                    <button class="btn btn-icon btn-primary download_file"--}}
                                {{--                                            type="button" @if($item->doc_status !="active") {{ 'disabled' }} @endif>--}}
                                {{--                                        <span class="btn-inner--icon"><i class="ni ni-cloud-download-95"></i></span>--}}
                                {{--                                        <span class="btn-inner--text">DOWNLOAD</span>--}}
                                {{--                                    </button>--}}
                                {{--                                </td>--}}

                                <td>
                                    {{--                                    <button class="btn btn-icon btn-primary issue_btn btn-sm download_file"--}}
                                    {{--                                            type="button" @if($item->doc_status !="active") {{ 'disabled' }} @endif>--}}
                                    {{--                                        <span class="btn-inner--text">DOWNLOAD</span>--}}
                                    {{--                                    </button>--}}
                                    <button class="btn btn-icon btn-primary download_file btn-sm"
                                            type="button" @if($item->doc_status !="active") {{ 'disabled' }} @endif>
                                        <span class="btn-inner--icon"><i class="ni ni-cloud-download-95"></i></span>
                                        <span class="btn-inner--text">DOWNLOAD</span>
                                    </button>
                                    <button class="btn btn-danger delete_btn btn-sm" type="button"
                                            data-id="{{$item->id}}">
                                        <span class="btn-inner--text">DELETE</span>
                                    </button>
                                </td>

                                <input name="file_token" type="hidden" value="{{$item->doc_storage_token ?? ''}}">
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-center">
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

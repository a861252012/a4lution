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
                    <form method="GET" action="{{ route('invoice.list.view') }}" role="form" class="form">
                        <div class="row">
                            {{-- CLIENT CODE --}}
                            <div class="col-lg-2 col-md-6 col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="form-control-label _fz-1" for="client_code">Client Code</label>
                                    <select class="form-control _fz-1" name="client_code" id="client_code">
                                        <option value={{''}} @if(request('client_code') === 'all')
                                            {{ 'selected' }} @endif>{{'all'}}</option>
                                        @forelse ($clientCodeList as $item)
                                            <option value="{{$item}}" @if(request('client_code') == $item)
                                                {{ 'selected' }} @endif>{{$item}}</option>
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
                                           type="text" placeholder="Report Date" value="{{ request('report_date') }}">
                                </div>
                            </div>

                            {{-- STATUS --}}
                            <div class="col-lg-2 col-md-6 col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="form-control-label _fz-1" for="status">Status</label>
                                    <select class="form-control _fz-1" data-toggle="select" name="status" id="status">
                                        <option value="">all</option>
                                        <option value="processing" @if(request('status') === 'processing')
                                            {{ 'selected' }} @endif>processing
                                        </option>
                                        <option value="deleted" @if(request('status') === 'deleted') {{ 'selected' }}
                                                @endif>deleted
                                        </option>
                                        <option value="active" @if(request('status') === 'active')
                                            {{ 'selected' }} @endif>active
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
                        <td class="acc_nick_name">{{ $item->report_date->format('F-Y') ?? '' }}</td>
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
    <script type="text/javascript">
        $(function () {
            $('#report_date').datepicker({
                format: 'yyyy-mm',//??????????????????
                viewMode: "months",
                minViewMode: "months",
                ignoreReadonly: true,  //????????????????????? ????????????
                autoclose: true
            });

            $('button.download_file').click(function () {
                let token = $(this).parent().parent().find('input[name="file_token"]').val();

                window.location.href = origin + '/invoice/download/' + token;
            });

            $('button.delete_btn').click(function () {
                let invoiceID = $(this).data("id");

                swal({
                    title: "Are you sure?",
                    text: ("DELETE"),
                    icon: 'warning',
                    buttons: ["No,Cancel", "Yes,Delete it!"]
                }).then(function (isConfirm) {
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
                            }, error: function (e) {
                                let errors = [];
                                $.each(JSON.parse(e.responseText).errors, function (col, msg) {
                                    errors.push(msg.toString());
                                });

                                swal({
                                    icon: 'error',
                                    text: errors.join("\n")
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush

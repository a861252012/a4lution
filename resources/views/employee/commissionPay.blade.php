@extends('layouts.app', [
    'parentSection' => 'employee',
    'elementName' => 'COMMISSION-PAY'
])

@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('EMPLOYEE') }}
            @endslot
            <li class="breadcrumb-item"><a href="{{ route('page.index', 'components') }}">{{ __('EMPLOYEE') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('COMMISSION PAY') }}</li>
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
                        <div>
                            <form method="GET" action="/employee/commissionpay" role="form" class="form">
                                <div class="row">
                                    {{-- USER NAME --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="user_name">USER NAME</label>
                                            <input class="form-control" name="user_name" id="user_name"
                                                   type="text" placeholder="USER NAME"
                                                   value="{{$user_name ?? ''}}">
                                        </div>
                                    </div>

                                    {{-- CLIENT CODE --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="client_code">CLIENT CODE</label>
                                            <input class="form-control" name="client_code" id="client_code"
                                                   type="text" placeholder="CLIENT CODE"
                                                   value="{{$client_code ?? ''}}">
                                        </div>
                                    </div>

                                    {{-- REPORT DATE --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="report_date">REPORT DATE</label>
                                            <input class="form-control" name="report_date" id="report_date"
                                                   type="text" placeholder="REPORT DATE"
                                                   value="{{$report_date ?? ''}}">
                                        </div>
                                    </div>

                                    {{-- SEARCH --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <label class="form-control-label" for="submit_btn"></label>
                                        <div class="form-group">
                                            <button class="form-control btn btn-primary" id="submit_btn" type="submit"
                                                    style="margin-top: 6px;">SEARCH
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- data table --}}
                    <div class="table-responsive py-4">
                        <table class="table table-flush" id="commission_detail">
                            <thead class="thead-light">
                            <tr>
                                <th></th>
                                <th>USER NAMES</th>
                                <th>ROLE</th>
                                <th>COMPANY</th>
                                <th>REGIONS</th>
                                <th>CURRENCY</th>
                                <th>CUSTOMER QTY</th>
                                <th>BILLED QTY</th>
                                <th>REPORT DATE</th>
                                <th>TOTAL BILLED COMMISSION</th>
                                <th>EXTRA MONTHLY FEE</th>
                                <th>EXTRA OPS COMM</th>
                                <th>TOTAL COMPENSATION</th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse ($lists as $item)
                                <tr>
                                    <td></td>
                                    <td data-attr="user_name">{{ $item->user_name }}</td>
                                    <td data-attr="role_name">{{ $item->role_name }}</td>
                                    <td data-attr="company_type">{{ $item->company_type }}</td>
                                    <td data-attr="region">{{ $item->region }}</td>
                                    <td data-attr="currency">{{ $item->currency }}</td>
                                    <td data-attr="customer_qty">{{ $item->customer_qty }}</td>
                                    <td data-attr="billed_qty">{{ $item->billed_qty }}</td>
                                    <td data-attr="report_date">{{ $item->report_date }}</td>
                                    <td data-attr="total_billed_commissions_amount">
                                        {{ $item->total_billed_commissions_amount }}
                                    </td>
                                    <td data-attr="extra_monthly_fee_amount">{{ $item->extra_monthly_fee }}</td>
                                    <td data-attr="extra_ops_commission_amount">{{ $item->extra_ops_commission }}</td>
                                    <td data-attr="total_compensation">{{ $item->total_employee_sharing }}</td>
                                    <input type="hidden" name="user_id" value="{{ $item->id }}">
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
    </div>
@endsection

@push('js')
    <script src="{{ asset('argon') }}/vendor/select2/dist/js/select2.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <script type="text/javascript">
        function format(d) {
            let html = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
                '<thead>' +
                '<tr>' +
                '<th>Customer</th>' +
                '<th>Contract (Month)</th>' +
                '<th>Computed Date</th>' +
                '<th>Billed Commissions</th>' +
                '<th>Monthly Fee</th>' +
                '<th>Cross Sales</th>' +
                '<th>Ops Commission</th>' +
                '</tr>' +
                '</thead>' +
                '<tbody>';

            d.data.forEach((item, index) => {
                html += '<tr>' +
                    '<td>' + item.client_code + '</td>' +
                    '<td>' + item.contract_length + '</td>' +
                    '<td>' + item.created_at + '</td>' +
                    '<td>' + item.avolution_commission + '</td>' +
                    '<td>' + item.monthly_fee + '</td>' +
                    '<td>' + item.cross_sales + '</td>' +
                    '<td>' + item.ops_commission + '</td>' +
                    '</tr>'
            })

            html += '</tbody>' +
                '</table>';

            return html;
        }

        $(function () {
            $(document).ready(function () {
                $('#report_date').datepicker({
                    format: 'yyyy-mm',//日期時間格式
                    viewMode: "months",
                    minViewMode: "months",
                    ignoreReadonly: false,  //禁止使用者輸入 啟用唯讀
                    autoclose: true
                });

                let dt = $('#commission_detail').DataTable({
                    // 參數設定[註1]
                    "bPaginate": false, // 顯示換頁
                    "searching": false, // 顯示搜尋
                    "info": false, // 顯示資訊
                    "fixedHeader": false, // 標題置頂
                    "columns": [
                        {
                            "class": "details-control",
                            "orderable": false,
                            "data": null,
                            "defaultContent": ""
                        },
                        {"data": "USER NAMES"},
                        {"data": "ROLE"},
                        {"data": "COMPANY"},
                        {"data": "REGIONS"},
                        {"data": "CURRENCY"},
                        {"data": "CUSTOMER QTY"},
                        {"data": "BILLED QTY"},
                        {"data": "REPORT DATE"},
                        {"data": "TOTAL BILLED COMMISSION"},
                        {"data": "EXTRA MONTHLY FEE"},
                        {"data": "EXTRA OPS COMM"},
                        {"data": "TOTAL COMPENSATION"}
                    ]
                });

                // Array to track the ids of the details displayed rows
                let detailRows = [];

                $('#commission_detail tbody').on('click', 'tr td.details-control', function () {
                    let tr = $(this).parents('tr');

                    let row = dt.row(tr);
                    let idx = $.inArray(tr.attr('id'), detailRows);
                    let date = $(this).parent().find('[data-attr="report_date"]').text();
                    let userID = $(this).parent().find('input[name="user_id"]').val();

                    if (row.child.isShown()) {
                        tr.removeClass('details');
                        row.child.hide();

                        // Remove from the 'open' array
                        detailRows.splice(idx, 1);
                    } else {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        //call api to check if monthly report exist
                        $.ajax({
                            url: origin + '/employee/commissionpay/detail/' + userID + '/' + date,
                            type: 'get',
                            async: false,
                            success: function (res) {
                                tr.addClass('details');
                                row.child(format(res)).show();
                            }
                        });

                        // Add to the 'open' array
                        if (idx === -1) {
                            detailRows.push(tr.attr('id'));
                        }
                    }
                });

                // On each draw, loop over the `detailRows` array and show any child rows
                dt.on('draw', function () {
                    $.each(detailRows, function (i, id) {
                        $('#' + id + ' td:details-control').trigger('click');
                    });
                });

            });
        });
    </script>
@endpush

@push('css')
    <style>
        td.details-control {
            background: url("{{ asset('pictures') }}/details_open.png") no-repeat center center;
            cursor: pointer;
        }

        tr.details td.details-control {
            background: url("{{ asset('pictures') }}/details_close.png") no-repeat center center;
        }
    </style>
@endpush

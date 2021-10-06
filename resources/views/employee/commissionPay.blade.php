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
                        {{--                        <h3 class="mb-0">Datatable</h3>--}}
                        {{--                        <div class="row input-daterange datepicker align-items-center">--}}
                        <div>
                            <form method="GET" action="/employee/commissionpay" role="form" class="form">
                                <div class="row">
                                    {{-- USER NAME --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="user_name">USER NAME</label>
                                            <input class="form-control" name="user_name" id="user_name"
                                                   type="text" placeholder="USER NAME"
                                                   value="{{$data['user_name'] ?? ''}}">
                                        </div>
                                    </div>

                                    {{-- CLIENT CODE --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="client_code">CLIENT CODE</label>
                                            <input class="form-control" name="client_code" id="client_code"
                                                   type="text" placeholder="CLIENT CODE"
                                                   value="{{$data['client_code'] ?? ''}}">
                                        </div>
                                    </div>

                                    {{-- REPORT DATE --}}
                                    <div class="col-2 col-lg-2  col-sm-2">
                                        <div class="form-group">
                                            <label class="form-control-label" for="report_date">REPORT DATE</label>
                                            <input class="form-control" name="report_date" id="report_date"
                                                   type="text" placeholder="REPORT DATE"
                                                   value="{{$data['report_date'] ?? ''}}">
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
                                    <td data-attr="user_name">{{ $item->user_name }}</td>
                                    <td data-attr="role_name">{{ $item->role_name }}</td>
                                    <td data-attr="company_type">{{ $item->company_type }}</td>
                                    <td data-attr="region">{{ $item->region }}</td>
                                    <td data-attr="currency">{{ $item->currency }}</td>
                                    <td data-attr="customer_qty">{{ $item->customer_qty }}</td>
                                    <td data-attr="billed_qty">{{ '-' }}</td>
                                    <td data-attr="report_date">{{ $item->report_date }}</td>
                                    <td data-attr="total_billed_commissions_amount">{{ $item->total_billed_commissions_amount }}</td>
                                    <td data-attr="extra_monthly_fee_amount">{{ $item->extra_monthly_fee_amount }}</td>
                                    <td data-attr="extra_ops_commission_amount">{{ $item->extra_ops_commission_amount }}</td>
                                    <td data-attr="total_compensation">{{ $item->total_compensation }}</td>
                                    <input type="hidden" name="user_id" value="{{ $item->id }}">
                                </tr>

                                {{--                                <tr>--}}
                                {{--                                    <td>{{ $item->user_name }}</td>--}}
                                {{--                                    <td>{{ $item->role_name }}</td>--}}
                                {{--                                    <td>{{ $item->company_type }}</td>--}}
                                {{--                                    <td>{{ $item->region }}</td>--}}
                                {{--                                    <td>{{ $item->currency }}</td>--}}
                                {{--                                    <td>{{ $item->customer_qty }}</td>--}}
                                {{--                                    <td>{{ '-' }}</td>--}}
                                {{--                                    <td>{{ $item->report_date }}</td>--}}
                                {{--                                    <td>{{ $item->total_billed_commissions_amount }}</td>--}}
                                {{--                                    <td>{{ $item->extra_monthly_fee_amount }}</td>--}}
                                {{--                                    <td>{{ $item->extra_ops_commission_amount }}</td>--}}
                                {{--                                    <td>{{ $item->total_compensation }}</td>--}}
                                {{--                                </tr>--}}
                            @empty
                            @endforelse
                            </tbody>
                            {{--                            <tbody>--}}
                            {{--                            @foreach ($data['lists'] as $item)--}}
                            {{--                                <tr>--}}
                            {{--                                    --}}{{-- edit --}}
                            {{--                                    <td>--}}
                            {{--                                        <a class="ajax_btn form-control btn">--}}
                            {{--                                            <div>--}}
                            {{--                                                <i class="ni ni-settings"></i>--}}
                            {{--                                            </div>--}}
                            {{--                                        </a>--}}
                            {{--                                    </td>--}}
                            {{--                                    <td class="platform">{{ $item->platform }}</td>--}}
                            {{--                                    <td class="acc_nick_name">{{ $item->acc_nick_name }}</td>--}}
                            {{--                                    <td class="acc_name">{{ $item->acc_name }}</td>--}}
                            {{--                                    <td class="site_id">{{ $item->site_id }}</td>--}}
                            {{--                                    <td class="shipped_date">{{ $item->shipped_date }}</td>--}}
                            {{--                                    <td class="package_id">{{ $item->package_id }}</td>--}}
                            {{--                                    <td class="erp_order_id">{{ $item->erp_order_id }}</td>--}}
                            {{--                                    <td class="sku">{{ $item->sku }}</td>--}}
                            {{--                                    <td class="order_price">{{ $item->order_price }}</td>--}}
                            {{--                                    <td class="supplier">{{ $item->supplier }}</td>--}}
                            {{--                                    <td class="warehouse">{{ $item->warehouse }}</td>--}}
                            {{--                                    <input class="hidden" type="hidden" value="{{$item->currency_code_org}}">--}}
                            {{--                                </tr>--}}
                            {{--                            @endforeach--}}
                            {{--                            </tbody>--}}
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
                '<th>Completed At</th>' +
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
                    '<td> billed commission </td>' +
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
                let dt = $('#commission_detail').DataTable({
                    // 參數設定[註1]
                    "bPaginate": false, // 顯示換頁
                    "searching": false, // 顯示搜尋
                    "info": false, // 顯示資訊
                    "fixedHeader": false, // 標題置頂
                });

                // Array to track the ids of the details displayed rows
                let detailRows = [];

                $('#commission_detail tbody').on('click', 'tr td:first-child', function () {
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
                        console.log('no ajax');
                    } else {
                        console.log('ajax');

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
                                console.log(res)
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
                        $('#' + id + ' td:first-child').trigger('click');
                    });
                });

            });
        });

        // function validateForm() {
        //     let accNickName = $('#acc_nick_name').val();
        //     let erpOrderId = $('#erp_order_id').val();
        //     let shippedDate = $('#shipped_date').val();
        //     let packageID = $('#package_id').val();
        //     let sku = $('#sku').val();
        //     if (!accNickName && !erpOrderId && !shippedDate && !packageID && !sku) {
        //         swal({
        //             icon: "warning",
        //             text: "must have at least one search condition"
        //         });
        //         return false;
        //     }
        // }
    </script>
@endpush

@push('css')
    <style>
        /*input[type=text] {*/
        /*    background: transparent;*/
        /*    border: none;*/
        /*    border-bottom: 1px solid #000000;*/
        /*}*/
    </style>
@endpush

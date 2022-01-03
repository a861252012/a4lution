@extends('layouts.app', [
    'parentSection' => 'erp-orders',
    'elementName' => 'ORDER-SEARCH'
])

@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('ERP ORDERS') }}
            @endslot
            <li class="breadcrumb-item"><a href="{{ route('monthlyFee.view') }}">{{ __('SYSTEM SETTING') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('MONTHLY FEE') }}</li>
        @endcomponent
    @endcomponent

    <div class="wrapper wrapper-content">
        <!-- Table -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                <form method="GET" action="{{route(('monthlyFee.view'))}}" role="form">
                    <div class="row">

                        {{-- Client Code --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="client_code">Client Code</label>
                                <input class="form-control _fz-1" name="client_code" id="client_code"
                                       type="text" placeholder="Client Code"
                                       value="{{ request('client_code') }}">
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="select_status">Active</label>
                                <select class="form-control _fz-1" data-toggle="select" name="status_type"
                                        id="select_status">
                                    <option value="all">all</option>
                                    <option value="1" @if(request('status_type') === '1'){{ 'selected' }}@endif>Active
                                    </option>
                                    <option value="0" @if(request('status_type') === '0'){{ 'selected' }}@endif>Inactive
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- SEARCH --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <label class="form-control-label _fz-1" for="submit_btn"></label>
                            <div class="form-group mb-0">
                                <button class="form-control _fz-1 btn btn-primary" id="submit_btn" type="submit"
                                        style="margin-top: 6px;">SEARCH
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            {{-- data table --}}
            <div class="table-responsive">
                <table class="table table-sm _table" id="monthly_fee_table">
                    <thead class="thead-light">
                    <tr>
                        <th></th>
                        <th>Client Code</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Contract Date</th>
                        <th>Last Updated</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse ($lists as $item)
                        <tr>
                            <td></td>
                            <td data-attr="client_code">{{ $item->client_code }}</td>
                            <td>{{ $item->company_name }}</td>
                            <td>{!! ($item->active) ? "<strong>Active</strong>" : 'Inactive' !!}</td>
                            <td>{{ $item->contract_date }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->updated_at)
                                                       ->setTimezone(config('services.timezone.taipei')) }}</td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                @if($lists && $lists->lastPage() > 1)
                    <div class="d-flex justify-content-center" style='margin-top: 20px;'>
                        {{ $data['lists']->appends($_GET)->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(function () {
            //initialize DataTable plugin
            let dt = $('#monthly_fee_table').DataTable({
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
                    {"data": "Client Code"},
                    {"data": "Name"},
                    {"data": "Status"},
                    {"data": "Contract Date"},
                    {"data": "Last Updated"}
                ]
            });

            // Array to track the ids of the details displayed rows
            let detailRows = [];

            $('#monthly_fee_table tbody').on('click', 'tr td.details-control', function () {
                let tr = $(this).parents('tr');

                let row = dt.row(tr);
                let idx = $.inArray(tr.attr('id'), detailRows);
                let clientCode = $(this).parent().find('[data-attr="client_code"]').text();

                console.log(clientCode);
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
                        url: origin + '/management/monthlyFee/ajax/employeeRule/' + clientCode,
                        type: 'get',
                        async: false,
                        success: function (res) {
                            console.log(res);
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

        function format(d) {
            let html = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
                '<thead>' +
                '<tr>' +
                '<th>Client Code</th>' +
                '<th>Role</th>' +
                '<th>Tiered</th>' +
                '<th>Base Rate</th>' +
                '<th>Threshold</th>' +
                '<th>Tier 1</th>' +
                '<th>Tier 2</th>' +
                '<th>Last Updated</th>' +
                '</tr>' +
                '</thead>' +
                '<tbody>';

            d.data.forEach((item, index) => {
                html += '<tr>' +
                    '<td>' + item.client_code + '</td>' +
                    '<td>' + item.roles.role_name + '</td>' +
                    '<td>' + item.is_tiered_rate + '</td>' +
                    '<td>First Year: ' + item.rate_base + '% <br/>' +
                    ' Renewal: ' + item.rate + '%</td>' +
                    '<td>' + item.threshold + '</td>' +
                    '<td>First Year: ' + item.tier_1_first_year + '% <br/>' +
                    ' Renewal: ' + item.tier_1_over_a_year + '%</td>' +
                    '<td>' + item.tier_2_first_year + '</td>' +
                    '<td>' + moment.tz(item.updated_at, "Asia/Taipei").format('YYYY-MM-DD HH:mm:ss'); + '</td>' +
                    '</tr>'
            })

            html += '</tbody>' +
                '</table>';

            return html;
        }
    </script>
@endpush
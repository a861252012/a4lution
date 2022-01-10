@extends('layouts.app')
@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('SETTINGS') }}
            @endslot
            <li class="breadcrumb-item"><a href="{{ route('sku_commission.index') }}">{{ __('SETTINGS') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('SKU COMMISSIONS') }}</li>
        @endcomponent
    @endcomponent

    <div class="wrapper wrapper-content">
        <!-- Card -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                <form id="searchForm" method="GET" action="{{ route('sku_commission.index') }}" role="form" class="form">
                    <div class="row">
                        {{-- CLIENT CODE --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="clientCode">Client Code</label>
                                <input class="form-control _fz-1" name="client_code" id="clientCode"
                                    type="text" placeholder="Client Code" value="{{ $query['client_code'] ?? '' }}">
                            </div>
                        </div>

                        {{-- SKU --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="sku">SKU</label>
                                <input class="form-control _fz-1" name="sku" id="sku"
                                    type="text" placeholder="SKU" value="{{ $query['sku'] ?? '' }}">
                            </div>
                        </div>

                        {{-- SEARCH --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <label class="form-control-label"></label>
                            <div class="form-group mb-0">
                                <button id="searchBtn" class="form-control _fz-1 btn _btn btn-primary"
                                    style="margin-top: 6px;">Search
                                </button>
                            </div>
                        </div>

                        {{-- UPLOAD --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <label class="form-control-label"></label>
                            <a id="uploadBtn">
                                <div class="form-control _fz-1 btn _btn btn-success text-white 
                                    mb-0 d-flex justify-content-center align-items-center"
                                    style="margin-top: 6px;">
                                    Upload SKU
                                </div>
                            </a>
                        </div>

                        {{-- EXPORT --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <label class="form-control-label"></label>
                            <div class="form-group mb-0">
                                <button class="form-control _fz-1 btn _btn btn-primary" 
                                    id="exportBtn" style="margin-top: 6px;">Export</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- data table --}}
            <div class="table-responsive">
                <table class="table table-sm _table">
                    <thead class="thead-light">
                    <tr>
                        <th>Client Code</th>
                        <th>Site</th>
                        <th>Currency</th>
                        <th>SKU</th>
                        <th>Threshold</th>
                        <th>Basic Rate</th>
                        <th>Higher Rate</th>
                        <th>Updated At</th>
                        <th>Updated By</th>
                    </tr>
                    </thead>

                    <tbody>
                        @forelse ($skuCommissions as $skuCommission)
                            <tr>
                                <td>{{ $skuCommission->client_code }}</td>
                                <td>{{ $skuCommission->site }}</td>
                                <td>{{ $skuCommission->currency }}</td>
                                <td>{{ $skuCommission->sku }}</td>
                                <td>{{ $skuCommission->threshold }}</td>
                                <td>{{ $skuCommission->basic_rate }}</td>
                                <td>{{ $skuCommission->upper_bound_rate }}</td>
                                <td>{{ optional($skuCommission->updated_at_tw)->format('Y-m-d') }}</td>
                                <td>{{ optional($skuCommission->updater)->user_name }}</td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($skuCommissions && $skuCommissions->lastPage() > 1)
                <div class="d-flex justify-content-center mt-1">
                    {{ $skuCommissions->appends($_GET)->links() }}
                </div>
            @endif
        </div>
        <!-- ./Card -->
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#uploadBtn').click(function () {

                $.colorbox({
                    iframe: false,
                    href: '{{ route('ajax.sku_commission.upload') }}',
                    width: '500px',
                    height: '300px',
                    returnFocus: false,
                    onComplete: function () {

                        $('#cancelBtn').click(function () {
                            $.colorbox.close();
                        });

                        // prepare Options Object
                        let options = {
                            url: '{{ route('ajax.sku_commission.upload.store') }}',
                            responseType: 'blob',
                            type: 'POST',
                            success: function (res) {
                                $.colorbox.close();
                                
                                let msg = "Created Success";

                                swal({
                                    icon: 'success',
                                    text: msg,
                                });

                                // location.reload();
                            },
                            error: function (e) {

                                let errors = [];
                                $.each(JSON.parse(e.responseText).errors, function(col, msg) {                    
                                    errors.push(msg.toString());
                                });

                                swal({
                                    icon: 'error',
                                    text: errors.join("\n")
                                });
                            }
                        };

                        // pass options to ajaxForm
                        $('#skuCommissionUploadForm').ajaxForm(options);
                    }
                    .bind(this)
                });
            });

            $('#searchBtn').click(function () {
                $('#searchForm').attr('action', '{{ route('sku_commission.index') }}');
                $('#searchForm').submit();
            });

            $('#exportBtn').click(function () {
                $('#searchForm').attr('action', '{{ route('sku_commission.export') }}');
                $('#searchForm').submit();
            });

        });

    </script>
@endpush

@push('css')
@endpush

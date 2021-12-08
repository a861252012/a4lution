@extends('layouts.app', [
    'parentSection' => 'INVOICE',
    'elementName' => 'ISSUE'
])
@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('SETTINGS') }}
            @endslot
            <li class="breadcrumb-item"><a href="{{ route('customer.index') }}">{{ __('SETTINGS') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('CUSTOMERS') }}</li>
        @endcomponent
    @endcomponent

    <div class="wrapper wrapper-content">
        <!-- Card -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                <input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}">

                <form method="GET" action="{{ route('customer.index') }}" role="form" class="form">
                    <div class="row">
                        {{-- CLIENT CODE --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="client_code">Client Code</label>
                                <input class="form-control _fz-1" name="client_code" id="client_code"
                                    type="text" placeholder="Client Code" value="{{ $clientCode ?? '' }}">

                                    {{-- type="text" placeholder="Client Code" value="{{ $clientCode ?? '' }}"> --}}
                            </div>
                        </div>

                        {{-- STATUS --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="active">Status</label>
                                <select class="form-control _fz-1" data-toggle="select" name="active" id="active">
                                    <option value="">All</option>
                                    {{-- <option value="1" @if($active) {{ 'selected' }} @endif> --}}
                                    <option value="1">
                                        Active
                                    </option>
                                    {{-- <option value="0" @if(!$active) {{ 'selected' }} @endif> --}}
                                    <option value="0">
                                        Inactive
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- Sales Region --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="sales_region">Sales Region</label>
                                <select class="form-control _fz-1" data-toggle="select" name="sales_region" id="sales_region">
                                    <option value="">All</option>
                                    {{-- <option value="hk" @if($sales_region == 'hk') {{ 'selected' }} @endif> --}}
                                    <option value="hk">
                                        HK
                                    </option>
                                    {{-- <option value="tw" @if($sales_region == 'tw') {{ 'selected' }} @endif> --}}
                                    <option value="tw">
                                        TW
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- SEARCH --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <label class="form-control-label" for="submit_btn"></label>
                            <div class="form-group mb-0">
                                <button class="form-control _fz-1 btn _btn btn-primary" id="submit_btn" type="submit"
                                        style="margin-top: 6px;">Search
                                </button>
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
                        <th>Contract Date</th>
                        <th>Status</th>
                        <th>Sales Region</th>
                        <th>Sales Rep</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    {{-- @forelse ($lists as $item)
                        <tr>
                            <input type="hidden" name="bill_state_id" value="{{ $item->id }}">
                            <td class="report_date">{{ $item->report_date ?? '' }}</td>
                            <td class="client_code">{{ $item->client_code ?? '' }}</td>
                            <td class="avolution_commission">{{ $item->avolution_commission ?? '' }}</td>
                            <td class="commission_type">{{ $item->commission_type ?? '' }}</td>
                            <td class="total_sales_orders">{{ $item->total_sales_orders ?? '' }}</td>
                            <td class="total_sales_amount">{{ $item->total_sales_amount ?? '' }}</td>
                            <td class="total_expenses">{{ $item->total_expenses ?? '' }}</td>
                            <td>
                                <button class="btn btn-primary issue_btn btn-sm _fz-1" type="button"
                                        billing-statement-id="{{ $item->id }}"
                                @if($item->commission_type === "manual") {{ 'disabled' }} @endif>
                                    <span class="btn-inner--text">Issue Invoice</span>
                                </button>
                                <button class="btn btn-danger delete_btn btn-sm _fz-1" type="button">
                                    <span class="btn-inner--text">Delete</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                    @endforelse --}}
                        <tr>
                            <td>1</td>
                            <td>2</td>
                            <td>3</td>
                            <td>4</td>
                            <td>5</td>
                            <td class="py-1">
                                <div class="dropdown">
                                    <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow" style="">
                                        <a class="dropdown-item" href="http://argon.test/user/2/edit">Edit</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>2</td>
                            <td>3</td>
                            <td>4</td>
                            <td>5</td>
                            <td class="py-1">
                                <div class="dropdown">
                                    <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow" style="">
                                        <a class="dropdown-item _edit-btn">Edit</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            {{-- @if($lists && $lists->lastPage() > 1)
                <div class="d-flex justify-content-center" style='margin-top: 20px;'>
                    {{ $lists->appends($_GET)->links() }}
                </div>
            @endif --}}
        </div>
        <!-- ./Card -->
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {

            $('._edit-btn').click(function () {

                let _token = $('meta[name="csrf-token"]').attr('content');

                $.colorbox({
                    iframe: false,
                    href: origin + '/customers/edit',
                    width: "70%",
                    height: "70%",
                    returnFocus: false,
                    data: {
                        _token: _token,
                    },
                    onComplete: function () {

                    },
                    onClosed:function(e){
                        // 隱藏 modal
                        $('#SetupCountry').modal('hide');
                        $(document.body).removeClass('modal-open');
                        $('.modal-backdrop').remove();
                        
                    }
                    .bind(this)
                });
            });
        });

    </script>
@endpush

@push('css')
@endpush

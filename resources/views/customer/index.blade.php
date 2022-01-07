@extends('layouts.app')
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
                <form method="GET" action="{{ route('customer.index') }}" role="form" class="form">
                    <div class="row">
                        {{-- CLIENT CODE --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="client_code">Client Code</label>
                                <input class="form-control _fz-1" name="client_code" id="client_code"
                                    type="text" placeholder="Client Code" value="{{ $query['client_code'] ?? '' }}">
                            </div>
                        </div>

                        {{-- STATUS --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="active">Status</label>
                                <select class="form-control _fz-1" data-toggle="select" name="active">
                                    <option value="">All</option>
                                    <option value="1" @if($query['active'] === '1') {{ 'selected' }} @endif>
                                        Active
                                    </option>
                                    <option value="0" @if($query['active'] === '0') {{ 'selected' }} @endif>
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
                                    <option value="HK" @if($query['sales_region'] === 'hk') {{ 'selected' }} @endif>
                                        HK
                                    </option>
                                    <option value="TW" @if($query['sales_region'] === 'tw') {{ 'selected' }} @endif>
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

                        {{-- Create --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <label class="form-control-label" for="create_btn"></label>
                            <a id="create_btn">
                                <div class="form-control _fz-1 btn _btn btn-primary text-white 
                                    mb-0 d-flex justify-content-center align-items-center"
                                    style="margin-top: 6px;">
                                    Create
                                </div>
                            </a>
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
                        <th>Account Service</th>
                        <th>Updated At</th>
                        <th>Updated By</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td>{{ $customer->client_code }}</td>
                                <td>{{ optional($customer->contract_date)->format('Y/m/d') }}</td>
                                <td>{{ $customer->active ? 'Active' : 'Inactive' }}</td>
                                <td>{{ $customer->sales_region }}</td>
                                <td>{{ $customer->salesReps->pluck('user_name')->implode(',') }}</td>
                                <td>{{ $customer->accountServices->pluck('user_name')->implode(',') }}</td>
                                <td>{{ $customer->updated_at_tw }}</td>
                                <td>{{ $customer->updater->user_name }}</td>
                                <td class="py-1">
                                    <div class="dropdown">
                                        <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow" style="">
                                            <a class="dropdown-item _edit-btn" client-code="{{ $customer->client_code }}">Edit</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($customers && $customers->lastPage() > 1)
                <div class="d-flex justify-content-center mt-1">
                    {{ $customers->appends($_GET)->links() }}
                </div>
            @endif
        </div>
        <!-- ./Card -->
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {

            $('._edit-btn').click(function () {

                let _token = $('meta[name="csrf-token"]').attr('content');
                let client_code = $(this).attr('client-code');

                $.colorbox({
                    iframe: false,
                    href: origin + '/ajax/customers/'+ client_code +'/edit',
                    width: "80%",
                    height: "90%",
                    returnFocus: false,
                    data: {
                        _token: _token,
                        client_code: client_code,
                    },
                    onComplete: function () {

                        $('#cancelBtn').click(function () {
                            $.colorbox.close();
                        });

                        // prepare Options Object
                        let options = {
                            url: '/ajax/customers/' + client_code,
                            responseType: 'blob', // important
                            type: 'PATCH',
                            success: function (res) {
                                $.colorbox.close();
                                
                                let msg = "Changed Success";

                                swal({
                                    icon: 'success',
                                    text: msg,
                                });

                                location.reload();
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
                        $('#customerUpdateForm').ajaxForm(options);
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


            $('#create_btn').click(function () {

                let _token = $('meta[name="csrf-token"]').attr('content');

                $.colorbox({
                    iframe: false,
                    href: origin + '/ajax/customers/create',
                    width: "80%",
                    height: "90%",
                    returnFocus: false,
                    data: {
                        _token: _token,
                    },
                    onComplete: function () {

                        $('#cancelBtn').click(function () {
                            $.colorbox.close();
                        });

                        // prepare Options Object
                        let options = {
                            url: '/ajax/customers/store',
                            responseType: 'blob',
                            type: 'POST',
                            success: function (res) {
                                $.colorbox.close();
                                
                                let msg = "Created Success";

                                swal({
                                    icon: 'success',
                                    text: msg,
                                });

                                location.reload();
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
                        $('#customerCreateForm').ajaxForm(options);
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

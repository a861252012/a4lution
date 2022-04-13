@extends('layouts.app', [
'parentSection' => 'fee',
'elementName' => 'wfs-storage-fee'
])

@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('FEE') }}
            @endslot
            <li class="breadcrumb-item">
                <a href="{{ route('management.sellerAccount.view') }}">
                    {{ __('MANAGEMENT') }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('SELLER ACCOUNT') }}</li>
        @endcomponent
    @endcomponent

    <div class="wrapper wrapper-content">
        <!-- Table -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header py-2">
                <form method="GET" action="{{ route('management.sellerAccount.view') }}" role="form" class="form">
                    <div class="row">

                        {{-- PLATFORM --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="platform">Platform</label>
                                <input class="form-control _fz-1" name="platform" id="platform"
                                       type="text" placeholder="platform" value="{{ request('platform') }}">
                            </div>
                        </div>

                        {{-- IS A4 ACCOUNT --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label _fz-1" for="is_a4_account">Is A4 Account</label>
                                <select class="form-control" name="is_a4_account">
                                    <option value=''>{{ __('All') }}</option>
                                    <option value='1' @if (request('is_a4_account') == '1') selected @endif>{{ __('Yes') }}
                                    </option>
                                    <option value='0' @if (request('is_a4_account') == '0') selected @endif>{{ __('No') }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- SEARCH --}}
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <label class="form-control-label" for="submit_btn"></label>
                            <div class="form-group mb-0">
                                <button class="form-control btn _btn btn-primary _fz-1" id="submit_btn" type="submit"
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
                        <th>Account Type</th>
                        <th>Platform</th>
                        <th>Account Name</th>
                        <th>ERP Nickname</th>
                        <th>Asinking Acc Name</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($lists as $item)
                        <tr>
                            <td>{{ ($item->is_a4_account) ? 'A4' : 'Client' }}</td>
                            <td>{{ $item->platform }}</td>
                            <td>{{ $item->account_name }}</td>
                            <td>{{ $item->erp_nick_name }}</td>
                            <td>{{ $item->asinking_account_name }}</td>
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
@endsection
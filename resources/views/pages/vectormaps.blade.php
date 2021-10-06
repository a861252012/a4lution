@extends('layouts.app', [
    'parentSection' => 'maps',
    'elementName' => 'vector'
])

@section('content') 
    @component('layouts.headers.auth') 
        @component('layouts.headers.breadcrumbs')
            @slot('title') 
                {{ __('Vector maps') }} 
            @endslot

            <li class="breadcrumb-item"><a href="{{ route('page.index', 'vectormaps') }}">{{ __('Maps') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Vector maps') }}</li>
        @endcomponent 
    @endcomponent

    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body pt-0">
                        <div class="vector-map" data-toggle="vectormap" data-map="world_mill"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        @include('layouts.footers.auth')
    </div>
@endsection

@push('js')
    <script src="{{ asset('argon') }}/vendor/jvectormap-next/jquery-jvectormap.min.js"></script>
    <script src="{{ asset('argon') }}/js/vendor/jvectormap/jquery-jvectormap-world-mill.js"></script>
@endpush
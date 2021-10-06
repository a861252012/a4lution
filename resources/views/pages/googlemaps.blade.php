@extends('layouts.app', [
    'parentSection' => 'maps',
    'elementName' => 'google'
])

@section('content') 
    @component('layouts.headers.auth') 
        @component('layouts.headers.breadcrumbs')
            @slot('title') 
                {{ __('Google maps') }} 
            @endslot

            <li class="breadcrumb-item"><a href="{{ route('page.index', 'googlemaps') }}">{{ __('Maps') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Google maps') }}</li>
        @endcomponent 
    @endcomponent

    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col">
                <div class="card border-0">
                    <div id="map-custom" class="map-canvas" data-lat="40.748817" data-lng="-73.985428" style="height: 600px;"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card border-0">
                    <div id="map-default" class="map-canvas" data-lat="40.748817" data-lng="-73.985428" style="height: 600px;"></div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        @include('layouts.footers.auth')
    </div>
@endsection

@push('js')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTTfWur0PDbZWPr7Pmq8K3jiDp0_xUziI"></script>
@endpush
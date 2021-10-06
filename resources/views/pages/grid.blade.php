@extends('layouts.app', [
    'parentSection' => 'components',
    'elementName' => 'grid'
])  

@section('content') 
    @component('layouts.headers.auth') 
        @component('layouts.headers.breadcrumbs')
            @slot('title') 
                {{ __('Grid') }} 
            @endslot

            <li class="breadcrumb-item"><a href="{{ route('page.index', 'grid') }}">{{ __('Components') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Grid') }}</li>
        @endcomponent 
    @endcomponent

    <div class="container-fluid mt--6">
        <div class="row justify-content-center">
            <div class="col-lg-8 card-wrapper">
                <!-- Grid system -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Grid system</h3>
                    </div>
                    <div class="card-body">
                        <div class="row row-example">
                            <div class="col-sm">
                                <span>One of three columns</span>
                            </div>
                            <div class="col-sm">
                                <span>One of three columns</span>
                            </div>
                            <div class="col-sm">
                                <span>One of three columns</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Equal-width -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Equal-width</h3>
                    </div>
                    <div class="card-body">
                        <div class="row row-example">
                            <div class="col">
                                <span>1 of 2</span>
                            </div>
                            <div class="col">
                                <span>2 of 2</span>
                            </div>
                        </div>
                        <div class="row row-example">
                            <div class="col">
                                <span>1 of 3</span>
                            </div>
                            <div class="col">
                                <span>2 of 3</span>
                            </div>
                            <div class="col">
                                <span>3 of 3</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Setting one column width -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Setting one column width</h3>
                    </div>
                    <div class="card-body">
                        <div class="row row-example">
                            <div class="col">
                                <span>1 of 3</span>
                            </div>
                            <div class="col-6">
                                <span>2 of 3 (wider)</span>
                            </div>
                            <div class="col">
                                <span>3 of 3</span>
                            </div>
                        </div>
                        <div class="row row-example">
                            <div class="col">
                                <span>1 of 3</span>
                            </div>
                            <div class="col-5">
                                <span>2 of 3 (wider)</span>
                            </div>
                            <div class="col">
                                <span>3 of 3</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Variable width content -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Variable width content</h3>
                    </div>
                    <div class="card-body">
                        <div class="row row-example justify-content-md-center">
                            <div class="col col-lg-2">
                                <span>1 of 3</span>
                            </div>
                            <div class="col-md-auto">
                                <span>Variable width content</span>
                            </div>
                            <div class="col col-lg-2">
                                <span>3 of 3</span>
                            </div>
                        </div>
                        <div class="row row-example">
                            <div class="col">
                                <span>1 of 3</span>
                            </div>
                            <div class="col-md-auto">
                                <span>Variable width content</span>
                            </div>
                            <div class="col col-lg-2">
                                <span>3 of 3</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Equal-width multi-row -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Equal-width multi-row</h3>
                    </div>
                    <div class="card-body">
                        <div class="row row-example">
                            <div class="col"><span>col</span></div>
                            <div class="col"><span>col</span></div>
                            <div class="w-100"></div>
                            <div class="col"><span>col</span></div>
                            <div class="col"><span>col</span></div>
                        </div>
                    </div>
                </div>
                <!-- Mix and match -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Mix and match</h3>
                    </div>
                    <div class="card-body">
                        <!-- Stack the columns on mobile by making one full-width and the other half-width -->
                        <div class="row row-example">
                            <div class="col-12 col-md-8"><span>.col-12 .col-md-8</span></div>
                            <div class="col-6 col-md-4"><span>.col-6 .col-md-4</span></div>
                        </div>
                        <!-- Columns start at 50% wide on mobile and bump up to 33.3% wide on desktop -->
                        <div class="row row-example">
                            <div class="col-6 col-md-4"><span>.col-6 .col-md-4</span></div>
                            <div class="col-6 col-md-4"><span>.col-6 .col-md-4</span></div>
                            <div class="col-6 col-md-4"><span>.col-6 .col-md-4</span></div>
                        </div>
                        <!-- Columns are always 50% wide, on mobile and desktop -->
                        <div class="row row-example">
                            <div class="col-6"><span>.col-6</span></div>
                            <div class="col-6"><span>.col-6</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        @include('layouts.footers.auth')
    </div>
@endsection
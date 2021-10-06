@extends('layouts.app', [
    'parentSection' => '',
    'elementName' => 'charts'
])

@section('content') 
    @component('layouts.headers.auth') 
        @component('layouts.headers.breadcrumbs')
            @slot('title') 
                {{ __('Charts') }} 
            @endslot

            <li class="breadcrumb-item"><a href="{{ route('page.index', 'charts') }}">{{ __('Charts') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Charts') }}</li>
        @endcomponent
        @include('layouts.headers.cards') 
    @endcomponent

    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col-xl-6">
                <!--* Card header *-->
                <!--* Card body *-->
                <!--* Card init *-->
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <!-- Surtitle -->
                        <h6 class="surtitle">Overview</h6>
                        <!-- Title -->
                        <h5 class="h3 mb-0">Total sales</h5>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="chart">
                            <!-- Chart wrapper -->
                            <canvas id="chart-sales" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <!--* Card header *-->
                <!--* Card body *-->
                <!--* Card init *-->
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <!-- Surtitle -->
                        <h6 class="surtitle">Performance</h6>
                        <!-- Title -->
                        <h5 class="h3 mb-0">Total orders</h5>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="chart">
                            <!-- Chart wrapper -->
                            <canvas id="chart-bars" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-6">
                <!--* Card header *-->
                <!--* Card body *-->
                <!--* Card init *-->
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <!-- Surtitle -->
                        <h6 class="surtitle">Growth</h6>
                        <!-- Title -->
                        <h5 class="h3 mb-0">Sales value</h5>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="chart">
                            <!-- Chart wrapper -->
                            <canvas id="chart-points" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <!--* Card header *-->
                <!--* Card body *-->
                <!--* Card init *-->
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <!-- Surtitle -->
                        <h6 class="surtitle">Users</h6>
                        <!-- Title -->
                        <h5 class="h3 mb-0">Audience overview</h5>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="chart">
                            <!-- Chart wrapper -->
                            <canvas id="chart-doughnut" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-6">
                <!--* Card header *-->
                <!--* Card body *-->
                <!--* Card init *-->
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <!-- Surtitle -->
                        <h6 class="surtitle">Partners</h6>
                        <!-- Title -->
                        <h5 class="h3 mb-0">Affiliate traffic</h5>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="chart">
                            <!-- Chart wrapper -->
                            <canvas id="chart-pie" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <!--* Card header *-->
                <!--* Card body *-->
                <!--* Card init *-->
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <!-- Surtitle -->
                        <h6 class="surtitle">Overview</h6>
                        <!-- Title -->
                        <h5 class="h3 mb-0">Product comparison</h5>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="chart">
                            <!-- Chart wrapper -->
                            <canvas id="chart-bar-stacked" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        @include('layouts.footers.auth')
    </div>
@endsection

@push('js')
    <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.extension.js"></script>
@endpush
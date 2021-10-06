@extends('layouts.app', [
    'parentSection' => '',
    'elementName' => 'widgets'
])

@section('content') 
    @component('layouts.headers.auth') 
        @component('layouts.headers.breadcrumbs')
            @slot('title') 
                {{ __('Widgets') }} 
            @endslot

            <li class="breadcrumb-item"><a href="{{ route('page.index', 'widgets') }}">{{ __('Widgets') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Widgets') }}</li>
        @endcomponent
        @include('layouts.headers.cards') 
    @endcomponent

    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col-lg-4">
                <!-- Image-Text card -->
                <div class="card">
                    <!-- Card image -->
                    <img class="card-img-top" src="{{ asset('argon') }}/img/theme/img-1-1000x900.jpg" alt="Image placeholder">
                    <!-- Card body -->
                    <div class="card-body">
                        <h5 class="h2 card-title mb-0">Get started with Argon</h5>
                        <small class="text-muted">by John Snow on Oct 29th at 10:23 AM</small>
                        <p class="card-text mt-4">Argon is a great free UI package based on Bootstrap 4 that includes the most important components and
                            features.</p>
                        <a href="#!" class="btn btn-link px-0">View article</a>
                    </div>
                </div>
                <!-- Members list group card -->
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <!-- Title -->
                        <h5 class="h3 mb-0">Team members</h5>
                    </div>
                    <!-- Card search -->
                    <div class="card-header py-0">
                        <!-- Search form -->
                        <form>
                            <div class="form-group mb-0">
                                <div class="input-group input-group-lg input-group-flush">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <span class="fas fa-search"></span>
                                        </div>
                                    </div>
                                    <input type="search" class="form-control" placeholder="Search">
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <!-- List group -->
                        <ul class="list-group list-group-flush list my--3">
                            <li class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <!-- Avatar -->
                                        <a href="#" class="avatar rounded-circle">
                                            <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1.jpg">
                                        </a>
                                    </div>
                                    <div class="col ml--2">
                                        <h4 class="mb-0">
                                            <a href="#!">John Michael</a>
                                        </h4>
                                        <span class="text-success">●</span>
                                        <small>Online</small>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-sm btn-primary">Add</button>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <!-- Avatar -->
                                        <a href="#" class="avatar rounded-circle">
                                            <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-2.jpg">
                                        </a>
                                    </div>
                                    <div class="col ml--2">
                                        <h4 class="mb-0">
                                            <a href="#!">Alex Smith</a>
                                        </h4>
                                        <span class="text-warning">●</span>
                                        <small>In a meeting</small>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-sm btn-primary">Add</button>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <!-- Avatar -->
                                        <a href="#" class="avatar rounded-circle">
                                            <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-3.jpg">
                                        </a>
                                    </div>
                                    <div class="col ml--2">
                                        <h4 class="mb-0">
                                            <a href="#!">Samantha Ivy</a>
                                        </h4>
                                        <span class="text-danger">●</span>
                                        <small>Offline</small>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-sm btn-primary">Add</button>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <!-- Avatar -->
                                        <a href="#" class="avatar rounded-circle">
                                            <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-4.jpg">
                                        </a>
                                    </div>
                                    <div class="col ml--2">
                                        <h4 class="mb-0">
                                            <a href="#!">John Michael</a>
                                        </h4>
                                        <span class="text-success">●</span>
                                        <small>Online</small>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-sm btn-primary">Add</button>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <!-- Avatar -->
                                        <a href="#" class="avatar rounded-circle">
                                            <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-5.jpg">
                                        </a>
                                    </div>
                                    <div class="col ml--2">
                                        <h4 class="mb-0">
                                            <a href="#!">John Snow</a>
                                        </h4>
                                        <span class="text-success">●</span>
                                        <small>Online</small>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-sm btn-primary">Add</button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Messages -->
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <!-- Title -->
                        <h5 class="h3 mb-0">Latest messages</h5>
                    </div>
                    <!-- Card body -->
                    <div class="card-body p-0">
                        <!-- List group -->
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action flex-column align-items-start py-4 px-4">
                                <div class="d-flex w-100 justify-content-between">
                                    <div>
                                        <div class="d-flex w-100 align-items-center">
                                            <img src="{{ asset('argon') }}/img/theme/team-1.jpg" alt="Image placeholder" class="avatar avatar-xs mr-2" />
                                            <h5 class="mb-1">Tim</h5>
                                        </div>
                                    </div>
                                    <small>2 hrs ago</small>
                                </div>
                                <h4 class="mt-3 mb-1"> New order for Argon Dashboard</h4>
                                <p class="text-sm mb-0">Doasdnec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.</p>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action flex-column align-items-start py-4 px-4">
                                <div class="d-flex w-100 justify-content-between">
                                    <div>
                                        <div class="d-flex w-100 align-items-center">
                                            <img src="{{ asset('argon') }}/img/theme/team-2.jpg" alt="Image placeholder" class="avatar avatar-xs mr-2" />
                                            <h5 class="mb-1">Mike</h5>
                                        </div>
                                    </div>
                                    <small>1 day ago</small>
                                </div>
                                <h4 class="mt-3 mb-1"><span class="text-info">●</span> Your theme has been updated</h4>
                                <p class="text-sm mb-0">Doasdnec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.</p>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Master card -->
                <div class="card bg-gradient-primary">
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="row justify-content-between align-items-center">
                            <div class="col">
                                <img src="{{ asset('argon') }}/img/icons/cards/mastercard.png" alt="Image placeholder" />
                            </div>
                            <div class="col-auto">
                                <div class="d-flex align-items-center">
                                    <small class="text-white font-weight-bold mr-3">Make default</small>
                                    <div>
                                        <label class="custom-toggle  custom-toggle-white">
                                <input type="checkbox" checked="">
                                <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span>
                                </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <form role="form" class="form-primary">
                                <div class="form-group">
                                    <div class="input-group input-group-alternative mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ni ni-single-02"></i></span>
                                        </div>
                                        <input class="form-control" placeholder="Name on card" type="text">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group input-group-alternative mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ni ni-credit-card"></i></span>
                                        </div>
                                        <input class="form-control" placeholder="Card number" type="text">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <div class="input-group input-group-alternative mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                </div>
                                                <input class="form-control" placeholder="MM/YY" type="text">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <div class="input-group input-group-alternative">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                                </div>
                                                <input class="form-control" placeholder="CCV" type="text">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-block btn-info">Save new card</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <!-- Calendar widget -->
                <!--* Card header *-->
                <!--* Card body *-->
                <!--* Card init *-->
                <div class="card widget-calendar">
                    <!-- Card header -->
                    <div class="card-header">
                        <div class="h5 text-muted mb-1 widget-calendar-year"></div>
                        <div class="h3 mb-0 widget-calendar-day"></div>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <div data-toggle="widget-calendar"></div>
                    </div>
                </div>
                <!-- Timeline card -->
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <!-- Title -->
                        <h5 class="h3 mb-0">Latest notifications</h5>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="timeline timeline-one-side" data-timeline-content="axis" data-timeline-axis-style="dashed">
                            <div class="timeline-block">
                                <span class="timeline-step badge-success">
                            <i class="ni ni-bell-55"></i>
                            </span>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between pt-1">
                                        <div>
                                            <span class="text-muted text-sm font-weight-bold">New message</span>
                                        </div>
                                        <div class="text-right">
                                            <small class="text-muted"><i class="fas fa-clock mr-1"></i>2 hrs ago</small>
                                        </div>
                                    </div>
                                    <h6 class="text-sm mt-1 mb-0">Let's meet at Starbucks at 11:30. Wdyt?</h6>
                                </div>
                            </div>
                            <div class="timeline-block">
                                <span class="timeline-step badge-danger">
                                    <i class="ni ni-html5"></i>
                                </span>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between pt-1">
                                        <div>
                                            <span class="text-muted text-sm font-weight-bold">Product issue</span>
                                        </div>
                                        <div class="text-right">
                                            <small class="text-muted"><i class="fas fa-clock mr-1"></i>3 hrs ago</small>
                                        </div>
                                    </div>
                                    <h6 class="text-sm mt-1 mb-0">A new issue has been reported for Argon.</h6>
                                </div>
                            </div>
                            <div class="timeline-block">
                                <span class="timeline-step badge-info">
                                    <i class="ni ni-like-2"></i>
                                </span>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between pt-1">
                                        <div>
                                            <span class="text-muted text-sm font-weight-bold">New likes</span>
                                        </div>
                                        <div class="text-right">
                                            <small class="text-muted"><i class="fas fa-clock mr-1"></i>5 hrs ago</small>
                                        </div>
                                    </div>
                                    <h6 class="text-sm mt-1 mb-0">Your posts have been liked a lot.</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Progress track -->
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <!-- Title -->
                        <h5 class="h3 mb-0">Progress track</h5>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <!-- List group -->
                        <ul class="list-group list-group-flush list my--3">
                            <li class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <!-- Avatar -->
                                        <a href="#" class="avatar rounded-circle">
                                            <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/bootstrap.jpg">
                                        </a>
                                    </div>
                                    <div class="col">
                                        <h5>Argon Design System</h5>
                                        <div class="progress progress-xs mb-0">
                                            <div class="progress-bar bg-orange" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <!-- Avatar -->
                                        <a href="#" class="avatar rounded-circle">
                                            <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/angular.jpg">
                                        </a>
                                    </div>
                                    <div class="col">
                                        <h5>Angular Now UI Kit PRO</h5>
                                        <div class="progress progress-xs mb-0">
                                            <div class="progress-bar bg-green" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <!-- Avatar -->
                                        <a href="#" class="avatar rounded-circle">
                                            <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/sketch.jpg">
                                        </a>
                                    </div>
                                    <div class="col">
                                        <h5>Black Dashboard</h5>
                                        <div class="progress progress-xs mb-0">
                                            <div class="progress-bar bg-red" role="progressbar" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100" style="width: 72%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <!-- Avatar -->
                                        <a href="#" class="avatar rounded-circle">
                                            <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/react.jpg">
                                        </a>
                                    </div>
                                    <div class="col">
                                        <h5>React Material Dashboard</h5>
                                        <div class="progress progress-xs mb-0">
                                            <div class="progress-bar bg-teal" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width: 90%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <!-- Avatar -->
                                        <a href="#" class="avatar rounded-circle">
                                            <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/vue.jpg">
                                        </a>
                                    </div>
                                    <div class="col">
                                        <h5>Vue Paper UI Kit PRO</h5>
                                        <div class="progress progress-xs mb-0">
                                            <div class="progress-bar bg-green" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Paypal card -->
                <div class="card">
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="row justify-content-between align-items-center">
                            <div class="col">
                                <img src="{{ asset('argon') }}/img/icons/cards/paypal.png" alt="Image placeholder" />
                            </div>
                            <div class="col-auto">
                                <span class="badge badge-lg badge-success">Active</span>
                            </div>
                        </div>
                        <div class="my-4">
                            <span class="h6 surtitle text-muted">
                            PayPal E-mail
                        </span>
                            <div class="h1">john.snow@gmail.com</div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <span class="h6 surtitle text-muted">Name</span>
                                <span class="d-block h3">John Snow</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <!-- Vector map -->
                <!--* Card header *-->
                <!--* Card body *-->
                <!--* Card init *-->
                <div class="card widget-calendar">
                    <!-- Card header -->
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <!-- Title -->
                                <h5 class="h3 mb-0">Real time</h5>
                            </div>
                            <div class="col-4 text-right">
                                <a href="#!" class="btn btn-sm btn-neutral">Action</a>
                            </div>
                        </div>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <!-- Vector map -->
                        <div class="vector-map vector-map-sm" data-toggle="vectormap" data-map="world_mill"></div>
                        <!-- List group -->
                        <ul class="list-group list-group-flush list my--3">
                            <li class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <!-- Country flag -->
                                        <img src="{{ asset('argon') }}/img/icons/flags/US.png" alt="Country flag" />
                                    </div>
                                    <div class="col">
                                        <small>Country:</small>
                                        <h5 class="mb-0">United States</h5>
                                    </div>
                                    <div class="col">
                                        <small>Visits:</small>
                                        <h5 class="mb-0">2500</h5>
                                    </div>
                                    <div class="col">
                                        <small>Bounce:</small>
                                        <h5 class="mb-0">30%</h5>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <!-- Country flag -->
                                        <img src="{{ asset('argon') }}/img/icons/flags/DE.png" alt="Country flag" />
                                    </div>
                                    <div class="col">
                                        <small>Country:</small>
                                        <h5 class="mb-0">Germany</h5>
                                    </div>
                                    <div class="col">
                                        <small>Visits:</small>
                                        <h5 class="mb-0">2500</h5>
                                    </div>
                                    <div class="col">
                                        <small>Bounce:</small>
                                        <h5 class="mb-0">30%</h5>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <!-- Country flag -->
                                        <img src="{{ asset('argon') }}/img/icons/flags/GB.png" alt="Country flag" />
                                    </div>
                                    <div class="col">
                                        <small>Country:</small>
                                        <h5 class="mb-0">Great Britain</h5>
                                    </div>
                                    <div class="col">
                                        <small>Visits:</small>
                                        <h5 class="mb-0">2500</h5>
                                    </div>
                                    <div class="col">
                                        <small>Bounce:</small>
                                        <h5 class="mb-0">30%</h5>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Visa card -->
                <div class="card bg-gradient-primary">
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="row justify-content-between align-items-center">
                            <div class="col">
                                <img src="{{ asset('argon') }}/img/icons/cards/visa.png" alt="Image placeholder" />
                            </div>
                            <div class="col-auto">
                                <span class="badge badge-lg badge-success">Active</span>
                            </div>
                        </div>
                        <div class="my-4">
                            <span class="h6 surtitle text-light">
                            Card number
                        </span>
                            <div class="card-serial-number h1 text-white">
                                <div>4358</div>
                                <div>7421</div>
                                <div>9256</div>
                                <div>6682</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <span class="h6 surtitle text-light">Name</span>
                                <span class="d-block h3 text-white">John Snow</span>
                            </div>
                            <div class="col">
                                <span class="h6 surtitle text-light">Expiry date</span>
                                <span class="d-block h3 text-white">11/23</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card stats -->
                <div class="card bg-gradient-default">
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title text-uppercase text-muted mb-0 text-white">Total traffic</h5>
                                <span class="h2 font-weight-bold mb-0 text-white">350,897</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-white text-dark rounded-circle shadow">
                                    <i class="ni ni-active-40"></i>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0 text-sm">
                            <span class="text-white mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>
                            <span class="text-nowrap text-light">Since last month</span>
                        </p>
                    </div>
                </div>
                <div class="card bg-gradient-primary">
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title text-uppercase text-muted mb-0 text-white">New users</h5>
                                <span class="h2 font-weight-bold mb-0 text-white">2,356</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-white text-dark rounded-circle shadow">
                                    <i class="ni ni-atom"></i>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0 text-sm">
                            <span class="text-white mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>
                            <span class="text-nowrap text-light">Since last month</span>
                        </p>
                    </div>
                </div>
                <div class="card bg-gradient-danger">
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title text-uppercase text-muted mb-0 text-white">Performance</h5>
                                <span class="h2 font-weight-bold mb-0 text-white">49,65%</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-white text-dark rounded-circle shadow">
                                    <i class="ni ni-spaceship"></i>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0 text-sm">
                            <span class="text-white mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>
                            <span class="text-nowrap text-light">Since last month</span>
                        </p>
                    </div>
                </div>
                <!-- Checklist -->
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <!-- Title -->
                        <h5 class="h3 mb-0">To do list</h5>
                    </div>
                    <!-- Card body -->
                    <div class="card-body p-0">
                        <!-- List group -->
                        <ul class="list-group list-group-flush" data-toggle="checklist">
                            <li class="checklist-entry list-group-item flex-column align-items-start py-4 px-4">
                                <div class="checklist-item checklist-item-success">
                                    <div class="checklist-info">
                                        <h5 class="checklist-title mb-0">Call with Dave</h5>
                                        <small>10:30 AM</small>
                                    </div>
                                    <div>
                                        <div class="custom-control custom-checkbox custom-checkbox-success">
                                            <input class="custom-control-input" id="chk-todo-task-1" type="checkbox" checked>
                                            <label class="custom-control-label" for="chk-todo-task-1"></label>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="checklist-entry list-group-item flex-column align-items-start py-4 px-4">
                                <div class="checklist-item checklist-item-warning">
                                    <div class="checklist-info">
                                        <h5 class="checklist-title mb-0">Lunch meeting</h5>
                                        <small>10:30 AM</small>
                                    </div>
                                    <div>
                                        <div class="custom-control custom-checkbox custom-checkbox-warning">
                                            <input class="custom-control-input" id="chk-todo-task-2" type="checkbox">
                                            <label class="custom-control-label" for="chk-todo-task-2"></label>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="checklist-entry list-group-item flex-column align-items-start py-4 px-4">
                                <div class="checklist-item checklist-item-info">
                                    <div class="checklist-info">
                                        <h5 class="checklist-title mb-0">Argon Dashboard Launch</h5>
                                        <small>10:30 AM</small>
                                    </div>
                                    <div>
                                        <div class="custom-control custom-checkbox custom-checkbox-info">
                                            <input class="custom-control-input" id="chk-todo-task-3" type="checkbox">
                                            <label class="custom-control-label" for="chk-todo-task-3"></label>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="checklist-entry list-group-item flex-column align-items-start py-4 px-4">
                                <div class="checklist-item checklist-item-danger">
                                    <div class="checklist-info">
                                        <h5 class="checklist-title mb-0">Winter Hackaton</h5>
                                        <small>10:30 AM</small>
                                    </div>
                                    <div>
                                        <div class="custom-control custom-checkbox custom-checkbox-danger">
                                            <input class="custom-control-input" id="chk-todo-task-4" type="checkbox" checked>
                                            <label class="custom-control-label" for="chk-todo-task-4"></label>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="checklist-entry list-group-item flex-column align-items-start py-4 px-4">
                                <div class="checklist-item checklist-item-success">
                                    <div class="checklist-info">
                                        <h5 class="checklist-title mb-0">Dinner with Family</h5>
                                        <small>10:30 AM</small>
                                    </div>
                                    <div>
                                        <div class="custom-control custom-checkbox custom-checkbox-success">
                                            <input class="custom-control-input" id="chk-todo-task-5" type="checkbox" checked>
                                            <label class="custom-control-label" for="chk-todo-task-5"></label>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        @include('layouts.footers.auth')
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('argon') }}/vendor/fullcalendar/dist/fullcalendar.min.css">
    <link rel="stylesheet" href="{{ asset('argon') }}/vendor/sweetalert2/dist/sweetalert2.min.css"> 
@endpush 

@push('js') 
    <script src="{{ asset('argon') }}/vendor/moment/min/moment.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/fullcalendar/dist/fullcalendar.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/jvectormap-next/jquery-jvectormap.min.js"></script>
    <script src="{{ asset('argon') }}/js/vendor/jvectormap/jquery-jvectormap-world-mill.js"></script>
@endpush
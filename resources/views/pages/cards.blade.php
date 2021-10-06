@extends('layouts.app', [
    'parentSection' => 'components',
    'elementName' => 'cards'
]) 

@section('content') 
    @component('layouts.headers.auth') 
        @component('layouts.headers.breadcrumbs')
            @slot('title') 
                {{ __('Cards') }} 
            @endslot

            <li class="breadcrumb-item"><a href="{{ route('page.index', 'cards') }}">{{ __('Components') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Cards') }}</li>
        @endcomponent 
    @endcomponent

    <div class="container-fluid mt--6">
        <div class="row card-wrapper">
            <div class="col-lg-4">
                <!-- Basic with list group -->
                <div class="card">
                    <!-- Card image -->
                    <img class="card-img-top" src="{{ asset('argon') }}/img/theme/img-1-1000x600.jpg" alt="Image placeholder">
                    <!-- List group -->
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Cras justo odio</li>
                        <li class="list-group-item">Dapibus ac facilisis in</li>
                        <li class="list-group-item">Vestibulum at eros</li>
                    </ul>
                    <!-- Card body -->
                    <div class="card-body">
                        <h3 class="card-title mb-3">Card title</h3>
                        <p class="card-text mb-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facilis non dolore est fuga nobis ipsum illum
                            eligendi nemo iure repellat, soluta, optio minus ut reiciendis voluptates enim impedit veritatis
                            officiis.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
                <!-- Basic with card title -->
                <div class="card">
                    <!-- Card image -->
                    <!-- List group -->
                    <!-- Card body -->
                    <div class="card-body">
                        <h3 class="card-title mb-3">Card title</h3>
                        <p class="card-text mb-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facilis non dolore est fuga nobis ipsum illum
                            eligendi nemo iure repellat, soluta, optio minus ut reiciendis voluptates enim impedit veritatis
                            officiis.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
                <!-- Profile card -->
                <div class="card card-profile">
                    <img src="{{ asset('argon') }}/img/theme/img-1-1000x600.jpg" alt="Image placeholder" class="card-img-top">
                    <div class="row justify-content-center">
                        <div class="col-lg-3 order-lg-2">
                            <div class="card-profile-image">
                                <a href="#">
                                    <img src="{{ asset('argon') }}/img/theme/team-4.jpg" class="rounded-circle">
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-header text-center border-0 pt-8 pt-md-4 pb-0 pb-md-4">
                        <div class="d-flex justify-content-between">
                            <a href="#" class="btn btn-sm btn-info mr-4">Connect</a>
                            <a href="#" class="btn btn-sm btn-default float-right">Message</a>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col">
                                <div class="card-profile-stats d-flex justify-content-center">
                                    <div>
                                        <span class="heading">22</span>
                                        <span class="description">Friends</span>
                                    </div>
                                    <div>
                                        <span class="heading">10</span>
                                        <span class="description">Photos</span>
                                    </div>
                                    <div>
                                        <span class="heading">89</span>
                                        <span class="description">Comments</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <h5 class="h3">
                                Jessica Jones<span class="font-weight-light">, 27</span>
                            </h5>
                            <div class="h5 font-weight-300">
                                <i class="ni location_pin mr-2"></i>Bucharest, Romania
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <!-- Basic with card header -->
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <!-- Title -->
                        <h5 class="h3 mb-0">Card title</h5>
                    </div>
                    <!-- Card image -->
                    <!-- List group -->
                    <!-- Card body -->
                    <div class="card-body">
                        <p class="card-text mb-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facilis non dolore est fuga nobis ipsum illum
                            eligendi nemo iure repellat, soluta, optio minus ut reiciendis voluptates enim impedit veritatis
                            officiis.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
                <!-- Contact card -->
                <div class="card">
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <!-- Avatar -->
                                <a href="#" class="avatar avatar-xl rounded-circle">
                                    <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-2.jpg">
                                </a>
                            </div>
                            <div class="col ml--2">
                                <h4 class="mb-0">
                                    <a href="#!">John Snow</a>
                                </h4>
                                <p class="text-sm text-muted mb-0">Working remoteley</p>
                                <span class="text-success">●</span>
                                <small>Active</small>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-sm btn-primary">Add</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Team member card -->
                <div class="card">
                    <!-- Card body -->
                    <div class="card-body">
                        <a href="#!">
                            <img src="{{ asset('argon') }}/img/theme/team-1.jpg" class="rounded-circle img-center img-fluid shadow shadow-lg--hover" style="width: 140px;">
                        </a>
                        <div class="pt-4 text-center">
                            <h5 class="h3 title">
                                <span class="d-block mb-1">Ryan Tompson</span>
                                <small class="h4 font-weight-light text-muted">Web Developer</small>
                            </h5>
                            <div class="mt-3">
                                <a href="#" class="btn btn-twitter btn-icon-only rounded-circle">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="btn btn-facebook btn-icon-only rounded-circle">
                                    <i class="fab fa-facebook"></i>
                                </a>
                                <a href="#" class="btn btn-dribbble btn-icon-only rounded-circle">
                                    <i class="fab fa-dribbble"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
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
                <!-- Basic with blockquote -->
                <div class="card bg-gradient-default">
                    <div class="card-body">
                        <h3 class="card-title text-white">Testimonial</h3>
                        <blockquote class="blockquote text-white mb-0">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
                            <footer class="blockquote-footer text-danger">Someone famous in <cite title="Source Title">Source Title</cite></footer>
                        </blockquote>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <!-- Pricing -->
                <div class="card card-pricing bg-gradient-success border-0 text-center mb-4">
                    <div class="card-header bg-transparent">
                        <h4 class="text-uppercase ls-1 text-white py-3 mb-0">Bravo pack</h4>
                    </div>
                    <div class="card-body px-lg-7">
                        <div class="display-2 text-white">$49</div>
                        <span class=" text-white">per application</span>
                        <ul class="list-unstyled my-4">
                            <li>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="icon icon-xs icon-shape bg-white shadow rounded-circle">
                                            <i class="fas fa-terminal"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="pl-2 text-sm text-white">Complete documentation</span>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="icon icon-xs icon-shape bg-white shadow rounded-circle">
                                            <i class="fas fa-pen-fancy"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="pl-2 text-sm text-white">Working materials in Sketch</span>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="icon icon-xs icon-shape bg-white shadow rounded-circle">
                                            <i class="fas fa-hdd"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="pl-2 text-sm text-white">2GB cloud storage</span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <button type="button" class="btn btn-primary mb-3">Start free trial</button>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="#!" class=" text-white">Request a demo</a>
                    </div>
                </div>
                <!-- Basic with action button -->
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <!-- Title -->
                                <h5 class="h3 mb-0">Card title</h5>
                            </div>
                            <div class="col-4 text-right">
                                <a href="#!" class="btn btn-sm btn-neutral">Action</a>
                            </div>
                        </div>
                    </div>
                    <!-- Card image -->
                    <!-- List group -->
                    <!-- Card body -->
                    <div class="card-body">
                        <p class="card-text mb-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facilis non dolore est fuga nobis ipsum illum
                            eligendi nemo iure repellat, soluta, optio minus ut reiciendis voluptates enim impedit veritatis
                            officiis.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
                <!-- Image overlay -->
                <div class="card bg-dark text-white border-0">
                    <img class="card-img" src="{{ asset('argon') }}/img/theme/img-1-1000x600.jpg" alt="Card image">
                    <div class="card-img-overlay d-flex align-items-center">
                        <div>
                            <h5 class="h2 card-title text-white mb-2">Card title</h5>
                            <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This
                                content is a little bit longer.</p>
                            <p class="card-text text-sm font-weight-bold">Last updated 3 mins ago</p>
                        </div>
                    </div>
                </div>
                <!-- Pricing card -->
                <div class="card card-pricing border-0 text-center mb-4">
                    <div class="card-header bg-transparent">
                        <h4 class="text-uppercase ls-1 text-primary py-3 mb-0">Bravo pack</h4>
                    </div>
                    <div class="card-body px-lg-7">
                        <div class="display-2">$49</div>
                        <span class=" text-muted">per application</span>
                        <ul class="list-unstyled my-4">
                            <li>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="icon icon-xs icon-shape bg-gradient-primary text-white shadow rounded-circle">
                                            <i class="fas fa-terminal"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="pl-2 text-sm">Complete documentation</span>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="icon icon-xs icon-shape bg-gradient-primary text-white shadow rounded-circle">
                                            <i class="fas fa-pen-fancy"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="pl-2 text-sm">Working materials in Sketch</span>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="icon icon-xs icon-shape bg-gradient-primary text-white shadow rounded-circle">
                                            <i class="fas fa-hdd"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="pl-2 text-sm">2GB cloud storage</span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <button type="button" class="btn btn-primary mb-3">Start free trial</button>
                    </div>
                    <div class="card-footer">
                        <a href="#!" class=" text-muted">Request a demo</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        @include('layouts.footers.auth')
    </div>
@endsection
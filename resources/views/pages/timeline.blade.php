@extends('layouts.app',[
    'parentSection' => 'pages',
    'elementName' => 'timeline'
]) 

@section('content') 
    @component('layouts.headers.auth') 
        @component('layouts.headers.breadcrumbs')
            @slot('title') 
                {{ __('Pages') }} 
            @endslot

            <li class="breadcrumb-item"><a href="{{ route('page.index', 'timeline') }}">{{ __('Timeline') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Timeline') }}</li>
        @endcomponent 
    @endcomponent

    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-transparent">
                        <h3 class="mb-0">Timeline</h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline timeline-one-side" data-timeline-content="axis" data-timeline-axis-style="dashed">
                            <div class="timeline-block">
                                <span class="timeline-step badge-success">
                                    <i class="ni ni-bell-55"></i>
                                </span>
                                <div class="timeline-content">
                                    <small class="text-muted font-weight-bold">10:30 AM</small>
                                    <h5 class=" mt-3 mb-0">New message</h5>
                                    <p class=" text-sm mt-1 mb-0">Nullam id dolor id nibh ultricies vehicula ut id elit. Cum sociis natoque penatibus et magnis
                                        dis parturient montes, nascetur ridiculus mus.</p>
                                    <div class="mt-3">
                                        <span class="badge badge-pill badge-success">design</span>
                                        <span class="badge badge-pill badge-success">system</span>
                                        <span class="badge badge-pill badge-success">creative</span>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-block">
                                <span class="timeline-step badge-danger">
                                    <i class="ni ni-html5"></i>
                                </span>
                                <div class="timeline-content">
                                    <small class="text-muted font-weight-bold">10:30 AM</small>
                                    <h5 class=" mt-3 mb-0">Product issue</h5>
                                    <p class=" text-sm mt-1 mb-0">Nullam id dolor id nibh ultricies vehicula ut id elit. Cum sociis natoque penatibus et magnis
                                        dis parturient montes, nascetur ridiculus mus.</p>
                                    <div class="mt-3">
                                        <span class="badge badge-pill badge-danger">design</span>
                                        <span class="badge badge-pill badge-danger">system</span>
                                        <span class="badge badge-pill badge-danger">creative</span>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-block">
                                <span class="timeline-step badge-info">
                                    <i class="ni ni-like-2"></i>
                                </span>
                                <div class="timeline-content">
                                    <small class="text-muted font-weight-bold">10:30 AM</small>
                                    <h5 class=" mt-3 mb-0">New likes</h5>
                                    <p class=" text-sm mt-1 mb-0">Nullam id dolor id nibh ultricies vehicula ut id elit. Cum sociis natoque penatibus et magnis
                                        dis parturient montes, nascetur ridiculus mus.</p>
                                    <div class="mt-3">
                                        <span class="badge badge-pill badge-info">design</span>
                                        <span class="badge badge-pill badge-info">system</span>
                                        <span class="badge badge-pill badge-info">creative</span>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-block">
                                <span class="timeline-step badge-success">
                                    <i class="ni ni-bell-55"></i>
                                </span>
                                <div class="timeline-content">
                                    <small class="text-muted font-weight-bold">10:30 AM</small>
                                    <h5 class=" mt-3 mb-0">New message</h5>
                                    <p class=" text-sm mt-1 mb-0">Nullam id dolor id nibh ultricies vehicula ut id elit. Cum sociis natoque penatibus et magnis
                                        dis parturient montes, nascetur ridiculus mus.</p>
                                    <div class="mt-3">
                                        <span class="badge badge-pill badge-success">design</span>
                                        <span class="badge badge-pill badge-success">system</span>
                                        <span class="badge badge-pill badge-success">creative</span>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-block">
                                <span class="timeline-step badge-danger">
                                    <i class="ni ni-html5"></i>
                                </span>
                                <div class="timeline-content">
                                    <small class="text-muted font-weight-bold">10:30 AM</small>
                                    <h5 class=" mt-3 mb-0">Product issue</h5>
                                    <p class=" text-sm mt-1 mb-0">Nullam id dolor id nibh ultricies vehicula ut id elit. Cum sociis natoque penatibus et magnis
                                        dis parturient montes, nascetur ridiculus mus.</p>
                                    <div class="mt-3">
                                        <span class="badge badge-pill badge-danger">design</span>
                                        <span class="badge badge-pill badge-danger">system</span>
                                        <span class="badge badge-pill badge-danger">creative</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card bg-gradient-default shadow">
                    <div class="card-header bg-transparent">
                        <h3 class="mb-0 text-white">Dark timeline</h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline timeline-one-side" data-timeline-content="axis" data-timeline-axis-style="dashed">
                            <div class="timeline-block">
                                <span class="timeline-step badge-success">
                                    <i class="ni ni-bell-55"></i>
                                </span>
                                <div class="timeline-content">
                                    <small class="text-light font-weight-bold">10:30 AM</small>
                                    <h5 class="text-white mt-3 mb-0">New message</h5>
                                    <p class="text-light text-sm mt-1 mb-0">Nullam id dolor id nibh ultricies vehicula ut id elit. Cum sociis natoque penatibus et magnis
                                        dis parturient montes, nascetur ridiculus mus.</p>
                                    <div class="mt-3">
                                        <span class="badge badge-pill badge-success">design</span>
                                        <span class="badge badge-pill badge-success">system</span>
                                        <span class="badge badge-pill badge-success">creative</span>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-block">
                                <span class="timeline-step badge-danger">
                                    <i class="ni ni-html5"></i>
                                </span>
                                <div class="timeline-content">
                                    <small class="text-light font-weight-bold">10:30 AM</small>
                                    <h5 class="text-white mt-3 mb-0">Product issue</h5>
                                    <p class="text-light text-sm mt-1 mb-0">Nullam id dolor id nibh ultricies vehicula ut id elit. Cum sociis natoque penatibus et magnis
                                        dis parturient montes, nascetur ridiculus mus.</p>
                                    <div class="mt-3">
                                        <span class="badge badge-pill badge-danger">design</span>
                                        <span class="badge badge-pill badge-danger">system</span>
                                        <span class="badge badge-pill badge-danger">creative</span>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-block">
                                <span class="timeline-step badge-info">
                                    <i class="ni ni-like-2"></i>
                                </span>
                                <div class="timeline-content">
                                    <small class="text-light font-weight-bold">10:30 AM</small>
                                    <h5 class="text-white mt-3 mb-0">New likes</h5>
                                    <p class="text-light text-sm mt-1 mb-0">Nullam id dolor id nibh ultricies vehicula ut id elit. Cum sociis natoque penatibus et magnis
                                        dis parturient montes, nascetur ridiculus mus.</p>
                                    <div class="mt-3">
                                        <span class="badge badge-pill badge-info">design</span>
                                        <span class="badge badge-pill badge-info">system</span>
                                        <span class="badge badge-pill badge-info">creative</span>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-block">
                                <span class="timeline-step badge-success">
                                    <i class="ni ni-bell-55"></i>
                                </span>
                                <div class="timeline-content">
                                    <small class="text-light font-weight-bold">10:30 AM</small>
                                    <h5 class="text-white mt-3 mb-0">New message</h5>
                                    <p class="text-light text-sm mt-1 mb-0">Nullam id dolor id nibh ultricies vehicula ut id elit. Cum sociis natoque penatibus et magnis
                                        dis parturient montes, nascetur ridiculus mus.</p>
                                    <div class="mt-3">
                                        <span class="badge badge-pill badge-success">design</span>
                                        <span class="badge badge-pill badge-success">system</span>
                                        <span class="badge badge-pill badge-success">creative</span>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-block">
                                <span class="timeline-step badge-danger">
                                    <i class="ni ni-html5"></i>
                                </span>
                                <div class="timeline-content">
                                    <small class="text-light font-weight-bold">10:30 AM</small>
                                    <h5 class="text-white mt-3 mb-0">Product issue</h5>
                                    <p class="text-light text-sm mt-1 mb-0">Nullam id dolor id nibh ultricies vehicula ut id elit. Cum sociis natoque penatibus et magnis
                                        dis parturient montes, nascetur ridiculus mus.</p>
                                    <div class="mt-3">
                                        <span class="badge badge-pill badge-danger">design</span>
                                        <span class="badge badge-pill badge-danger">system</span>
                                        <span class="badge badge-pill badge-danger">creative</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        @include('layouts.footers.auth')
    </div>
@endsection
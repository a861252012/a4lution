@extends('layouts.app', [
    'parentSection' => 'components',
    'elementName' => 'notifications'
])

@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('Notifications') }}
            @endslot

            <li class="breadcrumb-item"><a href="{{ route('page.index', 'notifications') }}">{{ __('Components') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Notifications') }}</li>
        @endcomponent
    @endcomponent

    <div class="container-fluid mt--6">
        <div class="row justify-content-center">
            <div class="col-lg-8 card-wrapper">
                <!-- Alerts -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Alerts</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-default alert-dismissible fade show" role="alert">
                            <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                            <span class="alert-text"><strong>Default!</strong> This is a default alert—check it out!</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="alert alert-primary alert-dismissible fade show" role="alert">
                            <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                            <span class="alert-text"><strong>Primary!</strong> This is a primary alert—check it out!</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="alert alert-secondary alert-dismissible fade show" role="alert">
                            <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                            <span class="alert-text"><strong>Secondary!</strong> This is a secondary alert—check it out!</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                            <span class="alert-text"><strong>Info!</strong> This is a info alert—check it out!</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                            <span class="alert-text"><strong>Success!</strong> This is a success alert—check it out!</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                            <span class="alert-text"><strong>Danger!</strong> This is a danger alert—check it out!</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                            <span class="alert-text"><strong>Warning!</strong> This is a warning alert—check it out!</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Modals -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Modals</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <button type="button" class="btn btn-block btn-primary mb-3" data-toggle="modal" data-target="#modal-default">Default</button>
                                <div class="modal fade" id="modal-default" tabindex="-1" role="dialog" aria-labelledby="modal-default" aria-hidden="true">
                                    <div class="modal-dialog modal- modal-dialog-centered modal-" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h6 class="modal-title" id="modal-title-default">Type your modal title</h6>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia,
                                                    there live the blind texts. Separated they live in Bookmarksgrove right at
                                                    the coast of the Semantics, a large language ocean.</p>
                                                <p>A small river named Duden flows by their place and supplies it with the necessary
                                                    regelialia. It is a paradisematic country, in which roasted parts of sentences
                                                    fly into your mouth.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary">Save changes</button>
                                                <button type="button" class="btn btn-link  ml-auto" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-block btn-warning mb-3" data-toggle="modal" data-target="#modal-notification">Notification</button>
                                <div class="modal fade" id="modal-notification" tabindex="-1" role="dialog" aria-labelledby="modal-notification" aria-hidden="true">
                                    <div class="modal-dialog modal-danger modal-dialog-centered modal-" role="document">
                                        <div class="modal-content bg-gradient-danger">
                                            <div class="modal-header">
                                                <h6 class="modal-title" id="modal-title-notification">Your attention is required</h6>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="py-3 text-center">
                                                    <i class="ni ni-bell-55 ni-3x"></i>
                                                    <h4 class="heading mt-4">You should read this!</h4>
                                                    <p>A small river named Duden flows by their place and supplies it with the necessary
                                                        regelialia.</p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-white">Ok, Got it</button>
                                                <button type="button" class="btn btn-link text-white ml-auto" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-block btn-default" data-toggle="modal" data-target="#modal-form">Form</button>
                                <div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                                    <div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body p-0">
                                                <div class="card bg-secondary border-0 mb-0">
                                                    <div class="card-header bg-transparent pb-5">
                                                        <div class="text-muted text-center mt-2 mb-3"><small>Sign in with</small></div>
                                                        <div class="btn-wrapper text-center">
                                                            <a href="#" class="btn btn-neutral btn-icon">
                                                                <span class="btn-inner--icon"><img src="{{ asset('argon') }}/img/icons/common/github.svg"></span>
                                                                <span class="btn-inner--text">Github</span>
                                                            </a>
                                                            <a href="#" class="btn btn-neutral btn-icon">
                                                                <span class="btn-inner--icon"><img src="{{ asset('argon') }}/img/icons/common/google.svg"></span>
                                                                <span class="btn-inner--text">Google</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="card-body px-lg-5 py-lg-5">
                                                        <div class="text-center text-muted mb-4">
                                                            <small>Or sign in with credentials</small>
                                                        </div>
                                                        <form role="form">
                                                            <div class="form-group mb-3">
                                                                <div class="input-group input-group-merge input-group-alternative">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                                                    </div>
                                                                    <input class="form-control" placeholder="Email" type="email">
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="input-group input-group-merge input-group-alternative">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                                                    </div>
                                                                    <input class="form-control" placeholder="Password" type="password">
                                                                </div>
                                                            </div>
                                                            <div class="custom-control custom-control-alternative custom-checkbox">
                                                                <input class="custom-control-input" id=" customCheckLogin" type="checkbox">
                                                                <label class="custom-control-label" for=" customCheckLogin">
                                                                    <span class="text-muted">Remember me</span>
                                                                </label>
                                                            </div>
                                                            <div class="text-center">
                                                                <button type="button" class="btn btn-primary my-4">Sign in</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Notifications -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Notifications</h3>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-default" data-toggle="notify" data-placement="top" data-align="center" data-type="default" data-icon="ni ni-bell-55">Default</button>
                        <button class="btn btn-info" data-toggle="notify" data-placement="top" data-align="center" data-type="info" data-icon="ni ni-bell-55">Info</button>
                        <button class="btn btn-success" data-toggle="notify" data-placement="top" data-align="center" data-type="success" data-icon="ni ni-bell-55">Success</button>
                        <button class="btn btn-warning" data-toggle="notify" data-placement="top" data-align="center" data-type="warning" data-icon="ni ni-bell-55">Warning</button>
                        <button class="btn btn-danger" data-toggle="notify" data-placement="top" data-align="center" data-type="danger" data-icon="ni ni-bell-55">Danger</button>
                    </div>
                </div>
                <!-- Sweet alerts -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Sweet alerts</h3>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-primary" data-toggle="sweet-alert" data-sweet-alert="basic">Basic alert</button>
                        <button class="btn btn-info" data-toggle="sweet-alert" data-sweet-alert="info">Info alert</button>
                        <button class="btn btn-success" data-toggle="sweet-alert" data-sweet-alert="success">Success alert</button>
                        <button class="btn btn-warning" data-toggle="sweet-alert" data-sweet-alert="warning">Warning alert</button>
                        <button class="btn btn-default" data-toggle="sweet-alert" data-sweet-alert="question">Question</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        @include('layouts.footers.auth')
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('argon') }}/vendor/animate.css/animate.min.css">
    <link rel="stylesheet" href="{{ asset('argon') }}/vendor/sweetalert2/dist/sweetalert2.min.css">
@endpush

@push('js')
    <script src="{{ asset('argon') }}/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/bootstrap-notify/bootstrap-notify.min.js"></script>
@endpush

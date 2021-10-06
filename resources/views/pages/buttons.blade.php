@extends('layouts.app', [
    'parentSection' => 'components',
    'elementName' => 'buttons'
]) 

@section('content') 
    @component('layouts.headers.auth') 
        @component('layouts.headers.breadcrumbs')
            @slot('title') 
                {{ __('Buttons') }} 
            @endslot

            <li class="breadcrumb-item"><a href="{{ route('page.index', 'buttons') }}">{{ __('Components') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Buttons') }}</li>
        @endcomponent 
    @endcomponent

    <div class="container-fluid mt--6">
        <div class="row justify-content-center">
            <div class="col-lg-8 card-wrapper ct-example">
                <!-- Styles -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Styles</h3>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-primary" type="button">Button</button>
                        <button class="btn btn-icon btn-primary" type="button">
                            <span class="btn-inner--icon"><i class="ni ni-bag-17"></i></span>
                            <span class="btn-inner--text">With icon</span>
                        </button>
                        <button class="btn btn-icon btn-primary" type="button">
                            <span class="btn-inner--icon"><i class="ni ni-atom"></i></span>
                        </button>
                    </div>
                </div>
                <!-- Colors -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Colors</h3>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-default">Default</button>
                        <button type="button" class="btn btn-primary">Primary</button>
                        <button type="button" class="btn btn-secondary">Secondary</button>
                        <button type="button" class="btn btn-info">Info</button>
                        <button type="button" class="btn btn-success">Success</button>
                        <button type="button" class="btn btn-danger">Danger</button>
                        <button type="button" class="btn btn-warning">Warning</button>
                    </div>
                </div>
                <!-- Outline -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Outline</h3>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-outline-default">Default</button>
                        <button type="button" class="btn btn-outline-primary">Primary</button>
                        <button type="button" class="btn btn-outline-secondary">Secondary</button>
                        <button type="button" class="btn btn-outline-info">Info</button>
                        <button type="button" class="btn btn-outline-success">Success</button>
                        <button type="button" class="btn btn-outline-danger">Danger</button>
                        <button type="button" class="btn btn-outline-warning">Warning</button>
                    </div>
                </div>
                <!-- Sizes -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Sizes</h3>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-primary btn-lg">Large button</button>
                        <button type="button" class="btn btn-secondary btn-lg">Large button</button>
                        <hr />
                        <button type="button" class="btn btn-primary btn-sm">Small button</button>
                        <button type="button" class="btn btn-secondary btn-sm">Small button</button>
                        <hr />
                        <button type="button" class="btn btn-primary btn-lg btn-block">Block level button</button>
                        <button type="button" class="btn btn-secondary btn-lg btn-block">Block level button</button>
                    </div>
                </div>
                <!-- Group -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Group</h3>
                    </div>
                    <div class="card-body">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button type="button" class="btn btn-secondary">Left</button>
                            <button type="button" class="btn btn-secondary active">Middle</button>
                            <button type="button" class="btn btn-secondary">Right</button>
                        </div>
                        <hr />
                        <div class="btn-group">
                            <button type="button" class="btn btn-info active">1</button>
                            <button type="button" class="btn btn-info">2</button>
                            <button type="button" class="btn btn-info">3</button>
                            <button type="button" class="btn btn-info">4</button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default">5</button>
                            <button type="button" class="btn btn-default">6</button>
                            <button type="button" class="btn btn-default">7</button>
                        </div>
                    </div>
                </div>
                <!-- Social -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Social</h3>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-facebook btn-icon my-2">
                            <span class="btn-inner--icon"><i class="fab fa-facebook"></i></span>
                            <span class="btn-inner--text">Facebook</span>
                        </button>
                        <button type="button" class="btn btn-twitter btn-icon">
                            <span class="btn-inner--icon"><i class="fab fa-twitter"></i></span>
                            <span class="btn-inner--text">Twitter</span>
                        </button>
                        <button type="button" class="btn btn-google-plus btn-icon">
                            <span class="btn-inner--icon"><i class="fab fa-google-plus-g"></i></span>
                            <span class="btn-inner--text">Google +</span>
                        </button>
                        <button type="button" class="btn btn-instagram btn-icon">
                            <span class="btn-inner--icon"><i class="fab fa-instagram"></i></span>
                            <span class="btn-inner--text">Instagram</span>
                        </button>
                        <button type="button" class="btn btn-pinterest btn-icon">
                            <span class="btn-inner--icon"><i class="fab fa-pinterest"></i></span>
                            <span class="btn-inner--text">Pinterest</span>
                        </button>
                        <button type="button" class="btn btn-youtube btn-icon">
                            <span class="btn-inner--icon"><i class="fab fa-youtube"></i></span>
                            <span class="btn-inner--text">Youtube</span>
                        </button>
                        <button type="button" class="btn btn-vimeo btn-icon">
                            <span class="btn-inner--icon"><i class="fab fa-vimeo-v"></i></span>
                            <span class="btn-inner--text">Vimeo</span>
                        </button>
                        <button type="button" class="btn btn-slack btn-icon">
                            <span class="btn-inner--icon"><i class="fab fa-slack"></i></span>
                            <span class="btn-inner--text">Slack</span>
                        </button>
                        <button type="button" class="btn btn-dribbble btn-icon">
                            <span class="btn-inner--icon"><i class="fab fa-dribbble"></i></span>
                            <span class="btn-inner--text">Dribbble</span>
                        </button>
                        <hr />
                        <button type="button" class="btn btn-facebook btn-icon-only">
                            class="fab fa-facebook"></i></span>
                        </button>
                        <button type="button" class="btn btn-twitter btn-icon-only">
                            <span class="btn-inner--icon"><i class="fab fa-twitter"></i></span>
                        </button>
                        <button type="button" class="btn btn-google-plus btn-icon-only">
                            <span class="btn-inner--icon"><i class="fab fa-google-plus-g"></i></span>
                        </button>
                        <button type="button" class="btn btn-instagram btn-icon-only">
                            <span class="btn-inner--icon"><i class="fab fa-instagram"></i></span>
                        </button>
                        <button type="button" class="btn btn-pinterest btn-icon-only">
                            <span class="btn-inner--icon"><i class="fab fa-pinterest"></i></span>
                        </button>
                        <button type="button" class="btn btn-youtube btn-icon-only">
                            <span class="btn-inner--icon"><i class="fab fa-youtube"></i></span>
                        </button>
                        <button type="button" class="btn btn-vimeo btn-icon-only">
                            <span class="btn-inner--icon"><i class="fab fa-vimeo-v"></i></span>
                        </button>
                        <button type="button" class="btn btn-slack btn-icon-only">
                            <span class="btn-inner--icon"><i class="fab fa-slack"></i></span>
                        </button>
                        <button type="button" class="btn btn-dribbble btn-icon-only">
                            <span class="btn-inner--icon"><i class="fab fa-dribbble"></i></span>
                        </button>
                        <hr />
                        <button type="button" class="btn btn-facebook btn-icon-only rounded-circle">
                            <span class="btn-inner--icon"><i class="fab fa-facebook"></i></span>
                        </button>
                        <button type="button" class="btn btn-twitter btn-icon-only rounded-circle">
                            <span class="btn-inner--icon"><i class="fab fa-twitter"></i></span>
                        </button>
                        <button type="button" class="btn btn-google-plus btn-icon-only rounded-circle">
                            <span class="btn-inner--icon"><i class="fab fa-google-plus-g"></i></span>
                        </button>
                        <button type="button" class="btn btn-instagram btn-icon-only rounded-circle">
                            <span class="btn-inner--icon"><i class="fab fa-instagram"></i></span>
                        </button>
                        <button type="button" class="btn btn-pinterest btn-icon-only rounded-circle">
                            <span class="btn-inner--icon"><i class="fab fa-pinterest"></i></span>
                        </button>
                        <button type="button" class="btn btn-youtube btn-icon-only rounded-circle">
                            <span class="btn-inner--icon"><i class="fab fa-youtube"></i></span>
                        </button>
                        <button type="button" class="btn btn-vimeo btn-icon-only rounded-circle">
                            <span class="btn-inner--icon"><i class="fab fa-vimeo-v"></i></span>
                        </button>
                        <button type="button" class="btn btn-slack btn-icon-only rounded-circle">
                            <span class="btn-inner--icon"><i class="fab fa-slack"></i></span>
                        </button>
                        <button type="button" class="btn btn-dribbble btn-icon-only rounded-circle">
                            <span class="btn-inner--icon"><i class="fab fa-dribbble"></i></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        @include('layouts.footers.auth')
    </div>
@endsection
@extends('layouts.app', ['class' => 'bg-default'])

@section('content')
    <div class="header bg-primary py-5 pb-7 pt-lg-9">
        <div class="container">
            <div class="header-body text-center mb-7">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-9 pt-5">
                        <h1 class="text-white">{{ __('Welcome to Argon Dashboard Pro Laravel Live Preview.') }}</h1>
                        <h2 class="text-white">
                            {{ __('Kickstart your Laravel web app like a PRO') }}
                        </h2>

                        <p class="text-lead text-light mt-3 mb-0">
                            {{ __('Log in and see how you can save more than 150 hours of work with CRUDs for managing: #users, #roles, #items, #categories, #tags and more.') }}
                            @include('alerts.migrations_check')
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="separator separator-bottom separator-skew zindex-100">
            <svg x="0" y="0" viewBox="0 0 2560 100" preserveAspectRatio="none" version="1.1" xmlns="http://www.w3.org/2000/svg">
                <polygon class="fill-default" points="2560 0 2560 100 0 100"></polygon>
            </svg>
        </div>
    </div>

    <div class="container mt--10 pb-5"></div>
@endsection

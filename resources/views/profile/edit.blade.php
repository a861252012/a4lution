@extends('layouts.app', [
    'title' => __('User Profile'),
    'navClass' => 'bg-default',
    'parentSection' => 'laravel',
    'elementName' => 'profile'
])

@section('content')
    @include('forms.header')

    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col-xl order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">{{ __('Profile Setting') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('profile.update') }}" autocomplete="off"
                              enctype="multipart/form-data">
                            @csrf
                            @method('put')

                            @include('alerts.success')
                            @include('alerts.error_self_update', ['key' => 'not_allow_profile'])

                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('user_name') ? ' has-danger' : '' }}">
                                    <label class="form-control-label"
                                           for="input-user-name">{{ __('user_name') }}</label>
                                    <input type="text" name="user_name" id="input-user-name"
                                           class="form-control{{ $errors->has('user_name') ? ' is-invalid' : '' }}"
                                           placeholder="{{ __('user_name') }}"
                                           value="{{ old('user_name', auth()->user()->user_name) }}" required disabled>

                                    @include('alerts.feedback', ['field' => 'user_name'])
                                </div>
                                <div class="form-group{{ $errors->has('full_name') ? ' has-danger' : '' }}">
                                    <label class="form-control-label"
                                           for="input-full-name">{{ __('full_name') }}</label>
                                    <input type="text" name="full_name" id="input-full-name" maxlength="20"
                                           class="form-control{{ $errors->has('full_name') ? ' is-invalid' : '' }}"
                                           placeholder="{{ __('full_name') }}"
                                           value="{{ old('full_name', auth()->user()->full_name) }}" required>

                                    @include('alerts.feedback', ['field' => 'full_name'])
                                </div>
                                <div class="form-group{{ $errors->has('company_name') ? ' has-danger' : '' }}">
                                    <label class="form-control-label required"
                                           for="input-company-name">{{ __('company_name') }}</label>
                                    <input type="text" name="company_name" id="input-company-name"
                                           class="form-control{{ $errors->has('company_name') ? ' is-invalid' : '' }}"
                                           placeholder="{{ __('company_name') }}" maxlength="30"
                                           value="{{ old('company_name', auth()->user()->company_name) }}" required>

                                    @include('alerts.feedback', ['field' => 'company_name'])
                                </div>
                                <div class="form-group{{ $errors->has('address') ? ' has-danger' : '' }}">
                                    <label class="form-control-label required"
                                           for="input-address">{{ __('address') }}</label>
                                    <input type="text" name="address" id="input-address"
                                           class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}"
                                           placeholder="{{ __('address') }}" maxlength="40"
                                           value="{{ old('address', auth()->user()->address) }}" required>

                                    @include('alerts.feedback', ['field' => 'address'])
                                </div>
                                <div class="form-group{{ $errors->has('phone_number') ? ' has-danger' : '' }}">
                                    <label class="form-control-label required"
                                           for="input-phone-number">{{ __('phone_number') }}</label>
                                    <input type="tel" name="phone_number" id="input-phone-number" minlength="6"
                                           maxlength="20" pattern="[0-9]+"
                                           class="form-control{{ $errors->has('phone_number') ? ' is-invalid' : '' }}"
                                           placeholder="{{ __('phone_number') }}"
                                           value="{{ old('phone_number', auth()->user()->phone_number) }}" required>

                                    @include('alerts.feedback', ['field' => 'phone_number'])
                                </div>
                                <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                                    <label class="form-control-label required" for="input-email">{{ __('Email') }}</label>
                                    <input type="email" name="email" id="input-email"
                                           class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                           placeholder="{{ __('Email') }}"
                                           value="{{ old('email', auth()->user()->email) }}" required>

                                    @include('alerts.feedback', ['field' => 'email'])
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('update') }}</button>
                                </div>
                            </div>
                        </form>
                        <hr class="my-4"/>
                        <form method="post" action="{{ route('profile.password') }}" autocomplete="off">
                            @csrf
                            @method('put')

                            <h6 class="heading-small text-muted mb-4">{{ __('Password') }}</h6>

                            @include('alerts.success', ['key' => 'password_status'])
                            @include('alerts.error_self_update', ['key' => 'not_allow_password'])

                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('old_password') ? ' has-danger' : '' }}">
                                    <label class="form-control-label required"
                                           for="input-current-password">{{ __('Current Password') }}</label>
                                    <input type="password" name="old_password" id="input-current-password" minlength="6"
                                           maxlength="20"
                                           class="form-control{{ $errors->has('old_password') ? ' is-invalid' : '' }}"
                                           placeholder="{{ __('Current Password') }}" value="" required>

                                    @include('alerts.feedback', ['field' => 'old_password'])
                                </div>
                                <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                    <label class="form-control-label required"
                                           for="input-password">{{ __('New Password') }}</label>
                                    <input type="password" name="password" id="input-password" minlength="6"
                                           maxlength="20"
                                           class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                           placeholder="{{ __('New Password') }}" value="" required>

                                    @include('alerts.feedback', ['field' => 'password'])
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label required"
                                           for="input-password-confirmation">{{ __('Confirm New Password') }}</label>
                                    <input type="password" name="password_confirmation" id="input-password-confirmation"
                                           class="form-control" placeholder="{{ __('Confirm New Password') }}" value=""
                                           minlength="6" maxlength="20" required>
                                </div>

                                <div class="text-center">
                                    <button type="submit"
                                            class="btn btn-success mt-4">{{ __('Change password') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

{{--        @include('layouts.footers.auth')--}}
    </div>
@endsection

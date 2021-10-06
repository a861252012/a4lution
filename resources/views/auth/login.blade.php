@extends('layouts.app', ['class' => 'bg-default'])

@section('content')
    @include('layouts.headers.guest')

    <div class="container mt--8 pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-body px-lg-5 py-lg-5">
                        <div class="text-center text-muted mb-4"><h1>Login</h1></div>
                        <form role="form" method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group{{ $errors->has('user_name') ? ' has-danger' : '' }} mb-3">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-key-25"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('user_name') ? ' is-invalid' : '' }}"
                                           placeholder="{{ __('user_name') }}" type="text" name="user_name"
                                           maxlength="20" value="{{ old('user_name') }}" required autofocus>
                                </div>
                                @if ($errors->has('user_name'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('user_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            @if (session('alert'))
                                <div class="alert alert-danger" role="alert">
                                    <span class="alert-icon"><i class="ni ni-fat-remove"></i></span>
                                    <span class="alert-text"><strong>{{ session('alert') }}</strong></span>
                                </div>
                            @endif
                            <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                           name="password" placeholder="{{ __('Password') }}" type="password"
                                           maxlength="20" value="" required>
                                </div>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                            {{--                            <div class="custom-control custom-control-alternative custom-checkbox">--}}
                            {{--                                <input class="custom-control-input" name="remember" id="customCheckLogin"--}}
                            {{--                                       type="checkbox" {{ old('remember') ? 'checked' : '' }}>--}}
                            {{--                                <label class="custom-control-label" for="customCheckLogin">--}}
                            {{--                                    <span class="text-muted">{{ __('Remember me') }}</span>--}}
                            {{--                                </label>--}}
                            {{--                            </div>--}}
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary my-4">{{ __('Sign in') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

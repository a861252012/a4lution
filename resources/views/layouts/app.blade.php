{{--

=========================================================
* Argon Dashboard PRO - v1.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard-pro-laravel
* Copyright 2018 Creative Tim (https://www.creative-tim.com) & UPDIVISION (https://www.updivision.com)

* Coded by www.creative-tim.com & www.updivision.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

--}}
        <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title itemprop="name">{{ $metaTitle ?? 'A4lution Admin' }}</title>

    <!-- Favicon -->
{{--    <link href="{{ asset('argon') }}/img/brand/favicon.png" rel="icon" type="image/png">--}}
    <link href="{{ asset('pictures') }}/A4lution_logo.png" rel="icon" type="image/png">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link href="{{ asset('argon') }}/vendor/nucleo/css/nucleo.css" rel="stylesheet">
    <link href="{{ asset('argon') }}/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">

    <!-- dataTables css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.1/css/jquery.dataTables.css">

    <!-- Argon CSS -->
    <link type="text/css" href="{{ asset('css') }}/argon.css?v=2.0.0" rel="stylesheet">
    <link type="text/css" href="{{ asset('argon') }}/vendor/colorbox/css/colorbox.css" rel="stylesheet">

    <!-- A4 custom CSS -->
    <link type="text/css" href="{{ asset('css/a4lution.css') }}" rel="stylesheet">
    @stack('css')

</head>
<body class="{{ $class ?? '' }}">
@auth()
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    @if (!in_array(request()->route()->getName(), ['welcome', 'page.pricing', 'page.lock']))
        @include('layouts.navbars.sidebar')
    @endif
@endauth

<div class="main-content">
    @include('layouts.navbars.navbar')
    @yield('content')
</div>

{{--        @if(!auth()->check() || in_array(request()->route()->getName(), ['welcome', 'page.pricing', 'page.lock']))--}}
{{--            @include('layouts.footers.guest')--}}
{{--        @endif--}}

<!-- sweetalert JS -->
<script src="{{ asset('js') }}/sweetalert.min.js"></script>

<script src="{{ asset('argon') }}/vendor/jquery/dist/jquery-1.9.1.js"></script>
<script src="{{ asset('argon') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('argon') }}/vendor/js-cookie/js.cookie.js"></script>
<script src="{{ asset('argon') }}/vendor/jquery.scrollbar/jquery.scrollbar.min.js"></script>
<script src="{{ asset('argon') }}/vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js"></script>

<!-- Optional JS -->
<script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.min.js"></script>
<script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.extension.js"></script>

<!-- jquery colorbox JS -->
<script src="{{ asset('argon') }}/vendor/colorbox/js/jquery.colorbox.js"></script>

<!-- select2 JS -->
{{--<script src="{{ asset('argon') }}/vendor/select2/dist/js/select2.min.js"></script>--}}

<!-- Argon JS -->
<script src="{{ asset('argon') }}/js/argon.js?v=1.0.1"></script>
<script src="{{ asset('argon') }}/js/demo.min.js"></script>

<script src="{{ asset('argon') }}/js/jquery-ui_1.11.1.js"></script>
<script src="{{ asset('argon') }}/js/jquery-migrate-1.4.0.js"></script>

<!-- jquery.form JS -->
<script src="{{ asset('argon') }}/js/jquery.form_4.3.0.js"></script>

<script src="{{ asset('argon') }}/vendor/select2/dist/js/select2.min.js"></script>
<script src="{{ asset('argon') }}/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('argon') }}/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<!-- dataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.1/js/jquery.dataTables.js"></script>

<!-- jquery-validation JS -->
<script src="{{ asset('js') }}/jquery-validation_1.19.3.js"></script>

<!-- moment JS -->
<script src="{{ asset('js') }}/momentJS.js"></script>
<!-- Moment Timezone JS(如有使用到時區就要額外include) -->
{{--<script src="{{ asset('js') }}/moment-timezone-with-data.js"></script>--}}
<script>
    // 顯示 validation errors
    @if ($errors->count())
        let errors = [];
        @foreach ($errors->all() as $error)
            errors.push('{{ $error }}');
        @endforeach

        swal({
            icon: 'error',
            text: errors.join("\n")
        });
    @endif
</script>
@stack('js')
</body>
</html>

<style>
    .required:after {
        content: " *";
        color: red;
    }
</style>
@stack('css-styles')

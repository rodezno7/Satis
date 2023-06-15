<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/img/default/iso-satis.png"/>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <title>@yield('title') - {{ config('app.name', 'POS') }}</title>

    <link rel="preload" href="{{ asset('fonts/Roboto/Roboto-Regular.ttf') }}" as="font" type="font/ttf" crossorigin>

    <link href="{{ asset('css/login/login.style.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="{{ asset('css/login/jquery.min.js') }}"></script>
    <script src="{{ asset('css/login/bootstrap.min.js') }}"></script>
    <script src="{{ asset('css/login/jquery-3.3.1.min.js') }}"></script>
</head>
<body class="theme-2">
    <div class="auth-wrapper auth-v3">
        @yield('content')
    </div>
</body>
</html>
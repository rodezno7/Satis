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

    <link href="{{ asset('css/login/Style.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/login/bootstrap.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('css/login/jquery.min.js') }}"></script>
    <script src="{{ asset('css/login/bootstrap.min.js') }}"></script>
    <script src="{{ asset('css/login/jquery-3.3.1.min.js') }}"></script>
</head>
<body class="bodyImg">
    <div>
        
    </div>
</body>
</html>
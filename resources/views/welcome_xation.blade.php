<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="/img/ISOTIPO.png" />
    <title>{{ config('app.name', 'Envex ERP') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,300,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('bootstrap/css/custumize.css') }}">
    <!-- Styles -->
    <style>
        html,
        body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
            background-color: #ffffff;
            background-image: url("img/Transparent-background-with-dots.png");
            overflow-y: hidden;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links>a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        .tagline {
            font-size: 25px;
            font-weight: 300;
        }

        .portada {
            background-image: fixed center;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            height: 80%;
            width: 80%;
            text-align: center;

        }

    </style>
</head>

<body>
    <div class="flex-center position-ref full-height">
        <div class="top-right links">

            @if (Route::has('login'))
                @if (Auth::check())
                    <a href="{{ action('HomeController@index') }}">@lang('home.home')</a>
                @else
                    <a href="{{ action('Auth\LoginController@login') }}"
                        style="color: #024c88">@lang('home.login')</a>
                    @if (env('ALLOW_REGISTRATION', true))
                        <a href="{{ route('business.getRegister') }}">@lang('home.register')</a>
                    @endif
                @endif
            @endif

            @if (Route::has('pricing') && config('app.env') != 'demo')
                <a
                    href="{{ action('\Modules\Superadmin\Http\Controllers\PricingController@index') }}">@lang('superadmin::lang.pricing')</a>
            @endif
        </div>

        <div class="content">
            <div class="row">
                <div>
                    <image class="portada" src="{{ asset('/img/xation-logo2.png') }}">
                </div>
            </div>

            {{-- <p class="tagline">
                Desarrollado por: <a href="http://blsolutionsv.com/" target="_blank">BL Solutions S.A de C.V</a>
                </p> --}}
        </div>
    </div>
</body>

</html>

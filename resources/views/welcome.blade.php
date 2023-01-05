<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/img/default/iso-satis.png"/>
        <title>{{ config('app.name', 'SATIS ERP') }}</title>
        @include('layouts.partials.css')
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,300,600" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="{{ asset('bootstrap/css/custumize.css') }}">
        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #fff;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
                background-color: #fff;
                background-image: url("img/default/satis_background.jpg");
                background-position: center; /* Center the image */
                background-repeat: no-repeat; /* Do not repeat the image */
                background-size: cover; /* Resize the background image to cover the entire container */
                overflow-y:hidden;
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

            .links > a {
                color: ##0057E1;
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

            .tagline{
                font-size:25px;
                font-weight: 300;
            }

            .portada{
                    background-image:fixed center;
                    -webkit-background-size: cover;
                    -moz-background-size: cover;
                    -o-background-size: cover;
                    background-size: cover;
                    height:40%;
                    width: 60%;
                    text-align: center;
                    border: 0;
                }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="top-right links">

                @if (Route::has('login'))
                    @if (Auth::check())
                        <a href="{{ action('HomeController@index') }}" style="color: #0057E1; font-weight: bold; text-decoration: underline;">@lang('home.home')</a>
                    @else
                        <a href="{{ action('Auth\LoginController@login') }}" style="color: #0057E1; font-weight: bold; text-decoration: underline;">@lang('home.login')</a>
                    @endif
                @endif
            </div>

            <div class="content">
                <div class="row">
                     <div  >
                        <img class="portada" src="{{ asset('/img/default/satis.png') }}">
                     </div>
                </div>
            </div>
        </div>
    </body>
</html>

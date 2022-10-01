@inject('request', 'Illuminate\Http\Request')

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr'}}">
    <head>
        <link rel="icon" type="image/png" href="/img/ISOTIPO.png"/>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title') - {{ Session::get('business.name') }}</title>
        
        @include('layouts.partials.css')

        {!! Charts::styles(['highcharts']) !!}
    </head>

    <body>
        {!! $sells_chart_3->html() !!}
        
        @include('layouts.partials.javascripts')
        <script src="{{ asset('js/home.js?v=' . $asset_v) }}"></script>
        <script src="{{ asset('plugins\chart\highchart\highcharts.js?v=' . $asset_v) }}"></script>
        <!-- {!! Charts::assets(['highcharts']) !!} -->
        {!! $sells_chart_3->script() !!}
    </body>

</html>
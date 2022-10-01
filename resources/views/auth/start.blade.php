<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'POS') }}</title> 

    @include('layouts.partials.css')

    <!-- Jquery Steps -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/pace/pace.css?v='.$asset_v) }}">
    <link rel="stylesheet" href="{{ asset('plugins/jquery.steps/jquery.steps.css?v=' . $asset_v) }}">
</head>
<body class="bg hold-transition register-page">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 style="font-family: Poppins-Regular;">
            {{ __('home.welcome_message', ['name' => Auth::User()->first_name . "&nbsp;" .Auth::User()->last_name]) }}
        </h1>

    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-3">
            </div>
            <div class="col-sm-6">
                {!! Form::open(['url' => action('UserController@updatePasswordFirst'), 'method' => 'post', 'id' => 'edit_password_form',
                'class' => 'form-horizontal' ]) !!}
                <div class="boxform_u box-solid_u">
                    <!--business info box start-->
                    <div class="box-header">
                        <div class="box-header">
                            <h3 class="box-title"> @lang('user.change_password_first')</h3>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            {!! Form::label('current_password', __('user.current_password') . ':', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <div class="wrap-inputform">
                                    <span class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                    {!! Form::password('current_password', ['class' => 'inputform','placeholder' => __('user.current_password'), 'required']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('new_password', __('user.new_password') . ':', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <div class="wrap-inputform">
                                    <span class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                    {!! Form::password('new_password', ['class' => 'inputform','placeholder' => __('user.new_password'), 'required']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('confirm_password', __('user.confirm_new_password') . ':', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <div class="wrap-inputform">
                                    <span class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                    {!! Form::password('confirm_password', ['class' => 'inputform','placeholder' => __('user.confirm_new_password'), 'required']); !!}
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right">@lang('messages.update')</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
            <div class="col-sm-6">
            </div>
        </div>
    </section>
    <!-- /.content -->
    @include('layouts.partials.javascripts')
    <script src="{{ asset('plugins/jquery.steps/jquery.steps.min.js?v=' . $asset_v) }}"></script>
    <!-- Scripts -->
    <script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>    
</body>
</html>
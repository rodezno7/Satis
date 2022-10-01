@extends('layouts.app')
@section('title', __('lang_v1.my_profile'))
<!-- <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 style="font-family: Poppins-Regular;">
        {{ __('home.hi', ['name' => Auth::User()->first_name]) }}
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- <div class="container bootstrap snippet">
    <div class="row">
    <div class="col-sm-3">
        <div class="text-center">
        <img src="http://ssl.gstatic.com/accounts/ui/avatar_2x.png" class="avatar img-circle img-thumbnail" alt="avatar">
        <h6>Upload a different photo...</h6>
        <input type="file" class="text-center center-block file-upload">
        </div>
    </div>
    </div>
</div> -->

<!-- Main content -->
<section class="content">
    
    <div class="row">
        <div class="col-sm-6">
            {!! Form::open(['url' => action('UserController@updateProfile'), 'method' => 'post', 'id' => 'edit_user_profile_form',
            'class' => 'form-horizontal', 'files' => true ]) !!}
            <div class="boxform_u box-solid_u">
                <!--business info box start-->
                <div class="box-header">
                    <div class="box-header">
                        <h3 style="font-family: Poppins-Regular;" class="box-title"> @lang('user.edit_profile')</h3>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        {!! Form::label('first_name', __('business.first_name') . ':', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">
                            <div class="wrap-inputform">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                {!! Form::text('first_name', $user->first_name, ['class' => 'inputform','placeholder' => __('business.first_name'), 'required']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('last_name', __('business.last_name') . ':', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">
                            <div class="wrap-inputform">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                {!! Form::text('last_name', $user->last_name, ['class' => 'inputform','placeholder' => __('business.last_name')]); !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('email', __('business.email') . ':', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">
                            <div class="wrap-inputform">
                                <span class="input-group-addon">
                                    <i class="fa fa-at"></i>
                                </span>
                                {!! Form::email('email', $user->email, ['class' => 'inputform','placeholder' => __('business.email') ]); !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('language', __('business.language') . ':', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">
                            <div class="wrap-inputform">
                                <span class="input-group-addon">
                                    <i class="fa fa-language"></i>
                                </span>
                                {!! Form::select('language',$languages, $user->language, ['class' => 'inputform select90']); !!}
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary pull-right">@lang('messages.update')</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="col-sm-6">
            {!! Form::open(['url' => action('UserController@updatePassword'), 'method' => 'post', 'id' => 'edit_password_form',
            'class' => 'form-horizontal' ]) !!}
            <div class="boxform_u box-solid_u">
                <!--business info box start-->
                <div class="box-header">
                    <div class="box-header">
                        <h3 class="box-title"> @lang('user.change_password')</h3>
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
    </div>

</section>
<!-- /.content -->

@endsection

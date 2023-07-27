@extends('layouts.login')
@section('title', __('lang_v1.login'))

@section('content')
<div class="auth-content">
    <div class="card border-0">
        <div class="row align-items-center text-start">
            <div class="col-xl-6">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="../img/default/satis.png" alt="" class="img-fluid logo" />
                    </div>
                    <form method="POST" action="{{ route('login') }}" autocomplete="off">
                        {{ csrf_field() }}
                        <br>
                        <div class="form-group mb-3">
                            <label class="form-label" for="username">@lang('lang_v1.username')</label>
                            <div data-validate="Username is required">
                                @php
                                $username = old('username');
                                $password = null;
                                @endphp
                                <input id="username" type="text" class="form-control" autocomplete="off" name="username" value="{{ $username }}" required autofocus>
                            </div>
                            @if ($errors->has('username'))
                                <div class="d-block invalid-feedback" role="alert">{{ $errors->first('username') }}</div>
                            @endif
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label" for="password">@lang('lang_v1.password')</label>
                            <div class="form-group" data-validate="Password is required">
                                <input id="password" type="password" class="form-control" autocomplete="off" name="password" value="{{ $password }}" required>
                            </div>
                            <div class="wrap-login100">
                                @if ($errors->has('password'))
                                <div class="invalid-feedback" role="alert">{{ $errors->first('password') }}</div>
                                @endif
                            </div>
                            @if ($business->count() > 1)    
                            <label for="business_id">@lang('business.business')</label>
                            <div data-validate="Business is required">
                                {!! Form::select('business_id', $business, '', [
                                    'class' => 'form-control',
                                    'placeholder' => __('messages.please_select'),
                                    'required']) !!}
                            </div>
                            @else
                            {!! Form::hidden('business_id', $business->keys()->first()) !!}
                            @endif
                        </div>
                        <div class="form-group mb-4">
                            <a class="txt-xs" href="{{ route('password.request') }}">
                                @lang('lang_v1.forgot_your_password')
                            </a>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Acceder</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-xl-6 auth-img-content">
                <img src="../img/erp_login_img.png" alt="" class="img-fluid" />
                <!--div class="auth-img-content">
                    <h3 class="text-white mb-4 mt-5">Attention is the new currency</h3>
                    <p class="text-white">The more effortless the writing looks, the more effort the
                        writer actually put into the process.</p>
                </div-->
            </div>
        </div>
    </div>
    <div class="auth-footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <p>&copy; Copyright Satis ERP {{ date('Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

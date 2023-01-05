@extends('layouts.login')
@section('title', __('lang_v1.login'))

@section('content')


    <div class="wrapper fadeInDown">
        <div id="formContent">
            <img src="../img/default/satis.png" style="width:60%;margin-bottom: 15px; margin-top: 15px"
                alt="SATIS ERP Logo" />
        </div>
        <div id="formContent">
            <div style="background-color:rgba(19,20,21,0.80); align-items:baseline;padding:4px">
                <!-- Formulario de Login -->
                <form method="POST" action="{{ route('login') }}" autocomplete="off">
                    {{ csrf_field() }}
                    <br>
                    <label for="username" style="color:#F7F7F9;font-size:18px">@lang('lang_v1.username')</label>
                    <div data-validate="Username is required">
                        @php
                        $username = old('username');
                        $password = null;
                        @endphp
                        <input id="username" type="text" class="fadeIn second center-block" autocomplete="off"
                            style="border-radius: 30px 30px 30px 30px; color: #F7F7F9" name="username" value="{{ $username }}" required
                            autofocus>
                    </div>
                    <div>
                        @if ($errors->has('username'))
                            <script>
                                swal({
                                    title: "¡Error!",
                                    text: "{{ $errors->first('username') }}",
                                    icon: "error"
                                });

                            </script>
                        @endif
                    </div>
                    <label for="password" style="color:#F7F7F9; font-size:18px">@lang('lang_v1.password')</label>
                    <div data-validate="Password is required">
                        <input id="password" type="password" class="fadeIn second center-block" autocomplete="off"
                            style="border-radius: 30px 30px 30px 30px; color: #F7F7F9" name="password" value="{{ $password }}" required>
                    </div>
                    <div class="wrap-login100">
                        @if ($errors->has('password'))
                            <script>
                                swal({
                                    title: "¡Error!",
                                    text: "{{ $errors->first('password') }}",
                                    icon: "error"
                                });

                            </script>
                        @endif
                    </div>
                    <br>

                    @if ($business->count() > 1)    
                    <label for="business_id" style="color:#F7F7F9; font-size:18px">@lang('business.business')</label>
                    <div data-validate="Business is required">
                        {!! Form::select('business_id', $business, '', [
                            'class' => 'fadeIn second center-block',
                            'style' => 'border-radius: 30px 30px 30px 30px; color: #dce8f1;',
                            'placeholder' => __('messages.please_select'),
                            'required']) !!}
                    </div>
                    <br>
                    @else
                    {!! Form::hidden('business_id', $business->keys()->first()) !!}
                    @endif

                    <input type="submit" class="fadeIn fourth"  value="Acceder" style="border-radius: 30px 30px 30px 30px; margin-bottom: 0px; padding-top: 10px; padding-bottom: 10px; font-size: 16px;" />
                    <a class="btn btn-link txt1" style="font-size: 18px; color: #F7F7F9" href="{{ route('password.request') }}">
                        @lang('lang_v1.forgot_your_password')
                    </a>
                </form>
            </div>
        </div>
    </div>

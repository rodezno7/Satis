@extends('layouts.auth')

@section('title', __('lang_v1.reset_password'))

@section('content')



<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100">
            @if (session('status'))

            <script>
                debugger
                swal({
                    title: "¡Enviado!",
                    text: "{{ session('status') }}",
                    icon: "success"
                });
            </script>

            <!-- <div class="alert alert-success">
                
                {{ session('status') }}
            </div> -->
            @endif
            <form class="login100-form validate-form" method="POST" action="{{ route('password.email') }}">
                {{ csrf_field() }}
                <br>
                <img src="../img/SatisPoint.png" id="logo">
                <h2 style="text-align: center">@lang('lang_v1.reset_password')</h2>
                <label class="txt3">@lang('lang_v1.reset_password_directions')</label><br><br>
                <label for="email" class="txt1">@lang('lang_v1.email_address')</label>
                <div class="wrap-input100 validate-input" data-validate="Email is required">
                    <input id="email" type="email" class="input100" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="wrap-login100">
                    @if ($errors->has('email'))
                    <script>
                        swal({
                            title: "¡Error!",
                            text: "{{ $errors->first('email') }}",
                            icon: "error"
                        });
                    </script>
                    @endif
                </div>
                <div class="container-login100-form-btn m-t-17">
                    <button id="enviarbtn" class="login100-form-btn"> @lang('lang_v1.send_password_reset_link')</button><br>
                </div><br>
            </form>
        </div>
    </div>
</div>
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        $('#change_lang').change(function() {
            window.location = "{{ route('password.request') }}?lang=" + $(this).val();
        });

        // $("#enviarbtn").click(function() {
        //     debugger;

        //     enviadolink();
        // });

        function enviadolink(value) {
            debugger;
            swal({
                title: "¡ERROR!",
                text: "Esto es un mensaje de exito",
                type: "success",
            });

            // Swal.fire(
            //     'Link Enviado',
            //     'Enviado',
            //     'success');
        };

    });
</script>
@endsection
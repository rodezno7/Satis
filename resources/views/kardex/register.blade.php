@extends('layouts.app')

@section('title', __('kardex.kardex'))

@section('content')
<style>
    ol.order ul,
    ol.order li {
        margin-left: 25px;
        list-style-type: decimal;
    }
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('kardex.kardex')</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang('kardex.generate_kardex')</h3>
        </div>
        
        <div class="box-body">
            <div class="row">
                <div class="col-xs-12">
                    <p>@lang('kardex.indications'):</p>
                    <ol class="order">
                        <li>@lang('kardex.line_1')</li>
                        <li>@lang('kardex.line_2')</li>
                        <li>@lang('kardex.line_3')</li>
                    </ol>
                    <br>
                    <input type="button" class="btn btn-primary" value="@lang('kardex.start')" id="start">
                    <div id="loading" style="display: none; margin-left: 5px;">
                        <img src="{{ asset('img/loader.gif') }}" alt="loading" />
                        @lang('accounting.wait_please')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
@endsection

@section('javascript')
<script>
    $("#start").click(function() {
        Swal.fire({
            title: LANG.sure,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: LANG.yes,
            cancelButtonText: LANG.not,
        }).then((resul) => {
            if (resul.isConfirmed) {
                $('#loading').css('display', 'inline-block');
                $('#start').attr('disabled', true);
                $.ajax({
                    method: "POST",
                    url: '/post-register-kardex',
                    success: function(result) {
                        if (result.success === true) {
                            Swal.fire({
                                title: ""+result.msg+"",
                                icon: "success",
                            });
                        } else {
                            Swal.fire({
                                title: ""+result.msg+"",
                                icon: "error",
                            });
                        }
                        $('#loading').css('display', 'none');
                        $('#start').attr('disabled', false);
                    }
                });
            }
        });
    });
</script>
@endsection
@extends('layouts.app')
@section('title', __('rrhh.settings'))
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('rrhh.settings_module')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="boxform_u box-solid_u">
            {!! Form::open([
                'url' => action('RrhhSettingController@store'),
                'method' => 'post',
                'id' => 'form_add',
                'files' => true,
            ]) !!}
            <div class="box-body">
                <label for="">CONTROL DE ASISTENCIAS</label>
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-6">
                        <div class="form-group">
                            <br>
                            <div class="checkbox">
                                <label for="automatic_closing">
                                    {!! Form::checkbox('automatic_closing', 1, ($setting)? $setting->automatic_closing : null, ['id' => 'automatic_closing', 'onClick' => 'enableExitTime()']) !!} 
                                    <strong>@lang('rrhh.automatic_closing')</strong> @show_tooltip(__("rrhh.message_automatic_closing"))
                                </label>
                            </div>
                            <br>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label>@lang('rrhh.exit_time')</label> <span class="text-danger">*</span> 
                            {!! Form::text('exit_time', ($setting)? $setting->exit_time : null, ['class' => 'form-control form-control-sm', 
                                'placeholder' => __('rrhh.exit_time'),'id' => 'exit_time']) !!}
                        </div>
                    </div>
                </div>
                <label for="">CÁLCULO DE AGUINALDO</label>
                <div class="row">
                    {{-- <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label>Fecha de fin </label> <span class="text-danger">*</span> @show_tooltip('El cálculo del aguinaldo se realizar hasta el día X de diciembre del presente año.')
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar-times-o"></i>
                                </span>
                                {!! Form::number('stock_expiry_alert_days', null, ['class' => 'form-control','required']); !!}
                                <span class="input-group-addon">
                                    Diciembre
                                </span>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label>@lang('payroll.exempt_bonus')</label> <span class="text-danger">*</span> @show_tooltip(__('payroll.message_bonus'))
                            {!! Form::number('exempt_bonus', ($setting)? $setting->exempt_bonus : null, ['class' => 'form-control form-control-sm', 
                                'placeholder' => __('payroll.exempt_bonus'),'id' => 'exempt_bonus']) !!}
                        </div>
                    </div>
                </div>
            {{-- <hr>
                <label for="">HORAS EXTRAS</label>
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label>Hora extra diurna</label> <span class="text-danger">*</span>
                            {!! Form::text('time', 2, ['class' => 'form-control form-control-sm', 
                                'placeholder' => __('rrhh.exit_time'),'id' => 'time']) !!}
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label>Hora extra nocturna</label> <span class="text-danger">*</span>
                            {!! Form::text('time', 2.5, ['class' => 'form-control form-control-sm', 
                                'placeholder' => __('rrhh.exit_time'),'id' => 'time']) !!}
                        </div>
                    </div>
                </div> --}}
            </div>
            <div class="box-footer text-right">
                <button type="submit" class="btn btn-primary" id="btn_edit_item">@lang('rrhh.save')</button>
    
                <a href="{!!URL::to('/home')!!}">
                    <button id="cancel_product" type="button" class="btn btn-danger">@lang('messages.cancel')</button>
                </a>
            </div>
            {!! Form::close() !!}
        </div>
    </section>
@endsection

@section('javascript')
    <script>
        $(function() {
            $('input#exit_time').datetimepicker({
                format: moment_time_format,
                ignoreReadonly: true
            });
            enableExitTime();
        });


        function enableExitTime() {
            if ($("#automatic_closing").is(":checked")) {
                $("#automatic_closing").val('1');
                $("#exit_time").prop('required', true);
                $("#exit_time").prop('disabled', false);
            } else {
                $("#automatic_closing").val('0');
                $("#exit_time").prop('required', false);
                $("#exit_time").prop('disabled', true);
            }
        }
    </script>
@endsection

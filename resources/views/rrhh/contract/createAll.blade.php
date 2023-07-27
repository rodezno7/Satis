@extends('layouts.app')
@section('title', __('rrhh.massive_contract'))
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang('rrhh.add') {{ strtolower(__('rrhh.massive_contract')) }}</h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="boxform_u box-solid_u">
    {!! Form::open(['url' => action('RrhhContractController@storeMassive'), 'method' => 'post', 'id' => 'form_add']) !!}
    <div class="box-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-secondary" role="alert" style="background: #CFE2FF">
                    <h4 class="alert-heading">{{ __('rrhh.important') }}</h4>
                    <p class="mb-0">{{ __('rrhh.alert_massive_contract') }}</p>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                    <label>@lang('rrhh.employee')</label> @show_tooltip(__('rrhh.employee_massive_contract'))
                    <select name="employees[]" id="employees" class="form-control form-control-sm select2"
                        style="width: 100%;" multiple="multiple">
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            {{-- <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label>@lang('rrhh.types_contracts')</label> <span class="text-danger">*</span>
                    <select name="rrhh_type_contract_id" id="rrhh_type_contract_id"
                        class="form-control form-control-sm select2" placeholder="{{ __('rrhh.types_contracts') }}"
                        style="width: 100%;">
                        <option value="">{{ __('rrhh.types_contracts') }}</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div> --}}
    
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <div class="form-group">
                    <label>@lang('rrhh.start_date')</label> <span class="text-danger">*</span>
                    {!! Form::text('contract_start_date', null, ['class' => 'form-control form-control-sm', 'id' => 'contract_start_date', 'required']) !!}
                </div>
            </div>
    
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <div class="form-group">
                    <label>@lang('rrhh.end_date')</label> <span class="text-danger">*</span>
                    {!! Form::text('contract_end_date', null, ['class' => 'form-control form-control-sm', 'id' => 'contract_end_date', 'required']) !!}
                </div>
            </div>
        </div>
        
    </div>
    <div class="box-footer text-right">
        <button type="submit" class="btn btn-primary" id="btn_edit_item">@lang('rrhh.add')</button>

        <a href="{!!URL::to('/rrhh-employees')!!}">
            <button id="cancel_product" type="button" class="btn btn-danger">@lang('messages.cancel')</button>
        </a>
    </div>
    {!! Form::close() !!}
</section>
@endsection
@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        $('#employees').select2({
            placeholder: LANG.all,
        });

        var fechaMaxima = new Date();
        fechaMaxima = fechaMaxima.toLocaleDateString("es-ES", {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });

        var fechaMinima = new Date();
        fechaMinima.setFullYear(fechaMinima.getFullYear() - 50);
        fechaMinima = fechaMinima.toLocaleDateString("es-ES", {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });

        $('#contract_start_date').datepicker({
            autoclose: true,
            format: datepicker_date_format,
            startDate: fechaMinima,
        });

        $("#contract_start_date").datepicker("setDate", fechaMaxima);


        $('#contract_end_date').datepicker({
            autoclose: true,
            format: datepicker_date_format,
            startDate: fechaMinima,
        });

        $("#contract_end_date").datepicker("setDate", fechaMaxima);
    });
</script>
@endsection
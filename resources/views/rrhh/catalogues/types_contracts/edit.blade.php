@extends('layouts.app')
@section('title', __('rrhh.type_contract'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('rrhh.edit') {{ mb_strtolower(__('rrhh.type_contract')) }}</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="boxform_u box-solid_u">
            <div class="box-body">
                {!! Form::model($type, ['url' => action('RrhhTypeContractController@update', $type->id), 'method' => 'patch',
                'id' => 'form_edit', 'files' => true]) !!}
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label>@lang('rrhh.name')</label> <span class="text-danger">*</span>
                            {!! Form::text('name', $type->name, [
                                'class' => 'form-control form-control-sm',
                                'placeholder' => __('rrhh.name'),
                                'id' => 'name',
                                'required',
                            ]) !!}
                        </div>
                    </div>  
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label>@lang('rrhh.status')</label>
                            {!! Form::select('status', [1 => __('rrhh.active'), 2 => __('rrhh.inactive')], $type->status, ['class' => 'form-control select2', 'id' => 'status', 'required', 'style' => 'width: 100%;' ]) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <h4 for="">Configurando márgenes del contrato</h4>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label>@lang('rrhh.margin_top')</label> <span class="text-danger">*</span>
                            {!! Form::number('margin_top', $type->margin_top, [
                                'class' => 'form-control form-control-sm',
                                'placeholder' => __('rrhh.margin_top'),
                                'id' => 'margin_top',
                                'step' => '0.01',
                                'min' => '0.01',
                                'required',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label>@lang('rrhh.margin_bottom')</label> <span class="text-danger">*</span>
                            {!! Form::number('margin_bottom', $type->margin_bottom, [
                                'class' => 'form-control form-control-sm',
                                'placeholder' => __('rrhh.margin_bottom'),
                                'id' => 'margin_bottom',
                                'step' => '0.01',
                                'min' => '0.01',
                                'required',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label>@lang('rrhh.margin_left')</label> <span class="text-danger">*</span>
                            {!! Form::number('margin_left', $type->margin_left, [
                                'class' => 'form-control form-control-sm',
                                'placeholder' => __('rrhh.margin_left'),
                                'id' => 'margin_left',
                                'step' => '0.01',
                                'min' => '0.01',
                                'required',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label>@lang('rrhh.margin_right')</label> <span class="text-danger">*</span>
                            {!! Form::number('margin_right', $type->margin_right, [
                                'class' => 'form-control form-control-sm',
                                'placeholder' => __('rrhh.margin_right'),
                                'id' => 'margin_right',
                                'step' => '0.01',
                                'min' => '0.01',
                                'required',
                            ]) !!}
                        </div>
                    </div>
                </div>
                <div class="row row-editor">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label>@lang('rrhh.contract_template')</label> <span class="text-danger">*</span>
                        <div class="editor-container">
                            <textarea class="ckeditor form-control" name="template" id="template" required>{{ $type->template }}</textarea>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h4>@lang('rrhh.available_parameters'):</h4>
                        <h5><b>Nota:</b> {{ __('rrhh.message_parameter_name') }}</h5>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <table class="table table-sm table-bordered">
                            <caption class="text-center">Párametros con respecto al empleado</caption>
                            <thead>
                                <tr>
                                    <th>Parámetro</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>employee_name</td>
                                    <td>Obtiene el nombre del empleado.</td>
                                </tr>
                                <tr>
                                    <td>employee_age</td>
                                    <td>Obtiene la edad del empleado.</td>
                                </tr>
                                <tr>
                                    <td>employee_dni</td>
                                    <td>Obtiene el DUI del empleado.</td>
                                </tr>
                                <tr>
                                    <td>employee_tax_number</td>
                                    <td>Obtiene el NIT del empleado.</td>
                                </tr>
                                <tr>
                                    <td>employee_city</td>
                                    <td>Obtiene el municipio de residencia del empleado.</td>
                                </tr>
                                <tr>
                                    <td>employee_state</td>
                                    <td>Obtiene el departamento de residencia del empleado.</td>
                                </tr>
                                <tr>
                                    <td>employee_salary</td>
                                    <td>Obtiene el salario actual del empleado.</td>
                                </tr>
                                <tr>
                                    <td>employee_department</td>
                                    <td>Obtiene el departamento del empleado.</td>
                                </tr>
                                <tr>
                                    <td>employee_position</td>
                                    <td>Obtiene el cargo del empleado.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <table class="table table-bordered table-sm">
                            <caption class="text-center">Párametros generales y de la empresa</caption>
                            <thead>
                                <tr>
                                    <th>Parámetro</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>business_name</td>
                                    <td>Obtiene el nombre de la empresa.</td>
                                </tr>
                                <tr>
                                    <td>business_tax_number</td>
                                    <td>Obtiene el NIT de la empresa.</td>
                                </tr>
                                <tr>
                                    <td>business_state</td>
                                    <td>Obtiene el municipio donde está ubicada la empresa.</td>
                                </tr>
                                <tr>
                                    <td>contract_start_date</td>
                                    <td>Obtiene la fecha de inicio del contrato.</td>
                                </tr>
                                <tr>
                                    <td>contract_end_date</td>
                                    <td>Obtiene la fecha de finalización del contrato.</td>
                                </tr>
                                <tr>
                                    <td>current_date_letters</td>
                                    <td>Obtiene la fecha actual en letras.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="text-right">
                    <div class="btn-group">
                        <div class="btn-group dropleft" role="group">
                            <button type="button" class="btn btn-primary submit_type_contract_form">@lang('rrhh.update')</button>
                        </div>
                    </div>
                    <a href="{!! URL::to('/rrhh-catalogues') !!}">
                        <button id="cancel_product" type="button" class="btn btn-danger">@lang('messages.cancel')</button>
                    </a>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </section>
@endsection

@section('javascript')
    <script src="{{ asset('js/ckeditor.js') }}"></script>
    
    <script type="text/javascript">
        // On submit of template form
        $(document).on( 'click', '.submit_type_contract_form', function(e){
            e.preventDefault();
            var submit_type = $(this).attr('value');
            $('#submit_type').val(submit_type);
            if($("form#form_edit").valid()) {
                $("form#form_edit").submit();
            }
        });
    </script>
@endsection
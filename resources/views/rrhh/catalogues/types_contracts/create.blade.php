@extends('layouts.app')
@section('title', __('rrhh.type_contract'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('rrhh.add') {{ mb_strtolower(__('rrhh.type_contract')) }}</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="boxform_u box-solid_u">
            <div class="box-body">
                {!! Form::open([
                    'url' => action('RrhhTypeContractController@store'),
                    'method' => 'post',
                    'id' => 'form_add',
                    'files' => true,
                ]) !!}
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label>@lang('rrhh.name')</label> <span class="text-danger">*</span>
                            {!! Form::text('name', null, [
                                'class' => 'form-control form-control-sm',
                                'placeholder' => __('rrhh.name'),
                                'id' => 'name',
                                'required',
                            ]) !!}
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
                            {!! Form::number('margin_top', '2.40', [
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
                            {!! Form::number('margin_bottom', '2.40', [
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
                            {!! Form::number('margin_left', '2.40', [
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
                            {!! Form::number('margin_right', '2.40', [
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
                            <textarea class="ckeditor form-control" name="template" id="template" required></textarea>
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
                                    <td>name_employee</td>
                                    <td>Obtiene el nombre del empleado.</td>
                                </tr>
                                <tr>
                                    <td>age_employee</td>
                                    <td>Obtiene la edad del empleado.</td>
                                </tr>
                                <tr>
                                    <td>dni_employee</td>
                                    <td>Obtiene el DUI del empleado.</td>
                                </tr>
                                <tr>
                                    <td>tax_number_employee</td>
                                    <td>Obtiene el NIT del empleado.</td>
                                </tr>
                                <tr>
                                    <td>state_employee</td>
                                    <td>Obtiene el municipio de residencia del empleado.</td>
                                </tr>
                                <tr>
                                    <td>city_employee</td>
                                    <td>Obtiene el departamento de residencia del empleado.</td>
                                </tr>
                                <tr>
                                    <td>salary_employee</td>
                                    <td>Obtiene el salario actual del empleado.</td>
                                </tr>
                                <tr>
                                    <td>department_employee</td>
                                    <td>Obtiene el departamento del empleado.</td>
                                </tr>
                                <tr>
                                    <td>position_employee</td>
                                    <td>Obtiene el cargo del empleado.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <table class="table table-sm table-bordered">
                            <caption class="text-center">Párametros generales y de la empresa</caption>
                            <thead>
                                <tr>
                                    <th>Parámetro</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>name_business</td>
                                    <td>Obtiene el nombre de la empresa.</td>
                                </tr>
                                <tr>
                                    <td>tax_number_business</td>
                                    <td>Obtiene el NIT de la empresa.</td>
                                </tr>
                                {{-- <tr>
                                    <td>city_business</td>
                                    <td>Obtiene el municipio donde está ubicada la empresa.</td>
                                </tr> --}}
                                <tr>
                                    <td>state_business</td>
                                    <td>Obtiene el departamento donde está ubicada la empresa.</td>
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
                            <button type="button"
                                class="btn btn-primary submit_type_contract_form">@lang('rrhh.save')</button>
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
        $(document).on('click', '.submit_type_contract_form', function(e) {
            e.preventDefault();
            var submit_type = $(this).attr('value');
            $('#submit_type').val(submit_type);
            if ($("form#form_add").valid()) {
                $("form#form_add").submit();
            }
        });
    </script>
@endsection

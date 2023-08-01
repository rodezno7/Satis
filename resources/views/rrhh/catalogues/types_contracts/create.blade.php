@extends('layouts.app')
@section('title', __('rrhh.type_contract'))

@section('css')
<script src="{{ asset('js/ckeditor4/ckeditor.js') }}"></script>
@endsection

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
                <textarea cols="80" rows="10" name="editor" id="editor" required></textarea>
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
                                    <td>Obtiene el edad del empleado.</td>
                                </tr>
                                <tr>
                                    <td>employee_gender</td>
                                    <td>Obtiene el genero del empleado.</td>
                                </tr>
                                <tr>
                                    <td>employee_civil_status</td>
                                    <td>Obtiene el estado civil del empleado.</td>
                                </tr>
                                <tr>
                                    <td>employee_nationality</td>
                                    <td>Obtiene la nacionalidad del empleado.</td>
                                </tr>
                                <tr>
                                    <td>employee_profession</td>
                                    <td>Obtiene la profesión del empleado.</td>
                                </tr>
                                <tr>
                                    <td>employee_dni</td>
                                    <td>Obtiene el DUI del empleado. El formato será en números, por ejemplo: 02781023-5</td>
                                </tr>
                                <tr>
                                    <td>employee_dni_letters</td>
                                    <td>Obtiene el DUI del empleado. El formato será en letras, por ejemplo: CERO DOS SIETE OCHO UNO CERO DOS TRES GUIÓN CINCO.</td>
                                </tr>
                                <tr>
                                    <td>employee_dni_expedition_date</td>
                                    <td>Obtiene la fecha de expedición del DUI del empleado.</td>
                                </tr>
                                <tr>
                                    <td>employee_dni_expedition_place</td>
                                    <td>Obtiene el lugar de expedición del DUI del empleado.</td>
                                </tr>
                                <tr>
                                    <td>employee_tax_number</td>
                                    <td>Obtiene el NIT del empleado. El formato será en números.</td>
                                </tr>
                                <tr>
                                    <td>employee_tax_number_letters</td>
                                    <td>Obtiene el NIT del empleado. El formato será en letras.</td>
                                </tr>
                                {{-- <tr>
                                    <td>employee_tax_number_approved</td>
                                    <td>Obtiene si el NIT del empleado está Homologado o no.</td>
                                </tr> --}}
                                <tr>
                                    <td>employee_state</td>
                                    <td>Obtiene el municipio de residencia del empleado.</td>
                                </tr>
                                <tr>
                                    <td>employee_city</td>
                                    <td>Obtiene el departamento de residencia del empleado.</td>
                                </tr>
                                <tr>
                                    <td>employee_address</td>
                                    <td>Obtiene la dirección del empleado.</td>
                                </tr>
                                <tr>
                                    <td>employee_salary</td>
                                    <td>Obtiene el salario actual del empleado. El formato será en números, por ejemplo: $500.00.</td>
                                </tr>
                                <tr>
                                    <td>employee_salary_letters</td>
                                    <td>Obtiene el salario actual del empleado. El formato será en letras, por ejemplo: QUINIENTOS 00/100 DOLARES.</td>
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
                                    <td>business_name</td>
                                    <td>Obtiene el nombre de la empresa.</td>
                                </tr>
                                <tr>
                                    <td>line_of_business</td>
                                    <td>Obtiene el giro de la empresa.</td>
                                </tr>
                                <tr>
                                    <td>business_tax_number</td>
                                    <td>Obtiene el NIT de la empresa.</td>
                                </tr>
                                <tr>
                                    <td>business_tax_number_letters</td>
                                    <td>Obtiene el NIT de la empresa. El formato será en letras.</td>
                                </tr>
                                <tr>
                                    <td>business_state</td>
                                    <td>Obtiene el departamento donde está ubicada la empresa.</td>
                                </tr>
                                <tr>
                                    <td>business_address</td>
                                    <td>Obtiene la dirección de la empresa.</td>
                                </tr>
                                <tr>
                                    <td>business_legal_representative</td>
                                    <td>Obtiene el representante legal de la empresa</td>
                                </tr>
                                <tr>
                                    <td>contract_start_date</td>
                                    <td>Obtiene la fecha de inicio del contrato. El formato es 12 de Julio de 2023.</td>
                                </tr>
                                <tr>
                                    <td>contract_start_date_letters</td>
                                    <td>Obtiene la fecha de inicio del contrato. El formato es en letras, por ejemplo: doce de julio de dos mil veintitres.</td>
                                </tr>
                                <tr>
                                    <td>contract_end_date</td>
                                    <td>Obtiene la fecha de finalización del contrato.</td>
                                </tr>
                                <tr>
                                    <td>contract_end_date_letters</td>
                                    <td>Obtiene la fecha de finalización del contrato. El formato es en letras.</td>
                                </tr>
                                <tr>
                                    <td>current_date</td>
                                    <td>Obtiene la fecha actual.</td>
                                </tr>
                                <tr>
                                    <td>current_date_letters</td>
                                    <td>Obtiene la fecha actual. El formato es en letras</td>
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
    <script type="text/javascript">
        CKEDITOR.replace('editor');

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

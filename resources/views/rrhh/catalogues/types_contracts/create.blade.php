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
                        <h4 for="">{{ __('rrhh.Configuring_contract_margins') }}</h4>
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
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('rrhh.available_parameters')
                        <div class="panel-tools pull-right">
                            <button type="button" class="btn btn-panel-tool" data-toggle="collapse" data-target="#parameters-information-fields-box" id="btn-collapse-ci">
                                <i class="fa fa-minus" id="create-icon-collapsed-ci"></i>
                            </button>
                        </div>
                    </div>

                    <div class="panel-body collapse in" id="parameters-information-fields-box" aria-expanded="true">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <h5><b>Nota:</b> {{ __('rrhh.message_parameter_name') }}</h5>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <table class="table table-sm table-bordered">
                                    <caption class="text-center">{{ __('rrhh.Parameters_employee') }}</caption>
                                    <thead>
                                        <tr>
                                            <th>{{ __('rrhh.Parameters') }}</th>
                                            <th>{{ __('rrhh.description') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>employee_name</td>
                                            <td>{{ __('rrhh.Gets_the_employee_name') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_age</td>
                                            <td>{{ __('rrhh.Gets_the_employee_age') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_gender</td>
                                            <td>{{ __('rrhh.Gets_the_employee_gender') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_civil_status</td>
                                            <td>{{ __('rrhh.Gets_the_employee_civil_status') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_nationality</td>
                                            <td>{{ __('rrhh.Gets_the_employee_nationality') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_profession</td>
                                            <td>{{ __('rrhh.Gets_the_employee_profession') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_dni</td>
                                            <td>{{ __('rrhh.Gets_the_employee_dni') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_dni_letters</td>
                                            <td>{{ __('rrhh.Gets_the_employee_dni_letters') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_dni_expedition_date</td>
                                            <td>{{ __('rrhh.Gets_the_employee_dni_expedition_date') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_dni_expedition_place</td>
                                            <td>{{ __('rrhh.Gets_the_employee_dni_expedition_place') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_tax_number</td>
                                            <td>{{ __('rrhh.Gets_the_employee_tax_number') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_tax_number_letters</td>
                                            <td>{{ __('rrhh.Gets_the_employee_tax_number_letters') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_state</td>
                                            <td>{{ __('rrhh.Gets_the_employee_state') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_city</td>
                                            <td>{{ __('rrhh.Gets_the_employee_city') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_address</td>
                                            <td>{{ __('rrhh.Gets_the_employee_address') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_salary</td>
                                            <td>{{ __('rrhh.Gets_the_employee_salary') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_salary_letters</td>
                                            <td>{{ __('rrhh.Gets_the_employee_salary_letters') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_department</td>
                                            <td>{{ __('rrhh.Gets_the_employee_department') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_position</td>
                                            <td>{{ __('rrhh.Gets_the_employee_position') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <table class="table table-sm table-bordered">
                                    <caption class="text-center">{{ __('rrhh.Parameters_business') }}</caption>
                                    <thead>
                                        <tr>
                                            <th>{{ __('rrhh.Parameters') }}</th>
                                            <th>{{ __('rrhh.description') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>business_name</td>
                                            <td>{{ __('rrhh.Gets_the_business_name') }}</td>
                                        </tr>
                                        <tr>
                                            <td>line_of_business</td>
                                            <td>{{ __('rrhh.Gets_the_line_of_business') }}</td>
                                        </tr>
                                        <tr>
                                            <td>business_tax_number</td>
                                            <td>{{ __('rrhh.Gets_the_business_tax_number') }}</td>
                                        </tr>
                                        <tr>
                                            <td>business_tax_number_letters</td>
                                            <td>{{ __('rrhh.Gets_the_business_tax_number_letters') }}</td>
                                        </tr>
                                        <tr>
                                            <td>business_state</td>
                                            <td>{{ __('rrhh.Gets_the_business_department') }}</td>
                                        </tr>
                                        <tr>
                                            <td>business_address</td>
                                            <td>{{ __('rrhh.Gets_the_business_address') }}</td>
                                        </tr>
                                        <tr>
                                            <td>business_legal_representative</td>
                                            <td>{{ __('rrhh.Gets_the_bsuiness_legal_representative') }}</td>
                                        </tr>
                                        <tr>
                                            <td>contract_start_date</td>
                                            <td>{{ __('rrhh.Gets_the_contract_start_date') }}</td>
                                        </tr>
                                        <tr>
                                            <td>contract_start_date_letters</td>
                                            <td>{{ __('rrhh.Gets_the_contract_start_date_letters') }}</td>
                                        </tr>
                                        <tr>
                                            <td>contract_end_date</td>
                                            <td>{{ __('rrhh.Gets_the_contract_end_date') }}</td>
                                        </tr>
                                        <tr>
                                            <td>contract_end_date_letters</td>
                                            <td>{{ __('rrhh.Gets_the_contract_end_date_letters') }}</td>
                                        </tr>
                                        <tr>
                                            <td>current_date</td>
                                            <td>{{ __('rrhh.Gets_the_current_date') }}</td>
                                        </tr>
                                        <tr>
                                            <td>current_date_letters</td>
                                            <td>{{ __('rrhh.Gets_the_current_date_letters') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
        
        $('#btn-collapse-ci').click(function(){
            if ($("#parameters-information-fields-box").hasClass("in")) {            
                $("#create-icon-collapsed-ci").removeClass("fa fa-minus");
                $("#create-icon-collapsed-ci").addClass("fa fa-plus");
            }else{
                $("#create-icon-collapsed-ci").removeClass("fa fa-plus");
                $("#create-icon-collapsed-ci").addClass("fa fa-minus"); 
            }
        });
    </script>
@endsection

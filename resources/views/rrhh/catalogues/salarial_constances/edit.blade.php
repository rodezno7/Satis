@extends('layouts.app')
@section('title', __('rrhh.salarial_constances'))

@section('css')
<script src="{{ asset('js/ckeditor4/ckeditor.js') }}"></script>
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('rrhh.edit') {{ mb_strtolower(__('rrhh.salarial_constances')) }}</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="boxform_u box-solid_u">
            <div class="box-body">
                {!! Form::model($type, ['url' => action('RrhhSalarialConstanceController@update', $type->id), 'method' => 'patch',
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
                            {!! Form::select('status', [1 => __('rrhh.active'), 0 => __('rrhh.inactive')], $type->status, ['class' => 'form-control select2', 'id' => 'status', 'required', 'style' => 'width: 100%;' ]) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <h4 for="">{{ __('rrhh.configuring_constance_margins') }}</h4>
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

                <textarea name="editor" id="editor" required>{{ $type->template }}</textarea>

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
                                    <caption class="text-center">{{ __('rrhh.Parameters_business_employee') }}</caption>
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
                                            <td>employee_hired_date</td>
                                            <td>{{ __('rrhh.Gets_the_employee_hired_date') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_salary</td>
                                            <td>{{ __('rrhh.Gets_the_employee_salary') }}</td>
                                        </tr>
                                        <tr>
                                            <td>employee_position</td>
                                            <td>{{ __('rrhh.Gets_the_employee_position') }}</td>
                                        </tr>
                                        <tr>
                                            <td>business_name</td>
                                            <td>{{ __('rrhh.Gets_the_business_name') }}</td>
                                        </tr>
                                        <tr>
                                            <td>business_email</td>
                                            <td>{{ __('rrhh.Gets_the_business_email') }}</td>
                                        </tr>
                                        <tr>
                                            <td>business_mobile</td>
                                            <td>{{ __('rrhh.Gets_the_business_mobile') }}</td>
                                        </tr>
                                        <tr>
                                            <td>current_date</td>
                                            <td>{{ __('rrhh.Gets_the_current_date') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <table class="table table-sm table-bordered">
                                    <caption class="text-center">{{ __('rrhh.Parameters_income_discount') }}</caption>
                                    <thead>
                                        <tr>
                                            <th>{{ __('rrhh.Parameters') }}</th>
                                            <th>{{ __('rrhh.description') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>bonus_income</td>
                                            <td>{{ __('rrhh.Get_employee_bonus') }}</td>
                                        </tr>
                                        <tr>
                                            <td>comission_income</td>
                                            <td>{{ __('rrhh.Get_employee_commissions') }}</td>
                                        </tr>
                                        <tr>
                                            <td>total_income</td>
                                            <td>{{ __('rrhh.Get_the_total_income') }}</td>
                                        </tr>
                                        <tr>
                                            <td>isss_discount</td>
                                            <td>{{ __('rrhh.Get_the_ISSS_discount') }}</td>
                                        </tr>
                                        <tr>
                                            <td>afp_discount</td>
                                            <td>{{ __('rrhh.Get_the_AFP_discount') }}</td>
                                        </tr>
                                        <tr>
                                            <td>rent_discount</td>
                                            <td>{{ __('rrhh.Get_the_rent_discount') }}</td>
                                        </tr>
                                        <tr>
                                            <td>bank_loans</td>
                                            <td>{{ __('rrhh.Get_bank_loans') }}</td>
                                        </tr>
                                        <tr>
                                            <td>mortgage_loans</td>
                                            <td>{{ __('rrhh.Get_mortgage_loans') }}</td>
                                        </tr>
                                        <tr>
                                            <td>judicial_discount</td>
                                            <td>{{ __('rrhh.Get_the_judicial_discount') }}</td>
                                        </tr>
                                        <tr>
                                            <td>total_deductions</td>
                                            <td>{{ __('rrhh.Get_the_total_deductions') }}</td>
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
    <script type="text/javascript">
        CKEDITOR.replace('editor');
        // On submit of template form
        $(document).on( 'click', '.submit_type_contract_form', function(e){
            e.preventDefault();
            var submit_type = $(this).attr('value');
            $('#submit_type').val(submit_type);
            if($("form#form_edit").valid()) {
                $("form#form_edit").submit();
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

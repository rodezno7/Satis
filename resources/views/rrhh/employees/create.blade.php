@extends('layouts.app')
@section('title', __('rrhh.employee'))
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('rrhh.add') @lang('rrhh.employee')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="boxform_u box-solid_u">
            {!! Form::open([
                'url' => action('EmployeesController@store'),
                'method' => 'post',
                'id' => 'form_add',
                'files' => true,
            ]) !!}
            <div class="box-body">

                <div class="panel panel-default">
                    <div class="panel-heading">@lang('customer.general_information')
                        <div class="panel-tools pull-right">
                            <button type="button" class="btn btn-panel-tool" data-toggle="collapse"
                                data-target="#general-information-fields-box" id="btn-collapse-gi">
                                <i class="fa fa-minus" id="create-icon-collapsed-gi"></i>
                            </button>
                        </div>
                    </div>

                    <div class="panel-body collapse in" id="general-information-fields-box" aria-expanded="true">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.name')</label> <span class="text-danger">*</span>
                                    {!! Form::text('first_name', null, [
                                        'class' => 'form-control form-control-sm',
                                        'placeholder' => __('rrhh.name'),
                                        'id' => 'first_name',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.last_name')</label> <span class="text-danger">*</span>
                                    {!! Form::text('last_name', null, [
                                        'class' => 'form-control form-control-sm',
                                        'placeholder' => __('rrhh.last_name'),
                                        'id' => 'last_name',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.gender')</label> <span class="text-danger">*</span>
                                    {!! Form::select('gender', ['M' => __('rrhh.male'), 'F' => __('rrhh.female')], null, [
                                        'class' => 'form-control form-control-sm select2',
                                        'style' => 'width:100%;',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.nationality')</label> <span class="text-danger">*</span>
                                    {!! Form::select('nationality_id', $nationalities, null, [
                                        'class' => 'form-control form-control-sm select2',
                                        'id' => 'nationality_id',
                                        'style' => 'width: 100%;',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.birthdate')</label> <span class="text-danger">*</span>
                                    {!! Form::text('birth_date', null, [
                                        'class' => 'form-control form-control-sm',
                                        'id' => 'birth_date',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.dni')</label> <span class="text-danger">*</span>
                                    {!! Form::text('dni', null, [
                                        'class' => 'form-control form-control-sm',
                                        'placeholder' => '00000000-0',
                                        'id' => 'dni',
                                        'required',
                                        'pattern' => '[0-9]{8}-\d',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.tax_number')</label> <label id="text-approved">(Homologado)</label> <span
                                        class="text-danger">*</span>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            {!! Form::checkbox('approved', 1, true, ['id' => 'approved', 'onClick' => 'dniApproved()']) !!}
                                        </span>
                                        {!! Form::text('tax_number', null, [
                                            'class' => 'form-control form-control-sm',
                                            'id' => 'tax_number',
                                            'placeholder' => __('rrhh.tax_number'),
                                            'required',
                                            'disabled',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.marital_status')</label> <span class="text-danger">*</span>
                                    {!! Form::select('civil_status_id', $civil_statuses, null, [
                                        'id' => 'civil_status_id',
                                        'class' => 'form-control form-control-sm select2',
                                        'style' => 'width: 100%;',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.phone')</label>
                                    {!! Form::text('phone', null, [
                                        'class' => 'form-control form-control-sm',
                                        'placeholder' => '0000-0000',
                                        'id' => 'phone',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.mobile_phone')</label>
                                    {!! Form::text('mobile', null, [
                                        'class' => 'form-control form-control-sm',
                                        'placeholder' => '0000-0000',
                                        'id' => 'mobile',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.personal_email')</label> <span class="text-danger">*</span>
                                    @show_tooltip(__('rrhh.tooltip_email'))
                                    {!! Form::email('email', null, [
                                        'class' => 'form-control form-control-sm',
                                        'placeholder' => __('rrhh.personal_email'),
                                        'id' => 'email',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.institutional_email')</label>
                                    {!! Form::email('institutional_email', null, [
                                        'class' => 'form-control form-control-sm',
                                        'placeholder' => __('rrhh.institutional_email'),
                                        'id' => 'institutional_email',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.address')</label> <span class="text-danger">*</span>
                                    {!! Form::text('address', null, [
                                        'class' => 'form-control form-control-sm',
                                        'placeholder' => __('rrhh.address'),
                                        'id' => 'address',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.country')</label>
                                    {!! Form::select('country_id', $countries, null, [
                                        'id' => 'country_id',
                                        'class' => 'form-control form-control-sm select2',
                                        'placeholder' => __('rrhh.country'),
                                        'style' => 'width: 100%;',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.state')</label>
                                    {!! Form::select('state_id', $states, null, [
                                        'id' => 'state_id',
                                        'class' => 'form-control form-control-sm select2',
                                        'placeholder' => __('rrhh.state'),
                                        'style' => 'width: 100%;',
                                        'disabled',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.city')</label>
                                    {!! Form::select('city_id', $cities, null, [
                                        'id' => 'city_id',
                                        'class' => 'form-control form-control-sm select2',
                                        'placeholder' => __('rrhh.city'),
                                        'style' => 'width: 100%;',
                                        'disabled',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.social_security_number')</label>
                                    {!! Form::number('social_security_number', null, [
                                        'class' => 'form-control form-control-sm',
                                        'placeholder' => __('rrhh.social_security_number'),
                                        'id' => 'social_security_number',
                                        'pattern' => '[0-9]+',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.afp')</label>
                                    {!! Form::select('afp_id', $afps, null, [
                                        'id' => 'afp_id',
                                        'class' => 'form-control form-control-sm select2',
                                        'placeholder' => __('rrhh.afp'),
                                        'style' => 'width: 100%;',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.afp_number')</label>
                                    {!! Form::number('afp_number', null, [
                                        'class' => 'form-control form-control-sm',
                                        'placeholder' => __('rrhh.afp_number'),
                                        'id' => 'afp_number',
                                        'pattern' => '[0-9]+',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.date_admission')</label> <span class="text-danger">*</span>
                                    {!! Form::text('date_admission', @format_date('now'), [
                                        'class' => 'form-control form-control-sm',
                                        'id' => 'date_admission',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.department')</label> <span class="text-danger">*</span>
                                    {!! Form::select('department_id', $departments, null, [
                                        'id' => 'department_id',
                                        'class' => 'form-control form-control-sm select2',
                                        'placeholder' => __('rrhh.department'),
                                        'style' => 'width: 100%;',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.position')</label> <span class="text-danger">*</span>
                                    {!! Form::select('position1_id', $positions, null, [
                                        'id' => 'position1_id',
                                        'class' => 'form-control form-control-sm select2',
                                        'placeholder' => __('rrhh.position'),
                                        'style' => 'width: 100%;',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.salary')</label> <span class="text-danger">*</span>
                                    {!! Form::number('salary', null, [
                                        'class' => 'form-control form-control-sm',
                                        'placeholder' => __('rrhh.salary'),
                                        'id' => 'salary',
                                        'step' => '0.01',
                                        'min' => '0.01',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.way_to_pay')</label> <span class="text-danger">*</span>
                                    {!! Form::select('payment_id', $payments, null, [
                                        'id' => 'payment_id',
                                        'class' => 'form-control form-control-sm select2',
                                        'placeholder' => __('rrhh.way_to_pay'),
                                        'style' => 'width: 100%;',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>

                            <div id='bank_information'>
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('rrhh.bank')</label> <span class="text-danger">*</span>
                                        {!! Form::select('bank_id', $banks, null, [
                                            'id' => 'bank_id',
                                            'class' => 'form-control form-control-sm select2',
                                            'placeholder' => __('rrhh.bank'),
                                            'style' => 'width: 100%;',
                                        ]) !!}
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('rrhh.bank_account')</label> <span class="text-danger">*</span>
                                        {!! Form::number('bank_account', null, [
                                            'class' => 'form-control form-control-sm',
                                            'placeholder' => __('rrhh.bank_account'),
                                            'id' => 'bank_account',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.type_employee')</label>
                                    <select name="type_id" id="type_id" class="form-control form-control-sm select2"
                                        placeholder="{{ __('rrhh.type_employee') }}" style="width: 100%">
                                        <option value="">{{ __('rrhh.type_employee') }}</option>
                                        @foreach ($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('rrhh.profession_occupation')</label>
                                    {!! Form::select('profession_id', $professions, null, [
                                        'id' => 'profession_id',
                                        'class' => 'form-control form-control-sm select2',
                                        'placeholder' => __('rrhh.profession_occupation'),
                                        'style' => 'width: 100%;',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>
                                        {{ __('employees.has_user') }} </label>
                                    {!! Form::checkbox('chk_has_user', '0', false, ['id' => 'chk_has_user', 'onClick' => 'showUserOption()']) !!}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div id="user_modal_option" style="display: none">
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {!! Form::label('username', __('employees.username') . ' : ') !!}
                                        {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' => __('employees.username')]) !!}
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {!! Form::label('role', __('employees.role') . ' : ') !!}
                                        {!! Form::select('role', $roles, null, [
                                            'id' => 'role',
                                            'class' => 'form-control form-control-sm select2',
                                            'style' => 'width: 100%;',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-input">
                                            {{ __('employees.pass_manual') }} {!! Form::radio('rdb_pass_mode', '0', true, [
                                                'class' => 'form-check-input',
                                                'id' => 'rdb_pass_manual',
                                                'onClick' => 'showPassMode()',
                                            ]) !!}
                                            @show_tooltip(__('lang_v1.tooltip_enable_password_manual'))
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-input">
                                            {{ __('employees.pass_auto') }} {!! Form::radio('rdb_pass_mode', 'generated', false, [
                                                'class' => 'form-check-input',
                                                'id' => 'rdb_pass_auto',
                                                'onClick' => 'showPassMode()',
                                            ]) !!}
                                            @show_tooltip(__('lang_v1.tooltip_enable_password_generated'))
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div id="pass_mode">
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('username', __('business.password') . ' : ') !!}
                                            <input id="password" name="password" type="password" class="form-control" ,
                                                placeholder="{{ __('business.password') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('username', __('business.confirm_password') . ' : ') !!}
                                            <input id="password_confirm" type="password" class="form-control" ,
                                                placeholder="{{ __('business.confirm_password') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- <br> --}}
                            <div class="row">
                                <div class="col-lg-2 col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-check-input">
                                            {{ __('employees.commission') }}
                                            {!! Form::checkbox('commission', '0', false, ['id' => 'chk_commission', 'onClick' => 'commision_enable()']) !!}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="commision_div" style="display: none">
                                <div class="col-lg-3 col-md-3 col-sm-6">
                                    <div class="input-group input-group">
                                        <span class="input-group-addon" id="sizing-addon1">%</span>
                                        {!! Form::number('commision_amount', null, [
                                            'class' => 'form-control',
                                            'id' => 'commision_amount',
                                            'placeholder' => __('employees.commision_amount'),
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">@lang('rrhh.files')
                        <div class="panel-tools pull-right">
                            <button type="button" class="btn btn-panel-tool" data-toggle="collapse"
                                data-target="#documents-information-fields-box" id="btn-collapse-fi">
                                <i class="fa fa-minus" id="create-icon-collapsed-fi"></i>
                            </button>
                        </div>
                    </div>

                    <div class="panel-body collapse in" id="documents-information-fields-box" aria-expanded="true">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    {!! Form::label('photo', __('rrhh.photo') . ':') !!}
                                    {!! Form::file('photo', ['id' => 'photo', 'accept' => 'image/*']) !!}
                                    <small class="help-block">@lang('purchase.max_file_size', ['size' => config('constants.document_size_limit_3') / 1000000]).</small>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    {!! Form::label('curriculum_vitae', __('rrhh.curriculum_vitae') . ':') !!}
                                    {!! Form::file('curriculum_vitae', ['id' => 'curriculum_vitae', 'accept' => 'application/pdf']) !!}
                                    <small class="help-block">@lang('purchase.max_file_size', ['size' => config('constants.document_size_limit_6') / 1000000]).</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12 text-right">
                        <input type="hidden" name="submit_type" id="submit_type">
                        <div class="btn-group">
                            <div class="btn-group dropleft" role="group">
                                <button type="button" class="btn btn-primary submit_employee_form"
                                    value="add">@lang('rrhh.save')</button>
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-sort-desc"></i>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="btn-primary dropdown-menu dropdown-menu-right" role="menu">
                                    <li>
                                        <a href="#" type="button" class="submit_employee_form" value='other'>
                                            @lang('rrhh.save_and_other')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" type="button" class="submit_employee_form" value='complete'>
                                            @lang('rrhh.save_and_complete')
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <a href="{!! URL::to('/rrhh-employees') !!}">
                            <button id="cancel_product" type="button" class="btn btn-danger">@lang('messages.cancel')</button>
                        </a>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
    </section>
@endsection
@section('css')
    <style>
        .dropdown-menu>li>a:hover {
            background-color: #e1e3e9;
            color: black;
        }

        .dropdown-menu>li>a {
            color: white;
        }
    </style>
@endsection
@section('javascript')
    <script>
        $(document).ready(function() {
            showBankInformation();
            $('.select2').select2();

            let dui = document.getElementById("dni");
            $(dui).mask("00000000-0");

            var fechaMaxima = new Date();
            fechaMaxima.setFullYear(fechaMaxima.getFullYear() - 18);
            fechaMaxima = fechaMaxima.toLocaleDateString("es-ES", {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });

            var fechaMinima = new Date();
            fechaMinima.setFullYear(fechaMinima.getFullYear() - 99);
            fechaMinima = fechaMinima.toLocaleDateString("es-ES", {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });

            var fechaActual = new Date();
            fechaActual = fechaActual.toLocaleDateString("es-ES", {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });

            $('#birth_date').datepicker({
                autoclose: true,
                format: datepicker_date_format,
                startDate: fechaMinima,
                endDate: fechaMaxima,
            });

            $("#birth_date").datepicker("setDate", fechaMaxima);

            $('#date_admission').datepicker({
                autoclose: true,
                format: datepicker_date_format
            });

            $("#date_admission").datepicker("setDate", fechaActual);

            showUserOption();
            commision_enable();
            showPassMode();
        });

        $('#btn-collapse-fi').click(function() {
            if ($("#documents-information-fields-box").hasClass("in")) {
                $("#create-icon-collapsed-fi").removeClass("fa fa-minus");
                $("#create-icon-collapsed-fi").addClass("fa fa-plus");
            } else {
                $("#create-icon-collapsed-fi").removeClass("fa fa-plus");
                $("#create-icon-collapsed-fi").addClass("fa fa-minus");
            }
        });

        $('#btn-collapse-gi').click(function() {
            if ($("#general-information-fields-box").hasClass("in")) {
                $("#create-icon-collapsed-gi").removeClass("fa fa-minus");
                $("#create-icon-collapsed-gi").addClass("fa fa-plus");
            } else {
                $("#create-icon-collapsed-gi").removeClass("fa fa-plus");
                $("#create-icon-collapsed-gi").addClass("fa fa-minus");
            }
        });

        $('#dni').on('change', function() {
            let valor = $(this).val();
            let route = '/rrhh-employees/verified_document/' + 'dni' + '/' + valor;
            $.get(route, function(data, status) {
                if (data.success == false) {
                    Swal.fire({
                        title: data.msg,
                        icon: "error",
                        timer: 3000,
                        showConfirmButton: true,
                    });
                }
            });
            let approved = $("#approved").val();
            if (approved == 1) {
                $("#tax_number").val($('#dni').val());
            }
        });

        function dniApproved() {
            if ($("#approved").is(":checked")) {
                var dni = $("#dni").val();
                $("#approved").val('1');
                $("#tax_number").prop('disabled', true);
                $("#tax_number").val(dni);
                $("#text-approved").show();
            } else {
                $("#approved").val('0');
                $("#tax_number").prop('disabled', false);
                $("#tax_number").val('');
                $("#text-approved").hide();
            }
        }

        $('#tax_number').on('change', function() {
            let tax_number = $(this).val();
            let dni = $('#dni').val();
            if (dni != tax_number) {
                let route = '/rrhh-employees/verified_document/' + 'tax_number' + '/' + tax_number;
                $.get(route, function(data, status) {
                    if (data.success == false) {
                        Swal.fire({
                            title: data.msg,
                            icon: "error",
                            timer: 3000,
                            showConfirmButton: true,
                        });
                    }
                });
            }
        });

        function showBankInformation() {
            selected_option = $("#payment_id option:selected").text();

            if (selected_option == 'Transferencia bancaria') {
                $('#bank_information').show();
                $("#bank_account").prop('required', true);
                $("#bank_id").prop('required', true);
            } else {
                $('#bank_information').hide();
                $("#bank_account").prop('required', false);
                $("#bank_id").prop('required', false);
                $('#bank_id').val('').change();
                $('#bank_account').val('');
            }
        }

        $('#payment_id').change(function() {
            showBankInformation();
        });


        function updateCities() {
            $("#city_id").empty();
            state_id = $('#state_id').val();

            if (state_id) {

                var route = "/cities/getCitiesByState/" + state_id;
                $.get(route, function(res) {

                    $("#city_id").append(
                        '<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');

                    $(res).each(function(key, value) {
                        $("#city_id").append('<option value="' + value.id + '">' + value.name +
                            '</option>');

                    });
                });

            }

        }

        $('#state_id').change(function() {
            updateCities();
            $('#city_id').prop('disabled', false);
        });

        $('#country_id').change(function() {
            updateStates();
            $('#state_id').prop('disabled', false);
            $('#city_id').prop('disabled', true);
        });


        function updateStates() {
            $("#state_id").empty();
            $("#city_id").empty();
            country_id = $('#country_id').val();

            var route = "/states/getStatesByCountry/" + country_id;
            $.get(route, function(res) {
                $("#state_id").append(
                    '<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');

                $(res).each(function(key, value) {
                    $("#state_id").append('<option value="' + value.id + '">' + value.name + '</option>');

                });
            });
        }

        validExtPhoto = ['jpg', 'jpeg', 'png'];

        $('#photo').on('change', function() {
            extension = this.files[0].type.split('/')[1];

            if (validExtPhoto.indexOf(extension) == -1) {
                $('#photo').val('');
                Swal.fire({
                    title: '@lang('rrhh.only_img')',
                    icon: "error",
                });
            } else {
                size = this.files[0].size;
                if (size > 5242880) {

                    $('#photo').val('');
                    Swal.fire({
                        title: '@lang('rrhh.bad_size_img1')',
                        icon: "error",
                    });
                }
            }
        });


        validExtCV = ['pdf'];

        $('#curriculum_vitae').on('change', function() {
            extension = this.files[0].type.split('/')[1];

            if (validExtCV.indexOf(extension) == -1) {
                $('#curriculum_vitae').val('');
                Swal.fire({
                    title: '@lang('rrhh.only_pdf1')',
                    icon: "error",
                });
            } else {
                size = this.files[0].size;
                if (size > 5242880) {

                    $('#curriculum_vitae').val('');
                    Swal.fire({
                        title: '@lang('rrhh.bad_size_cv')',
                        icon: "error",
                    });
                }
            }
        });


        var img_fileinput_setting = {
            'showUpload': false,
            'showPreview': true,
            'browseLabel': LANG.file_browse_label,
            'removeLabel': LANG.remove,
            'previewSettings': {
                image: {
                    width: "100%",
                    height: "100%",
                    'max-width': "100%",
                    'max-height': "100%",
                }
            }
        };
        $("#photo").fileinput(img_fileinput_setting);
        $("#curriculum_vitae").fileinput(img_fileinput_setting);

        function showUserOption() {
            if ($("#chk_has_user").is(":checked")) {
                $("#chk_has_user").val('has_user');
                $("#user_modal_option").show();
                $("#username").prop('required', true);
                $("#role").prop('required', true);
            } else {
                $("#chk_has_user").val('0');
                $("#user_modal_option").hide();
                $("#username").prop('required', false);
                $("#role").prop('required', false);
            }
        }

        function commision_enable() {
            if ($("#chk_commission").is(":checked")) {
                $("#chk_commission").val('has_commission');
                $("#commision_div").show();
                $("#commision_amount").prop('required', true);
                $("#commision_amount").focus();
            } else {
                $("#chk_commission").val('0');
                $("#commision_div").hide();
                $("#commision_amount").prop('required', false);
                $("#commision_amount").val('');
            }
        }

        function showPassMode() {
            if ($("#rdb_pass_manual").is(":checked")) {
                $("#pass_mode").show();
            } else if ($("#rdb_pass_auto").is(":checked")) {
                $("#pass_mode").hide();
            }
        }

        $(document).on('click', '.submit_employee_form', function(e) {
            e.preventDefault();
            var submit_type = $(this).attr('value');
            $('#submit_type').val(submit_type);
            if ($("form#form_add").valid()) {
                $("form#form_add").submit();
            }
        });
    </script>
@endsection

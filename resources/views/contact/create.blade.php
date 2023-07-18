@extends('layouts.app')
@section('title', __('contact.create_supplier'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="modal-title">@lang('contact.add_supplier')</h1>
    </section>

    @php
        $form_id = 'contact_add_form';
        if (isset($quick_add)) {
            $form_id = 'quick_add_contact';
        }
    @endphp
    <!-- Main content -->
    <section class="content">
        {!! Form::open(['url' => action('ContactController@store'), 'method' => 'post', 'id' => $form_id]) !!}
        <div class="boxform_u box-solid_u">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('supplier_business_name', __('business.social_reason') . ':') !!}&nbsp;<span class="text-danger">*</span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                {!! Form::text('supplier_business_name', null, ['class' => 'form-control', 'placeholder' => __('contact.name'), 'required']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 supplier_fields">
                        <div class="form-group">
                            {!! Form::label('name', __('business.business_name') . ':') !!}&nbsp;<span class="text-danger">*</span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-briefcase"></i>
                                </span>
                                {!! Form::text('name', null, [
                                    'class' => 'form-control',
                                    'required',
                                    'placeholder' => __('business.business_name'),
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('org_type', __('business.org_type') . ':') !!}&nbsp;<span class="text-danger">*</span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-briefcase"></i>
                                </span>
                                {!! Form::select('organization_type', $org_type, '', [
                                    'class' => 'form-control',
                                    'id' => 'organization_type',
                                    'style' => 'width: 100%;',
                                    'placeholder' => __('messages.please_select'),
                                    'required',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        {!! Form::label('supplier_sorting', __('business.sorting') . ':') !!}&nbsp;<span class="text-danger">*</span>
                        <div class="form-check">
                            {!! Form::checkbox('is_supplier', true, true, [
                                'class' => 'form-check-input is_supplier',
                                'id' => 'is_supplier'
                            ]) !!}
                            {!! Form::label('is_supplier', __('contact.is_supplier'), [
                                'class' => 'form-check-label'
                            ]) !!}
                        </div>
                        <div class="form-check">
                            {!! Form::checkbox('is_provider', true, false, [
                                'class' => 'form-check-input is_provider',
                                'id' => 'is_provider',
                            ]) !!}
                            {!! Form::label('is_provider', __('contact.is_provider'), ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('tax_number', __('contact.tax_no') . ':') !!}&nbsp;<span class="text-danger">*</span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-info"></i>
                                </span>
                                {!! Form::text('tax_number', null, [
                                    'class' => 'form-control',
                                    'placeholder' => __('contact.tax_no'),
                                    'required',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('nit', __('business.nit') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-id-badge"></i>
                                </span>
                                {!! Form::text('nit', null, [
                                    'class' => 'form-control',
                                    'id' => 'nit',
                                    'required',
                                    'placeholder' => __('business.nit'),
                                ]) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 supplier_fields">
                        <div class="form-group">
                            {!! Form::label('business_activity', __('contact.business_activity') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-building"></i>
                                </span>
                                {!! Form::text('business_activity', null, [
                                    'class' => 'form-control',
                                    'placeholder' => __('contact.business_activity'),
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('landmark', __('business.landmark') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-map-marker"></i>
                                </span>
                                {!! Form::text('landmark', null, ['class' => 'form-control', 'placeholder' => __('business.landmark')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('taxpayer_type_type', __('business.taxpayer_type') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-briefcase"></i>
                                </span>
                                {!! Form::select('business_type_id', $business_type, '', [
                                    'class' => 'select2',
                                    'id' => 'business_type',
                                    'style' => 'width: 100%;',
                                    'placeholder' => __('messages.please_select'),
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div id="box-dni" class="col-md-3" style="display: none;">
                        <div class="form-group">
                            {!! Form::label('dni', __('business.dui') . ':') !!}&nbsp;<span class="text-danger">*</span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-address-card-o"></i>
                                </span>
                                {!! Form::text('dni', null, [
                                    'class' => 'form-control',
                                    'id' => 'dni',
                                    'required',
                                    'placeholder' => __('business.dui'),
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>@lang('customer.country'):</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class=" glyphicon glyphicon-flag"></i></span>
                                {!! Form::select('country_id', $countries, '', [
                                    'class' => 'select2',
                                    'id' => 'country_id',
                                    'style' => 'width: 100%;',
                                    'placeholder' => __('messages.please_select'),
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 state">
                        <div class="form-group">
                            {!! Form::label('state', __('business.state') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-map-marker"></i>
                                </span>
                                {!! Form::select('state_id', [], '', [
                                    'class' => 'select2',
                                    'id' => 'state_id',
                                    'style' => 'width: 100%;',
                                    'placeholder' => __('messages.please_select'),
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('city', __('business.city') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-map-marker"></i>
                                </span>
                                {!! Form::select('city_id', [], '', [
                                    'class' => 'select2',
                                    'id' => 'city_id',
                                    'style' => 'width: 100%;',
                                    'placeholder' => __('messages.please_select'),
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3" style="margin-top: 40px;">
                        <div class="form-check">
                            {!! Form::checkbox('is_exempt', false, false, ['class' => 'form-check-input', 'id' => 'is_exempt']) !!}
                            {!! Form::label('is_exempt', __('contact.is_exempt') . ':') !!} @show_tooltip(__("contact.no_taxes_applied"))
                        </div>
                    </div>
                    <div class="col-md-3 payment" style="display: none;">
                        <div class="form-group">
                            <label>@lang('customer.payment_terms')</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-align-justify"></i></span>
                                {!! Form::select('payment_term_id', $payment_terms, '', [
                                    'class' => 'select2',
                                    'id' => 'payment_terms_id',
                                    'style' => 'width: 100%;',
                                    'placeholder' => __('messages.please_select'),
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 payment" style="display: none;">
                        <div class="form-group">
                            {!! Form::label('credit_limit', __('lang_v1.credit_limit') . ':') !!} @show_tooltip(__('lang_v1.credit_limit_help'))
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-money"></i>
                                </span>
                                {!! Form::text('credit_limit', null, [
                                    'class' => 'form-control input_number',
                                    'placeholder' => __('lang_v1.credit_limit'),
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    @if ($business_debt_to_pay_type == 'supplier')
                        <input type="hidden" value="{{ $supplier_account }}" id="main_account">
                        <div class="col-md-3 supplier_account">
                            <div class="form-group">
                                {!! Form::label('supplier_catalogue_id', __('contact.supplier_account') . ':') !!}
                                {!! Form::select('supplier_catalogue_id', [], null, [
                                    'class' => 'form-control select_account',
                                    'placeholder' => __('contact.supplier_account'),
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-3 provider_account" style="display: none;">
                            <div class="form-group">
                                {!! Form::label('provider_catalogue_id', __('contact.provider_account') . ':') !!}
                                {!! Form::select('provider_catalogue_id', [], null, [
                                    'class' => 'form-control select_account',
                                    'style' => 'width: 100%;',
                                    'placeholder' => __('contact.provider_account'),
                                ]) !!}
                            </div>
                        </div>
                    @endif
                    <div class="col-md-12">
                        <hr />
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('landline', __('contact.landline') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-phone"></i>
                                </span>
                                {!! Form::text('landline', null, ['class' => 'form-control', 'placeholder' => __('contact.landline')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('email', __('business.email') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => __('business.email')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('mobile', __('contact.mobile') . ':') !!}&nbsp;<span class="text-danger">*</span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-mobile"></i>
                                </span>
                                {!! Form::text('mobile', null, ['class' => 'form-control', 'required', 'placeholder' => __('contact.mobile')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('alternate_number', __('business.alternate_contact_number') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-whatsapp"></i>
                                </span>
                                {!! Form::text('alternate_number', null, [
                                    'class' => 'form-control',
                                    'placeholder' => __('contact.alternate_contact_number'),
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <data></data>
                    <div class="@if (isset($quick_add)) hide @endif"> </div>
                    <div class="clearfix"></div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-right">
                        <button type="submit" class="btn btn-primary" id="submit">@lang('messages.save')</button>
                        <a class="btn btn-default" href="{{action('ContactController@index', ['type' => 'supplier'])}}">@lang('messages.close')</a>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </section>
    <!-- /.content -->
@section('javascript')
    <script src="{{ asset('js/contacts.js?v=' . $asset_v) }}"></script>
@endsection
@endsection

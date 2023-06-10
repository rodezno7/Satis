<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content" style="border-radius: 20px;">
  @php
    $form_id = 'contact_add_form';
    if(isset($quick_add)){
    $form_id = 'quick_add_contact';
    }
  @endphp
    {!! Form::open(['url' => action('ContactController@store'), 'method' => 'post', 'id' => $form_id ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('contact.add_supplier')</h4>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="col-md-2">
          <div class="form-group">
            {!! Form::label("org_type", __("business.org_type") . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-briefcase"></i>
              </span>
              {!! Form::select('organization_type', $org_type, '', ['class' => 'select2', 'id' => 'organization_type', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}     
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('name', __('business.social_reason') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
                {!! Form::text('name', null, ['class' => 'form-control','placeholder' => __('contact.name'), 'required']); !!}
            </div>
          </div>
        </div>
        <div class="col-md-4 supplier_fields">
          <div class="form-group">
            {!! Form::label('supplier_business_name', __('business.business_name') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-briefcase"></i>
              </span>
              {!! Form::text('supplier_business_name', null, ['class' => 'form-control', 'required', 'placeholder' => __('business.business_name')]); !!}
            </div>
          </div>
        </div>
        <div class="col-md-2">
          {!! Form::label('supplier_sorting', __('business.sorting') . ':*') !!}
          <div class="form-check">
            {!! Form::checkbox("is_supplier", true, true, ["class" => "form-check-input is_supplier", "id" => 'is_supplier']) !!}
            {!! Form::label("is_supplier", __("contact.is_supplier"), ["class" => "form-check-label"]) !!}
          </div>
          <div class="form-check">
            {!! Form::checkbox("is_provider", true, false, ["class" => "form-check-input is_provider", "id" => 'is_provider']) !!}
            {!! Form::label("is_provider", __("contact.is_provider"), ["class" => "form-check-label"]) !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('tax_number', __('contact.tax_no') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-info"></i>
              </span>
              {!! Form::text('tax_number', null, ['class' => 'form-control', 'placeholder' => __('contact.tax_no'), "required"]); !!}
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
              {!! Form::text('nit', null, ['class' => 'form-control','id' => 'nit','required','placeholder' => __('business.nit')]); !!}
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label("taxpayer_type_type", __("business.taxpayer_type") . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-briefcase"></i>
              </span>
              {!! Form::select('business_type_id', $business_type, '', ['class' => 'select2', 'id' => 'business_type', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}     
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('dni', __('business.dui') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-address-card-o"></i>
              </span>
              {!! Form::text('dni', null, ['class' => 'form-control', 'id' => 'dni', 'required', 'placeholder' => __('business.dui')]); !!}
            </div>
          </div>
        </div>  
        <div class="col-md-3 supplier_fields">
          <div class="form-group">
            {!! Form::label("business_activity", __("contact.business_activity") . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-building"></i>
              </span>
              {!! Form::text("business_activity", null, ["class" => "form-control", "placeholder" => __("contact.business_activity")]) !!}
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('landmark', __('business.landmark') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-map-marker"></i>
              </span>
              {!! Form::text('landmark', null, ['class' => 'form-control', 'placeholder' => __('business.landmark')]); !!}
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>@lang('customer.country')</label>
            <div class="input-group">
                <span class="input-group-addon"><i class=" glyphicon glyphicon-flag"></i></span>
                {!! Form::select('country_id', $countries, '', ['class' => 'select2', 'id' => 'country_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
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
                {!! Form::select('state_id', [], '', ['class' => 'select2', 'id' => 'state_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
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
                {!! Form::select('city_id', [], '', ['class' => 'select2', 'id' => 'city_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-check">
            {!! Form::checkbox("is_exempt", true, false, ["class" => "form-check-input", "id" => 'is_exempt']) !!}
            {!! Form::label('is_exempt', __("contact.is_exempt") . ':') !!} @show_tooltip(__("contact.no_taxes_applied"))
          </div>
        </div>
        <div class="col-md-3 payment" style="display: none;">
          <div class="form-group">
            <label>@lang('customer.payment_terms')</label>
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-align-justify"></i></span>
              {!! Form::select('payment_term_id', $payment_terms, '', ['class' => 'select2', 'id' => 'payment_terms_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
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
              {!! Form::text('credit_limit', null, ['class' => 'form-control input_number', "placeholder" => __('lang_v1.credit_limit')]); !!}
            </div>
          </div>
        </div>
        @if($business_debt_to_pay_type == "supplier")
          <input type="hidden" value="{{ $supplier_account }}" id="main_account">
          <div class="col-md-3 supplier_account">
            <div class="form-group">
              {!! Form::label("supplier_catalogue_id", __("contact.supplier_account") . ":") !!}
              {!! Form::select("supplier_catalogue_id", [], null, ["class" => "form-control select_account", "placeholder" => __("contact.supplier_account")]) !!}
            </div>
          </div>
          <div class="col-md-3 provider_account" style="display: none;">
            <div class="form-group">
              {!! Form::label("provider_catalogue_id", __("contact.provider_account") . ":") !!}
              {!! Form::select("provider_catalogue_id", [], null, ["class" => "form-control select_account", "style" => "width: 100%;", "placeholder" => __("contact.provider_account")]) !!}
            </div>
          </div>
        @endif
        <div class="col-md-12">
          <hr/>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('landline', __('contact.landline') . ':') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-phone"></i>
                </span>
                {!! Form::text('landline', null, ['class' => 'form-control', 'placeholder' => __('contact.landline')]); !!}
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
              {!! Form::email('email', null, ['class' => 'form-control','placeholder' => __('business.email')]); !!}
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('mobile', __('contact.mobile') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-mobile"></i>
              </span>
              {!! Form::text('mobile', null, ['class' => 'form-control', 'required', 'placeholder' => __('contact.mobile')]); !!}
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
              {!! Form::text('alternate_number', null, ['class' => 'form-control', 'placeholder' => __('contact.alternate_contact_number')]); !!}
            </div>
          </div>
        </div>
        <data></data>
        <div class="@if(isset($quick_add)) hide @endif"> </div>
        <div class="clearfix"></div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="submit">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}
  
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
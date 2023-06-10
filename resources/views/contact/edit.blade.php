<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('ContactController@update', [$contact->id]), 'method' => 'PUT', 'id' => 'contact_edit_form']) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('contact.edit_contact')</h4>
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
              {!! Form::select('organization_type', $org_type, $contact->organization_type, ['class' => 'select2', 'id' => 'organization_type', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}  
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
                {!! Form::text('name', $contact->name, ['class' => 'form-control','placeholder' => __('contact.name'), 'required']); !!}
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
              {!! Form::text('supplier_business_name', $contact->supplier_business_name, ['class' => 'form-control', 'required', 'placeholder' => __('business.business_name')]); !!}
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-check" style="margin-top: 15px;">
            {!! Form::checkbox("is_supplier", true, $contact->is_supplier, ["class" => "form-check-input is_supplier", 
              "id" => "is_supplier"]) !!}
            {!! Form::label("is_supplier", __("contact.is_supplier"), ["class" => "form-check-label"]) !!}
          </div>
          <div class="form-check">
            {!! Form::checkbox("is_provider", true, $contact->is_provider, ["class" => "form-check-input is_provider", 
              "id" => "is_provider"]) !!}
            {!! Form::label("is_provider", __("contact.is_provider"), ["class" => "form-check-label"]) !!}
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('contact_id', __('lang_v1.contact_id') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-id-badge"></i>
              </span>
              <input type="hidden" id="hidden_id" value="{{$contact->id}}">
              {!! Form::text('contact_id', $contact->contact_id, ['class' => 'form-control','placeholder' => __('lang_v1.contact_id'), 'disabled'=>'disabled']); !!}
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('tax_number', __('contact.tax_no') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-info"></i>
              </span>
              {!! Form::text('tax_number', $contact->tax_number, ['class' => 'form-control', 'placeholder' => __('contact.tax_no'), "required"]); !!}
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
              {!! Form::text('nit', $contact->nit, ['class' => 'form-control','id'=>'nit_edit','required','placeholder' => __('business.nit')]); !!}
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label("business_type_id", __("business.taxpayer_type") . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-briefcase"></i>
              </span>
              {!! Form::select('business_type_id', $business_type, '', ['class' => 'select2', 'id' => 'business_type', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}     
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
              {!! Form::text("business_activity", $contact->business_activity, ["class" => "form-control", "placeholder" => __("contact.business_activity")]) !!}
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
              {!! Form::text('dni', $contact->dni, ['class' => 'form-control', 'id'=>'dni_edit', 'required', 'placeholder' => __('business.dui')]); !!}
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
                  {!! Form::text('landmark', $contact->landmark, ['class' => 'form-control', 'placeholder' => __('business.landmark')]); !!}
              </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>@lang('business.country')</label>
            <div class="input-group">
              <span class="input-group-addon"><i class=" glyphicon glyphicon-flag"></i></span>
              {!! Form::select('country_id', $countries, $contact->country_id, ['class' => 'select2', 'id' => 'country_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>@lang('business.state')</label>
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-link"></i></span>
              {!! Form::select('state_id', $states, $contact->state_id, ['class' => 'select2', 'id' => 'state_id', 
                'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>@lang('business.city')</label>
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-link"></i></span>
              {!! Form::select('city_id', $cities, $contact->city_id, ['class' => 'select2', 'id' => 'city_id', 
                'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-check">
            {!! Form::checkbox("is_exempt", true, $contact->is_exempt, ["class" => "form-check-input", "id" => 'is_exempt']) !!}
            {!! Form::label('is_exempt', __("contact.is_exempt") . ':') !!} @show_tooltip(__("contact.no_taxes_applied"))
          </div>
        </div>
        <div class="col-md-3 payment" style="display: {{ $contact->payment_condition == 'cash' || is_null($contact->payment_condition) ? 'none' : 'block' }}">
          <div class="form-group">
            <label>@lang('customer.payment_terms')</label>
            <div class="input-group">
              <span class="input-group-addon"><i
                class="glyphicon glyphicon-align-justify"></i></span>
                {!! Form::select('payment_term_id', $payment_terms, $contact->payment_term_id, ['class' => 'select2', 'id' => 'payment_terms_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
              </div>
          </div>
        </div>
        <div class="col-md-3 payment" style="display: {{ $contact->payment_condition == 'cash' || is_null($contact->payment_condition) ? 'none' : 'block' }}">
          <div class="form-group">
            {!! Form::label('credit_limit', __('lang_v1.credit_limit') . ':') !!}  @show_tooltip(__('lang_v1.credit_limit_help'))
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-money"></i>
              </span>
              {!! Form::text('credit_limit', $contact->credit_limit, ['class' => 'form-control input_number']); !!}
            </div>
          </div>
        </div>
        @if($business_debt_to_pay_type == "supplier")
          <input type="hidden" value="{{ $supplier_account }}" id="main_account">
          <div class="col-md-3 supplier_account" style="display: @if($contact->is_supplier) block; @else none; @endif">
            <div class="form-group">
              {!! Form::label("supplier_catalogue_id", __("contact.supplier_account") . ":") !!}
              {!! Form::select("supplier_catalogue_id", $account_name, $contact->supplier_catalogue_id, ["class" => "form-control select_account",
                "style" => "width: 100%;", "placeholder" => __("contact.supplier_account")]) !!}
            </div>
          </div>
          <div class="col-md-3 provider_account" style="display: @if($contact->is_provider) block; @else none; @endif">
            <div class="form-group">
              {!! Form::label("provider_catalogue_id", __("contact.provider_account") . ":") !!}
              {!! Form::select("provider_catalogue_id", $account_name, $contact->provider_catalogue_id, ["class" => "form-control select_account",
                "style" => "width: 100%;", "placeholder" => __("contact.provider_account")]) !!}
            </div>
          </div>
        @endif
        <div class="col-md-12">
          <hr/>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('email', __('business.email') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-envelope"></i>
              </span>
              {!! Form::email('email', $contact->email, ['class' => 'form-control','placeholder' => __('business.email')]); !!}
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
                {!! Form::text('mobile', $contact->mobile, ['class' => 'form-control', 'required', 'placeholder' => __('contact.mobile')]); !!}
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
                  {!! Form::text('alternate_number', $contact->alternate_number, ['class' => 'form-control', 'placeholder' => __('contact.alternate_contact_number')]); !!}
              </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
              {!! Form::label('landline', __('contact.landline') . ':') !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-phone"></i>
                  </span>
                  {!! Form::text('landline', $contact->landline, ['class' => 'form-control', 'placeholder' => __('contact.landline')]); !!}
              </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
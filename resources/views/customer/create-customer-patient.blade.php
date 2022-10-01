<div class="modal-dialog modal-lg" role="dialog">
  <div class="modal-content" style="border-radius: 10px;">
    {!! Form::open(['url' => action('CustomerController@storeCustomerAndPatient'), 'method' => 'post', 'id' =>
    'form-add-customer-patient']) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang('customer.add_customer')</h4>
    </div>
    <div class="modal-body">
      {{-- Customer Form --}}
      <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.name')*</label>
            <input type="text" name="name" id="name" value="{{ $customer_name }}" class="form-control"
              placeholder="@lang('customer.name')" required>
            <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
          </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.business_name')</label>
            <input type="text" name="business_name" id="business_name" class="form-control"
              placeholder="@lang('customer.business_name')">
          </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.email')</label>
            <input type="text" name="email" id="email" class="form-control" placeholder="@lang('customer.email')">
          </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.phone')*</label>
            <input type="text" name="telphone" id="telphone" class="form-control" placeholder="@lang('customer.phone')" required>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.dui')</label>
            <input type="text" name="dni" id="dni" class="form-control" placeholder="@lang('customer.dui')">
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.is_taxpayer')</label>
            {!! Form::checkbox('is_taxpayer', '1', false, ['id' => 'is_taxpayer']) !!}
          </div>
        </div>
      </div>

      <div class="row" id="div_taxpayer" style="display: none">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.reg_number')</label>
            <input type="text" name="reg_number" id="reg_number" class="form-control"
              placeholder="@lang('customer.reg_number')">
          </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.tax_number')</label>
            <input type="text" name="tax_number" id="tax_number" class="form-control"
              placeholder="@lang('customer.tax_number')">
          </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.business_line')</label>
            <input type="text" name="business_line" id="business_line" class="form-control"
              placeholder="@lang('customer.business_line')">
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.business_type')</label>
            {!! Form::select('business_type_id', $business_types, '', ['class' => 'select2', 'id' => 'business_type_id',
            'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
          </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.customer_portfolio')</label>
            {!! Form::select('customer_portfolio_id', $customer_portfolios, '', ['class' => 'select2', 'id' =>
            'customer_portfolio_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
          </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.customer_group')</label>
            {!! Form::select('customer_group_id', $customer_groups, '', ['class' => 'select2', 'id' =>
            'customer_portfolios', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.address')</label>
            <input type="text" name="address" id="address" class="form-control" placeholder="@lang('customer.address')">
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.country')</label>
            {!! Form::select('country_id', $countries, '', ['class' => 'select2', 'id' => 'country_id', 'style' =>
            'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
          </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.state')</label>
            {!! Form::select('state_id', [], '', ['class' => 'select2', 'id' => 'state_id', 'style' => 'width: 100%;',
            'placeholder' => __('messages.please_select')]) !!}
          </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.city')</label>
            {!! Form::select('city_id', [], '', ['class' => 'select2', 'id' => 'city_id', 'style' => 'width: 100%;',
            'placeholder' => __('messages.please_select')]) !!}
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.allowed_credit')</label>
            {!! Form::checkbox('allowed_credit', '1', false, ['id' => 'allowed_credit']) !!}
          </div>
        </div>
      </div>

      <div class="row" id="div_credit" style="display: none;">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.opening_balance')</label>
            <input type="text" name="opening_balance" id="opening_balance" class="form-control input_number"
              placeholder="@lang('customer.opening_balance')">
          </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.credit_limit')</label>
            <input type="text" name="credit_limit" id="credit_limit" class="form-control input_number"
              placeholder="@lang('customer.credit_limit')">
          </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
          <div class="form-group">
            <label>@lang('customer.payment_terms')</label>
            {!! Form::select('payment_terms_id', $payment_terms, '', ['class' => 'select2', 'id' => 'payment_terms_id',
            'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
          <div class="form-group">
            <label>@lang('patient.is_patient')</label>
            {!! Form::checkbox('is_patient', '1', false, ['id' => 'is_patient']) !!}
          </div>
        </div>
      </div>
      
      {{-- Patient Form --}}
      <div id="patient_form" style="display: none;">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('code', __('customer.code') . '*') !!}
              <div class="wrap-inputform">
                {!! Form::text('code', $code, ['class' => 'form-control text-center', 'readonly',
                'placeholder' => __('customer.code'), 'id' => 'code']) !!}
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('age', __('customer.age') . '*') !!}
              {!! Form::number('age', null, ['class' => 'form-control text-center', 'placeholder' =>
              __('customer.age'), 'id' => 'age']) !!}
            </div>
          </div>
  
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('sex', __('customer.sex') . '*') !!}
              {!! Form::select('sex', $sexs, null, ['class' => 'form-control select2', 'placeholder' =>
              __('messages.please_select'), 'id' => 'sex', 'style' => 'width: 100%;']) !!}
            </div>
          </div>
        </div>
  
        <div class="row">
          <div class="col-md-3">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" onclick="showgraduationbox()" id="chkhas_glasses">
              <label class="form-check-label" for="chkhas_glasses">@lang('customer.has_glasses')</label>
            </div>
          </div>
  
          <div class="col-md-9" id="graduation_box" style="display: none">
            {!! Form::label('glasses_graduation', __('customer.glasses_graduation')) !!}
            {!! Form::text('glasses_graduation', null, ['class' => 'form-control text-center', 'placeholder' =>
            __('customer.glasses_graduation'), 'id' => 'glasses_graduation']) !!}
          </div>
        </div>
        <hr>
  
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('location_id', __('customer.location') . '*') !!}
              @if ($business_locations->count() == 1)
                {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2',
                'readonly', 'id' => 'location_id', 'style' => 'width: 100%;']) !!}
              @else
                {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2',
                'placeholder' => __('messages.please_select'), 'id' => 'location_id', 'style' => 'width: 100%;']) !!}
              @endif
            </div>
          </div>
  
          <div class="col-md-8">
            <div class="form-group">
              <label>@lang('customer.notes')</label>
              <textarea name="txt-notes" id="txt-notes" class="form-control"></textarea>
            </div>
          </div>
        </div>
  
        <div class="row">
          <div class="col-md-4">
            <div class="input-group">
              {!! Form::text('employee_code', null, ['class' => 'form-control text-center', 'placeholder' =>
              __('customer.employee_code'), 'id' => 'employee_code']) !!}
              <span class="input-group-btn">
                <button type="button" onclick="SearchEmployee()" class="btn btn-info btn-flat">Go!</button>
              </span>
            </div>
          </div>
  
          <div class="col-md-8">
            <div class="form-group">
              {!! Form::text('employee_name', null, ['class' => 'form-control text-center', 'readonly', 'id' =>
              'employee_name']) !!}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
      <button type="button" data-dismiss="modal" aria-label="Close"
        class="btn btn-default">@lang('messages.close')</button>
    </div>
    {!! Form::close() !!}
  </div>
</div>

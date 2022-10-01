<div class="modal-dialog modal-lg" role="dialog" id="modal-create">
  <div class="modal-content" style="border-radius: 10px;">
    {!! Form::open(['url' => action('Optics\LabOrderController@store'), 'method' => 'post', 'id' => 'lab_order_add_form']) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang('lab_order.add_lab_order')</h4>
    </div>

    <div class="modal-body">
      {{-- header --}}
      <div class="row">
        {{-- patient_id --}}
        <div class="col-sm-8">
          <div class="form-group">
            {!! Form::label('patient_id', __('graduation_card.patient') . ':') !!}
            {!! Form::select('patient_id', $patients, '', ['class' => 'form-control select2', 'required']) !!}
          </div>
        </div>

        {{-- no_order --}}
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('no_order', __('lab_order.no_order') . ':') !!}
            {!! Form::text('no_order', $code, ['class' => 'form-control', 'readonly']) !!}
          </div>
        </div>

        {{-- customer_id --}}
        <div class="col-sm-8">
          <div class="form-group">
            {!! Form::label('customer_id', __('contact.customer') . ':') !!}
            {!! Form::select('lab_customer_id', $customers, '', ['class' => 'form-control select2', 'required']) !!}
          </div>
        </div>

        {{-- is_repair --}}
        <div class="col-sm-4">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('is_repair', 1, null, ['class' => 'input-icheck', 'id' => 'is_repair', 'onclick' =>
                'repairCheck()']) !!}
                <strong>@lang('lab_order.repair')</strong>
              </label>
            </div>
          </div>
        </div>
      </div>

      {{-- graduation card table --}}
      <div class="row graduation_card_fields">
        <div class="col-sm-12">
          <table class="table table-bordered text-center vt-align">
            <tbody>
              <tr>
                <th></th>
                <th>@lang('graduation_card.sphere_abbreviation')</th>
                <th>@lang('graduation_card.cylindir_abbreviation')</th>
                <th>@lang('graduation_card.axis_mayus')</th>
                <th>@lang('graduation_card.prism_mayus')</th>
                <th>@lang('graduation_card.addition_mayus')</th>
              </tr>

              <tr>
                <th>
                  @lang('graduation_card.od')
                  {{-- balance_od --}}
                  <div class="checkbox" style="margin-top: 0; margin-bottom: 0">
                    <label>
                      {!! Form::checkbox('balance_od', 1, null, ['class' => 'input-icheck', 'id' => 'balance_od',
                      'onclick' => 'balanceOD()']) !!}
                      <small>@lang('graduation_card.balance')</small>
                    </label>
                  </div>
                </th>

                {{-- sphere_od --}}
                <td>
                  {!! Form::text('sphere_od', null, ['class' => 'form-control input_graduation']) !!}
                </td>
                {{-- cylindir_od --}}
                <td>
                  {!! Form::text('cylindir_od', null, ['class' => 'form-control input_graduation']) !!}
                </td>
                {{-- axis_od --}}
                <td>
                  {!! Form::text('axis_od', null, ['class' => 'form-control input_only_number']) !!}
                </td>
                {{-- base_od --}}
                <td>
                  {!! Form::text('base_od', null, ['class' => 'form-control input_prism']) !!}
                </td>
                {{-- addition_od --}}
                <td>
                  {!! Form::text('addition_od', null, ['class' => 'form-control input_graduation']) !!}
                </td>
              </tr>

              <tr>
                <th>
                  @lang('graduation_card.os')
                  {{-- balance_os --}}
                  <div class="checkbox" style="margin-top: 0; margin-bottom:">
                    <label>
                      {!! Form::checkbox('balance_os', 1, null, ['class' => 'input-icheck', 'id' => 'balance_os',
                      'onclick' => 'balanceOS()']) !!}
                      <small>@lang('graduation_card.balance')</small>
                    </label>
                  </div>
                </th>

                {{-- sphere_os --}}
                <td>
                  {!! Form::text('sphere_os', null, ['class' => 'form-control input_graduation']) !!}
                </td>
                {{-- cylindir_os --}}
                <td>
                  {!! Form::text('cylindir_os', null, ['class' => 'form-control input_graduation']) !!}
                </td>
                {{-- axis_os --}}
                <td>
                  {!! Form::text('axis_os', null, ['class' => 'form-control input_only_number']) !!}
                </td>
                {{-- base_os --}}
                <td>
                  {!! Form::text('base_os', null, ['class' => 'form-control input_prism']) !!}
                </td>
                {{-- addition_os --}}
                <td>
                  {!! Form::text('addition_os', null, ['class' => 'form-control input_graduation']) !!}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      {{-- extra graduation card fields --}}
      <div class="row graduation_card_fields">
        {{-- dnsp --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('dnsp', __('graduation_card.dnsp') . ': ') !!}
            {!! Form::text('dnsp_od', null, ['class' => 'form-control di-mask', 'placeholder' =>
            __('graduation_card.od'), 'style' => 'margin-bottom: 3px;']) !!}
            {!! Form::text('dnsp_os', null, ['class' => 'form-control di-mask', 'placeholder' =>
            __('graduation_card.os')]) !!}
          </div>
        </div>

        {{-- dp --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('di', __('graduation_card.di') . ': ') !!}
            {!! Form::text('di', null, ['class' => 'form-control di-mask', 'placeholder' => __('graduation_card.di')])
            !!}
          </div>
        </div>

        {{-- ao --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('ao', __('graduation_card.ao') . ': ') !!}
            {!! Form::text('ao', null, ['class' => 'form-control input_graduation', 'placeholder' =>
            __('graduation_card.ao')]) !!}
          </div>
        </div>

        {{-- ap --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('ap', __('graduation_card.ap') . ': ') !!}
            {!! Form::text('ap', null, ['class' => 'form-control input_graduation', 'placeholder' =>
            __('graduation_card.ap')]) !!}
          </div>
        </div>
      </div>

      {{-- hoop fields --}}
      <div class="row">
        {{-- hoop (variation_id) --}}
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('hoop', __('graduation_card.ring') . ': ') !!}
            {!! Form::select('hoop', $products, null, ['style' => 'width: 100%', 'class' => 'form-control',
            'placeholder' => __('messages.please_select'), 'id' => 'hoop']) !!}
          </div>

          {{-- is_own_hoop --}}
          <div class="checkbox" style="margin-top: 0; margin-bottom:">
            <label>
              {!! Form::checkbox('is_own_hoop', 1, null, ['class' => 'input-icheck', 'id' => 'is_own_hoop']) !!}
              <strong>@lang('lab_order.own_hoop')</strong>
            </label>
          </div>
        </div>

        {{-- size --}}
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('size', __('graduation_card.size') . ': ') !!}
            {!! Form::text('size', null, ['class' => 'form-control size-mask', 'placeholder' =>
            __('graduation_card.size')]) !!}
          </div>
        </div>

        {{-- color --}}
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('color', __('graduation_card.color') . ': ') !!}
            {!! Form::text('color', null, ['class' => 'form-control', 'placeholder' => __('graduation_card.color')]) !!}
          </div>
        </div>
      </div>

      {{-- final fields --}}
      <div class="row" style="margin-top: 10px;">
        <div class="col-sm-8">
          {{-- hoop_type --}}
          <label>@lang('lab_order.hoop_type')</label>
          <br>
          <label class="radio-inline">
            {!! Form::radio('hoop_type', 'full', null) !!} @lang('lab_order.full')
          </label>
          <label class="radio-inline">
            {!! Form::radio('hoop_type', 'semi_air', null) !!} @lang('lab_order.semi_air')
          </label>
          <label class="radio-inline">
            {!! Form::radio('hoop_type', 'air', null) !!} @lang('lab_order.air')
          </label>
          <br>

          {{-- glass (variation_id) --}}
          <div class="form-group" style="margin-top: 10px;">
            {!! Form::label('glass', __('lab_order.glass') . ': ') !!}
            {!! Form::select('glass', $products, null, ['style' => 'width: 100%', 'class' => 'form-control',
            'placeholder' => __('messages.please_select'), 'id' => 'glass']) !!}
          </div>

          {{-- job_type --}}
          <div class="form-group" style="margin-top: 10px;">
            {!! Form::label('job_type', __('lab_order.job_type') . ': ') !!}
            {!! Form::textarea('job_type', null, ['class' => 'form-control', 'placeholder' => __('lab_order.job_type'),
            'rows' => '3']) !!}
          </div>

          {{-- check_ext_lab --}}
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('check_ext_lab', 1, null, ['class' => 'input-icheck', 'id' => 'check_ext_lab',
                'onclick' => 'extLabCheck()']) !!}
                <strong>@lang('lab_order.send_to_external_lab')</strong>
              </label>
            </div>
          </div>

          {{-- lab_extern_id --}}
          <div class="form-group" id="lab_extern_box" style="display: none">
            {!! Form::label('external_lab_id', __('lab_order.external_laboratory') . ':*') !!}
            {!! Form::select('external_lab_id', $external_labs, '', ['class' => 'form-control select2', 'placeholder' =>
            __('messages.please_select'), 'required', 'style' => 'width: 100%']) !!}
          </div>
        </div>

        <div class="col-sm-4">
          {{-- ar --}}
          <label>@lang('lab_order.ar')</label>
          <div class="radio" style="margin-top: 0;">
            <label>
              {!! Form::radio('ar', 'green', null) !!}
              @lang('lab_order.ar_green')
            </label>
          </div>
          <div class="radio">
            <label>
              {!! Form::radio('ar', 'blue', null) !!}
              @lang('lab_order.ar_blue')
            </label>
          </div>
          <div class="radio">
            <label>
              {!! Form::radio('ar', 'premium', null) !!}
              @lang('lab_order.ar_premium')
            </label>
          </div>

          {{-- status --}}
          <label>@lang('accounting.status')</label>
          @foreach ($status_lab_orders as $key => $slo)
            <div class="radio" @if ($key == 0) style="margin-top: 0;"
          @endif>
          <label>
            @if ($slo->name == 'Por segunda vez')
              {!! Form::radio('status_lab_order_id', $slo->id, null, ['class' => 'second_time']) !!}
            @else
              {!! Form::radio('status_lab_order_id', $slo->id, null) !!}
            @endif
            {{ $slo->name }}
          </label>
        </div>
        @endforeach

        {{-- delivery --}}
        <div class="form-group">
          <label>@lang('lab_order.date_hour_delivery')</label>
          <div class="form-group">
            <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
              <input type="text" class="form-control datetimepicker-input" data-toggle="datetimepicker"
                data-target="#datetimepicker1" name="delivery" id="delivery"
                placeholder="@lang('lab_order.date_hour_delivery')" required>
              <span class="input-group-addon" data-target="#datetimepicker1" data-toggle="datetimepicker">
                <span class="glyphicon glyphicon-calendar"></span>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Search material --}}
    <div class="row">
      {{-- select_location_id --}}
      <div class="col-sm-4">
        <div class="form-group">
          <label>@lang('accounting.location')</label>
          {!! Form::select('select_location_id', $business_locations, null,
            ['class' => 'select2', 'placeholder' => __('lang_v1.select_location'), 'id' => 'select_location_id',
            'required', 'autofocus', 'style' => 'width: 100%'], $bl_attributes) !!}
          {!! Form::hidden('location_id', $default_location, ['id' => 'location_id']) !!}
        </div>
      </div>

      {{-- warehouse_id --}}
      <div class="col-sm-4">
        <div class="form-group">
          <label>@lang('warehouse.warehouse')</label>
          {!! Form::select('warehouse_id', $warehouses, null, ['class' => 'select2', 'id' => 'warehouse_id', 'style' =>
          'width: 100%', 'placeholder' => __('messages.please_select')]) !!}
        </div>
      </div>

      {{-- search_product --}}
      <div class="col-sm-4">
        <div class="form-group">
          <label>@lang('material.clasification_material')</label>
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-barcode"></i>
            </span>
            {!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product',
            'placeholder' => __('messages.please_select'), 'disabled' => is_null($default_location) ? true : false,
            'autofocus' => is_null($default_location) ? false : true]) !!}
          </div>
        </div>
      </div>
    </div>

    {{-- Table --}}
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <th style="width: 7%;">Op</th>
              <th style="width: 10%;">@lang('order.code')</th>
              <th style="width: 53%;">@lang('order.description')</th>
              <th style="width: 15%;">@lang('order.available')</th>
              <th style="width: 15%;">@lang('order.quantity')</th>
            </thead>
            <tbody id="list">
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="modal-footer">
    <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-order">
    <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default"
      id="btn-close-modal-add-order">@lang('messages.close')</button>
  </div>
  {!! Form::close() !!}
</div>
</div>

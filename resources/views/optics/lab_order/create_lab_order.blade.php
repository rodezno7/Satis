<div class="modal-dialog modal-lg" role="dialog" id="modal-create">
  <div class="modal-content" style="border-radius: 10px;">
    {{-- Form --}}
    {!! Form::open(['url' => action('Optics\LabOrderController@store'), 'method' => 'post', 'id' => 'lab_order_add_form', 'files' => true]) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang('lab_order.add_lab_order')</h4>
    </div>

    <div class="modal-body">
      {{-- Header --}}
      <div class="row">
        {{-- product-type --}}
        <input type="hidden" id="product-type" value="">

        <input type="hidden" id="default_location" value="{{ $default_location }}">

        {{-- business_location_id --}}
        <div class="col-sm-8">
          <div class="form-group">
            {!! Form::label('location_lo', __('accounting.location') . ':') !!}
            {!! Form::select('location_lo', $business_locations, $default_location, [
              'class' => 'form-control select2',
              'required',
              'placeholder' => __('messages.please_select'),
              'id' => 'location_lo'
            ]) !!}
          </div>
        </div>

        {{-- no_invoice --}}
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('invoice_lo', __('document_type.invoice') . ':') !!}
            {!! Form::text('invoice_lo', '', ['class' => 'form-control', 'required']) !!}
          </div>
        </div>

        {{-- patient_id --}}
        <div class="col-sm-8">
          <div class="form-group">
            {!! Form::label('patient_id', __('graduation_card.patient') . ':') !!}
            {!! Form::select('patient_id', [], null, [
              'id' => 'patient_id',
              'class' => 'form-control',
              'required',
              'placeholder' => __('messages.please_select'),
              'style' => 'width: 100%;'
            ]) !!}
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
            {!! Form::select('lab_customer_id', [], null, [
              'class' => 'form-control select2',
              'id' => 'lab_customer_id',
              'placeholder' => __('messages.please_select'),
              'style' => 'width: 100%;'
            ]) !!}
          </div>
        </div>

        {{-- is_reparation --}}
        <div class="col-sm-4">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('is_reparation', 1, null, ['class' => 'input-icheck', 'id' => 'is_repair', 'onclick' =>
                'repairCheck()']) !!}
                <strong>@lang('lab_order.repair')</strong>
              </label>
            </div>
          </div>
        </div>

        <div class="clearfix"></div>

        <div class="graduation_card_fields">
          {{-- optometrist --}}
          <div class="col-sm-3">
            {!! Form::label('optometrist', __('graduation_card.optometrist') . ':') !!}
            <div class="input-group">
              {!! Form::text('input-optometrist', null,
                ['class' => 'form-control text-center', 'placeholder' => __('customer.employee_code'),
                'id' => 'input-optometrist']) !!}
              <span class="input-group-btn">
                <button type="button" id="btn-optometrist" class="btn btn-info btn-flat">
                  <i class="fa fa-search"></i>
                </button>
              </span>
            </div>
          </div>
          <div class="col-sm-5">
            <div class="form-group">
              {!! Form::text('txt-optometrist', null,
                ['class' => 'form-control text-center', 'readonly', 'id' => 'txt-optometrist', 'style' => 'margin-top: 24px;']) !!}
            </div>
          </div>
          {!! Form::hidden('optometrist', null, ['id' => 'optometrist']) !!}
  
          {{-- is_prescription --}}
          <div class="col-sm-4">
            <div class="form-group">
              <label>
                {!! Form::checkbox('is_prescription', 1, null,
                  ['class' => 'input-icheck', 'id' => 'is_prescription', 'onclick' => 'optometristBlock()']); !!}
                <strong>&nbsp;@lang('graduation_card.prescription')</strong>
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
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('hoop', __('graduation_card.ring') . ': ') !!}

            {!! Form::text('hoop_name', '',
              ['class' => 'form-control', 'style' => 'display: none', 'id' => 'hoop_name', 'placeholder' => __('graduation_card.ring')]) !!}
              
            <div id="div_hoop" class="select-glass-hoop">
              {!! Form::select('hoop', [], null, [
                'class' => 'form-control lab-order-products',
                'required',
                'placeholder' => __('lang_v1.none'),
                'style' => '100%',
                'id' => 'hoop_c',
                'data-product' => 'hoop'
              ]) !!}
            </div>
          </div>

          {{-- is_own_hoop --}}
          <div class="checkbox" style="margin-top: 0; margin-bottom:">
            <label>
              {!! Form::checkbox('is_own_hoop', 1, null,
                ['class' => 'input-icheck', 'id' => 'is_own_hoop', 'onclick' => 'checkOwnHoop()']) !!}
              <strong>@lang('lab_order.own_hoop')</strong>
            </label>
          </div>
        </div>

        {{-- size --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('size', __('graduation_card.size') . ': ') !!}
            {!! Form::text('size', '',
              ['class' => 'form-control size-mask size_c', 'placeholder' => __('graduation_card.size'), 'id' => 'size_c']) !!}
          </div>
        </div>

        {{-- color --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('color', __('graduation_card.color') . ': ') !!}
            {!! Form::text('color', '',
              ['class' => 'form-control color_c', 'placeholder' => __('graduation_card.color'), 'id' => 'color_c']) !!}
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

          {{-- glasses --}}
          <div class="form-group" style="margin-top: 10px;">
            {!! Form::label('glass', __('lab_order.glass') . ': ') !!}

            {{-- glass_vs (variation_id) --}}
            <div class="form-group select-glass-hoop" style="margin-bottom: 3px;">
              <div class="input-group">
                <span class="input-group-addon">@lang('graduation_card.vs') o B.I.</span>
                {!! Form::select('glass', [], null, [
                  'class' => 'form-control lab-order-products',
                  'required',
                  'placeholder' => __('lang_v1.none'),
                  'id' => 'glass_c',
                  'data-product' => 'glass_c'
                ]) !!}
              </div>
            </div>

            {{-- glass_od (variation_id) --}}
            <div class="form-group select-glass-hoop" style="margin-bottom: 3px;">
              <div class="input-group">
                <span class="input-group-addon">@lang('graduation_card.od')</span>
                {!! Form::select('glass_od', [], null, [
                  'class' => 'form-control lab-order-products',
                  'required',
                  'placeholder' => __('lang_v1.none'),
                  'id' => 'glass_od_c',
                  'data-product' => 'glass_od'
                ]) !!}
              </div>
            </div>

            {{-- glass_os (variation_id) --}}
            <div class="form-group select-glass-hoop">
              <div class="input-group">
                <span class="input-group-addon">@lang('graduation_card.os')</span>
                {!! Form::select('glass_os', [], null, [
                  'class' => 'form-control lab-order-products',
                  'required',
                  'placeholder' => __('lang_v1.none'),
                  'id' => 'glass_os_c',
                  'data-product' => 'glass_os'
                ]) !!}
              </div>
            </div>
          </div>

          {{-- job_type --}}
          <div class="form-group" style="margin-top: 10px;">
            {!! Form::label('job_type', __('lab_order.job_type') . ': ') !!}
            {!! Form::textarea('job_type', null, ['class' => 'form-control', 'placeholder' => __('lab_order.job_type'),
            'rows' => '3']) !!}
          </div>
        </div>

        <div class="col-sm-4">
          {{-- ar --}}
          <label>@lang('lab_order.ar')</label>
          
          <div class="radio" style="margin-top: 0;">
            <label>
              {!! Form::radio('ar', 'green', false) !!}
              @lang('lab_order.ar_green')
            </label>
          </div>
          <div class="radio">
            <label>
              {!! Form::radio('ar', 'blue', false) !!}
              @lang('lab_order.ar_blue')
            </label>
          </div>
          <div class="radio">
            <label>
              {!! Form::radio('ar', 'premium', false) !!}
              @lang('lab_order.ar_premium')
            </label>
          </div>

          {{-- delivery --}}
          <div class="form-group">
            <label>@lang('lab_order.date_hour_delivery')</label>
            <div class="form-group">
              <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                <input type="text" class="form-control datetimepicker-input" data-toggle="datetimepicker"
                  data-target="#datetimepicker1" name="delivery" id="delivery" value="{{ $date_delivery }}"
                  placeholder="@lang('lab_order.date_hour_delivery')" required>
                <span class="input-group-addon" data-target="#datetimepicker1" data-toggle="datetimepicker">
                  <span class="glyphicon glyphicon-calendar"></span>
                </span>
              </div>
            </div>
          </div>

          {{-- document --}}
          <div class="form-group">
            {!! Form::label('document', __('purchase.attach_document') . ':') !!}
            {!! Form::file('document', ['id' => 'upload_document']); !!}
            <p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])</p>
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

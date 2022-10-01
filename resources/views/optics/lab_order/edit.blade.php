{!! Form::open(['method' => 'PUT', 'id' => 'form-edit-order', 'files' => true]) !!}
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" id="modal-edit-order">
  <div class="modal-dialog modal-lg" role="dialog" id="modal-edit">
    <div class="modal-content" style="border-radius: 10px;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">@lang('lab_order.edit_lab_order')</h4>
      </div>

      <div class="modal-body">
        {{-- header --}}
        <div class="row">
          {{-- lab_order_id --}}
          <input type="hidden" name="order_id" id="order_id">

          {{-- business_location_id --}}
          <div class="col-sm-8">
            <div class="form-group">
              {!! Form::label('elocation_lo', __('accounting.location') . ':') !!}

              {!! Form::select('elocation_lo', $business_locations, '', [
                'class' => 'form-control select2',
                'required',
                'placeholder' => __('messages.please_select'),
                'id' => 'elocation_lo',
                'style' => 'width: 100%;'
              ]) !!}
            </div>
          </div>

          {{-- no_invoice --}}
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('einvoice_lo', __('document_type.invoice') . ':') !!}
              {!! Form::text('einvoice_lo', '', ['class' => 'form-control', 'required', 'id' => 'einvoice_lo']) !!}
            </div>
          </div>

          {{-- patient_id --}}
          <div class="col-sm-8">
            <div class="form-group">
              {!! Form::label('patient_id', __('graduation_card.patient') . ':') !!}

              {!! Form::select('epatient_id', [], '', [
                'class' => 'form-control',
                'required',
                'id' => 'epatient_id',
                'placeholder' => __('lang_v1.none'),
                'style' => 'width: 100%;'
              ]) !!}
            </div>
          </div>

          {{-- no_order --}}
          <div class="col-sm-4">
            <div class="form-group">
              {!! Form::label('no_order', __('lab_order.no_order') . ':') !!}
              {!! Form::text('eno_order', '', ['class' => 'form-control', 'readonly']) !!}
            </div>
          </div>

          {{-- customer_id --}}
          <div class="col-sm-8">
            <div class="form-group">
              {!! Form::label('customer_id', __('contact.customer') . ':') !!}

              {!! Form::select('ecustomer_id', [], '', [
                'class' => 'form-control',
                'required',
                'id' => 'ecustomer_id',
                'placeholder' => __('lang_v1.none'),
                'style' => 'width: 100%;'
              ]) !!}
            </div>
          </div>

          {{-- is_reparation --}}
          <div class="col-sm-4">
            <div class="form-group">
              <div class="checkbox">
                <label>
                  {!! Form::checkbox('eis_reparation', 1, null, ['class' => 'input-icheck', 'id' => 'eis_repair', 'onclick' =>
                  'erepairCheck()']) !!}
                  <strong>@lang('lab_order.repair')</strong>
                </label>
              </div>
            </div>
          </div>

          {{-- optometrist --}}
          <div class="col-sm-8 graduation_card_fields">
            <div class="form-group">
              {!! Form::label('optometrist', __('graduation_card.optometrist') . ':') !!}

              {!! Form::select('eoptometrist', $employees, '', [
                'class' => 'form-control select2',
                'id' => 'eoptometrist',
                'placeholder' => __('lang_v1.none'),
                'style' => 'width: 100%;'
              ]) !!}
            </div>
          </div>

          {{-- is_prescription --}}
          <div class="col-sm-4 graduation_card_fields">
            <div class="form-group">
              <label>
                {!! Form::checkbox('eis_prescription', 1, null,
                  ['class' => 'input-icheck', 'id' => 'eis_prescription', 'onclick' => 'optometristBlock()']); !!}
                <strong>&nbsp;@lang('graduation_card.prescription')</strong>
              </label>
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
                        {!! Form::checkbox('ebalance_od', 1, null, ['class' => 'input-icheck', 'id' => 'ebalance_od',
                        'onclick' => 'balanceOD()']) !!}
                        <small>@lang('graduation_card.balance')</small>
                      </label>
                    </div>
                  </th>

                  {{-- sphere_od --}}
                  <td>
                    {!! Form::text('esphere_od', '', ['class' => 'form-control input_graduation', 'id' => 'esphere_od']) !!}
                  </td>
                  {{-- cylindir_od --}}
                  <td>
                    {!! Form::text('ecylindir_od', '', ['class' => 'form-control input_graduation', 'id' => 'ecylindir_od']) !!}
                  </td>
                  {{-- axis_od --}}
                  <td>
                    {!! Form::text('eaxis_od', '', ['class' => 'form-control input_only_number']) !!}
                  </td>
                  {{-- base_od --}}
                  <td>
                    {!! Form::text('ebase_od', '', ['class' => 'form-control input_prism']) !!}
                  </td>
                  {{-- addition_od --}}
                  <td>
                    {!! Form::text('eaddition_od', '', ['class' => 'form-control input_graduation']) !!}
                  </td>
                </tr>

                <tr>
                  <th>
                    @lang('graduation_card.os')
                    {{-- balance_os --}}
                    <div class="checkbox" style="margin-top: 0; margin-bottom:">
                      <label>
                        {!! Form::checkbox('ebalance_os', 1, null, ['class' => 'input-icheck', 'id' => 'ebalance_os',
                        'onclick' => 'balanceOS()']) !!}
                        <small>@lang('graduation_card.balance')</small>
                      </label>
                    </div>
                  </th>

                  {{-- sphere_os --}}
                  <td>
                    {!! Form::text('esphere_os', '', ['class' => 'form-control input_graduation']) !!}
                  </td>
                  {{-- cylindir_os --}}
                  <td>
                    {!! Form::text('ecylindir_os', '', ['class' => 'form-control input_graduation']) !!}
                  </td>
                  {{-- axis_os --}}
                  <td>
                    {!! Form::text('eaxis_os', '', ['class' => 'form-control input_only_number']) !!}
                  </td>
                  {{-- base_os --}}
                  <td>
                    {!! Form::text('ebase_os', '', ['class' => 'form-control input_prism']) !!}
                  </td>
                  {{-- addition_os --}}
                  <td>
                    {!! Form::text('eaddition_os', '', ['class' => 'form-control input_graduation']) !!}
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
              {!! Form::text('ednsp_od', '', ['class' => 'form-control di-mask', 'placeholder' =>
              __('graduation_card.od'), 'style' => 'margin-bottom: 3px;']) !!}
              {!! Form::text('ednsp_os', '', ['class' => 'form-control di-mask', 'placeholder' =>
              __('graduation_card.os')]) !!}
            </div>
          </div>

          {{-- dp --}}
          <div class="col-sm-3">
            <div class="form-group">
              {!! Form::label('di', __('graduation_card.di') . ': ') !!}
              {!! Form::text('edi', '', ['class' => 'form-control di-mask', 'placeholder' => __('graduation_card.di')])
              !!}
            </div>
          </div>

          {{-- ao --}}
          <div class="col-sm-3">
            <div class="form-group">
              {!! Form::label('ao', __('graduation_card.ao') . ': ') !!}
              {!! Form::text('eao', '', ['class' => 'form-control input_graduation', 'placeholder' =>
              __('graduation_card.ao')]) !!}
            </div>
          </div>

          {{-- ap --}}
          <div class="col-sm-3">
            <div class="form-group">
              {!! Form::label('ap', __('graduation_card.ap') . ': ') !!}
              {!! Form::text('eap', '', ['class' => 'form-control input_graduation', 'placeholder' =>
              __('graduation_card.ap')]) !!}
            </div>
          </div>
        </div>

        {{-- search warehouse --}}
        <input type="hidden" id="default_warehouse" value="{{ $default_warehouse }}">

        <div class="row" style="background-color: ghostwhite; padding-top: 15px;">
          {{-- warehouse_id --}}
          <div class="col-sm-6">
            <div class="form-group">
              <label>@lang('warehouse.warehouse')</label>
              {!! Form::select('eselect_warehouse_id', $warehouses, null, [
                'class' => 'select2',
                'id' => 'eselect_warehouse_id',
                'style' => 'width: 100%',
                'placeholder' => __('messages.please_select'),
                'disabled'
              ]) !!}
              {!! Form::hidden('ewarehouse_id', $default_location, ['id' => 'ewarehouse_id']) !!}
              {!! Form::hidden('elocation_id', $default_location, ['id' => 'elocation_id']) !!}
            </div>
          </div>
  
          {{-- search_product --}}
          <div class="col-sm-6">
            <div class="form-group">
              <label>@lang('material.clasification_material')</label>
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-barcode"></i>
                </span>
                {!! Form::text('esearch_product', null, ['class' => 'form-control mousetrap', 'id' => 'esearch_product',
                'placeholder' => __('messages.please_select'), 'disabled' => is_null($default_location) ? true : false,
                'autofocus' => is_null($default_location) ? false : true]) !!}
              </div>
            </div>
          </div>
        </div>
  
        {{-- Table --}}
        <div class="row" style="background-color: ghostwhite;">
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
                <tbody id="elist">
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {{-- hoop fields --}}
        <div class="row" style="padding-top: 15px;">
          {{-- hoop (variation_id) --}}
          <div class="col-sm-6">
            <div class="form-group">
              {!! Form::label('hoop', __('graduation_card.ring') . ': ') !!}
              {!! Form::text('ehoop_name', '', ['class' => 'form-control', 'placeholder' => __('graduation_card.ring'), 'style' => 'display: none;']) !!}
              {!! Form::select('ehoop', [], null, [
                'style' => 'width: 100%',
                'class' => 'form-control',
                'placeholder' => __('lang_v1.none'),
                'id' => 'ehoop',
                'disabled',
                'style' => 'display: none;'
              ]) !!}
            </div>

            {{-- is_own_hoop --}}
            <div class="checkbox" style="margin-top: 0; margin-bottom:">
              <label>
                {!! Form::checkbox('eis_own_hoop', 1, null, ['class' => 'input-icheck', 'id' => 'eis_own_hoop']) !!}
                <strong>@lang('lab_order.own_hoop')</strong>
              </label>
            </div>
          </div>

          {{-- size --}}
          <div class="col-sm-3">
            <div class="form-group">
              {!! Form::label('size', __('graduation_card.size') . ': ') !!}
              {!! Form::text('esize', '', ['class' => 'form-control', 'placeholder' => __('graduation_card.size')]) !!}
            </div>
          </div>

          {{-- color --}}
          <div class="col-sm-3">
            <div class="form-group">
              {!! Form::label('color', __('graduation_card.color') . ': ') !!}
              {!! Form::text('ecolor', '', ['class' => 'form-control', 'placeholder' => __('graduation_card.color')]) !!}
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
              {!! Form::radio('ehoop_type', 'full', null, ['class' => 'ht_rb']) !!} @lang('lab_order.full')
            </label>
            <label class="radio-inline">
              {!! Form::radio('ehoop_type', 'semi_air', null, ['class' => 'ht_rb']) !!} @lang('lab_order.semi_air')
            </label>
            <label class="radio-inline">
              {!! Form::radio('ehoop_type', 'air', null, ['class' => 'ht_rb']) !!} @lang('lab_order.air')
            </label>
            <br>

            <div class="form-group" style="margin-top: 10px;">
              {!! Form::label('glass', __('lab_order.glass') . ': ') !!}

              <div class="form-group" id="div_glass" style="margin-bottom: 3px; display: none;">
                <div class="input-group">
                  <span class="input-group-addon">@lang('graduation_card.vs') o B.I.</span>
                  {{-- glass_vs (variation_id) --}}
                  {{-- {!! Form::select('eglass', $has_glass, null, ['style' => 'width: 100%', 'class' => 'form-control',
                    'placeholder' => __('lang_v1.none'), 'id' => 'eglass', 'disabled']) !!} --}}
                  {!! Form::textarea('eglass', null, ['class' => 'form-control', 'id' => 'eglass', 'rows' => '1', 'disabled']) !!}
                </div>
              </div>

              <div class="form-group" id="div_glass_od" style="margin-bottom: 3px; display: none;">
                <div class="input-group">
                  <span class="input-group-addon">@lang('graduation_card.od')</span>
                  {{-- glass_od (variation_id) --}}
                  {{-- {!! Form::select('eglass_od', $has_glass_od, null, ['style' => 'width: 100%', 'class' => 'form-control',
                    'placeholder' => __('lang_v1.none'), 'id' => 'eglass_od', 'disabled']) !!} --}}
                  {!! Form::textarea('eglass_od', null, ['class' => 'form-control', 'id' => 'eglass_od', 'rows' => '1', 'disabled']) !!}
                </div>
              </div>

              <div class="form-group" id="div_glass_os" style="margin-bottom: 3px; display: none;">
                <div class="input-group">
                  <span class="input-group-addon">@lang('graduation_card.os')</span>
                  {{-- glass_os (variation_id) --}}
                  {{-- {!! Form::select('eglass_os', $has_glass_os, null, ['style' => 'width: 100%', 'class' => 'form-control',
                    'placeholder' => __('lang_v1.none'), 'id' => 'eglass_os', 'disabled']) !!} --}}
                  {!! Form::textarea('eglass_os', null, ['class' => 'form-control', 'id' => 'eglass_os', 'rows' => '1', 'disabled']) !!}
                </div>
              </div>

              <div class="form-group" id="div_glass_empty" style="margin-bottom: 3px; display: none;">
                {{-- glass_empty (variation_id) --}}
                {!! Form::select('eglass_empty', [], '', ['style' => 'width: 100%', 'class' => 'form-control',
                  'placeholder' => __('lang_v1.none'), 'id' => 'eglass_empty', 'disabled']) !!}
              </div>
            </div>

            {{-- job_type --}}
            <div class="form-group" style="margin-top: 10px;">
              {!! Form::label('job_type', __('lab_order.job_type') . ': ') !!}
              {!! Form::textarea('ejob_type', '', ['class' => 'form-control', 'placeholder' => __('lab_order.job_type'),
              'rows' => '3', 'id' => 'ejob_type']) !!}
            </div>

            {{-- check_ext_lab --}}
            <div class="form-group">
              <div class="checkbox">
                <label>
                  {!! Form::checkbox('echeck_ext_lab', 1, null, ['class' => 'input-icheck', 'id' => 'echeck_ext_lab',
                  'onclick' => 'extLabCheck()']) !!}
                  <strong>@lang('lab_order.send_to_external_lab')</strong>
                </label>
              </div>
            </div>

            {{-- lab_extern_id --}}
            <div class="form-group" id="elab_extern_box" style="display: none">
              {!! Form::label('external_lab_id', __('lab_order.external_laboratory') . ':*') !!}
              {!! Form::select('eexternal_lab_id', $external_labs, null, ['class' => 'form-control select2',
              'placeholder' => __('lang_v1.none'), 'required', 'style' => 'width: 100%', 'id' => 'eexternal_lab_id']) !!}
            </div>

            {{-- Second time fields --}}
            <div id="div_second_time" style="display: none">
              {{-- employee_id --}}
              <div class="form-group">
                {!! Form::label('employee_id', __('customer.employee') . ':') !!}
                {!! Form::select('eemployee_id', $employees, '', [
                  'class' => 'form-control select2',
                  'id' => 'eemployee_id',
                  'placeholder' => __('lang_v1.none'),
                  'style' => 'width: 100%;'
                ]) !!}
              </div>

              {{-- reason --}}
              <div class="form-group">
                {!! Form::label('reason', __('lab_order.reason') . ':') !!}
                {!! Form::textarea('ereason', '',
                  ['class' => 'form-control', 'placeholder' => __('lab_order.reason'), 'rows' => '3', 'id' => 'ereason']) !!}
              </div>
            </div>
          </div>

          <div class="col-sm-4">
            {{-- ar --}}
            <label>@lang('lab_order.ar')</label>
            <div class="radio" style="margin-top: 0;">
              <label>
                {!! Form::radio('ear', 'green', null, ['class' => 'ar_rb']) !!}
                @lang('lab_order.ar_green')
              </label>
            </div>
            <div class="radio">
              <label>
                {!! Form::radio('ear', 'blue', null, ['class' => 'ar_rb']) !!}
                @lang('lab_order.ar_blue')
              </label>
            </div>
            <div class="radio">
              <label>
                {!! Form::radio('ear', 'premium', null, ['class' => 'ar_rb']) !!}
                @lang('lab_order.ar_premium')
              </label>
            </div>

            {{-- delivery --}}
            <div class="form-group">
              <label>@lang('lab_order.date_hour_delivery')</label>
              <div class="form-group">
                <div class="input-group date" id="edatetimepicker1" data-target-input="nearest">
                  <input type="text" class="form-control datetimepicker-input" data-toggle="datetimepicker"
                    data-target="#datetimepicker1" name="edelivery" id="edelivery"
                    placeholder="@lang('lab_order.date_hour_delivery')" required>
                  <span class="input-group-addon" data-target="#edatetimepicker1" data-toggle="datetimepicker">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div>
              </div>
            </div>
  
            {{-- document --}}
            {{-- <div class="form-group">
              {!! Form::label('edocument', __('purchase.attach_document') . ':') !!}
              {!! Form::file('edocument', ['id' => 'eupload_document']); !!}
              <p class="help-block">
                @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                <br>
                @lang('lang_v1.previous_file_will_be_replaced')
              </p>
            </div> --}}

            {{-- final_total --}}
            <div class="form-group">
              {!! Form::label('final_total', __('lang_v1.total')) !!}
              <input type="text" id="final_total" class="form-control" value="" readonly>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="btn-edit-order">
          @lang('messages.save')
        </button>
        <button type="button" class="btn btn-default" id="btn-save-print-order" style="display: none;">
          <i class="fa fa-print"></i> @lang('status_lab_order.save_and_print')
        </button>
        <button type="button" data-dismiss="modal" aria-label="Close" id="btn-close-modal-edit-order" class="btn btn-default">
          @lang('messages.close')
        </button>
      </div>
    </div>
  </div>
</div>
{{-- @include('optics.lab_order.partials.modal_second_time') --}}
@include('optics.lab_order.partials.modal_return_stock')
{!! Form::close() !!}

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
        {{-- transaction_id --}}
        {!! Form::hidden('transaction_id', isset($transaction_id) ? $transaction_id : null, ['id' => 'transaction_id']) !!}

        {{-- patient_id --}}
        <div class="col-sm-8">
          <div class="form-group">
            {!! Form::label('patient_id', __('graduation_card.patient') . ':') !!}
            {!! Form::select('patient_id', $patients, $patient_id,
              ['class' => 'form-control select2', 'id' => 'patient_id', 'required']) !!}
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
            {!! Form::select('select_customer_id', $customers, isset($transaction->customer_id) ? $transaction->customer_id : '',
              ['class' => 'form-control select2', 'disabled', 'id' => 'lab_customer_id']) !!}
            {!! Form::hidden('lab_customer_id', isset($transaction->customer_id) ? $transaction->customer_id : null) !!}
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
            {{-- @if ($own_hoop_aux == 1)
            {!! Form::text('hoop_name', isset($has_hoop->name) ? $has_hoop->name : '', ['class' => 'form-control', 'id'
            => 'hoop_name', 'placeholder' => __('graduation_card.ring')]) !!}
            {!! Form::hidden('hoop', isset($has_hoop->id) ? $has_hoop->id : null, ['id' => 'hoop']) !!}
            @elseif ($own_hoop_aux == 2)
            {!! Form::select('hoop', $has_hoop, '', ['class' => 'form-control select2', 'required', 'placeholder' => __('lang_v1.none'), 'id' => 'hoop']) !!}
            @else
            {!! Form::select('hoop', $has_hoop, '', ['class' => 'form-control select2', 'required', 'readonly', 'id' => 'hoop']) !!}
            @endif --}}

            @if ($own_hoop_aux == 1)
            {!! Form::text('hoop_name', '',
              ['class' => 'form-control', 'id' => 'hoop_name', 'placeholder' => __('graduation_card.ring')]) !!}
            <div id="div_hoop" style="display: none;">
              {!! Form::select('hoop', $has_hoop, '',
                ['class' => 'form-control select2', 'required', 'placeholder' => __('lang_v1.none'), 'id' => 'hoop']) !!}
            </div>
            @else
            <div id="div_hoop">
              {!! Form::select('hoop', $has_hoop, '',
                ['class' => 'form-control select2', 'required', 'placeholder' => __('lang_v1.none'), 'id' => 'hoop']) !!}
            </div>
            {!! Form::text('hoop_name', '',
              ['class' => 'form-control', 'id' => 'hoop_name', 'placeholder' => __('graduation_card.ring'), 'style' => 'display: none;']) !!}
            @endif
          </div>

          {{-- is_own_hoop --}}
          <div class="checkbox" style="margin-top: 0; margin-bottom:">
            <label>
              {!! Form::checkbox('is_own_hoop', 1, $own_hoop_aux == 1 ? true : false,
                ['class' => 'input-icheck', 'id' => 'is_own_hoop', 'onclick' => 'checkOwnHoop()']) !!}
              <strong>@lang('lab_order.own_hoop')</strong>
            </label>
          </div>
        </div>

        {{-- size --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('size', __('graduation_card.size') . ': ') !!}
            {!! Form::text('size', isset($hoop_values->size) ? $hoop_values->size : '', ['class' =>
            'form-control size-mask', 'placeholder' => __('graduation_card.size'), 'id' => 'size']) !!}
          </div>
        </div>

        {{-- color --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('color', __('graduation_card.color') . ': ') !!}
            {!! Form::text('color', isset($hoop_values->color) ? $hoop_values->color : '', ['class' => 'form-control',
            'placeholder' => __('graduation_card.color'), 'id' => 'color']) !!}
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
          {{-- <div class="form-group" style="margin-top: 10px;">
            {!! Form::label('glass', __('lab_order.glass') . ': ') !!}

            <div class="form-group" style="margin-bottom: 3px;">
              <div class="input-group">
                <span class="input-group-addon">@lang('graduation_card.od')</span> --}}
                {{-- glass_od (variation_id) --}}
                {{-- @if ($has_glass_od->count() == 0 || $has_glass_od->count() == 1)
                {!! Form::select('glass_od', $has_glass_od, '', ['class' => 'form-control select2', 'required', 'readonly']) !!}
                @elseif ($has_glass_od->count() > 1)
                {!! Form::select('glass_od', $has_glass_od, '', ['class' => 'form-control select2', 'required', 'placeholder' => __('lang_v1.none')]) !!}
                @endif
              </div>
            </div> --}}

            {{-- <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">@lang('graduation_card.os')</span> --}}
                {{-- glass_os (variation_id) --}}
                {{-- @if ($has_glass_os->count() == 0 || $has_glass_os->count() == 1)
                {!! Form::select('glass_os', $has_glass_os, '', ['class' => 'form-control select2', 'required', 'readonly']) !!}
                @elseif ($has_glass_os->count() > 1)
                {!! Form::select('glass_os', $has_glass_os, '', ['class' => 'form-control select2', 'required', 'placeholder' => __('lang_v1.none')]) !!}
                @endif
              </div>
            </div>
          </div> --}}

          {{-- glasses --}}
          <div class="form-group" style="margin-top: 10px;">
            {!! Form::label('glass', __('lab_order.glass') . ': ') !!}

            {{-- glass_vs (variation_id) --}}
            @if (!empty($has_glass))
            @if ($has_glass->count() > 0)
            <div class="form-group" style="margin-bottom: 3px;">
              <div class="input-group">
                <span class="input-group-addon">@lang('graduation_card.vs') o B.I.</span>
                {!! Form::select('glass', $has_glass, '',
                  ['class' => 'form-control select2', 'required', 'placeholder' => __('lang_v1.none'), 'id' => 'glass']) !!}
              </div>
            </div>
            @endif
            @endif

            {{-- glass_od (variation_id) --}}
            @if (!empty($has_glass_od))
            @if ($has_glass_od->count() > 0)
            <div class="form-group" style="margin-bottom: 3px;">
              <div class="input-group">
                <span class="input-group-addon">@lang('graduation_card.od')</span>
                {!! Form::select('glass_od', $has_glass_od, '',
                  ['class' => 'form-control select2', 'required', 'placeholder' => __('lang_v1.none'), 'id' => 'glass_od']) !!}
              </div>
            </div>
            @endif
            @endif

            {{-- glass_os (variation_id) --}}
            @if (!empty($has_glass_os))
            @if ($has_glass_os->count() > 0)
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">@lang('graduation_card.os')</span>
                {!! Form::select('glass_os', $has_glass_os, '',
                  ['class' => 'form-control select2', 'required', 'placeholder' => __('lang_v1.none'), 'id' => 'glass_os']) !!}
              </div>
            </div>
            @endif
            @endif

            {{-- glass empty (variation_id) --}}
            @if (!empty($has_glass) && !empty($has_glass_od) && !empty($has_glass_os))
            @if ($has_glass->count() == 0 && $has_glass_od->count() == 0 && $has_glass_os->count() == 0)
            <div class="form-group">
              {!! Form::select('glass_empty', [], '',
                ['class' => 'form-control select2', 'readonly', 'placeholder' => __('lang_v1.none')]) !!}
            </div>
            @endif
            @endif
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
          @if ($ar_aux != 0)
          <label>@lang('lab_order.ar')</label>
          @endif
          @if ($ar_aux == 1)
          {!! Form::hidden('ar', isset($has_ar->ar) ? $has_ar->ar : null) !!}
          <div class="radio" style="margin-top: 0;">
            <label>
              {!! Form::radio('arrb', 'green', isset($has_ar->ar) ? ($has_ar->ar == 'green' ? true : false) : false, ['disabled']) !!}
              @lang('lab_order.ar_green')
            </label>
          </div>
          <div class="radio">
            <label>
              {!! Form::radio('arrb', 'blue', isset($has_ar->ar) ? ($has_ar->ar == 'blue' ? true : false) : false, ['disabled']) !!}
              @lang('lab_order.ar_blue')
            </label>
          </div>
          <div class="radio">
            <label>
              {!! Form::radio('arrb', 'premium', isset($has_ar->ar) ? ($has_ar->ar == 'premium' ? true : false) : false, ['disabled']) !!}
              @lang('lab_order.ar_premium')
            </label>
          </div>
          @elseif ($ar_aux == 2)
          <div class="form-group">
            {!! Form::select('ar', $has_ar, '',
              ['class' => 'form-control select2', 'placeholder' => __('lang_v1.none'), 'id' => 'ar']) !!}
          </div>
          @endif

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
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-order">
      <input type="button" class="btn bg-maroon" value="@lang('lang_v1.save_n_add_another')" id="btn-save-n-add-another">
      <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default"
        id="btn-close-modal-add-order">@lang('messages.close')</button>
    </div>
    {!! Form::close() !!}
  </div>
</div>

<div class="modal-dialog modal-lg" role="dialog" id="modal-show">
  <div class="modal-content" style="border-radius: 10px;">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang('lab_order.show_lab_order')</h4>
    </div>

    <div class="modal-body">
      {{-- header --}}
      <div class="row">
        {{-- patient_id --}}
        <div class="col-sm-8">
          <div class="form-group">
            {!! Form::label('patient_id', __('graduation_card.patient') . ': ') !!}
            {{ $lab_order->patient_value }}
          </div>
        </div>

        {{-- no_order --}}
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('no_order', __('lab_order.no_order') . ':') !!}
            {{ $lab_order->correlative }}
          </div>
        </div>

        {{-- customer_id --}}
        <div class="col-sm-8">
          <div class="form-group">
            {!! Form::label('customer_id', __('contact.customer') . ':') !!}
            @if (!empty($lab_order->customer_name))    
            {{ $lab_order->customer_name }}
            @else
            {{ $lab_order->customer_value }}
            @endif
          </div>
        </div>

        {{-- is_reparation --}}
        <div class="col-sm-4">
          <div class="form-group">
            <div class="checkbox" style="margin-top: 0; margin-bottom: 0">
              <label>
                {!! Form::checkbox('eis_reparation', 1, $lab_order->is_reparation == 1 ? true : false,
                  ['class' => 'input-icheck', 'id' => 'eis_repair', 'disabled']) !!}
                <strong>@lang('lab_order.repair')</strong>
              </label>
            </div>
          </div>
        </div>

        {{-- optometrist --}}
        <div class="col-sm-8 graduation_card_fields">
          <div class="form-group">
            {!! Form::label('optometrist', __('graduation_card.optometrist') . ':') !!}
            {{ $lab_order->optometrist }}
          </div>
        </div>

        {{-- is_prescription --}}
        <div class="col-sm-4 graduation_card_fields">
          <div class="form-group">
            <div class="checkbox" style="margin-top: 0; margin-bottom: 0">
              <label>
                {!! Form::checkbox('eis_prescription', 1, $lab_order->is_prescription == 1 ? true : false,
                  ['class' => 'input-icheck', 'id' => 'eis_prescription', 'disabled']); !!}
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
                      {!! Form::checkbox('ebalance_od', 1, $lab_order->balance_od == 1 ? true : false,
                        ['class' => 'input-icheck', 'id' => 'ebalance_od', 'disabled']) !!}
                      <small>@lang('graduation_card.balance')</small>
                    </label>
                  </div>
                </th>

                {{-- sphere_od --}}
                <td>
                  {{ $lab_order->sphere_od }}
                </td>
                {{-- cylindir_od --}}
                <td>
                  {{ $lab_order->cylindir_od }}
                </td>
                {{-- axis_od --}}
                <td>
                  {{ $lab_order->axis_od }}
                </td>
                {{-- base_od --}}
                <td>
                  {{ $lab_order->base_od }}
                </td>
                {{-- addition_od --}}
                <td>
                  {{ $lab_order->addition_od }}
                </td>
              </tr>

              <tr>
                <th>
                  @lang('graduation_card.os')
                  {{-- balance_os --}}
                  <div class="checkbox" style="margin-top: 0; margin-bottom:">
                    <label>
                      {!! Form::checkbox('ebalance_os', 1, $lab_order->balance_os == 1 ? true : false,
                        ['class' => 'input-icheck', 'id' => 'ebalance_os', 'disabled']) !!}
                      <small>@lang('graduation_card.balance')</small>
                    </label>
                  </div>
                </th>

                {{-- sphere_os --}}
                <td>
                  {{ $lab_order->sphere_os }}
                </td>
                {{-- cylindir_os --}}
                <td>
                  {{ $lab_order->cylindir_os }}
                </td>
                {{-- axis_os --}}
                <td>
                  {{ $lab_order->axis_os }}
                </td>
                {{-- base_os --}}
                <td>
                  {{ $lab_order->base_os }}
                </td>
                {{-- addition_os --}}
                <td>
                  {{ $lab_order->addition_os }}
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
            @if (!empty($lab_order->dnsp_od) && !empty($lab_order->dnsp_os))    
            <br>
            @lang('graduation_card.od') {{ $lab_order->dnsp_od }}
            <br>
            @lang('graduation_card.os') {{ $lab_order->dnsp_os }}
            @endif
          </div>
        </div>

        {{-- dp --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('di', __('graduation_card.di') . ': ') !!}
            {{ $lab_order->di }}
          </div>
        </div>

        {{-- ao --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('ao', __('graduation_card.ao') . ': ') !!}
            {{ $lab_order->ao }}
          </div>
        </div>

        {{-- ap --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('ap', __('graduation_card.ap') . ': ') !!}
            {{ $lab_order->ap }}
          </div>
        </div>
      </div>

      {{-- hoop fields --}}
      <div class="row">
        {{-- hoop (variation_id) --}}
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('hoop', __('graduation_card.ring') . ': ') !!}
            @if (!empty($lab_order->hoop_name))    
            {{ $lab_order->hoop_name }}
            @else
            {{ $lab_order->hoop_value }}
            @endif
          </div>

          {{-- is_own_hoop --}}
          @if ($lab_order->is_own_hoop == 1)    
          <div class="checkbox" style="margin-top: 0; margin-bottom:">
            <label>
              {!! Form::checkbox('eis_own_hoop', 1, $lab_order->is_own_hoop == 1 ? true : false,
                ['class' => 'input-icheck', 'id' => 'eis_own_hoop', 'disabled']) !!}
              <strong>@lang('lab_order.own_hoop')</strong>
            </label>
          </div>
          @endif
        </div>

        {{-- size --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('size', __('graduation_card.size') . ': ') !!}
            {{ $lab_order->size }}
          </div>
        </div>

        {{-- color --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('color', __('graduation_card.color') . ': ') !!}
            {{ $lab_order->color }}
          </div>
        </div>
      </div>

      {{-- final fields --}}
      <div class="row" style="margin-top: 10px;">
        <div class="col-sm-8">

          {{-- hoop_type --}}
          <label>@lang('lab_order.hoop_type'):</label>
          @if ($lab_order->hoop_type == 'full')
          @lang('lab_order.full')
          @endif
          @if ($lab_order->hoop_type == 'semi_air')
          @lang('lab_order.semi_air')
          @endif
          @if ($lab_order->hoop_type == 'air')
          @lang('lab_order.air')
          @endif
          <br>

          <div class="form-group" style="margin-top: 10px;">
            {!! Form::label('glass', __('lab_order.glass') . ': ') !!}
            @if (!empty($lab_order->glass_value))
            <br>(@lang('graduation_card.vs')) {{ $lab_order->glass_value }}
            @endif
            @if (!empty($lab_order->glass_od_value))
            <br>(@lang('graduation_card.od')) {{ $lab_order->glass_od_value }}
            @endif
            @if (!empty($lab_order->glass_os_value))
            <br>(@lang('graduation_card.os')) {{ $lab_order->glass_os_value }}
            @endif
            @if (empty($lab_order->glass_value) && empty($lab_order->glass_od_value) && empty($lab_order->glass_os_value))
            <br>(@lang('lang_v1.none'))
            @endif
          </div>

          {{-- job_type --}}
          <div class="form-group" style="margin-top: 10px;">
            {!! Form::label('job_type', __('lab_order.job_type') . ': ') !!}
            {{ $lab_order->job_type }}
          </div>
        </div>

        <div class="col-sm-4">
          {{-- ar --}}
          <div class="form-group">
            {!! Form::label('ar', __('lab_order.ar') . ': ') !!}
            @if ($lab_order->ar == 'green')
            @lang('lab_order.ar_green')
            @endif
            @if ($lab_order->ar == 'blue')
            @lang('lab_order.ar_blue')
            @endif
            @if ($lab_order->ar == 'premium')
            @lang('lab_order.ar_premium')
            @endif
          </div>

          {{-- status --}}
          <label>@lang('accounting.status'):</label>
          <i class="fa fa-circle" style="color: {{ $lab_order->color_value }}"></i> {{ $lab_order->status_value }}
          
          {{-- delivery --}}
          <div class="form-group">
            <label>@lang('lab_order.date_hour_delivery'):</label>
            <div class="form-group">
              {{ $lab_order->delivery }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default">
        @lang('messages.close')
      </button>
    </div>
  </div>
</div>

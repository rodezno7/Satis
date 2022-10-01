<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content" style="border-radius: 10px;">
    {!! Form::open(['url' => action('GraduationCardController@update', [$graduation_card->id]),
      'method' => 'PUT', 'id' => 'graduation_cards_edit_form', 'autocomplete' => 'off']) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang('graduation_card.edit_graduation_card')</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        {{-- patient_id --}}
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('patient_id', __('graduation_card.patient') . ': *') !!}
            {!! Form::select('patient_id', $patients, $graduation_card->patient_id,
              ['class' => 'form-control select2', 'required']) !!}
          </div>
        </div>

        {{-- is_prescription --}}
        <div class="col-sm-2">
          <div class="form-group">
            <label>
              {!! Form::checkbox('is_prescription', 1, $graduation_card->is_prescription,
                ['class' => 'input-icheck', 'id' => 'is_prescription', 'onclick' => 'optometristBlock()']); !!}
              <strong>&nbsp;@lang('graduation_card.prescription')</strong>
            </label>
          </div>
        </div>

        {{-- invoice --}}
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('invoice', __('graduation_card.invoice') . ': ') !!}
            {!! Form::text('invoice', $graduation_card->invoice,
              ['class' => 'form-control', 'placeholder' => __('graduation_card.invoice')]) !!}
          </div>
        </div>

        {{-- attended_by --}}
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('attended_by', __('graduation_card.attended_by') . ': ') !!}
            {!! Form::select('attended_by', $employees, $graduation_card->attended_by,
              ['class' => 'form-control']) !!}
          </div>
        </div>

        {{-- optometrist --}}
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('optometrist', __('graduation_card.optometrist') . ': ') !!}
            {!! Form::select('optometrist', $employees, $graduation_card->optometrist,
              ['class' => 'form-control']) !!}
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <table class="table table-bordered text-center">
            <tbody>
              <tr>
                <th></th>
                <th>@lang('graduation_card.sphere_abbreviation')</th>
                <th>@lang('graduation_card.cylindir_abbreviation')</th>
                <th>@lang('graduation_card.axis_mayus')</th>
                <th>@lang('graduation_card.base_mayus')</th>
                <th>@lang('graduation_card.addition_mayus')</th>
              </tr>

              <tr>
                <th>
                  @lang('graduation_card.od')
                  {{-- balance_od --}}      
                  <div class="checkbox" style="margin-top: 0; margin-bottom: 0">
                    <label>
                      {!! Form::checkbox('balance_od', 1, null,
                        ['class' => 'input-icheck', 'id' => 'balance_od', 'onclick' => 'balanceOD()']) !!}
                      <small>@lang('graduation_card.balance')</small>
                    </label>
                  </div>
                </th>

                {{-- sphere_od --}}
                <td>
                  {!! Form::text('sphere_od', $graduation_card->sphere_od,
                    ['class' => 'form-control input_graduation']) !!}
                </td>
                {{-- cylindir_od --}}
                <td>
                  {!! Form::text('cylindir_od', $graduation_card->cylindir_od,
                    ['class' => 'form-control input_graduation']) !!}
                </td>
                {{-- axis_od --}}
                <td>
                  {!! Form::text('axis_od', $graduation_card->axis_od,
                    ['class' => 'form-control input_only_number']) !!}
                </td>
                {{-- base_od --}}
                <td>
                  {!! Form::text('base_od', $graduation_card->base_od,
                    ['class' => 'form-control input_graduation']) !!}
                </td>
                {{-- addition_od --}}
                <td>
                  {!! Form::text('addition_od', $graduation_card->addition_od,
                    ['class' => 'form-control input_graduation']) !!}
                </td>
              </tr>

              <tr>
                <th>
                  @lang('graduation_card.os')
                  {{-- balance_os --}}      
                  <div class="checkbox" style="margin-top: 0; margin-bottom:">
                    <label>
                      {!! Form::checkbox('balance_os', 1, null,
                        ['class' => 'input-icheck', 'id' => 'balance_os', 'onclick' => 'balanceOS()']) !!}
                      <small>@lang('graduation_card.balance')</small>
                    </label>
                  </div>
                </th>

                {{-- sphere_os --}}
                <td>
                  {!! Form::text('sphere_os', $graduation_card->sphere_os,
                    ['class' => 'form-control input_graduation']) !!}
                </td>
                {{-- cylindir_os --}}
                <td>
                  {!! Form::text('cylindir_os', $graduation_card->cylindir_os,
                    ['class' => 'form-control input_graduation']) !!}
                </td>
                {{-- axis_os --}}
                <td>
                  {!! Form::text('axis_os', $graduation_card->axis_os,
                    ['class' => 'form-control input_only_number']) !!}
                </td>
                {{-- base_os --}}
                <td>
                  {!! Form::text('base_os', $graduation_card->base_os,
                    ['class' => 'form-control input_graduation']) !!}
                </td>
                {{-- addition_os --}}
                <td>
                  {!! Form::text('addition_os', $graduation_card->addition_os,
                    ['class' => 'form-control input_graduation']) !!}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="row">
        {{-- dnsp --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('dnsp', __('graduation_card.dnsp') . ': ') !!}
            {!! Form::text('dnsp_od', $graduation_card->dnsp_od,
              ['class' => 'form-control di-mask', 'placeholder' => __('graduation_card.od'), 'style' => 'margin-bottom: 3px;']) !!}
            {!! Form::text('dnsp_os', $graduation_card->dnsp_os,
              ['class' => 'form-control di-mask', 'placeholder' => __('graduation_card.os')]) !!}
          </div>
        </div>

        {{-- dp --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('di', __('graduation_card.di') . ': ') !!}
            {!! Form::text('di', $graduation_card->di,
              ['class' => 'form-control di-mask', 'placeholder' => __('graduation_card.di')]) !!}
          </div>
        </div>

        {{-- ao --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('ao', __('graduation_card.ao') . ': ') !!}
            {!! Form::text('ao', $graduation_card->ao,
              ['class' => 'form-control input_graduation', 'placeholder' => __('graduation_card.ao')]) !!}
          </div>
        </div>

        {{-- ap --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('ap', __('graduation_card.ap') . ': ') !!}
            {!! Form::text('ap', $graduation_card->ap,
              ['class' => 'form-control input_graduation', 'placeholder' => __('graduation_card.ap')]) !!}
          </div>
        </div>
      </div>

      <div class="row">
        {{-- observations --}}
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('observations', __('graduation_card.observations') . ': ') !!}
            {!! Form::textarea('observations', $graduation_card->observations,
              ['class' => 'form-control', 'placeholder' => __('graduation_card.observations'), 'rows' => '3']) !!}
          </div>
        </div>
      </div>

      {{-- diagnostics --}}
      <div class="row">
        <div class="col-sm-12">
          <p>
            <strong>@lang('graduation_card.suffers')</strong>
          </p>
        </div>

        @foreach ($diagnostics as $i => $diagnostic)
        <div class="col-sm-4">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('diagnostics[]', $diagnostic->id, $status[$i],
                  ['class' => 'input-icheck', 'id' => 'diagnostics[]']); !!} {{ $diagnostic->name }}
              </label>
            </div>
          </div>
        </div>
        @endforeach
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

<script>
  $(document).ready(function() {
    if ($('#is_prescription').is(':checked')) {
      $('#optometrist').prop('disabled', true);
      $('#optometrist').val('0');
    }
  });
</script>

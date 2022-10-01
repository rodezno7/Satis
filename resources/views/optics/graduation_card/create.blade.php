<div class="modal-dialog modal-lg" role="dialog" id="modal-create">
  <div class="modal-content" style="border-radius: 10px;">
    {!! Form::open(['url' => action('GraduationCardController@store'), 'method' => 'post', 'id' => 'graduation_card_add_form', 'autocomplete' => 'off']) !!}
    
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang('graduation_card.add_graduation_card')</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        {{-- patient_id --}}
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('patient_id', __('graduation_card.patient') . ':*') !!}
            {!! Form::select('patient_id', $patients, '',
              ['class' => 'form-control select2', 'required']) !!}
          </div>
        </div>

        {{-- is_prescription --}}
        <div class="col-sm-2">
          <div class="form-group">
            <label>
              {!! Form::checkbox('is_prescription', 1, null,
                ['class' => 'input-icheck', 'id' => 'is_prescription', 'onclick' => 'optometristBlock()']); !!}
              <strong>&nbsp;@lang('graduation_card.prescription')</strong>
            </label>
          </div>
        </div>

        {{-- invoice --}}
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('invoice', __('graduation_card.invoice') . ': ') !!}
            {!! Form::text('invoice', null,
              ['class' => 'form-control', 'placeholder' => __('graduation_card.invoice')]) !!}
          </div>
        </div>

        {{-- attended_by --}}
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('attended_by', __('graduation_card.attended_by') . ': ') !!}
            {!! Form::select('attended_by', $employees, '',
              ['class' => 'form-control select2']) !!}
          </div>
        </div>

        {{-- optometrist --}}
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('optometrist', __('graduation_card.optometrist') . ': ') !!}
            {!! Form::select('optometrist', $employees, '',
              ['class' => 'form-control select2']) !!}
          </div>
        </div>
      </div>

      <div class="row">
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
                      {!! Form::checkbox('balance_od', 1, null,
                        ['class' => 'input-icheck', 'id' => 'balance_od', 'onclick' => 'balanceOD()']) !!}
                      <small>@lang('graduation_card.balance')</small>
                    </label>
                  </div>
                </th>
                
                {{-- sphere_od --}}
                <td>
                  {!! Form::text('sphere_od', null,
                    ['class' => 'form-control input_graduation']) !!}
                </td>
                {{-- cylindir_od --}}
                <td>
                  {!! Form::text('cylindir_od', null,
                    ['class' => 'form-control input_graduation']) !!}
                </td>
                {{-- axis_od --}}
                <td>
                  {!! Form::text('axis_od', null,
                    ['class' => 'form-control input_only_number']) !!}
                </td>
                {{-- base_od --}}
                <td>
                  {!! Form::text('base_od', null,
                    ['class' => 'form-control input_graduation']) !!}
                </td>
                {{-- addition_od --}}
                <td>
                  {!! Form::text('addition_od', null,
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
                  {!! Form::text('sphere_os', null,
                    ['class' => 'form-control input_graduation']) !!}
                </td>
                {{-- cylindir_os --}}
                <td>
                  {!! Form::text('cylindir_os', null,
                    ['class' => 'form-control input_graduation']) !!}
                </td>
                {{-- axis_os --}}
                <td>
                  {!! Form::text('axis_os', null,
                    ['class' => 'form-control input_only_number']) !!}
                </td>
                {{-- base_os --}}
                <td>
                  {!! Form::text('base_os', null,
                    ['class' => 'form-control input_graduation']) !!}
                </td>
                {{-- addition_os --}}
                <td>
                  {!! Form::text('addition_os', null,
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
            {!! Form::text('dnsp_od', null,
              ['class' => 'form-control di-mask', 'placeholder' => __('graduation_card.od'), 'style' => 'margin-bottom: 3px;']) !!}
            {!! Form::text('dnsp_os', null,
              ['class' => 'form-control di-mask', 'placeholder' => __('graduation_card.os')]) !!}
          </div>
        </div>

        {{-- dp --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('di', __('graduation_card.di') . ': ') !!}
            {!! Form::text('di', null,
              ['class' => 'form-control di-mask', 'placeholder' => __('graduation_card.di')]) !!}   
          </div>
        </div>

        {{-- ao --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('ao', __('graduation_card.ao') . ': ') !!}
            {!! Form::text('ao', null,
              ['class' => 'form-control input_graduation', 'placeholder' => __('graduation_card.ao')]) !!}
          </div>
        </div>

        {{-- ap --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('ap', __('graduation_card.ap') . ': ') !!}
            {!! Form::text('ap', null,
              ['class' => 'form-control input_graduation', 'placeholder' => __('graduation_card.ap')]) !!}
          </div>
        </div>
      </div>

      <div class="row">
        {{-- observations --}}
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('observations', __('graduation_card.observations') . ': ') !!}
            {!! Form::textarea('observations', null,
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

        @foreach ($diagnostics as $diagnostic)
        <div class="col-sm-4">
          <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('diagnostics[]', $diagnostic->id, null,
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

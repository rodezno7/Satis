<div class="modal-dialog" role="dialog" id="modal-create">
  <div class="modal-content" style="border-radius: 10px;">
    {!! Form::open(['url' => action('Optics\StatusLabOrderController@store'),
      'method' => 'post', 'id' => 'status_lab_order_add_form']) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang('status_lab_order.add_status_lab_order')</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        {{-- code --}}
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('code', __('cashier.code') . ':*') !!}
            {!! Form::text('code', $code,
              ['class' => 'form-control', 'required', /* 'readonly', */ 'placeholder' => __('cashier.code')]) !!}
          </div>
        </div>

        {{-- name --}}
        <div class="col-sm-8">
          <div class="form-group">
            {!! Form::label('name', __('cashier.name') . ':*') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __('cashier.name')])
            !!}
          </div>
        </div>

        {{-- color --}}
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('color', __('graduation_card.color') . ':') !!}
            <input type="color" id="color" name="color" class="form-control">
          </div>
        </div>

        {{-- status --}}
        <div class="col-sm-8">
          <div class="form-group">
            {!! Form::label('status', __('cashier.status') . ':') !!}
            {!! Form::select('status',
              ['active' => __('cashier.active'), 'inactive' => __('cashier.inactive')], '',
              ['class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
          </div>
        </div>

        {{-- steps --}}
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('steps', __('status_lab_order.states_it_enables') . ':') !!}
            {!! Form::select('steps[]', $status_list, '',
              ['class' => 'form-control select2', 'style' => 'width: 100%;', 'multiple' => 'multiple']) !!}
          </div>
        </div>

        {{-- descripction --}}
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('descripction', __('lang_v1.description') . ':') !!}
            {!! Form::textarea('descripction', null,
              ['class' => 'form-control', 'placeholder' => __('lang_v1.description'), 'rows' => '3']) !!}
          </div>
        </div>

        {{-- is_default --}}
        <div class="col-sm-6">
          <div class="form-group">
            <div class="checkbox">
              <label>
                  {!! Form::checkbox('is_default', 1, false, ['id' => 'is_default']) !!}
                  <strong>@lang('status_lab_order.default_status')</strong>
                  <br>
                  <small>@lang('status_lab_order.default_status_indication').</small>
              </label>
            </div>
          </div>
        </div>

        {{-- print_order --}}
        <div class="col-sm-6">
          <div class="form-group">
            <div class="checkbox">
              <label>
                  {!! Form::checkbox('print_order', 1, false, ['id' => 'print_order']) !!}
                  <strong>@lang('status_lab_order.print_order')</strong>
                  <br>
                  <small>@lang('status_lab_order.print_order_indication').</small>
              </label>
            </div>
          </div>
        </div>

        {{-- transfer_sheet --}}
        <div class="col-sm-6">
          <div class="form-group">
            <div class="checkbox">
              <label>
                  {!! Form::checkbox('transfer_sheet', 1, false, ['id' => 'transfer_sheet']) !!}
                  <strong>@lang('status_lab_order.include_in_transfer_sheet')</strong>
                  <br>
                  <small>@lang('status_lab_order.include_in_transfer_sheet_indication').</small>
              </label>
            </div>
          </div>
        </div>

        {{-- second_time --}}
        <div class="col-sm-6">
          <div class="form-group">
            <div class="checkbox">
              <label>
                  {!! Form::checkbox('second_time', 1, false, ['id' => 'second_time']) !!}
                  <strong>@lang('lab_order.second_time')</strong>
                  <br>
                  <small>@lang('status_lab_order.second_time_indication').</small>
              </label>
            </div>
          </div>
        </div>

        {{-- material_download --}}
        <div class="col-sm-6">
          <div class="form-group">
            <div class="checkbox">
              <label>
                  {!! Form::checkbox('material_download', 1, false, ['id' => 'material_download']) !!}
                  <strong>@lang('status_lab_order.material_download')</strong>
                  <br>
                  <small>@lang('status_lab_order.material_download_indication').</small>
              </label>
            </div>
          </div>
        </div>

        {{-- save_and_print --}}
        <div class="col-sm-6">
          <div class="form-group">
            <div class="checkbox">
              <label>
                  {!! Form::checkbox('save_and_print', 1, false, ['id' => 'save_and_print']) !!}
                  <strong>@lang('status_lab_order.save_and_print')</strong>
                  <br>
                  <small>@lang('status_lab_order.save_and_print_indication').</small>
              </label>
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

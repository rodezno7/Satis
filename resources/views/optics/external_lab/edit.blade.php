<div class="modal-dialog" role="document">
  <div class="modal-content" style="border-radius: 10px;">
    {!! Form::open(['url' => action('Optics\ExternalLabController@update', [$external_lab->id]),
      'method' => 'PUT', 'id' => 'external_labs_edit_form']) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang('external_lab.edit_external_lab')</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        {{-- name --}}
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('name', __('cashier.name') . ':*') !!}
            {!! Form::text('name', $external_lab->name,
              ['class' => 'form-control', 'required', 'placeholder' => __('cashier.name')]) !!}
          </div>
        </div>

        {{-- address --}}
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('address', __('business.address') . ':') !!}
            {!! Form::text('address', $external_lab->address,
              ['class' => 'form-control', 'placeholder' => __('business.address')]) !!}
          </div>
        </div>

        {{-- description --}}
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('description', __('lang_v1.description') . ':') !!}
            {!! Form::textarea('description', $external_lab->description,
              ['class' => 'form-control', 'placeholder' => __('lang_v1.description'), 'rows' => '3']) !!}
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

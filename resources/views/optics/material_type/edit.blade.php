<div class="modal-dialog" role="document">
  <div class="modal-content" style="border-radius: 10px;">
      {!! Form::open(['url' => action('Optics\MaterialTypeController@update', [$material_type->id]), 'method' => 'PUT', 'id' => 'material_types_edit_form']) !!}
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">@lang('material_type.edit_material_type')</h4>
      </div>
      <div  class="modal-body">
          <!-- Name -->
          <div class="form-group">
              {!! Form::label('name', __('cashier.name') . ': *') !!}
              <div class="wrap-inputform">
                  {!! Form::text('name', $material_type->name, ['class' => 'form-control', 'required', 'placeholder' => __('cashier.name')]) !!}
              </div>
          </div>
          <!-- Description -->
          <div class="form-group">
            {!! Form::label('name', __('accounting.description') . ':') !!}
            <div class="wrap-inputform">
                {!! Form::text('description', $material_type->description, ['class' => 'form-control', 'placeholder' => __('accounting.description')]) !!}
            </div>
        </div>
      </div>
      <div class="modal-footer">
          <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
          <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default">@lang('messages.close')</button>
      </div>
      {!! Form::close() !!}
  </div>
</div>

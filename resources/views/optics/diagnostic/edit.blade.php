<div class="modal-dialog" role="document">
  <div class="modal-content" style="border-radius: 10px;">
      {!! Form::open(['url' => action('Optics\DiagnosticController@update', [$diagnostic->id]), 'method' => 'PUT', 'id' => 'diagnostics_edit_form']) !!}
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">@lang('diagnostic.edit_diagnostic')</h4>
      </div>
      
      <div  class="modal-body">
          <!-- Name -->
          <div class="form-group">
              {!! Form::label('name', __('cashier.name') . ' : ') !!}
              <div class="wrap-inputform">
                  {!! Form::text('name', $diagnostic->name, ['class' => 'form-control', 'required', 'placeholder' => __('cashier.name')]) !!}
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

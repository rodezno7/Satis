<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('TaxGroupController@update', [$tax_group->id]), 'method' => 'PUT', 'id' => 'tax_group_edit_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'tax_rate.edit_tax_group' )</h4>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="col-sm-8">
          <div class="form-group">
            {!! Form::label('name', __( 'tax_rate.name' ) . ':*') !!}
            {!! Form::text('name', $tax_group->description, ['class' => 'form-control', 'required', 'placeholder' => __( 'tax_rate.name' )]); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('type', __( 'lang_v1.type' ) . ':*') !!}
            <select name="types" class="form-control" required>
              @if ($tax_group->type == null)
                  <option>@lang('messages.please_select')</option>
              @endif
              @foreach ($types as $t)
                @if($t == $tax_group->type)
                  <option value="{{ $t }}" selected>{{ __('lang_v1.'. $t) }}</option>
                @else
                  <option value="{{ $t }}">{{ __('lang_v1.'. $t) }}</option>
                @endif
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('taxes[]', __( 'tax_rate.taxes' ) . ':*') !!}
            {!! Form::select('taxes[]', $taxes, $tax_rate_tax_group, ['class' => 'form-control select2', 'required', 'multiple']); !!}
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
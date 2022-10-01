<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('TaxRateController@store'), 'method' => 'post', 'id' => 'tax_rate_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'tax_rate.add_tax_rate' )</h4>
    </div>

    <div class="modal-body">
      <div class="col-sm-12">
        <div class="form-group">
          {!! Form::label('name', __( 'tax_rate.name' ) . ':*') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'tax_rate.name' )]); !!}
        </div>
      </div>
      <div class="col-sm-6">
        <div class="form-group">
          {!! Form::label('type', __( 'tax_rate.type' ) . ':*') !!}
          <select name="type" class="form-control">
            @foreach ($type as $t)
              <option value="{{ $t }}">{{ __("tax_rate." . $t) }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="form-group">
          {!! Form::label('percent', __( 'tax_rate.rate' ) . ':*') !!} @show_tooltip(__('tooltip.tax_rate_options'))
          {!! Form::text('percent', null, ['class' => 'form-control input_number', 'placeholder' => __( 'tax_rate.rate'), 'required']); !!}
        </div>
      </div>
      <div class="col-sm-6">
        <div class="form-group">
          {!! Form::label('min_amount', __( 'tax_rate.min_amount' ) . ':') !!}
          {!! Form::text('min_amount', null, ['class' => 'form-control input_number', 'placeholder' => __( 'tax_rate.min_amount')]); !!}
        </div>
      </div>
      <div class="col-sm-6">
        <div class="form-group">
          {!! Form::label('max_amount', __( 'tax_rate.min_amount' ) . ':') !!}
          {!! Form::text('max_amount', null, ['class' => 'form-control input_number', 'placeholder' => __( 'tax_rate.max_amount')]); !!}
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
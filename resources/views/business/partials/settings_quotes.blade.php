<div class="pos-tab-content">
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<div class="form-group">
				{!! Form::label(__('quote.quote_validity') . ':') !!}
				{!! Form::text('quote_validity', $business->quote_validity, ['class' => 'form-control input_number']); !!}
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				{!! Form::label(__('quote.legend') . ':') !!}
				<input type="text" name="quote_legend" id="quote_legend" value="{{ $business->quote_legend }}" class="form-control">
			</div>
		</div>
	</div>
</div>
<div class="row">
	<input type="hidden" class="payment_row_index" value="{{ $row_index}}">
	
	@php
		$col_class = 'col-md-6';
		
		if (!empty($accounts)) {
			$col_class = 'col-md-4';
		}
	@endphp

	{{-- amount --}}
	<div class="{{ $col_class }}">
		<div class="form-group">
			{!! Form::label("amount_$row_index", __('purchase.amount') . ': *') !!}
			<div class="input-group">
				<span class="input-group-addon">
					<i class="fa fa-money"></i>
				</span>
				{!! Form::text("payment[$row_index][amount]", @num_format($payment_line['amount']), [
					'class' => 'form-control payment-amount input_number',
					'required',
					'id' => "amount_$row_index",
					'placeholder' => __('sale.amount')
				]) !!}
			</div>
		</div>
	</div>

	{{-- method --}}
	<div class="{{ $col_class }}">
		<div class="form-group">
			{!! Form::label("method_$row_index", __('lang_v1.payment_method') . ':') !!}
			<div class="input-group">
				<span class="input-group-addon">
					<i class="fa fa-money"></i>
				</span>
				{!! Form::select("payment[$row_index][method]", $payment_types, $payment_line['method'], [
					'class' => 'form-control col-md-12 payment_types_dropdown',
					'required',
					'id' => "method_$row_index",
					'style' => 'width: 100%;'
				]) !!}
			</div>
		</div>
	</div>

	{{-- account_id --}}
	@if (!empty($accounts))
		<div class="{{ $col_class }}">
			<div class="form-group">
				{!! Form::label("account_$row_index", __('lang_v1.payment_account') . ':') !!}
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-money"></i>
					</span>
					{!! Form::select("payment[$row_index][account_id]", $accounts, !empty($payment_line['account_id']) ? $payment_line['account_id'] : '', [
						'class' => 'form-control select2',
						'id' => "account_$row_index",
						'style' => 'width: 100%;'
					]) !!}
				</div>
			</div>
		</div>
	@endif

	<div class="clearfix"></div>

	@include('sale_pos.partials.payment_type_details')

	{{-- note --}}
	@if (config('app.business') == 'optics')
		<div class="col-md-4 show-mult-note" style="display: none;">
			<div class="form-group">
				<div class="input-group">
					{!! Form::label("note_$row_index", __('lab_order.payment_note') . ': *') !!}

					@if (auth()->user()->can('transaction_payment.edit_payment_note'))
						{!! Form::text("payment[$row_index][note]", $payment_line['note'], [
							'class' => 'form-control input_number validate-payment-note payment-form',
							'id' => "note_$row_index",
							'required',
							'placeholder' => __('lab_order.payment_note')
						]) !!}
					@else
						{!! Form::text("payment[$row_index][note]", $payment_line['note'], [
							'class' => 'form-control input_number validate-payment-note payment-form',
							'id' => "note_$row_index",
							'required',
							'placeholder' => __('lab_order.payment_note'),
							'readonly'
						]) !!}
					@endif
				</div>
			</div>
		</div>
	@else
		<div class="col-md-12">
			<div class="form-group">
				{!! Form::label("note_$row_index", __('sale.payment_note') . ':') !!}
				{!! Form::textarea("payment[$row_index][note]", $payment_line['note'], [
					'class' => 'form-control',
					'rows' => 3,
					'id' => "note_$row_index"
				]) !!}
			</div>
		</div>
	@endif
</div>
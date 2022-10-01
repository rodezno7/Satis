<div class="payment_details_div @if( $payment_line->method !== 'card' ) {{ 'hide' }} @endif" data-type="card" >
	<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("card_holder_name", __('payment.card_holder_name')) !!}
			{!! Form::text("card_holder_name", $payment_line->card_holder_name, ['class' => 'form-control', 'placeholder' => __('payment.card_holder_name')]); !!}
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("card_authotization_number", __('payment.card_authotization_number')) !!}
			{!! Form::text("card_authotization_number", $payment_line->card_authotization_number, ['class' => 'form-control input_number', 'placeholder' => __('payment.card_authotization_number')]); !!}
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("card_type", __('payment.card_type')) !!}
			{!! Form::select("card_type", ['credit' => __('payment.credit_card'), 'debit' => __('payment.debit_card'),
				'visa' => 'Visa', 'master' => 'MasterCard'], $payment_line->card_type, ['class' => 'form-control select2']); !!}
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("card_pos", __('payment.card_pos')) !!}
			{!! Form::select('card_pos', $pos, $payment_line->card_pos,
				['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]) !!}
		</div>
	</div>
	<div class="clearfix"></div>
</div>
<div class="payment_details_div @if( $payment_line->method !== 'check' ) {{ 'hide' }} @endif" data-type="check" >
	<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("check_number", __('payment.check_number')) !!}
			{!! Form::text("check_number", $payment_line->check_number,
				['class' => 'form-control input_number', 'placeholder' => __('payment.check_number')]); !!}
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("check_account", __('payment.check_account')) !!}
			{!! Form::text("check_account", $payment_line->check_account,
				['class' => 'form-control input_number', 'placeholder' => __('payment.check_account')]); !!}
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("check_bank", __('payment.check_bank')) !!}
			{!! Form::select('check_bank', $banks, $payment_line->check_bank,
				['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]) !!}
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("check_account_owner", __('payment.check_account_owner')) !!}
			{!! Form::text("check_account_owner", $payment_line->check_account_owner,
				['class' => 'form-control', 'placeholder' => __('payment.check_account_owner')]); !!}
		</div>
	</div>
</div>
<div class="payment_details_div @if( $payment_line->method !== 'bank_transfer' ) {{ 'hide' }} @endif" data-type="bank_transfer" >
	<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("transfer_ref_no", __('payment.transfer_ref_no')) !!}
			{!! Form::text( "transfer_ref_no", $payment_line->transfer_ref_no,
				['class' => 'form-control', 'placeholder' => __('payment.transfer_ref_no')]); !!}
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("transfer_issuing_bank", __('payment.transfer_issuing_bank')) !!}
			{!! Form::select('transfer_issuing_bank', $banks, $payment_line->transfer_issuing_bank,
				['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;']) !!}
		</div>
	</div>
	{{--<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("transfer_destination_account", __('payment.transfer_destination_account')) !!}
			{!! Form::text( "transfer_destination_account", $payment_line->transfer_destination_account,
				['class' => 'form-control input_number', 'placeholder' => __('payment.transfer_destination_account')]); !!}
		</div>
	</div> --}}
	<div class="col-md-4">
		<div class="form-group">
			{!! Form::label("transfer_receiving_bank", __('payment.transfer_receiving_bank')) !!}
			{!! Form::select('transfer_receiving_bank', $bank_accounts, $payment_line->transfer_receiving_bank,
				['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;']) !!}
		</div>
	</div>
	<div class="clearfix"></div>
</div>
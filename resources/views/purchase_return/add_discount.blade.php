<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle"> @lang('lang_v1.return_discount') ({{ $purchase->ref_no }})</h4>
        </div>

		{!! Form::open(['url' => action('PurchaseReturnController@postPurchaseReturnDiscount', [$purchase->id]), 'method' => 'post']) !!}
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    {!! Form::label("document_type", __('document_type.document_type')) !!} <span style="color: red">*</span>
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-file-text-o"></i>
						</span>
						{!! Form::select("document_type", $documents, !empty($purchase->return_parent->document_types_id) ? $purchase->return_parent->document_types_id : null,
							["class" => "form-control select2", "placeholder" => __("document_type.document_type"), "required"]) !!}
					</div>
                </div>
                <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('ref_no', __('purchase.ref_no').':') !!} <span style="color: red">*</span>
						<div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-hashtag"></i>
                            </span>
							{!! Form::text('ref_no', !empty($purchase->return_parent->ref_no) ? $purchase->return_parent->ref_no : null,
								['class' => 'form-control', 'placeholder' => __('purchase.ref_no'), 'required']); !!}
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('serie', __('accounting.serie').':') !!} <span style="color: red">*</span>
						<div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-hashtag"></i>
                            </span>
							{!! Form::text('serie', !empty($purchase->return_parent->serie) ? $purchase->return_parent->serie : null,
								['class' => 'form-control', 'placeholder' => __('accounting.serie'), 'required']); !!}
						</div>
					</div>
				</div>
                <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date')) !!} <span style="color: red">*</span>
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							@php
								$transaction_date = !empty($purchase->return_parent->transaction_date) ? $purchase->return_parent->transaction_date : 'now';
							@endphp
							{!! Form::text('transaction_date', @format_date($transaction_date), ['class' => 'form-control date', 'readonly', 'required']); !!}
						</div>
					</div>
				</div>
				<div class="col-md-4">
                    <div class="form-group">
                        <label for="">@lang('tax_rate.amount') <small>(@lang('expense.less_taxes'))</small></label><span style="color: red">*</span>
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-usd"></i>
							</span>
							{!! Form::text('total_before_tax', !empty($purchase->return_parent->total_before_tax) ? $purchase->return_parent->total_before_tax : null,
								['class' => 'form-control input_number', 'id' => 'subtotal', 'placeholder' => __('expense.less_taxes'), 'required']) !!}
						</div>
                    </div>
                </div>
				<div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('tax_group', __('tax_rate.tax_type') . ':') !!}
						<div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa">T</i>
                            </span>
							<select name="tax_group_id" id="taxes" class="form-control">
								<option value="">@lang('messages.please_select')</option>
								@foreach ($taxes as $t)
									<option data-tax_percent="{{ $t['percent'] }}" value="{{ $t['id'] }}"
										@if (!empty($purchase->return_parent->tax_id)) {{ $purchase->return_parent->tax_id == $t['id'] ? 'selected' : '' }} @endif>{{ $t['name'] }}</option>
								@endforeach
							</select>
						</div>
					</div>
                </div>
				<div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('iva', __('expense.taxes')) !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-usd"></i>
							</span>
							{!! Form::text('tax_amount', !empty($purchase->return_parent->tax_amount) ? $purchase->return_parent->tax_amount : '0.00', ['class' => 'form-control', 'id' => 'vat_amount', 'readonly', 'required']) !!}
						</div>
					</div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('final_total', __('sale.total_amount_expense')) !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-usd"></i>
							</span>
							{!! Form::text('final_total', !empty($purchase->return_parent->final_total) ? $purchase->return_parent->final_total : '0.00',
								['class' => 'form-control', 'id' => 'final_total', 'required']) !!}
						</div>
					</div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
		{!! Form::close() !!}
    </div>
</div>
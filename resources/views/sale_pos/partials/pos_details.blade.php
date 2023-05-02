<style>
	.pos-express-btn-op {
		font-size: 23px !important;
		overflow: hidden !important;
		height: 93px !important;
		white-space: normal;
	}
</style>

<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-body bg-gray disabled" style="margin-bottom: 0px !important">
				<table class="table table-condensed" style="margin-bottom: 0px !important">
					<tbody>
						{{-- Sale detail --}}
						<tr>
							<td>
								{{-- Number of items --}}
								<div class="col-sm-1 col-xs-3 d-inline-table">
									<b>@lang('sale.item'):</b> 
									<br/>
									<span class="total_quantity">0</span>
								</div>

								{{-- Subtotal --}}
								<div class="col-sm-2 col-xs-3 d-inline-table">
									<b>@lang('sale.subtotal'):</b> 
									<br/>
									<input type="hidden" name="subtotal" id="subtotal" value="0">
									<span class="price_total">0</span>

                                    <br>
								    {{-- Discount --}}
									<span class="@if($pos_settings['disable_discount'] != 0) hide @endif">
										<b>
											@lang('sale.discount')(-):
											{{--@show_tooltip(__('tooltip.sale_discount'))--}}
										</b> 
										<br/>
										<i class="fa fa-pencil-square-o cursor-pointer"
											id="pos-edit-discount"
											title="@lang('sale.edit_discount')"
											aria-hidden="true"
											data-toggle="modal"
											data-target="#posEditDiscountModal">
										</i>
										<span id="total_discount">0</span>

										@php
										if (empty($edit)) {
											if ($type_discount == 'percentage') {
												$discount_type = 'percentage';
											} else {
												$discount_type = 'fixed';

											}
											
										} else {
											$discount_type = $transaction->discount_type;
										}
										@endphp

										<input
											type="hidden"
											name="discount_type"
											id="discount_type"
											value="{{ $discount_type }}"
											data-default="{{ $discount_type }}">

										<input
											type="hidden"
											name="discount_amount"
											id="discount_amount"
											value="
												@if (empty($edit))
													{{ @num_format($business_details->default_sales_discount) }}
												@else
													{{ @num_format($transaction->discount_amount) }}
												@endif"
											data-default="{{ $business_details->default_sales_discount }}">
									</span>
								</div>

								<div class="col-sm-2 col-xs-6 d-inline-table">
									{{-- Tax --}}
									<span class="@if ($pos_settings['disable_order_tax'] != 0) hide @endif">
										<b>
											@lang('sale.order_tax')(+):
											{{-- @show_tooltip(__('tooltip.sale_tax')) --}}
										</b>
										<br/>
										
										{{-- <i class="fa fa-pencil-square-o"
											title="@lang('sale.edit_order_tax')"
											aria-hidden="true"
											data-toggle="modal"
											data-target="#posEditOrderTaxModal">
										</i> --}}

										<span id="order_tax">
											@if(empty($edit))
												0
											@else
												{{$transaction->tax_amount}}
											@endif
										</span>
										
										{{-- <input
											type="hidden"
											name="tax_rate_id" 
											id="tax_rate_id" 
											value="
												@if (empty($edit))
													{{ $business_details->default_sales_tax }}
												@else
													{{ $transaction->tax_id }}
												@endif" 
											data-default="{{ $business_details->default_sales_tax }}">--}}

										<input type="hidden" name="original_tax_amount" id="original_tax_amount">
										
										<input
											type="hidden"
											name="tax_calculation_amount"
											id="tax_calculation_amount" 
											value="
												@if (empty($edit))
													{{ @num_format($business_details->tax_calculation_amount) }}
												@else
													{{ @num_format(optional($transaction->tax)->amount) }}
												@endif"
											data-default="{{ $business_details->tax_calculation_amount }}">
									</span>
									<br/>

									{{-- Perception & Withheld --}}
									<b>@lang('tax_rate.withheld')(-):</b> 
									<br/>
									<input type="hidden" name="withheld" id="withheld">
									<span class="withheld">0</span>
								</div>

								{{-- Shipping --}}
								<div class="col-sm-2 col-xs-6 d-inline-table">
									<span class="@if($pos_settings['disable_discount'] != 0) hide @endif">
										<b>
											@lang('sale.shipping')(+): @show_tooltip(__('tooltip.shipping'))
										</b> 
										<br/>
										<i class="fa fa-pencil-square-o cursor-pointer"
											title="@lang('sale.edit_shipping')"
											aria-hidden="true"
											data-toggle="modal"
											data-target="#posShippingModal">
										</i>
										<span id="shipping_charges_amount">0</span>

										<input
											type="hidden"
											name="shipping_details"
											id="shipping_details"
											value="
												@if (empty($edit))
													{{ "" }}
												@else
													{{ $transaction->shipping_details }}
												@endif"
											data-default="">

										<input
											type="hidden"
											name="shipping_charges"
											id="shipping_charges"
											value="
												@if (empty($edit))
													{{ @num_format(0.00) }}
												@else
													{{ @num_format($transaction->shipping_charges) }}
												@endif"
											data-default="0.00">
									</span>
                                    <br>
                                    <b>Exportación (+)</b> @show_tooltip(__('tooltip.export_expenses'))
                                    <br>
                                    <i class="fa fa-pencil-square-o cursor-pointer"
											title="@lang('sale.edit_export_expenses')"
											aria-hidden="true"
											data-toggle="modal"
											data-target="#posEditExportExpenseModal">
										</i>
                                    <span id="export_expense_total">0</span>
                                    <input type="hidden" id="exp_exp_total" value="0">
								</div>

								<div class="col-sm-3 col-xs-12 d-inline-table">
									{{-- Total --}}
									<b>@lang('sale.total_payable'):</b>
									<br/>
									<input type="hidden" name="final_total" id="final_total_input" value=0>
									<span id="total_payable" class="text-success lead text-bold">0</span>

									{{-- Button to cancel --}}
									@if (empty($edit))
										<button
											type="button"
											class="btn btn-danger btn-flat btn-xs pull-right"
											id="pos-cancel">
											@lang('sale.cancel')
										</button>
									@else
										<button
											type="button"
											class="btn btn-danger btn-flat hide btn-xs pull-right"
											id="pos-delete">
											@lang('messages.delete')
										</button>
									@endif
								</div>
							</td>
						</tr>

						{{-- Buttons --}}
						@if (config('app.business') == 'optics')
						<tr>
							<td>
								<div class="col-sm-2 col-xs-6 col-2px-padding">
									{{-- Lab order --}}
									<button type="button"
										class="btn bg-maroon btn-block btn-flat btn-modal no-print pos-express-btn"
										data-href="{{ action('Optics\LabOrderController@createLabOrder') }}"
										data-container=".add_lab_order_modal"
										data-backdrop="static"
										title="@lang('lab_order.lab_order')">
										<div class="text-center" style="font-size: 0.9em">
											<b>@lang('lab_order.lab_order')</b>
										</div>
									</button>
								</div>
	
								<div class="col-sm-2 col-xs-6 col-2px-padding">
									{{-- Reservations --}}
									<button
										type="button"
										id="reservation-finalize"
										class="btn bg-blue btn-block btn-flat btn-lg no-print pos-express-btn"
										data-button="reservation"
										title="@lang('cash_register.reservations')"
										@if (! empty($edit)) @if (! $is_quote) disabled @endif @endif>
										<div class="text-center" style="font-size: 0.9em;">
											<i class="fa fa-calendar-check-o" aria-hidden="true"></i>
											<b>@lang('lang_v1.reservation')</b>
										</div>
									</button>
								</div>
	
								<div class="col-sm-4 col-xs-12 col-2px-padding">
									{{-- Process payment --}}
									<button
										type="button"
										class="btn bg-navy btn-block btn-flat btn-lg no-print pos-express-btn"
										id="pos-finalize"
										title="@lang('lang_v1.tooltip_checkout_multi_pay')"
										@if (! empty($edit)) @if ($is_quote) disabled @endif @endif>
										<div class="text-center" style="font-size: 0.9em">
											<i class="fa fa-credit-card" aria-hidden="true"></i>
											<b>@lang('lang_v1.process_payment')</b>
										</div>
									</button>
								</div>
	
								<div class="col-sm-2 col-xs-12 col-2px-padding">
									{{-- Inflow --}}
									<button
										type="button"
										id="btn_inflow"
										title="{{ __('inflow_outflow.inputs') }}"
										data-toggle="tooltip"
										data-placement="top"
										data-container=".inflow_outflow_modal"
										data-href="{{ action('Optics\InflowOutflowController@create', ['type' => 'input']) }}"
										class="btn btn-success btn-block btn-flat btn-lg no-print pos-express-btn"
										@if (! empty($edit)) disabled @endif>
										<div class="text-center" style="font-size: 0.9em">
											<i class="fa fa-arrow-circle-down" aria-hidden="true"></i>
											<b>@lang('inflow_outflow.inputs')</b>
										</div>
									</button>
	
									{!! Form::hidden("is_credit", 0, ["id" => "is_credit"]) !!}
									{!! Form::hidden("pay_term_number", 0, ["id" => "pay_term_number"]) !!}

									<input type="hidden" id="is_cash" value="0">
								</div>
	
								<div class="col-sm-2 col-xs-12 col-2px-padding">
									{{-- Outflow --}}
									<button
										type="button"
										id="btn_outflow"
										title="{{ __('inflow_outflow.outputs') }}"
										data-toggle="tooltip"
										data-placement="top"
										class="btn bg-red btn-block btn-flat btn-lg no-print pos-express-btn"
										data-container=".inflow_outflow_modal" 
										data-href="{{ action('Optics\InflowOutflowController@create', ['type' => 'output']) }}"
										@if (! empty($edit)) disabled @endif>
										<div class="text-center" style="font-size: 0.9em;">
											<i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
											<b>@lang('inflow_outflow.outputs')</b>
										</div>
									</button>
								</div>
	
								<div class="div-overlay pos-processing"></div>
							</td>
						</tr>
						@else
						<tr>
							<td>
								<!--- <div class="col-sm-2 col-xs-6 col-2px-padding">
									{{-- Draft --}}
									<button
										type="button" 
										class="btn btn-warning btn-block btn-flat @if ($pos_settings['disable_draft'] != 0) hide @endif" 
										id="pos-draft">
										@lang('sale.draft')
									</button>

									{{-- Quotation --}}
									<button type="button" 
										class="btn btn-info btn-block btn-flat" 
										id="pos-quotation">
										@lang('lang_v1.quotation')
									</button>
								</div>

								<div class="col-sm-2 col-xs-6 col-2px-padding">
									{{-- Card --}}
									<button
										type="button" 
										class="btn bg-maroon btn-block btn-flat no-print @if (!empty($pos_settings['disable_suspend'])) pos-express-btn btn-lg @endif pos-express-finalize" 
										data-pay_method="card"
										title="@lang('lang_v1.tooltip_express_checkout_card')">
										<div class="text-center">
											<i class="fa fa-check" aria-hidden="true"></i>
											<b>@lang('lang_v1.express_checkout_card')</b>
										</div>
									</button>

									@if (empty($pos_settings['disable_suspend']))
										{{-- Suspend --}}
										<button
											type="button" 
											class="btn bg-red btn-block btn-flat no-print pos-express-finalize" 
											data-pay_method="suspend"
											title="@lang('lang_v1.tooltip_suspend')">
											<div class="text-center">
												<i class="fa fa-pause" aria-hidden="true"></i>
												<b>@lang('lang_v1.suspend')</b>
											</div>
										</button>
									@endif
								</div> -->

								<div class="col-sm-4 col-xs-12 col-2px-padding">
									{{-- Multi pay --}}
									<button
										type="button"
										class="btn bg-navy btn-block btn-flat btn-lg no-print @if ($pos_settings['disable_pay_checkout'] != 0) hide @endif pos-express-btn"
										id="pos-finalize"
										title="@lang('lang_v1.tooltip_checkout_multi_pay')">
										<div class="text-center" style="font-size: 0.9em">
											<i class="fa fa-credit-card" aria-hidden="true"></i>
											<b>@lang('lang_v1.checkout_multi_pay')</b>
										</div>
									</button>
								</div>

								<div class="col-sm-4 col-xs-12 col-2px-padding">
									{{-- Credit --}}
									<button
										type="button"
										class="btn bg-blue btn-block btn-flat btn-lg no-print @if ($pos_settings['disable_express_checkout'] != 0) hide @endif pos-express-btn pos-express-finalize"
										data-pay_method="credit"
										title="@lang('lang_v1.tooltip_credit_sell')">
										<div class="text-center" style="font-size: 0.8em">
											<i class="fa fa-check" aria-hidden="true"></i>
											<b>@lang('lang_v1.credit')</b>
										</div>
									</button>

									{!! Form::hidden("is_credit", 0, ["id" => "is_credit"]) !!}
									{!! Form::hidden("pay_term_number", 0, ["id" => "pay_term_number"]) !!}
								</div>

								<div class="col-sm-4 col-xs-12 col-2px-padding">
									{{-- Cash --}}
									<button
										type="button"
										class="btn btn-success btn-block btn-flat btn-lg no-print @if ($pos_settings['disable_express_checkout'] != 0) hide @endif pos-express-btn pos-express-finalize"
										data-pay_method="cash"
										title="@lang('tooltip.express_checkout')">
										<div class="text-center" style="font-size: 0.8em;">
											<i class="fa fa-money" aria-hidden="true"></i>
											<b>@lang('lang_v1.express_checkout_cash')</b>
										</div>
									</button>
								</div>

								<div class="div-overlay pos-processing"></div>
							</td>
						</tr>
						@endif
					</tbody>
				</table>

				{{-- Button to perform various actions --}}
				<div class="row">
				</div>
			</div>
		</div>
	</div>
</div>

@if (isset($transaction))
	@include('sale_pos.partials.edit_discount_modal', [
		'sales_discount' => $transaction->discount_amount,
		'discount_type' => $transaction->discount_type,
		'max_sale_discount' => $max_discount
	])
@else
	@include('sale_pos.partials.edit_discount_modal', [
		'sales_discount' => $business_details->default_sales_discount,
		'discount_type' => $discount_type,
		'max_sale_discount' => $max_discount
	])
@endif

@include('sale_pos.partials.edit_export_expense_modal')

@if (isset($transaction))
	@include('sale_pos.partials.edit_order_tax_modal', ['selected_tax' => $transaction->tax_id])
@else
	@include('sale_pos.partials.edit_order_tax_modal', ['selected_tax' => $business_details->default_sales_tax])
@endif

@if (isset($transaction))
	@include('sale_pos.partials.edit_shipping_modal', [
		'shipping_charges' => $transaction->shipping_charges,
		'shipping_details' => $transaction->shipping_details
	])
@else
	@include('sale_pos.partials.edit_shipping_modal', [
		'shipping_charges' => '0.00',
		'shipping_details' => ''
	])
@endif
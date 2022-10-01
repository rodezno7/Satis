@extends('layouts.app')

@section('title', 'POS')

@section('content')

<input type="hidden" id="__precision" value="{{config('constants.currency_precision')}}">

{{-- Main content --}}
<section class="content no-print">
	<div class="row">
		<div class="@if(!empty($pos_settings['hide_product_suggestion']) && !empty($pos_settings['hide_recent_trans'])) col-md-10 col-md-offset-1 @else col-md-7 @endif col-sm-12">
			<div class="box box-success">
				<div class="box-header with-border">
					<div class="col-sm-3" style="padding-left: 0;">
						<h3 class="box-title" style="margin-top: 9px;">
							POS Terminal
							<i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('sale_pos.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i>
						</h3>
					</div>

					<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">

					<input type="hidden" id="cashier_closure_receipt" value="{{ $cashier_closure_id ? $cashier_closure_id : null }}">

					{{-- Allow invoicing sales with partial payment of any customer --}}
					<input type="hidden" id="partial_payment_any_customer" value="{{ $pos_settings['partial_payment_any_customer'] }}">

					{{-- Location --}}
					@if (is_null($default_location))
						<div class="col-sm-3">
							<div class="form-group" style="margin-bottom: 0px;">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-map-marker"></i>
									</span>
									{!! Form::select('select_location_id', $business_locations, null, [
										'class' => 'form-control mousetrap select2', 
										'style' => 'width: 100%',
										'placeholder' => __('lang_v1.select_location'),
										'id' => 'select_location_id', 
										'required'
									], $bl_attributes) !!}
									<span class="input-group-addon">
										@show_tooltip(__('tooltip.sale_location'))
									</span> 
								</div>
							</div>
						</div>
					@else
						<div class="col-sm-3">
							<div class="form-group" style="margin-bottom: 0px;">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-map-marker"></i>
									</span>
									{!! Form::select('select_location_id', $business_locations, $default_location, [
										'class' => 'form-control mousetrap select2', 
										'placeholder' => __('lang_v1.select_location'),
										'id' => 'select_location_id', 
										'required',
										'autofocus',
										'disabled'
									], $bl_attributes) !!}
									<span class="input-group-addon">
										@show_tooltip(__('tooltip.sale_location'))
									</span> 
								</div>
							</div>
						</div>
					@endif

					{{-- Warehouse --}}
					<div class="col-sm-3">
						<div class="form-group" style="margin-bottom: 0px;">
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-cubes"></i>
								</span>

								@if (is_null($default_warehouse))
								{!! Form::select("select_warehouse_id", $warehouses, null, [
									"class" => "form-control select2",
									"id" => "select_warehouse_id",
									"style" => "width: 100%",
									"placeholder" => __("lang_v1.select_warehouse")
								]) !!}
								@else
								{!! Form::select("select_warehouse_id", $warehouses, $default_warehouse, [
									"class" => "form-control select2",
									"id" => "select_warehouse_id",
									"style" => "width: 100%",
									"placeholder" => __("lang_v1.select_warehouse"),
									"disabled"
								]) !!}
								@endif

								<span class="input-group-addon">
									@show_tooltip(__('tooltip.warehouse_select'))
								</span> 
							</div>
						</div>
					</div>

					{{-- Cashier --}}
					<div class="col-sm-3" style="padding-right: 0;">
						<div class="form-group" style="margin-bottom: 0px;">
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-money"></i>
								</span>

								@if (is_null($default_cashier))
								{!! Form::select("select_cashier_id", $cashiers, null, [
									"class" => "form-control select2",
									"id" => "select_cashier_id",
									"style" => "width: 100%",
									"placeholder" => __("lang_v1.select_cashier")
								]) !!}
								@else
								{!! Form::select("select_cashier_id", $cashiers, $default_cashier, [
									"class" => "form-control select2",
									"id" => "select_cashier_id",
									"style" => "width: 100%",
									"placeholder" => __("lang_v1.select_cashier"),
									"disabled"
								]) !!}
								@endif

								<span class="input-group-addon">
									@show_tooltip(__('tooltip.cashier_select'))
								</span> 
							</div>
						</div>
					</div>
				</div>

				{!! Form::open(['url' => action('SellPosController@store'), 'method' => 'post', 'id' => 'add_pos_sell_form' ]) !!}

				{!! Form::hidden('location_id', $default_location, ['id' => 'location_id', 'data-receipt_printer_type' => isset($bl_attributes[$default_location]['data-receipt_printer_type']) ? $bl_attributes[$default_location]['data-receipt_printer_type'] : 'browser']) !!}
				{!! Form::hidden('warehouse_id', null, ['id' => 'warehouse_id']) !!}
				{!! Form::hidden('cashier_id', null, ['id' => 'cashier_id']) !!}

				{!! Form::hidden('flag-correlative', 0, ['id' => 'flag-correlative']) !!}

				{{-- Number of decimal places seen in the interface --}}
				<input type="hidden" id="decimals_in_sales" value="{{ $decimals_in_sales }}">

				{{-- Number of decimal places to store and use in calculations --}}
				<input type="hidden" id="price_precision" value="{{ config('app.price_precision') }}">

				<input type="hidden" id="flag-reservation" value="0">
				<input type="hidden" id="reservation-route" value="{{ action('ReservationController@store') }}">
				<input type="hidden" name="reservation_id" id="reservation_id" value="">
				<input type="hidden" name="flag-first-row" id="flag-first-row" value="0">

				@if (config('app.business') == 'optics')
				{!! Form::hidden('payment_note_id', $payment_note_id, ['id' => 'payment_note_id']) !!}

				<input type="hidden" id="flag-payment-note" value="0">

				{{-- Final correlative --}}
				<input type="hidden" id="final-correlative" value="" data-route="{{ action('SellPosController@getFinalCorrelative') }}">
				@endif

				{{-- App business --}}
				<input type="hidden" id="app_business" value="{{ config('app.business') }}">

				<input type="hidden" id="document_validate">

				{{-- Quote tax detail --}}
				<input type="hidden" id="quote-tax-detail" value="">

				<div class="box-body">
					<div class="row">
						{{-- Customer --}}
						<div class="@if (config('app.business') == 'optics') col-sm-6 @else col-sm-4 @endif">
							<div class="form-group" style="width: 100% !important">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-user-o"></i>
									</span>
									<input type="hidden" id="default_customer_id" value="{{ $walk_in_customer['id'] }}">
									<input type="hidden" id="default_customer_name" value="{{ $walk_in_customer['name'] }}">
									<input type="hidden" id="default_allowed_credit" value="{{ $walk_in_customer['allowed_credit'] }}">
									<input type="hidden" id="default_is_exempt" value="{{ $walk_in_customer['is_exempt'] }}">
									<input type="hidden" id="default_is_withholding_agent" value="{{ $walk_in_customer['is_withholding_agent'] }}">
									<input type="hidden" id="default_tax_group_id" value="{{ $walk_in_customer['tax_group_id'] }}">
									<input type="hidden" id="default_tax_percent" value="{{ $walk_in_customer['tax_percent'] }}">
									<input type="hidden" id="default_min_amount" value="{{ $walk_in_customer['min_amount'] }}">
									<input type="hidden" id="default_max_amount" value="{{ $walk_in_customer['max_amount'] }}">
									{!! Form::select('customer_id', [], null, [
										'class' => 'form-control mousetrap',
										'id' => 'customer_id',
										'placeholder' => 'Enter Customer name / phone',
										'required',
										'disabled']) !!}
									<span class="input-group-btn">
										<button
											type="button"
											class="btn btn-default bg-white btn-flat add_new_customer"
											data-name=""
											@if(!auth()->user()->can('customer.create')) disabled @endif>
											<i class="fa fa-plus-circle text-primary fa-lg"></i>
										</button>
									</span>
								</div>
							</div>
						</div>

						{{-- Transaction date --}}
						@if (config('app.business') != 'optics')
						<div class="col-sm-2">
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon">
										<span>
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									{!! Form::text("transaction_date", @format_date('now'), [
										'class' => 'form-control',
										'id' => 'transaction_date',
										'required',
										'readonly',
										'style' => 'width: 100%'
									]) !!}
								</div>
							</div>
						</div>
						@endif

						<input type="hidden" name="allowed_credit" id="allowed_credit" value="{{ $walk_in_customer['allowed_credit'] }}">
						<input type="hidden" name="is_withholding_agent" id="is_withholding_agent" value="{{ $walk_in_customer['is_withholding_agent'] }}">
						<input type="hidden" name="is_exempt" id="is_exempt" value="{{ $walk_in_customer['is_exempt'] }}">
						<input type="hidden" name="order_id" id="order_id" value="">
						<input type="hidden" name="tax_group_id" id="tax_group_id" value="{{ $walk_in_customer['tax_group_id'] }}">
						<input type="hidden" name="tax_group_percent" id="tax_group_percent" value="{{$walk_in_customer['tax_percent'] }}">
						<input type="hidden" name="min_amount" id="min_amount" value="{{ $walk_in_customer['min_amount'] }}">
						<input type="hidden" name="max_amount" id="max_amount" value="{{ $walk_in_customer['max_amount'] }}">

						{{-- Request DUIs for FCF equal to or greater than max operation --}}
						<input type="hidden" id="fcf_document" value="{{ $fcf_document->id }}" data-max-operation="{{ $fcf_document->max_operation }}">

						{{-- Request DUIs for CCF equal to or greater than max operation --}}
						<input type="hidden" id="ccf_document" value="{{ $ccf_document->id }}" data-max-operation="{{ $ccf_document->max_operation }}">

						{{-- Selected payment method --}}
						<input type="hidden" id="selected_payment_method" value="">

						{{-- Customer name --}}
						<div class="col-sm-4" id="customer_name_div">
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-user-o"></i>
								</span>
								{!! Form::text("customer_name", $walk_in_customer['name'], [
									"class" => "form-control",
									"id" => "customer_name",
									"placeholder" => __("sale.customer_name"),
									"disabled"
								]) !!}
								<span class="input-group-addon">
									@show_tooltip(__('tooltip.customer_name_help_text'))
								</span>
							</div>
						</div>

						{{-- DUI --}}
						@if (config('app.business') != 'optics')
						<div class="col-sm-2" id="customer_name_div">
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-id-card-o"></i>
								</span>
								{!! Form::text("customer_dui", null, [
									"class" => "form-control dui-mask",
									"id" => "customer_dui",
									"placeholder" => __("customer.dui")
								]) !!}
							</div>
						</div>
						@endif

						{{-- Transaction date --}}
						@if (config('app.business') == 'optics')
						<div class="col-sm-2">
							<div class="form-group">
								<div id="datetimepicker2" class="input-group date" data-target-input="nearest">
									<span class="input-group-addon" data-target="#datetimepicker2" data-toggle="datetimepicker">
										<span class="glyphicon glyphicon-calendar"></span>
									</span>
									<input
										type="text"
										class="form-control datetimepicker-input"
										data-toggle="datetimepicker"
										data-target="#datetimepicker2"
										name="transaction_date"
										id="transaction_date"
										value="{{ @format_date('now') }}"
										required
										readonly>
								</div>
							</div>
						</div>
						@endif
					</div>

					<div class="row">
						@if (config('app.business') == 'workshop')
							{{-- Customer vehicle --}}
							<div class="col-sm-6">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-car"></i>
										</span>
				
										{!! Form::select('customer_vehicle_id', [], null, [
											'class' => 'form-control select2',
											'id' => 'customer_vehicle_id',
											'placeholder' => __('quote.customer_vehicle'),
											'style' => 'width: 100;',
											'required'
										]) !!}
									</div>
								</div>
							</div>
						@endif

						@if (!empty($documents))
							@if (count($documents) > 0)
								{{-- Document --}}
								<div class="col-md-4 col-sm-4">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-money"></i>
											</span>
											@php
												reset($documents);
											@endphp
											{!! Form::hidden('hidden_documents', key($documents), ['id' => 'hidden_documents']) !!}
											<input type="hidden" id="default_doc" value="{{ $default->id }}" >
											{{--!! Form::select('documents', $documents, null, ['class' => '', 'id' => 'documents', 'style' => 'width: 100%;']); !!--}}
											<select name="documents" id="documents" class="form-control select2" style="width: 100%;" disabled>
											@foreach ($documents as $doc)
												<option value="{{ $doc->id }}" data-is_default="{{ $doc->is_default }}" {{ $doc->is_default == true ? 'selected':'' }} data-tax_inc="{{ $doc->tax_inc }}" data-tax_exempt="{{ $doc->tax_exempt }}">
													{{ $doc->short_name }}
												</option>
											@endforeach
											</select>
											<span class="input-group-addon">
												@show_tooltip(__('document_type.tooltip_print_format'))
											</span> 
										</div>
									</div>
								</div>

								{{-- Correlative --}}
								<div class="col-md-2 col-sm-2">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-hashtag"></i>
											</span>
											{!! Form::text('correlatives', null, [
												'class' => 'form-control',
												'disabled',
												'id' => 'correlatives',
												'style' => 'width: 100%'
											]) !!}
											<span class="input-group-addon">
												@show_tooltip(__('lang_v1.correlatives_help_text'))
											</span>
										</div>
									</div>
								</div>
							@else
								@php
									reset($documents);
								@endphp
								{!! Form::hidden('documents', key($documents), ['id' => 'documents']) !!}
							@endif
						@endif

						@if (config('app.business') == 'optics')
							{{-- Commission agent --}}
							<div class="col-sm-2">
								<div class="input-group">
									{!! Form::text('input-commission-agent', null, [
										'class' => 'form-control text-center',
										'placeholder' => __('customer.employee_code'),
										'id' => 'input-commission-agent'
									]) !!}
									<span class="input-group-btn">
										<button type="button" id="btn-commission-agent" class="btn btn-info">
											<i class="fa fa-search"></i>
										</button>
									</span>
								</div>
							</div>

							<div class="col-sm-4">
								<div class="form-group">
									{!! Form::text('txt-commission-agent', null, [
										'class' => 'form-control text-center',
										'readonly', 'id' => 'txt-commission-agent'
									]) !!}
								</div>
							</div>

							{!! Form::hidden('commission_agent', null, ['id' => 'commission_agent']) !!}
						@else
							{{-- Commission agent --}}
							@if (!empty($commission_agent))
								<div class="@if(!empty($commission_agent)) col-sm-3 @else col-sm-6 @endif">
									<div class="form-group">
										{!! Form::select('commission_agent', $commission_agent, null, [
											'class' => 'form-control select2',
											'placeholder' => __('lang_v1.commission_agent')
										]) !!}
									</div>
								</div>
							@endif

							{{-- Pending orders --}}
							<div class="@if(!empty($commission_agent)) col-sm-3 @else col-sm-6 @endif">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-file-o"></i>
										</span>
										@php
											reset($price_groups);
										@endphp
										{!! Form::select('orders', [], null, [
											'class' => 'form-control',
											'id' => 'orders',
											'style' => 'width: 100%;',
											'placeholder' => __("order.pending_orders"),
											'disabled'
										]) !!}
										<span class="input-group-addon">
											@show_tooltip(__('tooltip.work_order_help_text'))
										</span>
									</div>
								</div>
							</div>

							@if (config('app.business') == 'workshop')
								{{-- Product --}}
								<div class="col-sm-6">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-barcode"></i>
											</span>

											{!! Form::text('search_product', null, [
												'class' => 'form-control mousetrap',
												'id' => 'search_product',
												'placeholder' => __('lang_v1.search_product_placeholder'),
												'disabled' => is_null($default_location) ? true : false,
												'autofocus' => is_null($default_location) ? false : true,
											]) !!}

											<span class="input-group-btn">
												<button type="button" class="btn btn-default bg-white btn-flat pos_add_quick_product" data-href="{{ config('app.business') == 'optics' ? action('Optics\ProductController@quickAdd') : action('ProductController@quickAdd') }}" data-container=".quick_add_product_modal">
													<i class="fa fa-plus-circle text-primary fa-lg"></i>
												</button>
											</span>
										</div>
									</div>
								</div>
							@endif
						@endif
					</div>

					<div class="row" @if ($pos_settings['show_comment_field'] == 0 && $pos_settings['show_order_number_field'] == 0) style="display: none;" @endif>
						{{-- Comment --}}
						<div class="col-sm-9" @if ($pos_settings['show_comment_field'] == 0) style="display: none;" @endif>
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-comment-o"></i>
									</span>
									{!! Form::text('staff_note', null, [
										'class' => 'form-control',
										'id' => 'staff_note',
										'placeholder' => __('accounting.comment'),
										'style' => 'width: 100%'
									]) !!}
								</div>
							</div>
						</div>

						{{-- # Order --}}
						<div class="col-sm-3" @if ($pos_settings['show_order_number_field'] == 0) style="display: none;" @endif>
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-hashtag"></i>
									</span>
									{!! Form::text('sale_note', null, [
										'class' => 'form-control',
										'id' => 'sale_note',
										'placeholder' => __('sale.no_order'),
										'style' => 'width: 100%'
									]) !!}
								</div>
							</div>
						</div>
					</div>

					@if (config('app.business') == 'optics')
					<div class="row">
						{{-- Patients --}}
						<div class="col-sm-6">
							<div class="form-group" style="width: 100% !important">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-user-md"></i>
									</span>
									{!! Form::select('select_patient_id', [], null, [
										'class' => 'form-control mousetrap',
										'id' => 'select_patient_id',
										'placeholder' => __('graduation_card.patient'),
										'style' => 'width: 100%;'
									]) !!}
									<span class="input-group-btn">
										<button type="button" class="btn btn-default bg-white btn-flat add_new_patient" data-name=""
											@if (! auth()->user()->can('patients.create')) disabled @endif>
											<i class="fa fa-plus-circle text-primary fa-lg"></i>
										</button>
									</span>
								</div>
							</div>
						</div>

						{{-- Reservations --}}
						<div class="col-md-6 col-sm-6">
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-calendar-check-o"></i>
									</span>
									@php
										reset($price_groups);
									@endphp
									{!! Form::select('reservations', [], null, [
										'class' => 'form-control',
										'id' => 'reservations',
										'style' => 'width: 100%;',
										'placeholder' => __("lang_v1.pending_reservations"),
										'disabled'
									]) !!}
									<span class="input-group-addon">
										@show_tooltip(__('tooltip.reservation_help_text'))
									</span>
								</div>
							</div>
						</div>
					</div>
					@endif

					{{-- Price group --}}
					<div class="row">
						@if (!empty($price_groups))
							@if (count($price_groups) > 1)
								<div class="col-md-4 col-sm-4">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-money"></i>
											</span>
											@php
												reset($price_groups);
											@endphp
											{!! Form::hidden('hidden_price_group', key($price_groups), ['id' => 'hidden_price_group']) !!}
											{!! Form::select('price_group', $price_groups, null, [
												'class' => 'form-control select2',
												'id' => 'price_group',
												'style' => 'width: 100%;',
												'disabled',
												'placeholder' => __('product.price_list')
											]) !!}
											<span class="input-group-addon">
												@show_tooltip(__('lang_v1.price_group_help_text'))
											</span> 
										</div>
									</div>
								</div>
							@else
								@php
									reset($price_groups);
								@endphp
								{!! Form::hidden('price_group', key($price_groups), ['id' => 'price_group']) !!}
							@endif
						@endif

						{{-- Parent correlative --}}
						<div class="col-sm-2">
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-file-text-o"></i>
									</span>
									{!! Form::select("return_parent_id", [], null,
										["class" => "form-control", "id" => "return_parent_id",
											"placeholder" => __("sale.parent_doc"), "disabled"]) !!}
									<span class="input-group-addon">
										@show_tooltip(__('tooltip.parent_correlative_text'))
									</span>
								</div>
							</div>
							{!! Form::hidden("parent_correlative", null, ["id" => "parent_correlative"]) !!}
						</div>

						@if (config('app.business') != 'workshop')
							{{-- Product --}}
							<div class="@if(!empty($commission_agent)) col-sm-6 @else col-sm-6 @endif">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-barcode"></i>
										</span>
										{!! Form::text('search_product', null, [
											'class' => 'form-control mousetrap',
											'id' => 'search_product',
											'placeholder' => __('lang_v1.search_product_placeholder'),
											'disabled' => is_null($default_location) ? true : false,
											'autofocus' => is_null($default_location) ? false : true,
										]) !!}
										<span class="input-group-btn">
											<button type="button" class="btn btn-default bg-white btn-flat pos_add_quick_product" data-href="{{ config('app.business') == 'optics' ? action('Optics\ProductController@quickAdd') : action('ProductController@quickAdd') }}" data-container=".quick_add_product_modal">
												<i class="fa fa-plus-circle text-primary fa-lg"></i>
											</button>
										</span>
									</div>
								</div>
							</div>
						@endif

						@if (config('app.business') == 'optics')
						{{-- Pending orders --}}
						<div class="col-sm-6" style="display: none;">
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-file-o"></i>
									</span>
									@php
										reset($price_groups);
									@endphp
									{!! Form::select('orders', [], null, [
										'class' => 'form-control',
										'id' => 'orders',
										'style' => 'width: 100%;',
										'placeholder' => __("order.pending_orders"),
										'disabled'
									]) !!}
									<span class="input-group-addon">
										@show_tooltip(__('tooltip.work_order_help_text'))
									</span>
								</div>
							</div>
						</div>
						@endif

						<div class="clearfix"></div>

						{{-- Call restaurant module if defined --}}
				        @if (in_array('tables' ,$enabled_modules) || in_array('service_staff', $enabled_modules))
				        	<span id="restaurant_module_span">
				          		<div class="col-md-3"></div>
				        	</span>
				        @endif
			        </div>

					<div class="row col-sm-12 pos_product_div">
						<input type="hidden" name="sell_price_tax" id="sell_price_tax" value="{{ $business_details->sell_price_tax }}">

						{{-- Keeps count of product rows --}}
						<input type="hidden" id="product_row_count" value="0">
						@php
							$hide_tax = '';

							if (session()->get('business.enable_inline_tax') == 0 && $is_admin == false) {
								$hide_tax = 'hide';
							}
						@endphp

						<table class="table table-condensed table-bordered table-striped table-responsive" id="pos_table">
							<thead>
								<tr>
									<th class="tex-center col-md-4">	
										@lang('sale.product') @show_tooltip(__('lang_v1.tooltip_sell_product_column'))
									</th>
									<th class="text-center col-md-3">
										@lang('sale.qty')
									</th>
									<th class="text-center col-md-2 {{ $hide_tax }}">
										@lang('sale.unit_price')
									</th>
									<th class="text-center col-md-3">
										@lang('sale.subtotal')
									</th>
									<th class="text-center">
										<i class="fa fa-close" aria-hidden="true"></i>
									</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>

					@include('sale_pos.partials.pos_details')

					{{-- Fiscal document validation modal --}}
					@include('sale_pos.partials.document_validation_modal')

					@include('sale_pos.partials.payment_modal')

					@if (empty($pos_settings['disable_suspend']))
						@include('sale_pos.partials.suspend_note_modal')
					@endif
				</div>
				{!! Form::close() !!}
			</div>
		</div>

		<div class="col-md-5 col-sm-12">
			@include('sale_pos.partials.right_div')
		</div>
	</div>
</section>

{{-- This will be printed --}}
<section class="invoice print_section" id="receipt_section">
</section>

{{-- Customer modal --}}
<div class="modal fade customer_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	{{--@include('contact.create', ['quick_add' => true])--}}
</div>

{{-- Register details modal --}}
<div class="modal fade register_details_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

{{-- Close register modal --}}
<div class="modal fade close_register_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

{{-- Quick product modal --}}
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle">
</div>

{{-- Patient modal --}}
<div class="modal fade patient_modal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

{{-- Lab order modal --}}
<div class="modal fade add_lab_order_modal no-print" data-backdrop="static" data-keyboard="false" id="modal-add-order" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

{{-- Graduation card modal --}}
<div class="modal fade graduation_cards_modal no-print" data-backdrop="static" data-keyboard="false" id="modal-add-order" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

{{-- Inflow and outflow modal --}}
<div class="modal fade inflow_outflow_modal no-print" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

@stop

@section('javascript')
	<script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>

	@include('sale_pos.partials.keyboard_shortcuts')

	{{-- Call restaurant module if defined --}}
    @if (in_array('tables', $enabled_modules) || in_array('modifiers', $enabled_modules) || in_array('service_staff', $enabled_modules))
    <script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif

	@if (config('app.business') == 'optics')
	<script type="text/javascript" src="{{ asset('/plugins/tempus/moment-with-locales.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/plugins/tempus/tempusdominus-bootstrap-3.min.js') }}"></script>
	<script src="{{ asset('js/lab_order.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/inflow_outflow.js?v=' . $asset_v) }}"></script>

	<script>
		function SearchEmployee() {
			var code = $("#employee_code").val();

			if (code == '') {
				$('#employee_name').val('');
			} else {
				var route = "/patients/getEmployeeByCode/"+code;
				$.get(route, function(res) {
					if (res.success) {
						if (res.emp) {
							$('#employee_name').val(res.msg);
						} else {
							$('#employee_name').val('');
							$('#employee_code').val('');
							Swal.fire
							({
								title: ""+res.msg+"",
								icon: "error",
								timer: 2000,
								showConfirmButton: false,
							});
						}
					} else {
						$('#employee_name').val('');
						$('#employee_code').val('');
						Swal.fire
						({
							title: ""+res.msg+"",
							icon: "error",
							timer: 2000,
							showConfirmButton: false,
						});
					}
				});
			}
    	}
	</script>
	@endif
@endsection

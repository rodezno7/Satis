@extends('layouts.app')
@section('title', __('order.edit_order'))
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('order.edit_order')</i></h1>
</section>

<!-- Main content -->
<section class="content">
    {{-- App business --}}
    <input type="hidden" id="app_business" value="{{ config('app.business') }}">

    {!! Form::open(['url' => action('OrderController@update', [$order->id]), 'method' => 'PUT',
        'id' => 'edit_order_form' ]) !!}
    <div class="row" style="margin-top: -0.5cm;">
        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12"></div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 pull-right">
            <p style="width: 2.5cm; margin-left: 1.5cm; color: black;"><b id="ref_no_2">{{ "# ".$order->quote_ref_no }}</b></p>
        </div>
    </div>
	<div class="box box-solid">
		<div class="box-body">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("search_quote", __("quote.search_quote")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-search"></i>
                            </span>
                            {!! Form::select("search_quote", [], null, ["class" => "form-control",
                                "id" => "search_quote", "placeholder" => __("quote.search_quote"),
                                "style" => "width: 100%;"]) !!}
                            {!! Form::hidden("quote_id", $order->id, ["id" => "quote_id"]) !!}
                            {!! Form::hidden("ref_no", $order->quote_ref_no, ["id" => "ref_no", "readonly"]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("customer_id", __("contact.customer")) !!}
                        <span style="color: red;">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user-circle"></i>
                            </span>
                            {!! Form::select('customer_id', [], null, ['class' => 'form-control',
                                'id' => 'customer_id', 'placeholder' => __('contact.customer'),
                                'style' => 'width: 100%;', "required"]) !!}
                            {!! Form::hidden("default_customer_id", $order->customer_id,
                                ["id" => "default_customer_id"]) !!}
                            {!! Form::hidden("default_customer_name", $order->customer_real_name,
                                ["id" => "default_customer_name"]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        {!! Form::label("customer_name", __("sale.customer_name")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user-circle"></i>
                            </span>
                            {!! Form::text("customer_name", $order->customer_name, ["class" => "form-control", "id" => "customer_name", "placeholder" => __("sale.customer_name")]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("quote_date", __("quote.quote_date")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text("order_date", @format_date($order->quote_date), ["class" => "form-control", "id" => "order_date", "readonly"]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("location", __("business.location")) !!}
                        <span style="color: red;">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                            {!! Form::select("location_id", $locations, $order->location_id, ["class" => "form-control select2", "required"]) !!}
                        </div>
                    </div>
                </div>

                <div class="@if (config('app.business') == 'workshop') col-sm-6 @else col-md-3 col-sm-6 @endif">
                    <div class="form-group">
                        {!! Form::label("contact_name", __("lang_v1.contact_name")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-phone"></i>
                            </span>
                            {!! Form::text("contact_name", $order->contact_name, ["class" => "form-control", "id" => "contact_name", "placeholder" => __("lang_v1.contact_name")]) !!}
                        </div>
                    </div>
                </div>

                {{-- customer_vehicle_id --}}
                @if (config('app.business') == 'workshop')
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        {!! Form::label(__("quote.customer_vehicle")) !!}

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-car"></i>
                            </span>

                            {!! Form::select("customer_vehicle_id", [], null, [
                                "class" => "form-control",
                                "id" => "customer_vehicle_id",
                                "placeholder" => __("messages.please_select"),
                                "style" => "width: 100;"
                            ]) !!}
                        </div>
                    </div>
                </div>
                @endif

                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("document_type_id", __("document_type.document")) !!}
                        <span style="color: red;">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-file-text-o"></i>
                            </span>
                            {!! Form::select("document_type_id", $documents, $order->document_type_id ? $order->document_type_id : null, ["class" => "form-control select2",
                                "id" => "document_type_id", "style" => "width: 100%", "placeholder" => __("document_type.document")]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("mobile", __("quote.mobile")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-mobile"></i>
                            </span>
                            {!! Form::text("mobile", $order->mobile, ["class" => "form-control", "id" => "mobile", "placeholder" => __("quote.mobile")]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("email", __("lang_v1.email_address")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-envelope"></i>
                            </span>
                            {!! Form::text("email", $order->email, ["class" => "form-control", "id" => "email", "placeholder" => __("lang_v1.email_address")]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("payment_condition", __("lang_v1.payment_condition")) !!}
                        <span style="color: red;">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-credit-card-alt"></i>
                            </span>
                            {!! Form::select("payment_condition", $payment_condition, $order->payment_condition ? $order->payment_condition : null,
                                ["class" => "form-control select2", "id" => "payment_condition",
                                'required', "placeholder" => __("lang_v1.payment_condition"), "style" => "width: 100%;"]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("validity", __("quote.validity")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </span>
                            {!! Form::text("validity", $order->validity, ["class" => "form-control", "id" => "validity", "placeholder" => __("quote.validity")]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("delivery_time", __("quote.delivery_time")) !!}
                        <div class="input-group">
                                 <span class="input-group-addon">
                                <i class="fa fa-calendar-times-o"></i>
                            </span>
                            {!! Form::text("delivery_time", $order->delivery_time, ["class" => "form-control", "id" => "delivery_time", "placeholder" => __("quote.delivery_time")]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("delivery_type", __("order.delivery_type")) !!}
                        <div class="input-group">
                                 <span class="input-group-addon">
                                <i class="fa fa-car"></i>
                            </span>
                            {!! Form::select("delivery_type", $delivery_types, $order->delivery_type, ["class" => "form-control select2",
                                "id" => "delivery_type", "style" => "width: 100%;", "placeholder" => __("order.delivery_type")]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6" style="display: none;"  id="other_delivery_type_div">
                    <div class="form-group">
                        {!! Form::label("other_delivery_type", __("order.other_delivery_type")) !!}
                        <div class="input-group">
                                 <span class="input-group-addon">
                                <i class="fa fa-exclamation"></i>
                            </span>
                            {!! Form::text("other_delivery_type", $order->other_delivery_type, ["class" => "form-control", "id" => "other_delivery_type",
                                "placeholder" => __("order.other_delivery_type")]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("delivery_date", __("order.delivery_date")) !!}
                        <div class="input-group">
                                 <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text("delivery_date", @format_date($order->delivery_date), ["class" => "form-control", "id" => "delivery_date", "placeholder" => __("order.delivery_date"), "readonly", "required"]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("tax_detail", __("quote.tax_detail")) !!}
                        <span style="color: red;">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-info"></i>
                            </span>
                            {!! Form::select("tax_detail", $tax_detail, $order->tax_detail ? "yes" : "no",
                                ["class" => "form-control select2", "id" => "tax_detail", 'required', "style" => "width: 100%;"]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("state", __("customer.state")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-globe"></i>
                            </span>
                            {!! Form::select("state_id", $states, $order->state_id, ["class" => "form-control select2",
                                "id" => "state_id", "style" => "width: 100%;", "placeholder" => __("customer.state")]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("city", __("customer.city")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-globe"></i>
                            </span>
                            {!! Form::select("city_id", $cities, $order->city_id, ["class" => "form-control select2",
                                "id" => "city_id", "style" => "width: 100%;", "placeholder" => __("customer.city")]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        {!! Form::label("address", __("lang_v1.address")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                            {!! Form::text("delivery_address", $order->address, ["class" => "form-control", "id" => "delivery_address", "placeholder" => __("lang_v1.address")]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        {!! Form::label("landmark", __("order.landmark")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-thumb-tack"></i>
                            </span>
                            {!! Form::text("landmark", $order->landmark, ["class" => "form-control",
                                "id" => "landmark", "placeholder" => __("order.landmark")]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        {!! Form::label(__("quote.seller_name")) !!}
                        <span style="color: red;">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user-circle"></i>
                            </span>
                            {!! Form::select("employee_id", $employees, $order->employee_id,
                                ["class" => "form-control select2", "id" => "employee_id", "required"]) !!}
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div> <!--box end-->
	<div class="box box-solid"><!--box start-->
		<div class="box-body">
            @if (config('app.business') == 'workshop')
            <div id="services" class="row">
                {{-- warehouse_id --}}
                <div class="col-sm-4">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-industry"></i>
                            </span>
    
                            {!! Form::select('warehouse_id', $warehouses, null, [
                                'class' => 'select2',
                                'id' => 'warehouse_id',
                                'placeholder' => __('quote.select_warehouse'),
                                'style' => 'width: 100%;'
                            ]) !!}
                        </div>
                    </div>
                </div>
    
                {{-- search_service --}}
                <div class="col-sm-8">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-search"></i>
                            </span>
    
                            {!! Form::select('search_service', [], null, [
                                'class' => 'form-control select2',
                                'id' => 'search_service',
                                'placeholder' => __('lang_v1.search_service_placeholder'),
                                'disabled'
                            ]) !!}
                        </div>
                    </div>
                </div>
            </div>
    
            <div id="service-blocks">
                {{-- @php
                    $row_index = 0;
                @endphp
    
                @foreach ($service_blocks as $service_block)
                    @include('order.partials.service_block', [
                        'service_block_index' => $loop->index,
                        'service_blocks' => $service_block
                    ])
                @endforeach --}}
    
                {{-- Products counter --}}
                {{-- <input type="hidden" id="row-index" value="{{ isset($row_index_count) ? $row_index_count : 0 }}"> --}}
            </div>

            {{-- Service blocks counter --}}
            <input type="hidden" id="service-block-index" value="{{ empty($service_blocks) ? 0 : count($service_blocks) + 1 }}">
            @else
            <div class="row">
                {{--<div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::select('selling_price_group_id', $selling_price_groups, $order->selling_price_group_id, ['class' => 'select2', 'id' => 'selling_price_group_id', 'placeholder' => __('lang_v1.selling_price_group'), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>--}}
                <div class="col-sm-2 col-sm-offset-1">
                    <div class="form-group">
                        {!! Form::select('warehouse_id', $warehouses, null, ['class' => 'select2', 'id' => 'warehouse_id', 'placeholder' => __('quote.select_warehouse'), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                <div class="col-sm-8">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-search"></i>
							</span>
                            {!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'order_search_product',
                                'placeholder' => __('lang_v1.search_product_placeholder'), "readonly"]); !!}
						</div>
					</div>
				</div>
            </div>
			<div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-condensed7 table-bordered text-center table-striped" id="order_table">
                            <thead style="background-color: #ccc;">
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th>@lang('quote.product_name')</th>
                                    <th>@lang('order.group_price') @show_tooltip(__('lang_v1.selling_price_group'))</th>
                                    <th style="width: 10%;">@lang('lang_v1.quantity')</th>
                                    <th style="width: 15%;">@lang('sale.unit_price')</th>
                                    <th style="width: 10%;">@lang('order.discount')</th>
                                    <th style="width: 15%;">@lang('quote.affected_sales')</th>
                                    <th style="width: 7%"><i class="fa fa-cogs"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($quote_lines as $ql)
                                @php
                                    $line_total_before_discount = 0;
                                    $quantity = $ql->quantity ? $ql->quantity : 1;
                                    $unit_price = 0;
                                    if($order->tax_detail){
                                        $line_total_before_discount = (double)($quantity * $ql->unit_price_exc_tax);
                                        $unit_price = (double)($ql->unit_price_exc_tax);
                                    } else{
                                        $line_total_before_discount = (double)($quantity * $ql->unit_price_inc_tax);
                                        $unit_price = (double)($ql->unit_price_inc_tax);
                                    }
                                    
                                    $discount_calculated_line_amount = 0;
                                    $discount_amount = round($ql->discount_amount, 4);
                                    if($ql->discount_type == "fixed"){
                                        $discount_calculated_line_amount = $discount_amount * $quantity;
                                    } else if($ql->discount_type == "percentage"){
                                        $discount_calculated_line_amount = ($unit_price * ($discount_amount / 100)) * $quantity;
                                    }

                                    $tax_amount = round(($ql->unit_price_exc_tax * $ql->tax_percent), 4);

                                    $line_total = ($line_total_before_discount - $discount_calculated_line_amount);
                                @endphp
                                <tr>
                                    <td>
                                        <span id='row_no'></span>
                                        <input type='hidden' id='quote_line_id' value='{{ $ql->id ? $ql->id : 0 }}'>
                                        <!-- Discount modal -->
                                        <div class="modal fade" id="discount_line_modal_{{ $ql->variation_id}}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-sm" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title">@lang('order.add_edit_discount')</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    {!! Form::label("discount_line_type", __("order.type")) !!}
                                                                    {!! Form::select("discount_line_type", $discount_types, $ql->discount_type ? $ql->discount_type : "fixed",
                                                                        ["class" => "form-control select2", "id" => "discount_line_type", "style" => "width: 100%;"]) !!}
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    {!! Form::label("discount_line_amount", __("order.amount")) !!}
                                                                    {!! Form::text("discount_line_amount", $ql->discount_amount ? number_format($ql->discount_amount, 2) : 0,
                                                                        ["class" => "form-control input_number", "id" => "discount_line_amount"]) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: left;">
                                        <span>{{ $ql->product_name }}</span>
                                        <input type='hidden' id='variation_id' value='{{ $ql->variation_id }}'>
                                        <input type='hidden' id='warehouse_id' value='{{ $ql->warehouse_id }}'>
                                    </td>
                                    <td>
                                        <select id="group_price" class="form-control input-sm group_price_line">
                                            <option value="{{ $ql->sell_price_inc_tax }}" {{ $ql->sell_price_inc_tax == $ql->unit_price_inc_tax ? "selected" : "" }} >@lang('order.none')</option>
                                            @foreach ($group_prices as $gp)
                                                @if ($gp['variation_id'] == $ql->variation_id)
                                                    <option value="{{ $gp['price_inc_tax'] }}" {{ $gp['price_inc_tax'] == $ql->unit_price_inc_tax ? "selected" : "" }}>{{ $gp['price_group'] }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input class='form-control input-sm input_number' type='text' id='quantity' value='{{ $ql->quantity ? $ql->quantity : 1 }}'>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control input-sm input_number unit_price_text"
                                            value="{{ $order->tax_detail ? round($ql->unit_price_exc_tax, 4) : round($ql->unit_price_inc_tax, 4) }}">
                                        <input type='hidden' id='unit_price_exc_tax' value='{{ round($ql->unit_price_exc_tax, 4) }}'>
                                        <input type='hidden' id='unit_price_inc_tax' value='{{ round($ql->unit_price_inc_tax, 4) }}'>
                                        <input type='hidden' id='tax_percent' value='{{ $ql->tax_percent }}'>
                                    </td>
                                    <td>
                                        <span id='discount_calculated_line_amount_text' class='display_currency' data-currency_symbol='true'>$
                                            {{ $discount_calculated_line_amount ? @num_format($discount_calculated_line_amount) : 0.00 }}
                                        </span>
                                        {!! Form::hidden("discount_calculated_line_amount", $discount_calculated_line_amount ? $discount_calculated_line_amount : 0,
                                                                        ["id" => "discount_calculated_line_amount"]) !!}
                                    </td>
                                    <td>
                                        <span id='line_total_text' class='display_currency' data-currency_symbol='true'>${{ $line_total }}</span>
                                        <input type='hidden' id='tax_line_amount' value='{{ $tax_amount ? $tax_amount : 0.00 }}'>
                                        <input type='hidden' id='line_total' value='{{ $line_total }}'>
                                    </td>
                                    <td>
                                        <button class='btn btn-xs' id="discount_row" title='{{ __("order.add_edit_discount") }}'><i class='fa fa-pencil' aria-hidden='true'></i></button>
                                        <button class='btn btn-xs' id="delete_row" title='{{ __("order.delete_row") }}'><i class='fa fa-times' aria-hidden='true'></i></button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
			</div>
            @endif
		</div>
    </div><!--box end-->
    <div class="box box-solid"><!--box start-->
		<div class="box-body">
			<div class="row">
                {{-- Start discount --}}
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("discount_type", __("purchase.discount_type")) !!}
                        {!! Form::select("discount_type", $discount_types, $order->discount_type ? $order->discount_type : "fixed",
                            ["class" => "form-control select2", "id" => "discount_type", "style" => "width: 100%;"]) !!}
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("discount_amount", __("purchase.discount_amount")) !!}
                        {!! Form::text("discount_amount", $order->discount_amount, ["class" => "form-control input_number",
                            "id" => "discount_amount", "placeholder" => __("purchase.discount_amount") ]) !!}
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        <b>@lang('purchase.discount')</b>(-)
                        <span id="discount_calculated_amount_text" class="display_currency" data-currency_symbol='true'>0.00</span>
                        <input type="hidden" name="discount_calculated_amount" id="discount_calculated_amount">
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <b>@lang('tax_rate.tax_amount'): </b>(+)
                        <span id="tax_amount_text" data-currency_symbol="true" class="display_currency">0.00</span>
                        <input type="hidden" name="tax_amount" id="tax_amount" value="{{ $order->tax_amount }}">
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="input-group">
                        <b>@lang('lang_v1.sub_total')</b>
                        <span id="subtotal_text" class="display_currency" data-currency_symbol='true'>{{ @num_format($order->total_before_tax) }}</span>
                        <input type="hidden" name="subtotal" id="subtotal" value="{{ $order->total_before_tax }}">
                    </div>
                    <div class="input-group" style="margin-top: 15px;">
                        <b>@lang('quote.total_final')</b>
                        <span id="total_final_text" class="display_currency" data-currency_symbol='true'>{{ @num_format($order->total_final) }}</span>
                        <input type="hidden" name="total_final" id="total_final" value="{{ $order->total_final }}">
                    </div>
                </div>
            </div>
		</div>
    </div>
    <div class="box box-solid"><!--box start-->
		<div class="box-body">
			<div class="row">
                <div class="col-md-6 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("terms_conditions", __("quote.terms_conditions")) !!}
                        {!! Form::textarea("terms_conditions", $order->terms_conditions, ["class" => "form-control", "id" => "terms_conditions", "rows" => "2"]) !!}
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("note", __("quote.notes")) !!}
                        {!! Form::textarea("note", $order->note, ["class" => "form-control", "id" => "note", "rows" => "2"]) !!}
                    </div>
				</div>
			</div>
		</div>
	</div>
	<div class="box box-solid"><!--box start-->
		<div class="box-body">
			<div class="row">
				<div class="col-sm-12" style="text-align: right;">
                    <button type="submit" class="btn btn-primary">@lang('messages.update')</button>
                    <button type="button" class="btn btn-danger" id="go_back">@lang('messages.cancel')</button>
				</div>
			</div>
		</div>
	</div>
    {!! Form::close() !!}
</section>

@endsection
@section('javascript')
@php $asset_v = env('APP_VERSION'); @endphp
    <script src="{{ asset('js/order.js?v=' . $asset_v) }}"></script>
@endsection
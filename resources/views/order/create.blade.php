@extends('layouts.app')

@section('title', __('order.add_order'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('order.add_order')</i></h1>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open([
        'url' => action('OrderController@store'),
        'method' => 'post',
        'id' => 'add_order_form'
    ]) !!}

    <div class="row" style="margin-top: -0.5cm;">
        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12"></div>

        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 pull-right">
            <p style="width: 2.5cm; margin-left: 1.5cm; color: black;">
                <b id="ref_no_2"></b>
            </p>
        </div>
    </div>

    {{-- App business --}}
    <input type="hidden" id="app_business" value="{{ config('app.business') }}">

	<div class="box box-solid">
		<div class="box-body">
            <div class="row">
                {{-- search_quote --}}
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("search_quote", __("quote.search_quote")) !!}

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-search"></i>
                            </span>

                            {!! Form::select("search_quote", [], null, [
                                "class" => "form-control",
                                "id" => "search_quote",
                                "placeholder" => __("quote.search_quote"),
                                "style" => "width: 100%;"
                            ]) !!}

                            {{-- quote_id --}}
                            {!! Form::hidden("quote_id", 0, ["id" => "quote_id"]) !!}

                            {{-- ref_no --}}
                            {!! Form::hidden("ref_no", null, ["id" => "ref_no", "readonly"]) !!}
                        </div>
                    </div>
                </div>

                {{-- customer_id --}}
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("customer_id", __("contact.customer")) !!}
                        <span style="color: red;">*</span>
                        
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user-circle"></i>
                            </span>
                            {!! Form::select('customer_id', [], null, [
                                'class' => 'form-control',
                                'id' => 'customer_id',
                                'placeholder' => __('contact.customer'),
                                'style' => 'width: 100%;',
                                'required'
                            ]); !!}
                        </div>
                    </div>
                </div>

                {{-- customer_name --}}
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        {!! Form::label("customer_name", __("sale.customer_name")) !!}

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user-circle"></i>
                            </span>

                            {!! Form::text("customer_name", null, [
                                "class" => "form-control",
                                "id" => "customer_name",
                                "placeholder" => __("sale.customer_name")
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- quote_date --}}
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("quote_date", __("quote.quote_date")) !!}

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            
                            {!! Form::text("order_date", @format_date('now'),
                                ["class" => "form-control", "id" => "order_date", "readonly"]) !!}
                        </div>
                    </div>
                </div>

                {{-- location --}}
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("location", __("business.location")) !!}
                        <span style="color: red;">*</span>

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>

                            {!! Form::select("location_id", $locations, null,
                                ["class" => "form-control select2", "required"]) !!}
                        </div>
                    </div>
                </div>

                {{-- contact_name --}}
                <div class="@if (config('app.business') == 'workshop') col-sm-6 @else col-md-3 col-sm-6 @endif">
                    <div class="form-group">
                        {!! Form::label("contact_name", __("lang_v1.contact_name")) !!}

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-phone"></i>
                            </span>

                            {!! Form::text("contact_name", null, [
                                "class" => "form-control",
                                "id" => "contact_name",
                                "placeholder" => __("lang_v1.contact_name")
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- customer_vehicle_id --}}
                @if (config('app.business') == 'workshop')
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label(__("quote.customer_vehicle")) !!}

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-car"></i>
                            </span>

                            {!! Form::select("customer_vehicle_id", [], null, [
                                "class" => "form-control select2",
                                "id" => "customer_vehicle_id",
                                "placeholder" => __("messages.please_select"),
                                "style" => "width: 100;",
                            ]) !!}
                        </div>
                    </div>
                </div>
                @endif

                {{-- document_type_id --}}
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("document_type_id", __("document_type.document")) !!}
                        <span style="color: red;">*</span>

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-file-text-o"></i>
                            </span>

                            {!! Form::select("document_type_id", $documents, null, [
                                "class" => "form-control select2",
                                "id" => "document_type_id",
                                "style" => "width: 100%",
                                "placeholder" => __("document_type.document"),
                                "required"
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- mobile --}}
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("mobile", __("quote.mobile")) !!}

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-mobile"></i>
                            </span>

                            {!! Form::text("mobile", null, [
                                "class" => "form-control",
                                "id" => "mobile",
                                "placeholder" => __("quote.mobile")
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- email --}}
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("email", __("lang_v1.email_address")) !!}

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-envelope"></i>
                            </span>

                            {!! Form::text("email", null, [
                                "class" => "form-control",
                                "id" => "email",
                                "placeholder" => __("lang_v1.email_address")
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- payment_condition --}}
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("payment_condition", __("lang_v1.payment_condition")) !!}
                        <span style="color: red;">*</span>

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-credit-card-alt"></i>
                            </span>

                            {!! Form::select("payment_condition", $payment_condition, null, [
                                "class" => "form-control select2",
                                "id" => "payment_condition",
                                "required",
                                "placeholder" => __("lang_v1.payment_condition"),
                                "style" => "width: 100%;"
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- validity --}}
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("validity", __("quote.validity")) !!}
                        
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </span>

                            {!! Form::text("validity", null, [
                                "class" => "form-control",
                                "id" => "validity",
                                "placeholder" => __("quote.validity")
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- delivery_time --}}
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("delivery_time", __("quote.delivery_time")) !!}

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar-o"></i>
                            </span>

                            {!! Form::text("delivery_time", null, [
                                "class" => "form-control",
                                "id" => "delivery_time",
                                "placeholder" => __("quote.delivery_time")
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- delivery_type --}}
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("delivery_type", __("order.delivery_type")) !!}

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-car"></i>
                            </span>

                            {!! Form::select("delivery_type", $delivery_types, null, [
                                "class" => "form-control select2",
                                "id" => "delivery_type",
                                "style" => "width: 100%;",
                                "placeholder" => __("order.delivery_type")
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- other_delivery_type --}}
                <div class="col-md-3 col-sm-6" style="display: none;"  id="other_delivery_type_div">
                    <div class="form-group">
                        {!! Form::label("other_delivery_type", __("order.other_delivery_type")) !!}

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-exclamation"></i>
                            </span>

                            {!! Form::text("other_delivery_type", null, [
                                "class" => "form-control",
                                "id" => "other_delivery_type",
                                "placeholder" => __("order.other_delivery_type")
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- delivery_date --}}
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("delivery_date", __("order.delivery_date")) !!}

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>

                            {!! Form::text("delivery_date", @format_date('now'), [
                                "class" => "form-control",
                                "id" => "delivery_date",
                                "placeholder" => __("order.delivery_date"),
                                "readonly"
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- tax_detail --}}
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("tax_detail", __("quote.tax_detail")) !!}
                        <span style="color: red;">*</span>

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-info"></i>
                            </span>

                            {!! Form::select("tax_detail", $tax_detail, "no", [
                                "class" => "form-control select2",
                                "id" => "tax_detail",
                                "required",
                                "style" => "width: 100%;"
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- state --}}
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("state", __("customer.state")) !!}

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-globe"></i>
                            </span>

                            {!! Form::select("state_id", $states, null, [
                                "class" => "form-control select2",
                                "id" => "state_id",
                                "style" => "width: 100%;",
                                "placeholder" => __("customer.state")
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- city --}}
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("city", __("customer.city")) !!}

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-globe"></i>
                            </span>

                            {!! Form::select("city_id", [], null, [
                                "class" => "form-control select2",
                                "id" => "city_id",
                                "style" => "width: 100%;",
                                "placeholder" => __("customer.city")
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- delivery_address --}}
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        {!! Form::label("delivery_address", __("order.delivery_address")) !!}

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>

                            {!! Form::text("delivery_address", null, [
                                "class" => "form-control",
                                "id" => "delivery_address",
                                "placeholder" => __("order.delivery_address")
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- landmark --}}
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        {!! Form::label("landmark", __("order.landmark")) !!}

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-thumb-tack"></i>
                            </span>

                            {!! Form::text("landmark", null, [
                                "class" => "form-control",
                                "id" => "landmark",
                                "placeholder" => __("order.landmark")
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- employee_id --}}
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        {!! Form::label(__("quote.seller_name")) !!}
                        <span style="color: red;">*</span>

                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user-circle"></i>
                            </span>

                            {!! Form::select("employee_id", $employees, null, [
                                "class" => "form-control select2",
                                "id" => "employee_id",
                                "required"
                            ]) !!}
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div> <!--box end-->

	<div class="box box-solid"><!--box start-->
		<div class="box-body">
            @if (config('app.business') == 'workshop')
            <div class="row">
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
                            ]); !!}
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
                @php
                    $row_index = 0;
                @endphp

                @foreach ($service_blocks as $service_block)
                    @include('order.partials.service_block', [
                        'service_block_index' => $loop->index,
                        'service_blocks' => $service_block
                    ])
                @endforeach
    
                {{-- Products counter --}}
                {{-- <input type="hidden" id="row-index" value="{{ $row_index > 0 ? $row_index + 1 : 0 }}"> --}}
            </div>

            {{-- Service blocks counter --}}
            <input type="hidden" id="service-block-index" value="{{ empty($service_blocks) ? 0 : count($service_blocks) + 1 }}">
            @else
            <div class="row">
                {{--<div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::select('selling_price_group_id', $selling_price_groups, null, ['class' => 'select2', 'id' => 'selling_price_group_id', 'placeholder' => __('lang_v1.selling_price_group'), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>--}}

                {{-- warehouse_id --}}
                <div class="col-sm-2 col-sm-offset-1">
                    <div class="form-group">
                        {!! Form::select('warehouse_id', $warehouses, null, [
                            'class' => 'select2',
                            'id' => 'warehouse_id',
                            'placeholder' => __('quote.select_warehouse'), 
                            'style' => 'width: 100%;'
                        ]); !!}
                    </div>
                </div>

                {{-- search_product --}}
                <div class="col-sm-8">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-search"></i>
							</span>

                            {!! Form::text('search_product', null, [
                                'class' => 'form-control mousetrap',
                                'id' => 'order_search_product',
                                'placeholder' => __('lang_v1.search_product_placeholder'),
                                'readonly'
                            ]); !!}
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
                            <tbody></tbody>
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
                {{-- discount_type --}}
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("discount_type", __("purchase.discount_type")) !!}

                        {!! Form::select("discount_type", $discount_types, "fixed", [
                            "class" => "form-control select2",
                            "id" => "discount_type",
                            "style" => "width: 100%;"
                        ]) !!}
                    </div>
                </div>

                {{-- discount_amount --}}
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("discount_amount", __("purchase.discount_amount")) !!}

                        {!! Form::text("discount_amount", null, [
                            "class" => "form-control input_number",
                            "id" => "discount_amount",
                            "placeholder" => __("purchase.discount_amount")
                        ]) !!}
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    {{-- discount_calculated_amount --}}
                    <div class="form-group">
                        <b>@lang('purchase.discount')</b>(-)
                        <span id="discount_calculated_amount_text" class="display_currency" data-currency_symbol='true'>0.00</span>
                        <input type="hidden" name="discount_calculated_amount" id="discount_calculated_amount">
                    </div>

                    {{-- tax_amount --}}
                    <div class="form-group" style="margin-top: 15px;">
                        <b>@lang('tax_rate.tax_amount'): </b>(+)
                        <span id="tax_amount_text" data-currency_symbol="true" class="display_currency">0.00</span>
                        <input type="hidden" name="tax_amount" id="tax_amount">
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    {{-- subtotal --}}
                    <div class="input-group">
                        <b>@lang('lang_v1.sub_total')</b>
                        <span id="subtotal_text" class="display_currency" data-currency_symbol='true'>0.00</span>
                        <input type="hidden" name="subtotal" id="subtotal">
                    </div>

                    {{-- total_final --}}
                    <div class="input-group" style="margin-top: 15px;">
                        <b>@lang('quote.total_final')</b>
                        <span id="total_final_text" class="display_currency" data-currency_symbol='true'>0.00</span>
                        <input type="hidden" name="total_final" id="total_final">
                    </div>
                </div>
            </div>
		</div>
    </div>

    <div class="box box-solid"><!--box start-->
		<div class="box-body">
			<div class="row">
                {{-- terms_conditions --}}
                <div class="col-md-6 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("terms_conditions", __("quote.terms_conditions")) !!}
                        {!! Form::textarea("terms_conditions", null,
                            ["class" => "form-control", "id" => "terms_conditions", "rows" => "2"]) !!}
                    </div>
                </div>

                {{-- note --}}
                <div class="col-md-6 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("note", __("quote.notes")) !!}
                        {!! Form::textarea("note", null,
                            ["class" => "form-control", "id" => "note", "rows" => "2"]) !!}
                    </div>
				</div>
			</div>
		</div>
	</div>

    {{-- buttons --}}
	<div class="box box-solid"><!--box start-->
		<div class="box-body">
			<div class="row">
				<div class="col-sm-12" style="text-align: right;">
                    <button type="submit" class="btn btn-primary" id="btn_submit">@lang('messages.save')</button>

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
{!! Form::open(['id' => 'edit_quote_form']) !!}

<!--box start-->
<div class="row" style="margin-top: -0.5cm;">
    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12"></div>

    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 pull-right">
        <p style="width: 2.5cm; margin-left: 1.5cm; color: black;">
            <b>{{ "# ". $quote->quote_ref_no}}</b>
        </p>
    </div>
</div>

<div class="box box-solid">
    <div class="box-body">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <h4>@lang('quote.edit_quote')</h4>
            </div>

            {{-- search_customer --}}
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    {!! Form::label(__("quote.search_customer")) !!}
                    <span style="color: red;">*</span>

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-search"></i>
                        </span>

                        <select name="search_customer" id="search_customer" class="form-control mousetrap" style="width: 100%;">
                            <option value="0" disabled>@lang('messages.please_select')</option>

                            @foreach ($customers as $item)
                                @if ($quote->customer_id == $item->id)
                                    <option
                                        value="{{ $item->id }}"
                                        data-address="{{ $item->address }}"
                                        data-name="{{ $item->name }}"
                                        data-default="{{ $item->is_default }}"
                                        data-email="{{ $item->email }}"
                                        data-mobile="{{ $item->telphone }}"
                                        data-employee="{{ $item->employee_id }}"
                                        selected>
                                        {{ $item->name }}
                                    </option>
                                @else
                                    <option
                                        value="{{ $item->id }}"
                                        data-address="{{ $item->address }}"
                                        data-name="{{ $item->name }}"
                                        data-default="{{ $item->is_default }}"
                                        data-email="{{ $item->email }}"
                                        data-mobile="{{ $item->telphone }}"
                                        data-employee="{{ $item->employee_id }}">
                                        {{ $item->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>

                        {{-- _token --}}
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">

                        {{-- quote_id --}}
                        <input type="hidden" name="quote_id" value="{{ $quote->id }}" id="quote_id">
                    </div>
                </div>
            </div>

            {{-- customer_name --}}
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    {!! Form::label(__("sale.customer_name")) !!}

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user-circle"></i>
                        </span>

                        {!! Form::text("customer_name", $quote->customer_name, [
                            "class" => "form-control",
                            "id" => "customer_name",
                            "placeholder" => __("sale.customer_name")
                        ]) !!}

                        {{-- customer_id --}}
                        <input type="hidden" name="customer_id" id="customer_id" value="{{ $quote->customer_id }}">
                    </div>
                </div>
            </div>

            {{-- quote_date --}}
            <div class="col-md-2 col-sm-6">
                <div class="form-group">
                    <label>@lang('quote.quote_date')</label>

                    <div class="wrap-inputform">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar-o"></i>
                        </span>

                        <input
                            type="text"
                            id="_date"
                            name="quote_date"
                            class="form-control"
                            value="{{ @format_date($quote->quote_date) }}"
                            readonly>
                    </div>
                </div>
            </div>

            {{-- contact_name --}}
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    {!! Form::label(__("lang_v1.contact_name")) !!}

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-phone"></i>
                        </span>

                        {!! Form::text("contact_name", $quote->contact_name, [
                            "class" => "form-control",
                            "id" => "contact_name",
                            "placeholder" => __("lang_v1.contact_name")
                        ]) !!}
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

                        {!! Form::select("customer_vehicle_id", $customer_vehicles, $quote->customer_vehicle_id, [
                            "class" => "form-control",
                            "id" => "customer_vehicle_id",
                            "placeholder" => __("messages.please_select"),
                            "style" => "width: 100;",
                            "required"
                        ]) !!}
                    </div>
                </div>
            </div>
            @endif

            {{-- document_type_id --}}
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    {!! Form::label(__("document_type.document")) !!}
                    <span style="color: red;">*</span>

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-file-text-o"></i>
                        </span>

                        {!! Form::select("document_type_id", $documents, $quote->document_type_id, [
                            "class" => "form-control",
                            "id" => "document_type_id",
                            "style" => "width: 100%",
                            "placeholder" => __("document_type.document")
                        ]) !!}
                    </div>
                </div>
            </div>

            {{-- mobile --}}
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    {!! Form::label(__("quote.mobile")) !!}

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-mobile"></i>
                        </span>

                        {!! Form::text("mobile", $quote->mobile, [
                            "class" => "form-control",
                            "id" => "mobile",
                            "placeholder" => __("quote.mobile")
                        ]) !!}
                    </div>
                </div>
            </div>

            {{-- address --}}
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    {!! Form::label(__("lang_v1.address")) !!}

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-map-marker"></i>
                        </span>

                        {!! Form::text("address", $quote->address, [
                            "class" => "form-control",
                            "id" => "address",
                            "placeholder" => __("lang_v1.address")
                        ]) !!}
                    </div>
                </div>
            </div>

            {{-- email --}}
            <div class="col-md-6 col-sm-6">
                <div class="form-group">
                    {!! Form::label(__("lang_v1.email_address")) !!}

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-envelope"></i>
                        </span>

                        {!! Form::text("email", $quote->email, [
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
                    {!! Form::label(__("lang_v1.payment_condition")) !!}
                    <span style="color: red;">*</span>

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-credit-card-alt"></i>
                        </span>

                        {!! Form::select("payment_condition", $payment_condition, $quote->payment_condition, [
                            "class" => "form-control",
                            "id" => "payment_condition",
                            "placeholder" => __("lang_v1.payment_condition"),
                            "style" => "width: 100%;"
                        ]) !!}
                    </div>
                </div>
            </div>

            {{-- validity --}}
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    {!! Form::label(__("quote.validity")) !!}

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                        </span>

                        {!! Form::text("validity", $quote->validity, [
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
                    {!! Form::label(__("quote.delivery_time")) !!}

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar-times-o"></i>
                        </span>

                        {!! Form::text("delivery_time", $quote->delivery_time, [
                            "class" => "form-control",
                            "id" => "delivery_time",
                            "placeholder" => __("quote.delivery_time")
                        ]) !!}
                    </div>
                </div>
            </div>

            {{-- tax_detail --}}
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    {!! Form::label(__("quote.tax_detail")) !!}
                    <span style="color: red;">*</span>

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-info"></i>
                        </span>

                        {!! Form::select("tax_detail", $tax_detail, $quote->tax_detail, [
                            "class" => "form-control",
                            "id" => "tax_detail"
                        ]) !!}
                    </div>
                </div>
            </div>

            {{-- price_group_id --}}
            <div class="col-lg-6 col-md-6 col-sm-3 col-xs-12">
                <div class="form-group">
                    <label>@lang('customer.price_group')</label>

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-user"></i>
                        </span>

                        {!! Form::select('price_group_id', $prices_group, $quote->selling_price_group_id, [
                            'class' => 'select2 form-control',
                            'id' => 'selling_price_group_id'
                        ]) !!}
                    </div>
                </div>
            </div>

            {{-- employee_id --}}
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    {!! Form::label(__("quote.seller_name")) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user-circle"></i>
                        </span>
                        
                        {!! Form::select("employee_id", $employees, $quote->employee_id,
                            ["class" => "form-control", "id" => "employee_id"]) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--box end-->

<div class="box box-solid">
    <!--box start-->
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
        </div>

        {{-- Service blocks counter --}}
        <input type="hidden" id="service-block-index" value="0">

        {{-- Products counter --}}
        <input type="hidden" id="row-index" value="0">
        @else
        <div class="row">
            {{-- warehouse_id --}}
            <div class="col-lg-3 col-md-3 col-sm-3 cols-xs-12">
                <div class="form-group">
                    {!! Form::select('warehouse_id', $warehouses, null, [
                        'class' => 'select2',
                        'id' => 'warehouse_id',
                        'placeholder' => __('quote.select_warehouse'),
                        'style' => 'width: 100%;']); !!}
                </div>
            </div>

            {{-- search_product --}}
            <div class="col-lg-9 col-md-9 col-sm-9 cols-xs-12">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-search"></i>
                        </span>
                        {!! Form::text('search_product', null, [
                            'class' => 'form-control mousetrap',
                            'id' => 'search_product',
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
                    <table class="table table-condensed table-bordered text-center table-striped" id="quote_table">
                        <thead>
                            <tr>
                                <th><i class="fa fa-trash"></i></th>
                                <th>@lang('quote.product_name')</th>
                                <th>@lang('lang_v1.quantity')</th>
                                <th>@lang('quote.unit_price')</th>
                                <th>@lang('quote.discount_type')</th>
                                <th>@lang('quote.discount')</th>
                                <th>@lang('quote.subtotal')</th>
                            </tr>
                        </thead>
                        <tbody id="list">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- discount_type --}}
        <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <div class="form-group">
                    <label>@lang('quote.discount_type')</label>

                    <select name="discount_type" id="discount_type" class="select2" style="width: 100%;">
                        @if ($quote->discount_type == "fixed")
                            <option value="fixed" selected>@lang('quote.fixed')</option>
                            <option value="percentage">@lang('quote.percentage')</option>
                        @else
                            <option value="fixed">@lang('quote.fixed')</option>
                            <option value="percentage" selected="">@lang('quote.percentage')</option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
            </div>
        </div>

        {{-- sums --}}
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <label>@lang('quote.sums')</label>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <div class="form-group">
                    {!! Form::hidden("sums", null, ["class" => "form-control four_decimals", "id" => "sums", 'readonly']) !!}
                    {!! Form::text("sums_s", null, ["class" => "form-control four_decimals", "id" => "sums_s", 'readonly']) !!}
                </div>
            </div>
        </div>

        {{-- discount_amount --}}
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <label>@lang('quote.discount')</label>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <div class="form-group">
                    {!! Form::hidden("discount_amount", $quote->discount_amount, [
                        "class" => "form-control four_decimals",
                        "id" => "discount_amount"
                    ]) !!}

                    {!! Form::text("discount_amount_s", number_format($quote->discount_amount, 2), [
                        "class" => "form-control four_decimals",
                        "id" => "discount_amount_s"
                    ]) !!}
                </div>
            </div>
        </div>

        {{-- total_before_tax --}}
        @if ($quote->tax_detail == 1)
        <div class="row" id="div_subtotal">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <label>@lang('quote.subtotal')</label>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <div class="form-group">
                    {!! Form::hidden("total_before_tax", null, [
                        "class" => "form-control four_decimals",
                        "id" => "total_before_tax",
                        'readonly'
                    ]) !!}

                    {!! Form::text("total_before_tax_s", null, [
                        "class" => "form-control four_decimals",
                        "id" => "total_before_tax_s",
                        'readonly'
                    ]) !!}
                </div>
            </div>            
        </div>
        @else
        <div class="row" style="display: none" id="div_subtotal">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <label>@lang('quote.subtotal')</label>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <div class="form-group">
                    {!! Form::hidden("total_before_tax", null, [
                        "class" => "form-control four_decimals",
                        "id" => "total_before_tax", 'readonly'
                    ]) !!}

                    {!! Form::text("total_before_tax_s", null, [
                        "class" => "form-control four_decimals",
                        "id" => "total_before_tax_s", 'readonly'
                    ]) !!}
                </div>
            </div>            
        </div>
        @endif

        {{-- tax_amount --}}
        @if ($quote->tax_detail == 1)
        <div class="row" id="div_tax">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <label>@lang('quote.iva')</label>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <div class="form-group">
                    {!! Form::hidden("tax_amount", null, [
                        "class" => "form-control four_decimals",
                        "id" => "tax_amount",
                        'readonly'
                    ]) !!}

                    {!! Form::text("tax_amount_s", null, [
                        "class" => "form-control four_decimals",
                        "id" => "tax_amount_s",
                        'readonly'
                    ]) !!}
                </div>
            </div>
        </div>
        @else
        <div class="row" style="display: none" id="div_tax">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <label>@lang('quote.iva')</label>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <div class="form-group">
                    {!! Form::hidden("tax_amount", null, [
                        "class" => "form-control four_decimals",
                        "id" => "tax_amount",
                        'readonly'
                    ]) !!}

                    {!! Form::text("tax_amount_s", null, [
                        "class" => "form-control four_decimals",
                        "id" => "tax_amount_s",
                        'readonly'
                    ]) !!}
                </div>
            </div>
        </div>
        @endif

        {{-- total_final --}}
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <label>@lang('quote.total')</label>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <div class="form-group">
                    <div class="input-group">
                        {!! Form::hidden("total_final", null, [
                            "class" => "form-control four_decimals",
                            "id" => "total_final",
                            'readonly'
                        ]) !!}

                        {!! Form::text("total_final_s", null, [
                            "class" => "form-control four_decimals",
                            "id" => "total_final_s",
                            'readonly'
                        ]) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- note --}}
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label>@lang('quote.notes')</label></span>
                    {!! Form::textarea('note', $quote->note, ['class' => 'form-control', 'rows' => 1, 'id' => 'note']) !!}
                </div>
            </div>

            {{-- terms_conditions --}}
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label>@lang('quote.terms_conditions')</label></span>
                    {!! Form::textarea('terms_conditions', $quote->terms_conditions, [
                        'class' => 'form-control',
                        'rows' => 1,
                        'id' => 'terms_conditions'
                    ]) !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <button type="button" id="btn-cancel-add-quote" class="btn btn-danger pull-right">
                    @lang('messages.cancel')
                </button>

                <span class="pull-right"> &nbsp; </span>
                <span class="pull-right"> &nbsp; </span>

                <button type="submit" class="btn btn-primary pull-right" id="btn-edit-quote">
                    @lang('messages.save')
                </button>
            </div>
        </div>
    </div>
</div>

{!! Form::close() !!}
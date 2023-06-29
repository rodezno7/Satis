@extends('layouts.app')
@section('title', __('purchase.add_purchase'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('purchase.add_purchase') <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true"
                data-container="body" data-toggle="popover" data-placement="bottom"
                data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true"
                data-trigger="hover" data-original-title="" title=""></i></h1>
        <!-- <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                    <li class="active">Here</li>
                </ol> -->
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Page level currency setting -->
        <input type="hidden" id="p_code" value="{{ $currency_details->code }}">
        <input type="hidden" id="p_symbol" value="{{ $currency_details->symbol }}">
        <input type="hidden" id="p_thousand" value="{{ $currency_details->thousand_separator }}">
        <input type="hidden" id="p_decimal" value="{{ $currency_details->decimal_separator }}">

        {{-- Number of decimal places seen in the interface --}}
        <input type="hidden" id="decimals_in_purchases" value="{{ $decimals_in_purchases }}">

        {{-- Number of decimal places to store and use in calculations --}}
        <input type="hidden" id="price_precision" value="{{ config('app.price_precision') }}">

        @include('layouts.partials.error')

        {!! Form::open(['url' => action('PurchaseController@store'), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true]) !!}

        {{-- Purchase type --}}
        {!! Form::hidden('purchase_type', $purchase_type, ['id' => 'purchase_type']) !!}

        {{-- Expense type --}}
        {!! Form::hidden('expense_type', 'purchase', ['id' => 'expense_type']) !!}

        {{-- Total unit cost --}}
        <input type="hidden" id="purchase_total_unit_cost" value="0">

        {{-- Total weight --}}
        <input type="hidden" id="purchase_total_weight" value="0">

        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    {{-- Supplier data --}}
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('supplier_id', __('purchase.supplier') . ': ') !!} <span style="color: red">*</span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user-circle"></i>
                                </span>
                                {!! Form::select('contact_id', [], null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'supplier_id']) !!}
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default bg-white btn-flat add_new_supplier"
                                        data-name="" disabled><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                </span>
                                {!! Form::hidden("contact_tax_id", null, ["id" => "contact_tax_id"]) !!}
                                <input type="hidden" id="tax_min_amount">
                                <input type="hidden" id="tax_max_amount">
                                <input type="hidden" id="perception_percent">
                                <input type="hidden" id="flag">
                            </div>
                        </div>
                    </div>

                    {{-- Supplier name --}}
                    <div class="col-md-6 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('supplier_name', __('purchase.supplier_name') . ': ') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user-secret"></i>
                                </span>
                                {!! Form::text('supplier_name', null, ['class' => 'form-control', 'readonly', 'id' => 'supplier_name']) !!}
                            </div>
                        </div>
                    </div>

                    {{-- Date --}}
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('transaction_date', __('purchase.purchase_date') . ': ') !!} <span style="color: red">*</span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::text('transaction_date', @format_date('now'), ['class' => 'form-control', 'readonly', 'required']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Document date --}}
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('document_date', __('retention.document_date') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::text('document_date', @format_date('now'), ['class' => 'form-control', 'readonly']) !!}
                            </div>
                        </div>
                    </div>

                    {{-- Document type --}}
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('document_type_id', __('document_type.document') . ': ') !!} <span style="color: red">*</span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-file-text-o"></i>
                                </span>
                                {!! Form::select('document_types_id', $document_types, null, ['class' => 'form-control select2', 'id' => 'document_type_id', 'required']) !!}
                            </div>
                        </div>
                    </div>

                    {{-- Reference no. --}}
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('ref_no', __('purchase.ref_no') . ': ') !!} <span style="color: red">*</span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-hashtag"></i>
                                </span>
                                {!! Form::text('ref_no', null, ['class' => 'form-control', 'id' => 'ref_no', "placeholder" => __('purchase.ref_no')]) !!}
                            </div>
                        </div>
                    </div>

                    {{-- Serie --}}
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('serie', __('accounting.serie') . ': ') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-hashtag"></i>
                                </span>
                                {!! Form::text('serie', null, ['class' => 'form-control', 'id' => 'serie', "placeholder" => __('accounting.serie')]) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Payment condition --}}
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('payment_condition', __('lang_v1.payment_condition') . ': ') !!} <span style="color: red">*</span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-credit-card-alt"></i>
                                </span>
                                {!! Form::select('payment_condition', $payment_condition, null, ['class' => 'form-control select2', 'id' => 'payment_condition', 'required', 'placeholder' => __('lang_v1.payment_condition'), 'style' => 'width: 100%;']) !!}
                            </div>
                        </div>
                    </div>

                    {{-- Payment term --}}
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('payment_terms', __('purchase.credit_terms') . ': ') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-list-ol"></i>
                                </span>
                                {!! Form::select('payment_term_id', $payment_terms, null, ['class' => 'form-control select2', 'id' => 'payment_terms', 'disabled']) !!}
                            </div>
                        </div>
                    </div>

                    {{-- Purchase status --}}
                    <div class="col-md-3 col-sm-6 @if (!empty($default_purchase_status)) hide @endif">
                        <div class="form-group">
                            {!! Form::label('status', __('purchase.purchase_status') . ': ') !!} <span style="color: red">*</span>
                            @show_tooltip(__('tooltip.order_status'))
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-hourglass-start"></i>
                                </span>
                                {!! Form::select('status', $orderStatuses, $default_purchase_status, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']) !!}
                            </div>
                        </div>
                    </div>

                    {{-- <div class="clearfix"></div> --}}

                    @if (count($business_locations) == 1)
                        @php
                            $default_location = current(array_keys($business_locations->toArray()));
                        @endphp
                    @else
                        @php $default_location = null; @endphp
                    @endif

                    {{-- Location --}}
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('location_id', __('purchase.business_location') . ': ') !!} <span style="color: red">*</span>
                            @show_tooltip(__('tooltip.purchase_location'))
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-building"></i>
                                </span>
                                {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'location_id']) !!}
                            </div>
                        </div>
                    </div>

                    @if (count($warehouses) == 1)
                        @php
                            $default_warehouse = current(array_keys($warehouses->toArray()));
                        @endphp
                    @else
                        @php
                            $default_warehouse = null;
                        @endphp
                    @endif

                    <div class="clearfix"></div>

                    {{-- Warehouse --}}
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('warehouse_id', __('warehouse.warehouse') . ': ') !!} <span style="color: red">*</span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-industry"></i>
                                </span>
                                {!! Form::select('warehouse_id', [], null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'disabled', 'id' => 'warehouse_id']) !!}
                            </div>
                        </div>
                    </div>

                    {{-- Currency exchange rate --}}
                    <div class="col-md-3 col-sm-6 @if (!$currency_details->purchase_in_diff_currency) hide @endif">
                        <div class="form-group">
                            {!! Form::label('exchange_rate', __('purchase.p_exchange_rate') . ':*') !!}
                            @show_tooltip(__('tooltip.currency_exchange_factor'))
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-info"></i>
                                </span>
                                {!! Form::number('exchange_rate', $currency_details->p_exchange_rate, ['class' => 'form-control', 'required', 'step' => 0.001]) !!}
                            </div>
                            <span class="help-block text-danger">
                                @lang('purchase.diff_purchase_currency_help', ['currency' => $currency_details->name])
                            </span>
                        </div>
                    </div>

                    {{-- Import type --}}
                    @if ($purchase_type == 'international')
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('import_type', __('purchase.import_type')) !!}<span>: <small style="color: #ff0606;">*</small></span>
                            {!! Form::select('import_type', ['maritime' => __('purchase.maritime'), 'aerial' => __('purchase.aerial')],
                                null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']) !!}
                        </div>
                    </div>
                    @endif

                    {{-- Document --}}
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                            {!! Form::file('document', ['id' => 'upload_document']) !!}
                            <p class="help-block">@lang('purchase.max_file_size', ['size' =>
                                (config('constants.document_size_limit') / 1000000)])</p>
                        </div>
                    </div>

                    @if ($purchase_type == 'international')
                    {{-- Import expenses checkbox --}}
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="chk_import_expense" id="chk_import_expense" value="1" class="input-icheck">&nbsp;
                                <strong>@lang('import_expense.import_expenses')</strong>
                            </label>
                            <p class="help-block" style="margin-top: 0; margin-left: 20px;">@lang('product.check_import_expense_help')</p>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 import_expense_box" style="display: none;">
                        <div class="form-group">
                            {!! Form::label('base', __('apportionment.base_for_distributing') . ':') !!}
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <span>
                                        <i class="fa fa-balance-scale"></i>
                                    </span>
                                </div>
                                {!! Form::select('base', [
                                    'weight' => __('apportionment.weight'),
                                    'value' => __('product.cost')
                                ], '', ['class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <!--box end-->

        {{-- Import expenses --}}
        <div class="box box-solid import_expense_box" style="display: none;">
            <div class="box-header">
                <h3 class="box-title">@lang('import_expense.import_expenses')</h3>
            </div>

            <div class="box-body">
                {{-- Search bar --}}
                <div class="row">
                    <div class="col-sm-8 col-sm-offset-2">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-search"></i>
                                </span>
                                {!! Form::text('search_import_expense', null, [
                                    'class' => 'form-control',
                                    'id' => 'search_import_expense',
                                    'placeholder' => __('apportionment.add_import_expense')
                                ]); !!}
                            </div>
                        </div>
                    </div>
                </div>
    
                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table table-striped table-condensed table-th-gray table-text-center" id="import_expenses_table" width="100%">
                        <thead>
                            <tr>
                                <th width="70%">
                                    @lang('crm.name')
                                </th>
                                <th class="text-center" width="20%">
                                    @lang('accounting.amount')
                                </th>
                                <th class="text-center" width="10%">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr class="active">
                                <td class="text-center">
                                    <strong>@lang('purchase.total')</strong>
                                </th>
                                <td class="text-center">
                                    <strong><span id="spn_import_expense_total">$ 0.00</span></strong>
                                    {!! Form::hidden('import_expense_total', 0, ['id' => 'import_expense_total']) !!}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <input type="hidden" id="row_count_ie" value="0">
            </div>
        </div>

        <div class="box box-solid">
            <!--box start-->
            <div class="box-body">
                {{-- Search product --}}
                <div class="row">
                    <div class="col-sm-8 col-sm-offset-2">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-search"></i>
                                </span>
                                {!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'), 'autofocus']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        {{--<div class="form-group">
                            <button tabindex="-1" type="button" class="btn btn-link btn-modal"
                                data-href="{{ action('ProductController@quickAdd') }}"
                                data-container=".quick_add_product_modal"><i class="fa fa-plus"></i> @lang(
                                'product.add_new_product' ) </button>
                        </div>--}}
                    </div>
                </div>

                <div class="row">
                    {{-- Products table --}}
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-condensed table-th-gray text-center table-striped table-text-center" id="purchase_entry_table" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th width="35%">@lang('purchase.product')</th>
                                        <th>@lang('purchase.purchase_quantity')</th>
                                        <th>@lang('product.unit_cost')</th>
                                        @if ($purchase_type == 'international')
                                        <th>@lang('apportionment.weight') (kg)</th>
                                        <th class="col-import-expenses" style="display: none;">@lang('import_expense.import_expenses')</th>
                                        <th style="display: none">@lang('product.dai') (%)</th>
                                        @endif
                                        <th>@lang('product.total_amount')</th>
                                        <th><i class="fa fa-trash" aria-hidden="true"></i></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        
                        <input type="hidden" id="row_count" value="0">
                        <hr/>

                        <div class="row">
                            <div class="col-sm-6 col-sm-offset-4 text-right">
                                <b>@lang('purchase.total_before_tax'):</b> (=)
                            </div>

                            <div class="col-sm-2 text-left">
                                <span id="total_subtotal">$ 0.00</span>
                                <input type="hidden" id="total_subtotal_input" value=0 name="total_before_tax">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-solid">
            <!--box start-->
            <div class="box-body">
                <div class="row" @if ($purchase_type == 'international') style="display: none;" @endif>
                    {{-- Discount type --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('discount_type', __('purchase.discount_type') . ':') !!}
                            {!! Form::select('discount_type', ['' => __('lang_v1.none'), 'fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')], '', ['class' => 'form-control select2', 'id' => 'discount_type']) !!}
                        </div>
                    </div>

                    {{-- Discount amount --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('discount_amount', __('purchase.discount_amount') . ':') !!}
                            {!! Form::text('discount_amount', 0, ['class' => 'form-control input_number', 'required', 'id' => 'discount_amount']) !!}
                        </div>
                    </div>

                    <div class="col-sm-2"></div>

                    {{-- Discounts --}}
                    <div class="col-sm-2 text-right">
                        <b>@lang( 'purchase.discount' ):</b>(-)
                    </div>

                    <div class="col-sm-2 text-left">
                        <span id="discount_calculated_amount">$ 0.00</span>
                        <br>
                        {!! Form::hidden('discount_am', null, ['id' => 'discount_am']) !!}
                    </div>
                </div>

                <div class="row" @if ($purchase_type == 'international') style="display: none;" @endif>
                    {{-- Taxes --}}
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('tax_id', __('purchase.purchase_tax') . ':') !!}
                            <select name="tax_id" id="tax_id" class="form-control select2"
                                placeholder="{{ __('messages.please_select') }}" required>
                                <option value="0" selected>@lang('lang_v1.none')</option>
                                @foreach ($tax_groups as $tg)
                                    <option value="{{ $tg->id }}"> {{ $tg->name }} </option>
                                @endforeach
                            </select>

                            {!! Form::hidden('tax_percent_products', null, ['id' => 'tax_percent_products']) !!}
                            {!! Form::hidden('tax_amount', null, ['id' => 'tax_amount']) !!}
                            {!! Form::hidden('perception_amount', null, ['id' => 'perception_amount']) !!}
                        </div>
                    </div>

                    <div class="col-sm-3"></div>

                    <div class="col-sm-4 text-right">
                        <b>@lang('purchase.purchase_tax'):</b> (+)<br>
                        <b>@lang('tax_rate.perception'):</b> (+)
                    </div>

                    <div class="col-sm-2 text-left">
                        <span id="tax_calculated_amount">$ 0.00</span><br>
                        <span id="perception_amount_text">$ 0.00</span>
                    </div>
                </div>

                <div class="row">
                    {{-- Final total --}}
                    <div class="col-sm-7">
                        {!! Form::hidden('final_total', 0, ['id' => 'grand_total_hidden']) !!}
                    </div>

                    <div class="col-sm-3 text-right">
                        <b>@lang('purchase.purchase_total'): </b>(=)
                    </div>

                    <div class="col-sm-2 text-left">
                        <span id="grand_total">$ 0.00</span>
                    </div>
                </div>

                <div class="row">
                    {{-- Additional notes --}}
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label('additional_notes', __('purchase.additional_notes') . ':') !!}
                            {!! Form::textarea('additional_notes', null, ['class' => 'form-control', 'rows' => 3]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--box end-->
        <!--box end-->

        <div class="box box-solid">
            <!--box start-->
            <div class="box-header">
                <h3 class="box-title">
                    @lang('purchase.paymment_details')
                </h3>
            </div>

            <div class="box-body payment_row">
                @include('sale_pos.partials.payment_row_form', ['row_index' => 0])
                <hr>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="pull-right"><strong>@lang('customer.remaining_credit'):</strong> <span
                                id="payment_due">$ 0.00</span></div>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" id="submit_purchase_form"
                            class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </section>

    <!-- quick product modal -->
    <div class="modal fade quick_add_product_modal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modalTitle"></div>
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        {{-- @include('contact.create', [
            'quick_add' => true,
            'countries' => $countries,             
            'business_debt_to_pay_type' => $business_debt_to_pay_type
            ]) --}}
    </div>
    <!-- /.content -->
@endsection

@section('javascript')
    <script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/contacts.js?v=' . $asset_v) }}"></script>
    @include('purchase.partials.keyboard_shortcuts')
@endsection

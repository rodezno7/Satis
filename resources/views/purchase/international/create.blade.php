@extends('layouts.app')
@section('title', __('purchase.create_import'))

@section('css')
    <style>
        #purchase_entry_table th{
            background: #45B39D;
        }
    </style>
@endsection
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('purchase.create_import') <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true"
                data-container="body" data-toggle="popover" data-placement="bottom"
                data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true"
                data-trigger="hover" data-original-title="" title=""></i></h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Page level currency setting -->
        <input type="hidden" id="p_code" value="{{ $currency_details->code }}">
        <input type="hidden" id="p_symbol" value="{{ $currency_details->symbol }}">
        <input type="hidden" id="p_thousand" value="{{ $currency_details->thousand_separator }}">
        <input type="hidden" id="p_decimal" value="{{ $currency_details->decimal_separator }}">
        @include('layouts.partials.error')
        {!! Form::open(['url' => route('international-purchases.store'), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true]) !!}
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <input type="hidden" id="purchase_type" value="import">
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                {!! Form::select('contact_id', [], null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'supplier_id', 'style' => 'width: 100%;']) !!}
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default bg-white btn-flat add_new_supplier"
                                        data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                </span>
                            </div>
                            <input type="hidden" id="verify_tax_reg">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('ref_no', __('purchase.ref_no') . ':') !!}
                            {!! Form::text('ref_no', null, ['class' => 'form-control input_number', 'placeholder' => __('purchase.ref_no')]) !!}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('transaction_date', __('purchase.purchase_date')) !!}<span>: <small style="color: #ff0606;">*</small></span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::text('transaction_date', @format_date('now'), ['class' => 'form-control', 'readonly', 'required']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('import_type', __('purchase.import_type')) !!}<span>: <small style="color: #ff0606;">*</small></span>
                            {!! Form::select('import_type', ['maritime' => __('purchase.maritime'), 'aerial' => __('purchase.aerial')], null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']) !!}
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('freight', __('purchase.freight')) !!}<span>: <small style="color: #ff0606;">*</small></span>
                            {!! Form::select('freight', ['included' => __('purchase.included'), 'excluded' => __('purchase.excluded')], null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'freight']) !!}
                        </div>
                    </div>

                    <div class="col-sm-3 @if (!empty($default_purchase_status)) hide @endif">
                        <div class="form-group">
                            {!! Form::label('status', __('purchase.purchase_status')) !!}<span>: <small style="color: #ff0606;">*</small></span> @show_tooltip(__('tooltip.order_status'))
                            {!! Form::select('status', $orderStatuses, $default_purchase_status, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']) !!}
                        </div>
                    </div>

                    @if (count($business_locations) == 1)
                        @php
                            $default_location = current(array_keys($business_locations->toArray()));
                        @endphp
                    @else
                        @php $default_location = null; @endphp
                    @endif
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('location_id', __('purchase.business_location')) !!}<span>: <small style="color: #ff0606;">*</small></span>
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


                    {{-- Warehouses select --}}
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('warehouse_id', __('warehouse.warehouse')) !!}<span>: <small style="color: #ff0606;">*</small></span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-industry"></i>
                                </span>
                                {!! Form::select('warehouse_id', [], null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'disabled', 'id' => 'warehouse_id']) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Currency Exchange Rate -->
                    <div class="col-sm-3 @if (!$currency_details->purchase_in_diff_currency) hide @endif">
                        <div class="form-group">
                            {!! Form::label('exchange_rate', __('purchase.p_exchange_rate')) !!}<span>: <small style="color: #ff0606;">*</small></span>
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

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('freight_amount', __('purchase.freight_amount')) !!}<span>: <small style="color: #ff0606;">*</small></span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa">$</i>
                                </span>
                                {!! Form::text('freight_amount', null, ['class' => 'form-control input_number', 'placeholder' => __('purchase.freight_amount'), 'id' => 'freight_amount']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('tax_amount', __('purchase.vat_amount')) !!}<span>: <small style="color: #ff0606;">*</small></span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa">$</i>
                                </span>
                                {!! Form::text('tax_amount', null, ['class' => 'form-control input_number', 'placeholder' => __('purchase.vat_amount'), 'id' => 'vat_amount']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('deconsolidation_amount', __('purchase.deconsolidation_amount')) !!}<span>: <small style="color: #ff0606;">*</small></span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa">$</i>
                                </span>
                                {!! Form::text('deconsolidation_amount', null, ['class' => 'form-control input_number', 'placeholder' => __('purchase.deconsolidation_amount'), 'id' => 'deconsolidation_amount']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('dai_amount', __('purchase.dai_amount')) !!}<span>: <small style="color: #ff0606;">*</small></span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa">$</i>
                                </span>
                                {!! Form::text('dai_amount', null, ['class' => 'form-control input_number', 'placeholder' => __('purchase.dai_amount'), 'id' => 'dai_amount']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('external_storage', __('purchase.external_storage')) !!}<span>: <small style="color: #ff0606;">*</small></span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa">$</i>
                                </span>
                                {!! Form::text('external_storage', null, ['class' => 'form-control input_number', 'placeholder' => __('purchase.external_storage'), 'id' => 'external_storage']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('internal_storage', __('purchase.internal_storage')) !!}<span>: <small style="color: #ff0606;">*</small></span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-percent" aria-hidden="true"></i>
                                </span>
                                {!! Form::text('internal_storage', null, ['class' => 'form-control input_number', 'placeholder' => __('purchase.internal_storage'), 'id' => 'internal_storage']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('local_freight_amount', __('purchase.local_freight_amount')) !!}<span>: <small style="color: #ff0606;">*</small></span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa">$</i>
                                </span>
                                {!! Form::text('local_freight_amount', null, ['class' => 'form-control input_number', 'placeholder' => __('purchase.local_freight_amount'), 'id' => 'local_freight_amount']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('customs_procedure_amount', __('purchase.customs_procedure_amount')) !!}<span>: <small style="color: #ff0606;">*</small></span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa">$</i>
                                </span>
                                {!! Form::text('customs_procedure_amount', null, ['class' => 'form-control input_number', 'placeholder' => __('purchase.customs_procedure_amount'), 'id' => 'customs_procedure_amount']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('subtotal', __('purchase.subtotal')) !!}<span>: <small style="color: #ff0606;">*</small></span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa">$</i>
                                </span>
                                {!! Form::text('total_before_tax', null, ['class' => 'form-control input_number', 'placeholder' => __('purchase.subtotal'), 'id' => 'subtotal']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                            {!! Form::file('document', ['id' => 'upload_document']) !!}
                            <p class="help-block">@lang('purchase.max_file_size', ['size' =>
                                (config('constants.document_size_limit') / 1000000)])</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!--box end-->
        <div class="box box-solid">
            <!--box start-->
            <div class="box-body">
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
                </div>
                @php
                    $hide_tax = '';
                    if (session()->get('business.enable_inline_tax') == 0) {
                        $hide_tax = 'hide';
                    }
                @endphp
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-condensed table-bordered text-center table-striped"
                                id="purchase_entry_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th style="width: 18%;">@lang( 'purchase.product' )</th>
                                        <th>@lang( 'purchase.quantity' )</th>
                                        <th>@lang( 'purchase.weight_kg' )</th>
                                        <th>@lang( 'purchase.fob_price' )</th>
                                        <th>
                                            @lang( 'purchase.transfer_fee' )
                                            @show_tooltip(__('purchase.transfer_fee_tooltip'))
                                        </th>
                                        <th>@lang( 'purchase.freight' )</th>
                                        <th>
                                            @lang( 'purchase.deconsolidation' )
                                            @show_tooltip(__('purchase.deconsolidation_tooltip'))
                                        </th>
                                        <th>@lang( 'purchase.dai' )</th>
                                        <th>@lang( 'purchase.vat' )</th>
                                        <th>@lang( 'purchase.storage_ex_abr' )
                                            @show_tooltip(__('purchase.external_storage'))
                                        </th>
                                        <th>@lang( 'purchase.local_freight' )</th>
                                        <th>@lang( 'purchase.customs_procedure' )</th>
                                        <th>@lang( 'purchase.unit_cost' )</th>
                                        <th><i class="fa fa-trash" aria-hidden="true"></i></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" style="background-color: #FAFAFA;">@lang('purchase.total')</th>
                                        <th style="background-color: #FAFAFA;">
                                            <span class="display_currency" data-currency_symbol="false"
                                                id="total_quantity_text">0.00</span>
                                        </th>
                                        <th style="background-color: #FAFAFA;">
                                            <span class="display_currency" data-currency_symbol="false"
                                                id="total_weight_kg_text">0.00</span>
                                        </th>
                                        <th style="background-color: #FAFAFA;">
                                            <span class="display_currency" data-currency_symbol="true"
                                                id="total_fob_price_text">0.00</span>
                                        </th>
                                        <th style="background-color: #FAFAFA;">
                                            <span class="display_currency" data-currency_symbol="true"
                                                id="total_transfer_fee_text">0.00</span>
                                        </th>
                                        <th style="background-color: #FAFAFA;">
                                            <span class="display_currency" data-currency_symbol="true"
                                                id="total_freight_text">0.00</span>
                                        </th>
                                        <th style="background-color: #FAFAFA;">
                                            <span class="display_currency" data-currency_symbol="true"
                                                id="total_deconsolidation_text">0.00</span>
                                        </th>
                                        <th style="background-color: #FAFAFA;">
                                            <span class="display_currency" data-currency_symbol="true"
                                                id="total_dai_text">0.00</span>
                                        </th>
                                        <th style="background-color: #FAFAFA;">
                                            <span class="display_currency" data-currency_symbol="true"
                                                id="total_vat_text">0.00</span>
                                        </th>
                                        <th style="background-color: #FAFAFA;">
                                            <span class="display_currency" data-currency_symbol="true"
                                                id="total_external_storage">0.00</span>
                                        </th>
                                        <th style="background-color: #FAFAFA;">
                                            <span class="display_currency" data-currency_symbol="true"
                                                id="total_local_freight_text">0.00</span>
                                        </th>
                                        <th style="background-color: #FAFAFA;">
                                            <span class="display_currency" data-currency_symbol="true"
                                                id="total_customs_procedure_text">0.00</span>
                                        </th>
                                        <th style="background-color: #FAFAFA;"></th>
                                        <th style="background-color: #FAFAFA;"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <hr />
                        <div class="pull-right col-md-5">
                            <table class="pull-right col-md-12">
                                <tr>
                                    <th class="col-md-7 text-right">@lang( 'purchase.total' ):</th>
                                    <td class="col-md-5 text-left">
                                        <span id="final_total_text" class="display_currency"></span>
                                        <input type="hidden" id="final_total" name="final_total">
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <input type="hidden" id="row_count" value="0">
                    </div>
                </div>
            </div>
        </div>
        <!--box end-->
        <div class="box box-solid">
            <!--box start-->
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table">
                            <tr>
                                <td colspan="4">
                                    <div class="form-group">
                                        {!! Form::label('additional_notes', __('purchase.additional_notes')) !!}
                                        {!! Form::textarea('additional_notes', null, ['class' => 'form-control', 'rows' => 3]) !!}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" id="submit_purchase_form"
                            class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
                    </div>
                </div>
            </div>
        </div>
        <!--box end-->
        {{-- <div class="box box-solid"><!--box start-->
		<div class="box-header">
			<h3 class="box-title">
				@lang('purchase.add_payment')
			</h3>
		</div>
		<div class="box-body payment_row">
			@include('sale_pos.partials.payment_row_form', ['row_index' => 0])
			<hr>
			<div class="row">
				<div class="col-sm-12">
					<div class="pull-right"><strong>@lang('purchase.payment_due'):</strong> <span id="payment_due">0.00</span></div>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-sm-12">
					<button type="button" id="submit_purchase_form" class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
				</div>
			</div>
		</div>
	</div> --}}
        {!! Form::close() !!}
    </section>
    <!-- quick product modal -->
    <div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        {{-- @include('contact.create', ['quick_add' => true]) --}}
    </div>
    <!-- /.content -->
@endsection

@section('javascript')
    <script src="{{ asset('js/import_purchase.js?v' . $asset_v) }}"></script>
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    @include('purchase.partials.keyboard_shortcuts')
@endsection

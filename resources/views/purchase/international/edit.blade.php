@extends('layouts.app')
@section('title', __('purchase.edit_import'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('purchase.edit_import') <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true"
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

        {!! Form::open(['url' => route('international-purchases.update', $purchase->id), 'method' => 'PUT', 'id' => 'add_purchase_form', 'files' => true, 'target' => '_blank']) !!}

        <input type="hidden" id="purchase_id" value="{{ $purchase->id }}">
        <div class="box box-solid">
            <div class="box-body">
                <input type="hidden" id="purchase_type" value="import">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                {!! Form::select('contact_id', $contacts, $purchase->contact_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'supplier_id']) !!}
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default bg-white btn-flat add_new_supplier"
                                        data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                </span>
                            </div>
                            <input type="hidden" id="verify_tax_reg" value={{ $verify_tax_reg }}>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('ref_no', __('purchase.ref_no') . ':') !!}
                            {!! Form::text('ref_no', $purchase->ref_no, ['class' => 'form-control', 'placeholder' => __('purchase.ref_no')]) !!}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('transaction_date', __('purchase.purchase_date') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::text('transaction_date', @format_date($purchase->transaction_date), ['class' => 'form-control', 'readonly', 'required']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('import_type', __('purchase.import_type') . ':*') !!}
                            {!! Form::select('import_type', ['maritime' => __('purchase.maritime'), 'aerial' => __('purchase.aerial')], $purchase->import_type, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('freight', __('purchase.freight') . ':*') !!}
                            {!! Form::select('freight', ['included' => __('purchase.included'), 'excluded' => __('purchase.excluded')], $purchase->freight, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'freight']) !!}
                        </div>
                    </div>

                    <div class="col-sm-3 @if (!empty($default_purchase_status)) hide @endif">
                        <div class="form-group">
                            {!! Form::label('status', __('purchase.purchase_status') . ':*') !!} @show_tooltip(__('tooltip.order_status'))
                            {!! Form::select('status', $orderStatuses, $purchase->status, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']) !!}
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
                            {!! Form::label('location_id', __('purchase.business_location') . ':*') !!}
                            @show_tooltip(__('tooltip.purchase_location'))
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-building"></i>
                                </span>
                                {!! Form::select('location_id', $business_locations, $purchase->location_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'location_id']) !!}
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
                            {!! Form::label('warehouse_id', __('warehouse.warehouse') . ': ') !!} <span style="color: red">*</span>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-industry"></i>
                                </span>
                                {!! Form::select('warehouse_id', $warehouses, $purchase->warehouse_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'disabled', 'id' => 'warehouse_id']) !!}
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <!-- Currency Exchange Rate -->
                    <div class="col-sm-3 @if (!$currency_details->purchase_in_diff_currency) hide @endif">
                        <div class="form-group">
                            {!! Form::label('exchange_rate', __('purchase.p_exchange_rate') . ':*') !!}
                            @show_tooltip(__('tooltip.currency_exchange_factor'))
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-info"></i>
                                </span>
                                {!! Form::number('exchange_rate', $purchase->exchange_rate, ['class' => 'form-control', 'required', 'step' => 0.001]) !!}
                            </div>
                            <span class="help-block text-danger">
                                @lang('purchase.diff_purchase_currency_help', ['currency' => $currency_details->name])
                            </span>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('freight_amount', __('purchase.freight_amount') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa">$</i>
                                </span>
                                {!! Form::text('freight_amount', @num_format($purchase->freight_amount), ['class' => 'form-control input_number', 'placeholder' => __('purchase.freight_amount'), 'id' => 'freight_amount']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('tax_amount', __('purchase.vat_amount') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa">$</i>
                                </span>
                                {!! Form::text('tax_amount', @num_format($purchase->tax_amount), ['class' => 'form-control input_number', 'placeholder' => __('purchase.vat_amount'), 'id' => 'vat_amount']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('deconsolidation_amount', __('purchase.deconsolidation_amount') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa">$</i>
                                </span>
                                {!! Form::text('deconsolidation_amount', @num_format($purchase->deconsolidation_amount), ['class' => 'form-control input_number', 'placeholder' => __('purchase.deconsolidation_amount'), 'id' => 'deconsolidation_amount']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('dai_amount', __('purchase.dai_amount') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa">$</i>
                                </span>
                                {!! Form::text('dai_amount', @num_format($purchase->dai_amount), ['class' => 'form-control input_number', 'placeholder' => __('purchase.dai_amount'), 'id' => 'dai_amount']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('external_storage', __('purchase.external_storage') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa">$</i>
                                </span>
                                {!! Form::text('external_storage', @num_format($purchase->dai_amount), ['class' => 'form-control input_number', 'placeholder' => __('purchase.external_storage'), 'id' => 'external_storage']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('internal_storage', __('purchase.internal_storage') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-percent" aria-hidden="true"></i>
                                </span>
                                {!! Form::text('internal_storage', @num_format($purchase->internal_storage), ['class' => 'form-control input_number', 'placeholder' => __('purchase.internal_storage'), 'id' => 'internal_storage']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('local_freight_amount', __('purchase.local_freight_amount') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa">$</i>
                                </span>
                                {!! Form::text('local_freight_amount', @num_format($purchase->local_freight_amount), ['class' => 'form-control input_number', 'placeholder' => __('purchase.local_freight_amount'), 'id' => 'local_freight_amount']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('customs_procedure_amount', __('purchase.customs_procedure_amount') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa">$</i>
                                </span>
                                {!! Form::text('customs_procedure_amount', @num_format($purchase->customs_procedure_amount), ['class' => 'form-control input_number', 'placeholder' => __('purchase.customs_procedure_amount'), 'id' => 'customs_procedure_amount']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('subtotal', __('purchase.subtotal') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa">$</i>
                                </span>
                                {!! Form::text('total_before_tax', @num_format($purchase->total_before_tax), ['class' => 'form-control input_number', 'placeholder' => __('purchase.subtotal'), 'id' => 'subtotal']) !!}
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
                    <div class="col-sm-2">
                        <div class="form-group">
                            <button tabindex="-1" type="button" class="btn btn-link btn-modal"
                                data-href="{{ action('ProductController@quickAdd') }}"
                                data-container=".quick_add_product_modal"><i class="fa fa-plus"></i> @lang(
                                'product.add_new_product' ) </button>
                        </div>
                    </div>
                </div>
                {{-- seccion donde se agregan los productos --}}
                <div class="row">
                    <div class="col-sm-12">
                        @include('purchase.partials.edit_purchase_import_entry_row')

                        <hr />
                        <div class="pull-right col-md-5">
                            <table class="pull-right col-md-12">
                                <tr class="hide">
                                    <th class="col-md-7 text-right">@lang( 'purchase.total_before_tax' ):</th>
                                    <td class="col-md-5 text-left">
                                        <span id="total_st_before_tax" class="display_currency"></span>
                                        <input type="hidden" id="st_before_tax_input" value=0>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="col-md-7 text-right">@lang( 'purchase.total' ):</th>
                                    <td class="col-md-5 text-left">
                                        <span id="final_total_text"
                                            class="display_currency">{{ $purchase->final_total }}</span>
                                        <input type="hidden" id="final_total" name="final_total"
                                            value="{{ $purchase->final_total }}">
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!--box end-->

        {{-- se agrega las notas --}}
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
                                        {!! Form::textarea('additional_notes', $purchase->additional_notes, ['class' => 'form-control', 'rows' => 3]) !!}
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
    <!-- /.content -->
    <!-- quick product modal -->
    <div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        @include('contact.create', ['quick_add' => true])
    </div>

@endsection

@section('javascript')
    <script src="{{ asset('js/import_purchase.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>

    <script>
        $(function() {
            update_line_total();
            update_inputs_index();
        });

    </script>
    @include('purchase.partials.keyboard_shortcuts')
@endsection

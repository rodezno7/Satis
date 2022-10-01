@extends('layouts.app')
@section('title', __('lang_v1.edit_transfer'))

@section('content')

{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1>@lang('lang_v1.edit_transfer')</h1>
</section>

{{-- Main content --}}
<section class="content no-print">
	{!! Form::open(['url' => action('StockTransferController@update', [$sell_transfer->id]), 'method' => 'put', 'id' => 'stock_transfer_form' ]) !!}

	{{-- Redirect url --}}
	{!! Form::hidden('redirect_url', action('StockTransferController@index'), ['id' => 'redirect_url']) !!}

	{{-- Send url --}}
	<input type="hidden" id="send_url" value="{{ action('StockTransferController@send') }}">

	{{-- Id --}}
	{!! Form::hidden('id', $sell_transfer->id, ['id' => 'transaction_id']) !!}

    {{-- Enable remission note --}}
	<input type="hidden" id="enable_remission_note" value="{{ $enable_remission_note }}">

    {{-- Number of decimal places to store and use in calculations --}}
	<input type="hidden" id="price_precision" value="{{ config('app.price_precision') }}">

	{{-- Number of decimal places to show --}}
	<input type="hidden" id="inventory_precision" value="{{ $decimals_in_inventories }}">

	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
                {{-- transaction_date --}}
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date') . ': ') !!} <span style="color: red">*</span>
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date', @format_date($sell_transfer->transaction_date),
                                ['class' => 'form-control', 'readonly', 'required']); !!}
						</div>
					</div>
				</div>

                {{-- ref_no --}}
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
						{!! Form::text('ref_no', $sell_transfer->ref_no, ['class' => 'form-control']); !!}
					</div>
				</div>

                {{-- from --}}
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('from_warehouse_id', __('lang_v1.location_from').': ') !!} <span style="color: red">*</span>
						{!! Form::select('from_warehouse_id', $warehouses, $sell_transfer->warehouse_id, [
                            'class' => 'form-control select2',
                            'placeholder' => __('messages.please_select'),
                            'required',
                            'id' => 'from_warehouse_id'
                        ]); !!}
						{!! Form::hidden('from_location_id', $sell_transfer->location_id, ['id' => 'from_location_id']) !!}
					</div>
				</div>

                {{-- to --}}
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('to_warehouse_id', __('lang_v1.location_to').': ') !!} <span style="color: red">*</span>
						{!! Form::select('to_warehouse_id', $to_warehouses, $purchase_transfer->warehouse_id, [
                            'class' => 'form-control select2',
                            'placeholder' => __('messages.please_select'),
                            'required',
                            'id' => 'to_warehouse_id'
                        ]); !!}
						{!! Form::hidden('to_location_id', $purchase_transfer->location_id, ["id" => "to_location_id"]) !!}
					</div>
				</div>
			</div>
		</div>
	</div> <!--box end-->

	<div class="box box-solid">
		<div class="box-header">
        	<h3 class="box-title">{{ __('stock_adjustment.search_products') }}</h3>
       	</div>

		<div class="box-body">
			<div class="row">
                {{-- search_product --}}
				<div class="col-sm-8 col-sm-offset-2">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-search"></i>
							</span>
							{!! Form::text('search_product', null, [
                                'class' => 'form-control',
                                'id' => 'search_product_for_stock_adjustment',
                                'placeholder' => __('stock_adjustment.search_product'),
                                'disabled'
                            ]); !!}
						</div>
					</div>
				</div>
			</div>

            {{-- Table --}}
			<div class="row">
				<div class="col-sm-10 col-sm-offset-1">
					<div class="table-responsive">
                        <table class="table table-bordered table-striped table-condensed" id="stock_adjustment_product_table" style="font-size: inherit;">
                            <thead>
                                <tr>
                                    <th class="col-sm-4 text-center">	
                                        @lang('sale.product')
                                    </th>

                                    <th class="col-sm-2 text-center">
                                        @lang('sale.qty')
                                    </th>

                                    <th class="col-sm-2 text-center">
                                        @lang('product.unit_cost')
                                    </th>

                                    <th class="col-sm-2 text-center">
                                        @lang('sale.subtotal')
                                    </th>

                                    <th class="col-sm-2 text-center">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                $row_index = 0;
                                $final_total = 0;
                                @endphp
                                @foreach ($products as $product)
                                <tr class="product_row">
                                    {{-- Product --}}
                                    <td>
                                        {{ $product->product_name }}
                                        <br/>
                                        {{ $product->sub_sku }}
                                    </td>

                                    {{-- Quantity --}}
                                    <td>
                                        {{-- If edit then transaction sell lines will be present --}}
                                        @if (! empty($product->transaction_sell_lines_id))
                                        <input
                                            type="hidden"
                                            name="products[{{ $row_index }}][transaction_sell_lines_id]"
                                            class="form-control"
                                            value="{{ $product->transaction_sell_lines_id }}">

                                        <input
                                            type="hidden"
                                            name="products[{{ $row_index }}][purchase_lines_id]"
                                            class="form-control"
                                            value="{{ $product->purchase_lines_id }}">
                                        @endif
                                
                                        <input
                                            type="hidden"
                                            name="products[{{ $row_index }}][product_id]"
                                            class="form-control product_id"
                                            value="{{ $product->product_id }}">
                                
                                        <input
                                            type="hidden"
                                            value="{{ $product->variation_id }}" 
                                            name="products[{{ $row_index }}][variation_id]">
                                
                                        <input
                                            type="hidden"
                                            value="{{ $product->enable_stock }}" 
                                            name="products[{{ $row_index }}][enable_stock]">
                                        
                                        @if (empty($product->quantity_ordered))
                                        @php
                                        $product->quantity_ordered = 1;
                                        @endphp
                                        @endif
                                
                                        <input
                                            type="text"
                                            class="form-control product_quantity input_number"
                                            value="{{ @num_format($product->quantity_ordered) }}"
                                            name="products[{{ $row_index }}][quantity]" 
                                            @if ($product->unit_allow_decimal == 1)
                                            data-decimal=1
                                            @else
                                            data-decimal=0
                                            data-rule-abs_digit="true"
                                            data-msg-abs_digit="@lang('lang_v1.decimal_value_not_allowed')"
                                            @endif
                                            data-rule-required="true"
                                            data-msg-required="@lang('validation.custom-messages.this_field_is_required')"
                                            @if ($product->enable_stock)
                                            data-rule-max-value="{{ $product->qty_available }}"
                                            data-msg-max-value="@lang('validation.custom-messages.quantity_not_available', ['qty'=> $product->formatted_qty_available, 'unit' => $product->unit])"
                                            @endif >
                                            {{ $product->unit }}
                                    </td>

                                    @php
                                        $unit_cost = $show_costs_or_prices == 'costs' ? $product->last_purchased_price : $product->sell_price_inc_tax;
                                    @endphp

                                    {{-- Cost/Price --}}
                                    <td>
                                        <input
                                            type="text"
                                            readonly
                                            class="form-control product_unit_price input_number"
                                            value="{{ number_format($unit_cost, $decimals_in_inventories) }}">

                                        <input
                                            type="hidden"
                                            name="products[{{ $row_index }}][u_price_exc_tax]"
                                            class="product_unit_price_hidden"
                                            value="{{ $product->last_purchased_price }}">
                                    </td>

                                    {{-- Subtotal --}}
                                    <td>
                                        <input
                                            type="text"
                                            readonly
                                            class="form-control product_line_total"
                                            value="{{ number_format($product->quantity_ordered * $unit_cost, $decimals_in_inventories) }}">

                                        <input
                                            type="hidden"
                                            name="products[{{ $row_index }}][unit_price_exc_tax]"
                                            class="product_line_total_hidden"
                                            value="{{ $product->quantity_ordered * $product->last_purchased_price }}">
                                    </td>

                                    {{-- Remove product row button --}}
                                    <td class="text-center">
                                        <i class="fa fa-trash remove_product_row cursor-pointer" aria-hidden="true"></i>
                                    </td>
                                </tr>
                                @php
                                $row_index++;
                                $final_total += $product->quantity_ordered * $unit_cost;
                                @endphp
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr class="text-center">
                                    <td colspan="@if ($show_costs_or_prices == 'none') 2 @else 3 @endif"></td>
                                    
                                    <td @if ($show_costs_or_prices == 'none') style="display: none;" @endif>
                                        <div class="pull-right">
                                            <b>@lang('stock_adjustment.total_amount'):</b> <span id="total_adjustment">{{ $final_total }}</span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <input type="hidden" id="product_row_index" value="{{ $row_index }}">
					    <input type="hidden" id="total_amount" name="final_total" value="{{ $final_total }}">
					</div>
				</div>
			</div>
		</div>
	</div> <!--box end-->

	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
                {{-- shipping_charges --}}
				{{-- <div class="col-sm-4">
					<div class="form-group">
							{!! Form::label('shipping_charges', __('lang_v1.shipping_charges') . ':') !!}
							{!! Form::text('shipping_charges', 0, ['class' => 'form-control input_number', 'placeholder' => __('lang_v1.shipping_charges')]); !!}
					</div>
				</div> --}}

                {!! Form::hidden('shipping_charges', '') !!}

                {{-- additional_notes --}}
				<div class="col-sm-8">
					<div class="form-group">
						{!! Form::label('additional_notes',__('purchase.additional_notes')) !!}
						{!! Form::textarea('additional_notes', $sell_transfer->additional_notes, ['class' => 'form-control', 'rows' => 3]); !!}
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12 text-right">
					<button id="save_stock_transfer" class="btn btn-primary">@lang('messages.save')</button>
					&nbsp;
					<button id="send_stock_transfer" class="btn btn-success">@lang('lang_v1.send')</button>
					&nbsp;
					<button id="cancel_stock_transfer" class="btn btn-danger">@lang('messages.cancel')</button>
				</div>
			</div>

		</div>
	</div> <!--box end-->
	{!! Form::close() !!}
</section>
@stop

{{-- Print section --}}
<section id="receipt_section" class="print_section"></section>

@section('javascript')
<script src="{{ asset('js/stock_transfer.js?v=' . $asset_v) }}"></script>
@endsection

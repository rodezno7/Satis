@extends('layouts.app')
@section('title', __('lang_v1.add_stock_transfer'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.add_stock_transfer')</h1>
</section>

<!-- Main content -->
<section class="content no-print">
	{!! Form::open(['url' => action('StockTransferController@store'), 'method' => 'post', 'id' => 'stock_transfer_form' ]) !!}

	{{-- Redirect url --}}
	{!! Form::hidden('redirect_url', action('StockTransferController@index'), ['id' => 'redirect_url']) !!}

	{{-- Send url --}}
	<input type="hidden" id="send_url" value="{{ action('StockTransferController@send') }}">

	{{-- Id --}}
	{!! Form::hidden('id', 0, ['id' => 'transaction_id']) !!}

	{{-- Enable remission note --}}
	<input type="hidden" id="enable_remission_note" value="{{ $enable_remission_note }}">

	{{-- Number of decimal places to store and use in calculations --}}
	<input type="hidden" id="price_precision" value="{{ config('app.price_precision') }}">

	{{-- Number of decimal places to show --}}
	<input type="hidden" id="inventory_precision" value="{{ $decimals_in_inventories }}">

	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date') . ': ') !!} <span style="color: red">*</span>
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date', @format_date('now'), ['class' => 'form-control', 'readonly', 'required']); !!}
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
						{!! Form::text('ref_no', null, ['class' => 'form-control']); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('from_warehouse_id', __('lang_v1.location_from').': ') !!} <span style="color: red">*</span>
						{!! Form::select('from_warehouse_id', $warehouse_id, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'from_warehouse_id']); !!}
						{!! Form::hidden('from_location_id', null, ['id' => 'from_location_id']) !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('to_warehouse_id', __('lang_v1.location_to').': ') !!} <span style="color: red">*</span>
						{!! Form::select('to_warehouse_id', $to_warehouse_id, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'to_warehouse_id']); !!}
						{!! Form::hidden('to_location_id', null, ["id" => "to_location_id"]) !!}
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
				<div class="col-sm-8 col-sm-offset-2">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-search"></i>
							</span>
							{!! Form::text('search_product', null, ['class' => 'form-control', 'id' => 'search_product_for_stock_adjustment', 'placeholder' => __('stock_adjustment.search_product'), 'disabled']); !!}
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-10 col-sm-offset-1">
					<input type="hidden" id="product_row_index" value="0">
					<input type="hidden" id="total_amount" name="final_total" value="0">
					<div class="table-responsive">
					<table class="table table-bordered table-striped table-condensed" 
					id="stock_adjustment_product_table">
						<thead>
							<tr>
								<th class="col-sm-4 text-center">	
									@lang('sale.product')
								</th>
								<th class="col-sm-2 text-center">
									@lang('sale.qty')
								</th>
								<th class="col-sm-2 text-center" @if ($show_costs_or_prices == 'none') style="display: none;" @endif>
									@if ($show_costs_or_prices == 'costs')
										@lang('product.unit_cost')
									@else
										@lang('product.unit_price')
									@endif
								</th>
								<th class="col-sm-2 text-center" @if ($show_costs_or_prices == 'none') style="display: none;" @endif>
									@lang('sale.subtotal')
								</th>
								<th class="col-sm-2 text-center"><i class="fa fa-trash" aria-hidden="true"></i></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr class="text-center">
								<td colspan="@if ($show_costs_or_prices == 'none') 2 @else 3 @endif"></td>
								<td @if ($show_costs_or_prices == 'none') style="display: none;" @endif>
									<div class="pull-right">
										<b>@lang('stock_adjustment.total_amount'):</b> <span id="total_adjustment">0.00</span>
									</div>
								</td>
							</tr>
						</tfoot>
					</table>
					</div>
				</div>
			</div>
		</div>
	</div> <!--box end-->
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				{{-- <div class="col-sm-4">
					<div class="form-group">
							{!! Form::label('shipping_charges', __('lang_v1.shipping_charges') . ':') !!}
							{!! Form::text('shipping_charges', 0, ['class' => 'form-control input_number', 'placeholder' => __('lang_v1.shipping_charges')]); !!}
					</div>
				</div> --}}
				
				{!! Form::hidden('shipping_charges', '') !!}

				<div class="col-sm-8">
					<div class="form-group">
						{!! Form::label('additional_notes',__('purchase.additional_notes')) !!}
						{!! Form::textarea('additional_notes', null, ['class' => 'form-control', 'rows' => 3]); !!}
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

{{-- Print section --}}
<section id="receipt_section" class="print_section"></section>

@stop
@section('javascript')
	<script src="{{ asset('js/stock_transfer.js?v=' . $asset_v) }}"></script>
@endsection

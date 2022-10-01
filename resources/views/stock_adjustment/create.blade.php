@extends('layouts.app')
@section('title', __('stock_adjustment.add'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
<br>
    <h1>@lang('stock_adjustment.add')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content no-print">
	{{-- Number of decimal places to store and use in calculations --}}
	<input type="hidden" id="price_precision" value="{{ config('app.price_precision') }}">

	{{-- Number of decimal places to show --}}
	<input type="hidden" id="inventory_precision" value="{{ $decimals_in_inventories }}">

	{!! Form::open(['url' => action('StockAdjustmentController@store'), 'method' => 'post', 'id' => 'stock_adjustment_form' ]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				{{-- Reference count --}}
				{!! Form::hidden('ref_count', $ref_count, ['id' => 'ref_count']) !!}
				{{-- Redirect url --}}
				{!! Form::hidden('redirect_url', action('StockAdjustmentController@index'), ['id' => 'redirect_url']) !!}
				{{-- Adjustment type --}}
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('adjustment_type', __('stock_adjustment.adjustment_type') . ':*') !!}
						<div class="input-group" style="width: 100%">
							{!! Form::select('adjustment_type', ['normal' =>  __('messages.input'), 'abnormal' =>  __('messages.output')], null, ['class' => 'form-control select2', "id" => "adjustment_type", 'placeholder' => __('messages.please_select'), 'required']); !!}
						</div>
					</div>
				</div>
				{{-- Reference --}}
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('ref_no', __('purchase.ref_no') . ':') !!}
						<div class="input-group" style="width: 100%">
							{!! Form::text('ref_no', null, ['class' => 'form-control', 'placeholder' => __('purchase.ref_no')]); !!}
						</div>
					</div>
				</div>
				{{-- Warehouse --}}
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('warehouse_id', __('warehouse.warehouse').':*') !!}
						<div class="input-group" style="width: 100%">
							{!! Form::select('warehouse_id', $warehouses, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'warehouse_id']); !!}
						</div>
						{!! Form::hidden("location_id", null, ["id" => "location_id"]) !!}
					</div>
				</div>
				{{-- Date --}}
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date', @format_date('now'), ['class' => 'form-control', 'readonly', 'required']); !!}
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				{{-- Reason --}}
				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('additional_notes', __('stock_adjustment.reason_for_stock_adjustment') . ':*') !!}
						<div class="input-group" style="width: 100%">
							{!! Form::textarea('additional_notes', null, ['class' => 'form-control', 'placeholder' => __('stock_adjustment.reason_for_stock_adjustment'), 'rows' => 2, 'required']); !!}
						</div>
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
							{!! Form::text('search_product', null, ['class' => 'form-control', 'id' => 'search_product_for_srock_adjustment', 'placeholder' => __('stock_adjustment.search_product'), 'disabled']); !!}
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
				<div class="col-sm-12">
					<button type="button" id="submit_stock_adjustment_form" class="btn btn-primary pull-right">@lang('messages.save')</button>
				</div>
			</div>

		</div>
	</div> <!--box end-->
	{!! Form::close() !!}
</section>

<section id="receipt_section" class="print_section"></section>

@stop
@section('javascript')
	<script src="{{ asset('js/stock_adjustment.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
@endsection

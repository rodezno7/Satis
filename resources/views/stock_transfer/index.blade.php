@extends('layouts.app')
@section('title', __('lang_v1.stock_transfers'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang('lang_v1.stock_transfers')
        <small></small>
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">

	<div class="box">
        <div class="box-header">
        	<h3 class="box-title">@lang('lang_v1.all_stock_transfers')</h3>
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action('StockTransferController@create')}}">
                <i class="fa fa-plus"></i> @lang('messages.add')</a>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                {{-- Warehouse --}}
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label("warehouse", __("kardex.warehouse") . ":") !!}
                        @if (is_null($default_warehouse))
                        {!! Form::select("select_warehouse", $warehouses, null,
                            ["class" => "form-control select2", "id" => "select_warehouse"]) !!}
                        {!! Form::hidden('warehouse', 'all', ['id' => 'warehouse']) !!}
                        @else
                        {!! Form::select("select_warehouse", $warehouses, null,
                            ["class" => "form-control select2", "id" => "warehouse", 'disabled']) !!}
                        {!! Form::hidden('warehouse', $default_warehouse, ['id' => 'warehouse']) !!}
                        @endif
                    </div>
                </div>
            </div>
            <div class="table-responsive">
        	<table class="table table-striped table-text-center" id="stock_transfer_table" width="100%">
        		<thead>
        			<tr>
        				<th>@lang('messages.date')</th>
                        <th>@lang('order.ref_no')</th>
                        <th>@lang('lang_v1.transfer_from')</th>
                        <th>@lang('lang_v1.transfer_to')</th>
                        <th>@lang('lang_v1.quantity')</th>
                        <th>@lang('stock_adjustment.total_amount')</th>
                        <th>@lang('crm.responsable')</th>
                        <th>@lang('accounting.status')</th>
                        <th>@lang('purchase.additional_notes')</th>
						<th>@lang('messages.actions')</th>
        			</tr>
        		</thead>
        	</table>
            </div>
        </div>
    </div>

</section>

<section id="receipt_section" class="print_section"></section>

<!-- /.content -->
@stop
@section('javascript')
	<script src="{{ asset('js/stock_transfer.js?v=' . $asset_v) }}"></script>
@endsection
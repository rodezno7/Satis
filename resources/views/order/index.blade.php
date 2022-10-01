@extends('layouts.app')
@section('title', __('order.orders'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('order.orders')
        <small>@lang('order.manage_your_orders')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
	<div class="boxform_u box-solid_u">
        <div class="box-header">
        	<h3 class="box-title">@lang( 'order.all_your_orders')</h3>
            @if(auth()->user()->can('order.create'))
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action('OrderController@create')}}">
                <i class="fa fa-plus"></i> @lang('messages.add')</a>
            </div>
            @endif
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label("location", __("business.location")) !!}
                        {!! Form::select("location_id", $locations, null, ["class" => "form-control",
                            "id" => "location", "placeholder" => __("business.select_location")]) !!}
                    </div>
                </div>
            </div>
            @if(auth()->user()->can('order.view'))
            <div class="table-responsive-md">
            	<table class="table" id="orders_table">
            		<thead>
            			<tr>
                            <th style="text-align: center;">@lang('order.ref_no')</th>
                            <th style="text-align: center;">@lang('messages.date')</th>
                            <th style="text-align: center;">@lang('sale.customer_name')</th>
                            <th style="text-align: center;">@lang('quote.invoiced')</th>
                            <th style="text-align: center;">@lang('order.amount')</th>
                            <th style="text-align: center;">@lang('order.delivery_type')</th>
                            <th style="text-align: center;">@lang('quote.employee')</th>
                            <th style="text-align: center;">@lang('messages.actions')</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    <div class="modal fade show_order_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
</div>
</section>
<!-- /.content -->
@endsection

@section('javascript')
    <script src="{{ asset('js/order.js?v=' . $asset_v) }}"></script>
@endsection
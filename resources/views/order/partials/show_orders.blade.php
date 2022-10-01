@forelse($orders as $order)
	<div class="col-md-3 col-xs-6 order_div">
		<div class="small-box bg-gray" style="border: 1px solid #000; border-radius: 5px;">
            <div class="inner">
				<h4 style="margin-bottom: 35px;">
					<span class="pull-left">
						{{ __("order." . $order->delivery_type) }}	
					</span>
					<span class="pull-right">
						#{{ $order->quote_ref_no }}	
					</span>
				</h4>
            	<table class="table no-margin no-border table-slim">
            		<tr><th>@lang('order.delivery')</th><td>{{ @format_date($order->delivery_date) }}</td></tr>
            		<tr><th>@lang('order.status')</th><td>
						<span class="label font-size
							@if($order->status == 'opened' ) bg-info text-black
							@elseif($order->status == 'in_preparation') bg-blue text-white
							@elseif($order->status == 'prepared') bg-yellow text-black
							@elseif($order->status == 'on_route') bg-orange text-black
							@elseif($order->status == 'closed') bg-green text-black
							@endif">
						@lang('order.' . $order->status)
						</span></td></tr>
            		<tr><th>@lang('contact.customer')</th><td>{{$order->customer_name}}</td></tr>
            		<tr><th>@lang('order.address')</th><td>{{$order->address}}</td></tr>
            	</table>
			</div>
			<input type="hidden" id="order_status" value="{{ $order->status }}">
			<input type="hidden" id="invoiced" value="{{ $order->invoiced }}">
            @if($order->status == 'opened')
				<a href="#" class="btn btn-flat small-box-footer bg-info text-black change_order_status_btn"
					data-href="{{ action('OrderController@changeOrderStatus', [$order->id]) }}">
					<i class="fa fa-check-square-o"></i> @lang('order.mark_as_in_preparation')</a>
			@elseif($order->status == 'in_preparation')
				<a href="#" class="btn btn-flat small-box-footer bg-blue text-white change_order_status_btn"
					data-href="{{ action('OrderController@changeOrderStatus', [$order->id]) }}">
					<i class="fa fa-check-square-o"></i> @lang('order.mark_as_prepared')</a>
			@elseif($order->status == 'prepared')
				<a href="#" class="btn btn-flat small-box-footer bg-yellow text-black text-black change_order_status_btn"
					data-href="{{ action('OrderController@changeOrderStatus', [$order->id]) }}">
					<i class="fa fa-check-square-o"></i> @lang('order.mark_as_on_route')</a>
			@elseif($order->status == 'on_route')
				<a href="#" class="btn btn-flat small-box-footer bg-orange text-black change_order_status_btn"
					data-href="{{ action('OrderController@changeOrderStatus', [$order->id]) }}">
					<i class="fa fa-check-square-o"></i> @lang('order.mark_as_closed')</a>
			@elseif($order->status == 'closed')
				<div class="small-box-footer bg-green">&nbsp;</div>
            {{--@else
            	<div class="small-box-footer bg-green">&nbsp;</div>--}}
            @endif
            	<a href="#" class="btn btn-flat small-box-footer bg-black show_order" data-href="{{ action('OrderController@show', [$order->id])}}">@lang('restaurant.order_details') <i class="fa fa-arrow-circle-right"></i></a>
         </div>
	</div>
	@if($loop->iteration % 4 == 0)
		<div class="hidden-xs">
			<div class="clearfix"></div>
		</div>
	@endif
	@if($loop->iteration % 2 == 0)
		<div class="visible-xs">
			<div class="clearfix"></div>
		</div>
	@endif
@empty
<div class="col-md-12">
	<h4 class="text-center">@lang('restaurant.no_orders_found')</h4>
</div>
@endforelse
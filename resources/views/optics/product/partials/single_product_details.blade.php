@php
  $price_precision = config('app.price_precision');
@endphp

<br>
<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table bg-gray table-text-center">
				<tr class="bg-green">
					<th>@lang('product.default_purchase_price') (@lang('product.exc_of_tax'))</th>
					<th>@lang('product.default_purchase_price') (@lang('product.inc_of_tax'))</th>
					@can('access_default_selling_price')
				        <th>@lang('product.profit_percent')</th>
				        <th>@lang('product.default_selling_price') (@lang('product.exc_of_tax'))</th>
				        <th>@lang('product.default_selling_price') (@lang('product.inc_of_tax'))</th>
				    @endcan
				    @if(!empty($allowed_group_prices))
			        	<th>@lang('lang_v1.group_prices')</th>
			        @endif
					@if (
						auth()->user()->can('product.download_cost_history') ||
						auth()->user()->can('product.recalculate_cost')
					)
					<th style="width: 70px;">&nbsp;</th>
					@endif
				</tr>
				@foreach($product->variations as $variation)
				<tr>
					<td>
						<span class="display_currency default-purchase-price" data-currency_symbol="true" data-precision="{{ $price_precision }}">
							{{ $variation->default_purchase_price }}
						</span>
					</td>
					<td>
						<span class="display_currency dpp-inc-tax" data-currency_symbol="true" data-precision="{{ $price_precision }}">
							{{ $variation->dpp_inc_tax }}
						</span>
					</td>
					@can('access_default_selling_price')
						<td>
							<span class="profit-percent">
								{{ $variation->profit_percent }}
							</span>
						</td>
						<td>
							<span class="display_currency" data-currency_symbol="true" data-precision="{{ $price_precision }}">
								{{ $variation->default_sell_price }}
							</span>
						</td>
						<td>
							<span class="display_currency" data-currency_symbol="true" data-precision="{{ $price_precision }}">
								{{ $variation->sell_price_inc_tax }}
							</span>
						</td>
					@endcan
					@if(!empty($allowed_group_prices))
			        	<td class="td-full-width">
			        		@foreach($allowed_group_prices as $key => $value)
			        			<strong>{{$value}}</strong> - @if(!empty($group_price_details[$variation->id][$key]))
			        				<span class="display_currency" data-currency_symbol="true" data-precision="{{ $price_precision }}">
										{{ $group_price_details[$variation->id][$key] }}
									</span>
			        			@else
			        				0.000000
			        			@endif
			        			<br>
			        		@endforeach
			        	</td>
			        @endif
					@if (
						auth()->user()->can('product.download_cost_history') ||
						auth()->user()->can('product.recalculate_cost')
					)
					<td>
						@can('product.download_cost_history')
						<a
							href="{{ action('ReportController@generateCostHistory', ['variation_id' => $variation->id]) }}"
							target="_blank"
							id="btn-history"
							title
							data-toggle="tooltip"
							data-original-title="{{ __('product.download_cost_history') }}"
							class="btn btn-xs btn-success"
							data-variation-id="{{ $variation->id }}"
							style="width: 25px;">
							<i class="fa fa-download"></i>
						</a>
						@endcan
						@can('product.recalculate_cost')
						<button
							type="button"
							id="btn-recalculate"
							title
							data-toggle="tooltip"
							data-original-title="{{ __('product.recalculate_cost') }}"
							class="btn btn-xs btn-success"
							data-variation-id="{{ $variation->id }}"
							style="width: 25px;">
							<i class="fa fa-refresh"></i>
						</button>
						@endcan
					</td>
					@endif
				</tr>
				@endforeach
			</table>
		</div>
	</div>
</div>
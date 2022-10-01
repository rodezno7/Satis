<div class="row">
	<div class="col-xs-12 col-sm-10 col-sm-offset-1">
		<div class="table-responsive">
			<table class="table table-condensed bg-gray details-table">
				<tr>
					<th>@lang('sale.product')</th>
					@if(!empty($lot_n_exp_enabled))
                		<th>{{ __('lang_v1.lot_n_expiry') }}</th>
              		@endif
					<th>@lang('sale.qty')</th>
					<th @if ($show_costs_or_prices == 'none') style="display: none;" @endif>
						@if ($show_costs_or_prices == 'costs')
							@lang('product.unit_cost')
						@else
							@lang('product.unit_price')
						@endif
					</th>
					<th>@lang('sale.subtotal')</th>
				</tr>
				@foreach( $stock_adjustment_details as $details )
					<tr>
						<td>
							{{ $details->product }} 
							@if( $details->type == 'variable')
							 {{ '-' . $details->product_variation . '-' . $details->variation }} 
							@endif 
							( {{ $details->sub_sku }} )
						</td>
						@if(!empty($lot_n_exp_enabled))
                			<td>{{ $details->lot_number or '--' }}
			                  @if( session()->get('business.enable_product_expiry') == 1 && !empty($details->exp_date))
			                    ({{@format_date($details->exp_date)}})
			                  @endif
			                </td>
              			@endif
						<td>
							{{@num_format($details->quantity)}}
						</td>
						@php
							$price = $show_costs_or_prices == 'prices' && ! is_null($details->sale_price) ? $details->sale_price : $details->unit_price;
						@endphp
						<td @if ($show_costs_or_prices == 'none') style="display: none;" @endif>
							<span class="display_currency" data-currency_symbol="false" data-precision="{{ $decimals_in_inventories }}">
								{{ $price }}
							</span>
						</td>
						<td @if ($show_costs_or_prices == 'none') style="display: none;" @endif>
							<span class="display_currency" data-currency_symbol="false" data-precision="{{ $decimals_in_inventories }}">
								{{ $price * $details->quantity }}
							</span>
						</td>
					</tr>
				@endforeach
			</table>
		</div>
	</div>
</div>
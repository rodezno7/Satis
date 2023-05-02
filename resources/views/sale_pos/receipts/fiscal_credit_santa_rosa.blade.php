<style>
	div#container{ font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif }
	div#container { width: 7.5cm; margin: 0cm 0.5cm 0cm 0.5cm; font-size: 8.5px; }
	div#head { height: 3.5cm; width: 100%; }
	div#container table{ width: 100%; table-layout: fixed; }
	div#container table td { vertical-align: middle; }
	div#date table tr { height: 0.5cm; }
	div#header { height: 3cm; width: 100%; }
	div#header table tr { height: 0.5cm; }
	div#details { height: 10cm; width: 100%; }
	div#details table#sell_lines thead tr { height: 0.9cm; }
	div#details table#sell_lines tbody tr { height: 0.5cm; }
	@if ($receipt_details->discount_amount > 0)
	div#extra-details { position: relative; }
	div#extra-details #discount-text { position: absolute; left: 5cm; top: 0.1cm; padding-left: 0.2cm; }
	div#extra-details #discount-amount { position: absolute; left: 7cm; top: 0.1cm; width: 1.5cm; text-align: center; }
	@endif
	div#footer{ width: 100%; height: 5cm; }
	div#footer table td { padding-left: 0.1cm; }
	div#footer table tr { height: 0.625cm; }
	.cutter { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
	.text-center { text-align: center; }
	.text-right { text-align: right; }
</style>

<div id="container">
	<div id="head">
	</div>
	<div id="date">
		<table>
			<tr>
				<td style="width: 4.1cm;">&nbsp;</td>
				<td>{{ date("d",strtotime($receipt_details->invoice_date)) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . date("m",strtotime($receipt_details->invoice_date)) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . date("Y",strtotime($receipt_details->invoice_date)) }}</td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
		</table>
	</div>
	<div id="header">
		<table>
			<tr>
				<td class="cutter" style="padding-left: 0.8cm;" colspan="2">{{ $receipt_details->customer_name }}</td>
			</tr>
			<tr>
				<td class="cutter" style="padding-left: 1cm; width: 4.2cm;">{{ $receipt_details->customer_location }}</td>
				<td style="padding-left: 1.2cm;">{{ $receipt_details->customer_tax_number }}</td>
			</tr>
			<tr>
				<td class="cutter" style="padding-left: 1cm;">{{ $receipt_details->customer_city }}</td>
				<td style="padding-left: 0.7cm;">{{ $receipt_details->customer_nit }}</td>
			</tr>
			<tr>
				<td class="cutter" style="padding-left: 1.4cm;">{{ $receipt_details->customer_state }}</td>
				<td class="cutter" style="padding-left: 0.8cm;">{{ $receipt_details->customer_business_activity }}</td>
			</tr>
			<tr>
				<td class="cutter" style="padding-left: 2.5cm;">{{ __("messages." . $receipt_details->payment_condition ) }}</td>
				<td class="cutter" style="padding-left: 1.5cm;">{{ $receipt_details->seller_name }}</td>
			</tr>
		</table>
	</div>
	<div id="details">
		<table id="sell_lines" style="table-layout: fixed;">
			<thead>
				<tr style="height: 0.5cm;">
					<th style="width: 1cm">&nbsp;</th> <!-- CODE-->
					<th style="width: 0.8cm">&nbsp;</th><!-- CANT -->
					<th style="width: 2.5cm">&nbsp;</th><!-- DESCRIPCIÓN -->
					<th style="width: 1cm">&nbsp;</th><!-- PRECIO UNITARIO -->
					<th style="width: 0.5cm">&nbsp;</th><!-- VENTAS NO SUJETAS -->
					<th style="width: 0.5cm">&nbsp;</th><!-- VENTAS EXENTAS -->
					<th style="width: 1cm">&nbsp;</th><!-- VENTAS AFECTA -->
				</tr>
			</thead>
			<tbody>
				@forelse($receipt_details->lines as $line)
				<tr>
					<td class="text-center cutter">{{ $line['code'] }}</td>
					<td class="text-center">{{ $line['quantity'] }}</td>
					<td class="cutter" style="padding-left: 0.3cm;">
						{{ $line['name']}} {{$line['variation'] }}
						@if(!empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif 
						@if(!empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
						@if(!empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif
					</td>
					<td class="text-center">
						<span class="display_currency" data-currency_symbol="true">
						{{ $line['unit_price_exc'] }}
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td class="text-center">
						<span class="display_currency" data-currency_symbol="true">
						{{ $line['line_total_exc_tax'] }}
					</td>
				</tr>
				@empty
				<tr>
					<td colspan="7">&nbsp;</td>
				</tr>
				@endforelse
			</tbody>
		</table>
	</div>
	@if ($receipt_details->discount_amount > 0)
	<div id="extra-details">
		<div id="discount-text">
			DESCUENTO (-) {{ $receipt_details->discount_percent }}
		</div>
		<div id="discount-amount">
			<span class="display_currency" data-currency_symbol="true">
				{{ $receipt_details->discount_amount }}
			</span>
		</div>
	</div>
	@endif
	<div id="footer">
		<table>
			<tr>
				<td style="width: 3.5cm;">&nbsp;</td>
				<td class="text-right">
					<span class="display_currency" data-currency_symbol="true">
						{{ $receipt_details->total_before_tax }}
					</span>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td class="text-right">
					<span class="display_currency" data-currency_symbol="true">
						{{ $receipt_details->tax_amount }}
					</span>
				</td>
			</tr>
			<tr>
				<td rowspan="2" style="vertical-align: top;">{{ $receipt_details->total_letters }}</td>
				<td class="text-right">
					<span class="display_currency" data-currency_symbol="true">
					@if ($receipt_details->withheld)
						{{ round($receipt_details->total + $receipt_details->withheld, 2) }}
					@else
						{{ $receipt_details->total }}
					@endif
					</span>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td> <!-- IVA Percibido -->
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td class="text-right"> <!-- IVA retenido -->
					<span class="display_currency" data-currency_symbol="true">
						{{ $receipt_details->withheld ?? '&nbsp;' }}
					</span>
				</td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr> <!-- Suma Vta no sujeta -->
			<tr><td colspan="2">&nbsp;</td></tr> <!-- Suma Vta exenta -->
			<tr>
				<td>&nbsp;</td>
				<td class="text-right">
					<span class="display_currency" data-currency_symbol="true">
						{{ $receipt_details->total }}
					</span>
				</td>
			</tr>
		</table>
	</div>
</div>

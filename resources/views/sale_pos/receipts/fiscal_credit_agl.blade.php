<style>
	div#container{ font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif }
	div#container { width: 11.5cm; margin: 0cm 0.7cm 0cm 0.7cm; font-size: 9px; margin-left: 0.7cm;}
	div#head { height: 4.2cm; width: 100%; }
	div#container table{ width: 100%; }
	div#container table td { vertical-align: middle; }
	div#header { height: 4cm; width: 100%; }
	div#header div#header-left{ width: 58%; height: inherit; display: inline-block; vertical-align: top; }
	div#header div#header-right{ width: 41%; height: inherit; display: inline-block; }
	div#header div#header-right div { padding-top: 0.1cm; }
	div#header div#header-left div, div#header div#header-right div { width: 100%; vertical-align: middle; }
	div#details { height: 7.8cm; width: 100%; }
	div#details table#sell_lines thead tr { height: 0.7cm; }
	div#details table#sell_lines tbody tr  { height: 0.68cm; }
	div#details table#sell_lines tbody td { padding-left: -0.3cm; }
	@if ($receipt_details->discount_amount > 0)
	div#extra-details { position: relative; }
	div#extra-details #discount-text { position: absolute; left: 7.4cm; top: 0.1cm; padding-left: 0.2cm; }
	div#extra-details #discount-amount { position: absolute; left: 10.5cm; top: 0.1cm; width: 1.5cm; text-align: center; }
	@endif
	div#footer table tr { height: 0.45cm; }
	.cutter { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
	.text-center { text-align: center; }
	div#footer{ width: 100%; height: 3.8cm; }
</style>

<div id="container">
	<div id="head">
	</div>
	<div id="header">
		<div id="header-left">
			<div class="cutter" style="height: 1.05cm; padding-left: 1.5cm;  vertical-align: top; padding-top:0.1cm;">{{ $receipt_details->customer_name }}</div>
			<div class="cutter" style="height: 1.1cm; padding-left: 1.5cm; vertical-align: top; padding-top:0.1cm;">{{ $receipt_details->customer_location }}</div>
			<div style="height: 0.55cm; padding-left: 1.9cm;">{{ $receipt_details->customer_city }}</div>
			<div style="height: 0.55cm; padding-left: 2.5cm;">&nbsp;</div>
			<div style="height: 0.6cm; padding-left: 4cm;">&nbsp;</div>
		</div>
		<div id="header-right">
			<div style="height: 0.55cm; padding-left: 1.2cm; display: table-cell; vertical-align: middle;">{{ @format_date($receipt_details->invoice_date) }}</div>
			<div style="height: 0.55cm; padding-left: 1.7cm;">{{ $receipt_details->customer_tax_number }}</div>
			<div style="height: 0.55cm; padding-left: 1cm;">{{ $receipt_details->customer_nit }}</div>
			<div class="cutter" style="height: 1.05cm; padding-left: 1cm;">{{ $receipt_details->customer_business_activity }}</div>
			<div class="cutter" style="height: 0.55cm; padding-left: 2cm;">{{ $receipt_details->customer_state }}</div>
			<div style="height: 0.6cm; padding-left: 3cm;">{{ __("messages." . $receipt_details->payment_condition ) }}</div>
		</div>
	</div>
	<div id="details">
		<table id="sell_lines" style="table-layout: fixed;">
			<thead>
				<tr>
					<th style="width: 1.15cm">&nbsp;</th><!-- CANT -->
					<th style="width: 5.0cm">&nbsp;</th><!-- DESCRIPCIÓN -->
					<th style="width: 1.1cm">&nbsp;</th><!-- PRECIO UNITARIO -->
					<th style="width: 0.9cm">&nbsp;</th><!-- VENTAS NO SUJETAS -->
					<th style="width: 1.0cm">&nbsp;</th><!-- VENTAS EXENTAS -->
					<th style="width: 1.7cm">&nbsp;</th><!-- VENTAS AFECTA -->
				</tr>
			</thead>
			<tbody>
				@forelse($receipt_details->lines as $line)
				<tr>
					<td class="text-center">{{ $line['quantity'] }}</td>
					<td class="cutter" style="padding-left: 0.2cm;">
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
				<td rowspan="3" style="width: 6.5cm;text-align: center; padding-left: 1cm; padding-top: 0.8cm; vertical-align: top;">
					{{ $receipt_details->total_letters }}
				</td>
				<td style="width: 1.1cm">&nbsp;</td>
				<td style="width: 1.03cm">&nbsp;</td><!-- Suma Vta no sujeta -->
				<td style="width: 1.03cm">&nbsp;</td><!-- Suma Vta exenta -->
				<td style="width: 1.3cm;">
					<span class="display_currency" data-currency_symbol="true">
					{{ $receipt_details->total_before_tax }}
					</span>
				</td>
			</tr>
			<tr><!-- 13% IVA -->
				<td rowspan="2" colspan="3" style="width: 3.62cm"></td>
				<td style="height: 0.5cm;">
					<span class="display_currency" data-currency_symbol="true">
					{{ $receipt_details->tax_amount }}
					</span>
				</td>
			</tr>
			<tr><!-- Subtotal -->
				<td style="height: 0.5cm;">
					<span class="display_currency" data-currency_symbol="true">
					@if ($receipt_details->withheld)
						{{ round($receipt_details->total + $receipt_details->withheld, 2) }}
					@else
						{{ $receipt_details->total }}
					@endif
					</span>
				</td>
			</tr>
			<tr><!-- IVA Retenido -->
				<td colspan="4">&nbsp;</td>
				<td>
					<span class="display_currency" data-currency_symbol="true">
						{{ $receipt_details->withheld ?? '' }}
					</span>
				</td>
			</tr>
			<tr><!-- IVA Percibido -->
				<td rowspan="4" colspan="4">&nbsp;</td>
				<td style="height: 0.5cm;">&nbsp;</td>
			</tr>
			<tr><!-- Vta no sujeta -->
				<td>&nbsp;</td>
			</tr>
			<tr><!-- Vta exenta -->
				<td>&nbsp;</td>
			</tr>
			<tr><!-- Vta total -->
				<td>
					<span class="display_currency" data-currency_symbol="true">
					{{ $receipt_details->total }}
					</span>
				</td>
			</tr>
		</table>
	</div>
</div>

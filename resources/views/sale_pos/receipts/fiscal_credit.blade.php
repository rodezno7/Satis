<style>
	div#container{  }
	div#container { width: 14cm; margin: 0cm 1.2cm 0cm 1.2cm; font-size: 9px; }
	div#head { height: 4.5cm; width: 100%; }
	div#container table{ width: 100%; }
	div#container table td { vertical-align: middle; }
	div#header { height: 3.7cm; width: 100%; }
	div#header div#header-left{ width: 54.5%; height: inherit; display: inline-block; vertical-align: top; }
	div#header div#header-right{ width: 44.5%; height: inherit; display: inline-block; }
	div#header div#header-right div { padding-top: 0.1cm; }
	div#header div#header-left div, div#header div#header-right div { width: 100%; vertical-align: middle; }
	@if ($receipt_details->discount_amount > 0)
	div#details { height: 6.1cm; }
	div#extra-details { position: relative; height: 0.8cm; }
	div#extra-details #discount-text { position: absolute; left: 8.42cm; top: 0.1cm; padding-left: 0.2cm; }
	div#extra-details #discount-amount { position: absolute; left: 11.85cm; top: 0.1cm; width: 2.15cm; text-align: center; }
	@else
	div#details { height: 6.9cm; }
	@endif
	div#details table#sell_lines thead tr { height: 0.9cm; }
	div#details table#sell_lines tbody tr  { height: 0.5cm; }
	div#details table#sell_lines tbody td { padding-left: 0.1cm; padding-top: 0.1cm; }
	div#footer table td { padding-left: 0.3cm; }
	div#footer table tr { height: 0.66cm; }
	.cutter { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
	.text-center { text-align: center; }
</style>

<div id="container">
	<div id="head">
		<div style="position: fixed; left: 8.2cm; top: 3cm;">{{ $receipt_details->correlative }}</div>
	</div>
	<div id="header">
		<div id="header-left">
			<div style="height: 1cm; padding-left: 1.2cm; vertical-align: top; padding-top:0.1cm;">{{ $receipt_details->customer_name }}</div>
			<div style="height: 0.88cm; padding-left: 1.8cm; vertical-align: top; padding-top:0.1cm;">{{ $receipt_details->customer_location }}</div>
			<div style="height: 0.4cm; padding-left: 1.7cm;">{{ $receipt_details->customer_city }}</div>
			<div class="cutter" style="height: 0.4cm; padding-left: 2.3cm;">{{ $receipt_details->customer_state }}</div>
			<div style="height: 0.4cm; padding-left: 4cm;">&nbsp;</div>
			<div style="height: 0.4cm; padding-left: 4.7cm;">&nbsp;</div>
		</div>
		<div id="header-right">
			<div style="height: 0.6cm; padding-left: 1.5cm; display: table-cell; vertical-align: middle; ">{{ date("d",strtotime($receipt_details->invoice_date)) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . date("m",strtotime($receipt_details->invoice_date)) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . date("Y",strtotime($receipt_details->invoice_date)) }}</div>
			<div style="height: 0.5cm; padding-left: 2.2cm; ">{{ $receipt_details->customer_tax_number }}</div>
			<div style="height: 0.5cm; padding-left: 1.8cm;">{{ $receipt_details->customer_nit }}</div>
			<div style="height: 0.9cm; padding-left: 1.2cm;">{{ $receipt_details->customer_business_activity }}</div>
			<div style="height: 0.45cm; padding-left: 3.3cm;">{{ __("messages." . $receipt_details->payment_condition ) }}</div>
			<div class="cutter" style="height: 0.45cm; padding-left: 3cm;">{{ $receipt_details->customer_employee_name }}</div>
		</div>
	</div>
	<div id="details">
		<table id="sell_lines">
			<thead>
				<tr>
					<th style="width: 1cm">&nbsp;</th><!-- CANT -->
					<th style="width: 7.16cm">&nbsp;</th><!-- DESCRIPCIÓN -->
					<th style="width: 1.43cm">&nbsp;</th><!-- PRECIO UNITARIO -->
					<th style="width: 1.03cm">&nbsp;</th><!-- VENTAS NO SUJETAS -->
					<th style="width: 1.03cm">&nbsp;</th><!-- VENTAS EXENTAS -->
					<th style="width: 2.15cm">&nbsp;</th><!-- VENTAS AFECTA -->
				</tr>
			</thead>
			<tbody>
				@forelse($receipt_details->lines as $line)
				<tr>
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
					<td colspan="6">&nbsp;</td>
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
				<td rowspan="3" style="width: 8.21cm; padding-left: 1cm; padding-top: 0.1cm; vertical-align: top;">
					{{ $receipt_details->total_letters }}
				</td>
				<td style="width: 1.43cm">&nbsp;</td>
				<td style="width: 1.03cm">&nbsp;</td><!-- Suma Vta no sujeta -->
				<td style="width: 1.03cm">&nbsp;</td><!-- Suma Vta exenta -->
				<td style="width: 2.15cm">
					<span class="display_currency" data-currency_symbol="true">
					{{ $receipt_details->total_before_tax }}
				</td>
			</tr>
			<tr><!-- 13% IVA -->
				<td rowspan="2" colspan="3" style="width: 3.62cm"></td>
				<td>
					<span class="display_currency" data-currency_symbol="true">
					{{ $receipt_details->tax_amount }}
				</td>
			</tr>
			<tr><!-- Subtotal -->
				<td>
					<span class="display_currency" data-currency_symbol="true">
					@if ($receipt_details->withheld)
						{{ round($receipt_details->total + $receipt_details->withheld, 2) }}
					@else
						{{ $receipt_details->total }}
					@endif
				</td>
			</tr>
			<tr><!-- IVA Retenido -->
				<td rowspan="4" colspan="4">&nbsp;</td>
				<td>
					@if ($receipt_details->withheld)
						<span class="display_currency" data-currency_symbol="true">
						{{ $receipt_details->withheld }}
					@else
						&nbsp;
					@endif
				</td>
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
				</td>
			</tr>
		</table>
	</div>
</div>
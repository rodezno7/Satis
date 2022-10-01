<style>
	div#container{  }
	div#container { width: 14cm; margin: 0cm 1.2cm 0cm 1.2cm; font-size: 9px; }
	div#head { height: 4.2cm; width: 100%; }
	div#container table{ width: 100%; }
	div#container table td { vertical-align: middle; }
	div#header { height: 3.6cm; width: 100%; }
	div#header div#header-left{ width: 54.5%; height: inherit; display: inline-block; vertical-align: top; }
	div#header div#header-right{ width: 44.5%; height: inherit; display: inline-block; }
	div#header div#header-left div, div#header div#header-right div { width: 100%; vertical-align: middle; padding-top: 0.1cm; }
	@if ($receipt_details->discount_amount > 0)
	div#details { height: 7.2cm; }
	div#extra-details { position: relative; height: 0.8cm; }
	div#extra-details #discount-text { position: absolute; left: 10.37cm; top: 0.1cm; padding-left: 0.2cm; }
	div#extra-details #discount-amount { position: absolute; left: 11.77cm; top: 0.1cm; width: 2.12cm; text-align: right; }
	@else
	div#details { height: 8cm; }
	@endif
	div#details table#sell_lines thead tr { height: 1cm; }
	div#details table#sell_lines tbody tr  { height: 0.5cm; }
	div#details table#sell_lines tbody td { padding-left: 0.1cm; padding-top: 0.1cm; }
	div#footer table td { padding-left: 0.1cm; }
	div#footer table tr { height: 1.13cm; }
	.cutter { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
	.text-center { text-align: center; }
</style>

<div id="container">
	<div id="head"></div>
	<div id="header">
		<div id="header-left">
			<div style="height: 1.06cm; padding-left: 1.5cm; vertical-align: top; padding-top:0.1cm;">{{ $receipt_details->customer_name }}</div>
			<div class="cutter" style="height: 0.575cm; padding-left: 1.8cm; vertical-align: top; padding-top:0.1cm;">{{ $receipt_details->customer_location }}</div>
			<div style="height: 0.575cm; padding-left: 1.8cm;">{{ $receipt_details->customer_city }}</div>
			<div style="height: 0.575cm; padding-left: 2.3cm;">{{ $receipt_details->customer_state }}</div>
			<div style="height: 0.575cm; padding-left: 4.3cm;"></div>
		</div>
		<div id="header-right">
			<div style="height: 0.5cm; padding-left: 1.5cm; display: table-cell; vertical-align: middle; ">{{ date("d",strtotime($receipt_details->invoice_date)) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . date("m",strtotime($receipt_details->invoice_date)) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . date("Y",strtotime($receipt_details->invoice_date)) }}</div>
			<div style="height: 0.578cm; padding-left: 2cm; ">{{ $receipt_details->customer_tax_number }}</div>
			<div style="height: 0.578cm; padding-left: 1.8cm;">{{ $receipt_details->customer_nit }}</div>
			<div class="cutter" style="height: 0.578cm; padding-left: 1cm;">{{ $receipt_details->customer_business_activity }}</div>
			<div style="height: 1.156cm; padding-left: 3.5cm;">{{ __("messages." . $receipt_details->payment_condition ) }}</div> <!-- Condiciones de pago -->
		</div>
	</div>
	<div id="details">
		<table id="sell_lines">
			<thead>
				<tr>
					<th style="width: 1.1cm">&nbsp;</th><!-- CANT -->
					<th style="width: 9.27cm">&nbsp;</th><!-- DESCRIPCIÓN -->
					<th style="width: 1.4cm">&nbsp;</th><!-- PRECIO UNITARIO -->
					<th style="width: 2.12cm">&nbsp;</th><!-- VENTAS AFECTA -->
				</tr>
			</thead>
			<tbody>
				@forelse($receipt_details->lines as $line)
				<tr>
					<td class="text-center">{{ $line['quantity'] }}</td>
					<td class="cutter">
						{{ $line['name']}} {{$line['variation'] }}
						@if(!empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif 
						@if(!empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
						@if(!empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif
					</td>
					<td class="text-center">
						<span class="display_currency" data-currency_symbol="true">
						{{ $line['unit_price_exc'] }}
					</td>
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
				<td style="width: 8.88cm; padding-left: 1cm; padding-top: 0.5cm; vertical-align: top;">
					{{ $receipt_details->total_letters }}
				</td>
				<td rowspan="3" style="width: 2.89cm">&nbsp;</td>
				<td style="width: 2.11cm">
					{{ $receipt_details->total_before_tax }}
				</td>
			</tr>
			<tr><!-- 13% IVA -->
				<td>&nbsp;</td>
				<td>
					{{ $receipt_details->tax_amount }}
				</td>
			</tr>
			<tr><!-- Vta total -->
				<td>&nbsp;</td>
				<td>
					{{ $receipt_details->total }}
				</td>
			</tr>
		</table>
	</div>
</div>
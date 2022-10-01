<style>
	div#container div{ }
	div#container { width: 17cm; margin: 0cm 1cm 0.6cm 1.2cm; font-size: 10px; }
	div#head { height: 3.87cm; width: 100%; }
	div#container table{ width: 100%; }
	div#container table td { vertical-align: middle; }
	div#header { height: 1.5cm; width: 100%; }
	div#header table#header tr{ height: 0.5cm; }
	div#details { height: 4.6cm; }
	div#details table#sell_lines thead tr { height: 0.6cm; }
	div#details table#sell_lines tbody tr  { height: 0.5cm; }
	div#details table#sell_lines tbody td { padding-left: 0.1cm; }
	div#footer table td { padding-left: 0.1cm; }
	div#footer table tr { height: 0.5cm; }
</style>

<div id="container">
	<div id="head"></div>
	<div id="header">
		<table id="header">
			<tr>
				<td style="width: 11cm; padding-left: 1.7cm">{{ $receipt_details->customer_name }}</td>
				<td style="width: 6cm; padding-left: 2cm">{{ @format_date($receipt_details->invoice_date) }}</td>
			</tr>
			<tr>
				<td colspan="2" style="width: 100%; padding-left: 1.8cm">{{ $receipt_details->customer_location }}</td>
			</tr>
			<tr>
				<td style="width: 11cm; padding-left: 3.5cm">{{ $receipt_details->customer_employee_name }}</td>
				<td style="width: 6cm; padding-left: 2cm">{{ $receipt_details->customer_nit }}</td>
			</tr>
		</table>
	</div>
	<div id="details">
		<table id="sell_lines">
			<thead>
				<tr>
					<th style="width: 1cm">&nbsp;</th><!-- CANT -->
					<th style="width: 9.8cm">&nbsp;</th><!-- DESCRIPCIÓN -->
					<th style="width: 1.95cm">&nbsp;</th><!-- PRECIO UNITARIO -->
					<th style="width: 1cm">&nbsp;</th><!-- VENTAS NO SUJETAS -->
					<th style="width: 1cm">&nbsp;</th><!-- VENTAS EXENTAS -->
					<th style="width: 2.3cm">&nbsp;</th><!-- VENTAS AFECTA -->
				</tr>
			</thead>
			<tbody>
				@forelse($receipt_details->lines as $line)
				<tr>
					<td>{{ $line['quantity'] }}</td>
					<td>
						{{ $line['name']}} {{$line['variation'] }}
						@if(!empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif 
						@if(!empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
						@if(!empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif 
					</td>
					<td>
						<span class="display_currency" data-currency_symbol="true">
						{{ $line['unit_price'] }}
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>
						<span class="display_currency" data-currency_symbol="true">
						{{ $line['line_total'] }}
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
	<div id="footer">
		<table>
			<tr>
				<td rowspan="3" style="width: 9.5cm; padding-left: 1cm; padding-top: 0.1cm; vertical-align: top;">
					{{ $receipt_details->total_letters }}
				</td>
				<td rowspan="3" style="width: 5.15cm">&nbsp;</td>
				<td style="width: 2.3cm">
					@if ($receipt_details->withheld)
						<span class="display_currency" data-currency_symbol="true">
						{{ number_format( $receipt_details->total + $receipt_details->withheld, 2) }}
					@else
						<span class="display_currency" data-currency_symbol="true">
						{{ $receipt_details->total }}
					@endif
				</td>
			</tr>
			<tr>
				<td>
					@if ($receipt_details->withheld)
						<span class="display_currency" data-currency_symbol="true">
						{{ $receipt_details->withheld }}
					@else
						&nbsp;
					@endif
				</td>
			</tr>
			<tr>
				<td>
					<span class="display_currency" data-currency_symbol="true">
					{{ $receipt_details->total }}
				</td>
			</tr>
			<tr>
				<td rowspan="3">&nbsp;</td>
				<td rowspan="3">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<span class="display_currency" data-currency_symbol="true">
					{{ $receipt_details->total }}
				</td>
			</tr>
		</table>
	</div>
</div>
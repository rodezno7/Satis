<style>
	/* div#container { font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif; width: 12cm; margin: 0cm 1cm 0cm 1cm; font-size: 10pt; } */
	div#container { font-family: 'Courier New', Courier, monospace; width: 12cm; margin: 0cm 1cm 0cm 1cm; font-size: 10px; }
	div#head { height: 4.3cm; width: 100%; }
	div#container table{ width: 100%; }
	div#container table td { vertical-align: middle;}
	div#details { height: 14cm; }
	div#container table#header{ width: 100%; }
	div#details table#sell_lines { table-layout: fixed; }
	div#details table#sell_lines thead tr { height: 0.6cm; }
	div#details table#sell_lines tbody tr  { height: 0.5cm; }
	div#details table#sell_lines tbody td { padding-left: 0.1cm; padding-top: 0.05cm; }
	div#footer table td { padding-left: 0.1cm; }
	div#footer table tr { height: 0.5cm; }
	.cutter { white-space: normal; overflow: hidden; }
</style>

<div id="container">
	<div id="head"></div>
	<div id="header">
		<table id="header">
			<tr>
				<td colspan="2" style="width: 100%; vertical-align: middle; text-align: center;">{{ $receipt_details->correlative }}</td>
			</tr>
			<tr>
				<td style="width: 7cm; vertical-align: top;" rowspan="2">Cliente: {{ $receipt_details->customer_name }}</td>
				<td style="width: 5cm; text-align: right;">Fecha: {{ @format_date($receipt_details->invoice_date) }}</td>
			</tr>
			<tr>
				<td style="width: 5cm; text-align: right;">DUI/NIT: {{ $receipt_details->customer_nit }}</td>
			</tr>
			<tr>
				<td style="width: 7cm;">Dirección: {{ $receipt_details->customer_location }}</td>
				<td style="width: 5cm; text-align: right;">Condiciones: {{ __("messages.". $receipt_details->payment_condition) }}</td>
			</tr>
			<tr style="border-bottom: 1px dotted #000;">
				<td style="width: 7cm;">Vta/Cta de: {{ $receipt_details->customer_employee_name }}</td>
				<td style="width: 5cm; text-align: right;">&nbsp;</td>
			</tr>
		</table>
	</div>
	<div id="details">
		<table id="sell_lines">
			<thead>
				<tr>
					<th style="width: 7.5cm; text-align: center;">Descripción</th><!-- DESCRIPCIÓN -->
					<th style="width: 1cm; text-align: center;">Cant.</th><!-- CANT -->
					<th style="width: 1.5cm; text-align: center;">Precio</th><!-- PRECIO UNITARIO -->
					<th style="width: 2.0cm; text-align: center;">Gravado</th><!-- VENTAS AFECTA -->
				</tr>
			</thead>
			<tbody>
				@forelse($receipt_details->lines as $line)
				<tr>
					<td class="cutter">
						{{ $line['name']}} {{$line['variation'] }}
						@if(!empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif 
						@if(!empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
						@if(!empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif
					</td>
					<td style="text-align: right;">{{ $line['quantity'] }}</td>
					<td style="text-align: right;">
						<span class="display_currency" data-currency_symbol="true">
						{{ $line['unit_price'] }}
					</td>
					<td style="text-align: right;">
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
			<tr style="border-top: 1px dotted #000;">
				<td style="width: 8cm;" colspan="2">
					{{ $receipt_details->total_letters }}
				</td>
				<td style="width: 2.5cm;">Sumas:</td>
				<td style="width: 1.5cm; text-align: right;">
					<span class="display_currency" data-currency_symbol="true">
					{{ $receipt_details->total_before_tax }}
				</td>
			</tr>
			<tr>
				<td style="width: 8cm;" colspan="2">Llenar si la operación es igual o mayor a $200</td>
				<td style="width: 2.5cm;">Descuento:</td>
				<td style="width: 1.5cm; text-align: right;">
					@if ($receipt_details->discount_amount)
						<span class="display_currency" data-currency_symbol="true">
						{{ $receipt_details->discount_amount }}
					@else
						&nbsp;
					@endif
				</td>
			</tr>
			<tr>
				<td style="width: 4cm;">Entregado por:</td>
				<td style="width: 4cm;">Recibido por:</td>
				<td style="width: 2cm;">(-)IVA Rent.</td>
				<td style="width: 2cm; text-align: right;">
					@if ($receipt_details->withheld)
						<span class="display_currency" data-currency_symbol="true">
						{{ $receipt_details->withheld }}
					@else
						&nbsp;
					@endif
				</td>
			</tr>
			<tr>
				<td style="width: 4cm;">Nombre:</td>
				<td style="width: 4cm;">Nombre:</td>
				<td style="width: 2cm;">Subtotal:</td>
				<td style="width: 2cm; text-align: right;">
					<span class="display_currency" data-currency_symbol="true">
					{{ $receipt_details->total }}
				</td>
			</tr>
			<tr>
				<td style="width: 4cm;">DUI:</td>
				<td style="width: 4cm;">DUI:</td>
				<td style="width: 2cm;">Vta Excent.</td>
				<td style="width: 2cm;">&nbsp;</td>
			</tr>
			<tr>
				<td style="width: 4cm;">Firma:</td>
				<td style="width: 4cm;">Firma:</td>
				<td style="width: 2cm;">Vta Total.</td>
				<td style="width: 2cm; text-align: right;">
					<span class="display_currency" data-currency_symbol="true">
					{{ $receipt_details->total }}
				</td>
			</tr>
			<tr>
				<td style="width: 8cm;" colspan="2">G: Gravado, E: Excento</td>
				<td style="width: 4cm;" colspan="2">&nbsp;</td>
			</tr>
		</table>
	</div>
</div>
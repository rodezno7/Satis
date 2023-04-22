<style>
	div#container { width: 100%;  font-size: 7.5pt;
		font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif; }
	div#container { width: 13cm; margin: 0cm 0.7cm 0cm 0.7cm; }
	div#head { height: 3.3cm; width: 100%; }
	div#header { height: 1.2cm; width: 100%; }
	div#container table{ width: 100%; }
	div#container table td { vertical-align: middle; }
	div#details { height: 7.85cm; }
	div#container div#header table { width: 100%; }
	div#container div#header table td { height: 0.4cm; }
	div#details table#sell_lines,
	div#header table { table-layout: fixed; }
	div#details table#sell_lines thead th { height: 0.78cm; }
	div#details table#sell_lines tbody td  { height: 0.57cm; padding-left: 0.1cm; }
	div#footer { height: 2.4cm; }
	div#footer table td { padding-left: 0.1cm; }
	div#footer table tr { height: 0.4cm; }
	.cutter { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>
<div id="container">
	<div id="head"></div>
	<div id="header">
		<table id="header">
			<tr>
				<td style="padding-left: 1.5cm; width: 9.5cm; vertical-align: middle;" class="cutter">{{ $receipt_details->customer_name }}</td>
				<td style="padding-left: 1.2cm; width: 3.5cm; vertical-align: middle;">{{ @format_date($receipt_details->invoice_date) }}</td>
			</tr>
			<tr>
				<td colspan="2" style="padding-left: 1.5cm; vertical-align: middle;" class="cutter">{{ $receipt_details->customer_location }}</td>
			</tr>
			<tr>
				<td style="padding-left: 2.5cm; vertical-align: middle;" class="cutter">{{ $receipt_details->seller_name }}</td>
				<td style="padding-left: 1.5cm; vertical-align: middle;">{{ $receipt_details->customer_nit }}</td>
			</tr>
		</table>
	</div>
	<div id="details">
		<table id="sell_lines">
			<thead>
				<tr>
					<th style="width: 1.1cm; text-align: center;">&nbsp;</th><!-- CANT -->
					<th style="width: 6cm; text-align: center;">&nbsp;</th><!-- DESCRIPCIÓN -->
					<th style="width: 1.3cm; text-align: center;">&nbsp;</th><!-- PRECIO UNITARIO -->
					<th style="width: 1.4cm; text-align: center;">&nbsp;</th><!-- VTA. NO SUJETA -->
					<th style="width: 1.3cm; text-align: center;">&nbsp;</th><!-- VTA. EXENTA -->
					<th style="width: 1.7cm; text-align: center;">&nbsp;</th><!-- VTAS. GRABADAS -->
				</tr>
			</thead>
			<tbody>
				@forelse($receipt_details->lines as $line)
				<tr>
					<td>{{ $line['quantity'] }}</td>
					<td class="cutter">
						{{ $line['name']}} {{$line['variation'] }}
						@if(!empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif 
						@if(!empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
						@if(!empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif 
					</td>
					<td>
						<span class="display_currency" data-currency_symbol="false">
							{{ $line['unit_price'] }}
						</span>
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>
						<span class="display_currency" data-currency_symbol="false">
							{{ $line['line_total'] }}
						</span>
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
	<div id="footer">
		<table>
			<tr>
				<td style="width: 5.9cm; padding-left: 1cm;" rowspan="2">{{ $receipt_details->total_letters }}</td>
				<td style="width: 2cm;" rowspan="2">&nbsp;</td>
				<td style="width: 1.3cm;">
					<span class="display_currency" data-currency_symbol="false">
						{{ $receipt_details->total_before_tax }}
					</span>
				</td>	
			</tr>
			<tr>
				<td>
					@if ($receipt_details->withheld)
						<span class="display_currency" data-currency_symbol="false">
							{{ $receipt_details->withheld }}
						</span>
					@else
						&nbsp;
					@endif
				</td>
			</tr>
			<tr>
				<td rowspan="4">&nbsp;</td>
				<td rowspan="4">&nbsp;</td>
				<td>
					<span class="display_currency" data-currency_symbol="false">
						{{ $receipt_details->total }}
					</span>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td> <!-- VTA. NO SUJETA -->
			</tr>
			<tr>
				<td>&nbsp;</td> <!-- VTA. EXENTA -->
			</tr>
			<tr>
				<td style="vertical-align: top;">
					<span class="display_currency" data-currency_symbol="false">
						{{ $receipt_details->total }}
					</span>
				</td>
			</tr>
		</table>
	</div>
</div>
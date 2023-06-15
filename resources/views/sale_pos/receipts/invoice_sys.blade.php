<style>
	div#main { width: 100%;  font-size: 7.5pt;
		font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif; }
	div#container { width: 17.5cm; margin: 0 1.9cm 0 1.9cm; }
	div#head { height: 4.1cm; width: 100%; }
	div#header { height: 2.5cm; width: 100%; }
	div#container table{ width: 100%; }
	div#container table td { vertical-align: middle; }
	div#details { height: 9.2cm; }
	div#container div#header table { width: 100%; }
	div#container div#header table td { height: 0.5cm; }
	div#details table#sell_lines,
	div#header table { table-layout: fixed; }
	div#details table#sell_lines thead th { height: 0.6cm; }
	div#details table#sell_lines tbody td  { height: 0.4cm; }
	div#details table#sell_lines tbody td { padding-left: 0.1cm; }
    div#footer { height: 3.85cm; }
	div#footer table td { padding-left: 0.1cm; }
	div#footer table tr { height: 0.6cm; }
	.cutter { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>

<div id="main">
	<div id="container">
		<div id="head"></div>
		<div id="header">
			<table id="header">
				<tr>
					<td style="width: 12.2cm; height: 0.7cm;">&nbsp;</td>
					<td style="height: 0.7cm;">{{ @format_date($receipt_details->invoice_date) }}</td>
				</tr>
				<tr>
					<td style="padding-left: 1.3cm; height: 0.5cm;" class="cutter">{{ $receipt_details->customer_name }}</td>
                    <td class="cutter" style="padding-left: 1.7cm; height: 0.5cm;">{{ $receipt_details->customer_phone }}</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-left: 1.4cm; height: 0.5cm;" class="cutter">{{ $receipt_details->customer_location }}</td>
				</tr>
				<tr>
					<td style="padding-left: 1.4cm; height: 0.55cm;" class="cutter">{{ $receipt_details->customer_dui ?? $receipt_details->customer_nit }}</td>
					<td style="padding-left: -3.5cm; height: 0.55cm;" class="cutter">{{ $receipt_details->customer_employee_name }}</td>
				</tr>
			</table>
		</div>
		<div id="details">
			<table id="sell_lines">
				<thead>
					<tr>
						<th style="width: 1.4cm; text-align: center;">&nbsp;</th><!-- CÓDIGO -->
						<th style="width: 9.6cm; text-align: center;">&nbsp;</th><!-- DESCRIPCIÓN -->
						<th style="width: 1.65cm; text-align: center;">&nbsp;</th><!-- PRECIO UNITARIO -->
						<th style="width: 1.5cm; text-align: center;">&nbsp;</th><!-- VTA. NO SUJETA -->
						<th style="width: 1.5cm; text-align: center;">&nbsp;</th><!-- VTA. EXENTA -->
						<th style="width: 1.8cm; text-align: center;">&nbsp;</th><!-- VTAS. GRABADAS -->
					</tr>
				</thead>
				<tbody>
					@forelse($receipt_details->lines as $line)
					<tr>
						<td style="text-align: right;">{{ $line['quantity'] }}</td>
						<td class="cutter" style="padding-left: 0.3cm;">
							{{ $line['name']}} {{$line['variation'] }}
							@if(!empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif 
							@if(!empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
							@if(!empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif
						</td>
						<td style="text-align: right;">
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['unit_price'] }}
							</span>
						</td>
						<td>&nbsp;</td>
						<td style="text-align: right;">
							@if ($receipt_details->is_exempt == 1)
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['line_total'] }}
							</span>
							@else
							&nbsp;
							@endif
						</td>
						<td style="text-align: right;">
							@if ($receipt_details->is_exempt == 0)
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['line_total'] }}
							</span>
							@else
							&nbsp;
							@endif
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
					<td style="width: 5.9cm; padding-left: 1cm; vertical-align: top;" rowspan="2">{{ $receipt_details->total_letters }}</td>
					<td style="width: 2cm;" rowspan="2">&nbsp;</td>
					<td style="width: 1.8cm; text-align: right;">
						<span class="display_currency" data-currency_symbol="false">
							{{ $receipt_details->total_before_tax }}
						</span>
					</td>	
				</tr>
				<tr>
					<td style="text-align: right;">
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
					<td style="text-align: right; vertical-align: middle;">
						@if ($receipt_details->is_exempt == 0)
						<span class="display_currency" data-currency_symbol="false">
							{{ $receipt_details->total }}
						</span>
						@else
						&nbsp;
						@endif
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td> <!-- VTA. NO SUJETA -->
				</tr>
				<tr>
					<td style="text-align: right;">
						@if ($receipt_details->is_exempt == 1)
						<span class="display_currency" data-currency_symbol="false">
							{{ $receipt_details->total }}
						</span>
						@else
						&nbsp;
						@endif
					</td> <!-- VTA. EXENTA -->
				</tr>
				<tr>
					<td style="text-align: right; vertical-align: bottom;">
						<span class="display_currency" data-currency_symbol="false">
							{{ $receipt_details->total }}
						</span>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
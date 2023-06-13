<style>
	div#container{ font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif }
	div#container { width: 17.4cm; margin: 0cm 1cm 0cm 1.2cm; font-size: 9px; }
	div#head { height: 5.15cm; width: 100%; }
	div#container table{ width: 100%; }
	div#container table td { vertical-align: middle; }
	div#header { height: 4cm; width: 100%; margin-bottom: 0.15cm; }
	div#header div#header-left{ width: 60%; height: inherit; display: inline-block; vertical-align: top; }
	div#header div#header-right{ width: 39%; height: inherit; display: inline-block; }
	div#header div#header-right div { padding-top: 0.1cm; }
	div#header div#header-left div, div#header div#header-right div { width: 100%; vertical-align: middle; }
    @if (empty($receipt_details->staff_note))
        div#details { height: 7.3cm; width: 100%; }
    @else
        div#details { height: 5.3cm; width: 100%; }
        div#comment { height: 2cm; margin-left: 2cm; text-align: left; vertical-align: top; }
    @endif
	div#details table#sell_lines thead tr { height: 0.68cm; }
	div#details table#sell_lines tbody tr  { height: 0.6cm; }
	div#details table#sell_lines tbody td { padding-left: 0.2cm; }
	div#footer{ width: 100%; height: 4.05cm; }
	div#footer table tr { height: 0.5cm; }
	.cutter { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
	.text-center { text-align: center; }
</style>

<div id="container">
	<div id="head">
	</div>
	<div id="header">
		<div id="header-left">
			<div class="cutter" style="height: 0.7cm; padding-left: 1.5cm; padding-top: 0.1cm;">{{ $receipt_details->customer_name }}</div>
			<div class="cutter" style="height: 0.6cm; padding-left: 2cm; padding-top: 0.1cm;">{{ $receipt_details->customer_location }}</div>
			<div style="height: 0.6cm; padding-left: 2cm; padding-top: 0.1cm;">{{ $receipt_details->customer_city }}</div>
            <div class="cutter" style="height: 0.6cm; padding-left: 1.5cm; padding-top: 0.1cm;">{{ $receipt_details->customer_state }}</div>
		</div>
		<div id="header-right">
			<div style="height: 1.2cm; width: 1.9cm; padding-left: 1cm; padding-top: 0.5cm; display: table-cell; vertical-align: middle;">{{ date("d",strtotime($receipt_details->invoice_date)) }}</div>
			<div style="height: 1.2cm; width: 3.4cm; padding-left: 1.5cm; padding-top: 0.5cm; display: table-cell; vertical-align: middle;">{{ date("m",strtotime($receipt_details->invoice_date)) }}</div>
			<div style="height: 1.2cm; width: 1.5cm; padding-left: 0.5cm; padding-top: 0.5cm; display: table-cell; vertical-align: middle;">{{ date("Y",strtotime($receipt_details->invoice_date)) }}</div>
			<div style="height: 0.5cm; padding-left: 2cm;">{{ $receipt_details->customer_tax_number }}</div>
			<div class="cutter" style="height: 0.5cm; padding-left: 1.2cm;">{{ $receipt_details->customer_business_activity }}</div>
			<div style="height: 0.5cm; padding-left: 1.2cm;">{{ $receipt_details->customer_nit }}</div>
            <div style="height: 0.5cm;">&nbsp;</div>
			<div style="height: 0.53cm; padding-left: 4.7cm; padding-top: 0.15cm;">{{ __("messages." . $receipt_details->payment_condition ) }}</div>
		</div>
	</div>
	<div id="details">
		<table id="sell_lines" style="table-layout: fixed;">
			<thead>
				<tr>
					<th style="width: 1.9cm">&nbsp;</th><!-- CANT -->
					<th style="width: 8.5cm">&nbsp;</th><!-- DESCRIPCIÓN -->
					<th style="width: 1.9cm">&nbsp;</th><!-- PRECIO UNITARIO -->
					<th style="width: 1.5cm">&nbsp;</th><!-- VENTAS NO SUJETAS -->
					<th style="width: 1.5cm">&nbsp;</th><!-- VENTAS EXENTAS -->
					<th style="width: 2.1cm">&nbsp;</th><!-- VENTAS AFECTA -->
				</tr>
			</thead>
			<tbody>
				@forelse($receipt_details->lines as $line)
				<tr>
					<td class="text-center">{{ $line['quantity'] }}</td>
					<td style="padding-left: 0.2cm;">
						{{ $line['name']}} {{$line['variation'] }}
						@if(!empty($line['sell_line_note']))
                            <br>
                            {{ $line['sell_line_note'] }}
                        @endif
					</td>
					<td class="text-center">
						<span class="display_currency" data-currency_symbol="false">
						{{ $line['unit_price_exc'] }}
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td class="text-center">
						<span class="display_currency" data-currency_symbol="false">
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
    <div id="comment">
        {{ $receipt_details->staff_note }}
    </div>
	<div id="footer">
		<table>
			<tr>
				<td style="width: 6.5cm; padding-left: 1.2cm; vertical-align: bottom;">
					{{ $receipt_details->total_letters }}
				</td>
				<td style="width: 1.1cm">&nbsp;</td>
				<td style="width: 1.03cm">&nbsp;</td><!-- Suma Vta no sujeta -->
				<td style="width: 1.03cm">&nbsp;</td><!-- Suma Vta exenta -->
				<td style="width: 1.3cm; text-align: right; padding-right: 0.3cm;">
					<span class="display_currency" data-currency_symbol="false">
					{{ $receipt_details->total_before_tax }}
					</span>
				</td>
			</tr>
			<tr><!-- 13% IVA -->
                <td>&nbsp;</td>
				<td rowspan="2" colspan="3" style="width: 3.62cm"></td>
				<td style="height: 0.5cm; text-align: right; padding-right: 0.3cm;">
					<span class="display_currency" data-currency_symbol="false">
					{{ $receipt_details->tax_amount }}
					</span>
				</td>
			</tr>
			<tr><!-- Subtotal -->
                <td>&nbsp;</td>
				<td style="height: 0.5cm; text-align: right; padding-right: 0.3cm;">
					<span class="display_currency" data-currency_symbol="false">
					@if ($receipt_details->withheld)
						{{ round($receipt_details->total + $receipt_details->withheld, 2) }}
					@else
						{{ $receipt_details->total }}
					@endif
					</span>
				</td>
			</tr>
			<tr><!-- IVA Percibido -->
				<td colspan="3">&nbsp;</td>
				<td style="height: 0.5cm;">&nbsp;</td>
			</tr>
			<tr><!-- IVA Retenido -->
				<td colspan="4">&nbsp;</td>
				<td style="text-align: right; padding-right: 0.3cm;">
					<span class="display_currency" data-currency_symbol="false">
						{{ $receipt_details->withheld ?? '' }}
					</span>
				</td>
			</tr>
			<tr><!-- Vta no sujeta -->
				<td rowspan="3" colspan="4">&nbsp;</td>
			</tr>
			<tr><!-- Vta exenta -->
				<td>&nbsp;</td>
			</tr>
			<tr><!-- Vta total -->
				<td style="text-align: right; padding-right: 0.3cm;">
					<span class="display_currency" data-currency_symbol="false">
					{{ $receipt_details->total }}
					</span>
				</td>
			</tr>
		</table>
	</div>
</div>

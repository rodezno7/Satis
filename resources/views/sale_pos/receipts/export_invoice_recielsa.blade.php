<style>
    div#main { width: 100%;  font-size: 7.5pt; font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif; margin: 0; }
	
	div#head { height: 5.7cm; width: 100%; }
	
	div#container { width: 19.3cm; margin: 0cm 1.2cm 0cm 1.1cm; }
	div#container table { width: 100%; }
	div#container table td { vertical-align: middle; }

	div#header { height: 1.7cm; width: 100%; }
	div#header div#header-left { width: 66.4%; height: inherit; display: inline-block; vertical-align: top; }
	div#header div#header-right { width: 32.6%; height: inherit; display: inline-block; }
	div#header div#header-left div, div#header div#header-right div { width: 100%; vertical-align: middle; padding-top: 0.1cm; }

	@if (! empty($receipt_details->staff_note) || $receipt_details->discount_amount > 0)
	div#details { font-size: 7pt; height: 11.3cm; }
	div#extra-details { height: 3.0cm; position: relative; }
	div#extra-details #comment { position: absolute; left: 4.3cm; top: 0.2cm; width: 8.5cm; }
	div#extra-details #discount-text { position: absolute; left: 12.9cm; top: 0.2cm; }
	div#extra-details #discount-amount { position: absolute; left: 16.95cm; top: 0.2cm; width: 2.35cm; text-align: right; padding-right: 0.1cm; }
	@else
	div#details { font-size: 7pt; height: 14.3cm; }
	@endif

	div#details table#sell_lines { table-layout: fixed; }
	div#details table#sell_lines thead tr { height: 0.7cm; }
	div#details table#sell_lines tbody tr  { height: 0.5cm; }
	div#details table#sell_lines tbody td { padding-left: 0.2cm; vertical-align: top; }

	div#footer table td { padding-left: 0.1cm; }
	div#footer table tr { height: 1.13cm; }
	div#footer div#footer-left{ width: 66.4%; height: inherit; display: inline-block; vertical-align: top; }
	div#footer div#footer-right{ width: 32.6%; height: inherit; display: inline-block; }
	div#footer div#footer-left div, div#footer div#footer-right div { width: 100%; vertical-align: middle; padding-top: 0.1cm; }

	.cutter { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
	.break-word { word-wrap: break-word; }

	.text-center { text-align: center; }
    .text-left { text-align: left; }
    .text-right { text-align: right; }
</style>

<div id="main">
	<div id="container">
		<div id="head"></div>

		<div id="header">
			<div id="header-left">
				{{-- CLIENTE --}}
				<div style="width: initial; height: 0.5cm; padding-left: 1.4cm; vertical-align: top; padding-top: 0.1cm;">
					{{ $receipt_details->customer_name }}
				</div>
				
				{{-- DIRECCION --}}
				<div class="cutter" style="width: initial; height: 0.5cm; padding-left: 1.7cm; vertical-align: top; padding-top: 0.1cm;">
					{{ $receipt_details->customer_location }}
				</div>

				{{-- EXPORTACION A CUENTA DE --}}
				<div style="height: 0.6cm; padding-left: 4.0cm; vertical-align: top; padding-top: 0.2cm;">
					{{-- {{ $receipt_details->commission_agent }} --}}
					{{ $receipt_details->additional_notes }}
				</div>
			</div>

			<div id="header-right">
				{{-- FORMA DE PAGO --}}
				<div style="width: initial; height: 0.5cm; padding-left: 0.1cm; vertical-align: top; padding-top: 0.1cm;">
					{{ $receipt_details->payment_term }}
				</div>

				{{-- FECHA --}}
				{{-- <div style="width: initial; height: 0.5cm; padding-left: 1.3cm; vertical-align: top; padding-top: 0.1cm;"> --}}
				<div style="height: initial; padding-left: 1.3cm; vertical-align: top; padding-top: 0.1cm;">
					{{ @format_date($receipt_details->invoice_date) }}
				</div>

				{{-- RTN --}}
				<div style="height: 0.6cm; padding-left: 0.1cm; vertical-align: top; padding-top: 0.2cm;">
					@if (! empty($receipt_details->customer_tax_number))
					RTN: {{ $receipt_details->customer_tax_number }}
					@endif
				</div>
			</div>
		</div>

		<div id="details">
			<table id="sell_lines">
				<thead>
					<tr>
						{{-- CANT. --}}
						<th style="width: 2.15cm">&nbsp;</th>

						{{-- CODIGO --}}
						<th style="width: 2.85cm">&nbsp;</th>

						{{-- DESCRIPCION --}}
						<th style="width: 7.9cm">&nbsp;</th>
						
						{{-- PRECIO UNITARIO --}}
						<th style="width: 2.25cm">&nbsp;</th>

						{{-- VENTAS EXENTAS --}}
						<th style="width: 1.8cm">&nbsp;</th>

						{{-- VENTAS AFECTADAS --}}
						<th style="width: 2.35cm">&nbsp;</th>
					</tr>
				</thead>

				<tbody>
					@forelse($receipt_details->lines as $line)
					<tr>
						{{-- CANT. --}}
						<td class="text-right" style="padding-right: 0.2cm">
							{{ number_format($line['quantity_uf'], 0) }}
						</td>

						{{-- CODIGO --}}
						<td class="break-word">
							{{ $line['sub_sku'] }}
						</td>

						{{-- DESCRIPCION --}}
						<td class="cutter">
							{{ $line['name'] }} {{$line['variation'] }}
							@if(! empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif 
							@if(! empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
							@if(! empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif
						</td>

						{{-- PRECIO UNITARIO --}}
						<td class="text-right" style="padding-right: 0.2cm">
							{{ $line['unit_price_exc'] }}
						</td>

						{{-- VENTAS EXENTAS --}}
						<td class="text-right" style="padding-right: 0.2cm">
							@if ($receipt_details->is_exempt == 1)
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['line_total_exc_tax'] }}
							</span>
							@else
							&nbsp;
							@endif
						</td>

						{{-- VENTAS AFECTADAS --}}
						<td class="text-right" style="padding-right: 0.2cm">
							@if ($receipt_details->is_exempt == 0)
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['line_total_exc_tax'] }}
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

		@if (! empty($receipt_details->staff_note) || $receipt_details->discount_amount > 0)
		<div id="extra-details">
			<div id="comment">
				{{ $receipt_details->staff_note }}
			</div>
			<div id="discount-text">
				DESCUENTO {{ $receipt_details->discount_percent }}
			</div>
			<div id="discount-amount">
				{{ $receipt_details->discount_amount }}
			</div>
		</div>
		@endif

		<div id="footer">
			<div id="footer-left">
				{{-- CANTIDAD EN LETRAS --}}
				<div style="width: initial; height: 1.65cm; padding-left: 0.3cm; vertical-align: top; padding-top: 0.8cm;">
					{{ $receipt_details->total_letters }}
				</div>
				
				{{-- ENTREGA EL DOCUMENTO - NIT/DUI --}}
				<div class="cutter" style="width: initial; height: 1.55cm; padding-left: 1.7cm; vertical-align: top; padding-top: 0.65cm;">
					{{ ! empty($receipt_details->delivered_by_dui) ? $receipt_details->delivered_by_dui : $receipt_details->delivered_by_passport }}
				</div>

				{{-- RECIBE EL DOCUMENTO - NIT/DUI --}}
				<div style="width: initial; height: 1.25cm; padding-left: 1.7cm; vertical-align: top; padding-top: 0.5cm;">
					{{ $receipt_details->received_by_dui }}
				</div>
			</div>

			<div id="footer-right">
				{{-- SUMAS --}}
				<div class="text-right" style="width: initial; height: 1.1cm; padding-right: 0.2cm; vertical-align: top; padding-top: 0.4cm;">
					@if ($receipt_details->is_exempt == 0)
					{{ $receipt_details->total_before_tax }}
					@else
					&nbsp;
					@endif
				</div>

				{{-- VENTAS EXENTAS --}}
				<div class="text-right" style="width: initial; height: 1.6cm; padding-right: 0.2cm; vertical-align: top; padding-top: 0.7cm;">
					@if ($receipt_details->is_exempt == 1)
					{{ $receipt_details->total }}
					@else
					&nbsp;
					@endif
				</div>

				{{-- VENTA TOTAL --}}
				<div class="text-right" style="width: initial; height: 1.75cm; padding-right: 0.2cm; vertical-align: top; padding-top: 0.7cm;">
					{{ $receipt_details->total }}
				</div>
			</div>
		</div>
	</div>
</div>
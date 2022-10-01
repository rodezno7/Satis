<style>
    div#main { width: 100%;  font-size: 7.5pt; font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif; margin: 0; }
	
	div#head { height: 5.5cm; width: 100%; }
	
	div#container { width: 19.6cm; margin: 0cm 0.9cm 0cm 1.05cm; }
	div#container table { width: 100%; }
	div#container table td { vertical-align: middle; }

	div#header { height: 2.65cm; width: 100%; }
	div#header div#header-left { width: 53.0%; height: inherit; display: inline-block; vertical-align: top; }
	div#header div#header-right { width: 46.0%; height: inherit; display: inline-block; }
	div#header div#header-left div, div#header div#header-right div { width: 100%; vertical-align: middle; padding-top: 0.1cm; }

	@if (! empty($receipt_details->staff_note) || $receipt_details->discount_amount > 0)
	div#details { height: 10.6cm; }
	div#extra-details { height: 3.0cm; position: relative; }
	div#extra-details #comment { position: absolute; left: 1.9cm; top: 0.2cm; width: 10.54cm; }
	div#extra-details #discount-text { position: absolute; left: 14.15cm; top: 0.2cm; }
	div#extra-details #discount-amount { position: absolute; left: 17.05cm; top: 0.2cm; width: 2.45cm; text-align: right; padding-right: 0.1cm; }
	@else
	div#details { height: 13.6cm; }
	@endif

	div#details table#sell_lines { table-layout: fixed; }
	div#details table#sell_lines thead tr { height: 1.05cm; }
	div#details table#sell_lines tbody tr  { height: 0.5cm; }
	div#details table#sell_lines tbody td { padding-left: 0.2cm; vertical-align: top; }

	div#footer div#footer-left{ width: 31.8%; height: inherit; display: inline-block; vertical-align: top; }
    div#footer div#footer-center{ width: 31.4%; height: inherit; display: inline-block; vertical-align: top; }
	div#footer div#footer-right{ width: 35.9%; height: inherit; display: inline-block; }
	div#footer div#footer-left div, div#footer div#footer-center div, div#footer div#footer-right div { width: 100%; vertical-align: middle; padding-top: 0.1cm; }

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
				<div style="width: initial; height: 0.8cm; padding-left: 1.7cm; vertical-align: top; padding-top: 0.2cm;">
					{{ $receipt_details->customer_name }}
				</div>
				
				{{-- DIRECCION --}}
				<div class="cutter" style="height: 0.8cm; padding-left: 2.1cm; vertical-align: top; padding-top: 0.25cm;">
					{{ $receipt_details->customer_location }}
				</div>

				{{-- VENTA A CTA. DE --}}
				<div style="height: 1.05cm; padding-left: 2.9cm; vertical-align: top; padding-top: 0.6cm;">
					{{ $receipt_details->commission_agent }}
				</div>
			</div>

			<div id="header-right">
				{{-- FECHA --}}
				<div style="width: initial; height: 0.8cm; padding-left: 4.3cm; vertical-align: top; padding-top: 0.2cm;">
					{{ @format_date($receipt_details->invoice_date) }}
				</div>

                {{-- NADA --}}
                <div class="cutter" style="height: 0.8cm; padding-left: 2.1cm; vertical-align: top; padding-top: 0.25cm;">
					{{ $receipt_details->additional_notes }}
				</div>

                {{-- NIT/DUI DEL CLIENTE --}}
				<div style="height: 1.05cm; padding-left: 3.5cm; vertical-align: top; padding-top: 0.6cm;">
					{{ $receipt_details->customer_dui }}
				</div>
			</div>
		</div>

		<div id="details">
			<table id="sell_lines">
				<thead>
					<tr>
						{{-- CANTIDAD --}}
						<th style="width: 1.9cm">&nbsp;</th>

						{{-- DESCRIPCION --}}
						<th style="width: 10.55cm">&nbsp;</th>
						
						{{-- PRECIO UNITARIO --}}
						<th style="width: 1.7cm">&nbsp;</th>

                        {{-- VENTAS NO SUJETAS --}}
						<th style="width: 1.55cm">&nbsp;</th>

                        {{-- VENTAS EXENTAS --}}
						<th style="width: 1.35cm">&nbsp;</th>

						{{-- VENTAS GRAVADAS --}}
						<th style="width: 2.45cm">&nbsp;</th>
					</tr>
				</thead>

				<tbody>
					@forelse($receipt_details->lines as $line)
					<tr>
						{{-- CANTIDAD --}}
						<td class="text-right" style="padding-right: 0.2cm">
							{{ $line['quantity'] }}
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
							{{ $line['unit_price'] }}
						</td>

						{{-- VENTAS NO SUJETAS --}}
						<td class="text-right" style="padding-right: 0.2cm">
							&nbsp;
						</td>

                        {{-- VENTAS EXENTAS --}}
                        <td class="text-right" style="padding-right: 0.2cm">
							@if ($receipt_details->is_exempt == 1)
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['line_total'] }}
							</span>
							@else
							&nbsp;
							@endif
						</td>

                        {{-- VENTAS GRAVADAS --}}
                        <td class="text-right" style="padding-right: 0.2cm">
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
                {{-- SON --}}
                <div style="width: 12.5cm; height: 2.2cm; padding-left: 0.12cm; vertical-align: top; padding-top: 0.75cm;">
                    {{ $receipt_details->total_letters }}
                </div>

				{{-- ENTREGADO POR (NOMBRE) --}}
				<div class="cutter" style="width: initial; height: 1.3cm; padding-left: 1.6cm; vertical-align: top; padding-top: 1.0cm;">
					{{ $receipt_details->delivered_by }}
				</div>

                {{-- ENTREGADO POR (DUI/NIT) --}}
				<div class="cutter" style="width: initial; height: 0.8cm; padding-left: 1.3cm; vertical-align: top; padding-top: 0.2;">
					{{ ! empty($receipt_details->delivered_by_dui) ? $receipt_details->delivered_by_dui : $receipt_details->delivered_by_passport }}
				</div>
			</div>

            <div id="footer-center">
				{{-- NADA --}}
				<div style="height: 2.2cm; padding-left: 0.2cm; vertical-align: top; padding-top: 0.75cm;">
					&nbsp;
				</div>
				
				{{-- RECIBIDO POR (NOMBRE) --}}
				<div class="cutter" style="width: initial; height: 1.3cm; padding-left: 1.5cm; vertical-align: top; padding-top: 1.0cm;">
					{{ $receipt_details->received_by }}
				</div>

                {{-- RECIBIDO POR (DUI/NIT) --}}
				<div class="cutter" style="width: initial; height: 0.8cm; padding-left: 1.3cm; vertical-align: top; padding-top: 0.2;">
					{{ $receipt_details->received_by_dui }}
				</div>
			</div>

			<div id="footer-right">
				{{-- SUMAS --}}
				<div class="text-right" style="width: initial; height: 0.75cm; padding-right: 0.2cm; vertical-align: top; padding-top: 0.3cm;">
					@if ($receipt_details->is_exempt == 0)
					<span class="display_currency" data-currency_symbol="false">
						{{ $receipt_details->total_before_tax }}
					</span>
					@else
					&nbsp;
					@endif
				</div>

                {{-- IVA RETENIDO --}}
				<div class="text-right" style="width: initial; height: 0.75cm; padding-right: 0.2cm; vertical-align: top; padding-top: 0.3cm;">
					@if ($receipt_details->is_exempt == 0 && ! empty($receipt_details->withheld))
					<span class="display_currency" data-currency_symbol="false">
						{{ number_format($receipt_details->withheld, 4) }}
					</span>
					@else
					&nbsp;
					@endif
				</div>

                {{-- SUB TOTAL --}}
				<div class="text-right" style="width: initial; height: 0.75cm; padding-right: 0.2cm; vertical-align: top; padding-top: 0.3cm;">
					@if ($receipt_details->is_exempt == 0)
					<span class="display_currency" data-currency_symbol="false">
						{{ $receipt_details->total }}
					</span>
					@else
					&nbsp;
					@endif
				</div>

                {{-- VENTA NO SUJETA --}}
				<div class="text-right" style="width: initial; height: 0.75cm; padding-right: 0.2cm; vertical-align: top; padding-top: 0.3cm;">
					&nbsp;
				</div>

                {{-- VENTA EXENTA --}}
				<div class="text-right" style="width: initial; height: 0.75cm; padding-right: 0.2cm; vertical-align: top; padding-top: 0.3cm;">
					@if ($receipt_details->is_exempt == 1)
					<span class="display_currency" data-currency_symbol="false">
						{{ $receipt_details->total }}
					</span>
					@else
					&nbsp;
					@endif
				</div>

                {{-- VENTA TOTAL --}}
				<div class="text-right" style="width: initial; height: 0.75cm; padding-right: 0.2cm; vertical-align: top; padding-top: 0.1cm;">
					<span class="display_currency" data-currency_symbol="false">
						{{ $receipt_details->total }}
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
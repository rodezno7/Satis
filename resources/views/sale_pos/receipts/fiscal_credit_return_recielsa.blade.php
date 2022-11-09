<style>
    div#main { width: 100%;  font-size: 7.5pt; font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif; margin: 0; }
	
	div#head { height: 4.8cm; width: 100%; }
	
	div#container { width: 19.25cm; margin: 0cm 0.8cm 0cm 1.45cm; }
	div#container table { width: 100%; }
	div#container table td { vertical-align: middle; }

	div#header { height: 4.7cm; width: 100%; }
	div#header div#header-left { width: 63.4%; height: inherit; display: inline-block; vertical-align: top; }
	div#header div#header-right { width: 35.6%; height: inherit; display: inline-block; }
	div#header div#header-left div, div#header div#header-right div { width: 100%; vertical-align: middle; padding-top: 0.1cm; }

	@if ($receipt_details->staff_note)
	div#details { font-size: 7pt; height: 10.2cm; }
	div#comment { height: 3.0cm; margin: 0cm 5.95cm 0cm 3.7cm; padding: 0cm 0.2cm 0cm 0.2cm; }
	@else
	div#details { height: 13.2cm; }
	@endif

	@if (! empty($receipt_details->staff_note) || $receipt_details->discount_amount > 0)
	div#details { font-size: 7pt; height: 10.2cm; }
	div#extra-details { height: 3.0cm; position: relative; }
	div#extra-details #comment { position: absolute; left: 3.7cm; top: 0.2cm; width: 9.3cm; }
	div#extra-details #discount-text { position: absolute; left: 14.7cm; top: 0.2cm; }
	div#extra-details #discount-amount { position: absolute; left: 17.35cm; top: 0.2cm; width: 1.7cm; text-align: right; padding-right: 0.1cm; }
	@else
	div#details { font-size: 7pt; height: 13.2cm; }
	@endif

	div#details table#sell_lines { table-layout: fixed; }
	div#details table#sell_lines thead tr { height: 0.7cm; }
	div#details table#sell_lines tbody tr  { height: 0.5cm; }
	div#details table#sell_lines tbody td { padding-left: 0.2cm; vertical-align: top; }

	div#footer div#footer-left{ width: 32.9%; height: inherit; display: inline-block; vertical-align: top; }
    div#footer div#footer-center{ width: 35.2%; height: inherit; display: inline-block; vertical-align: top; }
	div#footer div#footer-right{ width: 30.9%; height: inherit; display: inline-block; }
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
				<div class="cutter" style="width: initial; height: 1.55cm; padding-left: 1.7cm; vertical-align: top; padding-top: 1.15cm;">
					{{ $receipt_details->customer_name }}
				</div>
				
				{{-- DIRECCION --}}
				<div class="cutter" style="height: 0.8cm; padding-left: 2.1cm; vertical-align: top; padding-top: 0.37cm;">
					{{ $receipt_details->customer_location }}
				</div>

				{{-- DEPARTAMENTO --}}
				<div class="cutter" style="height: 0.8cm; padding-left: 2.55cm; vertical-align: top; padding-top: 0.35cm;">
					{{ $receipt_details->customer_state }}
				</div>

                {{-- NO. DE CCF AJUSTADO/MODIFICADO --}}
				<div style="height: 0.8cm; padding-left: 5.5cm; vertical-align: top; padding-top: 0.35cm;">
					{{ $receipt_details->additional_notes }}
				</div>

                {{-- CONDICIONES DE LA OPERACION --}}
				<div style="height: 0.7cm; padding-left: 4.95cm; vertical-align: top; padding-top: 0.35cm;">
					{{ mb_strtoupper(__("messages." . $receipt_details->payment_condition)) }}
				</div>
			</div>

			<div id="header-right">
				{{-- FECHA --}}
				<div style="width: initial; height: 0.8cm; padding-left: 1.5cm; vertical-align: top; padding-top: 0.35cm;">
					{{
                        date('d', strtotime($receipt_details->invoice_date)) .
                        '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .
                        date('m', strtotime($receipt_details->invoice_date)) .
                        '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .
                        date("Y",strtotime($receipt_details->invoice_date))
                    }}
				</div>

                {{-- NADA --}}
				<div style="height: 0.7cm; padding-left: 1.55cm; vertical-align: top; padding-top: 0.4cm;">
					&nbsp;
				</div>

                {{-- REGISTRO NO. --}}
                <div class="cutter" style="height: 0.8cm; padding-left: 2.35cm; vertical-align: top; padding-top: 0.37cm;">
					{{ $receipt_details->customer_tax_number }}
				</div>

				{{-- GIRO --}}
				<div class="cutter" style="height: 0.8cm; padding-left: 1.2cm; vertical-align: top; padding-top: 0.35cm;">
					{{ $receipt_details->customer_business_activity }}
				</div>

                {{-- NIT --}}
				<div class="cutter" style="height: 0.8cm; padding-left: 0.9cm; vertical-align: top; padding-top: 0.35cm;">
					{{ $receipt_details->customer_nit }}
				</div>

                {{-- VENTA A CUENTA DE --}}
				<div class="cutter" style="height: 0.7cm; padding-left: 3.3cm; vertical-align: top; padding-top: 0.35cm;">
					{{ $receipt_details->commission_agent }}
				</div>
			</div>
		</div>

		<div id="details">
			<table id="sell_lines">
				<thead>
					<tr>
						{{-- CODIGO --}}
						<th style="width: 2.6cm">&nbsp;</th>

						{{-- CANTIDAD --}}
						<th style="width: 1.1cm">&nbsp;</th>

						{{-- DESCRIPCION --}}
						<th style="width: 9.4cm">&nbsp;</th>
						
						{{-- PRECIO UNITARIO --}}
						<th style="width: 1.6cm">&nbsp;</th>

                        {{-- VENTAS NO SUJETAS --}}
						<th style="width: 1.35cm">&nbsp;</th>

                        {{-- VENTAS EXENTAS --}}
						<th style="width: 1.3cm">&nbsp;</th>

						{{-- VENTAS GRAVADAS --}}
						<th style="width: 1.7cm">&nbsp;</th>
					</tr>
				</thead>

				<tbody>
					@forelse($receipt_details->lines as $line)
					@if ($line['quantity_uf'] > 0)
					<tr>
						{{-- CODIGO --}}
						<td class="break-word">
							{{ $line['sub_sku'] }}
						</td>

						{{-- CANTIDAD --}}
						<td class="text-right" style="padding-right: 0.2cm">
							{{ number_format($line['quantity_uf'], 0) }}
						</td>

						{{-- DESCRIPCION --}}
						<td class="cutter">
							{{ $line['name'] }} {{$line['variation'] }}
							@if(! empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif 
							@if(! empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
							@if(! empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif
						</td>

						{{-- PRECIO UNITARIO --}}
						<td class="text-right" style="padding-right: 0.35cm">
							{{ $line['unit_price_exc'] }}
						</td>

						{{-- VENTAS NO SUJETAS --}}
						<td class="text-right" style="padding-right: 0.2cm">
							&nbsp;
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

                        {{-- VENTAS GRAVADAS --}}
                        <td class="text-right" style="padding-right: 0.4cm">
							@if ($receipt_details->is_exempt == 0)
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['line_total_exc_tax'] }}
							@else
							&nbsp;
							@endif
						</td>
					</tr>
					@endif
					@empty
					<tr>
						<td colspan="7">&nbsp;</td>
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
                <div style="width: 13.2cm; height: 1.4cm; padding-left: 0.2cm; vertical-align: top; padding-top: 0.5cm;">
                    {{ $receipt_details->total_letters }}
                </div>

				{{-- ENTREGADO POR (NOMBRE) --}}
				<div class="cutter" style="width: initial; height: 1.2cm; padding-left: 1.4cm; vertical-align: top; padding-top: 0.9cm;">
					{{ $receipt_details->delivered_by }}
				</div>

                {{-- ENTREGADO POR (DUI/NIT) --}}
				<div class="cutter" style="width: initial; height: 0.8cm; padding-left: 1.2cm; vertical-align: top; padding-top: 0.25;">
					{{ ! empty($receipt_details->delivered_by_dui) ? $receipt_details->delivered_by_dui : $receipt_details->delivered_by_passport }}
				</div>
			</div>

            <div id="footer-center">
				{{-- NADA --}}
				<div style="height: 1.4cm; padding-left: 1.1cm; vertical-align: top; padding-top: 0.5cm;">
					&nbsp;
				</div>
				
				{{-- RECIBIDO POR (NOMBRE) --}}
				<div class="cutter" style="width: initial; height: 1.2cm; padding-left: 1.5cm; vertical-align: top; padding-top: 0.9cm;">
					{{ $receipt_details->received_by }}
				</div>

                {{-- RECIBIDO POR (DUI/NIT) --}}
				<div class="cutter" style="width: initial; height: 0.8cm; padding-left: 1.3cm; vertical-align: top; padding-top: 0.25;">
					{{ $receipt_details->received_by_dui }}
				</div>
			</div>

			<div id="footer-right">
				{{-- SUMAS --}}
				<div class="text-right" style="width: initial; height: 0.5cm; padding-right: 0.2cm; vertical-align: top; padding-top: 0cm;">
					@if ($receipt_details->is_exempt == 0)
					<span class="display_currency" data-currency_symbol="false">
						{{ $receipt_details->total_before_tax }}
					</span>
					@else
					&nbsp;
					@endif
				</div>

                {{-- 13% IVA --}}
				<div class="text-right" style="width: initial; height: 0.5cm; padding-right: 0.2cm; vertical-align: top; padding-top: 0cm;">
					@if ($receipt_details->is_exempt == 0)
					@php
						$tax_amount = $receipt_details->total - $receipt_details->total_before_tax;
						if ($receipt_details->withheld) {
							$tax_amount += $receipt_details->withheld;
						}
					@endphp
					<span class="display_currency" data-currency_symbol="false">
						{{ number_format($tax_amount, 4) }}
					</span>
					@else
					&nbsp;
					@endif
				</div>

                {{-- SUB TOTAL --}}
				<div class="text-right" style="width: initial; height: 0.5cm; padding-right: 0.2cm; vertical-align: top; padding-top: 0cm;">
					@if ($receipt_details->is_exempt == 0)
						@if ($receipt_details->withheld)
						<span class="display_currency" data-currency_symbol="false">
							{{ round($receipt_details->total + $receipt_details->withheld, 2) }}
						</span>
						@else
						<span class="display_currency" data-currency_symbol="false">
							{{ $receipt_details->total }}
						</span>
						@endif
					@else
					&nbsp;
					@endif
				</div>

                {{-- IVA RETENIDO --}}
				<div class="text-right" style="width: initial; height: 0.5cm; padding-right: 0.2cm; vertical-align: top; padding-top: 0cm;">
					@if ($receipt_details->is_exempt == 0 && ! empty($receipt_details->withheld))
					{{ number_format($receipt_details->withheld, 2) }}
					@else
					&nbsp;
					@endif
				</div>

                {{-- VENTA NO SUJETA --}}
				<div class="text-right" style="width: initial; height: 0.5cm; padding-right: 0.2cm; vertical-align: top; padding-top: 0cm;">
					&nbsp;
				</div>

                {{-- VENTA EXENTA --}}
				<div class="text-right" style="width: initial; height: 0.5cm; padding-right: 0.2cm; vertical-align: top; padding-top: 0cm;">
					@if ($receipt_details->is_exempt == 1)
					<span class="display_currency" data-currency_symbol="false">
						{{ $receipt_details->total }}
					</span>
					@else
					&nbsp;
					@endif
				</div>

                {{-- TOTAL TOTAL --}}
				<div class="text-right" style="width: initial; height: 0.5cm; padding-right: 0.2cm; vertical-align: top; padding-top: 0cm;">
					<span class="display_currency" data-currency_symbol="false">
						{{ $receipt_details->total }}
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
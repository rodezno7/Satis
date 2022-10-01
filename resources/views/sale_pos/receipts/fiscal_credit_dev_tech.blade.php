<style>
    div#main { width: 100%;  font-size: 7pt; font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif; margin: 0; }
    
	div#head { height: 3.45cm; width: 100%; }

	div#header div#header-left { width: 66.34%; height: inherit; display: inline-block; vertical-align: top; }
	div#header div#header-right { width: 32.66%; height: inherit; display: inline-block; vertical-align: top; margin-top: -0.03cm; }
	div#header div#header-left div, div#header div#header-right div { width: 100%; vertical-align: top; }

    div#container { width: 19.6cm; margin: 0cm 0.9cm 0cm 1.0cm; }
	div#container table { width: 100%; }
	div#container table td { vertical-align: middle; }

	div#details { height: 4.55cm; }

	@if ($receipt_details->discount_amount > 0)
	div#details { height: 3.75cm; }
	div#extra-details { position: relative; height: 0.8cm; }
	div#extra-details #discount-text { position: absolute; left: 15.2cm; top: 0.1cm; padding-left: 0.2cm; }
	div#extra-details #discount-amount { position: absolute; left: 17.0cm; top: 0.1cm; width: 2.2cm; text-align: right; }
	@else
	div#details { height: 4.55cm; }
	@endif

    div#details table#sell_lines { table-layout: fixed; }
	div#details table#sell_lines thead tr { height: 0.55cm; }
	div#details table#sell_lines tbody tr  { height: 0.4cm; }
	div#details table#sell_lines tbody td { padding-left: 0.1cm; }

    div#footer div#footer-left{ width: 35.04%; height: inherit; display: inline-block; vertical-align: top; font-size: 6pt; }
    div#footer div#footer-center{ width: 34.53%; height: inherit; display: inline-block; vertical-align: top; font-size: 6pt; }
	div#footer div#footer-right{ width: 29.43%; height: inherit; display: inline-block; }
	div#footer div#footer-left div, div#footer div#footer-center div, div#footer div#footer-right div { width: 100%; vertical-align: top; }

	.cutter { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

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
				<div style="height: 0.4cm; padding-left: 1.5cm; vertical-align: top; padding-top: 0.05cm;">
					{{ $receipt_details->customer_business_name }}
				</div>
				
				{{-- DIRECCION --}}
				<div class="cutter" style="height: 0.4cm; padding-left: 1.5cm; vertical-align: top; padding-top: 0.05cm;">
					{{ $receipt_details->customer_landmark }}
				</div>

                {{-- MUNICIPIO --}}
				<div style="height: 0.4cm; padding-left: 1.5cm; vertical-align: top; padding-top: 0.05cm;">
					{{ $receipt_details->customer_city }}
				</div>

				{{-- DEPARTAMENTO --}}
				<div style="height: 0.4cm; padding-left: 2.1cm; vertical-align: top; padding-top: 0.05cm;">
					{{ $receipt_details->customer_state }}
				</div>

                {{-- GIRO --}}
				<div style="height: 0.4cm; padding-left: 1.0cm; vertical-align: top; padding-top: 0.05cm;">
					{{ $receipt_details->customer_business_activity }}
				</div>

                {{-- VENTA A CUENTA DE --}}
				<div class="cutter" style="height: 0.4cm; padding-left: 2.5cm; vertical-align: top; padding-top: 0.05cm;">
					{{ $receipt_details->seller_name }}
				</div>
			</div>

			<div id="header-right">
				{{-- FECHA --}}
				<div style="height: 0.4cm; padding-left: 1.2cm; vertical-align: top; padding-top: 0;">
					{{ @format_date($receipt_details->invoice_date) }}
				</div>

                {{-- NRC --}}
                <div style="height: 0.4cm; padding-left: 1.2cm; vertical-align: top; padding-top: 0;">
					{{ $receipt_details->customer_tax_number }}
				</div>

                {{-- NIT --}}
				<div style="height: 0.4cm; padding-left: 1.0cm; vertical-align: top; padding-top: 0;">
					{{ $receipt_details->customer_nit }}
				</div>

                {{-- NOTA DE REMISION ANT. --}}
				<div style="height: 0.4cm; padding-left: 3.1cm; vertical-align: top; padding-top: 0;">
					&nbsp;
				</div>

                {{-- FECHA NOTA DE REMISION ANT. --}}
				<div style="height: 0.35cm; padding-left: 4.0cm; vertical-align: top; padding-top: 0;">
					&nbsp;
				</div>

                {{-- CONDICIONES DE LA OPERACION --}}
				<div style="height: 0.4cm; padding-left: 3.8cm; vertical-align: top; padding-top: 0;">
					{{ __("messages." . $receipt_details->payment_condition ) }}
				</div>
			</div>
		</div>

		<div id="details">
			<table id="sell_lines">
				<thead>
					<tr>
						{{-- CANTIDAD --}}
						<th style="width: 2.0cm">&nbsp;</th>

						{{-- DESCRIPCION --}}
						<th style="width: 9.1cm">&nbsp;</th>
						
						{{-- PRECIO UNITARIO --}}
						<th style="width: 1.8cm">&nbsp;</th>

                        {{-- VENTAS NO SUJETAS --}}
						<th style="width: 1.8cm">&nbsp;</th>

                        {{-- VENTAS EXENTAS --}}
						<th style="width: 1.8cm">&nbsp;</th>

						{{-- VENTAS GRAVADAS --}}
						<th style="width: 2.2cm">&nbsp;</th>
					</tr>
				</thead>

				<tbody>
					@forelse($receipt_details->lines as $line)
					<tr>
						{{-- CANTIDAD --}}
						<td class="text-right" style="padding-right: 0.45cm">
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
						<td class="text-right" style="padding-right: 0.15cm">
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['unit_price_exc'] }}
							</span>
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
                        <td class="text-right" style="padding-right: 0.35cm">
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

		@if ($receipt_details->discount_amount > 0)
		<div id="extra-details">
			<div id="discount-text">
				DESCUENTO (-) {{ $receipt_details->discount_percent }}
			</div>
			<div id="discount-amount">
				<span class="display_currency" data-currency_symbol="false">
					{{ $receipt_details->discount_amount }}
				</span>
			</div>
		</div>
		@endif

		<div id="footer">
			<div id="footer-left">
                {{-- SON --}}
                <div style="width: 13.2cm; height: 1.15cm; padding-left: 0.3cm; padding-top: 0.55cm;">
                    {{ $receipt_details->total_letters }}
                </div>

				{{-- RECIBIDO POR (NOMBRE) --}}
				<div class="cutter" style="width: initial; height: 0.8cm; padding-left: 1.3cm; padding-top: 0.55cm;">
					{{ $receipt_details->received_by }}
                </div>

                {{-- RECIBIDO POR (DUI/NIT) --}}
				<div class="cutter" style="width: initial; height: 0.4cm; padding-left: 0.9cm; padding-top: 0.05cm;">
					{{ $receipt_details->received_by_dui }}
				</div>
			</div>

            <div id="footer-center">
				{{-- NADA --}}
                <div style="height: 1.15cm;">
					&nbsp;
				</div>
				
				{{-- ENTREGADO POR (NOMBRE) --}}
				<div class="cutter" style="width: initial; height:0.8cm; padding-left: 1.3cm; padding-top: 0.55cm;">
					{{ $receipt_details->delivered_by }}
				</div>

                {{-- ENTREGADO POR (DUI/NIT) --}}
				<div class="cutter" style="width: initial; height: 0.4cm; padding-left: 0.9cm; padding-top: 0.05cm;">
                    {{ $receipt_details->delivered_by_dui }}
				</div>
			</div>

			<div id="footer-right">
				{{-- SUMAS --}}
				<div class="text-right" style="width: initial; height: 0.31cm; padding-right: 0.35cm;">
					@if ($receipt_details->is_exempt == 0)
					<span class="display_currency" data-currency_symbol="false">
						{{ $receipt_details->total_before_tax }}
					</span>
					@else
					&nbsp;
					@endif
				</div>

                {{-- 13% IVA --}}
				<div class="text-right" style="width: initial; height: 0.31cm; padding-right: 0.35cm;">
					@if ($receipt_details->is_exempt == 0)
					<span class="display_currency" data-currency_symbol="false">
						{{ $receipt_details->tax_amount }}
					</span>
					@else
					&nbsp;
					@endif
				</div>

                {{-- SUB TOTAL --}}
				<div class="text-right" style="width: initial; height: 0.31cm; padding-right: 0.35cm;">
					@if ($receipt_details->is_exempt == 0)
					<span class="display_currency" data-currency_symbol="false">
						@if ($receipt_details->withheld)
						{{ round($receipt_details->total + $receipt_details->withheld, 2) }}
						@else
						{{ $receipt_details->total }}
						@endif
					</span>
					@else
					&nbsp;
					@endif
				</div>

                {{-- IVA RETENIDO --}}
				<div class="text-right" style="width: initial; height: 0.3cm; padding-right: 0.35cm;">
					@if ($receipt_details->is_exempt == 0)
					<span class="display_currency" data-currency_symbol="false">
						{{ $receipt_details->withheld }}
					</span>
					@else
					&nbsp;
					@endif
				</div>

                {{-- IVA PERCIBIDO --}}
				<div class="text-right" style="width: initial; height: 0.3cm; padding-right: 0.35cm;">
					&nbsp;
				</div>

                {{-- VENTAS NO SUJETAS --}}
				<div class="text-right" style="width: initial; height: 0.3cm; padding-right: 0.35cm;">
					&nbsp;
				</div>

                {{-- VENTAS EXENTAS --}}
				<div class="text-right" style="width: initial; height: 0.3cm; padding-right: 0.35cm;">
					@if ($receipt_details->is_exempt == 1)
					<span class="display_currency" data-currency_symbol="false">
						{{ $receipt_details->total }}
					</span>
					@else
					&nbsp;
					@endif
				</div>

                {{-- VENTA TOTAL --}}
				<div class="text-right" style="width: initial; height: 0.3cm; padding-right: 0.35cm;">
					<span class="display_currency" data-currency_symbol="false">
						{{ $receipt_details->total }}
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
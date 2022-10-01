<style>
    div#main { width: 100%;  font-size: 7.5pt; font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif; margin: 0; }
	
	div#head { height: 4.7cm; width: 100%; }
	
	div#container { width: 19.2cm; margin: 0cm 1.05cm 0cm 1.3cm; }
	div#container table { width: 100%; }
	div#container table td { vertical-align: middle; }

	div#header { height: 4.9cm; width: 100%; }
	div#header div#header-left { width: 63.6%; height: inherit; display: inline-block; vertical-align: top; }
	div#header div#header-right { width: 35.4%; height: inherit; display: inline-block; }
	div#header div#header-left div, div#header div#header-right div { width: 100%; vertical-align: middle; padding-top: 0.1cm; }

	@if (! empty($receipt_details->staff_note) || $receipt_details->discount_amount > 0)
	div#details { height: 11.1cm; }
	div#extra-details { height: 3.0cm; position: relative; }
	div#extra-details #comment { position: absolute; left: 3.7cm; top: 0.2cm; width: 10.14cm; }
	div#extra-details #discount-text { position: absolute; left: 14.2cm; top: 0.2cm; }
	div#extra-details #discount-amount { position: absolute; left: 17.05cm; top: 0.2cm; width: 2.0cm; text-align: right; padding-right: 0.1cm; }
	@else
	div#details { height: 14.1cm; }
	@endif

	div#details table#sell_lines { table-layout: fixed; }
	div#details table#sell_lines thead tr { height: 0.7cm; }
	div#details table#sell_lines tbody tr  { height: 0.5cm; }
	div#details table#sell_lines tbody td { padding-left: 0.2cm; vertical-align: top; }

	div#footer div#footer-left{ width: 32.9%; height: inherit; display: inline-block; vertical-align: top; }
    div#footer div#footer-center{ width: 39.7%; height: inherit; display: inline-block; vertical-align: top; }
	div#footer div#footer-right{ width: 26.4%; height: inherit; display: inline-block; }
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
				<div style="width: initial; height: 1.5cm; padding-left: 1.6cm; vertical-align: top; padding-top: 1.2cm;">
					{{ $receipt_details->customer_name }}
				</div>
				
				{{-- DIRECCION --}}
				<div class="cutter" style="height: 0.8cm; padding-left: 2.1cm; vertical-align: top; padding-top: 0.4cm;">
					{{ $receipt_details->customer_location }}
				</div>

				{{-- DEPARTAMENTO --}}
				<div style="height: 0.8cm; padding-left: 2.55cm; vertical-align: top; padding-top: 0.4cm;">
					{{ $receipt_details->customer_state }}
				</div>

                {{-- NO. Y FECHA DE CCF EMITIDO PREVIAMENTE --}}
				<div style="height: 0.8cm; padding-left: 6.25cm; vertical-align: top; padding-top: 0.4cm;">
					{{ $receipt_details->additional_notes }}
				</div>

                {{-- CONDICIONES DE LA OPERACION --}}
				<div style="height: 0.7cm; padding-left: 4.95cm; vertical-align: top; padding-top: 0.4cm;">
					{{ __('messages.' . $receipt_details->payment_condition) }}
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
                <div style="height: 0.8cm; padding-left: 2.25cm; vertical-align: top; padding-top: 0.4cm;">
					{{ $receipt_details->customer_tax_number }}
				</div>

				{{-- GIRO --}}
				<div style="height: 0.8cm; padding-left: 1.15cm; vertical-align: top; padding-top: 0.4cm;">
					{{ $receipt_details->customer_business_activity }}
				</div>

                {{-- NIT --}}
				<div style="height: 0.8cm; padding-left: 0.85cm; vertical-align: top; padding-top: 0.4cm;">
					{{ $receipt_details->customer_nit }}
				</div>

                {{-- BIENES REMITIDOS A TITULO DE --}}
				<div class="cutter" style="height: 0.7cm; padding-left: 4.55cm; vertical-align: top; padding-top: 0.4cm;">
					&nbsp;
				</div>
			</div>
		</div>

		<div id="details">
			<table id="sell_lines">
				<thead>
					<tr>
						{{-- CODIGO --}}
						<th style="width: 1.9cm">&nbsp;</th>

						{{-- CANTIDAD --}}
						<th style="width: 1.8cm">&nbsp;</th>

						{{-- DESCRIPCION --}}
						<th style="width: 11.65cm">&nbsp;</th>
						
						{{-- PRECIO UNITARIO --}}
						<th style="width: 1.7cm">&nbsp;</th>

						{{-- VENTA TOTAL --}}
						<th style="width: 2.0cm">&nbsp;</th>
					</tr>
				</thead>

				<tbody>
					@forelse($receipt_details->lines as $line)
					<tr>
						{{-- CODIGO --}}
						<td class="break-word">
							{{ $line['sub_sku'] }}
						</td>

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

						{{-- VENTA TOTAL --}}
						<td class="text-right" style="padding-right: 0.2cm">
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['line_total'] }}
							</span>
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="5">&nbsp;</td>
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
                <div style="width: 14cm; height: 0.9cm; padding-left: 1.15cm; vertical-align: top; padding-top: 0cm;">
                    {{ $receipt_details->total_letters }}
                </div>

				{{-- ENTREGADO POR (NOMBRE) --}}
				<div class="cutter" style="width: initial; height: 0.8cm; padding-left: 1.4cm; vertical-align: top; padding-top: 0.25cm;">
					{{ $receipt_details->delivered_by }}
				</div>

                {{-- ENTREGADO POR (DUI/NIT) --}}
				<div class="cutter" style="width: initial; height: 0.8cm; padding-left: 1.2cm; vertical-align: top; padding-top: 0;">
					{{ ! empty($receipt_details->delivered_by_dui) ? $receipt_details->delivered_by_dui : $receipt_details->delivered_by_passport }}
				</div>
			</div>

            <div id="footer-center">
				{{-- NADA --}}
				<div style="height: 0.9cm; padding-left: 1.1cm; vertical-align: top; padding-top: 0.1cm;">
					&nbsp;
				</div>
				
				{{-- RECIBIDO POR (NOMBRE) --}}
				<div class="cutter" style="width: initial; height: 0.8cm; padding-left: 1.5cm; vertical-align: top; padding-top: 0.25cm;">
					{{ $receipt_details->received_by }}
				</div>

                {{-- RECIBIDO POR (DUI/NIT) --}}
				<div class="cutter" style="width: initial; height: 0.8cm; padding-left: 1.3cm; vertical-align: top; padding-top: 0;">
					{{ $receipt_details->received_by_dui }}
				</div>
			</div>

			<div id="footer-right">
				{{-- TOTAL TOTAL --}}
				<div class="text-right" style="width: initial; height: 2.5cm; padding-right: 0.2cm; vertical-align: top; padding-top: 1.5cm;">
					{{ $receipt_details->total }}
				</div>
			</div>
		</div>
	</div>
</div>
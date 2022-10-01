<style>
    div#main {
        width: 100%;
        font-size: 7pt;
        font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
        margin: 0;
    }
    
	div#head {
        height: 5cm;
        width: 100%;
    }

    div#container {
        width: 19.3cm;
        margin: 0.15cm 1.4cm 0cm 1.0cm;
    }

    div#container table {
        width: 100%;
    }

    div#container table td {
        vertical-align: middle;
    }

    div#header {
        position: relative;
        height: 3.0cm;
        width: 100%;
    }

    div#header div#customer {
        position: absolute;
        left: 1.7cm;
        top: 0.7cm;
        width: 8.5cm;
    }

    div#header div#address {
        position: absolute;
        left: 1.7cm;
        top: 1.3cm;
        width: 8.5cm;
    }

    div#header div#city {
        position: absolute;
        left: 1.7cm;
        top: 1.9cm;
        width: 8.5cm;
    }

    div#header div#state {
        position: absolute;
        left: 2.5cm;
        top: 2.45cm;
        width: 7.7cm;
    }

    div#header div#date-day {
        position: absolute;
        left: 12.8cm;
        top: 0.2cm;
        width: 1.0cm;
    }

    div#header div#date-month {
        position: absolute;
        left: 14.7cm;
        top: 0.2cm;
        width: 2.3cm;
    }

    div#header div#date-year {
        position: absolute;
        left: 18.0cm;
        top: 0.2cm;
        width: 1.0cm;
    }

    div#header div#nit {
        position: absolute;
        left: 12.7cm;
        top: 0.75cm;
        width: 6.2cm;
    }

    div#header div#seller {
        position: absolute;
        left: 14.6cm;
        top: 1.35cm;
        width: 4.4cm;
    }

    div#header div#pay-condition-cash {
        position: absolute;
        left: 17.6cm;
        top: 1.95cm;
        width: 1.0cm;
    }

    div#header div#pay-condition-credit {
        position: absolute;
        left: 12.8cm;
        top: 2.55cm;
        width: 1.0cm;
    }

    div#header div#pay-condition-warehouse {
        position: absolute;
        left: 16.5cm;
        top: 2.55cm;
        width: 1.0cm;
    }

    div#header div#pay-condition-other {
        position: absolute;
        left: 18.85cm;
        top: 2.55cm;
        width: 1.0cm;
    }

	@if ($receipt_details->discount_amount > 0)
    
	div#details {
        height: 10.7cm;
    }

	div#extra-details {
        position: relative;
        height: 0.8cm;
    }

	div#extra-details #discount-text {
        position: absolute;
        left: 13.5cm;
        top: 0.1cm;
        width: ;
    }

	div#extra-details #discount-amount {
        position: absolute;
        left: 17.0cm;
		top: 0.1cm;
		width: 2.0cm;
        text-align: right;
    }

	@else

	div#details {
        height: 11.5cm;
    }
	
    @endif

    div#details table#sell_lines {
        table-layout: fixed;
    }

	div#details table#sell_lines thead tr {
        height: 1.0cm;
    }

	div#details table#sell_lines tbody tr {
        height: 0.5cm;
    }

	div#details table#sell_lines tbody td {
        padding-left: 0.1cm;
    }

    div#footer {
		position: relative;
        height: 6.2cm;
        width: 100%;
	}

    div#footer div#total-letters {
		position: absolute;
        left: 0.3cm;
		top: 0.8cm;
		width: 11.0cm;
	}

    div#footer div#sums {
		position: absolute;
        left: 17.0cm;
		top: 0.2cm;
		width: 2.0cm;
        text-align: right;
	}

    div#footer div#iva {
		position: absolute;
        left: 17.0cm;
		top: 0.8cm;
		width: 2.0cm;
        text-align: right;
	}

    div#footer div#subtotal {
		position: absolute;
        left: 17.0cm;
		top: 1.25cm;
		width: 2.0cm;
        text-align: right;
	}

    div#footer div#iva-withheld {
		position: absolute;
        left: 17.0cm;
		top: 1.75cm;
		width: 2.0cm;
        text-align: right;
	}

    div#footer div#iva-received {
		position: absolute;
        left: 17.0cm;
		top: 2.25cm;
		width: 2.0cm;
        text-align: right;
	}

    div#footer div#not-subject {
		position: absolute;
        left: 17.0cm;
		top: 2.75cm;
		width: 2.0cm;
        text-align: right;
	}

    div#footer div#exempt {
		position: absolute;
        left: 17.0cm;
		top: 3.25cm;
		width: 2.0cm;
        text-align: right;
	}

    div#footer div#total {
		position: absolute;
        left: 17.0cm;
		top: 3.75cm;
		width: 2.0cm;
        text-align: right;
	}

    div#footer div#delivered-name {
		position: absolute;
        left: 1.5cm;
		top: 5.4cm;
		width: 7.5cm;
        font-size: 6pt;
	}

    div#footer div#delivered-dui {
		position: absolute;
        left: 1.5cm;
		top: 5.75cm;
		width: 7.5cm;
        font-size: 6pt;
	}

    div#footer div#received-name {
		position: absolute;
        left: 11.3cm;
		top: 5.4cm;
		width: 7.5cm;
        font-size: 6pt;
	}

    div#footer div#received-dui {
		position: absolute;
        left: 11.3cm;
		top: 5.75cm;
		width: 7.5cm;
        font-size: 6pt;
	}

	.cutter {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

	.text-center {
        text-align: center;
    }

    .text-left {
        text-align: left;
    }

    .text-right {
        text-align: right;
    }
</style>
    
<div id="main">
	<div id="container">

        {{-- HEAD --}}
		<div id="head"></div>

		<div id="header">
            {{-- CLIENTE --}}
            <div id="customer" class="cutter">
                {{ $receipt_details->customer_name }}
            </div>
            
            {{-- DIRECCIÓN --}}
            <div id="address" class="cutter">
                {{ $receipt_details->customer_landmark }}
            </div>

            {{-- MUNICIPIO --}}
            <div id="city" class="cutter">
                {{ $receipt_details->customer_city }}
            </div>

            {{-- DEPARTAMENTO --}}
            <div id="state" class="cutter">
                {{ $receipt_details->customer_state }}
            </div>

            {{-- FECHA (DÍA) --}}
            <div id="date-day" class="cutter">
                {{ date('d', strtotime($receipt_details->invoice_date)) }}
            </div>

            {{-- FECHA (MES) --}}
            <div id="date-month" class="cutter">
                {{ $receipt_details->months[date('n', strtotime($receipt_details->invoice_date))] }}
            </div>

            {{-- FECHA (AÑO) --}}
            <div id="date-year" class="cutter">
                {{ date('Y', strtotime($receipt_details->invoice_date)) }}
            </div>

            {{-- NIT --}}
            <div id="nit" class="cutter">
                {{ $receipt_details->customer_nit }}
            </div>

            {{-- VENTA A CUENTA DE --}}
            <div id="seller" class="cutter">
                {{ $receipt_details->commission_agent }}
            </div>

            {{--CONDICIONES DE LA OPERACIÓN --}}
            @if ($receipt_details->payment_condition == 'cash')
            <div id="pay-condition-cash" class="cutter">
                x
            </div>
            @elseif ($receipt_details->payment_condition == 'credit')
            <div id="pay-condition-credit" class="cutter">
                x
            </div>
            @elseif ($receipt_details->payment_condition == 'warehouse')
            <div id="pay-condition-warehouse" class="cutter">
                x
            </div>
            @else
            <div id="pay-condition-other" class="cutter">
                x
            </div>
            @endif
		</div>

		<div id="details">
			<table id="sell_lines">
				<thead>
					<tr>
						{{-- CANTIDAD --}}
						<th style="width: 1.6cm">&nbsp;</th>

						{{-- DESCRIPCIÓN --}}
						<th style="width: 9.8cm">&nbsp;</th>
						
						{{-- PRECIO UNITARIO --}}
						<th style="width: 1.9cm">&nbsp;</th>

                        {{-- VENTAS NO SUJETAS --}}
						<th style="width: 1.7cm">&nbsp;</th>

                        {{-- VENTAS EXENTAS --}}
						<th style="width: 1.5cm">&nbsp;</th>

						{{-- VENTAS GRAVADAS --}}
						<th style="width: 2.8cm">&nbsp;</th>
					</tr>
				</thead>

				<tbody>
					@forelse($receipt_details->lines as $line)
					<tr>
						{{-- CANTIDAD --}}
						<td class="cutter text-right" style="padding-right: 0.2cm">
							{{ $line['quantity'] }}
						</td>

						{{-- DESCRIPCION --}}
						<td class="cutter" style="padding-left: 0.35cm">
							{{ $line['name'] }} {{$line['variation'] }}
							@if(! empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif 
							@if(! empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
							@if(! empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif
						</td>

						{{-- PRECIO UNITARIO --}}
						<td class="text-right" style="padding-right: 0.2cm">
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['unit_price'] }}
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
								{{ $line['line_total'] }}
							</span>
							@else
							&nbsp;
							@endif
						</td>

                        {{-- VENTAS GRAVADAS --}}
                        <td class="text-right" style="padding-right: 0.3cm">
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
            {{-- SON --}}
            <div id="total-letters">
                {{ $receipt_details->total_letters }}
            </div>

            {{-- SUMAS --}}
            <div id="sums">
                @if ($receipt_details->is_exempt == 0)
                <span class="display_currency" data-currency_symbol="false">
                    {{ $receipt_details->total_before_tax }}
                </span>
                @else
                &nbsp;
                @endif
            </div>

            {{-- 13% IVA --}}
            <div id="iva">
                &nbsp;
            </div>

            {{-- SUB-TOTAL --}}
            <div id="subtotal">
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

            {{-- (-) IVA RETENIDO --}}
            <div id="iva-withheld">
                @if ($receipt_details->is_exempt == 0 && $receipt_details->withheld)
                <span class="display_currency" data-currency_symbol="false">
                    {{ $receipt_details->withheld }}
                </span>
                @else
                &nbsp;
                @endif
            </div>

            {{-- (+) IVA PERCIBIDO --}}
            <div id="iva-received">
                &nbsp;
            </div>

            {{-- VENTAS NO SUJETAS --}}
            <div id="not-subject">
                &nbsp;
            </div>

            {{-- VENTAS EXENTAS --}}
            <div id="exempt">
                @if ($receipt_details->is_exempt == 1)
                <span class="display_currency" data-currency_symbol="false">
                    {{ $receipt_details->total }}
                </span>
                @else
                &nbsp;
                @endif
            </div>

            {{-- VENTA TOTAL --}}
            <div id="total">
                <span class="display_currency" data-currency_symbol="false">
                    {{ $receipt_details->total }}
                </span>
            </div>

            {{-- ENTREGADO POR (NOMBRE) --}}
            <div id="delivered-name" class="cutter">
                {{ $receipt_details->delivered_by }}
            </div>

            {{-- ENTREGADO POR (DUI/NIT) --}}
            <div id="delivered-dui" class="cutter">
                {{ $receipt_details->delivered_by_dui }}
            </div>

            {{-- RECIBIDO POR (NOMBRE) --}}
            <div id="received-name" class="cutter">
                {{ $receipt_details->received_by }}
            </div>

            {{-- RECIBIDO POR (DUI/NIT) --}}
            <div id="received-dui" class="cutter">
                {{ $receipt_details->received_by_dui }}
            </div>
		</div>
	</div>
</div>
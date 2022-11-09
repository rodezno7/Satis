<style>
    div#main {
        width: 100%;
        font-size: 7pt;
        font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
        margin: 0;
    }
    
	div#head {
        height: 3cm;
        width: 100%;
    }

    div#container {
        width: 19.75cm;
        margin: 0cm 0.85cm 0cm 0.9cm;
    }

    div#container table {
        width: 100%;
    }

    div#container table td {
        vertical-align: middle;
    }

    div#header {
        position: relative;
        height: 1.7cm;
        width: 100%;
    }

    div#header div#customer {
        position: absolute;
        left: 1.8cm;
        top: 0.2cm;
        width: 11.6cm;
    }

    div#header div#address {
        position: absolute;
        left: 1.8cm;
        top: 0.8cm;
        width: 11.6cm;
    }

    div#header div#seller {
        position: absolute;
        left: 3.3cm;
        top: 1.35cm;
        width: 10.1cm;
    }

    div#header div#date {
        position: absolute;
        left: 15.2cm;
        top: 0.15cm;
        width: 4.3cm;
    }

    div#header div#nit {
        position: absolute;
        left: 15.2cm;
        top: 0.75cm;
        width: 4.3cm;
    }

    div#header div#pay-condition {
        position: absolute;
        left: 16.4cm;
        top: 1.35cm;
        width: 3.1cm;
    }

	@if ($receipt_details->discount_amount > 0)
    
	div#details {
        height: 4.7cm;
    }

	div#extra-details {
        position: relative;
        height: 0.8cm;
    }

	div#extra-details #discount-text {
        position: absolute;
        left: 9.0cm;
        top: 0.1cm;
        width: ;
    }

	div#extra-details #discount-amount {
        position: absolute;
        left: 18.2cm;
		top: 0.1cm;
		width: 1.55cm;
        text-align: right;
    }

	@else

	div#details {
        height: 5.5cm;
    }
	
    @endif

    div#details table#sell_lines {
        table-layout: fixed;
    }

	div#details table#sell_lines thead tr {
        height: 0.85cm;
    }

	div#details table#sell_lines tbody tr {
        height: 0.5cm;
    }

	div#details table#sell_lines tbody td {
        padding-left: 0.1cm;
    }

    div#footer {
		position: relative;
        height: 2.45cm;
        width: 100%;
	}

    div#footer div#total-letters {
		position: absolute;
        left: 0.3cm;
		top: 0.6cm;
		width: 13.5cm;
	}

    div#footer div#sums {
		position: absolute;
        left: 18.2cm;
		top: 0.1cm;
		width: 1.55cm;
        text-align: right;
	}

    div#footer div#subtotal {
		position: absolute;
        left: 18.2cm;
		top: 0.5cm;
		width: 1.55cm;
        text-align: right;
	}

    div#footer div#iva-withheld {
		position: absolute;
        left: 18.2cm;
		top: 0.9cm;
		width: 1.55cm;
        text-align: right;
	}

    div#footer div#not-subject {
		position: absolute;
        left: 18.2cm;
		top: 1.3cm;
		width: 1.55cm;
        text-align: right;
	}

    div#footer div#exempt {
		position: absolute;
        left: 18.2cm;
		top: 1.75cm;
		width: 1.55cm;
        text-align: right;
	}

    div#footer div#total {
		position: absolute;
        left: 18.2cm;
		top: 2.15cm;
		width: 1.55cm;
        text-align: right;
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

            {{-- VENTA A CUENTA DE --}}
            <div id="seller" class="cutter">
                {{ mb_strtoupper($receipt_details->commission_agent) }}
            </div>

            {{-- FECHA --}}
            <div id="date" class="cutter">
                {{ $receipt_details->transaction_date }}
            </div>

            {{-- NIT --}}
            <div id="nit" class="cutter">
                {{ $receipt_details->customer_nit }}
            </div>

            {{--CONDICIÓN DE PAGO --}}
            <div id="pay-condition" class="cutter">
                {{ mb_strtoupper(__("messages." . $receipt_details->payment_condition)) }}
            </div>
		</div>

		<div id="details">
			<table id="sell_lines">
				<thead>
					<tr>
						{{-- CANTIDAD --}}
						<th style="width: 1.55cm">&nbsp;</th>

						{{-- DESCRIPCIÓN --}}
						<th style="width: 12.5cm">&nbsp;</th>
						
						{{-- PRECIO UNITARIO --}}
						<th style="width: 1.05cm">&nbsp;</th>

                        {{-- VENTAS NO SUJETAS --}}
						<th style="width: 0.95cm">&nbsp;</th>

                        {{-- VENTAS EXENTAS --}}
						<th style="width: 1.8cm">&nbsp;</th>

						{{-- VENTAS GRAVADAS --}}
						<th style="width: 1.8cm">&nbsp;</th>
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
		</div>
	</div>
</div>
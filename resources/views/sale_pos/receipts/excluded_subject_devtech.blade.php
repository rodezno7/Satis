<style>
    div#main {
        width: 100%;
        font-size: 7pt;
        font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
        margin: 0;
    }
    
	div#head {
        height: 3.5cm;
        width: 100%;
    }

    div#container {
        width: 19.7cm;
        margin: 0cm 0.85cm 0cm 1.0cm;
    }

    div#container table {
        width: 100%;
    }

    div#container table td {
        vertical-align: middle;
    }

    div#header {
        position: relative;
        height: 1.75cm;
        width: 100%;
    }

    div#header div#customer {
        position: absolute;
        left: 4.5cm;
        top: 0.15cm;
        width: 11.0cm;
    }

    div#header div#address {
        position: absolute;
        left: 1.9cm;
        top: 0.7cm;
        width: 13.6cm;
    }

    div#header div#dui-nit {
        position: absolute;
        left: 1.9cm;
        top: 1.25cm;
        width: 17.5cm;
    }

    div#header div#date {
        position: absolute;
        left: 17.6cm;
        top: 0.15cm;
        width: 2.0cm;
    }

    div#header div#telephone {
        position: absolute;
        left: 17.2cm;
        top: 0.7cm;
        width: 2.4cm;
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
        left: 13.5cm;
        top: 0.1cm;
        width: ;
    }

	div#extra-details #discount-amount {
        position: absolute;
        left: 17.0cm;
		top: 0.1cm;
		width: 2.5cm;
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
        height: 0.7cm;
    }

	div#details table#sell_lines tbody tr {
        height: 0.5cm;
    }

	div#details table#sell_lines tbody td {
        padding-left: 0.1cm;
    }

    div#footer {
		position: relative;
        height: 2.5cm;
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
        left: 17.0cm;
		top: 0.15cm;
		width: 2.5cm;
        text-align: right;
	}

    div#footer div#income-withheld {
		position: absolute;
        left: 17.0cm;
		top: 0.6cm;
		width: 2.5cm;
        text-align: right;
	}

    div#footer div#subtotal {
		position: absolute;
        left: 17.0cm;
		top: 1.1cm;
		width: 2.5cm;
        text-align: right;
	}

    div#footer div#iva-withheld {
		position: absolute;
        left: 17.0cm;
		top: 1.6cm;
		width: 2.5cm;
        text-align: right;
	}

    div#footer div#total {
		position: absolute;
        left: 17.0cm;
		top: 2.1cm;
		width: 2.5cm;
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
            {{-- NOMBRE DEL SUJETO EXCLUIDO --}}
            <div id="customer" class="cutter">
                {{ $receipt_details->customer_name }}
            </div>
            
            {{-- DIRECCIÓN --}}
            <div id="address" class="cutter">
                {{ $receipt_details->customer_landmark }}
            </div>

            {{-- DUI O NIT --}}
            <div id="dui-nit" class="cutter">
                {{ $receipt_details->customer_dui }}
            </div>

            {{-- FECHA --}}
            <div id="date" class="cutter">
                {{ @format_date($receipt_details->invoice_date) }}
            </div>

            {{-- TEL --}}
            <div id="telephone" class="cutter">
                {{ $receipt_details->customer_phone }}
            </div>
		</div>

		<div id="details">
			<table id="sell_lines">
				<thead>
					<tr>
						{{-- CANTIDAD --}}
						<th style="width: 1.6cm">&nbsp;</th>

						{{-- DESCRIPCIÓN --}}
						<th style="width: 12.5cm">&nbsp;</th>
						
						{{-- PRECIO UNITARIO --}}
						<th style="width: 2.2cm">&nbsp;</th>

						{{-- TOTAL DE COMPRAS --}}
						<th style="width: 3.4cm">&nbsp;</th>
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
								{{ $line['unit_price_exc'] }}
							</span>
						</td>

                        {{-- TOTAL DE COMPRAS --}}
                        <td class="text-right" style="padding-right: 0.3cm">
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
						<td colspan="4">&nbsp;</td>
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
                <span class="display_currency" data-currency_symbol="false">
                    {{ $receipt_details->total_before_tax }}
                </span>
            </div>

            {{-- (-) RENTA RETENIDO --}}
            <div id="income-withheld">
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

            {{-- VENTA --}}
            <div id="total">
                <span class="display_currency" data-currency_symbol="false">
                    {{ $receipt_details->total }}
                </span>
            </div>
		</div>
	</div>
</div>
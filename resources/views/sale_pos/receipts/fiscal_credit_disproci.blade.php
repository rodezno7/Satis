<style>
    div.main {
        width: 100%;
        font-size: 7pt;
        font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
        margin: 0;
    }

    div.container {
        width: 19.0cm;
        padding: 0;
        margin: 0cm 1.4cm 0cm 0.95cm;
    }

    div.container table {
        width: 100%;
    }

    div.container table td {
        vertical-align: middle;
    }

    div.head {
        height: 2.9cm;
        width: 100%;
    }

    div.header {
        position: relative;
        height: 2.2cm;
        width: 100%;
    }

    div.header div.customer {
        position: absolute;
        left: 1.5cm;
        top: 0.1cm;
        width: 11.4cm;
    }

    div.header div.date {
        position: absolute;
        left: 14.3cm;
        top: 0.1cm;
        width: 4.5cm;
    }
    div.header div.address {
        position: absolute;
        left: 1.8cm;
        top: 0.6cm;
        width: 11.1cm;
    }

    div.header div.nrc {
        position: absolute;
        left: 14.5cm;
        top: 0.6cm;
        width: 4.3cm;
    }

    div.header div.nit-dui {
        position: absolute;
        left: 14.5cm;
        top: 1cm;
        width: 4.3cm;
    }

    div.header div.seller {
        position: absolute;
        left: 1.8cm;
        top: 1.5cm;
        width: 5.7cm;
    }

    div.header div.zone {
        position: absolute;
        left: 8.7cm;
        top: 1.25cm;
        width: 4.2cm;
    }

    div.header div.business-activity {
        position: absolute;
        left: 14cm;
        top: 1.4cm;
        width: 5cm;
    }    

    div.header div.payment-condition {
        position: absolute;
        left: 15.4cm;
        top: 1.75cm;
        width: 3.4cm;
    }

	div.details {
        height: 4.75cm;
        width: 100%;
    }

    div.details table.sell_lines {
        table-layout: fixed;
    }

	div.details table.sell_lines thead tr {
        height: 0.8cm;
    }

	div.details table.sell_lines tbody tr {
        height: 0.4cm;
    }

	div.details table.sell_lines tbody td {
        padding-left: 0.1cm;
        vertical-align: top;
    }

    div.footer {
		position: relative;
        height: 3.2cm;
        width: 100%;
	}

    div.footer div.total-letters {
		position: absolute;
        left: 1.0cm;
		top: 0.1cm;
		width: 12.0cm;
	}

    div.footer div.sums {
		position: absolute;
        left: 17.1cm;
		top: 0.15cm;
		width: 1.7cm;
        text-align: right;
        padding-right: 0.1cm;
	}

    div.footer div.iva {
		position: absolute;
        left: 17.1cm;
		top: 0.6cm;
		width: 1.7cm;
        text-align: right;
        padding-right: 0.1cm;
	}

    div.footer div.subtotal {
		position: absolute;
        left: 17.1cm;
		top: 1.05cm;
		width: 1.7cm;
        text-align: right;
        padding-right: 0.1cm;
	}

    div.footer div.iva-withheld {
		position: absolute;
        left: 17.1cm;
		top: 1.5cm;
		width: 1.7cm;
        text-align: right;
        padding-right: 0.1cm;
	}

    div.footer div.exempt {
		position: absolute;
        left: 17.1cm;
		top: 1.9cm;
		width: 1.7cm;
        text-align: right;
        padding-right: 0.1cm;
	}

    div.footer div.total {
		position: absolute;
        left: 17.1cm;
		top: 2.8cm;
		width: 1.7cm;
        text-align: right;
        padding-right: 0.1cm;
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
    
<div class="main">
	<div class="container">
        {{-- HEAD --}}
		<div class="head"></div>

		<div class="header">
            {{-- CLIENTE --}}
            <div class="customer cutter">
                {{ $receipt_details->customer_name }}
            </div>

            {{-- FECHA --}}
            <div class="date cutter">
                {{ @format_date($receipt_details->invoice_date) }}
            </div>
            
            {{-- DIRECCIÓN --}}
            <div class="address cutter">
                {{ $receipt_details->customer_landmark }}
            </div>

            {{-- NRC --}}
            <div class="nrc cutter">
                {{ $receipt_details->customer_tax_number }}
            </div>

            {{-- NIT/DUI --}}
            <div class="nit-dui cutter">
                {{ empty($receipt_details->customer_nit) ? $receipt_details->customer_dui : $receipt_details->customer_nit }}
            </div>

            {{-- VENDEDOR --}}
            <div class="seller cutter">
                {{ $receipt_details->customer_seller }}
            </div>

            {{-- ZONA --}}
            <div class="zone cutter">
                &nbsp;
            </div>

            {{-- GIRO --}}
            <div class="business-activity cutter">
                {{ $receipt_details->customer_business_activity }}
            </div>

            {{-- CONDICIONES DE PAGO --}}
            <div class="payment-condition cutter">
                {{ __('messages.' . $receipt_details->payment_condition) }}
            </div>
		</div>

		<div class="details">
			<table class="sell_lines">
				<thead>
					<tr>
						{{-- CODIGO --}}
						<th style="width: 1.9cm; height: 0.65cm;">&nbsp;</th>

                        {{-- CANTIDAD --}}
						<th style="width: 1.5cm; height: 0.65cm">&nbsp;</th>

						{{-- DESCRIPCIÓN --}}
						<th style="width: 7.6cm; height: 0.65cm">&nbsp;</th>

                        {{-- PRESENTACIÓN --}}
						<th style="width: 2.3cm; height: 0.65cm">&nbsp;</th>
						
						{{-- PRECIO UNITARIO --}}
						<th style="width: 1.5cm; height: 0.65cm">&nbsp;</th>

                        {{-- VENTAS NO SUJETAS --}}
						<th style="width: 1.0cm; height: 0.65cm">&nbsp;</th>

                        {{-- VENTAS EXENTAS --}}
						<th style="width: 1.0cm; height: 0.65cm">&nbsp;</th>

						{{-- VENTAS GRAVADAS --}}
						<th style="width: 2.1cm; height: 0.65cm">&nbsp;</th>
					</tr>
				</thead>

				<tbody>
					@forelse($receipt_details->lines as $line)
					<tr>
                        {{-- CÓDIGO --}}
                        <td class="cutter" style="padding-left: 0.2cm">
							{{ $line['id'] }}
						</td>

						{{-- CANTIDAD --}}
						<td class="cutter text-right" style="padding-right: 0.3cm;">
							{{ $line['quantity'] }}
						</td>

						{{-- DESCRIPCIÓN --}}
						<td style="padding-left: 0.3cm">
							{{ $line['sub_sku'] }} {{ $line['name'] }} {{ $line['variation'] }}
							@if (! empty($line['sell_line_note'])) ({{ $line['sell_line_note'] }}) @endif 
							@if (! empty($line['lot_number'])) <br> {{ $line['lot_number_label'] }}:  {{ $line['lot_number'] }} @endif 
							@if (! empty($line['product_expiry'])), {{ $line['product_expiry_label'] }}:  {{ $line['product_expiry'] }} @endif
						</td>

                        {{-- PRESENTACIÓN --}}
						<td class="cutter" style="padding-left: 0.3cm">
							&nbsp;
						</td>

						{{-- PRECIO UNITARIO --}}
						<td class="text-right" style="padding-right: 0.3cm">
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['unit_price_exc'] }}
							</span>
						</td>

						{{-- VENTAS NO SUJETAS --}}
						<td class="text-right" style="padding-right: 0cm">
							&nbsp;
						</td>

                        {{-- VENTAS EXENTAS --}}
                        <td class="text-right" style="padding-right: 0cm">
							@if ($receipt_details->is_exempt == 1)
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['line_total_exc_tax'] }}
							</span>
							@else
							&nbsp;
							@endif
						</td>

                        {{-- VENTAS GRAVADAS --}}
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
						<td colspan="8">&nbsp;</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<div class="footer">
            {{-- SON --}}
            <div class="total-letters">
                {{ $receipt_details->total_letters }}
            </div>

            {{-- SUMAS --}}
            <div class="sums">
                @if ($receipt_details->is_exempt == 0)
                <span class="display_currency" data-currency_symbol="false">
                    {{ $receipt_details->total_before_tax }}
                </span>
                @else
                &nbsp;
                @endif
            </div>

            {{-- 13% IVA --}}
            <div class="iva">
                @if ($receipt_details->is_exempt == 0)
                <span class="display_currency" data-currency_symbol="false">
                    {{ $receipt_details->tax_amount }}
                </span>
                @else
                &nbsp;
                @endif
            </div>

            {{-- SUB-TOTAL --}}
            <div class="subtotal">
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

            {{-- VENTA PERCIBIDO --}}
            {{--<div">
                &nbsp;
            </div>--}}

            {{-- (-) IVA RETENIDO --}}
            <div class="iva-withheld">
                @if ($receipt_details->is_exempt == 0 && $receipt_details->withheld)
                <span class="display_currency" data-currency_symbol="false">
                    {{ $receipt_details->withheld }}
                </span>
                @else
                &nbsp;
                @endif
            </div>

            {{-- VENTA NO SUJETA --}}
            {{--<div>
                &nbsp;
            </div>--}}

            {{-- VENTA EXENTA --}}
            <div class="exempt">
                @if ($receipt_details->is_exempt == 1)
                <span class="display_currency" data-currency_symbol="false">
                    {{ $receipt_details->total }}
                </span>
                @else
                &nbsp;
                @endif
            </div>

            {{-- TOTAL --}}
            <div class="total">
                <span class="display_currency" data-currency_symbol="false">
                    {{ $receipt_details->total }}
                </span>
            </div>
		</div>
	</div>
</div>
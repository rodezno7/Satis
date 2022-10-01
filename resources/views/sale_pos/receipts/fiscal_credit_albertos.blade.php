<style>
    div.main {
        width: 100%;
        font-size: 7pt;
        font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
        margin: 0;
    }

    div.container {
        width: 18.4cm;
        margin: 0cm 1.8cm 0cm 1.1cm;
    }

    div.container table {
        width: 100%;
    }

    div.container table td {
        vertical-align: top;
    }

    div.head {
        height: 3.75cm;
        width: 100%;
    }

    div.header {
        position: relative;
        height: 2.1cm;
        width: 100%;
    }

    div.header div.customer-code {
        position: absolute;
        left: 1.5cm;
        top: 0.05cm;
        width: 2.0cm;
    }

    div.header div.customer {
        position: absolute;
        left: 5.2cm;
        top: 0.05cm;
        width: 8.7cm;
    }

    div.header div.date {
        position: absolute;
        left: 15.5cm;
        top: 0.05cm;
        width: 2.5cm;
    }
    div.header div.address {
        position: absolute;
        left: 1.9cm;
        top: 0.45cm;
        width: 12.5cm;
    }

    div.header div.nrc {
        position: absolute;
        left: 15.6cm;
        top: 0.45cm;
        width: 2.5cm;
    }

    div.header div.city {
        position: absolute;
        left: 1.9cm;
        top: 0.95cm;
        width: 2.0cm;
    }

    div.header div.business-activity {
        position: absolute;
        left: 5.3cm;
        top: 0.95cm;
        width: 8.2cm;
    }

    div.header div.nit {
        position: absolute;
        left: 14.55cm;
        top: 1.0cm;
        width: 3.5cm;
    }

    div.header div.referral-note {
        position: absolute;
        left: 3.5cm;
        top: 1.45cm;
        width: 2.7cm;
    }

    div.header div.seller {
        position: absolute;
        left: 9.2cm;
        top: 1.45cm;
        width: 2.5cm;
    }

    div.header div.payment-condition {
        position: absolute;
        left: 15.5cm;
        top: 1.45cm;
        width: 2.5cm;
    }

    div.header div.referral-note-date {
        position: absolute;
        left: 4.2cm;
        top: 1.45cm;
        width: 1.16cm;
    }

    div.header div.referral-note-previous {
        position: absolute;
        left: 10.2cm;
        top: 1.5cm;
        width: 2.5cm;
    }

	@if ($receipt_details->discount_amount > 0)
    
	div.details {
        height: 3.7cm;
    }

	div.extra-details {
        position: relative;
        height: 0.6cm;
    }

	div.extra-details div.discount-text {
        position: absolute;
        left: 12.0cm;
        top: 0.2cm;
        width: 4.0cm;
    }

	div.extra-details div.discount-amount {
        position: absolute;
        left: 16.6cm;
		top: 0.2cm;
		width: 1.6cm;
        text-align: right;
        padding-right: 0.2cm;
    }

	@else

	div.details {
        height: 4.3cm;
    }
	
    @endif

    div.details table.sell_lines {
        table-layout: fixed;
    }

	div.details table.sell_lines thead tr {
        height: 0.75cm;
    }

	div.details table.sell_lines tbody tr {
        height: 0.4cm;
    }

	div.details table.sell_lines tbody td {
        padding-left: 0.1cm;
    }

    div.footer {
		position: relative;
        height: 3.3cm;
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
        left: 16.6cm;
		top: 0.1cm;
		width: 1.6cm;
        text-align: right;
        padding-right: 0.2cm;
	}

    div.footer div.iva {
        position: absolute;
        left: 16.6cm;
		top: 0.5cm;
		width: 1.6cm;
        text-align: right;
        padding-right: 0.2cm;
	}

    div.footer div.subtotal {
		position: absolute;
        left: 16.6cm;
		top: 0.85cm;
		width: 1.6cm;
        text-align: right;
        padding-right: 0.2cm;
	}

    div.footer div.iva-withheld {
		position: absolute;
        left: 16.6cm;
		top: 1.2cm;
		width: 1.6cm;
        text-align: right;
        padding-right: 0.2cm;
	}

    div.footer div.not-subject {
		position: absolute;
        left: 16.6cm;
		top: 1.6cm;
		width: 1.6cm;
        text-align: right;
        padding-right: 0.2cm;
	}

    div.footer div.exempt {
		position: absolute;
        left: 16.6cm;
		top: 1.9cm;
		width: 1.6cm;
        text-align: right;
        padding-right: 0.2cm;
	}

    div.footer div.total {
		position: absolute;
        left: 16.6cm;
		top: 2.3cm;
		width: 1.6cm;
        text-align: right;
        padding-right: 0.2cm;
	}

    div.footer div.received-name {
		position: absolute;
        left: 1.2cm;
		top: 1.25cm;
		width: 6.5cm;
        font-size: 6pt;
	}

    div.footer div.received-dui {
		position: absolute;
        left: 1.2cm;
		top: 1.6cm;
		width: 6.5cm;
        font-size: 6pt;
	}

    div.footer div.delivered-name {
		position: absolute;
        left: 9.2cm;
		top: 1.25cm;
		width: 4.0cm;
        font-size: 6pt;
	}

    div.footer div.delivered-dui {
		position: absolute;
        left: 9.2cm;
		top: 1.6cm;
		width: 4.0cm;
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
    
<div class="main">
    @for ($i = 0; $i < 2; $i++)
	<div class="container">

        {{-- HEAD --}}
		<div class="head"></div>

		<div class="header">
            {{-- CÓDIGO --}}
            <div class="customer-code cutter">
                {{ $receipt_details->customer_id }}
            </div>

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

            {{-- MUNICIPIO --}}
            <div class="city cutter">
                {{ $receipt_details->customer_city }}
            </div>

            {{-- GIRO --}}
            <div class="business-activity cutter">
                {{ $receipt_details->customer_business_activity }}
            </div>

            {{-- NIT --}}
            <div class="nit cutter">
                {{ $receipt_details->customer_nit }}
            </div>

            @if ($i == 0)
                {{-- SOBRE NOTA/REMISIÓN --}}
                <div class="referral-note cutter">
                    &nbsp;
                </div>

                {{-- VENTA A CUENTA DE --}}
                <div class="seller cutter">
                    {{ $receipt_details->commission_agent }}
                </div>
            @else
                {{-- FECHA DE NOTA DE REMISIÓN --}}
                <div class="referral-note-date cutter">
                    &nbsp;
                </div>

                {{-- NOTA DE REMISIÓN ANTERIOR --}}
                <div class="referral-note-previous cutter">
                    &nbsp;
                </div>
            @endif

            {{-- CONDICIONES DE PAGO --}}
            <div class="payment-condition cutter">
                {{ __('messages.' . $receipt_details->payment_condition) }}
            </div>
		</div>

		<div class="details">
			<table class="sell_lines">
				<thead>
					<tr>
						{{-- CANTIDAD --}}
						<th style="width: 1.1cm">&nbsp;</th>

                        {{-- CÓDIGO --}}
						<th style="width: 2.2cm">&nbsp;</th>

						{{-- DESCRIPCIÓN --}}
						<th style="width: 8.7cm">&nbsp;</th>
						
						{{-- PRECIO UNITARIO --}}
						<th style="width: 1.6cm">&nbsp;</th>

                        {{-- VENTAS NO SUJETAS --}}
						<th style="width: 1.4cm">&nbsp;</th>

                        {{-- VENTAS EXENTAS --}}
						<th style="width: 1.4cm">&nbsp;</th>

						{{-- VENTAS AFECTAS --}}
						<th style="width: 2.1cm">&nbsp;</th>
					</tr>
				</thead>

				<tbody>
					@forelse ($receipt_details->lines as $line)
					<tr>
						{{-- CANTIDAD --}}
						<td class="cutter text-right" style="padding-right: 0.2cm">
							{{ $line['quantity'] }}
						</td>

                        {{-- CÓDIGO --}}
                        <td class="cutter" style="padding-right: 0.2cm">
							{{ $line['sub_sku'] }}
						</td>

						{{-- DESCRIPCION --}}
						<td class="cutter" style="padding-left: 0.12cm">
							{{ $line['name'] }} {{$line['variation'] }}
							@if(! empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif 
							@if(! empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
							@if(! empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif
                            @if ($line['line_discount'] > 0) <br> (DESC. {{ $line['line_discount'] }}) @endif
						</td>

						{{-- PRECIO UNITARIO --}}
						<td class="text-right" style="padding-right: 0.3cm">
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['unit_price_exc'] }}
							</span>
						</td>

						{{-- VENTAS NO SUJETAS --}}
						<td class="text-right" style="padding-right: 0.3cm">
							&nbsp;
						</td>

                        {{-- VENTAS EXENTAS --}}
                        <td class="text-right" style="padding-right: 0.3cm">
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
								{{ $line['line_total_exc_tax'] - $line['line_discount'] }}
							</span>
							@else
							&nbsp;
							@endif
						</td>
					</tr>
                    @if (isset($line['spare_rows']))
                        @forelse ($line['spare_rows'] as $spare_row)
                        <tr>
                            {{-- CANTIDAD --}}
                            <td class="cutter text-right" style="padding-right: 0.2cm">
                                {{ $spare_row['quantity'] }}
                            </td>

                            {{-- CÓDIGO --}}
                            <td class="cutter" style="padding-right: 0.2cm">
                                {{ $spare_row['sub_sku'] }}
                            </td>

                            {{-- DESCRIPCION --}}
                            <td class="cutter" style="padding-left: 0.12cm">
                                {{ $spare_row['name'] }} {{$spare_row['variation'] }}
                                @if(! empty($spare_row['sell_line_note']))({{$spare_row['sell_line_note']}}) @endif 
                                @if(! empty($spare_row['lot_number']))<br> {{$spare_row['lot_number_label']}}:  {{$spare_row['lot_number']}} @endif 
                                @if(! empty($spare_row['product_expiry'])), {{$spare_row['product_expiry_label']}}:  {{$spare_row['product_expiry']}} @endif
                                @if ($spare_row['line_discount'] > 0) <br> (DESC. {{ $spare_row['line_discount'] }}) @endif
                            </td>

                            {{-- PRECIO UNITARIO --}}
                            <td class="text-right" style="padding-right: 0.3cm">
                                <span class="display_currency" data-currency_symbol="false">
                                    {{ $spare_row['unit_price_exc'] }}
                                </span>
                            </td>

                            {{-- VENTAS NO SUJETAS --}}
                            <td class="text-right" style="padding-right: 0.3cm">
                                &nbsp;
                            </td>

                            {{-- VENTAS EXENTAS --}}
                            <td class="text-right" style="padding-right: 0.3cm">
                                @if ($receipt_details->is_exempt == 1)
                                <span class="display_currency" data-currency_symbol="false">
                                    {{ $spare_row['line_total_exc_tax'] }}
                                </span>
                                @else
                                &nbsp;
                                @endif
                            </td>

                            {{-- VENTAS GRAVADAS --}}
                            <td class="text-right" style="padding-right: 0.3cm">
                                @if ($receipt_details->is_exempt == 0)
                                <span class="display_currency" data-currency_symbol="false">
                                    {{ $spare_row['line_total_exc_tax'] - $spare_row['line_discount'] }}
                                </span>
                                @else
                                &nbsp;
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">&nbsp;</td>
                        </tr>
                        @endforelse
                    @endif
					@empty
					<tr>
						<td colspan="7">&nbsp;</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		@if ($receipt_details->discount_amount > 0)
		<div class="extra-details">
			<div class="discount-text">
				DESCUENTO (-) {{ $receipt_details->discount_percent }}
			</div>

			<div class="discount-amount">
				<span class="display_currency" data-currency_symbol="false">
					{{ $receipt_details->discount_amount }}
				</span>
			</div>
		</div>
		@endif

		<div class="footer">
            {{-- SON --}}
            <div class="total-letters">
                {{ $receipt_details->total_letters }}
            </div>

            {{-- RECIBIDO POR (NOMBRE) --}}
            <div class="received-name cutter">
                {{ $receipt_details->received_by }}
            </div>

            {{-- RECIBIDO POR (DUI) --}}
            <div class="received-dui cutter">
                {{ $receipt_details->received_by_dui }}
            </div>

            {{-- ENTREGADO POR (NOMBRE) --}}
            <div class="delivered-name cutter">
                {{ $receipt_details->delivered_by }}
            </div>

            {{-- ENTREGADO POR (DUI) --}}
            <div class="delivered-dui cutter">
                {{ $receipt_details->delivered_by_dui }}
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

            {{-- (+) IVA --}}
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

            {{-- VENTAS NO SUJETAS --}}
            <div class="not-subject">
                &nbsp;
            </div>

            {{-- VENTAS EXENTAS --}}
            <div class="exempt">
                @if ($receipt_details->is_exempt == 1)
                <span class="display_currency" data-currency_symbol="false">
                    {{ $receipt_details->total }}
                </span>
                @else
                &nbsp;
                @endif
            </div>

            {{-- TOTAL GENERAL --}}
            <div class="total">
                <span class="display_currency" data-currency_symbol="false">
                    {{ $receipt_details->total }}
                </span>
            </div>
		</div>
	</div>
    @endfor
</div>
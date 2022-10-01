<style>
    div.main {
        width: 100%;
        font-size: 7pt;
        font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
        margin: 0;
    }

    div.container {
        width: 18.4cm;
        margin: 0cm 1.2cm 0cm 0.6cm;
    }

    div.container table {
        width: 100%;
    }

    div.container table td {
        vertical-align: middle;
    }

    div.head {
        height: 5.1cm;
        width: 100%;
    }

    div.header {
        position: relative;
        height: 3.9cm;
        width: 100%;
    }

    div.header div.customer {
        position: absolute;
        left: 1.7cm;
        top: 0.3cm;
        width: 11.7cm;
    }

    div.header div.date {
        position: absolute;
        left: 15.1cm;
        top: 0.3cm;
        width: 3.0cm;
    }
    div.header div.address {
        position: absolute;
        left: 2.0cm;
        top: 0.9cm;
        width: 9.7cm;
    }

    div.header div.nrc {
        position: absolute;
        left: 14.1cm;
        top: 0.9cm;
        width: 4.0cm;
    }

    div.header div.business-activity {
        position: absolute;
        left: 10.3cm;
        top: 1.5cm;
        width: 7.8cm;
    }

    div.header div.state {
        position: absolute;
        left: 2.7cm;
        top: 2.2cm;
        width: 7.5cm;
    }

    div.header div.nit {
        position: absolute;
        left: 11.5cm;
        top: 2.2cm;
        width: 6.6cm;
    }

    div.header div.seller {
        position: absolute;
        left: 3.0cm;
        top: 2.8cm;
        width: 7.2cm;
    }

    div.header div.referral-note-date {
        position: absolute;
        left: 15.2cm;
        top: 2.8cm;
        width: 2.9cm;
    }

    div.header div.referral-note-previous {
        position: absolute;
        left: 4.5cm;
        top: 3.5cm;
        width: 5.7cm;
    }

    div.header div.payment-condition {
        position: absolute;
        left: 14.2cm;
        top: 3.5cm;
        width: 3.9cm;
    }

	@if ($receipt_details->discount_amount > 0)
    
	div.details {
        height: 10.5cm;
    }

	div.extra-details {
        position: relative;
        height: 0.6cm;
    }

	div.extra-details div.discount-text {
        position: absolute;
        left: 11.5cm;
        top: 0.25cm;
        width: 4.3cm;
    }

	div.extra-details div.discount-amount {
        position: absolute;
        left: 16.4cm;
		top: 0.25cm;
		width: 1.8cm;
        text-align: right;
        padding-right: 0.2cm;
    }

	@else

	div.details {
        height: 11.1cm;
    }
	
    @endif

    div.details table.sell_lines {
        table-layout: fixed;
    }

	div.details table.sell_lines thead tr {
        height: 0.75cm;
    }

	div.details table.sell_lines tbody tr {
        height: 0.6cm;
    }

	div.details table.sell_lines tbody td {
        padding-left: 0.1cm;
        padding-top: 0.2cm;
    }

    div.footer {
		position: relative;
        height: 3.8cm;
        width: 100%;
	}

    div.footer div.total-letters {
		position: absolute;
        left: 0.25cm;
		top: 0.7cm;
		width: 10.7cm;
	}

    div.footer div.sums {
		position: absolute;
        left: 16.4cm;
		top: 0.25cm;
		width: 1.8cm;
        text-align: right;
        padding-right: 0.2cm;
	}

    div.footer div.iva {
        position: absolute;
        left: 16.4cm;
		top: 0.8cm;
		width: 1.8cm;
        text-align: right;
        padding-right: 0.2cm;
	}

    div.footer div.subtotal {
		position: absolute;
        left: 16.4cm;
		top: 1.35cm;
		width: 1.8cm;
        text-align: right;
        padding-right: 0.2cm;
	}

    div.footer div.iva-withheld {
		position: absolute;
        left: 16.4cm;
		top: 1.85cm;
		width: 1.8cm;
        text-align: right;
        padding-right: 0.2cm;
	}

    div.footer div.not-subject {
		position: absolute;
        left: 16.4cm;
		top: 2.4cm;
		width: 1.8cm;
        text-align: right;
        padding-right: 0.2cm;
	}

    div.footer div.exempt {
		position: absolute;
        left: 16.4cm;
		top: 2.95cm;
		width: 1.8cm;
        text-align: right;
        padding-right: 0.2cm;
	}

    div.footer div.total {
		position: absolute;
        left: 16.4cm;
		top: 3.5cm;
		width: 1.8cm;
        text-align: right;
        padding-right: 0.2cm;
	}

    div.footer div.received-name {
		position: absolute;
        left: 1.5cm;
		top: 2.1cm;
		width: 3.9cm;
        font-size: 6pt;
	}

    div.footer div.received-dui {
		position: absolute;
        left: 1.5cm;
		top: 2.5cm;
		width: 3.9cm;
        font-size: 6pt;
	}

    div.footer div.delivered-name {
		position: absolute;
        left: 7.5cm;
		top: 2.1cm;
		width: 3.6cm;
        font-size: 6pt;
	}

    div.footer div.delivered-dui {
		position: absolute;
        left: 7.5cm;
		top: 2.5cm;
		width: 3.6cm;
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

            {{-- REGISTRO NO. --}}
            <div class="nrc cutter">
                {{ $receipt_details->customer_tax_number }}
            </div>

            {{-- GIRO --}}
            <div class="business-activity cutter">
                {{ $receipt_details->customer_business_activity }}
            </div>

            {{-- DEPARTAMENTO --}}
            <div class="state cutter">
                {{ $receipt_details->customer_state }}
            </div>

            {{-- NIT --}}
            <div class="nit cutter">
                {{ $receipt_details->customer_nit }}
            </div>

            {{-- VENTA A CUENTA DE --}}
            <div class="seller cutter">
                {{ $receipt_details->commission_agent }}
            </div>

            {{-- NOTA DE REMISIÓN ANTERIOR --}}
            <div class="referral-note-previous cutter">
                &nbsp;
            </div>

            {{-- FECHA DE NOTA DE REMISIÓN --}}
            <div class="referral-note-date cutter">
                &nbsp;
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
						{{-- CANTIDAD --}}
						<th style="width: 1.4cm">&nbsp;</th>

						{{-- DESCRIPCIÓN --}}
						<th style="width: 9.9cm">&nbsp;</th>
						
						{{-- PRECIO UNITARIO --}}
						<th style="width: 1.6cm">&nbsp;</th>

                        {{-- VENTAS NO SUJETAS --}}
						<th style="width: 1.75cm">&nbsp;</th>

                        {{-- VENTAS EXENTAS --}}
						<th style="width: 1.4cm">&nbsp;</th>

						{{-- VENTAS AFECTAS --}}
						<th style="width: 2.3cm">&nbsp;</th>
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
						<td class="cutter" style="padding-left: 0.4cm">
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
                    @if (isset($line['spare_rows']))
                        @forelse ($line['spare_rows'] as $spare_row)
                        <tr>
                            {{-- CANTIDAD --}}
                            <td class="cutter text-right" style="padding-right: 0.2cm">
                                {{ $spare_row['quantity'] }}
                            </td>

                            {{-- DESCRIPCION --}}
                            <td class="cutter" style="padding-left: 0.4cm">
                                {{ $spare_row['name'] }} {{$spare_row['variation'] }}
                                @if(! empty($spare_row['sell_line_note']))({{$spare_row['sell_line_note']}}) @endif 
                                @if(! empty($spare_row['lot_number']))<br> {{$spare_row['lot_number_label']}}:  {{$spare_row['lot_number']}} @endif 
                                @if(! empty($spare_row['product_expiry'])), {{$spare_row['product_expiry_label']}}:  {{$spare_row['product_expiry']}} @endif
                            </td>

                            {{-- PRECIO UNITARIO --}}
                            <td class="text-right" style="padding-right: 0.2cm">
                                <span class="display_currency" data-currency_symbol="false">
                                    {{ $spare_row['unit_price_exc'] }}
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
                                    {{ $spare_row['line_total_exc_tax'] }}
                                </span>
                                @else
                                &nbsp;
                                @endif
                            </td>

                            {{-- VENTAS GRAVADAS --}}
                            <td class="text-right" style="padding-right: 0.2cm">
                                @if ($receipt_details->is_exempt == 0)
                                <span class="display_currency" data-currency_symbol="false">
                                    {{ $spare_row['line_total_exc_tax'] }}
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
                    @endif
					@empty
					<tr>
						<td colspan="6">&nbsp;</td>
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

            {{-- 13% DE IVA --}}
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

            {{-- TOTAL --}}
            <div class="total">
                <span class="display_currency" data-currency_symbol="false">
                    {{ $receipt_details->total }}
                </span>
            </div>
		</div>
	</div>
</div>
<style>
    div.main {
        width: 20.9cm;
        margin: 0 0.1cm;
        /*border: 1px solid #ff0000;*/
        font-size: 6pt;
        font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
    }

	div.head {
        height: 2.5cm;
        width: 100%;
    }

    div.container table {
        width: 100%;
        table-layout: fixed;
    }

    div.header {
        height: 1.4cm;
    }

    div.container table td {
        vertical-align: top;
    }

    div.details {
        height: 4.7cm;
    }
    div.details table thead th{
        height: 0.7cm;
    }

    div.footer {
        height: 1.7cm;
        width: 100%;
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

<div class="main" style="margin-bottom: 0.6cm;">
    @php
    $precision = 4;
    @endphp
	<div class="container">

        {{-- HEAD --}}
		<div class="head"></div>

		<div class="header">
            <table style="margin-top: 0.2cm;">
                <tr>
                    <td style="width: 2cm; padding-left: 0.3cm;"><b>CLIENTE</b></td>
                    <td style="width: 6cm;" class="cutter">{{ $receipt_details->customer_name }}</td>
                    <td style="width: 2cm;"><b>FECHA</b></td>
                    <td style="width: 3cm;">{{ @format_date($receipt_details->invoice_date) }}</td>
                    <td style="width: 3cm;;"><b>GIRO</b></td>
                    <td class="cutter">{{ $receipt_details->customer_business_activity }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 0.3cm;"><b>DUI</b></td>
                    <td>{{ $receipt_details->customer_dui }}</td>
                    <td><b>NRC</b></td>
                    <td>{{ $receipt_details->customer_tax_number }}</td>
                    <td><b>NIT</b></td>
                    <td>{{ $receipt_details->customer_nit }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 0.3cm;"><b>DIRECCIÓN</b></td>
                    <td class="cutter">{{ $receipt_details->customer_landmark }}</td>
                    <td><b>MUNICIPIO</b></td>
                    <td class="cutter">{{ $receipt_details->customer_city }}</td>
                    <td><b>CONDICIÓN DE PAGO</b></td>
                    <td>{{ __('messages.' . $receipt_details->payment_condition) }}</td>
                </tr>
            </table>
		</div>

		<div class="details">
			<table class="sell_lines">
				<thead>
					<tr>
						{{-- CANTIDAD --}}
						<th style="width: 0.9cm">&nbsp;</th>

						{{-- DESCRIPCIÓN --}}
						<th>&nbsp;</th>
						
						{{-- PRECIO UNITARIO --}}
						<th style="width: 1.2cm">&nbsp;</th>

                        {{-- VENTAS NO SUJETAS --}}
						<th style="width: 1cm">&nbsp;</th>

                        {{-- VENTAS EXENTAS --}}
						<th style="width: 1cm">&nbsp;</th>

						{{-- VENTAS GRAVADAS --}}
						<th style="width: 2cm">&nbsp;</th>
					</tr>
				</thead>

				<tbody>
					@forelse ($receipt_details->lines as $line)
					<tr>
						{{-- CANTIDAD --}}
						<td class="cutter text-right" style="padding-right: 0.2cm">
							{{ $line['quantity'] }}
						</td>

						{{-- DESCRIPCION --}}
						<td class="cutter" style="padding-left: 0.3cm">
							{{ $line['name'] }} {{$line['variation'] }}
							@if(! empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif 
							@if(! empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
							@if(! empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif
                            @if ($line['line_discount'] > 0) (DESC. {{ $line['line_discount'] }}) @endif
						</td>

						{{-- PRECIO UNITARIO --}}
						<td class="text-right">
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['unit_price_exc'] }}
							</span>
						</td>

						{{-- VENTAS NO SUJETAS --}}
						<td class="text-right">
							&nbsp;
						</td>

                        {{-- VENTAS EXENTAS --}}
                        <td class="text-right">
							@if ($receipt_details->is_exempt == 1)
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['unit_price_exc_tax'] }}
							</span>
							@else
							&nbsp;
							@endif
						</td>

                        {{-- VENTAS GRAVADAS --}}
                        <td class="text-right">
							@if ($receipt_details->is_exempt == 0)
							{{-- <span class="display_currency" data-currency_symbol="false"> --}}
                                {{ number_format($line['unit_price_exc_tax'], $precision) }}
							{{-- </span> --}}
							@else
							&nbsp;
							@endif
						</td>
					</tr>
                    @if (isset($line['spare_rows']))
                        @foreach ($line['spare_rows'] as $spare_row)
                        <tr>
                            {{-- CANTIDAD --}}
                            <td class="cutter text-right">
                                {{ $spare_row['quantity'] }}
                            </td>

                            {{-- DESCRIPCION --}}
                            <td class="cutter" style="padding-left: 0.3cm">
                                {{ $spare_row['name'] }} {{$spare_row['variation'] }}
                                @if(! empty($spare_row['sell_line_note']))({{$spare_row['sell_line_note']}}) @endif 
                                @if(! empty($spare_row['lot_number']))<br> {{$spare_row['lot_number_label']}}:  {{$spare_row['lot_number']}} @endif 
                                @if(! empty($spare_row['product_expiry'])), {{$spare_row['product_expiry_label']}}:  {{$spare_row['product_expiry']}} @endif
                                @if ($spare_row['line_discount'] > 0) (DESC. {{ $spare_row['line_discount'] }}) @endif
                            </td>

                            {{-- PRECIO UNITARIO --}}
                            <td class="text-right">
                                <span class="display_currency" data-currency_symbol="false">
                                    {{ $spare_row['unit_price_exc'] }}
                                </span>
                            </td>

                            {{-- VENTAS NO SUJETAS --}}
                            <td class="text-right">
                                &nbsp;
                            </td>

                            {{-- VENTAS EXENTAS --}}
                            <td class="text-right">
                                @if ($receipt_details->is_exempt == 1)
                                <span class="display_currency" data-currency_symbol="false">
                                    {{ $spare_row['unit_price_exc_tax'] }}
                                </span>
                                @else
                                &nbsp;
                                @endif
                            </td>

                            {{-- VENTAS GRAVADAS --}}
                            <td class="text-right">
                                @if ($receipt_details->is_exempt == 0)
                                {{-- <span class="display_currency" data-currency_symbol="false"> --}}
                                    {{ number_format($spare_row['unit_price_exc_tax'], $precision) }}
                                {{-- </span> --}}
                                @else
                                &nbsp;
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @endif
					@empty
					<tr>
						<td colspan="6">&nbsp;</td>
					</tr>
					@endforelse
				</tbody>

                @if ($receipt_details->discount_amount > 0)
                <tfoot>
                    <tr>
                        <td></td>
                        <td style="padding-left: 0.3cm">DESCUENTO</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">
                            <span class="display_currency" data-currency_symbol="false">
                                {{ $receipt_details->discount_amount }}
                            </span>
                            <span style="float: left;">(-)</span>
                        </td>
                    </tr>
                </tfoot>
                @endif
			</table>
		</div>

		<div class="footer" style="font-size: 5pt;">
            <table>
                <tr>{{-- SUMAS --}}
                    <td rowspan="6" style="padding-left: 1cm; vertical-align: top; font-size: 6pt;">{{ $receipt_details->total_letters }}</td>
                    <td rowspan="3" style="width: 2.35cm;">&nbsp;</td>
                    <td style="width: 2.1cm;" class="text-right">
                    @if ($receipt_details->is_exempt == 0)
                        <span class="display_currency" data-currency_symbol="false">
                            {{ $receipt_details->total_before_tax }}
                        </span>
                    @else
                        &nbsp;
                    @endif
                    </td>
                </tr>
                <tr>{{-- IVA --}}
                    <td class="text-right">
                    @if ($receipt_details->is_exempt == 0)
                    <span class="display_currency" data-currency_symbol="false">
                        {{ $receipt_details->tax_amount }}
                    </span>
                    @else
                    &nbsp;
                    @endif
                    </td>
                </tr>
                <tr>{{-- SUBTOTAL --}}
                    <td class="text-right">
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
                    </td>
                </tr>
                <tr>{{-- IVA PERCIBIDO --}}
                    <td rowspan="3">&nbsp;</td>
                    <td class="text-right less-padding">
                    @if ($receipt_details->is_exempt == 0 && $receipt_details->withheld)
                    <span class="display_currency" data-currency_symbol="false">
                        {{ $receipt_details->withheld }}
                    </span>
                    @else
                        &nbsp;
                    @endif
                    </td>
                </tr>
                <tr>{{-- VENTAS EXENTAS --}}
                    <td class="text-right">
                    @if ($receipt_details->is_exempt == 1)
                    <span class="display_currency" data-currency_symbol="false">
                        {{ $receipt_details->total }}
                    </span>
                    @else
                        &nbsp;
                    @endif
                    </td>
                </tr>
                <tr>{{-- TOTAL GENERAL --}}
                    <td class="text-right">
                        <span class="display_currency" data-currency_symbol="false">
                            {{ $receipt_details->total }}
                        </span>
                    </td>
                </tr>
            </table>
		</div>
	</div>
</div>
<div class="main" style="margin-bottom: 0.5cm;"> {{-- 2 --}}
	<div class="container">

        {{-- HEAD --}}
		<div class="head"></div>

		<div class="header">
            <table style="margin-top: 0.2cm;">
                <tr>
                    <td style="width: 2cm; padding-left: 0.3cm;"><b>CLIENTE</b></td>
                    <td style="width: 6cm;" class="cutter">{{ $receipt_details->customer_name }}</td>
                    <td style="width: 2cm;"><b>FECHA</b></td>
                    <td style="width: 3cm;">{{ @format_date($receipt_details->invoice_date) }}</td>
                    <td style="width: 3cm;;"><b>GIRO</b></td>
                    <td class="cutter">{{ $receipt_details->customer_business_activity }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 0.3cm;"><b>DUI</b></td>
                    <td>{{ $receipt_details->customer_dui }}</td>
                    <td><b>NRC</b></td>
                    <td>{{ $receipt_details->customer_tax_number }}</td>
                    <td><b>NIT</b></td>
                    <td>{{ $receipt_details->customer_nit }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 0.3cm;"><b>DIRECCIÓN</b></td>
                    <td class="cutter">{{ $receipt_details->customer_landmark }}</td>
                    <td><b>MUNICIPIO</b></td>
                    <td class="cutter">{{ $receipt_details->customer_city }}</td>
                    <td><b>CONDICIÓN DE PAGO</b></td>
                    <td>{{ __('messages.' . $receipt_details->payment_condition) }}</td>
                </tr>
            </table>
		</div>

		<div class="details">
			<table class="sell_lines">
				<thead>
					<tr>
						{{-- CANTIDAD --}}
						<th style="width: 0.9cm">&nbsp;</th>

						{{-- DESCRIPCIÓN --}}
						<th>&nbsp;</th>
						
						{{-- PRECIO UNITARIO --}}
						<th style="width: 1.2cm">&nbsp;</th>

                        {{-- VENTAS NO SUJETAS --}}
						<th style="width: 1cm">&nbsp;</th>

                        {{-- VENTAS EXENTAS --}}
						<th style="width: 1cm">&nbsp;</th>

						{{-- VENTAS GRAVADAS --}}
						<th style="width: 2cm">&nbsp;</th>
					</tr>
				</thead>

				<tbody>
					@forelse ($receipt_details->lines as $line)
					<tr>
						{{-- CANTIDAD --}}
						<td class="cutter text-right" style="padding-right: 0.2cm">
							{{ $line['quantity'] }}
						</td>

						{{-- DESCRIPCION --}}
						<td class="cutter" style="padding-left: 0.3cm">
							{{ $line['name'] }} {{$line['variation'] }}
							@if(! empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif 
							@if(! empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
							@if(! empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif
                            @if ($line['line_discount'] > 0) (DESC. {{ $line['line_discount'] }}) @endif
						</td>

						{{-- PRECIO UNITARIO --}}
						<td class="text-right">
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['unit_price_exc'] }}
							</span>
						</td>

						{{-- VENTAS NO SUJETAS --}}
						<td class="text-right">
							&nbsp;
						</td>

                        {{-- VENTAS EXENTAS --}}
                        <td class="text-right">
							@if ($receipt_details->is_exempt == 1)
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['unit_price_exc_tax'] }}
							</span>
							@else
							&nbsp;
							@endif
						</td>

                        {{-- VENTAS GRAVADAS --}}
                        <td class="text-right">
							@if ($receipt_details->is_exempt == 0)
							{{-- <span class="display_currency" data-currency_symbol="false"> --}}
								{{ number_format($line['unit_price_exc_tax'], $precision) }}
							{{-- </span> --}}
							@else
							&nbsp;
							@endif
						</td>
					</tr>
                    @if (isset($line['spare_rows']))
                        @foreach ($line['spare_rows'] as $spare_row)
                        <tr>
                            {{-- CANTIDAD --}}
                            <td class="cutter text-right">
                                {{ $spare_row['quantity'] }}
                            </td>

                            {{-- DESCRIPCION --}}
                            <td class="cutter" style="padding-left: 0.3cm">
                                {{ $spare_row['name'] }} {{$spare_row['variation'] }}
                                @if(! empty($spare_row['sell_line_note']))({{$spare_row['sell_line_note']}}) @endif 
                                @if(! empty($spare_row['lot_number']))<br> {{$spare_row['lot_number_label']}}:  {{$spare_row['lot_number']}} @endif 
                                @if(! empty($spare_row['product_expiry'])), {{$spare_row['product_expiry_label']}}:  {{$spare_row['product_expiry']}} @endif
                                @if ($spare_row['line_discount'] > 0) (DESC. {{ $spare_row['line_discount'] }}) @endif
                            </td>

                            {{-- PRECIO UNITARIO --}}
                            <td class="text-right">
                                <span class="display_currency" data-currency_symbol="false">
                                    {{ $spare_row['unit_price_exc'] }}
                                </span>
                            </td>

                            {{-- VENTAS NO SUJETAS --}}
                            <td class="text-right">
                                &nbsp;
                            </td>

                            {{-- VENTAS EXENTAS --}}
                            <td class="text-right">
                                @if ($receipt_details->is_exempt == 1)
                                <span class="display_currency" data-currency_symbol="false">
                                    {{ $spare_row['unit_price_exc_tax'] }}
                                </span>
                                @else
                                &nbsp;
                                @endif
                            </td>

                            {{-- VENTAS GRAVADAS --}}
                            <td class="text-right">
                                @if ($receipt_details->is_exempt == 0)
                                {{-- <span class="display_currency" data-currency_symbol="false"> --}}
                                    {{ number_format($spare_row['unit_price_exc_tax'], $precision) }}
                                {{-- </span> --}}
                                @else
                                &nbsp;
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @endif
					@empty
					<tr>
						<td colspan="6">&nbsp;</td>
					</tr>
					@endforelse
				</tbody>

                @if ($receipt_details->discount_amount > 0)
                <tfoot>
                    <tr>
                        <td></td>
                        <td style="padding-left: 0.3cm">DESCUENTO</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">
                            <span class="display_currency" data-currency_symbol="false">
                                {{ $receipt_details->discount_amount }}
                            </span>
                            <span style="float: left;">(-)</span>
                        </td>
                    </tr>
                </tfoot>
                @endif
			</table>
		</div>

		<div class="footer" style="font-size: 5pt;">
            <table>
                <tr>{{-- SUMAS --}}
                    <td rowspan="6" style="padding-left: 1cm; vertical-align: top; font-size: 6pt;">{{ $receipt_details->total_letters }}</td>
                    <td rowspan="3" style="width: 2.35cm;">&nbsp;</td>
                    <td style="width: 2.1cm;" class="text-right">
                    @if ($receipt_details->is_exempt == 0)
                        <span class="display_currency" data-currency_symbol="false">
                            {{ $receipt_details->total_before_tax }}
                        </span>
                    @else
                        &nbsp;
                    @endif
                    </td>
                </tr>
                <tr>{{-- IVA --}}
                    <td class="text-right">
                    @if ($receipt_details->is_exempt == 0)
                    <span class="display_currency" data-currency_symbol="false">
                        {{ $receipt_details->tax_amount }}
                    </span>
                    @else
                    &nbsp;
                    @endif
                    </td>
                </tr>
                <tr>{{-- SUBTOTAL --}}
                    <td class="text-right">
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
                    </td>
                </tr>
                <tr>{{-- IVA PERCIBIDO --}}
                    <td rowspan="3">&nbsp;</td>
                    <td class="text-right less-padding">
                    @if ($receipt_details->is_exempt == 0 && $receipt_details->withheld)
                    <span class="display_currency" data-currency_symbol="false">
                        {{ $receipt_details->withheld }}
                    </span>
                    @else
                        &nbsp;
                    @endif
                    </td>
                </tr>
                <tr>{{-- VENTAS EXENTAS --}}
                    <td class="text-right">
                    @if ($receipt_details->is_exempt == 1)
                    <span class="display_currency" data-currency_symbol="false">
                        {{ $receipt_details->total }}
                    </span>
                    @else
                        &nbsp;
                    @endif
                    </td>
                </tr>
                <tr>{{-- TOTAL GENERAL --}}
                    <td class="text-right">
                        <span class="display_currency" data-currency_symbol="false">
                            {{ $receipt_details->total }}
                        </span>
                    </td>
                </tr>
            </table>
		</div>
	</div>
</div>
<div class="main"> {{-- 3 --}}
	<div class="container">

        {{-- HEAD --}}
		<div class="head"></div>

		<div class="header">
            <table style="margin-top: 0.2cm;">
                <tr>
                    <td style="width: 2cm; padding-left: 0.3cm;"><b>CLIENTE</b></td>
                    <td style="width: 6cm;" class="cutter">{{ $receipt_details->customer_name }}</td>
                    <td style="width: 2cm;"><b>FECHA</b></td>
                    <td style="width: 3cm;">{{ @format_date($receipt_details->invoice_date) }}</td>
                    <td style="width: 3cm;;"><b>GIRO</b></td>
                    <td class="cutter">{{ $receipt_details->customer_business_activity }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 0.3cm;"><b>DUI</b></td>
                    <td>{{ $receipt_details->customer_dui }}</td>
                    <td><b>NRC</b></td>
                    <td>{{ $receipt_details->customer_tax_number }}</td>
                    <td><b>NIT</b></td>
                    <td>{{ $receipt_details->customer_nit }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 0.3cm;"><b>DIRECCIÓN</b></td>
                    <td class="cutter">{{ $receipt_details->customer_landmark }}</td>
                    <td><b>MUNICIPIO</b></td>
                    <td class="cutter">{{ $receipt_details->customer_city }}</td>
                    <td><b>CONDICIÓN DE PAGO</b></td>
                    <td>{{ __('messages.' . $receipt_details->payment_condition) }}</td>
                </tr>
            </table>
		</div>

		<div class="details">
			<table class="sell_lines">
				<thead>
					<tr>
						{{-- CANTIDAD --}}
						<th style="width: 0.9cm">&nbsp;</th>

						{{-- DESCRIPCIÓN --}}
						<th>&nbsp;</th>
						
						{{-- PRECIO UNITARIO --}}
						<th style="width: 1.2cm">&nbsp;</th>

                        {{-- VENTAS NO SUJETAS --}}
						<th style="width: 1cm">&nbsp;</th>

                        {{-- VENTAS EXENTAS --}}
						<th style="width: 1cm">&nbsp;</th>

						{{-- VENTAS GRAVADAS --}}
						<th style="width: 2cm">&nbsp;</th>
					</tr>
				</thead>

				<tbody>
					@forelse ($receipt_details->lines as $line)
					<tr>
						{{-- CANTIDAD --}}
						<td class="cutter text-right" style="padding-right: 0.2cm">
							{{ $line['quantity'] }}
						</td>

						{{-- DESCRIPCION --}}
						<td class="cutter" style="padding-left: 0.3cm">
							{{ $line['name'] }} {{$line['variation'] }}
							@if(! empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif 
							@if(! empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
							@if(! empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif
                            @if ($line['line_discount'] > 0) (DESC. {{ $line['line_discount'] }}) @endif
						</td>

						{{-- PRECIO UNITARIO --}}
						<td class="text-right">
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['unit_price_exc'] }}
							</span>
						</td>

						{{-- VENTAS NO SUJETAS --}}
						<td class="text-right">
							&nbsp;
						</td>

                        {{-- VENTAS EXENTAS --}}
                        <td class="text-right">
							@if ($receipt_details->is_exempt == 1)
							<span class="display_currency" data-currency_symbol="false">
								{{ $line['unit_price_exc_tax'] }}
							</span>
							@else
							&nbsp;
							@endif
						</td>

                        {{-- VENTAS GRAVADAS --}}
                        <td class="text-right">
							@if ($receipt_details->is_exempt == 0)
							{{-- <span class="display_currency" data-currency_symbol="false"> --}}
								{{ number_format($line['unit_price_exc_tax'], $precision) }}
							{{-- </span> --}}
							@else
							&nbsp;
							@endif
						</td>
					</tr>
                    @if (isset($line['spare_rows']))
                        @foreach ($line['spare_rows'] as $spare_row)
                        <tr>
                            {{-- CANTIDAD --}}
                            <td class="cutter text-right">
                                {{ $spare_row['quantity'] }}
                            </td>

                            {{-- DESCRIPCION --}}
                            <td class="cutter" style="padding-left: 0.3cm">
                                {{ $spare_row['name'] }} {{$spare_row['variation'] }}
                                @if(! empty($spare_row['sell_line_note']))({{$spare_row['sell_line_note']}}) @endif 
                                @if(! empty($spare_row['lot_number']))<br> {{$spare_row['lot_number_label']}}:  {{$spare_row['lot_number']}} @endif 
                                @if(! empty($spare_row['product_expiry'])), {{$spare_row['product_expiry_label']}}:  {{$spare_row['product_expiry']}} @endif
                                @if ($spare_row['line_discount'] > 0) (DESC. {{ $spare_row['line_discount'] }}) @endif
                            </td>

                            {{-- PRECIO UNITARIO --}}
                            <td class="text-right">
                                <span class="display_currency" data-currency_symbol="false">
                                    {{ $spare_row['unit_price_exc'] }}
                                </span>
                            </td>

                            {{-- VENTAS NO SUJETAS --}}
                            <td class="text-right">
                                &nbsp;
                            </td>

                            {{-- VENTAS EXENTAS --}}
                            <td class="text-right">
                                @if ($receipt_details->is_exempt == 1)
                                <span class="display_currency" data-currency_symbol="false">
                                    {{ $spare_row['unit_price_exc_tax'] }}
                                </span>
                                @else
                                &nbsp;
                                @endif
                            </td>

                            {{-- VENTAS GRAVADAS --}}
                            <td class="text-right">
                                @if ($receipt_details->is_exempt == 0)
                                {{-- <span class="display_currency" data-currency_symbol="false"> --}}
                                    {{ number_format($spare_row['unit_price_exc_tax'], $precision) }}
                                {{-- </span> --}}
                                @else
                                &nbsp;
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @endif
					@empty
					<tr>
						<td colspan="6">&nbsp;</td>
					</tr>
					@endforelse
				</tbody>

                @if ($receipt_details->discount_amount > 0)
                <tfoot>
                    <tr>
                        <td></td>
                        <td style="padding-left: 0.3cm">DESCUENTO</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">
                            <span class="display_currency" data-currency_symbol="false">
                                {{ $receipt_details->discount_amount }}
                            </span>
                            <span style="float: left;">(-)</span>
                        </td>
                    </tr>
                </tfoot>
                @endif
			</table>
		</div>

		<div class="footer" style="font-size: 5pt; margin-bottom: 0.8cm;">
            <table>
                <tr>{{-- SUMAS --}}
                    <td rowspan="6" style="padding-left: 1cm; vertical-align: top; font-size: 6pt;">{{ $receipt_details->total_letters }}</td>
                    <td rowspan="3" style="width: 2.35cm;">&nbsp;</td>
                    <td style="width: 2.1cm;" class="text-right">
                    @if ($receipt_details->is_exempt == 0)
                        <span class="display_currency" data-currency_symbol="false">
                            {{ $receipt_details->total_before_tax }}
                        </span>
                    @else
                        &nbsp;
                    @endif
                    </td>
                </tr>
                <tr>{{-- IVA --}}
                    <td class="text-right">
                    @if ($receipt_details->is_exempt == 0)
                    <span class="display_currency" data-currency_symbol="false">
                        {{ $receipt_details->tax_amount }}
                    </span>
                    @else
                    &nbsp;
                    @endif
                    </td>
                </tr>
                <tr>{{-- SUBTOTAL --}}
                    <td class="text-right">
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
                    </td>
                </tr>
                <tr>{{-- IVA PERCIBIDO --}}
                    <td rowspan="3">&nbsp;</td>
                    <td class="text-right less-padding">
                    @if ($receipt_details->is_exempt == 0 && $receipt_details->withheld)
                    <span class="display_currency" data-currency_symbol="false">
                        {{ $receipt_details->withheld }}
                    </span>
                    @else
                        &nbsp;
                    @endif
                    </td>
                </tr>
                <tr>{{-- VENTAS EXENTAS --}}
                    <td class="text-right">
                    @if ($receipt_details->is_exempt == 1)
                    <span class="display_currency" data-currency_symbol="false">
                        {{ $receipt_details->total }}
                    </span>
                    @else
                        &nbsp;
                    @endif
                    </td>
                </tr>
                <tr>{{-- TOTAL GENERAL --}}
                    <td class="text-right">
                        <span class="display_currency" data-currency_symbol="false">
                            {{ $receipt_details->total }}
                        </span>
                    </td>
                </tr>
            </table>
		</div>
	</div>
</div>
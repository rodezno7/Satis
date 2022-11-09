<style>
    div#main {
        width: 100%;
        font-size: 7pt;
        font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
        margin: 0;
    }

    div#head {
        height: 3.0cm;
        width: 100%;
    }

    div#container {
        width: 19.7cm;
        margin: 0cm 0.9cm 0cm 1.1cm;
    }

    div#container table {
        width: 100%;
    }

    div#container table td {
        vertical-align: middle;
    }

    div#header {
        position: relative;
        height: 1.8cm;
        width: 100%;
    }

    div#header div#customer {
        position: absolute;
        left: 1.7cm;
        top: 0.15cm;
        width: 11.8cm;
    }

    div#header div#address {
        position: absolute;
        left: 2.5cm;
        top: 0.63cm;
        width: 17.0cm;
        line-height: 1.8;
        display: -webkit-box;
        -webkit-box-orient: vertical;  
        overflow: hidden;
        -webkit-line-clamp: 2;
    }

    div#header div#date {
        position: absolute;
        left: 15.5cm;
        top: 0.15cm;
        width: 4.0cm;
    }

	@if ($receipt_details->discount_amount > 0)
    
	div#details {
        height: 6.15cm;
    }

	div#extra-details {
        position: relative;
        height: 0.8cm;
    }

	div#extra-details #discount-text {
        position: absolute;
        left: 11.0cm;
        top: 0.1cm;
        width: ;
    }

	div#extra-details #discount-amount {
        position: absolute;
        left: 18.3cm;
		top: 0.1cm;
		width: 1.2cm;
        text-align: right;
    }

	@else

	div#details {
        height: 6.95cm;
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
        height: 0.9cm;
        width: 100%;
	}

    div#footer div#total {
		position: absolute;
        left: 18.3cm;
		top: 0.25cm;
		width: 1.2cm;
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
            <div id="address">
                {{ $receipt_details->customer_location }}
            </div>

            {{-- FECHA --}}
            <div id="date" class="cutter">
                {{ $receipt_details->transaction_date }}
            </div>
		</div>

		<div id="details">
			<table id="sell_lines">
				<thead>
					<tr>
						{{-- CANTIDAD --}}
						<th style="width: 1.5cm">&nbsp;</th>

						{{-- DESCRIPCIÓN --}}
						<th style="width: 14.5cm">&nbsp;</th>
						
						{{-- PRECIO UNITARIO --}}
						<th style="width: 1.8cm">&nbsp;</th>

						{{-- VALOR --}}
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
						<td class="cutter" style="padding-left: 0.2cm">
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
            {{-- VENTA TOTAL --}}
            <div id="total">
                <span class="display_currency" data-currency_symbol="false">
                    {{ $receipt_details->total }}
                </span>
            </div>
		</div>
	</div>
</div>
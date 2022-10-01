<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ticket</title>
    <style>
        * { margin: 0;padding: 0; list-style: none; text-decoration: none; border: none; outline: none;}
        div#container {width: 100%; font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif; font-size: 7.5pt;}
        div.header {padding: 1cm 0.5cm 0cm 0.5cm;}
        div#details,div.footer { padding: 0cm 0.5cm 0cm 0.5cm;}
        .txt-center {text-align: center; }
        #sell_lines thead tr th, .tot_foot_letter { border-top: 0.5px solid #000;border-bottom: 0.5px solid #000;}
        .txt-rigth {text-align: right;}
    </style>
</head>

<body>
    <div id="container">
        <div class="header">
            <table style="width: 100%;">
                <tr>
                    <td class="txt-center">
                        <strong>{{ mb_strtoupper($receipt_details->business_name) }}</strong>
                    </td>
                </tr>
                <tr>
                    <td class="txt-center">{{ mb_strtoupper($receipt_details->location_name) }}</td>
                </tr>
                <tr>
                    <td class="txt-center">{{ $receipt_details->location_landmark }}</td>
                </tr>
            </table>
            <table id="tbl_header" style="width: 100%;">
                <tr>
                    <td colspan="4">
                        N.R.C. {{ $receipt_details->business_nrc }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        GIRO: {{ $receipt_details->business_line }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        NIT.: {{ $receipt_details->business_nit }}
                    </td>
                    <td colspan="2">
                        CAJA: {{ $receipt_details->cashier_code }}</td>
                </tr>
                <tr>
                    <td colspan="4">
                        RES.: {{ $receipt_details->document['res_ticket'] }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4">{{ $receipt_details->document['serie_correlative'] }}</td>
                </tr>
                <tr>
                    <td colspan="2">FECHA: {{ $receipt_details->transaction_date }}</td>
                    <td colspan="2">HORA: {{ $receipt_details->transaction_hour }}</td>
                </tr>
                <tr>
                    <td colspan="2">
                        ClIENTE: {{ $receipt_details->customer_short_name }}
                    </td>
                    <td colspan="2">
                        DUI: {{ $receipt_details->customer_dui }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <strong>TICKET DE VENTA # {{ $receipt_details->correlative }}</strong>
                    </td>
                </tr>
            </table>
        </div>
        <div id="details">
            <table id="sell_lines" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Cant.</th>
                        <th colspan="2">Descripción</th>
                        <th>P. Unitario</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $count = count($receipt_details->lines); @endphp
                    @forelse($receipt_details->lines as $line)
                        <tr>
                            <td class="txt-center">{{ number_format($line['quantity']) }}</td>
                            <td colspan="2" class="txt-center">{{ $line['code'] }}</td>
                            <td class="txt-center">
                                {{ $line['unit_price'] }}
                            </td>
                            <td colspan="2" class="txt-center">
                                {{ $line['line_total'] }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                {{ $line['name'] }} {{ $line['variation'] }}
                                {{ $line['sell_line_note'] ?? '' }}
                                {{ !empty($line['lot_number']) ? $line['lot_number_label'] . ': ' . $line['lot_number'] : '' }}
                                {{ !empty($line['product_expiry']) ? $line['product_expiry_label'] . ': ' . $line['product_expiry'] : '' }}
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
        <div class="footer">
            <table id="totals" style="width: 100%;">
                <tr>
                    <td class="tot_foot_letter" colspan="3"><strong>Total de articulos</strong></td>
                    <td class="tot_foot_letter" colspan="2">{{ $count }}</td>
                    <td class="tot_foot_letter" colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3">SUB TOTAL VENTA</td>
                    <td colspan="2">$</td>
                    <td colspan="2" class="txt-rigth">
                        <span class="display_currency" data-currency_symbol="false">
                            {{ number_format($receipt_details->total_before_tax, 2) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">VENTAS GRAVADAS</td>
                    <td colspan="2">$</td>
                    <td colspan="2" class="txt-rigth">
                        <span class="display_currency" data-currency_symbol="false">
                            {{ number_format($receipt_details->total_before_tax, 2) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">VENTAS EXENTAS</td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="3">VENTAS NO SUJETAS</td>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td colspan="3">TOTAL VENTA</td>
                    <td colspan="2">$</td>
                    <td class="txt-rigth" colspan="2">
                        <span class="display_currency" data-currency_symbol="false">
                            {{ number_format($receipt_details->total, 2) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="border-top: 0.5px solid #000;"></td>
                </tr>
                <tr>
                    <td colspan="7" class="txt-center"><strong>DISTRIBUCIÓN DE PAGO</strong></td>
                </tr>
                <tr>
                    <td colspan="3">EFECTIVO</td>
                    <td colspan="2">$</td>
                    <td colspan="2" class="txt-rigth">{{ number_format($receipt_details->payments['cash'], 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3">TARJETA</td>
                    <td colspan="2">$</td>
                    <td colspan="2" class="txt-rigth">{{ number_format($receipt_details->payments['card'], 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3">CHEQUE</td>
                    <td colspan="2">$</td>
                    <td colspan="2" class="txt-rigth">{{ number_format($receipt_details->payments['check'], 2) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="3">TRANSFERENCIA</td>
                    <td colspan="2">$</td>
                    <td colspan="2" class="txt-rigth">{{ number_format($receipt_details->payments['bank'], 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3">TOTAL</td>
                    <td colspan="2">$</td>
                    <td colspan="2" class="txt-rigth">{{ number_format($receipt_details->payments['total'], 2) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="border-top: 0.5px solid #000;"></td>
                </tr>
                <tr>
                    <td colspan="3">Efectivo Recibido</td>
                    <td colspan="2">$</td>
                    <td colspan="2" class="txt-rigth">
                        {{ number_format($receipt_details->payments['total_cash'], 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3">Cambio...</td>
                    <td colspan="2">$</td>
                    <td colspan="2" class="txt-rigth">
                        {{ number_format($receipt_details->payments['cash_return'], 2) }}</td>
                </tr>
                <tr>
                    <td colspan="2">ATENDIDO POR: </td>
                    <td colspan="5">{{ mb_strtoupper($receipt_details->seller_name) }}</td>
                </tr>
                <tr>
                    <td colspan="3"> G = GRAVADO</td>
                    <td colspan="4"> E = EXENTO</td>
                </tr>
            </table>
        </div>
        <div style="height: 10cm;">
            &nbsp;
        </div>
    </div>
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ticket de devolución</title>
    <style>
        * { margin: 0;padding: 0; list-style: none; text-decoration: none; border: none; outline: none;}
        div#container {width: 100%; font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif; font-size: 7.5pt;}
        div.header {padding: 1cm 0.5cm 0cm 0.5cm;}
        div#details,div.footer { padding: 0cm 0.5cm 0cm 0.5cm;}
        .txt-center {text-align: center; }
        #sell_lines thead tr th, .tot_foot_letter { border-top: 0.5px solid #000;border-bottom: 0.5px solid #000;}
        .txt-rigth {text-align: right;}
        #signatures { width: 100%; }
        #signatures tr td {
            height: 25%;
            padding-bottom: 0;
            vertical-align: bottom;
        }
    </style>
</head>

<body>
    <div id="container">
        <div class="header">
            <table style="width: 100%;">
                <tr>
                    <td class="txt-center">
                        <strong>TICKET DE DEVOLUCIÓN</strong>
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
                        TEL: {{ $receipt_details->location_mobile }}
                    </td>
                </tr>
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
                    <td colspan="4">
                        N.I.T.: {{ $receipt_details->business_nit }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        RESOLUCIÓN: {{ $receipt_details->document['res_ticket'] }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        {{ $receipt_details->document['serie_correlative_2'] }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        CAJA No. {{ $receipt_details->cashier_code }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        TICKET #:
                    </td>
                    <td colspan="2">
                        {{ $receipt_details->correlative }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        Hora:
                    </td>
                    <td colspan="2">
                        {{ $receipt_details->transaction_hour }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        Fecha:
                    </td>
                    <td colspan="2">
                        {{ $receipt_details->transaction_date }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        Tipo de venta:
                    </td>
                    <td colspan="2">
                        {{ __('messages.' . $receipt_details->payment_condition) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        CLIENTE: {{ $receipt_details->customer_short_name }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        DUI: {{ $receipt_details->customer_dui }}
                    </td>
                </tr>
            </table>
        </div>
        <div id="details">
            <table id="sell_lines" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="txt-center">Cant.</th>
                        <th class="txt-center" colspan="2">Descripción</th>
                        <th class="txt-center">Precio<br>Unitario</th>
                        <th class="txt-center" colspan="2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $count = count($receipt_details->lines);
                    $qty_total = 0;
                    @endphp
                    @forelse($receipt_details->lines as $line)
                        @php
                        $qty_total += $line['quantity'];
                        @endphp
                        @if ($line['quantity'] > 0)    
                        <tr>
                            <td>
                                <strong>{{ number_format($line['quantity']) }}</strong>
                            </td>
                            <td colspan="2" class="txt-center">
                                <strong>{{ $line['code'] }}</strong>
                            </td>
                            <td class="txt-center">
                                <strong>{{ $line['unit_price'] }}</strong>
                            </td>
                            <td colspan="2" class="txt-center">
                                <strong>{{ $line['line_total'] }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                <strong>
                                    {{ $line['name'] }} {{ $line['variation'] }}
                                    {{ $line['sell_line_note'] ?? '' }}
                                    {{ !empty($line['lot_number']) ? $line['lot_number_label'] . ': ' . $line['lot_number'] : '' }}
                                    {{ !empty($line['product_expiry']) ? $line['product_expiry_label'] . ': ' . $line['product_expiry'] : '' }}
                                </strong>
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5">&nbsp;</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td class="tot_foot_letter">
                            <strong>{{ $qty_total }}</strong>
                        </td>
                        <td class="tot_foot_letter" colspan="2">
                            &nbsp;
                        </td>
                        <td class="tot_foot_letter txt-center">
                            <strong>$</strong>
                        </td>
                        <td class="tot_foot_letter txt-center" colspan="2">
                            <strong>{{ number_format($receipt_details->total, 2) }}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="footer">
            <table id="signatures" style="width: 100%; margin-top: 15px;">
                <tr>
                    <td style="width: 20%;">
                        <strong>Cliente:</strong>
                    </td>
                    <td style="width: 80%;">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td>
                        <br>
                        <strong>DUI:</strong>
                    </td>
                    <td style="border-top: 0.5px solid #000;">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td>
                        <br>
                        <strong>Firma:</strong>
                    </td>
                    <td style="border-top: 0.5px solid #000;">
                        &nbsp;
                    </td>
                </tr>
            </table>
        </div>
        <div style="height: 10cm;">
            &nbsp;
        </div>
    </div>
</body>

</html>

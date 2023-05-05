<!DOCTYPE html>
<html lang="es">
<body>
    <table>
        <tr>
            <td colspan="17" style="text-align: center; font-size: 14pt;">
                <strong>{{ strtoupper($business->line_of_business) }}</strong>
            </td>
        </tr>
        <tr>
            <td colspan="17" style="text-align: center; font-size: 10pt;">
                <strong>{{ strtoupper(__("report.dispatch_report")) }}</strong>
            </td>
        </tr>
        <tr>
            <td colspan="17" style="text-align: center; font-size: 10pt;">
            <strong>
                {{ strtoupper(__("lang_v1.from")) }}
                {{ $initial_month ==
                    $final_month ? date('j', strtotime($initial_date)) ." ". strtoupper(__("lang_v1.to")) ." ". date('j', strtotime($final_date)) ." ". strtoupper(__("lang_v1.of")) ." ". strtoupper($initial_month) :
                    date('j', strtotime($initial_date)) ." ". strtoupper(__("lang_v1.from")) ." ". strtoupper($initial_month) ." ". strtoupper(__("lang_v1.to")) ." ". date('j', strtotime($final_date)) ." ". strtoupper(__("lang_v1.of")) ." ". strtoupper($final_month) }}
                {{ strtoupper(__("lang_v1.of")) }}
                {{ $initial_year == $final_year ? $initial_year : $initial_year . ' - ' . $final_year }}
            </strong>
            </td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th style="border: 1px solid black; text-align: center; font-size: 10pt;"><strong>N°</strong></th>
                <th style="border: 1px solid black; text-align: center; font-size: 10pt;"><strong>VENDEDOR</strong></th>
                <th style="border: 1px solid black; text-align: center; font-size: 10pt;"><strong>CLIENTE</strong></th>
                <th style="border: 1px solid black; text-align: center; font-size: 10pt;"><strong>DEPARTAMENTO</strong></th>
                <th style="border: 1px solid black; text-align: center; font-size: 10pt;"><strong>MUNICIPIO</strong></th>
                <th style="border: 1px solid black; text-align: center; font-size: 10pt;"><strong>DIRECCIÓN</strong></th>
                <th style="border: 1px solid black; text-align: center; font-size: 10pt;"><strong>REFERENCIA</strong></th>
                <th style="border: 1px solid black; text-align: center; font-size: 10pt;"><strong>CONTACTO</strong></th>
                <th style="border: 1px solid black; text-align: center; font-size: 10pt;"><strong>N° CONTACTO</strong></th>
                <th style="border: 1px solid black; text-align: center; font-size: 10pt;"><strong>DESPACHO</strong></th>
                <th style="border: 1px solid black; text-align: center; font-size: 10pt;"><strong>N° ORDEN</strong></th>
                <th style="border: 1px solid black; text-align: center; font-size: 10pt;"><strong>TIPO DOC.</strong></th>
                <th style="border: 1px solid black; text-align: center; font-size: 10pt;"><strong>MONTO</strong></th>
                <th style="border: 1px solid black; text-align: center; font-size: 10pt;"><strong>FORMA PAGO</strong></th>
                <th style="border: 1px solid black; text-align: center; font-size: 10pt;"><strong>CAMBIO DE $$</strong></th>
                <th style="border: 1px solid black; text-align: center; font-size: 10pt;"><strong>TRANSFERENCIA COMO ANEXO</strong></th>
                <th style="border: 1px solid black; text-align: center; font-size: 10pt;"><strong>OBSERVACIONES</strong></th>
            </tr>
        </thead>
        <tbody>
        @php
            $count = 1;
        @endphp
        @foreach ($quote_trans as $qt)
            <tr>
                <td style="border: 1px solid black; font-size: 10; text-align: center;">{{ $count }}</td>
                <td style="border: 1px solid black; font-size: 10;">{{ $qt->seller_name }}</td>
                <td style="border: 1px solid black; font-size: 10;">{{ $qt->customer_name }}</td>
                <td style="border: 1px solid black; font-size: 10;">{{ mb_strtoupper($qt->state_name) }}</td>
                <td style="border: 1px solid black; font-size: 10;">{{ mb_strtoupper($qt->city_name) }}</td>
                <td style="border: 1px solid black; font-size: 10;">{{ $qt->address }}</td>
                <td style="border: 1px solid black; font-size: 10;">{{ $qt->landmark }}</td>
                <td style="border: 1px solid black; font-size: 10;">{{ $qt->contact_name }}</td>
                <td style="border: 1px solid black; font-size: 10;">{{ $qt->contact_mobile }}</td>
                <td style="border: 1px solid black; font-size: 10;">{{ mb_strtoupper(__('order.'.$qt->delivery_type)) }}</td>
                <td style="border: 1px solid black; font-size: 10;">{{ $qt->order_number }}</td>
                <td style="border: 1px solid black; font-size: 10;">{{ mb_strtoupper($qt->doc_type) }}</td>
                <td style="border: 1px solid black; font-size: 10; text-align: right;">{{ "$ " . @num_format($qt->final_total) }}</td>
                <td style="border: 1px solid black; font-size: 10;">
                    @switch($qt->payment_counts)
                        @case(0)
                            {{ mb_strtoupper(__("lang_v1.credit")) }}
                            @break
                        @case(1)
                            {{ mb_strtoupper(__("lang_v1." . $qt->pay_method)) }}
                            @break
                        @default
                            {{ strtoupper(__("lang_v1.multiple")) }}
                    @endswitch
                </td>
                <td style="border: 1px solid black; font-size: 10;">&nbsp;</td>
                <td style="border: 1px solid black; font-size: 10;">&nbsp;</td>
                <td style="border: 1px solid black; font-size: 10;">&nbsp;</td>
            </tr>
        @php
            $count ++;
        @endphp
        @endforeach
        </tbody>
    </table>
</body>
</html>
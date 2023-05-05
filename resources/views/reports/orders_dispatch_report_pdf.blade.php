<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('report.dispatch_report')</title>
    <style>
        @page {margin: 1.5cm 1cm 1.5cm 1cm; }
        body { font-family: sans-serif; font-size: 10pt;s }
        table { width: 100%; border-collapse: collapse; }
        div#header h1,
        div#header h3,
        div#header h4 { text-align: center; margin: 0.2cm; }
        table#body td, table#body th { border: 1px solid #000; padding: 3px; }
        .page-number:before { content: "Página " counter(page); }
        div#footer {position: fixed; right: 10px; bottom: -10px; }
    </style>
</head>
<body>
    <div id="footer">
        <div class="page-number"></div>
    </div>
    <div id="header">
        <h1><strong>{{ strtoupper($business->line_of_business) }}</strong></h1>
        <h3><strong>{{ strtoupper(__("report.dispatch_report")) }}</strong></h3>
        <h4>
            {{ strtoupper(__("lang_v1.from")) }}
            {{ $initial_month ==
                $final_month ? date('j', strtotime($initial_date)) ." ". strtoupper(__("lang_v1.to")) ." ". date('j', strtotime($final_date)) ." ". strtoupper(__("lang_v1.of")) ." ". strtoupper($initial_month) :
                date('j', strtotime($initial_date)) ." ". strtoupper(__("lang_v1.from")) ." ". strtoupper($initial_month) ." ". strtoupper(__("lang_v1.to")) ." ". date('j', strtotime($final_date)) ." ". strtoupper(__("lang_v1.of")) ." ". strtoupper($final_month) }}
            {{ strtoupper(__("lang_v1.of")) }}
            {{ $initial_year == $final_year ? $initial_year : $initial_year . ' - ' . $final_year }}
        </h4>
    </div>
    <table id="body" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th>No</th>
                <th>VENDEDOR</th>
                <th>CLIENTE</th>
                <th>DEPARTAMENTO.</th>
                <th>MUNICIPIO</th>
                <th>DIRECCIÓN</th>
                <th>REFERENCIA</th>
                <th>CONTACTO</th>
                <th>N° CONTACTO</th>
                <th>DESPACHO</th>
                <th>N° ORDEN</th>
                <th>TIPO DOC.</th>
                <th>MONTO</th>
                <th>FORMA PAGO</th>
                <th>CAMBIO DE $$</th>
                <th>TRANSFERENCIA COMO ANEXO</th>
                <th>OBSERVACIONES</th>
            </tr>
        </thead>
        <tbody>
        @php
            $count = 1;
        @endphp
        @foreach ($quote_trans as $qt)
            <tr>
                <td style="text-align: center;">{{ $count }}</td>
                <td>{{ $qt->seller_name }}</td>
                <td>{{ $qt->customer_name }}</td>
                <td>{{ mb_strtoupper($qt->state_name) }}</td>
                <td>{{ mb_strtoupper($qt->city_name) }}</td>
                <td>{{ $qt->address }}</td>
                <td>{{ $qt->landmark }}</td>
                <td>{{ $qt->contact_name }}</td>
                <td>{{ $qt->contact_mobile }}</td>
                <td>{{ mb_strtoupper(__('order.'.$qt->delivery_type)) }}</td>
                <td>{{ $qt->order_number }}</td>
                <td>{{ mb_strtoupper($qt->doc_type) }}</td>
                <td style="text-align: right;">{{ "$ " . @num_format($qt->final_total) }}</td>
                <td>
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
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        @php
            $count ++;
        @endphp
        @endforeach
        </tbody>
    </table>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@lang('report.daily_z_cut_report')</title>
    <style>
        @page{ margin: 3pt; }
        body{ font: normal 8pt 'Courier New', Courier, monospace; }
        h1.ticket { font-size: 16pt; margin: 0; font-weight: bolder; }
        h1.business_name { font-size: 14pt; margin: 0; }
        h2.business_line { font-size: 10pt; margin: 0; }
        h3.address { margin: 0.5cm 0 0.5cm 0; }
        h3.business_info { margin: 1pt 0 1pt 0; }
        h3.correlative { margin: 0; }
        table { width: 100%; margin: 3pt 10pt; }
        .left { text-align: left; }
        .center { text-align: center; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div id="container">
        <h1 class="business_name center">{{ mb_strtoupper(__('cash_register.daily_z_cut')) }}</h1>
        <h2 class="business_line center">{{ $business_info->business_full_name }}</h2>
        <h3 class="address center">{{ $location_info->address }}</h3>
        <h3 class="business_info">TEL:&nbsp;{{ $location_info->mobile }}</h3>
        <h3 class="business_info">N.R.C.&nbsp;{{ $business_info->nrc }}</h3>
        <h3 class="business_info">GIRO:&nbsp;{{ $business_info->line_of_business }}</h3>
        <h3 class="business_info">N.I.T.&nbsp;{{ $business_info->nit }}</h3>
        <h3 class="business_info">DEL&nbsp;{{ $document_info->serie . "-" . $document_info->initial . "&nbsp;AL&nbsp;" . $document_info->serie . "-" . $document_info->final }}</h3>
        <h3 class="business_info">CAJA N°&nbsp;{{ str_pad($cashier_closure['cashier_id'], 4, '0', STR_PAD_LEFT) }}</h3>
        <h3 class="business_info center"><strong>FECHA</strong>&nbsp;{{ $cashier_closure['date'] }}&nbsp;<strong>HORA</strong>&nbsp;{{ $cashier_closure['time'] }}</h3>
        <hr>
        <h3 class="business_info center">ZETA:&nbsp;&nbsp;1</h3>
        <hr>
        <h1 class="ticket center">TICKET # {{ $cashier_closure['correlative'] }}</h1>
        <hr>
        <h3 class="correlative center"><strong>TICKETS DEL&nbsp;&nbsp;</strong>{{ $daily_z_cut_report->min_correlative }} <strong>&nbsp;&nbsp;&nbsp;AL:&nbsp;&nbsp;</strong>{{ $daily_z_cut_report->max_correlative }}</h3>
        @php
            $payments = $daily_z_cut_report->cash_amount + $daily_z_cut_report->card_amount + $daily_z_cut_report->check_amount + $daily_z_cut_report->bank_transfer_amount;
        @endphp
        <table>
            <tr>
                <th style="width: 60%" class="left">VENTA TOTAL:</th>
                <th>$</th>
                <th class="right">{{ number_format($payments, 2) }}</th>
            </tr>
            <tr>
                <th style="width: 60%" class="left">(-)DEVOLUCIONES:</th>
                <th>$</th>
                <th class="right">{{ number_format($daily_z_cut_report->return_amount, 2) }}</th>
            </tr>
            <tr>
                <th style="width: 60%" class="left">VENTA GENERAL:</th>
                <th>$</th>
                <th class="right">{{ number_format($payments - $daily_z_cut_report->return_amount, 2) }}</th>
            </tr>
            <tr>
                <th style="width: 60%" class="left">VENTAS GRAVADAS:</th>
                <th>$</th>
                <th class="right">{{ number_format($payments - $daily_z_cut_report->return_amount, 2) }}</th>
            </tr>
            <tr>
                <th style="width: 60%" class="left">VENTAS EXENTAS:</th>
                <th>$</th>
                <th class="right">{{ number_format(0, 2) }}</th>
            </tr>
            <tr>
                <th style="width: 60%" class="left">VENTAS NO SUJETAS:</th>
                <th>$</th>
                <th class="right">{{ number_format(0, 2) }}</th>
            </tr>
            <tr>
                <th style="width: 60%" class="left">TOTAL GENERAL:</th>
                <th>$</th>
                <th class="right">{{ number_format($payments - $daily_z_cut_report->return_amount, 2) }}</th>
            </tr>
        </table>
        <hr>
        <h3 class="correlative center"><strong>TICKETS DEL&nbsp;&nbsp;</strong>{{ $daily_z_cut_report->min_correlative }} <strong>&nbsp;&nbsp;&nbsp;AL:&nbsp;&nbsp;</strong>{{ $daily_z_cut_report->max_correlative }}</h3>
        <h3 class="correlative center">DISTRIBUCIÓN DE MONTOS</h3>
        <table>
            <tr>
                <th style="width: 60%" class="left">EFECTIVO:</th>
                <th>$</th>
                <th class="right">{{ number_format($daily_z_cut_report->cash_amount, 2) }}</th>
            </tr>
            <tr>
                <th style="width: 60%" class="left">TARJETA:</th>
                <th>$</th>
                <th class="right">{{ number_format($daily_z_cut_report->card_amount, 2) }}</th>
            </tr>
            <tr>
                <th style="width: 60%" class="left">CHEQUE:</th>
                <th>$</th>
                <th class="right">{{ number_format($daily_z_cut_report->check_amount, 2) }}</th>
            </tr>
            <tr>
                <th style="width: 60%" class="left">TRANSFERENCIA BANCARIA:</th>
                <th>$</th>
                <th class="right">{{ number_format($daily_z_cut_report->bank_transfer_amount, 2) }}</th>
            </tr>
        </table>
    </div>
</body>
</html>
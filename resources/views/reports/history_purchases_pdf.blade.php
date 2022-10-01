<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('report.history_purchase')</title>
    <style>
        body {font-family: 'Arial', sans-serif;color: #000000; font-size: 0.9em; }
        h3, h4 {text-align: center;}
        .table1 { brder: 0px; }
        .table2 {border-collapse: collapse; border: 0.25px solid black;font-size: 0.9em; margin-top: 1cm; border-left: none}
        .td2 {  border: 0px;text-align: center;}
        td {border: 0.25px solid black; padding-right: 4px; text-align: left;}
        th {border: 0.25px solid black; padding-right: 4px; text-align: center;}
        .table3 td {border: 0.25px solid black; padding-right: 4px;text-align: right;}
        .table3 th { border: 0.25px solid black; padding-right: 4px;text-align: center;}
        .alnleft {text-align: left;}
        .alncenter { text-align: center;}
        @page {margin-bottom: 30px;margin-top: 30px;}
        #header,#footer {position: fixed;left: 0;right: 0;color: #000000;font-size: 0.9em;}
        #header { top: 0; border-bottom: 0.1pt solid #aaa; }
        #footer { bottom: 0; border-top: 0.1pt solid #aaa;}
        .page-number:before { content: "Página " counter(page); }
        .locations { text-align: center; font-size: 0.7em; }
        .tran_date { text-align: center; font-size: 0.8em; }
        .mrgB { margin-bottom: -1.5%; font-size: 18px; }
        .mrgB1 { margin-bottom: -1.5%; font-size: 14px; }
        .mrgB2 { margin-bottom: -1.5%; font-size: 11px; }
        .table3 { width: 70%; border: none; border-collapse: collapse; font-size: 0.9em;}

    </style>
</head>

<body>
    <div id="footer">
        <div class="page-number"></div>
    </div>
    <table class="table1" style="width: 100%;">
        <tr>
            <td class="td2">
                <h1 class="mrgB"><strong>{{ mb_strtoupper($business->line_of_business) }}</strong></h1>
                <h3 class="mrgB1"><strong>{{ mb_strtoupper(__("Historial de productos comprados por clientes")) }}</strong></h3>
                <h5 class="mrgB2">
                    DEL
                    {{ $initial_month == $final_month ? date('j', strtotime($initial_date)) . ' AL ' . date('j', strtotime($final_date)) . ' DE ' . mb_strtoupper($initial_month) : date('j', strtotime($initial_date)) . ' DE ' . mb_strtoupper($initial_month) . ' AL ' . date('j', strtotime($final_date)) . ' DE ' . mb_strtoupper($final_month) }}
                    DE
                    {{ $initial_year == $final_year ? $initial_year : $initial_year . ' - ' . $final_year }}
                </h5>
            </td>
        </tr>
    </table>
    <table class="table2" style="width: 100%;">
        <thead style="font-size: 0.7em;">
            <tr>
                <th>{{ mb_strtoupper(__('Fecha')) }}</th>
                <th>{{ mb_strtoupper(__('Cliente')) }}</th>
                <th>{{ mb_strtoupper(__('Documento')) }}</th>
                <th>{{ mb_strtoupper(__('Producto')) }}</th>
                <th>{{ mb_strtoupper(__('Cantidad')) }}</th>
                <th>{{ mb_strtoupper(__('Precio')) }}</th>
                <th>{{ mb_strtoupper(__('Total')) }}</th>
                <th>{{ mb_strtoupper(__('Estado de pago')) }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lines as $item)
                <tr>
                    <td class="tran_date">{{ $item->transaction_date }}</td>
                    <td class="locations">{{ $item->name_customer }}</td>
                    <td class="locations">{{ $item->document}}</td>
                    <td style="font-size: 0.6em; text-align: center;">{{ $item->product_name }}</td>
                    <td class="locations">{{($item->quantity)}}</td>
                    <td class="locations">{{ $item->unit_price}}</td>
                    <td class="locations">{{ $item->total }}</td>
                    <td class="locations">@lang('lang_v1.'.$item->status.'') </td>
                    {{-- <td class="locations">{{ $item->status }} </td> --}}
                </tr>
            @endforeach

        </tbody>
    </table>

</body>

</html>

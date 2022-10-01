<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('Notas de remisión')</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000000;
            font-size: 0.9em;
        }

        h3,
        h4 {
            text-align: center;
        }

        .table1 {
            brder: 0px;
        }

        .table2 {
            border-collapse: collapse;
            /* border: 0.25px solid black; */
            font-size: 0.9em;
        }

        .td2 {
            border: 0px;
            text-align: center;
        }

        td,
        th {
            /* border: 0.25px solid black; */
            /* padding: 4px; */
            padding-right: 4px;
            text-align: left;
        }


        .alnleft {
            text-align: left;
        }

        .alnrigth {
            text-align: left;
        }

        .alncenter {
            text-align: center;
        }

        @page {
            margin-bottom: 30px;
            margin-top: 30px;
            margin-left: 1cm;
        }

        #header,
        #footer {
            position: fixed;
            left: 0;
            right: 0;
            color: #000000;
            font-size: 0.9em;
        }

        #header {
            top: 0;
            border-bottom: 0.1pt solid #aaa;
        }

        #footer {
            bottom: 0;
            border-top: 0.1pt solid #aaa;
        }

        /* .page-number:before {
            content: "Página " counter(page);
        } */

        .locations {
            text-align: right;
            font-size: 0.9em;
        }

        .tran_date {
            text-align: center;
            font-size: 0.9em;
        }

        .mrgB {
            margin-bottom: -1.5%;
            font-size: 18px;
        }

        .mrgB1 {
            margin-bottom: -1.5%;
            font-size: 14px;
        }

        .mrgB2 {
            margin-bottom: -1.5%;
            font-size: 11px;
        }

        .table3 {
            width: 70%;
            border: none;
            border-collapse: collapse;
            font-size: 0.9em;
        }
        .premrg{
            margin-top: -4.5%;
            margin-left: 20%;
            padding-top: 20px;
        }
        .premrg2{
            margin-top: -8.5%;
            margin-left: 20%;
        }
        .pdd_b{
            padding-bottom: 10px;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    {{-- <div id="footer">
        <div class="page-number"></div>
    </div> --}}

    <br>
    <table style="margin-top: 6.5%; width: 75%; font-size: 0.8em; word-spacing: -4px; padding-left: 14%;">
        <tr>
            <td>
                <pre style="margin-left: 20%; padding-top: 20px; padding-bottom: 12px;"><b>{{ $business_name->name }}   -   {{ $trans_ware->name }}</b></pre>
                <pre class="premrg"><b>{{ $trans_ware->landmark }}</b></pre><br>
                <pre class="premrg"><b>{{ $trans_ware->state }}</b></pre><br>
            </td>
            <td>
                <pre style="margin-left: 20%;  padding-top: -3.4%; padding-bottom: -20px;"><b>{{ @format_date($trans_ware->transaction_date) }}</b></pre>
                <pre class="premrg2"><b>{{ $trans_ware->city }}</b></pre>
                <pre class="premrg"><b></b></pre>
            </td>
        </tr>
    </table>
<br><br>
    <table class="table2" style="width: 90%; text-align: center; margin-top: 2%; border-collapse: collapse; padding-left: 12%;">
        <thead style="font-size: 0.8em;">
            <tr>
                <th class="alncenter" style="color: white;">{{ mb_strtoupper(__('Cantidad')) }}</th>
                <th class="alncenter" colspan="5" style="color: white;">{{ mb_strtoupper(__('Descripción')) }}</th>
                <th class="alncenter" style="color: white;">{{ mb_strtoupper(__('Precio')) }} <br> {{ mb_strtoupper(__('unitario')) }}</th>
                <th class="alncenter" style="color: white;">{{ mb_strtoupper(__('Monto')) }} <br>{{ mb_strtoupper(__('total')) }}</th>
            </tr>
        </thead>
        <tbody style="font-size: 0.75em;">
            @foreach ($transfer as $item)
            <tr>
                <td class="pdd_b" style="padding-right: 30px; text-align: center;">{{ @num_format($item->quantity) }}</td>
                <td class="alnleft pdd_b" colspan="5">
                    {{ $item->sub_sku . " - " . $item->product }}
                </td>
                <td class="alncenter pdd_b" style="padding-left: 50px;">$ {{ @num_format($item->unit_price) }}</td>
                <td class="alncenter pdd_b" style="padding-left: -10px;">$ {{ $item->quantity * $item->unit_price }}</td>
            </tr>
            @endforeach

        </tbody>
    </table>

    <table style="margin-top: 19%; width: 74%; font-size: 0.9em; padding-left: 12%;">
        <tr>
            <td style="padding-left: 90px;">
                <pre>{{ $total_letters }}</pre>
            </td>
            <td style="padding-left: 100px;">
                <pre>$ {{ $total }}</pre>
            </td>
        </tr>
    </table>
</body>

</html>

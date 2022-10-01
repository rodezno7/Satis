<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@lang('report.sales_by_seller_report')</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm;
            font-family: 'Courier New', Courier, monospace;
            font-size: 10pt;
        }

        h1, h2, h3 {
            text-align: center;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table thead th {
            text-align: center;
        }

        table tr th, table tr td {
            border: 1px solid #000;
            padding: 3px 5px;
        }

        table tr th.header {
            background-color: lightgray;
            font-size: 1.3em;
        }

        table tr td.white-line {
            border-left: none;
            border-right: none;
        }

        table tr .align-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>{{ mb_strtoupper($business_name) }}</h1>
    <h2>{{ mb_strtoupper(__('report.sales_by_seller_report')) }}</h2>
    <h3>{{  strtoupper(__('accounting.from')) ." ". @format_date($start_date) ." ". strtoupper(__('accounting.to')) ." ". @format_date($end_date) }}</h3>
    @php
        $count = 0;
        $counter = 1;
        $location_id = null;
        $total_before_tax = 0;
        $total_amount = 0;
        $currency_symbol = session('currency')['symbol'];
    @endphp
    <table>
    @foreach ($transactions as $t)
        @php
            $total_before_tax += $t->total_before_tax;
            $total_amount += $t->total_amount;
        @endphp
        @if ($t->location_id != $location_id)
            @php $count = $transactions->where('location_id', $t->location_id)->count(); @endphp
            <tr>
                <th class="header" colspan="4">{{ mb_strtoupper($t->location_name) }}</th>
            </tr>
            <tr>
                <th>{{ mb_strtoupper(__('employees.seller_code')) }}</th>
                <th>{{ mb_strtoupper(__('employees.seller_name')) }}</th>
                <th>{{ mb_strtoupper(__('sale.total_no_vat')) }}</th>
                <th>{{ mb_strtoupper(__('sale.total')) }}</th>
            </tr>
        @endif
        <tr>
            <td>{{ $t->seller_code }}</td>
            <td>{{ $t->seller_name }}</td>
            <td class="align-right">{{ $currency_symbol ." ". round($t->total_before_tax, 2) }}</td>
            <td class="align-right">{{ $currency_symbol ." ". round($t->total_amount, 2) }}</td>
        </tr>
        @if ($count == $counter)
            <tr>
                <th colspan="2">{{ mb_strtoupper(__('report.grand_total')) }}</th>
                <th class="align-right">{{ $currency_symbol ." ". round($total_before_tax, 2) }}</th>
                <th class="align-right">{{ $currency_symbol ." ". round($total_amount, 2) }}</th>
            </tr>
            @if (!$loop->last)
            <tr>
                <td class="white-line" colspan="4">&nbsp;</td>
            </tr>
            @endif
            @php
                $counter = 1;
                $total_before_tax = 0;
                $total_amount = 0;
            @endphp
        @else
            @php $counter ++; @endphp
        @endif
        @php $location_id = $t->location_id; @endphp
    @endforeach
    </table>
</body>
</html>
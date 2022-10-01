<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __("report.debts_to_pay_report") }}</title>
    <style>
        @page{ margin: 1.5cm 1cm; }
        body{ font-family: 'Courier New', Courier, monospace; font-size: 10pt; }
        h1{ margin: 0; text-align: center; font-size: 16pt; }
        h2{ margin: 0; text-align: center; font-size: 12pt; }
        table{ width: 100%; margin-top: 5px; table-layout: fixed; border-collapse: collapse; }
        table thead tr th{ border: 1px solid #000; background-color: #000; color: #fff; height: 20pt; }
        table th, table td { vertical-align: middle; padding: 3px 5px; }
        table tbody td.separator { height: 7pt; }
        table tr.footer td { height: 15pt; }
        table tr.final_totals td { font-size: 1.1em; font-weight: bold; }
        .bordered { border: 1px solid #000; height: 15pt; }
        .border-top { border-top: 1px solid #000; }
        .border-left { border-left: 1px solid #000; }
        .border-right { border-right: 1px solid #000; }
        .border-bottom { border-bottom: 1px solid #000; }
        .color-red { color: red; }
        .color-green { color: green; }
	    .cutter { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        div#footer{ position: fixed; right: 10px; bottom: -10px; font-size: 8pt; font-family: 'Courier New', Courier, monospace; }
        .page-number:before{ content: "Página " counter(page); }
        .text-align-right { text-align: right; }
        .text-align-center { text-align: center; }
    </style>
</head>
<body>
    <div id="footer">
        <div class="page-number"></div>
    </div>
    <h1>{{ mb_strtoupper($business_name) }}</h1>
    <h2>{{ mb_strtoupper($report_name) }}</h2>
    <table>
        <thead>
            <tr>
                <th>@lang('lang_v1.reference')</th>
                <th>@lang('messages.date')</th>
                <th>@lang('contact.expire_date')</th>
                <th>@lang('lang_v1.days')</th>
                <th>@lang('sale.total')</th>
                <th>@lang('payment.payments')</th>
                <th>@lang('payment.30_days')</th>
                <th>@lang('payment.60_days')</th>
                <th>@lang('payment.90_days')</th>
                <th>@lang('payment.120_days')</th>
                <th>@lang('payment.more_than_120')</th>
                <th>@lang('sale.total')</th>
            </tr>
        </thead>
        <tbody>
            @php
                $id = 0;
                $count = 1;
                $counter = 0;
                $total_days_30 = 0;
                $total_days_60 = 0;
                $total_days_90 = 0;
                $total_days_120 = 0;
                $total_more_than_120 = 0;
                $totals = 0;
            @endphp
            @foreach ($transactions as $t)
                @php
                    $total = $t->days_30 + $t->days_60 + $t->days_90 +$t->days_120 + $t->more_than_120;
                    $total_days_30 += $t->days_30;
                    $total_days_60 += $t->days_60;
                    $total_days_90 += $t->days_90;
                    $total_days_120 += $t->days_120;
                    $total_more_than_120 += $t->more_than_120;
                    $totals += $total;
                @endphp
                @if ($id != $t->supplier_id )
                    @php $counter = $transactions->where('supplier_id', $t->supplier_id)->count(); @endphp
                    <tr><td class="separator" colspan="12"></td></tr>
                    <tr>
                        <td class="cutter bordered" colspan="12">
                            <strong>{{ $t->supplier_name }}</strong>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td class="border-left">{{ $t->reference }}</td>
                    <td class="text-align-center">{{ @format_date($t->transaction_date) }}</td>
                    <td class="text-align-center">{{ @format_date($t->expire_date) }}</td>
                    <td class="text-align-center">{{ $t->days }}</td>
                    <td class="text-align-right">{{ "$ ". number_format($t->final_total, 2) }}</td>
                    <td class="text-align-right">{{ $t->payments > 0 ? "$ ". number_format($t->payments, 2) : "$ 0.00" }}</td>
                    <td class="text-align-right color-green">{{ $t->days_30 > 0 ? "$ ". number_format($t->days_30, 2) : "" }}</td>
                    <td class="text-align-right color-red">{{ $t->days_60 > 0 ? "$ ". number_format($t->days_60, 2) : "" }}</td>
                    <td class="text-align-right color-red">{{ $t->days_90 > 0 ? "$ ". number_format($t->days_90, 2) : "" }}</td>
                    <td class="text-align-right color-red">{{ $t->days_120 > 0 ? "$ ". number_format($t->days_120, 2) : "" }}</td>
                    <td class="text-align-right color-red">{{ $t->more_than_120 > 0 ? "$ ". number_format($t->more_than_120, 2) : "" }}</td>
                    <td class="text-align-right border-right">{{ $total > 0 ? "$ ". number_format($total, 2) : "" }}</td>
                </tr>
                @if ($counter == $count)
                    <tr class="footer">
                        <td class="border-top border-left border-bottom" colspan="6"><strong>{{ __("sale.total") . ": ". $counter }}</strong></td>
                        <td class="text-align-right border-top border-bottom color-green"><strong>{{ "$ ". number_format($total_days_30, 2) }}</strong></td>
                        <td class="text-align-right border-top border-bottom color-red"><strong>{{ "$ ". number_format($total_days_60, 2) }}</strong></td>
                        <td class="text-align-right border-top border-bottom color-red"><strong>{{ "$ ". number_format($total_days_90, 2) }}</strong></td>
                        <td class="text-align-right border-top border-bottom color-red"><strong>{{ "$ ". number_format($total_days_120, 2) }}</strong></td>
                        <td class="text-align-right border-top border-bottom color-red"><strong>{{ "$ ". number_format($total_more_than_120, 2) }}</strong></td>
                        <td class="text-align-right border-top border-right border-bottom"><strong>{{ "$ ". number_format($totals, 2) }}</strong></td>
                    </tr>
                    @php
                        $count = 1;
                        $total_days_30 = 0;
                        $total_days_60 = 0;
                        $total_days_90 = 0;
                        $total_days_120 = 0;
                        $total_more_than_120 = 0;
                        $totals = 0;
                    @endphp
                @else
                    @php $count ++; @endphp
                @endif
                @php $id = $t->supplier_id; @endphp
            @endforeach
            <tr><td class="separator" colspan="12"></td></tr>
            <tr class="final_totals">
                <td class="bordered" colspan="6">{{ __("report.totals") }}</td>
                <td class="border-top border-bottom text-align-right color-green">{{ $final_totals['days_30'] > 0 ? "$ ". number_format($final_totals['days_30'], 2) : "$ 0.00" }}</td>
                <td class="border-top border-bottom text-align-right color-red">{{ $final_totals['days_60'] > 0 ? "$ ". number_format($final_totals['days_60'], 2) : "$ 0.00" }}</td>
                <td class="border-top border-bottom text-align-right color-red">{{ $final_totals['days_90'] > 0 ? "$ ". number_format($final_totals['days_90'], 2) : "$ 0.00" }}</td>
                <td class="border-top border-bottom text-align-right color-red">{{ $final_totals['days_120'] > 0 ? "$ ". number_format($final_totals['days_120'], 2) : "$ 0.00" }}</td>
                <td class="border-top border-bottom text-align-right color-red">{{ $final_totals['more_than_120_days'] > 0 ? "$ ". number_format($final_totals['more_than_120_days'], 2) : "$ 0.00" }}</td>
                <td class="border-top border-bottom text-align-right border-right">{{ $final_totals['totals'] > 0 ? "$ ". number_format($final_totals['totals'], 2) : "$ 0.00" }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
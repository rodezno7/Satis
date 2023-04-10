<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@lang('accounting.ledgers_menu')</title>    
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000000;
            font-size: {{ $size }}pt;
        }
        h3, h4 {
            text-align: center;
        }
        table {
            border-collapse: collapse;
        }
        th {
            border: 0.25px solid black;
            padding: 4px;
            text-align: center;
        }
        .alnright { text-align: right; }
        .alnleft { text-align: left; }
        .alncenter { text-align: center; }
        @page{
            margin-bottom: 75px;
        }
        #header, #footer {
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
        .page-number:before {
            content: "Página " counter(page);
        }
        .border-top {
            border-top: 1px solid #000;
        }
        .border-top-bottom {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        .border-bottom {
            border-bottom: 1px solid #000;
        }
        thead tr th {
            border-top: none;
            border-left: none;
            border-right: none;
            border-bottom: 1px solid #000;
        }
        .text-left {
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div id="footer">
        <div class="page-number"></div>
    </div>
    <table style="width: 100%;">
        <thead>
            <tr>
                <td colspan="5" class="td2" style="text-align: center;">
                    <strong>{{ mb_strtoupper($business_name) }} </strong><br>
                    <strong>{{ mb_strtoupper($report_name) }} </strong><br>
                    <b>{{ mb_strtoupper($date_range) }}</b><br>
                    <b>{{ mb_strtoupper(__('accounting.accountant_report_values')) }}</b>
                </td>
            </tr>
            <tr>
                <th class="border-bottom text-center" style="width: 10%">
                    {{ mb_strtoupper(__('accounting.date')) }}
                </th>
                <th class="border-bottom text-center">
                    {{ mb_strtoupper(__('accounting.concept')) }}
                </th>
                <th class="border-bottom text-center" style="width: 12%">
                    {{ mb_strtoupper(__('accounting.debit')) }}
                </th>
                <th class="border-bottom text-center" style="width: 12%">
                    {{ mb_strtoupper(__('accounting.credit')) }}
                </th>
                <th class="border-bottom text-center" style="width: 12%">
                    {{ mb_strtoupper(__('accounting.balance')) }}
                </th>
            </tr>
        </thead>

        @php
        $total_debit = 0.00;
        $total_credit = 0.00;
        @endphp

        @foreach($accounts as $account)
            @if((number_format($account->debit_initial, 2) != 0.00) || (number_format($account->credit_initial, 2) != 0.00) || (number_format($account->debit_range, 2) != 0.00) || (number_format($account->credit_range, 2) != 0.00) || (number_format($account->debit_final, 2) != 0.00) || (number_format($account->credit_final, 2) != 0.00))
                @if($account->type == 'debtor')
                    @php($balance = $account->debit_initial - $account->credit_initial)
                    @php($balance_final = $account->debit_final - $account->credit_final)
                @else
                    @php($balance = $account->credit_initial - $account->debit_initial)
                    @php($balance_final = $account->credit_final - $account->debit_final)
                @endif
                @if(number_format($balance, 2) >= 0.00)
                    @php($balance_label = number_format($balance, 2))
                @else
                    @php($balance_label = "(".number_format(($balance * -1), 2).")")
                @endif
                {{-- Month diff --}}
                @php($diff = $account->debit_range - $account->credit_range)
                @if ($diff >= 0)
                    @php( $diff_formatted = number_format($diff, 2))
                @else
                    @php( $diff_formatted = "(".number_format(($diff * -1), 2).")")
                @endif
                {{-- Balance total --}}
                @if ($balance_final >= 0)
                    @php( $diff_formatted = number_format($diff, 2))
                @else
                    @php( $balance_final_formatted = "(".number_format(($diff * -1), 2).")")
                @endif
                @if((number_format($balance, 2) != 0.00) || (number_format($balance_final, 2) != 0.00) || (number_format($account->debit_range, 2) != 0.00) || (number_format($account->credit_range, 2) != 0.00))
                    <tr>
                        <td>
                            <b>{{ $account->code }}</b>
                        </td>
                        <td colspan="4">
                            <b>{{ $account->name }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="border-bottom text-right">
                            {{ mb_strtoupper(__('accounting.previous_balance')) }}
                        </td>
                        <td class="alnright border-bottom">
                            {{ $balance_label }}
                        </td>
                    </tr>
                    @foreach($lines as $detail)
                        @if($account->code == $detail->code)
                            @if($account->type == 'debtor')
                                @php($balance = $balance + $detail->debit - $detail->credit)
                            @else
                            @php($balance = $balance - $detail->debit + $detail->credit)
                        @endif
                        @if(number_format($balance, 2) >= 0.00)
                            @php($balance_label_line = number_format($balance, 2))
                        @else
                            @php($balance_label_line = "(".number_format(($balance * -1), 2).")")
                        @endif
                        @if((number_format($account->debit_range, 2) != 0.00) || (number_format($account->credit_range, 2) != 0.00))
                            @if(number_format($detail->debit, 2) >= 0.00)
                                @php($debit_label = number_format($detail->debit, 2))
                            @else
                                @php($debit_label = "(".number_format(($detail->debit * -1), 2).")")
                            @endif
                            @if(number_format($detail->credit, 2) >= 0.00)
                                @php($credit_label = number_format($detail->credit, 2))
                            @else
                                @php($credit_label = "(".number_format(($detail->credit * -1), 2).")")
                            @endif
                            <tr>
                                <td>{{ date('d/m/y', strtotime($detail->date)) }}</td>
                                <td>{{ mb_strtoupper(__('accounting.movements_day')) }}</td>
                                <td class="alnright">{{ $debit_label }}</td>
                                <td class="alnright">{{ $credit_label }}</td>
                                <td class="alnright">{{ $balance_label_line }}</td>
                            </tr>
                            @endif
                        @endif
                    @endforeach
                    @if((number_format($account->debit_range, 2) == 0.00) && (number_format($account->credit_range, 2) == 0.00))
                        <tr>
                            <td>&nbsp;</td>
                            <td>{{ mb_strtoupper(__('accounting.out_moves')) }}</td>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                    @endif
                    @if(number_format($account->debit_range, 2) >= 0.00)
                        @php($debit_range_label = number_format($account->debit_range, 2))
                    @else
                        @php($debit_range_label = "(".number_format(($account->debit_range * -1), 2).")")
                    @endif
                    @if(number_format($account->credit_range, 2) >= 0.00)
                        @php($credit_range_label = number_format($account->credit_range, 2))
                    @else
                        @php($credit_range_label = "(".number_format(($account->credit_range * -1), 2).")")
                    @endif
                    @if(number_format($balance_final, 2) >= 0.00)
                        @php($balance_final_label = number_format($balance_final, 2))
                    @else
                        @php($balance_final_label = "(".number_format(($balance_final * -1), 2).")")
                    @endif
                    <tr>
                        <td class="alnright border-top-bottom text-left" colspan="2">
                            {{ mb_strtoupper(__('accounting.totals')) }}
                        </td>
                        <td class="alnright border-top-bottom">
                            <b>{{ $debit_range_label }}</b>
                        </td>
                        <td class="alnright border-top-bottom">
                            <b>{{ $credit_range_label }}</b>
                        </td>
                        <td class="alnright border-top-bottom">
                            <b>{{ $balance_final_label }}</b>
                        </td>
                    </tr>
                    @php($total_debit = $total_debit + $account->debit_range)
                    @php($total_credit = $total_credit + $account->credit_range)
                    <tr>
                        <td colspan="5">&nbsp;</td>
                    </tr>
                @endif
            @endif
        @endforeach
    </table>
    <table style="width: 100%;" class="table1">
        @if(number_format($total_debit, 2) >= 0.00)
            @php($total_debit_label = number_format($total_debit, 2))
        @else
            @php($total_debit_label = "(".number_format(($total_debit * -1), 2).")")
        @endif
        @if(number_format($total_credit, 2) >= 0.00)
            @php($total_credit_label = number_format($total_credit, 2))
        @else
            @php($total_credit_label = "(".number_format(($total_credit * -1), 2).")")
        @endif
        <tr>
            <td><strong>{{ mb_strtoupper(__('accounting.total_general')) }}</strong></td>
            <td style="width: 12%"class="alnright border-top"><strong>{{ $total_debit_label }}</strong></td>
            <td style="width: 12%"class="alnright border-top"><strong>{{ $total_credit_label }}</strong></td>
            <td style="width: 12%" class="alnright border-top">&nbsp;</td>
        </tr>
    </table>
</body>
</html>
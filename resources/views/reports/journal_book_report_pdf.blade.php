<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@lang('accounting.general_journal_book')</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm;
            font-family: 'Courier New', Courier, monospace;
            font-size: 10pt;
        }

        h1 { font-size: 14pt; text-align: center; font-weight: bold; margin: 0.1cm 0 0 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 0.5cm; }
        table tr td, table tr th { padding: 3px 5px; }
        .border-top { border-top: 1px solid #000; }
        .border-bottom { border-bottom: 1px solid #000; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h1>{{ mb_strtoupper($business_name) }}</h1>
    <h1>{{ mb_strtoupper(__('accounting.general_journal_book')) ." ". strtoupper(__('accounting.from_date')) ." ". $start_date ." ". strtoupper(__('accounting.to_date')) ." ". $end_date }}</h1>
    <h1>{{ mb_strtoupper(__('accounting.accountant_report_values')) }}</h1>
    <table>
        <thead>
            <tr>
                <th class="border-bottom" style="width: 10%;">{{ strtoupper(__('accounting.correlative')) }}</th>
                <th class="border-bottom" style="width: 10%;">{{ strtoupper(__('accounting.account')) }}</th>
                <th class="border-bottom">{{ strtoupper(__('accounting.concept')) }}</th>
                <th class="border-bottom" style="width: 10%;">{{ strtoupper(__('accounting.charges')) }}</th>
                <th class="border-bottom" style="width: 10%;">{{ strtoupper(__('accounting.payments')) }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $day = 0;
                $total_debit = 0;
                $total_credit = 0;
                $total_total_debit = 0;
                $total_total_credit = 0;
            @endphp
            @foreach ($journal_book as $jb)
                @if ($day != $jb->day && $day != 0)
                    <tr>
                        <th colspan="2">&nbsp;</th>
                        <th>TOTAL</th>
                        <th class="border-top text-right">
                            {{ number_format($total_debit, 2) }}
                        </th>
                        <th class="border-top text-right">
                            {{ number_format($total_credit, 2) }}
                        </th>
                    </tr>
                    <tr>
                        <td colspan="5">&nbsp;</td>
                    </tr>
                    @php
                        $total_total_debit += $total_debit;
                        $total_total_credit += $total_credit;
                        $total_debit = 0;
                        $total_credit = 0;
                    @endphp
                @endif
                @if ($day != $jb->day)
                    <tr>
                        <th colspan="3" class="text-center border-bottom">
                            NO. DE COMPROBANTE CONTABLE: D {{ $jb->day }}
                        </th>
                        <th colspan="2" class="border-bottom">
                            FECHA {{ @format_date($jb->date) }}
                        </th>
                    </tr>
                @endif
                <tr>
                    <td class="text-center">{{ $jb->correlative }}</td>
                    <td>{{ $jb->account_code }}</td>
                    <td>{{ $jb->account_name }}</td>
                    <td class="text-right">
                        @if ($jb->debit > 0) {{ number_format($jb->debit, 2) }} @endif
                    </td>
                    <td class="text-right">
                        @if ($jb->credit > 0) {{ number_format($jb->credit, 2) }} @endif
                    </td>
                </tr>
                @php
                    $day = $jb->day;
                    $total_debit += $jb->debit;
                    $total_credit += $jb->credit;
                @endphp
            @endforeach
            <tr>
                <th colspan="2">&nbsp;</th>
                <th>TOTALES</th>
                <th class="border-top text-right">
                    {{ number_format($total_debit, 2) }}
                </th>
                <th class="border-top text-right">
                    {{ number_format($total_credit, 2) }}
                </th>
            </tr>
            <tr>
                <td colspan="5">&nbsp;</td>
            </tr>
            <tr>
                <th colspan="2" class="border-top border-bottom">&nbsp;</th>
                <th class="border-top border-bottom">TOTAL GENERAL</th>
                <th class="border-top border-bottom text-right">
                    {{ number_format($total_total_debit, 2) }}
                </th>
                <th class="border-top border-bottom text-right">
                    {{ number_format($total_total_credit, 2) }}
                </th>
            </tr>
        </tbody>
    </table>
</body>
</html>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('accounting.book_sales_taxpayer')</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000000;
            font-size: {{ $size }}pt;
        }
        h3, h4 {
            text-align: center;
        }
        .table1 {
            border: 0px;
        }
        .table2 {
            border-collapse: collapse;
            border: 0.25px solid black;
        }
        .td2 {
            border: 0px;
        }
        td {
            border: 0.25px solid black;
            padding: 4px;
            text-align: left;
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
        .page-number:before {
            content: "Página " counter(page);
        }
        .bt { border-top: 0.25px solid black; }
        .bb { border-bottom: 0.25px solid black; }
        .br { border-right: 0.25px solid black; }
        .bl { border-left: 0.25px solid black; }
        .no-bt { border-top: 0.25px solid white; }
        .no-bb { border-bottom: 0.25px solid white; }
        .no-br { border-right: 0.25px solid white; }
        .no-bl { border-left: 0.25px solid white; }
  </style>   
</head>

<body>
    <div id="footer">
        <div class="page-number"></div>
    </div>
    <table class="table1" style="width: 100%;">
        <tr>
            <td class="td2 alncenter">
                <strong>{{ mb_strtoupper($business->business_full_name) }}</strong>
            </td>
        </tr>
        <tr>
            <td class="td2 alncenter">
                <strong>{{ mb_strtoupper(__('accounting.book_sales_taxpayer')) }}</strong>
            </td>
        </tr>
    </table>
    <br>

    <table class="table1" style="width: 100%;">
        <tr>
            <td class="td2" colspan="4">
                <strong>{{ mb_strtoupper(__('accounting.property_name')) }}:</strong>
                {{ $business->business_full_name }}
            </td>
        </tr>
        <tr>
            <td class="td2" style="width: 25%">
                <strong>{{ mb_strtoupper(__('accounting.month')) }}:</strong>
                @if ($initial_month == $final_month)
                {{ $initial_month }}
                @else
                {{ $initial_month }} - {{ $final_month }}
                @endif
            </td>
            <td class="td2" style="width: 25%">
                <strong>{{ mb_strtoupper(__('accounting.year')) }}:</strong>
                @if ($initial_year == $final_year)
                {{ $initial_year }}
                @else
                {{ $initial_year }} - {{ $final_year }}
                @endif
            </td>
            <td class="td2" style="width: 25%">
                <strong>{{ mb_strtoupper(__('business.nit')) }}:</strong>
                {{ $business->nit }}
            </td>
            <td class="td2" style="width: 25%">
                <strong>{{ mb_strtoupper(__('accounting.record_no')) }}:</strong>
                {{ $business->nrc }}
            </td>
        </tr>
    </table>
    <br>

    <table class="table2" style=" width: 100%;">
        <thead>
            <tr>
                <th rowspan="3">{{ mb_strtoupper(__('accounting.no_tag')) }}</th>
                <th rowspan="3">{{ mb_strtoupper(__('accounting.date_of_issue')) }}</th>
                <th rowspan="3">{{ mb_strtoupper(__('accounting.pre-printed_serial_number')) }}</th>
                <th rowspan="3">SERIE</th>
                <th rowspan="3">N° RESOLUCIÓN</th>
                <th rowspan="3">{{ mb_strtoupper(__('accounting.control_single_computer_system')) }}</th>
                <th rowspan="3">{{ mb_strtoupper(__('business.nrc')) }}</th>
                <th rowspan="3">DUI</th>
                <th rowspan="3">NIT</th>
                <th rowspan="3" style="width: 15%;">{{ mb_strtoupper(__('accounting.name_client_principal_agent')) }}</th>
                <th colspan="6">{{ mb_strtoupper(__('accounting.own_sales_operations_and_third_parties')) }}</th>
                <th rowspan="3">{{ mb_strtoupper(__('accounting.tax_withheld')) }} 1%</th>
                <th rowspan="3">{{ mb_strtoupper(__('accounting.total_sales')) }}</th>
            </tr>
            <tr>
                <th colspan="3">{{ mb_strtoupper(__('accounting.own_sales')) }}</th>
                <th colspan="3">{{ mb_strtoupper(__('accounting.third_party_account')) }}</th>
            </tr>
            <tr>
                <th>{{ mb_strtoupper(__('accounting.exempt')) }}</th>
                <th>{{ mb_strtoupper(__('accounting.internal')) }}</th>
                <th>{{ mb_strtoupper(__('accounting.fiscal_debit')) }}</th>
                <th>{{ mb_strtoupper(__('accounting.exempt')) }}</th>
                <th>{{ mb_strtoupper(__('accounting.internal')) }}</th>
                <th>{{ mb_strtoupper(__('accounting.fiscal_debit')) }}</th>
            </tr>
        </thead>
        <tbody>
            @php
            $total_internal = 0;
            $total_fiscal_debit = 0;
            $sum_total_sales = 0;
            $total_tax_amount = 0;
            @endphp
            @foreach ($lines as $item)
            <tr>
                <td class="alnright">{{ $loop->iteration }}</td>
                <td>{{ $item->transaction_date }}</td>
                <td class="alnright">{{ $item->correlative }}</td>
                <td class="alnright">{{ $item->serie }}</td>
                <td class="alnright">{{ $item->resolution }}</td>
                <td></td>
                <td>
                    @if ($item->status != 'annulled')
                    {{ $item->nrc }}
                    @endif
                </td>
                <td>
                    @if ($item->status != 'annulled')
                    {{ $item->dui }}
                    @endif
                </td>
                <td>
                    @if ($item->status != 'annulled')
                    {{ $item->nit }}
                    @endif
                </td>
                <td>
                    {{ $item->customer }}
                    @if ($item->status == 'annulled')
                    - {{ mb_strtoupper(__('accounting.annulled')) }}
                    @endif
                </td>
                <td></td>
                <td class="alnright">
                    @if ($item->status != 'annulled')
                        @if ($item->internal < 0)
                        $ ({{ number_format(abs($item->internal), 2) }})
                        @else
                        $ {{ number_format($item->internal, 2) }}
                        @endif
                    @else
                    $&nbsp;&nbsp;&nbsp;0.00
                    @endif
                </td>
                <td class="alnright">
                    @if ($item->status != 'annulled')
                        @if ($item->fiscal_debit < 0)
                        $ ({{ number_format(abs($item->fiscal_debit), 2) }})
                        @else
                        $ {{ number_format($item->fiscal_debit, 2) }}
                        @endif
                    @else
                    $&nbsp;&nbsp;&nbsp;0.00
                    @endif
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td class="alnright">
                    @if ($item->status != 'annulled')
                        @if ($item->tax_amount < 0)
                        $ ({{ number_format(abs($item->tax_amount), 2) }})
                        @else
                        $ {{ number_format($item->tax_amount, 2) }}
                        @endif
                    @else
                    &nbsp;&nbsp;&nbsp;0.00
                    @endif
                </td>
                <td class="alnright">
                    @if ($item->status != 'annulled')
                        @if ($item->total_sales < 0)
                        $ ({{ number_format(abs($item->total_sales), 2) }})
                        @else
                        $ {{ number_format($item->total_sales, 2) }}
                        @endif
                    @else
                    &nbsp;&nbsp;&nbsp;0.00
                    @endif
                </td>
            </tr>
            @php
            if ($item->status != 'annulled') {
                $total_internal += $item->internal;
                $total_fiscal_debit += $item->fiscal_debit;
                $sum_total_sales += $item->total_sales;
                $total_tax_amount += $item->tax_amount;
            }
            @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="10" class="alnright">
                    <strong>{{ mb_strtoupper(__('accounting.totals')) }}</strong>
                </td>
                <td></td>
                <td class="alnright">
                    <strong>
                        @if ($total_internal < 0)
                        $ ({{ number_format(abs($total_internal), 2) }})
                        @else
                        $ {{ number_format($total_internal, 2) }}
                        @endif
                    </strong>
                </td>
                <td class="alnright">
                    <strong>
                        @if ($total_fiscal_debit < 0)
                        $ ({{ number_format(abs($total_fiscal_debit), 2) }})
                        @else
                        $ {{ number_format($total_fiscal_debit, 2) }}
                        @endif
                    </strong>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td class="alnright">
                    <strong>
                        @if ($total_tax_amount < 0)
                        $ ({{ number_format(abs($total_tax_amount), 2) }})
                        @else
                        $ {{ number_format($total_tax_amount, 2) }}
                        @endif
                    </strong>
                </td>
                <td class="alnright">
                    <strong>
                        @if ($sum_total_sales < 0)
                        $ ({{ number_format(abs($sum_total_sales), 2) }})
                        @else
                        $ {{ number_format($sum_total_sales, 2) }}
                        @endif
                    </strong>
                </td>
            </tr>
        </tfoot>
    </table>
    <br><br><br>

    <table class="table1" style="width: 5cm;">
        <tr>
            <td class="td2" style="width: 0.2cm;">F.</td>
            <td class="td2 bb"></td>
        </tr>
        <tr>
            <td class="td2"></td>
            <td class="td2 alncenter">Contador</td>
        </tr>
    </table>

</body>
</html>
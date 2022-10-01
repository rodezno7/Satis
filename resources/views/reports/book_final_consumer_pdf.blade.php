<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('accounting.book_sales_final_consumer')</title>
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
        .pd-rg-5 { padding-right: 10px; }
  </style>   
</head>

<body>
    <div id="footer">
        <div class="page-number"></div>
    </div>
    <table class="table1" style="width: 100%;">
        <tr>
            <td class="td2 alncenter">
                <strong>{{ mb_strtoupper($business->business_full_name) }}</strong><br>
                <strong>{{ mb_strtoupper(__('accounting.book_sales_final_consumer')) }}</strong>
            </td>
        </tr>
    </table>
    <br>

    <table class="table1" style="width: 100%;">
        <tr>
            <td class="td2">
                <strong>{{ mb_strtoupper(__('accounting.month')) }}:</strong>
                @if ($initial_month == $final_month)
                {{ $initial_month }}
                @else
                {{ $initial_month }} - {{ $final_month }}
                @endif
                <span style="float: right">
                    <strong>{{ mb_strtoupper(__('accounting.record_no')) }}:</strong>
                    {{ $business->nrc }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="td2">
                <strong>{{ mb_strtoupper(__('accounting.year')) }}:</strong>
                @if ($initial_year == $final_year)
                {{ $initial_year }}
                @else
                {{ $initial_year }} - {{ $final_year }}
                @endif
                <span style="float: right">
                    <strong>{{ mb_strtoupper(__('accounting.nit_no')) }}:</strong>
                    {{ $business->nit }}
                </span>
            </td>
        </tr>
    </table>
    <br>

    <table class="table2" style=" width: 100%;">
        <thead>
            <tr>
                <th rowspan="2">@lang('accounting.date_of_issue')</th>
                <th colspan="4">@lang('accounting.documents_issued')</th>
                <th rowspan="2">@lang('accounting.cash_or_computerized_system_number')</th>
                <th colspan="3">@lang('role.sales')</th>
                <th rowspan="2">@lang('accounting.total_daily_own_sales')</th>
                <th rowspan="2">@lang('accounting.sales_on_behalf_of_third_parties')</th>
            </tr>
            <tr>
                <th>@lang('accounting.from_no')</th>
                <th>@lang('accounting.to_no')</th>
                <th>SERIE</th>
                <th>RESOLUCIÓN</th>
                <th>@lang('accounting.exempt')</th>
                <th>@lang('accounting.internal_taxed')</th>
                <th>@lang('accounting.exports')</th>
            </tr>
        </thead>
        <tbody>
            @php
            $total_fcf = 0;
            $total_ticket = 0;
            $total_exports = 0;
            @endphp
            @foreach ($lines as $item)
            <tr>
                <td>{{ @format_date($item->transaction_date) }}</td>
                <td class="alnright">{{ $item->initial_correlative }}</td>
                <td class="alnright">{{ $item->final_correlative }}</td>
                <td class="alnright">{{ $item->serie }}</td>
                <td class="alnright">{{ $item->resolution }}</td>
                <td></td>
                <td class="alnright"></td>
                <td>
                    <span style="float: right;">
                        @if (! empty($item->taxed_sales))
                            {{ "$ " . number_format($item->taxed_sales, 2) }}
                        @endif
                    </span>
                </td>
                <td>
                    <span style="float: right;">
                        @if (! empty($item->exports))
                            {{ "$ " . number_format($item->exports, 2) }}
                        @endif
                    </span>
                </td>
                <td>
                    <span style="float: right;">{{ "$ " . number_format($item->taxed_sales + $item->exports, 2) }}</span>
                </td>
                <td class="alnright"></td>
            </tr>
            @php
                if (config('app.business') == 'optics') {
                    if ($item->short_name == 'FACTURA') {
                        $total_fcf += $item->taxed_sales;
                    } else if ($item->short_name == 'Ticket') {
                        $total_ticket += $item->taxed_sales;
                    }

                } else {
                    if ($item->short_name == 'FCF') {
                        $total_fcf += $item->taxed_sales;
                    } else if ($item->short_name == 'Ticket') {
                        $total_ticket += $item->taxed_sales;
                    }
                }

                $total_exports += $item->exports;
            @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="alncenter"><strong>{{ mb_strtoupper(__('accounting.totals')) }}</strong></td>
                <td class="alnright"></td>
                <td>
                    <span style="float: right;"><strong>{{ "$ " . number_format(($total_fcf + $total_ticket), 2) }}</strong></span>
                </td>
                <td>
                    <span style="float: right;"><strong>{{ "$ " . number_format($total_exports, 2) }}</strong></span>
                </td>
                <td>
                    <span style="float: right;"><strong>{{ "$ " . number_format(($total_fcf + $total_ticket + $total_exports), 2) }}</strong></span>
                </td>
                <td class="alnright"></td>
            </tr>
        </tfoot>
    </table>
    <br><br>

    <table class="table2" style="width: 100%; page-break-inside: avoid;">
        <thead>
            <tr>
                <th rowspan="2" class="no-bt no-bl no-bb"></th>
                <th colspan="2" class="alncenter"><strong>{{ mb_strtoupper(__('accounting.own')) }}</strong></th>
                <th colspan="2" class="alncenter"><strong>{{ mb_strtoupper(__('accounting.third_party_account')) }}</strong></th>
                <th rowspan="2" class="alncenter">TOTAL</th>
            </tr>
            <tr>
                <th class="alncenter"><strong>{{ mb_strtoupper(__('accounting.net_worth')) }}</strong></th>
                <th class="alncenter"><strong>{{ mb_strtoupper(__('accounting.fiscal_debit')) }}</strong></th>
                <th class="alncenter"><strong>{{ mb_strtoupper(__('accounting.net_worth')) }}</strong></th>
                <th class="alncenter"><strong>{{ mb_strtoupper(__('accounting.fiscal_debit')) }}</strong></th>
            </tr>
        </thead>
        @php
            $total_fcf_exc_tax = $total_fcf / 1.13;
            $total_fcf_tax = $total_fcf - ($total_fcf / 1.13);

            $total_ticket_exc_tax = $total_ticket / 1.13;
            $total_ticket_tax = $total_ticket - ($total_ticket / 1.13);

            $total_exports_exc_tax = $total_exports;
            $total_exports_tax = 0;
        @endphp
        <tbody>
            <tr>
                <td class="no-bb no-bl">{{ mb_strtoupper(__('accounting.consumer_taxed_domestic_sales')) }}</td>
                <td class="alnright pd-rg-5">$ {{ $total_fcf ? number_format($total_fcf_exc_tax, 2) : "&nbsp;&nbsp;&nbsp;0" }}</td>
                <td class="alnright pd-rg-5">$ {{ $total_fcf ? number_format($total_fcf_tax, 2) : "&nbsp;&nbsp;&nbsp;0" }} </td>
                <td></td>
                <td></td>
                <th class="alnright pd-rg-5">$ {{ $total_fcf ? number_format($total_fcf, 2) : "&nbsp;&nbsp;&nbsp;0" }}</th>
            </tr>
            <tr>
                <td class="no-bb no-bl">
                    {{ mb_strtoupper(__('accounting.domestic_sales_exempt_consumer')) }}
                </td>
                <td class="alnright pd-rg-5">$&nbsp;&nbsp;&nbsp;0</td>
                <td class="alnright pd-rg-5">$&nbsp;&nbsp;&nbsp;0</td>
                <td></td>
                <td></td>
                <td class="alnright pd-rg-5">$&nbsp;&nbsp;&nbsp;0</td>
            </tr>
            @if (config('app.business') != 'optics')
            <tr>
                <td class="no-bb no-bl">VENTAS INTERNAS GRAVADAS CONSUMIDOR TICKET</td>
                <td class="alnright pd-rg-5">$ {{ $total_ticket ? number_format($total_ticket_exc_tax, 2) : "&nbsp;&nbsp;&nbsp;0" }}</td>
                <td class="alnright pd-rg-5">$ {{ $total_ticket ? number_format($total_ticket_tax, 2) : "&nbsp;&nbsp;&nbsp;0" }}</td>
                <td></td>
                <td></td>
                <th class="alnright pd-rg-5">$ {{ $total_ticket ? number_format($total_ticket, 2) : "&nbsp;&nbsp;&nbsp;0" }}</th>
            </tr>
            @endif
            <tr>
                <td class="no-bb no-bl">{{ mb_strtoupper(__('accounting.exports_according_to_invoices')) }}</td>
                <td class="alnright pd-rg-5">$ {{ $total_exports ? number_format($total_exports_exc_tax, 2) : "&nbsp;&nbsp;&nbsp;0" }}</td>
                <td class="alnright pd-rg-5">$ {{ $total_exports ? number_format($total_exports_tax, 2) : "&nbsp;&nbsp;&nbsp;0" }}</td>
                <td></td>
                <td></td>
                <th class="alnright pd-rg-5">$ {{ $total_exports ? number_format($total_exports, 2) : "&nbsp;&nbsp;&nbsp;0" }}</th>
            </tr>
            <tr>
                <td class="no-bb no-bl">TOTALES</td>
                <th class="alnright pd-rg-5">$ {{ number_format(($total_fcf_exc_tax + $total_ticket_exc_tax + $total_exports_exc_tax), 2) }}</th>
                <th class="alnright pd-rg-5">$ {{ number_format(($total_fcf_tax + $total_ticket_tax + $total_exports_tax), 2) }}</th>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <th class="alnright pd-rg-5">$ {{ number_format($total_fcf + $total_ticket + $total_exports, 2) }}</th>
            </tr>
        </tbody>
    </table>
    <br><br><br><br>

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
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('accounting.book_sales_final_consumer')</title>
    <style>
        @page {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000000;
            font-size: 8pt;
            margin: 1.5cm;
            /** font-size: {{ $size }}pt; */
        }
        h1 {
            text-align: center;
            font-size: 8pt;
            margin: 0;
        }

        h1.business-name,
        h1.report-name { margin: 0 0 8px 0; }
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table.header {
            margin: 12px 0 6px 0;
            page-break-inside: avoid;
        }

        table.header tr td { padding: 2px 0; }
        table.detail-report tr th,
        table.detail-report tr td { border: 1px solid #000; padding: 2px 4px; }
        table.summary-report {
            margin-top: 18px;
            margin-bottom: 32px;
            page-break-inside: avoid;
        }
        table.summary-report tr th,
        table.summary-report tr td { border: 1px solid #000; padding: 2px 4px; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .no-bt { border-top: none !important; }
        .no-br { border-right: none !important; }
        .no-bb { border-bottom: none !important; }
        .no-bl { border-left: none !important; }
  </style>   
</head>
<body>
    <div class="container">
        <h1 class="business-name">{{ mb_strtoupper($business->business_full_name) }}</h1>
        @if ($location > 0)
            <h1 class="report-name">{{ mb_strtoupper(__('accounting.book_sales_final_consumer')) }}</h1>
        @endif
        @php $general_total = collect(); @endphp
        @foreach ($locations as $k => $l)
            <table class="header">
                <tr>
                    <td style="width: 25%" class="text-left">
                        <b>{{ mb_strtoupper(__('accounting.month')) }}:</b>&nbsp;
                        @if ($initial_month == $final_month)
                            {{ $initial_month }}
                        @else
                            {{ $initial_month }} - {{ $final_month }}
                        @endif
                    </td>
                    <td>
                        @if ($location == 0)
                            <h1>{{ mb_strtoupper($l) }}</h1>
                        @else
                            &nbsp;
                        @endif
                    </td>
                    <td style="width: 25%;" class="text-right">
                        <b>{{ mb_strtoupper(__('accounting.record_no')) }}:</b>&nbsp;
                        {{ $business->nrc }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%" class="text-left">
                        <b>{{ mb_strtoupper(__('accounting.year')) }}:</b>&nbsp;
                        @if ($initial_year == $final_year)
                            {{ $initial_year }}
                        @else
                            {{ $initial_year }} - {{ $final_year }}
                        @endif
                    </td>
                    <td>
                        @if ($location == 0)
                            <h1>{{ mb_strtoupper(__('accounting.book_sales_final_consumer')) }}</h1>
                        @else
                            &nbsp;
                        @endif
                    </td>
                    <td style="width: 25%;" class="text-right">
                        <b>{{ mb_strtoupper(__('accounting.nit_no')) }}:</b>&nbsp;
                        {{ $business->nit }}
                    </td>
                </tr>
            </table>
            <table class="detail-report">
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
                        <th>Serie</th>
                        <th>Resolución</th>
                        <th>@lang('accounting.exempt')</th>
                        <th>@lang('accounting.internal_taxed')</th>
                        <th>@lang('accounting.exports')</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $data = $lines->where('location_id', $k);
                        $total_fcf = 0;
                        $total_ticket = 0;
                        $total_exports = 0;
                    @endphp
                    @foreach ($data as $d)
                        <tr>
                            <td>{{ @format_date($d->transaction_date) }}</td>
                            <td class="text-right">{{ $d->initial_correlative }}</td>
                            <td class="text-right">{{ $d->final_correlative }}</td>
                            <td class="text-right">{{ $d->serie }}</td>
                            <td class="text-right">{{ $d->resolution }}</td>
                            <td></td>
                            <td class="text-right"></td>
                            <td class="text-right">
                                @if (! empty($d->taxed_sales))
                                    {{ "$ ". number_format($d->taxed_sales, 2) }}
                                @endif
                            </td>
                            <td class="text-right">
                                @if (! empty($d->exports))
                                    {{ "$ ". number_format($d->exports, 2) }}
                                @endif
                            </td>
                            <td class="text-right">
                                {{ "$ ". number_format($d->taxed_sales + $d->exports, 2) }}
                            </td>
                            <td></td>
                        </tr>
                        @php
                            if (config('app.business') == 'optics') {
                                if ($d->short_name == 'FACTURA') {
                                    $total_fcf += $d->taxed_sales;
                                } else if ($d->short_name == 'Ticket') {
                                    $total_ticket += $d->taxed_sales;
                                }
                            } else {
                                if ($d->short_name == 'FCF') {
                                    $total_fcf += $d->taxed_sales;
                                } else if ($d->short_name == 'Ticket') {
                                    $total_ticket += $d->taxed_sales;
                                }
                            }
                            $total_exports += $d->exports;
                        @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-center">{{ mb_strtoupper(__('accounting.totals')) }}</th>
                        <th>&nbsp;</th>
                        <th class="text-right">
                            {{ "$ " . number_format(($total_fcf + $total_ticket), 2) }}
                        </th>
                        <th class="text-right">
                            {{ "$ " . number_format($total_exports, 2) }}
                        </th>
                        <th class="text-right">
                            {{ "$ " . number_format(($total_fcf + $total_ticket + $total_exports), 2) }}
                        </th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            <table class="summary-report">
                <thead>
                    <tr>
                        <th rowspan="2" class="no-bt no-bl no-bb" style="width: 40%;">TOTAL {{ mb_strtoupper($l) }}</th>
                        <th colspan="2" class="text-center"><strong>{{ mb_strtoupper(__('accounting.own')) }}</strong></th>
                        <th colspan="2" class="text-center"><strong>{{ mb_strtoupper(__('accounting.third_party_account')) }}</strong></th>
                        <th rowspan="2" class="text-center">TOTAL</th>
                    </tr>
                    <tr>
                        <th class="text-center"><strong>{{ mb_strtoupper(__('accounting.net_worth')) }}</strong></th>
                        <th class="text-center"><strong>{{ mb_strtoupper(__('accounting.fiscal_debit')) }}</strong></th>
                        <th class="text-center"><strong>{{ mb_strtoupper(__('accounting.net_worth')) }}</strong></th>
                        <th class="text-center"><strong>{{ mb_strtoupper(__('accounting.fiscal_debit')) }}</strong></th>
                    </tr>
                </thead>
                @php
                    $total_fcf_exc_tax = $total_fcf / 1.13;
                    $total_fcf_tax = $total_fcf - ($total_fcf / 1.13);

                    $general_total->push(
                        collect([
                            "order" => 1,
                            "description"  => "VENTAS INTERNAS GRAVADAS CONSUMIDOR FINAL ". strtoupper($l),
                            "excluded_tax"   => $total_fcf_exc_tax,
                            "taxes"   => $total_fcf_tax,
                            "final_total" => $total_fcf
                        ])
                    );
                    if (config('app.business') != 'optics') {
                        $general_total->push(
                            collect([
                                "order" => 2,
                                "description"  => "VENTAS INTERNAS EXENTAS CONSUMIDOR ". strtoupper($l),
                                "excluded_tax"   => "0",
                                "taxes"   => "0",
                                "final_total" => "0"
                            ])
                        );
                    }

                    $total_ticket_exc_tax = $total_ticket / 1.13;
                    $total_ticket_tax = $total_ticket - ($total_ticket / 1.13);
                    
                    if (config('app.business') != 'optics') {
                        $general_total->push(
                            collect([
                                "order" => 3,
                                "description"  => "VENTAS INTERNAS GRAVADAS CONSUMIDOR TICKET ". strtoupper($l),
                                "excluded_tax"   => $total_ticket_exc_tax,
                                "taxes"   => $total_ticket_tax,
                                "final_total" => $total_ticket
                            ])
                        );
                    }

                    $total_exports_exc_tax = $total_exports;
                    $total_exports_tax = 0;

                    if (config('app.business') != 'optics') {
                        $general_total->push(
                            collect([
                                "order" => 4,
                                "description"  => "EXPORTACIONES SEGÚN FACTURAS ". strtoupper($l),
                                "excluded_tax"   => $total_exports_exc_tax,
                                "taxes"   => $total_exports_tax,
                                "final_total" => $total_exports
                            ])
                        );
                    }
                @endphp
                <tbody>
                    <tr>
                        <td class="no-bb no-bl no-bt">{{ mb_strtoupper(__('accounting.consumer_taxed_domestic_sales')) }}</td>
                        <td class="text-right">$ {{ $total_fcf ? number_format($total_fcf_exc_tax, 2) : "&nbsp;0" }}</td>
                        <td class="text-right">$ {{ $total_fcf ? number_format($total_fcf_tax, 2) : "&nbsp;0" }} </td>
                        <td></td>
                        <td></td>
                        <th class="text-right">$ {{ $total_fcf ? number_format($total_fcf, 2) : "&nbsp;0" }}</th>
                    </tr>
                    <tr>
                        <td class="no-bb no-bl no-bt">
                            {{ mb_strtoupper(__('accounting.domestic_sales_exempt_consumer')) }}
                        </td>
                        <td class="text-right">$&nbsp;0</td>
                        <td class="text-right">$&nbsp;0</td>
                        <td></td>
                        <td></td>
                        <td class="text-right">$&nbsp;0</td>
                    </tr>
                    @if (config('app.business') != 'optics')
                        <tr>
                            <td class="no-bb no-bl no-bt">VENTAS INTERNAS GRAVADAS CONSUMIDOR TICKET</td>
                            <td class="text-right">$ {{ $total_ticket ? number_format($total_ticket_exc_tax, 2) : "&nbsp;0" }}</td>
                            <td class="text-right">$ {{ $total_ticket ? number_format($total_ticket_tax, 2) : "&nbsp;0" }}</td>
                            <td></td>
                            <td></td>
                            <th class="text-right">$ {{ $total_ticket ? number_format($total_ticket, 2) : "&nbsp;0" }}</th>
                        </tr>
                    @endif
                    <tr>
                        <td class="no-bb no-bl no-bt">{{ mb_strtoupper(__('accounting.exports_according_to_invoices')) }}</td>
                        <td class="text-right">$ {{ $total_exports ? number_format($total_exports_exc_tax, 2) : "&nbsp;0" }}</td>
                        <td class="text-right">$ {{ $total_exports ? number_format($total_exports_tax, 2) : "&nbsp;0" }}</td>
                        <td></td>
                        <td></td>
                        <th class="text-right">$ {{ $total_exports ? number_format($total_exports, 2) : "&nbsp;0" }}</th>
                    </tr>
                    <tr>
                        <th class="text-left no-bb no-bl no-bt">TOTALES</td>
                        <th class="text-right">$ {{ number_format(($total_fcf_exc_tax + $total_ticket_exc_tax + $total_exports_exc_tax), 2) }}</th>
                        <th class="text-right">$ {{ number_format(($total_fcf_tax + $total_ticket_tax + $total_exports_tax), 2) }}</th>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <th class="text-right">$ {{ number_format($total_fcf + $total_ticket + $total_exports, 2) }}</th>
                    </tr>
                </tbody>
            </table>
        @endforeach
        @php
            $total_excluded_tax = 0;
            $total_taxes = 0;
            $total_final_total = 0;
        @endphp
        @if ($location == 0)
            <table class="summary-report">
                <thead>
                    <tr>
                        <th rowspan="2" class="no-bt no-bl no-bb" style="width: 50%;">TOTAL GENERAL</th>
                        <th colspan="2" class="text-center"><strong>{{ mb_strtoupper(__('accounting.own')) }}</strong></th>
                        <th colspan="2" class="text-center"><strong>{{ mb_strtoupper(__('accounting.third_party_account')) }}</strong></th>
                        <th rowspan="2" class="text-center">TOTAL</th>
                    </tr>
                    <tr>
                        <th class="text-center"><strong>{{ mb_strtoupper(__('accounting.net_worth')) }}</strong></th>
                        <th class="text-center"><strong>{{ mb_strtoupper(__('accounting.fiscal_debit')) }}</strong></th>
                        <th class="text-center"><strong>{{ mb_strtoupper(__('accounting.net_worth')) }}</strong></th>
                        <th class="text-center"><strong>{{ mb_strtoupper(__('accounting.fiscal_debit')) }}</strong></th>
                    </tr>
                </thead>
                @php
                    $general_total = $general_total->sortBy('order');
                @endphp
                <tbody>
                    @foreach($general_total as $gt)
                        @php
                            $total_excluded_tax += $gt["excluded_tax"];
                            $total_taxes += $gt["taxes"];
                            $total_final_total += $gt["final_total"];
                        @endphp
                        <tr>
                            <td class="no-bb no-bl no-bt">{{ $gt["description"] }}</td>
                            <td class="text-right">$ {{ $gt["excluded_tax"] ? number_format($gt["excluded_tax"], 2) : "&nbsp;0" }}</td>
                            <td class="text-right">$ {{ $gt["taxes"] ? number_format($gt["taxes"], 2) : "&nbsp;0" }} </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <th class="text-right">$ {{ $gt["final_total"] ? number_format($gt["final_total"], 2) : "&nbsp;0" }}</th>
                        </tr>
                    @endforeach
                    <tr>
                        <th class="text-left no-bb no-bl no-bt">TOTALES</td>
                        <th class="text-right">$ {{ number_format($total_excluded_tax, 2) }}</th>
                        <th class="text-right">$ {{ number_format($total_taxes, 2) }}</th>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <th class="text-right">$ {{ number_format($total_final_total, 2) }}</th>
                    </tr>
                </tbody>
            </table>
        @endif
        <br><br>
        <table style="width: 5cm;">
            <tr>
                <td style="width: 0.2cm;">F.</td>
                <td style="border-bottom: 1px solid #000;"></td>
            </tr>
            <tr>
                <td></td>
                <td class="text-center">Contador</td>
            </tr>
        </table>
    </div>
    <div id="footer">
        <div class="page-number"></div>
    </div>
</body>
</html>
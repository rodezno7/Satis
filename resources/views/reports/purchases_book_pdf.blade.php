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
                <strong>{{ mb_strtoupper(__('accounting.purchases_book')) }}</strong>
            </td>
        </tr>
    </table>
    <br>

    <table class="table1" style="width: 100%;">
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
                <strong>{{ mb_strtoupper(__('business.nrc')) }}:</strong>
                {{ $business->nrc }}
            </td>
            <td class="td2" style="width: 25%">
                <strong>{{ mb_strtoupper(__('business.nit')) }}:</strong>
                {{ $business->nit }}
            </td>
        </tr>
    </table>
    <br>

    <table class="table2" style=" width: 100%;">
        <thead>
            <tr>
                <th rowspan="2">{{ mb_strtoupper(__('accounting.no_tag')) }}</th>
                <th rowspan="2">{{ mb_strtoupper(__('accounting.date_of_issue')) }}</th>
                <th rowspan="2" style="width: 5%;">{{ mb_strtoupper(__('accounting.document_no')) }}</th>
                <th rowspan="2">{{ mb_strtoupper(__('business.nrc')) }}</th>
                <th rowspan="2">{{ mb_strtoupper(__('accounting.dui_excluded_subject')) }}</th>
                <th rowspan="2">{{ mb_strtoupper(__('business.nit')) }}</th>
                <th rowspan="2" style="width: 15%;">{{ mb_strtoupper(__('accounting.supplier_name')) }}</th>
                <th colspan="2">{{ mb_strtoupper(__('accounting.exempt_purchases')) }}</th>
                <th colspan="3">{{ mb_strtoupper(__('accounting.taxed_purchases')) }}</th>
                <th rowspan="2">{{ mb_strtoupper(__('accounting.total_purchases')) }}</th>
                <th rowspan="2">{{ mb_strtoupper(__('accounting.iva_withheld_third_parties')) }}</th>
                <th rowspan="2">{{ mb_strtoupper(__('accounting.purchases_excluded_subjects')) }}</th>
            </tr>
            <tr>
                <th>{{ mb_strtoupper(__('accounting.local_interns')) }}</th>
                <th>{{ mb_strtoupper(__('accounting.imports_or_internationals')) }}</th>
                <th>{{ mb_strtoupper(__('accounting.local_interns')) }}</th>
                <th>{{ mb_strtoupper(__('accounting.imports_or_internationals')) }}</th>
                <th>{{ mb_strtoupper(__('document_type.fiscal_credit')) }}</th>
            </tr>
        </thead>
        <tbody>
            @php
            $total_internal = 0;
            $total_imports = 0;
            $total_fiscal_credit = 0;
            $total_purchases = 0;
            $total_withheld = 0;
            $total_internal_exempt = 0;
            $total_imports_exempt = 0;
            $total_excluded_subject = 0;
            @endphp
            @foreach ($lines as $item)
            <tr>
                <td class="alnright">{{ $loop->iteration }}</td>
                <td>{{ $item->transaction_date }}</td>
                <td class="alnright">{{ $item->correlative }}</td>
                <td>{{ $item->nrc }}</td>
                <td>{{ $item->dui }}</td>
                <td>{{ $item->nit }}</td>
                <td>{{ $item->supplier }}</td>
                <td class="alnright">
                    @if ($item->internal_exempt)
                    $ {{ number_format($item->internal_exempt, 2) }}
                    @endif
                </td>
                <td class="alnright">
                    @if ($item->imports_exempt)
                    $ {{ number_format($item->imports_exempt, 2) }}
                    @endif
                </td>
                <td class="alnright">
                    @if ($item->internal)
                    $ {{ number_format($item->internal, 2) }}
                    @endif
                </td>
                <td class="alnright">
                    @if ($item->imports)
                    $ {{ number_format($item->imports, 2) }}
                    @endif
                </td>
                <td class="alnright">
                    @if ($item->organization_type != 'natural')
                    $ {{ number_format($item->fiscal_credit, 2) }}
                    @endif
                </td>
                <td class="alnright">
                    @if ($item->organization_type != 'natural')
                    $ {{ number_format($item->total_purchases, 2) }}
                    @endif
                </td>
                <td class="alnright">
                    @if ($item->organization_type != 'natural')
                    $ {{ $item->withheld_amount ? number_format($item->withheld_amount, 2) : "" }}
                    @endif
                </td>
                <td class="alnright">
                    @if ($item->organization_type == 'natural')
                    $ {{ number_format($item->total_purchases, 2) }}
                    @endif
                </td>
            </tr>
            @php
            $total_internal += $item->internal;
            $total_imports += $item->imports;
            $total_fiscal_credit += $item->fiscal_credit;
            if($item->organization_type != 'natural'){
                $total_purchases += $item->total_purchases;
            }else{
                $total_excluded_subject += $item->total_purchases;
            }
            $total_withheld += $item->withheld_amount;
            $total_internal_exempt += $item->internal_exempt;
            $total_imports_exempt += $item->imports_exempt;
            @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="alnright">
                    <strong>{{ mb_strtoupper(__('accounting.totals')) }}</strong>
                </td>
                <td class="alnright">
                    @if ($total_internal_exempt > 0)
                    <strong>{{ number_format($total_internal_exempt, 2) }}</strong>
                    @endif
                </td>
                <td class="alnright">
                    @if ($total_imports_exempt > 0)
                    <strong>{{ number_format($total_imports_exempt, 2) }}</strong>
                    @endif
                </td>
                <td class="alnright">
                    @if ($total_internal > 0)
                    <strong>{{ number_format($total_internal, 2) }}</strong>
                    @endif
                </td>
                <td class="alnright">
                    @if ($total_imports > 0)
                    <strong>{{ number_format($total_imports, 2) }}</strong>
                    @endif
                </td>
                <td class="alnright">
                    <strong>{{ number_format($total_fiscal_credit, 2) }}</strong>
                </td>
                <td class="alnright">
                    <strong>{{ number_format($total_purchases, 2) }}</strong>
                </td>
                <td class="alnright">
                    <strong>{{ number_format($total_withheld, 2) }}</strong>
                </td>
                <td class="alnright">
                    <strong>{{ number_format($total_excluded_subject, 2) }}</strong>
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
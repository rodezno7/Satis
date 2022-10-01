<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('quote.lost_sale_report')</title>
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
            border-collapse: collapse;
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
                <strong>{{ mb_strtoupper($business->line_of_business) }}</strong>
            </td>
        </tr>
        <tr>
            <td class="td2 alncenter">
                <strong>{{ mb_strtoupper(__('quote.lost_sale_report')) }}</strong>
            </td>
        </tr>
    </table>
    <br>

    <table class="table1" style="width: 100%;">
        <thead>
            <tr>
                <th class="td2 bb">@lang('messages.date')</th>
                <th class="td2 bb">@lang('quote.due_date')</th>
                <th class="td2 bb">@lang('quote.lost_date')</th>
                <th class="td2 bb">@lang('order.ref_no')</th>
                <th class="td2 bb">@lang('Documento')</th>
                <th class="td2 bb">@lang('quote.reason')</th>
                <th class="td2 bb">@lang('customer.customer_code')</th>
                <th class="td2 bb">@lang('sale.customer_name')</th>
                <th class="td2 bb">@lang('quote.seller')</th>
                <th class="td2 bb">@lang('report.quoted_amount')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quotes as $item)
            <tr>
                <td class="alncenter td2">
                    {{ @format_date($item->quote_date) }}
                </td>
                <td class="alncenter td2">
                    {{ @format_date($item->due_date) }}
                </td>
                <td class="alncenter td2">
                    {{ @format_date($item->lost_date) }}
                </td>
                <td class="alncenter td2">
                    {{ $item->ref_no }}
                </td>
                <td class="alncenter td2">
                    {{ $item->document }}
                </td>
                <td class="alncenter td2">
                    {{ $item->reason }}
                </td>
                <td class="alncenter td2">
                    {{ $item->customer_id }}
                </td>
                <td class="alncenter td2">
                    {{ $item->customer_name }}
                </td>
                <td class="alncenter td2">
                    {{ $item->seller_name }}
                </td>
                <td class="alncenter td2">
                    <span class="display_currency" data-currency_symbol="true">$ {{ @num_format($item->total_final) }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
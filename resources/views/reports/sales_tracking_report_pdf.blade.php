<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('report.sales_tracking_report')</title>
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
                <strong>{{ mb_strtoupper(__('report.sales_tracking_report')) }}</strong>
            </td>
        </tr>
    </table>
    <br>

    <table class="table1" style="width: 100%;">
        <thead>
            <tr>
                <th class="td2 bb">{{ __('order.ref_no') }}</th>
                <th class="td2 bb">{{ __('messages.date') }}</th>
                <th class="td2 bb">{{ __('customer.customer_code') }}</th>
                <th class="td2 bb">{{ __('sale.customer_name') }}</th>
                <th class="td2 bb">{{ __('order.delivery_type') }}</th>
                <th class="td2 bb">{{ __('quote.invoiced') }}</th>
                <th class="td2 bb">{{ __('report.quoted_amount') }}</th>
                <th class="td2 bb">{{ __('report.invoiced_amount') }}</th>
                <th class="td2 bb">{{ __('quote.seller') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $item)
            <tr>
                <td class="alncenter td2">
                    {{ $item->code }}
                </td>
                <td class="alncenter td2">
                    {{ @format_date($item->quote_date) }}
                </td>
                <td class="td2 alncenter">
                    {{ $item->customer_id }}
                </td>
                <td class="td2">
                    {{ $item->customer }}
                </td>
                <td class="alncenter td2">
                    {{ __('order.' . $item->delivery_type) }}
                </td>
                <td class="alncenter td2">
                    {{ __('messages.' . $item->invoiced) }}
                </td>
                <td class="alncenter td2">
                    <span class="display_currency" data-currency_symbol="true">$ {{ @num_format($item->quoted_amount) }}</span>
                </td>
                <td class="alncenter td2">
                    <span class="display_currency" data-currency_symbol="true">$ {{ @num_format($item->invoiced_amount) }}</span>
                </td>
                <td class="td2">
                    {{ $item->seller }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
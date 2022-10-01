<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('report.lab_orders_report')</title>
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
                <strong>{{ mb_strtoupper(__('report.lab_orders_report')) }}</strong>
            </td>
        </tr>
    </table>
    <br>

    <table class="table1" style="width: 100%;">
        <thead>
            <tr>
                <th class="td2 bb">{{ __('lab_order.no_order') }}</th>
                <th class="td2 bb">{{ __('document_type.document') }}</th>
                <th class="td2 bb">{{ __('accounting.location') }}</th>
                <th class="td2 bb">{{ __('contact.customer') }}</th>
                <th class="td2 bb">{{ __('graduation_card.patient') }}</th>
                <th class="td2 bb">{{ __('accounting.status') }}</th>
                <th class="td2 bb">{{ __('business.register') }}</th>
                <th class="td2 bb">{{ __('lab_order.delivery') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
            $util = new \App\Utils\Util;
            @endphp
            @foreach ($lab_orders as $item)
            <tr>
                <td class="alncenter td2">
                    {{ $item->no_order }}
                </td>
                <td class="alncenter td2">
                    {{ $item->correlative }}
                    <br>
                    <small>{{ $item->document }}</small>
                </td>
                <td class="alncenter td2">
                    {{ $item->location }}
                </td>
                <td class="td2">
                    {{ $item->customer }}
                </td>
                <td class="td2">
                    {{ $item->patient }}
                </td>
                <td class="alncenter td2">
                    {{ $item->status }}
                </td>
                <td class="alncenter td2">
                    {{ $util->format_date($item->created_at, true) }}
                </td>
                <td class="alncenter td2">
                    {{ $util->format_date($item->delivery, true) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
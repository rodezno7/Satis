<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('report.payment_notes_report')</title>
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

<body onload="window.print()">
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
                <strong>{{ mb_strtoupper(__('report.all_sales_report')) }}</strong>
            </td>
        </tr>
    </table>
    <br>

    <table class="table1" style="width: 100%;">
        <thead>
            <tr>
                <th class="td2 bb">{{ __('messages.date') }}</th>
                <th class="td2 bb">{{ __('lang_v1.payment_note') }}</th>
                <th class="td2 bb">{{ __('contact.customer') }}</th>
                <th class="td2 bb">{{ __('inflow_outflow.document_no') }}</th>
                <th class="td2 bb">{{ __('document_type.title') }}</th>
                <th class="td2 bb">{{ __('accounting.amount') }}</th>
                <th class="td2 bb">{{ __('accounting.balance') }}</th>
                <th class="td2 bb">{{ __('accounting.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
            $total_amount = 0;
            @endphp
            @foreach ($payments as $item)
            <tr>
                <td class="alncenter td2">
                    {{ $item->paid_on }}
                </td>
                <td class="alncenter td2">
                    {{ $item->note }}
                </td>
                <td class="td2">
                    {{ $item->customer_name }}
                </td>
                <td class="alncenter td2">
                    {{ $item->correlative }}
                </td>
                <td class="alncenter td2">
                    {{ $item->document_name }}
                </td>
                <td class="alncenter td2">
                    {{ @num_format($item->amount) }}
                </td>
                <td class="alncenter td2">
                    {{ @num_format($item->balance) }}
                </td>
                <td class="alncenter td2">
                    @php
                        $status = 'partial';
                        if ($item->balance == 0) {
                            $status = 'paid';
                        } else if ($item->balance == $item->final_total) {
                            $status = 'due';
                        }
                    @endphp
                    {{ __('lang_v1.' . $status) }}
                </td>
            </tr>
            @php
            $total_amount += $item->amount;
            @endphp
            @endforeach
        </tbody>
        <tr>
            <td colspan="5" class="alncenter td2 bt">
                <strong>{{ __('accounting.totals') }}</strong>
            </td>
            <td class="alncenter td2 bt">
                <strong>{{ @num_format($total_amount) }}</strong>
            </td>
            <td class="td2 bt"></td>
            <td class="td2 bt"></td>
        </tr>
    </table>
</body>
</html>
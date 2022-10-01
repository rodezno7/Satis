<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('report.warehouse_closure_report')</title>
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
        .bt { border-top: 1px solid black; }
        .bb { border-bottom: 1px solid black; }
        .br { border-right: 0.25px solid black; }
        .bl { border-left: 0.25px solid black; }
        .no-bt { border-top: 0.25px solid white; }
        .no-bb { border-bottom: 0.25px solid white; }
        .no-br { border-right: 0.25px solid white; }
        .no-bl { border-left: 0.25px solid white; }
        .dbb { border-bottom: 1px double black; }
  </style>   
</head>

<body>
    <div id="footer">
        <div class="page-number"></div>
    </div>

    {{-- Header --}}
    <table class="table1" style="width: 100%;">
        <tr>
            <td class="td2 alncenter">
                <strong>{{ mb_strtoupper($business->business_full_name) }}</strong>
                <br>
                {{ mb_strtoupper(__('report.range_cost_of_sale_detail', ['from' => @format_date($start), 'to' => @format_date($end)])) }}
            </td>
        </tr>
    </table>
    <br>

    {{-- Table --}}
    <table class="table1" style=" width: 100%;">
        <thead>
            <tr>
                <th class="td2 alnleft bb" style="width: 7%">{{ mb_strtoupper(__('accounting.date')) }}</th>
                <th class="td2 alnleft bb" style="width: 7%">{{ mb_strtoupper(__('accounting.code')) }}</th>
                <th class="td2 alnleft bb">{{ mb_strtoupper(__('accounting.description')) }}</th>
                <th class="td2 alnleft bb">{{ mb_strtoupper(__('credit.observations')) }}</th>
                <th class="td2 alnleft bb" style="width: 5%">{{ mb_strtoupper(__('accounting.type')) }}</th>
                <th class="td2 alnleft bb" style="width: 9%">{{ mb_strtoupper(__('accounting.reference')) }}</th>
                <th class="td2 alnleft bb" style="width: 7%">{{ mb_strtoupper(__('accounting.inflow')) }}</th>
                <th class="td2 alnleft bb" style="width: 7%">{{ mb_strtoupper(__('accounting.outflow')) }}</th>
                <th class="td2 alnleft bb" style="width: 7%">{{ mb_strtoupper(__('accounting.annulled')) }}</th>
            </tr>
        </thead>
        
        <tbody>
            @php
            $total_input = 0;
            $total_output = 0;
            $total_annulled = 0;
            @endphp
            @foreach ($query as $item)
            <tr>
                <td class="td2">{{ $item->transaction_date }}</td>
                <td class="td2">{{ $item->code }}</td>
                <td class="td2">{{ $item->description }}</td>
                <td class="td2">{{ $item->observation }}</td>
                <td class="td2">{{ $item->document_type }}</td>
                <td class="td2">{{ $item->reference }}</td>
                <td class="alnright td2">
                    {{ @num_format($item->input) }}
                </td>
                <td class="alnright td2">
                    {{ @num_format($item->output) }}
                </td>
                <td class="alnright td2">
                    {{ @num_format($item->annulled) }}
                </td>
            </tr>
            @php
            $total_input += $item->input;
            $total_output += $item->output;
            $total_annulled += $item->annulled;
            @endphp
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="6" class="td2 alnright">&nbsp;</td>
                <td class="td2 alnright bt dbb">
                    <strong>{{ @num_format($total_input) }}</strong>
                </td>
                <td class="td2 alnright bt dbb">
                    <strong>{{ @num_format($total_output) }}</strong>
                </td>
                <td class="td2 alnright bt dbb">
                    <strong>{{ @num_format($total_annulled) }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('kardex.kardex')</title>
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
            <td class="td2">
                <center><strong>{{ mb_strtoupper($business->business_full_name) }}</strong></center>
                <center>
                    @if (is_null($start) || is_null($end))
                    {{ mb_strtoupper(__('kardex.kardex')) }}
                    @else
                    {{ mb_strtoupper(__('kardex.kardex_detail', ['from' => @format_date($start), 'to' => @format_date($end)])) }}
                    @endif
                </center>
            </td>
        </tr>
    </table>
    <br>

    <table class="table1" style="width: 100%;">
        <tr>
            <td class="td2">
                <strong>{{ mb_strtoupper(__('messages.location')) }}:</strong> {{ $warehouse->name }}
                <span style="float: right">
                    <strong>{{ mb_strtoupper(__('business.product')) }}:</strong> {{ $variation->product->name }} ({{ $variation->sub_sku }})
                </span>
            </td>
        </tr>
    </table>
    <br>

    {{-- Table --}}
    <table class="table1" style=" width: 100%;">
        <thead>
            <tr>
                <th class="td2 bb">{{ mb_strtoupper(__('kardex.date')) }}</th>
                <th class="td2 bb">{{ mb_strtoupper(__('kardex.transaction')) }}</th>
                <th class="td2 bb">{{ mb_strtoupper(__('kardex.type')) }}</th>
                <th class="td2 bb">{{ mb_strtoupper(__('kardex.reference')) }}</th>
                <th class="td2 bb">{{ mb_strtoupper(__('kardex.initial_stock')) }}</th>
                <th class="td2 bb">{{ mb_strtoupper(__('kardex.input')) }}</th>
                <th class="td2 bb">{{ mb_strtoupper(__('kardex.output')) }}</th>
                <th class="td2 bb">{{ mb_strtoupper(__('kardex.final_stock')) }}</th>
                @if (auth()->user()->can('kardex.view_costs'))
                    <th class="td2 bb">{{ mb_strtoupper(__('kardex.input_cost')) }}</th>
                    <th class="td2 bb">{{ mb_strtoupper(__('kardex.output_cost')) }}</th>
                    <th class="td2 bb">{{ mb_strtoupper(__('kardex.balance')) }}</th>
                @endif
            </tr>
        </thead>
        
        <tbody>
            @foreach ($kardex as $item)
            <tr>
                <td class="td2">{{ $item->date_time }}</td>
                <td class="td2">{{ __("movement_type." . $item->movement_type) }}</td>
                <td class="td2">{{ __("movement_type." . $item->type) }}</td>
                <td class="td2">{{ $item->reference }}</td>
                <td class="alnright td2">{{ $item->initial_stock }}</td>
                <td class="alnright td2">{{ $item->inputs_quantity }}</td>
                <td class="alnright td2">{{ $item->outputs_quantity }}</td>
                <td class="alnright td2">{{ $item->balance }}</td>
                @if (auth()->user()->can('kardex.view_costs'))
                    <td class="alnright td2">{{ $item->total_cost_inputs }}</td>
                    <td class="alnright td2">{{ $item->total_cost_outputs }}</td>
                    <td class="alnright td2">{{ $item->balance_cost }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
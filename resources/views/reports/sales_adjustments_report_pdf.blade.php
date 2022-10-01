<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('report.consumption_report')</title>
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

    {{-- Header --}}
    <table class="table1" style="width: 100%;">
        <tr>
            <td class="td2">
                <center><strong>{{ mb_strtoupper($business->line_of_business) }}</strong></center>
                <center><strong>{{ mb_strtoupper(__('report.consumption_report')) }}</strong></center>
            </td>
        </tr>
    </table>
    <br>

    <table class="table1" style="width: 100%;">
        <tr>
            <td class="td2">
                <strong>{{ mb_strtoupper(__('accounting.location')) }}:</strong> {{ $location->name }}
                <span style="float: right">
                    <strong>{{ mb_strtoupper(__('accounting.month')) }}:</strong> {{ $month_name }}
                </span>
            </td>
        </tr>
    </table>
    <br>

    {{-- Table --}}
    <table class="table2" style=" width: 100%;">
        <thead>
            <tr>
                <th>SKU</th>
                <th>@lang('business.product')</th>
                <th>@lang('sale.unit_price')</th>
                <th>@lang('report.unit_cost')</th>
                <th>@lang('report.total_unit_sold')</th>
                <th>@lang('report.input_adjustment')</th>
                <th>@lang('report.output_adjustment')</th>
            </tr>
        </thead>
        
        <tbody>
            @php
            $total_total_sold = [];
            $total_input_adjustment = [];
            $total_output_adjustment = [];
            @endphp
            @foreach ($query as $item)
            <tr>
                <td>{{ $item->sku }}</td>
                <td>{{ $item->product }}</td>
                <td class="alnright">
                    $ {{ @num_format($item->unit_price) }}
                </td>
                <td class="alnright">
                    $ {{ @num_format($item->unit_cost) }}
                </td>
                <td class="alnright">
                    {{ @num_format($item->total_sold) }}
                </td>
                <td class="alnright">
                    {{ @num_format($item->input_adjustment) }}
                </td>
                <td class="alnright">
                    {{ @num_format($item->output_adjustment) }}
                </td>
            </tr>
            @php
            if (empty($item->unit)) {
                $item->unit = 'none';
            }

            if (! empty($item->total_sold)) {
                if (! array_key_exists($item->unit, $total_total_sold)) {
                    $total_total_sold[$item->unit] = $item->total_sold;
                } else {
                    $total_total_sold[$item->unit] += $item->total_sold;
                }
            }

            if (! empty($item->input_adjustment)) {
                if (! array_key_exists($item->unit, $total_input_adjustment)) {
                    $total_input_adjustment[$item->unit] = $item->input_adjustment;
                } else {
                    $total_input_adjustment[$item->unit] += $item->input_adjustment;
                }
            }

            if (! empty($item->output_adjustment)) {
                if (! array_key_exists($item->unit, $total_output_adjustment)) {
                    $total_output_adjustment[$item->unit] = $item->output_adjustment;
                } else {
                    $total_output_adjustment[$item->unit] += $item->output_adjustment;
                }
            }
            @endphp
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="4" class="alncenter">
                    <strong>{{ mb_strtoupper(__('sale.total')) }}</strong>
                </td>
                <td class="alnright">
                    @foreach ($total_total_sold as $unit => $quantity)
                    {{ @num_format($quantity) }}
                    @if ($unit != 'none')
                    {{ $unit }}
                    @endif
                    <br>
                    @endforeach
                </td>
                <td class="alnright">
                    @foreach ($total_input_adjustment as $unit => $quantity)
                    {{ @num_format($quantity) }}
                    @if ($unit != 'none')
                    {{ $unit }}
                    @endif
                    <br>
                    @endforeach
                </td>
                <td class="alnright">
                    @foreach ($total_output_adjustment as $unit => $quantity)
                    {{ @num_format($quantity) }}
                    @if ($unit != 'none')
                    {{ $unit }}
                    @endif
                    <br>
                    @endforeach
                </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
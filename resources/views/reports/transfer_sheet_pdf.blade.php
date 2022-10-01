<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('lab_order.transfers_sheet')</title>
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
    {{-- <div id="footer">
        <div class="page-number"></div>
    </div> --}}

    <table class="table1" style="width: 100%;">
        <tr>
            <td class="td2 alncenter">
                <strong>{{ mb_strtoupper($business->business_full_name) }}</strong>
            </td>
        </tr>
        <tr>
            <td class="td2 alncenter">
                <strong>{{ mb_strtoupper(__('lab_order.transfers_sheet')) }}</strong>
            </td>
        </tr>
    </table>
    <br>

    <table class="table2" style="width: 100%;">
        <thead>
            <tr>
                <th>No.</th>
                <th>{{ __('accounting.location') }}</th>
                <th>{{ __('payment.transfer_ref_no') }}</th>
                <th>{{ __('lang_v1.quantity') }}</th>
                <th>{{ __('accounting.description') }}</th>
                @if ($enable_signature_column == 1)
                <th colspan="2">{{ __('report.received') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($lines as $line)
            @php
            $correlative = $loop->iteration;
            $index_reference = 0;
            $counts = array_count_values(array_column($line->toArray(), 'reference'));
            @endphp
            @foreach ($line as $key => $item)
            <tr>
                @if ($key == 0 || $key % $line->count() == 0)
                <td class="alncenter" rowspan="{{ $line->count() }}">
                    {{ $correlative }}
                </td>

                <td class="alncenter" rowspan="{{ $line->count() }}">
                    {{ $item->location }}
                </td>
                @endif

                @if ($index_reference % $counts[$item->reference] == 0)
                <td class="alncenter" rowspan="{{ $counts[$item->reference] }}">
                    {{ $item->reference }}
                </td>
                @endif
                
                <td class="alncenter">
                    {{ @num_format($item->quantity) }}
                </td>
                
                <td>
                    {{ $item->description }}
                </td>
                
                @if ($enable_signature_column == 1)    
                <td></td>
                <td></td>
                @endif
            </tr>
            @php
            $index_reference++;

            if ($index_reference % $counts[$item->reference] == 0) {
                $index_reference = 0;
            }
            @endphp
            @endforeach
            @endforeach
        </tbody>
    </table>

    <br>
    <br>
    <br>

    <table class="table1" style="width: 100%;">
        <tr>
            <td class="td2 alncenter">
                _____________________________________
                <br>
                @if (! empty($delivers))
                {{ $delivers }}
                <br>
                @endif
                {{ __('report.delivers')  }}
            </td>
            <td class="td2 alncenter">
                _____________________________________
                <br>
                @if (! empty($receives))
                {{ $receives }}
                <br>
                @endif
                {{ __('report.receives')  }}
            </td>
        </tr>
    </table>
</body>
</html>
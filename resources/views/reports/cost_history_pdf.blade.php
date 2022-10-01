<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('report.history_purchase')</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000000;
            font-size: 7pt;
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
            border: 0px;
        }

        .table2 thead tr th,
        .table2 thead tr td,
        .table2 tbody tr th,
        .table2 tbody tr td,
        .table2 tfoot tr th,
        .table2 tfoot tr td {
            border: 0px;
            font-size: 6pt;
        }

        .td2 {
            border: 0px;
        }

        td {
            border: 0.25px solid black;
            padding: 4px;
            text-align: left;
            vertical-align: top;
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
    {{-- Footer --}}
    <div id="footer">
        <table class="table2" style="width: 100%;">
            <tbody>
                <tr>
                    <td style="width: 50%;">{{ $business->business_full_name }}</td>
                    <td style="text-align: right; width: 50%;" class="page-number"></td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Header --}}
    <table class="table1" style="width: 100%;">
        <tr>
            <td class="td2 alncenter" style="font-size: 10pt;">
                <strong>{{ mb_strtoupper($business->business_full_name) }}</strong>
            </td>
        </tr>
        <tr>
            <td class="td2 alncenter" style="font-size: 8pt;">
                <strong>{{ mb_strtoupper(__('product.cost_history')) }}</strong>
            </td>
        </tr>
        <tr>
            @php
            $name = $product->name;

            if ($product->sku != $variation->sub_sku) {
                $name .= ' - ' . $variation->name;
            }

            $name .= ' (' . $variation->sub_sku . ')';
            @endphp
            <td class="td2 alncenter">
                {{ mb_strtoupper(__('product.product')) }}: {{ $name }}
            </td>
        </tr>
    </table>
    <br>

    {{-- Table --}}
    <table class="table1" style=" width: 100%;">
        <thead>
            <tr>
                <th class="td2 alncenter bb">{{ mb_strtoupper(__('accounting.date')) }}</th>
                <th class="td2 alncenter bb">{{ mb_strtoupper(__('accounting.reference')) }}</th>
                <th class="td2 alncenter bb" style="width: 35%">{{ mb_strtoupper(__('purchase.supplier')) }}</th>
                <th class="td2 alncenter bb">{{ mb_strtoupper(__('lang_v1.quantity')) }}</th>
                <th class="td2 alncenter bb">{{ mb_strtoupper(__('product.unit_cost')) }}</th>
                <th class="td2 alncenter bb">{{ mb_strtoupper(__('lang_v1.stock')) }}</th>
                <th class="td2 alncenter bb">{{ mb_strtoupper(__('product.average_cost')) }}</th>
            </tr>
        </thead>

        @php
        $precision_quantities = 0;
        $precision_amounts = 6;
        @endphp
        
        <tbody>
            @foreach ($lines as $item)
            <tr>
                <td class="td2 alncenter">
                    {{ @format_date($item['date']) }}
                </td>

                <td class="td2 alncenter">
                    @if (is_null($item['reference']))
                    -
                    @else
                    {{ $item['reference'] }}
                    @endif
                </td>

                <td class="td2">
                    @if (is_null($item['supplier']))
                    -
                    @else
                    {{ $item['supplier'] }}
                    @endif
                </td>

                <td class="td2 alnright">
                    {{ number_format($item['quantity'], $precision_quantities) }}
                </td>

                <td class="td2 alnright">
                    {{ number_format($item['unit_cost'], $precision_amounts) }}
                </td>

                <td class="td2 alnright">
                    {{ number_format($item['stock'], $precision_quantities) }}
                </td>

                <td class="td2 alnright">
                    {{ number_format($item['avg_cost'], $precision_amounts) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
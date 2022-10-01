<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('report.stock_report')</title>
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
            <td class="td2 alncenter" style="font-size: 14pt;">
                <strong>{{ mb_strtoupper($business->business_full_name) }}</strong>
            </td>
        </tr>
        <tr>
            <td class="td2 alncenter" style="font-size: 10pt;">
                <strong>{{ mb_strtoupper(__('report.stock_report')) }}</strong>
            </td>
        </tr>
        <tr>
            <td class="td2 alncenter">
                {{ mb_strtoupper(__('report.date_range_report', ['from' => @format_date($start), 'to' => @format_date($end)])) }}
            </td>
        </tr>
        <tr>
            <td class="td2 alncenter">
                {{ mb_strtoupper(__('report.amounts_in', ['currency' => __('report.' . $business->currency->currency), 'code' => $business->currency->code])) }}
            </td>
        </tr>
    </table>
    <br>

    <table class="table1" style="width: 100%;">
        <thead>
            <tr>
                <th class="td2 bb">SKU</th>
                <th class="td2 bb">{{ mb_strtoupper(__('business.product')) }}</th>
                <th class="td2 bb">{{ mb_strtoupper(__('category.category')) }}</th>
                <th class="td2 bb">{{ mb_strtoupper(__('product.sub_category')) }}</th>
                <th class="td2 bb">{{ mb_strtoupper(__('brand.brand')) }}</th>
                <th class="td2 bb">{{ mb_strtoupper(__('report.unit_cost')) }}</th>
                <th class="td2 bb">{{ mb_strtoupper(__('sale.unit_price')) }}</th>
                <th class="td2 bb">{{ mb_strtoupper(__('report.current_stock')) }}</th>
                <th class="td2 bb">{{ mb_strtoupper(__('report.vld_stock')) }}</th>
                <th class="td2 bb">{{ mb_strtoupper(__('report.value_total')) }}</th>
                <th class="td2 bb">{{ mb_strtoupper(__('report.total_unit_sold')) }}</th>
            </tr>
        </thead>
        <tbody>
            @php
            $total_stock = 0;
            $total_vld_stock = 0;
            $total_sold = 0;
            $total_value = 0;
            @endphp
            @foreach ($products as $item)
            @php
            $value = $item->stock * $item->unit_cost;
            @endphp
            <tr>
                <td class="td2">{{ $item->sku }}</td>
                <td class="td2">{{ $item->product }}</td>
                <td class="td2">{{ $item->category }}</td>
                <td class="td2">{{ $item->sub_category }}</td>
                <td class="td2">{{ $item->brand }}</td>
                <td class="alncenter td2">{{ @num_format($item->unit_cost) }}</td>
                <td class="alncenter td2">{{ @num_format($item->unit_price) }}</td>
                <td class="alncenter td2">
                    @if ($product_settings['show_stock_without_decimals'])
                    {{ number_format($item->stock, 0) }}
                    @else
                    {{ @num_format($item->stock) }}
                    @endif
                </td>
                <td class="alncenter td2">
                    @if ($product_settings['show_stock_without_decimals'])
                    {{ number_format($item->vld_stock, 0) }}
                    @else
                    {{ @num_format($item->vld_stock) }}
                    @endif
                </td>
                <td class="alncenter td2">{{ @num_format($value) }}</td>
                <td class="alncenter td2">{{ @num_format($item->total_sold) }}</td>
            </tr>
            @php
            $total_stock += $item->stock;
            $total_vld_stock += $item->vld_stock;
            $total_sold += $item->total_sold;
            $total_value += $value;
            @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="alncenter td2 bt">
                    <strong>{{ mb_strtoupper(__('accounting.totals')) }}</strong>
                </td>
                <td class="alncenter td2 bt">
                    <strong>
                        @if ($product_settings['show_stock_without_decimals'])
                        {{ number_format($total_stock, 0) }}
                        @else
                        {{ @num_format($total_stock) }}
                        @endif
                    </strong>
                </td>
                <td class="alncenter td2 bt">
                    <strong>
                        @if ($product_settings['show_stock_without_decimals'])
                        {{ number_format($total_vld_stock, 0) }}
                        @else
                        {{ @num_format($total_vld_stock) }}
                        @endif
                    </strong>
                </td>
                <td class="alncenter td2 bt">
                    <strong>{{ @num_format($total_value) }}</strong>
                </td>
                <td class="alncenter td2 bt">
                    <strong>{{ @num_format($total_sold) }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
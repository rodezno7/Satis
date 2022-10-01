<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('report.products_report')</title>
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
                <strong>{{ mb_strtoupper(__('report.products_report')) }}</strong>
            </td>
        </tr>
    </table>
    <br>

    <table class="table1" style="width: 100%;">
        <thead>
            <tr>
                <th class="td2 bb">@lang('product.sku')</th>
                <th class="td2 bb">@lang('sale.product')</th>
                <th class="td2 bb">@lang('product.clasification')</th>
                <th class="td2 bb">@lang('product.category')</th>
                <th class="td2 bb">@lang('product.sub_category')</th>
                <th class="td2 bb">@lang('product.unit')</th>
                <th class="td2 bb">@lang('product.brand')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $item)
            <tr>
                <td class="td2">{{ $item->sku }} @if ($item->status == 'inactive') ({{ __('accounting.inactive') }}) @endif</td>
                <td class="td2">{{ $item->name }}</td>
                <td class="td2">{{ __('product.clasification_' . $item->clasification) }}</td>
                <td class="td2">{{ $item->category }}</td>
                <td class="td2">{{ $item->sub_category }}</td>
                <td class="td2">{{ $item->unit }}</td>
                <td class="td2">{{ $item->brand }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
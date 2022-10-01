<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@lang('report.list_price_report')</title>
    <style>
        @page { padding: 0; margin: 1.5cm; }
        body { 
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
        }
        h1 { text-align: center; margin: 0; }
        h2 { text-align: center; margin: 5px 0 0 0; }
        table { margin-top: 8px; width: 100%; border-collapse: collapse; }
        table th, table td { border: 1px solid #000; padding: 3px 5px; }
    </style>
</head>
<body>
    <h1>{{ mb_strtoupper($business_name) }}</h1>
    <h2>{{ mb_strtoupper(__('report.list_price_report')) }}</h2>
    <table>
        <thead>
            <tr>
                <th>{{ mb_strtoupper(__('product.sku')) }}</th>
                <th>{{ mb_strtoupper(__('product.product')) }}</th>
                <th>{{ mb_strtoupper(__('brand.brand')) }}</th>
                <th>{{ mb_strtoupper(__('category.category')) }}</th>
                <th>{{ mb_strtoupper(__('lang_v1.default')) }}</th>
                @foreach ($list_prices as $lp)
                    <th>{{ $lp['name'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $p)
                <tr>
                    <td>{{ $p['sku'] }}</td>
                    <td>{{ mb_strtoupper($p['product_name']) }}</td>
                    <td>{{ mb_strtoupper($p['brand_name']) }}</td>
                    <td>{{ mb_strtoupper($p['category_name']) }}</td>
                    <td style="text-align: right;">{{ '$ '. @num_format($p['default_price']) }}</td>
                    @foreach ($list_prices as $lp)
                        <td style="text-align: right;">{{ $p[$lp['name']] > 0 ? '$ '. @num_format($p[$lp['name']]) : '' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@lang('report.sales_summary_seller_report')</title>
    <style>
        @page{
            margin: 1.5cm 1cm;
        }
        body { font-family: 'Courier New', Courier, monospace; font-size: 8pt; }
        table { width: 100%; border-collapse: collapse; }
        table td, table th { border: 1px solid #000; }
    </style>
</head>
<body>
    <h1 style="margin-bottom: 10px; margin-top: 0; text-align: center;"> {{ mb_strtoupper(__('report.sales_summary_seller_report') . "|" . $location_name) }}</h1>
    <h3 style="text-align: center;">{{ @format_date($start_date) . " - " . @format_date($end_date) }}</h3>
    <table>
        <thead>
            <tr>
                <th>PRODUCTO</th>
                <th>DESCRIPCIÓN</th>
                <th>FAMILIA</th>
                <th>SUBFAMILIA</th>
                <th>MARCA</th>
                <th>CANTIDAD</th>
                <th>PRECIO UNITARIO</th>
                <th>VENTA TOTAL</th>
                <th>COD. VENDEDOR</th>
                <th>NOMBRE VENDEDOR</th>
                <th>TIPO VENTA</th>
                <th>COSTO</th>
                <th>COSTO TOTAL</th>
                <th>UTILIDAD</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $t)
                <tr>
                    <td>{{ $t->sku }}</td>
                    <td>{{ $t->product_name }}</td>
                    <td>{{ $t->category }}</td>
                    <td>{{ $t->sub_category }}</td>
                    <td>{{ $t->brand }}</td>
                    <td>{{ $t->quantity }}</td>
                    <td>{{ $t->unit_price }}</td>
                    <td>{{ $t->total_sale }}</td>
                    <td>{{ $t->employee_id }}</td>
                    <td>{{ $t->employee_name }}</td>
                    <td>{{ __("messages." . $t->payment_condition) }}</td>
                    <td>{{ $t->cost }}</td>
                    <td>{{ $t->total_cost }}</td>
                    <td>{{ $t->utility }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
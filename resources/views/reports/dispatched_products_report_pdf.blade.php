<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ __('report.dispatched_products_report') }}</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
        }
        h1 {
            text-align: center;
            margin: 0;
        }
        h2 {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 8px;
        }
        table tr th, table tr td {
            border: 1px solid #000;
            padding: 8px 4px;
        }
        table thead tr th {
            text-align: center;
            height: 125px;
        }
        table tfoot tr td {
            font-weight: bold;
            background-color: #ccc;
        }
        table tfoot tr td:first-child {
            text-align: center;
        }
        .text-vertical {
            display: inline-block;
            writing-mode: vertical-lr;
            transform: rotate(270deg);
            text-align: center;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>{{ mb_strtoupper($business_name) }}</h1>
    <h2>{{ mb_strtoupper(__('report.dispatched_products_report')) ." ". strtoupper(__('accounting.from_date')) ." ". $start_date ." ". strtoupper(__('accounting.to_date')) ." ". $end_date }}</h2>
    <table>
        <thead>
            <tr>
                <th>{{ mb_strtoupper(__('customer.customer')) }}</th>
                <th>{{ mb_strtoupper(__('customer.seller')) }}</th>
                <th style="width: 5%;">{{ mb_strtoupper(__('document_type.doc')) }}</th>
                @foreach ($products as $p)
                    <th style="width: 7%;"><span class="text-vertical">{{ mb_strtoupper( $p->product_name) }}</span></th>
                @endforeach
                <th style="width: 7%;">{{ mb_strtoupper( __('lang_v1.weight')) }}</th>
                <th style="width: 8%;">{{ mb_strtoupper( __('sale.total')) }}</th>
            </tr>
        </thead>
        @php
            $qty = 0;
            $currency_symbol = session('currency')['symbol'];
            foreach ($products as $p) {
                $total['product_'. $p->variation_id] = 0;
            }
            $total['weight'] = 0;
            $total['final'] = 0;
        @endphp
        <tbody>
            @foreach ($dispatched_products as $dp)
                <tr>
                    <td>{{ $dp->customer_name }}</td>
                    <td>{{ $dp->seller_name }}</td>
                    <td>{{ $dp->doc }}</td>
                    @foreach ($products as $p)
                        @php
                            $qty = $dispatched_products->where('customer_id', $dp->customer_id)
                                ->where('transaction_id', $dp->transaction_id)
                                ->sum('product_'. $p->variation_id);
                                
                            $total['product_'. $p->variation_id] += $qty;
                        @endphp
                        <td class="text-right">{{ $qty > 0 ? number_format($qty, 1) : "" }}</td>
                    @endforeach
                    <td class="text-right">{{ number_format($dp->weight_total, 1) }}</td>
                    <td class="text-right">{{ $currency_symbol ." ". number_format($dp->final_total, 2) }}</td>
                    @php
                        $total['weight'] += $dp->weight_total;
                        $total['final'] += $dp->final_total;
                    @endphp
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">{{ mb_strtoupper(__('report.grand_total')) }}</td>
                @foreach ($products as $p)
                    <td class="text-right">{{ number_format($total['product_'. $p->variation_id], 1) }}</td>
                @endforeach
                <td class="text-right">{{ number_format($total['weight'], 1) }}</td>
                <td class="text-right">{{ $currency_symbol ." ". number_format($total['final'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
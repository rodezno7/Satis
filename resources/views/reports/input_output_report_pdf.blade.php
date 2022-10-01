<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ __('report.input_output_report') }}</title>
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
        }
        table tr.foot td{
            font-weight: bold;
            background-color: #eee;
        }
        table tr.foot td:first-child {
            text-align: center;
        }
        table tr.white-space td {
            border-left: 0;
            border-right: 0;
            border-bottom: 0;
            height: 2px;
        }
        table tr.grand_total td {
            font-weight: bold;
            background-color: #aaa;
            font-size: 1.1rem;
        }
        table tr.grand_total td:first-child {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .cutter {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>{{ mb_strtoupper($business_name) }}</h1>
    <h2>{{ mb_strtoupper(__('report.input_output_report')) ." ". strtoupper(__('accounting.from_date')) ." ". $start_date ." ". strtoupper(__('accounting.to_date')) ." ". $end_date }}</h2>
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 5%;">{{ mb_strtoupper(__('product.sku')) }}</th>
                <th rowspan="2">{{ mb_strtoupper( __('product.product')) }}</th>
                <th rowspan="2" style="width: 7%;">{{ mb_strtoupper( __('lang_v1.initial')) }}</th>
                <th colspan="4">{{ mb_strtoupper( __('lang_v1.inputs')) }}</th>
                <th colspan="4">{{ mb_strtoupper( __('lang_v1.outputs')) }}</th>
                <th rowspan="2" style="width: 7%;">{{ mb_strtoupper( __('lang_v1.stock')) }}</th>
            </tr>
            <tr>
                <th style="width: 7%;">{{ mb_strtoupper( __('purchase.purchases')) }}</th>
                <th style="width: 7%;">{{ mb_strtoupper( __('lang_v1.transfers')) }}</th>
                <th style="width: 7%;">{{ mb_strtoupper( __('stock_adjustment.adjustments')) }}</th>
                <th style="width: 7%; font-size: 13px;">{{ mb_strtoupper( __('lang_v1.returns')) }}</th>
                <th style="width: 7%;">{{ mb_strtoupper( __('sale.sells')) }}</th>
                <th style="width: 7%;">{{ mb_strtoupper( __('lang_v1.transfers')) }}</th>
                <th style="width: 7%;">{{ mb_strtoupper( __('stock_adjustment.adjustments')) }}</th>
                <th style="width: 7%; font-size: 13px;">{{ mb_strtoupper( __('lang_v1.returns')) }}</th>
            </tr>
        </thead>
        @php
            $category = null;
            $count = 0;
            $counter = 1;
            /* category sub total variables */
            $cat_total_initial = 0; $cat_total_purchase = 0; $cat_total_in_transf = 0; $cat_total_in_adjust = 0; $cat_total_in_return = 0;
            $cat_total_sell = 0; $cat_total_out_transf = 0; $cat_total_out_adjust = 0; $cat_total_out_return = 0; $cat_total_stock = 0;

            /* total variables */
            $total_initial = 0; $total_purchase = 0; $total_in_transf = 0; $total_in_adjust = 0; $total_in_return = 0;
            $total_sell = 0; $total_out_transf = 0; $total_out_adjust = 0; $total_out_return = 0; $total_stock = 0;
        @endphp
        <tbody>
            {{-- product with categories --}}
            @foreach ($categories as $c)
                @php
                    $cat_total_initial += $c->initial_inventory; $cat_total_purchase += $c->purchases; $cat_total_in_transf += $c->input_stock_adjustments;
                    $cat_total_in_adjust += $c->input_stock_adjustments; $cat_total_in_return += $c->sell_returns; $cat_total_sell += $c->sales;
                    $cat_total_out_transf += $c->sell_transfers; $cat_total_out_adjust += $c->output_stock_adjustments;
                    $cat_total_out_return += $c->purchase_returns; $cat_total_stock += $c->stock;
                @endphp
                @if ($c->category_id != $category)
                    @php $count = $categories->where('category_id', $c->category_id)->count(); @endphp
                @endif
                <tr>
                    <td class="cutter">{{ $c->sku }}</td>
                    <td class="cutter">{{ $c->product_name }}</td>
                    <td class="text-right">{{ $c->initial_inventory }}</td>
                    <td class="text-right">{{ $c->purchases }}</td>
                    <td class="text-right">{{ $c->purchase_transfers }}</td>
                    <td class="text-right">{{ $c->input_stock_adjustments }}</td>
                    <td class="text-right">{{ $c->sell_returns }}</td>
                    <td class="text-right">{{ $c->sales }}</td>
                    <td class="text-right">{{ $c->sell_transfers }}</td>
                    <td class="text-right">{{ $c->output_stock_adjustments }}</td>
                    <td class="text-right">{{ $c->purchase_returns }}</td>
                    <td class="text-right">{{ $c->stock }}</td>
                </tr>
                @if ($count == $counter)
                    <tr class="foot">
                        <td colspan="2">{{ mb_strtoupper(__('sale.total') ." ". $c->category_name) }}</td>
                        <td class="text-right">{{ number_format($cat_total_initial, 1) }}</td>
                        <td class="text-right">{{ number_format($cat_total_purchase, 1) }}</td>
                        <td class="text-right">{{ number_format($cat_total_in_transf, 1) }}</td>
                        <td class="text-right">{{ number_format($cat_total_in_adjust, 1) }}</td>
                        <td class="text-right">{{ number_format($cat_total_in_return, 1) }}</td>
                        <td class="text-right">{{ number_format($cat_total_sell, 1) }}</td>
                        <td class="text-right">{{ number_format($cat_total_out_transf, 1) }}</td>
                        <td class="text-right">{{ number_format($cat_total_out_adjust, 1) }}</td>
                        <td class="text-right">{{ number_format($cat_total_out_return, 1) }}</td>
                        <td class="text-right">{{ number_format($cat_total_stock, 1) }}</td>
                    </tr>
                    <tr class="white-space">
                        <td colspan="12"></td>
                    </tr>
                    @php
                        /* reset counter */
                        $counter = 1;

                        /* sum to totals */
                        $total_initial += $cat_total_initial; $total_purchase += $cat_total_purchase; $total_in_transf += $cat_total_in_transf;
                        $total_in_adjust += $cat_total_in_adjust; $total_in_return += $cat_total_in_return; $total_sell += $cat_total_sell;
                        $total_out_transf += $cat_total_out_transf; $total_out_adjust += $cat_total_out_adjust;
                        $total_out_return += $cat_total_out_return; $total_stock += $cat_total_stock;

                        /* reset category totals */
                        $cat_total_initial = 0; $cat_total_purchase = 0; $cat_total_in_transf = 0; $cat_total_in_adjust = 0; $cat_total_in_return =  0;
                        $cat_total_sell = 0; $cat_total_out_transf = 0; $cat_total_out_adjust = 0; $cat_total_out_return = 0; $cat_total_stock = 0;
                    @endphp
                @else
                    @php $counter ++; @endphp
                @endif

                @php $category = $c->category_id; @endphp
            @endforeach
            <!-- no category products -->
            @foreach ($no_categories as $c)
                @php
                    $cat_total_initial += $c->initial_inventory; $cat_total_purchase += $c->purchases; $cat_total_in_transf += $c->input_stock_adjustments;
                    $cat_total_in_adjust += $c->input_stock_adjustments; $cat_total_in_return += $c->sell_returns; $cat_total_sell += $c->sales;
                    $cat_total_out_transf += $c->sell_transfers; $cat_total_out_adjust += $c->output_stock_adjustments;
                    $cat_total_out_return += $c->purchase_returns; $cat_total_stock += $c->stock;
                @endphp
                @if ($c->category_id != $category)
                    @php $count = $no_categories->count(); @endphp
                @endif
                <tr>
                    <td class="cutter">{{ $c->sku }}</td>
                    <td class="cutter">{{ $c->product_name }}</td>
                    <td class="text-right">{{ $c->initial_inventory }}</td>
                    <td class="text-right">{{ $c->purchases }}</td>
                    <td class="text-right">{{ $c->purchase_transfers }}</td>
                    <td class="text-right">{{ $c->input_stock_adjustments }}</td>
                    <td class="text-right">{{ $c->sell_returns }}</td>
                    <td class="text-right">{{ $c->sales }}</td>
                    <td class="text-right">{{ $c->sell_transfers }}</td>
                    <td class="text-right">{{ $c->output_stock_adjustments }}</td>
                    <td class="text-right">{{ $c->purchase_returns }}</td>
                    <td class="text-right">{{ $c->stock }}</td>
                </tr>
                @if ($count == $counter)
                    <tr class="foot">
                        <td colspan="2">{{ mb_strtoupper(__('sale.total') ." ". __('category.no_category')) }}</td>
                        <td class="text-right">{{ number_format($cat_total_initial, 1) }}</td>
                        <td class="text-right">{{ number_format($cat_total_purchase, 1) }}</td>
                        <td class="text-right">{{ number_format($cat_total_in_transf, 1) }}</td>
                        <td class="text-right">{{ number_format($cat_total_in_adjust, 1) }}</td>
                        <td class="text-right">{{ number_format($cat_total_in_return, 1) }}</td>
                        <td class="text-right">{{ number_format($cat_total_sell, 1) }}</td>
                        <td class="text-right">{{ number_format($cat_total_out_transf, 1) }}</td>
                        <td class="text-right">{{ number_format($cat_total_out_adjust, 1) }}</td>
                        <td class="text-right">{{ number_format($cat_total_out_return, 1) }}</td>
                        <td class="text-right">{{ number_format($cat_total_stock, 1) }}</td>
                    </tr>
                    <tr class="white-space">
                        <td colspan="12"></td>
                    </tr>
                    @php
                        /* reset counter */
                        $counter = 1;

                        /* sum to totals */
                        $total_initial += $cat_total_initial; $total_purchase += $cat_total_purchase; $total_in_transf += $cat_total_in_transf;
                        $total_in_adjust += $cat_total_in_adjust; $total_in_return += $cat_total_in_return; $total_sell += $cat_total_sell;
                        $total_out_transf += $cat_total_out_transf; $total_out_adjust += $cat_total_out_adjust;
                        $total_out_return += $cat_total_out_return; $total_stock += $cat_total_stock;

                        /* reset category totals */
                        $cat_total_initial = 0; $cat_total_purchase = 0; $cat_total_in_transf = 0; $cat_total_in_adjust = 0; $cat_total_in_return =  0;
                        $cat_total_sell = 0; $cat_total_out_transf = 0; $cat_total_out_adjust = 0; $cat_total_out_return = 0; $cat_total_stock = 0;
                    @endphp
                @else
                    @php $counter ++; @endphp
                @endif
            @endforeach
            <tr class="grand_total">
                <td colspan="2">{{ mb_strtoupper(__('report.grand_total')) }}</td>
                <td class="text-right">{{ number_format($total_initial, 1) }}</td>
                <td class="text-right">{{ number_format($total_purchase, 1) }}</td>
                <td class="text-right">{{ number_format($total_in_transf, 1) }}</td>
                <td class="text-right">{{ number_format($total_in_adjust, 1) }}</td>
                <td class="text-right">{{ number_format($total_in_return, 1) }}</td>
                <td class="text-right">{{ number_format($total_sell, 1) }}</td>
                <td class="text-right">{{ number_format($total_out_transf, 1) }}</td>
                <td class="text-right">{{ number_format($total_out_adjust, 1) }}</td>
                <td class="text-right">{{ number_format($total_out_return, 1) }}</td>
                <td class="text-right">{{ number_format($total_stock, 1) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
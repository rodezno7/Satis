<table>
    {{-- Header --}}
    <tr>
        <td>
            <strong>{{ mb_strtoupper($business->line_of_business) }}</strong>
        </td>
    </tr>
    <tr>
        <td>
            <strong>{{ mb_strtoupper(__('report.stock_report')) }}</strong>
        </td>
    </tr>

    <tr></tr>

    {{-- Table --}}
    <thead>
        <tr>
            <th style="border-bottom: 0.25px solid black;">
                <strong>SKU</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('business.product')) }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('report.unit_cost')) }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('sale.unit_price')) }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('report.current_stock')) }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('report.total_unit_sold')) }}</strong>
            </th>
        </tr>
    </thead>
    
    <tbody>
        @php
        $total_stock = 0;
        $total_sold = 0;
        @endphp
        @foreach ($products as $item)
        <tr>
            <td>{{ $item->sku }}</td>
            <td>
                {{ $item->product }} @if ($item->type == 'variable') {{ $item->product_variation }} . '-' . {{ $item->variation_name }} @endif
            </td>
            <td>{{ ! empty($item->unit_cost) ? $item->unit_cost : 0 }}</td>
            <td>{{ ! empty($item->unit_price) ? $item->unit_price : 0 }}</td>
            <td>{{ ! empty($item->stock) ? $item->stock : 0 }}</td>
            <td>{{ ! empty($item->total_sold) ? $item->total_sold : 0 }}</td>
        </tr>
        @php
        $total_stock += $item->stock;
        $total_sold += $item->total_sold;
        @endphp
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="4" style="border-top: 0.25px solid black; text-align: center;">
                <strong>{{ mb_strtoupper(__('accounting.totals')) }}</strong>
            </td>
            <td style="border-top: 0.25px solid black;">
                <strong>{{ $total_stock }}</strong>
            </td>
            <td style="border-top: 0.25px solid black;">
                <strong>{{ $total_sold }}</strong>
            </td>
        </tr>
    </tfoot>
</table>
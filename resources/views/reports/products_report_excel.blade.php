<table>
    {{-- Header --}}
    <tr>
        <td>
            <strong>{{ mb_strtoupper($business->business_full_name) }}</strong>
        </td>
    </tr>
    <tr>
        <td>
            <strong>{{ mb_strtoupper(__('report.products_report')) }}</strong>
        </td>
    </tr>

    <tr></tr>

    {{-- Table --}}
    <thead>
        <tr>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('product.sku')) }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('sale.product')) }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('product.clasification')) }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('product.category')) }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('product.sub_category')) }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('product.unit')) }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('product.brand')) }}</strong>
            </th>
        </tr>
    </thead>
    
    <tbody>
        @foreach ($products as $item)
        <tr>
            <td>{{ $item->sku }} @if ($item->status == 'inactive') ({{ __('accounting.inactive') }}) @endif</td>
            <td>{{ $item->name }}</td>
            <td>{{ __('product.clasification_' . $item->clasification) }}</td>
            <td>{{ $item->category }}</td>
            <td>{{ $item->sub_category }}</td>
            <td>{{ $item->unit }}</td>
            <td>{{ $item->brand }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
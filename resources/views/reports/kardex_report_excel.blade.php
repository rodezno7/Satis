<table>
    {{-- Header --}}
    <tr>
        <td>
            <strong>{{ mb_strtoupper($business->business_full_name) }}</strong>
        </td>
    </tr>

    <tr>
        <td>
            @if (is_null($start) || is_null($end))
            {{ mb_strtoupper(__('kardex.kardex')) }}
            @else
            {{ mb_strtoupper(__('kardex.kardex_detail', ['from' => @format_date($start), 'to' => @format_date($end)])) }}
            @endif
        </td>
    </tr>

    <tr></tr>

    <tr>
        <td><strong>{{ mb_strtoupper(__('messages.location')) }}</strong></td>
        <td colspan="2">{{ $warehouse->name }}</td>
        <td></td>
        @if (auth()->user()->can('kardex.view_costs'))
            <td></td>
            <td></td>
            <td></td>
        @endif
        <td><strong>{{ mb_strtoupper(__('product.product')) }}</strong></td>
        <td colspan="3">{{ $variation->product->name }} ({{ $variation->sub_sku }})</td>
    </tr>

    <tr></tr>

    {{-- Table --}}
    <thead>
        <tr>
            <th style="width: 10%; border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('kardex.date')) }}</strong>
            </th>
            <th style="width: 10%; border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('kardex.transaction')) }}</strong>
            </th>
            <th style="width: 25%; border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('kardex.type')) }}</strong>
            </th>
            <th style="width: 20%; border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('kardex.reference')) }}</strong>
            </th>
            <th style="width: 5%; border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('kardex.initial_stock')) }}</strong>
            </th>
            <th style="width: 10%; border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('kardex.input')) }}</strong>
            </th>
            <th style="width: 10%; border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('kardex.output')) }}</strong>
            </th>
            <th style="width: 10%; border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('kardex.final_stock')) }}</strong>
            </th>
            @if (auth()->user()->can('kardex.view_costs'))
                <th style="width: 10%; border-bottom: 0.25px solid black;">
                    <strong>{{ mb_strtoupper(__('kardex.input_cost')) }}</strong>
                </th>
                <th style="width: 10%; border-bottom: 0.25px solid black;">
                    <strong>{{ mb_strtoupper(__('kardex.output_cost')) }}</strong>
                </th>
                <th style="width: 10%; border-bottom: 0.25px solid black;">
                    <strong>{{ mb_strtoupper(__('kardex.balance')) }}</strong>
                </th>
            @endif
        </tr>
    </thead>
    
    <tbody>
        @foreach ($kardex as $item)
        <tr>
            <td>{{ $item->date_time }}</td>
            <td>{{ __("movement_type." . $item->movement_type) }}</td>
            <td>{{ __("movement_type." . $item->type) }}</td>
            <td>{{ $item->reference }}</td>
            <td>{{ $item->initial_stock }}</td>
            <td>{{ $item->inputs_quantity }}</td>
            <td>{{ $item->outputs_quantity }}</td>
            <td>{{ $item->balance }}</td>
            @if (auth()->user()->can('kardex.view_costs'))
                <td>{{ $item->total_cost_inputs }}</td>
                <td>{{ $item->total_cost_outputs }}</td>
                <td>{{ $item->balance_cost }}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>
<table>
    <tr>
        <td>
            <strong>{{ mb_strtoupper(__('kardex.kardex')) }} {{ mb_strtoupper($header_date) }}</strong>
        </td>
    </tr>
    <tr>
        <td>
            <strong>{{ mb_strtoupper(__('kardex.product')) }}:</strong> {{ mb_strtoupper($product) }}
        </td>
    </tr>
    <tr>

        <td>
            <strong>{{ mb_strtoupper(__('kardex.warehouse')) }}:</strong> {{ $warehouse }}
        </td>
    </tr>
    <tr>

        <td>
            <strong>{{ mb_strtoupper(__('kardex.initial_quantity')) }}:</strong> {{ $initial_quantity }}
        </td>
    </tr>
</table>

<table>
    <tr>
        <td><strong>{{ mb_strtoupper(__('kardex.number')) }}</strong></td>
        <td><strong>{{ mb_strtoupper(__('kardex.date')) }}</strong></td>
        <td><strong>{{ mb_strtoupper(__('kardex.initial_quantity')) }}</strong></td>
        <td><strong>{{ mb_strtoupper(__('kardex.type')) }}</strong></td>
        <td><strong>{{ mb_strtoupper(__('kardex.document')) }}</strong></td>
        <td><strong>{{ mb_strtoupper(__('kardex.quantity')) }}</strong></td>
        <td><strong>{{ mb_strtoupper(__('kardex.final_quantity')) }}</strong></td>
    </tr>
    @php($final_quantity = $initial_quantity)
    @php($cont = 1)
    @foreach($lines as $item)
    <tr>
        <td>{{ $cont }}</td>
        @php($cont = $cont + 1)
        <td>{{ $item->date }}</td>
        <td>{{ $final_quantity }}</td>
        <td>
            @if($item->type == 'opening_stock')
            @if($item->status != 'annulled')
            @lang('kardex.opening_stock')
            @php($final_quantity = $final_quantity + $item->quantity)
            @else
            @lang('kardex.opening_stock_annulled')
            @php($final_quantity = $final_quantity)
            @endif                
            @endif

            @if($item->type == 'purchase')
            @if($item->status != 'annulled')
            @lang('kardex.purchase')
            @php($final_quantity = $final_quantity + $item->quantity)
            @else
            @lang('kardex.purchase_annulled')
            @php($final_quantity = $final_quantity)
            @endif
            @endif

            @if($item->type == 'purchase_return')
            @if($item->status != 'annulled')
            @lang('kardex.purchase_return')
            @php($final_quantity = $final_quantity)
            @else
            @lang('kardex.purchase_return_annulled')
            @php($final_quantity = $final_quantity)
            @endif
            @endif

            @if($item->type == 'purchase_transfer')
            @if($item->status != 'annulled')
            @lang('kardex.purchase_transfer')
            @php($final_quantity = $final_quantity)
            @else
            @lang('kardex.purchase_transfer_annulled')
            @php($final_quantity = $final_quantity)
            @endif                
            @endif

            @if($item->type == 'sell')
            @if($item->status != 'annulled')
            @lang('kardex.sell')
            @php($final_quantity = $final_quantity - $item->quantity)
            @else
            @lang('kardex.sell_annulled')
            @php($final_quantity = $final_quantity)
            @endif
            @endif

            @if($item->type == 'sell_return')
            @if($item->status != 'annulled')
            @lang('kardex.sell_return')
            @php($final_quantity = $final_quantity)
            @else
            @lang('kardex.sell_return_annulled')
            @php($final_quantity = $final_quantity)
            @endif
            @endif

            @if($item->type == 'sell_transfer')
            @if($item->status != 'annulled')
            @lang('kardex.sell_tranfer')
            @php($final_quantity = $final_quantity)
            @else
            @lang('kardex.sell_tranfer_annulled')
            @php($final_quantity = $final_quantity)
            @endif
            @endif

            @if($item->type == 'ADJUSTMENT_IN')
            @if($item->status != 'annulled')
            @lang('kardex.adjustment_in')
            @php($final_quantity = $final_quantity + $item->quantity)
            @else
            @lang('kardex.adjustment_in_annulled')
            @php($final_quantity = $final_quantity)
            @endif
            @endif

            @if($item->type == 'ADJUSTMENT_OUT')
            @if($item->status != 'annulled')
            @lang('kardex.adjustment_out')
            @php($final_quantity = $final_quantity - $item->quantity)
            @else
            @lang('kardex.adjustment_out_annulled')
            @php($final_quantity = $final_quantity)
            @endif
            @endif

            @if($item->type == 'kit_out')
            @if($item->status != 'annulled')
            @lang('kardex.kit_out')
            @php($final_quantity = $final_quantity - $item->quantity)
            @else
            @lang('kardex.kit_out_annulled')
            @php($final_quantity = $final_quantity)
            @endif
            @endif

        </td>
        <td>
            @if($item->document != null)
            {{ $item->document }}
            @else
            N/A
            @endif
        </td>
        <td>{{ $item->quantity }}</td>
        <td>{{ $final_quantity }}</td>
    </tr>
    @endforeach
</table>
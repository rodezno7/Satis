<table>
    <tr>
        <td>
            {{ mb_strtoupper(__('quote.quote')) }}
        </td>

        <td>
            <strong>
                {{ $quote->quote_ref_no }}
            </strong>
        </td>
        <td></td>
        <td></td>
    </tr>

    <tr>
        <td>
            {{ mb_strtoupper(__('quote.date')) }}: 
        </td>

        <td>
            {{ $quote->quote_date }}

        </td>

        <td>
        </td>

        <td>
        </td>
    </tr>


    <tr>
        <td>
            {{ mb_strtoupper(__('quote.name')) }}: 
        </td>
        <td>


            {{ $quote->customer_name }}

            

        </td>

        <td>

        </td>

        <td>

        </td>
    </tr>

    <tr>
        <td>
            {{ mb_strtoupper(__('quote.address')) }}: 
        </td>
        <td>            
            {{ $quote->address }}
        </td>

        <td>

        </td>

        <td>

        </td>
    </tr>

    <tr>
        <td>
            {{ mb_strtoupper(__('quote.phone')) }}: 
        </td>
        <td>            
            {{ $quote->mobile }}

        </td>

        <td>

        </td>

        <td>

        </td>
    </tr>
    
    <tr>
        <td>
            {{ mb_strtoupper(__('quote.seller')) }}:
        </td>
        <td>
            {{ $quote->short_employee }}
        </td>
        <td>

        </td>

        <td>

        </td>
    </tr>



    <tr>
        <td>
            {{ mb_strtoupper(__('quote.conditions')) }}
        </td>
        <td>
            {{ $quote->terms_conditions }}
        </td>

        <td>
            
        </td>

        <td>
            
        </td>
    </tr>

    <tr>
        <td>
            {{ mb_strtoupper(__('quote.validity_report')) }}
        </td>

        <td>
            {{ $quote->validity }}
        </td>

        <td>

        </td>

        <td>
            
        </td>
    </tr>

    <tr>
        <td>
            {{ mb_strtoupper(__('quote.delivery_time')) }}
        </td>

        <td>
            {{ $quote->delivery_time }}
        </td>

        <td>
            
        </td>

        <td>
            
        </td>
    </tr>

    <tr>
        <td>
            <strong>{{ mb_strtoupper(__('quote.quantity')) }}</strong>
        </td>

        <td>
            <strong>{{ mb_strtoupper(__('quote.description')) }}</strong>
        </td>

        <td>
            <strong>{{ mb_strtoupper(__('quote.unit_price')) }}</strong>
        </td>

        <td>
            <strong>{{ mb_strtoupper(__('quote.affected')) }}</strong>
        </td>
    </tr>

    @foreach($lines as $item)

    <tr>
        <td>
            {{ $item->quantity }}
            @php($quantity = $item->quantity)
        </td>

        <td>
            @if($item->sku == $item->sub_sku)
            {{ $item->name_product }}
            @else
            {{ $item->name_product }} {{ $item->name_variation }}
            @endif
            @if($item->warranty != null)
            . <strong>@lang('quote.warranty')</strong>:{{ $item->warranty }}
            @endif
        </td>

        <td>
            @if($quote->tax_detail == 1)
            @php($unit_price = $item->unit_price_exc_tax)
            @else
            @php($unit_price = $item->unit_price_inc_tax)
            @endif

            @php($total = $unit_price * $quantity)
            @if($item->discount_type == "fixed")
            @php($discount = $item->discount_amount * $quantity)
            @php($discount_single = $item->discount_amount)
            @else
            @php($discount = (($item->discount_amount / 100 ) * $unit_price) * $quantity)
            @php($discount_single = (($item->discount_amount / 100 ) * $unit_price))
            @endif
            @php($total_final = $total - $discount )
            @php($unit_price_final = $unit_price - $discount_single )
            {{ number_format($unit_price_final, 4) }}


        </td>

        <td>


            {{ number_format($total_final, 2) }}
        </td>
    </tr>

    @endforeach
    

    <tr>
        @if($quote->tax_detail == 1)
        <td colspan="2">{{ $value_letters }}</td>
        @else
        <td colspan="2">{{ $value_letters }}</td>
        @endif
        @if($quote->discount_type == "fixed")
        <td>{{ mb_strtoupper(__('quote.discount')) }}</td>
        @else
        <td>{{ mb_strtoupper(__('quote.discount_percent')) }}</td>
        @endif
        <td>{{ number_format($quote->discount_amount, 2) }}</td>
    </tr>

    <tr>
        <td></td>
        <td></td>
        <td>{{ mb_strtoupper(__('quote.subtotal')) }}</td>
        <td>{{ number_format($quote->total_before_tax, 2) }}</td>

    </tr>

    @if($quote->tax_detail == 1)
    <tr>
        <td></td>
        <td></td>
        <td>IVA</td>
        <td>{{ number_format($quote->tax_amount, 2) }}</td>
    </tr>
    @endif

    <tr>
        <td></td>
        <td></td>
        <td>{{ mb_strtoupper(__('quote.total')) }}</td>
        <td>{{ number_format($quote->total_final, 2) }}</td>
    </tr>


    <tr>     


        <td colspan="4">
            {{ mb_strtoupper(__('quote.notes')) }}: {{ $quote->note }}
        </td>

    </tr>

    
</table>



@php
    // Number of decimal places to store and use in calculations
    $price_precision = config('app.price_precision');
@endphp

<tr>
    {{-- Reference --}}
    <td>
        {{ $purchase->ref_no }}
        {!! Form::hidden('purchases[' . $row_count . '][transaction_id]', $purchase->id) !!}

        <input type="hidden" class="purchase_id" value="{{ $purchase->id }}">
    </td>

    {{-- Supplier --}}
    <td>
        {{ $purchase->name }}
    </td>

    {{-- Amount --}}
    <td>
        <input
            type="text"
            class="form-control input-sm"
            value="{{ number_format($purchase->final_total - $purchase->purchase_expense_amount, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator) }}"
            readonly>

        <input
            type="hidden"
            class="purchase_amount"
            value="{{ number_format($purchase->final_total - $purchase->purchase_expense_amount, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator) }}">
    </td>

    {{-- Remove icon --}}
    <td class="text-center">
        <i class="fa fa-times remove_purchase_row text-danger" title="{{ __('lang_v1.remove') }}" style="cursor: pointer;"></i>
    </td>
</tr>

<input type="hidden" id="row_count_p" value="{{ $row_count }}">

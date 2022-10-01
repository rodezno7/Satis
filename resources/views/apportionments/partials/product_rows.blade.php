@php
    $purchase_price = is_null($line->initial_purchase_price) ? $line->purchase_price : $line->initial_purchase_price;

    // Number of decimal places to store and use in calculations
    $price_precision = config('app.price_precision');
@endphp

<tr>
    {{-- # --}}
    <td class="text-right">
        <span class="product_number"></span>

        {!! Form::hidden('products[' . $row_count . '][purchase_line_id]', $line->id) !!}

        <input type="hidden" class="product_total_import_expenses" value="{{ number_format($purchase->total_import_expenses, $price_precision) }}">
        <input type="hidden" class="apply_purchase_expense_amount" value="{{ number_format($purchase->purchase_expense_amount, $price_precision) }}">
        <input type="hidden" class="product_total_purchase" value="{{ number_format($purchase->total_purchase, $price_precision) }}">
        <input type="hidden" class="product_vat_percent" value="{{ number_format($product_vat, $price_precision) }}">
        <input type="hidden" class="product_purchase_id" value="{{ $line->transaction_id }}">
    </td>

    {{-- Product --}}
    <td>
        {{ $line->product->name }}
        {{ ! (empty($line->variations->name) || $line->variations->name == 'DUMMY') ? $line->variations->name : '' }}
        <br>
        <small>
            <strong>SKU:</strong> {{ $line->variations->sub_sku }}
        </small>
    </td>
    
    {{-- Quantity --}}
    <td class="text-right">
        {{ number_format($line->quantity, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
        <input type="hidden" class="product_quantity" value="{{ $line->quantity }}">
    </td>

    {{-- Weight --}}
    <td class="text-right">
        {{ number_format($line->weight_kg, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
        <input type="hidden" class="product_weight" value="{{ $line->weight_kg }}">
    </td>

    {{-- FOB --}}
    <td class="text-right">
        {{ number_format($purchase_price, $decimals_in_purchases, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
        <input type="hidden" class="product_fob" value="{{ $purchase_price }}">

        {!! Form::hidden('products[' . $row_count . '][initial_purchase_price]', $purchase_price) !!}
    </td>

    {{-- Total --}}
    <td class="text-right">
        {{ number_format($line->quantity * $purchase_price, $decimals_in_purchases, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
        <input type="hidden" class="product_total" value="{{ number_format($line->quantity * $purchase_price, $price_precision) }}">
    </td>

    {{-- Import expenses --}}
    <td class="text-right">
        <span class="spn_product_import_expenses"></span>
        {!! Form::hidden('products[' . $row_count . '][product_import_expenses]', 0, ['class' => 'product_import_expenses']) !!}
    </td>

    {{-- Other expenses --}}
    <td class="text-right">
        <span class="spn_product_other_expenses"></span>
        <input type="hidden" class="product_other_expenses" value="0">
    </td>

    {{-- CIF --}}
    <td class="text-right">
        <span class="spn_product_cif"></span>
        <input type="hidden" class="product_cif" value="0">
    </td>

    {{-- DAI percent --}}
    <td class="text-center">
        <div class="form-group" style="margin-bottom: 0;">
            <div class="input-group">
                {!! Form::text(
                    'products[' . $row_count . '][product_dai_percent]',
                    number_format($line->dai_percent, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator),
                    ['class' => 'form-control input-sm product_dai_percent input_number mousetrap', 'style' => 'width: 80px;']
                ) !!}
                <div class="input-group-addon">
                    <span>%</span>
                </div>
            </div>
        </div>
    </td>

    {{-- DAI amount --}}
    <td class="text-right">
        {!! Form::text(
            'products[' . $row_count . '][product_dai_amount]',
            number_format($line->dai_amount, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator),
            ['class' => 'form-control input-sm product_dai_amount input_number mousetrap', 'style' => 'width: 80px;']
        ) !!}
    </td>

    {{-- VAT --}}
    <td class="text-right">
        <span class="spn_product_vat"></span>
        {!! Form::hidden('products[' . $row_count . '][product_vat]', 0, ['class' => 'product_vat']) !!}
    </td>

    {{-- Total cost --}}
    <td class="text-right">
        <span class="spn_product_total_cost"></span>
        <input type="hidden" class="product_total_cost">
    </td>

    {{-- Unit cost --}}
    <td class="text-right">
        <span class="spn_product_unit_cost"></span>
        {!! Form::hidden('products[' . $row_count . '][product_unit_cost]', 0, ['class' => 'product_unit_cost']) !!}
        {!! Form::hidden('products[' . $row_count . '][product_unit_cost_exc_tax]', 0, ['class' => 'product_unit_cost_exc_tax']) !!}
    </td>

    {{-- Remove icon --}}
    {{-- <td class="text-center">
        <i class="fa fa-times remove_product_row text-danger" title="{{ __('lang_v1.remove') }}" style="cursor: pointer;"></i>
    </td> --}}
</tr>

<input type="hidden" id="row_count_pr" value="{{ $row_count }}">
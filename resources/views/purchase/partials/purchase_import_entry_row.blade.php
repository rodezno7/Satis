@foreach ($variations as $variation)
    <tr>
        <td><span class="sr_number"></span></td>
        <td>
            {{ $product->name }} ({{ $variation->sub_sku }})
            @if ($product->type == 'variable')
                <br />
                (<b>{{ $variation->product_variation->name }}</b> : {{ $variation->name }})
            @endif
        </td>
        <td>
            {!! Form::hidden('purchases[' . $row_count . '][product_id]', $product->id, ['id' => 'product_id']) !!}
            {!! Form::hidden('purchases[' . $row_count . '][variation_id]', $variation->id, ['id' => 'variation_id']) !!}

            @php
                $check_decimal = 'false';
                // if($product->unit->allow_decimal == 0){
                //     $check_decimal = 'true';
                // }
            @endphp
            {!! Form::text('purchases[' . $row_count . '][quantity]', 0, ['class' => 'form-control input-sm purchase_quantity input_number mousetrap', 'required', 'data-rule-abs_digit' => $check_decimal, 'data-msg-abs_digit' => __('lang_v1.decimal_value_not_allowed'), 'id' => 'quantity', 'style' => 'width: 70px;']) !!}
        </td>
        <td>
            <div class="input-group" style='width: 70px;'>
                {!! Form::text('purchases[' . $row_count . '][weight_kg]', 0.0, ['class' => 'form-control input-sm input_number', 'required', 'id' => 'weight_kg']) !!}
            </div>
        </td>
        <td>
            <div class="input-group" style='width: 70px;'>
                {!! Form::text('purchases[' . $row_count . '][price]', 0.0, ['class' => 'form-control input-sm input_number', 'required', 'id' => 'price']) !!}
            </div>
        </td>
        <td>
            <div class="input-group" style='width: 70px;'>
                {!! Form::text('purchases[' . $row_count . '][line_transfer_fee]', 0.0, ['class' => 'form-control input-sm input_number', 'required', 'id' => 'line_transfer_fee']) !!}
            </div>
        </td>
        <td>
            {!! Form::select('purchases[' . $row_count . '][line_freight_inc]', ['yes' => __('purchase.yes'), 'no' => __('purchase.no')], 'yes', ['class' => 'form-control select2 input-sm', 'id' => 'line_freight_inc']) !!}
            {!! Form::hidden('purchases[' . $row_count . '][line_freight_amount]', 0, ['id' => 'line_freight_amount']) !!}
            <span class="display_currency" data-currency_symbol="true" id="line_freight_amount_text">0.00</span>
        </td>
        <td>
            <span class="display_currency" data-currency_symbol="true" id="line_deconsolidation_amount_text">0.00</span>
            {!! Form::hidden('purchases[' . $row_count . '][line_deconsolidation_amount]', null, ['id' => 'line_deconsolidation_amount']) !!}
        </td>
        <td>
            <span class="display_currency" data-currency_symbol="true" id="line_dai_amount_text">0.00</span>
            {!! Form::hidden('purchases[' . $row_count . '][line_dai_amount]', null, ['id' => 'line_dai_amount']) !!}
        </td>
        <td>
            <span class="display_currency" data-currency_symbol="true" id="line_tax_amount_text">0.00</span>
            {!! Form::hidden('purchases[' . $row_count . '][line_tax_amount]', null, ['id' => 'line_tax_amount']) !!}
        </td>
        {{-- <td>
            <div class="input-group" style="width: 70px;">
                {!! Form::text('purchases[' . $row_count . '][storage_percentage]', 0,
                    ['class' => 'form-control input-sm input_number', 'id' => 'storage_percentage']); !!}
            </div>
            <span class="display_currency" data-currency_symbol="true" id="storage_percentage_text">0.00</span>
        </td> --}}
        <td>
            <span class="display_currency" data-currency_symbol="true" id="line_external_storage_text">0.00</span>
            {!! Form::hidden('purchases[' . $row_count . '][line_external_storage]', null, ['id' => 'line_external_storage']) !!}
        </td>
        <td>
            <span class="display_currency" data-currency_symbol="true" id="line_local_freight_amount_text">0.00</span>
            {!! Form::hidden('purchases[' . $row_count . '][line_local_freight_amount]', null, ['id' => 'line_local_freight_amount']) !!}
        </td>
        <td>
            <span class="display_currency" data-currency_symbol="true"
                id="line_customs_procedure_amount_text">0.00</span>
            {!! Form::hidden('purchases[' . $row_count . '][line_customs_procedure_amount]', null, ['id' => 'line_customs_procedure_amount']) !!}
        </td>
        <td>
            <span class="display_currency" data-currency_symbol="true" id="purchase_price_text">0.00</span>
            {!! Form::hidden('purchases[' . $row_count . '][purchase_price]', null, ['id' => 'purchase_price']) !!}
        </td>

        <?php $row_count++; ?>
        <td><i class="fa fa-times remove_purchase_entry_row text-danger" title="Remove" style="cursor:pointer;"></i>
        </td>
    </tr>
@endforeach

<input type="hidden" id="row_count" value="{{ $row_count }}">

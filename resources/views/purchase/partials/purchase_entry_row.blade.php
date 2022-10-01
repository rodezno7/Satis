@php
    // Number of decimal places to store and use in calculations
    $price_precision = config('app.price_precision');
@endphp

@foreach ($variations as $variation)
    <tr>
        {{-- No. --}}
        <td><span class="sr_number"></span></td>

        {{-- Product --}}
        <td style="text-align: left;">
            {{ $product->name }}
            @if ($product->type == 'variable')
                <br />
                (<b>{{ $variation->product_variation->name }}</b> : {{ $variation->name }})
            @endif
            <br>
            <small><strong>SKU:</strong> {{ $variation->sub_sku }}</small>
        </td>

        {{-- Quantity --}}
        <td>
            {!! Form::hidden('purchases[' . $row_count . '][product_id]', $product->id) !!}
            {!! Form::hidden('purchases[' . $row_count . '][variation_id]', $variation->id) !!}

            @php
                $check_decimal = 'false';
                if ($product->unit->allow_decimal == 0) {
                    $check_decimal = 'true';
                }
            @endphp
            {!! Form::text('purchases[' . $row_count . '][quantity]', 
            number_format(1, 4, $currency_details->decimal_separator, $currency_details->thousand_separator), 
            ['class' => 'form-control input-sm purchase_quantity input_number mousetrap', 
            'required', 'data-rule-abs_digit' => $check_decimal, 'data-msg-abs_digit' => __('lang_v1.decimal_value_not_allowed')]) !!}
        </td>

        {{-- Unit cost without discount --}}
        <td style='display:none;'>
            {!! Form::text('purchases[' . $row_count . '][pp_without_discount]', 
            number_format($variation->default_purchase_price, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator),
            ['class' => 'form-control input-sm purchase_unit_cost_without_discount input_number', 'required']) !!}
        </td>

        {{-- Discount percent --}}
        <td style='display:none;'>
            {!! Form::text('purchases[' . $row_count . '][discount_percent]', 0, ['class' => 'form-control input-sm inline_discounts input_number', 'required']) !!}
        </td>

        {{-- Purchase price --}}
        <td>
            {!! Form::text('purchases[' . $row_count . '][purchase_price]', number_format($variation->default_purchase_price, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost input_number', 'required']) !!}
        </td>

        @if ($purchase_type == 'international')
        {{-- Weight --}}
        <td>
            {!! Form::text('purchases[' . $row_count . '][product_weight]', number_format(0, 4, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm input_number purchase_product_weight']) !!}
        </td>

        {{-- Import expenses --}}
        <td class="col-import-expenses" style="display: none;">
            <input
                type="text"
                class="form-control input-sm input_number product_import_expenses"
                value="{{ number_format(0, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator) }}"
                readonly>
        </td>

        {{-- DAI --}}
        <td style="display: none;">
            <input
                type="text"
                name="purchases[{{ $row_count }}][dai_percent]"
                value="{{ number_format(0, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator) }}"
                class="form-control input-sm purchase_dai_percent input_number"
                @if ($product->check_dai != 1) readonly @endif>
            <input
                type="hidden"
                name="purchases[{{ $row_count }}][dai_amount]"
                class="purchase_dai_amount"
                value="{{ number_format(0, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator) }}">
        </td>
        @endif

        {{-- Total amount --}}
        <td>
            {!! Form::text('purchases[' . $row_count . '][total_amount]', null, ['class' => 'form-control input-sm row_purchase_total_amount input_number', 'required', 'readonly']) !!}
            {!! Form::hidden('purchases['. $row_count. '][tax_line_amount]', null, ["class" => 'tax_line_amount']) !!}
            {!! Form::hidden('purchases[' . $row_count . '][purchase_price_inc_tax]', null, ['class' => 'purchase_price_inc_tax']) !!}
        </td>

        {{-- Subtotal before tax --}}
        <td class="{{ $hide_tax }}" style="display: none;">
            <span class="row_subtotal_before_tax display_currency">0</span>
            <input type="hidden" class="row_subtotal_before_tax_hidden" value=0 >
        </td>

        {{-- Purchase line tax --}}
        <td class="{{$hide_tax}}" style="display: none;">
            <div class="input-group">
                <select name="purchases[{{ $row_count }}][purchase_line_tax_id]" class="form-control select2 input-sm purchase_line_tax_id" disabled placeholder="'Please Select'">
                    <option value="" data-tax_amount="0" @if( $hide_tax == 'hide' )
                    selected @endif >@lang('lang_v1.none')</option>
                    @foreach($taxes as $tax)
                        <option value="{{ $tax['id'] }}" data-tax_amount="{{ $tax['percent'] }}" @if( $product->tax == $tax['id'] && $hide_tax != 'hide') selected @endif >{{ $tax['name'] }}</option>
                    @endforeach
                </select>
                {!! Form::hidden('purchases[' . $row_count . '][item_tax]', 0, ['class' => 'purchase_product_unit_tax', 'disabled']); !!}
                <span class="input-group-addon purchase_product_unit_tax_text">0.00</span>
            </div>
        </td>

        {{-- Purchase price include tax --}}
        <td class="{{ $hide_tax }}" style="display: none;">
            @php
                $dpp_inc_tax = number_format($variation->dpp_inc_tax, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator);
                if ($hide_tax == 'hide') {
                    $dpp_inc_tax = number_format($variation->default_purchase_price, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator);
                }
            @endphp
            {!! Form::hidden('purchases[' . $row_count . '][purchase_price_inc_tax]', $dpp_inc_tax, ['class' => 'form-control input-sm purchase_unit_cost_after_tax input_number', 'required', 'disabled']) !!}
        </td>

        {{-- Subtotal after tax --}}
        <td style="display: none;">
            <span class="row_subtotal_after_tax display_currency">0</span>
            <input type="hidden" class="row_subtotal_after_tax_hidden" value=0 disabled>
        </td>

        {{-- Profit percent --}}
        <td style='display:none;' class="@if (!session('business.enable_editing_product_from_purchase')) hide @endif">
            {!! Form::text('purchases[' . $row_count . '][profit_percent]', number_format($variation->profit_percent, 4, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm input_number profit_percent', 'required']) !!}
        </td>

        {{-- Default sell price --}}
        <td style='display:none;'>
            @if (session('business.enable_editing_product_from_purchase'))
                {!! Form::text('purchases[' . $row_count . '][default_sell_price]', number_format($variation->default_sell_price, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm input_number default_sell_price', 'disabled']) !!}
            @else
                {{ number_format($variation->default_sell_price, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
            @endif
        </td>

        {{-- Lot number --}}
        @if (session('business.enable_lot_number'))
            <td style="display: none;">
                {!! Form::hidden('purchases[' . $row_count . '][lot_number]', null, ['class' => 'form-control input-sm']) !!}
            </td>
        @endif

        @if (session('business.enable_product_expiry'))
            <td style="text-align: left; display: none;">

                {{-- Maybe this condition for checkin expiry date need to be removed --}}
                @if (!empty($product->expiry_period_type))
                    <input type="hidden" class="row_product_expiry" value="{{ $product->expiry_period }}">
                    <input type="hidden" class="row_product_expiry_type" value="{{ $product->expiry_period_type }}">

                    @if (session('business.expiry_type') == 'add_manufacturing')
                        @php
                            $hide_mfg = false;
                        @endphp
                    @else
                        @php
                            $hide_mfg = true;
                        @endphp
                    @endif

                    <b class="@if ($hide_mfg) hide @endif"><small>@lang('product.mfg_date'):</small></b>
                    <div class="input-group @if ($hide_mfg) hide @endif">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! Form::text('purchases[' . $row_count . '][mfg_date]', null, ['class' => 'form-control input-sm expiry_datepicker mfg_date', 'readonly']) !!}
                    </div>

                    <b><small>@lang('product.exp_date'):</small></b>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! Form::text('purchases[' . $row_count . '][exp_date]', null, ['class' => 'form-control input-sm expiry_datepicker exp_date', 'readonly']) !!}
                    </div>
                @else
                    <div class="text-center">
                        @lang('product.not_applicable')
                    </div>
                @endif
            </td>
        @endif
        <?php $row_count++; ?>

        {{-- Remove icon --}}
        <td>
            <i class="fa fa-times remove_purchase_entry_row text-danger" title="Remove" style="cursor:pointer;"></i>
        </td>
    </tr>
@endforeach

<input type="hidden" id="row_count" value="{{ $row_count }}">

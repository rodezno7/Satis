@php
$hide_tax = '';
if (session()->get('business.enable_inline_tax') == 0) {
    $hide_tax = 'hide';
}
@endphp

@php
  $price_precision = config('app.price_precision');
@endphp

<div class="table-responsive">
    <table class="table table-condensed table-th-gray text-center table-striped table-text-center" id="purchase_entry_table" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th width="35%">@lang('product.product_name')</th>
                <th>@lang('purchase.purchase_quantity')</th>
                <th>@lang('product.unit_cost')</th>
                @if ($purchase->purchase_type == 'international')
                <th>@lang('apportionment.weight') (kg)</th>
                <th class="col-import-expenses" @if ($purchase->purchase_type != 'international' || ! $has_import_expenses) style="display: none;" @endif>
                    @lang('import_expense.import_expenses')
                </th>
                <th style="display: none;">@lang('product.dai') (%)</th>
                @endif
                <th>@lang('product.total_amount')</th>
                <th>
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </th>
            </tr>
        </thead>

        <tbody>
            <?php $row_count = 0; ?>
            @foreach ($purchase->purchase_lines as $purchase_line)
                <tr>
                    {{-- No. --}}
                    <td><span class="sr_number"></span></td>

                    {{-- Product --}}
                    <td style="text-align: left;">
                        {{ $purchase_line->product->name }}
                        <br>
                        <small><strong>SKU:</strong> {{ $purchase_line->variations->sub_sku }}</small>
                        @if ($purchase_line->product->type == 'variable')
                            <br />
                            (<b>{{ $purchase_line->variations->product_variation->name }}</b> : {{ $purchase_line->variations->name }})
                        @endif
                    </td>

                    {{-- Quantity --}}
                    <td>
                        {!! Form::hidden('purchases[' . $loop->index . '][product_id]', $purchase_line->product_id) !!}
                        {!! Form::hidden('purchases[' . $loop->index . '][variation_id]', $purchase_line->variation_id) !!}
                        {!! Form::hidden('purchases[' . $loop->index . '][purchase_line_id]', $purchase_line->id) !!}
                        @php
                            $check_decimal = 'false';
                            if ($purchase_line->product->unit->allow_decimal == 0) {
                                $check_decimal = 'true';
                            }
                        @endphp

                        {!! Form::text(
                            'purchases[' . $loop->index . '][quantity]',
                            number_format($purchase_line->quantity, 4),
                            [
                                'class' => 'form-control input-sm purchase_quantity input_number mousetrap',
                                'required',
                                'data-rule-abs_digit' => $check_decimal,
                                'data-msg-abs_digit' => __('lang_v1.decimal_value_not_allowed'),
                                $readonly
                            ]
                        ) !!}
                    </td>

                    {{-- Discount percent --}}
                    <td style='display: none;'>
                        {!! Form::text(
                            'purchases[' . $loop->index . '][pp_without_discount]',
                            number_format($purchase_line->pp_without_discount / $purchase->exchange_rate, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator),
                            ['class' => 'form-control input-sm purchase_unit_cost_without_discount input_number']
                        ) !!}
                    </td>

                    {{-- Purchase price --}}
                    <td>
                        {!! Form::text(
                            'purchases[' . $loop->index . '][purchase_price]',
                            number_format(is_null($purchase_line->initial_purchase_price) ? $purchase_line->purchase_price : $purchase_line->initial_purchase_price, $price_precision),
                            [
                                'class' => 'form-control input-sm purchase_unit_cost input_number',
                                $readonly
                            ]
                        ) !!}
                    </td>

                    @if ($purchase->purchase_type == 'international')
                    {{-- Weight --}}
                    <td>
                        {!! Form::text(
                            'purchases[' . $loop->index . '][product_weight]',
                            number_format($purchase_line->weight_kg, 4, $currency_details->decimal_separator, $currency_details->thousand_separator),
                            [
                                'class' => 'form-control input-sm input_number purchase_product_weight',
                                $readonly
                            ]
                        ) !!}
                    </td>

                    {{-- Import expenses --}}
                    <td class="col-import-expenses" @if (! $has_import_expenses) style="display: none;" @endif>
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
                            name="purchases[{{ $loop->index }}][dai_percent]"
                            value="{{ number_format($purchase_line->dai_percent, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator) }}"
                            class="form-control input-sm purchase_dai_percent input_number"
                            @if ($purchase_line->product->check_dai != 1) readonly @endif>
                        <input
                            type="hidden"
                            name="purchases[{{ $loop->index }}][dai_amount]"
                            class="purchase_dai_amount"
                            value="{{ number_format($purchase_line->dai_amount, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator) }}">
                    </td>
                    @endif

                    {{-- Total amount --}}
                    <td>
                        {!! Form::text(
                            'purchases[' . $row_count . '][total_amount]',
                            number_format($purchase_line->purchase_price * $purchase_line->quantity + $purchase_line->dai_amount, $price_precision),
                            ['class' => 'form-control input-sm row_purchase_total_amount input_number', 'required', 'readonly']
                        ) !!}

                        {!! Form::hidden(
                            'purchases[' . $row_count . '][tax_line_amount]',
                            number_format($purchase_line->tax_amount, $price_precision),
                            ['class' => 'tax_line_amount']
                        ) !!}

                        {!! Form::hidden(
                            'purchases[' . $row_count . '][purchase_price_inc_tax]',
                            number_format($purchase_line->purchase_price_inc_tax, $price_precision),
                            ['class' => 'purchase_price_inc_tax']
                        ) !!}
                    </td>

                    {{-- Subtotal before tax --}}
                    <td style="display: none;">
                        <span class="row_subtotal_after_tax display_currency">0</span>
                        <input type="hidden" class="row_subtotal_after_tax_hidden"
                            value={{ number_format($purchase_line->quantity * $purchase_line->purchase_price + $purchase_line->dai_amount + $purchase_line->tax_amount, $price_precision) }}
                            disabled>
                        <input type="hidden" class="row_subtotal_before_tax_hidden"
                            value={{ number_format($purchase_line->quantity * $purchase_line->purchase_price + $purchase_line->dai_amount, $price_precision) }}>
                    </td>

                    {{-- Default sell price --}}
                    <td style='display: none;' class="@if (!session('business.enable_editing_product_from_purchase')) hide @endif">
                        @php
                            $pp = $purchase_line->purchase_price;
                            $sp = $purchase_line->variations->default_sell_price;
                        @endphp
                    </td>

                    <td style='display: none;'>
                        @if (session('business.enable_editing_product_from_purchase'))
                            {!! Form::text(
                                'purchases[' . $loop->index . '][default_sell_price]',
                                number_format($purchase_line->variations->default_sell_price, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator),
                                ['class' => 'form-control input-sm input_number default_sell_price', 'required']
                            ) !!}
                        @else
                            {{ number_format($purchase_line->variations->default_sell_price, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                        @endif
                    </td>
                    
                    @if (session('business.enable_product_expiry'))
                        <td style="text-align: left; display: none;">

                            {{-- Maybe this condition for checkin expiry date need to be removed --}}
                            @if (!empty($product->expiry_period_type))
                                <input type="hidden" class="row_product_expiry" value="{{ $product->expiry_period }}">
                                <input type="hidden" class="row_product_expiry_type"
                                    value="{{ $product->expiry_period_type }}">

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

                    <td @if ($disabled) style="pointer-events: none;" @endif>
                        <i class="fa fa-times remove_purchase_entry_row text-danger" title="Remove" style="cursor:pointer;"></i>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<input type="hidden" id="row_count" value="{{ $row_count }}">

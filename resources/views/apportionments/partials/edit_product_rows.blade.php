@php
    // Number of decimal places to store and use in calculations
    $price_precision = config('app.price_precision');
@endphp

<div class="table-responsive">
    <table class="table table-striped table-condensed table-th-gray table-text-center" id="apportionment_table" width="100%">
        <thead>
            <tr>
                {{-- # --}}
                <th class="text-center">
                    #
                </th>
                {{-- Product --}}
                <th>
                    @lang('purchase.product')
                </th>
                {{-- Quantity --}}
                <th class="text-center">
                    @lang('lang_v1.quantity')
                </th>
                {{-- Weight --}}
                <th class="text-center">
                    @lang('lang_v1.weight')
                </th>
                {{-- FOB --}}
                <th class="text-center">
                    FOB
                </th>
                {{-- Total --}}
                <th class="text-center">
                    @lang('accounting.total')
                </th>
                {{-- Import expenses --}}
                <th class="text-center">
                    @lang('import_expense.import_expenses')
                </th>
                {{-- Other expenses --}}
                <th class="text-center">
                    @lang('apportionment.other_expenses')
                </th>
                {{-- CIF --}}
                <th class="text-center">
                    CIF
                </th>
                {{-- DAI --}}
                <th class="text-center" colspan="2">
                    DAI
                </th>
                {{-- VAT --}}
                <th class="text-center">
                    @lang('purchase.vat')
                </th>
                {{-- Total cost --}}
                <th class="text-center">
                    @lang('report.total_cost')
                </th>
                {{-- Unit cost --}}
                <th class="text-center">
                    @lang('purchase.unit_cost')
                </th>
                {{-- Remove icon --}}
                {{-- <th class="text-center">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </th> --}}
            </tr>
        </thead>
        <tbody>
            @php $row_count = 0; @endphp

            @foreach ($lines as $line)

            @php
            $purchase_price = is_null($line->initial_purchase_price) ? $line->purchase_price : $line->initial_purchase_price;
            @endphp

            <tr>
                {{-- # --}}
                <td class="text-right">
                    <span class="product_number"></span>
            
                    {!! Form::hidden('products[' . $loop->index . '][purchase_line_id]', $line->id) !!}
                    {!! Form::hidden('products[' . $loop->index . '][transaction_id]', $line->transaction_id, ['class' => 'product_purchase_id']) !!}
            
                    <input type="hidden" class="product_total_import_expenses" value="{{ number_format($purchases_p[$line->id]->total_import_expenses, $price_precision) }}">
                    <input type="hidden" class="product_total_purchase" value="{{ number_format($purchases_p[$line->id]->total_purchase, $price_precision) }}">
                    <input type="hidden" class="product_vat_percent" value="{{ number_format($products_vat[$line->id], $price_precision) }}">
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
                    
                    {!! Form::hidden('products[' . $loop->index . '][initial_purchase_price]', $purchase_price) !!}
                </td>
            
                {{-- Total --}}
                <td class="text-right">
                    {{ number_format($line->quantity * $purchase_price, $decimals_in_purchases, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                    <input type="hidden" class="product_total" value="{{ number_format($line->quantity * $purchase_price, $price_precision) }}">
                </td>
            
                {{-- Import expenses --}}
                <td class="text-right">
                    <span class="spn_product_import_expenses"></span>
                    {!! Form::hidden('products[' . $loop->index . '][product_import_expenses]', 0, ['class' => 'product_import_expenses']) !!}
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
                                'products[' . $loop->index . '][product_dai_percent]',
                                number_format($line->dai_percent, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator),
                                [
                                    'class' => 'form-control input-sm product_dai_percent input_number mousetrap',
                                    'style' => 'width: 80px;',
                                    'data-line-id' => $line->id,
                                    $disabled
                                ]
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
                        'products[' . $loop->index . '][product_dai_amount]',
                        number_format($line->dai_amount, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator),
                        ['class' => 'form-control input-sm product_dai_amount input_number mousetrap', 'style' => 'width: 80px;', $disabled]
                    ) !!}
                </td>
            
                {{-- VAT --}}
                <td class="text-right">
                    <span class="spn_product_vat"></span>
                    {!! Form::hidden('products[' . $loop->index . '][product_vat]', 0, ['class' => 'product_vat']) !!}
                </td>
            
                {{-- Total cost --}}
                <td class="text-right">
                    <span class="spn_product_total_cost"></span>
                    <input type="hidden" class="product_total_cost">
                </td>
            
                {{-- Unit cost --}}
                <td class="text-right">
                    <span class="spn_product_unit_cost"></span>
                    {!! Form::hidden('products[' . $loop->index . '][product_unit_cost]', 0, ['class' => 'product_unit_cost']) !!}
                    {!! Form::hidden('products[' . $loop->index . '][product_unit_cost_exc_tax]', 0, ['class' => 'product_unit_cost_exc_tax']) !!}
                </td>
            
                {{-- Remove icon --}}
                {{-- <td class="text-center">
                    <i class="fa fa-times remove_product_row text-danger" title="{{ __('lang_v1.remove') }}" style="cursor: pointer;"></i>
                </td> --}}
            </tr>

            @php $row_count++; @endphp

            @endforeach
        </tbody>
        <tfoot>
            <tr class="active">
                {{-- # --}}
                <td></td>
                {{-- Product --}}
                <td></td>
                {{-- Quantity --}}
                <td class="text-right">
                    <strong><span id="product_total_quantity">0.0000</span></strong>
                </td>
                {{-- Weight --}}
                <td class="text-center">
                    <strong><span id="spn_product_total_weight">0.0000</span></strong>
                    <input type="hidden" id="product_total_weight" value="0">
                </td>
                {{-- FOB --}}
                <td class="text-center">
                    <strong><span id="product_total_fob">0.0000</span></strong>
                </td>
                {{-- Total --}}
                <td class="text-right">
                    <strong><span id="spn_product_total_total">0.0000</span></strong>
                    <input type="hidden" id="product_total_total" value="0">
                </td>
                {{-- Import expenses --}}
                <td class="text-right">
                    <strong><span id="product_total_import_expenses">0.0000</span></strong>
                </td>
                {{-- Other expenses --}}
                <td class="text-right">
                    <strong><span id="product_total_other_expenses">0.0000</span></strong>
                </td>
                {{-- CIF --}}
                <td class="text-right">
                    <strong><span id="product_total_cif">0.0000</span></strong>
                </td>
                {{-- DAI percent --}}
                <td></td>
                {{-- DAI amount --}}
                <td class="text-right">
                    <strong><span id="product_total_dai_amount">0.0000</span></strong>
                </td>
                {{-- VAT --}}
                <td class="text-right">
                    <strong><span id="product_total_vat">0.0000</span></strong>
                </td>
                {{-- Total cost --}}
                <td class="text-right">
                    <strong><span id="product_total_total_cost">0.0000</span></strong>
                </td>
                {{-- Unit cost --}}
                <td></td>
                {{-- Remove icon --}}
                {{-- <td></td> --}}
            </tr>
        </tfoot>
    </table>
</div>

<input type="hidden" id="row_count_pr" value="{{ $row_count }}">
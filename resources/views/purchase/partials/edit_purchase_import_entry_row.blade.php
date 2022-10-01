@php
$hide_tax = '';
if (session()->get('business.enable_inline_tax') == 0) {
    $hide_tax = 'hide';
}
@endphp
<div class="table-responsive">
    <table class="table table-condensed table-bordered table-th-green text-center table-striped"
        id="purchase_entry_table">
        <thead>
            <tr>
                <th>#</th>
                <th style="width: 18%;">@lang( 'purchase.product' )</th>
                <th>@lang( 'purchase.quantity' )</th>
                <th>@lang( 'purchase.weight_kg' )</th>
                <th>@lang( 'purchase.fob_price' )</th>
                <th>
                    @lang( 'purchase.transfer_fee' )
                    @show_tooltip(__('purchase.transfer_fee_tooltip'))
                </th>
                <th>@lang( 'purchase.freight' )</th>
                <th>
                    @lang( 'purchase.deconsolidation' )
                    @show_tooltip(__('purchase.deconsolidation_tooltip'))
                </th>
                <th>@lang( 'purchase.dai' )</th>
                <th>@lang( 'purchase.vat' )</th>
                <th>@lang( 'purchase.storage_ex_abr' )
                    @show_tooltip(__('purchase.external_storage'))
                </th>
                <th>@lang( 'purchase.local_freight' )</th>
                <th>@lang( 'purchase.customs_procedure' )</th>
                <th>@lang( 'purchase.unit_cost' )</th>
                <th><i class="fa fa-trash" aria-hidden="true"></i></th>
            </tr>
        </thead>
        <tbody>
            <?php $row_count = 0; ?>
            @foreach ($purchase->purchase_lines as $purchase_line)
                <tr>
                    <td><span class="sr_number"></span></td>
                    <td>
                        {{ $purchase_line->product->name }} ({{ $purchase_line->variations->sub_sku }})
                        @if ($purchase_line->product->type == 'variable')
                            <br />
                            {{-- (<b>{{ $purchase_line->variations->name }}</b> : {{ $purchase_line->variations->name }}) --}}
                            (<b>{{ $purchase_line->variations->name }}</b>)
                        @endif
                    </td>
                    <td>
                        {!! Form::hidden('purchases[' . $loop->index . '][product_id]', $purchase_line->product->id, ['id' => 'product_id']) !!}
                        {!! Form::hidden('purchases[' . $loop->index . '][variation_id]', $purchase_line->variations->id, ['id' => 'variation_id']) !!}
                        {!! Form::hidden('purchases[' . $loop->index . '][purchase_line_id]', $purchase_line->id) !!}
                        {{-- {!! Form::hidden('purchases[' . $loop->index . '][default_sell_price]', number_format($purchase_line->variations->default_sell_price, 2, $currency_details->decimal_separator, $currency_details->thousand_separator)); !!} --}}
                        @php
                            $check_decimal = 'false';
                            // if($product->unit->allow_decimal == 0){
                            //     $check_decimal = 'true';
                            // }
                        @endphp
                        {!! Form::text('purchases[' . $loop->index . '][quantity]', $purchase_line->quantity, ['class' => 'form-control input-sm purchase_quantity input_number mousetrap', 'required', 'data-rule-abs_digit' => $check_decimal, 'data-msg-abs_digit' => __('lang_v1.decimal_value_not_allowed'), 'id' => 'quantity', 'style' => 'width: 70px;']) !!}
                    </td>
                    <td>
                        <div class="input-group" style='width: 70px;'>
                            {!! Form::text('purchases[' . $loop->index . '][weight_kg]', $purchase_line->weight_kg, ['class' => 'form-control input-sm input_number', 'required', 'id' => 'weight_kg']) !!}
                        </div>
                    </td>
                    <td>
                        <div class="input-group" style='width: 70px;'>
                            {!! Form::text('purchases[' . $loop->index . '][price]', $purchase_line->purchase_price, ['class' => 'form-control input-sm input_number', 'required', 'id' => 'price']) !!}
                        </div>
                    </td>
                    <td>
                        <div class="input-group" style='width: 70px;'>
                            {!! Form::text('purchases[' . $loop->index . '][line_transfer_fee]', $purchase_line->transfer_fee, ['class' => 'form-control input-sm input_number', 'required', 'id' => 'line_transfer_fee']) !!}
                        </div>
                    </td>
                    <td>
                        {!! Form::select('purchases[' . $loop->index . '][line_freight_inc]', ['yes' => __('purchase.yes'), 'no' => __('purchase.no')], 'yes', ['class' => 'form-control select2 input-sm', 'id' => 'line_freight_inc']) !!}
                        {!! Form::hidden('purchases[' . $loop->index . '][line_freight_amount]', $purchase_line->freight_amount, ['id' => 'line_freight_amount']) !!}
                        <span class="display_currency" data-currency_symbol="true"
                            id="line_freight_amount_text">{{ $purchase_line->freight_amount }}</span>
                    </td>
                    <td>
                        <span class="display_currency" data-currency_symbol="true"
                            id="line_deconsolidation_amount_text">{{ $purchase_line->deconsolidation_amount }}</span>
                        {!! Form::hidden('purchases[' . $loop->index . '][line_deconsolidation_amount]', $purchase_line->deconsolidation_amount, ['id' => 'line_deconsolidation_amount']) !!}
                    </td>
                    <td>
                        <span class="display_currency" data-currency_symbol="true"
                            id="line_dai_amount_text">{{ $purchase_line->dai_amount }}</span>
                        {!! Form::hidden('purchases[' . $loop->index . '][line_dai_amount]', $purchase_line->dai_amount, ['id' => 'line_dai_amount']) !!}
                    </td>
                    <td>
                        <span class="display_currency" data-currency_symbol="true"
                            id="line_tax_amount_text">{{ $purchase_line->tax_amount }}</span>
                        {!! Form::hidden('purchases[' . $loop->index . '][line_tax_amount]', $purchase_line->tax_amount, ['id' => 'line_tax_amount']) !!}
                    </td>
                    <td>
                        <span class="display_currency" data-currency_symbol="true"
                            id="line_external_storage_text">{{ $purchase_line->external_storage }}</span>
                        {!! Form::hidden('purchases[' . $loop->index . '][line_external_storage]', $purchase_line->external_storage, ['id' => 'line_external_storage']) !!}
                    </td>
                    <td>
                        <span class="display_currency" data-currency_symbol="true"
                            id="line_local_freight_amount_text">{{ $purchase_line->local_freight_amount }}</span>
                        {!! Form::hidden('purchases[' . $loop->index . '][line_local_freight_amount]', $purchase_line->local_freight_amount, ['id' => 'line_local_freight_amount']) !!}
                    </td>
                    <td>
                        <span class="display_currency" data-currency_symbol="true"
                            id="line_customs_procedure_amount_text">{{ $purchase_line->customs_procedure_amount }}</span>
                        {!! Form::hidden('purchases[' . $loop->index . '][line_customs_procedure_amount]', $purchase_line->customs_procedure_amount, ['id' => 'line_customs_procedure_amount']) !!}
                    </td>
                    <td>
                        <span class="display_currency" data-currency_symbol="true"
                            id="purchase_price_text">{{ $purchase_line->purchase_price }}</span>
                        {!! Form::hidden('purchases[' . $loop->index . '][purchase_price]', $purchase_line->purchase_price, ['id' => 'purchase_price']) !!}
                    </td>

                    <?php $row_count++; ?>
                    <td><i class="fa fa-times remove_purchase_entry_row text-danger" title="Remove"
                            style="cursor:pointer;"></i></td>
                </tr>
                <?php $row_count = $loop->index + 1; ?>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" style="background-color: seagreen;">@lang('purchase.total')</th>
                <th style="background-color: seagreen;">
                    <span class="display_currency" data-currency_symbol="false" id="total_quantity_text">0.00</span>
                </th>
                <th style="background-color: seagreen;">
                    <span class="display_currency" data-currency_symbol="false" id="total_weight_kg_text">0.00</span>
                </th>
                <th style="background-color: seagreen;">
                    <span class="display_currency" data-currency_symbol="true" id="total_fob_price_text">0.00</span>
                </th>
                <th style="background-color: seagreen;">
                    <span class="display_currency" data-currency_symbol="true" id="total_transfer_fee_text">0.00</span>
                </th>
                <th style="background-color: seagreen;">
                    <span class="display_currency" data-currency_symbol="true" id="total_freight_text">0.00</span>
                </th>
                <th style="background-color: seagreen;">
                    <span class="display_currency" data-currency_symbol="true"
                        id="total_deconsolidation_text">0.00</span>
                </th>
                <th style="background-color: seagreen;">
                    <span class="display_currency" data-currency_symbol="true" id="total_dai_text">0.00</span>
                </th>
                <th style="background-color: seagreen;">
                    <span class="display_currency" data-currency_symbol="true" id="total_vat_text">0.00</span>
                </th>
                <th style="background-color: seagreen;">
                    <span class="display_currency" data-currency_symbol="true" id="total_external_storage">0.00</span>
                </th>
                <th style="background-color: seagreen;">
                    <span class="display_currency" data-currency_symbol="true" id="total_local_freight_text">0.00</span>
                </th>
                <th style="background-color: seagreen;">
                    <span class="display_currency" data-currency_symbol="true"
                        id="total_customs_procedure_text">0.00</span>
                </th>
                <th style="background-color: seagreen;"></th>
                <th style="background-color: seagreen;"></th>
            </tr>
        </tfoot>
    </table>
</div>
<input type="hidden" id="row_count" value="{{ $row_count }}">

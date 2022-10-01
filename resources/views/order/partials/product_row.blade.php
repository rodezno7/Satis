@foreach ($quote_lines as $ql)
    @php
        $line_total_before_discount = 0;
        $quantity = $ql->quantity ? $ql->quantity : 1;
        $unit_price = 0;
        if($tax_detail){
            $line_total_before_discount = (double)($quantity * $ql->unit_price_exc_tax);
            $unit_price = (double)($ql->unit_price_exc_tax);
        } else{
            $line_total_before_discount = (double)($quantity * $ql->unit_price_inc_tax);
            $unit_price = (double)($ql->unit_price_inc_tax);
        }
        
        $discount_calculated_line_amount = 0;
        $discount_amount = number_format($ql->discount_amount, 4);
        if($ql->discount_type == "fixed"){
            $discount_calculated_line_amount = $discount_amount * $quantity;
        } else if($ql->discount_type == "percentage"){
            $discount_calculated_line_amount = ($unit_price * ($discount_amount / 100)) * $quantity;
        }

        $tax_amount = round(($ql->unit_price_exc_tax * $ql->tax_percent), 4);

        $line_total = ($line_total_before_discount - $discount_calculated_line_amount);
    @endphp
<tr>
    <td>
        <span id='row_no'></span>
        <input type='hidden' id='quote_line_id' value='{{ $ql->id ? $ql->id : 0 }}'>
        <!-- Discount modal -->
        <div class="modal fade" id="discount_line_modal_{{ $ql->variation_id}}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">@lang('order.add_edit_discount')</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label("discount_line_type", __("order.type")) !!}
                                    {!! Form::select("discount_line_type", $discount_types, $ql->discount_type ? $ql->discount_type : "fixed",
                                        ["class" => "form-control select2", "id" => "discount_line_type", "style" => "width: 100%;"]) !!}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label("discount_line_amount", __("order.amount")) !!}
                                    {!! Form::text("discount_line_amount", $ql->discount_amount ? number_format($ql->discount_amount, 2) : 0,
                                        ["class" => "form-control input_number", "id" => "discount_line_amount"]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                    </div>
                </div>
            </div>
        </div>
    </td>
    <td style="text-align: left;">
        <span>{{ $ql->product_name }}</span>
        <input type='hidden' id='variation_id' value='{{ $ql->variation_id }}'>
        <input type='hidden' id='warehouse_id' value='{{ $ql->warehouse_id }}'>
    </td>
    <td>
        <select id="group_price" class="form-control input-sm group_price_line">
            {{--<option value="{{ $ql->unit_price_inc_tax }}">@lang('order.none')</option>--}}
            @foreach ($ql->variations as $v)
                @if ($loop->iteration == 1)
                    <option value="{{ $v['price_inc_tax'] }}" selected>{{ $v['price_group'] }}</option>
                @else
                    <option value="{{ $v['price_inc_tax'] }}">{{ $v['price_group'] }}</option>
                @endif
            @endforeach
        </select>
    </td>
    <td>
        <input class='form-control input-sm input_number' type='text' id='quantity' value='{{ $quantity }}'>
        <input class="qty_available" type="hidden" value="{{ $ql->qty_available }}">
    </td>
    <td>
        <input type="text" class="form-control input-sm input_number unit_price_text"
            value="{{ $tax_detail ? round($ql->unit_price_exc_tax, 4) : round($ql->unit_price_inc_tax, 4) }}">
        <input type='hidden' id='unit_price_exc_tax' value='{{ round($ql->unit_price_exc_tax, 4) }}'>
        <input type='hidden' id='unit_price_inc_tax' value='{{ round($ql->unit_price_inc_tax, 4) }}'>
        <input type='hidden' id='tax_percent' value='{{ $ql->tax_percent }}'>
    </td>
    <td>
        <span id='discount_calculated_line_amount_text' class='display_currency' data-currency_symbol='true'>$
            {{ $discount_calculated_line_amount ? @num_format($discount_calculated_line_amount) : 0.00 }}
        </span>
        {!! Form::hidden("discount_calculated_line_amount", $discount_calculated_line_amount ? $discount_calculated_line_amount : 0,
                                        ["id" => "discount_calculated_line_amount"]) !!}
    </td>
    <td>
        <span id='line_total_text' class='display_currency' data-currency_symbol='true'>${{ @num_format($line_total) }}</span>
        <input type='hidden' id='tax_line_amount' value='{{ $tax_amount ? $tax_amount : 0.00 }}'>
        <input type='hidden' id='line_total' value='{{ $line_total }}'>
    </td>
    <td>
        <button class='btn btn-xs' id="discount_row" title='{{ __("order.add_edit_discount") }}'><i class='fa fa-pencil' aria-hidden='true'></i></button>
        <button class='btn btn-xs' id="delete_row" title='{{ __("order.delete_row") }}'><i class='fa fa-times' aria-hidden='true'></i></button>
    </td>
</tr>
@endforeach
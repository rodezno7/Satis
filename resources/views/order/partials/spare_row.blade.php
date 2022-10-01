@foreach ($quote_lines as $ql)
@php
    $line_total_before_discount = 0;

    $quantity = $ql->quantity ? $ql->quantity : 1;

    $unit_price = 0;

    if ($tax_detail) {
        $line_total_before_discount = (double)($quantity * $ql->unit_price_exc_tax);
        $unit_price = (double)($ql->unit_price_exc_tax);

    } else {
        $line_total_before_discount = (double)($quantity * $ql->unit_price_inc_tax);
            $unit_price = (double)($ql->unit_price_inc_tax);
    }
    
    $discount_calculated_line_amount = 0;
        $discount_amount = number_format($ql->discount_amount, 4);

    if ($ql->discount_type == "fixed") {
        $discount_calculated_line_amount = $discount_amount * $quantity;

    } else if ($ql->discount_type == "percentage") {
        $discount_calculated_line_amount = ($unit_price * ($discount_amount / 100)) * $quantity;
    }

    $tax_amount = round(($ql->unit_price_exc_tax * $ql->tax_percent), 4);

    $line_total = ($line_total_before_discount - $discount_calculated_line_amount);
@endphp

<tr id="row-{{ $service_block_index }}-{{ $ql->variation_id }}">
    <td class="text-center">
        <span id='row_no'></span>

        {{-- quote_line_id --}}
        @if (isset($ql->id))
        <input
            type="hidden"
            id="quote_line_id"
            value="{{ $ql->id ? $ql->id : 0 }}">
        @endif

        {{-- service_parent_id --}}
        <input
            type='hidden'
            id='service_parent_id'
            value='{{ $ql->service_parent_id ?? $service_parent_id }}'>

        {{-- service_block_index --}}
        <input
            type="hidden"
            id="service_block_index-{{ $service_block_index }}-{{ $ql->variation_id }}"
            class="service_block_index"
            value="{{ $service_block_index }}">

        {{-- note --}}
        <input
            type="hidden"
            id="note_line"
            value="{{ isset($ql->note) ? $ql->note : '' }}">

        {{-- Discount modal --}}
        <div
            class="modal fade"
            id="discount_line_modal-{{ $service_block_index }}-{{ $ql->variation_id }}"
            tabindex="-1"
            role="dialog">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>

                        <h4 class="modal-title">
                            @lang('order.add_edit_discount')
                        </h4>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            {{-- discount_line_type --}}
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label("discount_line_type", __("order.type")) !!}

                                    {!! Form::select(
                                        "discount_line_type[]",
                                        $discount_types, $ql->discount_type ? $ql->discount_type : "fixed",
                                        ["class" => "form-control select2", "id" => "discount_line_type", "style" => "width: 100%;"]
                                    ) !!}
                                </div>
                            </div>

                            {{-- discount_line_amount --}}
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label("discount_line_amount", __("order.amount")) !!}

                                    {!! Form::text(
                                        "discount_line_amount[]",
                                        $ql->discount_amount ? number_format($ql->discount_amount, 2) : 0,
                                        ["class" => "form-control input_number", "id" => "discount_line_amount"]
                                    ) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            @lang('messages.close')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </td>

    <td>
        <span>{{ $ql->product_name }}</span>

        {{-- variation_id --}}
        <input
            type='hidden'
            id='variation_id'
            value='{{ $ql->variation_id }}'>

        {{-- warehouse_id --}}
        <input
            type='hidden'
            id='warehouse_id'
            value='{{ $ql->warehouse_id }}'>
    </td>

    {{-- group_price --}}
    <td>
        <select id="group_price"
            {{-- name="group_price[]" --}}
            class="form-control input-sm group_price_line">
            <option value="{{ $ql->unit_price_inc_tax }}">
                @lang('order.none')
            </option>
            @foreach ($ql->variations as $v)
            <option value="{{ $v['price_inc_tax'] }}">
                {{ $v['price_group'] }}
            </option>
            @endforeach
        </select>
    </td>

    {{-- quantity --}}
    <td>
        <input
            class='form-control input-sm input_number'
            type='text'
            id='quantity'
            value='{{ $ql->quantity ? $ql->quantity : 1 }}'>
    </td>

    <td>
        {{-- unit_price_text --}}
        <input
            type="text"
            class="form-control input-sm input_number unit_price_text"
            value="{{ $tax_detail ? round($ql->unit_price_exc_tax, 4) : round($ql->unit_price_inc_tax, 4) }}">

        {{-- unit_price_exc_tax --}}
        <input
            type='hidden'
            id='unit_price_exc_tax'
            value='{{ round($ql->unit_price_exc_tax, 4) }}'>

        {{-- unit_price_inc_tax --}}
        <input
            type='hidden'
            id='unit_price_inc_tax'
            value='{{ round($ql->unit_price_inc_tax, 4) }}'>

        {{-- tax_percent --}}
        <input
            type='hidden'
            id='tax_percent'
            value='{{ $ql->tax_percent }}'>
    </td>

    {{-- discount_calculated_line_amount --}}
    <td class="text-center">
        <span id='discount_calculated_line_amount_text' class='display_currency' data-currency_symbol='true'>
            $ {{ $discount_calculated_line_amount ? @num_format($discount_calculated_line_amount) : 0.00 }}
        </span>

        {!! Form::hidden(
            "discount_calculated_line_amount",
            $discount_calculated_line_amount ? $discount_calculated_line_amount : 0,
            ["id" => "discount_calculated_line_amount"]
        ) !!}
    </td>

    <td class="text-center">
        {{-- line_total --}}
        <span id='line_total_text' class='display_currency' data-currency_symbol='true'>
            $ {{ @num_format($line_total) }}
        </span>

        {{-- tax_line_amount --}}
        <input
            type='hidden'
            id='tax_line_amount'
            value='{{ $tax_amount ? $tax_amount : 0.00 }}'>

        {{-- line_total --}}
        <input
            type='hidden'
            id='line_total'
            value='{{ $line_total }}'>
    </td>

    {{-- Buttons --}}
    <td class="text-center">
        <button class='btn btn-xs' id="discount_row" title='{{ __("order.add_edit_discount") }}'>
            <i class='fa fa-pencil' aria-hidden='true'></i>
        </button>

        <button class='btn btn-xs' id="delete_row" title='{{ __("order.delete_row") }}'>
            <i class='fa fa-times' aria-hidden='true'></i>
        </button>
    </td>
</tr>
@endforeach
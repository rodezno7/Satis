<tr id="row-{{ $service_block_index }}-{{ $row_index }}">
    <td>
        {{-- Service block index --}}
        <input type="hidden" id="row_index_id" value="{{ $row_index }}">

        {{-- Button to remove row --}}
        <button
            id="spare-item-{{ $service_block_index }}-{{ $row_index }}"
            type="button"
            class="btn btn-danger btn-xs"
            onclick="deleteSpare({{ $service_block_index }}, {{ $row_index }}, {{ $product['variation_id'] }});">
            <i class="fa fa-times"></i>
        </button>
    </td>

    <td>
        {{-- quote_line_id --}}
        @if (isset($product['quote_line_id']))
        <input
            type="hidden"
            name="quote_line_id[]"
            value="{{ isset($product['quote_line_id']) ? $product['quote_line_id'] : 0 }}">
        @endif

        {{-- service_parent_id --}}
        <input
            type="hidden"
            name="service_parent_id[]"
            value="{{ isset($service_parent_id) ? $service_parent_id : $product['service_parent_id'] }}">

        {{-- variation_id --}}
        <input
            type="hidden"
            id="variation_id-{{ $service_block_index }}"
            name="variation_id[]"
            value="{{ $product['variation_id'] }}">

        {{-- line_warehouse_id --}}
        <input
            type="hidden"
            name="line_warehouse_id[]"
            value="{{ isset($warehouse_id) ? $warehouse_id : $product['warehouse_id'] }}">

        {{-- note --}}
        <input
            type="hidden"
            id="{{ 'note-' . $service_block_index }}"
            name="note_line[]"
            value="{{ isset($service_block['note']) ? $service_block['note'] : '' }}">

        {{ $product['name_product'] }}
        @if ($product['type_product'] != 'single')
            {{ $product['name_variation'] }}
        @endif
    </td>

    <td>
        {{-- quantity --}}
        <input
            type="text"
            name="quantity[]"
            id="quantity-{{ $service_block_index }}-{{ $row_index }}"
            class="form-control form-control-sm input_number"
            value="{{ $product['quantity'] ?? 1 }}"
            onchange="workshopCalculate()"
            required>
    </td>

    <td>
        {{-- tax_percent --}}
        <input
            class="four_decimals"
            type="hidden"
            id="tax_percent-{{ $service_block_index }}-{{ $row_index }}"
            name="tax_percent[]"
            value="{{ $product['tax_percent'] + 1 }}">

        {{-- unit_price_exc_tax --}}
        <input
            type="hidden"
            class="four_decimals"
            id="unit_price_exc_tax-{{ $service_block_index }}-{{ $row_index }}"
            name="unit_price_exc_tax[]"
            value="{{ $product['price'] }}">

        {{-- line_tax_amount --}}
        <input
            type="hidden"
            class="four_decimals"
            id="line_tax_amount-{{ $service_block_index }}-{{ $row_index }}"
            name="line_tax_amount[]"
            value="{{ $product['price_inc_tax'] - $product['price'] }}">

        {{-- unit_price_inc_tax --}}
        <input
            type="hidden"
            class="four_decimals"
            id="unit_price_inc_tax-{{ $service_block_index }}-{{ $row_index }}"
            name="unit_price_inc_tax[]"
            value="{{ $product['price_inc_tax'] }}">

        {{-- note --}}
        <input
            type="hidden"
            class="four_decimals"
            id="note-{{ $service_block_index }}-{{ $row_index }}"
            name="note[]"
            value="">

        @php
            if ($tax_detail == 1) {
                $unit_price = $product['price'];
            } else {
                $unit_price = $product['price_inc_tax'];
            }
        @endphp

        {{-- unit_price --}}
        <input
            type="text"
            id="unit_price-{{ $service_block_index }}-{{ $row_index }}"
            name="unit_price[]"
            class="form-control form-control-sm input_number four_decimals price_editable"
            value="{{ $unit_price }}"
            onchange="workshopChangePrice({{ $service_block_index }}, {{ $row_index }})">
    </td>

    <td>
        {{-- line_discount_type --}}
        <select
            name="line_discount_type[]"
            id="line_discount_type-{{ $service_block_index }}-{{ $row_index }}"
            class="form-control select_discount select2"
            style="width: 100%;">
            <option value="fixed" @if (isset($product['discount_type']) && $product['discount_type'] == 'fixed') selected @endif>
                @lang('quote.fixed')
            </option>
            <option value="percentage" @if (isset($product['discount_type']) && $product['discount_type'] == 'percentage') selected @endif>
                @lang('quote.percentage')
            </option>
        </select>
    </td>

    <td>
        {{-- line_discount_amount --}}
        <input
            type="text"
            id="line_discount_amount-{{ $service_block_index }}-{{ $row_index }}"
            name="line_discount_amount[]"
            class="form-control form-control-sm input_number four_decimals"
            value="{{ $product['discount_amount'] ?? '' }}"
            onchange="workshopCalculate()">
    </td>

    <td>
        {{-- subtotal --}}
        <input
            type="text"
            id="subtotal-{{ $service_block_index }}-{{ $row_index }}"
            name="subtotal[]"
            class="form-control form-control-sm input_number four_decimals"
            readonly
            required>
    </td>
</tr>
<div id="panel-{{ $service_block_index }}" class="panel panel-default">
    <div class="panel-body" style="padding-top: 10px;">
        {{-- Service block index --}}
        <input type="hidden" id="service_block_index_id" value="{{ $service_block_index }}">

        {{-- Service block index --}}
        <input type="hidden" id="row_index_id" value="{{ $row_index }}">

        {{-- Service id --}}
        <input type="hidden" class="service-id" value="{{ $id }}">

        {{-- Button to remove service block --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="pull-right">
                    <button
                        type="button"
                        id="remove-panel-{{ $service_block_index }}"
                        class="btn btn-box-tool remove-service-block"
                        style="padding: 0;"
                        data-panel-id="{{ $service_block_index }}">
                        <i class="fa fa-times fa-2x"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- quote_line_id --}}
        @if (isset($service_block['quote_line_id']))
        <input
            type="hidden"
            name="quote_line_id[]"
            value="{{ $service_block['quote_line_id'] > 0 ? $service_block['quote_line_id'] : 0 }}">
        @endif

        {{-- service_parent_id --}}
        <input
            type="hidden"
            name="service_parent_id[]"
            value="0">

        <div class="row">
            <div class="col-sm-7">
                <div class="table-responsive">
                    <table
                        id="table-service-{{ $service_block_index }}"
                        class="table table-condensed table-th-gray table-text-center">
                        <thead>
                            <tr>
                                <th width="80%">
                                    @lang('product.clasification_service')
                                </th>
                                <th class="text-center" width="20%">
                                    @lang('quote.subtotal')
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                {{-- name --}}
                                <td>
                                    {{ $service_block['name_product'] }}
                                    @if ($service_block['type_product'] != 'single')
                                        {{ $service_block['name_variation'] }}
                                    @endif
                                </td>

                                {{-- subtotal --}}
                                <td>
                                    <input
                                        type="text"
                                        id="subtotal-{{ $service_block_index }}"
                                        name="subtotal[]"
                                        class="form-control form-control-sm input_number four_decimals"
                                        value="0.0000"
                                        readonly
                                        required>
                                </td>

                                {{-- variation_id --}}
                                <input
                                    type="hidden"
                                    name="variation_id[]"
                                    value="{{ $service_block['variation_id'] }}">

                                {{-- warehouse_id --}}
                                <input
                                    type="hidden"
                                    name="line_warehouse_id[]"
                                    value="{{ isset($warehouse_id) ? $warehouse_id : $service_block['warehouse_id'] }}">

                                {{-- quantity --}}
                                <input
                                    type="hidden"
                                    name="quantity[]"
                                    id="quantity-{{ $service_block_index }}"
                                    class="form-control form-control-sm input_number"
                                    value="1"
                                    required>

                                {{-- tax_percent --}}
                                <input
                                    class="four_decimals"
                                    type="hidden"
                                    id="tax_percent-{{ $service_block_index }}"
                                    name="tax_percent[]"
                                    value="{{ $service_block['tax_percent'] + 1 }}">
                                
                                {{-- unit_price_exc_tax --}}
                                <input
                                    type="hidden"
                                    class="four_decimals"
                                    id="unit_price_exc_tax-{{ $service_block_index }}"
                                    name="unit_price_exc_tax[]"
                                    value="{{ $service_block['price'] }}">
                                
                                {{-- line_tax_amount --}}
                                <input
                                    type="hidden"
                                    class="four_decimals"
                                    id="line_tax_amount-{{ $service_block_index }}"
                                    name="line_tax_amount[]"
                                    value="{{ $service_block['price_inc_tax'] - $service_block['price'] }}">
                                
                                {{-- unit_price_inc_tax --}}
                                <input
                                    type="hidden"
                                    class="four_decimals"
                                    id="unit_price_inc_tax-{{ $service_block_index }}"
                                    name="unit_price_inc_tax[]"
                                    value="{{ $service_block['price_inc_tax'] }}">
                                
                                {{-- unit_price --}}
                                <input
                                    type="hidden"
                                    id="unit_price-{{ $service_block_index }}"
                                    name="unit_price[]"
                                    class="form-control form-control-sm input_number four_decimals price_editable"
                                    value="{{ $tax_detail == 1 ? $service_block['price'] : $service_block['price_inc_tax'] }}">

                                {{-- line_discount_type --}}
                                <input
                                    type="hidden"
                                    class="four_decimals"
                                    id="line_discount_type-{{ $service_block_index }}"
                                    name="line_discount_type[]"
                                    value="fixed">

                                {{-- line_discount_amount --}}
                                <input
                                    type="hidden"
                                    id="line_discount_amount-{{ $service_block_index }}"
                                    name="line_discount_amount[]"
                                    class="four_decimals">
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- note --}}
            <div class="col-sm-5">
                <div class="form-group">
                    <label for="">@lang('quote.description')</label>
                    {!! Form::textarea(
                        'note_line[]',
                        isset($service_block['note']) ? $service_block['note'] : '',
                        ['id' => 'note-' . $service_block_index, 'class' => 'form-control', 'rows' => 1]
                    ) !!}
                </div>
            </div>
        </div>

        <div class="row">
            {{-- search_product --}}
            <div class="col-sm-8 col-sm-offset-2">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-search"></i>
                        </span>

                        {!! Form::text('search_product-' . $service_block_index, null, [
                            'class' => 'form-control search-spare',
                            'id' => 'search_product-' . $service_block_index,
                            'placeholder' => __('lang_v1.search_product_placeholder'),
                            'data-service-block-index' => $service_block_index,
                            'data-service-parent-id' => $service_block['variation_id']
                        ]); !!}
                    </div>
                </div>
            </div>
        </div>

        @php
        $row_index = $row_index + 1;
        @endphp

        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="table-responsive">
                    <table
                        id="table-spares-{{ $service_block_index }}"
                        class="table table-condensed table-th-gray table-text-center">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    <i class="fa fa-trash"></i>
                                </th>
                                <th width="40%">
                                    @lang('quote.product_name')
                                </th>
                                <th class="text-center">
                                    @lang('lang_v1.quantity')
                                </th>
                                <th class="text-center">
                                    @lang('quote.unit_price')
                                </th>
                                <th class="text-center">
                                    @lang('quote.discount_type')
                                </th>
                                <th class="text-center">
                                    @lang('quote.discount')
                                </th>
                                <th class="text-center">
                                    @lang('quote.subtotal')
                                </th>
                            </tr>
                        </thead>
                        <tbody id="list-{{ $service_block_index }}">
                            @foreach ($service_block['spare_rows'] as $product)
                                @include('quote.partials.spare_row')
    
                                @php
                                $row_index = $row_index + 1;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
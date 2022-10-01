<div id="panel-{{ $service_block_index }}" class="panel panel-default">
    <div class="panel-body" style="padding-top: 10px;">
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

        {{-- Service id --}}
        <input type="hidden" class="service-id" value="{{ $id }}">

        <div class="row">
            <div class="col-sm-7">
                <div class="table-responsive">
                    <table
                        id="table-service-{{ $service_block_index }}"
                        class="table table-condensed table-th-gray table-text-center table-service">
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
                                {{-- service_block_index_id --}}
                                <input
                                    type="hidden"
                                    id="service_block_index_id"
                                    value="{{ $service_block_index }}">

                                {{-- quote_line_id --}}
                                @if (isset($service_block['quote_line_id']))
                                <input
                                    type="hidden"
                                    id="quote_line_id"
                                    {{-- name="quote_line_id[]" --}}
                                    value="{{ $service_block['quote_line_id'] > 0 ? $service_block['quote_line_id'] : 0 }}">
                                @endif

                                {{-- service_parent_id --}}
                                <input
                                    type="hidden"
                                    id="service_parent_id"
                                    {{-- name="service_parent_id[]" --}}
                                    value="0">

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
                                        id="line_total"
                                        {{-- name="line_total[]" --}}
                                        class="form-control form-control-sm input_number four_decimals line_total_block"
                                        value="0.0000"
                                        readonly
                                        required>
                                </td>

                                {{-- variation_id --}}
                                <input
                                    type="hidden"
                                    id="variation_id"
                                    {{-- name="variation_id[]" --}}
                                    value="{{ $service_block['variation_id'] }}">

                                {{-- warehouse_id --}}
                                <input
                                    type="hidden"
                                    id="warehouse_id"
                                    {{-- name="warehouse_id[]" --}}
                                    value="{{ isset($warehouse_id) ? $warehouse_id : $service_block['warehouse_id'] }}">

                                {{-- quantity --}}
                                <input
                                    type="hidden"
                                    id="quantity"
                                    {{-- name="quantity[]" --}}
                                    class="form-control form-control-sm input_number"
                                    value="1"
                                    required>

                                {{-- tax_percent --}}
                                <input
                                    class="four_decimals"
                                    type="hidden"
                                    id="tax_percent"
                                    {{-- name="tax_percent[]" --}}
                                    value="{{ $service_block['tax_percent'] + 1 }}">
                                
                                {{-- unit_price_exc_tax --}}
                                <input
                                    type="hidden"
                                    class="four_decimals"
                                    id="unit_price_exc_tax"
                                    {{-- name="unit_price_exc_tax[]" --}}
                                    value="{{ round($service_block['price'], 4) }}">
                                
                                {{-- line_tax_amount --}}
                                <input
                                    type="hidden"
                                    class="four_decimals"
                                    id="tax_amount"
                                    {{-- name="tax_amount[]" --}}
                                    value="{{ $service_block['price_inc_tax'] - $service_block['price'] }}">
                                
                                {{-- unit_price_inc_tax --}}
                                <input
                                    type="hidden"
                                    class="four_decimals"
                                    id="unit_price_inc_tax"
                                    {{-- name="unit_price_inc_tax[]" --}}
                                    value="{{ $service_block['price_inc_tax'] }}">
                                
                                {{-- unit_price --}}
                                {{-- <input
                                    type="hidden"
                                    id="unit_price"
                                    name="unit_price[]"
                                    class="form-control form-control-sm input_number four_decimals price_editable"
                                    value="{{ $tax_detail == 1 ? $service_block['unit_price'] : $service_block['price_inc_tax'] }}"> --}}

                                {{-- line_discount_type --}}
                                <input
                                    type="hidden"
                                    class="four_decimals"
                                    id="discount_line_type"
                                    {{-- name="discount_line_type[]" --}}
                                    value="fixed">

                                {{-- line_discount_amount --}}
                                <input
                                    type="hidden"
                                    id="discount_line_amount"
                                    {{-- name="discount_line_amount[]" --}}
                                    class="four_decimals">

                                @php
                                    $tax_amount = round(($service_block['price'] * $service_block['tax_percent']), 4);
                                @endphp

                                {{-- tax_line_amount --}}
                                <input
                                    type='hidden'
                                    id='tax_line_amount'
                                    {{-- name='tax_line_amount[]' --}}
                                    value='{{ $tax_amount ? $tax_amount : 0.00 }}'>
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
                        null,
                        isset($service_block['note']) ? $service_block['note'] : '',
                        ['id' => 'note_line', 'class' => 'form-control', 'rows' => 1]
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
                            'class' => 'form-control',
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
                        class="table table-condensed table-th-gray table-text-center table-spares">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 5%;">#</th>
                                <th>@lang('quote.product_name')</th>
                                <th class="text-center">@lang('order.group_price') @show_tooltip(__('lang_v1.selling_price_group'))</th>
                                <th class="text-center" style="width: 10%;">@lang('lang_v1.quantity')</th>
                                <th class="text-center" style="width: 15%;">@lang('sale.unit_price')</th>
                                <th class="text-center" style="width: 10%;">@lang('order.discount')</th>
                                <th class="text-center" style="width: 15%;">@lang('quote.affected_sales')</th>
                                <th class="text-center" style="width: 7%"><i class="fa fa-cogs"></i></th>
                            </tr>
                        </thead>
                        {{-- <tbody id="list-{{ $service_block_index }}">
                        </tbody> --}}
                        <tbody id="list-{{ $service_block_index }}">
                            @foreach ($service_block['spare_rows'] as $product)
                                @include('order.partials.spare_row')

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
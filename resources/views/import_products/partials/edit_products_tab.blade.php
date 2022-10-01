<div class="table-responsive">
    <table class="table table-striped">
        <tr>
            <th>@lang('lang_v1.col_no')</th>
            <th>@lang('lang_v1.col_name')</th>
            <th>@lang('lang_v1.instruction')</th>
        </tr>

        {{-- SKU --}}
        <tr>
            <td>1</td>
            <td>@lang('product.sku') <br><small class="text-muted">(@lang('lang_v1.required'))</small></td>
            <td>@lang('lang_v1.sku_ins_2') <br><small class="text-muted">(@lang('lang_v1.upper_lower_no_matter'))</small></td>
        </tr>

        {{-- Name --}}
        <tr>
            <td>2</td>
            <td>@lang('product.product_name') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.name_ins').</td>
        </tr>

        {{-- Status --}}
        <tr>
            <td>3</td>
            <td>@lang('accounting.status') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>
                @lang('lang_v1.status_ins')
                <br>
                <strong>@lang('lang_v1.available_options'): @lang('accounting.active'), @lang('accounting.inactive').</strong>
            </td>
        </tr>

        @if (config('app.business') == 'optics')
            {{-- Model --}}
            <tr>
                <td>4</td>
                <td>@lang('product.model') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                <td>@lang('lang_v1.model_ins').</td>
            </tr>

            {{-- Measurement --}}
            <tr>
                <td>5</td>
                <td>@lang('product.measurement') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                <td>@lang('lang_v1.measurement_ins').</td>
            </tr>

            {{-- Material --}}
            <tr>
                <td>6</td>
                <td>@lang('material.clasification_material') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                <td>@lang('lang_v1.material_ins'). <br><small class="text-muted">(@lang('lang_v1.upper_lower_no_matter'))</small></td>
            </tr>
        @endif

        {{-- Category --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 7 @else 4 @endif</td>
            <td>@lang('product.category') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.category_ins'). <br><small class="text-muted">(@lang('lang_v1.upper_lower_no_matter'))</small></td>
        </tr>

        {{-- Subcategory --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 8 @else 5 @endif</td>
            <td>@lang('product.sub_category') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.sub_category_ins'). <br><small class="text-muted">(@lang('lang_v1.upper_lower_no_matter'))</small></td>
        </tr>

        {{-- Barcode type --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 9 @else 6 @endif</td>
            <td>@lang('product.barcode_type') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td
                @lang('lang_v1.barcode_type_ins').
                <br>
                <strong>@lang('lang_v1.barcode_type_ins2'): C39, C128, EAN13, EAN8, UPCA, UPCE</strong>
            </td>
        </tr>

        {{-- Brand --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 10 @else 7 @endif</td>
            <td>@lang('product.brand') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.brand_ins') <br><small class="text-muted">(@lang('lang_v1.upper_lower_no_matter'))</small></td>
        </tr>

        {{-- Measurement unit --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 11 @else 8 @endif</td>
            <td>@lang('product.unit') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.unit_ins') <br><small class="text-muted">({!! __('lang_v1.upper_lower_no_matter') !!})</small></td>
        </tr>

        {{-- Alert quantity --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 12 @else 9 @endif</td>
            <td>@lang('product.alert_quantity') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('product.alert_quantity')</td>
        </tr>

        @if (config('app.business') != 'optics')
            {{-- Provider code --}}
            <tr>
                <td>10</td>
                <td>@lang('product.provider_code') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                <td>@lang('product.provider_code')</td>
            </tr>

            {{-- Drive unit --}}
            <tr>
                <td>11</td>
                <td>@lang('product.additional_description') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                <td>@lang('product.unit_drive')</td>
            </tr>
        @endif

        {{-- Description --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 13 @else 12 @endif</td>
            <td>@lang('product.sales_tax') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.product_description')</td>
        </tr>

        {{-- Has warranty? --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 14 @else 13 @endif</td>
            <td>@lang('product.has_warranty') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>
                <strong>@lang('lang_v1.available_options'): @lang('accounting.yes'), @lang('accounting.not')</strong>
            </td>
        </tr>

        {{-- Warranty --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 15 @else 14 @endif</td>
            <td>@lang('credit.warranty') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.warranty_help_text')</td>
        </tr>

        {{-- Enable IMEI? --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 16 @else 15 @endif</td>
            <td>@lang('product.enable_imei') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.enable_imei_or_sr_no')</td>
        </tr>

        {{-- Weight --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 17 @else 16 @endif</td>
            <td>@lang('lang_v1.weight') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.weight_ins')</td>
        </tr>

        {{-- Sales tax --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 18 @else 17 @endif</td>
            <td>@lang('product.sales_tax') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>
                @lang('product.selling_price_tax_type') <br>
                <strong>@lang('lang_v1.available_options'): @lang('product.inclusive'), @lang('product.exclusive')</strong>
            </td>
        </tr>

        {{-- Applied tax --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 19 @else 18 @endif</td>
            <td>@lang('product.applied_tax') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.applicable_tax_ins')</td>
        </tr>

        {{-- Cost (without tax) --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 20 @else 19 @endif</td>
            <td>@lang('product.cost_without_tax') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>{!! __('lang_v1.purchase_price_exc_tax_ins_min') !!}</td>
        </tr>

        {{-- Sale price --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 21 @else 20 @endif</td>
            <td>@lang('lang_v1.selling_price') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.selling_price_ins')</td>
        </tr>

        @if (config('app.business') == 'optics')
            <tr>
                <td>22</td>
                <td>@lang('lang_v1.image') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                <td>@lang('lang_v1.image_ins')</td>
            </tr>
        @endif
    </table>
</div>
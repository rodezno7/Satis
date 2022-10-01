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

        {{-- Category --}}
        <tr>
            <td>4</td>
            <td>@lang('product.category') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.category_ins'). <br><small class="text-muted">(@lang('lang_v1.upper_lower_no_matter'))</small></td>
        </tr>

        {{-- Subcategory --}}
        <tr>
            <td>5</td>
            <td>@lang('product.sub_category') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.sub_category_ins'). <br><small class="text-muted">(@lang('lang_v1.upper_lower_no_matter'))</small></td>
        </tr>

        @if (config('app.business') == 'optics')
            {{-- AR --}}
            <tr>
                <td>6</td>
                <td>AR <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                <td>
                    @lang('product.anti_reflective'). <br>
                    <strong>@lang('lang_v1.available_options'): green, blue, premium</strong>
                </td>
            </tr>
        @endif

        {{-- Description --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 7 @else 6 @endif</td>
            <td>@lang('product.sales_tax') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.product_description')</td>
        </tr>

        {{-- Has warranty? --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 8 @else 7 @endif</td>
            <td>@lang('product.has_warranty') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>
                <strong>@lang('lang_v1.available_options'): @lang('accounting.yes'), @lang('accounting.not')</strong>
            </td>
        </tr>

        {{-- Warranty --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 9 @else 8 @endif</td>
            <td>@lang('credit.warranty') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.warranty_help_text')</td>
        </tr>

        {{-- Sales tax --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 10 @else 9 @endif</td>
            <td>@lang('product.sales_tax') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>
                @lang('product.selling_price_tax_type') <br>
                <strong>@lang('lang_v1.available_options'): @lang('product.inclusive'), @lang('product.exclusive')</strong>
            </td>
        </tr>

        {{-- Applied tax --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 11 @else 10 @endif</td>
            <td>@lang('product.applied_tax') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.applicable_tax_ins')</td>
        </tr>

        {{-- Cost (without tax) --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 12 @else 11 @endif</td>
            <td>@lang('product.cost_without_tax') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>{!! __('lang_v1.purchase_price_exc_tax_ins_min') !!}</td>
        </tr>

        {{-- Sale price --}}
        <tr>
            <td>@if (config('app.business') == 'optics') 13 @else 12 @endif</td>
            <td>@lang('lang_v1.selling_price') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.selling_price_ins')</td>
        </tr>

        @if (config('app.business') == 'optics')
            <tr>
                <td>14</td>
                <td>@lang('lang_v1.image') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                <td>@lang('lang_v1.image_ins')</td>
            </tr>
        @endif
    </table>
</div>
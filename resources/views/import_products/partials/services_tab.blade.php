<div class="table-responsive">
    <table class="table table-striped">
        <tr>
            <th>@lang('lang_v1.col_no')</th>
            <th>@lang('lang_v1.col_name')</th>
            <th>@lang('lang_v1.instruction')</th>
        </tr>
        <tr>
            <td>1</td>
            <td>@lang('product.product_name') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
            <td>@lang('lang_v1.name_ins')</td>
        </tr>
        <tr>
            <td>2</td>
            <td>@lang('product.sku') <small class="text-muted">(@lang('lang_v1.recommended'))</small></td>
            <td>@lang('lang_v1.sku_ins')</td>
        </tr>
        <tr>
            <td>3</td>
            <td>@lang('accounting.status') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
            <td>
                @lang('lang_v1.status_ins')
                <br>
                <strong>@lang('lang_v1.available_options'): @lang('accounting.active'), @lang('accounting.inactive')</strong>
            </td>
        </tr>
        <tr>
            <td>4</td>
            <td>@lang('product.category') <small class="text-muted">(@lang('lang_v1.recommended'))</small></td>
            <td>@lang('lang_v1.category_ins') <br><small class="text-muted">(@lang('lang_v1.category_ins2'))</small></td>
        </tr>
        <tr>
            <td>5</td>
            <td>@lang('product.sub_category') <small class="text-muted">(@lang('lang_v1.recommended'))</small></td>
            <td>@lang('lang_v1.sub_category_ins') <br><small class="text-muted">({!! __('lang_v1.sub_category_ins2') !!})</small></td>
        </tr>
        <tr>
            <td>6</td>
            <td>@lang('product.additional_description') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.product_description')</td>
        </tr>
        <tr>
            <td>7</td>
            <td>@lang('product.has_warranty') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>
                <strong>@lang('lang_v1.available_options'): @lang('accounting.yes'), @lang('accounting.not')</strong>
            </td>
        </tr>
        <tr>
            <td>8</td>
            <td>@lang('credit.warranty') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
            <td>@lang('lang_v1.warranty_help_text')</td>
        </tr>
        <tr>
            <td>9</td>
            <td>@lang('product.sales_tax') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
            <td>@lang('product.selling_price_tax_type') <br>
                <strong>@lang('lang_v1.available_options'): @lang('product.inclusive'), @lang('product.exclusive')</strong>
            </td>
        </tr>
        <tr>
            <td>10</td>
            <td>@lang('product.applied_tax') <small class="text-muted">(@lang('lang_v1.recommended'))</small></td>
            <td>@lang('lang_v1.applicable_tax_ins') {!! __('lang_v1.applicable_tax_help') !!}</td>
        </tr>
        <tr>
            <td>11</td>
            <td>@lang('product.cost_without_tax')  <br><small class="text-muted">(@lang('lang_v1.recommended'))</small></td>
            <td>{!! __('lang_v1.purchase_price_exc_tax_ins_min') !!}</td>
        </tr>
        <tr>
            <td>12</td>
            <td>@lang('lang_v1.selling_price') <small class="text-muted">(@lang('lang_v1.recommended'))</small></td>
            <td>@lang('lang_v1.selling_price_ins')</td>
        </tr>
        <tr>
            <td>13</td>
            <td>@lang('product.clasification') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
            <td>
                @lang('product.clasification') <br>
                <strong>@lang('lang_v1.available_options'): @lang('product.clasification_product'), @lang('product.clasification_service')</strong>
            </td>
        </tr>
    </table>
</div>
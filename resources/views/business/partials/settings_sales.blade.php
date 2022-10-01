<div class="pos-tab-content">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('default_sales_discount', __('business.default_sales_discount') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-percent"></i>
                    </span>
                    {!! Form::number('default_sales_discount', $business->default_sales_discount, ['class' => 'form-control', 'min' => 0, 'step' => 0.01, 'max' => 100]) !!}
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('default_sales_tax', __('business.default_sales_tax') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::select('default_sales_tax', $tax_rates, $business->default_sales_tax, ['class' => 'form-control select2', 'placeholder' => __('business.default_sales_tax'), 'style' => 'width: 100%;']) !!}
                </div>
            </div>
        </div>
        <!-- <div class="clearfix"></div> -->

        <div class="col-sm-12 hide">
            <div class="form-group">
                {!! Form::label('sell_price_tax', __('business.sell_price_tax') . ':') !!}
                <div class="input-group">
                    <div class="radio">
                        <label>
                            <input type="radio" name="sell_price_tax" value="includes" class="input-icheck" @if ($business->sell_price_tax == 'includes') {{ 'checked' }} @endif> Includes the Sale Tax
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="sell_price_tax" value="excludes" class="input-icheck" @if ($business->sell_price_tax == 'excludes') {{ 'checked' }} @endif>Excludes the Sale Tax (Calculate sale
                            tax on Selling Price provided in Add Purchase)
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('sales_cmsn_agnt', __('lang_v1.sales_commission_agent') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::select('sales_cmsn_agnt', $commission_agent_dropdown, $business->sales_cmsn_agnt, ['class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('item_addition_method', __('lang_v1.sales_item_addition_method') . ':') !!}
                {!! Form::select('item_addition_method', [0 => __('lang_v1.add_item_in_new_row'), 1 => __('lang_v1.increase_item_qty')], $business->item_addition_method, ['class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
            </div>
        </div>

        {{-- 'product_settings[decimals_in_sales]', --}}
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('decimals_in_sales', __('business.decimals_in_pos') . ':') !!}
                @show_tooltip(__('tooltip.decimals_in_pos_tooltip'))

                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-hashtag"></i>
                    </span>
                    {!! Form::number(
                        'product_settings[decimals_in_sales]',
                        $product_settings['decimals_in_sales'],
                        ['class' => 'form-control', 'min' => 2, 'max' => 6, 'step' => 1]
                    ) !!}
                </div>
            </div>
        </div>

        {{-- 'product_settings[decimals_in_fiscal_documents]', --}}
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('decimals_in_fiscal_documents', __('business.decimals_in_fiscal_documents') . ':') !!}
                @show_tooltip(__('tooltip.decimals_in_fiscal_documents_tooltip'))

                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-hashtag"></i>
                    </span>
                    {!! Form::number(
                        'product_settings[decimals_in_fiscal_documents]',
                        $product_settings['decimals_in_fiscal_documents'],
                        ['class' => 'form-control', 'min' => 2, 'max' => 6, 'step' => 1]
                    ) !!}
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox" style="margin-top: 24px;">
                    <label>
                        {!! Form::checkbox('pos_settings[enable_msp]', 1, !empty($pos_settings['enable_msp']) ? true : false, ['class' => 'input-icheck']) !!} {{ __('lang_v1.sale_price_is_minimum_sale_price') }}
                    </label>
                    @show_tooltip(__('lang_v1.minimum_sale_price_help'))
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox" style="margin-top: 24px;">
                    <label>
                        {!! Form::checkbox('annull_sale_expiry', 1, $business->annull_sale_expiry, 
                            ['class' => 'input-icheck', 'id' => 'annull_sale_expiry']) !!} {{ __('business.annull_sale_expiry') }}
                    </label>
                    @show_tooltip(__('business.annull_sale_expiry_tooltip'))
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox" style="margin-top: 24px;">
                  <label>
                    {!! Form::checkbox('pos_settings[show_comment_field]', 1,
                        ! empty($pos_settings['show_comment_field']) ? true : false, ['class' => 'input-icheck']) !!}
                    {{ __('business.show_comment_field') }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox" style="margin-top: 24px;">
                  <label>
                    {!! Form::checkbox('pos_settings[show_order_number_field]', 1,
                        ! empty($pos_settings['show_order_number_field']) ? true : false, ['class' => 'input-icheck']) !!}
                    {{ __('business.show_order_number_field') }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox" style="margin-top: 24px;">
                  <label>
                    {!! Form::checkbox('enable_inline_tax', 1, $business->enable_inline_tax , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_inline_tax' ) }}
                  </label>
                </div>
            </div>
        </div>

        {{-- Partial payment any customer --}}
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox" style="margin-top: 24px;">
                    <label>
                        @php
                            $partial_payment_any_customer = 0;

                            if (! empty($pos_settings['partial_payment_any_customer'])) {
                                $partial_payment_any_customer = $pos_settings['partial_payment_any_customer'];
                            }
                        @endphp

                        {!! Form::checkbox('pos_settings[partial_payment_any_customer]', 1,
                            $partial_payment_any_customer, ['class' => 'input-icheck']) !!}
                        {{ __('business.partial_payment_any_customer') }}
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox" style="margin-top: 24px;">
                  <label>
                    {!! Form::checkbox('show_expenses_on_sales_report', 1, $business->show_expenses_on_sales_report , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.show_expenses_on_sales_report' ) }}
                  </label>
                </div>
            </div>
        </div>

        {{-- sale_settings[no_note_full_payment] --}}
        @if (config('app.business') == 'optics')
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox(
                            'sale_settings[no_note_full_payment]',
                            1,
                            $sale_settings['no_note_full_payment'],
                            ['class' => 'input-icheck']
                        ) !!}
                        {{ __('business.no_note_full_payment') }}
                    </label>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

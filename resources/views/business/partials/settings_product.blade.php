<div class="pos-tab-content">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('sku_prefix', __('business.sku_prefix') . ':') !!}
                 {!! Form::text('sku_prefix', $business->sku_prefix, ['class' => 'form-control text-uppercase']); !!}
            </div>
        </div>

        @if(!config('constants.disable_expiry', true))
        <div class="col-sm-4">
            {!! Form::label('enable_product_expiry', __( 'product.enable_product_expiry' ) . ':') !!}
            @show_tooltip(__('lang_v1.tooltip_enable_expiry'))

            <div class="input-group">
                <span class="input-group-addon">
                    {!! Form::checkbox('enable_product_expiry', 1, $business->enable_product_expiry ); !!} 
                </span>

                <select class="form-control" id="expiry_type"
                    name="expiry_type" 
                    @if(!$business->enable_product_expiry) disabled @endif>
                    <option value="add_expiry" @if($business->expiry_type == 'add_expiry') selected @endif>
                        {{__('lang_v1.add_expiry')}}
                    </option>
                  <option value="add_manufacturing" @if($business->expiry_type == 'add_manufacturing') selected @endif>{{__('lang_v1.add_manufacturing_auto_expiry')}}</option>
                </select>
            </div>
        </div>

        <div class="col-sm-4 @if(!$business->enable_product_expiry) hide @endif" id="on_expiry_div">
            <div class="form-group">
                <div class="multi-input">
                    {!! Form::label('on_product_expiry', __('lang_v1.on_product_expiry') . ':') !!}
                    @show_tooltip(__('lang_v1.tooltip_on_product_expiry'))
                    <br>

                    {!! Form::select('on_product_expiry',     ['keep_selling'=>__('lang_v1.keep_selling'), 'stop_selling'=>__('lang_v1.stop_selling') ], $business->on_product_expiry, ['class' => 'form-control pull-left', 'style' => 'width:60%;']); !!}

                    @php
                        $disabled = '';
                        if($business->on_product_expiry == 'keep_selling'){
                            $disabled = 'disabled';
                        }
                    @endphp

                    {!! Form::number('stop_selling_before', $business->stop_selling_before, ['class' => 'form-control pull-left', 'placeholder' => 'stop n days before', 'style' => 'width:40%;', $disabled, 'required', 'id' => 'stop_selling_before']); !!}
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('enable_brand', 1, $business->enable_brand, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_brand' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('enable_category', 1, $business->enable_category, [ 'class' => 'input-icheck', 'id' => 'enable_category']); !!} {{ __( 'lang_v1.enable_category' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4 enable_sub_category @if($business->enable_category != 1) hide @endif">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('enable_sub_category', 1, $business->enable_sub_category, [ 'class' => 'input-icheck', 'id' => 'enable_sub_category']); !!} {{ __( 'lang_v1.enable_sub_category' ) }}
                  </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('enable_price_tax', 1, $business->enable_price_tax, [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_price_tax' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('default_unit', __('lang_v1.default_unit') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-balance-scale"></i>
                    </span>
                    {!! Form::select('default_unit', $units_dropdown, $business->default_unit, ['class' => 'form-control select2', 'style' => 'width: 100%;' ]); !!}
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('enable_unit_groups', 1, $business->enable_unit_groups, [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_unit_group' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('enable_racks', 1, $business->enable_racks, [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_racks' ) }}
                  </label>
                  @show_tooltip(__('lang_v1.tooltip_enable_racks'))
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('enable_row', 1, $business->enable_row, [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_row' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('enable_position', 1, $business->enable_position, [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_position' ) }}
                  </label>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>

        {{-- Physical inventory record date --}}
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('physical_inventory_record_date', __('lang_v1.physical_inventory_record_date') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::select('physical_inventory_record_date', [
                            'current_date' => __('lang_v1.current_date'),
                            'define_date' => __('lang_v1.define_date'),
                        ], $business->physical_inventory_record_date,
                        ['class' => 'form-control select2', 'style' => 'width: 100%;']
                    ) !!}
                </div>
            </div>
        </div>

        {{-- 'product_settings[decimals_in_fiscal_documents]', --}}
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('product_rotation', __('business.load_in_physical_inventory') . ':') !!}
                @show_tooltip(__('tooltip.load_in_physical_inventory_tooltip'))

                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-hashtag"></i>
                    </span>
                    {!! Form::number(
                        'product_settings[product_rotation]',
                        $product_settings['product_rotation'],
                        ['class' => 'form-control', 'min' => 0, 'step' => 1]
                    ) !!}
                </div>
            </div>
        </div>

        {{-- product_settings[show_stock_without_decimals] --}}
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox" style="margin-top: 24px;">
                    <label>
                        {!! Form::checkbox(
                            'product_settings[show_stock_without_decimals]',
                            1,
                            $product_settings['show_stock_without_decimals'],
                            ['class' => 'input-icheck']
                        ) !!}

                        {{ __('business.show_stock_without_decimals') }}
                    </label>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>

        {{-- 'product_settings[decimals_in_inventories]', --}}
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('decimals_in_inventories', __('business.decimals_in_inventories') . ':') !!}
                @show_tooltip(__('tooltip.decimals_in_inventories_tooltip'))

                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-hashtag"></i>
                    </span>
                    {!! Form::number(
                        'product_settings[decimals_in_inventories]',
                        $product_settings['decimals_in_inventories'],
                        ['class' => 'form-control', 'min' => 2, 'max' => 6, 'step' => 1]
                    ) !!}
                </div>
            </div>
        </div>

        {{-- Show costs or prices --}}
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('show_costs_or_prices', __('lang_v1.show_costs_or_prices') . ':') !!}
                @show_tooltip(__('tooltip.show_costs_or_prices_tooltip'))

                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-usd"></i>
                    </span>
                    {!! Form::select('product_settings[show_costs_or_prices]', [
                            'costs' => __('lang_v1.costs'),
                            'prices' => __('lang_v1.prices'),
                            'none' => __('lang_v1.none'),
                        ], $product_settings['show_costs_or_prices'],
                        ['class' => 'form-control select2', 'style' => 'width: 100%;']
                    ) !!}
                </div>
            </div>
        </div>

        {{-- product_settings[default_products_tax] --}}
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('default_products_tax', __('business.default_products_tax') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-info"></i>
                    </span>
                    {!! Form::select(
                        'product_settings[default_products_tax]',
                        $product_taxes,
                        $product_settings['default_products_tax'],
                        [
                            'class' => 'form-control select2',
                            'placeholder' => __('lang_v1.none'),
                            'style' => 'width: 100%;'
                        ]
                    ) !!}
                </div>
            </div>
        </div>
    </div>
</div>
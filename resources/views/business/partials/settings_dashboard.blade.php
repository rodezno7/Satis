<div class="pos-tab-content">
     <div class="row">
        {{-- stock_expiry_alert_days --}}
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('stock_expiry_alert_days', __('business.view_stock_expiry_alert_for') . ':*') !!}
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-calendar-times-o"></i>
                </span>
                {!! Form::number('stock_expiry_alert_days', $business->stock_expiry_alert_days, ['class' => 'form-control','required']); !!}
                <span class="input-group-addon">
                    @lang('business.days')
                </span>
                </div>
            </div>
        </div>

        {{-- dashboard_settings[subtract_sell_return] --}}
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox" style="margin-top: 24px;">
                    <label>
                        {!! Form::checkbox('dashboard_settings[subtract_sell_return]', 1, $dashboard_settings['subtract_sell_return'],
                            ['class' => 'input-icheck']) !!}
                        {{ __('business.subtract_sell_return') }}
                        @show_tooltip(__('tooltip.subtract_sell_return'))
                    </label>
                </div>
            </div>
        </div>

        {{-- dashboard_settings[box_exc_tax] --}}
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox" style="margin-top: 24px;">
                    <label>
                        {!! Form::checkbox('dashboard_settings[box_exc_tax]', 1, $dashboard_settings['box_exc_tax'],
                            ['class' => 'input-icheck']) !!}
                        {{ __('business.box_exc_tax') }}
                        @show_tooltip(__('tooltip.box_exc_tax'))
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>@lang('lang_v1.charts')</strong>
                    @show_tooltip(__('tooltip.charts_dashboard'))
                </div>
                <div class="panel-body">
                    <div class="row">
                        {{-- dashboard_settings[sales_month] --}}
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                <div class="checkbox" style="margin-top: 24px;">
                                    <label>
                                        {!! Form::checkbox('dashboard_settings[sales_month]', 1,
                                            $dashboard_settings['sales_month'], ['class' => 'input-icheck']) !!}
                                        {{ __('home.sells_last_30_days') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- dashboard_settings[sell_and_product] --}}
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                <div class="checkbox" style="margin-top: 24px;">
                                    <label>
                                        {!! Form::checkbox('dashboard_settings[sell_and_product]', 1,
                                            $dashboard_settings['sell_and_product'], ['class' => 'input-icheck']) !!}
                                        Ventas de las 2 ultimas semanas y productos en tendencia
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- dashboard_settings[sales_year] --}}
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                <div class="checkbox" style="margin-top: 24px;">
                                    <label>
                                        {!! Form::checkbox('dashboard_settings[sales_year]', 1,
                                            $dashboard_settings['sales_year'], ['class' => 'input-icheck']) !!}
                                        {{ __('home.sells_current_fy') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- dashboard_settings[peak_sales_hours_month] --}}
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                <div class="checkbox" style="margin-top: 24px;">
                                    <label>
                                        {!! Form::checkbox('dashboard_settings[peak_sales_hours_month]', 1,
                                            $dashboard_settings['peak_sales_hours_month'], ['class' => 'input-icheck']) !!}
                                        {{ __('home.peak_sales_hours_month') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- dashboard_settings[peak_sales_hours_year] --}}
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                <div class="checkbox" style="margin-top: 24px;">
                                    <label>
                                        {!! Form::checkbox('dashboard_settings[peak_sales_hours_year]', 1,
                                            $dashboard_settings['peak_sales_hours_year'], ['class' => 'input-icheck']) !!}
                                        {{ __('home.peak_sales_hours_year') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- dashboard_settings[purchases_month] --}}
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                <div class="checkbox" style="margin-top: 24px;">
                                    <label>
                                        {!! Form::checkbox('dashboard_settings[purchases_month]', 1,
                                            $dashboard_settings['purchases_month'], ['class' => 'input-icheck']) !!}
                                        {{ __('home.purchases_last_30_days') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        {{-- dashboard_settings[purchases_year] --}}
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                <div class="checkbox" style="margin-top: 24px;">
                                    <label>
                                        {!! Form::checkbox('dashboard_settings[purchases_year]', 1,
                                            $dashboard_settings['purchases_year'], ['class' => 'input-icheck']) !!}
                                        {{ __('home.purchases_current_fy') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- dashboard_settings[stock_month] --}}
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                <div class="checkbox" style="margin-top: 24px;">
                                    <label>
                                        {!! Form::checkbox('dashboard_settings[stock_month]', 1,
                                            $dashboard_settings['stock_month'], ['class' => 'input-icheck']) !!}
                                        {{ __('home.stock_last_30_days') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- dashboard_settings[stock_year] --}}
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                <div class="checkbox" style="margin-top: 24px;">
                                    <label>
                                        {!! Form::checkbox('dashboard_settings[stock_year]', 1,
                                            $dashboard_settings['stock_year'], ['class' => 'input-icheck']) !!}
                                        {{ __('home.stock_current_fy') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
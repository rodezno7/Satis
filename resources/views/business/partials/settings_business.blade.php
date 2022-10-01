<div class="pos-tab-content active">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('name',__('business.business_name') . ':*') !!}
                {!! Form::text('name', $business->name, ['class' => 'form-control', 'required',
                'placeholder' => __('business.business_name')]); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('business_full_name',__('business.business_full_name') . ':*') !!}
                {!! Form::text('business_full_name', $business->business_full_name, ['class' => 'form-control', 'required',
                'placeholder' => __('business.business_full_name')]); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('legal_representative',__('business.legal_representative') . ':*') !!}
                {!! Form::text('legal_representative', $business->legal_representative, ['class' => 'form-control', 'required',
                'placeholder' => __('business.legal_representative')]); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('line_of_business',__('business.line_of_business') . ':*') !!}
                {!! Form::text('line_of_business', $business->line_of_business, ['class' => 'form-control', 'required',
                'placeholder' => __('business.line_of_business')]); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('nit',__('business.nit') . ':*') !!}
                {!! Form::text('nit', $business->nit, ['class' => 'form-control', 'required',
                'placeholder' => __('business.nit')]); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('nrc',__('business.nrc') . ':') !!}
                {!! Form::text('nrc', $business->nrc, ['class' => 'form-control', 'required',
                'placeholder' => __('business.nrc')]); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('mobile',__('business.mobile') . ':*') !!}
                {!! Form::text('mobile', $business_location->mobile, ['class' => 'form-control','',
                'placeholder' => __('business.mobile')]); !!}
            </div>
        </div> 
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('alternate_number',__('business.alternate_number') . ':*') !!}
                {!! Form::text('alternate_number', $business_location->alternate_number, ['class' => 'form-control','',
                'placeholder' => __('business.alternate_number')]); !!}
            </div>
        </div> 
         <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('email',__('business.email') . ':*') !!}
                {!! Form::text('email', $business_location->email, ['class' => 'form-control','',
                'placeholder' => __('business.email')]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('state_id',__('customer.state') . ':*') !!}
                {!! Form::select('state_id', $states, $business->state_id,
                    ['class' => 'form-control select2', 'placeholder' => __('customer.state')]); !!}
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('landmark',__('business.landmark') . ':*') !!}
                {!! Form::text('landmark', $business_location->landmark, ['class' => 'form-control','',
                'placeholder' => __('business.landmark')]); !!}
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('start_date', __('business.start_date') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    @php
                        $start_date = null;
                        if(!empty($business->start_date)){
                            $start_date = date('m/d/Y', strtotime($business->start_date));
                        }
                    @endphp
                    {!! Form::text('start_date', $start_date, ['class' => 'form-control start-date-picker','placeholder' => __('business.start_date'), 'readonly']); !!}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('currency_id', __('business.currency') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-money"></i>
                    </span>
                    {!! Form::select('currency_id', $currencies, $business->currency_id, ['class' => 'form-control select2','placeholder' => __('business.currency'), 'required']); !!}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('currency_symbol_placement', __('lang_v1.currency_symbol_placement') . ':') !!}
                {!! Form::select('currency_symbol_placement', ['before' => __('lang_v1.before_amount'), 'after' => __('lang_v1.after_amount')], $business->currency_symbol_placement, ['class' => 'form-control select2', 'required']); !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('time_zone', __('business.time_zone') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-clock-o"></i>
                    </span>
                    {!! Form::select('time_zone', $timezone_list, $business->time_zone, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('business_logo', __('business.upload_logo') . ':') !!}
                    {!! Form::file('business_logo', ['accept' => 'image/*']); !!}
                    <p class="help-block"><i> @lang('business.logo_help')</i></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('fy_start_month', __('business.fy_start_month') . ':') !!} @show_tooltip(__('tooltip.fy_start_month'))
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::select('fy_start_month', $months, $business->fy_start_month, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('accounting_method', __('business.accounting_method') . ':*') !!}
                @show_tooltip(__('tooltip.accounting_method'))
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calculator"></i>
                    </span>
                    {!! Form::select('accounting_method', $accounting_methods, $business->accounting_method, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('transaction_edit_days', __('business.transaction_edit_days') . ':*') !!}
                @show_tooltip(__('tooltip.transaction_edit_days'))
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-edit"></i>
                    </span>
                    {!! Form::number('transaction_edit_days', $business->transaction_edit_days, ['class' => 'form-control','placeholder' => __('business.transaction_edit_days'), 'required']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('date_format', __('lang_v1.date_format') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::select('date_format', $date_formats, $business->date_format, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('time_format', __('lang_v1.time_format') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-clock-o"></i>
                    </span>
                    {!! Form::select('time_format', [12 => __('lang_v1.12_hour'), 24 => __('lang_v1.24_hour')], $business->time_format, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('default_profit_percent', __('business.default_profit_percent') . ':*') !!} @show_tooltip(__('tooltip.default_profit_percent'))
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-plus-circle"></i>
                    </span>
                    {!! Form::number('default_profit_percent', $business->default_profit_percent, ['class' => 'form-control', 'min' => 0, 
                    'step' => 0.01, 'max' => 100]); !!}
                </div>
            </div>
        </div>
    </div>
</div>
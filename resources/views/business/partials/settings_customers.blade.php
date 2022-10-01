<div class="pos-tab-content">
	<div class="row">
		{{-- customer_settings[nit_in_general_info] --}}
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('customer_settings[nit_in_general_info]', 1, $customer_settings['nit_in_general_info'],
                            ['class' => 'input-icheck']) !!}
                        {{ __('business.nit_in_general_info') }}
                    </label>
                </div>
            </div>
        </div>

        {{-- account_statement_legend --}}
        <div class="col-sm-12">
            <div class="form-group">
                <label>{{ __('business.account_statement_legend') }}:</label>
                {!! Form::text('account_statement_legend', $business->account_statement_legend,
                    ['class' => 'form-control']); !!}
            </div>
        </div>
	</div>
</div>
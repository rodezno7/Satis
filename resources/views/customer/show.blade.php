<div class="modal-dialog modal-dialog-centered" role="document" style="width: 90%">    
    <div class="modal-content" style="border-radius: 20px;">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h3>@lang('customer.general_data')</h3>
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.name')</label>
                        <span>{{ $customer->name }}</span>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.business_name')</label>
                        <span>{{ $customer->business_name }}</span>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.email')</label>
                        <span>{{ $customer->email }}</span>
                    </div>
                </div>
                
            </div>

            <div class="row">

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.phone')</label>
                        <span>{{ $customer->telphone }}</span>
                    </div>
                </div>


                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.dui')</label>
                        <span>{{ $customer->dni }}</span>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.address')</label>
                        <span>{{ $customer->address }}</span>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.country')</label>
                        <span>{{ $customer->country_value }}</span>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.state')</label>
                        <span>{{ $customer->state_value }}</span>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.city')</label>
                        <span>{{ $customer->city_value }}</span>
                    </div>
                </div>


            </div>

            <div class="row">

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.business_type')</label>
                        <span>{{ $customer->business_type_value }}</span>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.customer_portfolio')</label>
                        <span>{{ $customer->customer_portfolio_value }}</span>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.customer_group')</label>
                        <span>{{ $customer->customer_group_value }}</span>
                    </div>
                </div>


            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <hr>
                </div>
            </div>
            <div class="row">

                @if($customer->is_taxpayer == 1)
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">

                    <h3>@lang('customer.taxpayer_data')</h3>
                    
                </div>               
                @endif
            </div>

            @if($customer->is_taxpayer == 1)
            <div class="row" id="div_taxpayer">

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.reg_number')</label>
                        <span>{{ $customer->reg_number }}</span>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.tax_number')</label>
                        <span>{{ $customer->tax_number }}</span>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.business_line')</label>
                        <span>{{ $customer->business_line }}</span>
                    </div>
                </div>
            </div>            
            @endif


            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <hr>
                </div>
            </div>



            

            <div class="row">
                @if($customer->allowed_credit == 1)
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">

                    <h3>@lang('customer.credit_data')</h3>
                    
                </div>
                @endif
            </div>

            @if($customer->allowed_credit == 1)

            <div class="row" id="div_credit">

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.opening_balance')</label>
                        <span>{{ number_format($customer->opening_balance, 2) }}</span>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.credit_limit')</label>
                        <span>{{ number_format($customer->credit_limit, 2) }}</span>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.credit_balance')</label>
                        <span>{{ number_format($customer->credit_balance, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>@lang('customer.payment_terms')</label>
                        <span>{{ $customer->payment_terms_value }}</span>
                    </div>
                </div>
            </div>
            @endif                

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-view-customer">@lang('messages.close')</button>
        </div>
    </div>
    
</div>
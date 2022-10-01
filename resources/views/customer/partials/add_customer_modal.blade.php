<div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <form id="form-add-customer">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3>@lang('customer.add_customer')</h3>
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('customer.info_g')
                        <div class="panel-tools pull-right">
                            <button type="button" class="btn btn-panel-tool"  data-toggle="collapse" data-target="#general-information-fields-box" id="btn-collapse-gi">
                                <i class="fa fa-minus" id="create-icon-collapsed-gi"></i>
                            </button>
                        </div>
                    </div>
                    <div class="panel-body collapse in" id="general-information-fields-box" aria-expanded="true">
                        
                        <div class="row">
                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.name')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        <input type="text" name="name" id="name" class="form-control"
                                            aria-label="Left Align" placeholder="@lang('customer.name')"
                                            value="{{ $customer_name }}">
                                    </div>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.dui')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-list-alt"></i></span>
                                        <input type="text" name="dni" id="dni" class="form-control"
                                            placeholder="@lang('customer.dui')">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.email')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-envelope"></i></span>
                                        <input type="text" name="email" id="email" class="form-control"
                                            placeholder="@lang('customer.email')">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- direccion y Pais --}}
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.phone')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-earphone"></i></span>
                                        <input type="text" name="telphone" id="telphone"
                                            class="form-control input_number" placeholder="@lang('customer.phone')">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                <div class="form-group">
                                    <label>@lang('customer.address')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-tags"></i></span>
                                        <input type="text" name="address" id="address" class="form-control"
                                            placeholder="@lang('customer.address')">
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.country')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class=" glyphicon glyphicon-flag"></i></span>
                                        {!! Form::select('country_id', $countries, '', ['class' => 'select2', 'id' => 'country_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- latitude, etc --}}
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.state')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-link"></i></span>
                                        {!! Form::select('state_id', [], '', ['class' => 'select2', 'id' => 'state_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.city')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-link"></i></span>
                                        {!! Form::select('city_id', [], '', ['class' => 'select2', 'id' => 'city_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.latitude')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker"></i></span>
                                        <input type="text" name="latitude" id="latitude"
                                            class="form-control input_number" placeholder="@lang('customer.latitude')">
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.length')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker"></i></span>
                                        <input type="text" name="length" id="length" class="form-control input_number"
                                            placeholder="@lang('customer.length')">
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            @if($business_receivable_type == "customer")
                                <input type="hidden" value="{{ $main_customer_account }}" id="main_account">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('customer.accounting_account')</label>
                                            {!! Form::select("accounting_account_id", [], null, ["class" => "form-control select_account", "style" => "width: 100%;", 
                                            "placeholder" => __("customer.accounting_account")]) !!}
                                    </div>
                                </div>
                            @endif   
                            {{-- is_exempt --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="checkbox" style="margin-top: 0;">
                                        <label>
                                            {!! Form::checkbox('is_exempt', 1, false, ['id' => 'is_exempt']) !!}
                                            <strong>@lang('customer.is_exempt')</strong>
                                            <br>
                                            <small>@lang('customer.is_exempt_indication').</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        {{-- Contactos multiples --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <h4><b>@lang('customer.contactMult')</b></h4>
                                <table class="table table-responsive">
                                    <thead>
                                        <button type="button" id="add_reference" class="btn btn-info btn-xs"
                                            title='@lang(' contact.add_contact')'><i class="fa fa-plus"></i>
                                        </button>
                                        <tr>
                                            <th>
                                                @lang('contact.name')
                                            </th>
                                            <th>@lang('contact.mobile')</th>
                                            <th>@lang('contact.landline')</th>
                                            <th>@lang('lang_v1.email_address')</th>
                                            <th>@lang('contact.cargo')</th>
                                            <th id="dele" class="hidden">@lang('contact.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody id="referencesItems">
                                        <!--Ingreso un id al tbody-->
                                        <tr>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">@lang('customer.info_f')
                        <div class="panel-tools pull-right">
                            <button type="button" class="btn btn-panel-tool"  data-toggle="collapse" data-target="#fiscal-information-fields-box" id="btn-collapse-fi">
                                <i class="fa fa-plus" id="create-icon-collapsed-fi"></i>
                            </button>
                        </div>
                    </div>
                    <div class="panel-body collapse" id="fiscal-information-fields-box" aria-expanded="false">
                        {{-- Es contrinuyente? --}}
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.is_taxpayer')</label>
                                    {!! Form::checkbox('is_taxpayer', '1', false, ['id' => 'is_taxpayer']) !!}
                                </div>
                            </div>
                        </div>

                        <div id="div_taxpayer" style="display: none">
                            <div  class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('customer.business_name')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                            <input type="text" name="business_name" id="business_name" class="form-control"
                                                placeholder="@lang('customer.business_name')">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('customer.tax_number')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i
                                                    class="glyphicon glyphicon-list-alt"></i></span>
                                            <input type="text" name="tax_number" id="tax_number" class="form-control"
                                                placeholder="@lang('customer.tax_number')">
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('customer.reg_number')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i
                                                    class="glyphicon glyphicon-list-alt"></i></span>
                                            <input type="text" name="reg_number" id="reg_number" class="form-control"
                                                placeholder="@lang('customer.reg_number')">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('customer.business_line')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-cog"></i></span>
                                            <input type="text" name="business_line" id="business_line" class="form-control"
                                                placeholder="@lang('customer.business_line')">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('customer.business_type')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                                            {!! Form::select('business_type_id', $business_types, '', ['class' => 'select2', 'id' => 'business_type_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12" id="taxesP" style="display:none;">
                                    <div class="form-group">
                                        {!! Form::label("tax_group", __("tax_rate.taxes") . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa">T</i>
                                            </span>
                                            <select name="tax_group_id" class="form-control" id="tax_group_id">
                                                <option value="0">{{ __('lang_v1.none') }}</option>
                                                @foreach ($tax_groups as $tg)
                                                <option value="{{ $tg->id }}">{{ $tg->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">@lang('customer.info_c')
                        <div class="panel-tools pull-right">
                            <button type="button" class="btn btn-panel-tool"  data-toggle="collapse" data-target="#commercial-information-fields-box" id="btn-collapse-ci">
                                <i class="fa fa-plus"id="create-icon-collapsed"></i>
                            </button>
                        </div>
                    </div>
                    <div class="panel-body collapse" id="commercial-information-fields-box" aria-expanded="false">
                        {{-- posee credito? --}}
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.allowed_credit')</label>
                                    {!! Form::checkbox('allowed_credit', '1', false, ['id' => 'allowed_credit']) !!}
                                </div>
                            </div>

                        </div>
                        <div class="row" id="div_credit" style="display: none;">

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.opening_balance')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></span>
                                        <input type="text" name="opening_balance" id="opening_balance"
                                            class="form-control input_number"
                                            placeholder="@lang('customer.opening_balance')">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.credit_limit')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></span>
                                        <input type="text" name="credit_limit" id="credit_limit"
                                            class="form-control input_number"
                                            placeholder="@lang('customer.credit_limit')">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.payment_terms')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-align-justify"></i></span>
                                        {!! Form::select('payment_terms_id', $payment_terms, '', ['class' => 'select2', 'id' => 'payment_terms_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                                    </div>

                                </div>
                            </div>
                        </div>
                        {{-- contact_mode_id --}}
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>@lang('customer.contact_mode')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-ok"></i></span>
                                        {!! Form::select('contact_mode_id', $contact_modes, '', ['class' => 'select2', 'id' => 'contact_mode_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                                    </div>

                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.customer_portfolio')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-blackboard"></i></span>
                                        {!! Form::select('customer_portfolio_id', $customer_portfolios, '', ['class' => 'select2', 'id' => 'customer_portfolio_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.customer_group')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        <select name="customer_group_id" id="customer_group_id" class="select2" style="width: 100%;">
                                            <option value="">@lang('messages.please_select')</option>
                                            @foreach ($customer_groups as $item)
                                                <option value="{{$item->id}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-customer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-add-customer">@lang('messages.close')</button>
            </div>
        </div>
    </form>
</div>
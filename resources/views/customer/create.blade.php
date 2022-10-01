<div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <form id="form-add-customer">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3>@lang('customer.add_customer')</h3>
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('customer.general_information')
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
                                            aria-label="Left Align" placeholder="@lang('customer.name')">
                                    </div>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label class="check-foreign">@lang('customer.dui')</label>

                                    {{-- is_foreign --}}
                                    <span class="pull-right">
                                        {!! Form::checkbox('is_foreign', 1, false, ['id' => 'is_foreign']) !!}
                                        @lang('sale.is_foreign')
                                    </span>

                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-list-alt"></i></span>
                                        <input type="text" name="dni" id="dni" class="form-control"
                                            placeholder="@lang('customer.dui')">
                                    </div>
                                </div>
                            </div>

                            @if ($nit_in_general_info)
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
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
                            @else
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
                            @endif
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

                            @if ($nit_in_general_info)
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
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
                            @endif

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

                            @if (! $nit_in_general_info)
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.country')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class=" glyphicon glyphicon-flag"></i></span>
                                        {!! Form::select('country_id', $countries, '', ['class' => 'select2', 'id' => 'country_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- latitude, etc --}}
                        <div class="row">
                            @if ($nit_in_general_info)
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.country')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class=" glyphicon glyphicon-flag"></i></span>
                                        {!! Form::select('country_id', $countries, '', ['class' => 'select2', 'id' => 'country_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                                    </div>
                                </div>
                            </div>
                            @endif

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

                            @if (! $nit_in_general_info)
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
                            @endif
                        </div>

                        <div class="row">
                            @if ($nit_in_general_info)
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
                            @endif

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

                        {{-- Multiple contacts --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <h4>
                                        <button
                                            type="button"
                                            onclick="addReference()"
                                            class="btn btn-info"
                                            title="@lang('contact.add_contact')"
                                            style="padding: 5px 8px; margin-right: 5px; margin-top: -2px;">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                        <b>@lang('customer.contactMult')</b>
                                    </h4>
                                    <table class="table table-responsive table-condensed table-text-center" style="font-size: inherit;">
                                        <thead>
                                            <tr class="active">
                                                <th width="35%">@lang('contact.name')</th>
                                                <th class="text-center" width="15%">@lang('contact.mobile')</th>
                                                <th class="text-center" width="15%">@lang('contact.landline')</th>
                                                <th class="text-center" width="20%">@lang('lang_v1.email_address')</th>
                                                <th class="text-center" width="15%">@lang('customer.charge')</th>
                                                <th id="dele">&nbsp;</th>
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

                        {{-- Customer vehicles --}}
                        @if (config('app.business') == 'workshop')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <h4>
                                        <button
                                            id="btn-add-vehicle"
                                            type="button"
                                            class="btn btn-info"
                                            title="@lang('customer.add_vehicles')"
                                            style="padding: 5px 8px; margin-right: 5px; margin-top: -2px;">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                        <b>@lang('customer.add_vehicles')</b>
                                    </h4>

                                    <table id="customer-vehicles" class="table table-responsive table-condensed table-text-center" style="font-size: inherit;">
                                        <thead>
                                            <tr class="active">
                                                <th class="text-center" width="9%">@lang('customer.license_plate')</th>
                                                <th class="text-center" width="11%">@lang('brand.brand')</th>
                                                <th class="text-center" width="15%">@lang('card_pos.model')</th>
                                                <th class="text-center" width="7%">@lang('accounting.year')</th>
                                                <th class="text-center" width="9%">@lang('crm.color')</th>
                                                <th width="22%">@lang('customer.responsible_vehicle')</th>
                                                <th class="text-center" width="9%">@lang('customer.engine_num')</th>
                                                <th class="text-center" width="9%">@lang('customer.vin_chassis')</th>
                                                <th class="text-center" width="9%">@lang('customer.mi_km')</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>

                                    <input type="hidden" id="row_count_veh" value="0">
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">@lang('customer.fiscal_information')
                        <div class="panel-tools pull-right">
                            <button type="button" class="btn btn-panel-tool"  data-toggle="collapse" data-target="#fiscal-information-fields-box" id="btn-collapse-fi">
                                <i class="fa fa-plus" id="create-icon-collapsed-fi"></i>
                            </button>
                        </div>
                    </div>

                    <div class="panel-body collapse" id="fiscal-information-fields-box" aria-expanded="false">
                        <div class="row">
                            {{-- is_taxpayer --}}
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.is_taxpayer')</label>
                                    {!! Form::checkbox('is_taxpayer', '1', false, ['id' => 'is_taxpayer', 'onClick' => 'showTaxPayer()']) !!}
                                </div>
                            </div>

                            {{-- is_gov_institution --}}
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.is_gov_institution')</label>
                                    {!! Form::checkbox('is_gov_institution', '1', false, ['id' => 'is_gov_institution', 'onClick' => 'showGovInstitution()']) !!}
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

                                @if (! $nit_in_general_info)
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
                                @endif

                                <div class="@if ($nit_in_general_info) col-lg-6 col-md-6 col-sm-6 col-xs-12 @else col-lg-3 col-md-3 col-sm-3 col-xs-12 @endif no-gov-institution">
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
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-gov-institution">
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
                                        <label>@lang('lang_v1.size')</label>
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

                <div class="panel panel-default" id="commercial-information-box">
                    <div class="panel-heading">@lang('customer.commercial_information')
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
                                    {!! Form::checkbox('allowed_credit', '1', false, ['id' => 'allowed_credit', 'onClick' => 'showCredit()']) !!}
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
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
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
                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    {!! Form::label('from', __('accounting.from')) !!}
                                    {!! Form::text('from', null, ['class' => 'form-control', 'placeholder' => 'hh:mm:ss']) !!}
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    {!! Form::label('to', __('accounting.to')) !!}
                                    {!! Form::text('to', null, ['class' => 'form-control', 'placeholder' => 'hh:mm:ss']) !!}
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    {!! Form::label('cost', __('product.cost')) !!}
                                    {!! Form::text('cost', null, ['class' => 'form-control input_number', 'placeholder' => __('product.cost')]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-customer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"
                    id="btn-close-modal-add-customer">@lang('messages.close')</button>
            </div>
        </div>
    </form>
</div>

<script>

    $(document).ready(function() {        
        let dui = document.getElementById("dni");
        $(dui).mask("00000000-0");

        let nit = document.getElementById('tax_number');
        $(nit).mask('0000-000000-000-0');

        if (!($("#is_taxpayer").is(":checked"))) {
            $('#reg_number').prop('required', false);
            $("#dni").prop('required', true);
        }
    });

    $('#dni').on('change', function() {
        let valor = $(this).val();
        let route = '/customers/verified_document/'+'dni'+'/'+valor;
        console.log(route);
        $.get(route, function(data, status) {
            console.log(status);
            if (data.success == true) {
                $("#btn-add-customer").prop('disabled', true);
                // $('#msg').css('color', 'red');
                // $('#msg').text(data.msg);
                Swal.fire({
                title: data.msg,
                icon: "error",
                timer: 3000,
                showConfirmButton: true,
                });
            } else {
                $("#btn-add-customer").prop('disabled', false);
                // $('#msg').css('color', 'green');
                // $('#msg').text(data.msg);
                Swal.fire({
                title: data.msg,
                timer:3000,
                icon: "success",
                });
            }
        });
    });

    $('#reg_number').on('change', function() {
        let valor = $(this).val();
        let route = '/customers/verified_document/'+'reg_number'+'/'+valor;
        console.log(route);
        $.get(route, function(data, status) {
            console.log(status);
            if (data.success == true) {
                $("#btn-add-customer").prop('disabled', true);
                // $('#msgR').css('color', 'red');
                // $('#msgR').text(data.msg);
                Swal.fire({
                    title: data.msg,
                    icon: "error",
                    timer: 3000,
                    showConfirmButton: true,
                });
            } else {
                $("#btn-add-customer").prop('disabled', false);
                // $('#msgR').css('color', 'green');
                // $('#msgR').text(data.msg);
                Swal.fire({
                    title: data.msg,
                    timer:3000,
                    icon: "success",
                });
            }
        });
    });

    $('input#tax_number').on('change', function() {
        let tax_number = $(this).val();
        let route = '/customer/verify-if-exists-tax-number';
        if(tax_number != ""){
            $.ajax({
                method: "get",
                url: route,
                data: {'tax_number': tax_number},
                dataType: "json",
                success: function(result) {
                    if (result.success == true) {
                        Swal.fire({ title: result.msg, icon: "success", timer: 4000});
                    }else if(result.error == true){
                        Swal.fire({ title: result.fail, icon: "info", timer: 4000});
                    }
                    else {
                        Swal.fire({ title: result.msg, icon: "error",timer: 4000});
                    }
                }
            });
        }
    });

    $('select#business_type_id').on('change', function() {
        let valor = $(this).val();
        if (valor == 4) {
            $('#taxesP').show();
            $("#tax_group_id option:contains(RETENCIÓN)").prop('selected', true);
            $("#tax_group_id").prop("style", "pointer-events: none;");
            
            $("#is_exempt").prop("checked", false);
        }
        if (valor != 4) {
            $('#taxesP').hide();
            $('#tax_group_id').val('0');
        }
    });

    function showTaxPayer() {
        if ($("#is_taxpayer").is(":checked")) {
            $('#div_taxpayer').show();
            $("#reg_number").val('');
            $("#tax_number").val('');
            $("#business_line").val('');
            $('#msgR').text("");
            $("#dni").prop('required', false);
            $("#btn-add-customer").prop('disabled', false);
            $('#reg_number').prop('required', true);
            $("input#tax_number").prop('required', true);
            setTimeout(function() {
                $('#reg_number').focus();
            }, 800);

            $('.no-gov-institution').css('display', 'block');

            $('#is_gov_institution').prop('checked', false);

        } else {
            $('#div_taxpayer').hide();
            $("#btn-add-customer").prop('disabled', false);
            $("#reg_number").val('');
            $("#tax_number").val('');
            $("#business_line").val('');
            $('#msg').text("");
            $("#dni").prop('required', true);
            $("input#tax_number").prop('required', false);

            $('.no-gov-institution').css('display', 'none');
        }
    }

    function showGovInstitution() {
        if ($("#is_gov_institution").is(":checked")) {
            $('#div_taxpayer').show();
            $("#reg_number").val('');
            $("#tax_number").val('');
            $("#business_line").val('');
            $('#msgR').text("");
            $("#dni").prop('required', false);
            $("#btn-add-customer").prop('disabled', false);
            $('#reg_number').prop('required', false);
            $("input#tax_number").prop('required', true);

            $('.no-gov-institution').css('display', 'none');

            $('#is_taxpayer').prop('checked', false);

        } else {
            $('#div_taxpayer').hide();
            $("#btn-add-customer").prop('disabled', false);
            $("#reg_number").val('');
            $("#tax_number").val('');
            $("#business_line").val('');
            $('#msg').text("");
            $("#dni").prop('required', true);
            $("input#tax_number").prop('required', false);

            $('.no-gov-institution').css('display', 'block');
        }
    }

    $('#btn-collapse-ci').click(function(){
        if ($("#commercial-information-fields-box").hasClass("in")) {            
            $("#create-icon-collapsed").removeClass("fa fa-minus");
            $("#create-icon-collapsed").addClass("fa fa-plus");
        }else{
            $("#create-icon-collapsed").removeClass("fa fa-plus");
            $("#create-icon-collapsed").addClass("fa fa-minus"); 
        }
    });

    $('#btn-collapse-fi').click(function(){
        if ($("#fiscal-information-fields-box").hasClass("in")) {            
            $("#create-icon-collapsed-fi").removeClass("fa fa-minus");
            $("#create-icon-collapsed-fi").addClass("fa fa-plus");
        }else{
            $("#create-icon-collapsed-fi").removeClass("fa fa-plus");
            $("#create-icon-collapsed-fi").addClass("fa fa-minus"); 
        }
    });

    $('#btn-collapse-gi').click(function(){
        if ($("#general-information-fields-box").hasClass("in")) {            
            $("#create-icon-collapsed-gi").removeClass("fa fa-minus");
            $("#create-icon-collapsed-gi").addClass("fa fa-plus");
        }else{
            $("#create-icon-collapsed-gi").removeClass("fa fa-plus");
            $("#create-icon-collapsed-gi").addClass("fa fa-minus"); 
        }
    });

    //hidden input that contain the main_account code
    let main_account = $("#main_account").val();
    main_account = main_account ? main_account : null;

    /** select accounting account to customer */
        $("select.select_account").select2({
            ajax: {
                type: "post",
                url: "/catalogue/get_accounts_for_select2",
                dataType: "json",
                data: function (params) {
                    return {
                        q: params.term,
                        main_account: main_account
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            },
            minimumInputLength: 1,
            escapeMarkup: function (markup) {
                return markup;
            }
        });

</script>
@extends('layouts.app')

@section('title', __('customer.customers'))

<style>
    hr {
        margin-top: 1rem;
        margin-bottom: 1rem;
        border: 2;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    i.edit-glyphicon {
        position: inherit;
        line-height: inherit;
    }

    .select2-container--open {
        z-index: 1061;
    }
</style>

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('customer.edit_customer')</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="boxform_u box-solid_u">
        <div class="box-body">
            {!! Form::open(['url' => action('CustomerController@update' , [$customer->id] ), 'method' => 'POST', 'class' => 'product_form', 'id' => 'form_add_customer', 'files' => true ]) !!}
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('customer.general_information')
                        <div class="panel-tools pull-right">
                            <button type="button" class="btn btn-panel-tool" data-toggle="collapse"
                                data-target="#general-information-fields-box" id="btn-collapse-gi">
                                <i class="fa fa-minus" id="create-icon-collapsed-gi"></i>
                            </button>
                        </div>
                    </div>

                    <div class="panel-body collapse in" id="general-information-fields-box" aria-expanded="true">
                        <div class="row">
                            <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.name')</label> <span class="text-danger">*</span>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        <input type="text" name="name" id="name" class="form-control"
                                            value="{{ $customer->name }}" placeholder="@lang('customer.name')">
                                    </div>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                                    <input type="hidden" name="customer_id" id="customer_id"
                                        value="{{ $customer->id }}">
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="check-foreign">@lang('customer.dui')</label> <span class="text-danger">*</span>

                                    {{-- is_foreign --}}
                                    <span class="pull-right">
                                        {!! Form::checkbox('is_foreign', 1, $customer->is_foreign, ['id' =>
                                        'is_foreign']) !!}
                                        @lang('sale.is_foreign')
                                    </span>

                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-list-alt"></i></span>
                                        <input type="text" name="dni" id="dni" class="form-control"
                                            value="{{ $customer->dni }}" placeholder="@lang('customer.dui')">
                                    </div>
                                </div>
                            </div>

                            @if ($nit_in_general_info)
                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.tax_number')</label> 
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-list-alt"></i></span>
                                        <input type="text" name="tax_number" id="tax_number" class="form-control"
                                            value="{{ $customer->tax_number }}"
                                            placeholder="@lang('customer.tax_number')">
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.email')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-envelope"></i></span>
                                        <input type="text" name="email" id="email" class="form-control"
                                            value="{{ $customer->email }}" placeholder="@lang('customer.email')">
                                    </div>
                                </div>
                            </div>
                            @endif


                            {{-- direccion y pais --}}
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.phone')</label> <span class="text-danger">*</span>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-earphone"></i></span>
                                        <input type="text" name="telphone" id="telphone" class="form-control"
                                            value="{{ $customer->telphone }}" placeholder="@lang('customer.phone')">
                                    </div>
                                </div>
                            </div>

                            @if ($nit_in_general_info)
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.email')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-envelope"></i></span>
                                        <input type="text" name="email" id="email" class="form-control"
                                            value="{{ $customer->email }}" placeholder="@lang('customer.email')">
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-9">
                                <div class="form-group">
                                    <label>@lang('customer.address')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-tags"></i></span>
                                        <input type="text" name="address" id="address" class="form-control"
                                            value="{{ $customer->address }}" placeholder="@lang('customer.address')">
                                    </div>
                                </div>
                            </div>

                            @if (! $nit_in_general_info)
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.country')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class=" glyphicon glyphicon-flag"></i></span>
                                        {!! Form::select('country_id', $countries, $customer->country_id, ['class'
                                        => 'select2', 'id' => 'country_id', 'style' => 'width: 100%;', 'placeholder'
                                        => __('messages.please_select')]) !!}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- latitude,rtc --}}
                        <div class="row">
                            @if ($nit_in_general_info)
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.country')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class=" glyphicon glyphicon-flag"></i></span>
                                        {!! Form::select('country_id', $countries, $customer->country_id, ['class'
                                        => 'select2', 'id' => 'country_id', 'style' => 'width: 100%;', 'placeholder'
                                        => __('messages.please_select')]) !!}
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.state')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-link"></i></span>
                                        {!! Form::select('state_id', $states, $customer->state_id, ['class' =>
                                        'select2', 'id' => 'state_id', 'style' => 'width: 100%;', 'placeholder' =>
                                        __('messages.please_select')]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.city')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-link"></i></span>
                                        {!! Form::select('city_id', $cities, $customer->city_id, ['class' =>
                                        'select2', 'id' => 'city_id', 'style' => 'width: 100%;', 'placeholder' =>
                                        __('messages.please_select')]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.latitude')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-map-marker"></i></span>
                                        <input type="text" name="latitude" id="latitude"
                                            value="{{ $customer->latitude }}" class="form-control"
                                            placeholder="@lang('customer.latitude')">
                                    </div>
                                </div>
                            </div>

                            @if (! $nit_in_general_info)
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.length')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-map-marker"></i></span>
                                        <input type="text" name="length" id="length" value="{{ $customer->length }}"
                                            class="form-control" placeholder="@lang('customer.length')">
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="row">
                            @if ($nit_in_general_info)
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.length')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-map-marker"></i></span>
                                        <input type="text" name="length" id="length" value="{{ $customer->length }}"
                                            class="form-control" placeholder="@lang('customer.length')">
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($business_receivable_type == "customer")
                            <input type="hidden" value="{{ $main_customer_account }}" id="main_account">
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.accounting_account')</label>
                                    {!! Form::select("accounting_account_id", $account_name,
                                    $customer->accounting_account_id, ["class" => "form-control select_account",
                                    "style" => "width: 100%;",
                                    "placeholder" => __("customer.accounting_account")]) !!}
                                </div>
                            </div>
                            @endif

                            {{-- is_exempt --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="checkbox" style="margin-top: 0;">
                                        <label>
                                            {!! Form::checkbox('is_exempt', 1, $customer->is_exempt, ['id' =>
                                            'is_exempt']) !!}
                                            <strong>@lang('customer.is_exempt')</strong>
                                            <br>
                                            <small>@lang('customer.is_exempt_indication').</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Customer vehicles --}}
                        @if (config('app.business') == 'workshop')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <h4>
                                        <button id="btn-add-vehicle" type="button" class="btn btn-info"
                                            title="@lang('customer.add_vehicles')"
                                            style="padding: 5px 8px; margin-right: 5px; margin-top: -2px;">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                        <b>@lang('customer.add_vehicles')</b>
                                    </h4>

                                    <table id="customer-vehicles"
                                        class="table table-responsive table-condensed table-text-center"
                                        style="font-size: inherit;">
                                        <thead>
                                            <tr class="active">
                                                <th class="text-center" width="9%">@lang('customer.license_plate')
                                                </th>
                                                <th class="text-center" width="11%">@lang('brand.brand')</th>
                                                <th class="text-center" width="15%">@lang('card_pos.model')</th>
                                                <th class="text-center" width="7%">@lang('accounting.year')</th>
                                                <th class="text-center" width="9%">@lang('crm.color')</th>
                                                <th width="22%">@lang('customer.responsible_vehicle')</th>
                                                <th class="text-center" width="9%">@lang('customer.engine_num')</th>
                                                <th class="text-center" width="9%">@lang('customer.vin_chassis')
                                                </th>
                                                <th class="text-center" width="9%">@lang('customer.mi_km')</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $row_count = 0; @endphp
                                            @foreach ($vehicles as $vehicle)
                                            <tr>
                                                {{-- license_plate --}}
                                                <td>
                                                    {!! Form::text(
                                                    'vehicles[' . $loop->index . '][license_plate]',
                                                    $vehicle->license_plate,
                                                    ['class' => 'form-control input-sm vehicle_license_plate']
                                                    ) !!}
                                                    {!! Form::hidden('vehicles[' . $loop->index . '][id]',
                                                    $vehicle->id) !!}
                                                </td>
                                                {{-- brand_id --}}
                                                <td>
                                                    {!! Form::select(
                                                    'vehicles[' . $loop->index . '][brand_id]', $brands,
                                                    $vehicle->brand_id,
                                                    ['class' => 'form-control input-sm vehicle_brand_id',
                                                    'placeholder' => __('messages.please_select')]
                                                    ) !!}
                                                </td>
                                                {{-- model --}}
                                                <td>
                                                    {!! Form::text(
                                                    'vehicles[' . $loop->index . '][model]', $vehicle->model,
                                                    ['class' => 'form-control input-sm vehicle_model']
                                                    ) !!}
                                                </td>
                                                {{-- year --}}
                                                <td>
                                                    {!! Form::text(
                                                    'vehicles[' . $loop->index . '][year]', $vehicle->year,
                                                    ['class' => 'form-control input-sm input_number vehicle_year']
                                                    ) !!}
                                                </td>
                                                {{-- color --}}
                                                <td>
                                                    {!! Form::text(
                                                    'vehicles[' . $loop->index . '][color]', $vehicle->color,
                                                    ['class' => 'form-control input-sm vehicle_color']
                                                    ) !!}
                                                </td>
                                                {{-- responsible --}}
                                                <td>
                                                    {!! Form::text(
                                                    'vehicles[' . $loop->index . '][responsible]',
                                                    $vehicle->responsible,
                                                    ['class' => 'form-control input-sm vehicle_responsible']
                                                    ) !!}
                                                </td>
                                                {{-- engine_number --}}
                                                <td>
                                                    {!! Form::text(
                                                    'vehicles[' . $loop->index . '][engine_number]',
                                                    $vehicle->engine_number,
                                                    ['class' => 'form-control input-sm vehicle_engine_number']
                                                    ) !!}
                                                </td>
                                                {{-- vin_chassis --}}
                                                <td>
                                                    {!! Form::text(
                                                    'vehicles[' . $loop->index . '][vin_chassis]',
                                                    $vehicle->vin_chassis,
                                                    ['class' => 'form-control input-sm vehicle_vin_chassis']
                                                    ) !!}
                                                </td>
                                                {{-- mi_km --}}
                                                <td>
                                                    {!! Form::text(
                                                    'vehicles[' . $loop->index . '][mi_km]', $vehicle->mi_km,
                                                    ['class' => 'form-control input-sm vehicle_mi_km']
                                                    ) !!}
                                                </td>
                                                {{-- Remove button --}}
                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn btn-danger btn-xs remove_vehicle_row"
                                                        title="{{ __('lang_v1.remove') }}">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @php $row_count++; @endphp
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <input type="hidden" id="row_count_veh" value="{{ $row_count }}">
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">@lang('customer.fiscal_information')
                        <div class="panel-tools pull-right">
                            <button type="button" class="btn btn-panel-tool" data-toggle="collapse"
                                data-target="#fiscal-information-fields-box" id="btn-collapse-fi">
                                <i class="fa fa-minus" id="create-icon-collapsed-fi"></i>
                            </button>
                        </div>
                    </div>

                    <div class="panel-body collapse in" id="fiscal-information-fields-box" aria-expanded="true">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    @php
                                    $check = $customer->is_taxpayer == 1 ? 'checked' : '';
                                    @endphp
                                    <label>@lang('customer.is_taxpayer')</label>
                                    <input type="checkbox" name="is_taxpayer" id="is_taxpayer" onclick="showTaxPayer()"
                                        {{ $check }}>
                                </div>
                            </div>

                            {{-- is_gov_institution --}}
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    @php
                                    $check_gov = $customer->is_taxpayer == 2 ? 'checked' : '';
                                    @endphp
                                    <label>@lang('customer.is_gov_institution')</label>
                                    <input type="checkbox" name="is_gov_institution" id="is_gov_institution"
                                        onclick="showGovInstitution()" {{ $check_gov }}>
                                </div>
                            </div>
                        </div>

                        @php
                        $display = $customer->is_taxpayer ? '' : 'none';
                        @endphp
                        <div id="div_taxpayer" style="display: {{ $display }}">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('customer.business_name')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i
                                                    class="glyphicon glyphicon-user"></i></span>
                                            <input type="text" name="business_name" id="business_name"
                                                value="{{ $customer->business_name }}" class="form-control"
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
                                                value="{{ $customer->tax_number }}"
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
                                                value="{{ $customer->reg_number }}"
                                                placeholder="@lang('customer.reg_number')">
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-gov-institution">
                                    <div class="form-group">
                                        <label>@lang('customer.business_line')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i
                                                    class="glyphicon glyphicon-cog"></i></span>
                                            <input type="text" name="business_line" id="business_line"
                                                value="{{ $customer->business_line }}" class="form-control"
                                                placeholder="@lang('customer.business_line')">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('lang_v1.size')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i
                                                    class="glyphicon glyphicon-home"></i></span>
                                            {!! Form::select('business_type_id', $business_types,
                                            $customer->business_type_id, ['class' => 'select2', 'id' =>
                                            'business_type_id', 'style' => 'width: 100%;', 'placeholder' =>
                                            __('messages.please_select')]) !!}
                                        </div>
                                    </div>
                                </div>

                                @php
                                $taxes = $customer->tax_group_id == 5 ? '': 'none';
                                @endphp
                                <div class="col-md-3" id="taxesP" style="display: {{$taxes}}">
                                    <div class="form-group">
                                        {!! Form::label('tax_group', __('tax_rate.taxes') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa">T</i>
                                            </span>
                                            <select name="tax_group_id" class="form-control select2"
                                                style="width: 100%;" id="tax_group_id_e">
                                                @if ($customer->tax_group_id == null)
                                                <option selected value="0">@lang('lang_v1.none')</option>
                                                @else
                                                <option value="0">@lang('lang_v1.none')</option>
                                                @endif
                                                @foreach ($tax_groups as $tg)
                                                @if ($tg->id == $customer->tax_group_id)
                                                <option value="{{ $tg->id }}" selected>{{ $tg->name }}
                                                </option>
                                                @else
                                                <option value="{{ $tg->id }}">{{ $tg->name }}</option>
                                                @endif
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
                            <button type="button" class="btn btn-panel-tool" data-toggle="collapse"
                                data-target="#commercial-information-fields-box" id="btn-collapse-ci">
                                <i class="fa fa-minus" id="create-icon-collapsed"></i>
                            </button>
                        </div>
                    </div>
                    <div class="panel-body collapse in" id="commercial-information-fields-box" aria-expanded="true">
                        {{-- posee credito? --}}
                        <div class="row">
                            @php
                            $check2 = $customer->allowed_credit == 1 ? 'checked' : '';
                            @endphp
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.allowed_credit')</label>
                                    {{-- {!! Form::checkbox('allowed_credit', '1', false, ['id' => 'allowed_credit',
                                    'onClick' => 'showCredit()', 'checked']) !!} --}}
                                    <input type="checkbox" name="allowed_credit" id="allowed_credit"
                                        onclick="showCredit()" {{ $check2 }}>
                                </div>
                            </div>
                        </div>

                        @php
                        $display2 = $customer->allowed_credit == 1 ? '' : 'none';
                        @endphp
                        <div class="row" id="div_credit" style="display: {{ $display2 }}">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.opening_balance')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></span>
                                        <input type="text" name="opening_balance" id="opening_balance"
                                            class="form-control input_number" value="{{ $customer->opening_balance }}"
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
                                            class="form-control input_number" value="{{ $customer->credit_limit }}"
                                            placeholder="@lang('customer.credit_limit')">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.payment_terms')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-align-justify"></i></span>
                                        {!! Form::select('payment_terms_id', $payment_terms,
                                        $customer->payment_terms_id, ['class' => 'select2', 'id' =>
                                        'payment_terms_id', 'style' => 'width: 100%;', 'placeholder' =>
                                        __('messages.please_select')]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{-- contact_mode_id --}}
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.contact_mode')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-ok"></i></span>
                                        {!! Form::select('contact_mode_id', $contact_modes,
                                        $customer->contact_mode_id, ['class' => 'select2', 'id' =>
                                        'contact_mode_id', 'style' => 'width: 100%;', 'placeholder' =>
                                        __('messages.please_select')]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.customer_portfolio')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-blackboard"></i></span>
                                        {!! Form::select('customer_portfolio_id', $customer_portfolios,
                                        $customer->customer_portfolio_id, ['class' => 'select2', 'id' =>
                                        'customer_portfolio_id', 'style' => 'width: 100%;', 'placeholder' =>
                                        __('messages.please_select')]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.customer_group')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        {!! Form::select('group_id', $customer_groups, $customer->customer_group_id,
                                        ['class' => 'select2', 'id' => 'customer_portfolio_id', 'style' => 'width:
                                        100%;', 'placeholder' => __('messages.please_select')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    {!! Form::label('from', __('accounting.from')) !!}
                                    {!! Form::text('from', $customer->from, ['class' => 'form-control',
                                    'placeholder' => 'hh:mm:ss']) !!}
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    {!! Form::label('to', __('accounting.to')) !!}
                                    {!! Form::text('to', $customer->to, ['class' => 'form-control', 'placeholder' =>
                                    'hh:mm:ss']) !!}
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    {!! Form::label('cost', __('product.cost')) !!}
                                    {!! Form::text('cost', $customer->cost, ['class' => 'form-control input_number',
                                    'placeholder' => __('product.cost')]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-right">

                    <div class="btn-group">
                        <div class="btn-group dropleft" role="group">
                            <button type="submit" value="submit" class="btn btn-primary submit_product_edit_form"
                                id="btn-edit-customer">@lang('messages.update')</button>
                        </div>
                    </div>
                    <a href="{!!URL::to('/customers')!!}">
                        <button id="btn-close-modal-edit-customer" type="button"
                            class="btn btn-danger">@lang('messages.cancel')</button>
                    </a>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</section>
@endsection

@section('javascript')
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

        $('.remove-item').click(function(e) {
            Swal.fire({
                title: LANG.sure,
                text: '{{ __('messages.delete_content') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('messages.accept') }}",
                cancelButtonText: "{{ __('messages.cancel') }}"
            }).then((willDelete) => {
                if (willDelete.isConfirmed) {
                    $(this).parent('td').parent('tr').slideDown(300, function() {
                        $(this).remove(); //En accion elimino el contacto de la Tabla
                    });
                    console.log("eliminado");
                } else {
                    console.log('fue salvador');
                }
            });
        });
    });

    $(document).on('change', '#country_id', function(e) {
      id = $("#country_id").val();
      if (id) {
        getStatesByCountry(id);
      } else {
        $("#state_id").empty();
        $("#state_id").append('<option value="0" disabled selected>@lang('messages.please_select')</option>');
        $("#city_id").empty();
        $("#city_id").append('<option value="0" disabled selected>@lang('messages.please_select')</option>');
      }
    });

    $(document).on('change', '#state_id', function(e) {
      id = $("#state_id").val();
      if (id) {
        getCitiesByState(id);
      } else {
        $("#city_id").empty();
        $("#city_id").append('<option value="0" disabled selected>@lang('messages.please_select')</option>');
      }
    });


    function getStatesByCountry(id) {
      $("#state_id").empty();
      var route = "/states/getStatesByCountry/" + id;
      $.get(route, function(res) {
        $("#state_id").append('<option value="0" disabled selected>@lang('messages.please_select')</option>');
        $(res).each(function(key, value) {
          $("#state_id").append('<option value="' + value.id + '">' + value.name + '</option>');
        });
      });
    }

    function getCitiesByState(id) {
      $("#city_id").empty();
      var route = "/cities/getCitiesByState/" + id;
      $.get(route, function(res) {
        $("#city_id").append('<option value="0" disabled selected>@lang('messages.please_select')</option>');
        $(res).each(function(key, value) {
          $("#city_id").append('<option value="' + value.id + '">' + value.name + '</option>');
        });
      });
    }

    $('select#business_type_id').on('change', function() {
        let valor = $(this).val();
        if (valor == 4) {
            $('#taxesP').show();
            $("#tax_group_id_e option:contains(RETENCIÓN)").prop('selected', true).trigger('change');
            $("#tax_group_id_e").prop("style", "pointer-events: none;");
                        
            $("#is_exempt").prop("checked", false);
        }
        if (valor != 4) {
            $('#taxesP').hide();
            $('#tax_group_id_e').val('0');
        }
    });

    $('input#tax_number').on('change', function() {
        let tax_number = $(this).val();
        let customer_id = $('input#customer_id').val();
        let route = '/customer/verify-if-exists-tax-number';
        if(tax_number != ""){
            $.ajax({
                method: "get",
                url: route,
                data: {'customer_id': customer_id,'tax_number': tax_number},
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

    $('#dni').on('change', function() {
        let valor = $(this).val();
        let id = $("#customer_id").val();
        let route = '/customers/verified_documentID/' + 'dni' + '/' + valor + '/' + id;
        console.log(route);
        $.get(route, function(data, status) {
            console.log(status);
            if (data.success == true) {
                $("#btn-add-customer").prop('disabled', true);
                // $('#msgEd').css('color', 'red');
                // $('#msgEd').text(data.msg);
                Swal.fire({
                title: data.msg,
                icon: "error",
                timer: 3000,
                showConfirmButton: true,
                });
            } else {
                $("#btn-add-customer").prop('disabled', false);
                // $('#msgEd').css('color', 'green');
                // $('#msgEd').text(data.msg);
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
        let id = $("#customer_id").val();
        let route = '/customers/verified_documentID/' + 'reg_number' + '/' + valor + '/' + id;
        console.log(route);
        $.get(route, function(data, status) {
            console.log(status);
            if (data.success == true) {
                $("#btn-add-customer").prop('disabled', true);
                // $('#msgREd').css('color', 'red');
                // $('#msgREd').text(data.msg);
                Swal.fire({
                title: data.msg,
                icon: "error",
                timer: 3000,
                showConfirmButton: true,
                });
            } else {
                $("#btn-add-customer").prop('disabled', false);
                // $('#msgREd').css('color', 'green');
                // $('#msgREd').text(data.msg);
                Swal.fire({
                title: data.msg,
                timer:3000,
                icon: "success",
                });
            }
        });
    });

    function showTaxPayer() {
        if ($("#is_taxpayer").is(":checked")) {
            $('#div_taxpayer').show();
            $("#reg_number").val('');
            $("#tax_number").val('');
            $("#business_line").val('');
            $('#msgREd').text("");
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
            $('#msgEd').text("");
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


    function showCredit() {
      if ($("#allowed_credit").is(":checked")) {
        $('#div_credit').show();
        $("#opening_balance").val('');
        $("#credit_limit").val('');
        $("#payment_terms_id").val('').change();
        setTimeout(function() {
          $('#opening_balance').focus();
        },
        800);
      } else {
        $('#div_credit').hide();
        $("#opening_balance").val('');
        $("#credit_limit").val('');
        $("#payment_terms_id").val('').change();
      }
    }

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
</script>
@endsection
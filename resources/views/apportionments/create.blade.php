@extends('layouts.app')

@section('title', __('apportionment.apportionments'))

@section('content')
{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1>@lang('apportionment.apportionments')</h1>
</section>

{{-- Main content --}}
<section class="content">
    @php
        // Number of decimal places to store and use in calculations
        $price_precision = config('app.price_precision');
    @endphp

    {{-- Page level currency setting --}}
    <input type="hidden" id="p_code" value="{{ $currency_details->code }}">
    <input type="hidden" id="p_symbol" value="{{ $currency_details->symbol }}">
    <input type="hidden" id="p_thousand" value="{{ $currency_details->thousand_separator }}">
    <input type="hidden" id="p_decimal" value="{{ $currency_details->decimal_separator }}">

    {{-- Number of decimal places seen in the interface --}}
    <input type="hidden" id="decimals_in_purchases" value="{{ $decimals_in_purchases }}">

    {{-- Number of decimal places to store and use in calculations --}}
    <input type="hidden" id="price_precision" value="{{ config('app.price_precision') }}">

    {!! Form::open(['url' => action('ApportionmentController@store'), 'method' => 'post', 'id' => 'add_apportionment_form', 'files' => true]) !!}

    {{-- Expense type --}}
    {!! Form::hidden('expense_type', 'retaceo', ['id' => 'expense_type']) !!}

    {{-- Submit type --}}
    {!! Form::hidden('submit_type', null, ['id' => 'submit_type']) !!}

    {{-- General info --}}
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang('apportionment.register_apportionment')</h3>
        </div>
        
        <div class="box-body">
            <div class="row">
                {{-- Reference --}}
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('reference', 'DUCA:') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span>
                                    <i class="fa fa fa-hashtag"></i>
                                </span>
                            </div>
                            {!! Form::text('reference', '',
                                ['class' => 'form-control', 'placeholder' => 'DUCA', 'required']) !!}
                        </div>
                    </div>
                </div>

                {{-- Name --}}
                <div class="col-sm-8">
                    <div class="form-group">
                        {!! Form::label('name', __('crm.name') . ':') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span>
                                    <i class="fa fa-file-text-o"></i>
                                </span>
                            </div>
                            {!! Form::text('name', '',
                                ['class' => 'form-control', 'placeholder' => __('crm.name'), 'required']) !!}
                        </div>
                    </div>
                </div>

                {{-- Base --}}
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('base', __('apportionment.base_for_distributing') . ':') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span>
                                    <i class="fa fa-balance-scale"></i>
                                </span>
                            </div>
                            {!! Form::select('distributing_base', [
                                'weight' => __('apportionment.weight'),
                                'value' => __('product.cost')
                            ], '', ['class' => 'form-control select2', 'id' => 'base']) !!}
                        </div>
                    </div>
                </div>

                {{-- VAT --}}
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('base', __('purchase.vat_amount') . ':') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span>
                                    <i class="fa fa-money"></i>
                                </span>
                            </div>
                            {!! Form::text(
                                'vat_amount', 
                                number_format(0, 4, $currency_details->decimal_separator, $currency_details->thousand_separator),
                                ['class' => 'form-control input_number', 'id' => 'general_vat']
                            ) !!}
                        </div>
                    </div>
                </div>

                {{-- Apportionment date --}}
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('apportionment_date', __('accounting.date') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text(
                                'apportionment_date',
                                @format_date('now'),
                                ['class' => 'form-control', 'readonly', 'required']
                            ) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Purchases --}}
        <div class="col-sm-6">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">@lang('accounting.purchases')</h3>
                </div>
                
                <div class="box-body">
                    {{-- Search bar --}}
                    <div class="row">
                        <div class="col-sm-8 col-sm-offset-2">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-search"></i>
                                    </span>
                                    {!! Form::text('search_purchase', null, [
                                        'class' => 'form-control',
                                        'id' => 'search_purchase',
                                        'placeholder' => __('apportionment.add_purchase')
                                    ]); !!}
                                </div>
                            </div>
                        </div>
                    </div>
        
                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed table-th-gray table-text-center" id="purchases_table" width="100%">
                            <thead>
                                <tr>
                                    <th width="20%">
                                        @lang('accounting.reference')
                                    </th>
                                    <th width="50%">
                                        @lang('purchase.supplier')
                                    </th>
                                    <th width="20%">
                                        @lang('accounting.amount')
                                    </th>
                                    <th class="text-center" width="10%">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr class="active">
                                    <td class="text-center" colspan="2">
                                        <strong>@lang('purchase.total')</strong>
                                    </th>
                                    <td class="text-center">
                                        <strong><span id="spn_purchase_total">0.0000</span></strong>
                                        {!! Form::hidden('purchase_total', 0, ['id' => 'purchase_total']) !!}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <input type="hidden" id="row_count_p" value="0">
                </div>
            </div>
        </div>

        {{-- Import expenses --}}
        <div class="col-sm-6">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">@lang('import_expense.import_expenses')</h3>
                </div>
                
                <div class="box-body">
                    {{-- Search bar --}}
                    <div class="row">
                        <div class="col-sm-8 col-sm-offset-2">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-search"></i>
                                    </span>
                                    {!! Form::text('search_import_expense', null, [
                                        'class' => 'form-control',
                                        'id' => 'search_import_expense',
                                        'placeholder' => __('apportionment.add_import_expense')
                                    ]); !!}
                                </div>
                            </div>
                        </div>
                    </div>
        
                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed table-th-gray table-text-center" id="import_expenses_table" width="100%">
                            <thead>
                                <tr>
                                    <th width="70%">
                                        @lang('crm.name')
                                    </th>
                                    <th class="text-center" width="20%">
                                        @lang('accounting.amount')
                                    </th>
                                    <th class="text-center" width="10%">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr class="active">
                                    <td class="text-center">
                                        <strong>@lang('purchase.total')</strong>
                                    </th>
                                    <td class="text-center">
                                        <strong><span id="spn_import_expense_total">0.0000</span></strong>
                                        {!! Form::hidden('import_expense_total', 0, ['id' => 'import_expense_total']) !!}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <input type="hidden" id="row_count_ie" value="0">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Apportionment --}}
        <div class="col-sm-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">@lang('apportionment.apportionment')</h3>
                </div>

                <div class="box-body">
                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed table-th-gray table-text-center" id="apportionment_table" width="100%">
                            <thead>
                                <tr>
                                    {{-- # --}}
                                    <th class="text-center">
                                        #
                                    </th>
                                    {{-- Product --}}
                                    <th>
                                        @lang('purchase.product')
                                    </th>
                                    {{-- Quantity --}}
                                    <th class="text-center">
                                        @lang('lang_v1.quantity')
                                    </th>
                                    {{-- Weight --}}
                                    <th class="text-center">
                                        @lang('lang_v1.weight')
                                    </th>
                                    {{-- FOB --}}
                                    <th class="text-center">
                                        FOB
                                    </th>
                                    {{-- Total --}}
                                    <th class="text-center">
                                        @lang('accounting.total')
                                    </th>
                                    {{-- Import expenses --}}
                                    <th class="text-center">
                                        @lang('import_expense.import_expenses')
                                    </th>
                                    {{-- Other expenses --}}
                                    <th class="text-center">
                                        @lang('apportionment.other_expenses')
                                    </th>
                                    {{-- CIF --}}
                                    <th class="text-center">
                                        CIF
                                    </th>
                                    {{-- DAI --}}
                                    <th class="text-center" colspan="2">
                                        DAI
                                    </th>
                                    {{-- VAT --}}
                                    <th class="text-center">
                                        @lang('purchase.vat')
                                    </th>
                                    {{-- Total cost --}}
                                    <th class="text-center">
                                        @lang('report.total_cost')
                                    </th>
                                    {{-- Unit cost --}}
                                    <th class="text-center">
                                        @lang('purchase.unit_cost')
                                    </th>
                                    {{-- Remove icon --}}
                                    {{-- <th class="text-center">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </th> --}}
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr class="active">
                                    {{-- # --}}
                                    <td></td>
                                    {{-- Product --}}
                                    <td></td>
                                    {{-- Quantity --}}
                                    <td class="text-right">
                                        <strong><span id="product_total_quantity">0.0000</span></strong>
                                    </td>
                                    {{-- Weight --}}
                                    <td class="text-center">
                                        <strong><span id="spn_product_total_weight">0.0000</span></strong>
                                        <input type="hidden" id="product_total_weight" value="0">
                                    </td>
                                    {{-- FOB --}}
                                    <td class="text-center">
                                        <strong><span id="product_total_fob">0.0000</span></strong>
                                    </td>
                                    {{-- Total --}}
                                    <td class="text-right">
                                        <strong><span id="spn_product_total_total">0.0000</span></strong>
                                        <input type="hidden" id="product_total_total" value="0">
                                    </td>
                                    {{-- Import expenses --}}
                                    <td class="text-right">
                                        <strong><span id="product_total_import_expenses">0.0000</span></strong>
                                    </td>
                                    {{-- Other expenses --}}
                                    <td class="text-right">
                                        <strong><span id="product_total_other_expenses">0.0000</span></strong>
                                    </td>
                                    {{-- CIF --}}
                                    <td class="text-right">
                                        <strong><span id="product_total_cif">0.0000</span></strong>
                                    </td>
                                    {{-- DAI percent --}}
                                    <td></td>
                                    {{-- DAI amount --}}
                                    <td class="text-right">
                                        <strong><span id="product_total_dai_amount">0.0000</span></strong>
                                    </td>
                                    {{-- VAT --}}
                                    <td class="text-right">
                                        <strong><span id="product_total_vat">0.0000</span></strong>
                                    </td>
                                    {{-- Total cost --}}
                                    <td class="text-right">
                                        <strong><span id="product_total_total_cost">0.0000</span></strong>
                                    </td>
                                    {{-- Unit cost --}}
                                    <td></td>
                                    {{-- Remove icon --}}
                                    {{-- <td></td> --}}
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <input type="hidden" id="row_count_pr" value="0">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Submit buttons --}}
        <div class="col-sm-12">
            <div class="box">
                <div class="box-body">
                    <div class="pull-right">
                        <button type="submit" value="save" class="btn btn-primary submit-apportionment">
                            @lang('messages.save')
                        </button>
                        <button type="submit" value="save_and_process" class="btn btn-success submit-apportionment">
                            @lang('messages.save_and_process')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
</section>
@endsection

@section('javascript')
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
@endsection
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

    {!! Form::open(['url' => action('ApportionmentController@update', [$apportionment->id]), 'method' => 'put', 'id' => 'edit_apportionment_form', 'files' => true]) !!}

    {{-- Expense type --}}
    {!! Form::hidden('expense_type', 'retaceo', ['id' => 'expense_type']) !!}

    {{-- Submit type --}}
    {!! Form::hidden('submit_type', null, ['id' => 'submit_type']) !!}

    {{-- General info --}}
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang('apportionment.update_apportionment')</h3>
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
                                    <i class="fa fa-hashtag"></i>
                                </span>
                            </div>
                            {!! Form::text('reference', $apportionment->reference,
                                ['class' => 'form-control', 'placeholder' => 'DUCA', 'required', $disabled]) !!}
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
                            {!! Form::text('name', $apportionment->name,
                                ['class' => 'form-control', 'placeholder' => __('crm.name'), 'required', $disabled]) !!}
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
                            ], $apportionment->distributing_base, ['class' => 'form-control select2', 'id' => 'base', $disabled]) !!}
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
                                number_format($apportionment->vat_amount, 4, $currency_details->decimal_separator, $currency_details->thousand_separator),
                                ['class' => 'form-control input_number', 'id' => 'general_vat', $disabled]
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
                                @format_date($apportionment->apportionment_date),
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
                    @if (! $apportionment->is_finished)
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
                    @endif
        
                    {{-- Table --}}
                    @include('purchase.partials.edit_purchase_row')
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
                    @if (! $apportionment->is_finished)
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
                    @endif
        
                    {{-- Table --}}
                    @include('import_expense.partials.edit_import_expense_row', ['has_import_expenses' => 1, 'apportionment' => $apportionment])
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
                    @include('apportionments.partials.edit_product_rows')
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
                        @if ($apportionment->is_finished)
                        <a class="btn btn-block btn-default" href="{{ action('ApportionmentController@index') }}">
                            @lang('messages.go_back')
                        </a>
                        @else
                        <button type="submit" value="save" class="btn btn-primary submit-apportionment">
                            @lang('messages.update')
                        </button>
                        <button type="submit" value="save_and_process" class="btn btn-success submit-apportionment">
                            @lang('messages.update_and_process')
                        </button>
                        @endif
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
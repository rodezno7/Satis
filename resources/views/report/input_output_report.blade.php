@extends('layouts.app')

@section('title', __('report.input_output_report'))

@section('css')
    <style>
        .align-right { text-align: right; }
    </style>
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('report.input_output_report')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-body">
                        {!! Form::open(['id' => 'input_output_form', 'action' => 'ReportController@postInputOutputReport', 'method' => 'post', 'target' => '_blank']) !!}
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('location', __('business.location') . ':') !!}
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-map-marker"></i>
                                        </span>
                                        {!! Form::select('location', $locations, null, ['class' => 'form-control select2 location', 'style' => 'width: 100%;']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('brand', __('brand.brand') . ':') !!} 
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-cube"></i>
                                        </span>
                                        {!! Form::select('brand', $brands, null, ['class' => 'form-control select2 brand',
                                            'style' => 'width: 100%;', 'placeholder' => __('brand.all_brands')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class="input-group">
                                    <button type="button" class="btn btn-primary" id="input_output_date_filter" style="margin-top: 25px;">
                                        <span>
                                        <i class="fa fa-calendar"></i>&nbsp; {{ __('messages.filter_by_date') }}
                                        </span>
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                    {!! Form::hidden("start_date", date('Y-m-d'), ['id' => 'start_date']) !!}
                                    {!! Form::hidden("end_date", date('Y-m-d'), ['id' => 'end_date']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('category', __('category.category') . ':') !!} 
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-cubes"></i>
                                        </span>
                                        {!! Form::select('category', $categories, null, ['class' => 'form-control select2 category', 'style' => 'width: 100%;']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group" style="margin-top: 15px; margin-bottom: 0;">
                                    <label>
                                        {!! Form::checkbox('transactions', 1, null, ['class' => 'input-icheck', 'id' => 'transactions']); !!}
                                        <strong>@lang('report.show_produts_without_transactions')</strong>
                                    </label>
                                    <label>
                                        {!! Form::checkbox('stock', 1, null, ['class' => 'input-icheck', 'id' => 'stock']); !!}
                                        <strong>@lang('report.show_produts_out_stock')</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>@lang('accounting.format')</label>
                                    <select name="report_format" class="form-control">
                                        <option value="excel" selected>Excel</option>
                                        <option value="pdf">PDF</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4" style="margin-top: 25px;">
                                <div class="form-group">
                                    <input type="submit" class="btn btn-success" value="@lang('accounting.generate')">
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="input_output">
                                <thead>
                                    <tr>
                                        <th rowspan="2">@lang('product.sku')</th>
                                        <th rowspan="2">@lang('product.product')</th>
                                        <th rowspan="2">@lang('lang_v1.initial')</th>
                                        <th colspan="4" style="text-align: center;">@lang('lang_v1.inputs')</th>
                                        <th colspan="4" style="text-align: center;">@lang('lang_v1.outputs')</th>
                                        <th rowspan="2">@lang('lang_v1.stock')</th>
                                    </tr>
                                    <tr>
                                        <th>@lang('purchase.purchases')</th>
                                        <th>@lang('lang_v1.transfers')</th>
                                        <th>@lang('stock_adjustment.adjustments')</th>
                                        <th>@lang('lang_v1.returns')</th>
                                        <th>@lang('sale.sells')</th>
                                        <th>@lang('lang_v1.transfers')</th>
                                        <th>@lang('stock_adjustment.adjustments')</th>
                                        <th>@lang('lang_v1.returns')</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray font-14 footer-total text-center">
                                        <td colspan="2"><strong>@lang('report.grand_total')</strong></td>
                                        <td><span class="display_currency" id="footer_total_initial_inventory" data-currency_symbol ="false"></span></td>
                                        <td><span class="display_currency" id="footer_total_purchases" data-currency_symbol ="false"></span></td>
                                        <td><span class="display_currency" id="footer_total_in_transfers" data-currency_symbol ="false"></span></td>
                                        <td><span class="display_currency" id="footer_total_in_adjustments" data-currency_symbol ="false"></span></td>
                                        <td><span class="display_currency" id="footer_total_sell_returns" data-currency_symbol ="false"></span></td>
                                        <td><span class="display_currency" id="footer_total_sells" data-currency_symbol ="false"></span></td>
                                        <td><span class="display_currency" id="footer_total_out_transfers" data-currency_symbol ="false"></span></td>
                                        <td><span class="display_currency" id="footer_total_out_adjustments" data-currency_symbol ="false"></span></td>
                                        <td><span class="display_currency" id="footer_total_purchase_returns" data-currency_symbol ="false"></span></td>
                                        <td><span class="display_currency" id="footer_total_stock" data-currency_symbol ="false"></span></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection

@section('javascript')
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
@endsection
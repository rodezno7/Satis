@extends('layouts.app')

@section('title', __('report.dispatched_products_report'))

@section('css')
    <style>
        .align-right { text-align: right; }
    </style>
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('report.dispatched_products_report')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-body">
                        {!! Form::open(['id' => 'dispatched_products_form', 'action' => 'ReportController@postDispatchedProducts', 'method' => 'post', 'target' => '_blank']) !!}
                        <input type="hidden" id="product_counts" value="{{ $product_counts }}">
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
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {!! Form::label(__("quote.seller_name")) !!}
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-user-circle"></i>
                                        </span>
                                        {!! Form::select("seller", $sellers, null, ["class" => "form-control select2 seller",
                                            'placeholder' => __('sale.all_sellers')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class="input-group">
                                    <button type="button" class="btn btn-primary" id="dispatched_products_date_filter" style="margin-top: 25px;">
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
                                    <label>@lang('accounting.format')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-file-text-o"></i>
                                        </span>
                                        <select class="form-control" name="report_format" id="report_format">
                                            <option value="pdf" selected>PDF</option>
                                            <option value="excel">Excel</option>
                                        </select>
                                    </div>
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
                            <table class="table table-bordered" id="dispatched_products">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">@lang('customer.customer')</th>
                                        <th style="text-align: center;">@lang('customer.seller')</th>
                                        <th style="width: 15%; text-align: center;">@lang('lang_v1.quantity')</th>
                                        <th style="width: 15%; text-align: center;">@lang('lang_v1.weight')</th>
                                        <th style="width: 15%; text-align: center;">@lang('sale.total')</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray font-14 footer-total text-center">
                                        <td colspan="2"><strong>@lang('report.grand_total')</strong></td>
                                        <td><span class="display_currency" id="footer_total_qty" data-currency_symbol ="false" data-precision="1"></span></td>
                                        <td><span class="display_currency" id="footer_total_weight" data-currency_symbol ="false" data-precision="1"></span></td>
                                        <td><span class="display_currency" id="footer_total_final" data-currency_symbol ="true"></span></td>
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
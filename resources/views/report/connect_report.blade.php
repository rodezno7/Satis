@extends('layouts.app')

@section('title', __('report.connect_report'))

@section('css')
    <style>
        .align-right { text-align: right; }
    </style>
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('report.connect_report')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-body">
                        {!! Form::open(['id' => 'connect_report_form', 'action' => 'ReportController@postConnectReport', 'method' => 'post', 'target' => '_blank']) !!}
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
                                    <button type="button" class="btn btn-primary" id="connect_report_date_filter" style="margin-top: 25px;">
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
                                    <select name="report_format" class="form-control">
                                        <option value="excel" selected>Excel</option>
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
                            <table class="table table-bordered" id="connect_report">
                                <thead>
                                    <tr>
                                        <th style="width: 27%; text-align: center;">@lang('customer.customer')</th>
                                        <th style="text-align: center;">@lang('customer.latitude')</th>
                                        <th style="text-align: center;">@lang('customer.length')</th>
                                        <th style="text-align: center;">@lang('accounting.from')</th>
                                        <th style="text-align: center;">@lang('accounting.to')</th>
                                        <th style="text-align: center;">@lang('product.cost')</th>
                                        <th style="text-align: center;">@lang('lang_v1.weight')</th>
                                        <th style="text-align: center;">@lang('product.volume')</th>
                                        <th style="text-align: center;">@lang('product.download_time')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        
                                </tbody>
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
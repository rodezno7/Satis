@extends('layouts.app')

@section('title', __('report.sales_summary_seller_report'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('report.sales_summary_seller_report')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-body">
                        {!! Form::open(['id' => 'form_sales_summary', 'action' => 'ReporterController@postSalesSummarySellerReport', 'method' => 'post', 'target' => '_blank']) !!}
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('transaction_date', __('accounting.from') . ':') !!} <span style="color: red;"><small>*</small></span>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="text" value="{{ @format_date('now') }}" name="start_date" readonly class="form-control start_date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('transaction_date', __('accounting.to') . ':') !!} <span style="color: red;"><small>*</small></span>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="text" value="{{ @format_date('now') }}" name="end_date" readonly class="form-control end_date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {!! Form::label('location', __('business.location') . ':') !!} <span style="color: red;"><small>*</small></span> 
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-user-circle"></i>
                                        </span>
                                        {!! Form::select('location_id', $locations, null, ['class' => 'form-control location_id', 'required']) !!}
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
                                        <option value="pdf" disabled>PDF</option>
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
                            <table class="table table-bordered table-striped" id="sales_summary_by_seller">
                                <thead>
                                    <tr>
                                        <th>@lang('product.sku')</th>
                                        <th>@lang('product.product_name')</th>
                                        <th>@lang('product.category')</th>
                                        <th>@lang('sale.qty')</th> {{-- qty --}}
                                        <th>@lang('sale.sells')</th> {{-- total sales --}}
                                        <th>@lang('sale.seller')</th> {{-- employee --}}
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
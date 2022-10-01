@extends('layouts.app')

@section('title', __('report.list_price_report'))

@section('css')
    <style>
        .align-right { text-align: right; }
    </style>
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('report.list_price_report')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-body">
                        {!! Form::open(['id' => 'list_price_report_form', 'action' => 'ReportController@postListPriceReport', 'method' => 'post', 'target' => '_blank']) !!}
                        <input type="hidden" id="count" value="{{ $count }}">
                        <div class="row">
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
                                    {!! Form::label('category', __('category.category') . ':') !!} 
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-cubes"></i>
                                        </span>
                                        {!! Form::select('category', $categories, null, ['class' => 'form-control select2 category', 'style' => 'width: 100%;']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>@lang('accounting.format')</label>
                                    <select name="report_format" id="report_format" class="form-control">
                                        @can('list_price_report_pdf.view')
                                            <option value="pdf">PDF</option>
                                        @endcan
                                        @can('list_price_report_excel.view')
                                            <option value="excel">Excel</option>
                                        @endcan
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
                            <table class="table table-bordered" id="list_price_report">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; width: 10%;">@lang('product.sku')</th>
                                        <th style="text-align: center;">@lang('product.product')</th>
                                        <th style="text-align: center; width: 20%;">@lang('brand.brand')</th>
                                        <th style="text-align: center; width: 20%;">@lang('category.category')</th>
                                        <th style="text-align: center; width: 20%;">@lang('product.price')</th>
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
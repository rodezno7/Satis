@extends('layouts.app')

@section('title', __('report.price_lists_report'))

@section('css')
    <style>
        .align-right { text-align: right; }
    </style>
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('report.price_lists_report')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-body">
                        {!! Form::open(['action' => 'ReportController@postPriceListsReport', 'method' => 'post', 'target' => '_blank']) !!}
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    {!! Form::label('location', __('business.location') . ':') !!} <span class="text-red">*</span>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-map-marker"></i>
                                        </span>
                                        {!! Form::select('location', $locations, null, ['class' => 'form-control select2', 'style' => 'width: 100%;',
                                            'required', 'placeholder' => __('business.select_location')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    {!! Form::label('category', __('category.category') . ':') !!} 
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-cubes"></i>
                                        </span>
                                        {!! Form::select('category', $categories, null, ['class' => 'form-control select2',
                                            'style' => 'width: 100%;', 'placeholder' => __('category.all_categories')]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    {!! Form::label('brand', __('brand.brand') . ':') !!} 
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-cube"></i>
                                        </span>
                                        {!! Form::select('brand', $brands, null, ['class' => 'form-control select2',
                                            'style' => 'width: 100%;', 'placeholder' => __('brand.all_brands')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6" style="margin-top: 25px;">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-file-excel-o"></i> @lang('accounting.generate')</button>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection
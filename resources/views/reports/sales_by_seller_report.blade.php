@extends('layouts.app')

@section('title', __('report.sales_by_seller_report'))

@section('css')
    <style>
        .align-right { text-align: right; }
    </style>
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('report.sales_by_seller_report')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-body">
                        {!! Form::open(['id' => 'form_sales_by_seller', 'action' => 'ReporterController@postSalesBySellerReport', 'method' => 'post', 'target' => '_blank']) !!}
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
                                        {!! Form::select('location_id', $locations, null, ['class' => 'form-control location_id']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
        
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>@lang('accounting.format')</label>
                                    <select name="report_format" class="form-control">
                                        <option value="pdf" selected>PDF</option>
                                        <option value="excel">Excel</option>
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
                            <table class="table table-bordered table-striped" id="sales_by_seller">
                                <thead>
                                    <tr>
                                        <th>@lang('business.location')</th>
                                        <th>@lang('employees.seller_code')</th>
                                        <th>@lang('employees.seller_name')</th>
                                        <th>@lang('sale.total_no_vat')</th>
                                        <th>@lang('sale.total')</th>
                                    </tr>
                                </thead>
                                <tbody>
    
                                </tbody>
                                <tfoot>
                                <tr class="bg-gray font-17 footer-total text-center">
                                    <td colspan="3"><strong>@lang('report.grand_total')</strong></td>
                                    <td><span class="display_currency" id="footer_total_before_tax" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_total_amount" data-currency_symbol ="true"></span></td>
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
@extends('layouts.app')
@section('title', __('report.expense_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.expense_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary" id="accordion">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseFilter">
                            <i class="fa fa-filter" aria-hidden="true"></i> @lang('report.filters')
                        </a>
                    </h3>
                </div>
                <div id="collapseFilter" class="panel-collapse active collapse in" aria-expanded="true">
                    <div class="box-body">
                        {!! Form::open(['id'=>'form_expense_summary_report', 'action' => 'ReporterController@postExpensePurchaseReport', 'method' => 'post', 'target' => '_blank']) !!}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label("year", __("lang_v1.year")) !!}
                                {!! Form::select("year", $years, null, ["class" => "form-control", "id" => "year"]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label("location", __("business.location")) !!}
                                {!! Form::select("location", $locations, null, ["class" => "form-control", "id" => "location"]) !!}
                            </div>
                        </div>
                        <div class="col-sm-3" style="margin-top: 25px;">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                    @lang('report.export')
                                </button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="expense_summary_report_table">
                            <thead>
                                <tr>
                                    <th>@lang('lang_v1.description')</th>
                                    <th>@lang('report.jan')</th>
                                    <th>@lang('report.feb')</th>
                                    <th>@lang('report.mar')</th>
                                    <th>@lang('report.apr')</th>
                                    <th>@lang('report.may')</th>
                                    <th>@lang('report.jun')</th>
                                    <th>@lang('report.jul')</th>
                                    <th>@lang('report.aug')</th>
                                    <th>@lang('report.sep')</th>
                                    <th>@lang('report.oct')</th>
                                    <th>@lang('report.nov')</th>
                                    <th>@lang('report.dec')</th>
                                    <th>@lang('sale.total')</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="bg-gray font-14 footer-total text-center">
                                    <td><strong>@lang('sale.total')</strong></td>
                                    <td><span class="display_currency" id="footer_jan" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_feb" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_mar" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_apr" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_may" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_jun" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_jul" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_aug" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_sep" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_oct" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_nov" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_dec" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_total" data-currency_symbol ="true"></span></td>
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
@extends('layouts.app')
@section('title', __('report.consumption_report'))

@section('content')

{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1>{{ __('report.consumption_report')}}</h1>
</section>

{{-- Main content --}}
<section class="content">

    {{-- Filters --}}
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
                    {!! Form::open(['url' => action('ReportController@postSalesAndAdjustmentsReport'),
                        'method' => 'post', 'id' => 'sales_n_adjustments_report_filter_form', 'target' => '_blank']) !!}
                        
                        {{-- location_id --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                                {!! Form::select('location_id', $locations, null,
                                    ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'style' => 'width: 100%', 'id' => 'location_id', 'required']); !!}
                            </div>
                        </div>

                        {{-- month --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('month', __('accounting.month') . ':') !!}
                                {!! Form::select('month', $months, null,
                                    ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'style' => 'width: 100%', 'id' => 'month', 'required']); !!}
                            </div>
                        </div>

                        {{-- size --}}
                        <div class="col-sm-3">
                            <label>@lang('accounting.size_font')</label>
                            <select name="size" id="size" class="form-control select2" style="width: 100%;" required>
                                <option value="7">7</option>
                                <option value="8" selected>8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                            </select>
                        </div>

                        {{-- format --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>@lang('accounting.format')</label>
                                <select name="report_type" id="report_type" class="form-control select2" style="width: 100%" required>
                                    <option value="pdf" selected>PDF</option>
                                    <option value="excel">Excel</option>
                                </select>                       
                            </div>
                        </div>
                        
                        {{-- button --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                <button type="submit" id="btn_report" class="btn btn-success">
                                    @lang('accounting.generate')
                                </button>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
              </div>

            </div>
        </div>  
    </div>

    {{-- Datatable --}}
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="sales_n_adjustments_report_table">
                        <thead>
                            <tr>
                                <th style="width: 10%">SKU</th>
                                <th style="width: 40%">@lang('business.product')</th>
                                <th style="width: 10%">@lang('sale.unit_price')</th>
                                <th style="width: 10%">@lang('report.unit_cost')</th>
                                <th style="width: 10%">@lang('report.total_unit_sold')</th>
                                <th style="width: 10%">@lang('report.input_adjustment')</th>
                                <th style="width: 10%">@lang('report.output_adjustment')</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="bg-gray font-17 text-center footer-total">
                                <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                                <td id="footer_total_sold"></td>
                                <td id="footer_total_input_adjustment"></td>
                                <td id="footer_total_output_adjustment"></td>
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
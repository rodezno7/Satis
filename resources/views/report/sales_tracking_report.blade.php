@extends('layouts.app')
@section('title', __('report.sales_tracking_report'))

@section('content')

{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1>{{ __('report.sales_tracking_report')}}</h1>
</section>

{{-- Main content --}}
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
                  {!! Form::open(['id'=>'form_sales_tracking_report', 'action' => 'ReportController@postSalesTrackingReport', 'method' => 'post', 'target' => '_blank']) !!}
                    <div class="row">
                        {{-- customer_id --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('customer_id', __('contact.customer') . ':') !!}
                                {!! Form::select('customer_id', [], null,
                                    ['placeholder' => __('accounting.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'customer_id']); !!}
                            </div>
                        </div>

                        {{-- delivery_type --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('delivery_type', __('order.delivery_type') . ':') !!}
                                {!! Form::select('delivery_type', $delivery_types, null,
                                    ['placeholder' => __('accounting.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'delivery_type']); !!}
                            </div>
                        </div>

                        {{-- invoiced --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('invoiced', __('quote.invoiced') . ':') !!}
                                {!! Form::select('invoiced', ['1' => __('messages.yes'), '0' => __('messages.no')], null,
                                    ['placeholder' => __('accounting.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'invoiced']); !!}
                            </div>
                        </div>

                        {{-- start and end --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group">
                                    <button type="button" class="btn btn-primary" id="date_filter" style="margin-top: 25px;">
                                        <span>
                                            <i class="fa fa-calendar"></i>&nbsp; {{ __('messages.filter_by_date') }}
                                        </span>
                                        <i class="fa fa-caret-down" style="margin-left: 3px;"></i>
                                    </button>
                                </div>
                                {!! Form::hidden('start_date', null, ['id' => 'start_date']) !!}
                                {!! Form::hidden('end_date', null, ['id' => 'end_date']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- employee_id --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('employee_id', __('quote.employee') . ':') !!}
                                {!! Form::select('employee_id', [], null,
                                    ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'employee_id']); !!}
                            </div>
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

                        {{-- size_font --}}
                        <div class="col-sm-3">
                            <label>@lang('accounting.size_font')</label>
                            <select name="size" id="size" class="form-control select2" style="width: 100%;" required>
                                <option value="7">7</option>
                                <option value="8" selected>8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                            </select>
                        </div>

                        {{-- button --}}
                        <div class="col-sm-3" style="margin-top: 25px;">
                            <div class="form-group">
                                <input type="submit" class="btn btn-success" value="@lang('accounting.generate')" id="button_report">
                            </div>
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
                    <table class="table table-bordered table-striped" id="sales_tracking_report_table">
                        <thead>
                            <tr>
                                <th>@lang('order.ref_no')</th>
                                <th>@lang('messages.date')</th>
                                <th>@lang('customer.customer_code')</th>
                                <th>@lang('sale.customer_name')</th>
                                <th>@lang('order.delivery_type')</th>
                                <th>@lang('quote.invoiced')</th>
                                <th>@lang('report.quoted_amount')</th>
                                <th>@lang('report.invoiced_amount')</th>
                                <th>@lang('quote.seller')</th>
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
<script>
    $(document).ready(function() {
        // Date filter
        $('#date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));

                var start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                $('#start_date').val(start_date);
                $('#end_date').val(end_date);

                sales_tracking_report_table.ajax.reload();
            }
        );

        $('#date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#sell_date_filter').html('<i class="fa fa-calendar"></i> {{ __("messages.filter_by_date") }}');
            sales_tracking_report_table.ajax.reload();
        });
    });
</script>
@endsection
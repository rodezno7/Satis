@extends('layouts.app')
@section('title', __('Reporte de ventas perdidas'))

@section('content')

    {{-- Content Header (Page header) --}}
    <section class="content-header">
        <h1>{{ __('Reporte de ventas perdidas') }}</h1>
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
                            {!! Form::open(['id' => 'form_lost_sales_report', 'action' => 'ReportController@postLostSalesReport', 'method' => 'post', 'target' => '_blank']) !!}
                            <div class="row">
                                {{-- employee_id --}}
                                <div class="col-md-3 col-sm-3 col-lg-3 col-xs-12">
                                    <div class="form-group">
                                        {!! Form::label('employee_id', __('quote.employee') . ':') !!}
                                        {!! Form::select('employee_id', [], null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'employee_id']) !!}
                                    </div>
                                </div>

                                {{-- customer_id --}}
                                <div class="col-md-3 col-sm-3 col-lg-3 col-xs-12">
                                    <div class="form-group">
                                        {!! Form::label('customer_id', __('contact.customer') . ':') !!}
                                        {!! Form::select('customer_id', [], null,
                                            ['placeholder' => __('accounting.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'customer_id']); !!}
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3 col-lg-3 col-xs-12">
                                    <div class="form-group">
                                        {!! Form::label('reason', __('quote.reason') . ':') !!}
                                        {!! Form::select('reason_id', $reasons, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'reason_id']) !!}
                                    </div>
                                </div>

                                {{-- start and end --}}
                                <div class="col-md-3 col-sm-3 col-lg-3 col-xs-12">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <button type="button" class="btn btn-primary" id="date_filter"
                                                style="margin-top: 25px; ">
                                                <span>
                                                    <i class="fa fa-calendar"></i>&nbsp;
                                                    {{ __('messages.filter_by_date') }}
                                                </span>
                                                <i class="fa fa-caret-down" style="margin-left: 3px;"></i>
                                            </button>
                                        </div>
                                        {!! Form::hidden('start_date', null, ['id' => 'start_date_lost']) !!}
                                        {!! Form::hidden('end_date', null, ['id' => 'end_date_lost']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{-- format --}}
                                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('accounting.format')</label>
                                        <select name="report_type" id="report_type" class="form-control select2"
                                            style="width: 100%" required>
                                            <option value="pdf" selected>PDF</option>
                                            <option value="excel">Excel</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- size_font --}}
                                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                                    <label>@lang('accounting.size_font')</label>
                                    <select name="size" id="size" class="form-control select2" style="width: 100%;"
                                        required>
                                        <option value="7">7</option>
                                        <option value="8" selected>8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>
                                </div>
                                {{-- button --}}
                                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12" style="margin-top: 25px;">
                                    <div class="form-group">
                                        <input type="submit" class="btn btn-success" value="@lang('accounting.generate')"
                                            id="button_report">
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
                            <table class="table table-bordered table-striped" id="lost_sales_report_table">
                                <thead>
                                    <tr>
                                        <th>@lang('messages.date')</th>
                                        <th>@lang('quote.due_date')</th>
                                        <th>@lang('quote.lost_date')</th>
                                        <th>@lang('quote.seller')</th>
                                        <th>@lang('customer.customer_code')</th>
                                        <th>@lang('contact.customer')</th>
                                        <th>@lang('order.ref_no')</th>
                                        <th>@lang('quote.reason')</th>
                                        <th>@lang('quote.comments')</th>
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
                function(start, end) {
                    $('#date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(
                        moment_date_format));

                    var start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                    $('#start_date_lost').val(start_date);
                    $('#end_date_lost').val(end_date);

                    $('#lost_sales_report_table').DataTable().ajax.reload();
                }
            );
            $('#date_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#sell_date_filter').html(
                    '<i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}');
                $("#lost_sales_report_table").DataTable().ajax.reload();
            });

            // lost sale report
            // Datatable
            $('#lost_sales_report_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [1, 'asc']
                ],
                "ajax": {
                    "url": "/reports/lost-sales",
                    "data": function(d) {
                        d.start_date_lost = $('#start_date_lost').val();
                        d.end_date_lost = $('#end_date_lost').val();
                        d.employee_id = $('#employee_id').val();
                        d.reason_id = $('#reason_id').val();
                        d.customer_id = $("#customer_id").val();
                    }
                },
                columns: [{
                        data: 'quote_date',
                        name: 'quote_date'
                    },
                    {
                        data: 'due_date',
                        name: 'due_date'
                    },
                    {
                        data: 'lost_date',
                        name: 'lost_date'
                    },
                    {
                        data: 'seller_name',
                        name: 'seller_name'
                    },
                    {
                        data: 'customer_id',
                        name: 'customer_id'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'ref_no',
                        name: 'ref_no'
                    },
                    {
                        data: 'reason',
                        name: 'reason'
                    },
                    {
                        data: 'lose_comment',
                        name: 'lose_comment'
                    },

                ],
            });

        });



        // Reload table when changing params
        $('#form_lost_sales_report #employee_id, #form_lost_sales_report #reason_id, #form_lost_sales_report #customer_id')
            .change(function() {
                $("#lost_sales_report_table").DataTable().ajax.reload();
            });
    </script>
@endsection

@extends('layouts.app')

@section('title', __('report.sales_per_seller_report'))

@section('content')

{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1>{{ __('report.sales_per_seller_report')}}</h1>
</section>

{{-- Main content --}}
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default" id="accordion">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-filter" aria-hidden="true"></i> @lang('report.filters')
                    </h3>
                </div>

                <div id="collapseFilter" class="panel-collapse active collapse in" aria-expanded="true">
                    <div class="box-body">
                        {!! Form::open([
                            'id'=>'form_sales_per_seller_report',
                            'action' => 'ReportController@postSalesPerSellerReport',
                            'method' => 'post',
                            'target' => '_blank'
                        ]) !!}

                        <div class="row">
                            {{-- seller --}}
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('location_id', __('customer.seller') . ':') !!}

                                    {!! Form::select('seller', $sellers, null,
                                        ['class' => 'form-control select2', 'placeholder' => __('kardex.all')]) !!}
                                </div>
                            </div>

                            {{-- location_id --}}
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('location_id', __('kardex.location') . ':') !!}
        
                                    @if (is_null($default_location))
                                        {!! Form::select('select_location', $locations, null,
                                            ['class' => 'form-control select2', 'id' => 'select_location']) !!}
            
                                        {!! Form::hidden('location_id', 'all', ['id' => 'location_id']) !!}
                                    @else
                                        {!! Form::select('select_location', $locations, null,
                                            ['class' => 'form-control select2', 'id' => 'location', 'disabled']) !!}
            
                                        {!! Form::hidden('location_id', $default_location, ['id' => 'location_id']) !!}
                                    @endif
                                </div>
                            </div>
        
                            {{-- start and end --}}
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class="input-group">
                                    <button type="button" class="btn btn-primary" id="date_filter" style="margin-top: 25px;">
                                        <span>
                                        <i class="fa fa-calendar"></i>&nbsp; {{ __('messages.filter_by_date') }}
                                        </span>
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                    </div>

                                    {!! Form::hidden('start_date', null, ['id' => 'start_date']) !!}
                                    {!! Form::hidden('end_date', null, ['id' => 'end_date']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
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
                                <div class="form-group" style="margin-top: 25px;">
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
</section>

@endsection

@section('javascript')
<script>
    $(document).ready(function () {
        let current_time = new Date();

        let initial_start_date = current_time.getFullYear() + '-01-01';
        let initial_end_date = current_time.getFullYear() + '-12-31';

        $('#start_date').val(initial_start_date);
        $('#end_date').val(initial_end_date);

        dateRangeSettings['startDate'] = moment(initial_start_date, 'YYYY-MM-DD');
        dateRangeSettings['endDate'] = moment(initial_end_date, 'YYYY-MM-DD');

        // Date filter
        $('#date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));

                var start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                $('#start_date').val(start_date);
                $('#end_date').val(end_date);
            }
        );

        // Location filter
        $('#select_location').on('change', function() {
            $('#location_id').val($('#select_location').val());
        });
    });
</script>
@endsection
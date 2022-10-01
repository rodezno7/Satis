@extends('layouts.app')
@section('title', __('report.lab_orders_report'))

@section('content')

{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1>{{ __('report.lab_orders_report')}}</h1>
</section>

{{-- Main content --}}
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default" id="accordion">
              <div class="box-header with-border">
                <h3 class="box-title">
                  <a data-toggle="collapse" data-parent="#accordion" href="#collapseFilter">
                    <i class="fa fa-filter" aria-hidden="true"></i>&nbsp; @lang('report.filters')
                  </a>
                </h3>
              </div>
              <div id="collapseFilter" class="panel-collapse active collapse in" aria-expanded="true">
                <div class="box-body">
                  {!! Form::open(['id'=>'form_lab_orders_report', 'action' => 'ReportController@postLabOrdersReport', 'method' => 'post', 'target' => '_blank']) !!}
                    <div class="row">
                        {{-- location_id --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                                {!! Form::select('location_id', $locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                            </div>
                        </div>

                        {{-- status_id --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('status_id',  __('accounting.status') . ':') !!}
                                {!! Form::select('status_id', $status, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
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
                        {{-- report_type --}}
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
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input type="submit" class="btn btn-success" value="@lang('accounting.generate')" id="button_report" style="margin-top: 24px;">
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
                    <table class="table table-bordered table-striped table-text-center" id="lab_orders_report_table">
                        <thead>
                            <tr>
                                <th>@lang('lab_order.no_order')</th>
                                <th>@lang('document_type.document')</th>
                                <th>@lang('accounting.location')</th>
                                <th>@lang('contact.customer')</th>
                                <th>@lang('graduation_card.patient')</th>
                                <th>@lang('accounting.status')</th>
                                <th>@lang('business.register')</th>
                                <th>@lang('lab_order.delivery')</th>
                            </tr>
                        </thead>
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
<script>
    $(document).ready(function() {
        $.fn.dataTable.ext.errMode = 'throw';

        // Datatable
        lab_orders_report_table = $('#lab_orders_report_table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            aaSorting: [[6, 'desc']],
            'ajax': {
                'url': '/reports/lab-orders-report',
                'data': function(d) {
                    d.location_id = $('#location_id').val();
                    d.status_id = $('#status_id').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: [
                { data: 'no_order', name: 'no_order' },
                { data: 'correlative', name: 'correlative' },
                { data: 'location', name: 'location' },
                { data: 'customer', name: 'customer' },
                { data: 'patient', name: 'patient' },
                { data: 'status', name: 'status' } ,
                { data: 'created_at', name: 'created_at' },
                { data: 'delivery', name: 'delivery' }
            ]
        });

        // Date filter
        $('#date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));

                var start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                $('#start_date').val(start_date);
                $('#end_date').val(end_date);

                lab_orders_report_table.ajax.reload();
            }
        );

        $('#date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_filter').html('<i class="fa fa-calendar"></i> {{ __("messages.filter_by_date") }}');
            lab_orders_report_table.ajax.reload();
        });

        // Reload table when changing params
        $('#form_lab_orders_report #location_id, #form_lab_orders_report #status_id').change(function() {
            lab_orders_report_table.ajax.reload();
        });
    });
</script>
@endsection
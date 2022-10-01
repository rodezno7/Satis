@extends('layouts.app')
@section('title', __('lab_order.external_laboratory_work'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang( 'lab_order.external_laboratory_work' )</h1>
</section>

<!-- Main content -->
<section class="content no-print">

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">
                @lang('report.generate_report')
            </h3>
        </div>
        
        <div class="box-body">
            {{-- Form --}}
            <div class="row">
                {{-- location_id --}}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id',  __('accounting.location') . ':') !!}
                        @if (is_null($default_location))
                        {!! Form::select('location_id', $locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                        @else
                        {!! Form::select('location_id', $locations, $default_location, ['class' => 'form-control select2', 'style' => 'width:100%', 'disabled']); !!}
                        @endif
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

            {{-- Datatable --}}
            <div class="table-responsive">
                <table class="table table-hover table-text-center" id="lab_orders_table" width="100%">
                    <thead>
                        <tr>
                            <th>@lang('accounting.status')</th>
                            <th>@lang('lab_order.no_order')</th>
                            <th>@lang('document_type.document')</th>
                            <th>@lang('accounting.location')</th>
                            <th>@lang('external_lab.external_lab')</th>
                            <th>@lang('business.register')</th>
                            <th>@lang('lab_order.delivery')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('optics.lab_order.edit')

    <div class="modal fade lab_orders_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade view_lab_order_modal" tabindex="-1"
        role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade add_lab_order_modal" data-backdrop="static" data-keyboard="false"
	    id="modal-add-order" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>

<section id="order_section" class="print_section"></section>
<!-- /.content -->

@endsection

@section('javascript')
<script type="text/javascript" src="{{ asset('/plugins/tempus/moment-with-locales.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/plugins/tempus/tempusdominus-bootstrap-3.min.js') }}"></script>

{{-- Moment JS --}}
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>

{{-- Datetime JS --}}
<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/dataRender/datetime.js"></script>

<script src="{{ asset('js/lab_order.js?v=' . $asset_v) }}"></script>

<script>
    $(document).ready(function() {
        // Data Table
        lab_orders_table = $('#lab_orders_table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            order: [[5, 'desc']],
            ajax: {
                'url': '/lab-order-reports/external-labs-report',
                'data': function(d) {
                    d.location_id = $('#location_id').val();
                    d.status_id = $('#status_id').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: [
                { data: 'status', name: 'status' } ,
                { data: 'no_order', name: 'no_order', searchable: false },
                { data: 'correlative', name: 'correlative' },
                { data: 'location', name: 'location' },
                { data: 'external_lab', name: 'external_lab' },
                { data: 'created_at', name: 'created_at', searchable: false },
                { data: 'delivery', name: 'delivery', searchable: false },
                { data: 'action', name: 'action', searchable: false, orderable: false }
            ],
            columnDefs: [{
                targets: [5, 6],
                render: $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'DD/MM/YYYY <br> h:mm a')
            }]
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

                lab_orders_table.ajax.reload();
            }
        );

        $('#date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_filter').html('<i class="fa fa-calendar"></i> {{ __("messages.filter_by_date") }}');
            lab_orders_table.ajax.reload();
        });

        // Reload table when changing params
        $('#location_id, #status_id').change(function() {
            lab_orders_table.ajax.reload();
        });
    });

    // Refresh page
    setInterval( function () {
        $('#lab_orders_table').DataTable().ajax.reload(null, false);
    }, 300000 );

    /** Print order */
    function printOrder(id) {
        var url = '{!! URL::to('/lab-orders/get-report/:id') !!}';
        url = url.replace(':id', id);
        window.open(url, '_blank');
    }
</script>
@endsection
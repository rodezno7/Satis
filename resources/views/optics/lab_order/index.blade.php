@extends('layouts.app')
@section('title', __('lab_order.lab_orders'))

@section('content')

{{-- Content Header (Page header) --}}
<section class="content-header no-print">
    <h1>@lang( 'lab_order.lab_orders' )
        <small>@lang( 'lab_order.manage_lab_orders' )</small>
    </h1>
</section>

{{-- Main content --}}
<section class="content no-print">
    {{-- Update permission --}}
    <input type="hidden" id="update-permission" value="{{ auth()->user()->can('lab_order.update') }}">

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">
                @lang( 'lab_order.all_lab_orders' )
            </h3>

            {{-- Add button --}}
            @can('lab_order.create')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{ action('Optics\LabOrderController@createLabOrder') }}" 
                    data-container=".add_lab_order_modal" id="btn-new-lab-orders">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )
                </button>
            </div>
            @endcan
        </div>
        
        <div class="box-body">
            @can('lab_order.view')
            {{-- Form --}}
            <div id="filters" class="row">
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

            {{-- Multiple options div --}}
            <div id="multiple-options" class="row" style="display: none;">
                <div class="col-sm-12">
                    <div class="alert" style="background-color: #eee; border-color: #eee; padding: 9px;">
                        <form class="form-inline">
                            <div class="form-group">
                                {!! Form::label('change_status',  __('lab_order.change_status') . ':') !!}
                                &nbsp;
                                {!! Form::select('change_status', $change_status, null,
                                    ['id' => 'change_status', 'class' => 'form-control select2', 'style' => 'width: 300px;']) !!}
                            </div>
                            &nbsp;
                            <div class="form-group">
                                <button
                                    type="button"
                                    id="btn-change-status"
                                    title
                                    data-toggle="tooltip"
                                    data-original-title="{{ __('messages.update') }}"
                                    class="btn btn-success"
                                    style="padding: 5px 12px;">
                                    <i class="fa fa-refresh"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Datatable --}}
            <div class="table-responsive">
                <table class="table table-hover table-text-center" id="lab_orders_table" width="100%">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all-row"></th>
                            <th>@lang('accounting.status')</th>
                            <th>@lang('lab_order.no_order')</th>
                            <th>@lang('document_type.document')</th>
                            <th>@lang('accounting.location')</th>
                            <th>@lang('contact.customer')</th>
                            <th>@lang('business.register')</th>
                            <th>@lang('lab_order.delivery')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
            @endcan
        </div>
    </div>
    
    {{-- Edit modal --}}
    @include('optics.lab_order.edit', ['default_warehouse' => $default_warehouse])

    {{-- Lab order modal --}}
    <div class="modal fade lab_orders_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

    {{-- View detail modal --}}
    <div class="modal fade view_lab_order_modal" tabindex="-1"
        role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    {{-- Add modal --}}
    <div class="modal fade add_lab_order_modal" data-backdrop="static" data-keyboard="false"
	    id="modal-add-order" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>

<section id="order_section" class="print_section"></section>
<!-- /.content -->

@endsection

@section('javascript')
{{-- Tempus JS --}}
<script type="text/javascript" src="{{ asset('/plugins/tempus/moment-with-locales.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/plugins/tempus/tempusdominus-bootstrap-3.min.js') }}"></script>

{{-- Moment JS --}}
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>

{{-- Datetime JS --}}
<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/dataRender/datetime.js"></script>

{{-- Lab order script --}}
<script src="{{ asset('js/lab_order.js?v=' . $asset_v) }}"></script>

<script>
    $(document).ready(function() {
        $.fn.dataTable.ext.errMode = 'throw';

        let update_permission = $('#update-permission').val();
        
        // Data Table
        lab_orders_table = $('#lab_orders_table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            order: [[6, 'desc']],
            ajax: {
                'url': '/lab-orders',
                'data': function(d) {
                    d.location_id = $('#location_id').val();
                    d.status_id = $('#status_id').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            rowCallback: function(row, data) {
                if (data.status_value == 'Nuevo') {
                    $('td:eq(4)', row).addClass('active');
                }
            },
            columns: [
                { data: 'checkbox', searchable: false, orderable: false, visible: update_permission },
                { data: 'status', name: 'status' },
                { data: 'no_order', name: 'no_order' },
                { data: 'correlative', name: 'correlative' },
                { data: 'location', name: 'location' },
                { data: 'customer', name: 'customer' },
                { data: 'created_at', name: 'created_at', searchable: false },
                { data: 'delivery', name: 'delivery', searchable: false },
                { data: 'action', name: 'action', searchable: false, orderable: false }
            ],
            columnDefs: [{
                targets: [6, 7],
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

        // On click of select-all-row input and row-select inputs
        $(document).on('click', 'input#select-all-row, input.row-select', function (e) {
            showMultipleOptions();
        });

        // On processing of lab_orders_table table
        $('#lab_orders_table').on('processing.dt', function () {
            showMultipleOptions();
            $('#select-all-row').prop('checked', false);
        });

        // On click of btn-change-status button
        $(document).on('click', 'button#btn-change-status', function (e) {
            e.preventDefault();

            let icon = $(this).find('i');

            Swal.fire({
                title: LANG.sure,
                text: LANG.lab_order_multiple_options,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelmButtonText: LANG.cancel,
                confirmButtonText: LANG.yes
            }).then((result) => {
                if (result.isConfirmed) {
                    let selected_rows = [];
            
                    let i = 0;
                    
                    $('input.row-select:checked').each(function () {
                        selected_rows[i++] = $(this).val();
                    });

                    let change_status = $('select#change_status').val();

                    if (selected_rows.length > 0 && change_status != '') {
                        icon.removeClass('fa-refresh').addClass('fa-spinner fa-pulse');

                        $.ajax({
                            type: 'post',
                            url: '/lab_orders/multiple-change-status',
                            data: {
                                lab_orders: selected_rows,
                                status_id: change_status
                            },
                            dataType: 'json',
                            success: function (res) {
                                icon.removeClass('fa-spinner fa-pulse').addClass('fa-refresh');

                                if (res.success === 1) {
                                    lab_orders_table.ajax.reload(null, false);

                                    Swal.fire({
                                        title: res.msg,
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });

                                } else {
                                    Swal.fire({
                                        title: res.msg,
                                        icon: 'error',
                                    });
                                }
                            }
                        });

                    } else {
                        Swal.fire({
                            title: LANG.no_item_selected,
                            icon: 'error',
                            timer: 1000,
                            showConfirmButton: false
                        });
                    }
                }
            })
        });
    });

    // Refresh page
    setInterval( function () {
        $('#lab_orders_table').DataTable().ajax.reload(null, false);
    }, 300000 );

    // Print order
    function printOrder(id) {
        var url = '{!! URL::to('/lab-orders/get-report/:id') !!}';
        url = url.replace(':id', id);
        window.open(url, '_blank');
    }

    /**
     * Show or hide multiple choice div.
     * 
     * @return void
     */
    function showMultipleOptions() {
        let count = 0;

        $('input.row-select:checked').each(function () {
            count++;
        });

        if (count > 0) {
            $('#filters').hide();
            $('#multiple-options').show();

        } else {
            $('#multiple-options').hide();
            $('#filters').show();
        }
    }
</script>
@endsection
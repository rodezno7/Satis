@extends('layouts.app')

@section('title', __('binnacle.binnacle'))

@section('content')
{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1>@lang('binnacle.binnacle')
        <small>@lang('binnacle.view_binnacle')</small>
    </h1>
</section>

{{-- Main content --}}
<section class="content">

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang('binnacle.actions_register')</h3>
        </div>
        
        <div class="box-body">
            @can('binnacle.view')
            {{-- Form --}}
            <div class="row">
                {{-- user_id --}}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('user_id',  __('binnacle.user1') . ':') !!}
                        {!! Form::select('user_id', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%']) !!}
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

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover table-text-center ajax_view" id="binnacle_table" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>@lang('binnacle.action')</th>
                            <th>@lang('binnacle.user1')</th>
                            <th>@lang('accounting.date')</th>
                            <th>@lang('binnacle.machine_name')</th>
                            <th>IP</th>
                            <th>@lang('binnacle.geolocation')</th>
                            <th>@lang('binnacle.domain')</th>
                        </tr>
                    </thead>
                </table>
            </div>
            @endcan
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade binnacle_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
{{-- Moment JS --}}
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>

{{-- Datetime JS --}}
<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/dataRender/datetime.js"></script>

<script>
    $(document).ready(function() {
        $.fn.dataTable.ext.errMode = 'throw';

        // Datatable
        binnacle_table = $('#binnacle_table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            aaSorting: [[0, 'desc']],
            'ajax': {
                'url': '/binnacle',
                'data': function(d) {
                    d.user_id = $('#user_id').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'action', name: 'action' },
                { data: 'user', name: 'user' },
                { data: 'realized_in', name: 'realized_in' },
                { data: 'machine_name', name: 'machine_name' },
                { data: 'ip', name: 'ip' },
                { data: 'geolocation', name: 'geolocation' },
                { data: 'domain', name: 'domain' }
            ],
        });

        binnacle_table.on('order.dt search.dt', function () {
            binnacle_table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            });
        }).draw();

        // Date filter
        $('#date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));

                var start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                $('#start_date').val(start_date);
                $('#end_date').val(end_date);

                binnacle_table.ajax.reload();
            }
        );

        $('#date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_filter').html('<i class="fa fa-calendar"></i> {{ __("messages.filter_by_date") }}');
            binnacle_table.ajax.reload();
        });

        // Reload table when changing params
        $('#user_id').change(function() {
            binnacle_table.ajax.reload();
        });
    });
</script>
@endsection
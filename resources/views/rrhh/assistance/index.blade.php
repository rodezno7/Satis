@extends('layouts.app')
@section('title', __('rrhh.rrhh'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> @lang('rrhh.assistance')
            <small></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        @can('rrhh_assistance.generate')
        <div class="row">
            <div class="col-md-12">
                <div class="box box-default" id="accordion">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseFilter">
                                <i class="fa fa-filter" aria-hidden="true"></i> @lang('report.filters')
                            </a>
                        </h3>
                    </div>
                    <div id="collapseFilter" class="panel-collapse active collapse in" aria-expanded="true">
                        <div class="box-body">
                            {!! Form::open([
                                'id' => 'form_assistance',
                                'action' => 'AssistanceEmployeeController@postAssistancesReport',
                                'method' => 'post',
                                'target' => '_blank',
                            ]) !!}
                            <div class="row">

                                {{-- document_type_id --}}
                                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('employee', __('rrhh.employee') . ':') !!}

                                        <select name="select_employee" id="select_employee" class="form-control select2"
                                            style="width: 100%" required>
                                            <option value="0">{{ __('rrhh.all') }}</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}">{{ $employee->first_name }}
                                                    {{ $employee->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- start and end --}}
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <button type="button" class="btn btn-primary" id="date_filter" style="margin-top: 25px;">
                                                <span>
                                                    <i class="fa fa-calendar"></i>&nbsp; {{ __('messages.filter_by_date') }}
                                                </span>
                                                <i class="fa fa-caret-down"></i>
                                            </button>
                                        </div>
                                        {!! Form::hidden('start_date', null, ['id' => 'start_date', 'required']) !!}
                                        {!! Form::hidden('end_date', null, ['id' => 'end_date', 'required']) !!}
                                    </div>
                                </div>

                                {{-- App business --}}
                                <input type="hidden" id="app-business" value="{{ config('app.business') }}">

                                {{-- format --}}
                                <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>@lang('accounting.format')</label>
                                        <select name="report_type" id="report_type" class="form-control select2"
                                            style="width: 100%" required>
                                            <option value="pdf" selected>PDF</option>
                                            <option value="excel">Excel</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- button --}}
                                <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6">
                                    <div id="button_report" class="btn btn-success ld-ext-right" {{-- onmouseover="this.classList.add('running')" onmouseout="this.classList.remove('running')" --}}
                                        style="margin-top: 24px;">
                                        <span id="spn-generate">@lang('accounting.generate')</span>
                                        <div class="ld ld-ring ld-spin" style="color:#fff"></div>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-condensed table-hover"
                                id="assistances-table" width="100%">
                                <thead>
                                    <th>@lang('rrhh.employee')</th>
                                    <th>@lang('rrhh.schedule')</th>
                                    <th>@lang('rrhh.time_worked')</th>
                                    <th>@lang('rrhh.status')</th>
                                    <th width="12%">@lang('rrhh.actions')</th>
                                </thead>
                            </table>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div tabindex="-1" class="modal fade" id="assistance_modal" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content" id="modal_content_assistance">
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_photo" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" id="modal_content_photo">
    
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'none';

            $('#modal_action').on('shown.bs.modal', function() {
                $(this).find('#rrhh_type_personnel_action_id').select2({
                    dropdownParent: $(this),
                })
            });

            dateRangeSettings['startDate'] = moment().startOf('month');
            dateRangeSettings['endDate'] = moment().endOf('month');

            $('#date_filter').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));

                    var start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                    $('#start_date').val(start_date);
                    $('#end_date').val(end_date);

                    assistances.ajax.reload();
                }
            );


            var assistances = $("#assistances-table").DataTable({
                deferRender: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/rrhh-assistances',
                    data: function(d) {
                        d.employee_id = $('#select_employee').val();
                        // d.start_date = $('#start_date').val();
                        // d.end_date = $('#end_date').val();
                        d.start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        d.end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    }
                },
                columns: [
                    {
                        data: 'employee',
                        name: 'employee',
                        className: "text-center"
                    },
                    {
                        data: 'schedule',
                        name: 'schedule',
                        className: "text-center"
                    },
                    {
                        data: 'number_of_hours',
                        name: 'number_of_hours',
                        className: "text-center"
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: "text-center"
                    },
                    { data: 'actions',  orderable: false, searchable: false, className: 'text-center'}
                ],
                order: [
                    [0, 'desc']
                ],
                dom: '<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
            });

            // Seller filter
            $('select#select_employee').on('change', function() {
                assistances.ajax.reload();
            });

            // // Payment status filter
            // $('select#start_date').on('change', function() {
            //     assistances.ajax.reload();
            // });

            // $('select#end_date').on('change', function() {
            //     assistances.ajax.reload();
            // });

            $(document).on('click', '#button_report', function(e) {
                $('form#form_assistance').submit();
            });
        });

        function viewDetail(id) {
            $("#modal_content_assistance").html('');
            var url = "{!! URL::to('/rrhh-assistances-show/:id') !!}";
            url = url.replace(':id', id);
            $.get(url, function(data) {
                $("#modal_content_assistance").html(data);
                $('#assistance_modal').modal({
                    backdrop: 'static'
                });
            });
        }
        
    </script>
@endsection
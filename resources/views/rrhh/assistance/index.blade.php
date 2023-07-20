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
                                        {!! Form::hidden('start_date', @format_date('now'), ['id' => 'start_date', 'required']) !!}
                                        {!! Form::hidden('end_date', @format_date('now'), ['id' => 'end_date', 'required']) !!}
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
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            loadAssistances();
            $.fn.dataTable.ext.errMode = 'none';

            $('#modal_action').on('shown.bs.modal', function() {
                $(this).find('#rrhh_type_personnel_action_id').select2({
                    dropdownParent: $(this),
                })
            });

            $('#date_filter').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(
                        moment_date_format));

                    var start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                    $('#start_date').val(start_date);
                    $('#end_date').val(end_date);
                }
            );

            // On click of button_report div
            $(document).on('click', '#button_report', function(e) {
                $('form#form_assistance').submit();
                //loadAssistances();
                //assistances_table.ajax.reload();
            });

            // var assistances_table = $("#assistances-table").DataTable({
            //     deferRender: true,
            //     processing: true,
            //     serverSide: true,
            //     //ajax: "/rrhh-assistances-getAssistances",
            //     ajax: {
            //         url: "/rrhh-assistances-getAssistances",
            //         data: function (d) {
            //             d.start_date = $('#start_date').val();
            //             d.end_date = $('#end_date').val();
            //             d.select_employee = $("input#select_employee").val();
            //         }
            //     },
            //     columns: [
            //         {
            //             data: 'employee',
            //             name: 'employee',
            //             className: "text-center"
            //         },
            //         {
            //             data: 'schedule',
            //             name: 'schedule',
            //             className: "text-center"
            //         },
            //         {
            //             data: 'number_of_hours',
            //             name: 'number_of_hours',
            //             className: "text-center"
            //         },
            //         {
            //             data: 'status',
            //             name: 'status',
            //             className: "text-center"
            //         },
            //         // {
            //         //     data: null,
            //         //     render: function(data) {
            //         //         html =
            //         //             '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> @lang('messages.actions') <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu">';
            //         //         html += '</ul></div>';

            //         //         return html;
            //         //     },
            //         //     orderable: false,
            //         //     searchable: false,
            //         //     className: "text-center"
            //         // }
            //     ],
            //     order: [
            //         [1, 'desc']
            //     ],
            //     dom: '<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
            // });
        });

        function loadAssistances() {
            var table = $("#assistances-table").DataTable();
            table.destroy();
            var table = $("#assistances-table").DataTable({
                select: true,
                deferRender: true,
                processing: true,
                serverSide: true,
                ajax: "/rrhh-assistances-getAssistances",
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
                    {
                        data: null,
                        render: function(data) {
                            html = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> @lang('messages.actions') <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu">';
                            html += '<li> <a href="#" onClick="viewDetail('+data.id+')"><i class="fa fa-eye"></i>@lang('messages.view')</a></li>';
                            html += '</ul></div>';

                            return html;
                        },
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                dom: '<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
            });
        }

        function viewDetail(id) {
            var route = '/rrhh-assistances-getByAssistances/'+id;
            $("#assistance_modal").load(route, function() {
                $(this).modal({
                backdrop: 'static'
                });
            });
        }
        /**
         * Get report in Excel format.
         * 
         * @param  array  data
         * @return void
        */
        function export_excel(data) {
            var wb = XLSX.utils.book_new();

            wb.Props = {
                Title: LANG.all_sales_report
            };

            wb.SheetNames.push(LANG.all_sales_report);

            var ws_data = data;
            var ws = XLSX.utils.aoa_to_sheet(ws_data);

            /** Set number format */
            let data_length = data.length + 1;
            for (let i = 5; i < data_length; i++) {
                ws["H"+i].t = "n";
                ws["H"+i].z = "0.0000";
                ws["I"+i].t = "n";
                ws["I"+i].z = "0.0000";
                ws["J"+i].t = "n";
                ws["J"+i].z = "0.0000";
                ws["K"+i].t = "n";
                ws["K"+i].z = "0.0000";
            }

            wb.Sheets[LANG.all_sales_report] = ws;

            var wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'binary' });

            saveAs(new Blob([s2ab(wbout)], { type: "application/octet-stream" }), LANG.all_sales_report + '.xlsx');
        }

        /**
         * Convert string to array buffer.
         * 
         * @param  workbook  s
         * @return void
        */
        function s2ab(s) {
            var buf = new ArrayBuffer(s.length);
            var view = new Uint8Array(buf);
            for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
            return buf;
        }

        /**
         * Get report in PDF format.
         * 
         * @param  array  data
         * @param  array  header_data
         * @param  array  headers
         * @return void
        */
        function export_pdf(data, header_data, headers) {
            window.jsPDF = window.jspdf.jsPDF;

            var doc = new jsPDF('l', 'mm', 'a4');

            doc.setFontSize(8);
            doc.text(header_data.business_name, doc.internal.pageSize.getWidth() / 2, 14, null, null, 'center');
            doc.text(header_data.report_name, doc.internal.pageSize.getWidth() / 2, 20, null, null, 'center');

            doc.autoTable({
                styles: { fontSize: 7 },
                head: headers,
                body: data,
                startY: 24,
                theme: 'plain'
            });

            doc.save(LANG.all_sales_report + '.pdf');
        }
    </script>
@endsection

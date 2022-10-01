@extends('layouts.app')

@section('title', __('report.glasses_consumption_report'))

@section('css')
<link rel="stylesheet" href="{{ asset('plugins/loadingio/loading.min.css?v='.$asset_v) }}">
<link rel="stylesheet" href="{{ asset('plugins/loadingio/ldbtn.min.css?v='.$asset_v) }}">

<style>
    .disabled-div {
        pointer-events: none;
        opacity: 0.8;
        cursor: not-allowed;
    }
</style>
@endsection

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.glasses_consumption_report')}}</h1>
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
                        'id'=>'form_glasses_consumption_report',
                        'action' => 'ReportController@postGlassesConsumptionReport',
                        'method' => 'post',
                        'target' => '_blank'
                    ]) !!}

                    <div class="row">
                        {{-- warehouse_id --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label('warehouse', __('kardex.warehouse') . ':') !!}
    
                                {!! Form::select("select_warehouse", $warehouses, null,
                                    ['class' => 'form-control select2', 'id' => 'select_warehouse']) !!}
    
                                {!! Form::hidden('warehouse', 'all', ['id' => 'warehouse']) !!}
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
                            <div id="button_report" class="btn btn-success ld-ext-right" style="margin-top: 24px;">
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
                        <table id="glasses_consumption_report_table" class="table table-striped table-text-center" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('messages.date')</th>
                                    <th class="text-center">@lang('lab_order.no_order')</th>
                                    <th class="text-center">@lang('sale.document_no')</th>
                                    <th class="text-center">@lang('document_type.title')</th>
                                    <th class="text-left">@lang('product.sku')</th>
                                    <th class="text-left">@lang('product.product')</th>
                                    <th class="text-center">@lang('report.base')</th>
                                    <th class="text-center">@lang('report.addition')</th>
                                    <th class="text-center">@lang('lang_v1.quantity')</th>
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
{{-- Moment JS --}}
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>

{{-- Datetime JS --}}
<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/dataRender/datetime.js"></script>

{{-- FileSaver --}}
<script src="{{ asset('plugins/filesaver/FileSaver.min.js?v=' . $asset_v) }}"></script>

{{-- SheetJs --}}
<script src="{{ asset('plugins/sheetjs/xlsx.full.min.js?v=' . $asset_v) }}"></script>

{{-- jsPDF --}}
<script src="{{ asset('plugins/jspdf/jspdf.min.js?v=' . $asset_v) }}"></script>

{{-- jsPDF-AutoTable --}}
<script src="{{ asset('plugins/jspdf-autotable/jspdf-autotable.min.js?v=' . $asset_v) }}"></script>

<script>
    $(document).ready(function() {
        $.fn.dataTable.ext.errMode = 'throw';

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

                glasses_consumption_report_table.ajax.reload();
            }
        );

        // Datatable
        glasses_consumption_report_table = $('#glasses_consumption_report_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '/reports/glasses-consumption',
                data: function (d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.warehouse_id = $('input#warehouse').val();
                }
            },
            columns: [
                { data: 'date', name: 'date', className: 'text-center' },
                { data: 'no_order', name: 'no_order', className: 'text-center' },
                { data: 'correlative', name: 'correlative', className: 'text-center' },
                { data: 'document_type', name: 'document_type', className: 'text-center' },
                { data: 'sku', name: 'sku', className: 'text-left' },
                { data: 'product', name: 'product', className: 'text-left' },
                { data: 'base', name: 'base', className: 'text-center', searchable: false },
                { data: 'addition', name: 'addition', className: 'text-center' },
                { data: 'quantity', name: 'quantity', className: 'text-center' }
            ],
            columnDefs: [{
                targets: 0,
                render: $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'DD/MM/YYYY')
            }],
            fnDrawCallback: function (oSettings) {
                __currency_convert_recursively($('#glasses_consumption_report_table'));
            }
        });

        $('#date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_filter').html('<i class="fa fa-calendar"></i> {{ __("messages.filter_by_date") }}');
            glasses_consumption_report_table.ajax.reload();
        });

        // Warehouse filter
        $('select#select_warehouse').on('change', function() {
            $("input#warehouse").val($("select#select_warehouse").val());
            glasses_consumption_report_table.ajax.reload();
        });

        // On click of button_report div
        $(document).on('click', '#button_report', function(e) {
            if (! $('div#button_report').hasClass('running')) {
                $('span#spn-generate').text(LANG.generating);
                $(this).addClass('running');
                $(this).addClass('disabled-div');

                $.ajax({
                    method: 'post',
                    url: $('form#form_glasses_consumption_report').attr('action'),
                    dataType: 'json',
                    data: $('form#form_glasses_consumption_report').serialize(),
                    success: function (result) {
                        if (result.success === true) {
                            if (result.type === 'pdf') {
                                export_pdf(result.data, result.header_data, result.headers);
                            } else {
                                export_excel(result.data);
                            }

                            $('div#button_report').removeClass('running');
                            $('span#spn-generate').text(LANG.generate);
                            $('div#button_report').removeClass('disabled-div');
                            
                        } else {
                            Swal.fire({
                                title: result.msg,
                                icon: 'error',
                            });
                        }
                    }
                });
            }
        });
    });

    /**
     * Get report in Excel format.
     * 
     * @param  array  data
     * @return void
    */
    function export_excel(data) {
        var wb = XLSX.utils.book_new();

        wb.Props = {
            Title: LANG.glasses_consumption_report
        };

        wb.SheetNames.push(LANG.glasses_consumption_report);

        var ws_data = data;
        var ws = XLSX.utils.aoa_to_sheet(ws_data);

        wb.Sheets[LANG.glasses_consumption_report] = ws;

        var wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'binary' });

        saveAs(new Blob([s2ab(wbout)], { type: "application/octet-stream" }), LANG.glasses_consumption_report + '.xlsx');
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

        doc.save(LANG.glasses_consumption_report + '.pdf');
    }
</script>
@endsection
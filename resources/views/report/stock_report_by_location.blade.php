@extends('layouts.app')

@section('title', __('report.stock_report_by_location'))

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
    <h1>{{ __('report.stock_report_by_location')}}</h1>
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
                        'id'=>'form_stock_report_by_location',
                        'action' => 'ReportController@postStockReportByLocation',
                        'method' => 'post',
                        'target' => '_blank'
                    ]) !!}

                    <div class="row">
                        {{-- format --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>@lang('accounting.format')</label>
                                <select name="report_type" id="report_type" class="form-control select2" style="width: 100%" required>
                                    {{-- <option value="pdf" selected>PDF</option> --}}
                                    <option value="excel" selected>Excel</option>
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
                        <table id="stock_report_by_location_table" class="table table-striped table-text-center" width="100%">
                            <thead>
                                <tr>
                                    <th>@lang('product.sku')</th>
                                    <th>@lang('accounting.location')</th>
                                    <th class="text-left">@lang('lang_v1.quantity')</th>
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

        // Datatable
        stock_report_by_location_table = $('#stock_report_by_location_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'asc']],
            ajax: '/reports/stock-by-location',
            columns: [
                { data: 'sku', name: 'sku' },
                { data: 'location', name: 'location', orderable: false },
                { data: 'quantity', name: 'quantity', className: 'text-right', orderable: false },
            ],
        });

        // On click of button_report div
        $(document).on('click', '#button_report', function(e) {
            if (! $('div#button_report').hasClass('running')) {
                $('span#spn-generate').text(LANG.generating);
                $(this).addClass('running');
                $(this).addClass('disabled-div');

                $.ajax({
                    method: 'post',
                    url: $('form#form_stock_report_by_location').attr('action'),
                    dataType: 'json',
                    data: $('form#form_stock_report_by_location').serialize(),
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
            Title: LANG.stock_report_by_location
        };

        wb.SheetNames.push(LANG.stock_report_by_location_menu);

        var ws_data = data;
        var ws = XLSX.utils.aoa_to_sheet(ws_data);

        wb.Sheets[LANG.stock_report_by_location_menu] = ws;

        var wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'binary' });

        saveAs(new Blob([s2ab(wbout)], { type: "application/octet-stream" }), LANG.stock_report_by_location + '.xlsx');
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

        doc.save(LANG.stock_report_by_location + '.pdf');
    }
</script>
@endsection
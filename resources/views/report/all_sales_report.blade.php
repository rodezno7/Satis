@extends('layouts.app')

@section('title', __('report.all_sales_report'))

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
    <h1>{{ __('report.all_sales_report')}}</h1>
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
                    {!! Form::open(['id'=>'form_all_sales_report', 'action' => 'ReportController@postAllSalesReport', 'method' => 'post', 'target' => '_blank']) !!}
                    <div class="row">
                        {{-- location_id --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label("location", __("kardex.location") . ":") !!}
    
                                @if (is_null($default_location))
                                {!! Form::select("select_location", $locations, null,
                                    ["class" => "form-control select2", "id" => "select_location"]) !!}
    
                                {!! Form::hidden('location', 'all', ['id' => 'location']) !!}
    
                                @else
                                {!! Form::select("select_location", $locations, null,
                                    ["class" => "form-control select2", "id" => "location", 'disabled']) !!}
    
                                {!! Form::hidden('location', $default_location, ['id' => 'location']) !!}
                                @endif
                            </div>
                        </div>
    
                        {{-- document_type_id --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label("document_type", __("document_type.title") . ":") !!}
    
                                {!! Form::select("select_document_type", $document_types, null,
                                    ["class" => "form-control select2", "id" => "select_document_type"]) !!}
    
                                {!! Form::hidden('document_type', 'all', ['id' => 'document_type']) !!}
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

                        {{-- App business --}}
                        <input type="hidden" id="app-business" value="{{ config('app.business') }}">
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
                            <div id="button_report" class="btn btn-success ld-ext-right" {{-- onmouseover="this.classList.add('running')" onmouseout="this.classList.remove('running')" --}} style="margin-top: 24px;">
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
                        <table class="table table-striped table-text-center" id="all_sales_report_table" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('messages.date')</th>
                                    <th class="text-center">@lang('sale.document_no')</th>
                                    <th class="text-center">@lang('document_type.title')</th>
                                    <th>@lang('contact.customer')</th>
                                    @if (config('app.business') == 'optics')
                                    <th class="text-center">@lang('sale.location')</th>
                                    <th class="text-center">@lang('sale.payment_status')</th>
                                    <th class="text-center">@lang('sale.total_invoice')</th>
                                    <th class="text-center">@lang('lang_v1.payment_note')</th>
                                    <th class="text-center">@lang('sale.total_paid')</th>
                                    <th class="text-center">@lang('sale.total_balance_due')</th>
                                    @else
                                    <th class="text-center">@lang('sale.payment_status')</th>
                                    <th class="text-center">@lang('lang_v1.payment_method')</th>
                                    <th class="text-center">@lang('sale.subtotal')</th>
                                    <th class="text-center">@lang('sale.discount')</th>
                                    <th class="text-center">@lang('tax_rate.taxes')</th>
                                    <th class="text-center">@lang('sale.total_amount')</th>
                                    @endif
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="bg-gray font-17 footer-total text-center">
                                    @if (config('app.business') == 'optics')
                                    <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                                    <td id="footer_payment_status_count"></td>
                                    <td><span class="display_currency" id="footer_sale_total" data-currency_symbol ="true"></span></td>
                                    <td>&nbsp;</td>
                                    <td><span class="display_currency" id="footer_total_paid" data-currency_symbol ="true"></span></td>
                                    <td class="text-left"><span class="display_currency" id="footer_total_remaining" data-currency_symbol ="true"></span></td>
                                    @else
                                    <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                                    <td id="footer_payment_status_count"></td>
                                    <td>&nbsp;</td>
                                    <td><span class="display_currency" id="footer_sale_subtotal" data-currency_symbol="true"></span></td>
                                    <td><span class="display_currency" id="footer_sale_discount_amount" data-currency_symbol="true"></span></td>
                                    <td><span class="display_currency" id="footer_sale_tax_amount" data-currency_symbol="true"></span></td>
                                    <td><span class="display_currency" id="footer_sale_total" data-currency_symbol="true"></span></td>
                                    @endif
                                </tr>
                            </tfoot>
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

        let app_business = $('#app-business').val();

        let table_columns = [];

        if (app_business == 'optics') {
            table_columns =  [
                { data: 'transaction_date', name: 'transaction_date', className: 'text-center' },
                { data: 'correlative', name: 'correlative', className: 'text-center' },
                { data: 'document_name', name: 'document_name', className: 'text-center' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'location', name: 'location', className: 'text-center' },
                { data: 'payment_status', name: 'payment_status', className: 'text-center' },
                { data: 'final_total', name: 'final_total', className: 'text-center' },
                { data: 'note', name: 'note', className: 'text-center' },
                { data: 'total_paid', name: 'total_paid', className: 'text-center' },
                { data: 'total_remaining', name: 'total_remaining', className: 'text-center' }
            ];
        } else {
            table_columns =  [
                { data: 'transaction_date', name: 'transaction_date', className: 'text-center' },
                { data: 'correlative', name: 'correlative', className: 'text-center' },
                { data: 'document_name', name: 'document_name', className: 'text-center' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'payment_status', name: 'payment_status', className: 'text-center' },
                { data: 'method', name: 'method', className: 'text-center' },
                { data: 'total_before_tax', name: 'total_before_tax', className: 'text-right' },
                { data: 'discount_amount', name: 'discount_amount', className: 'text-right' },
                { data: 'tax_amount', name: 'tax_amount', className: 'text-right' },
                { data: 'final_total', name: 'final_total', className: 'text-right' }
            ];
        }

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

                all_sales_report_table.ajax.reload();
            }
        );

        // Datatable
        all_sales_report_table = $('#all_sales_report_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/sales-reports/all-sales-report",
                "data": function (d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.is_direct_sale = 0;
                    d.location_id = $("input#location").val();
                    d.document_type_id = $("input#document_type").val();
                }
            },
            columns: table_columns,
            columnDefs: [{
                targets: 0,
                render: $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'DD/MM/YYYY')
            }],
            "fnDrawCallback": function (oSettings) {
                let app_business = $('#app-business').val();

                if (app_business == 'optics') {
                    $('#footer_sale_total').text(sum_table_col($('#all_sales_report_table'), 'final-total'));
                    $('#footer_total_paid').text(sum_table_col($('#all_sales_report_table'), 'total_paid'));
                    $('#footer_total_remaining').text(sum_table_col($('#all_sales_report_table'), 'total_remaining'));
                    $('#footer_total_sell_return_due').text(sum_table_col($('#all_sales_report_table'), 'sell_return_due'));
                    $('#footer_payment_status_count ').html(__sum_status_html($('#all_sales_report_table'), 'payment-status-label'));

                } else {
                    $('#footer_sale_total').text(sum_table_col($('#all_sales_report_table'), 'final-total'));
                    $('#footer_sale_subtotal').text(sum_table_col($('#all_sales_report_table'), 'subtotal'));
                    $('#footer_sale_discount_amount').text(sum_table_col($('#all_sales_report_table'), 'discount_amount'));
                    $('#footer_sale_tax_amount').text(sum_table_col($('#all_sales_report_table'), 'tax-amount'));
                    $('#footer_payment_status_count').html(__sum_status_html($('#all_sales_report_table'), 'payment-status-label'));
                }

                __currency_convert_recursively($('#all_sales_report_table'));
            }
        });

        $('#date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_filter').html('<i class="fa fa-calendar"></i> {{ __("messages.filter_by_date") }}');
            all_sales_report_table.ajax.reload();
        });

        // Location filter
        $('select#select_location').on('change', function() {
            $("input#location").val($("select#select_location").val());
            all_sales_report_table.ajax.reload();
        });

        // Document type filter
        $('select#select_document_type').on('change', function() {
            $("input#document_type").val($("select#select_document_type").val());
            all_sales_report_table.ajax.reload();
        });

        // On click of button_report div
        $(document).on('click', '#button_report', function(e) {
            if (! $('div#button_report').hasClass('running')) {
                $('span#spn-generate').text(LANG.generating);
                $(this).addClass('running');
                $(this).addClass('disabled-div');

                $.ajax({
                    method: 'post',
                    url: $('form#form_all_sales_report').attr('action'),
                    dataType: 'json',
                    data: $('form#form_all_sales_report').serialize(),
                    success: function(result) {
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
            Title: LANG.all_sales_report
        };

        wb.SheetNames.push(LANG.all_sales_report);

        var ws_data = data;
        var ws = XLSX.utils.aoa_to_sheet(ws_data);

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
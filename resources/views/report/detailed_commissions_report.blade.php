@extends('layouts.app')

@section('title', config('app.business') == 'optics' ? __('report.optics_detailed_commissions_report') : __('report.detailed_commissions_report'))

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

{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1>
        {{ config('app.business') == 'optics' ? __('report.optics_detailed_commissions_report') : __('report.detailed_commissions_report')}}
    </h1>
</section>

{{-- Main content --}}
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default" id="accordion">
              <div class="box-header with-border">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseFilter">
                  <h3 class="box-title" style="color: #444;">
                    <i class="fa fa-filter" aria-hidden="true"></i>&nbsp; @lang('report.filters')
                  </h3>
                </a>
              </div>
              <div id="collapseFilter" class="panel-collapse active collapse in" aria-expanded="true">
                <div class="box-body">
                  {!! Form::open(['id' => 'form_detailed_commissions_report', 'action' => 'ReportController@postDetailedCommissionsReport', 'method' => 'post']) !!}
                    {{-- App business --}}
                    <input type="hidden" id="app-business" value="{{ config('app.business') }}">

                    <div class="row">
                        {{-- location_id --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                                {!! Form::select('location_id', $locations, null,
                                    ['class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
                            </div>
                        </div>

                        {{-- commission_agent --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('commission_agent',  __('report.employee') . ':') !!}
                                {!! Form::select('commission_agent', $commission_agents, null,
                                    ['class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
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
                                {!! Form::hidden("start_date", date('Y-m-d', strtotime('first day of this month')), ['id' => 'start_date']) !!}
                                {!! Form::hidden("end_date", date('Y-m-d', strtotime('last day of this month')), ['id' => 'end_date']) !!}
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
                    <table class="table table-bordered table-striped table-text-center" id="detailed_commissions_report_table">
                        <thead>
                            <tr>
                                @if (config('app.business') == 'optics')
                                    <th>@lang('accounting.date')</th>
                                    <th>@lang('inflow_outflow.document_no')</th>
                                    <th>@lang('document_type.title')</th>
                                    <th>@lang('lang_v1.payment_condition')</th>
                                    <th>@lang('contact.customer')</th>
                                    <th>@lang('accounting.location')</th>
                                    <th>@lang('category.category')</th>
                                    <th>@lang('product.sub_category')</th>
                                    <th>@lang('product.brand')</th>
                                    <th>SKU</th>
                                    <th>@lang('business.product')</th>
                                    <th>@lang('lang_v1.quantity')</th>
                                    <th>@lang('purchase.unit_price')</th>
                                    <th>@lang('quote.seller')</th>
                                    <th>@lang('graduation_card.optometrist')</th>
                                    <th>@lang('report.unit_cost')</th>
                                @else
                                    <th>@lang('accounting.date')</th>
                                    <th>@lang('inflow_outflow.document_no')</th>
                                    <th>@lang('document_type.title')</th>
                                    <th>@lang('lang_v1.payment_condition')</th>
                                    <th>@lang('customer.customer_code')</th>
                                    <th>@lang('contact.customer')</th>
                                    <th>@lang('accounting.location')</th>
                                    <th>@lang('category.category')</th>
                                    <th>@lang('product.sub_category')</th>
                                    <th>@lang('product.brand')</th>
                                    <th>SKU</th>
                                    <th>@lang('business.product')</th>
                                    <th>@lang('lang_v1.quantity')</th>
                                    <th>@lang('report.price_inc_tax')</th>
                                    <th>@lang('report.price_exc_tax')</th>
                                    <th>@lang('sale.payments')</th>
                                    <th>@lang('sale.payment_status')</th>
                                    <th>@lang('quote.seller')</th>
                                    <th>@lang('report.unit_cost')</th>
                                    <th>@lang('report.total_cost')</th>
                                    <th>@lang('customer.customer_portfolio')</th>
                                    <th>@lang('geography.state')</th>
                                    <th>@lang('geography.city')</th>
                                @endif
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

        let app_business = $('#app-business').val();

        let table_columns = [];

        if (app_business == 'optics') {
            table_columns = [
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'doc_no', name: 'doc_no', orderable: false },
                { data: 'doc_type', name: 'doc_type', orderable: false } ,
                { data: 'payment_condition', name: 'payment_condition', orderable: false },
                { data: 'customer_name', name: 'customer_name', orderable: false },
                { data: 'location', name: 'location', orderable: false },
                { data: 'category', name: 'category', orderable: false },
                { data: 'sub_category', name: 'sub_category', orderable: false },
                { data: 'brand_name', name: 'brand_name', orderable: false },
                { data: 'sku', name: 'sku', orderable: false },
                { data: 'product_name', name: 'product_name', orderable: false },
                { data: 'quantity', name: 'quantity', orderable: false },
                { data: 'unit_price', name: 'unit_price', orderable: false },
                { data: 'seller_name', name: 'seller_name', orderable: false },
                { data: 'optometrist', name: 'optometrist', orderable: false },
                { data: 'unit_cost', name: 'unit_cost', orderable: false },
            ];

        } else {
            table_columns = [
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'doc_no', name: 'doc_no', orderable: false },
                { data: 'doc_type', name: 'doc_type', orderable: false } ,
                { data: 'payment_condition', name: 'payment_condition', orderable: false },
                { data: 'customer_id', name: 'customer_id', orderable: false },
                { data: 'customer_name', name: 'customer_name', orderable: false },
                { data: 'location', name: 'location', orderable: false },
                { data: 'category', name: 'category', orderable: false },
                { data: 'sub_category', name: 'sub_category', orderable: false },
                { data: 'brand_name', name: 'brand_name', orderable: false },
                { data: 'sku', name: 'sku', orderable: false },
                { data: 'product_name', name: 'product_name', orderable: false },
                { data: 'quantity', name: 'quantity', orderable: false },
                { data: 'price_inc', name: 'price_inc', orderable: false },
                { data: 'price_exc', name: 'price_exc', orderable: false },
                { data: 'payment_balance', name: 'payment_balance', orderable: false },
                { data: 'payment_status', name: 'payment_status', orderable: false },
                { data: 'seller_name', name: 'seller_name', orderable: false },
                { data: 'unit_cost', name: 'unit_cost', orderable: false },
                { data: 'total_cost', name: 'total_cost', orderable: false },
                { data: 'portfolio_name', name: 'portfolio_name', orderable: false },
                { data: 'state', name: 'state', orderable: false },
                { data: 'city', name: 'city', orderable: false }
            ];
        }

        // Datatable
        detailed_commissions_report_table = $('#detailed_commissions_report_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '/reports/detailed-commissions-report',
                data: function(d) {
                    d.location_id = $('#location_id').val();
                    d.commission_agent = $('#commission_agent').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: table_columns,
            columnDefs: [{
                targets: [0],
                render: $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'DD/MM/YYYY')
            }]
        });

        // On change of dataTables_filter input
        $('.dataTables_filter input').off().on('change', function() {
            $('#detailed_commissions_report_table').DataTable().search(this.value.trim(), false, false).draw();
        });

        dateRangeSettings['startDate'] = moment().startOf('month');
        dateRangeSettings['endDate'] = moment().endOf('month');

        // Date filter
        $('#date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));

                var start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                $('#start_date').val(start_date);
                $('#end_date').val(end_date);

                detailed_commissions_report_table.ajax.reload();
            }
        );

        $('#date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_filter').html('<i class="fa fa-calendar"></i> {{ __("messages.filter_by_date") }}');
            detailed_commissions_report_table.ajax.reload();
        });

        // Reload table when changing params
        $('#form_detailed_commissions_report #location_id, #form_detailed_commissions_report #commission_agent').change(function() {
            detailed_commissions_report_table.ajax.reload();
        });

        // On click of button_report div
        $(document).on('click', '#button_report', function(e) {
            if (! $('div#button_report').hasClass('running')) {
                $('span#spn-generate').text(LANG.generating);
                $(this).addClass('running');
                $(this).addClass('disabled-div');

                $.ajax({
                    method: 'post',
                    url: $('form#form_detailed_commissions_report').attr('action'),
                    dataType: 'json',
                    data: $('form#form_detailed_commissions_report').serialize(),
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
            Title: LANG.detailed_commissions_report
        };

        wb.SheetNames.push(LANG.detailed_commissions_report);

        var ws_data = data;
        var ws = XLSX.utils.aoa_to_sheet(ws_data);

        wb.Sheets[LANG.detailed_commissions_report] = ws;

        var wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'binary' });

        saveAs(new Blob([s2ab(wbout)], { type: "application/octet-stream" }), LANG.detailed_commissions_report + '.xlsx');
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

        doc.setFontSize(7);
        doc.text(header_data.business_name, doc.internal.pageSize.getWidth() / 2, 14, null, null, 'center');
        doc.text(header_data.report_name, doc.internal.pageSize.getWidth() / 2, 20, null, null, 'center');

        doc.autoTable({
            styles: { fontSize: 5 },
            head: headers,
            body: data,
            startY: 24,
            theme: 'plain'
        });

        doc.save(LANG.detailed_commissions_report + '.pdf');
    }
</script>
@endsection
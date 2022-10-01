@extends('layouts.app')
@section('title', __('report.all_sales_with_utility_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.all_sales_with_utility_report')}}</h1>
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
                    {!! Form::open(['id'=>'form_all_sales_with_utility_report', 'action' => 'ReportController@postAllSalesWithUtilityReport', 'method' => 'post', 'target' => '_blank']) !!}
                    <div class="row">
                        {{-- location_id --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label("location", __("kardex.location") . ":") !!}
    
                                @if (is_null($default_location))
                                {!! Form::select("select_location", $locations, $default_location,
                                    ["class" => "form-control select2", "id" => "location"]) !!}
                                @else
                                {!! Form::select("select_location", $locations, null,
                                    ["class" => "form-control select2", "id" => "location", 'disabled']) !!}
                                @endif
                            </div>
                        </div>
    
                        {{-- document_type_id --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label("document_type", __("document_type.title") . ":") !!}
    
                                {!! Form::select("select_document_type", $document_types, null,
                                    ["class" => "form-control select2", "id" => "document_type"]) !!}
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
                                  {!! Form::hidden("start_date", date('Y-m-d', strtotime('- 6 days')), ['id' => 'start_date']) !!}
                                  {!! Form::hidden("end_date", date('Y-m-d'), ['id' => 'end_date']) !!}
                                </div>
                              </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- format --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>@lang('accounting.format')</label>
                                <select name="report_type" id="report_type" class="form-control select2" style="width: 100%" required>
                                    <option value="excel" selected>Excel</option>
                                </select>                       
                            </div>
                        </div>

                        {{-- button --}}
                        <div class="col-sm-3" style="margin-top: 25px;">
                            <div class="form-group">
                                <input type="submit" class="btn btn-success" value="@lang('accounting.generate')" id="button_report">
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
                        <table class="table table-bordered table-striped" id="all_sales_with_utility_report_table">
                            <thead>
                                <tr>
                                    <th>@lang('messages.date')</th>
                                    <th>@lang('report.document_no')</th>
                                    <th>@lang('document_type.document_type')</th>
                                    <th>@lang('sale.customer_name')</th>
                                    <th>@lang('lang_v1.payment_method')</th>
                                    <th>@lang('sale.cost')</th>
                                    <th>@lang('sale.total')</th>
                                    <th>@lang('sale.utility')</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="bg-gray font-17 footer-total text-center">
                                    <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                                    <td></td>
                                    <td><span class="display_currency" id="footer_cost_total" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_sale_total" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_utility_total" data-currency_symbol ="true"></span></td>
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
    <script>
        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'throw';

            dateRangeSettings['startDate'] = moment().subtract(6, 'days');
            dateRangeSettings['endDate'] = moment();
            //Date range as a button
            $('#date_filter').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    
                    let start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    let end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    $("input#start_date").val(start_date);
                    $("input#end_date").val(end_date);

                    all_sales_with_utility_report_table.ajax.reload();
                }
            );
            $('#date_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#date_filter').html('<i class="fa fa-calendar"></i>'+ LANG.filter_by_day);
                
                let start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                let end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                $("input#start_date").val(start_date);
                $("input#end_date").val(end_date);

                all_sales_with_utility_report_table.ajax.reload();
            });

            all_sales_with_utility_report_table = $('table#all_sales_with_utility_report_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[0, 'asc'], [1, 'asc']],
                "ajax": {
                    "url": "/sales-reports/all-sales-with-utility-report",
                    "data": function (d) {
                        d.start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        d.end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.location_id = $("select#location").val();
                        d.document_type_id = $("select#document_type").val();
                    }
                },
                columns: [
                    { data: 'transaction_date', name: 'transaction_date' },
                    { data: 'correlative', name: 'correlative' },
                    { data: 'doc_type', name: 'doc_type' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'payment_method', name: 'payment_method' },
                    { data: 'cost_total', name: 'cost_total'},
                    { data: 'final_total', name: 'final_total'},
                    { data: 'utility', name: 'utility'},
                ],
                "fnDrawCallback": function (oSettings) {
                    $('span#footer_cost_total').text(sum_table_col($('table#all_sales_with_utility_report_table'), 'cost-total'));
                    $('span#footer_sale_total').text(sum_table_col($('table#all_sales_with_utility_report_table'), 'final-total'));
                    $('span#footer_utility_total').text(sum_table_col($('table#all_sales_with_utility_report_table'), 'utility'));

                    __currency_convert_recursively($('table#all_sales_with_utility_report_table'));
                }
            });

            // Location filter
            $('select#location').on('change', function() {
                all_sales_with_utility_report_table.ajax.reload();
            });

            // Document type filter
            $('select#document_type').on('change', function() {
                all_sales_with_utility_report_table.ajax.reload();
            });
        });
    </script>
@endsection
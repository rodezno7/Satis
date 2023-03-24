@extends('layouts.app')
@section('title', __('cxc.cxc'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('cxc.cxc')}}</h1>
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
                    {!! Form::open(['id'=>'form_accounts_receivable_report', 'action' => 'CustomerController@accountsReceivableReport', 'method' => 'post', 'target' => '_blank']) !!}
                    <div class="row">
                        {{-- location_id --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label("location", __("business.location") . ":") !!}
                                {!! Form::select("location_id", $locations, null, ["class" => "form-control", "id" => "location"]) !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label("customer_seller", __("customer.seller")) !!}
                                {!! Form::select("customer_seller", $sellers, null,
                                    ['class' => 'form-control select2', 'id' => 'seller', 'placeholder' => __('customer.all_sellers')]) !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label("customer", __("contact.customer") . ":") !!}
                                {!! Form::select('customer_id', [], null, ['class' => 'form-control',
                                    'placeholder' => __('messages.please_select'), 'id' => 'customer']) !!}
                            </div>
                        </div>
                        {{-- range date --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                <div class="input-group">
                                    <button type="button" class="btn btn-primary" id="date_filter" style="margin-top: 25px;">
                                    <span>
                                        <i class="fa fa-calendar"></i>&nbsp; {{ __('messages.filter_by_date') }}
                                    </span>
                                    <i class="fa fa-caret-down"></i>
                                    </button>
                                    {!! Form::hidden("start_date", date('Y-m-d', strtotime('- 30 days')), ['id' => 'start_date']) !!}
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
                                <select name="report_type" id="report_type" class="form-control" required>
                                    <option value="pdf" selected>PDF</option>
                                    <option value="excel">Excel</option>
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
                        <table class="table table-bordered table-striped" id="accounts_receivable_report_table">
                            <thead>
                                <tr>
                                    <th style="width: 10%; text-align: center;">@lang('lang_v1.reference')</th>
                                    <th style="width: 30%; text-align: center;">@lang('customer.customer')</th>
                                    <th style="width: 10%; text-align: center;">@lang('messages.date')</th>
                                    <th style="width: 10%; text-align: center;">@lang('contact.expire_date')</th>
                                    <th style="text-align: center;">@lang('lang_v1.days')</th>
                                    <th style="width: 12%; text-align: center;">@lang('sale.total')</th>
                                    <th style="width: 12%; text-align: center;">@lang('payment.payments')</th>
                                    <th style="width: 12%; text-align: center;">@lang('payment.debt_amount')</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="bg-gray font-17 footer-total text-center">
                                    <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                                    <td><span class="display_currency" id="footer_total" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_payments" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_receivable_amount" data-currency_symbol ="true"></span></td>
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

            /** get customers */
            $("select#customer").select2({
                ajax: {
                    type: "get",
                    url: "/customers/get_only_customers",
                    dataType: "json",
                    data: function(params){
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                },
                placeholder: LANG.search_customer,
                allowClear: true,
                minimumInputLength: 1,
                escapeMarkup: function (markup) {
                    return markup;
                }
            });

            dateRangeSettings['startDate'] = moment().startOf('month');
            dateRangeSettings['endDate'] = moment().endOf('month');
            //Date range as a button
            $('#date_filter').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    
                    let start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    let end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    $("input#start_date").val(start_date);
                    $("input#end_date").val(end_date);

                    accounts_receivable_report_table.ajax.reload();
                }
            );
            $('#date_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#date_filter').html('<i class="fa fa-calendar"></i>'+ LANG.filter_by_date);
                
                let start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                let end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                $("input#start_date").val(start_date);
                $("input#end_date").val(end_date);

                accounts_receivable_report_table.ajax.reload();
            });

            accounts_receivable_report_table = $('table#accounts_receivable_report_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[2, 'asc']],
                "ajax": {
                    "url": "/accounts-receivable",
                    "data": function (d) {
                        d.start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        d.end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.location_id = $("select#location").val();
                        d.seller_id = $("select#seller").val();
                        d.customer_id = $("select#customer").val();
                    }
                },
                columnDefs: [
                    { "targets": [2, 3, 5, 6, 7], "className": 'td-dt-right' },
                    { "targets": [4], "className": 'td-dt-center' }
                ],
                columns: [
                    { data: 'correlative', name: 'correlative' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'transaction_date', name: 'transaction_date' },
                    { data: 'expire_date', name: 'expire_date' },
                    { data: 'days', name: 'days' },
                    { data: 'final_total', name: 'final_total' },
                    { data: 'payments', name: 'payments' },
                    { data: 'receivable_amount', name: 'receivable_amount' }
                ],
                "fnDrawCallback": function (oSettings) {
                    $('span#footer_total').text(sum_table_col($('table#accounts_receivable_report_table'), 'final_total'));
                    $('span#footer_payments').text(sum_table_col($('table#accounts_receivable_report_table'), 'payments'));
                    $('span#footer_receivable_amount').text(sum_table_col($('table#accounts_receivable_report_table'), 'receivable_amount'));

                    __currency_convert_recursively($('table#accounts_receivable_report_table'));
                }
            });

            // Location filter
            $('select#customer, select#location, select#seller').on('change', function() {
                accounts_receivable_report_table.ajax.reload();
            });
        });
    </script>
@endsection
@extends('layouts.app')
@section('title', __('accounting.general_journal_book'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('accounting.general_journal_book')</h1>
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
                    {!! Form::open(['id'=>'form_journay_book_report', 'action' => 'ReporterController@postGralJournalBook', 'method' => 'post', 'target' => '_blank']) !!}
                    <div class="row">
                        {{-- range date --}}
                        <div class="col-md-3 col-sm-4 col-xs-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <button type="button" class="btn btn-primary" id="date_filter" style="margin-top: 25px;">
                                    <span>
                                        <i class="fa fa-calendar"></i>&nbsp; {{ __('messages.filter_by_date') }}
                                    </span>
                                    <i class="fa fa-caret-down"></i>
                                    </button>
                                    {!! Form::hidden("start_date", date('Y-m-d', strtotime('first day of this month')), ['id' => 'start_date']) !!}
                                    {!! Form::hidden("end_date", date('Y-m-d', strtotime('last day of this month')), ['id' => 'end_date']) !!}
                                </div>
                            </div>
                        </div>
                        
                        {{-- format --}}
                        <div class="col-md-3 col-sm-4 col-xs-6">
                            <div class="form-group">
                                <label>@lang('accounting.format')</label>
                                <select name="report_format" id="report_format" class="form-control" required>
                                    <option value="pdf" selected>PDF</option>
                                    <option value="excel">Excel</option>
                                </select>
                            </div>
                        </div>

                        {{-- button --}}
                        <div class="col-md-3 col-sm-4 col-xs-6" style="margin-top: 25px;">
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
                        <table class="table table-bordered table-striped" id="journal_book_table">
                            <thead>
                                <tr>
                                    <th style="width: 10%; text-align: center;">@lang('accounting.date')</th>
                                    <th style="width: 10%; text-align: center;">@lang('accounting.correlative')</th>
                                    <th style="width: 10%; text-align: center;">@lang('accounting.account')</th>
                                    <th style="text-align: center;">@lang('accounting.description')</th>
                                    <th style="width: 12%; text-align: center;">@lang('accounting.charges')</th>
                                    <th style="width: 12%; text-align: center;">@lang('accounting.payments')</th>
                                </tr>
                            </thead>
                            {{--<tfoot>
                                <tr class="bg-gray font-17 footer-total text-center">
                                    <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                                    <td><span class="display_currency" id="footer_total" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_payments" data-currency_symbol ="true"></span></td>
                                    <td><span class="display_currency" id="footer_receivable_amount" data-currency_symbol ="true"></span></td>
                                </tr>
                            </tfoot>--}}
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
    $(function () {
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

                journal_book.ajax.reload();
            }
        );
        $('#date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_filter').html('<i class="fa fa-calendar"></i>'+ LANG.filter_by_date);
            
            let start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
            let end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
            $("input#start_date").val(start_date);
            $("input#end_date").val(end_date);

            journal_book.ajax.reload();
        });

        var journal_book = $("table#journal_book_table").DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'asc']],
            ajax: {
                url: "/journal-book",
                data: function (d) {
                    d.start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    d.end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
            },
            columns: [
                { data: 'date', name: 'date' },
                { data: 'correlative', name: 'correlative' },
                { data: 'account_code', name: 'account_code' },
                { data: 'account_name', name: 'account_name' },
                { data: 'debit', name: 'debit' },
                { data: 'credit', name: 'credit' },
            ]
        });
    });
</script>
@endsection
@extends('layouts.app')
@section('title', __('report.payment_notes_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.payment_notes_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary" id="accordion">
              <div class="box-header with-border">
                <h3 class="box-title">
                  <a data-toggle="collapse" data-parent="#accordion" href="#collapseFilter">
                    <i class="fa fa-filter" aria-hidden="true"></i> @lang('report.filters')
                  </a>
                </h3>
              </div>
              <div id="collapseFilter" class="panel-collapse active collapse in" aria-expanded="true">
                <div class="box-body">
                  {!! Form::open(['id'=>'form_payment_notes_report', 'action' => 'ReportController@postPaymentNoteReport', 'method' => 'post', 'target' => '_blank']) !!}
                    <div class="row">
                        {{-- location_id --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                                {!! Form::select('location_id', $locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
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

                        {{-- size_font --}}
                        <div class="col-sm-3">
                            <label>@lang('accounting.size_font')</label>
                            <select name="size" id="size" class="form-control select2" style="width: 100%;" required>
                                <option value="7">7</option>
                                <option value="8" selected>8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        {{-- button --}}
                        <div class="col-sm-3 col-sm-offset-6">
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
                    <table class="table table-bordered table-striped" id="payment_note_report_table">
                        <thead>
                            <tr>
                                <th>@lang('messages.date')</th>
                                <th>@lang('lang_v1.payment_note')</th>
                                <th>@lang('contact.customer')</th>
                                <th>@lang('inflow_outflow.document_no')</th>
                                <th>@lang('document_type.title')</th>
                                <th>@lang('accounting.amount')</th>
                                <th>@lang('accounting.balance')</th>
                                <th>@lang('accounting.status')</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="bg-gray text-center footer-total">
                                <td colspan="5"><strong>@lang('sale.total')</strong></td>
                                <td id="footer_total_amount"></td>
                                <td></td>
                                <td></td>
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

        // Datatable
        payment_note_report_table = $('#payment_note_report_table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            "ajax": {
                "url": "/reports/payment-note-report",
                "data": function(d) {
                    d.location_id = $('#location_id').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: [
                { data: 'paid_on', name: 'paid_on' },
                { data: 'note', name: 'note' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'correlative', name: 'correlative' },
                { data: 'document_name', name: 'document_name' },
                { data: 'amount', name: 'amount', searchable: false } ,
                { data: 'balance', name: 'balance', searchable: false },
                { data: 'status', name: 'status', searchable: false }
            ],
            "fnDrawCallback": function(oSettings) {
                $('#footer_total_amount').html(__sum_column($('#payment_note_report_table'), 'amount'));
                __currency_convert_recursively($('#footer_total_amount'));
            }
        });

        // Date filter
        $('#date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));

                var start_date = $('#date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end_date = $('#date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                $('#start_date').val(start_date);
                $('#end_date').val(end_date);

                payment_note_report_table.ajax.reload();
            }
        );

        $('#date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_filter').html('<i class="fa fa-calendar"></i> {{ __("messages.filter_by_date") }}');
            payment_note_report_table.ajax.reload();
        });

        // Reload table when changing params
        $('#form_payment_notes_report #location_id, #form_payment_notes_report #payment_date').change(function() {
            payment_note_report_table.ajax.reload();
        });
    });
</script>
@endsection
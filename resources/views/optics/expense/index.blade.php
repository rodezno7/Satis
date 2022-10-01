@extends('layouts.app')
@section('title', __('expense.expenses'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('expense.expenses')
        <small></small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
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
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                            {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('expense_for', __('expense.expense_for').':') !!}
                            {!! Form::select('expense_for', $users, null, ['class' => 'form-control select2']); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('expense_category_id',__('expense.expense_category').':') !!}
                            {!! Form::select('expense_category_id', $categories, null, ['placeholder' =>
                            __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'expense_category_id']); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('expense_date_range', __('report.date_range') . ':') !!}
                            {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'expense_date_range', 'readonly']); !!}
                        </div>
                    </div>
                </div>
              </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
        	<div class="box">
                <div class="box-header">
                	<h3 class="box-title">@lang('expense.all_expenses')</h3>
                    <div class="box-tools">
                        <a class="btn btn-block btn-primary" href="{{action('Optics\ExpenseController@create')}}">
                        <i class="fa fa-plus"></i> @lang('messages.add')</a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                	<table class="table table-bordered table-striped table-text-center" id="optics_expense_table" width="100%">
                		<thead>
                			<tr>
                				<th>@lang('messages.date')</th>
        						<th>@lang('purchase.ref_no')</th>
                                <th>@lang('expense.expense_category')</th>
                                <th>@lang('business.location')</th>
                                <th>@lang('sale.payment_status')</th>
                                <th>@lang('sale.total_amount')</th>
                                <th>@lang('sale.total_balance_due')
                                <th>@lang('expense.expense_for')</th>
                                <th>@lang('expense.expense_note')</th>
        						<th>@lang('messages.action')</th>
                			</tr>
                		</thead>
                        <tfoot>
                            <tr class="bg-gray font-17 text-center footer-total">
                                <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                                <td id="footer_payment_status_count"></td>
                                <td><span class="display_currency" id="footer_expense_total" data-currency_symbol ="true"></span></td>
                                <td><span class="display_currency" id="footer_total_due" data-currency_symbol ="true"></span></td>
                                <td colspan="3"></td>
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
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
@stop

@section('javascript')
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>

<script>
    // Date filter for expense table
    if ($('#expense_date_range').length == 1) {
        $('#expense_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#expense_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                optics_expense_table.ajax.reload();
            }
        );

        $('#expense_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
            optics_expense_table.ajax.reload();
        });

        $('#expense_date_range').data('daterangepicker').setStartDate(moment().startOf('month'));
        $('#expense_date_range').data('daterangepicker').setEndDate(moment().endOf('month'));
    }

    // Expense table
    optics_expense_table = $('#optics_expense_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [
            [0, 'desc']
        ],
        "ajax": {
            "url": "/expenses",
            "data": function(d) {
                d.expense_for = $('select#expense_for').val();
                d.location_id = $('select#location_id').val();
                d.expense_category_id = $('select#expense_category_id').val();
                d.start_date = $('input#expense_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                d.end_date = $('input#expense_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
        },
        columnDefs: [{
            "targets": [6, 7],
            "orderable": false,
            "searchable": false
        }],
        columns: [
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'ref_no', name: 'ref_no' },
            { data: 'category', name: 'ec.name' },
            { data: 'location_name', name: 'bl.name' },
            { data: 'payment_status', name: 'payment_status' },
            { data: 'final_total', name: 'final_total' },
            { data: 'payment_due', name: 'payment_due' },
            { data: 'expense_for', name: 'expense_for' },
            { data: 'additional_notes', name: 'additional_notes' },
            { data: 'action', name: 'action' }
        ],
        "fnDrawCallback": function(oSettings) {
            var expense_total = sum_table_col($('#optics_expense_table'), 'final-total');

            $('#footer_expense_total').text(expense_total);

            var total_due = sum_table_col($('#optics_expense_table'), 'payment_due');

            $('#footer_total_due').text(total_due);

            $('#footer_payment_status_count').html(__sum_status_html($('#optics_expense_table'), 'payment-status'));

            __currency_convert_recursively($('#optics_expense_table'));
        },
        createdRow: function(row, data, dataIndex) {
            $(row).find('td:eq(4)').attr('class', 'clickable_td');
        }
    });

    $('select#location_id, select#expense_for, select#expense_category_id').on('change', function () {
        optics_expense_table.ajax.reload();
    });

    $(document).on('click', 'a.optics_delete_expense', function (e) {
        e.preventDefault();

        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_expense,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();

                $.ajax({
                    method: "DELETE",
                    url: href,
                    dataType: "json",
                    data: data,
                    success: function(result) {
                        if (result.success === true) {
                            Swal.fire({
                                title: result.msg,
                                icon: "success",
                            });

                            optics_expense_table.ajax.reload();

                        } else {
                            Swal.fire({
                                title: result.msg,
                                icon: "error",
                            });
                        }
                    }
                });
            }
        });
    });
</script>
@endsection
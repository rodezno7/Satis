@extends('layouts.app')
@section('title', __('expense.expenses'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>@lang('expense.expenses')
            <small></small>
        </h1>
        <!-- <ol class="breadcrumb">
                            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                            <li class="active">Here</li>
                        </ol> -->
    </section>

    <!-- Main content -->
    <section class="content no-print">
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
                                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']) !!}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('expense_for', __('expense.expense_for') . ':') !!}
                                    {!! Form::select('expense_for', $users, null, ['class' => 'form-control select2']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('expense_category_id', __('expense.expense_category') . ':') !!}
                                    {!! Form::select('expense_category_id', $categories, null, ['placeholder' => __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'expense_category_id']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('expense_date_range', __('report.date_range') . ':') !!}
                                    {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month'), ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'expense_date_range', 'readonly']) !!}
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
                            {{-- <a class="btn btn-block btn-primary" href="{{action('ExpenseController@create')}}">
                        <i class="fa fa-plus"></i> @lang('messages.add')</a> --}}
                            <button type="button" class="btn btn-block btn-primary btn-modal" id="hola"
                                data-href="{{ action('ExpenseController@create') }}" data-container=".expenses_modal">
                                <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="expense_table">
                                <thead>
                                    <tr>
                                        {{--  --}}
                                        <th>@lang('messages.date')</th>{{-- fecha  --}}
                                        <th @if ($hide_location_column) class="hide-column" @endif>@lang('business.location')</th>
                                        <th>@lang('expense.expense_provider')</th>
                                        <th>@lang('expense.expense_category')</th>
                                        <th>@lang('document_type.title')</th>
                                        <th>@lang('purchase.ref_no')</th> 
                                        <th>@lang('sale.payment_status')</th>
                                        <th>@lang('sale.total_amount')</th>
                                        <th>@lang('messages.action')</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr class="bg-gray font-17 text-center footer-total">
                                        <td colspan="6"><strong>@lang('sale.total'):</strong></td>
                                        <td id="footer_payment_status_count"></td>
                                        <td><span class="display_currency" id="footer_expense_total"
                                                data-currency_symbol="true"></span></td>
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
    <!-- /.content -->
    {{-- Print section --}}
    <section id="receipt_section" class="print_section"></section>
    <div class="modal fade expenses_modal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade payment_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
@stop
@section('javascript')
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
    <script>
        $(document).ready(function() {

            $.fn.modal.Constructor.prototype.enforceFocus = function() {};
        });
        $(document).on('submit', 'form#expense_add_form', function(e) {
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', false);
            var data = $(this).serialize();
            $.ajax({
                method: "POST",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        $("table#expense_table").DataTable().ajax.reload();
                        $('div.expenses_modal').modal('hide');
                        Swal.fire({
                            title: result.msg,
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        $("#content").hide();
                    } else {
                        Swal.fire({
                            title: result.msg,
                            icon: "error",
                        });
                    }
                },
                error: function(msj) {
                    var errormessages = "";
                    $.each(msj.responseJSON.errors, function(i, field) {
                        errormessages += "<li>" + field + "</li>";
                    });
                    Swal.fire({
                        title: "{{ __('customer.errors') }}",
                        icon: "error",
                        html: "<ul>" + errormessages + "</ul>",
                    });
                }
            });
        });

        $(document).on('click', 'a.edit_expense_button', function() {
            $("div.expenses_modal").load($(this).data('href'), function() {
                $(this).modal('show');

                $('form#edit_expense_form').submit(function(e) {
                    e.preventDefault();
                    $(this).find('button[type="submit"]').attr('disabled', false);
                    var data = $(this).serialize();

                    $.ajax({
                        method: "POST",
                        url: $(this).attr("action"),
                        dataType: "json",
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                $("table#expense_table").DataTable().ajax.reload();
                                $('div.expenses_modal').modal('hide');
                                Swal.fire({
                                    title: result.msg,
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                                $('#content').hide();
                            } else {
                                Swal.fire({
                                    title: result.msg,
                                    icon: "error",
                                });
                            }
                        }
                    });
                });
            });
        });

    </script>
@endsection

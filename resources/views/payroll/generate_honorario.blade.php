@extends('layouts.app')
@section('title', __('payroll.payroll'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> @lang('payroll.payroll') de honorarios
            @if ($payroll->payrollStatus->name == 'Aprobada')
                <span class="badge" style="background: #449D44">{{ $payroll->payrollStatus->name }}</span>
            @endif
            @if ($payroll->payrollStatus->name == 'Calculada')
                <span class="badge" style="background: #00A6DC">{{ $payroll->payrollStatus->name }}</span>
            @endif
            <small></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="boxform_u box-solid_u">
            @if ($payroll->payrollStatus->name == 'Calculada')
                <div class="box-header">
                    <h3></h3>
                    <div class="box-tools">
                        <a href="/payroll/{{ $payroll->id }}/exportPayrollSalary" class="btn btn-success" type="button">
                            <i class="fa fa-file"></i> @lang('report.export')
                        </a>
                        <a href="#" class="btn btn-info" type="button"
                            onClick="recalculatePayroll({{ $payroll->id }})">
                            <i class="fa fa-plus"></i> @lang('payroll.recalculate')
                        </a>

                        <a href="#" class="btn btn-primary" type="button"
                            onClick="approvePayroll({{ $payroll->id }})">
                            <i class="fa fa-check-square"></i> @lang('payroll.approve')
                        </a>
                    </div>
                </div>
            @endif
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-hover table-responsive table-condensed table-text-center"
                        style="font-size: inherit; width: 100%" id="payroll-detail-table">
                        <thead>
                            <tr class="active">
                                <th width="15%">@lang('rrhh.employee')</th>
                                <th>@lang('payroll.total_calculation')</th>
                                <th>@lang('payroll.rent')</th>
                                <th>@lang('payroll.total_to_pay')</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr class="bg-gray font-14 footer-total text-center">
                                <td><strong>@lang('report.grand_total')</strong></td>
                                <td>
                                    <span class="display_currency" id="total_salary" data-currency_symbol="true"></span>
                                </td>
                                <td>
                                    <span class="display_currency" id="tota_rent" data-currency_symbol="true"></span>
                                </td>
                                <td>
                                    <span class="display_currency" id="total_total_to_pay"
                                        data-currency_symbol="true"></span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>
        </div>
        <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
        <input type="hidden" name="id" value="{{ $payroll->id }}" id="id">
    </section>

    <div class="modal fade" id="modal_edit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" id="modal_content_edit">

            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            loadPayrolls();
            $.fn.dataTable.ext.errMode = 'none';

            $('#modal_action').on('shown.bs.modal', function() {
                $(this).find('#rrhh_type_personnel_action_id').select2({
                    dropdownParent: $(this),
                })
            })
        });

        function loadPayrolls() {
            var id = $("#id").val();
            var table = $("#payroll-detail-table").DataTable();
            table.destroy();
            var table = $("#payroll-detail-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: "/payroll-getPayrollDetail/" + id,
                columns: [{
                        data: 'employee',
                        name: 'employee1',
                        className: "text-center"
                    },
                    {
                        data: 'salary',
                        name: 'salary',
                        className: "text-center salary"
                    },
                    {
                        data: 'rent',
                        name: 'rent',
                        className: "text-center rent"
                    },
                    {
                        data: 'total_to_pay',
                        name: 'total_to_pay',
                        className: "text-center total_to_pay"
                    },
                ],
                "fnDrawCallback": function(oSettings) {
                    $('span#total_salary').text(sum_table_col_name($('table#payroll-detail-table'), 'salary'));
                    $('span#tota_rent').text(sum_table_col_name($('table#payroll-detail-table'), 'rent'));
                    $('span#total_total_to_pay').text(sum_table_col_name($('table#payroll-detail-table'),
                        'total_to_pay'));
                    __currency_convert_recursively($('table#payroll-detail-table'));
                },
                dom: '<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
            });
        }

        function recalculatePayroll(id) {
            route = "/payroll/" + id + "/recalculate";
            token = $("#token").val();

            $.ajax({
                url: route,
                type: 'GET',
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.success == true) {
                        Swal.fire({
                            title: result.msg,
                            icon: "success",
                            timer: 1000,
                            showConfirmButton: false,
                        });
                        $("#payroll-detail-table").DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire({
                            title: result.msg,
                            icon: "error",
                        });
                    }
                },
                error: function(msj) {
                    errormessages = "";
                    $.each(msj.responseJSON.errors, function(i, field) {
                        errormessages += "<li>" + field + "</li>";
                    });
                    Swal.fire({
                        title: LANG.error_list,
                        icon: "error",
                        html: "<ul>" + errormessages + "</ul>",
                    });
                }
            });
        }

        function approvePayroll(id) {
            Swal.fire({
                title: "{{ __('messages.approve_question') }}",
                text: "{{ __('messages.approve_content') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('messages.yes') }}",
                cancelButtonText: "{{ __('messages.no') }}",
            }).then((willDelete) => {
                if (willDelete.value) {

                    Swal.fire({
                        title: "{{ __('messages.pay_question') }}",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: "{{ __('messages.yes') }}",
                        cancelButtonText: "{{ __('messages.no') }}",
                    }).then((result) => {
                        var sendEmail = 0;
                        if (result.isConfirmed) {
                            sendEmail = 1;
                            sendApprove(id, sendEmail);
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            sendApprove(id, sendEmail);
                        }
                    })
                }
            });
        }

        function sendApprove(id, sendEmail) {
            Swal.fire({
                title: "{{ __('messages.confirm_approval') }}",
                text: "{{ __('messages.message_to_confirm') }}",
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('messages.approve') }}",
                cancelButtonText: "{{ __('messages.cancel') }}",
                showLoaderOnConfirm: true,
                inputValidator: (value) => {
                    if (!value) {
                        return "{{ __('messages.password_required') }}"
                    }
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    var route = "{!! URL::to('/payroll/:id/approve') !!}";
                    route = route.replace(':id', id);
                    token = $("#token").val();

                    $.ajax({
                        url: route,
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            'password': result.value,
                            'sendEmail': sendEmail
                        },
                        success: function(result) {
                            if (result.success == true) {
                                Swal.fire({
                                    title: result.msg,
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                                $("#payroll-detail-table").DataTable().ajax
                                    .reload(null, false);
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: result.msg,
                                    icon: "error",
                                });
                            }
                        },
                        error: function(msj) {
                            errormessages = "";
                            $.each(msj.responseJSON.errors, function(i,
                                field) {
                                errormessages += "<li>" + field +
                                    "</li>";
                            });
                            Swal.fire({
                                title: "@lang('rrhh.error_list')",
                                icon: "error",
                                html: "<ul>" + errormessages +
                                    "</ul>",
                            });
                        }
                    });
                }
            })
        }


        function sum_table_col_name(table, class_name) {
            var sum = 0;

            table.find('tbody').find('tr').each(function() {
                $(this).find('td.' + class_name).each(function() {
                    sum += parseFloat(__number_uf($(this).html(), false));
                });
            });

            return sum;
        }
    </script>
@endsection

@extends('layouts.app')
@section('title', __('payroll.payroll'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header" id="section_content-header">
        <h1> {{ $payroll->payrollType->name }}
            @if ($payroll->payrollStatus->name == 'Calculada')
                <span class="badge" style="background: #00A6DC">{{ $payroll->payrollStatus->name }}</span>
            @endif
            @if ($payroll->payrollStatus->name == 'Aprobada')
                <span class="badge" style="background: #449D44">{{ $payroll->payrollStatus->name }}</span>
            @endif
            @if ($payroll->payrollStatus->name == 'Pagada') 
                <span class="badge" style="background: #367FA9">{{ $payroll->payrollStatus->name }}</span>
            @endif
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <h3></h3>
                <div class="box-tools">
                    @can('payroll.export')
                        <a href="/payroll/{{ $payroll->id }}/exportPayrollSalary" class="btn btn-success" type="button">
                            <i class="fa fa-file"></i> @lang('report.export')
                        </a>
                    @endcan
                    @if ($payroll->payrollStatus->name == 'Calculada')
                        @can('payroll.recalculate')
                            <a href="#" class="btn btn-info" type="button"
                                onClick="recalculatePayroll({{ $payroll->id }})" id="btn_recalculate">
                                <i class="fa fa-plus"></i> @lang('payroll.recalculate')
                            </a>
                        @endcan
                        @can('payroll.approve')
                            <a href="#" class="btn btn-primary" type="button"
                                onClick="approvePayroll({{ $payroll->id }})" id="btn_approve">
                                <i class="fa fa-check-square"></i> @lang('payroll.approve')
                            </a>
                        @endcan
                    @endif
                    @if ($payroll->payrollStatus->name == 'Aprobada')
                        @can('payroll.pay')
                            <a href="#" class="btn btn-primary" type="button"
                                onClick="payPayroll({{ $payroll->id }})" id="btn_pay">
                                <i class="fa fa-check-square"></i> @lang('payroll.pay')
                            </a>
                        @endcan
                    @endif
                </div>
            </div>
            
            @if ($payroll->payrollType->name == 'Planilla de sueldos')
                @include('payroll.generate_salary')
            @endif
            @if ($payroll->payrollType->name == 'Planilla de honorarios')
                @include('payroll.generate_honorary')
            @endif
        </div>

        <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
        <input type="hidden" name="id" value="{{ $payroll->id }}" id="id">
        <input type="hidden" name="type" value="{{ $payroll->payrollType->name }}" id="type">
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

            var type = $('#type').val();
            if(type == 'Planilla de sueldos'){
                var table = $("#payroll-detail-table").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "/payroll-getPayrollDetail/" + id,
                    columns: [
                        {
                            data: 'code',
                            name: 'code',
                            className: "text-center"
                        },    
                        {
                            data: 'employee',
                            name: 'employee',
                            className: "text-center"
                        },
                        {
                            data: 'montly_salary',
                            name: 'days',
                            className: "text-center"
                        },
                        {
                            data: 'days',
                            name: 'days',
                            className: "text-center"
                        },
                        // {
                        //     data: 'hours',
                        //     name: 'hours',
                        //     className: "text-center"
                        // },
                        {
                            data: 'regular_salary',
                            name: 'regular_salary',
                            className: "text-center salary"
                        },
                        {
                            data: 'daytime_overtime',
                            name: 'daytime_overtime',
                            className: "text-center daytime_overtime"
                        },
                        {
                            data: 'night_overtime_hours',
                            name: 'night_overtime_hours',
                            className: "text-center night_overtime_hours"
                        },
                        {
                            data: 'total_hours',
                            name: 'total_hours',
                            className: "text-center total_hours"
                        },
                        {
                            data: 'other_income',
                            name: 'other_income',
                            className: "text-center other_income"
                        },
                        {
                            data: 'subtotal',
                            name: 'subtotal',
                            className: "text-center subtotal"
                        },
                        {
                            data: 'isss',
                            name: 'isss',
                            className: "text-center isss"
                        },
                        {
                            data: 'afp',
                            name: 'afp',
                            className: "text-center afp"
                        },
                        {
                            data: 'rent',
                            name: 'rent',
                            className: "text-center rent"
                        },
                        {
                            data: 'other_deductions',
                            name: 'other_deductions',
                            className: "text-center other_deductions"
                        },
                        {
                            data: 'total_to_pay',
                            name: 'total_to_pay',
                            className: "text-center total_to_pay"
                        },
                    ],
                    "fnDrawCallback": function(oSettings) {
                        $('span#regular_salary').text(sum_table_col_name($('table#payroll-detail-table'), 'salary'));
                        $('span#total_daytime_overtime').text(sum_table_col_name($('table#payroll-detail-table'),
                            'daytime_overtime'));
                        $('span#total_night_overtime_hours').text(sum_table_col_name($(
                            'table#payroll-detail-table'), 'night_overtime_hours'));
                        $('span#total_overtime').text(sum_table_col_name($('table#payroll-detail-table'),
                            'total_hours'));
                        $('span#other_income').text(sum_table_col_name($('table#payroll-detail-table'),
                            'other_income'));
                        $('span#total_subtotal').text(sum_table_col_name($('table#payroll-detail-table'),
                            'subtotal'));
                        $('span#total_isss').text(sum_table_col_name($('table#payroll-detail-table'), 'isss'));
                        $('span#total_afp').text(sum_table_col_name($('table#payroll-detail-table'), 'afp'));
                        $('span#tota_rent').text(sum_table_col_name($('table#payroll-detail-table'), 'rent'));
                        $('span#total_other_deductions').text(sum_table_col_name($('table#payroll-detail-table'),
                            'other_deductions'));
                        $('span#total_total_to_pay').text(sum_table_col_name($('table#payroll-detail-table'),
                            'total_to_pay'));
                        __currency_convert_recursively($('table#payroll-detail-table'));
                    },
                    dom: '<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
                });
            }

            if(type == 'Planilla de honorarios'){
                var table = $("#payroll-detail-table").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "/payroll-getPayrollDetail/" + id,
                    columns: [
                        {
                            data: 'employee',
                            name: 'employee',
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


        function payPayroll(id) {
            Swal.fire({
                title: "{{ __('messages.pay_question') }}",
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
                        title: "{{ __('messages.pay_slips_question') }}",
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
                            sendPay(id, sendEmail);
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            sendPay(id, sendEmail);
                        }
                    })
                }
            });
        }

        function sendPay(id, sendEmail) {
            Swal.fire({
                title: "{{ __('messages.confirm_pay') }}",
                text: "{{ __('messages.message_to_confirm') }}",
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('messages.pay') }}",
                cancelButtonText: "{{ __('messages.cancel') }}",
                showLoaderOnConfirm: true,
                inputValidator: (value) => {
                    if (!value) {
                        return "{{ __('messages.password_required') }}"
                    }
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    var route = "{!! URL::to('/payroll/:id/pay') !!}";
                    var type = $('#type').val();
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
                                $('#btn_pay').hide();
                                $("#section_content-header").find('h1').remove();
                                $("#section_content-header").append('<h1>'+type+' <span class="badge" style="background: #367FA9">Pagada</span></h1>');
                                $("#payroll-detail-table").DataTable().ajax.reload(null, false);
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
                    var type = $('#type').val();
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
                                $('#btn_recalculate').hide();
                                $('#btn_approve').hide();
                                $("#section_content-header").find('h1').remove();
                                $("#section_content-header").append('<h1>'+type+' <span class="badge" style="background: #449D44">Aprobada</span></h1>');
                                $("#payroll-detail-table").DataTable().ajax.reload(null, false);
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

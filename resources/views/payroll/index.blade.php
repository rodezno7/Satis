@extends('layouts.app')
@section('title', __('payroll.payroll'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> @lang('payroll.payroll')
            <small></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <h3 class="box-title"></h3>
                <div class="box-tools">
                    @can('payroll.create')
                        <a href="#" class="btn btn-primary" type="button" id="btn_add" onClick="addPayroll()">
                            <i class="fa fa-plus"></i> @lang('messages.add')
                        </a>
                    @endcan
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover" id="payroll-table"
                        width="100%">
                        <thead>
                            <th width="15%">@lang('payroll.type')</th>
                            <th width="20%">@lang('payroll.name')</th>
                            <th width="20%">@lang('payroll.period')</th>
                            <th>@lang('payroll.payment_period')</th>
                            <th>@lang('payroll.ISR_apply')</th>
                            <th>@lang('payroll.status')</th>
                            <th width="11%">@lang('payroll.actions')</th>
                        </thead>
                    </table>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modal_edit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" id="modal_content_edit">

            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_add" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" id="modal_content_add">

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
            var table = $("#payroll-table").DataTable();
            table.destroy();
            var table = $("#payroll-table").DataTable({
                select: true,
                deferRender: true,
                processing: true,
                serverSide: true,
                ajax: "/payroll-getPayrolls",
                columns: [
                    {
                        data: 'type',
                        name: 'type',
                        className: "text-center"
                    },
                    {
                        data: 'name',
                        name: 'name',
                        className: "text-center"
                    },
                    {
                        data: 'period',
                        name: 'period',
                        className: "text-center"
                    },
                    {
                        data: 'payment_period',
                        name: 'payment_period',
                        className: "text-center"
                    },
                    {
                        data: 'isr',
                        name: 'isr',
                        className: "text-center"
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: "text-center"
                    },
                    {
                        data: null,
                        render: function(data) {
                            html =
                                '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> @lang('messages.actions') <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu">';

                            html += '<li><a href="/payroll/' + data.id +
                                '"><i class="fa fa-eye"></i>@lang('payroll.view_detail')</a></li>';

                            
                            if (data.statusPayroll == 'Calculada') {
                                @can('payroll.recalculate')
                                    html += '<li><a href="#" onClick="recalculatePayroll('+data.id+
                                    ')"><i class="fa fa-plus"></i>{{ __('payroll.recalculate') }}</a></li>';
                                @endcan
                                
                                @can('payroll.approve')
                                    html += '<li><a href="#" onClick="approvePayroll(' + data.id +
                                    ')"><i class="fa fa-check-square"></i>{{ __('payroll.approve') }}</a></li>';
                                @endcan
                            }

                            @can('payroll.export')
                                html += '<li><a href="/payroll/' + data.id +
                                '/exportPayroll"><i class="fa fa-file"></i>@lang('report.export')</a></li>';
                            @endcan

                            if (data.statusPayroll == 'Aprobada') {
                                html += '<li><a href="#" onClick="payPayroll('+ data.id +')"><i class="fa fa-money"></i>@lang('payroll.pay')</a></li>';
                            }

                            if (data.statusPayroll == 'Aprobada' || data.statusPayroll == 'Pagada') {
                                html += '<li><a href="#" onClick="sendPaymentSlips('+ data.id +')"><i class="fa fa-credit-card-alt"></i>@lang('payroll.send_payment_slips1')</a></li>';
                                
                                html += '<li><a href="/payroll/' + data.id +'/generatePaymentSlips" target="_blank"><i class="fa fa-print"></i>@lang('payroll.print_payment_slips')</a></li>';
                                html += '<li><a href="/payroll/' + data.id +'/generatePaymentFiles" target="_blank" id="generatePaymentFile"><i class="fa fa-credit-card-alt"></i>Generar archivos de pago</a></li>';
                            
                            }
                            html += '</ul></div>';

                            return html;
                        },
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    }
                ],
                dom: '<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
            });
        }

        function recalculatePayroll(id) {
            route = "/payroll/" + id + "/recalculate";
            token = $("#token").val();

            $.ajax({
                url: route,
                headers: {
                    'X-CSRF-TOKEN': token
                },
                type: 'POST',
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


        function sendPaymentSlips(id) {
            Swal.fire({
                title: "{{ __('messages.payment_slips_question') }}",
                //text: "{{ __('messages.approve_content') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('messages.yes') }}",
                cancelButtonText: "{{ __('messages.no') }}",
            }).then((result) => {
                if (result.isConfirmed) {
                    var route = "{!! URL::to('/payroll/:id/paymentSlips') !!}";
                    route = route.replace(':id', id);
                    token = $("#token").val();

                    $.ajax({
                        url: route,
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        type: 'POST',
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == true) {
                                Swal.fire({
                                    title: result.msg,
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                                $("#payroll-table").DataTable().ajax.reload(null, false);
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

        function generatePaymentFiles(id) {
            Swal.fire({
                title: "{{ __('messages.payment_file_question') }}",
                //text: "{{ __('messages.approve_content') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('messages.yes') }}",
                cancelButtonText: "{{ __('messages.no') }}",
            }).then((result) => {
                if (result.isConfirmed) {
                    var route = "{!! URL::to('/payroll/:id/generatePaymentFiles') !!}";
                    route = route.replace(':id', id);
                    token = $("#token").val();

                    $.ajax({
                        url: route,
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        type: 'POST',
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == true) {
                                Swal.fire({
                                    title: result.msg,
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                                $("#payroll-table").DataTable().ajax.reload(null, false);
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
                                $("#payroll-table").DataTable().ajax.reload(null, false);
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
                        title: "{{ __('messages.payment_file_question') }}",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: "{{ __('messages.yes') }}",
                        cancelButtonText: "{{ __('messages.no') }}",
                    }).then((result) => {
                        var sendEmail = 0;
                        if (result.isConfirmed) {
                            downloadFile = 1;
                            downloadFlie(id, downloadFile);
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            downloadFlie(id, downloadFile);
                        }
                    })
                }
            });
        }

        function downloadFlie(id, downloadFile) {
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
                            'downloadFile': downloadFile
                        },
                        success: function(result) {
                            if (result.success == true) {
                                $("#payroll-table").DataTable().ajax.reload(null, false);

                                if(result.download == true){
                                    var a = document.createElement('a');
                                    a.href = "/payroll/" + id +"/generatePaymentFiles";
                                    a.download = 'your_pdf_name.pdf';
                                    a.click();
                                    window.URL.revokeObjectURL(url);
                                }
                                
                                Swal.fire({
                                    title: result.msg,
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                                
                                
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

        function addPayroll() {
            $("#modal_content_add").html('');
            var url = "{!! URL::to('/payroll/create') !!}";
            $.get(url, function(data) {
                $("#modal_content_add").html(data);
                $('#modal_add').modal({
                    backdrop: 'static'
                });
            });
        }
    </script>
@endsection

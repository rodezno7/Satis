@extends('layouts.app')
@section('title', __('planilla.planilla'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> @lang('planilla.planilla')
            <small></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <h3 class="box-title"></h3>
                <div class="box-tools">
                    @can('planilla.create')
                        <a href="#" class="btn btn-primary" type="button" id="btn_add" onClick="addPlanilla()">
                            <i class="fa fa-plus"></i> @lang('messages.add')
                        </a>
                    @endcan
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover" id="planilla-table"
                        width="100%">
                        <thead>
                            <th>@lang('planilla.type')</th>
                            <th>@lang('planilla.name')</th>
                            <th>@lang('planilla.period')</th>
                            <th width="15%">@lang('planilla.payment_period')</th>
                            <th>@lang('planilla.status')</th>
                            <th width="12%">@lang('planilla.actions')</th>
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
            loadPlanillas();
            $.fn.dataTable.ext.errMode = 'none';

            $('#modal_action').on('shown.bs.modal', function() {
                $(this).find('#rrhh_type_personnel_action_id').select2({
                    dropdownParent: $(this),
                })
            })
        });

        function loadPlanillas() {
            var table = $("#planilla-table").DataTable();
            table.destroy();
            var table = $("#planilla-table").DataTable({
                select: true,
                deferRender: true,
                processing: true,
                serverSide: true,
                ajax: "/payroll-getPlanillas",
                columns: [{
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
                                '"><i class="fa fa-eye"></i>@lang('planilla.view_detail')</a></li>';

                            
                            if (data.statusPlanilla == 'Calculada') {
                                html += '<li><a href="#" onClick="recalculatePlanilla('+data.id+
                                    ')"><i class="fa fa-plus"></i>{{ __('planilla.recalculate') }}</a></li>';
                                
                                html += '<li><a href="#" onClick="approvePlanilla(' + data.id +
                                    ')"><i class="fa fa-check-square"></i>{{ __('planilla.approve') }}</a></li>';
                            }

                            html += '<li><a href="/payroll/' + data.id +
                                '/exportPayrollSalary"><i class="fa fa-file"></i>@lang('report.export')</a></li>';
                            
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

        function recalculatePlanilla(id) {
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

        function approvePlanilla(id) {
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
                                $("#planilla-table").DataTable().ajax
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

        function addPlanilla() {
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

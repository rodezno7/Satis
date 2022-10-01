@extends('layouts.app')
@section('title', __('employees.employees'))
    <link rel="stylesheet" href="{{ asset('accounting/css/jquery-confirm.min.css') }}">
    <script th:src="@{/js/datatables.min.js}"></script>
    <style>
        .swal2-popup {
            font-size: 1.4rem !important;
        }

    </style>
@section('content')

    <!-- Contect Header (Page Header) -->
    <section class="content-header">
        <h1>@lang( 'employees.employees' )<small>@lang( 'employees.manage_employees' )</small></h1>
    </section>

    <section class="content">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <h3 class="box-title">@lang( 'employees.all_employees' )</h3>
                @can('employees.create')
                    <div class="box-tools">
                        <button type="button" class="btn btn-block btn-primary btn-modal"
                            data-href="{{ action('ManageEmployeesController@create') }}" data-container=".employees_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                    </div>
                @endcan
            </div>
            <div class="box-body">
                @can('employees.view')
                    <div id="lista" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-condensed table-hover" id="employees_table"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('employees.full_name')</th>
                                        <th>@lang('employees.email')</th>
                                        <th>@lang('business.location')</th>
                                        <th>@lang('employees.position')</th>
                                        <th>@lang('customer.employee_code')</th>
                                        <th>@lang('employees.hired_date')</th>
                                        <th>@lang('messages.actions')</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                @endcan
            </div>
        </div>

        <div class="modal fade employees_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
@endsection
@section('javascript')
    <script type="text/javascript" src="{{ asset('js/sweetalert2.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            var positions_table = $('#employees_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/employees/getEmployeesData',
                columns: [
                    { data: 'full_name', name: 'full_name' },
                    { data: 'email', name: 'email' },
                    { data: 'location_name', name: 'business_locations.name' },
                    { data: 'position', name: 'positions.name' },
                    { data: 'agent_code', name: 'agent_code' },
                    { data: 'hired_date', name: 'hired_date' },
                    { data: 'action', name: 'action' }
                ],
                columnDefs: [{
                    "targets": [0, 1, 2, 3, 4, 5, 6],
                    "className": "text-center",
                    "orderable": true,
                    "searchable": true
                }]
            });

            $.extend($.fn.dataTableExt.oStdClasses, {
                "sFilterInput": "inputform2"
            });

        });

        $('.employees_modal').on('shown.bs.modal', function() {

            $('#hired_date').datepicker({
                autoclose: true,
                format: datepicker_date_format
            });

            $('#birth_date').datepicker({
                autoclose: true,
                format: datepicker_date_format
            });

            $('#fired_date').datepicker({
                autoclose: true,
                format: datepicker_date_format
            });

            // On change of agent code input
            $('input#agent_code').on('change', function () {
                let agent_code = $(this).val();
                let employee_id = $('#employee_id').length > 0 ? $('#employee_id').val() : 0;
                let route = '/employees/verify-if-exists-agent-code';
                let form = $(this).closest('form');

                form.find('button[type="submit"]').attr('disabled', false);

                if (agent_code != '') {
                    $.ajax({
                        method: 'get',
                        url: route,
                        data: {
                            agent_code: agent_code,
                            employee_id: employee_id
                        },
                        dataType: 'json',
                        success: function (result) {
                            if (result.success == true) {
                                Swal.fire({
                                    title: result.msg,
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 2000
                                });

                            } else {
                                Swal.fire({
                                    title: result.msg,
                                    icon: 'error',
                                    showConfirmButton: false,
                                    timer: 2000
                                });

                                form.find('button[type="submit"]').attr('disabled', true);
                            }
                        }
                    });
                }
            });

        });

        $(document).on('submit', 'form#employees_add_form', function(e) {
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            var data = $(this).serialize();
            $.ajax({
                method: "POST",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        $("#employees_table").DataTable().ajax.reload();
                        $('div.employees_modal').modal('hide');
                        Swal.fire({
                            title: "{{ __('employees.added_success') }}",
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
                }
            });
        });

        $(document).on('click', 'button.edit_employees_button', function() {
            $("div.employees_modal").load($(this).data('href'), function() {
                $(this).modal('show');

                $('form#employees_edit_form').submit(function(e) {
                    e.preventDefault();
                    $(this).find('button[type="submit"]').attr('disabled', true);
                    var data = $(this).serialize();

                    $.ajax({
                        method: "POST",
                        url: $(this).attr("action"),
                        dataType: "json",
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                $("#employees_table").DataTable().ajax.reload();
                                $('div.employees_modal').modal('hide');
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

        $(document).on('click', 'button.delete_employees_button', function() {
            Swal.fire({
                title: "{{ __('employees.tittle_confirm_delete') }}",
                text: "{{ __('positions.text_confirm_delete') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('employees.confirm_button_delete') }}"
            }).then((willDelete) => {
                if (willDelete.value) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();
                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                $("#employees_table").DataTable().ajax.reload();
                                $('div.employees_modal').modal('hide');
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
                }
            });
        });

        function showUserOption() {
            if ($("#chk_has_user").is(":checked")) {
                $("#chk_has_user").val('has_user');
                $("#user_modal_option").show();
                $("#username").prop('required', true);
                $("#role").prop('required', true);
            } else {
                $("#chk_has_user").val('0');
                $("#user_modal_option").hide();
                $("#username").prop('required', false);
                $("#role").prop('required', false);
            }
        }

        function commision_enable() {
            if ($("#chk_commission").is(":checked")) {
                $("#chk_commission").val('has_commission');
                $("#commision_div").show();
                $("#commision_amount").prop('required', true);
                $("#commision_amount").focus();
            } else {
                $("#chk_commission").val('0');
                $("#commision_div").hide();
                $("#commision_amount").prop('required', false);
                $("#commision_amount").val('');
            }
        }

        function showPassMode() {
            if ($("#rdb_pass_manual").is(":checked")) {
                $("#pass_mode").show();
            } else if ($("#rdb_pass_auto").is(":checked")) {
                $("#pass_mode").hide();
            }
        }

    </script>
@endsection

@extends('layouts.app')
@section('title', __('business.business_type'))
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
        <h1><small>@lang( 'business.manage_business' )</small></h1>
    </section>

    <section class="content">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <h3 class="box-title">@lang( 'business.all_business' )</h3>
                @can('employees.create')
                    <div class="box-tools">
                        <button type="button" class="btn btn-block btn-primary btn-modal"
                            data-href="{{ action('BusinessTypeController@create') }}" data-container=".employees_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                    </div>
                @endcan
            </div>
            <div class="box-body">
                @can('employees.view')
                    <div id="lista" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-condensed table-hover"
                                id="busyness_type_table" width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('business.business_name')</th>
                                        <th>@lang('business.description')</th>
                                        <th>@lang('business.actions')</th>
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
        function dataTableBusiness() {
            var customer_table = $("#busyness_type_table").DataTable({
                pageLength: 25,
                //deferRender: true,
                processing: true,
                serverSide: true,
                ajax: "/business_types/get-data",
                columns: [{
                        data: 'name'
                    },
                    {
                        data: 'description'
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                    "targets": '_all',
                    "className": "text-center"
                }]
            });

        }
        $(document).ready(function() {
            dataTableBusiness();
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

        });

        $(document).on('submit', 'form#business_type_add_form', function(e) {
            debugger;
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
                        $("#busyness_type_table").DataTable().ajax.reload();
                        $('div.employees_modal').modal('hide');
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
                }
            });
        });

        $(document).on('click', 'a.edit_business_type_button', function() {
            $("div.employees_modal").load($(this).data('href'), function() {
                $(this).modal('show');

                $('form#business_type_edit_form').submit(function(e) {
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
                                $("#busyness_type_table").DataTable().ajax.reload();
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
                                $("#busyness_type_table").DataTable().ajax.reload();
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

        function deleteBusinessType(id) {
            Swal.fire({
                title: LANG.sure,
                text: "{{ __('messages.delete_content') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('messages.accept') }}",
                cancelButtonText: "{{ __('messages.cancel') }}"
            }).then((willDelete) => {
                if (willDelete.value) {
                    route = '/business_types/' + id;
                    $.ajax({
                        url: route,
                        type: 'DELETE',
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == true) {
                                Swal.fire({
                                    title: result.msg,
                                    icon: "success",
                                    timer: 3000,
                                    showConfirmButton: false,
                                });
                                $("#busyness_type_table").DataTable().ajax.reload(null, false);
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
        }

    </script>
@endsection

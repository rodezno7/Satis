@extends('layouts.app')
@section('title', __('positions.positions'))
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
        <h1>@lang( 'positions.positions' )<small>@lang( 'positions.manage_positions' )</small></h1>
    </section>

    <section class="content">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <h3 class="box-title">@lang( 'positions.all_positions' )</h3>
                @can('positions.create')
                    <div class="box-tools">
                        <button type="button" class="btn btn-block btn-primary btn-modal"
                            data-href="{{ action('ManagePositionsController@create') }}" data-container=".positions_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                    </div>
                @endcan
            </div>
            <div class="box-body">
                @can('positions.view')
                    <div id="lista" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-condensed table-hover" id="positions_table"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('positions.name')</th>
                                        <th>@lang('positions.description')</th>
                                        <th>@lang('messages.actions')</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                @endcan
            </div>
        </div>

        <div class="modal fade positions_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
@endsection
@section('javascript')
    <script type="text/javascript" src="{{ asset('js/sweetalert2.js') }}"></script>
    <script type="text/javascript" src="{{ asset('accounting/js/jquery-confirm.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            var positions_table = $('#positions_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/positions/getPositionsData',
                columnDefs: [{
                    "targets": [0, 1, 2],
                    "className": "text-center",
                    "orderable": true,
                    "searchable": true
                }]
            });

            // $('.dataTables_filter input[type="search"]').className('.inputform2');

            $.extend($.fn.dataTableExt.oStdClasses, {
                "sFilterInput": "inputform2"
            });

        });

        $(document).on('submit', 'form#positions_add_form', function(e) {
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
                        $("#positions_table").DataTable().ajax.reload();
                        $('div.positions_modal').modal('hide');
                        Swal.fire({
                            title: "{{ __('positions.added_success') }}",
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        $('#content').hide();
                    } else {
                        Swal.fire({
                            title: "{{ __('messages.errors') }}",
                            icon: "error",
                        });
                    }
                }
            });
        });

        $(document).on('click', 'button.edit_positions_button', function() {

            $("div.positions_modal").load($(this).data('href'), function() {

                $(this).modal('show');

                $('form#positions_edit_form').submit(function(e) {
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
                                $("#positions_table").DataTable().ajax.reload();
                                $('div.positions_modal').modal('hide');
                                Swal.fire({
                                    title: "{{ __('positions.updated_success') }}",
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                                $('#content').hide();
                            } else {
                                Swal.fire({
                                    title: "{{ __('messages.errors') }}",
                                    icon: "error",
                                });
                            }
                        }
                    });
                });
            });
        });

        $(document).on('click', 'button.delete_positions_button', function() {

            Swal.fire({
                title: "{{ __('positions.tittle_confirm_delete') }}",
                text: "{{ __('positions.text_confirm_delete') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('positions.confirm_button_delete') }}"
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
                                $("#positions_table").DataTable().ajax.reload();
                                $('div.positions_modal').modal('hide');
                                Swal.fire({
                                    title: "{{ __('positions.deleted_success') }}",
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                                $('#content').hide();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });

    </script>
@endsection

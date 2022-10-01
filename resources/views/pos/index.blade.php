@extends('layouts.app')
@section('title', __('card_pos.card_pos'))
    <link rel="stylesheet" href="{{ asset('accounting/css/jquery-confirm.min.css') }}">
    <script th:src="@{/js/datatables.min.js}"></script>
    <style>
        .swal2-popup {
            font-size: 1.4rem !important;
            color: rgb(50, 243, 50);
        }
        

    </style>
@section('content')

    <!-- Contect Header (Page Header) -->
    <section class="content-header">
        <h1>@lang( 'card_pos.manage' )</small></h1>
    </section>

    <section class="content">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <h3 class="box-title">@lang( 'card_pos.card_pos' )</h3>
                @can('pos.create')
                    <div class="box-tools">
                        <button type="button" class="btn btn-block btn-primary btn-modal"
                            data-href="{{ action('PosController@create') }}" data-container=".pos_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                    </div>
                @endcan
            </div>
            <div class="box-body">
                @can('pos.view')
                <div class="row">
                    <div id="lista" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-condensed table-hover table-text-center"
                                id="pos_table" width="100%" style="font-size: inherit;">
                                <thead>
                                    <tr>
                                        <th>@lang('card_pos.name')</th>
                                        <th>@lang('card_pos.description')</th>
                                        <th>@lang('card_pos.brand')</th>
                                        <th>@lang('card_pos.bank')</th>
                                        <th>@lang('card_pos.business')</th>
                                        <th>@lang('card_pos.employee')</th>
                                        <th>@lang('card_pos.status')</th>
                                        <th>@lang('card_pos.action')</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                @endcan
            </div>
        </div>

        <div class="modal fade pos_modal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
@endsection
@section('javascript')
    <script type="text/javascript" src="{{ asset('js/sweetalert2.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            dataTablePos();
        });

        function dataTablePos() {
            var pos = $("#pos_table").DataTable({
                pageLength: 25,
                //deferRender: true,
                processing: true,
                serverSide: true,
                ajax: "/terminal",
                columns: [
                    {data: 'name'},
                    {data: 'description'},
                    {data: 'brand'},
                    {data: 'bank_name', name: 'banks.name'},
                    {data: 'business_name', name: 'bl.name', orderable: false, searchable: false},
                    {data: 'firstname', name: 'employees.first_name'},
                    {data: 'status', orderable: false, searchable: false},
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

        //funcion para agregar una nueva terminal 
        $(document).on('submit', 'form#pos_add_form', function(e) {
            // debugger;
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
                        $("#pos_table").DataTable().ajax.reload();
                        $('div.pos_modal').modal('hide');
                        Swal.fire({
                            title: "{{ __('payment.payment_terms_create') }}",
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
                    $("#btn-add-customer").prop('disabled', false);
                    $("#btn-close-modal-add-customer").prop('disabled', false);
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

        //funcion para editar una terminal
        $(document).on('click', 'a.edit_pos_button', function() {
            $("div.pos_modal").load($(this).data('href'), function() {
                $(this).modal('show');

                $('form#pos_edit_form').submit(function(e) {
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
                                $("#pos_table").DataTable().ajax.reload();
                                $('div.pos_modal').modal('hide');
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
                        },
                        error: function(msj) {
                            $(this).find('button[type="submit"]').attr('disabled', false);
                            var errormessages = "";
                            $.each(msj.responseJSON.errors, function (i, field) {
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
            });
        });

        //funcion para eliminar una terminal
        function deletePos(id) {
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
                    route = '/terminal/' + id;
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
                                $("#pos_table").DataTable().ajax.reload(null, false);
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

        //funciona para anular una terminal
        function anulPos(id) {
            Swal.fire({
                title: LANG.sure,
                text: "{{ __('card_pos.anull') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('messages.accept') }}",
                cancelButtonText: "{{ __('messages.cancel') }}"
            }).then((willDelete) => {
                if (willDelete.value) {
                    route = '/terminal/anull/' + id;
                    $.ajax({
                        url: route,
                        type: 'POST',
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == true) {
                                Swal.fire({
                                    title: result.msg,
                                    icon: "success",
                                    timer: 3000,
                                    showConfirmButton: false,
                                });
                                $("#pos_table").DataTable().ajax.reload(null, false);
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
        //funcion para activar una terminal
        function activePos(id) {
            Swal.fire({
                title: LANG.sure,
                text: "{{ __('card_pos.enable') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('messages.accept') }}",
                cancelButtonText: "{{ __('messages.cancel') }}"
            }).then((willDelete) => {
                if (willDelete.value) {
                    route = '/terminal/activate/' + id;
                    $.ajax({
                        url: route,
                        type: 'POST',
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == true) {
                                Swal.fire({
                                    title: result.msg,
                                    icon: "success",
                                    timer: 3000,
                                    showConfirmButton: false,
                                });
                                $("#pos_table").DataTable().ajax.reload(null, false);
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

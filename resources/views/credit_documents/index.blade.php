@extends('layouts.app')
@section('title', __('cxc.cdocs'))
    <script th:src="@{/js/datatables.min.js}"></script>
@section('content')
    <section class="content-header">
        <h1>@lang('cxc.cdocs')<br>
            <small>@lang('cxc.manage_cdocs')</small>
        </h1>
    </section>
    <section class="content">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal"
                        data-href="{{ action('CreditDocumentsController@create') }}" data-container=".cdocs_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            </div><br>
            <div class="box-body">
                @can('cdocs.view')
                    <div id="lista" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-stripe table-bordered table-condensed table-hover" id="cdocs_table"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('cxc.date')</th>
                                        <th>@lang('cxc.customer')</th>
                                        <th>@lang('cxc.doctypes')</th>
                                        <th>@lang('cxc.invoice')</th>
                                        <th>@lang('messages.actions')</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
        {{-- Div para renderizar el modal --}}
        <div class="modal fade cdocs_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false"
            aria-labelledby="gridSystemModalLabel"></div>
    </section>
@endsection
@section('javascript')
    <script type="text/javascript">
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};

        //Inicializar componentes del modal
        $('div.cdocs_modal').on('shown.bs.modal', function() {
            $(this).find(".select2").select2();
        });

        $(document).ready(function() {
            var cdocs_table = $('#cdocs_table').DataTable({
                processing: true,
                serverSide: true,
                type: 'GET',
                ajax: '/credit-documents/getCDocsData',
                columns: [{
                        data: 'date'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'document_name'
                    },
                    {
                        data: 'correlative'
                    },
                    {
                        data: 'action'
                    }
                ],
                columnDefs: [{
                    "targets": [0, 1, 2, 3, 4],
                    "className": "text-center",
                    "searchable": true
                }, {
                    orderable: false,
                    targets: 4
                }]
            });
        });

        function SearchInvoice() {
            var invoice = $("#invoice").val();
            var doctype = $("#document_types").val();
            if (invoice == '' || doctype == '') {
                Swal.fire({
                    title: "{{ __('cxc.requirements_msg') }}",
                    icon: "error",
                    timer: 2000,
                    showConfirmButton: false,
                });
            } else {
                var route = "/credit-documents/getTransactionByInvoice/" + invoice + "/" + doctype;
                $.get(route, function(res) {
                    if (res.success) {
                        if (res.inv) {
                            if (res.found == 1) {
                                $("#transaction_id").val(res.id);
                                $("#amount").val("$" + res.amount);
                                $("#date").val(res.date);
                                $("#customer").val(res.customer);
                            } else {
                                Swal.fire({
                                    title: res.msg,
                                    icon: "warning",
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                            }
                        }
                    } else {
                        Swal.fire({
                            title: res.msg,
                            icon: "error",
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                });
            }
        }

        $(document).on('submit', 'form#cdocs_add_form', function(e) {
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            var data = $(this).serialize();
            $.ajax({
                method: "POST",
                url: $(this).attr("action"),
                datatype: "json",
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        $("#cdocs_table").DataTable().ajax.reload();
                        $('div.cdocs_modal').modal('hide');
                        Swal.fire({
                            title: result.msg,
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    } else {
                        Swal.fire({
                            title: result.msg,
                            icon: "error",
                        });
                    }
                }
            });
        });

        $(document).on('click', 'a.reception_cdocs_button', function() {
            $("div.cdocs_modal").load($(this).data('href'), function() {
                $(this).modal('show');

                $('form#cdocs_reception_form').submit(function(e) {
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
                                $("#cdocs_table").DataTable().ajax.reload();
                                $('div.cdocs_modal').modal('hide');
                                Swal.fire({
                                    title: result.msg,
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                            } else {
                                $("#cdocs_table").DataTable().ajax.reload();
                                $('div.cdocs_modal').modal('hide');
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

        $(document).on('click', 'a.custodian_cdocs_button', function() {
            $("div.cdocs_modal").load($(this).data('href'), function() {
                $(this).modal('show');

                $('form#cdocs_custodian_form').submit(function(e) {
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
                                $("#cdocs_table").DataTable().ajax.reload();
                                $('div.cdocs_modal').modal('hide');
                                Swal.fire({
                                    title: result.msg,
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
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

        $(document).on('click', 'a.show_cdocs_button', function() {
            $("div.cdocs_modal").load($(this).data('href'), function() {
                $(this).modal('show');
                var route = $(this).attr('action'); // show

                $.get(route, function(res) {
                    console.log(res);
                });
            });
        });


        $(document).on('click', 'a.edit_cdocs_button', function() {
            $("div.cdocs_modal").load($(this).data('href'), function() {
                $(this).modal({
                    backdrop: 'static'
                });
            });
        });

        $(document).on('submit', 'form#form-edit-credit-documents', function(e) {
            e.preventDefault();
            $("#btn-edit-credit-documents").prop('disabled', true);
            $("#btn-close-modal-edit-credit-d").prop('disabled', true);
            var data = $(this).serialize();
            id = $("#credit_document_id").val();
            route = "/credit-documents/" + id;
            token = $("#token").val();
            $.ajax({
                url: route,
                headers: {
                    'X-CSRF-TOKEN': token
                },
                type: 'PUT',
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        $("#btn-edit-credit-documents").prop('disabled', false);
                        $("#btn-close-modal-edit-credit-d").prop('disabled', false);
                        $("#cdocs_table").DataTable().ajax.reload();
                        $('div.cdocs_modal').modal('hide');

                        Swal.fire({
                            title: result.msg,
                            icon: "success",
                            timer: 3000,
                            showConfirmButton: false,
                        });
                    } else {
                        $("#btn-edit-credit-documents").prop('disabled', false);
                        $("#btn-close-modal-edit-credit-d").prop('disabled', false);
                        Swal.fire({
                            title: result.msg,
                            icon: "error",
                        });
                    }
                },
                error: function(msj) {
                    $("#btn-edit-credit-documents").prop('disabled', false);
                    $("#btn-close-modal-edit-credit-d").prop('disabled', false);
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

        //Delete 

        function deleteCreditDocuments(id) {
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
                    route = '/credit-documents/' + id;
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
                                $("#cdocs_table").DataTable().ajax.reload(null, false);
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

@extends('layouts.app')
@section('title', __('quote.reasons_lost_sale'))
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
        <h1>@lang('quote.manage_reason')</small></h1>
    </section>

    <section class="content">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <h3 class="box-title">@lang('quote.reasons_lost_sale')</h3>
                @can('pos.create')
                    <div class="box-tools">
                        <button type="button" class="btn btn-block btn-primary btn-modal"
                            data-href="{{ action('ReasonController@create') }}" data-container=".reason_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                    </div>
                @endcan
            </div>
            <div class="box-body">
                @can('pos.view')
                    <div id="lista" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            
                            <table class="table table-striped table-bordered table-condensed table-hover"
                                id="reason_table" width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('Razón')</th>
                                        <th>@lang('Descripción')</th>
                                        <th>@lang('card_pos.action')</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                @endcan
            </div>
        </div>

        <div class="modal fade reason_modal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="gridSystemModalLabel">
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
            $("#reason_table").DataTable({
                pageLength: 25,
                //deferRender: true,
                processing: true,
                serverSide: true,
                ajax: "/quote/reason",
                columns: [
                    {data: 'reason'},
                    {data: 'comments'},
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

        //funcion para agregar un motivo 
        $(document).on('submit', 'form#reason_add_form', function(e) {
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
                        $("#reason_table").DataTable().ajax.reload();
                        $('div.reason_modal').modal('hide');
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
                    // $("#btn-add-customer").prop('disabled', false);
                    // $("#btn-close-modal-add-customer").prop('disabled', false);
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

        //funcion para editar un motivo
        $(document).on('click', 'a.edit_reason_button', function() {
            $("div.reason_modal").load($(this).data('href'), function() {
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
                                $("#reason_table").DataTable().ajax.reload();
                                $('div.reason_modal').modal('hide');
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

        //funcion para eliminar una terminal
        function deleteReason(id) {
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
                    route = '/quote/reason/' + id;
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
                                $("#reason_table").DataTable().ajax.reload(null, false);
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

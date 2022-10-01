@extends('layouts.app')
@section('title', __('customer.sdocs'))
    <script th:src="@{/js/datatables.min.js}"></script>
@section('content')
    <section class="content-header">
        <h1>@lang( 'customer.sdocs' )<br>
            <small>@lang( 'customer.manage_sdocs' )</small>
        </h1>
    </section>
    <section class="content">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal"
                        data-href="{{ action('SupportDocumentsController@create') }}" data-container=".sdocs_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            </div><br>
            <div class="box-body">
                @can('sdocs.view')
                    <div id="lista" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-stripe table-bordered table-condensed table-hover" id="sdocs_table"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('customer.id')</th>
                                        <th>@lang('customer.name')</th>
                                        <th>@lang('customer.description')</th>
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
        <div class="modal fade sdocs_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false"
            aria-labelledby="gridSystemModalLabel"></div>

    </section>
@endsection
@section('javascript')
    <script type="text/javascript">
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
        $(document).ready(function() {
            var sdocs_table = $('#sdocs_table').DataTable({
                processing: true,
                serverSide: true,
                type: 'GET',
                ajax: '/sdocs/getSDocsData',
                columnDefs: [{
                    "targets": [0, 1, 2, 3],
                    "className": "text-center",
                    "searchable": true
                }, { orderable: false, targets: 3 }]
            });
        });
        $(document).on('submit', 'form#sdocs_add_form', function(e) {
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
                        $("#sdocs_table").DataTable().ajax.reload();
                        $('div.sdocs_modal').modal('hide');
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

        $('.sdocs_modal').on('shown.bs.modal', function() {
            $('.sdocs_modal').modal({
                backdrop: 'static',
                keyboard: false
            })
        });

        $(document).on('click', 'button.edit_sdocs_button', function() {

            $("div.sdocs_modal").load($(this).data('href'), function() {
                $(this).modal('show');


                $('form#sdocs_edit_form').submit(function(e) {
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
                                $("#sdocs_table").DataTable().ajax.reload();
                                $('div.sdocs_modal').modal('hide');
                                Swal.fire({
                                    title: result.msg,
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

        $(document).on('click', 'button.delete_sdocs_button', function() {
            Swal.fire({
                title: "{{ __('lang_v1.tittle_confirm_delete') }}",
                text: "{{ __('lang_v1.text_confirm_delete') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('lang_v1.confirm_button_delete') }}"
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
                                $("#sdocs_table").DataTable().ajax.reload();
                                $('div.sdocs_modal').modal('hide');
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
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                            }
                        }
                    });
                }
            });
        });

    </script>
@endsection

@extends('layouts.app')
@section('title', __('carrousel.carrousel_config'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('carrousel.carrousel')
            <small>@lang('carrousel.manage_carrousel')</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <span id="header_index">
                    <h3 class="box-title">@lang('carrousel.carrousel_images')</h3>
                </span>
                <div class="box-tools">
                    <a data-href="{{ url('image/upload') }}" class="btn btn-primary btn-modal" data-toggle="tooltip"
                        title="Create" data-container=".contact_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div id="div_index">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped ajax_view table-text-center" id="slider_table"
                            width="100%">
                            <thead>
                                <tr id="div_datatable">
                                    <th>@lang('carrousel.file')</th>
                                    <th>@lang('carrousel.description')</th>
                                    <th>@lang('messages.show')</th>
                                    <th>@lang('messages.actions')</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
    </section>
@endsection
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            let slider_table = $('#slider_table').DataTable({
                processing: true,
                serverSide: false,
                deferRender: true,
                ajax: '/carrousel/index',
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'is_active',
                        render: function(data) {
                            if (data == 1) {
                                return "@lang('messages.yes')";
                            } else {
                                return 'No';
                            }
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            html =
                                '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> @lang('messages.actions') <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu">';
                            html +=
                                '<li><a href="/image/' + data.id +
                                '/download" data-toggle="tooltip" title="Download"><i class="fa fa-download"></i>@lang('carrousel.download_image')</a></li>';
                            if (data.is_active == 1) {
                                html +=
                                    '<li><a href="/image/' + data.id +
                                    '/status" data-toggle="tooltip" title="Hide image" class="status-image"><i class="fa fa-eye-slash"></i>@lang('messages.hide')</a></li>';
                            } else {
                                html +=
                                    '<li><a href="/image/' + data.id +
                                    '/status" data-toggle="tooltip" title="Show image" class="status-image"><i class="fa fa-eye"></i>@lang('messages.show')</a></li>';
                            }
                            html +=
                                '<li><a data-href="/image/' + data.id +
                                '/show" class="btn-modal" data-toggle="tooltip" title="View" data-container=".contact_modal"><i class="fa fa-image"></i>@lang('messages.view')</a></li>';
                            html +=
                                '<li><a data-href="/image/' + data.id +
                                '/edit" class="btn-modal" data-toggle="tooltip" title="Edit" data-container=".contact_modal"><i class="fa fa-edit"></i>@lang('messages.edit')</a></li>';
                            html +=
                                '<li><a href="/image/' + data.id +
                                '/delete" data-toggle="tooltip" title="Delete" class="delete-image"><i class="fa fa-trash"></i>@lang('messages.delete')</a></li>';
                            html += '</ul></div>';
                            return html;

                        },
                        orderable: false,
                        searchable: false
                    }
                ]

            });

            $('table#slider_table tbody').on('click', 'a.delete-image', function(e) {
                e.preventDefault();
                href = $(this).attr("href");
                swal({
                    title: LANG.sure,
                    text: '{{ __('messages.delete_content') }}',
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            method: "DELETE",
                            url: href,
                            dataType: "json",
                            success: function(result) {
                                if (result.success == true) {
                                    Swal.fire({
                                        title: result.msg,
                                        icon: "success",
                                    });
                                    slider_table.ajax.reload(null, false);
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
            })
            $('table#slider_table tbody').on('click', 'a.status-image', function(e) {
                e.preventDefault();
                href = $(this).attr("href");
                $.ajax({
                    method: "PATCH",
                    url: href,
                    dataType: "json",
                    success: function(result) {
                        if (result.success == true) {
                            Swal.fire({
                                title: result.msg,
                                icon: "success",
                            });
                            slider_table.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                title: 'Error, contacte al administrador.',
                                icon: "error",
                            });
                        }
                    }
                });
            })

            $('.contact_modal').on('shown.bs.modal', function(s) {
                $('form#image_add_form, form#image_update_form').submit(function(e) {
                    e.preventDefault();
                    let data = new FormData(this);
                    $(this).find('button[type="submit"]').attr('disabled', true);
                    $.ajax({
                            method: $(this).attr('method'),
                            url: $(this).attr("action"),
                            dataType: "json",
                            data: data,
                            processData: false,
                            contentType: false,
                            success: function (result) {
                                if (result.success == true) {
                                    $('div.contact_modal').modal('hide');
                                    Swal.fire({
                                        title: result.msg,
                                        icon: "success",
                                    });
                                    slider_table.ajax.reload(null, false);
                                } else {
                                    $('div.contact_modal').modal('hide');
                                    Swal.fire({
                                        title: 'Error, contacte al administrador.',
                                        icon: "error",
                                    });
                                }
                            }
                    });
                })
            });

        });
    </script>
@endsection

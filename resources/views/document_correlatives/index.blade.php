@extends('layouts.app')
@section('title', __('correlatives.correlatives'))
    <link rel="stylesheet" href="{{ asset('accounting/css/jquery-confirm.min.css') }}">
    <script th:src="@{/js/datatables.min.js}"></script>
    <style>
        .swal2-popup {
            font-size: 1.4rem !important;
        }

    </style>
@section('content')

    <section class="content-header">
        <h1>@lang('correlatives.correlatives')</h1>
    </section>

    <section class="content">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <h3 class="box-title">@lang('correlatives.manage_correlatives')</h3>
                @can('correlatives.create')
                    <div class="box-tools">
                        <button type="button" class="btn btn-block btn-primary btn-modal"
                            data-href="{{ action('DocumentCorrelativeController@create') }}"
                            data-container=".correlatives_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                    </div>
                @endcan
            </div>
            <div class="box-body">
                @can('correlatives.view')
                    <div class="row">
                        {{-- Location --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label("location", __("kardex.location") . ":") !!}
                                @if (is_null($default_location))
                                {!! Form::select("select_location", $locations, null,
                                    ["class" => "form-control select2", "id" => "select_location"]) !!}
                                {!! Form::hidden('location', 'all', ['id' => 'location']) !!}
                                @else
                                {!! Form::select("select_location", $locations, null,
                                    ["class" => "form-control select2", "id" => "location", 'disabled']) !!}
                                {!! Form::hidden('location', $default_location, ['id' => 'location']) !!}
                                @endif
                            </div>
                        </div>
        
                        {{-- Document type --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label("document_type", __("document_type.title") . ":") !!}
                                {!! Form::select("select_document_type", $document_types, null,
                                    ["class" => "form-control select2", "id" => "select_document_type"]) !!}
                                {!! Form::hidden('document_type', 'all', ['id' => 'document_type']) !!}
                            </div>
                        </div>
        
                        {{-- Status --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label("status", __("accounting.status") . ":") !!}
                                {!! Form::select("status", $status_list, null,
                                    ["class" => "form-control select2", "id" => "status"]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div id="lstCorrelatives" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table
                                    id="correlatives_table"
                                    class="table table-striped table-condensed table-hover table-text-center"
                                    width="100%"
                                    style="font-size: inherit;">
                                    <thead>
                                        <tr>
                                            <th>@lang('correlatives.business_location')</th>
                                            <th>@lang('correlatives.documenttype')</th>
                                            <th>@lang('correlatives.serie')</th>
                                            <th>@lang('correlatives.resolution')</th>
                                            <th>@lang('correlatives.initial')</th>
                                            <th>@lang('correlatives.actual')</th>
                                            <th>@lang('correlatives.final')</th>
                                            <th>@lang('accounting.status')</th>
                                            <th>@lang('messages.actions')</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>

        <div class="modal fade correlatives_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

    </section>
@endsection
@section('javascript')
    <script type="text/javascript" src="{{ asset('js/sweetalert2.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'throw';

            correlatives_table = $('#correlatives_table').DataTable({
                processing: true,
                serverside: true,
                ajax: {
                    url: '/correlatives/getCorrelativesData',
                    data: function (d) {
                        d.location_id = $('#location').val();
                        d.document_type_id = $('#document_type').val();
                        d.status = $('#status').val();
                    }
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'document_name', name: 'document_name' },
                    { data: 'serie', name: 'serie' },
                    { data: 'resolution', name: 'resolution' },
                    { data: 'initial', name: 'initial', className: 'text-center' },
                    { data: 'actual', name: 'actual', className: 'text-center' },
                    { data: 'final', name: 'final', className: 'text-center' },
                    { data: 'status', name: 'status', className: 'text-center' },
                    { data: 'action', orderable: false, searchable: false, className: 'text-center' }
                ]
            });

            // Location filter
            $('select#select_location').on('change', function() {
                $('input#location').val($('select#select_location').val());
                correlatives_table.ajax.reload();
            });

            // Document type filter
            $('select#select_document_type').on('change', function() {
                $('input#document_type').val($('select#select_document_type').val());
                correlatives_table.ajax.reload();
            });

            // Payment status filter
            $('select#status').on('change', function() {
                correlatives_table.ajax.reload();
            });
        });

        $(document).on('submit', 'form#correlatives_add_form', function(e) {
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            var data = $(this).serialize();
            $.ajax({
                method: "POST",
                url: $(this).attr('action'),
                dataType: "json",
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        $("#correlatives_table").DataTable().ajax.reload();
                        $('div.correlatives_modal').modal('hide');
                        Swal.fire({
                            title: result.msg,
                            icon: "success",
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

        $(document).on('click', '.edit_correlatives_button', function() {
            $("div.correlatives_modal").load($(this).data('href'), function() {
                $(this).modal('show');

                $('form#correlatives_edit_form').submit(function(e) {
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
                                $("#correlatives_table").DataTable().ajax.reload();
                                $('div.correlatives_modal').modal('hide');
                                Swal.fire({
                                    title: result.msg,
                                    icon: "success",
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

        $(document).on('click', '.delete_correlatives_button', function() {
            Swal.fire({
                title: "{{ __('correlatives.tittle_confirm_delete') }}",
                text: "{{ __('correlatives.text_confirm_delete') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('correlatives.confirm_button_delete') }}"
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
                                $("#correlatives_table").DataTable().ajax.reload();
                                $('div.correlatives_modal').modal('hide');
                                Swal.fire({
                                    title: result.msg,
                                    icon: "success",
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

    </script>
@endsection

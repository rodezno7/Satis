@extends('layouts.app')

@section('title', __('movement_type.movement_types'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('movement_type.movement_types')
        <small>@lang('movement_type.manage_movement_types')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang('movement_type.all_movement_types')</h3>
            @can('movement_type.create')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{ action('MovementTypeController@create') }}" 
                    data-container=".movement_types_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </button>
            </div>
            @endcan
        </div>
        
        <div class="box-body">
            @can('movement_type.view')
            <div class="table-responsive">
            <table class="table table-bordered table-striped vg-middle" id="movement_types_table">
                <thead>
                    <tr>
                        <th>@lang('crm.name')</th>
                        <th>@lang('lang_v1.description')</th>
                        <th>@lang('crm.type')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
            </div>
            @endcan
        </div>
    </div>

    <div class="modal fade movement_types_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function() {
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};

        // Select2
        $('.movement_types_modal').on('shown.bs.modal', function () {
            $(this).find('.select2').select2();
        });

        // Data Table
        var movement_types_table = $('#movement_types_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/movement-types',
            columnDefs: [{
                "targets": [2, 3],
                "orderable": false,
                "searchable": false
            }]
        });

        // Add Form
        $(document).on('submit', 'form#movement_type_add_form', function(e) {
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            var data = $(this).serialize();
            $.ajax({
                method: "POST",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success: function(result) {
                    if (result.success === true) {
                        $('div.movement_types_modal').modal('hide');
                        Swal.fire({
                            title: ""+result.msg+"",
                            icon: "success",
                        });
                        movement_types_table.ajax.reload();
                    } else {
                        Swal.fire
                        ({
                            title: ""+result.msg+"",
                            icon: "error",
                        });
                    }
                }
            });
        });

        // Edit Form
        $(document).on('click', 'button.edit_movement_types_button', function() {
            $("div.movement_types_modal").load($(this).data('href'), function() {
                $(this).modal('show');
                $('form#movement_types_edit_form').submit(function(e) {
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
                                $('div.movement_types_modal').modal('hide');
                                Swal.fire({
                                    title: ""+result.msg+"",
                                    icon: "success",
                                });
                                movement_types_table.ajax.reload();
                            } else {
                                Swal.fire
                                ({
                                    title: ""+result.msg+"",
                                    icon: "error",
                                });
                            }
                        }
                    });
                });
            });
        });

        // Delete
        $(document).on('click', 'button.delete_movement_types_button', function() {
            swal({
                title: LANG.sure,
                text: '{{__('messages.delete_content')}}',
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();

                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result) {
                            if (result.success === true) {
                                Swal.fire({
                                    title: ""+result.msg+"",
                                    icon: "success",
                                });
                                movement_types_table.ajax.reload();
                            } else {
                                Swal.fire
                                ({
                                    title: ""+result.msg+"",
                                    icon: "error",
                                });
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
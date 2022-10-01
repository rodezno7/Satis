@extends('layouts.app')
@section('title', __('external_lab.external_labs'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'external_lab.external_labs' )
        <small>@lang( 'external_lab.manage_external_labs' )</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang('external_lab.all_external_labs')</h3>
            @can('external_lab.create')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{ action('Optics\ExternalLabController@create') }}" 
                    data-container=".external_labs_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )
                </button>
            </div>
            @endcan
        </div>
        
        <div class="box-body">
            @can('external_lab.view')
            <div class="table-responsive">
            <table class="table table-bordered table-striped" id="external_labs_table">
                <thead>
                    <tr>
                        <th>@lang('cashier.name')</th>
                        <th>@lang('business.address')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
            </div>
            @endcan
        </div>
    </div>

    <div class="modal fade external_labs_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function() {

        // Data Table
        var external_labs_table = $('#external_labs_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/external-labs',
            columnDefs: [{
                "targets": [2],
                "orderable": false,
                "searchable": false
            }]
        });

        // Add Form
        $(document).on('submit', 'form#external_lab_add_form', function(e) {
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
                        $('div.external_labs_modal').modal('hide');
                        Swal.fire({
                            title: ""+result.msg+"",
                            icon: "success",
                        });
                        external_labs_table.ajax.reload();
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
        $(document).on('click', 'button.edit_external_labs_button', function() {
            $("div.external_labs_modal").load($(this).data('href'), function() {
                $(this).modal('show');
                $('form#external_labs_edit_form').submit(function(e) {
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
                                $('div.external_labs_modal').modal('hide');
                                Swal.fire({
                                    title: ""+result.msg+"",
                                    icon: "success",
                                });
                                external_labs_table.ajax.reload();
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
        $(document).on('click', 'button.delete_external_labs_button', function() {
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
                                external_labs_table.ajax.reload();
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
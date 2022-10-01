@extends('layouts.app')
@section('title', __('warehouse.warehouses'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'warehouse.warehouses' )
        <small>@lang( 'warehouse.manage_warehouses' )</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang( 'warehouse.all_warehouses' )</h3>
            @can('warehouse.create')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{ action('WarehouseController@create') }}" 
                    data-container=".warehouses_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )
                </button>
            </div>
            @endcan
        </div>
        
        <div class="box-body">
            @can('warehouse.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="warehouses_table">
                    <thead>
                        <tr>
                            <th>@lang('cashier.code')</th>
                            <th>@lang('cashier.name')</th>
                            <th>@lang('cashier.business_location')</th>
                            <th>@lang('warehouse.location')</th>
                            <th>@lang('cashier.status')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
            @endcan
        </div>
    </div>

    <div class="modal fade warehouses_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function() {
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

        // Data Table
        var warehouses_table = $('#warehouses_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/warehouses',
            columnDefs: [{
                "targets": [4, 5],
                "orderable": false,
                "searchable": false
            }]
        });

        // Add Form
        $(document).on('submit', 'form#warehouse_add_form', function(e) {
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
                        $('div.warehouses_modal').modal('hide');
                        Swal.fire({
                            title: ""+result.msg+"",
                            icon: "success",
                        });
                        warehouses_table.ajax.reload();
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
        $(document).on('click', 'button.edit_warehouses_button', function() {
            $("div.warehouses_modal").load($(this).data('href'), function() {
                $(this).modal('show');
                $('form#warehouses_edit_form').submit(function(e) {
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
                                $('div.warehouses_modal').modal('hide');
                                Swal.fire({
                                    title: ""+result.msg+"",
                                    icon: "success",
                                });
                                warehouses_table.ajax.reload();
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
        $(document).on('click', 'button.delete_warehouses_button', function() {
            swal({
                title: LANG.sure,
                text: LANG.confirm_delete_warehouse,
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
                                warehouses_table.ajax.reload();
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

        // accounting account
        $('div.warehouses_modal').on('shown.bs.modal', function(){
            $("select#catalogue_id").select2({
                ajax: {
                    type: "post",
                    url: "/catalogue/get_accounts_for_select2",
                    dataType: "json",
                    data: function(params){
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                },
                minimumInputLength: 2,
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
        });
    });
</script>
@endsection
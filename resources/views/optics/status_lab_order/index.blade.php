@extends('layouts.app')
@section('title', __('status_lab_order.status_lab_orders'))

@section('content')
<style>
    .dot {
      height: 20px;
      width: 20px;
      background-color: #bbb;
      border-radius: 50%;
      display: inline-block;
    }

    table.vg-middle thead tr th,
    table.vg-middle tbody tr td {
        vertical-align: middle;
    }
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'status_lab_order.status_lab_orders' )
        <small>@lang( 'status_lab_order.manage_status_lab_orders' )</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang( 'status_lab_order.all_status_lab_orders' )</h3>
            @can('status_lab_order.create')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{ action('Optics\StatusLabOrderController@create') }}" 
                    data-container=".status_lab_orders_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )
                </button>
            </div>
            @endcan
        </div>
        
        <div class="box-body">
            @can('status_lab_order.view')
            <div class="table-responsive">
            <table class="table table-bordered table-striped vg-middle" id="status_lab_orders_table">
                <thead>
                    <tr>
                        <th>@lang('cashier.code')</th>
                        <th>@lang('cashier.name')</th>
                        <th>@lang('graduation_card.color')</th>
                        <th>@lang('cashier.status')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
            </div>
            @endcan
        </div>
    </div>

    <div class="modal fade status_lab_orders_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function() {
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};

        // Modal
        $('.status_lab_orders_modal').on('shown.bs.modal', function() {
            // Select2
            $(this).find('.select2').select2();

            is_default_check($('#is_default'));
            second_time_check($('#second_time'));
            material_download_check($('#material_download'));

            // Is default checked
            $(document).on('click', '#is_default', function() {
                is_default_check($('#is_default'));
            });

            // Second time checked
            $(document).on('click', '#second_time', function() {
                second_time_check($('#second_time'));
            });

            // Material download checked
            $(document).on('click', '#material_download', function() {
                material_download_check($('#material_download'));
            });

            /**
             * Disable checkboxs when "is default" is checked.
             * 
             * @param  HTML DOM Input Checkbox Object
             */
            function is_default_check(is_default_input) {
                if (is_default_input.is(':checked')) {
                    $('#print_order').prop('checked', false);
                    $('#print_order').prop('disabled', true);

                    $('#transfer_sheet').prop('checked', false);
                    $('#transfer_sheet').prop('disabled', true);

                    $('#second_time').prop('checked', false);
                    $('#second_time').prop('disabled', true);

                    $('#material_download').prop('checked', false);
                    $('#material_download').prop('disabled', true);

                } else {
                    $('#print_order').removeAttr('disabled');
                    $('#transfer_sheet').removeAttr('disabled');
                    $('#second_time').removeAttr('disabled');
                    $('#material_download').removeAttr('disabled');
                }
            }

            /**
             * Disable checkboxs when "second time" is checked.
             * 
             * @param  HTML DOM Input Checkbox Object
             */
             function second_time_check(second_time_input) {
                if (second_time_input.is(':checked')) {
                    $('#print_order').prop('checked', false);
                    $('#print_order').prop('disabled', true);

                    $('#transfer_sheet').prop('checked', false);
                    $('#transfer_sheet').prop('disabled', true);

                    $('#is_default').prop('checked', false);
                    $('#is_default').prop('disabled', true);

                    $('#material_download').prop('checked', false);
                    $('#material_download').prop('disabled', true);

                } else {
                    $('#print_order').removeAttr('disabled');
                    $('#transfer_sheet').removeAttr('disabled');
                    $('#is_default').removeAttr('disabled');
                    $('#material_download').removeAttr('disabled');
                }
            }

            /**
             * Disable checkboxs when "material download" is checked.
             * 
             * @param  HTML DOM Input Checkbox Object
             */
             function material_download_check(material_download_input) {
                if (material_download_input.is(':checked')) {
                    $('#print_order').prop('checked', false);
                    $('#print_order').prop('disabled', true);

                    $('#transfer_sheet').prop('checked', false);
                    $('#transfer_sheet').prop('disabled', true);

                    $('#is_default').prop('checked', false);
                    $('#is_default').prop('disabled', true);

                    $('#second_time').prop('checked', false);
                    $('#second_time').prop('disabled', true);

                } else {
                    $('#print_order').removeAttr('disabled');
                    $('#transfer_sheet').removeAttr('disabled');
                    $('#is_default').removeAttr('disabled');
                    $('#second_time').removeAttr('disabled');
                }
            }
        });

        // Data Table
        var status_lab_orders_table = $('#status_lab_orders_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/status-lab-orders',
            columnDefs: [{
                "targets": [2, 3, 4],
                "orderable": false,
                "searchable": false,
                "className": "text-center"
            }]
        });

        // Add Form
        $(document).on('submit', 'form#status_lab_order_add_form', function(e) {
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
                        $('div.status_lab_orders_modal').modal('hide');
                        Swal.fire({
                            title: ""+result.msg+"",
                            icon: "success",
                        });
                        status_lab_orders_table.ajax.reload();
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
        $(document).on('click', 'button.edit_status_lab_orders_button', function() {
            $("div.status_lab_orders_modal").load($(this).data('href'), function() {
                $(this).modal('show');
                $('form#status_lab_orders_edit_form').submit(function(e) {
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
                                $('div.status_lab_orders_modal').modal('hide');
                                Swal.fire({
                                    title: ""+result.msg+"",
                                    icon: "success",
                                });
                                status_lab_orders_table.ajax.reload();
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
        $(document).on('click', 'button.delete_status_lab_orders_button', function() {
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
                                status_lab_orders_table.ajax.reload();
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
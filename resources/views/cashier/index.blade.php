@extends('layouts.app')
@section('title', __('cashier.cashier'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'cashier.cashier' )
        <small>@lang( 'cashier.manage_cashiers' )</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang( 'cashier.all_cashiers' )</h3>
            @can('cashier.create')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal"
                    data-href="{{ action('CashierController@create') }}" data-container=".cashiers_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
            </div>
            @endcan
        </div>
        <div class="box-body">
            @can('cashier.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="cashiers_table">
                    <thead>
                        <tr>
                            <th>@lang('cashier.code')</th>
                            <th>@lang('cashier.name')</th>
                            <th>@lang('cashier.business_location')</th>
                            <th>@lang('cashier.status')</th>
                            <th>@lang('cashier.active')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
            @endcan
        </div>
    </div>

    <div class="modal fade cashiers_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function() {

        // Data Table
        var cashiers_table = $('#cashiers_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/cashiers',
            columnDefs: [{
                "targets": [0,1,2,3,4,5],
                "orderable": false,
                "className": "text-center",
                "searchable": false
            }]
        });

        // Add Form
        $(document).on('submit', 'form#cashier_add_form', function(e) {
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
                        $('div.cashiers_modal').modal('hide');
                        Swal.fire({
                            title: ""+result.msg+"",
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        cashiers_table.ajax.reload();
                    } else {
                        Swal.fire
                        ({
                            title: ""+result.msg+"",
                            icon: "error",
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                }
            });
        });

        // Edit Form
        $(document).on('click', 'button.edit_cashiers_button', function() {
            $("div.cashiers_modal").load($(this).data('href'), function() {
                $(this).modal('show');
                $('form#cashiers_edit_form').submit(function(e) {
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
                                $('div.cashiers_modal').modal('hide');
                                Swal.fire({
                                    title: ""+result.msg+"",
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                                cashiers_table.ajax.reload();
                            } else {
                                Swal.fire
                                ({
                                    title: ""+result.msg+"",
                                    icon: "error",
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                            }
                        }
                    });
                });
            });
        });

        // Delete
        $(document).on('click', 'button.delete_cashiers_button', function() {
            swal({
                title: LANG.sure,
                text: LANG.confirm_delete_cashier,
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
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                                cashiers_table.ajax.reload();
                            } else {
                                Swal.fire
                                ({
                                    title: ""+result.msg+"",
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
    });
</script>
@endsection
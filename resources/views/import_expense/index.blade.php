@extends('layouts.app')

@section('title', __('import_expense.import_expenses'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('import_expense.import_expenses')
        <small>@lang('import_expense.manage_import_expenses')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang('import_expense.all_import_expenses')</h3>
            @can('import_expense.create')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{ action('ImportExpenseController@create') }}" 
                    data-container=".import_expenses_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </button>
            </div>
            @endcan
        </div>
        
        <div class="box-body">
            @can('import_expense.view')
            <div class="table-responsive">
            <table class="table table-striped table-text-center" id="import_expenses_table" width="100%">
                <thead>
                    <tr>
                        <th>@lang('crm.name')</th>
                        <th>@lang('crm.type')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
            </div>
            @endcan
        </div>
    </div>

    <div class="modal fade import_expenses_modal" tabindex="-1" role="dialog" 
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
        $('.import_expenses_modal').on('shown.bs.modal', function () {
            $(this).find('.select2').select2();
        });

        // Data Table
        var import_expenses_table = $('#import_expenses_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/import-expenses',
            columns: [
                { data: 'name', name: 'name' },
                { data: 'type', name: 'type' },
                { data: 'action', name: 'action', searchable: false, orderable: false },
            ]
        });

        // Add Form
        $(document).on('submit', 'form#import_expense_add_form', function(e) {
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
                        $('div.import_expenses_modal').modal('hide');
                        Swal.fire({
                            title: ""+result.msg+"",
                            icon: "success",
                        });
                        import_expenses_table.ajax.reload();
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
        $(document).on('click', 'button.edit_import_expenses_button', function() {
            $("div.import_expenses_modal").load($(this).data('href'), function() {
                $(this).modal('show');
                $('form#import_expenses_edit_form').submit(function(e) {
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
                                $('div.import_expenses_modal').modal('hide');
                                Swal.fire({
                                    title: ""+result.msg+"",
                                    icon: "success",
                                });
                                import_expenses_table.ajax.reload();
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
        $(document).on('click', 'button.delete_import_expenses_button', function() {
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
                                import_expenses_table.ajax.reload();
                            } else {
                                Swal.fire({
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
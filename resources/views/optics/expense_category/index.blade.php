@extends('layouts.app')
@section('title', __('expense.expense_categories'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'expense.expense_categories' )
        <small>@lang( 'expense.manage_your_expense_categories' )</small>
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
        	<h3 class="box-title">@lang( 'expense.all_your_expense_categories' )</h3>
        	<div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                	data-href="{{action('Optics\ExpenseCategoryController@create')}}" 
                	data-container=".expense_category_modal">
                	<i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
        	<table class="table table-bordered table-striped" id="optics_expense_category_table">
        		<thead>
        			<tr>
        				<th>@lang( 'expense.category_name' )</th>
        				<th>@lang( 'expense.account' )</th>
                        <th>@lang( 'messages.action' )</th>
        			</tr>
        		</thead>
        	</table>
            </div>
        </div>
    </div>

    <div class="modal fade expense_category_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@endsection

@section('javascript')
<script>
    // Expense category table
    var optics_expense_cat_table = $('#optics_expense_category_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/expense-categories',
        columnDefs: [{
            "targets": 2,
            "orderable": false,
            "searchable": false
        }]
    });

    $(document).on('submit', 'form#optics_expense_category_add_form', function(e) {
        e.preventDefault();

        var data = $(this).serialize();

        $.ajax({
            method: "POST",
            url: $(this).attr("action"),
            dataType: "json",
            data: data,
            success: function(result) {
                if (result.success === true) {
                    $('div.expense_category_modal').modal('hide');

                    Swal.fire({
                        title: result.msg,
                        icon: "success",
                    });

                    optics_expense_cat_table.ajax.reload();

                } else {
                    Swal.fire({
                        title: result.msg,
                        icon: "error",
                    });
                }
            }
        });
    });

    $(document).on('click', 'button.optics_delete_expense_category', function () {
        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_expense_category,
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
                                title: result.msg,
                                icon: "success",
                            });

                            optics_expense_cat_table.ajax.reload();

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


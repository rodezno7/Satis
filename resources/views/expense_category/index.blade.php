@extends('layouts.app')
@section('title', __('expense.expense_categories'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang( 'expense.expense_categories' ) <br>
            <small>@lang( 'expense.manage_your_expense_categories' )</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        @if ($verifiedAccount)
        <div class="box">
                <div class="box-header">
                    <!-- <h3 class="box-title">@lang( 'expense.all_your_expense_categories' )</h3> --><br>
                    <div class="box-tools">
                        <button type="button" class="btn btn-block btn-primary btn-modal"
                            data-href="{{ action('ExpenseCategoryController@create') }}"
                            data-container=".expense_category_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                    </div>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="expense_category_table">
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
            @else
            
                {{-- <h4 class="text-danger">Aun no has configurado la cuenta principal de gastos de la empresa</h4> --}}
                <div class="alert  alert-dismissible" role="alert" style="background-color: #F4D03F;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <strong>@lang('expense.warning')</strong> @lang('expense.warning_message')
                </div>
            @endif
        </div>

        <div class="modal fade expense_category_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
    <!-- /.content -->

@endsection

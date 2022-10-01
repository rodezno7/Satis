<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open(["url" => action("CostCenterController@postOperationAccounts", [$cost_center->id]), "method" => "post",
            "id" => "create_operation_accounts_form", "file" => false, "class" => "form-horizontal"]) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    {{ $cost_center->name }} - @lang('cost_Center.operation_accounts')
                </h4>
            </div>
            <div class="modal-body">
                <div class="box box-primary expenses">
                    <div class="box-header">
                        <div class="box-title">
                            @lang('cost_center.expenses')
                        </div>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <input type="hidden" class="main_account" value="{{ $expense_account_code }}">
                        <div class="form-group">
                            {!! Form::label("sell_expense_account", __("cost_center.sell_expenses"), ["class" => "control-label col-md-4"]) !!}
                            <div class="col-md-8">
                                {!! Form::select("sell_expense_account",
                                    !empty($account_names['sell_expense_account_name']) ?
                                        [$cost_center_operation_account->sell_expense_account => $account_names['sell_expense_account_name']] : [],
                                    !empty($account_names['sell_expense_account_name']) ? $cost_center_operation_account->sell_expense_account : null,
                                    ["class" => "form-control", "id" => "sell_expense_account"]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label("admin_expense_account", __("cost_center.admin_expenses"), ["class" => "control-label col-md-4"]) !!}
                            <div class="col-md-8">
                                {!! Form::select("admin_expense_account",
                                    !empty($account_names['admin_expense_account_name']) ?
                                        [$cost_center_operation_account->admin_expense_account => $account_names['admin_expense_account_name']] : [],
                                    !empty($account_names['admin_expense_account_name']) ? $cost_center_operation_account->admin_expense_account : null,
                                    ["class" => "form-control", "id" => "admin_expense_account"]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label("finantial_expense_account", __("cost_center.finantial_expenses"), ["class" => "control-label col-md-4"]) !!}
                            <div class="col-md-8">
                                {!! Form::select("finantial_expense_account",
                                    !empty($account_names['finantial_expense_account_name']) ?
                                        [$cost_center_operation_account->finantial_expense_account => $account_names['finantial_expense_account_name']] : [],
                                    !empty($account_names['finantial_expense_account_name']) ? $cost_center_operation_account->finantial_expense_account : null,
                                    ["class" => "form-control", "id" => "finantial_expense_account"]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label("non_dedu_expense_account", __("cost_center.non-dedu_expenses"), ["class" => "control-label col-md-4"]) !!}
                            <div class="col-md-8">
                                {!! Form::select("non_dedu_expense_account",
                                    !empty($account_names['non_dedu_expense_account_name']) ?
                                        [$cost_center_operation_account->non_dedu_expense_account => $account_names['non_dedu_expense_account_name']] : [],
                                    !empty($account_names['non_dedu_expense_account_name']) ? $cost_center_operation_account->non_dedu_expense_account : null,
                                    ["class" => "form-control", "id" => "non_dedu_expense_account"]) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        {!! Form::close() !!}
    </div>
</div>
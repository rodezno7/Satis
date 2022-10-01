<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        {!! Form::open(["url" => action("CostCenterController@postMainAccounts", [$cost_center->id]), "method" => "post",
            "id" => "add_main_accounts_form", "file" => false, "class" => "form-horizontal"]) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    {{ $cost_center->name }} - @lang('cost_Center.main_accounts')
                </h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label("expense_main_account", __("cost_center.expenses"), ["class" => "control-label col-md-3"]) !!}
                    <div class="col-md-9">
                        {!! Form::select("expense_account",
                            !is_null($expense_account) ?
                                [$expense_account->id => $expense_account->code . " " . $expense_account->name] : [],
                            !is_null($expense_account) ? $expense_account->id : null,
                            ["class" => "form-control", "id" => "expense_account"]) !!}
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
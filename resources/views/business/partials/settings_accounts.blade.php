<!--Purchase related settings -->
<div class="pos-tab-content active">
    <div class="panel panel-primary">
        <div class="panel-heading">
            @lang('business.primary_accounts')
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('accounting_bank_id', __('accounting.bank_account') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::select('accounting_bank_id', $business_accounts, $business->accounting_bank_id,
                                ['class' => 'form-control select_account', 'placeholder' => __('accounting.bank_account')]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('accounting_customer_id', __('accounting.customer_account') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </span>
                            {!! Form::select('accounting_customer_id', $business_accounts, $business->accounting_customer_id,
                                ['class' => 'form-control select_account', 'placeholder' => __('accounting.customer_account')]) !!}
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('accounting_supplier_id', __('accounting.supplier_account') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </span>
                            {!! Form::select('accounting_supplier_id', $business_accounts, $business->accounting_supplier_id,
                                ['class' => 'form-control select_account', 'placeholder' => __('accounting.supplier_account') ]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('accounting_expense_id', __('accounting.expense_account') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::select('accounting_expense_id', $business_accounts, $business->accounting_expense_id,
                                ['class' => 'form-control select_account', 'placeholder' => __('accounting.expense_account')]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('accounting_utility_id', __('accounting.utility_account') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::select('accounting_utility_id', $business_accounts, $business->accounting_utility_id,
                                ['class' => 'form-control select_account', 'placeholder' => __('accounting.utility_account')]) !!}
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('accounting_deficit_id', __('accounting.deficit_account') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::select('accounting_deficit_id', $business_accounts, $business->accounting_deficit_id,
                                ['class' => 'form-control select_account', 'placeholder' => __('accounting.deficit_account')]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('accounting_profit_and_loss_id', __('accounting.profit_and_loss_account') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::select('accounting_profit_and_loss_id', $business_accounts, $business->accounting_profit_and_loss_id,
                                ['class' => 'form-control select_account', 'placeholder' =>  __('accounting.profit_and_loss_account')]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('accounting_cost_id', __('accounting.cost_account') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::select('accounting_cost_id', $business_accounts, $business->accounting_cost_id,
                                ['class' => 'form-control select_account']) !!}
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('accounting_creditor_result_id', __('accounting.creditor_result_account') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::select('accounting_creditor_result_id', $business_accounts, $business->accounting_creditor_result_id,
                                ['class' => 'form-control select_account', 'placeholder' => __('accounting.creditor_result_account')]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('accounting_debtor_result_id', __('accounting.debtor_result_account') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::select('accounting_debtor_result_id', $business_accounts, $business->accounting_debtor_result_id,
                                ['class' => 'form-control select_account', 'placeholder' => __('accounting.debtor_result_account')]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('accounting_inventory_id', __('accounting.inventory_account') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-cubes"></i>
                            </span>
                            {!! Form::select('accounting_inventory_id', $business_accounts, $business->accounting_inventory_id,
                                ['class' => 'form-control select_account', 'placeholder' => __('accounting.inventory_account')]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('accounting_vat_local_purchase_id', __('accounting.vat_local_purchase') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-cubes"></i>
                            </span>
                            {!! Form::select('accounting_vat_local_purchase_id', $business_accounts, $business->accounting_vat_local_purchase_id,
                                ['class' => 'form-control select_account', 'placeholder' => __('accounting.vat_local_purchase')]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('accounting_vat_import_id', __('accounting.vat_import') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-cubes"></i>
                            </span>
                            {!! Form::select('accounting_vat_import_id', $business_accounts, $business->accounting_vat_import_id,
                                ['class' => 'form-control select_account', 'placeholder' => __('accounting.vat_import')]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('accounting_perception_id', __('accounting.perception_account') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-cubes"></i>
                            </span>
                            {!! Form::select('accounting_perception_id', $business_accounts, $business->accounting_perception_id,
                                ['class' => 'form-control select_account', 'placeholder' => __('accounting.perception_account')]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('accounting_withheld_id', __('accounting.withheld_account') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-cubes"></i>
                            </span>
                            {!! Form::select('accounting_withheld_id', $business_accounts, $business->accounting_withheld_id,
                                ['class' => 'form-control select_account', 'placeholder' => __('accounting.withheld_account')]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
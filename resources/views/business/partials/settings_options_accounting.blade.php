<!--Purchase related settings -->
<div class="pos-tab-content">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('enable_sub_accounts_in_bank_transactions', 1, $business->enable_sub_accounts_in_bank_transactions , 
                        [ 'class' => 'input-icheck', 'id' => 'enable_sub_accounts_in_bank_transactions']); !!} {{ __( 'accounting.enable_sub_accounts' ) }}
                    </label>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>@lang('accounting.numeration_mode')</label>

                {!! Form::select('entries_numeration_mode', ['year' => __('accounting.yearly'), 'month' => __('accounting.monthly')], $business->entries_numeration_mode, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]); !!}
                {{--

                
                {!! Form::select('entries_numeration_mode', ['year' => __('accounting.yearly'), 'month' => __('accounting.monthly'), 'manual' => __('accounting.manual_at_approve')], $business->entries_numeration_mode, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]); !!}
                --}}
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('enable_validation_entries', 1, $business->enable_validation_entries, 
                        [ 'class' => 'input-icheck', 'id' => 'enable_validation_entries']); !!} {{ __( 'accounting.enable_validation_entries' ) }}
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('edition_in_approved_entries', 1, $business->edition_in_approved_entries, 
                        [ 'class' => 'input-icheck', 'id' => 'edition_in_approved_entries']); !!} {{ __( 'accounting.edition_in_approved_entries' ) }}
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('deletion_in_approved_entries', 1, $business->deletion_in_approved_entries, 
                        [ 'class' => 'input-icheck', 'id' => 'deletion_in_approved_entries']); !!} {{ __( 'accounting.deletion_in_approved_entries' ) }}
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('edition_in_number_entries', 1, $business->edition_in_number_entries, 
                        [ 'class' => 'input-icheck', 'id' => 'edition_in_number_entries']); !!} {{ __( 'accounting.edition_in_number_entries' ) }}
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('allow_uneven_totals_entries', 1, $business->allow_uneven_totals_entries, 
                        [ 'class' => 'input-icheck', 'id' => 'allow_uneven_totals_entries']); !!} {{ __( 'accounting.allow_uneven_totals_entries' ) }}
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('allow_nullate_checks_in_approved_entries', 1, $business->allow_nullate_checks_in_approved_entries, 
                        [ 'class' => 'input-icheck', 'id' => 'allow_nullate_checks_in_approved_entries']); !!} {{ __( 'accounting.allow_nullate_checks_in_approved_entries' ) }}
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('enable_description_line_entries_report', 1, $business->enable_description_line_entries_report, 
                        [ 'class' => 'input-icheck', 'id' => 'enable_description_line_entries_report']); !!} {{ __( 'accounting.enable_description_line_entries_report' ) }}
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('allow_entries_approval_disorder', 1, $business->allow_entries_approval_disorder, 
                        [ 'class' => 'input-icheck', 'id' => 'allow_entries_approval_disorder']); !!} {{ __( 'accounting.allow_entries_approval_disorder' ) }}
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('match_check_n_expense', 1, $business->match_check_n_expense, 
                        ['class' => 'input-icheck', 'id' => 'match_check_n_expense']); !!}
                        {{ __( 'accounting.match_check_n_expense_text' ) }}
                    </label>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label>@lang('accounting.balance_debit_levels_number')</label>
                {!! Form::select('balance_debit_levels_number', ['1' => '1', '2' => '2', '3' => '3'], $business->balance_debit_levels_number, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]); !!}
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <label>@lang('accounting.balance_credit_levels_number')</label>
                {!! Form::select('balance_credit_levels_number', ['1' => '1', '2' => '2', '3' => '3'], $business->balance_credit_levels_number, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]); !!}
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <label>@lang('accounting.receivable_type')</label>
                {!! Form::select('receivable_type', 
                    ['customer' => __('accounting.type_customer'), 
                    'bag_account' => __('accounting.type_bag_account'), 
                    'cost_center' => __('accounting.type_cost_center')], 
                    $receivable_type_selected, 
                    ['class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]); !!}
            </div>
        </div>
    </div>
    <div class="row">        
        <div class="col-sm-4">
            <div class="form-group">
                <label>@lang('accounting.debt_to_pay_type')</label>
                {!! Form::select('debt_to_pay_type', 
                    ['supplier' => __('accounting.type_supplier'), 
                    'bag_account' => __('accounting.type_bag_account'), 
                    'cost_center' => __('accounting.type_cost_center')], 
                    $debt_to_pay_type_selected, 
                    ['class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]); !!}
            </div>
        </div>

        {{-- Check printing format kit --}}
        <div class="col-sm-4">
            <div class="form-group">
                <label>@lang('accounting.check_printing_format_kit')</label>
                {!! Form::select('check_format_kit', $check_format_kits, $business->check_format_kit,
                ['class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <label>@lang('accounting.ledger_digits')</label>
                {!! Form::select('ledger_digits', ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5'], $business->ledger_digits, ['class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('sale_accounting_entry_mode', __('accounting.sale_accounting_entry_mode')) !!}
                {!! Form::select('sale_accounting_entry_mode',
                    ['cashier_closure' => __('accounting.cashier_closure'), 'transaction' => __('accounting.transaction')],
                    $business->sale_accounting_entry_mode, ['class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
            </div>
        </div>
    </div>
</div>
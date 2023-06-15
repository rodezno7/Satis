@extends('layouts.app')
@section('title', __( 'accounting.banks_menu' ))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('accounting.banks_operations' )</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="panel with-nav-tabs panel-default boxform_u box-solid_u">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-banks" id="link-banks" data-toggle="tab">@lang('accounting.banks_menu')</a></li>
                <li><a href="#tab-accounts" id="link-accounts" data-toggle="tab">@lang('accounting.accounts')</a></li>
                <li><a href="#tab-checkbooks" id="link-checkbooks" data-toggle="tab">@lang('accounting.checkbooks')</a></li>
                <li><a href="#tab-types" id="link-types" data-toggle="tab">@lang('accounting.transaction_types')</a></li>
                <li><a href="#tab-transactions" id="link-transactions" data-toggle="tab">@lang('accounting.transactions')</a></li>
                <li><a href="#tab-reports" id="link-reports" data-toggle="tab">@lang('accounting.report')</a></li>
                <li><a href="#tab-bank-reconciliation" id="link-bank-reconciliation" data-toggle="tab">@lang('accounting.reconciliation')</a></li>
            </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">

                <!-- banks tab -->
                <div class="tab-pane fade in active" id="tab-banks">
                    <div class="boxform_u box-solid_u">
                        <div class="box-header">
                            <h3 class="box-title">@lang( 'accounting.all_your_banks' )</h3>
                            <div class="box-tools">
                                <button type="button" class="btn btn-primary" data-toggle='modal' data-target='#modal-add-bank' data-backdrop="static" data-keyboard="false" id="btn-new-bank"><i class="fa fa-plus"></i> @lang( 'messages.add' )
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-condensed table-hover" id="banks-table" width="100%">
                                    <thead id="thead-banks">
                                        <th>@lang('accounting.name')</th>
                                        <th>@lang( 'messages.action' )</th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- bank accounts tab -->
                <div class="tab-pane fade" id="tab-accounts">
                    <div class="boxform_u box-solid_u">
                        <div class="box-header">
                            <h3 class="box-title">@lang( 'accounting.all_your_bank_accounts' )</h3>
                            <div class="box-tools">
                                <button type="button" class="btn btn-primary" data-toggle='modal' data-target='#modal-add-bank-account' data-backdrop="static" data-keyboard="false" id="btn-new-bank-account"><i class="fa fa-plus"></i> @lang( 'messages.add' )
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-condensed table-hover" id="bank-accounts-table" width="100%">
                                    <thead>
                                        <th>@lang('accounting.bank')</th>
                                        <th>@lang('accounting.catalogue_account')</th>
                                        <th>@lang('accounting.type_account')</th>
                                        <th>@lang('accounting.number')</th>
                                        <th>@lang('accounting.name')</th>
                                        <th>@lang('accounting.description')</th>
                                        <th>@lang('messages.action')</th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- checkbooks tab -->
                <div class="tab-pane fade" id="tab-checkbooks">
                    <div class="boxform_u box-solid_u">
                        <div class="box-header">
                            <h3 class="box-title">@lang( 'accounting.all_your_checkbooks' )</h3>
                            <div class="box-tools">
                                <button type="button" class="btn btn-primary" data-toggle='modal' data-target='#modal-add-checkbook' data-backdrop="static" data-keyboard="false" id="btn-new-checkbook"><i class="fa fa-plus"></i> @lang( 'messages.add' )
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-condensed table-hover" id="bank-checkbooks-table" width="100%">
                                    <thead>
                                        <th>@lang('accounting.name')</th>
                                        <th>@lang('accounting.description')</th>
                                        <th>@lang('accounting.serie')</th>
                                        <th>@lang('accounting.initial_correlative')</th>
                                        <th>@lang('accounting.final_correlative')</th>
                                        <th>@lang('accounting.actual_correlative')</th>
                                        <th>@lang('accounting.bank_account')</th>
                                        <th>@lang('accounting.status')</th>
                                        <th>@lang('messages.actions')</th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- banks movement types tab -->
                <div class="tab-pane fade" id="tab-types">
                    <div class="boxform_u box-solid_u">
                        <div class="box-header">
                            <h3 class="box-title">@lang( 'accounting.all_your_transaction_types' )</h3>
                            <div class="box-tools">
                                <button type="button" class="btn btn-primary" data-toggle='modal' data-target='#modal-add-transaction-type' data-backdrop="static" data-keyboard="false" id="btn-new-transaction-type"><i class="fa fa-plus"></i> @lang( 'messages.add' )
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-condensed table-hover" id="bank-transaction-types-table" width="100%">
                                    <thead>
                                        <th>@lang('accounting.name')</th>
                                        <th>@lang('accounting.type')</th>
                                        <th>@lang('accounting.entrie_type')</th>
                                        <th>@lang('accounting.enable_checkbook')</th>
                                        <th>@lang('accounting.enable_headline')</th>
                                        <th>@lang('accounting.enable_date_constraint')</th>
                                        <th>@lang('messages.actions')</th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- transaction movements tab -->
                <div class="tab-pane fade" id="tab-transactions">
                    <div id="div-list-transaction">
                        <div class="boxform_u box-solid_u">
                            <div class="box-header">
                                <h3 class="box-title">@lang( 'accounting.all_your_bank_transactions' )</h3>
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <label>@lang('accounting.period')</label>
                                        <select name="filter-period" id="filter-period" class="form-control select2" style="width: 100%;">
                                            <option value="0" selected>@lang('messages.please_select')</option>
                                            @foreach($periods_filter as $period)
                                            <option value="{{ $period->id }}">{{ $period->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <label>@lang('accounting.type')</label>
                                        <select name="filter-type" id="filter-type" class="form-control select2" style="width: 100%;">
                                            <option value="0" selected>@lang('messages.please_select')</option>
                                            @foreach($bank_transaction_types_ddl as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <label>@lang('accounting.bank_account')</label>
                                        <select name="filter-bank" id="filter-bank" class="form-control select2" style="width: 100%;">
                                            <option value="0" selected>@lang('messages.please_select')</option>
                                            @foreach($bank_accounts_ddl as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                                <div class="box-tools">
                                    <button type="button" class="btn btn-primary" id="btn-new-bank-transaction"><i class="fa fa-plus"></i> @lang( 'messages.add' )
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-condensed table-hover" id="bank-transactions-table" width="100%">
                                        <thead>
                                            <th>@lang('accounting.bank_account')</th>
                                            <th>@lang('accounting.entrie')</th>
                                            <th>@lang('accounting.type')</th>
                                            <th>@lang('accounting.reference')</th>
                                            <th>@lang('accounting.date')</th>
                                            <th>@lang('accounting.amount')</th>
                                            <th>@lang('accounting.check_title')</th>
                                            <th>@lang('accounting.description')</th>
                                            <th>@lang('accounting.check_number')</th>
                                            <th>@lang('accounting.status')</th>
                                            <th>@lang('messages.action')</th>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="div-add-transaction" style="display: none;">
                        <div class="boxform_u box-solid_u">
                            <div class="box-header">
                                <h3 class="box-title">@lang('accounting.add_bank_transaction')</h3>
                            </div>
                            <div class="box-body">
                                <form id="form-add-bank-transaction">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label>@lang('accounting.type')</label>
                                                <select name="select-type-transaction" id="select-type-transaction" class="form-control select2" style="width: 100%;">
                                                    <option value="0" selected disabled>@lang('messages.please_select')</option>
                                                    @foreach($bank_transaction_types_ddl as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>

                                                {!! Form::hidden("is_enable_checkbook", 0, ["id" => "is_enable_checkbook"]) !!}

                                                <input type="hidden" name="edition_in_approved_entries" id="edition_in_approved_entries" value="{{ $business->edition_in_approved_entries }}">

                                                <input type="hidden" name="deletion_in_approved_entries" id="deletion_in_approved_entries" value="{{ $business->deletion_in_approved_entries }}">

                                                <input type="hidden" name="edition_in_number_entries" id="edition_in_number_entries" value="{{ $business->edition_in_number_entries }}">

                                                <input type="hidden" name="allow_uneven_totals_entries" id="allow_uneven_totals_entries" value="{{ $business->allow_uneven_totals_entries }}">

                                                <input type="hidden" name="allow_nullate_checks_in_approved_entries" id="allow_nullate_checks_in_approved_entries" value="{{ $business->allow_nullate_checks_in_approved_entries }}">

                                                @foreach($shortcuts as $item)
                                                <input type="hidden" name="{{ $item->shortcut }}" id="description_{{ $item->id }}" value="{{ $item->description }}">
                                                @endforeach

                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="select-bank-account-id-transaction">@lang('accounting.bank_account')</label>
                                                <select name="select-bank-account-id-transaction" id="select-bank-account-id-transaction" class="form-control select2" style="width: 100%;">
                                                    <option value="0" selected disabled>@lang('messages.please_select')</option>
                                                    @foreach($bank_accounts_ddl as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12" id="div_reference">
                                            <div class="form-group">
                                                <label for="txt-reference-transaction">@lang('accounting.reference')</label>
                                                <input type="text" id="txt-reference-transaction" name="txt-reference-transaction" class="form-control" placeholder="@lang('accounting.reference')...">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="code">@lang('accounting.period')</label>
                                                <select name="period_id" id="period_id" class="form-control select2" style="width: 100%;">
                                                    @foreach($periods as $period)
                                                    <option value="{{ $period->id }}">{{ $period->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="txt-date-transaction">@lang('accounting.date')</label>
                                                <div class="wrap-inputform">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                    <input type="date" id="txt-date-transaction" name="txt-date-transaction" class="inputform2" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label>@lang('accounting.location')</label>
                                            <select name="business_location_id" id="business_location_id" class="form-control select2" style="width: 100%">
                                                <option value="0" selected disabled>@lang('messages.please_select')</option>
                                                @foreach($business_locations_ddl as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label>@lang('accounting.amount')</label>
                                            <input type="text" name="txt-amount-transaction" id="txt-amount-transaction" class="form-control">

                                        </div>
                                        <div id="div_checkbook" class="col-lg-3 col-md-3 col-sm-3 col-xs-12" style="display: none;">
                                            <label>@lang('accounting.checkbook')</label>
                                            <select name="select-checkbook-transaction" id="select-checkbook-transaction" class="form-control select2" style="width: 100%">
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="form-group">
                                                <label for="txt-description-transaction">@lang('accounting.description')</label>
                                                <input type="text" id="txt-description-transaction" name="txt-description-transaction" class="form-control" placeholder="@lang('accounting.description')...">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div id="div_contact" style="display: none;">
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="select-carrier-transaction">@lang('accounting.payment_to')</label>
                                                    <select name="select-carrier-transaction" id="select-carrier-transaction" class="form-control select2" style="width: 100%;" required>
                                                        <option value="0" disabled selected>@lang('messages.please_select')</option>
                                                        @foreach($contacts as $item)
                                                        <option value="{{$item->id}}">{{$item->supplier_business_name}} {{$item->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label>@lang('accounting.carrier_name')</label>
                                                <div class="form-group">
                                                    <input type="text" name="txt-payment-to" id="txt-payment-to" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12" id="check_number" style="display: none;">
                                                <label>@lang('accounting.check_number')</label>
                                                <div class="form-group">
                                                    <input type="text" name="txt-check-number-transaction" id="txt-check-number-transaction" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12" id="div_accounts" style="display: none;">
                                            <div class="form-group">
                                                <label><span id="label-type"></span></label>
                                                <select id="accounts" class="form-control select2" style="width: 100%;">
                                                    <option value="0" disabled selected>@lang('messages.please_select')</option>
                                                    @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}">{{ $account->code }} {{ $account->name }}=>{{$account->padre->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-condensed table-hover" id="newData">
                                            <thead>
                                                <th style="width: 5%">
                                                    Op
                                                </th>
                                                <th style="width: 15%">
                                                    @lang('accounting.code')
                                                </th>
                                                <th style="width: 50%">
                                                    @lang('accounting.account')
                                                </th>
                                                <th style="width: 15%">
                                                    @lang('accounting.debit')
                                                </th>
                                                <th style="width: 15%">
                                                    @lang('accounting.credit')
                                                </th>
                                            </thead>
                                            <tbody id="lista">
                                                <tr id="vacio"><td colspan="5">@lang('accounting.no_data')</td></tr>
                                            </tbody>
                                            <tfoot id="pie" style="display: none;">
                                                <th class="text-right" style="width: 70%" colspan="3">
                                                    @lang('accounting.totals')
                                                </th>                   
                                                <th style="width: 15%">
                                                    <input type="text" name="total_debe" id="total_debe" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" readonly>
                                                </th>
                                                <th style="width: 15%">
                                                    <input type="text" name="total_haber" id="total_haber" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" readonly>
                                                </th>
                                            </tfoot>
                                        </table>
                                    </div>
                                    {{-- Expenses modal --}}
                                    <div class="modal fade list_expenses_modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>
                                    <div class="modal fade add_expense_modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>
                                </form>
                            </div>
                            <div class="box-footer">
                                <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-transaction">
                                <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-add-transaction">@lang('messages.cancel')</button>
                                <button type="button" class="btn btn-success" id="add_expenses" style="display: none;">@lang('expense.add_expenses') <i class="fa fa-shopping-cart" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </div>

                    <div id="div-edit-transaction" style="display: none;">
                        <div class="boxform_u box-solid_u">
                            <div class="box-header">
                                <h3 class="box-title">@lang('accounting.edit_bank_transaction')</h3>
                            </div>
                            <div class="box-body">
                                <form id="form-edit-bank-transaction">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label>@lang('accounting.type')</label>
                                                <select name="eselect-type-transaction" id="eselect-type-transaction" class="form-control select2" style="width: 100%;">
                                                    <option value="0" selected disabled>@lang('messages.please_select')</option>
                                                    @foreach($bank_transaction_types_ddl as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="transaction_id" id="transaction_id">
                                                <input type="hidden" name="flag-edit" id="flag-edit" value="0">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="eselect-bank-account-id-transaction">@lang('accounting.bank_account')</label>
                                                <select name="eselect-bank-account-id-transaction" id="eselect-bank-account-id-transaction" class="form-control select2" style="width: 100%;">
                                                    <option value="0" selected disabled>@lang('messages.please_select')</option>
                                                    @foreach($bank_accounts_ddl as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12" id="ediv_reference">
                                            <div class="form-group">
                                                <label for="txt-ereference-transaction">@lang('accounting.reference')</label>
                                                <input type="text" id="txt-ereference-transaction" name="txt-ereference-transaction" class="form-control" placeholder="@lang('accounting.reference')...">
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label>@lang('accounting.period')</label>
                                                <select name="eperiod_id" id="eperiod_id" class="form-control select2" style="width: 100%;">
                                                    @foreach($periods as $period)
                                                    <option value="{{ $period->id }}">{{ $period->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="txt-edate-transaction">@lang('accounting.date')</label>
                                                <div class="wrap-inputform">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                    <input type="date" id="txt-edate-transaction" name="txt-edate-transaction" class="inputform2" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label>@lang('accounting.location')</label>
                                            <select name="ebusiness_location_id" id="ebusiness_location_id" class="form-control select2" style="width: 100%">
                                                <option value="0" selected disabled>@lang('messages.please_select')</option>
                                                @foreach($business_locations_ddl as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label>@lang('accounting.amount')</label>
                                            <input type="text" name="txt-eamount-transaction" id="txt-eamount-transaction" class="form-control">
                                        </div>
                                        <div id="ediv_checkbook" class="col-lg-3 col-md-3 col-sm-3 col-xs-12" style="display: none;">
                                            <label>@lang('accounting.checkbook')</label>
                                            <select name="eselect-checkbook-transaction" id="eselect-checkbook-transaction" class="form-control select2" style="width: 100%">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="form-group">
                                                <label for="txt-edescription-transaction">@lang('accounting.description')</label>
                                                <input type="text" id="txt-edescription-transaction" name="txt-edescription-transaction" class="form-control" placeholder="@lang('accounting.description')...">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div id="ediv_contact" style="display: none;">
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="eselect-carrier-transaction">@lang('accounting.payment_to')</label>
                                                    <select name="eselect-carrier-transaction" id="eselect-carrier-transaction" class="form-control select2" style="width: 100%;" required>
                                                        <option value="0" disabled selected>@lang('messages.please_select')</option>
                                                        @foreach($contacts as $item)
                                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label>@lang('accounting.carrier_name')</label>
                                                <div class="form-group">
                                                    <input type="text" name="txt-epayment-to" id="txt-epayment-to" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <label>@lang('accounting.check_number')</label>
                                                <div class="form-group">
                                                    <input type="text" name="txt-echeck-number-transaction" id="txt-echeck-number-transaction" class="form-control">

                                                    <input type="hidden" name="hidden-echeck-number-transaction" id="hidden-echeck-number-transaction">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12" id="ediv_accounts" style="display: none;">
                                            <div class="form-group">
                                                <label><span id="elabel-type"></span></label>
                                                <select id="eaccounts" class="form-control select2" style="width: 100%;">
                                                    <option value="0" disabled selected>@lang('messages.please_select')</option>
                                                    @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}">{{ $account->code }} {{ $account->name }}=>{{$account->padre->name}}</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-condensed table-hover" id="newData2">
                                                <thead>
                                                    <th style="width: 5%">
                                                        Op
                                                    </th>
                                                    <th style="width: 15%">
                                                        @lang('accounting.code')
                                                    </th>
                                                    <th style="width: 50%">
                                                        @lang('accounting.account')
                                                    </th>
                                                    <th style="width: 15%">
                                                        @lang('accounting.debit')
                                                    </th>
                                                    <th style="width: 15%">
                                                        @lang('accounting.credit')
                                                    </th>
                                                </thead>
                                                <tbody id="lista2">
                                                    <tr id="vacio2">
                                                        <td colspan="5">
                                                            @lang('accounting.no_data')
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot id="pie2" style="display: none;">
                                                    <th class="text-right" style="width: 70%" colspan="3">
                                                        @lang('accounting.totals')
                                                    </th>                   
                                                    <th style="width: 15%">
                                                        <input type="text" name="total_debe2" id="total_debe2" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" readonly>
                                                    </th>
                                                    <th style="width: 15%">
                                                        <input type="text" name="total_haber2" id="total_haber2" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" readonly>
                                                    </th>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="box-footer">
                                <div class="row">
                                    <div id="content2" class="col-lg-12" style="display: none;">
                                        <img src="{{ asset('img/loader.gif') }}" alt="loading" />
                                    </div>
                                </div>
                                <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-edit-transaction">
                                <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-transaction">@lang('messages.cancel')</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- reports tab -->
                <div class="tab-pane fade" id="tab-reports">
                    <div class="boxform_u box-solid_u">
                        <div class="box-header">
                            <h3 class="box-title">@lang( 'accounting.bank_transactions_report' )</h3>
                        </div>
                        <div class="box-body">
                            {!! Form::open(['action' => 'ReporterController@getBankTransactions', 'method' => 'post', 'target' => '_blank']) !!}
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                            <div class="row">
                                <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>@lang('accounting.from')</label>
                                    <div class="wrap-inputform">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        {!! Form::date('report_from', \Carbon\Carbon::now()->format('Y-m-d'), ['id'=>'report_from', 'class'=>'inputform2']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">               
                                    <label>@lang('accounting.to')</label>
                                    <div class="wrap-inputform">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        {!! Form::date('report_to', \Carbon\Carbon::now()->format('Y-m-d'), ['id'=>'report_to', 'class'=>'inputform2']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">               
                                    <label>@lang('accounting.bank_account')</label>
                                    <select name="report_account" id="report_account" class="form-control select2" style="width: 100%;">
                                        <option value="0" selected>@lang('messages.please_select')</option>
                                        @foreach($bank_accounts_ddl as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>@lang('accounting.type')</label>
                                    <select name="report_type" id="report_type" class="form-control select2" style="width: 100%;">
                                        <option value="0" selected>@lang('messages.please_select')</option>
                                        @foreach($bank_transaction_types_ddl as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div id="div_select_checkbook" class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>@lang('accounting.checkbook')</label>
                                    <select name="report_checkbook" id="report_checkbook" class="form-control select2" style="width: 100%;">
                                        <option value="0" selected>@lang('messages.please_select')</option>
                                        @foreach($checkbooks as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="left col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>@lang('accounting.format')</label>
                                    <select name="report_format" id="report_format" class="form-control select2" style="width: 100%">
                                        <option value="pdf" selected>PDF</option>
                                        <option value="excel">Excel</option>
                                    </select>                       
                                </div>
                                <div class="left col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>@lang('accounting.size_font')</label>
                                    <select name="size" id="size" class="form-control select2" style="width: 100%;">
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9" selected>9</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                    </select>                       
                                </div>
                            </div>
                            <div class="row">
                                <div class="left col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <input type="submit" class="btn btn-primary" value="@lang('accounting.generate')" id="report_bank-transactions" style="margin-top: 10px;">
                                </div>
                            </div>
                            <div class="row">
                                <div id="content2" class="col-lg-2 col-md-2 col-sm-2 col-xs-12" style="display: none;">
                                    @lang('accounting.wait_please')
                                    <img src="{{ asset('img/loader.gif') }}" alt="loading" />
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>

                <!-- bank reconciliation tab -->
                <div class="tab-pane fade" id="tab-bank-reconciliation">
                    <div class="boxform_u box-solid_u">
                        <div class="box-header">
                            <h3 class="box-title">@lang( 'accounting.bank_reconciliation' )</h3>
                        </div>
                        <div class="box-body">
                            {!! Form::open(['url' => action('BankTransactionController@getBankReconciliation'), 'method' => 'post', 'enctype' => 'multipart/form-data', 'target' => '_blank']) !!}
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            {!! Form::label('bank', __('accounting.bank')) !!}
                                            <select name="bank" class="form-control select2" style="width: 100%;" id="bank-reconc-bank" required>
                                                <option value="" selected>@lang('accounting.select_bank')</option>
                                                @foreach ($banks_ddl as $b)
                                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            {!! Form::label('bank_account', __('accounting.bank_account_')) !!}
                                            {!! Form::select("bank_account", [], null, ['class' => 'form-control select2', 'id' => 'bank-reconc-bank-accounts',
                                            'style' => 'width: 100%;', 'placeholder' => __('accounting.select_bank_account'), 'required']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            {!! Form::label('start_date', __('accounting.from')) !!}
                                            {!! Form::text('start_date', @format_date('now'), ['class' => 'form-control', 'id' => 'format_date', 'readonly']) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            {!! Form::label('end_date', __('accounting.to')) !!}
                                            {!! Form::text('end_date', @format_date('now'), ['class' => 'form-control', 'id' => 'format_date', 'readonly']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            {!! Form::label("transaction_type", __('accounting.transaction_type')) !!}
                                            <select name="transaction_type" class="form-control select2" style="width: 100%;">
                                                <option value="all">@lang('messages.all')</option>
                                                @foreach ($bank_transaction_types_ddl as $btt)
                                                <option value="{{ $btt->id }}">{{ $btt->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        {!! Form::label("attach_file", __('lang_v1.attach_file')) !!}
                                        <input type="file" name="bank_reconciliation_xlsx" id="bank_reconciliation_xlsx" accept=".xlsx" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <a href="{{ asset('uploads/files/bank_reconciliation_xlsx_template.xlsx') }}"
                                        class="btn btn-success" download><i class="fa fa-download"></i>
                                    @lang('accounting.download_template')</a>
                                </div>
                                <div class="col-sm-6">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                        @lang('accounting.generate_reconciliation')
                                    </button>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                        <div class="col-md-6">
                            <div class="col-ms-6">
                                <strong>@lang('lang_v1.instructions'):</strong>
                                @lang('lang_v1.bank_reconciliation_instructions')
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>@lang('lang_v1.column')</th>
                                            <th>@lang('lang_v1.name')</th>
                                            <th>@lang('lang_v1.instruction')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>@lang('lang_v1.date')<small class="text-muted">(@lang('lang_v1.required'))</small>
                                            </td>
                                            <td>@lang('lang_v1.date_instruction')</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>@lang('lang_v1.reference')<small class="text-muted">(@lang('lang_v1.required'))
                                                <td>@lang('lang_v1.reference_instruction')</td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>@lang('accounting.description')<small class="text-muted">(@lang('lang_v1.optional'))
                                                    <td>@lang('lang_v1.description_instruction')</td>
                                                </tr>
                                                <tr>
                                                    <td>4</td>
                                                    <td>@lang('lang_v1.amount')<small class="text-muted">(@lang('lang_v1.required'))
                                                        <td>@lang('lang_v1.amount_instruction')</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-add-bank" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document" style="width: 30%">
                    <div class="modal-content" style="border-radius: 20px;">
                        <div class="modal-body">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <h3>@lang('accounting.add_bank')</h3>
                                    <form id="form-add-bank">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                                        <div class="form-group">
                                            <label for="txt-name-bank">@lang('accounting.name')</label>
                                            <input type="text" id="txt-name-bank" name="txt-name-bank" class="form-control" placeholder="@lang('accounting.name')...">
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('print_format', __( 'document_type.print_format' ) . ':') !!}
                                            @show_tooltip(__('document_type.tooltip_print_format'))
                                            {!! Form::select('print_format', $checkbook_formats, null, [
                                                'id' => 'print_format',
                                                'class' => 'select2',
                                                'placeholder' => __( 'document_type.print_format'),
                                                'style' => 'width: 100%;'
                                                ]) !!}
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-bank">
                                <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-add-bank">@lang('messages.close')</button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="modal-edit-bank" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document" style="width: 30%">
                        <div class="modal-content" style="border-radius: 20px;">
                            <div class="modal-body">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <h3>@lang('accounting.edit_bank')</h3>
                                        <form id="form-edit-bank">
                                            <div class="form-group">
                                                <label for="txt-name-ebank">@lang('accounting.name')</label>
                                                <input type="text" id="txt-name-ebank" name="txt-name-ebank" class="form-control" placeholder="@lang('accounting.name')...">
                                                <input type="hidden" name="bank_id" id="bank_id">
                                            </div>
                                        </form>
                                    </div>

                                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        {!! Form::label('print_format', __( 'document_type.print_format' ) . ':') !!}
                                        @show_tooltip(__('document_type.tooltip_print_format'))
                                        {!! Form::select('eprint_format', $checkbook_formats, null, [
                                            'id' => 'eprint_format',
                                            'class' => 'select2',
                                            'placeholder' => __( 'document_type.print_format'),
                                            'style' => 'width: 100%;']
                                            ) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-edit-bank">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-bank">@lang('messages.close')</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modal-add-bank-account" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 30%">
                            <div class="modal-content" style="border-radius: 20px;">
                                <div class="modal-body">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <h3>@lang('accounting.add_bank_account')</h3>
                                            <form id="form-add-bank-account">
                                                <div class="form-group">
                                                    <label for="select-bank-id-bank-account">@lang('accounting.bank')</label>
                                                    <select name="select-bank-id-bank-account" id="select-bank-id-bank-account" class="form-control select2" style="width: 100%;">
                                                        <option value="0" selected disabled>@lang('messages.please_select')</option>
                                                        @foreach($banks_ddl as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="select-catalogue-id-bank-account">@lang('accounting.catalogue_account')</label>
                                                    {!! Form::select('select-catalogue-id-bank-account', $banks, null, ['id' => 'select-catalogue-id-bank-account', 'class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]); !!}
                                                </div>
                                                <div class="form-group">
                                                    <label for="select-type-bank-account">@lang('accounting.type_account')</label>
                                                    <select name="select-type-bank-account" id="select-type-bank-account" class="form-control select2" style="width: 100%;">
                                                        <option value="0" disabled selected>@lang('messages.please_select')</option>
                                                        <option value="@lang('accounting.savings')">@lang('accounting.savings')</option>
                                                        <option value="@lang('accounting.checking')">@lang('accounting.checking')</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="txt-number-bank-account">@lang('accounting.number')</label>
                                                    <input type="text" id="txt-number-bank-account" name="txt-number-bank-account" class="form-control" placeholder="@lang('accounting.number')...">
                                                </div>
                                                <div class="form-group">
                                                    <label for="txt-name-bank-account">@lang('accounting.name')</label>
                                                    <input type="text" id="txt-name-bank-account" name="txt-name-bank-account" class="form-control" placeholder="@lang('accounting.name')...">
                                                </div>
                                                <div class="form-group">
                                                    <label for="txt-description-bank-account">@lang('accounting.description')</label>
                                                    <input type="text" id="txt-description-bank-account" name="txt-description-bank-account" class="form-control" placeholder="@lang('accounting.description')...">
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-bank-account">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-add-bank-account">@lang('messages.close')</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="verPartida" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content" style="border-radius: 20px;">
                                <div class="modal-body">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <table class="table table-striped" id="tablaPartida">
                                                <tr>
                                                    <td colspan="6">
                                                        <h4>
                                                            Partida Número:
                                                            <span id="numeroPartida">
                                                            </span>
                                                        </h4>
                                                        <h4>
                                                            Fecha:
                                                            <span id="fecha">                                   
                                                            </span>
                                                        </h4>
                                                        <h4>
                                                            Descripción:
                                                            <span id="descripcion">
                                                            </span>
                                                        </h4>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 20%">Código</th>
                                                    <th style="width: 50%">Cuenta</th>
                                                    <th style="width: 1%"></th>
                                                    <th style="width: 14%">Debe</th>
                                                    <th style="width: 1%"></th>
                                                    <th style="width: 14%">Haber</th>
                                                </tr>
                                                <tbody id="detallePartida">
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="2" class="text-right">
                                                            Totales
                                                        </th>
                                                        <th>
                                                            $
                                                        </th>
                                                        <th>
                                                            <span id="tdebe">                   
                                                            </span>
                                                        </th>
                                                        <th>
                                                            $
                                                        </th>
                                                        <th>
                                                            <span id="thaber">                  
                                                            </span>
                                                        </th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="close-show">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modal-add-transaction-type" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 30%">
                            <div class="modal-content" style="border-radius: 20px;">
                                <div class="modal-body">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <h3>@lang('accounting.add_bank_transaction_type')</h3>
                                            <form id="form-add-bank-transaction-type">
                                                <div class="form-group">
                                                    <label>@lang('accounting.name')</label>
                                                    <input type="text" id="txt-name-bank-transacion-type" name="txt-name-bank-transacion-type" class="form-control" placeholder="@lang('accounting.name')...">
                                                </div>
                                                <div class="form-group">
                                                    <label>@lang('accounting.type_bank_transaction')</label>
                                                    <select name="select-bank-transaction-type" id="select-bank-transaction-type" class="form-control select2" style = 'width: 100%;'>
                                                        <option value="0" selected disabled>@lang('messages.please_select')</option>
                                                        <option value='debit'>@lang('accounting.inflow')</option>
                                                        <option value='credit'>@lang('accounting.outflow')</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>@lang('accounting.entrie_type')</label>
                                                    {!! Form::select('select-type-entrie-transaction-type', $types, null, ['id' => 'select-type-entrie-transaction-type', 'class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]); !!}
                                                </div>
                                                <div class="form-group">
                                                    <label>
                                                        <input type="checkbox" name="enable_checkbook" id="enable_checkbook" value="1" class="input-icheck">
                                                        <strong>@lang('accounting.enable_checkbook')</strong>
                                                    </label>
                                                    <label>
                                                        <input type="checkbox" name="enable_headline" id="enable_headline" value="1" class="input-icheck">
                                                        <strong>@lang('accounting.enable_headline')</strong>
                                                    </label>
                                                </div>

                                                <div class="form-group">
                                                    <label>
                                                        <input type="checkbox" name="enable_date_constraint" id="enable_date_constraint" value="0" class="input-icheck">
                                                        <strong>@lang('accounting.enable_date_constraint')</strong>
                                                    </label>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-bank-transaction-type">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-add-bank-transaction-type">@lang('messages.close')</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modal-edit-transaction-type" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 30%">
                            <div class="modal-content" style="border-radius: 20px;">
                                <div class="modal-body">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <h3>@lang('accounting.edit_bank_transaction_type')</h3>
                                            <form id="form-add-edit-transaction-type">
                                                <div class="form-group">
                                                    <label>@lang('accounting.name')</label>
                                                    <input type="text" id="txt-ename-bank-transacion-type" name="txt-ename-bank-transacion-type" class="form-control" placeholder="@lang('accounting.name')...">
                                                    <input type="hidden" name='bank-transaction-type-id' id='bank-transaction-type-id'>
                                                </div>
                                                <div class="form-group">
                                                    <label>@lang('accounting.type_bank_transaction')</label>
                                                    <select name="eselect-bank-transaction-type" id="eselect-bank-transaction-type" class="form-control select2" disabled style = 'width: 100%;'>
                                                        <option value="0" selected disabled>@lang('messages.please_select')</option>
                                                        <option value='debit'>@lang('accounting.inflow')</option>
                                                        <option value='credit'>@lang('accounting.outflow')</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>@lang('accounting.entrie_type')</label>
                                                    {!! Form::select('eselect-type-entrie-transaction-type', $types, null, ['id' => 'eselect-type-entrie-transaction-type', 'class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]); !!}
                                                </div>
                                                <div class="form-group">
                                                    <label>
                                                        <input type="checkbox" name="eenable_checkbook" id="eenable_checkbook" value="1" class="input-icheck">
                                                        <strong>@lang('accounting.enable_checkbook')</strong>
                                                    </label>
                                                    <label>
                                                        <input type="checkbox" name="eenable_headline" id="eenable_headline" value="1" class="input-icheck">
                                                        <strong>@lang('accounting.enable_headline')</strong>
                                                    </label>
                                                </div>
                                                <div class="form-group">
                                                    <label>
                                                        <input type="checkbox" name="eenable_date_constraint" id="eenable_date_constraint" value="0" class="input-icheck">
                                                        <strong>@lang('accounting.enable_date_constraint')</strong>
                                                    </label>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-edit-bank-transaction-type">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-bank-transaction-type">@lang('messages.close')</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modal-add-checkbook" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 60%">
                            <div class="modal-content" style="border-radius: 20px;">
                                <div class="modal-body">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <h3>@lang('accounting.add_checkbook')</h3>
                                            <form id="form-add-checkbook">
                                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>@lang('accounting.name')</label>
                                                    <input type="text" id="txt-name-checkbook" name="txt-name-checkbook" class="form-control" placeholder="@lang('accounting.name')...">
                                                </div>
                                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>@lang('accounting.description')</label>
                                                    <input type="text" id="txt-description-checkbook" name="txt-description-checkbook" class="form-control" placeholder="@lang('accounting.description')...">
                                                </div>
                                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>@lang('accounting.serie')</label>
                                                    <input type="text" id="txt-serie-checkbook" name="txt-serie-checkbook" class="form-control" placeholder="@lang('accounting.serie')...">
                                                </div>
                                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>@lang('accounting.initial_correlative')</label>
                                                    <input type="text" id="txt-initial-correlative-checkbook" name="txt-initial-correlative-checkbook" class="form-control" placeholder="@lang('accounting.initial_correlative')...">
                                                </div>
                                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>@lang('accounting.final_correlative')</label>
                                                    <input type="text" id="txt-final-correlative-checkbook" name="txt-final-correlative-checkbook" class="form-control" placeholder="@lang('accounting.final_correlative')...">
                                                </div>

                                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>@lang('accounting.actual_correlative')</label>
                                                    <input type="text" id="txt-actual-correlative-checkbook" name="txt-actual-correlative-checkbook" class="form-control" placeholder="@lang('accounting.actual_correlative')...">
                                                </div>


                                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>@lang('accounting.bank')</label>
                                                    <select name="select-bank-id-checkbook" id="select-bank-id-checkbook" class="form-control select2" style="width: 100%;">
                                                        <option value="0" selected disabled>@lang('messages.please_select')</option>
                                                        @foreach($banks_ddl as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>


                                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>@lang('accounting.bank_account')</label>
                                                    <select name="select-bank-account-id-checkbook" id="select-bank-account-id-checkbook" class="form-control select2" style="width: 100%;">
                                                    </select>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-checkbook">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-add-checkbook">@lang('messages.close')</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modal-edit-checkbook" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 60%">
                            <div class="modal-content" style="border-radius: 20px;">
                                <div class="modal-body">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <h3>@lang('accounting.edit_checkbook')</h3>
                                            <form id="form-add-checkbook">
                                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>@lang('accounting.name')</label>
                                                    <input type="text" id="txt-ename-checkbook" name="txt-ename-checkbook" class="form-control" placeholder="@lang('accounting.name')...">
                                                    <input type="hidden" name="checkbook_id" id="checkbook_id">
                                                </div>
                                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>@lang('accounting.description')</label>
                                                    <input type="text" id="txt-edescription-checkbook" name="txt-edescription-checkbook" class="form-control" placeholder="@lang('accounting.description')...">
                                                </div>
                                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>@lang('accounting.serie')</label>
                                                    <input type="text" id="txt-eserie-checkbook" name="txt-eserie-checkbook" class="form-control" placeholder="@lang('accounting.serie')...">
                                                </div>
                                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>@lang('accounting.initial_correlative')</label>
                                                    <input type="text" id="txt-einitial-correlative-checkbook" name="txt-einitial-correlative-checkbook" class="form-control" placeholder="@lang('accounting.initial_correlative')...">
                                                </div>
                                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>@lang('accounting.final_correlative')</label>
                                                    <input type="text" id="txt-efinal-correlative-checkbook" name="txt-efinal-correlative-checkbook" class="form-control" placeholder="@lang('accounting.final_correlative')...">
                                                </div>


                                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>@lang('accounting.actual_correlative')</label>
                                                    <input type="text" id="txt-eactual-correlative-checkbook" name="txt-eactual-correlative-checkbook" class="form-control" placeholder="@lang('accounting.actual_correlative')...">
                                                </div>


                                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>@lang('accounting.bank')</label>
                                                    <select name="eselect-bank-id-checkbook" id="eselect-bank-id-checkbook" class="form-control select2" style="width: 100%;" disabled>
                                                        <option value="0" selected disabled>@lang('messages.please_select')</option>
                                                        @foreach($banks_ddl as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>


                                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>@lang('accounting.bank_account')</label>
                                                    <select name="eselect-bank-account-id-checkbook" id="eselect-bank-account-id-checkbook" class="form-control select2" style="width: 100%;" disabled>

                                                        <option value="0" disabled selected>@lang('messages.please_select')</option>
                                                        @foreach($bank_accounts_ddl as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                        @endforeach



                                                    </select>
                                                </div>


                                                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>@lang('accounting.status')</label>
                                                    <select name="select-status-checkbook" id="select-status-checkbook" class="form-control select2" style="width: 100%">
                                                        <option value="1">@lang('accounting.open')</option>
                                                        <option value="0">@lang('accounting.closed')</option>
                                                    </select>

                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-edit-checkbook">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-checkbook">@lang('messages.close')</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modal-edit-bank-account" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 30%">
                            <div class="modal-content" style="border-radius: 20px;">
                                <div class="modal-body">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>

                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <h3>@lang('accounting.edit_bank_account')</h3>
                                            <form id="form-edit-bank-account">
                                                <div class="form-group">
                                                    <label for="select-bank-id-bank-eaccount">@lang('accounting.bank')</label>
                                                    <select name="select-bank-id-bank-eaccount" id="select-bank-id-bank-eaccount" class="form-control select2" style="width: 100%;">
                                                        <option value="0" selected disabled>@lang('messages.please_select')</option>
                                                        @foreach($banks_ddl as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="bank-account-id" id="bank-account-id">
                                                </div>
                                                <div class="form-group">
                                                    <label for="select-catalogue-id-bank-eaccount">@lang('accounting.catalogue_account')</label>
                                                    {!! Form::select('select-catalogue-id-bank-eaccount', $banks, null, ['id' => 'select-catalogue-id-bank-eaccount', 'class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select'), 'disabled']); !!}
                                                </div>
                                                <div class="form-group">
                                                    <label for="select-type-bank-eaccount">@lang('accounting.type_account')</label>
                                                    <select name="select-type-bank-eaccount" id="select-type-bank-eaccount" class="form-control select2" style="width: 100%;">
                                                        <option value="0" disabled selected>@lang('messages.please_select')</option>
                                                        <option value="@lang('accounting.savings')">@lang('accounting.savings')</option>
                                                        <option value="@lang('accounting.checking')">@lang('accounting.checking')</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="txt-number-bank-eaccount">@lang('accounting.number')</label>
                                                    <input type="text" id="txt-number-bank-eaccount" name="txt-number-bank-eaccount" class="form-control" placeholder="@lang('accounting.number')...">
                                                </div>
                                                <div class="form-group">
                                                    <label for="txt-name-bank-eaccount">@lang('accounting.name')</label>
                                                    <input type="text" id="txt-name-bank-eaccount" name="txt-name-bank-eaccount" class="form-control" placeholder="@lang('accounting.name')...">
                                                </div>
                                                <div class="form-group">
                                                    <label for="txt-description-bank-eaccount">@lang('accounting.description')</label>
                                                    <input type="text" id="txt-description-bank-eaccount" name="txt-description-bank-eaccount" class="form-control" placeholder="@lang('accounting.description')...">
                                                </div>
                                            </form>



                                        </div>
                                        <div class="modal-footer">
                                            <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-edit-bank-account">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-bank-account">@lang('messages.close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Expenses modal --}}
                    <div class="modal fade add_expenses_modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>

                </section>
                <!-- /.content -->
                @endsection
                @section('javascript')
                <script src="{{ asset('js/banks.js?v=' . $asset_v ) }}"></script>

                <script type="text/javascript">
                    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

                    var cont=0;
                    total=0;
                    id_a=[];
                    valor=[];

                    var cont2=0;
                    total2=0;
                    id_a2=[];
                    valor2=[];

                    $(document).ready(function() {
                        loadBankAccountsData();
                        loadBanksData();
                        loadTransactionsData();
                        loadTransactionTypesData();
                        loadCheckBooksData();
                        showSelectCheckbook();
        /*updateSelectsBanks();
        updateSelectsBankAccounts()
        updateSelectsBankTypeTransactions()
        updateSelectsBankCheckbooks();*/

        //changeType();
        //echangeType();
                        $.fn.dataTable.ext.errMode = 'none';

        /** START JS FOR EXPENSES **/
                        $(document).on("change", "select#select-type-transaction", function() {
                            $.ajax({
                                type: "GET",
                                url: "/type-bank-transactions/get_if_enable_checkbook/" + $(this).val(),
                                dataType: "text",
                                success: function(data) {
                                    $("input#is_enable_checkbook").val(data).change();
                                }
                            });
                        });

                        $(document).on("change", "input#is_enable_checkbook", function() {
                            var btn_add_expenses = $("button#add_expenses");

            /*if ($(this).val() == "1") {
                btn_add_expenses.show();
            } else{
                btn_add_expenses.hide();
            }*/
                        });

                        $(document).on("click", "button#add_expenses", function() {
                            var expenses_modal = $("div.list_expenses_modal");

                            $.ajax({
                                url: "/expenses/get_add_expenses",
                                dataType: "html",
                                success: function(result) {
                                    expenses_modal.html(result).modal('show');
                                }
                            });
                        });

                        $('div.add_expenses_modal').on('shown.bs.modal', function(e) {
                            var modal = $(this);

                            modal.find('select#purchase_and_expenses_due').select2({
                                ajax: {
                                    type: "get",
                                    url: "/expenses/get-purchases-expenses",
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
                                minimumInputLength: 1,
                                escapeMarkup: function (markup) {
                                    return markup;
                                }
                            });

                            $(this).find("select#purchase_and_expenses_due").on("change", function() {
                                if($(this).val() > 0){
                                    add_single_expense_row_from_due($(this).val());
                                }
                            });

                            var bank_transaction_id = $("input#bank_transaction_id").val();

                            if (bank_transaction_id) {
                                $("button#save_expenses").on("click", function() {
                                    var expenses = get_expenses_due();
                                    var payments = get_expenses_payment();

                                    $.ajax({
                                        type: "POST",
                                        url: "/expenses/post_add_expenses",
                                        dataType: "json",
                                        data: {
                                            expenses: expenses,
                                            bank_transaction_id: bank_transaction_id,
                                            payments: payments
                                        },
                                        success: function(data) {
                                            if (data.success) {
                                                Swal.fire({
                                                    title: data.msg,
                                                    icon: "success",
                                                });
                                                $('table#bank-transactions-table').DataTable().ajax.reload(null, false);
                                            } else {
                                                Swal.fire({
                                                    title: data.msg,
                                                    icon: "error",
                                                });
                                            }
                                        }
                                    });
                                });

                            } else {
                                $("button#save_expenses").off("click");
                            }
                        });
        /** END JS FOR EXPENSES **/
                    });

function get_expenses_due() {
    var modal = $("div.add_expenses_modal");
    var expenses = modal.find("table#hidden_table tbody tr");
    var expense_ids = [];

    expenses.each(function(i, e){
        var id = $(this).find("input#_expense_id").val();
        expense_ids.push(id);
    });

    return expense_ids;
}

function get_expenses_payment() {
    var modal = $("div.add_expenses_modal");
    var payments = modal.find("table#showed_table tbody tr");
    var payment_values = [];

    payments.each(function(i, e){
        var amount = $(this).find("input#payment_amount").val();
        payment_values.push(amount);
    });

    return payment_values;
}

function loadBanksData()
{
    var table = $("#banks-table").DataTable();
    table.destroy();
    var table = $("#banks-table").DataTable(
    {
        pageLength: 25,
        deferRender: true,
        processing: true,
        serverSide: true,
        ajax: "/banks/getBanksData",
        columns: [
            {data: 'name', name: 'bank.name'},
            {data: null, render: function(data){
                edit_button = '<a class="btn btn-xs btn-primary" onClick="editBank('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                delete_button = ' <a class="btn btn-xs btn-danger" onClick="deleteBank('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                return edit_button + delete_button;
            } , orderable: false, searchable: false}
            ]
    });
}

$("#btn-new-bank").click(function(){
    $('#txt-name-bank').val('');
    $('#print_format').val('').change();
    setTimeout(function()
    {               
        $('#txt-name-bank').focus();
    },
    800);
});

$("#btn-add-bank").click(function(){
    $("#btn-add-bank").prop("disabled", true);
    $("#btn-close-modal-add-bank").prop("disabled", true);  
    name = $("#txt-name-bank").val();
    print_format = $("#print_format").val();
    route = "/banks";
    token = $("#token").val();
    $.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {
            name: name,
            print_format: print_format
        },
        success:function(){
            updateSelectsBanks();
            $("#btn-add-bank").prop("disabled", false);
            $("#btn-close-modal-add-bank").prop("disabled", false); 
            $("#banks-table").DataTable().ajax.reload(null, false);
            Swal.fire
            ({
                title: "{{__('accounting.bank_added')}}",
                icon: "success",
            });
            $("#modal-add-bank").modal('hide');
        },
        error:function(msj){
            $("#btn-add-bank").prop("disabled", false);
            $("#btn-close-modal-add-bank").prop("disabled", false);
            var errormessages = "";
            $.each(msj.responseJSON.errors, function(i, field){
                errormessages+="<li>"+field+"</li>";
            });
            Swal.fire
            ({
                title: "{{__('accounting.errors')}}",
                icon: "error",
                html: "<ul>"+ errormessages+ "</ul>",
            });
        }
    });
});

function editBank(id)
{
    $('#bank_id').val('');
    $('#txt-name-ebank').val('');
    var route = "/banks/"+id+"/edit";
    $.get(route, function(res){
        $('#bank_id').val(res.id);
        $('#txt-name-ebank').val(res.name);
        $('#eprint_format').val(res.print_format).change();
    });
    $('#modal-edit-bank').modal({backdrop: 'static', keyboard: false});
}

$("#btn-edit-bank").click(function(){
    $("#btn-edit-bank").prop("disabled", true);
    $("#btn-close-modal-edit-bank").prop("disabled", true);
    id = $("#bank_id").val();
    name = $("#txt-name-ebank").val();
    print_format = $("#eprint_format").val();
    route = "/banks/"+id;
    token = $("#token").val();
    $.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'PUT',
        dataType: 'json',
        data: {
            name: name,
            print_format: print_format
        },
        success:function(){
            updateSelectsBanks();
            $("#btn-edit-bank").prop("disabled", false);
            $("#btn-close-modal-edit-bank").prop("disabled", false);
            $("#banks-table").DataTable().ajax.reload(null, false);
            Swal.fire
            ({
                title: "{{__('accounting.bank_updated')}}",
                icon: "success",
            });
            $('#modal-edit-bank').modal('hide');
        },
        error:function(msj){
            $("#btn-edit-bank").prop("disabled", false);
            $("#btn-close-modal-edit-bank").prop("disabled", false);
            var errormessages = "";
            $.each(msj.responseJSON.errors, function(i, field){
                errormessages+="<li>"+field+"</li>";
            });
            Swal.fire
            ({
                title: "{{__('accounting.errors')}}",
                icon: "error",
                html: "<ul>"+ errormessages+ "</ul>",
            });
        }
    });
});

function deleteBank(id)
{
    swal({
        title: LANG.sure,
        text: '{{__('messages.delete_content')}}',
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete){
            route = '/banks/'+id;
            token = $("#token").val();
            $.ajax({                    
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'DELETE',
                dataType: 'json',                       
                success:function(result){
                    updateSelectsBanks();
                    if(result.success == true){
                        Swal.fire
                        ({
                            title: result.msg,
                            icon: "success",
                        });
                        $("#banks-table").DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire
                        ({
                            title: result.msg,
                            icon: "error",
                        });
                    }
                }
            });
        }
    });
}

function loadBankAccountsData()
{
    var table = $("#bank-accounts-table").DataTable();
    table.destroy();
    var table = $("#bank-accounts-table").DataTable(
    {
        deferRender: true,
        processing: true,
        serverSide: true,
        ajax: "/bank-accounts/getBankAccountsData",
        columns: [
            {data: 'bank_name', name: 'bank.name'},
            {data: 'catalogue_code', name: 'catalogue.code'},
            {data: 'type', name: 'account.type'},
            {data: 'number', name: 'account.number'},
            {data: 'name', name: 'account.name'},
            {data: 'description', name: 'account.description'},
            {data: null, render: function(data){
                edit_button = '<a class="btn btn-xs btn-primary" onClick="editBankAccount('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                delete_button = ' <a class="btn btn-xs btn-danger" onClick="deleteBankAccount('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                return edit_button + delete_button;
            } , orderable: false, searchable: false}
            ]
    });
}

$("#btn-new-bank-account").click(function(){
    $('#txt-name-bank-account').val('');
    $('#txt-description-bank-account').val('');
    $('#txt-number-bank-account').val('');
});

$("#btn-add-bank-account").click(function(){
    $("#btn-add-bank-account").prop("disabled", true);
    $("#btn-close-modal-add-bank-account").prop("disabled", true);  
    name = $("#txt-name-bank-account").val();
    description = $("#txt-description-bank-account").val();
    bank_id = $("#select-bank-id-bank-account").val();
    catalogue_id = $("#select-catalogue-id-bank-account").val();
    type = $("#select-type-bank-account").val();
    number = $("#txt-number-bank-account").val();
    route = "/bank-accounts";
    token = $("#token").val();
    $.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {
            bank_id: bank_id,
            catalogue_id: catalogue_id,
            name: name,
            description: description,
            type: type,
            number: number
        },
        success:function(){
            updateSelectsBankAccounts();
            $("#btn-add-bank-account").prop("disabled", false);
            $("#btn-close-modal-add-bank-account").prop("disabled", false); 
            $("#bank-accounts-table").DataTable().ajax.reload(null, false);
            Swal.fire
            ({
                title: "{{__('accounting.bank_account_added')}}",
                icon: "success",
            });
            $("#modal-add-bank-account").modal('hide');
        },
        error:function(msj){
            $("#btn-add-bank-account").prop("disabled", false);
            $("#btn-close-modal-add-bank-account").prop("disabled", false);
            var errormessages = "";
            $.each(msj.responseJSON.errors, function(i, field){
                errormessages+="<li>"+field+"</li>";
            });
            Swal.fire
            ({
                title: "{{__('accounting.errors')}}",
                icon: "error",
                html: "<ul>"+ errormessages+ "</ul>",
            });
        }
    });
});

function editBankAccount(id)
{
    $('#bank-account-id').val('');
    $('#txt-name-bank-eaccount').val('');
    $('#txt-description-bank-eaccount').val('');
    var route = "/bank-accounts/"+id+"/edit";
    $.get(route, function(res){
        $("#select-bank-id-bank-eaccount").val(res.bank_id).change();
        $("#select-catalogue-id-bank-eaccount").val(res.catalogue_id).change();
        $("#select-type-bank-eaccount").val(res.type).change();
        $('#bank-account-id').val(res.id);
        $('#txt-name-bank-eaccount').val(res.name);
        $('#txt-number-bank-eaccount').val(res.number);
        $('#txt-description-bank-eaccount').val(res.description);
        $('#modal-edit-bank-account').modal({backdrop: 'static', keyboard: false});
    });
}

$("#btn-edit-bank-account").click(function(){
    $("#btn-edit-bank-account").prop("disabled", true);
    $("#btn-close-modal-edit-bank-account").prop("disabled", true);
    id = $("#bank-account-id").val();
    bank_id = $("#select-bank-id-bank-eaccount").val();
    catalogue_id = $("#select-catalogue-id-bank-eaccount").val();
    name = $("#txt-name-bank-eaccount").val();
    description = $("#txt-description-bank-eaccount").val();
    type = $("#select-type-bank-eaccount").val();
    number = $("#txt-number-bank-eaccount").val();
    route = "/bank-accounts/"+id;
    token = $("#token").val();
    $.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'PUT',
        dataType: 'json',
        data: {
            bank_id: bank_id,
            catalogue_id: catalogue_id,
            name: name,
            description: description,
            type: type,
            number: number
        },
        success:function(){
            updateSelectsBankAccounts();
            $("#btn-edit-bank-account").prop("disabled", false);
            $("#btn-close-modal-edit-bank-account").prop("disabled", false);
            $("#bank-accounts-table").DataTable().ajax.reload(null, false);
            Swal.fire
            ({
                title: "{{__('accounting.bank_account_updated')}}",
                icon: "success",
            });
            $('#modal-edit-bank-account').modal('hide');
        },
        error:function(msj){
            $("#btn-edit-bank-account").prop("disabled", false);
            $("#btn-close-modal-edit-bank-account").prop("disabled", false);
            var errormessages = "";
            $.each(msj.responseJSON.errors, function(i, field){
                errormessages+="<li>"+field+"</li>";
            });
            Swal.fire
            ({
                title: "{{__('accounting.errors')}}",
                icon: "error",
                html: "<ul>"+ errormessages+ "</ul>",
            });
        }
    });
});

function deleteBankAccount(id)
{
    swal({
        title: LANG.sure,
        text: '{{__('messages.delete_content')}}',
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete){
            route = '/bank-accounts/'+id;
            token = $("#token").val();
            $.ajax({
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'DELETE',
                dataType: 'json',                       
                success:function(result){
                    if(result.success == true){
                        updateSelectsBankAccounts();
                        Swal.fire
                        ({
                            title: result.msg,
                            icon: "success",
                        });
                        $("#bank-accounts-table").DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire
                        ({
                            title: result.msg,
                            icon: "error",
                        });
                    }
                }
            });
        }
    });
}

$("#filter-period").change(function(){
    loadTransactionsData();
});
$("#filter-type").change(function(){
    loadTransactionsData();
});
$("#filter-bank").change(function(){
    loadTransactionsData();
});

function loadTransactionsData()
{
    var table = $("#bank-transactions-table").DataTable();
    table.destroy();
    var table = $("#bank-transactions-table").DataTable(
    {
        columnDefs: [{ "visible": false, "targets": [0, 7] }],
        pageLength: 25,
        deferRender: true,
        processing: true,
        serverSide: true,
        ajax: "/bank-transactions/getBankTransactionsData/"+$("#filter-period").val()+"/"+$("#filter-type").val()+"/"+$("#filter-bank").val()+"",
        columns: [
            {data: 'bank', name:'bank_accounts.name'},
            {data: 'entrie', name: 'entrie.short_name'},
            {data: 'type_transaction', name: 'type.name'},
            {data: 'reference', name: 'transaction.reference'},
            {data: 'date', name: 'transaction.date'},
            {data: 'amount', name: 'transaction.amount'},
            {data: 'headline', name: 'transaction.headline'},
            {data: 'description', name: 'transaction.description'},
            {data: 'check_number', name: 'transaction.check_number'},
            {data: null, render: function(data){
                if (data.status == 0) {
                    status = '@lang("accounting.canceled")';
                    
                } else {
                    if (data.entrie_status == 1) {
                        status = '@lang("accounting.approved")';
                    } else {
                        status = '@lang("accounting.pending")';
                    }
                }
                
                return status;
            } , orderable: false, searchable: false},
            {data: null, render: function(data){
                if (data.status == 1) {

                    if(data.entrie_status == 0) {
                        if(data.type == 'credit') {
                            expenses = parseInt(data.expenses);

                            if (expenses > 0) {

                                html_expense = "";
                                
                            } else {
                                html_expense = '<li><a href="#" onClick="addExpenses('+data.id+')"><i class="fa fa-credit-card-alt"></i>@lang('expense.pay_transaction')</a></li>';
                            }

                        }

                        if(data.type == 'debit') { 
                            html_expense = "";
                        }

                        if(data.check_number != null) {
                            actions = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="#" onClick="viewbanktransaction('+data.accounting_entrie_id+')"><i class="fa fa-eye"></i>@lang('messages.view')</a></li><li><a href="#" onClick="editBankTransaction('+data.id+');"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a></li><li><a href="#" onClick="printBankTransaction('+data.id+');"><i class="glyphicon glyphicon-print"></i>@lang('messages.print')</a></li><li><a href="#" onClick="cancelCheck('+data.id+');"><i class="fa fa-ban"></i>@lang('accounting.anulate')</a></li>'+html_expense+'<li><a href="#" onClick="deleteBankTransaction('+data.id+');"><i class="fa fa-trash"></i>@lang('messages.delete')</a></li></ul></div>';
                        } else {
                            actions = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="#" onClick="viewbanktransaction('+data.accounting_entrie_id+')"><i class="fa fa-eye"></i>@lang('messages.view')</a></li><li><a href="#" onClick="editBankTransaction('+data.id+');"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a></li><li><a href="#" onClick="deleteBankTransaction('+data.id+');"><i class="fa fa-trash"></i>@lang('messages.delete')</a></li>'+html_expense+'</ul></div>';
                        }
                    } else {
                        enable_edit = $("#edition_in_approved_entries").val();
                        enable_delete = $("#deletion_in_approved_entries").val();
                        enable_nullate = $("#allow_nullate_checks_in_approved_entries").val();

                        if(data.type == 'credit') {
                            expenses = parseInt(data.expenses);

                            if (expenses > 0) {
                                html_expense = "";
                            } else {
                                html_expense = '<li><a href="#" onClick="addExpenses('+data.id+')"><i class="fa fa-credit-card-alt"></i>@lang('expense.pay_transaction')</a></li>';
                            }
                        }

                        if(data.type == 'debit') { 
                            html_expense = "";
                        }

                        if (enable_nullate == 1) {
                            if (data.check_number != null) {

                                if((enable_edit == 1) && (enable_delete == 1)) {
                                    actions = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="#" onClick="viewbanktransaction('+data.accounting_entrie_id+')"><i class="fa fa-eye"></i>@lang('messages.view')</a></li><li><a href="#" onClick="editBankTransaction('+data.id+');"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a></li><li><a href="#" onClick="printBankTransaction('+data.id+');"><i class="glyphicon glyphicon-print"></i>@lang('messages.print')</a></li><li><a href="#" onClick="cancelCheck('+data.id+');"><i class="fa fa-ban"></i>@lang('accounting.anulate')</a></li>'+html_expense+'<li><a href="#" onClick="deleteBankTransaction('+data.id+');"><i class="fa fa-trash"></i>@lang('messages.delete')</a></li></ul></div>';
                                }

                                if((enable_edit == 1) && (enable_delete == 0)) {
                                    actions = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="#" onClick="viewbanktransaction('+data.accounting_entrie_id+')"><i class="fa fa-eye"></i>@lang('messages.view')</a></li><li><a href="#" onClick="printBankTransaction('+data.id+');"><i class="glyphicon glyphicon-print"></i>@lang('messages.print')</a></li><li><a href="#" onClick="cancelCheck('+data.id+');"><i class="fa fa-ban"></i>@lang('accounting.anulate')</a></li>'+html_expense+'<li><a href="#" onClick="editBankTransaction('+data.id+');"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a></li></ul></div>';
                                }

                                if((enable_edit == 0) && (enable_delete == 1)) {
                                    actions = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="#" onClick="viewbanktransaction('+data.accounting_entrie_id+')"><i class="fa fa-eye"></i>@lang('messages.view')</a></li><li><a href="#" onClick="printBankTransaction('+data.id+');"><i class="glyphicon glyphicon-print"></i>@lang('messages.print')</a></li><li><a href="#" onClick="cancelCheck('+data.id+');"><i class="fa fa-ban"></i>@lang('accounting.anulate')</a></li>'+html_expense+'<li><a href="#" onClick="deleteBankTransaction('+data.id+');"><i class="fa fa-trash"></i>@lang('messages.delete')</a></li></ul></div>';
                                }

                                if((enable_edit == 0) && (enable_delete == 0)) {
                                    actions = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="#" onClick="viewbanktransaction('+data.accounting_entrie_id+')"><i class="fa fa-eye"></i>@lang('messages.view')</a></li><li><a href="#" onClick="printBankTransaction('+data.id+');"><i class="glyphicon glyphicon-print"></i>@lang('messages.print')</a></li><li><a href="#" onClick="cancelCheck('+data.id+');"><i class="fa fa-ban"></i>@lang('accounting.anulate')</a></li>'+html_expense+'</ul></div>';
                                }

                            } else {

                                if((enable_edit == 1) && (enable_delete == 1)) {
                                    actions = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="#" onClick="viewbanktransaction('+data.accounting_entrie_id+')"><i class="fa fa-eye"></i>@lang('messages.view')</a></li><li><a href="#" onClick="editBankTransaction('+data.id+');""><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a></li><li><a href="#" onClick="deleteBankTransaction('+data.id+');""><i class="fa fa-trash"></i>@lang('messages.delete')</a></li>'+html_expense+'</ul></div>';
                                }

                                if((enable_edit == 1) && (enable_delete == 0)) {
                                    actions = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="#" onClick="viewbanktransaction('+data.accounting_entrie_id+')"><i class="fa fa-eye"></i>@lang('messages.view')</a></li><li><a href="#" onClick="editBankTransaction('+data.id+');""><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a></li>'+html_expense+'</ul></div>';
                                }

                                if((enable_edit == 0) && (enable_delete == 1)) {
                                    actions = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="#" onClick="viewbanktransaction('+data.accounting_entrie_id+')"><i class="fa fa-eye"></i>@lang('messages.view')</a></li><li><a href="#" onClick="deleteBankTransaction('+data.id+');""><i class="fa fa-trash"></i>@lang('messages.delete')</a></li>'+html_expense+'</ul></div>';
                                }
                                if((enable_edit == 0) && (enable_delete == 0)) {
                                    actions = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="#" onClick="viewbanktransaction('+data.accounting_entrie_id+')"><i class="fa fa-eye"></i>@lang('messages.view')</a></li>'+html_expense+'</ul></div>';
                                }

                            }

                            
                        } else {

                            if((enable_edit == 1) && (enable_delete == 1)) {
                                actions = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="#" onClick="viewbanktransaction('+data.accounting_entrie_id+')"><i class="fa fa-eye"></i>@lang('messages.view')</a></li><li><a href="#" onClick="editBankTransaction('+data.id+');""><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a></li><li><a href="#" onClick="deleteBankTransaction('+data.id+');""><i class="fa fa-trash"></i>@lang('messages.delete')</a></li>'+html_expense+'</ul></div>';
                            }

                            if((enable_edit == 1) && (enable_delete == 0)) {
                                actions = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="#" onClick="viewbanktransaction('+data.accounting_entrie_id+')"><i class="fa fa-eye"></i>@lang('messages.view')</a></li><li><a href="#" onClick="editBankTransaction('+data.id+');""><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a></li>'+html_expense+'</ul></div>';
                            }

                            if((enable_edit == 0) && (enable_delete == 1)) {
                                actions = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="#" onClick="viewbanktransaction('+data.accounting_entrie_id+')"><i class="fa fa-eye"></i>@lang('messages.view')</a></li><li><a href="#" onClick="deleteBankTransaction('+data.id+');""><i class="fa fa-trash"></i>@lang('messages.delete')</a></li>'+html_expense+'</ul></div>';
                            }
                            if((enable_edit == 0) && (enable_delete == 0)) {
                                actions = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="#" onClick="viewbanktransaction('+data.accounting_entrie_id+')"><i class="fa fa-eye"></i>@lang('messages.view')</a></li>'+html_expense+'</ul></div>';
                            }
                        }
                    }

                } else {

                    actions = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="#" onClick="viewbanktransaction('+data.accounting_entrie_id+')"><i class="fa fa-eye"></i>@lang('messages.view')</a></li><li><a href="#" onClick="deleteBankTransaction('+data.id+');""><i class="fa fa-trash"></i>@lang('messages.delete')</a></li></ul></div>';

                    //actions = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="#" onClick="viewbanktransaction('+data.accounting_entrie_id+')"><i class="fa fa-eye"></i>@lang('messages.view')</a></li><li><a href="#" onClick="cancelCheck('+data.id+');""><i class="fa fa-ban"></i>@lang('accounting.restore')</a></li></ul></div>';
                }
                
                return actions;
            } , orderable: false, searchable: false}
            ]
});

/*$('#bank-transactions-table').on( 'dblclick', 'tr', function () {
    var data = table.row(this).data();
    if (typeof data.id != "undefined"){
        if(data.entrie_status == 0){
            editBankTransaction(data.id);
        }
        else {
            enable_edit = $("#edition_in_approved_entries").val();
            if(enable_edit == 1) {
                editBankTransaction(data.id);
            }
        }
    }
});*/
}

$("#btn-new-bank-transaction ").click(function(){       
    clean();
    $("#div-list-transaction").hide();
    $("#div-add-transaction").show();
});

function clean()
{
    $('#txt-reference-transaction').val('');
    $('#txt-amount-transaction').val('');
    $('#txt-description-transaction').val('');
    $('#txt-payment-to').val('');
    $('#txt-check-number-transaction').val('');

    $('#select-type-transaction').val('0').change();
    $('#select-bank-account-id-transaction').val('0').change();
    $('#select-checkbook-transaction').empty();
    $('#business_location_id').val('0').change();

    $("#newData tbody tr").remove();
    $("#total_debe").val('');
    $("#total_haber").val('');
    total = 0;
    cont = 0;
    id_a.length = 0;
    valor.length = 0;

    $('#div_contact').hide();
    $('#div_checkbook').hide();
    $('#div_accounts').hide();
    $('#div_reference').show();
}

function clean2()
{
    $('#txt-ereference-transaction').val('');
    $('#txt-eamount-transaction').val('');
    $('#txt-edescription-transaction').val('');
    $('#txt-epayment-to').val('');
    $('#txt-echeck-number-transaction').val('');

    $('#eselect-type-transaction').val('0').change();
    $('#eselect-bank-account-id-transaction').val('0').change();
    $('#eselect-checkbook-transaction').empty();
    $('#ebusiness_location_id').val('0').change();

    $("#newData2 tbody tr").remove();
    $("#total_debe2").val('');
    $("#total_haber2").val('');
    total2 = 0;
    cont2 = 0;
    id_a2.length = 0;
    valor2.length = 0;

    $('#ediv_contact').hide();
    $('#ediv_checkbook').hide();
    $('#ediv_accounts').hide();
    $('#ediv_reference').show();
}

$("#btn-close-modal-add-transaction").click(function(){
    clean();
    $("#div-add-transaction").hide();
    $("#div-list-transaction").show();
});

$("#btn-add-transaction").click(function(){
    id = $("#select-type-transaction").val();
    if (id != null) {
        var route = "/type-bank-transactions/"+id;
        $.get(route, function(res){
            if(res.enable_date_constraint == 1) {
                var route = "/bank-transactions/getDateValidation/"+id+"/"+$("#select-checkbook-transaction").val()+"/"+$("#txt-date-transaction").val();
                $.get(route, function(res){
                    if (res.success == true) {
                        addBankTransaction();
                    }
                    else {
                        Swal.fire({
                            title: LANG.sure,
                            text: "{{__('accounting.check_date_confirm')}}",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: "{{__('messages.save')}}",
                            cancelButtonText: "{{__('messages.cancel')}}"
                        }).then((willDelete) => {
                            if (willDelete.value) {
                                addBankTransaction();
                            }
                        });
                    }
                });
            }
            else {
                addBankTransaction();
            }
        });
    }
});

function addBankTransaction()
{
    $("#btn-add-transaction").prop("disabled", true);
    $("#btn-close-modal-add-transaction").prop("disabled", true);
    datastring = $("#form-add-bank-transaction").serialize();
    route = "/bank-transactions";
    token = $("#token").val();
    $.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: datastring,
        success:function(result){
            if (result.success == true){
                $("#btn-add-transaction").prop("disabled", false);
                $("#btn-close-modal-add-transaction").prop("disabled", false); 
                $("#bank-transactions-table").DataTable().ajax.reload(null, false);
                $("#bank-checkbooks-table").DataTable().ajax.reload(null, false);
                Swal.fire
                ({
                    title: ""+result.msg+"",
                    icon: "success",
                });
                clean();
                $("#div-add-transaction").hide();
                $("#div-list-transaction").show();
            }
            else{
                Swal.fire
                ({
                    title: ""+result.msg+"",
                    icon: "error",
                });
                $("#btn-add-transaction").prop("disabled", false);
                $("#btn-close-modal-add-transaction").prop("disabled", false); 
            }
        },
        error:function(msj){
            $("#btn-add-transaction").prop("disabled", false);
            $("#btn-close-modal-add-transaction").prop("disabled", false);
            var errormessages = "";
            $.each(msj.responseJSON.errors, function(i, field){
                errormessages+="<li>"+field+"</li>";
            });
            Swal.fire
            ({
                title: "{{__('accounting.errors')}}",
                icon: "error",
                html: "<ul>"+ errormessages+ "</ul>",
            });
        }
    });
}

function viewbanktransaction(id)
{
    tablaDatos = $("#detallePartida");
    $("#detallePartida").empty();
    var route = "/entries/"+id;
    var route2 = "/entries/getDetails/"+id;    
    $.get(route, function(res){
        $('#numeroPartida').text(' '+res.correlative);
        $('#fecha').text(' '+res.date);
        $('#descripcion').text(' '+res.description);
    });
    total_debe = 0;
    total_haber = 0;
    $.get(route2, function(res2){
        $(res2).each(function(key,value)
        {
            if (value.description == null) {
                description = " ";
            }
            else {
                description = value.description;
            }
            debe = 0;
            if (value.debit != 0)
            {
                tablaDatos.append("<tr><td>"+value.code+"</td><td>"+value.name+"</td><td>$</td><td style = 'text-align: right;'>"+value.debit+"</td><td></td><td></td></tr><tr><td colspan='6'>"+description+"</td></tr>");
                total_debe = parseFloat(total_debe) + parseFloat(value.debit);
            }
        });
        $(res2).each(function(key,value)
        {
            if (value.description == null) {
                description = " ";
            }
            else {
                description = value.description;
            }
            haber= 0;
            if (value.credit != 0)
            {
                tablaDatos.append("<tr><td>"+value.code+"</td><td>"+value.name+"</td><td></td><td></td><td>$</td><td style = 'text-align: right;'>"+value.credit+"</td></tr><tr><td colspan='6'>"+description+"</td></tr>");
                total_haber = parseFloat(total_haber) + parseFloat(value.credit)
            }
        });
        $("#tdebe").text(total_debe.toFixed(2));
        $("#thaber").text(total_haber.toFixed(2));
        $('#verPartida').modal({backdrop: 'static', keyboard: false});
    });
}

$("#btn-close-modal-edit-transaction").click(function(){
    $("#flag-edit").val(0);
    $("#newData2 tbody tr").remove(); 
    var fila_vacia2 = "<tr id='vacio2'><td colspan='5'>@lang('accounting.no_data')</td></tr>";
    $("#lista2").append(fila_vacia2);
    $("#total_debe2").val('');
    $("#total_haber2").val('');
    total2 = 0;
    cont2 = 0;
    id_a2.length = 0;
    valor2.length = 0;
    $("#div-edit-transaction").hide();
    $("#div-list-transaction").show();

    $("#eselect-type-transaction").prop('disabled', false);
    $("#eselect-bank-account-id-transaction").prop('disabled', false);
    $("#eselect-checkbook-transaction").prop('disabled', false);
    $("#txt-echeck-number-transaction").prop('disabled', false);


    clean2();
});

function editBankTransaction(id){

    $('#content2').show();
    $("#btn-edit-transaction").prop('disabled', true);
    $("#btn-close-modal-edit-transaction").prop('disabled', true);
    var route = "/bank-transactions/"+id;
    $.get(route, function(transaction){
        $("#transaction_id").val(transaction.id);
        $("#txt-ereference-transaction").val(transaction.reference);
        $("#txt-edate-transaction").val(transaction.date);
        $("#txt-eamount-transaction").val(transaction.amount);            
        $("#txt-edescription-transaction").val(transaction.description);

        $("#eselect-type-transaction").val(transaction.type_bank_transaction_id).change();
        $("#eselect-bank-account-id-transaction").val(transaction.bank_account_id).change();
        $("#eperiod_id").val(transaction.period_id).change();
        $("#ebusiness_location_id").val(transaction.business_location_id).change();

        accounting_account = transaction.accounting_account;

        id = transaction.type_bank_transaction_id;

        var route = "/type-bank-transactions/"+id;
        $.get(route, function(type){
            $("#ediv_accounts").show();
            if(type.type == "credit") {
                $("#elabel-type").text("{{__('accounting.account_to_debit')}}");
            }
            else {
                $("#elabel-type").text("{{__('accounting.account_to_credit')}}");
            }
            if(type.enable_checkbook == 1) {

                $("#eselect-type-transaction").prop('disabled', true);
                $("#eselect-bank-account-id-transaction").prop('disabled', true);
                $("#eselect-checkbook-transaction").prop('disabled', true);
                //$("#txt-echeck-number-transaction").prop('disabled', true);

                $("#txt-echeck-number-transaction").val(transaction.check_number);
                $("#hidden-echeck-number-transaction").val(transaction.check_number);

                $("#ediv_reference").hide();
                $("#txt-ereference-transaction").val('0');

                $("#ediv_checkbook").show();
                account_id = $("#eselect-bank-account-id-transaction").val();
                $("#eselect-checkbook-transaction").empty();
                var route = "/bank-checkbooks/getBankCheckbooks/"+account_id;
                $.get(route, function(checkbook){
                    $("#eselect-checkbook-transaction").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
                    $(checkbook).each(function(key,value){
                        $("#eselect-checkbook-transaction").append('<option value="'+value.id+'">'+value.name+'</option>');
                    });
                    //$("#eselect-checkbook-transaction").val(transaction.bank_checkbook_id).change();
                    $("#eselect-checkbook-transaction").val(transaction.bank_checkbook_id).trigger('change.select2');
                });
            }
            else
            {
                $("#hidden-echeck-number-transaction").val('');
                $("#ediv_reference").show();
                $("#ediv_checkbook").hide();
            }
            if(type.enable_headline == 1) {
                $("#ediv_contact").show();
                $("#txt-epayment-to").val(transaction.headline);

            }
            else
            {
                $("#ediv_contact").hide();
                $("#txt-echeck-number-transaction").val('0');
            }
        });
        entrie_id = transaction.accounting_entrie_id;
        $("#pie2").show();
        var route2 = "/entries/getTotalEntrie/"+entrie_id;
        $.get(route2, function(res2){
            $('#total_debe2').val(res2.debe);
            $('#total_haber2').val(res2.haber);
        });
        editBankTransactionPartial();
        $("#div-list-transaction").hide();
        $("#div-edit-transaction").show();
    });
}

function editBankTransactionPartial()
{
    var route3 = "/entries/getEntrieDetails/"+entrie_id;
    $.get(route3, function(res3) {
        $(res3).each(function(key,value) {
            id_c2 = value.account_id;
            code2 = value.code;
            name2 = value.name;
            if (value.description != null) {
                description2 = value.description;
            }
            else {
                description2 = "";
            }

            existe2 = parseInt(jQuery.inArray(id_c2, id_a2));
            id_a2.push(id_c2);
            valor2.push(cont2);                
            $("#vacio2").remove();
            if (accounting_account == value.account_id) {

                if(value.debit > 0.00 || value.debit < 0.00) {
                    var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="deleteLineInEditView('+cont2+', '+id_c2+');" disabled>X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="'+value.debit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control cost" step="0.01" onchange="disableCreditInEditView('+cont2+')" readonly></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" value="'+value.credit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" onchange="disableDebitInEditView('+cont2+')" readonly></td></tr><tr id="fila_description2'+cont2+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}" value="'+description2+'"></td></tr>';
                    $("#lista2").append(fila2);
                    cont2++;
                }

                if(value.credit > 0.00 || value.credit < 0.00) {
                    var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="deleteLineInEditView('+cont2+', '+id_c2+');" disabled>X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="'+value.debit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01" onchange="disableCreditInEditView('+cont2+')" readonly></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" value="'+value.credit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control cost" onchange="disableDebitInEditView('+cont2+')" readonly></td></tr><tr id="fila_description2'+cont2+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}" value="'+description2+'"></td></tr>';
                    $("#lista2").append(fila2);
                    cont2++;
                }
            } else {
                var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="deleteLineInEditView('+cont2+', '+id_c2+');" disabled>X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="'+value.debit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01" onchange="disableCreditInEditView('+cont2+')"></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" value="'+value.credit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" onchange="disableDebitInEditView('+cont2+')"></td></tr><tr id="fila_description2'+cont2+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}" value="'+description2+'"></td></tr>';
                $("#lista2").append(fila2);
                cont2++;
            }
        });
editBankTransactionPartialPartial();
});
}

function editBankTransactionPartialPartial()
{
    var route4 = "/entries/getEntrieDetailsHaber/"+entrie_id;
    $.get(route4, function(res4){
        $('html, body').animate( {scrollTop: $(document).height() }, 1000);
        $("#btn-edit-transaction").prop('disabled', false);
        $("#btn-close-modal-edit-transaction").prop('disabled', false);
        $.each(valor2, function(value2) {
            $("#bitem2"+value2+"").prop("disabled", false);
        });
        $('#content2').hide();
        $("#flag-edit").val(1);
        $("#pie2").show();
    });
}

$("#btn-edit-transaction").click(function(){
    id = $("#eselect-type-transaction").val();
    if (id != null) {
        var route = "/type-bank-transactions/"+id;
        $.get(route, function(res){
            if(res.enable_date_constraint == 1) {
                var route = "/bank-transactions/getDateValidation/"+id+"/"+$("#eselect-checkbook-transaction").val()+"/"+$("#txt-edate-transaction").val();
                $.get(route, function(res){
                    if (res.success == true) {
                        editTransaction();
                    }
                    else {
                       Swal.fire({
                        title: LANG.sure,
                        text: "{{__('accounting.check_date_confirm')}}",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: "{{__('messages.save')}}",
                        cancelButtonText: "{{__('messages.cancel')}}"
                    }).then((willDelete) => {
                        if (willDelete.value) {
                            editTransaction();
                        }
                    });
                }
            });
            }
            else {
                editTransaction();
            }
        });
    }
});

function editTransaction()
{
    $("#btn-edit-transaction").prop("disabled", true);
    $("#btn-close-modal-edit-transaction").prop("disabled", true);

    $("#eselect-type-transaction").prop('disabled', false);
    $("#eselect-bank-account-id-transaction").prop('disabled', false);
    $("#eselect-checkbook-transaction").prop('disabled', false);
    $("#txt-echeck-number-transaction").prop('disabled', false);


    datastring = $("#form-edit-bank-transaction").serialize();
    id = $("#transaction_id").val();
    route = "/bank-transactions/"+id;
    token = $("#token").val();
    $.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'PUT',
        dataType: 'json',
        data: datastring,
        success:function(result){
            if (result.success == true){
                $("#btn-edit-transaction").prop("disabled", false);
                $("#btn-close-modal-edit-transaction").prop("disabled", false); 
                $("#bank-transactions-table").DataTable().ajax.reload(null, false);

                Swal.fire
                ({
                    title: ""+result.msg+"",
                    icon: "success",
                });
                $("#div-edit-transaction").hide();
                clean2();
                $("#div-list-transaction").show();
                $("#flag-edit").val(0);
            }
            else{
                Swal.fire
                ({
                    title: ""+result.msg+"",
                    icon: "error",
                });
                $("#btn-edit-transaction").prop("disabled", false);
                $("#btn-close-modal-edit-transaction").prop("disabled", false);

                $("#eselect-type-transaction").prop('disabled', true);
                $("#eselect-bank-account-id-transaction").prop('disabled', true);
                $("#eselect-checkbook-transaction").prop('disabled', true);
                //$("#txt-echeck-number-transaction").prop('disabled', true);
            }
        },
        error:function(msj){
            $("#btn-edit-transaction").prop("disabled", false);
            $("#btn-close-modal-edit-transaction").prop("disabled", false);

            $("#eselect-type-transaction").prop('disabled', true);
            $("#eselect-bank-account-id-transaction").prop('disabled', true);
            $("#eselect-checkbook-transaction").prop('disabled', true);
            //$("#txt-echeck-number-transaction").prop('disabled', true);

            var errormessages = "";
            $.each(msj.responseJSON.errors, function(i, field){
                errormessages+="<li>"+field+"</li>";
            });
            Swal.fire
            ({
                title: "{{__('accounting.errors')}}",
                icon: "error",
                html: "<ul>"+ errormessages+ "</ul>",
            });
        }
    });
}

function deleteBankTransaction(id){
    swal({
        title: LANG.sure,
        text: '{{__('messages.delete_content')}}',
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete){
            route = '/bank-transactions/'+id;
            token = $("#token").val();
            $.ajax({
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'DELETE',
                dataType: 'json',                       
                success:function(result){
                    if(result.success == true){
                        Swal.fire
                        ({
                            title: result.msg,
                            icon: "success",
                        });
                        $("#bank-transactions-table").DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire
                        ({
                            title: result.msg,
                            icon: "error",
                        });
                    }
                }
            });
        }
    });
}

function loadTransactionTypesData()
{
    var table = $("#bank-transaction-types-table").DataTable();
    table.destroy();
    var table = $("#bank-transaction-types-table").DataTable(
    {
        pageLength: 25,
        deferRender: true,
        processing: true,
        serverSide: true,
        ajax: "/type-bank-transactions/getTypeBankTransactionsData",
        columns: [
            {data: 'name', name: 'type.name'},
            {data: null, render: function(data){
                if(data.type == 'debit') {
                    return '@lang('accounting.inflow')';
                }
                else {
                    return '@lang('accounting.outflow')';
                }
            } , orderable: false, searchable: false},
            {data: 'entrie_type', name: 'entrie.name'},
            {data: null, render: function(data){
                if(data.enable_checkbook == 1) {
                    return '@lang('accounting.yes')';
                }
                else {
                    return '@lang('accounting.not')';
                }
            } , orderable: false, searchable: false},
            {data: null, render: function(data){
                if(data.enable_headline == 1) {
                    return '@lang('accounting.yes')';
                }
                else {
                    return '@lang('accounting.not')';
                }
            } , orderable: false, searchable: false},
            {data: null, render: function(data){
                if(data.enable_date_constraint == 1) {
                    return '@lang('accounting.yes')';
                }
                else {
                    return '@lang('accounting.not')';
                }
            } , orderable: false, searchable: false},
            {data: null, render: function(data){
                edit_button = '<a class="btn btn-xs btn-primary" onClick="editBankTransactionType('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                delete_button = ' <a class="btn btn-xs btn-danger" onClick="deleteBankTransactionType('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                return edit_button + delete_button;
            } , orderable: false, searchable: false}
            ]
    });
}

$("#btn-new-transaction-type").click(function(){
    $('#txt-name-bank-transacion-type').val('');


    $("#select-bank-transaction-type").val(0).change();



    $("#enable_checkbook").prop('checked', false);
    $("#enable_headline").prop('checked', false);
    $("#enable_date_constraint").prop('checked', false);

    setTimeout(function()
    {               
        $('#txt-name-bank-transacion-type').focus();
    },
    800);
});


$("#btn-add-bank-transaction-type").click(function(){
    $("#btn-add-bank-transaction-type").prop("disabled", true);
    $("#btn-close-modal-add-bank-transaction-type").prop("disabled", true);
    name = $("#txt-name-bank-transacion-type").val();
    type = $("#select-bank-transaction-type").val();
    type_entrie_id = $("#select-type-entrie-transaction-type").val();
    if ($('#enable_headline').prop('checked')){
        enable_headline = 1;
    }
    else{
        enable_headline = 0;
    }
    if ($('#enable_checkbook').prop('checked')){
        enable_checkbook = 1;
    }
    else{
        enable_checkbook = 0;
    }

    if ($('#enable_date_constraint').prop('checked')){
        enable_date_constraint = 1;
    }
    else{
        enable_date_constraint = 0;
    }

    route = "/type-bank-transactions";
    token = $("#token").val();
    $.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data:{
            name: name,
            type: type,
            type_entrie_id: type_entrie_id,
            enable_checkbook,
            enable_headline,
            enable_date_constraint
        },
        success:function(result){
            if (result.success == true){
                updateSelectsBankTypeTransactions();
                $("#btn-add-bank-transaction-type").prop("disabled", false);
                $("#btn-close-modal-add-bank-transaction-type").prop("disabled", false);
                $("#bank-transaction-types-table").DataTable().ajax.reload(null, false);
                Swal.fire
                ({
                    title: ""+result.msg+"",
                    icon: "success",
                });
                $("#modal-add-transaction-type").modal('hide');
            }
            else{
                Swal.fire
                ({
                    title: ""+result.msg+"",
                    icon: "error",
                });
                $("#btn-add-bank-transaction-type").prop("disabled", false);
                $("#btn-close-modal-add-bank-transaction-type").prop("disabled", false);
            }
        },
        error:function(msj){
            $("#btn-add-bank-transaction-type").prop("disabled", false);
            $("#btn-close-modal-add-bank-transaction-type").prop("disabled", false);
            var errormessages = "";
            $.each(msj.responseJSON.errors, function(i, field){
                errormessages+="<li>"+field+"</li>";
            });
            Swal.fire
            ({
                title: "{{__('accounting.errors')}}",
                icon: "error",
                html: "<ul>"+ errormessages+ "</ul>",
            });
        }
    });
});

$("#btn-edit-bank-transaction-type").click(function(){
    $("#btn-edit-bank-transaction-type").prop("disabled", true);
    $("#btn-close-modal-edit-bank-transaction-type").prop("disabled", true);
    name = $("#txt-ename-bank-transacion-type").val();
    type_entrie_id = $("#eselect-type-entrie-transaction-type").val();
    if ($('#eenable_headline').prop('checked')){
        enable_headline = 1;
    }
    else{
        enable_headline = 0;

    }
    if ($('#eenable_checkbook').prop('checked')){
        enable_checkbook = 1;
    }
    else{
        enable_checkbook = 0;
    }

    if ($('#eenable_date_constraint').prop('checked')){
        enable_date_constraint = 1;
    }
    else{
        enable_date_constraint = 0;
    }

    id = $("#bank-transaction-type-id").val();
    route = "/type-bank-transactions/"+id;
    token = $("#token").val();
    $.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'PUT',
        dataType: 'json',
        data:{
            name: name,
            type_entrie_id: type_entrie_id,
            enable_checkbook,
            enable_headline,
            enable_date_constraint
        },
        success:function(result){
            if (result.success == true){
                updateSelectsBankTypeTransactions();
                $("#btn-edit-bank-transaction-type").prop("disabled", false);
                $("#btn-close-modal-edit-bank-transaction-type").prop("disabled", false);
                $("#bank-transaction-types-table").DataTable().ajax.reload(null, false);
                Swal.fire
                ({
                    title: ""+result.msg+"",
                    icon: "success",
                });
                $("#modal-edit-transaction-type").modal('hide');
            }
            else{
                Swal.fire
                ({
                    title: ""+result.msg+"",
                    icon: "error",
                });
                $("#btn-edit-bank-transaction-type").prop("disabled", false);
                $("#btn-close-modal-edit-bank-transaction-type").prop("disabled", false);
            }
        },
        error:function(msj){
            $("#btn-edit-bank-transaction-type").prop("disabled", false);
            $("#btn-close-modal-edit-bank-transaction-type").prop("disabled", false);
            var errormessages = "";
            $.each(msj.responseJSON.errors, function(i, field){
                errormessages+="<li>"+field+"</li>";
            });
            Swal.fire
            ({
                title: "{{__('accounting.errors')}}",
                icon: "error",
                html: "<ul>"+ errormessages+ "</ul>",
            });
        }
    });
});

function editBankTransactionType(id)
{
    var route = "/type-bank-transactions/"+id+"/edit";
    $.get(route, function(res){
        $('#bank-transaction-type-id').val(res.id);
        $('#txt-ename-bank-transacion-type').val(res.name);

        $('#eselect-bank-transaction-type').val(res.type).change();

        $('#eselect-type-entrie-transaction-type').val(res.type_entrie_id).change();

        if(res.enable_headline == 1) {
            $('#eenable_headline').prop('checked', true);
        }
        else {
            $('#eenable_headline').prop('checked', false);
        }

        if(res.enable_checkbook == 1) {
            $('#eenable_checkbook').prop('checked', true);
        }
        else {
            $('#eenable_checkbook').prop('checked', false);
        }

        if(res.enable_date_constraint == 1) {
            $('#eenable_date_constraint').prop('checked', true);
        }
        else {
            $('#eenable_date_constraint').prop('checked', false);
        }
    });
    $('#modal-edit-transaction-type').modal({backdrop: 'static', keyboard: false});
}

function deleteBankTransactionType(id)
{

    swal({
        title: LANG.sure,
        text: '{{__('messages.delete_content')}}',
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete){
            route = '/type-bank-transactions/'+id;
            token = $("#token").val();
            $.ajax({                    
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'DELETE',
                dataType: 'json',                       
                success:function(result){
                    if(result.success == true){
                        updateSelectsBankTypeTransactions();
                        Swal.fire
                        ({
                            title: result.msg,
                            icon: "success",
                        });
                        $("#bank-transaction-types-table").DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire
                        ({
                            title: result.msg,
                            icon: "error",
                        });
                    }
                }
            });
        }
    });
}

function loadCheckBooksData()
{
    var table = $("#bank-checkbooks-table").DataTable();
    table.destroy();
    var table = $("#bank-checkbooks-table").DataTable(
    {
        deferRender: true,
        processing: true,
        serverSide: true,
        ajax: "/bank-checkbooks/getBankCheckbooksData",
        columns: [
            {data: 'name', name: 'checkbook.name'},
            {data: 'description', name: 'checkbook.description'},
            {data: 'serie', name: 'checkbook.serie'},
            {data: 'initial_correlative', name: 'checkbook.initial_correlative'},
            {data: 'final_correlative', name: 'checkbook.final_correlative'},
            {data: 'actual_correlative', name: 'checkbook.actual_correlative'},
            {data: 'account_name', name: 'account.name'},
            {data: null, render: function(data){
                if(data.status == 1) {
                    return '@lang("accounting.open")';
                }
                else {
                    return '@lang("accounting.closed")';
                }
            } , orderable: false, searchable: false},
            {data: null, render: function(data){
                edit_button = '<a class="btn btn-xs btn-primary" onClick="editCheckbook('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
                delete_button = ' <a class="btn btn-xs btn-danger" onClick="deleteCheckbook('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
                return edit_button + delete_button;
            } , orderable: false, searchable: false}
            ]
    });
}

$("#btn-new-checkbook").click(function(){
    $('#txt-name-checkbook').val('');
    $('#txt-description-checkbook').val('');
    $('#txt-serie-checkbook').val('');
    $('#txt-initial-correlative-checkbook').val('');
    $('#txt-final-correlative-checkbook').val('');
    $('#txt-actual-correlative-checkbook').val('');

    $("#select-bank-id-checkbook").val(0).change();
    $("#select-bank-account-id-checkbook").val(0).change();

    setTimeout(function()
    {               
        $('#txt-name-checkbook').focus();
    },
    800);
});

$("#btn-add-checkbook").click(function(){
    $("#btn-add-checkbook").prop("disabled", true);
    $("#btn-close-modal-add-checkbook").prop("disabled", true);
    name = $("#txt-name-checkbook").val();
    description = $("#txt-description-checkbook").val();
    serie = $("#txt-serie-checkbook").val();
    initial_correlative = $("#txt-initial-correlative-checkbook").val();
    final_correlative = $("#txt-final-correlative-checkbook").val();
    actual_correlative = $("#txt-actual-correlative-checkbook").val();
    bank_account_id = $('#select-bank-account-id-checkbook').val();
    route = "/bank-checkbooks";
    token = $("#token").val();
    $.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data:{
            name: name,
            description: description,
            serie: serie,
            initial_correlative: initial_correlative,
            final_correlative: final_correlative,
            actual_correlative: actual_correlative,
            bank_account_id: bank_account_id
        },
        success:function(result){
            if (result.success == true){
                $("#btn-add-checkbook").prop("disabled", false);
                $("#btn-close-modal-add-checkbook").prop("disabled", false);
                $("#bank-checkbooks-table").DataTable().ajax.reload(null, false);
                Swal.fire
                ({
                    title: ""+result.msg+"",
                    icon: "success",
                });
                $("#modal-add-checkbook").modal('hide');
            }
            else{
                Swal.fire
                ({
                    title: ""+result.msg+"",
                    icon: "error",
                });
                $("#btn-add-checkbook").prop("disabled", false);
                $("#btn-close-modal-add-checkbook").prop("disabled", false);
            }
        },
        error:function(msj){
            $("#btn-add-checkbook").prop("disabled", false);
            $("#btn-close-modal-add-checkbook").prop("disabled", false);
            var errormessages = "";
            $.each(msj.responseJSON.errors, function(i, field){
                errormessages+="<li>"+field+"</li>";
            });
            Swal.fire
            ({
                title: "{{__('accounting.errors')}}",
                icon: "error",
                html: "<ul>"+ errormessages+ "</ul>",
            });
        }
    });
});

$("#btn-edit-checkbook").click(function(){
    $("#btn-edit-checkbook").prop("disabled", true);
    $("#btn-close-modal-edit-checkbook").prop("disabled", true);

    name = $("#txt-ename-checkbook").val();
    description = $("#txt-edescription-checkbook").val();
    serie = $("#txt-eserie-checkbook").val();
    initial_correlative = $("#txt-einitial-correlative-checkbook").val();
    final_correlative = $("#txt-efinal-correlative-checkbook").val();
    actual_correlative = $("#txt-eactual-correlative-checkbook").val();
    bank_account_id = $('#eselect-bank-account-id-checkbook').val();
    status = $("#select-status-checkbook").val();
    id = $("#checkbook_id").val();
    route = "/bank-checkbooks/"+id;
    token = $("#token").val();
    $.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'PUT',
        dataType: 'json',
        data:{
            name: name,
            description: description,
            serie: serie,
            initial_correlative: initial_correlative,
            final_correlative: final_correlative,
            actual_correlative: actual_correlative,
            bank_account_id: bank_account_id,
            status: status
        },
        success:function(result){
            if (result.success == true){
                $("#btn-edit-checkbook").prop("disabled", false);
                $("#btn-close-modal-edit-checkbook").prop("disabled", false);
                $("#bank-checkbooks-table").DataTable().ajax.reload(null, false);
                Swal.fire
                ({
                    title: ""+result.msg+"",
                    icon: "success",
                });
                $("#modal-edit-checkbook").modal('hide');
            }
            else{
                Swal.fire
                ({
                    title: ""+result.msg+"",
                    icon: "error",
                });
                $("#btn-edit-checkbook").prop("disabled", false);
                $("#btn-close-modal-edit-checkbook").prop("disabled", false);
            }
        },
        error:function(msj){
            $("#btn-edit-checkbook").prop("disabled", false);
            $("#btn-close-modal-edit-checkbook").prop("disabled", false);
            var errormessages = "";
            $.each(msj.responseJSON.errors, function(i, field){
                errormessages+="<li>"+field+"</li>";
            });
            Swal.fire
            ({
                title: "{{__('accounting.errors')}}",
                icon: "error",
                html: "<ul>"+ errormessages+ "</ul>",
            });
        }
    });
});

function editCheckbook(id)
{
    var route = "/bank-checkbooks/"+id+"/edit";
    $.get(route, function(res){
        $('#checkbook_id').val(res.id);
        $('#txt-ename-checkbook').val(res.name);
        $('#txt-edescription-checkbook').val(res.description);
        $('#txt-eserie-checkbook').val(res.serie);
        $('#txt-einitial-correlative-checkbook').val(res.initial_correlative);
        $('#txt-efinal-correlative-checkbook').val(res.final_correlative);
        $('#txt-eactual-correlative-checkbook').val(res.actual_correlative);
        $('#eselect-bank-id-checkbook').val(res.bank).change();
        $("#eselect-bank-account-id-checkbook").val(res.bank_account_id).change();
        $('#select-status-checkbook').val(res.status).change();

        $('#modal-edit-checkbook').modal({backdrop: 'static', keyboard: false});
    });


}

function deleteCheckbook(id)
{
    swal({
        title: LANG.sure,
        text: '{{__('messages.delete_content')}}',
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete){
            route = '/bank-checkbooks/'+id;
            token = $("#token").val();
            $.ajax({                    
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'DELETE',
                dataType: 'json',                       
                success:function(result){
                    if(result.success == true){
                        Swal.fire
                        ({
                            title: result.msg,
                            icon: "success",
                        });
                        $("#bank-checkbooks-table").DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire
                        ({
                            title: result.msg,
                            icon: "error",
                        });
                    }
                }
            });
        }
    });
}

function updateSelectsBanks()
{
    $("#select-bank-id-bank-account").empty();
    $("#select-bank-id-bank-eaccount").empty();

    $("#select-bank-id-checkbook").empty();
    $("#eselect-bank-id-checkbook").empty();

    var route = "/banks/getBanks";
    $.get(route, function(res){
        $("#select-bank-id-bank-account").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
        $("#select-bank-id-bank-eaccount").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');

        $("#select-bank-id-checkbook").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');

        $("#eselect-bank-id-checkbook").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');

        $(res).each(function(key,value){
            $("#select-bank-id-bank-account").append('<option value="'+value.id+'">'+value.name+'</option>');
            $("#select-bank-id-bank-eaccount").append('<option value="'+value.id+'">'+value.name+'</option>');

            $("#select-bank-id-checkbook").append('<option value="'+value.id+'">'+value.name+'</option>');
            $("#eselect-bank-id-checkbook").append('<option value="'+value.id+'">'+value.name+'</option>');
        });
    });
}

$("#select-bank-id-checkbook").change(function(){
    id = $("#select-bank-id-checkbook").val();
    if (id != null) {
        $("#select-bank-account-id-checkbook").empty();
        var route = "/bank-accounts/getBankAccountsById/"+id;
        $.get(route, function(res){
            $("#select-bank-account-id-checkbook").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
            $(res).each(function(key,value){
                $("#select-bank-account-id-checkbook").append('<option value="'+value.id+'">'+value.name+'</option>');
            });
        });
    }
});

function updateSelectsBankAccounts()
{
    $("#select-bank-account-id-transaction").empty();
    $("#eselect-bank-account-id-transaction").empty();

    $("#eselect-bank-account-id-checkbook").empty();

    var route = "/bank-accounts/getBankAccounts";
    $.get(route, function(res){
        $("#select-bank-account-id-transaction").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
        $("#eselect-bank-account-id-transaction").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');


        $("#eselect-bank-account-id-checkbook").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');

        $(res).each(function(key,value){
            $("#select-bank-account-id-transaction").append('<option value="'+value.id+'">'+value.name+'</option>');
            $("#eselect-bank-account-id-transaction").append('<option value="'+value.id+'">'+value.name+'</option>');
            $("#eselect-bank-account-id-checkbook").append('<option value="'+value.id+'">'+value.name+'</option>');
        });
    });
}

function updateSelectsBankCheckbooks()
{
    account_id = $("#select-bank-account-id-transaction").val();
    $("#select-checkbook-transaction").empty();
    var route = "/bank-checkbooks/getBankCheckbooks/"+account_id;
    $.get(route, function(res){
        $("#select-checkbook-transaction").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
        $(res).each(function(key,value){
            $("#select-checkbook-transaction").append('<option value="'+value.id+'">'+value.name+'</option>');
        });
    });
}

function eupdateSelectsBankCheckbooks()
{
    account_id = $("#eselect-bank-account-id-transaction").val();
    $("#eselect-checkbook-transaction").empty();
    var route = "/bank-checkbooks/getBankCheckbooks/"+account_id;
    $.get(route, function(res){
        $("#eselect-checkbook-transaction").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
        $(res).each(function(key,value){
            $("#eselect-checkbook-transaction").append('<option value="'+value.id+'">'+value.name+'</option>');
        });
    });
}

function updateSelectsBankTypeTransactions()
{
    $("#select-type-transaction").empty();
    $("#eselect-type-transaction").empty();        

    var route = "/type-bank-transactions/getTypeBankTransactions";
    $.get(route, function(res){
        $("#select-type-transaction").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
        $("#eselect-type-transaction").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');

        $(res).each(function(key,value){
            $("#select-type-transaction").append('<option value="'+value.id+'">'+value.name+'</option>');
            $("#eselect-type-transaction").append('<option value="'+value.id+'">'+value.name+'</option>');
        });
    });
}

$("#select-type-transaction").change(function(event){
    value = $("#select-type-transaction").val();
    if (value != null) {
        changeType();
        $("#newData tbody tr").remove();
        var fila_vacia = "<tr id='vacio'><td colspan='5'>@lang('accounting.no_data')</td></tr>";
        $("#lista").append(fila_vacia);
        $("#total_debe").val('');
        $("#total_haber").val('');
        total = 0;
        cont = 0;
        id_a.length = 0;
        valor.length = 0;
    }
})

$("#eselect-type-transaction").change(function(event){
    flag = $("#flag-edit").val();
    if (flag == 1) {
        value = $("#eselect-type-transaction").val();
        if (value != null) {
            echangeType();
            $("#newData2 tbody tr").remove();
            var fila_vacia2 = "<tr id='vacio2'><td colspan='5'>@lang('accounting.no_data')</td></tr>";
            $("#lista2").append(fila_vacia2);
            $("#total_debe2").val('');
            $("#total_haber2").val('');
            total2 = 0;
            cont2 = 0;
            id_a2.length = 0;
            valor2.length = 0;
        }
    }
});

function changeType()
{
    id = $("#select-type-transaction").val();
    if (id != null) {
        var route = "/type-bank-transactions/"+id;
        $.get(route, function(res){
            if(res.type == "credit") {
                $("#label-type").text("{{__('accounting.account_to_debit')}}");
                $("#div_accounts").show();
            }
            else {
                $("#label-type").text("{{__('accounting.account_to_credit')}}");
                $("#div_accounts").show();
            }
            if(res.enable_checkbook == 1) {
                $("#div_checkbook").show();
                $("div#check_number").show();
                $("#div_reference").hide();
                $("#txt-reference-transaction").val('0');
                $("#txt-check-number-transaction").val('');
            }
            else
            {
                $("#div_checkbook").hide();
                $("div#check_number").hide();
                $("#div_reference").show();
                $("#txt-reference-transaction").val('');
                $("#txt-check-number-transaction").val('0');
            }
            if(res.enable_headline == 1) {
                $("#div_contact").show();
            }
            else
            {
                $("#div_contact").hide();
            }
            $("#select-bank-account-id-transaction").val('0').change();
            $("#select-checkbook-transaction").empty();
            $("#txt-amount-transaction").val('');
            $("#txt-payment-to").val('');
        });
    }
}

function echangeType()
{
    id = $("#eselect-type-transaction").val();
    if (id != null) {
        var route = "/type-bank-transactions/"+id;
        $.get(route, function(res){
            if(res.type == "credit") {
                $("#elabel-type").text("{{__('accounting.account_to_debit')}}");
                $("#ediv_accounts").show();
            }
            else {
                $("#elabel-type").text("{{__('accounting.account_to_credit')}}");
                $("#ediv_accounts").show();
            }
            if(res.enable_checkbook == 1) {
                $("#ediv_checkbook").show();
                $("#ediv_reference").hide();
                $("#txt-ereference-transaction").val("0");
            }
            else
            {
                $("#ediv_checkbook").hide();
                $("#ediv_reference").show();
            }
            if(res.enable_headline == 1) {
                $("#txt-echeck-number-transaction").val('');
                $("#ediv_contact").show();
                $("#txt-ereference-transaction").val("");
            }
            else
            {
                $("#txt-echeck-number-transaction").val('0');
                $("#ediv_contact").hide();
            }
            $("#eselect-bank-account-id-checkbook").val('0').change();
            $("#eselect-bank-account-id-transaction").val('0').change();
            $("#eselect-checkbook-transaction").empty();
            $("#txt-eamount-transaction").val('');
            $("#txt-epayment-to").val('');
            $("#txt-ereference-transaction").val('');
        });
    }
}



$("#select-carrier-transaction").change(function(){
    value = $("#select-carrier-transaction").val();
    if (value != null) {
        carrier = $("#select-carrier-transaction option:selected").text();
        $("#txt-payment-to").val(carrier);
        $("#select-carrier-transaction").val(0);
    }
});

$("#eselect-carrier-transaction").change(function(){
    value = $("#eselect-carrier-transaction").val();
    if (value != null) {
        carrier = $("#eselect-carrier-transaction option:selected").text();
        $("#txt-epayment-to").val(carrier);
        $("#select-carrier-transaction").val(0);
    }
});


function addLineInEditView()
{
    var route2 = "/entries/search/"+id;
    $.get(route2, function(res){
        id_c2 = res.id;
        code2 = res.code;
        name2 = res.name;
        parent2 = res.parent;
        condition2 = res.condition;
        existe2 = parseInt(jQuery.inArray(id_c2, id_a2));
        id_a2.push(id_c2);
        valor2.push(cont2);
        $("#vacio2").remove();


        type_id = $("#eselect-type-transaction").val();
        var route = "/type-bank-transactions/"+type_id+"/edit";
        $.get(route, function(res){
            if(res.type == "debit") {
                var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="deleteLineInEditView('+cont2+', '+id_c2+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01" onchange="disableCreditInEditView('+cont2+')"></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" onchange="disableDebitInEditView('+cont2+')"></td></tr><tr id="fila_description2'+cont2+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
                $("#lista2").append(fila2);
                $("#pie2").show();
                $("#haber2"+cont2+"").focus();
                cont2++;
            }
            else {
               var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="deleteLineInEditView('+cont2+', '+id_c2+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01" onchange="disableCreditInEditView('+cont2+')"></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" onchange="disableDebitInEditView('+cont2+')"></td></tr><tr id="fila_description2'+cont2+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
               $("#lista2").append(fila2);
               $("#pie2").show();
               $("#debe2"+cont2+"").focus();
               cont2++;
           }
       });
    });
}

function addLineInAddView()
{
    var route = "/entries/search/"+id;
    $.get(route, function(res){
        id_c = res.id;
        code = res.code;
        name = res.name;
        parent = res.parent;
        condition = res.condition;
        existe = parseInt(jQuery.inArray(id_c, id_a));
        id_a.push(id_c);
        valor.push(cont);
        $("#vacio").remove();

        type_id = $("#select-type-transaction").val();
        var route = "/type-bank-transactions/"+type_id+"/edit";
        $.get(route, function(res){
            if(res.type == "debit") {
                var fila='<tr class="selected" id="fila'+cont+'" style="height: 10px"><td style="width: 5%"><button id="bitem'+cont+'" type="button" class="btn btn-warning btn-sm" onclick="deleteLineInAddView('+cont+', '+id_c+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id[]" value="'+id_c+'">'+code+'</td><td style="width: 50%">'+name+'</td><td style="width: 15%"><input type="text" name="debe[]" id="debe'+cont+'" value="" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01" onchange="disableCreditInAddView('+cont+')"></td><td style="width: 15%"><input type="text" name="haber[]" id="haber'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" onchange="disableDebitInAddView('+cont+')"></td></tr><tr id="fila_description'+cont+'"><td colspan="5"><input type="text" name="description_line[]" id="description_line'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
                $("#lista").append(fila);
                $("#pie").show();
                $("#haber"+cont+"").focus();
                cont++;
            }
            else {
               var fila='<tr class="selected" id="fila'+cont+'" style="height: 10px"><td style="width: 5%"><button id="bitem'+cont+'" type="button" class="btn btn-warning btn-sm" onclick="deleteLineInAddView('+cont+', '+id_c+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id[]" value="'+id_c+'">'+code+'</td><td style="width: 50%">'+name+'</td><td style="width: 15%"><input type="text" name="debe[]" id="debe'+cont+'" value="" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01" onchange="disableCreditInAddView('+cont+')"></td><td style="width: 15%"><input type="text" name="haber[]" id="haber'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" onchange="disableDebitInAddView('+cont+')"></td></tr><tr id="fila_description'+cont+'"><td colspan="5"><input type="text" name="description_line[]" id="description_line'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
               $("#lista").append(fila);
               $("#pie").show();
               $("#debe"+cont+"").focus();
               cont++;
           }
       });
    });
}

Array.prototype.removeItem = function (a) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == a) {
            for (var i2 = i; i2 < this.length - 1; i2++) {
                this[i2] = this[i2 + 1];
            }
            this.length = this.length - 1;
            return;
        }
    }
};

function deleteLineInAddView(index, id_p){ 
    $("#fila" + index).remove();
    $("#fila_description" + index).remove();
    id_a.removeItem(id_p);
    if(id_a.length == 0)
    {
        var fila_vacia = "<tr id='vacio'><td colspan='5'>@lang('accounting.no_data')</td></tr>";
        $("#lista").append(fila_vacia);
        $("#pie").hide();
    }
    calculate();
}

function deleteLineInEditView(index2, id_p2){  
    $("#fila2" + index2).remove();
    $("#fila_description2" + index2).remove();
    id_a2.removeItem(id_p2);
    if(id_a2.length == 0)
    {
        var fila_vacia2 = "<tr id='vacio2'><td colspan='5'>@lang('accounting.no_data')</td></tr>";
        $("#lista2").append(fila_vacia2);
        $("#pie2").hide();
    }
    calculate2();
}

$("#accounts").change(function(event) {
    value = $("#accounts").val();
    if (value != null) {
        id_bank_account = $("#select-bank-account-id-transaction").val();
        if (id_bank_account != null) {
            id = $("#accounts").val()
            addLineInAddView();
            $("#accounts").val('');
            $("#accounts").val(0);
        }
        else {
            Swal.fire
            ({
                title: "{{__('accounting.select_account_first')}}",
                icon: "error",
            });
        }
    }
});

$("#eaccounts").change(function(event) {
    value = $("#eaccounts").val();
    if (value != null) {
        id_bank_account = $("#eselect-bank-account-id-transaction").val();
        if (id_bank_account != null) {
            id = $("#eaccounts").val()
            addLineInEditView();
            $("#eaccounts").val('');
            $("#eaccounts").val(0);
        }
        else {
            Swal.fire
            ({
                title: "{{__('accounting.select_account_first')}}",
                icon: "error",
            });
        }
    }
});


function disableCreditInAddView(cont)
{
    if($("#debe"+cont+"").val()!="")
    {
        amount = parseFloat($("#debe"+cont+"").val());
        $("#debe"+cont+"").val(amount.toFixed(2));
        $("#haber"+cont+"").val('0');
    }
    else
    {
        $("#haber"+cont+"").val('');
    }   
    calculate();
}

function disableDebitInAddView(cont)
{
    if($("#haber"+cont+"").val()!="")
    {
        amount = parseFloat($("#haber"+cont+"").val());
        $("#haber"+cont+"").val(amount.toFixed(2));
        $("#debe"+cont+"").val('0');
    }
    else
    {
        $("#debe"+cont+"").val('');
    }
    calculate();
}

function disableCreditInEditView(cont2)
{
    if($("#debe2"+cont2+"").val()!="")
    {
        amount = parseFloat($("#debe2"+cont2+"").val());
        $("#debe2"+cont2+"").val(amount.toFixed(2));
        $("#haber2"+cont2+"").val('0.00');
    }
    else
    {
        $("#haber2"+cont2+"").val('');
    }   
    calculate2();
}

function disableDebitInEditView(cont2)
{
    if($("#haber2"+cont2+"").val()!="")
    {
        amount = parseFloat($("#haber2"+cont2+"").val());
        $("#haber2"+cont2+"").val(amount.toFixed(2));
        $("#debe2"+cont2+"").val('0.00');

    }
    else
    {
        $("#debe2"+cont2+"").val('');
    }
    calculate2();
}

function calculate()
{
    total_debe = 0;
    total_haber = 0;
    $.each(valor, function(value){
        if($("#debe"+value+"").val()!="")
        {
            if (typeof $("#debe"+value+"").val() != "undefined")
            {
                total_debe = total_debe + parseFloat($("#debe"+value+"").val());
            }
        }
        if($("#haber"+value+"").val()!="")
        {
            if (typeof $("#haber"+value+"").val() != "undefined")
            {
                total_haber = total_haber + parseFloat($("#haber"+value+"").val());
            }
        }
    });
    $("#total_debe").val(total_debe.toFixed(2));
    $("#total_haber").val(total_haber.toFixed(2));
}

function calculate2()
{
    total_debe2 = 0;
    total_haber2 = 0;
    $.each(valor2, function(value2) {
        if($("#debe2"+value2+"").val()!="")
        {
            if (typeof $("#debe2"+value2+"").val() != "undefined")
            {
                total_debe2 = total_debe2 + parseFloat($("#debe2"+value2+"").val());
            }
        }
        if($("#haber2"+value2+"").val()!="")
        {
            if (typeof $("#haber2"+value2+"").val() != "undefined")
            {
                total_haber2 = total_haber2 + parseFloat($("#haber2"+value2+"").val());
            }
        }
    });
    $("#total_debe2").val(total_debe2.toFixed(2));
    $("#total_haber2").val(total_haber2.toFixed(2));
}

function filterFloat(evt,input){
    var key = window.Event ? evt.which : evt.keyCode;    
    var chark = String.fromCharCode(key);
    var tempValue = input.value+chark;
    if(key >= 48 && key <= 57){
        if(filter(tempValue)=== false){
            return false;
        }else{       
            return true;
        }
    }else{
        if(key == 8 || key == 13 || key == 0) {     
            return true;              
        }else if(key == 46){
            if(filter(tempValue)=== false){
                return false;
            }else{       
                return true;
            }
        }else{
            return false;
        }
    }
}

function filter(__val__){
    var preg = /^([0-9]+\.?[0-9]{0,2})$/; 
    if(preg.test(__val__) === true){
        return true;
    }else{
        return false;
    }
}

$("#txt-amount-transaction").change(function(){
    amount = parseFloat($("#txt-amount-transaction").val());
    type_id = $("#select-type-transaction").val();
    var route = "/type-bank-transactions/"+type_id+"/edit";
    $.get(route, function(res){
        if (res.type == "debit") {
            $("#debe0").val(amount.toFixed(2));
            $("#haber0").val("0");
            calculate();
        }
        else {
            $("#haber0").val(amount.toFixed(2));
            $("#debe0").val("0");
            calculate();
        }
    });
});


$("#txt-eamount-transaction").change(function(){
    amount = parseFloat($("#txt-eamount-transaction").val());
    $(".cost").val(amount.toFixed(2));
    calculate2();

});

$("#select-bank-account-id-transaction").change(function(event){
    value = $("#select-bank-account-id-transaction").val();
    value_type = $("#select-type-transaction").val();
    if ((value != null) && (value_type != null)) {
        updateSelectsBankCheckbooks();
        text_account = $("#select-bank-account-id-transaction option:selected").text();
        $("#newData tbody tr").remove();
        $("#total_debe").val('');
        $("#total_haber").val('');

        total = 0;
        cont = 0;
        id_a.length = 0;
        valor.length = 0;

        id = $("#select-bank-account-id-transaction").val()

        var route = "/bank-accounts/"+id;
        $.get(route, function(res){
            catalogue_id = res.catalogue_id;
            var route = "/entries/search/"+catalogue_id;
            $.get(route, function(res){
                id_c = res.id;
                code = res.code;
                name = res.name;
                parent = res.parent;
                condition = res.condition;
                existe = parseInt(jQuery.inArray(id_c, id_a));
                id_a.push(id_c);
                valor.push(cont);
                $("#vacio").remove();
                type_id = $("#select-type-transaction").val();
                var route = "/type-bank-transactions/"+type_id+"/edit";
                $.get(route, function(res){
                    if(res.type == "debit") {
                        var fila='<tr class="selected" id="fila'+cont+'" style="height: 10px"><td style="width: 5%"><button id="bitem'+cont+'" type="button" class="btn btn-warning btn-sm" onclick="deleteLineInAddView('+cont+', '+id_c+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id[]" value="'+id_c+'">'+code+'</td><td style="width: 50%">'+text_account+'</td><td style="width: 15%"><input type="text" name="debe[]" id="debe'+cont+'" value="" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01" onchange="disableCreditInAddView('+cont+')" readonly></td><td style="width: 15%"><input type="text" name="haber[]" id="haber'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" onchange="disableDebitInAddView('+cont+')" readonly></td></tr><tr id="fila_description'+cont+'"><td colspan="5"><input type="text" name="description_line[]" id="description_line'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
                        $("#lista").append(fila);
                        $("#pie").show();
                        $("#txt-amount-transaction").val('');
                        cont++;

                    }
                    else {
                       var fila='<tr class="selected" id="fila'+cont+'" style="height: 10px"><td style="width: 5%"><button id="bitem'+cont+'" type="button" class="btn btn-warning btn-sm" onclick="deleteLineInAddView('+cont+', '+id_c+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id[]" value="'+id_c+'">'+code+'</td><td style="width: 50%">'+text_account+'</td><td style="width: 15%"><input type="text" name="debe[]" id="debe'+cont+'" value="" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01" onchange="disableCreditInAddView('+cont+')" readonly></td><td style="width: 15%"><input type="text" name="haber[]" id="haber'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" onchange="disableDebitInAddView('+cont+')" readonly></td></tr><tr id="fila_description'+cont+'"><td colspan="5"><input type="text" name="description_line[]" id="description_line'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
                       $("#lista").append(fila);
                       $("#pie").show();
                       $("#txt-amount-transaction").val('');
                       cont++;

                   }
               });
                $("#select-checkbook-transaction").val('0').change();
                $("#txt-amount-transaction").val('');
                $("#txt-payment-to").val('');
            });

        });
    }
});

$("#eselect-bank-account-id-transaction").change(function(event){
    flag = $("#flag-edit").val();
    if (flag == 1) {
        value = $("#eselect-bank-account-id-transaction").val();
        value_type = $("#eselect-type-transaction").val();
        if ((value != null) && (value_type != null)) {
            eupdateSelectsBankCheckbooks();
            text_account = $("#eselect-bank-account-id-transaction option:selected").text();
            $("#newData2 tbody tr").remove();
            $("#total_debe2").val('');
            $("#total_haber2").val('');

            total2 = 0;
            cont2 = 0;
            id_a2.length = 0;
            valor2.length = 0;

            id = $("#eselect-bank-account-id-transaction").val()

            var route = "/bank-accounts/"+id;
            $.get(route, function(res){
                catalogue_id = res.catalogue_id;
                var route = "/entries/search/"+catalogue_id;
                $.get(route, function(res){
                    id_c2 = res.id;
                    code2 = res.code;
                    name2 = res.name;
                    parent2 = res.parent;
                    condition2 = res.condition;
                    existe2 = parseInt(jQuery.inArray(id_c2, id_a2));
                    id_a2.push(id_c2);
                    valor2.push(cont2);
                    $("#vacio2").remove();
                    type_id = $("#eselect-type-transaction").val();
                    var route = "/type-bank-transactions/"+type_id+"/edit";
                    $.get(route, function(res){
                        if(res.type == "debit") {
                            var fila2 = '<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="deleteLineInEditView('+cont2+', '+id_c2+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+text_account+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control cost" step="0.01" onchange="disableCreditInEditView('+cont2+')" readonly></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" onchange="disableDebitInEditView('+cont2+')" readonly></td></tr><tr id="fila_description2'+cont2+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
                            $("#lista2").append(fila2);
                            $("#pie2").show();
                            $("#txt-eamount-transaction").val('');
                            cont2++;
                        }
                        else {
                            var fila2 = '<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="deleteLineInEditView('+cont2+', '+id_c2+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+text_account+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01" onchange="disableCreditInEditView('+cont2+')" readonly></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control cost" onchange="disableDebitInEditView('+cont2+')" readonly></td></tr><tr id="fila_description2'+cont+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
                            $("#lista2").append(fila2);
                            $("#pie2").show();
                            $("#txt-eamount-transaction").val('');
                            cont2++;

                        }
                    });
                    $("#eselect-checkbook-transaction").val('0').change();
                    $("#txt-eamount-transaction").val('');
                    $("#txt-epayment-to").val('');
                });

            });
        }
    }
});

$("#select-checkbook-transaction").change(function(){
    id = $("#select-checkbook-transaction").val();
    if (id != null) {
        var route = "/banks/getCheckNumber/"+id;
        $.get(route, function(res){
            $("#txt-check-number-transaction").val(res.number);
        });
    }
});

$("#eselect-checkbook-transaction").change(function(){
    flag = $("#flag-edit").val();
    if (flag == 1) {
        id = $("#eselect-checkbook-transaction").val();
        if (id != null) {
            var route = "/banks/getCheckNumber/"+id;
            $.get(route, function(res){
                $("#txt-echeck-number-transaction").val(res.number);
            });
        }
    }
});

$("#period_id").change(function(){
    id = $("#period_id").val();

    if (id != null) {
        var route = "/bank-transactions/getDateByPeriod/"+id;
        $.get(route, function(res){
            $("#txt-date-transaction").val(res.date);
        });
    }
});

$("#eperiod_id").change(function(){
    id = $("#eperiod_id").val();

    flag = $("#flag-edit").val();
    if (flag == 1) {
        if (id != null) {
            var route = "/bank-transactions/getDateByPeriod/"+id;
            $.get(route, function(res){
                $("#txt-edate-transaction").val(res.date);
            });
        }
    }

    
});

$("#txt-check-number-transaction").change(function(){
    checkbook = $("#select-checkbook-transaction").val();
    if (checkbook != null) {
        number = $("#txt-check-number-transaction").val();
        if(Math.floor(number) == number && $.isNumeric(number)) {
           var route = "/bank-checkbooks/validateNumber/"+checkbook+"/"+number;
           $.get(route, function(res){
            if(res.success == false) {
                Swal.fire
                ({
                    title: "{{__('accounting.number_exist')}}",
                    icon: "error",
                });
                $("#txt-check-number-transaction").val('');
            }
            else {
                var route = "/bank-checkbooks/validateRange/"+checkbook+"/"+number;
                $.get(route, function(result){
                    if(result.success == false) {
                        Swal.fire
                        ({
                            title: result.msg,
                            icon: "error",
                        });
                        $("#txt-check-number-transaction").val('');
                    }
                });
            }
            
        });
       }
       else {
         Swal.fire
         ({
            title: "{{__('accounting.number_invalid')}}",
            icon: "error",
        });
         $("#txt-check-number-transaction").val('');
     }
 }
 else {
    Swal.fire
    ({
        title: "{{__('accounting.select_checkbook')}}",
        icon: "error",
    });
    $("#txt-check-number-transaction").val('');
}
});


$("#txt-echeck-number-transaction").change(function(){
    checkbook = $("#eselect-checkbook-transaction").val();
    if (checkbook != null) {
        number = $("#txt-echeck-number-transaction").val();
        old_number = $("#hidden-echeck-number-transaction").val();
        if(Math.floor(number) == number && $.isNumeric(number)) {
            if (number != old_number) {
                var route = "/bank-checkbooks/validateNumber/"+checkbook+"/"+number;
                $.get(route, function(res){
                    if(res.success == false) {
                        Swal.fire
                        ({
                            title: "{{__('accounting.number_exist')}}",
                            icon: "error",
                        });
                        $("#txt-echeck-number-transaction").val('');
                    }
                    else {
                        var route = "/bank-checkbooks/validateRange/"+checkbook+"/"+number;
                        $.get(route, function(result){
                            if(result.success == false) {
                                Swal.fire
                                ({
                                    title: result.msg,
                                    icon: "error",
                                });
                                $("#txt-echeck-number-transaction").val('');
                            }
                        });
                    }

                });
            }
        }
        else {
         Swal.fire
         ({
            title: "{{__('accounting.number_invalid')}}",
            icon: "error",
        });
         $("#txt-echeck-number-transaction").val('');
     }
 } else {    
    Swal.fire
    ({
        title: "{{__('accounting.select_checkbook')}}",
        icon: "error",
    });
    $("#txt-echeck-number-transaction").val('');
}
});

$("#txt-date-transaction").change(function(){
    id = $("#period_id").val();
    dat = $("#txt-date-transaction").val();
    if (id != null) {
        var route = "/bank-transactions/validateDate/"+id+"/"+dat;
        $.get(route, function(result){
            if (result.success == false) {
                Swal.fire
                ({
                    title: result.msg,
                    icon: "error",
                });
                $("#txt-date-transaction").val('');
                $("#period_id").val(0).change();
            }
        });
    }
    else {
        Swal.fire
        ({
            title: "{{__('accounting.select_period')}}",
            icon: "error",
        });
        $("#txt-date-transaction").val('');
        $("#period_id").val(0).change();
    }
});

$("#txt-edate-transaction").change(function(){
    id = $("#eperiod_id").val();
    dat = $("#txt-edate-transaction").val();
    if (id != null) {
        var route = "/bank-transactions/validateDate/"+id+"/"+dat;
        $.get(route, function(result){
            if (result.success == false) {
                Swal.fire
                ({
                    title: result.msg,
                    icon: "error",
                });
                $("#txt-edate-transaction").val('');
                $("#eperiod_id").val(0).change();
            }
        });
    }
    else {
        Swal.fire
        ({
            title: "{{__('accounting.select_period')}}",
            icon: "error",
        });
        $("#txt-edate-transaction").val('');
        $("#eperiod_id").val(0).change();
    }
});

$("#link-banks").click(function(){
    $("#thead-banks").show();
});

$("#link-accounts").click(function(){
    $("#thead-banks").hide();
});

$("#link-checkbooks").click(function(){
    $("#thead-banks").hide();
});

$("#link-types").click(function(){
    $("#thead-banks").hide();
});

$("#link-transactions").click(function(){
    $("#thead-banks").hide();
});

$("#link-reports").click(function(){
    $("#thead-banks").hide();
});

$("#link-bank-reconciliation").click(function(){
    $("#thead-banks").hide();
});
function cancelCheck(id)
{
    Swal.fire({
        title: LANG.sure,
        text: "{{__('accounting.confirm_alert')}}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "{{__('messages.accept')}}",
        cancelButtonText: "{{__('messages.cancel')}}"
    }).then((willDelete) => {
        if (willDelete.value) {
           var route = "/bank-transactions/cancelCheck/"+id;
           $.get(route, function(res){
            if (res.success == true) {
                Swal.fire
                ({
                    title: res.msg,
                    icon: "success",
                });
                $("#bank-transactions-table").DataTable().ajax.reload(null, false);
            }
            else {
                Swal.fire
                ({
                    title: res.msg,
                    icon: "error",
                });
            }
        });
       }
   });
}

$("#report_type").change(function(){
    showSelectCheckbook();
});
function showSelectCheckbook()
{
    id = $("#report_type").val();
    if (id != 0) {
        var route = "/type-bank-transactions/"+id;
        $.get(route, function(type){
            if(type.enable_checkbook == 1) {
                $("#report_checkbook").val(0).change();
                $("#div_select_checkbook").show();
            }
            else {
                $("#report_checkbook").val(0).change();
                $("#div_select_checkbook").hide();
            }
        });
    }
    else {
        $("#report_checkbook").val(0).change();
        $("#div_select_checkbook").hide();
    }
}

$(document).bind('keydown', 'Shift+1', function(){
    description = $("#description_2").val();
    flag = $("#flag-edit-entrie")
    $("#txt-description-transaction").val(description);
    
});
$(document).bind('keydown', 'Shift+2', function(){
    description = $("#description_3").val();
    flag = $("#flag-edit-entrie")
    $("#txt-description-transaction").val(description);
    
});
$(document).bind('keydown', 'Shift+3', function(){
    description = $("#description_4").val();
    flag = $("#flag-edit-entrie")
    $("#txt-description-transaction").val(description);
    
});
$(document).bind('keydown', 'Shift+4', function(){
    description = $("#description_5").val();
    flag = $("#flag-edit-entrie")
    $("#txt-description-transaction").val(description);
    
});
$(document).bind('keydown', 'Shift+5', function(){
    description = $("#description_6").val();
    flag = $("#flag-edit-entrie")
    $("#txt-description-transaction").val(description);
    
});
$(document).bind('keydown', 'Shift+6', function(){
    description = $("#description_7").val();
    flag = $("#flag-edit-entrie")
    $("#txt-description-transaction").val(description);
    
});
$(document).bind('keydown', 'Shift+7', function(){
    description = $("#description_8").val();
    flag = $("#flag-edit-entrie")
    $("#txt-description-transaction").val(description);
    
});
$(document).bind('keydown', 'Shift+8', function(){
    description = $("#description_9").val();
    flag = $("#flag-edit-entrie")
    $("#txt-description-transaction").val(description);
    
});
$(document).bind('keydown', 'Shift+9', function(){
    description = $("#description_10").val();
    flag = $("#flag-edit-entrie")
    $("#txt-description-transaction").val(description);
    
});

$(document).bind('keyup', 'Shift+m', function(){
    $("#txt-amount-transaction").focus();
    $("#txt-amount-transaction").val('');
});

function printBankTransaction(id) {
    var url = '{!! URL::to('/bank-transactions/printCheck/:id/:print') !!}';
    url = url.replace(':id', id);

    Swal.fire({
        title: LANG.what_print,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#7367f0',
        confirmButtonText: LANG.check,
        cancelButtonText: LANG.check_and_voucher
    }).then((result) => {
        console.log(result.isConfirmed);
        if (result.isConfirmed) {
            url = url.replace(':print', 1)
            window.open(url, '_blank');
        } else {
            url = url.replace(':print', 2)
            window.open(url, '_blank');
        }
    });
}

function addExpenses(id) {
    route = '/expenses/get_add_expenses/'+id;
    $("div.add_expenses_modal").load(route, function() {
        $(this).modal({
            backdrop: 'static'
        });
    });
}
</script>
@endsection
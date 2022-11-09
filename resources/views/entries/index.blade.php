@extends('layouts.app')
@section('title', __('accounting.tittle_entries'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('accounting.tittle_entries')
        <small>@lang('accounting.entries_menu')</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>
<!-- Main content -->
<section class="content">
    <div class="panel with-nav-tabs panel-default boxform_u box-solid_u">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-list" id="tab_list" data-toggle="tab">@lang('accounting.tab_list_entrie')</a></li>
                <li><a href="#tab-new" data-toggle="tab" id="tab_new_entrie">@lang('accounting.tab_new_entrie')</a></li>
                <li><a href="#tab-report" id="tab_report" data-toggle="tab">@lang('accounting.tab_report_entrie')</a></li>
                <li><a href="#tab-type" id="tab_type" data-toggle="tab">@lang('accounting.tab_type_entrie')</a></li>
                @if($business->entries_numeration_mode != 'manual')
                <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-one-numeration-tab-tab" data-toggle="pill"
                    href="#custom-tabs-one-numeration" role="tab" aria-controls="custom-tabs-one-messages"
                    aria-selected="false">@lang('accounting.tab_numeration_entrie')</a>
                </li>
                @endif
            </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade in active" id="tab-list">
                    <div id="div_lista">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>@lang('accounting.period')</label>
                                <select name="filter-period" id="filter-period" class="form-control select2">
                                    <option value="0" selected>@lang('accounting.all')</option>
                                    @foreach($periods_filter as $period)
                                    <option value="{{$period->id}}">{{$period->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>@lang('accounting.type')</label>
                                <select name="filter-type-entrie-id" id="filter-type-entrie-id" class="form-control select2" style="width: 100%;">
                                    <option value="0" selected>@lang('accounting.all')</option>
                                    @foreach($types as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>@lang('accounting.location')</label>
                                <select name="filter-business-location-id" id="filter-business-location-id" class="form-control select2">
                                    <option value="0" selected>@lang('accounting.all')</option>
                                    @foreach($business_locations as $location)
                                    <option value="{{$location->id}}">{{$location->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>                    
                        <h4>@lang('accounting.tab_list_entrie')</h4>
                        <div id="msj-success2" class="alert alert-success" role="alert" style="display: none;">
                            <strong id="msj22"></strong>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-condensed table-hover" id="entriesData" width="100%">
                                <thead id="thead-entries">
                                    <th>@lang('accounting.date')</th>
                                    <th>@lang('accounting.number')</th>
                                    <th>@lang('accounting.correlative')</th>
                                    <th>@lang('accounting.correlative')</th>
                                    <th>@lang('accounting.description')</th>
                                    <th>@lang('accounting.period')</th>
                                    <th>@lang('accounting.type')</th>
                                    <th>@lang('accounting.location')</th>
                                    <th>@lang('accounting.status')</th>
                                    <th>@lang('messages.actions')</th>
                                </thead>
                            </table>
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
                                                <input type="text" id="txt-ereference-transaction" name="txt-ereference-transaction" class="form-control" placeholder="@lang('accounting.reference')">
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
                                            <select name="ebusiness_location_id" id="eebusiness_location_id" class="form-control select2" style="width: 100%">
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
                                                <input type="text" id="txt-edescription-transaction" name="txt-edescription-transaction" class="form-control" placeholder="@lang('accounting.description')">
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
                                                        <option value="{{$item->id}}">{{$item->supplier_business_name}} {{$item->name}}</option>
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
                             <div class="table-responsive">
                                <table class="table table-striped table-bordered table-condensed table-hover" id="newData3">
                                    <thead>
                                        <th style="width: 5%">
                                            @lang('accounting.options')
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
                                    <tbody id="lista3">
                                        <tr id="vacio3"><td colspan="5">@lang('accounting.no_data')</td></tr>
                                    </tbody>
                                    <tfoot id="pie3" style="display: none;">
                                        <th class="text-right" style="width: 70%" colspan="3">
                                            @lang('accounting.totals')
                                        </th>                   
                                        <th style="width: 15%">
                                            <input type="text" name="total_debe2" id="total_debe3" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" readonly>
                                        </th>
                                        <th style="width: 15%">
                                            <input type="text" name="total_haber2" id="total_haber3" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" readonly>
                                        </th>
                                    </tfoot>
                                </table>
                            </div>
                        </form>
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div id="content4" class="col-lg-12" style="display: none;">
                                <img src="{{ asset('img/loader.gif') }}" alt="loading" />
                            </div>
                        </div>

                        <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-edit-transaction">
                        <button type="button" class="btn btn-danger" id="btn-close-modal-edit-transaction">@lang('messages.cancel')</button>
                    </div>
                </div>
            </div>




        </div>
        <div class="tab-pane fade" id="tab-new">
            {!! Form::open(['id'=>'form_entrie']) !!}
            <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
            <h4>@lang('accounting.tab_new_entrie')</h4>
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label for="code">@lang('accounting.accounts')</label>
                        <select id="account" class="form-control select2" style="width: 100%;">
                            <option value="0" disabled selected>@lang('messages.please_select')</option>
                            @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->code }} {{ $account->name }}=>{{$account->padre->name}}</option>
                            @endforeach
                            <option value="-1">@lang('accounting.close_result_accounts')</option>
                            <option value="-2">Apertura de Saldos</option>
                        </select>
                        <input type="hidden" name="numeration-mode" id="numeration-mode" value="{{ $business_numeration_entries->entries_numeration_mode }}">

                        <input type="hidden" name="edition_in_approved_entries" id="edition_in_approved_entries" value="{{ $business->edition_in_approved_entries }}">

                        <input type="hidden" name="deletion_in_approved_entries" id="deletion_in_approved_entries" value="{{ $business->deletion_in_approved_entries }}">

                        <input type="hidden" name="edition_in_number_entries" id="edition_in_number_entries" value="{{ $business->edition_in_number_entries }}">

                        <input type="hidden" name="allow_uneven_totals_entries" id="allow_uneven_totals_entries" value="{{ $business->allow_uneven_totals_entries }}">
                        @foreach($shortcuts as $item)
                        <input type="hidden" name="{{ $item->shortcut }}" id="description_{{ $item->id }}" value="{{ $item->description }}">
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label for="code">@lang('accounting.period')</label>
                        <select name="period_id" id="period_id" class="form-control select2" style="width: 100%;">
                            @foreach($periods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                   <div class="form-group">
                    <label for="code">@lang('accounting.date')</label>
                    <div class="wrap-inputform">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! Form::date('date', \Carbon\Carbon::now()->format('Y-m-d'), ['name'=>'date', 'id'=>'date', 'class'=>'inputform2']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">


            @if($business->edition_in_number_entries == 1) 

            
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <label>@lang('accounting.number')</label>
                <input type="text" name="number" id="number" class="form-control">
            </div>

            @else
            
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <label>@lang('accounting.number')</label>
                <input type="text" name="number" id="number" class="form-control" readonly>
            </div>                    

            @endif
            

            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <label>@lang('accounting.type')</label>
                <select name="type_entrie_id" id="type_entrie_id" class="form-control select2" style="width: 100%">

                    <option value="0" selected>@lang('accounting.all')</option>
                    @foreach($types as $item)
                    <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach


                </select>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <label>@lang('accounting.location')</label>
                <select name="business_location_id" id="business_location_id" class="form-control select2" style="width: 100%;">
                    <option value="0" selected disabled>@lang('messages.please_select')</option>
                    @foreach($business_locations as $location)
                    <option value="{{$location->id}}">{{$location->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                    <label for="code">@lang('accounting.description')</label>
                    <input class="form-control" name="description" id="description" type="text" placeholder="@lang('accounting.description')">
                </div>
            </div>
        </div>
        <div id="msj-errors" class="alert alert-danger" role="alert" style="display: none;">
            <button type="button" class="close" aria-label="Close" id="close-error">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong id="msj"></strong>
        </div>
        <div id="msj-success" class="alert alert-success" role="alert" style="display: none;">
            <strong id="msj2"></strong>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover" id="newData">
                <thead>
                    <th style="width: 5%">
                        @lang('accounting.options')
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
            <div class="form-group" style="display: none;" id="button_save">
                <input type="button" class="btn btn-success" value="@lang('messages.save')" id="registro_entrie">
                <input type="button" class="btn btn-danger" value="@lang('accounting.clean')" id="btn-clean-new-entrie">
                <input type="button" class="btn btn-dark" value="↑" id="subir2">
            </div>  
        </div>
        {!! Form::close() !!}
    </div>
    <div class="tab-pane fade" id="tab-report">
        <h4> @lang('accounting.entrie_report')</h4>
        <div id="msj-errors2" class="alert alert-danger" role="alert" style="display: none;">
            <strong id="msj3"></strong>
        </div>
        
        {!! Form::open(['action' => 'ReporterController@allEntries', 'method' => 'post', 'target' => '_blank']) !!}
        <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
        <div  class="row">
            <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
                <label for="from"> @lang('accounting.from')</label>
                <div class="wrap-inputform">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::date('from', \Carbon\Carbon::now()->format('Y-m-d'), ['id'=>'from', 'class'=>'inputform2']) !!}
                </div>
            </div>
            <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
                <label for="from"> @lang('accounting.to')</label>
                <div class="wrap-inputform">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::date('to', \Carbon\Carbon::now()->format('Y-m-d'), ['id'=>'to', 'class'=>'inputform2']) !!}
                </div>
            </div>

            <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <label for="numero">@lang('accounting.numeration')</label>
                <input type="text" id="numero" name="numero" class="form-control" placeholder="@lang('accounting.numeration')">
            </div>

            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-xs-12">
                <label>@lang('accounting.format')</label>
                <select name="report-type" id="report-type" class="form-control select2" style="width: 100%">
                    <option value="pdf" selected>PDF</option>
                    <option value="excel">Excel</option>
                </select>                       
            </div>

            <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
                <label>@lang('accounting.size_font')</label>
                <select name="size" id="size" class="form-control select2" style="width: 100%;">
                    <option value="7">7</option>
                    <option value="8" selected>8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                </select>                       
            </div>

        </div>
        <div class="row">
            <div id="content3" class="col-lg-12" style="display: none;">
                @lang('accounting.wait_please')...
                <img src="{{ asset('img/loader.gif') }}" alt="loading" />
            </div>
        </div>
        <div class="form-group">            
            <input type="submit" class="btn btn-primary" value="@lang('accounting.generate')" id="report_pdf">
        </div>
        {!! Form::close() !!}
    </div>
    <div class="tab-pane fade" id="tab-type">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <h3 class="box-title">@lang( 'accounting.all_your_types' )</h3>
                <div class="box-tools">
                    <button type="button" class="btn btn-primary" data-toggle='modal' data-target='#modal-add-type' data-backdrop="static" data-keyboard="false" id="btn-new-type"><i class="fa fa-plus"></i> @lang( 'messages.add' )
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover" id="types-table" width="100%">
                        <thead>
                            <th>@lang('accounting.name')</th>
                            <th>@lang('accounting.description')</th>
                            <th>@lang('accounting.short_name')</th>
                            <th>@lang('messages.action' )</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="tab-pane fade" id="custom-tabs-one-numeration" role="tabpanel"
    aria-labelledby="custom-tabs-one-numeration-tab">
    <div class="card">
        <div class="card-body">
            <h4> @lang('accounting.tab_numeration_entrie')</h4>
            <input type="hidden" name='numeration_mode' id='numeration_mode' value='{{ $business->entries_numeration_mode }}'>

            <div class="row">
                @if($business->entries_numeration_mode == 'month')

                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <div class="form-group">
                        <label>@lang('accounting.month_plural')</label>
                        {!! Form::select('period', $months, null, ['id' => 'period', 'class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;']) !!}
                    </div>
                </div>
                @endif

                @if($business->entries_numeration_mode == 'year')

                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <div class="form-group">
                        <label>@lang('accounting.age')</label>
                        {!! Form::select('period', $years, null, ['id' => 'period', 'class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;']) !!}
                    </div>
                </div>

                @endif
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <input type="button" class="btn btn-primary" value='@lang("accounting.send")' id='btn_renumeration'>
                </div>
            </div>

        </div>
    </div>
</div>
</div>
</div>
</div>

<div class="modal fade" tabindex="-1" id="verPartida" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content"  style="border-radius: 20px;">
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
                                    @lang('accounting.number')
                                    <span id="numeroPartida">
                                    </span>
                                </h4>
                                <h4>
                                    @lang('accounting.date')
                                    <span id="fecha">                                   
                                    </span>
                                </h4>
                                <h4>
                                    @lang('accounting.description')
                                    <span id="descripcion">
                                    </span>
                                </h4>
                            </td>
                        </tr>                           
                        <tr>
                            <th style="width: 20%">@lang('accounting.code')</th>
                            <th style="width: 50%">@lang('accounting.account')</th>
                            <th style="width: 1%"></th>
                            <th style="width: 14%">@lang('accounting.debit')</th>
                            <th style="width: 1%"></th>
                            <th style="width: 14%">@lang('accounting.credit')</th>
                        </tr>
                        <tbody id="detallePartida">
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-right">
                                    @lang('accounting.totals')
                                </th>
                                <th>
                                    $
                                </th>
                                <th>
                                    <span id="tdebe">                   
                                    </span>
                                    <input type="hidden" name="total_debit" id="total_debit">
                                </th>
                                <th>
                                    $
                                </th>
                                <th>
                                    <span id="thaber">                  
                                    </span>
                                    <input type="hidden" name="total_credit" id="total_credit">
                                </th>
                            </tr>
                            <tr id="tr-validate" style="display: none;">
                                <th colspan="2" class="text-right">

                                </th>
                                <th>

                                </th>
                                <th>

                                </th>
                                <th>

                                </th>
                                <th>

                                 @lang('accounting.number')
                                 <input type="text" id="txt-number-entrie" name="txt-number-entrie" class="form-control" placeholder="@lang('accounting.number')">
                                 <input type="hidden" name="entrie_id" id="entrie_id">

                                 <button type="button" class="btn btn-primary" id="btn-add-number" style="margin-top: 5px;">@lang('messages.save')</button>

                             </th>
                         </tr>
                     </tfoot>
                 </table>
             </div>
         </div>
     </div>
     <div class="modal-footer">
        <span id="lbl-validate">
        </span>
        <button type="button" class="btn btn-danger" data-dismiss="modal" id="close-show">@lang('messages.close')</button>
    </div>
</div>
</div>
</div>

<div class="modal fade" tabindex="-1" id="eentrie" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="width: 30%">
        <div class="modal-content"  style="border-radius: 20px;">
            <div class="modal-body">
                <div class="row">

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <h3>@lang('accounting.entrie_edit')
                        </h3>
                        @include('common.errors-e')
                        @include('common.success-e')
                        {!! Form::open(['id'=>'edit_entrie']) !!}
                        <div class="form-group">
                            <label for="number-e">@lang('accounting.number')</label>

                            @if($business->edition_in_number_entries == 1) 
                            <input type="text" id="number-e" name="number-e" class="form-control" placeholder="@lang('accounting.number')">
                            @else
                            <input type="text" id="number-e" name="number-e" class="form-control" placeholder="@lang('accounting.number')" readonly>
                            @endif

                            



                            <input type="hidden" id="id-e" name="id-e">
                            <input type="hidden" name="flag-edit-entrie" id="flag-edit-entrie" value="0">
                        </div>
                        <div class="form-group">
                            <label for="date-e">@lang('accounting.date')</label>
                            <div class="wrap-inputform">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::date('from', \Carbon\Carbon::now()->format('Y-m-d'), ['name'=>'date-e', 'id'=>'date-e', 'class'=>'inputform2', 'readonly']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group">
                                <label for="description-e">@lang('accounting.description')</label>
                                <textarea id="description-e" class="form-control" rows="4" placeholder="@lang('accounting.description')"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div id="content" class="col-lg-12" style="display: none;">
                                @lang('accounting.wait_please')...
                                <img src="{{ asset('img/loader.gif') }}" alt="loading" />
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="editar_entrie">
                        </div>
                        {!!Form::close()!!}     
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-add-type" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="width: 30%">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <h3>@lang('accounting.add_type')</h3>
                    <form id="form-add-type">
                        <div class="form-group">
                            <label for="txt-name-type">@lang('accounting.name')</label>
                            <input type="text" id="txt-name-type" name="txt-name-type" class="form-control" placeholder="@lang('accounting.name')">
                        </div>
                        <div class="form-group">
                            <label for="txt-description-type">@lang('accounting.description')</label>
                            <input type="text" id="txt-description-type" name="txt-description-type" class="form-control" placeholder="@lang('accounting.description')">
                        </div>

                        <div class="form-group">
                            <label>@lang('accounting.short_name')</label>
                            <input type="text" id="txt-short_name-type" name="txt-short_name-type" class="form-control" placeholder="@lang('accounting.short_name')">
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-type">
            <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-add-type">@lang('messages.close')</button>
        </div>
    </div>
</div>
</div>


<div class="modal fade" id="modal-edit-type" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="width: 30%">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <h3>@lang('accounting.edit_type')</h3>
                    <form id="form-edit-type">
                        <div class="form-group">
                            <label for="txt-name-etype">@lang('accounting.name')</label>
                            <input type="text" id="txt-name-etype" name="txt-name-etype" class="form-control" placeholder="@lang('accounting.name')">
                            <input type="hidden" name="type_id" id="type_id">
                        </div>
                        <div class="form-group">
                            <label for="txt-description-etype">@lang('accounting.name')</label>
                            <input type="text" id="txt-description-etype" name="txt-description-etype" class="form-control" placeholder="@lang('accounting.description')">
                            <input type="hidden" name="type_id" id="type_id">
                        </div>

                        <div class="form-group">
                            <label>@lang('accounting.short_name')</label>
                            <input type="text" id="txt-short_name-etype" name="txt-short_name-etype" class="form-control" placeholder="@lang('accounting.short_name')">
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="button" class="btn btn-dark" value="@lang('messages.save')" id="btn-edit-type">
            <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-type">@lang('messages.close')</button>
        </div>
    </div>
</div>
</div>



<div class="modal fade" id="modal-edit-entrie" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="width: 70%">
        <div class="modal-content"  style="border-radius: 20px;">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div id="div_editar" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        {!! Form::open(['id'=>'form_entrie2']) !!}
                        <h4>@lang('accounting.entrie_edit')</h4>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="account2">@lang('accounting.accounts')</label>
                                    <select id="account2" class="form-control select2" style="width: 100%;">
                                        <option value="0" disabled selected>@lang('messages.please_select')</option>
                                        @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->code }} {{ $account->name }}=>{{$account->padre->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">

                                <div class="form-group">
                                    <label for="code">@lang('accounting.period')</label>
                                    <select name="period_id2" id="period_id2" class="form-control select2" style="width: 100%;">
                                        @foreach($periods as $period)
                                        <option value="{{ $period->id }}">{{ $period->name }}</option>
                                        @endforeach
                                    </select>
                                </div>


                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="code">@lang('accounting.date')</label>
                                    <div class="wrap-inputform">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        {!! Form::date('date2', \Carbon\Carbon::now()->format('Y-m-d'), ['name'=>'date2', 'id'=>'date2', 'class'=>'inputform2']) !!}
                                    </div>

                                    <input id="id_partida" name="id_partida" type="hidden">
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            @if($business_numeration_entries->entries_numeration_mode != 'manual')
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" id="div_number">
                                <label for="code">@lang('accounting.number')</label>
                                <input type="text" name="number2" id="number2" class="form-control" readonly>
                            </div>
                            @else
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" id="div_number" style="display: none">
                                <label for="code">@lang('accounting.number')</label>
                                <input type="text" name="number2" id="number2" class="form-control" readonly>
                            </div>
                            @endif

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>@lang('accounting.type')</label>
                                <select name="etype_entrie_id" id="etype_entrie_id" class="form-control select2" style="width: 100%">


                                    <option value="0" selected>@lang('accounting.all')</option>
                                    @foreach($types as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach


                                </select>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>@lang('accounting.location')</label>

                                <select name="ebusiness_location_id" id="ebusiness_location_id" class="form-control select2" style="width: 100%;">
                                    <option value="0" selected disabled>@lang('messages.please_select')</option>
                                    @foreach($business_locations as $location)
                                    <option value="{{$location->id}}">{{$location->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="code">@lang('accounting.description')</label>
                                    <input class="form-control" name="description2" id="description2" type="text" placeholder="@lang('accounting.description')">
                                </div>
                            </div>
                        </div>
                        <div id="msj-errors4" class="alert alert-danger" role="alert" style="display: none;">
                            <button type="button" class="close" aria-label="Close" id="close-error-4">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong id="msj4"></strong>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-condensed table-hover" id="newData2">
                                <thead>
                                    <th style="width: 5%">
                                        @lang('accounting.options')
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
                                    <tr id="vacio2"><td colspan="5">@lang('accounting.no_data')</td></tr>
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
                            <div class="row">
                                <div id="content2" class="col-lg-12" style="display: none;">
                                    @lang('accounting.wait_please')...
                                    <img src="{{ asset('img/loader.gif') }}" alt="loading" />
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>


                </div>
            </div>
        </div>


        <div class="modal-footer">
            <input type="button" class="btn btn-success" value="@lang('messages.save')" id="registro_entrie2">
            <button type="button" class="btn btn-danger" data-dismiss="modal" id="undo">@lang('messages.close')</button>
        </div>



    </div>
</div>
</div>




</section>
<!-- /.content -->
@endsection
@section('javascript')
<script>

    var cont=0;
    total=0;
    id_a=[];
    valor=[];

    var cont2=0;
    total2=0;
    id_a2=[];
    valor2=[];

    $("#undo").click(function(){    
        limpiar2();
    });
    $("#subir2").click(function(){
        $('html, body').animate( {scrollTop: 0 }, 800);
    });

    $("#account").change(function(event) {
        id = $("#account").val()
        if (id != null) {
            if(id == -1) {
                $("#newData tbody tr").remove();
                $("#total_debe").val('');
                $("#total_haber").val('');
                total = 0;
                cont = 0;
                id_a.length = 0;
                valor.length = 0;

                total_debtors = 0.00;
                total_creditors = 0.00;

                date = $("#date").val();


                var route = "/entries/getResultCreditorAccounts/"+date;
                $.get(route, function(res){
                    $(res).each(function(key,value){
                        if((value.balance != 0.00) && (value.balance != null)) {
                            id_c = value.id;
                            code = value.code;
                            name = value.name;
                            balance = value.balance;
                            total_creditors = parseFloat(total_creditors) + parseFloat(value.balance);
                            id_a.push(id_c);
                            valor.push(cont);
                            var fila='<tr class="selected" id="fila'+cont+'" style="height: 10px"><td style="width: 5%"><button id="bitem'+cont+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar('+cont+', '+id_c+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id[]" value="'+id_c+'">'+code+'</td><td style="width: 50%">'+name+'</td><td style="width: 15%"><input type="text" name="debe[]" onchange="deshabilitar1('+cont+')" id="debe'+cont+'" value="'+balance+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01"></td><td style="width: 15%"><input type="text" name="haber[]" onchange="deshabilitar2('+cont+')" id="haber'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" value="0.00" readonly></td></tr><tr id="fila_description'+cont+'"><td colspan="5"><input type="text" name="description_line[]" id="description_line'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
                            $("#lista").append(fila);
                            cont++;
                        }
                    });

                    var route = "/entries/getResultDebtorAccounts/"+date;
                    $.get(route, function(res){
                        $(res).each(function(key,value){
                            if((value.balance != 0.00) && (value.balance != null)) {
                                id_c = value.id;
                                code = value.code;
                                name = value.name;
                                balance = value.balance;
                                total_debtors = parseFloat(total_debtors) + parseFloat(value.balance);
                                id_a.push(id_c);
                                valor.push(cont);
                                var fila='<tr class="selected" id="fila'+cont+'" style="height: 10px"><td style="width: 5%"><button id="bitem'+cont+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar('+cont+', '+id_c+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id[]" value="'+id_c+'">'+code+'</td><td style="width: 50%">'+name+'</td><td style="width: 15%"><input type="text" name="debe[]" onchange="deshabilitar1('+cont+')" id="debe'+cont+'" value="0.00" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" readonly></td><td style="width: 15%"><input type="text" name="haber[]" onchange="deshabilitar2('+cont+')" id="haber'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" value="'+balance+'"></td></tr><tr id="fila_description'+cont+'"><td colspan="5"><input type="text" name="description_line[]" id="description_line'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
                                $("#lista").append(fila);
                                cont++;
                            }
                        });

                        var route = "/entries/getProfitAndLossAccount";
                        $.get(route, function(res){
                            id_c = res.id;
                            code = res.code;
                            name = res.name;
                            balance = parseFloat(total_creditors - total_debtors);

                            if (balance.toFixed(2) != 0.00) {
                                id_a.push(id_c);
                                valor.push(cont);
                                var fila='<tr class="selected" id="fila'+cont+'" style="height: 10px"><td style="width: 5%"><button id="bitem'+cont+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar('+cont+', '+id_c+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id[]" value="'+id_c+'">'+code+'</td><td style="width: 50%">'+name+'</td><td style="width: 15%"><input type="text" name="debe[]" onchange="deshabilitar1('+cont+')" id="debe'+cont+'" value="0.00" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" readonly></td><td style="width: 15%"><input type="text" name="haber[]" onchange="deshabilitar2('+cont+')" id="haber'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" value="'+balance.toFixed(2)+'"></td></tr><tr id="fila_description'+cont+'"><td colspan="5"><input type="text" name="description_line[]" id="description_line'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
                                $("#lista").append(fila);
                                cont++;
                            }
                            $("#button_save").show();
                            $("#pie").show();
                            calcular();
                        });
                    });
                });
} else if (id == -2) {

    $("#newData tbody tr").remove();

    $("#total_debe").val('');
    $("#total_haber").val('');

    $("#difference_debit").val('');
    $("#difference_credit").val('');

    total = 0;
    cont = 0;
    id_a.length = 0;
    valor.length = 0;

    total_debtors = 0.00;
    total_creditors = 0.00;

    date = $("#date").val();


    var route = "/entries/getApertureDebitAccounts/"+date;
    $.get(route, function(res){
        $(res).each(function(key,value){
            if((value.balance != 0.00) && (value.balance != null)) {
                id_c = value.id;
                code = value.code;
                name = value.name;
                balance = value.balance;
                total_creditors = parseFloat(total_creditors) + parseFloat(value.balance);
                id_a.push(id_c);
                valor.push(cont);
                var fila='<tr class="selected" id="fila'+cont+'" style="height: 10px"><td style="width: 5%"><button id="bitem'+cont+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar('+cont+', '+id_c+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id[]" value="'+id_c+'">'+code+'</td><td style="width: 50%">'+name+'</td><td style="width: 15%"><input type="text" name="debe[]" onchange="deshabilitar1('+cont+')" id="debe'+cont+'" value="'+balance+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01"></td><td style="width: 15%"><input type="text" name="haber[]" onchange="deshabilitar2('+cont+')" id="haber'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" value="0.00" readonly></td></tr><tr id="fila_description'+cont+'"><td colspan="5"><input type="text" name="description_line[]" id="description_line'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
                $("#lista").append(fila);
                cont++;
            }
        });

        var route = "/entries/getApertureCreditAccounts/"+date;
        $.get(route, function(res){
            $(res).each(function(key,value){
                if((value.balance != 0.00) && (value.balance != null)) {
                    id_c = value.id;
                    code = value.code;
                    name = value.name;
                    balance = value.balance;
                    total_debtors = parseFloat(total_debtors) + parseFloat(value.balance);
                    id_a.push(id_c);
                    valor.push(cont);
                    var fila='<tr class="selected" id="fila'+cont+'" style="height: 10px"><td style="width: 5%"><button id="bitem'+cont+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar('+cont+', '+id_c+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id[]" value="'+id_c+'">'+code+'</td><td style="width: 50%">'+name+'</td><td style="width: 15%"><input type="text" name="debe[]" onchange="deshabilitar1('+cont+')" id="debe'+cont+'" value="0.00" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" readonly></td><td style="width: 15%"><input type="text" name="haber[]" onchange="deshabilitar2('+cont+')" id="haber'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" value="'+balance+'"></td></tr><tr id="fila_description'+cont+'"><td colspan="5"><input type="text" name="description_line[]" id="description_line'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
                    $("#lista").append(fila);
                    cont++;
                }
                $("#button_save").show();
                $("#pie").show();
                calcular();
            });
        });
    });

} else {

    agregar();
}
}
$("#account").val('');
$("#account").val(0);
});

$("#account2").change(function(event) {
    id2 = $("#account2").val()
    agregar2();
    $("#account2").val('');
    $("#account2").val(0);
});


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
function eliminar(index, id_p){
    $("#fila" + index).remove();
    $("#fila_description" + index).remove();
    id_a.removeItem(id_p);
    if(id_a.length == 0)
    {
        var fila_vacia = "<tr id='vacio'><td colspan='5'>No Hay datos</td></tr>";
        $("#lista").append(fila_vacia);
        $("#button_save").hide();
        $("#pie").hide();
    }
    calcular();
}
function eliminar2(index2, id_p2){  
    $("#fila2" + index2).remove();
    $("#fila_description2" + index2).remove();
    id_a2.removeItem(id_p2);
    if(id_a2.length == 0)
    {
        var fila_vacia2 = "<tr id='vacio2'><td colspan='5'>No Hay datos</td></tr>";
        $("#lista2").append(fila_vacia2);
        $("#button_save2").hide();
        $("#pie2").hide();
    }
    calcular2();
}

$("#registro_entrie").click(function(){
    $('#registro_entrie').prop("disabled", true);
    $('#subir2').prop("disabled", true);
    $.each(valor, function(value){
        $("#bitem"+value+"").prop("disabled", true);
    });
    var datastring = $("#form_entrie").serialize();
    var route = "/entries";
    var token = $("#token").val();

    $.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: datastring,
        success:function(result){
            if(result.success == true){
                getNumber();
                Swal.fire
                ({
                    title: ""+result.msg+"",
                    icon: "success",
                });
                $('#registro_entrie').prop("disabled", false);
                $('#subir2').prop("disabled", false);
                $.each(valor, function(value){
                    $("#bitem"+value+"").prop("disabled", false);
                });
                clean();
                $("#entriesData").DataTable().ajax.reload();
            }
            else{
                $('#registro_entrie').prop("disabled", false);
                $('#subir2').prop("disabled", false);
                $.each(valor, function(value){
                    $("#bitem"+value+"").prop("disabled", false);
                });
                Swal.fire
                ({
                    title: ""+result.msg+"",
                    icon: "error",
                });
            }


        },
        error:function(msj){
            var errormessages = "";
            $.each(msj.responseJSON.errors, function(i, field){
                errormessages+="<li>"+field+"</li>";
            });
            $('#msj').html( "<ul>"+ errormessages+ "</ul>" );
            $("#number").focus();
            $('#msj-errors').fadeIn();
        }
    });
});
$('#close-error').click(function()
{
    $('#msj-errors').fadeOut();
    $('#registro_entrie').prop("disabled", false);
    $('#subir2').prop("disabled", false);
    $.each(valor, function(value){
        $("#bitem"+value+"").prop("disabled", false);
    });
});
$("#registro_entrie2").click(function(){
    $('#registro_entrie2').prop("disabled", true);
    $('#undo').prop("disabled", true);
    $.each(valor2, function(value2){
        $("#bitem2"+value2+"").prop("disabled", true);
    });
    var datastring = $("#form_entrie2").serialize();
    var route = "/entries/editEntrie";
    var token = $("#token").val();

    $.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: datastring,
        success:function(result){
            if (result.success == true) {
                $("#flag-edit-entrie").val(0);
                $('#registro_entrie2').val('Modificar')
                $('#registro_entrie2').prop("disabled", false);
                $('#undo').prop("disabled", false);
                limpiar2();
                $("#entriesData").DataTable().ajax.reload();
                $("#modal-edit-entrie").modal('hide');

                Swal.fire
                ({
                    title: result.msg,
                    icon: "success",
                });

            }
            else
            {
                $('#undo').prop("disabled", false);
                Swal.fire
                ({
                    title: result.msg,
                    icon: "error",
                });
            }
        },
        error:function(msj){
            var errormessages = "";
            $.each(msj.responseJSON.errors, function(i, field){
                errormessages+="<li>"+field+"</li>";
            });
            $('#msj4').html("<ul>"+ errormessages+ "</ul>");
            $("#number2").focus();
            $('#msj-errors4').fadeIn();
        }
    });
});
$('#close-error-4').click(function()
{
    $('#msj-errors4').fadeOut();
    $('#registro_entrie2').prop("disabled", false);
    $('#undo').prop("disabled", false);
    $.each(valor2, function(value2){
        $("#bitem2"+value2+"").prop("disabled", false);
    });
});
function clean()
{
    $("#description").val('');  
    $("#button_save").hide();   
    $("#pie").hide();
    $("#newData tbody tr").remove(); 
    var fila_vacia = "<tr id='vacio'><td colspan='5'>No Hay datos</td></tr>";
    $("#lista").append(fila_vacia);
    $("#total_debe").val('');
    $("#total_haber").val('');
    total = 0;
    cont = 0;
    id_a.length = 0;
    valor.length = 0;
}
function limpiar2()
{
    $("#date2").val('');
    $("#number2").val('');
    $("#id_partida").val('');
    $("#description2").val(''); 
    $("#button_save2").hide();  
    $("#pie2").hide();
    $("#newData2 tbody tr").remove(); 
    var fila_vacia2 = "<tr id='vacio2'><td colspan='5'>No Hay datos</td></tr>";
    $("#lista2").append(fila_vacia2);
    $("#total_debe2").val('');
    $("#total_haber2").val('');
    $("#flag-edit-entrie").val(0);
    total2 = 0;
    cont2 = 0;
    id_a2.length = 0;
    valor2.length = 0;
}
function deshabilitar1(cont)
{
    if($("#debe"+cont+"").val()!="")
    {
        $("#haber"+cont+"").val('0');
        $("#haber"+cont+"").prop('readonly', true);     
    }
    else
    {
        $("#haber"+cont+"").prop('readonly', false);
        $("#haber"+cont+"").val('');
    }   
    calcular();
}
function deshabilitar2(cont)
{
    if($("#haber"+cont+"").val()!="")
    {
        $("#debe"+cont+"").val('0');
        $("#debe"+cont+"").prop('readonly', true);
    }
    else
    {
        $("#debe"+cont+"").prop('readonly', false);
        $("#debe"+cont+"").val('');
    }
    calcular();
}
function deshabilitar12(cont2)
{
    if($("#debe2"+cont2+"").val()!="")
    {
        $("#haber2"+cont2+"").val('0');
        $("#haber2"+cont2+"").prop('readonly', true);       
    }
    else
    {
        $("#haber2"+cont2+"").prop('readonly', false);
        $("#haber2"+cont2+"").val('');
    }   
    calcular2();
}
function deshabilitar22(cont2)
{
    if($("#haber2"+cont2+"").val()!="")
    {
        $("#debe2"+cont2+"").val('0');
        $("#debe2"+cont2+"").prop('readonly', true);
    }
    else
    {
        $("#debe2"+cont2+"").prop('readonly', false);
        $("#debe2"+cont2+"").val('');
    }
    calcular2();
}
function calcular()
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
function calcular2()
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
    // Backspace = 8, Enter = 13, ‘0′ = 48, ‘9′ = 57, ‘.’ = 46, ‘-’ = 43
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
function mostrarPartida($id)
{
    $('#tr-validate').hide();
    tablaDatos = $("#detallePartida");
    $("#detallePartida").empty();
    var route = "/entries/"+$id;
    var route2 = "/entries/getDetails/"+$id;    
    $.get(route, function(res){
        $('#numeroPartida').text(' '+res.correlative);
        $('#fecha').text(' '+res.date);
        $('#descripcion').text(' '+res.description);
        if(res.status == 0) {
            html = '<button id="btn-approve" type="button" class="btn btn-success" onClick="changeStatus('+res.id+', '+res.status+', '+res.number+', \''+ res.date + '\')">@lang('accounting.approve')</button>';
            //$('#lbl-validate').html(html);
        }
        else {
            html = ' ';
        }
        
        $('#lbl-validate').html(html);
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
                tablaDatos.append("<tr><td>"+value.code+"</td><td>"+value.name+"</td><td></td><td></td><td>$</td><td style = 'text-align: right;'>"+value.credit+"</td><tr><td colspan='6'>"+description+"</td></tr></tr>");
                total_haber = parseFloat(total_haber) + parseFloat(value.credit)
            }
        });
        $("#tdebe").text(total_debe.toFixed(2));
        $("#thaber").text(total_haber.toFixed(2));

        $("#total_debit").val(total_debe.toFixed(2));
        $("#total_credit").val(total_haber.toFixed(2));
    });
}

$("#editar_entrie").click(function(){
    $(this).prop("disabled", true);
    var id_e = $("#id-e").val();
    var number_e = $("#number-e").val();
    var date_e = $("#date-e").val();
    var description_e = $("#description-e").val();
    var route = "/entries/"+id_e;
    var token = $("#token").val();

    $.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'PUT',
        dataType: 'json',
        data: {
            date: date_e,
            number: number_e,
            description: description_e
        },
        success:function(){
            $('#msj-errors-e').fadeOut();
            $("#msj-success-e").fadeIn();
            $("#entriesData").DataTable().ajax.reload();
            setTimeout(function()
            {
                $('#msj-success-e').fadeOut();
                $('#editar_entrie').val('@lang("messages.save")')
                $('#editar_entrie').prop("disabled", false);
                $('#number-e-e').focus();
                $('#eentrie').modal('hide');
            },
            1000);
        },
        error:function(msj){
            var errormessages = "";
            $.each(msj.responseJSON.errors, function(i, field){
                errormessages+="<li>"+field+"</li>";
            });
            $('#msj-e').html( "<ul>"+ errormessages+ "</ul>" );
            $('#msj-success-e').fadeOut();
            $('#msj-errors-e').fadeIn();
        }
    });
});
$("#close-error-e").click(function(){
    $('#msj-errors-e').fadeOut();
    $('#editar_entrie').val('@lang("messages.save")')
    $('#editar_entrie').prop("disabled", false);
});
$("#close-success-e").click(function(){
    $('#msj-success-e').fadeOut();
    $('#editar_entrie').val('@lang("messages.save")')
    $('#editar_entrie').prop("disabled", false);
});
$("#close-edit").click(function(){
    $('#msj-success-e').fadeOut();
    $('#msj-errors-e').fadeOut();
    $('#editar_entrie').prop("disabled", false);
    $('#editar_entrie').val('@lang("messages.save")')
});


function loadEntriesData() {

    var table = $("#entriesData").DataTable();
    table.destroy();
    var table = $("#entriesData").DataTable(
    {
        order: [[ 0, "desc" ]],
        columnDefs: [{ "visible": false, "targets": [1, 2, 5, 6, 7] }],
        processing: true,
        deferRender: true,
        serverSide: true,
        ajax: "/entries/getEntries/"+$("#filter-type-entrie-id").val()+"/"+$("#filter-business-location-id").val()+"/"+$("#filter-period").val()+"",
        columns: [
        {data: 'date', name: 'entrie.date'},
        {data: 'number', name: 'entrie.number'},
        {data: 'correlative', name: 'entrie.correlative'},
        {data: 'short_name', name: 'entrie.short_name'},
        {data: 'description', name: 'entrie.description'},
        {data: 'period_name', name: 'period.name'},
        {data: 'name_type', name: 'type.name'},
        {data: 'name_location', name: 'location.name'},
        {data: null, render: function(data) {

            if (data.status == 1) {

                return "@lang('accounting.approved')";
            } else {
                return "@lang('accounting.pending')";
            }} , orderable: false, searchable: false },

            {data: null, render: function(data) {

                actions = '';

                actions += '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span> </button> <ul class="dropdown-menu dropdown-menu-right" role="menu"><li><a href="#" data-toggle="modal" data-target="#verPartida" OnClick="mostrarPartida('+ data.id +');"><i class="fa fa-eye"></i>@lang('messages.view')</a></li><li><a href="#" OnClick="printEntriePdf('+ data.id +');"><i class="fa fa-file-pdf-o"></i>Pdf</a></li><li><a href="#" OnClick="printEntrieExcel('+ data.id +');"><i class="fa fa-file-excel-o"></i>Excel</a></li><li><a href="#" OnClick="searchPeriod('+ data.id +');"><i class="fa fa-files-o"></i>@lang('messages.clone')</a></li>';

                actions_edit = '<li><a href="#" OnClick="editarPartida('+ data.id +', '+data.accounting_period_id+');"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a></li>';

                actions_delete = '<li><a href="#" OnClick="eliminarPartida('+ data.id +', '+data.accounting_period_id+');"><i class="fa fa-trash"></i>@lang('messages.delete')</a></li>';

                if (data.status == 0) {

                    actions += actions_edit + actions_delete;

                } else {

                    enable_edit = $("#edition_in_approved_entries").val();
                    enable_delete = $("#deletion_in_approved_entries").val();

                    if(enable_edit == 1) {

                        actions += actions_edit;
                        
                    }

                    if(enable_delete == 1) {

                        actions += actions_delete;

                    }
                    

                }

                actions += '</ul></div>';

                return actions;
            } , orderable: false, searchable: false}
            ]
        });


    $('#entriesData').on( 'dblclick', 'tr', function () {
        var data = table.row(this).data();
        if (typeof data.id != "undefined"){

            if(data.status == 0) {

                editarPartida(data.id, data.accounting_period_id);
            } else {

                enable_edit = $("#edition_in_approved_entries").val();
                if(enable_edit == 1) {
                    editarPartida(data.id, data.accounting_period_id);
                }
            }        
        }
    });
}

function searchPeriod(id) {

    var route = "/entries/search-period";
    $.get(route, function(result) {
        if(result.success == false) {

            swal({
                title: '{{ __('accounting.period_not_found') }}',
                text: '{{ __('accounting.add_period') }}',
                icon: "warning",
                buttons: true,
                dangerMode: true,
                buttons: ["{{ __('messages.cancel') }}", "{{ __('messages.accept') }}"],
            }).then((willDelete) => {

                if (willDelete) {
                    route = '/entries/create-period';

                    $.get(route, function(result) {
                        if (result.success == true) {

                            $("#period_id").empty();
                            $("#period_id2").empty();
                            var route = "/entries/get-periods";
                            $.get(route, function(res) {

                                $("#period_id").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
                                $("#period_id2").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
                                
                                $(res).each(function(key,value){
                                    $("#period_id").append('<option value="'+value.id+'">'+value.name+'</option>');
                                    $("#period_id2").append('<option value="'+value.id+'">'+value.name+'</option>');
                                });

                                cloneEntrie(id);
                            });

                            
                        } else {
                            Swal.fire
                            ({
                                title: '{{__('accounting.success_false')}}',
                                icon: "error",
                            });
                        }
                    });

                }
            });


        } else {
            cloneEntrie(id);
        }
    });
}

function cloneEntrie(id) {

    route = '/entries/clone-entrie/'+id;
    total_debit = 0;
    total_credit = 0;
    $.get(route, function(result) {
        if (result.success == true) {

            clean();
            $("#tab_new_entrie").click();
            $("#description").val(result.data.description);
            $("#period_id").val(result.data.accounting_period_id).change();
            $("#date").val(result.date);
            $("#type_entrie_id").val(result.data.type_entrie_id).change();
            $("#business_location_id").val(result.data.business_location_id).change();

            $.each(result.data.details, function(key, value) {

                id_c = value.id;
                code = value.code;
                name = value.name;
                description = value.description;
                debit = parseFloat(value.debit).toFixed(2);
                credit = parseFloat(value.credit).toFixed(2);
                total_debit += parseFloat(value.debit);
                total_credit += parseFloat(value.credit);
                id_a.push(id_c);
                valor.push(cont);
                $("#vacio").remove();

                fila = '';

                fila += '<tr class="selected" id="fila'+cont+'" style="height: 10px"><td style="width: 5%"><button id="bitem'+cont+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar('+cont+', '+id_c+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id[]" value="'+id_c+'">'+code+'</td><td style="width: 50%">'+name+'</td>';

                if(debit > 0 || debit < 0) {


                    fila += '<td style="width: 15%"><input type="text" name="debe[]" onchange="deshabilitar1('+cont+')" id="debe'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" value="'+ debit +'"></td><td style="width: 15%"><input type="text" name="haber[]" onchange="deshabilitar2('+cont+')" id="haber'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" value="0" readonly></td>';
                } else  {


                    fila += '<td style="width: 15%"><input type="text" name="debe[]" onchange="deshabilitar1('+cont+')" id="debe'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" value="0" readonly></td><td style="width: 15%"><input type="text" name="haber[]" onchange="deshabilitar2('+cont+')" id="haber'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" value="'+ credit +'"></td>';

                }

                if (description == null) {

                    fila += '</tr><tr id="fila_description'+cont+'"><td colspan="5"><input type="text" name="description_line[]" id="description_line'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" value="" placeholder="{{ __('accounting.description') }}"></td></tr>';

                } else {

                    fila += '</tr><tr id="fila_description'+cont+'"><td colspan="5"><input type="text" name="description_line[]" id="description_line'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" value="'+ description +'" placeholder="{{ __('accounting.description') }}"></td></tr>';

                }

                $("#lista").append(fila);
                cont++;
            });

            $('#total_debe').val(total_debit.toFixed(2));
            $('#total_haber').val(total_credit.toFixed(2));

            $("#button_save").show();
            $("#pie").show();

        } else {
            Swal.fire
            ({
                title: '{{__('accounting.success_false')}}',
                icon: "error",
            });
        }
    });
}

function loadTypesData()
{
    var table = $("#types-table").DataTable();
    table.destroy();
    var table = $("#types-table").DataTable(
    {
        processing: true,
        serverSide: true,
        deferRender: true,
        ajax: "/type-entries/getTypesData",
        columns: [
        {data: 'name', name: 'type.name'},
        {data: 'description', name: 'type.description'},
        {data: 'short_name', name: 'type.short_name'},
        {data: null, render: function(data){
            edit_button = '<a class="btn btn-xs btn-primary" onClick="editType('+data.id+')"><i class="glyphicon glyphicon-edit"></i>@lang('messages.edit')</a>';
            delete_button = ' <a class="btn btn-xs btn-danger" onClick="deleteType('+data.id+')"><i class="glyphicon glyphicon-trash"></i>@lang('messages.delete')</a>';
            return edit_button + delete_button;
        } , orderable: false, searchable: false}
        ]
    });
}

$(document).ready(function()
{
    loadEntriesData();
    loadTypesData();
    $.fn.dataTable.ext.errMode = 'none';
    getNumber();
    updateSelects();
});
function getNumber()
{
    date = $("#date").val();
    var route = "/entries/getNumberEntrie/"+date;
    $.get(route, function(res){
        $("#number").val(res.number);
    });

}
$("#date").change(function(){

    id = $("#period_id").val();
    dat = $("#date").val();
    if (id != null) {
        var route = "/bank-transactions/validateDate/"+id+"/"+dat;
        $.get(route, function(result){
            if (result.success == false) {
                Swal.fire
                ({
                    title: result.msg,
                    icon: "error",
                });
                $("#date").val('');
                $("#period_id").val(0).change();
            }
            else {
                getNumber();
            }
        });
    }
    else {
        Swal.fire
        ({
            title: "{{__('accounting.select_period')}}",
            icon: "error",
        });
        $("#date").val('');
        $("#period_id").val(0).change();
    }
});

$("#date2").change(function(){

    id = $("#period_id2").val();
    dat = $("#date2").val();
    if (id != null) {
        var route = "/bank-transactions/validateDate/"+id+"/"+dat;
        $.get(route, function(result){
            if (result.success == false) {
                Swal.fire
                ({
                    title: result.msg,
                    icon: "error",
                });
                $("#date2").val('');
                $("#period_id2").val(0).change();
            }
        });
    }
    else {
        Swal.fire
        ({
            title: "{{__('accounting.select_period')}}",
            icon: "error",
        });
        $("#date2").val('');
        $("#period_id2").val(0).change();
    }
});

$("#filter-period").change(function(){
    loadEntriesData();
});
$("#filter-type-entrie-id").change(function(){
    loadEntriesData();
});
$("#filter-business-location-id").change(function(){
    loadEntriesData();
});

function eliminarPartida(id, period_id) {
    var route = "/accounting-periods/getPeriodStatus/"+period_id;
    $.get(route, function(result){
        if(result == 0){
            msj = "{{__('accounting.period_is_closed')}}";
            Swal.fire
            ({
                title: msj,
                icon: "error",
            });
        } else {
            swal({
                title: LANG.sure,
                text: '{{__('messages.delete_content')}}',
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete){
                    route = '/entries/'+id;
                    token = $("#token").val();
                    $.ajax({
                        url: route,
                        headers: {'X-CSRF-TOKEN': token},
                        type: 'DELETE',
                        dataType: 'json',                       
                        success:function(result){
                            if(result.success == true){
                                getNumber();
                                Swal.fire
                                ({
                                    title: result.msg,
                                    icon: "success",
                                });
                                $("#entriesData").DataTable().ajax.reload(null, false);
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
    });
}

function editarPartida(id, pediod_id)
{
    var route = "/accounting-periods/getPeriodStatus/"+pediod_id;
    $.get(route, function(result) {
        if (result == 0) {
            msj = "{{__('accounting.period_is_closed')}}";
            Swal.fire({
                title: msj,
                icon: "error",
            });

        } else {
            limpiar2();

            $('#modal-edit-entrie').find('.select2').each(function () {
                $(this).select2({
                    dropdownParent: $('#modal-edit-entrie')
                });
            });

            var route = "/entries/searchBankTransaction/"+id;
            $.get(route, function(result) {
                if (result.count >= 1) {
                    $('#content4').show();
                    $("#btn-edit-transaction").prop('disabled', true);
                    $("#btn-close-modal-edit-transaction").prop('disabled', true);
                    var route = "/bank-transactions/"+result.id;
                    $.get(route, function(transaction) {
                        $("#transaction_id").val(transaction.id);
                        $("#txt-ereference-transaction").val(transaction.reference);
                        $("#txt-edate-transaction").val(transaction.date);
                        $("#txt-eamount-transaction").val(transaction.amount);
                        $("#txt-edescription-transaction").val(transaction.description);

                        $("#eselect-type-transaction").val(transaction.type_bank_transaction_id).change();
                        $("#eselect-bank-account-id-transaction").val(transaction.bank_account_id).change();
                        $("#eperiod_id").val(transaction.period_id).change();
                        $("#eebusiness_location_id").val(transaction.business_location_id).change();

                        accounting_account = transaction.accounting_account;

                        id = transaction.type_bank_transaction_id;

                        var route = "/type-bank-transactions/"+id;
                        $.get(route, function(type) {
                            $("#ediv_accounts").show();
                            if (type.type == "credit") {
                                $("#elabel-type").text("{{__('accounting.account_to_debit')}}");
                            } else {
                                $("#elabel-type").text("{{__('accounting.account_to_credit')}}");
                            }

                            if(type.enable_checkbook == 1) {

                                $("#eselect-type-transaction").prop('disabled', true);
                                $("#eselect-bank-account-id-transaction").prop('disabled', true);
                                $("#eselect-checkbook-transaction").prop('disabled', true);
                                $("#txt-echeck-number-transaction").prop('disabled', true);

                                $("#txt-echeck-number-transaction").val(transaction.check_number);
                                $("#hidden-echeck-number-transaction").val(transaction.check_number);
                                
                                $("#ediv_reference").hide();
                                $("#txt-ereference-transaction").val('0');

                                $("#ediv_checkbook").show();
                                account_id = $("#eselect-bank-account-id-transaction").val();
                                $("#eselect-checkbook-transaction").empty();
                                var route = "/bank-checkbooks/getBankCheckbooks/"+account_id;
                                $.get(route, function(checkbook) {
                                    $("#eselect-checkbook-transaction").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
                                    $(checkbook).each(function(key,value){
                                        $("#eselect-checkbook-transaction").append('<option value="'+value.id+'">'+value.name+'</option>');
                                    });
                                    $("#eselect-checkbook-transaction").val(transaction.bank_checkbook_id).change();
                                });

                            } else {
                                $("#hidden-echeck-number-transaction").val('');
                                $("#ediv_reference").show();
                                $("#ediv_checkbook").hide();
                            }

                            if (type.enable_headline == 1) {
                                $("#ediv_contact").show();
                                $("#txt-epayment-to").val(transaction.headline);
                                
                            } else {
                                $("#ediv_contact").hide();
                                $("#txt-echeck-number-transaction").val('0');
                            }
                        });

                        entrie_id = transaction.accounting_entrie_id;
                        $("#pie3").show();
                        var route2 = "/entries/getTotalEntrie/"+entrie_id;
                        $.get(route2, function(res2) {
                            $('#total_debe3').val(res2.debe);
                            $('#total_haber3').val(res2.haber);
                        });
                        editBankTransactionPartial();
                        $("#div_lista").hide();
                        $("#div-edit-transaction").show();
                    });
                } else {
                    $('#modal-edit-entrie').modal({backdrop: 'static', keyboard: false});
                    $('#content2').show();
                    $('#registro_entrie2').prop("disabled", true);
                    $('#undo').prop("disabled", true);
                    var data = id;
                    $('#date2').val('');
                    $('#number2').val('');
                    $("#id_partida").val('');
                    $('#description2').val('');
                    $("#button_save2").show();
                    $("#pie2").show();
                    var route = "/entries/"+data+"/edit";
                    var route2 = "/entries/getTotalEntrie/"+data;
                    $.get(route, function(res) {
                        $('#date2').val(res.date);
                        $('#number2').val(res.number);
                        $('#description2').val(res.description);
                        $('#period_id2').val(res.accounting_period_id).change();
                        $('#number2').val(res.number);
                        $("#id_partida").val(res.id);
                        $("#etype_entrie_id").val(res.type_entrie_id).change();
                        $("#ebusiness_location_id").val(res.business_location_id).change();
                    });
                    $.get(route2, function(res2) {
                        $('#total_debe2').val(res2.debe);
                        $('#total_haber2').val(res2.haber);
                    });
                    var route3 = "/entries/getEntrieDetailsDebe/"+data;
                    $.get(route3, function(res3) {
                        $(res3).each(function(key,value) {
                            id_c2 = value.account_id;
                            code2 = value.code;
                            name2 = value.name;
                            if (value.description != null) {
                                description2 = value.description;
                            } else {
                                description2 = "";
                            }
                            existe2 = parseInt(jQuery.inArray(id_c2, id_a2));
                            id_a2.push(id_c2);
                            valor2.push(cont2);
                            $("#vacio2").remove();
                            // $("#newData2 tbody tr").remove();
                            if(value.debit > 0 || value.debit < 0) {
                                var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar2('+cont2+', '+id_c2+');" disabled>X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" onchange="deshabilitar12('+cont2+')" id="debe2'+cont2+'" value="'+value.debit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01"></td><td style="width: 15%"><input type="text" name="haber2[]" onchange="deshabilitar22('+cont2+')" id="haber2'+cont2+'" value="0" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" readonly></td></tr><tr id="fila_description2'+cont2+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}" value="'+description2+'"></td></tr>';
                            } else {
                                var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar2('+cont2+', '+id_c2+');" disabled>X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" onchange="deshabilitar12('+cont2+')" id="debe2'+cont2+'" value="0" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01" readonly></td><td style="width: 15%"><input type="text" name="haber2[]" onchange="deshabilitar22('+cont2+')" id="haber2'+cont2+'" value="'+value.credit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control"></td></tr><tr id="fila_description2'+cont2+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}" value="'+description2+'"></td></tr>'
                            }
                            $("#lista2").append(fila2);
                            cont2++;
                        });
                        var route4 = "/entries/getEntrieDetailsHaber/"+data;
                        $.get(route4, function(res4) {
                            $(res4).each(function(key,value) {
                                id_c2 = value.account_id;
                                code2 = value.code;
                                name2 = value.name;
                                if (value.description != null) {
                                    description2 = value.description;
                                } else {
                                    description2 = "";
                                }
                                existe2 = parseInt(jQuery.inArray(id_c2, id_a2));
                                id_a2.push(id_c2);
                                valor2.push(cont2);
                                if (value.debe > 0 || value.debe < 0) {
                                    var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar2('+cont2+', '+id_c2+');" disabled>X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" onchange="deshabilitar12('+cont2+')" id="debe2'+cont2+'" value="'+value.debit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01"></td><td style="width: 15%"><input type="text" name="haber2[]" onchange="deshabilitar22('+cont2+')" id="haber2'+cont2+'" value="0" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" readonly></td></tr><tr id="fila_description2'+cont2+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}" value="'+description2+'"></td></tr>'
                                } else {
                                    var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar2('+cont2+', '+id_c2+');" disabled>X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" onchange="deshabilitar12('+cont2+')" id="debe2'+cont2+'" value="0" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01" readonly></td><td style="width: 15%"><input type="text" name="haber2[]" onchange="deshabilitar22('+cont2+')" id="haber2'+cont2+'" value="'+value.credit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control"></td></tr><tr id="fila_description2'+cont2+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}" value="'+description2+'"></td></tr>'
                                }
                                $("#lista2").append(fila2);
                                $("#button_save2").show()
                                $("#pie2").show()
                                cont2++;
                            });
                            //$('html, body').animate( {scrollTop: $("#modal-edit-entrie").height() }, 1000);
                            $('#registro_entrie2').prop("disabled", false);
                            $('#undo').prop("disabled", false);
                            $.each(valor2, function(value2) {
                                $("#bitem2"+value2+"").prop("disabled", false);
                            });
                            $('#flag-edit-entrie').val(1);
                            $('#content2').hide();
                        });
                    });
                }
            });
        }
    });
}

$("#btn-new-type").click(function(){
    $('#txt-name-type').val('');
    $('#txt-description-type').val('');
    setTimeout(function()
    {               
        $('#txt-name-type').focus();
    },
    800);
});

$("#btn-add-type").click(function(){
    $("#btn-add-type").prop("disabled", true);
    $("#btn-close-modal-add-type").prop("disabled", true);  
    name = $("#txt-name-type").val();
    description = $("#txt-description-type").val();
    short_name = $("#txt-short_name-type").val();
    route = "/type-entries";
    token = $("#token").val();
    $.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {
            name: name,
            description: description,
            short_name: short_name
        },
        success:function(){
            updateSelects();
            $("#btn-add-type").prop("disabled", false);
            $("#btn-close-modal-add-type").prop("disabled", false); 
            $("#types-table").DataTable().ajax.reload(null, false);
            Swal.fire
            ({
                title: "{{__('accounting.type_added')}}",
                icon: "success",
            });
            $("#modal-add-type").modal('hide');
        },
        error:function(msj){
            $("#btn-add-type").prop("disabled", false);
            $("#btn-close-modal-add-type").prop("disabled", false);
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

$("#btn-edit-type").click(function(){
    $("#btn-edit-type").prop("disabled", true);
    $("#btn-close-modal-edit-type").prop("disabled", true);
    id = $("#type_id").val();
    name = $("#txt-name-etype").val();
    description = $("#txt-description-etype").val();
    short_name = $("#txt-short_name-etype").val();
    route = "/type-entries/"+id;
    token = $("#token").val();
    $.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'PUT',
        dataType: 'json',
        data: {
            name: name,
            description: description,
            short_name: short_name
        },
        success:function(){
            updateSelects();
            $("#btn-edit-type").prop("disabled", false);
            $("#btn-close-modal-edit-type").prop("disabled", false);
            $("#types-table").DataTable().ajax.reload(null, false);
            Swal.fire
            ({
                title: "{{__('accounting.type_updated')}}",
                icon: "success",
            });
            $('#modal-edit-type').modal('hide');
        },
        error:function(msj){
            $("#btn-edit-type").prop("disabled", false);
            $("#btn-close-modal-edit-type").prop("disabled", false);
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

function editType(id)
{
    $('#type_id').val('');
    $('#txt-name-etype').val('');
    $('#txt-description-etype').val('');
    $('#txt-short_name-etype').val('');
    var route = "/type-entries/"+id+"/edit";
    $.get(route, function(res){
        $('#type_id').val(res.id);
        $('#txt-name-etype').val(res.name);
        $('#txt-description-etype').val(res.description);
        $('#txt-short_name-etype').val(res.short_name);
    });
    $('#modal-edit-type').modal({backdrop: 'static', keyboard: false});
}

function deleteType(id)
{
    swal({
        title: LANG.sure,
        text: '{{__('messages.delete_content')}}',
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete){
            route = '/type-entries/'+id;
            token = $("#token").val();
            $.ajax({                    
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'DELETE',
                dataType: 'json',                       
                success:function(result){
                    if(result.success == true){
                        updateSelects();
                        Swal.fire
                        ({
                            title: result.msg,
                            icon: "success",
                        });
                        $("#types-table").DataTable().ajax.reload(null, false);
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

function updateSelects()
{
    $("#type_entrie_id").empty();
    $("#etype_entrie_id").empty();
    $("#filter-type-entrie-id").empty();
    var route = "/type-entries/getTypes";
    $.get(route, function(res){
        $("#type_entrie_id").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
        $("#etype_entrie_id").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
        $("#filter-type-entrie-id").append('<option value="0" selected>{{ __('accounting.all') }}</option>');
        $(res).each(function(key,value){
            $("#type_entrie_id").append('<option value="'+value.id+'">'+value.name+'</option>');
            $("#etype_entrie_id").append('<option value="'+value.id+'">'+value.name+'</option>');
            $("#filter-type-entrie-id").append('<option value="'+value.id+'">'+value.name+'</option>');
        });
    });
}

function agregar()
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
        var fila='<tr class="selected" id="fila'+cont+'" style="height: 10px"><td style="width: 5%"><button id="bitem'+cont+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar('+cont+', '+id_c+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id[]" value="'+id_c+'">'+code+'</td><td style="width: 50%">'+name+'</td><td style="width: 15%"><input type="text" name="debe[]" onchange="deshabilitar1('+cont+')" id="debe'+cont+'" value="" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01"></td><td style="width: 15%"><input type="text" name="haber[]" onchange="deshabilitar2('+cont+')" id="haber'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control"></td></tr><tr id="fila_description'+cont+'"><td colspan="5"><input type="text" name="description_line[]" id="description_line'+cont+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
        $("#lista").append(fila);
        $("#button_save").show();
        $("#pie").show();
        $("#debe"+cont+"").focus();
        cont++;
    });
}

function agregar2()
{
    var route2 = "/entries/search/"+id2;
    $.get(route2, function(res2){
        id_c2 = res2.id;
        code2 = res2.code;
        name2 = res2.name;
        parent2 = res2.parent;
        condition2 = res2.condition;
        existe2 = parseInt(jQuery.inArray(id_c2, id_a2));
        id_a2.push(id_c2);
        valor2.push(cont2);
        $("#vacio2").remove();
        var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar2('+cont2+', '+id_c2+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" onchange="deshabilitar12('+cont2+')" id="debe2'+cont2+'" value="" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01"></td><td style="width: 15%"><input type="text" name="haber2[]" onchange="deshabilitar22('+cont2+')" id="haber2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control"></td></tr><tr id="fila_description2'+cont2+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
        $("#lista2").append(fila2);
        $("#button_save2").show()
        $("#pie2").show()
        $("#debe2"+cont2+"").focus();
        cont2++;
    });
}

function changeStatus(id, status, number, date)
{
    total_debit = parseFloat($("#total_debit").val()); 
    total_credit = parseFloat($("#total_credit").val());

    if (total_debit == total_credit) {
        numeration_mode = $("#numeration-mode").val();
        if (numeration_mode == 'manual') {
            if (status == 0) {
                $("#txt-number-entrie").val('');
                $("#entrie_id").val(id);
                $('#tr-validate').show();
                $('#txt-number-entrie').focus();
            }
            else
            {
                var route = "/entries/changeStatus/"+id+"/0";
                $.get(route, function(res){
                    if (res.success == true) {
                        $("#entriesData").DataTable().ajax.reload(null, false);
                        $('#tr-validate').hide();
                        $('#verPartida').modal('hide');
                        html = ' ';
                        $('#lbl-validate').html(html);
                        Swal.fire
                        ({
                            title: res.msg,
                            icon: "success",
                        });
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
        }
        else {
            $("#btn-approve").prop('disabled', true);
            $("#close-show").prop('disabled', true);
            var route_number = "/entries/getCorrelativeEntrie/"+date;
            $.get(route_number, function(num){

                if (status == 0) {
                    var route = "/entries/changeStatus/"+id+"/"+num.number;
                    $.get(route, function(res){
                        if (res.success == true) {
                            $("#entriesData").DataTable().ajax.reload(null, false);
                            $('#tr-validate').hide();
                            $('#verPartida').modal('hide');
                            html = ' ';
                            $('#lbl-validate').html(html);
                            $("#btn-approve").prop('disabled', false);
                            $("#close-show").prop('disabled', false);
                            Swal.fire
                            ({
                                title: res.msg,
                                icon: "success",
                            });
                        }
                        else {
                            $("#btn-approve").prop('disabled', false);
                            $("#close-show").prop('disabled', false);
                            Swal.fire
                            ({
                                title: res.msg,
                                icon: "error",
                            });
                        }
                    });
                }
                else
                {
                    var route = "/entries/changeStatus/"+id+"/"+num.number;
                    $.get(route, function(res){
                        if (res.success == true) {
                            $("#entriesData").DataTable().ajax.reload(null, false);
                            $('#tr-validate').hide();
                            $('#verPartida').modal('hide');
                            html = ' ';
                            $('#lbl-validate').html(html);
                            $("#btn-approve").prop('disabled', false);
                            $("#close-show").prop('disabled', false);
                            Swal.fire
                            ({
                                title: res.msg,
                                icon: "success",
                            });
                        }
                        else {
                            $("#btn-approve").prop('disabled', false);
                            $("#close-show").prop('disabled', false);
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
    }
    else {
        $("#btn-approve").prop('disabled', false);
        $("#close-show").prop('disabled', false);
        Swal.fire
        ({
            title: '@lang("accounting.entrie_incorrect")',
            icon: "error",
        });

    }
    
}

$("#btn-add-number").click(function(){
    id = $("#entrie_id").val();
    number = $("#txt-number-entrie").val();
    if ((number != "") && $.isNumeric(number)) {
        var route = "/entries/changeStatus/"+id+"/"+number;
        $.get(route, function(res){
            if (res.success == true) {
                $("#entriesData").DataTable().ajax.reload(null, false);
                $('#tr-validate').hide();
                $('#verPartida').modal('hide');
                html = ' ';
                $('#lbl-validate').html(html);
                Swal.fire
                ({
                    title: res.msg,
                    icon: "success",
                });
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
    else {
        Swal.fire
        ({
            title: '@lang('accounting.invalid_number')',
            icon: "error",
        });
    }
});

$("#tab_new_entrie").click(function(){
    getNumber();
});

$("#period_id").change(function(){
    id = $("#period_id").val();

    if (id != null) {

        var route = "/bank-transactions/getDateByPeriod/"+id;
        $.get(route, function(res){
            $("#date").val(res.date);
            date = $("#date").val();
            var route = "/entries/getNumberEntrie/"+date;
            $.get(route, function(res){
                $("#number").val(res.number);
            });
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


$("#period_id2").change(function(){
    id = $("#period_id2").val();
    flag = $("#flag-edit-entrie").val();

    if ((id != null) && (flag == 1)){

        var route = "/bank-transactions/getDateByPeriod/"+id;
        $.get(route, function(res){
            $("#date2").val(res.date);
        });
    }
    
});

function editBankTransactionPartial()
{
    var route3 = "/entries/getEntrieDetails/"+entrie_id;
    $.get(route3, function(res3){
        $(res3).each(function(key,value)
        {
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
            $("#vacio3").remove();

            if (accounting_account == value.account_id) {
                if(value.debit > 0.00 || value.debit < 0.00) {
                    var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="deleteLineInEditView('+cont2+', '+id_c2+');" disabled>X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="'+value.debit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control cost" step="0.01" onchange="disableCreditInEditView('+cont2+')" readonly></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" value="'+value.credit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" onchange="disableDebitInEditView('+cont2+')" readonly></td></tr><tr id="fila_description2'+cont2+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}" value="'+description2+'"></td></tr>';
                    $("#lista3").append(fila2);
                    cont2++;
                }
                if(value.credit > 0.00 || value.credit < 0.00) {
                    var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="deleteLineInEditView('+cont2+', '+id_c2+');" disabled>X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="'+value.debit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01" onchange="disableCreditInEditView('+cont2+')" readonly></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" value="'+value.credit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control cost" onchange="disableDebitInEditView('+cont2+')" readonly></td></tr><tr id="fila_description2'+cont2+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}" value="'+description2+'"></td></tr>';
                    $("#lista3").append(fila2);
                    cont2++;
                }
            }
            else {
                var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="deleteLineInEditView('+cont2+', '+id_c2+');" disabled>X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="'+value.debit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01" onchange="disableCreditInEditView('+cont2+')"></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" value="'+value.credit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" onchange="disableDebitInEditView('+cont2+')"></td></tr><tr id="fila_description2'+cont2+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}" value="'+description2+'"></td></tr>';
                $("#lista3").append(fila2);
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
        $.each(valor2, function(value2){
            $("#bitem2"+value2+"").prop("disabled", false);
        });
        $('#content4').hide();
        $("#flag-edit").val(1);
        $("#pie3").show()
    });
}

$("#btn-close-modal-edit-transaction").click(function(){
    $("#flag-edit").val(0);
    $("#newData3 tbody tr").remove(); 
    var fila_vacia2 = "<tr id='vacio3'><td colspan='5'>@lang('accounting.no_data')</td></tr>";
    $("#lista3").append(fila_vacia2);
    $("#total_debe3").val('');
    $("#total_haber3").val('');
    total2 = 0;
    cont2 = 0;
    id_a2.length = 0;
    valor2.length = 0;
    $("#div-edit-transaction").hide();
    $("#div_lista").show();

    $("#eselect-type-transaction").prop('disabled', false);
    $("#eselect-bank-account-id-transaction").prop('disabled', false);
    $("#eselect-checkbook-transaction").prop('disabled', false);
    $("#txt-echeck-number-transaction").prop('disabled', false);

    clean2();

    
});

$("#eselect-type-transaction").change(function(event){
    flag = $("#flag-edit").val();
    if (flag == 1) {
        value = $("#eselect-type-transaction").val();
        if (value != null) {
            echangeType();
            $("#newData3 tbody tr").remove();
            var fila_vacia2 = "<tr id='vacio3'><td colspan='5'>@lang('accounting.no_data')</td></tr>";
            $("#lista3").append(fila_vacia2);
            $("#total_debe3").val('');
            $("#total_haber3").val('');
            total2 = 0;
            cont2 = 0;
            id_a2.length = 0;
            valor2.length = 0;
        }
    }
});

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
                $("#txt-ereference-transaction").val("");
            }
            if(res.enable_headline == 1) {
                $("#txt-echeck-number-transaction").val('');
                $("#ediv_contact").show();
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

$("#txt-eamount-transaction").change(function(){
    amount = parseFloat($("#txt-eamount-transaction").val());
    $(".cost").val(amount.toFixed(2));
    calculate3();
});

$("#eselect-bank-account-id-transaction").change(function(event){
    flag = $("#flag-edit").val();
    if (flag == 1) {
        value = $("#eselect-bank-account-id-transaction").val();
        if (value != null) {
            eupdateSelectsBankCheckbooks();
            text_account = $("#eselect-bank-account-id-transaction option:selected").text();
            $("#newData3 tbody tr").remove();
            $("#total_debe3").val('');
            $("#total_haber3").val('');

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
                    $("#vacio3").remove();
                    type_id = $("#eselect-type-transaction").val();
                    var route = "/type-bank-transactions/"+type_id+"/edit";
                    $.get(route, function(res){
                        if(res.type == "debit") {
                         var fila2 = '<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="deleteLineInEditView('+cont2+', '+id_c2+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+text_account+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control cost" step="0.01" onchange="disableCreditInEditView('+cont2+')" readonly></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" onchange="disableDebitInEditView('+cont2+')" readonly></td></tr><tr id="fila_description2'+cont2+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
                         $("#lista3").append(fila2);
                         $("#pie3").show();
                         $("#txt-eamount-transaction").val('');
                         cont2++;

                     }
                     else {
                       var fila2 = '<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="deleteLineInEditView('+cont2+', '+id_c2+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+text_account+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01" onchange="disableCreditInEditView('+cont2+')" readonly></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control cost" onchange="disableDebitInEditView('+cont2+')" readonly></td></tr><tr id="fila_description2'+cont+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
                       $("#lista3").append(fila2);
                       $("#pie3").show();
                       $("#txt-eamount-transaction").val('');
                       cont2++;

                   }
               });
                    $("#eselect-checkbook-transaction").val('0').change();
                    //$("#txt-ereference-transaction").val('');
                    $("#txt-eamount-transaction").val('');
                    $("#txt-epayment-to").val('');
                    $("#txt-eamount-transaction").prop('readonly', false);
                    //$("#txt-edescription-transaction").val('');
                });

            });
        }
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

$("#eselect-carrier-transaction").change(function(){
    value = $("#eselect-carrier-transaction").val();
    if (value != null) {
        carrier = $("#eselect-carrier-transaction option:selected").text();
        $("#txt-epayment-to").val(carrier);
        $("#select-carrier-transaction").val(0);
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
    $('#eebusiness_location_id').val('0').change();

    $("#newData3 tbody tr").remove();
    $("#total_debe3").val('');
    $("#total_haber3").val('');
    total2 = 0;
    cont2 = 0;
    id_a2.length = 0;
    valor2.length = 0;

    $('#ediv_contact').hide();
    $('#ediv_checkbook').hide();
    $('#ediv_accounts').hide();
    $('#ediv_reference').show();
}

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
        $("#vacio3").remove();


        type_id = $("#eselect-type-transaction").val();
        var route = "/type-bank-transactions/"+type_id+"/edit";
        $.get(route, function(res){
            if(res.type == "debit") {
                var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="deleteLineInEditView('+cont2+', '+id_c2+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01" onchange="disableCreditInEditView('+cont2+')"></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" onchange="disableDebitInEditView('+cont2+')"></td></tr><tr id="fila_description2'+cont2+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
                $("#lista3").append(fila2);
                $("#pie3").show();
                $("#haber2"+cont2+"").focus();
                cont2++;
            }
            else {
               var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="deleteLineInEditView('+cont2+', '+id_c2+');">X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" step="0.01" onchange="disableCreditInEditView('+cont2+')"></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" onchange="disableDebitInEditView('+cont2+')"></td></tr><tr id="fila_description2'+cont2+'"><td colspan="5"><input type="text" name="description_line2[]" id="description_line2'+cont2+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control" placeholder="{{ __('accounting.description') }}"></td></tr>';
               $("#lista3").append(fila2);
               $("#pie3").show();
               $("#debe2"+cont2+"").focus();
               cont2++;
           }
       });
    });
}

function deleteLineInEditView(index2, id_p2){  
    $("#fila2" + index2).remove();
    $("#fila_description2" + index2).remove();
    id_a2.removeItem(id_p2);
    if(id_a2.length == 0)
    {
        var fila_vacia2 = "<tr id='vacio3'><td colspan='5'>@lang('accounting.no_data')</td></tr>";
        $("#lista3").append(fila_vacia2);
        $("#pie3").hide();
    }
    calculate3();
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
    calculate3();
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
    calculate3();
}

function calculate3()
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
    $("#total_debe3").val(total_debe2.toFixed(2));
    $("#total_haber3").val(total_haber2.toFixed(2));
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
                $("#entriesData").DataTable().ajax.reload(null, false);
                Swal.fire
                ({
                    title: ""+result.msg+"",
                    icon: "success",
                });
                $("#div-edit-transaction").hide();
                clean2();
                $("#div_lista").show();
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

                $("#eselect-type-transaction").prop('disabled', false);
                $("#eselect-bank-account-id-transaction").prop('disabled', false);
                $("#eselect-checkbook-transaction").prop('disabled', false);
                $("#txt-echeck-number-transaction").prop('disabled', false);
            }
        },
        error:function(msj){
            $("#btn-edit-transaction").prop("disabled", false);
            $("#btn-close-modal-edit-transaction").prop("disabled", false);

            $("#eselect-type-transaction").prop('disabled', false);
            $("#eselect-bank-account-id-transaction").prop('disabled', false);
            $("#eselect-checkbook-transaction").prop('disabled', false);
            $("#txt-echeck-number-transaction").prop('disabled', false);

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
    }
    else {
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

$("#tab_list").click(function(){
    $("#thead-entries").show();
});

$("#tab_new_entrie").click(function(){
    $("#thead-entries").hide();
});

$("#tab_report").click(function(){
    $("#thead-entries").hide();
});

$("#tab_type").click(function(){
    $("#thead-entries").hide();
});

function printEntriePdf(id)
{

    var url = '{!!URL::to('/entries/singleEntrie/:id/:type')!!}';
    url = url.replace(':id', id);
    url = url.replace(':type', 'pdf');
    window.open(url, '_blank');
}
function printEntrieExcel(id)
{

    var url = '{!!URL::to('/entries/singleEntrie/:id/:type')!!}';
    url = url.replace(':id', id);
    url = url.replace(':type', 'excel');
    window.open(url, '_blank');
}

$("#btn-clean-new-entrie").click(function(){
    clean();
});


$(document).bind('keydown', 'Shift+1', function(){
    description = $("#description_2").val();
    flag = $("#flag-edit-entrie")
    $("#description").val(description);
    
});
$(document).bind('keydown', 'Shift+2', function(){
    description = $("#description_3").val();
    flag = $("#flag-edit-entrie")
    $("#description").val(description);
    
});
$(document).bind('keydown', 'Shift+3', function(){
    description = $("#description_4").val();
    flag = $("#flag-edit-entrie")
    $("#description").val(description);
    
});
$(document).bind('keydown', 'Shift+4', function(){
    description = $("#description_5").val();
    flag = $("#flag-edit-entrie")
    $("#description").val(description);
    
});
$(document).bind('keydown', 'Shift+5', function(){
    description = $("#description_6").val();
    flag = $("#flag-edit-entrie")
    $("#description").val(description);
    
});
$(document).bind('keydown', 'Shift+6', function(){
    description = $("#description_7").val();
    flag = $("#flag-edit-entrie")
    $("#description").val(description);
    
});
$(document).bind('keydown', 'Shift+7', function(){
    description = $("#description_8").val();
    flag = $("#flag-edit-entrie")
    $("#description").val(description);
    
});
$(document).bind('keydown', 'Shift+8', function(){
    description = $("#description_9").val();
    flag = $("#flag-edit-entrie")
    $("#description").val(description);
    
});
$(document).bind('keydown', 'Shift+9', function(){
    description = $("#description_10").val();
    flag = $("#flag-edit-entrie")
    $("#description").val(description);
    
});

$("#close-show").click(function(){
    html = ' ';
    $('#lbl-validate').html(html);
});



$('#btn_renumeration').click(function() {

    $('#btn_renumeration').prop('disabled', true);
    period = $('#period').val();

    if (period != '') {

        mode = $('#numeration_mode').val();
        period = $('#period').val();


        var route = "/entries/setNumeration/"+mode+"/"+period;
        $.get(route, function(result){
            if (result.success == true) {
                $('#btn_renumeration').prop('disabled', false);
                Swal.fire({
                    title: result.msg,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                })
                $("#entriesData").DataTable().ajax.reload();
            } else {
                $('#btn_renumeration').prop('disabled', false);
                Swal.fire({
                    title: result.msg,
                    icon: 'error',
                })
            }

        });

    }
});




</script>
@endsection
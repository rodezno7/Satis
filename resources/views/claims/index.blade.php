@extends('layouts.app')
@section('title', __( 'crm.claims' ))
<style>
    .dot {
      height: 25px;
      width: 25px;
      background-color: #bbb;
      border-radius: 50%;
      display: inline-block;
  }
</style>
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('crm.claims' )</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="panel with-nav-tabs panel-default boxform_u box-solid_u">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-claims" data-toggle="tab" id="link_claims">@lang('crm.claims')</a></li>
                <li><a href="#tab-types" data-toggle="tab" id="link_types">@lang('crm.claim_types')</a></li>
                <li><a href="#tab-status" data-toggle="tab" id="link_status">@lang('crm.status_claims')</a></li>
            </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade in active" id="tab-claims">
                    <div class="boxform_u box-solid_u">

                        <div class="box-header">
                            <h3 class="box-title">@lang( 'crm.all_your_claims' )</h3>
                            @can('claim.create')
                            <div class="box-tools">
                                <button type="button" class="btn btn-primary" data-toggle='modal' data-target='#modal-add-claim' data-backdrop="static" id="btn-new-claim"><i class="fa fa-plus"></i> @lang( 'messages.add' )
                                </button>
                            </div>
                            @endcan
                        </div>
                        <div class="box-body">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                @can('claim.view')
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-condensed table-hover" id="claims-table" width="100%">
                                        <thead id="thead-claims">
                                            <th>@lang('crm.correlative')</th>
                                            <th>@lang('crm.type')</th>
                                            
                                            <th>@lang('crm.status')</th>
                                            
                                            <th>@lang('crm.close_date')</th>
                                            <th>@lang('crm.suggested_closing_date')</th>
                                            <th>@lang('crm.responsable')</th>
                                            
                                            <th>@lang( 'messages.actions' )</th>
                                        </thead>
                                    </table>
                                </div>
                                @endcan
                            </div>
                        </div>
                    </div>

                </div>

                <div class="tab-pane fade" id="tab-types">
                    <div class="boxform_u box-solid_u">
                        <div class="box-header">
                            <h3 class="box-title">@lang( 'crm.all_your_claim_types' )</h3>
                            @can('claim_type.create')

                            <div class="box-tools">
                                <button type="button" class="btn btn-primary" data-toggle='modal' data-target='#modal-add-type' data-backdrop="static" id="btn-new-type"><i class="fa fa-plus"></i> @lang( 'messages.add' )
                                </button>
                            </div>
                            @endcan
                        </div>
                        <div class="box-body">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                @can('claim_type.view')
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-condensed table-hover" id="types-table" width="100%">
                                        <thead>
                                            <th>@lang('crm.correlative')</th>
                                            <th>@lang('crm.name')</th>
                                            <th>@lang('crm.description')</th>
                                            <th>@lang('crm.resolution_time')</th>
                                            <th>@lang('crm.required_customer')</th>
                                            <th>@lang('crm.required_product')</th>
                                            <th>@lang('crm.required_invoice')</th>
                                            <th>@lang( 'messages.actions' )</th>
                                        </thead>
                                    </table>
                                </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-status">
                    <div class="boxform_u box-solid_u">
                        <div class="box-header">
                            <h3 class="box-title">@lang( 'crm.all_your_status_claims' )</h3>
                            @can('claim_status.create')
                            {{--
                            <div class="box-tools">
                                <button type="button" class="btn btn-primary" data-toggle='modal' data-target='#modal-add-status' data-backdrop="static" id="btn-new-status"><i class="fa fa-plus"></i> @lang( 'messages.add' )
                                </button>
                            </div>
                            --}}
                            @endcan
                        </div>
                        <div class="box-body">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    @can('claim_status.create')
                                    <table class="table table-striped table-bordered table-condensed table-hover" id="status-table" width="100%">
                                        <thead>
                                            <th>@lang('crm.correlative')</th>
                                            <th>@lang('crm.name')</th>
                                            <th>@lang('crm.status')</th>
                                            <th>@lang('crm.predecessor')</th>
                                            <th>@lang('crm.color')</th>
                                            <th>@lang('messages.actions')</th>
                                        </thead>
                                    </table>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('claim_type.create')
    <div tabindex="-1" class="modal fade" id="modal-add-type" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 40%">
            <form id="form-add-type">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                <div class="modal-content" style="border-radius: 20px;">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3>@lang('crm.add_claim_type')</h3>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.correlative')</label>
                                    <input type="text" name="txt-correlative-type" id="txt-correlative-type" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.name')</label>
                                    <input type="text" name="txt-name-type" id="txt-name-type" class="form-control" placeholder="@lang('crm.name')">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.description')</label>
                                    <input type="text" name="txt-description-type" id="txt-description-type" class="form-control" placeholder="@lang('crm.description')">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.resolution_time')</label>
                                    <input type="number" name="txt-resolution-time-type" id="txt-resolution-time-type" class="form-control input_number" placeholder="@lang('crm.resolution_time')" step="1">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.required_customer')</label>
                                    {!! Form::checkbox('required_customer', '1', false, ['id' => 'required_customer']); !!}
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.required_product')</label>
                                    {!! Form::checkbox('required_product', '1', false, ['id' => 'required_product']); !!}
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.required_invoice')</label>
                                    {!! Form::checkbox('required_invoice', '1', false, ['id' => 'required_invoice']); !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.all_access_users')</label>
                                    {!! Form::checkbox('all_access_users', '1', false, ['id' => 'all_access_users', 'onClick' => 'showUsers()']); !!}
                                </div>
                            </div>
                        </div>

                        <div class="row" id="div_users" style="display: none;">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.users')</label>
                                    <select name="users" id="users" class="select2" style="width: 100%">
                                        <option value="0">@lang('messages.please_select')</option>
                                        @foreach($users as $item)
                                        <option value="{{ $item->id }}">{{ $item->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <th style="width: 10%;">Op</th>
                                            <th style="width: 90%;">@lang('crm.user')</th>
                                        </thead>
                                        <tbody id="list">
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-type">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-add-type">@lang('messages.close')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endcan

    @can('claim_type.update')
    <div tabindex="-1" class="modal fade" id="modal-edit-type" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 40%">
            <form id="form-edit-type">
                <div class="modal-content" style="border-radius: 20px;">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3>@lang('crm.edit_claim_type')</h3>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label>@lang('crm.correlative')</label>
                                        <input type="text" name="txt-ecorrelative-type" id="txt-ecorrelative-type" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label>@lang('crm.name')</label>
                                        <input type="text" name="txt-ename-type" id="txt-ename-type" class="form-control" placeholder="@lang('crm.name')">
                                        <input type="hidden" name="type_id" id="type_id">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label>@lang('crm.description')</label>
                                        <input type="text" name="txt-edescription-type" id="txt-edescription-type" class="form-control" placeholder="@lang('crm.description')">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.resolution_time')</label>
                                    <input type="number" name="txt-eresolution-time-type" id="txt-eresolution-time-type" class="form-control input_number" placeholder="@lang('crm.resolution_time')" step="1">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.required_customer')</label>
                                    {!! Form::checkbox('erequired_customer', '1', false, ['id' => 'erequired_customer']); !!}
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.required_product')</label>
                                    {!! Form::checkbox('erequired_product', '1', false, ['id' => 'erequired_product']); !!}
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.required_invoice')</label>
                                    {!! Form::checkbox('erequired_invoice', '1', false, ['id' => 'erequired_invoice']); !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.all_access_users')</label>
                                    {!! Form::checkbox('eall_access_users', '1', false, ['id' => 'eall_access_users', 'onClick' => 'eshowUsers()']); !!}
                                </div>
                            </div>
                        </div>

                        <div class="row" id="ediv_users" style="display: none;">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.users')</label>
                                    <select name="eusers" id="eusers" class="select2" style="width: 100%">
                                        <option value="0">@lang('messages.please_select')</option>
                                        @foreach($users as $item)
                                        <option value="{{ $item->id }}">{{ $item->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <th style="width: 10%;">Op</th>
                                            <th style="width: 90%;">@lang('crm.user')</th>
                                        </thead>
                                        <tbody id="elist">
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>


                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-edit-type">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-type">@lang('messages.close')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endcan

    @can('claim.create')
    <div tabindex="-1" class="modal fade" id="modal-add-claim" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 60%">
            <form id="form-add-claim">
                <div class="modal-content" style="border-radius: 20px;">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3>@lang('crm.add_claim')</h3>
                        <div class="row">

                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <label>@lang('crm.correlative')</label>
                                <input type="text" name="txt-correlative-claim" id="txt-correlative-claim" class="form-control" readonly>
                            </div>


                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <label>@lang('crm.type')</label>
                                <select name="select-type-claim" id="select-type-claim" class="select2" style="width: 100%" required>
                                    <option value="0" disabled selected>@lang('messages.please_select')</option>
                                    @foreach($types as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row" style="display: none;">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" id="div_status">
                                <div class="form-group">
                                    <label>@lang('crm.status')</label>
                                    <select name="select-status-claim" id="select-status-claim" class="select2" style="width: 100%">
                                        <option value="0" selected disabled>@lang('messages.please_select')</option>
                                        @foreach($status_claims as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.claim_date')</label>

                                    <div class="wrap-inputform">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="date" id="txt-claim-date-claim" name="txt-claim-date-claim" class="inputform2" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                <div class="form-group">
                                    <label>@lang('crm.equipment_reception')</label>
                                    {!! Form::checkbox('equipment_reception', '1', false, ['id' => 'equipment_reception', 'onClick' => 'showEquipment()']); !!}
                                </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="div_equipment" style="display: none;">
                                <div class="form-group">
                                    <textarea name="txt-equipment-reception-desc-claim" id="txt-equipment-reception-desc-claim" class="form-control">

                                    </textarea>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" id="div_customer" style="display: none;">
                                <div class="form-group">
                                    <label>@lang('crm.customer')</label>
                                    <select name="select-customer-id-claim" id="select-customer-id-claim" class="select2" style="width: 100%;">
                                        <option value="0" disabled selected>@lang('messages.please_select')</option>
                                        @foreach($customers as $item)

                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" id="div_product" style="display: none;">
                                <div class="form-group">
                                    <label>@lang('crm.product')</label>
                                    <select name="select-product-id-claim" id="select-product-id-claim" class="select2" style="width: 100%;">
                                        <option value="0" disabled selected>@lang('messages.please_select')</option>
                                        @foreach($products as $item)
                                        @if($item->sku != $item->sub_sku)
                                        <option value="{{ $item->id }}">{{ $item->name_product }} {{ $item->name_variation }}</option>
                                        @else
                                        <option value="{{ $item->id }}">{{ $item->name_product }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" id="div_invoice" style="display: none;">
                                <div class="form-group">
                                    <label>@lang('crm.invoice')</label>
                                    <input type="text" name="txt-invoice-claim" id="txt-invoice-claim" class="form-control input_number" placeholder="@lang('crm.invoice')">
                                </div>
                            </div>



                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label>@lang('crm.description')</label>
                                <textarea name="txt-description-claim" id="txt-description-claim" class="form-control" required>
                                </textarea>
                            </div>
                        </div>

                        <div class="row" style="display: none;">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" id="div_review">
                                <label>@lang('crm.review')</label>
                                <textarea name="txt-review-description-claim" id="txt-review-description-claim" class="form-control">
                                </textarea>
                            </div>
                        </div>

                        <div class="row" style="display: none;">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" id="div_proceed">
                                <label>{{ __('crm.proceed')}}</label> {!! Form::checkbox('chk_proceed', '1', false, ['id' => 'chk_proceed', 'onClick' => 'showResolution()']); !!}
                                <div id="div_resolution" style="display: none;">
                                    <label>@lang('crm.resolution')</label>
                                    <textarea name="txt-resolution-claim" id="txt-resolution-claim" class="form-control">
                                    </textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="display: none;">
                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12" id="div_close_date">
                                <label>@lang('crm.close_date')</label>

                                <div class="wrap-inputform">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <input type="date" id="txt-close-date-claim" name="txt-close-date-claim" class="inputform2" value="">
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.suggested_closing_date')</label>

                                    <div class="wrap-inputform">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="date" id="txt-suggested-date-claim" name="txt-suggested-date-claim" class="inputform2" readonly>
                                    </div>

                                </div>
                            </div>


                        </div>

                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-claim">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-add-claim">@lang('messages.close')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endcan

    @can('claim.update')
    <div tabindex="-1" class="modal fade" id="modal-edit-claim" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 60%">
            <form id="form-edit-claim">
                <div class="modal-content" style="border-radius: 20px;">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3>@lang('crm.edit_claim')</h3>

                        <div class="row">

                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <label>@lang('crm.correlative')</label>
                                <input type="text" name="txt-ecorrelative-claim" id="txt-ecorrelative-claim" class="form-control" readonly>
                                <input type="hidden" name="claim_id" id="claim_id">
                                <input type="hidden" name="status_open_id" id="status_open_id">
                                <input type="hidden" name="user_id" id="user_id" value="{{ $user_id }}">
                                <input type="hidden" name="is_default" id="is_default" value="{{ $is_default }}">
                            </div>


                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <label>@lang('crm.type')</label>
                                <select name="select-etype-claim" id="select-etype-claim" class="select2" style="width: 100%" disabled>
                                    <option value="0" disabled selected>@lang('messages.please_select')</option>
                                    @foreach($types as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row" style="display: none;">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" id="ediv_status">
                                <div class="form-group">
                                    <label>@lang('crm.status')</label>
                                    <select name="select-estatus-claim" id="select-estatus-claim" class="select2" style="width: 100%">
                                        <option value="0" selected disabled>@lang('messages.please_select')</option>
                                        @foreach($status_claims_follow as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.claim_date')</label>

                                    <div class="wrap-inputform">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="date" id="txt-eclaim-date-claim" name="txt-eclaim-date-claim" class="inputform2" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                <div class="form-group">
                                    <label>@lang('crm.equipment_reception')</label>
                                    {!! Form::checkbox('eequipment_reception', '1', false, ['id' => 'eequipment_reception', 'onClick' => 'eshowEquipment()']); !!}
                                </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="ediv_equipment" style="display: none;">
                                <div class="form-group">
                                    <textarea name="txt-eequipment-reception-desc-claim" id="txt-eequipment-reception-desc-claim" class="form-control">

                                    </textarea>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" id="ediv_customer" style="display: none;">
                                <div class="form-group">
                                    <label>@lang('crm.customer')</label>
                                    <select name="select-ecustomer-id-claim" id="select-ecustomer-id-claim" class="select2" style="width: 100%;">
                                        <option value="0" disabled selected>@lang('messages.please_select')</option>
                                        @foreach($customers as $item)

                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" id="ediv_product" style="display: none;">
                                <div class="form-group">
                                    <label>@lang('crm.product')</label>
                                    <select name="select-eproduct-id-claim" id="select-eproduct-id-claim" class="select2" style="width: 100%;">
                                        <option value="0" disabled selected>@lang('messages.please_select')</option>
                                        @foreach($products as $item)
                                        @if($item->sku != $item->sub_sku)
                                        <option value="{{ $item->id }}">{{ $item->name_product }} {{ $item->name_variation }}</option>
                                        @else
                                        <option value="{{ $item->id }}">{{ $item->name_product }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" id="ediv_invoice" style="display: none;">
                                <div class="form-group">
                                    <label>@lang('crm.invoice')</label>
                                    <input type="text" name="txt-einvoice-claim" id="txt-einvoice-claim" class="form-control input_number" placeholder="@lang('crm.invoice')">
                                </div>
                            </div>



                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label>@lang('crm.description')</label>
                                <textarea name="txt-edescription-claim" id="txt-edescription-claim" class="form-control" required>
                                </textarea>
                            </div>
                        </div>

                        <div class="row" id="div_chk_review">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label>{{ __('crm.review')}}</label>
                                {!! Form::checkbox('echk_review', '1', false, ['id' => 'echk_review', 'onClick' => 'eshowReview()']); !!}
                            </div>
                        </div>


                        <div class="row" id="ediv_review" style="display: none;">

                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label>@lang('crm.review')</label>
                                <textarea name="txt-ereview-description-claim" id="txt-ereview-description-claim" class="form-control">
                                </textarea>
                            </div>

                        </div>

                        <div class="row" id="ediv_proceed" style="display: none;">
                            <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12" id="div_proceed">
                                <label>{{ __('crm.proceed')}}</label>
                                {!! Form::checkbox('echk_proceed', '1', false, ['id' => 'echk_proceed', 'onClick' => 'eshowJustificationProceed()']); !!}
                                
                            </div>

                            <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12" id="div_not_proceed">
                                <label>{{ __('crm.not_proceed')}}</label>
                                {!! Form::checkbox('echk_not_proceed', '1', false, ['id' => 'echk_not_proceed', 'onClick' => 'eshowJustificationNotProceed()']); !!}
                                
                            </div>
                        </div>

                        <div class="row" id="ediv_justification" style="display: none;">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" id="">

                                <label>@lang('crm.justification')</label>
                                <textarea name="txt-ejustification-claim" id="txt-ejustification-claim" class="form-control">
                                </textarea>
                                
                            </div>
                        </div>


                        <div class="row" id="ediv_closed" style="display: none;">
                            <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12" id="div_proceed">
                                <label>{{ __('crm.close')}}</label>
                                {!! Form::checkbox('echk_close', '1', false, ['id' => 'echk_close', 'onClick' => 'eshowResolution()']); !!}
                                
                            </div>

                        </div>


                        <div class="row" id="ediv_resolution" style="display: none;">

                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" id="">

                                <label>@lang('crm.resolution')</label>
                                <textarea name="txt-eresolution-claim" id="txt-eresolution-claim" class="form-control">
                                </textarea>

                            </div>

                        </div>

                        <div class="row" style="display: none;">
                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12" id="div_close_date">
                                <label>@lang('crm.close_date')</label>

                                <div class="wrap-inputform">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <input type="date" id="txt-eclose-date-claim" name="txt-eclose-date-claim" class="inputform2" value="">
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.suggested_closing_date')</label>

                                    <div class="wrap-inputform">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="date" id="txt-esuggested-date-claim" name="txt-esuggested-date-claim" class="inputform2" readonly>
                                    </div>

                                </div>
                            </div>


                        </div>

                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-primary" value="@lang('messages.save')" id="btn-edit-claim">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-claim">@lang('messages.close')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endcan

    @can('claim_status.create')
    <div tabindex="-1" class="modal fade" id="modal-add-status" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 40%">
            <form id="form-add-status">
                <div class="modal-content" style="border-radius: 20px;">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3>@lang('crm.add_status_claim')</h3>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.correlative')</label>
                                    <input type="text" name="txt-correlative-status" id="txt-correlative-status" class="form-control" readonly>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.name')</label>
                                    <input type="text" name="txt-name-status" id="txt-name-status" class="form-control" @lang('crm.name')>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.predecessor')</label>
                                    <select name="predecessor" id="predecessor" class="select2" style="width: 100%">
                                        <option value="0" disabled selected>@lang('messages.please_select')</option>
                                        @foreach($status as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.color')</label>
                                    <input type="color" id="color" name="color" value="#00bb5e" class="form-control">
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.active')</label>
                                    {!! Form::checkbox('status', '1', false, ['id' => 'status']); !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-status">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-add-status">@lang('messages.close')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endcan

    @can('claim_status.update')
    <div tabindex="-1" class="modal fade" id="modal-edit-status" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="width: 40%">
            <form id="form-edit-status">
                <div class="modal-content" style="border-radius: 20px;">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3>@lang('crm.edit_status_claim')</h3>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.correlative')</label>
                                    <input type="text" name="txt-ecorrelative-status" id="txt-ecorrelative-status" class="form-control" readonly>
                                    <input type="hidden" name="status_id" id="status_id">
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.name')</label>
                                    <input type="text" name="txt-ename-status" id="txt-ename-status" class="form-control" @lang('crm.name')>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.predecessor')</label>
                                    <select name="epredecessor" id="epredecessor" class="select2" style="width: 100%" disabled>
                                        <option value="0" disabled selected>@lang('messages.please_select')</option>
                                        @foreach($status as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.color')</label>
                                    <input type="color" id="ecolor" name="ecolor" value="" class="form-control">
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('crm.active')</label>
                                    {!! Form::checkbox('estatus', '1', false, ['id' => 'estatus', 'disabled']); !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-primary" value="@lang('messages.save')" id="btn-edit-status">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-close-modal-edit-status">@lang('messages.close')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endcan



    <div tabindex="-1" class="modal fade claim_modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    </div>


</section>
<!-- /.content -->
@endsection
@section('javascript')
<script type="text/javascript" src="{{ asset('/plugins/picolormap.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function()
    {
        loadClaimsData();
        loadTypesData();
        loadStatusData();
        $.fn.dataTable.ext.errMode = 'none';
    });

    function loadTypesData()
    {
        var table = $("#types-table").DataTable(
        {
            pageLength: 25,
            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/claims/getClaimTypesData",
            columns: [
            {data: 'correlative'},
            {data: 'name'},
            {data: 'description'},
            {data: 'resolution'},
            {data: 'customer'},
            {data: 'product'},
            {data: 'invoice'},
            {data: 'actions', orderable: false, searchable: false}
            ],
            columnDefs: [{
                "targets": '_all',
                "className": "text-center"
            }]
        });
    }

    $("#btn-new-type").click(function(){
        $("#txt-correlative-type").val('');
        getTypeCorrelative();
        $("#txt-name-type").val('');
        $("#txt-description-type").val('');
        $("#txt-resolution-time-type").val('');
        $("#required_customer").prop('checked', false);
        $("#required_product").prop('checked', false);
        $("#required_invoice").prop('checked', false);
        $("#all_access_users").prop('checked', true);
        $("#div_users").hide();
        $("#list").empty();
        cont = 0;
        user_ids=[];
        rowCont=[];
        setTimeout(function(){
            $("#txt-name-type").focus();
        },
        800);
    });

    $("#btn-add-type").click(function() {
        $("#btn-add-type").prop("disabled", true);
        $("#btn-close-modal-add-type").prop("disabled", true);

        datastring = $("#form-add-type").serialize();

        route = "/claim-types";
        token = $("#token").val();
        $.ajax({
            url: route,
            type: 'POST',
            datatype: "json",
            headers: {'X-CSRF-TOKEN': token},
            data: datastring,
            success:function(result){
                if (result.success == true) {
                    updateSelectTypes();
                    $("#btn-add-type").prop("disabled", false);
                    $("#btn-close-modal-add-type").prop("disabled", false); 
                    $("#types-table").DataTable().ajax.reload(null, false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "success",
                        timer: 3000,
                        showConfirmButton: false,
                    });
                    $("#modal-add-type").modal('hide');
                }
                else
                {
                    $("#btn-add-type").prop("disabled", false);
                    $("#btn-close-modal-add-type").prop("disabled", false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "error",
                    });
                }

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

    function editType(id)
    {
        var route = "/claim-types/"+id+"/edit";
        $.get(route, function(res) {

            $("#elist").empty();
            econt = 0;
            euser_ids=[];
            erowCont=[];

            $('#type_id').val(res.id);
            $('#txt-ecorrelative-type').val(res.correlative);
            $('#txt-ename-type').val(res.name);            
            $('#txt-edescription-type').val(res.description);
            $('#txt-eresolution-time-type').val(res.resolution_time);

            if (res.required_customer == 1) {
                $("#erequired_customer").prop('checked', true);
            } else {
                $("#erequired_customer").prop('checked', false);
            }

            if (res.required_product == 1) {
                $("#erequired_product").prop('checked', true);
            } else {
                $("#erequired_product").prop('checked', false);
            }

            if (res.required_invoice == 1) {
                $("#erequired_invoice").prop('checked', true);
            } else {
                $("#erequired_invoice").prop('checked', false);
            }

            if(res.all_access == 1) {
                $("#eall_access_users").prop('checked', true);
                $("#ediv_users").hide();
            } else {
                $("#eall_access_users").prop('checked', false);

                var route = "/claim-types/getUsersByClaimType/"+id;
                $.get(route, function(res) {
                    $(res).each(function(key,value){
                        user_id = value.user_id;
                        name = value.full_name;


                        count = parseInt(jQuery.inArray(user_id, euser_ids));
                        if (count >= 0)
                        {
                            Swal.fire
                            ({
                                title: "{{__('crm.user_already_added')}}",
                                icon: "error",
                            });
                        }
                        else
                        {

                            euser_ids.push(user_id);
                            erowCont.push(cont);
                            var erow = '<tr class="selected" id="erow'+econt+'" style="height: 10px"><td><button id="ebitem'+econt+'" type="button" class="btn btn-danger btn-xs" onclick="edeleteUser('+econt+', '+user_id+');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="euser_id[]" value="'+user_id+'">'+name+'</td></tr>';
                            $("#elist").append(erow);
                            econt++;


                        }


                    });
                });

                $("#ediv_users").show();
            }

            $('#modal-edit-type').modal({backdrop: 'static'});
        });
    }

    $("#btn-edit-type").click(function(event) {
        $("#btn-edit-type").prop("disabled", true);
        $("#btn-close-modal-edit-type").prop("disabled", true);

        datastring = $("#form-edit-type").serialize();
        id = $("#type_id").val();

        route = "/claim-types/"+id;
        token = $("#token").val();
        $.ajax({
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: 'PUT',
            datatype: "json",
            data: datastring,
            success:function(result){
                if (result.success == true) {
                    updateSelectTypes();
                    $("#btn-edit-type").prop("disabled", false);
                    $("#btn-close-modal-edit-type").prop("disabled", false); 
                    $("#types-table").DataTable().ajax.reload(null, false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "success",
                        timer: 3000,
                        showConfirmButton: false,
                    });
                    $("#modal-edit-type").modal('hide');
                }
                else
                {
                    $("#btn-edit-type").prop("disabled", false);
                    $("#btn-close-modal-edit-type").prop("disabled", false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "error",
                    });
                }

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

    function deleteType(id)
    {
        Swal.fire({
            title: LANG.sure,
            text: '{{__('messages.delete_content')}}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{__('messages.accept')}}",
            cancelButtonText: "{{__('messages.cancel')}}"
        }).then((willDelete) => {
            if (willDelete.value) {
                route = '/claim-types/'+id;
                token = $("#token").val();
                $.ajax({                    
                    url: route,
                    headers: {'X-CSRF-TOKEN': token},
                    type: 'DELETE',
                    dataType: 'json',                       
                    success:function(result){
                        if(result.success == true){
                            updateSelectTypes();
                            Swal.fire
                            ({
                                title: result.msg,
                                icon: "success",
                                timer: 3000,
                                showConfirmButton: false,
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

    function updateSelectTypes(){
        $("#select-type-claim").empty();
        $("#select-etype-claim").empty();
        var route = "/claims/getClaimTypes";
        $.get(route, function(res){
            $("#select-type-claim").append('<option value="0" disabled selected>{{__('messages.please_select')}}</option>');
            $("#select-etype-claim").append('<option value="0" disabled selected>{{__('messages.please_select')}}</option>');
            $(res).each(function(key,value){
                $("#select-type-claim").append('<option value="'+value.id+'">'+value.name+'</option>');
                $("#select-etype-claim").append('<option value="'+value.id+'">'+value.name+'</option>');
            });
        });
    }

    function loadClaimsData()
    {
        var claims_table = $("#claims-table").DataTable(
        {
            pageLength: 25,
            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/claims/getClaimsData",
            columns: [
            {data: 'correlative'},
            {data: 'name_type'},
            /*{data: null, render: function(data){
                if (data.proceed == 1) {
                    return "@lang('crm.yes')";
                }
                else {
                    return "@lang('crm.no')";
                }
            } , orderable: false, searchable: false},*/
            {data: 'color_label'},
            //{data: 'name_authorize'},
            {data: 'close_date'},
            {data: 'suggested_closing_date'},
            {data: 'name_register'},
            {data: 'actions', orderable: false, searchable: false}
            ],
            columnDefs: [{
                "targets": [0, 1, 2, 4, 5, 6],
                "className": "text-center"
            }]
        });

        $('#claims-table').on( 'dblclick', 'tr', function () {
            var data = claims_table.row(this).data();
            if (typeof data.id != "undefined") {
                var url = '{!!URL::to('/claims/:id')!!}';
                url = url.replace(':id', data.id);
                $("div.claim_modal").load(url, function(){
                    $(this).modal({backdrop: 'static'});
                });
            }
        });
    }

    function viewClaim(id)
    {
        var url = '{!!URL::to('/claims/:id')!!}';
        url = url.replace(':id', id);
        $("div.claim_modal").load(url, function(){
            $(this).modal({backdrop: 'static'});
        });
    }

    $("#btn-new-claim").click(function(){
        $("#txt-correlative-claim").val('');
        getClaimCorrelative();

        $("#select-type-claim").val('0').change();
        $("#select-status-claim").val('0').change();
        $("#txt-description-claim").val('');
        $("#txt-review-description-claim").val('');
        $("#chk_proceed").prop('checked', false);
        $("#div_resolution").hide();

        $("#div_customer").hide();
        $("#div_product").hide();
        $("#div_invoice").hide();

        $("#txt-resolution-claim").val('');
        $("#txt-close-date-claim").val('');
        $("#txt-suggested-date-claim").val('');
    });

    function showResolution(){
        if ($("#chk_proceed").is(":checked")) {
            $('#div_resolution').show();
            $("#txt-resolution-claim").focus();
        } else {
            $('#div_resolution').hide();
            $("#txt-resolution-claim").val('');
        }
    }

    function showEquipment(){
        if ($("#equipment_reception").is(":checked")) {
            $("#txt-equipment-reception-desc-claim").val('');
            $("#txt-equipment-reception-desc-claim").prop('required', true);
            $('#div_equipment').show();
            $("#txt-equipment-reception-desc-claim").focus();
        } else {
            $("#txt-equipment-reception-desc-claim").val('');
            $("#txt-equipment-reception-desc-claim").prop('required', false);
            $('#div_equipment').hide();
            $("#txt-equipment-reception-desc-claim").focus();
        }
    }

    function eshowEquipment(){
        if ($("#eequipment_reception").is(":checked")) {
            $("#txt-eequipment-reception-desc-claim").val('');
            $("#txt-eequipment-reception-desc-claim").prop('required', true);
            $('#ediv_equipment').show();
            $("#txt-eequipment-reception-desc-claim").focus();
        } else {
            $("#txt-eequipment-reception-desc-claim").val('');
            $("#txt-eequipment-reception-desc-claim").prop('required', false);
            $('#ediv_equipment').hide();
            $("#txt-eequipment-reception-desc-claim").focus();
        }
    }

    $(document).on('submit', 'form#form-add-claim', function(e){
        e.preventDefault();
        $("#btn-add-claim").prop("disabled", true);
        $("#btn-close-modal-add-claim").prop("disabled", true);
        
        correlative = $("#txt-correlative-claim").val();
        claim_type = $("#select-type-claim").val();
        status_claim_id = $("#select-status-claim").val();
        description = $("#txt-description-claim").val();
        claim_date = $("#txt-claim-date-claim").val();
        suggested_closing_date = $("#txt-suggested-date-claim").val();
        review_description = $("#txt-review-description-claim").val();
        if ($("#chk_proceed").is(":checked")) {
            proceed = 1;
        } else {
            proceed = 0;
        }
        resolution = $("#txt-resolution-claim").val();
        close_date = $("#txt-close-date-claim").val();
        customer_id = $("#select-customer-id-claim").val();
        variation_id = $("#select-product-id-claim").val();
        invoice = $("#txt-invoice-claim").val();
        if ($("#equipment_reception").is(":checked")) {
            equipment_reception = 1;
        } else {
            equipment_reception = 0;
        }
        equipment_reception_desc = $("#txt-equipment-reception-desc-claim").val();


        

        route = "/claims";
        token = $("#token").val();
        $.ajax({
            url: route,
            type: 'POST',
            datatype: "json",
            headers: {'X-CSRF-TOKEN': token},
            data: {
                correlative: correlative,
                claim_type: claim_type,
                status_claim_id: status_claim_id,
                description: description,
                claim_date: claim_date,
                suggested_closing_date: suggested_closing_date,
                review_description: review_description,
                proceed: proceed,
                resolution: resolution,
                close_date: close_date,
                customer_id: customer_id,
                variation_id: variation_id,
                invoice: invoice,
                equipment_reception: equipment_reception,
                equipment_reception_desc: equipment_reception_desc
            },
            success:function(result){
                if (result.success == true) {
                    $("#btn-add-claim").prop("disabled", false);
                    $("#btn-close-modal-add-claim").prop("disabled", false); 
                    $("#claims-table").DataTable().ajax.reload(null, false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "success",
                        timer: 3000,
                        showConfirmButton: false,
                    });
                    $("#modal-add-claim").modal('hide');
                }
                else
                {
                    $("#btn-add-claim").prop("disabled", false);
                    $("#btn-close-modal-add-claim").prop("disabled", false);
                    Swal.fire
                    ({
                        title: result.msg,
                        icon: "error",
                    });
                }
            },
            error:function(msj){
                $("#btn-add-claim").prop("disabled", false);
                $("#btn-close-modal-add-claim").prop("disabled", false);
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

    function editClaim(id)
    {
        $("#echk_review").prop('checked', false);
        $("#echk_proceed").prop('checked', false);
        $("#echk_not_proceed").prop('checked', false);

        $("#ediv_review").hide();
        $("#ediv_justification").hide();
        $("#ediv_resolution").hide();

        
        $('#eequipment_reception').prop('disabled', false);
        $('#txt-eequipment-reception-desc-claim').prop('disabled', false);
        $('#select-ecustomer-id-claim').prop('disabled', false);
        $('#select-eproduct-id-claim').prop('disabled', false);
        $('#txt-einvoice-claim').prop('disabled', false);
        $('#txt-edescription-claim').prop('disabled', false);

        $('#echk_review').prop('disabled', false);
        $("#txt-ereview-description-claim").prop('disabled', false);
        $("#echk_proceed").prop('disabled', false);
        $("#echk_not_proceed").prop('disabled', false);
        $("#txt-ejustification-claim").prop('disabled', false);
        $("#echk_close").prop('disabled', false);
        $("#txt-eresolution-claim").prop('disabled', false);




        var route = "/claims/"+id+"/edit";
        var permission_cont = 0;
        var permission_ids = [];
        var permission_rowCont=[];
        $.get(route, function(res) {
            $('#claim_id').val(res.id);
            $('#status_open_id').val(res.status_claim_id);
            $('#txt-ecorrelative-claim').val(res.correlative);
            $('#select-etype-claim').val(res.claim_type).change();
            $('#txt-edescription-claim').val(res.description);
            $("#select-ecustomer-id-claim").val(res.customer_id).change();
            $("#select-eproduct-id-claim").val(res.variation_id).change();
            $("#txt-einvoice-claim").val(res.invoice);

            if (res.equipment_reception == 1) {
                $("#eequipment_reception").prop('checked', true);
                $('#txt-eequipment-reception-desc-claim').val(res.equipment_reception_desc);
                $("#ediv_equipment").show();
            }
            else {
                $("#eequipment_reception").prop('checked', false);
                $("#ediv_equipment").hide();
            }

            if (res.status_claim_id == 1) {

                $('#ediv_justification').hide();
                $('#ediv_review').hide();
                $('#ediv_proceed').hide();
                $("#echk_proceed").prop('checked', false);
                $("#echk_not_proceed").prop('checked', false);
                $("#txt-ejustification-claim").val('');
                $("#txt-ereview-description-claim").val('');
                $('#ediv_closed').hide();
                $('#ediv_resolution').hide();
                $("#txt-eresolution-claim").val('');
                $("#echk_close").prop('checked', false);
                $("#txt-eresolution-claim").val('');
            }


            if(res.status_claim_id == 2) {

                $('#ediv_review').show();
                $('#ediv_proceed').show();
                $('#echk_review').prop('checked', true);
                $("#txt-ereview-description-claim").val(res.review_description);

                $("#echk_proceed").prop('checked', false);
                $("#echk_not_proceed").prop('checked', false);
                $("#txt-ejustification-claim").val('');
                $("#ediv_justification").hide();

                $("#echk_close").prop('checked', false);
                $("#txt-eresolution-claim").val('');

            }

            if(res.status_claim_id == 3) {

                $('#ediv_review').show();


                $('#ediv_proceed').show();


                $('#echk_review').prop('checked', true);



                $("#txt-ereview-description-claim").val(res.review_description);


                if(res.proceed == 1) {
                    $("#echk_proceed").prop('checked', true);


                    $("#txt-ejustification-claim").val(res.justification);


                    $("#ediv_justification").show();
                } 

                if(res.not_proceed == 1) {
                    $("#echk_not_proceed").prop('checked', true);


                    $("#txt-ejustification-claim").val(res.justification);


                    $("#ediv_justification").show();
                }

                $('#ediv_closed').show();

                $("#echk_close").prop('checked', false);


                $("#txt-eresolution-claim").val('');

            }



            if(res.status_claim_id == 4) {

                $('#ediv_review').show();
                $('#ediv_proceed').show();

                $('#echk_review').prop('checked', true);

                
                $('#eequipment_reception').prop('disabled', true);
                $('#txt-eequipment-reception-desc-claim').prop('disabled', true);
                $('#select-ecustomer-id-claim').prop('disabled', true);
                $('#select-eproduct-id-claim').prop('disabled', true);
                $('#txt-einvoice-claim').prop('disabled', true);
                $('#txt-edescription-claim').prop('disabled', true);

                $('#echk_review').prop('disabled', true);
                $("#txt-ereview-description-claim").prop('disabled', true);
                $("#echk_proceed").prop('disabled', true);
                $("#echk_not_proceed").prop('disabled', true);
                $("#txt-ejustification-claim").prop('disabled', true);
                $("#echk_close").prop('disabled', true);
                $("#txt-eresolution-claim").prop('disabled', true);
                

                $("#txt-ereview-description-claim").val(res.review_description);


                if(res.proceed == 1) {
                    $("#echk_proceed").prop('checked', true);
                    $("#txt-ejustification-claim").val(res.justification);
                    $("#ediv_justification").show();
                } 

                if(res.not_proceed == 1) {
                    $("#echk_not_proceed").prop('checked', true);
                    $("#txt-ejustification-claim").val(res.justification);
                    $("#ediv_justification").show();
                }

                $('#ediv_closed').show();
                $("#echk_close").prop('checked', true);
                $("#txt-eresolution-claim").val(res.resolution);
                $('#ediv_resolution').show();

            }


            if(res.status_claim_id == 5) {

                $('#ediv_review').show();
                $('#ediv_proceed').show();
                $('#echk_review').prop('checked', true);
                $("#txt-ereview-description-claim").val(res.review_description);

                
                $('#eequipment_reception').prop('disabled', true);
                $('#txt-eequipment-reception-desc-claim').prop('disabled', true);
                $('#select-ecustomer-id-claim').prop('disabled', true);
                $('#select-eproduct-id-claim').prop('disabled', true);
                $('#txt-einvoice-claim').prop('disabled', true);
                $('#txt-edescription-claim').prop('disabled', true);

                $('#echk_review').prop('disabled', true);
                $("#txt-ereview-description-claim").prop('disabled', true);
                $("#echk_proceed").prop('disabled', true);
                $("#echk_not_proceed").prop('disabled', true);
                $("#txt-ejustification-claim").prop('disabled', true);
                $("#echk_close").prop('disabled', true);
                $("#txt-eresolution-claim").prop('disabled', true);
                

                if(res.proceed == 1) {
                    $("#echk_proceed").prop('checked', true);
                    $("#txt-ejustification-claim").val(res.justification);
                    $("#ediv_justification").show();
                } 

                if(res.not_proceed == 1) {
                    $("#echk_not_proceed").prop('checked', true);
                    $("#txt-ejustification-claim").val(res.justification);
                    $("#ediv_justification").show();
                }

                $('#ediv_closed').show();
                $("#echk_close").prop('checked', true);
                $("#txt-eresolution-claim").val(res.resolution);
                $('#ediv_resolution').show();

            }

            var route = "/claims/getUsersByClaimType/"+res.claim_type;
            $.get(route, function(res) {
                $(res).each(function(key,value) {
                    user_id = value.user_id;
                    count = parseInt(jQuery.inArray(user_id, permission_ids));
                    if (count < 0){
                        permission_ids.push(user_id);
                        permission_rowCont.push(permission_cont);
                        econt++;
                    }
                });
            }); 

            current_user_id = $("#user_id").val();

            count = parseInt(jQuery.inArray(current_user_id, permission_ids));
            if (count >= 0){

                if((res.status_claim_id == 4) || (res.status_claim_id == 5)) {
                    $("#echk_review").prop('disabled', true);
                    $("#txt-ereview-description-claim").prop('disabled', true);


                    $("#echk_proceed").prop('disabled', true);
                    $("#echk_not_proceed").prop('disabled', true);
                    $("#txt-ejustification-claim").prop('disabled', true);

                    $("#echk_close").prop('disabled', true);
                    $("#txt-eresolution-claim").prop('disabled', true);
                } else {
                    $("#echk_review").prop('disabled', false);
                    $("#txt-ereview-description-claim").prop('disabled', false);


                    $("#echk_proceed").prop('disabled', false);
                    $("#echk_not_proceed").prop('disabled', false);
                    $("#txt-ejustification-claim").prop('disabled', false);

                    $("#echk_close").prop('disabled', false);
                    $("#txt-eresolution-claim").prop('disabled', false);
                }

                
                



            } else {
                is_default = $("#is_default").val();
                if (is_default == 1) {

                    if((res.status_claim_id == 4) || (res.status_claim_id == 5)) {
                        $("#echk_review").prop('disabled', true);
                        $("#txt-ereview-description-claim").prop('disabled', true);


                        $("#echk_proceed").prop('disabled', true);
                        $("#echk_not_proceed").prop('disabled', true);
                        $("#txt-ejustification-claim").prop('disabled', true);

                        $("#echk_close").prop('disabled', true);
                        $("#txt-eresolution-claim").prop('disabled', true);
                    } else {
                        $("#echk_review").prop('disabled', false);
                        $("#txt-ereview-description-claim").prop('disabled', false);


                        $("#echk_proceed").prop('disabled', false);
                        $("#echk_not_proceed").prop('disabled', false);
                        $("#txt-ejustification-claim").prop('disabled', false);

                        $("#echk_close").prop('disabled', false);
                        $("#txt-eresolution-claim").prop('disabled', false);
                    }

                } else {

                    if(res.all_access == 1) {
                        if((res.status_claim_id == 4) || (res.status_claim_id == 5)) {
                            $("#echk_review").prop('disabled', true);
                            $("#txt-ereview-description-claim").prop('disabled', true);


                            $("#echk_proceed").prop('disabled', true);
                            $("#echk_not_proceed").prop('disabled', true);
                            $("#txt-ejustification-claim").prop('disabled', true);

                            $("#echk_close").prop('disabled', true);
                            $("#txt-eresolution-claim").prop('disabled', true);
                        } else {
                            $("#echk_review").prop('disabled', false);
                            $("#txt-ereview-description-claim").prop('disabled', false);


                            $("#echk_proceed").prop('disabled', false);
                            $("#echk_not_proceed").prop('disabled', false);
                            $("#txt-ejustification-claim").prop('disabled', false);

                            $("#echk_close").prop('disabled', false);
                            $("#txt-eresolution-claim").prop('disabled', false);
                        }


                    } else {

                        if((res.status_claim_id == 4) || (res.status_claim_id == 5)) {
                            $("#echk_review").prop('disabled', true);
                            $("#txt-ereview-description-claim").prop('disabled', true);


                            $("#echk_proceed").prop('disabled', true);
                            $("#echk_not_proceed").prop('disabled', true);
                            $("#txt-ejustification-claim").prop('disabled', true);

                            $("#echk_close").prop('disabled', true);
                            $("#txt-eresolution-claim").prop('disabled', true);
                        } else {
                            $("#echk_review").prop('disabled', false);
                            $("#txt-ereview-description-claim").prop('disabled', false);


                            $("#echk_proceed").prop('disabled', false);
                            $("#echk_not_proceed").prop('disabled', false);
                            $("#txt-ejustification-claim").prop('disabled', false);

                            $("#echk_close").prop('disabled', false);
                            $("#txt-eresolution-claim").prop('disabled', false);
                        }

                    }


                }
            }





            $('#modal-edit-claim').modal({backdrop: 'static'});
        });
}

function eshowResolution(){
    if ($("#echk_close").is(":checked")) {
        $('#ediv_resolution').show();
        $("#txt-eresolution-claim").val('');
        $("#txt-eresolution-claim").prop('required', true);
        $("#txt-eresolution-claim").focus();
    } else {
        $('#ediv_resolution').hide();
        $("#txt-eresolution-claim").val('');
        $("#txt-eresolution-claim").prop('required', false);
    }
}

function eshowReview(){
    if ($("#echk_review").is(":checked")) {

        $('#ediv_review').show();
        $('#ediv_proceed').show();
        $("#echk_proceed").prop('checked', false);
        $("#echk_not_proceed").prop('checked', false);
        $("#txt-ereview-description-claim").val('');
        $("#txt-ereview-description-claim").prop('required', true);
        $("#txt-ejustification-claim").val('');
        $("#txt-ereview-description-claim").focus();

    } else {

        $('#ediv_justification').hide();
        $('#ediv_review').hide();
        $('#ediv_proceed').hide();
        $("#echk_proceed").prop('checked', false);
        $("#echk_not_proceed").prop('checked', false);
        $("#txt-ejustification-claim").val('');
        $("#txt-ereview-description-claim").val('');
        $("#txt-ereview-description-claim").prop('required', false);
        $('#ediv_closed').hide();
        $('#ediv_resolution').hide();
        $("#txt-eresolution-claim").val('');
        $("#echk_close").prop('checked', false);
    }
}

function eshowJustificationProceed(){
    if ($("#echk_proceed").is(":checked")) {

        $("#echk_not_proceed").prop('checked', false);
        $('#ediv_justification').show();
        $("#txt-ejustification-claim").val('');
        //$("#txt-ejustification-claim").prop('disabled', true);
        $("#txt-ejustification-claim").focus();
        $('#ediv_closed').show();

        $('#ediv_resolution').hide();
        $("#txt-eresolution-claim").val('');
        $("#echk_close").prop('checked', false);

    } else {

        $('#ediv_justification').hide();
        $("#txt-ejustification-claim").val('');
        //$("#txt-ejustification-claim").prop('disabled', false);
        $('#ediv_closed').hide();

        $('#ediv_closed').hide();
        $('#ediv_resolution').hide();
        $("#txt-eresolution-claim").val('');
        $("#echk_close").prop('checked', false);
    }
}

function eshowJustificationNotProceed() {
    if ($("#echk_not_proceed").is(":checked")) {

        $("#echk_proceed").prop('checked', false);
        $('#ediv_justification').show();
        $("#txt-ejustification-claim").val('');
        //$("#txt-ejustification-claim").prop('disabled', true);
        $("#txt-ejustification-claim").focus();
        $('#ediv_closed').show();


        $('#ediv_resolution').hide();
        $("#txt-eresolution-claim").val('');
        $("#echk_close").prop('checked', false);

    } else {

        $('#ediv_justification').hide();
        $("#txt-ejustification-claim").val('');
        //$("#txt-ejustification-claim").prop('disabled', false);
        $('#ediv_closed').hide();

        $('#ediv_closed').hide();
        $('#ediv_resolution').hide();
        $("#txt-eresolution-claim").val('');
        $("#echk_close").prop('checked', false);
    }
}



$(document).on('submit', 'form#form-edit-claim', function(e){
    e.preventDefault();
    $("#btn-edit-claim").prop("disabled", true);
    $("#btn-close-modal-edit-claim").prop("disabled", true);

    correlative = $("#txt-ecorrelative-claim").val();
    description = $("#txt-edescription-claim").val();
    review_description = $("#txt-ereview-description-claim").val();
    justification = $("#txt-ejustification-claim").val();
    resolution = $("#txt-eresolution-claim").val();
    customer_id = $("#select-ecustomer-id-claim").val();
    variation_id = $("#select-eproduct-id-claim").val();
    invoice = $("#txt-einvoice-claim").val();

    if ($("#eequipment_reception").is(":checked")) {
        equipment_reception = 1;
    } else {
        equipment_reception = 0;
    }

    equipment_reception_desc = $("#txt-eequipment-reception-desc-claim").val();

    if ($("#echk_review").is(":checked")) {
        review = 1;
    } else {
        review = 0;
    }

    if ($("#echk_proceed").is(":checked")) {
        proceed = 1;
    } else {
        proceed = 0;
    }

    if ($("#echk_not_proceed").is(":checked")) {
        not_proceed = 1;
    } else {
        not_proceed = 0;
    }

    if ($("#echk_close").is(":checked")) {
        close = 1;
    } else {
        close = 0;
    }

    id = $("#claim_id").val();

    route = "/claims/"+id;
    token = $("#token").val();
    $.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'PUT',
        datatype: "json",
        data: {
            correlative: correlative,
            description: description,
            review_description: review_description,
            justification: justification,
            resolution: resolution,
            customer_id: customer_id,
            variation_id: variation_id,
            invoice: invoice,
            equipment_reception: equipment_reception,
            equipment_reception_desc: equipment_reception_desc,
            review: review,
            proceed: proceed,
            not_proceed: not_proceed,
            close: close
        },
        success:function(result){
            if (result.success == true) {
                $("#btn-edit-claim").prop("disabled", false);
                $("#btn-close-modal-edit-claim").prop("disabled", false); 
                $("#claims-table").DataTable().ajax.reload(null, false);
                Swal.fire
                ({
                    title: result.msg,
                    icon: "success",
                    timer: 3000,
                    showConfirmButton: false,
                });
                $("#modal-edit-claim").modal('hide');
            }
            else
            {
                $("#btn-edit-claim").prop("disabled", false);
                $("#btn-close-modal-edit-claim").prop("disabled", false);
                Swal.fire
                ({
                    title: result.msg,
                    icon: "error",
                });
            }

        },
        error:function(msj){
            $("#btn-edit-claim").prop("disabled", false);
            $("#btn-close-modal-edit-claim").prop("disabled", false);
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

function deleteClaim(id)
{
    Swal.fire({
        title: LANG.sure,
        text: '{{__('messages.delete_content')}}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "{{__('messages.accept')}}",
        cancelButtonText: "{{__('messages.cancel')}}"
    }).then((willDelete) => {
        if (willDelete.value) {
            route = '/claims/'+id;
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
                            timer: 3000,
                            showConfirmButton: false,
                        });
                        $("#claims-table").DataTable().ajax.reload(null, false);
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

$("#link_claims").click(function(){
    $("#thead-claims").show();
});

$("#link_status").click(function(){
    $("#thead-claims").hide();
});

$("#link_types").click(function(){
    $("#thead-claims").hide();
});

function loadStatusData()
{
    var table = $("#status-table").DataTable(
    {
        pageLength: 25,
        deferRender: true,
        processing: true,
        serverSide: true,
        ajax: "/status-claims/getStatusClaimsData",
        columns: [
        {data: 'correlative'},
        {data: 'name'},
        {data: 'status_label'},
        {data: 'predecessor'},
        {data: 'color_label', orderable: false, searchable: false},
        {data: 'actions', orderable: false, searchable: false}
        ],
        columnDefs: [{
            "targets": '_all',
            "className": "text-center"
        }]
    });
}

$("#btn-new-status").click(function(){
    $("#txt-correlative-status").val('');
    getStatusCorrelative();
    $("#txt-name-status").val('');
    $("#predecessor").val(0).change();
    $("#status").prop('checked', false);
    setTimeout(function(){
        $("#txt-name-status").focus();
    },
    800);
});

function getStatusCorrelative()
{
    var route = "/status-claims/getStatusClaimCorrelative";
    $.get(route, function(res) {
        $("#txt-correlative-status").val(res.correlative);
    });
}

function getTypeCorrelative()
{
    var route = "/claim-types/getClaimTypeCorrelative";
    $.get(route, function(res) {
        $("#txt-correlative-type").val(res.correlative);
    });
}

function getClaimCorrelative()
{
    var route = "/claims/getClaimCorrelative";
    $.get(route, function(res) {
        $("#txt-correlative-claim").val(res.correlative);
    });
}

$("#btn-add-status").click(function() {
    $("#btn-add-status").prop("disabled", true);
    $("#btn-close-modal-add-status").prop("disabled", true);

    correlative = $("#txt-correlative-status").val();
    name = $("#txt-name-status").val();

    if ($('#status').prop('checked')){
        status = 1;
    }
    else{
        status = 0;
    }
    predecessor = $("#predecessor").val();
    color = $("#color").val();

    route = "/status-claims";
    token = $("#token").val();
    $.ajax({
        url: route,
        type: 'POST',
        datatype: "json",
        headers: {'X-CSRF-TOKEN': token},
        data: {
            correlative: correlative,
            name: name,
            status: status,
            predecessor: predecessor,
            color: color
        },
        success:function(result){
            if (result.success == true) {
                updateSelectsPredecessor();
                $("#btn-add-status").prop("disabled", false);
                $("#btn-close-modal-add-status").prop("disabled", false); 
                $("#status-table").DataTable().ajax.reload(null, false);
                Swal.fire
                ({
                    title: result.msg,
                    icon: "success",
                    timer: 3000,
                    showConfirmButton: false,
                });
                $("#modal-add-status").modal('hide');
            }
            else
            {
                $("#btn-add-status").prop("disabled", false);
                $("#btn-close-modal-add-status").prop("disabled", false); 
                Swal.fire
                ({
                    title: result.msg,
                    icon: "error",
                });
            }
        },
        error:function(msj){
            $("#btn-add-status").prop("disabled", false);
            $("#btn-close-modal-add-status").prop("disabled", false); 
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

function editStatus(id)
{
    var route = "/status-claims/"+id+"/edit";
    $.get(route, function(res) {
        $('#status_id').val(res.id);
        $('#txt-ecorrelative-status').val(res.correlative);
        $('#txt-ename-status').val(res.name);
        $('#ecolor').val(res.color);
        $('#epredecessor').val(res.predecessor).change();
        if(res.status == 1) {
            $("#estatus").prop('checked', true);
        } else {
            $("#estatus").prop('checked', false);
        }
        $('#modal-edit-status').modal({backdrop: 'static'});
    });
}

$("#btn-edit-status").click(function(event) {
    $("#btn-edit-status").prop("disabled", true);
    $("#btn-close-modal-edit-status").prop("disabled", true);

    name = $("#txt-ename-status").val();

    if ($('#estatus').prop('checked')){
        status = 1;
    }
    else{
        status = 0;
    }
    predecessor = $("#epredecessor").val();
    color = $("#ecolor").val();
    id = $("#status_id").val();

    route = "/status-claims/"+id;
    token = $("#token").val();
    $.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'PUT',
        datatype: "json",
        data: {
            name: name,
            status: status,
            predecessor: predecessor,
            color: color
        },
        success:function(result){
            if (result.success == true) {
                updateSelectsPredecessor();
                $("#btn-edit-status").prop("disabled", false);
                $("#btn-close-modal-edit-status").prop("disabled", false);
                $("#status-table").DataTable().ajax.reload(null, false);
                Swal.fire
                ({
                    title: result.msg,
                    icon: "success",
                    timer: 3000,
                    showConfirmButton: false,
                });
                $("#modal-edit-status").modal('hide');
            }
            else
            {
                $("#btn-edit-status").prop("disabled", false);
                $("#btn-close-modal-edit-status").prop("disabled", false);
                Swal.fire
                ({
                    title: result.msg,
                    icon: "error",
                });
            }

        },
        error:function(msj){
            $("#btn-edit-status").prop("disabled", false);
            $("#btn-close-modal-edit-status").prop("disabled", false);
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

function deleteStatus(id)
{
    Swal.fire({
        title: LANG.sure,
        text: '{{__('messages.delete_content')}}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "{{__('messages.accept')}}",
        cancelButtonText: "{{__('messages.cancel')}}"
    }).then((willDelete) => {
        if (willDelete.value) {
            route = '/status-claims/'+id;
            token = $("#token").val();
            $.ajax({                    
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'DELETE',
                dataType: 'json',                       
                success:function(result){
                    if(result.success == true){
                        updateSelectsPredecessor();
                        Swal.fire
                        ({
                            title: result.msg,
                            icon: "success",
                            timer: 3000,
                            showConfirmButton: false,
                        });
                        $("#status-table").DataTable().ajax.reload(null, false);
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

function updateSelectsPredecessor()
{
    $("#predecessor").empty();
    $("#epredecessor").empty();
    $("#select-estatus-claim").empty();

    var route = "/status-claims/getStatusClaims";
    $.get(route, function(res){
        $("#predecessor").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
        $("#epredecessor").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');
        $("#select-estatus-claim").append('<option value="0" disabled selected>{{ __('messages.please_select') }}</option>');



        $(res).each(function(key,value){
            $("#predecessor").append('<option value="'+value.id+'">'+value.name+'</option>');
            $("#epredecessor").append('<option value="'+value.id+'">'+value.name+'</option>');
            $("#select-estatus-claim").append('<option value="'+value.id+'">'+value.name+'</option>');


        });
    });
}


var cont = 0;
var user_ids = [];
var rowCont=[];

var econt = 0;
var euser_ids = [];
var erowCont=[];

var permission_cont = 0;
var permission_ids = [];
var permission_rowCont=[];

function addUser()
{
    var route = "/claim-types/getUserById/"+id;
    $.get(route, function(res){
        user_id = res.id;
        name = res.full_name
        count = parseInt(jQuery.inArray(user_id, user_ids));
        if (count >= 0)
        {
            Swal.fire
            ({
                title: "{{__('crm.user_already_added')}}",
                icon: "error",
            });
        }
        else
        {
            user_ids.push(user_id);
            rowCont.push(cont);
            var row = '<tr class="selected" id="row'+cont+'" style="height: 10px"><td><button id="bitem'+cont+'" type="button" class="btn btn-danger btn-xs" onclick="deleteUser('+cont+', '+user_id+');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="user_id[]" value="'+user_id+'">'+name+'</td></tr>';
            $("#list").append(row);
            cont++;
        }
    });
}

function eaddUser()
{
    var route = "/claim-types/getUserById/"+id;
    $.get(route, function(res){
        user_id = res.id;
        name = res.full_name
        count = parseInt(jQuery.inArray(user_id, euser_ids));
        if (count >= 0)
        {
            Swal.fire
            ({
                title: "{{__('crm.user_already_added')}}",
                icon: "error",
            });
        }
        else
        {
            euser_ids.push(user_id);
            erowCont.push(cont);
            var erow = '<tr class="selected" id="erow'+econt+'" style="height: 10px"><td><button id="ebitem'+econt+'" type="button" class="btn btn-danger btn-xs" onclick="edeleteUser('+econt+', '+user_id+');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="euser_id[]" value="'+user_id+'">'+name+'</td></tr>';
            $("#elist").append(erow);
            econt++;
        }
    });

}

function deleteUser(index, id){ 
    $("#row" + index).remove();
    user_ids.removeItem(id);
    if(user_ids.length == 0)
    {
        cont = 0;
        user_ids = [];
        rowCont = [];
    }
}

function edeleteUser(index, id){ 
    $("#erow" + index).remove();
    euser_ids.removeItem(id);
    if(euser_ids.length == 0)
    {
        econt = 0;
        euser_ids = [];
        erowCont = [];
    }
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

function showUsers(){
    if ($("#all_access_users").is(":checked")) {
        $("#list").empty();
        cont = 0;
        user_ids=[];
        rowCont=[];
        $('#div_users').hide();
    } else {
        $('#div_users').show();
        $("#list").empty();
        cont = 0;
        user_ids=[];
        rowCont=[];
    }
}

function eshowUsers(){
    if ($("#eall_access_users").is(":checked")) {
        $("#elist").empty();
        econt = 0;
        euser_ids=[];
        erowCont=[];
        $('#ediv_users').hide();
    } else {
        $("#elist").empty();
        econt = 0;
        euser_ids=[];
        erowCont=[];
        $('#ediv_users').show();
    }
}

$("#users").change(function() {
    id = $("#users").val();
    if(id != 0){
        addUser();
        $("#users").val(0).change();
    }
});

$("#eusers").change(function() {
    id = $("#eusers").val();
    if(id != 0){
        eaddUser();
        $("#eusers").val(0).change();
    }
});

$("#select-type-claim").change(function(event) {

    id = $("#select-type-claim").val();
    if(id != null) {

        var route = "/claim-types/"+id;
        $.get(route, function(res) {

            if(res.required_customer == 1) {
                $("#select-customer-id-claim").val(0).change();
                $("#select-customer-id-claim").prop('required', true);
                $("#div_customer").show();
            } else {
                $("#select-customer-id-claim").val(0).change();
                $("#select-customer-id-claim").prop('required', false);
                $("#div_customer").hide();
            }

            if(res.required_product == 1) {
                $("#select-product-id-claim").val(0).change();
                $("#select-product-id-claim").prop('required', true);
                $("#div_product").show();
            } else {
                $("#select-product-id-claim").val(0).change();
                $("#select-product-id-claim").prop('required', false);
                $("#div_product").hide();
            }

            if(res.required_invoice == 1) {
                $("#txt-invoice-claim").val('');
                $("#txt-invoice-claim").prop('required', true);
                $("#div_invoice").show();
            } else {
                $("#txt-invoice-claim").val('');
                $("#txt-invoice-claim").prop('required', false);
                $("#div_invoice").hide();
            }

            date = $("#txt-claim-date-claim").val();
            days = res.resolution_time;
            var route = "/claim-types/getSuggestedClosingDate/"+date+"/"+days;
            $.get(route, function(res) {
                $("#txt-suggested-date-claim").val(res.suggested_date);
            });
        });
    }
});


$("#select-etype-claim").change(function(event) {

    id = $("#select-etype-claim").val();
    if(id != null) {

        var route = "/claim-types/"+id;
        $.get(route, function(res) {

            if(res.required_customer == 1) {
                //$("#select-ecustomer-id-claim").val(0).change();
                $("#select-ecustomer-id-claim").prop('required', true);
                $("#ediv_customer").show();
            } else {
                $("#select-ecustomer-id-claim").val(0).change();
                $("#select-ecustomer-id-claim").prop('required', false);
                $("#ediv_customer").hide();
            }

            if(res.required_product == 1) {
                //$("#select-eproduct-id-claim").val(0).change();
                $("#select-eproduct-id-claim").prop('required', true);
                $("#ediv_product").show();
            } else {
                $("#select-eproduct-id-claim").val(0).change();
                $("#select-eproduct-id-claim").prop('required', false);
                $("#ediv_product").hide();
            }

            if(res.required_invoice == 1) {
                //$("#txt-einvoice-claim").val('');
                $("#txt-einvoice-claim").prop('required', true);
                $("#ediv_invoice").show();
            } else {
                $("#txt-einvoice-claim").val('');
                $("#txt-einvoice-claim").prop('required', false);
                $("#ediv_invoice").hide();
            }

            date = $("#txt-eclaim-date-claim").val();
            days = res.resolution_time;
            var route = "/claim-types/getSuggestedClosingDate/"+date+"/"+days;
            $.get(route, function(res) {
                $("#txt-esuggested-date-claim").val(res.suggested_date);
            });
        });
    }
});

$("#txt-claim-date-claim").change(function(event) {

    id = $("#select-type-claim").val();
    date = $("#txt-claim-date-claim").val();
    if(id != null) {

        var route = "/claim-types/"+id;
        $.get(route, function(res) {
            var route = "/claim-types/getSuggestedClosingDate/"+date+"/"+res.resolution_time;
            $.get(route, function(res) {
                $("#txt-suggested-date-claim").val(res.suggested_date);
            });
        });
    }
});

$("#txt-eclaim-date-claim").change(function(event) {

    id = $("#select-etype-claim").val();
    date = $("#txt-eclaim-date-claim").val();
    if(id != null) {

        var route = "/claim-types/"+id;
        $.get(route, function(res) {
            var route = "/claim-types/getSuggestedClosingDate/"+date+"/"+res.resolution_time;
            $.get(route, function(res) {
                $("#txt-esuggested-date-claim").val(res.suggested_date);
            });
        });
    }
});

$("#select-estatus-claim").change(function(event) {
    status_id = $("#select-estatus-claim").val();
    claim_id = $("#claim_id").val();
    status_open_id = $("#status_open_id").val();
    if ((status_id != status_open_id) && (status_id != null) && (claim_id != null) && ($("#modal-edit-claim").data('bs.modal') || {}).isShown ) {
        var route = "/claims/getNexState/"+ status_id +"/"+claim_id;
        $.get(route, function(res) {
            if (res.success == false) {
                Swal.fire
                ({
                    title: res.msg,
                    icon: "error",
                });
                $("#select-estatus-claim").val(0).change();

            } else {
                    //
                }
            });
    }
});

</script>
@endsection
@extends('layouts.app')
@section('title', __( 'accounting.costs_menu' ))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('cost_center.cost_centers' )</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="panel with-nav-tabs panel-default boxform_u box-solid_u">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-costs" data-toggle="tab">@lang('cost_center.cost_center')</a></li>
                <li><a href="#tab-categories" data-toggle="tab">@lang('accounting.categories')</a></li>
            </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade in active" id="tab-costs">
                    <div class="boxform_u box-solid_u">
                        <div class="box-header">
                            <h3 class="box-title">@lang( 'cost_center.all_your_cost_centers' )</h3>
                            <div class="box-tools">
                                <button type="button" class="btn btn-primary" data-href="{{ action("CostCenterController@create") }}" id="btn_add_cost_center">
                                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-condensed table-hover" id="cost_centers_table" width="100%">
                                    <thead>
                                        <th>@lang('accounting.name')</th>
                                        <th>@lang('accounting.description')</th>
                                        <th>@lang('business.location')</th>
                                        <th>@lang( 'messages.actions' )</th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-categories">
                    <div class="boxform_u box-solid_u">
                        <div class="box-header">
                            <h3 class="box-title">@lang( 'accounting.all_your_categories' )</h3>
                            <div class="box-tools">
                                <button type="button" class="btn btn-primary" data-toggle='modal' data-target='#modal-add-categorie' data-backdrop="static" data-keyboard="false" id="btn-new-categorie"><i class="fa fa-plus"></i> @lang( 'messages.add' )
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-condensed table-hover" id="bank-accounts-table" width="100%">
                                    <thead>
                                        <th>@lang('accounting.name')</th>
                                        <th>@lang('accounting.description')</th>
                                        <th>@lang('accounting.costs_menu')</th>
                                        <th>@lang('messages.actions')</th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade cost_center_modal" tabindex="-1" data-backdrop="static"
        role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade cost_center_accounts_modal" tabindex="-1" data-backdrop="static"
        role="dialog" aria-labelledby="gridSystemModalLabel"></div>

</section>
<!-- /.content -->
@endsection
@section('javascript')
<script src="{{ asset('js/cost_center.js?v=' . $asset_v) }}"></script>
@endsection
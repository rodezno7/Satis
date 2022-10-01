@extends('layouts.app')

@section('title', __('inflow_outflow.inputs_and_outputs'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('inflow_outflow.inputs_and_outputs')
        <small>@lang('inflow_outflow.manage_inputs_and_outputs')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang('inflow_outflow.all_inputs_and_outputs')</h3>
            {{-- @can('inflow_outflow.create')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal"
                    data-href="{{ action('Optics\InflowOutflowController@create', ['type' => 'both']) }}" 
                    data-container=".inflow_outflows_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </button>
            </div>
            @endcan --}}
        </div>
        
        <div class="box-body">
            @can('inflow_outflow.view')
            <div class="table-responsive">
            <table class="table table-bordered table-striped vg-middle" id="inflow_outflows_table">
                <thead>
                    <tr>
                        <th>@lang('inflow_outflow.cash_register')</th>
                        <th>@lang('inflow_outflow.amount')</th>
                        <th>@lang('inflow_outflow.reason')</th>
                        <th>@lang('inflow_outflow.type')</th>
                        <th>@lang('inflow_outflow.responsible')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
            </div>
            @endcan
        </div>
    </div>

    <div class="modal fade inflow_outflow_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script src="{{ asset('js/inflow_outflow.js?v=' . $asset_v) }}"></script>
@endsection
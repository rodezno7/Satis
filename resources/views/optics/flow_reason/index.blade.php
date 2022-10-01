@extends('layouts.app')

@section('title', __('flow_reason.flow_reasons'))

@section('content')
{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1>@lang('flow_reason.flow_reasons')
        <small>@lang('flow_reason.manage_flow_reasons')</small>
    </h1>
</section>

{{-- Main content --}}
<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang('flow_reason.all_flow_reasons')</h3>
            @can('flow_reason.create')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{ action('Optics\FlowReasonController@create') }}" 
                    data-container=".flow_reasons_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </button>
            </div>
            @endcan
        </div>
        
        <div class="box-body">
            @can('flow_reason.view')
            <div class="table-responsive">
            <table class="table table-bordered table-striped vg-middle" id="flow_reasons_table">
                <thead>
                    <tr>
                        <th>@lang('inflow_outflow.reason')</th>
                        <th>@lang('lang_v1.description')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
            </div>
            @endcan
        </div>
    </div>

    <div class="modal fade flow_reasons_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->
@endsection

@section('javascript')
<script src="{{ asset('js/flow_reason.js?v=' . $asset_v) }}"></script>
@endsection
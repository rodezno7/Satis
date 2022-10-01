@extends('layouts.app')

@section('title', __('physical_inventory.physical_inventory'))

@section('content')
{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1>@lang('physical_inventory.physical_inventory')</h1>
</section>

{{-- Main content --}}
<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang('physical_inventory.manage_physical_inventory')</h3>
            @can('physical_inventory.create')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{ action('PhysicalInventoryController@create') }}" 
                    data-container=".physical_inventory_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </button>
            </div>
            @endcan
        </div>
        
        <div class="box-body">
            @can('physical_inventory.view')
            <div class="table-responsive">
                <table class="table table-hover table-text-center" id="physical_inventory_table" width="100%">
                    <thead>
                        <tr>
                            <th>@lang('accounting.code')</th>
                            <th>@lang('accounting.name')</th>
                            <th>@lang('messages.date')</th>
                            <th>@lang('accounting.location')</th>
                            <th>@lang('warehouse.warehouse')</th>
                            <th>@lang('product.status')</th>
                            <th>@lang('physical_inventory.responsible')</th>
                            <th>@lang('messages.actions')</th>
                        </tr>
                    </thead>
                </table>
            </div>
            @endcan
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade physical_inventory_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

    {{-- Notification --}}
    @if (! empty($output))
    {!! Form::hidden('notification', $output['success'], ['id' => 'notification', 'data-msg' => $output['msg']]) !!}
    @endif
</section>

@endsection

@section('javascript')
<script src="{{ asset('js/physical_inventory.js?v=' . $asset_v) }}"></script>
@endsection
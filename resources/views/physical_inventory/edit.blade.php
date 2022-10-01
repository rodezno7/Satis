@extends('layouts.app')

@section('title', __('physical_inventory.physical_inventory'))

@section('content')
{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1>@lang('physical_inventory.physical_inventory')</h1>
</section>

{{-- Main content --}}
<section class="content">
    {{-- General info --}}
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang('physical_inventory.register_physical_inventory')</h3>
        </div>
        
        <div class="box-body">
            {{-- Information --}}
            <div class="row">
                {!! Form::hidden('physical_inventory_id', $physical_inventory->id, ['id' => 'physical_inventory_id']) !!}

                {!! Form::hidden('is_editable', $is_editable, ['id' => 'is_editable']) !!}

                {{-- Code --}}
                <div class="col-sm-2">
                    <div class="form-group">
                        {!! Form::label('code', __('accounting.code') . ':') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span>
                                    <i class="fa fa fa-hashtag"></i>
                                </span>
                            </div>
                            {!! Form::text('code', $physical_inventory->code,
                                ['class' => 'form-control', $is_editable == 0 ? 'readonly' : '', 'placeholder' => __('accounting.code')]) !!}
                        </div>
                    </div>
                </div>

                {{-- Name --}}
                <div class="@if ($physical_inventory_record_date == 'define_date') col-sm-4 @else col-sm-6 @endif">
                    <div class="form-group">
                        {!! Form::label('name', __('accounting.name') . ':') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span>
                                    <i class="fa fa-file-text-o"></i>
                                </span>
                            </div>
                            {!! Form::text('name', $physical_inventory->name,
                                ['class' => 'form-control', 'readonly', 'placeholder' => __('accounting.name')]) !!}
                        </div>
                    </div>
                </div>

                {{-- Date --}}
                <div class="col-sm-2">
                    <div class="form-group">
                        {!! Form::label('start_date', __('messages.date') . ':') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span>
                                    <i class="fa fa-calendar"></i>
                                </span>
                            </div>
                            {!! Form::text('name', @format_date($physical_inventory->start_date),
                                ['class' => 'form-control', 'readonly', 'placeholder' => __('messages.date')]) !!}
                        </div>
                    </div>
                </div>

                @if ($physical_inventory_record_date == 'define_date')
                {{-- End date --}}
                <div class="col-sm-2">
                    <div class="form-group">
                        {!! Form::label('end_date', __('physical_inventory.execution_date') . ':') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span>
                                    <i class="fa fa-calendar"></i>
                                </span>
                            </div>
                            {!! Form::text('end_date', is_null($physical_inventory->end_date) ? @format_date('now') : @format_date($physical_inventory->end_date),
                                ['class' => 'form-control', 'readonly', 'placeholder' => __('messages.date')]) !!}
                        </div>
                    </div>
                </div>
                @endif

                {{-- Status --}}
                <div class="col-sm-2">
                    <div class="form-group">
                        {!! Form::label('status', __('product.status') . ':') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span>
                                    <i class="fa fa-hourglass-start"></i>
                                </span>
                            </div>
                            {!! Form::text('status', __('physical_inventory.' . $physical_inventory->status),
                                ['class' => 'form-control', 'readonly', 'placeholder' => __('product.status')]) !!}
                        </div>
                    </div>
                </div>

                {{-- Location --}}
                <div class="@if (config('app.business') == 'optics') col-sm-3 @else col-sm-4 @endif">
                    <div class="form-group">
                        {!! Form::label('location_id', __('accounting.location') . ':') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span>
                                    <i class="fa fa-building"></i>
                                </span>
                            </div>
                            {!! Form::text('txt_location_id', $physical_inventory->location->name,
                                ['class' => 'form-control', 'readonly', 'placeholder' => __('accounting.location')]) !!}
                        </div>
                    </div>
                    {!! Form::hidden('location_id', $physical_inventory->location_id, ['id' => 'location_id']) !!}
                </div>

                {{-- Warehouse --}}
                <div class="@if (config('app.business') == 'optics') col-sm-3 @else col-sm-4 @endif">
                    <div class="form-group">
                        {!! Form::label('warehouse_id', __('warehouse.warehouse') . ':') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span>
                                    <i class="fa fa-industry"></i>
                                </span>
                            </div>
                            {!! Form::text('txt_warehouse_id', $physical_inventory->warehouse->name,
                                ['class' => 'form-control', 'readonly', 'placeholder' => __('warehouse.warehouse')]) !!}
                        </div>
                    </div>
                    {!! Form::hidden('warehouse_id', $physical_inventory->warehouse_id, ['id' => 'warehouse_id']) !!}
                </div>

                {{-- Responsible --}}
                <div class="@if (config('app.business') == 'optics') col-sm-3 @else col-sm-4 @endif">
                    <div class="form-group">
                        {!! Form::label('responsible', __('physical_inventory.responsible') . ':') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span>
                                    <i class="fa fa-user-circle"></i>
                                </span>
                            </div>
                            {!! Form::text('responsible', $physical_inventory->user->first_name . ' ' . $physical_inventory->user->last_name,
                                ['class' => 'form-control', 'readonly', 'placeholder' => __('warehouse.warehouse')]) !!}
                        </div>
                    </div>
                </div>

                {{-- Category --}}
                @if (config('app.business') == 'optics')
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('category', __('physical_inventory.category') . ':') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span>
                                    <i class="fa fa-tag"></i>
                                </span>
                            </div>
                            {!! Form::text('txt_category', __('physical_inventory.' . $physical_inventory->category),
                                ['class' => 'form-control', 'readonly', 'placeholder' => __('physical_inventory.category')]) !!}
                        </div>
                    </div>
                    {!! Form::hidden('category', $physical_inventory->category, ['id' => 'category']) !!}
                </div>
                @endif

                {{-- Show or hide stock and difference columns --}}
                <input type="hidden" id="view_stock" @if (auth()->user()->can('physical_inventory.view_stock')) value="1" @else value="2" @endif>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang('lang_v1.list_products')</h3>
        </div>
        
        <div class="box-body">
            {{-- Search bar --}}
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                    <div class="form-group">
                        <div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-search"></i>
							</span>
                            @if ($is_editable == 1)    
							{!! Form::text('search_product', null,
                                ['class' => 'form-control', 'id' => 'search_product_for_physical_inventory',
                                    'placeholder' => __('product.add_product')]) !!}
                            @else
                            {!! Form::text('search_product', null,
                                ['class' => 'form-control', 'id' => 'search_product_for_physical_inventory',
                                    'placeholder' => __('product.add_product'), 'disabled']) !!}
                            @endif
						</div>
                    </div>
                </div>
            </div>

            {{-- Datatable --}}
            <div class="table-responsive">
                <table class="table table-hover table-text-center" id="physical_inventory_lines_table" width="100%">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>@lang('sale.product')</th>
                            <th>@lang('physical_inventory.quantity_in_physical')</th>
                            <th>@lang('physical_inventory.quantity_in_system')</th>
                            <th>@lang('physical_inventory.difference')</th>
                            <th>@lang('physical_inventory.price')</th>
                            <th class="text-center"><i class="fa fa-trash" aria-hidden="true"></i></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="box box-solid">
        <div class="box-body">
            <div class="row">
                <div class="col-sm-12 text-right">
                    @if (
                        (config('app.business') != 'optics') ||
                        (config('app.business') == 'optics' && auth()->user()->can('physical_inventory.send_to_review'))
                    )
                        @if ($physical_inventory->status == 'new' || $physical_inventory->status == 'process')
                        <a href="{{ action('PhysicalInventoryController@changeStatus', ['id' => $physical_inventory->id, 'review']) }}" class="btn btn-primary">
                            @lang('physical_inventory.send_to_review')
                        </a>
                        @endif
                    @endif

                    @if (
                        (config('app.business') != 'optics') ||
                        (config('app.business') == 'optics' && auth()->user()->can('physical_inventory.authorize'))
                    )
                        @if ($physical_inventory->status == 'review')
                        <a href="{{ action('PhysicalInventoryController@changeStatus', ['id' => $physical_inventory->id, 'authorized']) }}" class="btn btn-primary">
                            @lang('physical_inventory.authorize')
                        </a>
                        @endif
                    @endif

                    @if (
                        (config('app.business') != 'optics') ||
                        (config('app.business') == 'optics' && auth()->user()->can('physical_inventory.authorized'))
                    )
                        @if ($physical_inventory->status == 'authorized')
                        <a href="{{ action('PhysicalInventoryController@finalize', ['id' => $physical_inventory->id]) }}" class="btn btn-primary">
                            @lang('physical_inventory.finalize')
                        </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('javascript')
<script src="{{ asset('js/physical_inventory.js?v=' . $asset_v) }}"></script>
@endsection
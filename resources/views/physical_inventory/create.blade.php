<div class="modal-dialog" role="dialog" id="modal-create">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open([
            'url' => action('PhysicalInventoryController@store'),
            'method' => 'post',
            'id' => 'physical_inventory_add_form'
        ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>

            <h4 class="modal-title">@lang('physical_inventory.register_physical_inventory')</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                {{-- name --}}
                <div class="col-sm-8">
                    <div class="form-group">
                        {!! Form::label('name', __('accounting.name') . ':') !!}
                        <span style="color: red;">*</span>
                        {!! Form::text('name', null,
                            ['class' => 'form-control', 'required', 'placeholder' => __('accounting.name')]) !!}
                    </div>
                </div>

                {{-- code --}}
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('code', __('accounting.code') . ':') !!}
                        <span style="color: red;">*</span>
                        {!! Form::text('code', $code,
                            ['class' => 'form-control', 'required', 'placeholder' => __('accounting.code')]) !!}
                    </div>
                </div>

                {{-- responsible --}}
                <div class="col-sm-8">
                    <div class="form-group">
                        {!! Form::label('responsible', __('physical_inventory.responsible') . ':') !!}
                        <span style="color: red;">*</span>
                        {!! Form::select('responsible', $users, '',
                            ['class' => 'form-control select2', 'required', 'placeholder' => __('messages.please_select')]) !!}
                    </div>
                </div>

                {{-- start_date --}}
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('start_date', __('messages.date') . ':') !!}
                        <span style="color: red;">*</span>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span>
                                    <i class="fa fa-calendar"></i>
                                </span>
                            </div>
                            {!! Form::text("start_date", @format_date('now'),
                                ['class' => 'form-control', 'id' => 'start_date', 'required', 'readonly', "style" => "width: 100%;"]) !!}
                        </div>
                    </div>
                </div>

                {{-- location_id --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('location_id', __('accounting.location') . ':') !!}
                        <span style="color: red;">*</span>
                        {!! Form::select('location_id', $locations, '',
                            ['class' => 'form-control select2', 'id' => 'location_id', 'required', 'placeholder' => __('messages.please_select')]) !!}
                    </div>
                </div>

                {{-- warehouse_id --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('warehouse_id', __('warehouse.warehouse') . ':') !!}
                        <span style="color: red;">*</span>
                        {!! Form::select('warehouse_id', [], '',
                            ['class' => 'form-control select2', 'id' => 'warehouse_id', 'required', 'disabled', 'placeholder' => __('messages.please_select')]) !!}
                    </div>
                </div>

                @if (config('app.business') == 'optics')
                {{-- category --}}
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('category', __('physical_inventory.category') . ':') !!}
                        <span style="color: red;">*</span>
                        {!! Form::select('category', $categories, '',
                            ['class' => 'form-control select2', 'id' => 'category', 'required', 'placeholder' => __('messages.please_select')]) !!}
                    </div>
                </div>
                @endif

                {{-- autoload --}}
                @if (
                    (config('app.business') != 'optics') ||
                    (config('app.business') == 'optics' && auth()->user()->can('physical_inventory.autoload'))
                )
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="checkbox">
                            <label>
                                {!! Form::checkbox('autoload', 1, null, ['id' => 'autoload', 'class' => 'input-icheck']) !!}
                                {{ __('physical_inventory.autoload_all_products') }}
                            </label>
                            </div>
                        </div>
                    </div>

                    @if (! is_null($product_settings['product_rotation']) && $product_settings['product_rotation'] > 0)
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('autoload_rotation', 1, null, ['id' => 'autoload_rotation', 'class' => 'input-icheck']) !!}
                                    @if ($product_settings['product_rotation'] == 1)
                                        {{ __('physical_inventory.autoload_rotation_products_one_month') }}
                                    @else
                                        {{ __('physical_inventory.autoload_rotation_products', ['number' => $product_settings['product_rotation']]) }}
                                    @endif
                                </label>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" data-dismiss="modal" aria-label="Close"
                class="btn btn-default">@lang('messages.close')</button>
        </div>

        {!! Form::close() !!}
    </div>
</div>

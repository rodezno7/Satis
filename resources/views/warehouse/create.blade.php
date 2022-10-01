<div class="modal-dialog" role="dialog" id="modal-create">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('WarehouseController@store'), 'method' => 'post', 'id' => 'warehouse_add_form'])
        !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('warehouse.add_warehouse')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-4">
                    <!-- Code -->
                    <div class="form-group">
                        {!! Form::label('code', __('cashier.code') . ':*') !!}
                        {!! Form::text('code', $code, ['class' => 'form-control', 'required', 'readonly',
                        'placeholder' => __('cashier.code')]) !!}
                    </div>
                </div>
                <div class="col-sm-8">
                    <!-- Name -->
                    <div class="form-group">
                        {!! Form::label('name', __('cashier.name') . ':*') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' =>
                        __('cashier.name')]) !!}
                    </div>
                </div>
                <div class="col-sm-12">
                    <!-- Location -->
                    <div class="form-group">
                        {!! Form::label('location', __('warehouse.location') . ':*') !!}
                        {!! Form::text('location', null, ['class' => 'form-control', 'required', 'placeholder' =>
                        __('warehouse.location')]) !!}
                    </div>
                </div>
                <div class="col-sm-12">
                    <!-- Description -->
                    <div class="form-group">
                        {!! Form::label('description', __('lang_v1.description') . ':') !!}
                        {!! Form::text('description', null, ['class' => 'form-control', 'placeholder' =>
                        __('lang_v1.description')]) !!}
                    </div>
                </div>
                <!-- Accounting account -->
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('catalogue_id', __('accounting.accounting_account') . ':') !!}
                        {!! Form::select('catalogue_id', [], null, ['class' => 'form-control', 'id' => 'catalogue_id', 'placeholder' => __('accounting.accounting_account')]) !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <!-- Status -->
                    <div class="form-group">
                        {!! Form::label('status', __('cashier.status') . ':*') !!}
                        {!! Form::select('status', ['active' => __('cashier.active'), 'inactive' =>
                        __('cashier.inactive')], '', ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="col-sm-8">
                    <!-- Business Location -->
                    <div class="form-group">
                        {!! Form::label('business_location_id', __('cashier.business_location') . ':') !!}
                        {!! Form::select('business_location_id', $business_locations, '', ['class' => 'form-control', 'placeholder' => __('warehouse.none')]) !!}
                    </div>
                </div>
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

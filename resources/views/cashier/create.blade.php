<div class="modal-dialog" role="dialog" id="modal-create">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('CashierController@store'), 'method' => 'post', 'id' => 'cashier_add_form']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('cashier.add_cashier')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <!-- Code -->
                <div class="col-sm-8">
                    <div class="form-group">
                        {!! Form::label('code', __('cashier.code') . ' : ') !!}
                        {!! Form::text('code', $code, ['class' => 'form-control text-center', 'required',
                        'readonly', 'placeholder' => __('cashier.code')]); !!}
                    </div>
                </div>
                <!-- Status -->
                <div class="col-sm-4">
                    <div class="form-group">
                    {!! Form::label('status', __('cashier.active') . ' : ') !!}
                        {!! Form::select('is_active', ['1' => __('messages.yes'), '0' => __('messages.no')], '1', ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <!-- Name -->
                <div class="col-sm-12">
                    <div class="form-group">
                    {!! Form::label('name', __('cashier.name') . ' : ') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __('cashier.name')]) !!}
                    </div>
                </div>
                <!-- Business Location -->
                <div class="col-sm-12">
                    <div class="form-group">
                    {!! Form::label('business_location_id', __('cashier.business_location') . ' : ') !!}
                        {!! Form::select('business_location_id', $business_locations, '', ['class' => 'form-control',
                            'required', 'placeholder' => __('cashier.business_location')]) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

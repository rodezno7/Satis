<div class="modal-dialog modal-lg" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('Optics\PatientController@store'), 'method' => 'post', 'id' => 'patient_add_form']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('patient.add_patient')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('code', __('customer.code') . ' : ') !!}
                        <div class="wrap-inputform">
                            {!! Form::text('code', $code, ['class' => 'form-control text-center', 'required',
                            'readonly', 'placeholder' => __('customer.code')]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="form-group">
                        {!! Form::label('name', __('customer.full_name') . ' : ') !!}
                        {!! Form::text('full_name', $patient_name, ['class' => 'form-control', 'required', 'placeholder' =>
                        __('crm.name'), 'id' => 'full_name']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('age', __('customer.age') . ' : ') !!}
                        {!! Form::number('age', null, ['class' => 'form-control text-center', 'required', 'placeholder'
                        => __('customer.age')]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sex', __('customer.sex') . ' : ') !!}
                        {!! Form::select('sex', $sexs, null, ['class' => 'form-control select2', 'placeholder' =>
                        __('messages.please_select'), 'required']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('email', __('customer.email') . ' : ') !!}
                        {!! Form::email('email', null, ['class' => 'form-control text-center', 'placeholder' =>
                        __('customer.email')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('phone', __('customer.phone') . ' : ') !!}
                        {!! Form::text('contacts', null, ['class' => 'form-control text-center', 'required',
                        'placeholder' => __('customer.phone')]) !!}
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="form-group">
                        {!! Form::label('address', __('customer.address') . ' : ') !!}
                        {!! Form::text('address', null, ['class' => 'form-control text-center', 'placeholder' =>
                        __('customer.address')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" onclick="showgraduationbox()"
                            id="chkhas_glasses">
                        <label class="form-check-label" for="chkhas_glasses">@lang('customer.has_glasses')</label>
                    </div>
                </div>
                <div class="col-md-9" id="graduation_box" style="display: none">
                    {!! Form::label('glasses_graduation', __('customer.glasses_graduation') . ' : ') !!}
                    {!! Form::text('glasses_graduation', null, ['class' => 'form-control text-center', 'placeholder' =>
                    __('customer.glasses_graduation')]) !!}
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('address', __('customer.location') . ' : ') !!}
                        @if ($business_locations->count() == 1)
                        {!! Form::select('location_id', $business_locations, null,
                            ['class' => 'form-control select2', 'required', 'readonly']) !!}
                        @else
                        {!! Form::select('location_id', $business_locations, null,
                            ['class' => 'form-control select2', 'required', 'placeholder' => __('messages.please_select')]) !!}
                        @endif
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label>@lang('customer.notes')</label>
                        <textarea name="txt-notes" id="txt-notes" class="form-control"></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group">
                        {!! Form::text('employee_code', null, ['class' => 'form-control text-center', 'placeholder' =>
                        __('customer.employee_code'), 'id' => 'employee_code']) !!}
                        <span class="input-group-btn">
                            <button type="button" onclick="SearchEmployee()" class="btn btn-info btn-flat">Go!</button>
                        </span>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        {!! Form::text('employee_name', null, ['class' => 'form-control text-center', 'readonly', 'id' => 'employee_name']) !!}
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

<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('ManageEmployeesController@update', [$employees->id]), 'method' => 'PUT', 'id'
        => 'employees_edit_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('employees.edit_employees')</h4>
        </div>
        <div class="modal-body">
            <input type="hidden" id="employee_id" value="{{ $employees->id }}">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('first_name', __('employees.first_name') . ' : ') !!}
                        {!! Form::text('first_name', $employees->first_name, ['class' => 'form-control', 'required',
                        'placeholder' => __('employees.first_name')]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('last_name', __('employees.last_name') . ' : ') !!}
                        {!! Form::text('last_name', $employees->last_name, ['class' => 'form-control', 'required',
                        'placeholder' => __('employees.last_name')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('email', __('employees.email') . ' : ') !!}
                        {!! Form::text('email', $employees->email, ['class' => 'form-control', 'required', 'placeholder'
                        => __('employees.email')]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('mobile', __('employees.mobile') . ' : ') !!}
                        {!! Form::text('mobile', $employees->mobile, ['class' => 'form-control', 'placeholder' =>
                        __('employees.mobile')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label("location", __("business.location")) !!}
                        {!! Form::select("location_id", $locations, $employees->location_id, ["class" => "form-control", "required"]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('position_id', __('employees.position') . ' : ') !!}
                        {!! Form::select('position_id', $positions, $employees->position_id, ['class' => 'form-control',
                        'required']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('birth_date', __('employees.birth_date') . ' : ') !!}
                        {!! Form::text('birth_date', @format_date($employees->birth_date), ['class' => 'form-control'])
                        !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('hired_date', __('employees.hired_date') . ' : ') !!}
                        {!! Form::text('hired_date', @format_date($employees->hired_date), ['class' => 'form-control'])
                        !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('fired_date', __('employees.fired_date') . ' : ') !!}
                        {!! Form::text('fired_date', @format_date($employees->fired_date), ['class' => 'form-control'])
                        !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('agent_code', __('customer.employee_code') . ' : ') !!}
                        {!! Form::text('agent_code', $employees->agent_code, ['class' => 'form-control', 'id' => 'agent_code'])
                        !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('short_name', __('employees.short_name') . ' : ') !!}
                        {!! Form::text('short_name', $employees->short_name, ['class' => 'form-control']) !!}
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

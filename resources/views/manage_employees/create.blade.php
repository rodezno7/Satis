<div class="modal-dialog modal-lg" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('ManageEmployeesController@store'), 'method' => 'post', 'id' =>
        'employees_add_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('employees.add_employees')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('first_name', __('employees.first_name') . ' : ') !!}
                        {!! Form::text('first_name', null, ['class' => 'form-control', 'required', 'placeholder' =>
                        __('employees.first_name')]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('last_name', __('employees.last_name') . ' : ') !!}
                        {!! Form::text('last_name', null, ['class' => 'form-control', 'required', 'placeholder' =>
                        __('employees.last_name')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('email', __('employees.email') . ' : ') !!}
                        {!! Form::email('email', null, ['class' => 'form-control', 'required', 'email', 'placeholder' =>
                        __('employees.email')]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('mobile', __('employees.mobile') . ' : ') !!}
                        {!! Form::text('mobile', null, ['class' => 'form-control', 'placeholder' =>
                        __('employees.mobile')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label("location", __("business.location")) !!}
                        {!! Form::select("location_id", $locations, null, ["class" => "form-control", "required"]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('position_id', __('employees.position') . ' : ') !!}
                        {!! Form::select('position_id', $positions, '', ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('birth_date', __('employees.birth_date') . ' : ') !!}
                        {!! Form::text('birth_date', @format_date('now'), ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('hired_date', __('employees.hired_date') . ' : ') !!}
                        {!! Form::text('hired_date', @format_date('now'), ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('fired_date', __('employees.fired_date') . ' : ') !!}
                        {!! Form::text('fired_date', @format_date('now'), ['class' => 'form-control', 'readonly']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('short_name', __('employees.short_name') . ' : ') !!}
                        {!! Form::text('short_name', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-check-input">
                            {{ __('employees.has_user') }} {!! Form::checkbox('chk_has_user', '0', false, ['class' =>
                            'form-check-input', 'id' => 'chk_has_user', 'onClick' => 'showUserOption()']) !!}
                        </label>
                    </div>
                </div>
            </div>
            <div id="user_modal_option" style="display: none">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('username', __('employees.username') . ' : ') !!}
                            {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' =>
                            __('employees.username')]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('role', __('employees.role') . ' : ') !!}
                            {!! Form::select('role', $roles, null, ['id' => 'role', 'class' => 'form-control select2']) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check form-check-inline">
                            <label class="form-check-input">
                                {{ __('employees.pass_manual') }} {!! Form::radio('rdb_pass_mode', '0', true, ['class'
                                => 'form-check-input', 'id' => 'rdb_pass_manual', 'onClick' => 'showPassMode()']) !!}
                                @show_tooltip(__('lang_v1.tooltip_enable_password_manual'))
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-check-inline">
                            <label class="form-check-input">
                                {{ __('employees.pass_auto') }} {!! Form::radio('rdb_pass_mode', 'generated', false,
                                ['class' => 'form-check-input', 'id' => 'rdb_pass_auto', 'onClick' => 'showPassMode()'])
                                !!}
                                @show_tooltip(__('lang_v1.tooltip_enable_password_generated'))
                            </label>
                        </div>
                    </div>
                </div>
                <div id="pass_mode">
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('username', __('business.password') . ' : ') !!}
                                <input id="password" name="password" type="password" class="form-control" ,
                                    placeholder="{{ __('business.password') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('username', __('business.confirm_password') . ' : ') !!}
                                <input id="password_confirm" type="password" class="form-control" ,
                                    placeholder="{{ __('business.confirm_password') }}">
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <br> --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-check-input">
                                {{ __('employees.commission') }} {!! Form::checkbox('commission', '0', false, ['class'
                                => 'form-check-input', 'id' => 'chk_commission', 'onClick' => 'commision_enable()']) !!}
                            </label>
                        </div>
                    </div>
                </div>
                <div id="commision_div" style="display: none">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-percent"></i>
                                </span>
                                {!! Form::number('commision_amount', null, ['class' => 'form-control', 'id' =>
                                'commision_amount', 'placeholder' => __('employees.commision_amount')]) !!}
                            </div>
                        </div>
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

@section('javascript')
    <script type="text/javascript" src="{{ asset('js/sweetalert2.js') }}"></script>
@endsection
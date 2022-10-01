<div class="modal-dialog modal-lg" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('OportunityController@store'), 'method' => 'post', 'id' => 'oportunity_add_form']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('crm.add_oportunity')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('name', __('crm.contact_type') . ' : ') !!}
                        <select name="contact_type" id="contact_type" class="form-control required">
                            <option value='entrante'>@lang('crm.option_in')</option>
                            <option value='saliente'>@lang('crm.option_out')</option>
                            <option value='no_aplica'>@lang('crm.option_none')</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('crm.contact_date')</label>
                        <input type="date" id="contact_date" name="contact_date" class="form-control"
                            value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('name', __('crm.contactreason') . ' : ') !!}
                        {!! Form::select('contact_reason_id', $contactreason, '', ['class' => 'form-control select2',
                        'required', 'id' => 'contact_reason_id']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('name', __('crm.name') . ' : ') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' =>
                        __('crm.name')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('company', __('crm.company') . ' : ') !!}
                        {!! Form::text('company', null, ['class' => 'form-control', 'placeholder' => __('crm.company')])
                        !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('charge', __('crm.position') . ' : ') !!}
                        {!! Form::text('charge', null, ['class' => 'form-control', 'placeholder' => __('crm.position')])
                        !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('email', __('crm.email') . ' : ') !!}
                        {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => __('crm.email')])
                        !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('contacts', __('crm.contacts') . ' : ') !!}
                        {!! Form::text('contacts', null, ['class' => 'form-control', 'placeholder' =>
                        __('crm.contacts')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('knowm_by', __('crm.known_by')) !!}
                        {!! Form::select('known_by', $known_by, '', ['class' => 'form-control select2', 'required',
                        'onchange' => 'getValSel(this)', 'id' => 'known_by']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    {!! Form::label('refered_id', __('crm.refered_by') . ' : ') !!}
                    {!! Form::select('refered_id', [], null, ['class' => 'form-control', 'disabled',
                    'placeholder' => 'Seleccione Cliente', 'id' => 'refered_id']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('contact_mode', __('crm.conctact_mode')) !!}
                        {!! Form::select('contact_mode_id', $contactmode, '', ['class' => 'form-control select2', 'id'
                        => 'contact_mode_id']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('social_user', __('crm.social_user') . ' : ') !!}
                        {!! Form::text('social_user', null, ['class' => 'form-control', 'placeholder' =>
                        __('crm.social_user')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('country', __('geography.country')) !!}
                        {!! Form::select('country_id', $countries, '', ['class' => 'form-control select2', 'id' =>
                        'country_id']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('state', __('geography.state')) !!}
                        <select name="state_id" id="state_id" class="form-control select2">
                            <option value="0" selected disabled>@lang('messages.please_select')</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('city', __('geography.city')) !!}
                        <select name="city_id" id="city_id" class="form-control select2">
                            <option value="0" selected disabled>@lang('messages.please_select')</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('product_cat_id', __('crm.interest') . ' : ') !!}
                        {!! Form::select('product_cat_id', $categories, '', ['class' => 'form-control select2', 'id' =>
                        'product_cat_id']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-check-input">
                            {{ __('crm.not_found') }} {!! Form::checkbox('chk_not_found', '1', false, ['class' =>
                            'form-check-input', 'id' => 'chk_not_found', 'onClick' => 'showNotFoundDesc()']) !!}
                        </label>
                        {!! Form::textarea('products_not_found_desc', null, ['class' => 'form-control', 'id' =>
                        'products_not_found_desc']) !!}
                    </div>
                </div>
                <div class="col-md-6">

                </div>
            </div>
        </div>
        <div class="modal-footer">

            <button type="submit" class="btn btn-primary" id="btn-add-oportunitys">@lang('messages.save')</button>
            <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default"
                id="btn-close-modal-add-oportunity">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

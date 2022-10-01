<div class="modal-dialog modal-lg" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('OportunityController@update', [$oportunity->id]), 'method' => 'PUT', 'id' => 'oportunity_edit_form']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('crm.edit_oportunity')</h4>
        </div>
        <div class="modal-body">
            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('name', __('crm.contact_type') . ' : ') !!}
                        {!! Form::select('contact_type', ['entrante' => __('crm.option_in'), 'saliente' => __('crm.option_out'), 'no_aplica' => __('crm.option_none')], $oportunity->contact_type, ['id' => 'contact_type', 'class' => 'form-control select2', 'required']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('crm.contact_date')</label>
                        <input type="date" id="contact_date" name="contact_date" class="form-control"
                            value="{{ $oportunity->contact_date }}" required>
                    </div>
                </div>


            </div>
            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('name', __('crm.contactreason') . ' : ') !!}
                        {!! Form::select('contact_reason_id', $contactreason, $oportunity->contact_reason_id, ['id' => 'contact_reason_id', 'class' => 'form-control select2', 'required']) !!}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('name', __('crm.name') . ' : ') !!}
                        {!! Form::text('name', $oportunity->name, ['class' => 'form-control', 'required', 'placeholder' => __('crm.name')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('company', __('crm.company') . ' : ') !!}
                        {!! Form::text('company', $oportunity->company, ['class' => 'form-control', 'placeholder' => __('crm.company')]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('charge', __('crm.position') . ' : ') !!}
                        {!! Form::text('charge', $oportunity->charge, ['class' => 'form-control', 'placeholder' => __('crm.position')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('email', __('crm.email') . ' : ') !!}
                        {!! Form::text('email', $oportunity->email, ['class' => 'form-control', 'placeholder' => __('crm.email')]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('contacts', __('crm.contacts') . ' : ') !!}
                        {!! Form::text('contacts', $oportunity->contacts, ['class' => 'form-control', 'placeholder' => __('crm.contacts')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('known_by', __('crm.known_by')) !!}
                        {!! Form::select('known_by', $known_by, $oportunity->known_by, ['id' => 'known_by', 'required', 'class' => 'form-control select2', 'onchange' => 'getValSel(this)']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    {!! Form::label('refered_id', __('crm.refered_by') . ' : ') !!}

                    {!! Form::select('refered_id', $refered_by, $oportunity->refered_id, ['class' => 'form-control select2', 'disabled', 'placeholder' => 'Seleccione Cliente', 'id' => 'refered_id']) !!}
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('contact_mode', __('crm.conctact_mode')) !!}
                        {!! Form::select('contact_mode_id', $contactmode, $oportunity->contact_mode_id, ['class' => 'form-control select2', 'id' => 'contact_mode_id']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('social_user', __('crm.social_user') . ' : ') !!}
                        {!! Form::text('social_user', $oportunity->social_user, ['class' => 'form-control', 'placeholder' => __('crm.social_user')]) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('country', __('geography.country')) !!}
                        {!! Form::select('country_id', $countries, $oportunity->country_id, [
    'class' => 'form-control
                        select2',
    'id' => 'country_id',
]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('state', __('geography.state')) !!}
                        {!! Form::select('state_id', $states, $oportunity->state_id, [
    'class' => 'form-control
                        select2',
    'id' => 'state_id',
    'placeholder' => __('messages.please_select'),
]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('city_id', __('geography.city')) !!}
                        {!! Form::select('city_id', $cities, $oportunity->city_id, [
    'class' => 'form-control
                        select2',
    'id' => 'city_id',
    'placeholder' => __('messages.please_select'),
]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    @if ($oportunity->product_not_found == 1)
                        <div class="form-group">
                            {!! Form::label(__('crm.interest') . ' : ') !!}
                            {!! Form::select('eproduct_cat_id', $categories, $oportunity->product_cat_id, ['class' => 'form-control select2', 'id' => 'eproduct_cat_id', 'disabled']) !!}
                        </div>
                    @else
                        <div class="form-group">
                            {!! Form::label(__('crm.interest') . ' : ') !!}
                            {!! Form::select('eproduct_cat_id', $categories, $oportunity->product_cat_id, ['id' => 'eproduct_cat_id', 'class' => 'form-control select2']) !!}
                        </div>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    {{-- @if ($oportunity->product_not_found == 1)
                        <div class="form-group">
                            <label class="form-check-input">
                                {{ __('crm.not_found') }} {!! Form::checkbox('echk_not_found', '1', true, ['class' =>
                                'form-check-input', 'id' => 'echk_not_found', 'onClick' => 'eshowNotFoundDesc()']) !!}
                            </label>
                            {!! Form::textarea('eproducts_not_found_desc', $oportunity->products_not_found_desc,
                            ['class' => 'form-control', 'id' => 'eproducts_not_found_desc']) !!}
                        </div>
                    @else
                        <div class="form-group">
                            <label class="form-check-input">
                                {{ __('crm.not_found') }} {!! Form::checkbox('echk_not_found', '1', false, ['class' =>
                                'form-check-input', 'id' => 'echk_not_found', 'onClick' => 'eshowNotFoundDesc()']) !!}
                            </label>
                            {!! Form::textarea('eproducts_not_found_desc', $oportunity->products_not_found_desc, [
                            'class' => 'form-control',
                            'id' => 'eproducts_not_found_desc',
                            'style' => 'display:
                            none;',
                            ]) !!}
                        </div>
                    @endif --}}
                    @php
                        $show = $oportunity->product_not_found == 1 ? "display:show;" : "display:none;";
                    @endphp
                    <div class="form-group">
                        <label class="form-check-input">
                            {{ __('crm.not_found') }} {!! Form::checkbox('echk_not_found', '1', $oportunity->product_not_found, ['class' => 'form-check-input', 'id' => 'echk_not_found', 'onClick' => 'eshowNotFoundDesc()']) !!}
                        </label>
                        {!! Form::textarea('eproducts_not_found_desc', $oportunity->products_not_found_desc, [
                                'class' => 'form-control', 'id' => 'eproducts_not_found_desc','style' => $show]) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="btn-edit-oportunity">@lang('messages.save')</button>
            <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default"
                id="btn-close-modal-edit-oportunity">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

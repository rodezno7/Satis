<div class="modal-dialog" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('CRMContactReasonController@store'), 'method' => 'post', 'id' => 'contactreason_add_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('crm.add_contactreason')</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __('crm.name') . ' : ') !!}
                <div class="wrap-inputform">
                    {!! Form::text('name', null, ['class' => 'inputform2', 'required', 'placeholder' => __('crm.name')]); !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('description', __('crm.description') . ' : ') !!}
                <div class="wrap-inputform">
                    {!! Form::text('description', null, ['class' => 'inputform2', 'placeholder' => __('crm.description')]); !!}
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
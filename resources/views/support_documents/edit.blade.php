<div class="modal-dialog" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('SupportDocumentsController@update', [$sdocs->id]), 'method' => 'PUT', 'id' =>
        'sdocs_edit_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('customer.add_sdocs')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('name', __('customer.name') . ' : ') !!}
                        {!! Form::text('name', $sdocs->name, ['class' => 'form-control', 'required', 'placeholder' =>
                        __('crm.name')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('description', __('customer.description') . ' : ') !!}
                        {!! Form::text('description', $sdocs->description, ['class' => 'form-control', 'placeholder' =>
                        __('crm.description')]) !!}
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

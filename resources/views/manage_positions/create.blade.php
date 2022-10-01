<div class="modal-dialog" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('ManagePositionsController@store'), 'method' => 'post', 'id' =>
        'positions_add_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('positions.add_positions')</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __('positions.name') . ' : ') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' =>
                __('positions.name')]) !!}
            </div>
            <div class="form-group">
                {!! Form::label('descriptions', __('positions.description') . ' : ') !!}
                {!! Form::text('descriptions', null, ['class' => 'form-control', 'placeholder' =>
                __('positions.description')]) !!}
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

<div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('ManagePositionsController@update', [$positions->id]), 'method' => 'PUT', 'id'
        => 'positions_edit_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('positions.add_positions')</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __('positions.name') . ' : ') !!}
                {!! Form::text('name', $positions->name, ['class' => 'form-control', 'required', 'placeholder' =>
                __('positions.name')]) !!}
            </div>
            <div class="form-group">
                {!! Form::label('descriptions', __('positions.description') . ' : ') !!}
                {!! Form::text('descriptions', $positions->descriptions, ['class' => 'form-control', 'placeholder' =>
                __('positions.description')]) !!}
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.update')</button>
            <button type="button" data-dismiss="modal" aria-label="Close"
                class="btn btn-default">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

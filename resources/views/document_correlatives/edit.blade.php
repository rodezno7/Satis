<div class="modal-dialog modal-lg" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('DocumentCorrelativeController@update', [$correlatives->id]), 'method' => 'PUT', 'id' => 'correlatives_edit_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('correlatives.add_correlatives')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('location_id', __('correlatives.business_location') . ' : ') !!}
                        <div class="wrap-inputform">
                            {!! Form::select('location_id', $locations, $correlatives->location_id, ['class' => 'inputform2', 'required']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('document_type_id', __('correlatives.documenttype') . ' : ') !!}
                        <div class="wrap-inputform">
                            {!! Form::select('document_type_id', $documentstypes, $correlatives->document_type_id, ['class' => 'inputform2', 'required']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('serie', __('correlatives.serie') . ' : ') !!}
                        <div class="wrap-inputform">
                            {!! Form::text('serie', $correlatives->serie, ['class' => 'inputform2', 'placeholder' => __('correlatives.serie')]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('resolution', __('correlatives.resolution') . ' : ') !!}
                        <div class="wrap-inputform">
                            {!! Form::text('resolution', $correlatives->resolution, ['class' => 'inputform2', 'placeholder' => __('correlatives.resolution')]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('initial', __('correlatives.initial') . ' : ') !!}
                        <div class="wrap-inputform">
                            {!! Form::number('initial', $correlatives->initial, ['class' => 'inputform2', 'required', 'placeholder' => __('correlatives.initial')]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('final', __('correlatives.final') . ' : ') !!}
                        <div class="wrap-inputform">
                            {!! Form::number('final', $correlatives->final, ['class' => 'inputform2', 'required', 'placeholder' => __('correlatives.final')]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('actual', __('correlatives.actual') . ' : ') !!}
                        <div class="wrap-inputform">
                            {!! Form::number('actual', $correlatives->actual, ['class' => 'inputform2', 'required', 'placeholder' => __('correlatives.actual')]) !!}
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

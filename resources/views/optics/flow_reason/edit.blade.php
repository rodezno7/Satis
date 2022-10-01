<div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('Optics\FlowReasonController@update',
            [$flow_reason->id]), 'method' => 'PUT', 'id' => 'flow_reasons_edit_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('flow_reason.edit_flow_reason')</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                {{-- reason --}}
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('reason', __('inflow_outflow.reason') . ':*') !!}
                        {!! Form::text('reason', $flow_reason->reason,
                            ['class' => 'form-control', 'required', 'placeholder' => __('inflow_outflow.reason')]) !!}
                    </div>
                </div>

                {{-- description --}}
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('description', __('lang_v1.description') . ':') !!}
                        {!! Form::textarea('description', $flow_reason->description,
                            ['class' => 'form-control', 'placeholder' => __('lang_v1.description'), 'rows' => '3']) !!}
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

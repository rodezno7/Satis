<div class="modal-dialog" role="dialog" id="modal-create">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('Optics\FlowReasonController@store'),
            'method' => 'POST', 'id' => 'flow_reason_add_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('flow_reason.add_flow_reason')</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                {{-- reason --}}
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('reason', __('inflow_outflow.reason') . ':*') !!}
                        {!! Form::text('reason', null,
                            ['class' => 'form-control', 'required', 'placeholder' => __('inflow_outflow.reason')]) !!}
                    </div>
                </div>

                {{-- descripction --}}
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('description', __('lang_v1.description') . ':') !!}
                        {!! Form::textarea('description', null,
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

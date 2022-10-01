<div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('Optics\InflowOutflowController@update',
            [$inflow_outflow->id]), 'method' => 'PUT', 'id' => 'inflow_outflows_edit_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('inflow_outflow.edit_inflow_outflow', ['type' => __('inflow_outflow.' . $inflow_outflow->type)])</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                {{-- reason --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('reason', __('inflow_outflow.reason') . ':*') !!}
                        {!! Form::select('flow_reason_id', $flow_reasons, $inflow_outflow->flow_reason_id,
                            ['class' => 'form-control select2', 'required', 'placeholder' => __('messages.please_select')]) !!}
                    </div>
                </div>

                {{-- amount --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('amount', __('inflow_outflow.amount') . ':*') !!}
                        {!! Form::text('amount', $inflow_outflow->amount,
                            ['class' => 'form-control input_number', 'required', 'placeholder' => __('inflow_outflow.amount')]) !!}
                    </div>
                </div>

                {{-- employee_id --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('employee_id', __('inflow_outflow.responsible') . ':*') !!}
                        {!! Form::select('employee_id',
                            [$inflow_outflow->employee_id => $inflow_outflow->employee->first_name . ' ' . $inflow_outflow->employee->last_name],
                            $inflow_outflow->employee_id,
                            ['class' => 'form-control select2', 'required', 'placeholder' => __('messages.please_select')]) !!}
                    </div>
                </div>

                @if ($inflow_outflow->type == 'output')
                {{-- supplier_id --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('supplier_id', __('inflow_outflow.supplier') . ':') !!}
                        {!! Form::select('supplier_id',
                            ! empty($inflow_outflow->supplier_id) ? [$inflow_outflow->supplier_id => $inflow_outflow->contact->name] : [],
                            $inflow_outflow->supplier_id,
                            ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]) !!}
                    </div>
                </div>
                
                {{-- document_type_id --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('document_type_id', __('inflow_outflow.document_type') . ':') !!}
                        {!! Form::select('document_type_id', $document_types, $inflow_outflow->document_type_id,
                            ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]) !!}
                    </div>
                </div>

                {{-- document_no --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('document_no', __('inflow_outflow.document_no') . ':') !!}
                        {!! Form::text('document_no', $inflow_outflow->document_no,
                            ['class' => 'form-control input_number', 'placeholder' => __('inflow_outflow.document_no')]) !!}
                    </div>
                </div>
                @endif
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

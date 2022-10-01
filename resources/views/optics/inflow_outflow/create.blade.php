<div class="modal-dialog" role="dialog" id="modal-create">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('Optics\InflowOutflowController@store'),
            'method' => 'post', 'id' => 'inflow_outflow_add_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('inflow_outflow.add_inflow_outflow', ['type' => __('inflow_outflow.' . $type)])</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                {{-- type --}}
                {!! Form::hidden('type', $type) !!}

                {{-- cashier_id --}}
                {!! Form::hidden('cashier_id', $cashier_id) !!}

                {{-- location_id --}}
                {!! Form::hidden('location_id', $location_id) !!}
                
                @if ($type == 'input')
                {{-- flow_reason_id --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('flow_reason_id', __('inflow_outflow.reason') . ':*') !!}
                        {!! Form::select('flow_reason_id', $flow_reasons, '',
                            ['class' => 'form-control select2', 'required', 'placeholder' => __('messages.please_select')]) !!}
                    </div>
                </div>
                @else
                {{-- expense_category_id --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('expense_category_id', __('inflow_outflow.reason') . ':*') !!}
                        {!! Form::select('expense_category_id', $expense_categories, '',
                            ['class' => 'form-control select2', 'required', 'placeholder' => __('messages.please_select')]) !!}
                    </div>
                </div>
                @endif

                {{-- amount --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('amount', __('inflow_outflow.amount') . ':*') !!}
                        {!! Form::text('amount', null,
                            ['class' => 'form-control input_number', 'required', 'placeholder' => __('inflow_outflow.amount')]) !!}
                    </div>
                </div>

                {{-- employee_id --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('employee_id', __('inflow_outflow.responsible') . ':*') !!}
                        {!! Form::select('employee_id', [], '',
                            ['class' => 'form-control select2', 'required', 'placeholder' => __('messages.please_select')]) !!}
                    </div>
                </div>

                @if ($type == 'output')
                {{-- supplier_id --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('supplier_id', __('inflow_outflow.supplier') . ':') !!}
                        {!! Form::select('supplier_id', [], '',
                            ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]) !!}
                    </div>
                </div>

                {{-- document_type_id --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('document_type_id', __('inflow_outflow.document_type') . ':') !!}
                        {!! Form::select('document_type_id', $document_types, '',
                            ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]) !!}
                    </div>
                </div>

                {{-- document_no --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('document_no', __('inflow_outflow.document_no') . ':') !!}
                        {!! Form::text('document_no', null,
                            ['class' => 'form-control input_number', 'placeholder' => __('inflow_outflow.document_no')]) !!}
                    </div>
                </div>
                @endif

                {{-- description --}}
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('description', __('accounting.description') . ':') !!}
                        {!! Form::textarea('description', null,
                            ['class' => 'form-control', 'placeholder' => __('accounting.description'), 'rows' => 2]) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" data-dismiss="modal" aria-label="Close"
                class="btn btn-default">@lang('messages.close')
            </button>
        </div>

        {!! Form::close() !!}
    </div>
</div>

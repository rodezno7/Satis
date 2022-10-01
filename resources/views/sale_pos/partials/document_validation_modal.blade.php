<div class="modal fade" tabindex="-1" role="dialog" id="document_validation_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">@lang('sale.additional_information')</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    {{-- delivered_by --}}
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('delivered_by', __('sale.delivered_by') . ':*') !!}

                            {!! Form::text('delivered_by', null,
                                ['id' => 'delivered_by', 'class' => 'form-control', 'placeholder' => __('accounting.name'), 'style' => 'margin-bottom: 5px;']) !!}

                            {!! Form::text('delivered_by_dui', null,
                                ['id' => 'delivered_by_dui', 'class' => 'form-control dui-mask', 'placeholder' => __('business.dui')]) !!}

                            {!! Form::text('delivered_by_passport', null,
                                ['id' => 'delivered_by_passport', 'class' => 'form-control', 'placeholder' => __('sale.passport_residence_card'), 'style' => 'display: none;']) !!}
                        </div>
                    </div>

                    {{-- received_by --}}
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('received_by', __('sale.received_by') . ':*') !!}

                            {!! Form::text('received_by', null,
                                ['id' => 'received_by', 'class' => 'form-control', 'placeholder' => __('accounting.name'), 'style' => 'margin-bottom: 5px;']) !!}

                            {!! Form::text('received_by_dui', null,
                                ['id' => 'received_by_dui', 'class' => 'form-control dui-mask', 'placeholder' => __('business.dui')]) !!}
                        </div>
                    </div>

                    {{-- check_foreigner --}}
                    <div class="col-sm-6">
                        <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="check_foreign" value="1">
                                <strong>@lang('sale.is_foreign')</strong>
                            </label>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                {{-- accept --}}
                <button type="button" class="btn btn-primary" id="btn-accept" disabled>
                    @lang('messages.accept')
                </button>

                {{-- close --}}
                <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default">
                    @lang('messages.close')
                </button>
            </div>
        </div>
    </div>
</div>
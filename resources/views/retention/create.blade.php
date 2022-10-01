<div class="modal-dialog modal-lg" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open([
            'url' => action('RetentionController@store'),
            'method' => 'post',
            'id' => 'retention_add_form'
        ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('retention.add_retention')</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                {{-- transaction_date --}}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('transaction_date', __('accounting.date') . ':') !!}
                        <span style="color: red">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('transaction_date', @format_date('now'),
                                ['class' => 'form-control datepicker1', 'readonly', 'required']) !!}
                        </div>
                    </div>
                </div>

                {{-- document_date --}}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('document_date', __('retention.document_date') . ':') !!}
                        <span style="color: red">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('document_date', @format_date('now'),
                                ['class' => 'form-control datepicker1', 'readonly', 'required']) !!}
                        </div>
                    </div>
                </div>

                {{-- customer_id --}}
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('customer_id', __('contact.customer') . ':') !!}
                        <span style="color: red">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </span>
                            {!! Form::select('customer_id', [], null,
                                ['id' => 'customer_id', 'class' => 'form-control mousetrap', 'placeholder' => __('messages.please_select'), 'required']) !!}
                        </div>
                    </div>
                </div>

                {{-- retention_type --}}
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('retention_type', __('accounting.type') . ':') !!}
                        <span style="color: red">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-file-text-o"></i>
                            </span>
                            {!! Form::select('retention_type', [
                                    'fcf' => __('retention.fcf'),
                                    'ccf' => __('retention.ccf')
                                ], null,
                                ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']) !!}
                        </div>
                    </div>
                </div>

                {{-- ref_no --}}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('ref_no', __('accounting.document_no') . ':') !!}
                        <span style="color: red">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-hashtag"></i>
                            </span>
                            {!! Form::text('ref_no', null,
                                ['class' => 'form-control', 'placeholder' => __('accounting.document_no'), 'required']) !!}
                        </div>
                    </div>
                </div>

                {{-- serie --}}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('serie', __('accounting.serie') . ':') !!}
                        <span style="color: red">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-hashtag"></i>
                            </span>
                            {!! Form::text('serie', null,
                                ['class' => 'form-control', 'required', 'placeholder' => __('accounting.serie')]) !!}
                        </div>
                    </div>
                </div>

                {{-- additional_notes --}}
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('additional_notes', __('lang_v1.description') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-edit"></i>
                            </span>
                            {!! Form::text('additional_notes', null,
                                ['class' => 'form-control', 'placeholder' => __('lang_v1.description')]) !!}
                        </div>
                    </div>
                </div>

                {{-- final_total --}}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('final_total', __('accounting.amount') . ':') !!}
                        <span style="color: red">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::text('final_total', null,
                                ['class' => 'form-control input_number', 'required', 'placeholder' => __('accounting.amount')]) !!}
                        </div>
                    </div>
                </div>

                {{-- retention --}}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('retention', __('retention.retention') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::text('retention', null,
                                ['class' => 'form-control input_number', 'required', 'readonly', 'placeholder' => __('retention.retention')]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">
                @lang('messages.save')
            </button>

            <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default">
                @lang('messages.close')
            </button>
        </div>

        {!! Form::close() !!}
    </div>
</div>

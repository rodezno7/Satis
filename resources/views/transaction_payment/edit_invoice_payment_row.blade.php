<div class="modal-dialog modal-md" role="dialog" id="modal_edit_invoice">
    <div class="modal-content">

        {!! Form::open(['url' => action('TransactionPaymentController@update', [$payment_line->id]), 'method' => 'put', 'id' => 'transaction_payment_add_form', 'files' => true]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'Editar método de pago' )</h4>
        </div>

        <div class="modal-body">
            <div class="row payment_row">
                <div class=" col-md-4" >
                <div class="form-group">
                    {!! Form::label('amount', __('payment.amount')) !!}:
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-money"></i>
                        </span>
                        {!! Form::text('amount', @num_format($payment_line->amount), ['class' => 'form-control input_number', 'required', 'placeholder' => 'Amount', 'readonly']) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4" >
                <div class="form-group">
                    {!! Form::label('paid_on', __('payment.paid_on')) !!}:
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! Form::text('paid_on', date('m/d/Y', strtotime($payment_line->paid_on)), ['class' => 'form-control', 'readonly', 'required', 'id'=>'ignore']) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                {!! Form::label('method', __('payment.payment_method')) !!}:<span style="color: red;"><small>*</small></span>
                {!! Form::select('method', $payment_types, $payment_line->method, ['class' => 'form-control select2 payment_types_dropdown', 'required', 'style' => 'width:100%;']) !!}
            </div>
            @if (!empty($accounts))
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('account_id', __('lang_v1.payment_account') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::select('account_id', $accounts, !empty($payment_line->account_id) ? $payment_line->account_id : '', ['class' => 'form-control select2', 'id' => 'account_id', 'style' => 'width:100%;']) !!}
                        </div>
                    </div>
                </div>
            @endif

            <div class="clearfix"></div>
            @include('transaction_payment.payment_type_details')
            <div class="col-md-12" style="display: none;">
                <div class="form-group">
                    {!! Form::label('note', 'Payment Note:') !!}
                    {!! Form::textarea('note', $payment_line->note, ['class' => 'form-control', 'rows' => 3]) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal"  id="cerrar_modal_i">@lang( 'messages.close'
            )</button>
    </div>

    {!! Form::close() !!}

</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

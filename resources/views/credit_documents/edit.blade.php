<div class="modal-dialog modal-lg" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {{-- {!! Form::model(['url' => [action('CreditDocumentsController@update'), $credit_document->id], 'method' => 'post', 'id' => 'cdocs_add_form']) !!}
        --}}
        <form id="form-edit-credit-documents">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('cxc.add_cdocs')</h4>
        </div>
        <div class="modal-body">
            <h3 class="text-center">@lang('cxc.sale_info')</h3>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('document_types', __('cxc.doctypes') . ':') !!}
                        {!! Form::select('document_types', $document_types, $credit_document->document_types_id , ['class' => 'form-control select2',
                        'required', 'id' => 'document_types']) !!}
                    </div>
                    <input type="hidden" id="credit_document_id" name="credit_document_id" value="{{ $credit_document->id}}">
                    <input type="hidden" name="token" id="token" value="{{ csrf_token() }}">
                </div>
                <div class="col-md-4">
                    {!! Form::label('invoice', __('cxc.invoice') . ':') !!}
                    <div class="input-group">
                        {!! Form::text('invoice', $credit_document->correlative, ['class' => 'form-control text-center', 'required', 'id' => 'invoice']) !!}
                        <input type="hidden" name="transaction_id" value="{{ $credit_document->transaction_id }}" id="transaction_id">
                        <span class="input-group-btn">
                            <button type="button" onclick="SearchInvoice()"
                                class="btn btn-info btn-flat">@lang('cxc.search')</button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    {!! Form::label('date', __('cxc.date') . ':') !!}
                    {!! Form::text('date', $credit_document->transaction_date, ['class' => 'form-control text-center', 'disabled', 'id' => 'date'])
                    !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('amount', __('cxc.amount') . ':') !!}
                    {!! Form::text('amount', $credit_document->final_total, ['class' => 'form-control text-center', 'disabled', 'id' =>
                    'amount']) !!}
                </div>
                <div class="col-md-6">
                    {!! Form::label('customer', __('cxc.customer') . ':') !!}
                    {!! Form::text('customer', $credit_document->name, ['class' => 'form-control text-center', 'disabled', 'id' =>
                    'customer']) !!}
                </div>
            </div><br>
            <h3 class="text-center">@lang('cxc.send_info')</h3>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('reason_id', __('cxc.reason') . ':') !!}
                        {!! Form::select('reason_id', $supprt_docs, $credit_document->reason_id, ['class' => 'form-control select2',
                        'required', 'id' => 'reason_id']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('courier_id', __('cxc.courier') . ':') !!}
                        {!! Form::select('courier_id', $employees, $credit_document->courier_id, ['class' => 'form-control select2',
                        'required', 'id' => 'courier_id']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" id="btn-edit-credit-documents" class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" id="btn-close-modal-edit-credit-d" data-dismiss="modal" aria-label="Close"
                class="btn btn-danger">@lang('messages.close')</button>
        </div>
        </form>
    </div>
</div>

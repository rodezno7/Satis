<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        {!! Form::open(['url' => action('CreditDocumentsController@saveReception', [$cdocs->id]), 'method' => 'POST', 'id' => 'cdocs_reception_form']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('cxc.add_reception_form')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        {!! Form::label('document_type_received', __('cxc.doctypes') . ':') !!}
                        {!! Form::select('document_type_received', $supprt_docs, $cdocs->document_type_received, ['class' => 'form-control select2',
                        'required', 'id' => 'document_type_received']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    {!! Form::label('document_number', __('cxc.invoice') . ':') !!}
                    {!! Form::text('document_number', $cdocs->document_number, ['class' => 'form-control text-center', 'required', 'id' =>
                    'document_number']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        {!! Form::label('reception_user_id', __('cxc.reception_user') . ':') !!}
                        {!! Form::select('reception_user_id', $employees, $user_id, ['class' => 'form-control select2',
                        'required', 'id' => 'reception_user_id']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" data-dismiss="modal" aria-label="Close"
                class="btn btn-danger">@lang('messages.close')</button>
        </div>
    </div>
</div>
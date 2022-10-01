<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        {!! Form::open(['url' => action('CreditDocumentsController@saveCustodian', [$cdocs->id]), 'method' => 'POST', 'id' => 'cdocs_custodian_form']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('cxc.add_custodian_form')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    {!! Form::label('custodian_id', __('cxc.doctypes') . ':') !!}
                        {!! Form::select('custodian_id', $custodians, $cdocs->custodian_id, ['class' => 'form-control select2',
                        'required', 'id' => 'custodian_id']) !!}
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
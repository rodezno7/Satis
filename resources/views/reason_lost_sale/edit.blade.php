<div class="modal-dialog modal-md modal-dialog-centered" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('ReasonController@update', [$reason->id]), 'method' => 'PUT', 'id' => 'reason_add_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('quote.edit_reason')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('name', __('quote.reason')) !!} <span style="color: red;"><small>*</small></span>
                        {!! Form::text('reason', $reason->reason, ['class' => 'form-control', 'required', 'placeholder' =>
                        __('Motivo')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('comment', __('quote.description')) !!} <span style="color: red;"><small>*</small></span>
                        <textarea name="comments" style="max-width: 100%" class="form-control" required
                            placeholder="Descripción">{{ $reason->comments }}</textarea>
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
<script>
    $(document).ready(function(){
        $('select.select2').off().select2();
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    });
</script>
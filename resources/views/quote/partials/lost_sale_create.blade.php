<div class="modal-dialog modal-md modal-dialog-centered" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('QuoteController@storeLostSale'), 'method' => 'post', 'id' => 'lost_sale_add_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('quote.register_lost_sale')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <input type="hidden" name="quote_id" value="{{ $quote_id }}">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('reason', __('quote.reason')) !!} <span style="color: red;"><small>*</small></span>
                        {!! Form::select('reason_id', $reasons, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'required', 'placeholder' => __('messages.please_select')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('comment', __('quote.comments')) !!} <span style="color: red;"><small>*</small></span>
                        <textarea name="comments" style="max-width: 100%" class="form-control" required
                            placeholder="Explicación"></textarea>
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
    $(document).ready(function() {
        $('select.select2').off().select2();
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};

        $('input#_date').datetimepicker({
            format: moment_date_format + ' '+ moment_time_format,
            ignoreReadonly: true
        });
    });
    

</script>

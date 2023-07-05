{!! Form::open(['method' => 'post', 'id' => 'form_edit','files' => true ]) !!}
<div class="modal-header">
    <h4 class="modal-title" id="formModal">{{ __('rrhh.confirm_authorization') }}
		<button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
			<span aria-hidden="true">&times;</span>
		</button>
	</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="swal2-icon swal2-question swal2-icon-show" style="display: flex;"><div class="swal2-icon-content">?</div></div>

            <h4 style="text-align: center;">{{ __('rrhh.message_to_confirm_authorization') }}</h4>
            <div class="form-group py-3">
                {!! Form::label('password',__('business.password')) !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-lock"></i>
                    </span>
                    {!! Form::password('password', ['class' => 'form-control','placeholder' => 'Same as Login Password']); !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
	<input type="hidden" name="id" value="{{ $personnelAction->id }}" id="id">
	<button type="button" class="btn btn-primary" id="btn_edit_document">@lang('rrhh.update')</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal" onClick="closeModal()">@lang( 'messages.cancel')</button>
</div>
{!! Form::close() !!}
<script>
    function closeModal(){
		$('#document_modal').modal({backdrop: 'static'});
		$('#modal_doc').modal( 'hide' ).data( 'bs.modal', null );
	}
</script>
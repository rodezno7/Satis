{!! Form::open(['method' => 'post', 'id' => 'form_add_document','files' => true ]) !!}
<div class="modal-header">
	<h4 class="modal-title" id="formModal">@lang('rrhh.file')
		<button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
			<span aria-hidden="true">&times;</span>
		</button>
	</h4>
</div>
<div class="modal-body">
	<div class="row">
		<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label>@lang('rrhh.file')</label> <span class="text-danger">*</span>
				<input type="file" name="file" id='file' class="form-control form-control-sm" accept="application/pdf">
			</div>
		</div>
	</div>
</div>

<div class="modal-footer">
	<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
    <input type="hidden" name="id" value="{{ $contract->id }}" id="id">
    <input type="hidden" name="employee_id" value="{{ $employee_id }}" id="employee_id">
	<button type="button" class="btn btn-primary" id="btn_add_document">@lang('rrhh.add')</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal" onClick="closeModal()">
		@lang('messages.cancel')
	</button>
</div>
{!! Form::close() !!}

<script>
	$( document ).ready(function() {
		$.fn.modal.Constructor.prototype.enforceFocus = function() {};
		select2 = $('.select2').select2();
	});


	validExt = ['pdf'];

	$('#file').on('change', function() {
		extension = this.files[0].type.split('/')[1];

		if(validExt.indexOf(extension) == -1){
			$('#file').val('');
			Swal.fire
			({
				title: '@lang('rrhh.only_pdf')',
				icon: "error",
			});
		} else {
			size = this.files[0].size;
			if(size > 5242880) {

				$('#file').val('');
				Swal.fire
				({
					title: '@lang('rrhh.bad_size_img')',
					icon: "error",
				});
			}
		}
	});

	$("#btn_add_document").click(function() {
		route = "/rrhh-contracts-storeDocument";    
		token = $("#token").val();

		var form = $("#form_add_document");
		var formData = new FormData(form[0]);
		
		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': token},
			type: 'POST',
			processData: false,
			contentType: false,       
			data: formData,
			success:function(result) {
				if(result.success == true) {
					getContract($('#employee_id').val());
                    Swal.fire({
                        title: result.msg,
                        icon: "success",
                        timer: 1000,
                        showConfirmButton: false,
                    });
                    $('#modal_edit_action').modal('hide').data('bs.modal', null);
				}
				else {
					Swal.fire
					({
						title: result.msg,
						icon: "error",
					});
				}
			},
			error:function(msj){
				errormessages = "";
				$.each(msj.responseJSON.errors, function(i, field){
					errormessages+="<li>"+field+"</li>";
				});
				Swal.fire
				({
					title: "@lang('rrhh.error_list')",
					icon: "error",
					html: "<ul>"+ errormessages+ "</ul>",
				});
			}
		});
  	});

	function closeModal() {
        $('#modal_action').modal({ backdrop: 'static' });
        $('#modal_edit_action').modal('hide').data('bs.modal', null);
    }
</script>
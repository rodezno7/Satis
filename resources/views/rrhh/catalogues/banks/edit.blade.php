<div class="modal-header">
	<h4 class="modal-title" id="formModal">@lang('rrhh.edit') @lang('rrhh.bank')
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</h4>
</div>
<div class="modal-body">
	<form id="form_edit" method="post">

		<div class="form-group">
			<label>@lang('rrhh.name')</label>
			<input type="text" name='name' id='name' class="form-control" value='{{ $item->name }}' placeholder="@lang('rrhh.name')">
			<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
			
		</div>

	</form>	
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btn_edit_bank">@lang('rrhh.update')</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal">@lang( 'messages.cancel' )</button>
</div>

<script>

	$("#btn_edit_bank").click(function() {
		$("#btn_edit_bank").prop("disabled", true);
		id = {{ $item->id }}
		route = "/rrhh-banks/"+id;
		datastring = $("#form_edit").serialize();
		token = $("#token").val();
		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': token},
			type: 'PUT',
			dataType: 'json',
			data: datastring,
			success:function(result){
				if(result.success == true) {
					$("#btn_edit_bank").prop("disabled", false);
					Swal.fire
					({
						title: result.msg,
						icon: "success",
						timer: 2000,
						showConfirmButton: false,
					});
					$("#banks-table").DataTable().ajax.reload(null, false);
					$('#modal').modal('hide');

				} else {
					$("#btn_edit_bank").prop("disabled", false);
					Swal.fire
					({
						title: result.msg,
						icon: "error",
					});
				}
			},
			error:function(msj){
				$("#btn_edit_bank").prop("disabled", false);
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

</script>
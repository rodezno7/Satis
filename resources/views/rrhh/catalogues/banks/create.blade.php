<div class="modal-header">
	<h3 class="modal-title" id="formModal">@lang('rrhh.add') @lang('rrhh.bank')</h3>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
	<form id="form_add" method="post">

		<div class="form-group">
			<label>@lang('rrhh.name')</label>
			<input type="text" name='name' id='name' class="form-control" placeholder="@lang('rrhh.name')">
			<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
		</div>

	</form>	
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btn_add_bank">@lang('rrhh.add')</button>
</div>

<script>

	$("#btn_add_bank").click(function() {
		$("#btn_add_bank").prop("disabled", true);
		route = "/rrhh-banks";
		datastring = $("#form_add").serialize();
		token = $("#token").val();
		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': token},
			type: 'POST',
			dataType: 'json',
			data: datastring,
			success:function(result){
				if(result.success == true) {
					$("#btn_add_bank").prop("disabled", false);
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
					$("#btn_add_bank").prop("disabled", false);
					Swal.fire
					({
						title: result.msg,
						icon: "error",
					});
				}
			},
			error:function(msj){
				$("#btn_add_bank").prop("disabled", false);
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
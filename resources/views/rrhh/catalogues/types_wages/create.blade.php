<div class="modal-header">
	<h4 class="modal-title" id="formModal">@lang('rrhh.types_wages')
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</h4>
</div>
<div class="modal-body">
	<form id="form_add" method="post">
		<div class="row">
			<div class="col-sm-12">
				<div class="form-group">
					<label>@lang('rrhh.name')</label>
					<input type="text" name='name' id='name' class="form-control" placeholder="@lang('rrhh.name')">
					<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
				</div>
			</div>
			<div class="col-md-6">
				<label for="">{{ __('rrhh.salary_applies') }}</label>
				<div class="form-group">
					<label>
						<input type="checkbox" name='isss' id='isss'>
						ISSS
					</label>
					<br>
					<label>
						<input type="checkbox" name='afp' id='afp'>
						AFP
					</label>
				</div>
			</div>
			<div class="col-md-6">
				<label for="">{{ __('rrhh.type') }}</label>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="wage_law" id="wage_law">
					<label class="form-check-label" for="wage_law">
					{{ __('rrhh.wage_law') }}
					</label>
				  </div>
				  <div class="form-check">
					<input class="form-check-input" type="radio" name="honorary" id="honorary" checked>
					<label class="form-check-label" for="honorary">
					  {{ __('rrhh.honorary') }}
					</label>
				</div>
			</div>
		</div>
	</form>	
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btn_add_type_wages">@lang('rrhh.add')</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal">@lang( 'messages.cancel' )</button>
</div>

<script>
	$("#wage_law").click(function() {
		$("input#honorary").prop('checked', false);
	});

	$("#honorary").click(function() {
		$("input#wage_law").prop('checked', false);
	});

	$("#btn_add_type_wages").click(function() {
		route = "/rrhh-type-wages";
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
					Swal.fire
					({
						title: result.msg,
						icon: "success",
						timer: 2000,
						showConfirmButton: false,
					});
					$("#types_wages-table").DataTable().ajax.reload(null, false);
					$('#modal').modal('hide');
				
				} else {
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

</script>
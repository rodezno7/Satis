<div class="modal-header">
	<h3 class="modal-title" id="formModal">@lang('rrhh.edit') {{ $type_item }}</h3>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
	<form id="form_edit" method="post">


		@if($header_id > 1 && $header_id < 6)		
		<div class="form-group">
			<label>@lang('rrhh.code')</label>
			<input type="text" name='code' id='code' value='{{ $item->code }}' class="form-control" placeholder="@lang('rrhh.code')" readonly>
		</div>
		@endif

		@if($header_id == 4)	
		<div class="form-group">
			<label>@lang('rrhh.short_name')</label>
			<input type="text" name='short_name' id='short_name' value='{{ $item->short_name }}' class="form-control" placeholder="@lang('rrhh.short_name')">
		</div>
		@endif

		<div class="form-group">
			<label>@lang('rrhh.name')</label>
			<input type="text" name='value' id='value' class="form-control" value='{{ $item->value }}' placeholder="@lang('rrhh.name')">

			<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
			<input type="hidden" name="human_resources_header_id" value="{{ $item->human_resources_header_id }}" id="human_resources_header_id">
		</div>

		<div class="form-group">
			<label>@lang('rrhh.status')</label>
			

			{!! Form::select('status', [1 => __('rrhh.active'), 2 => __('rrhh.inactive')], $item->status, ['class' => 'form-control select2', 'id' => 'status', 'required', 'style' => 'width: 100%;' ]) !!}

		</div>


	</form>	
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btn_edit_item">@lang('rrhh.update')</button>
</div>

<script>

	$( document ).ready(function() {		
		select2 = $('.select2').select2();
	});

	$("#btn_edit_item").click(function() {
		$("#btn_edit_item").prop("disabled", true);
		id = {{ $item->id }}
		route = "/rrhh-catalogues-data/"+id;
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
					$("#btn_edit_item").prop("disabled", false);
					Swal.fire
					({
						title: result.msg,
						icon: "success",
						timer: 2000,
						showConfirmButton: false,
					});
					$("#afps-table").DataTable().ajax.reload(null, false);
					$("#marital-statuses-table").DataTable().ajax.reload(null, false);
					$("#departments-table").DataTable().ajax.reload(null, false);
					$("#positions-table").DataTable().ajax.reload(null, false);
					$("#types-table").DataTable().ajax.reload(null, false);
					$("#nationalities-table").DataTable().ajax.reload(null, false);
					$("#professions-table").DataTable().ajax.reload(null, false);
					$("#way-to-pays-table").DataTable().ajax.reload(null, false);
					$("#document-types-table").DataTable().ajax.reload(null, false);
					$('#modal').modal('hide');


				}
				else {
					$("#btn_edit_item").prop("disabled", false);
					Swal.fire
					({
						title: result.msg,
						icon: "error",
					});
				}
			},
			error:function(msj){
				$("#btn_edit_item").prop("disabled", false);
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
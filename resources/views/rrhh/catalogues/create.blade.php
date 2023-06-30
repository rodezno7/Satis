<div class="modal-header">
	<h4 class="modal-title" id="formModal">@lang('rrhh.add') {{ $type_item }}
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</h4>
</div>
<div class="modal-body">
	<form id="form_add" method="post">
		<div class="form-group">
			<label>@lang('rrhh.name')</label>
			<input type="text" name='value' id='value' class="form-control" placeholder="@lang('rrhh.name')">

			@if ($header_id == 9)
			<br>
			{{-- <input type="checkbox" name='value' id='value' class="form-control"> --}}
			<label>
                {!! Form::checkbox('date_required', '0', false, ['id' => 'date_required', 'onClick' => 'dateRequired()']) !!}
			@lang('rrhh.date_required')</label>
			@endif

			<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
			<input type="hidden" name="human_resources_header_id" value="{{ $header_id }}" id="human_resources_header_id">
		</div>
	</form>	
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btn_add_item">@lang('rrhh.add')</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal">@lang( 'messages.cancel' )</button>
</div>

<script>

	$( document ).ready(function() {		
		select2 = $('.select2').select2();
		dateRequired();
	});

	$("#btn_add_item").click(function() {
		$("#btn_add_item").prop("disabled", true);
		route = "/rrhh-catalogues-data";
		datastring = $("#form_add").serialize();
		token = $("#token").val();
		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': token},
			type: 'POST',
			dataType: 'json',
			data: datastring,
			success:function(result) {

				if(result.success == true) {
					$("#btn_add_item").prop("disabled", false);
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
					$("#btn_add_item").prop("disabled", false);
					Swal.fire
					({
						title: result.msg,
						icon: "error",
					});
				}
			},
			error:function(msj){
				$("#btn_add_item").prop("disabled", false);
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

	function dateRequired() {
		if ($("#date_required").is(":checked")) {
		$("#date_required").val('1');
		} else {
		$("#chk_has_user").val('0');
		}
	}
</script>
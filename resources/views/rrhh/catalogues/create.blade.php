<div class="modal-header">
	{{-- <h4 class="modal-title" id="formModal">@lang('rrhh.add') {{ $type_item }} --}}
	<h4 class="modal-title" id="formModal">{{ $type_item }}
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
				<br>
				<label>
					{!! Form::checkbox('number_required', '0', false, ['id' => 'number_required', 'onClick' => 'numberRequired()']) !!}
				@lang('rrhh.number_required')</label>
				<br>
				<label>
					{!! Form::checkbox('expedition_place', '0', false, ['id' => 'expedition_place', 'onClick' => 'expeditionPlace()']) !!}
				@lang('rrhh.expedition_place')</label>
			@endif

			<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
			<input type="hidden" name="rrhh_header_id" value="{{ $header_id }}" id="rrhh_header_id">
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
		expeditionPlace();
		numberRequired();
	});

	$("#btn_add_item").click(function() {
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

					$("#special_capabilities-table").DataTable().ajax.reload(null, false);
					//$("#types_wages-table").DataTable().ajax.reload(null, false);
					$("#employee_classification-table").DataTable().ajax.reload(null, false);
					//$("#cost_center-table").DataTable().ajax.reload(null, false);
					//$("#types_contracts-table").DataTable().ajax.reload(null, false);
					$("#types_professions_occupations-table").DataTable().ajax.reload(null, false);
					$("#types_studies-table").DataTable().ajax.reload(null, false);
					$("#types_personnel_actions-table").DataTable().ajax.reload(null, false);
					$("#types_income_discounts-table").DataTable().ajax.reload(null, false);
					$("#types_inabilities-table").DataTable().ajax.reload(null, false);
					$("#types_relationships-table").DataTable().ajax.reload(null, false);

					$('#modal').modal('hide');
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

	function dateRequired() {
		if ($("#date_required").is(":checked")) {
			$("#date_required").val('1');
		} else {
			$("#date_required").val('0');
		}
	}

	function expeditionPlace() {
		if ($("#expedition_place").is(":checked")) {
			$("#expedition_place").val('1');
		} else {
			$("#expedition_place").val('0');
		}
	}

	function numberRequired() {
		if ($("#number_required").is(":checked")) {
			$("#number_required").val('1');
		} else {
			$("#number_required").val('0');
		}
	}
</script>
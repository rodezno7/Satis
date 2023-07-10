{!! Form::open(['method' => 'post', 'id' => 'form_edit' ]) !!}
<div class="modal-header">
	<h4 class="modal-title" id="formModal">@lang('rrhh.economic_dependencies')
		<button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
			<span aria-hidden="true">&times;</span>
		</button>
	</h4>
</div>
<div class="modal-body">
	<div class="row">
		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label>@lang('rrhh.types_relationships')</label> <span class="text-danger">*</span>
				<select name="type_relationship_id" id="type_relationship_id"
					class="form-control form-control-sm select2" placeholder="{{ __('rrhh.types_relationships') }}"
					style="width: 100%;">
					@foreach ($typeRelationships as $type)
						@if ($type->id == $economicDependence->type_relationship_id)
						<option value="{{ $type->id }}" selected>{{ $type->value }}</option>
						@else
						<option value="{{ $type->id }}">{{ $type->value }}</option>
						@endif
					@endforeach
				</select>
			</div>
		</div>

		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label>@lang('rrhh.name')</label> <span class="text-danger">*</span>
				{!! Form::text("name", $economicDependence->name, ['class' => 'form-control form-control-sm', 'placeholder' =>
				__('rrhh.name'), 'id' => 'name', 'required'])!!}
			</div>
		</div>

		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label>@lang('rrhh.birthdate')</label> <span class="text-danger">*</span>
				{!! Form::text("birthdate", @format_date($economicDependence->birthdate), ['class' => 'form-control form-control-sm', 'id' => 'birthdate',
				'required'])!!}
			</div>
		</div>

		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label>@lang('rrhh.phone')</label>
				{!! Form::text("phone", $economicDependence->phone,
				['class' => 'form-control form-control-sm', 'placeholder' => '0000-0000', 'id' => 'phone']) !!}
			</div>
		</div>

		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label>@lang('rrhh.status')</label>
				{!! Form::select('status', [1 => __('rrhh.active'), 0 => __('rrhh.inactive')], $economicDependence->status, ['class' => 'form-control select2', 'id' => 'status', 'required', 'style' => 'width: 100%;' ]) !!}
			</div>
		</div>
	</div>

</div>
<div class="modal-footer">
	<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
	<input type="hidden" name="id" value="{{ $economicDependence->id }}" id="id">
	<input type="hidden" name="employee_id" value="{{ $employee_id }}" id="employee_id">
	<button type="button" class="btn btn-primary" id="btn_edit_economic_dependence">@lang('rrhh.update')</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal" onClick="closeModal()">@lang(
		'messages.cancel')</button>
</div>
{!! Form::close() !!}
<script>
	$( document ).ready(function() {
		$.fn.modal.Constructor.prototype.enforceFocus = function() {};
   		select2 = $('.select2').select2();
	});

	var fechaMaxima = new Date();
    fechaMaxima = fechaMaxima.toLocaleDateString("es-ES", { day: '2-digit', month: '2-digit', year: 'numeric' });

	$('#birthdate').datepicker({
		autoclose: true,
		format: datepicker_date_format,
		endDate: fechaMaxima,
	});

	$("#btn_edit_economic_dependence").click(function() {
		route = "/rrhh-economic-dependence-update";
		token = $("#token").val();

		var form = $("#form_edit");
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
					getEconomicDependence($('#employee_id').val());
					Swal.fire
					({
						title: result.msg,
						icon: "success",
						timer: 1000,
						showConfirmButton: false,
					});
					$('#modal_edit_action').modal( 'hide' ).data( 'bs.modal', null );
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

	function closeModal(){
		$('#modal_action').modal({backdrop: 'static'});
		$('#modal_edit_action').modal('hide').data('bs.modal', null);
	}
</script>
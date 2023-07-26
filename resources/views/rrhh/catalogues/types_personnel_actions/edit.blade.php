<div class="modal-header">
	<h4 class="modal-title" id="formModal">@lang('rrhh.types_personnel_actions')
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</h4>
</div>
<div class="modal-body">
	<form id="form_edit" method="post">
		<div class="row">
			<div class="col-lg-7 col-md-7 col-sm-12">
				<div class="form-group">
					<label>@lang('rrhh.name')</label>
					<input type="text" name='name' id='name' class="form-control" placeholder="@lang('rrhh.name')" value="{{ $item->name }}">
					<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
				</div>
			</div>
			<div class="col-lg-5 col-md-5 col-sm-12">
				<div class="form-group">
					<input type="checkbox" name='required_authorization' id='required_authorization' onclick="requiredAuthorization()">
					<label for="required_authorization"> {{ __('rrhh.required_authorization') }} @show_tooltip(__('rrhh.message_authorization'))</label>
					<br>
					<input type="checkbox" name='apply_to_many' id='apply_to_many' onclick="applyMany()" value="{{ $item->apply_to_many }}">
					<label for="apply_to_many"> {{ __('rrhh.apply_to_many') }} @show_tooltip(__('rrhh.message_apply_to_many'))</label>
				</div>
			</div>
			@foreach ($clases as $class)
				<div class="col-md-6">
					<label style="text-transform: uppercase;">Clase: {{ $class->name }}</label> <br>
					<ul class="list-group">
						@foreach ($actions as $action)
							@if($action->class_id == $class->id)
								@php
									$exist = 0;
								@endphp
								@foreach ($actionTypes as $actionType)
									@if($action->rrhh_required_action_id == $actionType->rrhh_required_action_id && $action->rrhh_class_personnel_action_id == $actionType->rrhh_class_personnel_action_id)
										@php
											$exist = $action->id;
										@endphp
									@endif
								@endforeach
								@if ($exist == $action->id)
								<label class="list-group-item"> 
									<input type="checkbox" name='action[]' id='action' value="{{ $action->id }}" checked> {{ $action->name }}
								</label>
								@else
								<label class="list-group-item"> 
									<input type="checkbox" name='action[]' id='action' value="{{ $action->id }}"> {{ $action->name }}
								</label>
								@endif
								
							@endif
						@endforeach
					</ul>
				</div>	
			@endforeach
		</div>
	</form>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btn_edit_type_personnel_action">@lang('rrhh.update')</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal">@lang( 'messages.cancel' )</button>
</div>

<script>
	$( document ).ready(function() {	

		let required_authorization = {{ $item->required_authorization }};
		if (required_authorization == 1) {
			$("#required_authorization").prop("checked", true);
		} else {
			$("#required_authorization").prop("checked", false);
		}

		let apply_to_many = $("#apply_to_many").val();
		if (apply_to_many == 1) {
			$("#apply_to_many").prop("checked", true);
		} else {
			$("#apply_to_many").prop("checked", false);
		}
	});

	$("#btn_edit_type_personnel_action").click(function() {
		id = {{ $item->id }}
		route = "/rrhh-type-personnel-action/"+id;
		datastring = $("#form_edit").serialize();
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
					$("#types_personnel_actions-table").DataTable().ajax.reload(null, false);
					$('#modal_type').modal('hide');
				
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
					html: "<ul>"+ msj+ "</ul>",
				});
			}
		});
	});


	function requiredAuthorization() {
		if ($("#required_authorization").is(":checked")) {
			$("#required_authorization").val('1');
		} else {
			$("#required_authorization").val('0');
		}
	}

	function applyMany() {
		if ($("#apply_to_many").is(":checked")) {
			$("#apply_to_many").val('1');
		} else {
			$("#apply_to_many").val('0');
		}
	}

</script>
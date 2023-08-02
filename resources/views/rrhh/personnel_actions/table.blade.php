<table class="table table-responsive table-condensed table-text-center" style="font-size: inherit;" id="documents-table">
	<thead>
		<tr class="active">
			<th>@lang('rrhh.type_personnel_action')</th>
			<th>@lang('rrhh.description')</th>
			<th>@lang('rrhh.status')</th>
			<th>@lang('rrhh.created_date')</th>
			<th width="15%" id="dele">@lang('rrhh.actions' )</th>
		</tr>
	</thead>
	<tbody id="referencesItems">
		@if (count($personnelActions) > 0)
			@foreach($personnelActions as $item)
				<tr>
					<td>{{ $item->type }}</td>
					<td>
						{{ $item->description }}
					</td>
					<td>
						{{ $item->status }}
					</td>
					<td>
						{{ @format_date($item->created_at) }}
					</td>
					<td>
						<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> @lang("messages.actions") <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu">
						<li><a href="#" onClick='viewPersonnelAction({{ $item->id }})'><i class="fa fa-eye"></i>{{ __('messages.view') }}</a></li>
						
						@if (!isset($show))
						@php
							$usersAuthorized = 0;
							$users = 0;
							foreach($personnelActionAuthorizers as $personnelActionAuthorizer){
								if($personnelActionAuthorizer->rrhh_personnel_action_id == $item->id){
									if($personnelActionAuthorizer->authorizer == 0){
										$usersAuthorized++;
									}
									$users++;
								}
							}
						@endphp
						
						@can('rrhh_personnel_action.update')
							@if ($item->status != 'Autorizada' && $usersAuthorized == $users)
								<li><a href="#" onClick='editPersonnelAction({{ $item->id }})'><i class="glyphicon glyphicon-edit"></i>{{ __('messages.edit') }}</a></li>
							@endif
						@endcan
						@endif

						<li><a href="/rrhh-personnel-action/{{ $item->id }}/authorization-report"><i class="fa fa-file"></i>{{ __('rrhh.download') }}</a></li>
						
						@if (!isset($show))
						<li><a href="#" onClick="addFile({{ $item->id }})"><i class="fa fa-upload"></i>{{ __('rrhh.attach_file') }}</a></li>
						@endif

						@can('rrhh_personnel_action.view')
							<li><a href="#" onClick="files({{ $item->id }})"><i class="fa fa-list"></i>{{ __('rrhh.files') }}</a></li>
						@endcan

						@if (!isset($show))
						@can('rrhh_personnel_action.delete')
							@if ($item->status != 'Autorizada' && $usersAuthorized == $users)
								<li><a href="#" onClick='deletePersonnelAction({{ $item->id }})'><i class="glyphicon glyphicon-trash"></i>{{ __('rrhh.delete') }}</a></li>
							@endif
						@endcan
						@endif
					</td>
				</tr>
			@endforeach
		@else
			<tr>
				<td colspan="5" class="text-center">@lang('lang_v1.no_records')</td>
			</tr>
		@endif
	</tbody>
</table>
<input type="hidden" name="_employee_id" value="{{ $employee->id }}" id="_employee_id">
<div tabindex="-1" class="modal fade" id="file_modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true"></div>

<script type="text/javascript">
	function addFile(id) 
	{
		$("#modal_content_document").html('');
		var url = "{!!URL::to('/rrhh-personnel-action-createDocument/:id')!!}";
		url = url.replace(':id', id);
		$.get(url, function(data) {
			$("#modal_content_document").html(data);
			$('#modal_doc').modal({backdrop: 'static'});
		});
		$('#modal_action').modal('hide').data('bs.modal', null);
    }

	function files(id) 
	{
		$("#modal_content_personnel_action").html('');
		var url = "{!!URL::to('/rrhh-personnel-action-files/:id')!!}";
		url = url.replace(':id', id);
		$.get(url, function(data) {
			$("#modal_content_personnel_action").html(data);
			$('#modal_personnel_action').modal({backdrop: 'static'});
		});
		$('#modal_action').modal('hide').data('bs.modal', null);
	}
	
	function viewPersonnelAction(id) 
	{
		$("#modal_content_personnel_action").html('');
		var url = "{!!URL::to('/rrhh-personnel-action-view/:id')!!}";
		url = url.replace(':id', id);
		$.get(url, function(data) {
			$("#modal_content_personnel_action").html(data);
			$('#modal_personnel_action').modal({backdrop: 'static'});
		});
		$('#modal_action').modal('hide').data('bs.modal', null);
	}

	function editPersonnelAction(id) 
	{
		$("#modal_content_personnel_action").html('');
		var url = "{!!URL::to('/rrhh-personnel-action/:id/edit')!!}";
		url = url.replace(':id', id);
		$.get(url, function(data) {
			$("#modal_content_personnel_action").html(data);
			$('#modal_personnel_action').modal({backdrop: 'static'});
		});
		$('#modal_action').modal('hide').data('bs.modal', null);
	}

	function deletePersonnelAction(id) 
	{
		Swal.fire({
            title: LANG.sure,
            text: "{{ __('messages.delete_content') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('messages.accept') }}",
            cancelButtonText: "{{ __('messages.cancel') }}"
        }).then((willDelete) => {
            if (willDelete.value) {
                route = '/rrhh-personnel-action/'+id;
				token = $("#token").val();
				$.ajax({
				url: route,
				headers: {'X-CSRF-TOKEN': token},
				type: 'DELETE',
				dataType: 'json',                       
				success:function(result){
					if(result.success == true) {
					Swal.fire
					({
						title: result.msg,
						icon: "success",
						timer: 1000,
						showConfirmButton: false,
					});

					getPersonnelActions($('#_employee_id').val());

					} else {
					Swal.fire
					({
						title: result.msg,
						icon: "error",
					});
					}
				}
				});
            }
        });
	}

	$("#btn_add_personnel_actions").click(function() 
    {
        $("#modal_content_personnel_action").html('');
        var url = "{!!URL::to('/rrhh-personnel-action-create/:id')!!}";
        id = $('#_employee_id').val();
        url = url.replace(':id', id);
        $.get(url, function(data) {
			$("#modal_content_personnel_action").html(data);
			$('#modal_personnel_action').modal({
            	backdrop: 'static'
            });
        });
		$('#modal_action').modal('hide').data('bs.modal', null);
    });

	function getPersonnelActions(id){
		$("#modal_action").html('');
		var route = '/rrhh-personnel-action-getByEmployee/'+id;
		$.get(route, function(data) {
			$("#modal_action").html(data);
			$('#modal_action').modal({
            	backdrop: 'static'
            });
        });
	}
</script>
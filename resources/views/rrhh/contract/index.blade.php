<div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('rrhh.contract'): <span style="color: gray">{{ $employee->first_name }} {{ $employee->last_name }}</span></h4>
        </div>
        <div class="modal-body">
            <div class="row">
				
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="form-group">
						@can('rrhh_contract.create')
							@if (count($contracts) > 0)
								@if ($contracts[count($contracts) - 1]->status == 0)
									<button type="button" class="btn btn-info btm-sm" id='btn_add_contract'
										style="padding: 5px 8px; margin-right: 5px; margin-top: -2px;">
										<i class="fa fa-plus"></i> @lang('rrhh.add')
									</button>
								@endif
							@else
								<button type="button" class="btn btn-info btm-sm" id='btn_add_contract'
									style="padding: 5px 8px; margin-right: 5px; margin-top: -2px;">
									<i class="fa fa-plus"></i> @lang('rrhh.add')
								</button>
							@endif
						@endcan
					</div>
					<table class="table table-responsive table-condensed table-text-center" style="font-size: inherit;" id="types_relationships-table">
						<thead>
							<tr class="active">
								<th>@lang('rrhh.type')</th>
								<th>@lang('rrhh.start_date')</th>
								<th>@lang('rrhh.end_date')</th>
								<th>@lang('rrhh.status')</th>
								<th width="15%" id="dele">@lang('rrhh.actions' )</th>
							</tr>
						</thead>
						<tbody id="referencesItems">
							@if (count($contracts) > 0)
								@foreach($contracts as $item)
									<tr>
										<td>{{ $item->type }}</td>
										<td>{{ @format_date($item->contract_start_date) }}</td>
										<td>{{ @format_date($item->contract_end_date) }}</td>
										<td>
											@if ($item->status == 1)
												{{ __('rrhh.current') }}
											@else
												{{ __('rrhh.defeated') }}
											@endif
										</td>
										<td>
											@if ($item->status == 1)
												@can('rrhh_contract.update')
													<button type="button" onClick='editContract({{ $item->id }})' class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i></button>
												@endcan
												@can('rrhh_contract.delete')
													<button type="button" onClick='deleteContract({{ $item->id }})' class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></button>
												@endcan
											@else
												@can('rrhh_contract.update')
													<button type="button" class="btn btn-primary btn-xs" disabled><i class="glyphicon glyphicon-edit"></i></button>
												@endcan
												@can('rrhh_contract.delete')
													<button type="button" class="btn btn-danger btn-xs" disabled><i class="glyphicon glyphicon-trash"></i></button>
												@endcan
											@endif
											
											<a href="/rrhh-contracts-generate/{{ $item->id }}"  title="{{ __('rrhh.generate') }}" target="_blank" class="btn btn-primary btn-xs"><i class="fa fa-file-text"></i></a>
										</td>
									</tr>
								@endforeach
							@else
								<tr>
									<td colspan="4" class="text-center">@lang('lang_v1.no_records')</td>
								</tr>
							@endif
						</tbody>
					</table>
					<input type="hidden" name="_employee_id" value="{{ $employee->id }}" id="_employee_id">
				</div>				
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">
	function editContract(id) 
	{
		$("#modal_content_edit_document").html('');
		var url = "{!!URL::to('/rrhh-contracts/:id/edit')!!}";
		url = url.replace(':id', id);
		$.get(url, function(data) {
			$("#modal_content_edit_document").html(data);
			$('#modal_edit_action').modal({backdrop: 'static'});
		});
		$('#modal_action').modal('hide').data('bs.modal', null);
	}

	function deleteContract(id) 
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
                route = '/rrhh-contracts/'+id;
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

					getContract($('#_employee_id').val());

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

	$("#btn_add_contract").click(function() 
    {
        $("#modal_content_document").html('');
        var url = "{!!URL::to('/rrhh-contracts-create/:id')!!}";
        id = $('#_employee_id').val();
        url = url.replace(':id', id);
        $.get(url, function(data) {
			$("#modal_content_document").html(data);
			$('#modal_doc').modal({
            	backdrop: 'static'
            });
        });
		$('#modal_action').modal('hide').data('bs.modal', null);
    });

	function getContract(id){
		$("#modal_action").html('');
		var route = '/rrhh-contracts-getByEmployee/'+id;
		$.get(route, function(data) {
			$("#modal_action").html(data);
			$('#modal_action').modal({
            	backdrop: 'static'
            });
        });
	}
</script>
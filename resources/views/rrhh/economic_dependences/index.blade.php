<div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('rrhh.economic_dependencies')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
				
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="form-group">
						<button type="button" class="btn btn-info btm-sm" id='btn_add_economic_dependencies'
						style="padding: 5px 8px; margin-right: 5px; margin-top: -2px;">
						<i class="fa fa-plus"></i>
						@lang('rrhh.add_economic_dependence')
						</button>
					</div>
					<table class="table table-responsive table-condensed table-text-center" style="font-size: inherit;" id="types_relationships-table">
						<thead>
							<tr class="active">
								<th>@lang('rrhh.name')</th>
								<th>@lang('rrhh.relationships')</th>
								<th>@lang('rrhh.birthdate')</th>
								<th width="12%">@lang('rrhh.phone')</th>
								<th width="10%">@lang('rrhh.status')</th>
								<th width="15%" id="dele">@lang('rrhh.actions' )</th>
							</tr>
						</thead>
						<tbody id="referencesItems">
							@if (count($economicDependences) > 0)
								@foreach($economicDependences as $item)
									<tr>
										<td>{{ $item->type }}</td>
										<td>{{ $item->name }}</td>
										<td>
											@if ($item->birthdate != null)
											{{ @format_date($item->birthdate) }}
											@else
												N/A
											@endif
										</td>
										<td>{{ $item->phone }}</td>
										<td>
											@if ($item->status == 1)
												{{ __('rrhh.active') }}
											@else
											{{ __('rrhh.inactive') }}
											@endif
										</td>
										<td>
											<button type="button" onClick='editEconomicDependence({{ $item->id }})' class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i></button>
											<button type="button" onClick='deleteEconomicDependence({{ $item->id }})' class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></button>
										</td>
									</tr>
								@endforeach
							@else
								<tr>
									<td colspan="6" class="text-center">@lang('lang_v1.no_records')</td>
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
	function editEconomicDependence(id) 
	{
		$("#modal_content_edit_document").html('');
		var url = "{!!URL::to('/rrhh-economic-dependence/:id/edit')!!}";
		url = url.replace(':id', id);
		$.get(url, function(data) {
			$("#modal_content_edit_document").html(data);
			$('#modal_edit_action').modal({backdrop: 'static'});
		});
		$('#modal_action').modal('hide').data('bs.modal', null);
	}

	function deleteEconomicDependence(id) 
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
                route = '/rrhh-economic-dependence/'+id;
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

					getEconomicDependence($('#_employee_id').val());

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

	$("#btn_add_economic_dependencies").click(function() 
    {
        $("#modal_content_document").html('');
        var url = "{!!URL::to('/rrhh-economic-dependence-create/:id')!!}";
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

	function getEconomicDependence(id){
		$("#modal_action").html('');
		var route = '/rrhh-economic-dependence-getByEmployee/'+id;
		$.get(route, function(data) {
			$("#modal_action").html(data);
			$('#modal_action').modal({
            	backdrop: 'static'
            });
        });
	}
</script>
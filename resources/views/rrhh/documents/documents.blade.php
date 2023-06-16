<div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('rrhh.documents')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
				
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					@if (count($types) > count($documents))
					<div class="form-group">
						<button type="button" class="btn btn-info btm-sm" id='btn_add_documents'
						style="padding: 5px 8px; margin-right: 5px; margin-top: -2px;">
						<i class="fa fa-plus"></i>
						@lang('rrhh.add_document')
						</button>
					</div>
					@endif
					<table class="table table-responsive table-condensed table-text-center" style="font-size: inherit;" id="documents-table">
						<thead>
							<tr class="active">
								<th width="20%">@lang('rrhh.document_type')</th>
								<th width="20%">@lang('rrhh.state_expedition')</th>
								<th width="20%">@lang('rrhh.city_expedition')</th>
								<th width="15%">@lang('rrhh.number')</th>
								@if(!isset($route))<th width="10%" id="dele">@lang('rrhh.actions' )</th>@endif
							</tr>
						</thead>
						<tbody id="referencesItems">
							@if (count($documents) > 0)
								@foreach($documents as $item)
									<tr>
										<td>{{ $item->type }}</td>
										<td>{{ $item->state }}</td>
										<td>{{ $item->city }}</td>
										<td>{{ $item->number }}</td>
										@if(!isset($route))
										<td>
											@if ($item->file != '')
												<button type="button" onClick="viewFile({{ $item->id }})" class="btn btn-info btn-xs"><i class="fa fa-eye"></i></button>
											@endif
											<button type="button" onClick='editDocument({{ $item->id }})' class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i></button>
											<button type="button" onClick='deleteDocument({{ $item->id }})' class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></button>
										</td>
										@endif
									</tr>
								@endforeach
							@else
								<tr>
									@if(!isset($route))
										<td colspan="4" class="text-center">@lang('lang_v1.no_records')</td>
									@else
										<td colspan="5" class="text-center">@lang('lang_v1.no_records')</td>
									@endif
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
	function viewFile(id) 
	{
		$("#modal_content_photo").html('');
		var url = "{!!URL::to('/rrhh-documents-viewFile/:id')!!}";
		url = url.replace(':id', id);
		$.get(url, function(data) {
			$("#modal_content_photo").html(data);
			$('#modal_photo').modal({backdrop: 'static'});
		});
		$('#document_modal').modal('hide').data('bs.modal', null);
	}

	function editDocument(id) 
	{
		$("#modal_content_edit_document").html('');
		var url = "{!!URL::to('/rrhh-documents/:id/edit')!!}";
		url = url.replace(':id', id);
		$.get(url, function(data) {
			$("#modal_content_edit_document").html(data);
			$('#modal_edit_document').modal({backdrop: 'static'});
		});
		$('#document_modal').modal('hide').data('bs.modal', null);
	}

	function deleteDocument(id) 
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
                route = '/rrhh-documents/'+id;
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

					getDocuments($('#_employee_id').val());

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

	$("#btn_add_documents").click(function() 
    {
        $("#modal_content_document").html('');
        var url = "{!!URL::to('/rrhh-documents-createDocument/:id')!!}";
        id = $('#_employee_id').val();
        url = url.replace(':id', id);
        $.get(url, function(data) {
			$("#modal_content_document").html(data);
			$('#modal_doc').modal({
            	backdrop: 'static'
            });
        });
		$('#document_modal').modal('hide').data('bs.modal', null);
    });

	function getDocuments(id){
		$("#document_modal").html('');
		var route = '/rrhh-documents-getByEmployee/'+id;
		$.get(route, function(data) {
			$("#document_modal").html(data);
			$('#document_modal').modal({
            	backdrop: 'static'
            });
        });
	}
</script>
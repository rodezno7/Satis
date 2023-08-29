<div class="modal-header">
	<button type="button" class="close no-print" data-dismiss="modal" aria-label="Close" onClick="closeModal()"><span
		aria-hidden="true">&times;</span></button>
	<h4 class="modal-title">@lang('rrhh.personnel_actions'): <span style="color: gray">{{ $employee->first_name }} {{ $employee->last_name }}</span></h4> 
</div>
<div class="modal-body">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<table class="table table-responsive table-condensed table-text-center" style="font-size: inherit;" id="documents-table">
				<thead>
					<tr class="active">
						<th>@lang('rrhh.name')</th>
						<th width="15%" id="dele">@lang('rrhh.actions' )</th>
					</tr>
				</thead>
				<tbody id="referencesItems">
					@if (count($personnelActionsFile) > 0)
						@foreach($personnelActionsFile as $item)
							<tr>
								@php
                                	$name = explode('_', $item->file);
								@endphp
								<td>{{ $name[1] }}</td>
								{{-- <td>{{ $item->file }}</td> --}}
								<td>
									@can('rrhh_personnel_action.view')
										<button type="button" onClick="showFile({{ $item->id }})"
											class="btn btn-info btn-xs"><i class="fa fa-eye"></i></button>
									@endcan
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
			<input type="hidden" name="_employee_id" value="{{ $employee->id }}" id="_employee_id_pa3">
			<div tabindex="-1" class="modal fade" id="file_modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true"></div>
		</div>				
	</div>
</div>

<script type="text/javascript">
	function showFile(id) {
        $("#modal_content_show").html('');
        var url = "{!! URL::to('/rrhh-personnel-action-viewFile/:id') !!}";
        url = url.replace(':id', id);
        $.get(url, function(data) {
            $("#modal_content_show").html(data);
            $('#modal_show').modal({
                backdrop: 'static'
            });
        });
        $('#modal_edit_action').modal('hide').data('bs.modal', null);
    }

    function closeModal(){
		$('#modal_action').modal({backdrop: 'static'});
		$('#modal_edit_action').modal( 'hide' ).data( 'bs.modal', null );
	}
</script>
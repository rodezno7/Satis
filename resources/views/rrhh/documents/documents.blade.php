<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<table class="table table-responsive table-condensed table-text-center" style="font-size: inherit;">
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
</div>

<div class="modal fade" id="modal_photo" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content" id="modal_content_photo">

		</div>
	</div>
</div>

<div class="modal fade" id="modal_edit_document" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content" id="modal_content_edit_document">

		</div>
	</div>
</div>

<script type="text/javascript">
	function viewFile(id) {
		$("#modal_content_photo").html('');
		var url = '{!!URL::to('/rrhh-documents-viewFile/:id')!!}';
		url = url.replace(':id', id);
		$.get(url, function(data) {
			$("#modal_content_photo").html(data);
			$('#modal_photo').modal({backdrop: 'static'});
		});
	}

	function editDocument(id) {
		$("#modal_content_edit_document").html('');
		var url = '{!!URL::to('/rrhh-documents/:id/edit')!!}';
		url = url.replace(':id', id);
		$.get(url, function(data) {
			$("#modal_content_edit_document").html(data);
			$('#modal_edit_document').modal({backdrop: 'static'});
		});
	}
</script>
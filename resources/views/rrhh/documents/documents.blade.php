<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<table class="table table-responsive table-condensed table-text-center" style="font-size: inherit;">
		<thead>
			<tr class="active">
				<th width="20%">@lang('rrhh.document_type')</th>
				<th class="text-center" width="20%">@lang('rrhh.state_expedition')</th>
				<th class="text-center" width="20%">@lang('rrhh.city_expedition')</th>
				<th class="text-center" width="15%">@lang('rrhh.number')</th>
				<th class="text-center" width="15%">@lang('rrhh.file')</th>
				<th width="10%" id="dele">&nbsp;</th>
			</tr>
		</thead>
		<tbody id="referencesItems">
			@foreach($documents as $item)

			<tr>

				<td>{{ $item->type }}</td>
				<td>{{ $item->state }}</td>
				<td>{{ $item->city }}</td>
				<td>{{ $item->number }}</td>
				<td>
					@if ($item->file != '')

					<button type="button" onClick="viewFile({{ $item->id }})" class="btn btn-info btn-xs">@lang('rrhh.view_file')</button>

					@endif
				</td>
				<td>
					<button type="button" onClick='deleteDocument({{ $item->id }})' class="btn btn-danger btn-xs"><i class="fa fa-times"></i></button>

					<button type="button" onClick='editDocument({{ $item->id }})' class="btn btn-success btn-xs"><i class="glyphicon glyphicon-edit"></i></button>
				</td>
			</tr>

			@endforeach
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
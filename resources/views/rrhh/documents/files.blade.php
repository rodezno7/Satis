<div class="modal-header">
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close" onClick="closeModal()"><span
            aria-hidden="true">&times;</span></button>
<h4 class="modal-title">@lang('rrhh.documents'): <span style="color: gray">{{ $employee->first_name }}
    {{ $employee->last_name }}</span></h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <table class="table table-responsive table-condensed table-text-center" style="font-size: inherit;"
                id="documents-table">
                <thead>
                    <tr class="active">
                        <th>@lang('rrhh.name')</th>
                        <th width="15%" id="dele">@lang('rrhh.actions')</th>
                    </tr>
                </thead>
                <tbody id="referencesItems">
                    @foreach ($documentsFile as $item)
                        <tr>
                            @php
                                $name = explode('_', $item->file);
                            @endphp
                            <td>{{ $name[1] }}</td>
                            <td>
                                @can('rrhh_document_employee.view')
                                    <button type="button" onClick="viewFile({{ $item->id }})"
                                        class="btn btn-info btn-xs"><i class="fa fa-eye"></i></button>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    function viewFile(id) {
        $("#modal_content_photo").html('');
        var url = "{!! URL::to('/rrhh-documents-viewFile/:id') !!}";
        url = url.replace(':id', id);
        $.get(url, function(data) {
            $("#modal_content_photo").html(data);
            $('#modal_photo').modal({
                backdrop: 'static'
            });
        });
        $('#modal_edit_action').modal('hide').data('bs.modal', null);
    }

    function closeModal(){
		$('#document_modal').modal({backdrop: 'static'});
		$('#modal_edit_action').modal( 'hide' ).data( 'bs.modal', null );
	}
</script>

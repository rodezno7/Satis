<table class="table table-responsive table-condensed table-text-center" style="font-size: inherit;" id="documents-table">
    <thead>
        <tr class="active">
            <th>@lang('rrhh.document_type')</th>
            <th>@lang('rrhh.date_expedition')</th>
            <th>@lang('rrhh.date_expiration')</th>
            <th width="12%">@lang('rrhh.number')</th>
            <th width="10%">@lang('rrhh.status')</th>
            <th width="15%" id="dele">@lang('rrhh.actions')</th>
        </tr>
    </thead>
    <tbody id="referencesItems">
        @if (count($documents) > 0)
            @foreach ($documents as $item)
                <tr>
                    <td>{{ $item->type }}</td>
                    <td>
                        @if ($item->date_expedition != null)
                            {{ @format_date($item->date_expedition) }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if ($item->date_expiration != null && $item->date_required == 1)
                            {{ @format_date($item->date_expiration) }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if ($item->number != null)
                            {{ $item->number }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if ($item->date_expiration == null || $item->date_expiration >= Carbon::now()->format('Y-m-d'))
                            <span class="badge" style="background: #449D44">{{ __('rrhh.current') }}</span>
                        @else
                            <span class="badge" style="background: #C9302C">{{ __('rrhh.expired') }}</span>
                        @endif
                    </td>
                    <td>
                        @can('rrhh_document_employee.view')
                            <button type="button" onClick="filesDocument({{ $item->id }}, {{ $employee->id }})" class="btn btn-info btn-xs"><i
                                    class="fa fa-list"></i></button>
                        @endcan
						@if (!isset($show))
							@can('rrhh_document_employee.update')
								<button type="button" onClick='editDocument({{ $item->id }})'
									class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i></button>
							@endcan
							@can('rrhh_document_employee.delete')
								<button type="button" onClick='deleteDocument({{ $item->id }})'
									class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></button>
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
<input type="hidden" name="_employee_id" value="{{ $employee->id }}" id="_employee_id_doc">

<script type="text/javascript">

    function filesDocument(id, employee_id) {
        $("#modal_content_edit_document").html('');
        var url = "{!! URL::to('/rrhh-documents-files/:id/:employee_id') !!}";
        url = url.replace(':id', id);
        url = url.replace(':employee_id', employee_id);
        $.get(url, function(data) {
            $("#modal_content_edit_document").html(data);
            $('#modal_edit_action').modal({
                backdrop: 'static'
            });
        });
        $('#document_modal').modal('hide').data('bs.modal', null);
    }

    function editDocument(id) {
        $("#modal_content_edit_document").html('');
        var url = "{!! URL::to('/rrhh-documents/:id/edit') !!}";
        url = url.replace(':id', id);
        $.get(url, function(data) {
            $("#modal_content_edit_document").html(data);
            $('#modal_edit_action').modal({
                backdrop: 'static'
            });
        });
        $('#document_modal').modal('hide').data('bs.modal', null);
    }

    function deleteDocument(id) {
        employee_id = $('#_employee_id_doc').val();
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
                route = '/rrhh-documents/' + id;
                token = $("#token").val();
                $.ajax({
                    url: route,
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    type: 'DELETE',
                    dataType: 'json',
                    success: function(result) {
                        if (result.success == true) {
                            Swal.fire({
                                title: result.msg,
                                icon: "success",
                                timer: 1000,
                                showConfirmButton: false,
                            });

                            getDocuments(employee_id);

                        } else {
                            Swal.fire({
                                title: result.msg,
                                icon: "error",
                            });
                        }
                    }
                });
            }
        });
    }

    function btnAddDocuments(){
        $("#modal_content_document").html('');
        var url = "{!! URL::to('/rrhh-documents-createDocument/:id') !!}";
        id = $('#_employee_id_doc').val();
        url = url.replace(':id', id);
        $.get(url, function(data) {
            $("#modal_content_document").html(data);
            $('#modal_doc').modal({
                backdrop: 'static'
            });
        });
        $('#document_modal').modal('hide').data('bs.modal', null);
    }

    function getDocuments(id) {
        //$('#_employee_id_doc').val('');
        $("#document_modal").html('');
        var route = '/rrhh-documents-getByEmployee/'+id;
        $("#document_modal").load(route, function() {
            $(this).modal({
                backdrop: 'static'
            });
        });
    }
</script>

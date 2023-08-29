<table class="table table-responsive table-condensed table-text-center" style="font-size: inherit;"
    id="types_relationships-table">
    <thead>
        <tr class="active">
            <th>@lang('rrhh.type_study')</th>
            <th>@lang('rrhh.title')</th>
            <th>@lang('rrhh.institution')</th>
            <th>@lang('rrhh.year_graduation')</th>
            <th>@lang('rrhh.study_status')</th>
            <th >@lang('rrhh.status')</th>
			@if(!isset($show))
            <th id="dele">@lang('rrhh.actions')</th>
			@endif
        </tr>
    </thead>
    <tbody id="referencesItems">
        @if (count($studies) > 0)
            @foreach ($studies as $item)
                <tr>
                    <td>{{ $item->type }}</td>
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->institution }}</td>
                    <td>
                        @if ($item->year_graduation != null)
                            {{ $item->year_graduation }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if ($item->study_status == 'En curso')
                            {{ __('rrhh.in_progress') }}
                        @else
                            @if ($item->study_status == 'Finalizado')
                                {{ __('rrhh.finalized') }}
                            @else
                                {{ __('rrhh.graduate') }}
                            @endif
                        @endif
                    </td>
                    <td>
                        @if ($item->status == 1)
                            {{ __('rrhh.active') }}
                        @else
                            {{ __('rrhh.inactive') }}
                        @endif
                    </td>
					@if(!isset($show))
                    <td>
                        @can('rrhh_study.update')
                            <button type="button" onClick='editStudy({{ $item->id }})'
                                class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i></button>
                        @endcan
                        @can('rrhh_study.delete')
                            <button type="button" onClick='deleteStudy({{ $item->id }})'
                                class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></button>
                        @endcan
                    </td>
					@endif
                </tr>
            @endforeach
        @else
            <tr>
                @if(!isset($show))
                <td colspan="6" class="text-center">@lang('lang_v1.no_records')</td>
                @else
                <td colspan="5" class="text-center">@lang('lang_v1.no_records')</td>
                @endif
            </tr>
        @endif
    </tbody>
</table>
<input type="hidden" name="_employee_id" value="{{ $employee->id }}" id="_employee_id_st">



<script type="text/javascript">
    function editStudy(id) {
        $("#modal_content_edit_document").html('');
        var url = "{!! URL::to('/rrhh-study/:id/edit') !!}";
        url = url.replace(':id', id);
        $.get(url, function(data) {
            $("#modal_content_edit_document").html(data);
            $('#modal_edit_action').modal({
                backdrop: 'static'
            });
        });
        $('#modal_action').modal('hide').data('bs.modal', null);
    }

    function deleteStudy(id) {
        employee_id = $('#_employee_id_st').val();
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
                route = '/rrhh-study/' + id;
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

                            getStudy(employee_id);

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

    function btnAddTypeStudies(){
        $("#modal_content_document").html('');
        var url = "{!! URL::to('/rrhh-study-create/:id') !!}";
        id = $('#_employee_id_st').val();
        url = url.replace(':id', id);
        $.get(url, function(data) {
            $("#modal_content_document").html(data);
            $('#modal_doc').modal({
                backdrop: 'static'
            });
        });
        $('#modal_action').modal('hide').data('bs.modal', null);
    }

    function getStudy(id) {
        //$('#_employee_id_st').val('');
        $("#modal_action").html('');
        var route = '/rrhh-study-getByEmployee/'+id;
        $("#modal_action").load(route, function() {
            $(this).modal({
                backdrop: 'static'
            });
        });
    }
</script>

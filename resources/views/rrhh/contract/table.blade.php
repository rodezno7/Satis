<table class="table table-responsive table-condensed table-text-center" style="font-size: inherit;"
    id="types_relationships-table">
    <thead>
        <tr class="active">
            <th>@lang('rrhh.type')</th>
            <th>@lang('rrhh.start_date')</th>
            <th>@lang('rrhh.end_date')</th>
            <th>@lang('rrhh.status')</th>
            <th width="15%" id="dele">@lang('rrhh.actions')</th>
        </tr>
    </thead>
    <tbody id="referencesItems">
        @if (count($contracts) > 0)
            @foreach ($contracts as $item)
                <tr>
                    <td>{{ $item->type }}</td>
                    <td>{{ @format_date($item->contract_start_date) }}</td>
                    <td>@if ($item->contract_end_date == null)
                        ---
                    @else
                        {{ @format_date($item->contract_end_date) }}
                    @endif
                    </td>
                    <td>
                        @if ($item->contract_status == 'Vigente')
                            <span class="badge" style="background: #449D44">{{ __('rrhh.current') }}</span>
                        @else
                            @if ($item->contract_status == 'Finalizado')
                                <span class="badge" style="background: #4e58b6">{{ __('rrhh.finalized') }}</span>
                            @else
                                <span class="badge" style="background: #C9302C">{{ __('rrhh.defeated') }}</span>
                            @endif
                        @endif
                    </td>
                    <td>
                        @can('rrhh_contract.view')
                            <button type="button" onClick="viewFilesContract({{ $item->id }}, {{ $employee->id }})"
                                class="btn btn-info btn-xs"><i class="fa fa-eye"></i></button>
                        @endcan

                        <a href="/rrhh-contracts-generate/{{ $item->id }}" title="{{ __('rrhh.generate') }}"
                            target="_blank" class="btn btn-primary btn-xs"><i class="fa fa-file-pdf-o"></i></a>

                        @can('rrhh_contract.uploads')
                            <a href="#" onClick="addDocumentContract({{ $item->id }}, {{ $employee->id }})"
                                type="button" class="btn btn-primary btn-xs" title="{{ __('rrhh.attach_file') }}">
                                <i class="fa fa-upload"></i>
                            </a>
                        @endcan

                        @if (!isset($show))
                            @can('rrhh_contract.finish')
                                @if ($item->contract_status == 'Finalizado')
                                    <button type="button" class="btn btn-danger btn-xs" disabled>
                                        <i class="fa fa-close"></i>
                                    </button>
                                @else
                                    <button type="button" onClick='finishContract({{ $item->id }})'
                                        class="btn btn-danger btn-xs" title="{{ __('rrhh.finish_contract') }}">
                                        <i class="fa fa-close"></i>
                                    </button>
                                @endif
                            @endcan
                        @endif
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
<input type="hidden" name="_employee_id" value="{{ $employee->id }}" id="_employee_id_con">


<script type="text/javascript">
    function viewFilesContract(id, employee_id) {
        $("#modal_content_edit_document").html('');
        var url = "{!! URL::to('/rrhh-contracts-show/:id/:employee_id') !!}";
        url = url.replace(':id', id);
        url = url.replace(':employee_id', employee_id)
        $.get(url, function(data) {
            $("#modal_content_edit_document").html(data);
            $('#modal_edit_action').modal({
                backdrop: 'static'
            });
        });
        $('#modal_action').modal('hide').data('bs.modal', null);
    }

    function addDocumentContract(id, employee_id) {
        $("#modal_content_edit_document").html('');
        var url = "{!! URL::to('/rrhh-contracts-createDocument/:id/:employee_id') !!}";
        url = url.replace(':id', id);
        url = url.replace(':employee_id', employee_id)
        $.get(url, function(data) {
            $("#modal_content_edit_document").html(data);
            $('#modal_edit_action').modal({
                backdrop: 'static'
            });
        });
        $('#modal_action').modal('hide').data('bs.modal', null);
    }

    function finishContract(id) {
        Swal.fire({
            title: LANG.sure,
            text: "{{ __('messages.question_content') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('messages.accept') }}",
            cancelButtonText: "{{ __('messages.cancel') }}"
        }).then((willDelete) => {
            if (willDelete.value) {
                route = '/rrhh-contracts-finish/' + id;
                token = $("#token").val();
                employee_id = $('#_employee_id_con').val();
                $.ajax({
                    url: route,
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    type: 'POST',
                    data: {
                        'employee_id': employee_id
                    },
                    dataType: 'json',
                    success: function(result) {
                        if (result.success == true) {
                            Swal.fire({
                                title: result.msg,
                                icon: "success",
                                timer: 1000,
                                showConfirmButton: false,
                            });

                            getContract(employee_id);

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


    $("#btn_add_contract").click(function() {
        $("#modal_content_document").html('');
        var url = "{!! URL::to('/rrhh-contracts-create/:id') !!}";
        id = $('#_employee_id_con').val();
        url = url.replace(':id', id);
        $.get(url, function(data) {
            $("#modal_content_document").html(data);
            $('#modal_doc').modal({
                backdrop: 'static'
            });
        });
        $('#modal_action').modal('hide').data('bs.modal', null);
    });

    function getContract(id) {
        //$('#_employee_id_con').val('');
        $("#modal_action").html('');
        var route = '/rrhh-contracts-getByEmployee/'+id;
        $("#modal_action").load(route, function() {
            $(this).modal({
            backdrop: 'static'
            });
        });
    }
</script>

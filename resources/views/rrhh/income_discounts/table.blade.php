<table class="table table-responsive table-condensed table-text-center" style="font-size: inherit;"
    id="type-income-discount-table">
    <thead>
        <tr class="active">
            <th>@lang('rrhh.type')</th>
            <th>@lang('rrhh.name')</th>
            <th>@lang('rrhh.apply_in')</th>
            <th>@lang('rrhh.total_value')</th>
            <th>@lang('rrhh.quota')</th>
            <th>@lang('rrhh.quota_value')</th>
            <th>@lang('rrhh.period')</th>
			@if(!isset($show))
                <th id="dele">@lang('rrhh.actions')</th>
			@endif
        </tr>
    </thead>
    <tbody id="referencesItems">
        @if (count($employee->rrhhIncomeDiscounts) > 0)
            @foreach ($employee->rrhhIncomeDiscounts as $item)
                <tr>
                    <td>
                        @if ($item->rrhhTypeIncomeDiscount->type == 1)
                            {{ __('rrhh.income') }}
                        @else
                            {{ __('rrhh.discount') }}
                        @endif
                    </td>
                    <td>{{ $item->rrhhTypeIncomeDiscount->name }}</td>
                    <td>{{ $item->paymentPeriod->name }}</td>
                    <td>
                        @if ($business->currency_symbol_placement == 'after')
                            {{ @num_format($item->total_value) }} {{ $business->currency->symbol }}
                        @else
                            {{ $business->currency->symbol }} {{ @num_format($item->total_value) }}
                        @endif
                    </td>
                    <td>{{ $item->quota }}</td>
                    <td>
                        @if ($business->currency_symbol_placement == 'after')
                            {{ @num_format($item->quota_value) }} {{ $business->currency->symbol }}
                        @else
                            {{ $business->currency->symbol }} {{ @num_format($item->quota_value) }}
                        @endif
                    </td>
                    <td>{{ @format_date($item->start_date) }} - {{ @format_date($item->end_date) }}</td>
					@if(!isset($show))
                        <td>
                            @can('rrhh_income_discount.update')
                                <button type="button" onClick='showIncomeDiscount({{ $item->id }})'
                                    class="btn btn-info btn-xs"><i class="fa fa-eye"></i></button>
                            @endcan
                            @can('rrhh_income_discount.update')
                                <button type="button" onClick='editIncomeDiscount({{ $item->id }})'
                                    class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i></button>
                            @endcan
                            @can('rrhh_income_discount.delete')
                                <button type="button" onClick='deleteIncomeDiscount({{ $item->id }})'
                                    class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></button>
                            @endcan
                        </td>
					@endif
                </tr>
            @endforeach
        @else
            <tr>
                @if(!isset($show))
                <td colspan="8" class="text-center">@lang('lang_v1.no_records')</td>
                @else
                <td colspan="7" class="text-center">@lang('lang_v1.no_records')</td>
                @endif
            </tr>
        @endif
    </tbody>
</table>
<input type="hidden" name="_employee_id" value="{{ $employee->id }}" id="employee_id_id">



<script type="text/javascript">
    function showIncomeDiscount(id) {
        $("#modal_content_edit_document").html('');
        var url = "{!! URL::to('/rrhh-income-discount/:id') !!}";
        url = url.replace(':id', id);
        $.get(url, function(data) {
            $("#modal_content_edit_document").html(data);
            $('#modal_edit_action').modal({
                backdrop: 'static'
            });
        });
        $('#modal_action').modal('hide').data('bs.modal', null);
    }

    function editIncomeDiscount(id) {
        $("#modal_content_edit_document").html('');
        var url = "{!! URL::to('/rrhh-income-discount/:id/edit') !!}";
        url = url.replace(':id', id);
        $.get(url, function(data) {
            $("#modal_content_edit_document").html(data);
            $('#modal_edit_action').modal({
                backdrop: 'static'
            });
        });
        $('#modal_action').modal('hide').data('bs.modal', null);
    }

    function deleteIncomeDiscount(id) {
        employee_id = $('#employee_id_id').val();
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
                route = '/rrhh-income-discount/' + id;
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

                            getIncomeDiscount(employee_id);

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

    function btnAddIncomeDiscounts(){
        $("#modal_content_document").html('');
        var url = "{!! URL::to('/rrhh-income-discount-create/:id') !!}";
        id = $('#employee_id_id').val();
        url = url.replace(':id', id);
        $.get(url, function(data) {
            $("#modal_content_document").html(data);
            $('#modal_doc').modal({
                backdrop: 'static'
            });
        });
        $('#modal_action').modal('hide').data('bs.modal', null);
    }

    function getIncomeDiscount(id) {
        $("#modal_action").html('');
        var route = '/rrhh-income-discount-getByEmployee/'+id;
        $("#modal_action").load(route, function() {
            $(this).modal({
                backdrop: 'static'
            });
        });
    }
</script>

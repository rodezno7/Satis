{!! Form::model($contract, ['method' => 'post', 'id' => 'form_edit_contract']) !!}
<div class="modal-header">
    <h4 class="modal-title" id="formModal">@lang('rrhh.contracts')
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
            <span aria-hidden="true">&times;</span>
        </button>
    </h4>
</div>
<div class="modal-body">
    <div class="row">
        {{-- <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
                <label>@lang('rrhh.types_contracts')</label> <span class="text-danger">*</span>
                <select name="rrhh_type_contract_id" id="rrhh_type_contract_id"
                    class="form-control form-control-sm" placeholder="{{ __('rrhh.types_contracts') }}"
                    style="width: 100%;">
                    <option value="">{{ __('rrhh.types_contracts') }}</option>
                    @foreach ($types as $type)
                        @if ($type->id == $contract->rrhh_type_contract_id)
                            <option value="{{ $type->id }}" selected>{{ $type->name }}</option>
                        @else
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div> --}}
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>@lang('rrhh.start_date')</label> <span class="text-danger">*</span>
                {!! Form::text('contract_start_date', @format_date($contract->contract_start_date), [
                    'class' => 'form-control form-control-sm', 'id' => 'contract_start_date', 'required'
                ]) !!}
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>@lang('rrhh.end_date')</label> <span class="text-danger">*</span>
                {!! Form::text('contract_end_date', @format_date($contract->contract_end_date), [
                    'class' => 'form-control form-control-sm', 'id' => 'contract_end_date', 'required'
                ]) !!}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
    <input type="hidden" name="id" value="{{ $contract->id }}" id="id">
    <input type="hidden" name="employee_id" value="{{ $employee_id }}" id="employee_id">
    <button type="button" class="btn btn-primary" id="btn_edit_contract">@lang('rrhh.update')</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal" onClick="closeModal()">@lang('messages.cancel')</button>
</div>
{!! Form::close() !!}
<script>
    $(document).ready(function() {
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
        select2 = $('.select2').select2();

        var fechaMinima = new Date();
        fechaMinima.setFullYear(fechaMinima.getFullYear() - 50);
        fechaMinima = fechaMinima.toLocaleDateString("es-ES", {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });

        $('#contract_start_date').datepicker({
            autoclose: true,
            format: datepicker_date_format,
            startDate: fechaMinima,
        });

        $('#contract_end_date').datepicker({
            autoclose: true,
            format: datepicker_date_format,
            startDate: fechaMinima,
        });
    });


    $("#btn_edit_contract").click(function() {
        route = "/rrhh-contracts-update";
        token = $("#token").val();

        var form = $("#form_edit_contract");
        var formData = new FormData(form[0]);

        $.ajax({
            url: route,
            headers: {
                'X-CSRF-TOKEN': token
            },
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            success: function(result) {
                if (result.success == true) {
                    getContract($('#employee_id').val());
                    Swal.fire({
                        title: result.msg,
                        icon: "success",
                        timer: 1000,
                        showConfirmButton: false,
                    });
                    $('#modal_edit_action').modal('hide').data('bs.modal', null);
                } else {
                    Swal.fire({
                        title: result.msg,
                        icon: "error",
                    });
                }
            },
            error: function(msj) {
                errormessages = "";
                $.each(msj.responseJSON.errors, function(i, field) {
                    errormessages += "<li>" + field + "</li>";
                });
                Swal.fire({
                    title: "@lang('rrhh.error_list')",
                    icon: "error",
                    html: "<ul>" + errormessages + "</ul>",
                });
            }
        });
    });

    function closeModal() {
        $('#modal_action').modal({
            backdrop: 'static'
        });
        $('#modal_edit_action').modal('hide').data('bs.modal', null);
    }
</script>

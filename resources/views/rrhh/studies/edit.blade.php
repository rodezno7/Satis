{!! Form::open(['method' => 'post', 'id' => 'form_edit']) !!}
<div class="modal-header">
    <h4 class="modal-title" id="formModal">@lang('rrhh.studies')
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
            <span aria-hidden="true">&times;</span>
        </button>
    </h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>@lang('rrhh.types_studies')</label> <span class="text-danger">*</span>
                <select name="type_study_id" id="type_study_id" class="form-control form-control-sm select2"
                    placeholder="{{ __('rrhh.types_studies') }}" style="width: 100%;">
                    <option value="">{{ __('rrhh.types_studies') }}</option>
                    @foreach ($typeStudies as $type)
                        @if ($type->id == $study->type_study_id)
                            <option value="{{ $type->id }}" selected>{{ $type->value }}</option>
                        @else
                            <option value="{{ $type->id }}">{{ $type->value }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>

		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>@lang('rrhh.institution')</label> <span class="text-danger">*</span>
                {!! Form::text('institution', $study->institution, [
                    'class' => 'form-control form-control-sm',
                    'placeholder' => __('rrhh.institution'),
                    'id' => 'institution',
                    'required',
                ]) !!}
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>@lang('rrhh.title')</label> <span class="text-danger">*</span>
                {!! Form::text('title', $study->title, [
                    'class' => 'form-control form-control-sm',
                    'placeholder' => __('rrhh.title'),
                    'id' => 'title',
                    'required',
                ]) !!}
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>@lang('rrhh.year_graduation')</label> <span class="text-danger">*</span>
                {!! Form::text('year_graduation', $study->year_graduation, [
                    'class' => 'form-control form-control-sm',
                    'id' => 'year_graduation1',
                    'required',
                ]) !!}
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>@lang('rrhh.study_status')</label> <span class="text-danger">*</span>
                <select name="study_status" id="study_status" class="form-control form-control-sm select2"
                    placeholder="{{ __('rrhh.types_studies') }}" style="width: 100%;">
					@if ($study->study_status == 'En curso')
						<option value="en_curso" selected>{{ __('rrhh.in_progress') }}</option>
						<option value="finalizado">{{ __('rrhh.finalized') }}</option>
					@else
						<option value="en_curso">{{ __('rrhh.in_progress') }}</option>
						<option value="finalizado" selected>{{ __('rrhh.finalized') }}</option>
					@endif
                </select>
            </div>
        </div>
		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label>@lang('rrhh.status')</label>
				{!! Form::select('status', [1 => __('rrhh.active'), 0 => __('rrhh.inactive')], $study->status, ['class' => 'form-control select2', 'id' => 'status', 'required', 'style' => 'width: 100%;' ]) !!}
			</div>
		</div>
    </div>
</div>
<div class="modal-footer">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
    <input type="hidden" name="id" value="{{ $study->id }}" id="id">
    <input type="hidden" name="employee_id" value="{{ $employee_id }}" id="employee_id">
    <button type="button" class="btn btn-primary" id="btn_edit_study">@lang('rrhh.update')</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal" onClick="closeModal()">@lang('messages.cancel')</button>
</div>
{!! Form::close() !!}
<script>
    $(document).ready(function() {
        //$.fn.modal.Constructor.prototype.enforceFocus = function() {};
        select2 = $('.select2').select2();
    });

    $("#year_graduation1").datepicker({
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years"
    });

    $("#btn_edit_study").click(function() {
        route = "/rrhh-study-update";
        token = $("#token").val();
        employee_id = $('#employee_id').val();

        var form = $("#form_edit");
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
                    getStudy(employee_id);
                    $('#employee_id').val('');
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
        $('#modal_action').modal({backdrop: 'static'});
        $('#modal_edit_action').modal('hide').data('bs.modal', null);
    }
</script>

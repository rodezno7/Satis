<div class="modal-header">
    <h4 class="modal-title" id="formModal">@lang('rrhh.types_income_discounts')
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </h4>
</div>
<div class="modal-body">
    <form id="form_add" method="post">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                    <label>@lang('rrhh.name')</label> <span class="text-danger">*</span>
                    <input type="text" name='name' id='name' class="form-control"
                        placeholder="@lang('rrhh.name')">
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label>@lang('rrhh.type')</label> <span class="text-danger">*</span>
                    {!! Form::select('type', [1 => __('rrhh.income'), 2 => __('rrhh.discount')], null, [
                        'class' => 'form-control select2',
                        'id' => 'type',
                        'required',
                        'style' => 'width: 100%;',
                    ]) !!}
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label>@lang('rrhh.planilla_column')</label> <span class="text-danger">*</span>
                    <select id="planilla_column" name="planilla_column" class="form-control select2" style="width: 100%" required>
                        @for ($i = 0; $i < count($planillaColumns); $i++)
                            <option value="{{ $i }}"> {{ __($planillaColumns[$i]) }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label>@lang('planilla.percentage')</label>
                    {!! Form::number('percentage', null, [
                        'class' => 'form-control form-control-sm',
                        'placeholder' => __('planilla.percentage'),
                        'id' => 'percentage',
                        'required',
                    ]) !!}
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>
                        <input type="checkbox" name='isss' id='isss'>
                        {{ __('rrhh.affect_isss') }}
                    </label>
                    <br>
                    <label>
                        <input type="checkbox" name='afp' id='afp'>
                        {{ __('rrhh.affect_afp') }}
                    </label>
                    <br>
                    <label>
                        <input type="checkbox" name='rent' id='rent'>
                        {{ __('rrhh.affect_rent') }}
                    </label>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
    <button type="button" class="btn btn-primary" id="btn_add_type_wages">@lang('rrhh.add')</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('messages.cancel')</button>
</div>

<script>
    $( document ).ready(function() {
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
        $('.select2').select2();
    });

    $("#btn_add_type_wages").click(function() {
        route = "/rrhh-types-income-discounts";
        datastring = $("#form_add").serialize();
        token = $("#token").val();
        $.ajax({
            url: route,
            headers: {
                'X-CSRF-TOKEN': token
            },
            type: 'POST',
            dataType: 'json',
            data: datastring,
            success: function(result) {
                if (result.success == true) {
                    Swal.fire({
                        title: result.msg,
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    $("#types_income_discounts-table").DataTable().ajax.reload(null, false);
                    $('#modal').modal('hide');

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
</script>

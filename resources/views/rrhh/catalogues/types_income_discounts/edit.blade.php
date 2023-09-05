<div class="modal-header">
    <h4 class="modal-title" id="formModal">@lang('rrhh.types_income_discounts')
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </h4>
</div>
<div class="modal-body">
    <form id="form_edit" method="post">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                    <label>@lang('rrhh.name')</label> <span class="text-danger">*</span>
                    <input type="text" name='name' id='name1' class="form-control"
                        placeholder="@lang('rrhh.name')" value="{{ $item->name }}">
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label>@lang('rrhh.type')</label> <span class="text-danger">*</span>
                    {!! Form::select('type', [1 => __('rrhh.income'), 2 => __('rrhh.discount')], $item->type, [
                        'class' => 'form-control select2',
                        'id' => 'type1',
                        'required',
                        'style' => 'width: 100%;',
                    ]) !!}
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label>@lang('rrhh.payroll_column')</label> <span class="text-danger">*</span>
                    <select id="payroll_column1" name="payroll_column" class="form-control select2" style="width: 100%" required>
                        @for ($i = 0; $i < count($payrollColumns); $i++)
                            @if ($item->payroll_column == $payrollColumns[$i])
                                <option value="{{ $i }}" selected> {{ __($payrollColumns[$i]) }}</option>
                            @else
                                <option value="{{ $i }}"> {{ __($payrollColumns[$i]) }}</option>
                            @endif
                        @endfor
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
    <button type="button" class="btn btn-primary" id="btn_edit_type_income_discounts">@lang('rrhh.edit')</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('messages.cancel')</button>
</div>

<script>
    $( document ).ready(function() {
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
        $('.select2').select2();
    });

    $("#btn_edit_type_income_discounts").click(function() {
        id = {{ $item->id }}
        route = "/rrhh-types-income-discounts/"+id;
        datastring = $("#form_edit").serialize();
        token = $("#token").val();
        $.ajax({
            url: route,
            headers: {
                'X-CSRF-TOKEN': token
            },
            type: 'PUT',
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

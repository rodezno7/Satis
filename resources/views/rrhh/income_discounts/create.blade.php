{!! Form::open(['method' => 'post', 'id' => 'form_add_income_discount']) !!}
<div class="modal-header">
    <h4 class="modal-title" id="formModal">@lang('rrhh.income_discount')
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
            <span aria-hidden="true">&times;</span>
        </button>
    </h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>@lang('rrhh.option')</label> <span class="text-danger">*</span>
                <select name="type" id="type" class="form-control form-control-sm">
                    <option value="1">{{ __('rrhh.income') }}</option>
                    <option value="2">{{ __('rrhh.discount') }}</option>
                </select>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12" id="div_type_discount">
            <div class="form-group">
                <label>@lang('rrhh.types_discounts')</label> <span class="text-danger">*</span>
                <select name="rrhh_type_discount_id" id="rrhh_type_discount_id"
                    class="form-control form-control-sm select2" placeholder="{{ __('rrhh.types_discounts') }}"
                    style="width: 100%;">
                    <option value="">{{ __('rrhh.types_discounts') }}</option>
                    @foreach ($typeDiscounts as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12" id="div_type_income">
            <div class="form-group">
                <label>@lang('rrhh.types_incomes')</label> <span class="text-danger">*</span>
                <select name="rrhh_type_income_id" id="rrhh_type_income_id" class="form-control form-control-sm select2"
                    placeholder="{{ __('rrhh.types_incomes') }}" style="width: 100%;">
                    <option value="">{{ __('rrhh.types_incomes') }}</option>
                    @foreach ($typeIncomes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>@lang('rrhh.apply_in')</label> <span class="text-danger">*</span>
                <select name="payment_period_id" id="payment_period_id" class="form-control form-control-sm select2"
                    placeholder="{{ __('rrhh.apply_in') }}" style="width: 100%;">
                    <option value="">{{ __('rrhh.apply_in') }}</option>
                    @foreach ($paymentPeriods as $payment)
                        <option value="{{ $payment->id }}">{{ $payment->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>@lang('rrhh.total_value')</label> <span class="text-danger">*</span>
                {!! Form::number('total_value', null, [
                    'class' => 'form-control form-control-sm',
                    'placeholder' => __('rrhh.total_value'),
                    'id' => 'total_value',
                    'required',
                ]) !!}
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>@lang('rrhh.quota_value')</label> <span class="text-danger">*</span>
                {!! Form::number('quota_value', null, [
                    'class' => 'form-control form-control-sm',
                    'placeholder' => __('rrhh.quota_value'),
                    'id' => 'quota_value',
                    'readonly' => 'readonly',
                ]) !!}
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>@lang('rrhh.quota')</label> <span class="text-danger">*</span>
                {!! Form::number('quota', null, [
                    'class' => 'form-control form-control-sm',
                    'placeholder' => __('rrhh.quota'),
                    'id' => 'quota',
                    'required',
                ]) !!}
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>@lang('payroll.start_date')</label> <span class="text-danger">*</span>
                {!! Form::text('start_date', @format_date('now'), [
                    'class' => 'form-control form-control-sm',
                    'placeholder' => __('payroll.start_date'),
                    'id' => 'start_date',
                ]) !!}
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>@lang('payroll.end_date')</label>
                {!! Form::text('end_date', null, [
                    'class' => 'form-control form-control-sm',
                    'placeholder' => __('payroll.end_date'),
                    'id' => 'end_date',
                    'readonly' => 'readonly',
                ]) !!}
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
    <input type="hidden" name="employee_id" value="{{ $employee_id }}" id="employee_id_id">
    <button type="button" class="btn btn-primary" id="btn_add_income_discount">@lang('rrhh.add')</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"
        onClick="closeModal()">@lang('messages.cancel')</button>
</div>
{!! Form::close() !!}
<script>
    $(document).ready(function() {
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
        select2 = $('.select2').select2();

        $('#start_date').datepicker({
            autoclose: true,
            format: datepicker_date_format,
        });

        $('#end_date').datepicker({
            autoclose: true,
            format: datepicker_date_format,
        });

        typeOption();
    });


    $('#type').on('change', function() {
        typeOption();
    });


    function typeOption() {
        let type = $('#type').val();

        $('#div_type_discount').hide();
        $("#type_discount_id").prop('required', false);

        $('#div_type_income').hide();
        $("#type_income_id").prop('required', false);

        //Evaluando si es un ingreso o descuento
        if (type == 1) { //Ingreso
            $('#div_type_income').show();
            $("#type_income_id").prop('required', true);
        } else { //Descuento
            $('#div_type_discount').show();
            $("#type_discount_id").prop('required', true);
        }
    }


    //Get value quota value
    $('#total_value').on('change', function() {
        let total_value = $(this).val();
        let quota = $('#quota').val();
        if (quota != null) {
            let quota_value = total_value / quota;
            $('#quota_value').val(quota_value);
        }
    });

    //Get value quota
    $('#quota').on('change', function() {
        let total_value = $('#total_value').val();
        let quota = $(this).val();
        if (total_value != null) {
            let quota_value = total_value / quota;
            $('#quota_value').val(quota_value);
        }

        calculateDate();
    });


    //Get option payment period
    $('#payment_period_id').on('change', function() {
        calculateDate();
    });


    //Get start date
    $('#start_date').on('change', function() {
        calculateDate();
    });
    

    //Calculate Date
    function calculateDate(){
        let payment_period = $('select[name="payment_period_id"] option:selected').text();
        let start_date = $('#start_date').val();
        start_date = start_date.replace(/\//g, '-');
        let quota = $('#quota').val();

        if (payment_period != "" && quota != "" && start_date != null) {
            start_date = start_date.split("-").reverse().join("-");
            var fecha = Date.parse(start_date);
            fecha = new Date(start_date);
            fecha.setDate(fecha.getDate() + 1);

            if(payment_period == 'Quincenal'){
                $("#end_date").datepicker("setDate", applyQuincena(fecha, quota));
            }

            if(payment_period == 'Mensual'){
                $("#end_date").datepicker("setDate", applyMensual(fecha, quota));
            }
        }
    }


    //Calculate option apply quincena
    function applyQuincena(date, quota){
        let currentDay = date.getDate();
        let currentMonth = date.getMonth() + 1; 
        let currentYear = date.getFullYear();
        var lastDay = new Date(currentYear, currentMonth, 0);
        let quotaNumber = 0;
        let quincena = 0;
        let fecha = '';

        while (quotaNumber <= quota) {
            if(quincena == 0){
                if(currentDay >= 0 && currentDay < 15){
                    quotaNumber++;
                    quincena = 1;
                }
                if(currentDay >= 15 && currentDay <= lastDay.getDate()){
                    quotaNumber++;
                    quincena = 2;
                }
            }
            
            if(quincena > 0){
                if(currentMonth >= 1 && currentMonth <= 12){
                    if(quincena%2==0){ // par
                        lastDay = new Date(currentYear, currentMonth, 0);
                        lastDay = lastDay.getDate();
                        fecha = new Date(currentYear, currentMonth-1, lastDay);
                        currentMonth++;
                    }else{//impar
                        lastDay = 15;
                        fecha = new Date(currentYear, currentMonth-1, lastDay);
                        if(currentMonth > 12){
                            currentMonth = 1;
                            currentYear++;
                        }
                    }  
                    quotaNumber++;
                    quincena++;    
                }
            }            
        }        
        return fecha;
    }

    //Calculate option apply mensual
    function applyMensual(date, quota){
        let currentMonth = date.getMonth() + 1; 
        let currentYear = date.getFullYear();
        var lastDay = '';
        let quotaNumber = 0;
        let fecha = "";

        while (quotaNumber < quota) {
            if(currentMonth >= 0 && currentMonth <= 13){
                currentMonth++;
                quotaNumber++;
                if(currentMonth > 13){
                    currentMonth = 1;
                    currentYear++;
                }
            }

            lastDay = new Date(currentYear, currentMonth-1, 0);
            lastDay =lastDay.getDate();
            fecha = new Date(currentYear, currentMonth-2, lastDay);
        }        
        return fecha;
    }


    //Save income or discount to employee
    $("#btn_add_income_discount").click(function() {
        route = "/rrhh-income-discount";
        token = $("#token").val();
        employee_id = $('#employee_id_id').val();

        var form = $("#form_add_income_discount");
        var formData = new FormData(form[0]);
        formData.append('employee_id', employee_id);

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
                    getIncomeDiscount(employee_id);
                    Swal.fire({
                        title: result.msg,
                        icon: "success",
                        timer: 1000,
                        showConfirmButton: false,
                    });
                    $('#modal_doc').modal('hide').data('bs.modal', null);
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
        $('#modal_doc').modal('hide').data('bs.modal', null);
    }
</script>

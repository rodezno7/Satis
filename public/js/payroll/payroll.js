$( document ).ready(function() {
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    $('.select2').select2();

    var fechaMaxima = new Date();
    fechaMaxima = fechaMaxima.toLocaleDateString("es-ES", {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });

    $("#year").datepicker( {
        format: "yyyy",
        viewMode: "years", 
        minViewMode: "years",
        endDate: fechaMaxima,
    });

    $('#start_date').datepicker({
        autoclose: true,
        format: datepicker_date_format,
    });

    $('#end_date').datepicker({
        autoclose: true,
        format: datepicker_date_format,
    });
});

$('#payroll_type_id').on('change', function() {
    let payroll_type = $('#payroll_type_id').val();
    var route = "/payroll-getPayrollType/" + payroll_type;

    $("#div_day").hide();
    $("#div_isr").hide();
    $("#div_payment_period").show();
    $("#div_month").show();
    $("#div_start_date").show();
    $("#month").val(1).change();
    $("#payment_period_id").val('').change();
    $("#isr_id").val('').change();
    $("#end_date").val('');
    $("#days").val('');
    $("#end_date").prop("readonly", true);

    $.get(route, function(res) {
        if(res.name == 'Planilla de aguinaldos'){
            $("#div_day").hide();
            $("#div_isr").show();
            $("#div_payment_period").hide();
            $("#div_month").hide();
            $("#div_start_date").hide();
            $("#end_date").prop("readonly", false);
        }
    });
});

$('#payment_period_id').on('change', function() {
    paymentPeriod();
});

$('#year').on('change', function() {
    let payroll_type = $('#payroll_type_id').val();
    if(payroll_type != ''){
        var route = "/payroll-getPayrollType/" + payroll_type;

        $.get(route, function(res) {
            if(res.name != 'Planilla de aguinaldos'){
                paymentPeriod();
            }
        });
    }    
});

$('#month').on('change', function() {
    paymentPeriod();
});

$('#start_date').on('change', function() {
    calculateDays();
});

$('#end_date').on('change', function() {
    calculateDays();
});


function paymentPeriod(){
    let payment_period = $('#payment_period_id').val();
    let month = $('#month').val();
    let year = $('#year').val();

    // Ponemos el atributo de solo lectura
    $("#start_date").prop("readonly", true);
    $("#end_date").prop("readonly", true);
    $("#div_day").hide();
    $("#div_isr").hide();
    $("#start_date").val('');
    $("#end_date").val('');
    $("#days").val('');
    $("#isr_id").val('').change();

    if(payment_period != ''){
        var route = "/payroll-getPaymentPeriod/" + payment_period;
        $.get(route, function(res) {
            if(year != ''){
                if (res.name == 'Primera quincena') {
                    firstFortnight(month, year);
                }
        
                if (res.name == 'Segunda quincena') {
                    secondFortnight(month, year);
                }
        
                if (res.name == 'Mensual') {
                    monthly(month, year);
                }
            }

            if(res.name == 'Personalizado'){
                $("#start_date").val('');
                $("#end_date").val('');
                
                // Eliminamos el atributo de solo lectura
                $("#start_date").prop("readonly", false);
                $("#end_date").prop("readonly", false);

                $("#div_isr").show();
                $("#div_day").show();
            }            
        });
    }
}


function calculateDays(){
    var start_date = $('#start_date').val();
    
    var end_date = $('#end_date').val();
    if(end_date != '' && start_date != ''){
        start_date = start_date.replace(/\//g, '-');
        start_date = start_date.split("-").reverse().join("-");
        var startDate = Date.parse(start_date);

        end_date = end_date.replace(/\//g, '-');
        end_date = end_date.split("-").reverse().join("-");
        var endDate = Date.parse(end_date);
    
        days = ((endDate - startDate) / 1000 / 60 / 60 / 24) + 1;
    
        if(days > 1){
            $("#days").val(days);
        }else{
            Swal.fire
            ({
                title: 'Error',
                html: 'El campo fecha de fin debe ser posterior a la fecha de inicio',
                icon: "error",
            });
        }
    }
}


//Calculate option apply first fortnight (primera quincena)
function firstFortnight(month, year){  
    //Start date
    startDate = new Date(year, month-1, 1);
    $("#start_date").datepicker("setDate", startDate);

    //End date
    endDate = new Date(year, month-1, 15);
    $("#end_date").datepicker("setDate", endDate); 
}

//Calculate option apply second fortnight (segunda quincena)
function secondFortnight(month, year){   
    //Start date
    startDate = new Date(year, month-1, 16);
    $("#start_date").datepicker("setDate", startDate);
    
    //End date
    endDate = new Date(year, month, 0);
    $("#end_date").datepicker("setDate", endDate); 
}

//Calculate option apply monthly (mensual)
function monthly(month, year){    
    //Start date
    startDate = new Date(year, month-1, 1);
    $("#start_date").datepicker("setDate", startDate);
    
    //End date
    endDate = new Date(year, month, 0);
    $("#end_date").datepicker("setDate", endDate); 
}


$("#btn_add_payroll").click(function() {
    createPayroll(0);
});

$("#btn_add_calculate_payroll").click(function() {
    createPayroll(1);
});


function createPayroll(calculate){
    route = "/payroll";    
    token = $("#token").val();

    var form = $("#form_add_payroll");
    var formData = new FormData(form[0]);
    formData.append('calculate', calculate);

    $.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        processData: false,
        contentType: false,       
        data: formData,
        success:function(result) {
            if(result.success == true) {
                Swal.fire
                ({
                    title: result.msg,
                    icon: "success",
                    timer: 1000,
                    showConfirmButton: false,
                });
                $("#payroll-table").DataTable().ajax.reload(null, false);
                $('#modal_add').modal( 'hide' ).data( 'bs.modal', null );
            }
            else {
                Swal.fire
                ({
                    title: result.msg,
                    icon: "error",
                });
            }
        },
        error:function(msj){
            errormessages = "";
            $.each(msj.responseJSON.errors, function(i, field){
                errormessages+="<li>"+field+"</li>";
            });
            Swal.fire
            ({
                title: LANG.error_list,
                icon: "error",
                html: "<ul>"+ errormessages+ "</ul>",
            });
        }
    });
}

function closeModal(){
    $('#modal_add').modal( 'hide' ).data( 'bs.modal', null );
}
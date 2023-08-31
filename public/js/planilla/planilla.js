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

$('#payment_period_id').on('change', function() {
    paymentPeriod();
});

$('#year').on('change', function() {
    paymentPeriod();
});

$('#month').on('change', function() {
    paymentPeriod();
});

function paymentPeriod(){
    let payment_period = $('#payment_period_id').val();
    let month = $('#month').val();
    let year = $('#year').val();

    if(payment_period == 4){//Primera Quincena
        firstFortnight(month, year);
    }

    if(payment_period == 5){//Segunda Quincena
        secondFortnight(month, year);
    }

    if(payment_period == 6){//Mensual
        monthly(month, year);
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

    // days = ((endDate - startDate) / 1000 / 60 / 60 / 24) + 1;

    // $("#days").val(days);
}

//Calculate option apply second fortnight (segunda quincena)
function secondFortnight(month, year){     
    //Start date
    startDate = new Date(year, month-1, 16);
    $("#start_date").datepicker("setDate", startDate);
    
    //End date
    endDate = new Date(year, month, 0);
    $("#end_date").datepicker("setDate", endDate);

    // days = ((endDate - startDate) / 1000 / 60 / 60 / 24) + 1;

    // $("#days").val(days);
}

//Calculate option apply monthly (mensual)
function monthly(month, year){     
    //Start date
    startDate = new Date(year, month-1, 1);
    $("#start_date").datepicker("setDate", startDate);
    
    //End date
    endDate = new Date(year, month, 0);
    $("#end_date").datepicker("setDate", endDate);

    // days = ((endDate - startDate) / 1000 / 60 / 60 / 24) + 1;

    // $("#days").val(days);
}


$("#btn_add_planilla").click(function() {
    createPlanilla(false);
});

$("#btn_add_calculate_planilla").click(function() {
    createPlanilla(true);
});


function createPlanilla(calculate){
    route = "/planilla";    
    token = $("#token").val();

    var form = $("#form_add_planilla");
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
                $("#planilla-table").DataTable().ajax.reload(null, false);
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
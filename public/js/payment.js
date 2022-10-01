$(document).ready( function(){
	$(document).on('click', '.add_payment_modal', function(e){
        e.preventDefault();
        var container = $('.payment_modal');

        $.ajax({
            url: $(this).attr("href"),
            dataType: "json",
            success: function(result){
                if(result.status == 'due'){
                    container.html(result.view).modal('show');
                    __currency_convert_recursively(container);
                    $('#paid_on').datepicker({
                        autoclose: true,
                    });
                    container.find('form#transaction_payment_add_form').validate();

                } else {
                    toastr.error(result.msg);
                }
                
            }
        });
    });
    $(document).on('click', '.edit_payment', function(e){
        e.preventDefault();
        var container = $('.edit_payment_modal');

        $.ajax({
            url: $(this).data("href"),
            dataType: "html",
            success: function(result){
                container.html(result).modal('show');
                __currency_convert_recursively(container);
                $('#paid_on').datepicker({
                    autoclose: true,
                    toggleActive: false
                });
                container.find('form#transaction_payment_add_form').validate();
            }
        });
    });

    //enable and disabled Credit Terms
    $("#payment_condition").on('change', function() {
        if ($("#payment_condition").val() == "credit") {
            $('#payment_term_id').attr('disabled', false);
        } else {
            $('#payment_term_id').attr('disabled', true);
        }
    });

    
    $(document).on('click', '.view_payment_modal', function(e){
        e.preventDefault();
        var container = $('.payment_modal');

        $.ajax({
            url: $(this).attr("href"),
            dataType: "html",
            success: function(result){
                $(container).html(result).modal('show');
                __currency_convert_recursively(container);
            }
        });
    });
    $(document).on('click', '.delete_payment', function(e){
        swal({
          title: LANG.sure,
          text: LANG.confirm_delete_payment,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                url: $(this).data("href"),
                method: 'delete',
                dataType: "json",
                success: function(result){
                    if(result.success === true){
                        $('div.payment_modal').modal('hide');
                        $('div.edit_payment_modal').modal('hide');
                        toastr.success(result.msg);
                        if(typeof purchase_table != 'undefined'){
                            purchase_table.ajax.reload();
                        }
                        if(typeof sell_table != 'undefined'){
                            sell_table.ajax.reload();
                        }
                        if(typeof expense_table != 'undefined'){
                            expense_table.ajax.reload();
                        }
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
          }
        });
    });

    //view single payment
    $(document).on("click", '.view_payment', function(){
        var url = $(this).data("href");
        var container = $(".view_modal");
        $.ajax({
            method: "GET",
            url: url,
            dataType: "html",
            success: function(result){
                $(container).html(result).modal("show");
                __currency_convert_recursively(container);
            }
        });
    });

    // Check if the payment note exists
	$(document).on('change', 'input.validate-payment-note', function(e) {
		var payment_note = $(this).val();
        var payment_note_value = $('#payment-note-value').val();
		var _input = $(this);
		var route = "/pos/validatePaymentNote/" + payment_note;

        console.log(payment_note);
        console.log(payment_note_value);

		$('label#payment-note-error').remove();
        $('#btn-payment-form').removeAttr('disabled');
        
		$.get(route, function(res) {
			if (res.flag && (payment_note != payment_note_value)) {
				error = '<label id="payment-note-error" class="error">' + LANG.payment_note_alredy_exists + '</label>';
				$(error).insertAfter(_input.parent('div'));

                $('#btn-payment-form').attr('disabled','disabled');
			}
		});
	});

    // Update payment note by location
    $(document).on('change', 'select#location_id', function(e) {
        // Correlative to payment note
        let idSuc = $('#location_id').val();
        let idDoc = $('input#payment_note_id').val();
    
        $.ajax({
            method: "POST",
            url: '/pos/getCorrelatives',
            data: { 'location_id': idSuc, 'document_type': idDoc },
            dataType: 'text',
            success: function(result) {
                if (result != null) {
                    $('input#note').val(result);
                } else {
                    $('input#note').val(0);
                }
            }
        });
    });
});
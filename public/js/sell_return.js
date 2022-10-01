$(document).ready( function(){

	//For edit pos form
	if($('form#sell_return_form').length > 0){
		pos_form_obj = $('form#sell_return_form');
	} else {
		pos_form_obj = $('form#add_pos_sell_form');
	}
	if($('form#sell_return_form').length > 0 || $('form#add_pos_sell_form').length > 0){
		initialize_printer();
	}

	//Date picker
    $('#transaction_date').datepicker({
        autoclose: true,
        format: datepicker_date_format
    });
	
	/** disabled 5seg form button on submit */
	$(document).on("click", "form#sell_return_form button[type='submit']", function(e) {
		e.preventDefault();
		let btn = $(this);
		btn.attr('disabled', true);

		setTimeout(() => {
			btn.removeAttr('disabled');
		}, 5000);

		$("form#sell_return_form").trigger('submit');
	});

	pos_form_validator = pos_form_obj.validate({
		submitHandler: function(form) {
			var cnf = true;

			// Check if the correlative exists
			if ($('input#flag-correlative').val() == 1) {
				cnf = false;

				var input = $('input#correlative');

				error = '<label id="correlative-error" class="error">' + LANG.correlative_alredy_exists + '</label>';
				$(error).insertAfter(input.parent('div'));

				toastr.error(LANG.correlative_alredy_exists);
			}
			
			if(cnf){
			 	var data = $(form).serialize();
				var url = $(form).attr('action');
				$.ajax({
					method: "POST",
					url: url,
					data: data,
					dataType: "json",
					success: function(result){
						if(result.success == 1){
							toastr.success(result.msg);
							//Check if enabled or not
							if(result.receipt.is_enabled){
								pos_print(result.receipt);
							}
						} else {
							toastr.error(result.msg);
						}
					}
				});
			}
			return false;
		}
	});

	$(document).on("change", "select#document_type", function(){
		var document_type = $(this).val();
		var location_id = $("input#location_id").val();

		$.ajax({
			method: "post",
			url: "/pos/getCorrelatives",
			data: { 'location_id': location_id, 'document_type': document_type },
			dateType: "text",
			success: function(data){
				if(data){
					$("input#correlative").val(data);
				} else{
					$("input#correlative").val(0);
				}
			}
		});
	});

	/** Check if the correlative exists */
	$(document).on('change', 'input#correlative', function(e) {
		var correlative = $(this).val();
		var document = $('select#document_type :selected').val();
		var location = $('input#location_id').val();
		var transaction_id = $('input#parent_id').val();

		var input = $(this);

		var route = "/pos/validateCorrelative/" + location + "/" + document + "/" + correlative + "/"+ transaction_id;

		$('label#correlative-error').remove();
		$('input#flag-correlative').val(0);
        
		$.get(route, function(res) {
			if (res.flag) {
				$('input#flag-correlative').val(1);
				error = '<label id="correlative-error" class="error">' + LANG.correlative_alredy_exists + '</label>';
				$(error).insertAfter(input.parent('div'));
			} else {
				$('input#flag-correlative').val(0);
			}
		});
	});
});


function initialize_printer(){
	if($('input#location_id').data('receipt_printer_type') == 'printer'){
		initializeSocket();
	}
}

function pos_print(receipt){
	//If printer type then connect with websocket
	if(receipt.print_type == 'printer'){

		var content = receipt;
		content.type = 'print-receipt';

		//Check if ready or not, then print.
		if(socket.readyState != 1){
			initializeSocket();
			setTimeout(function() {
				socket.send(JSON.stringify(content));
			}, 700);
		} else {
			socket.send(JSON.stringify(content));
		}

	} else if(receipt.html_content != '') {
		//If printer type browser then print content
		$('#receipt_section').html(receipt.html_content);
		__currency_convert_recursively($('#receipt_section'));
		setTimeout(function(){window.print();}, 1000);
	}
}

// //Set the location and initialize printer
// function set_location(){
// 	if($('input#location_id').length == 1){
// 	       $('input#location_id').val($('select#select_location_id').val());
// 	       //$('input#location_id').data('receipt_printer_type', $('select#select_location_id').find(':selected').data('receipt_printer_ty
// 	}

// 	if($('input#location_id').val()){
// 	       $('input#search_product').prop( "disabled", false ).focus();
// 	} else {
// 	       $('input#search_product').prop( "disabled", true );
// 	}

// 	initialize_printer();
// }

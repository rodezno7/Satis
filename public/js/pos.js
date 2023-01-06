// Identify type of business
app_business = $('#app_business').val();

// Number of decimal places seen in the interface
decimals_in_sales = $('#decimals_in_sales').length > 0 ? $('#decimals_in_sales').val() : 6;

// Number of decimal places to store and use in calculations
price_precision = $('#price_precision').length > 0 ? $('#price_precision').val() : 6;

// Number of decimal places in POS footer
footer_precision = 2;

$(document).ready(function () {
	/**
	 * --------------------------------------------------------------------------------
	 * INITIALIZE
	 * --------------------------------------------------------------------------------
	 */

	set_location();
	set_correlative();
	verifiedIfExistsTaxNumber();
	set_default_customer();

	if ($('form#edit_pos_sell_form').length > 0 || $('form#add_pos_sell_form').length > 0) {
		initialize_printer();
	}

	// Direct sell submit
    sell_form = $('form#add_sell_form');

	if ($('form#edit_sell_form').length) {
		sell_form = $('form#edit_sell_form');
		pos_total_row();
	}

	if (app_business == 'optics') {
		set_default_patient();
	}

	// For edit pos form
	if ($('form#edit_pos_sell_form').length > 0) {
		$('table#pos_table tbody').find("tr").each(function() {
			pos_each_row($(this));
		});

		pos_total_row();

		pos_form_obj = $('form#edit_pos_sell_form');

	} else {
		pos_form_obj = $('form#add_pos_sell_form');
	}

	// On keyup or keypress of form
	$('form').on('keyup keypress', function(e) {
		var keyCode = e.keyCode || e.which;
		
		if (keyCode === 13) { 
			e.preventDefault();
			return false;
		}
	});

	/**
	 * --------------------------------------------------------------------------------
	 * FORM HEADER
	 * --------------------------------------------------------------------------------
	 */

	// DUI mask
	$("input.dui-mask").mask("00000000-0");

	// Datetime picker
	if (app_business == 'optics') {
		$('#datetimepicker2').datetimepicker({
			format: 'L',
			locale: 'es',
			ignoreReadonly: true
		});

	} else {
		$('input#transaction_date').datetimepicker({
			format: moment_date_format,
			ignoreReadonly: true
		});

		$('#transaction_date').datetimepicker({
			format: moment_date_format + ' ' +moment_time_format,
			ignoreReadonly: true,
		});
	}

	// On change of select_location_id select
	$('select#select_location_id').change(function(){
		reset_pos_form();
	});

	// On change of documents select
	$(document).on('change', 'select#documents', function() {
		/*** Star parent correlative */
		let doc_type = $('option:selected', this).text().trim();
		let parent_correlative = $('select#return_parent_id');

		if (doc_type == 'FCF') {
			parent_correlative.prop('disabled', false);
		} else {
			parent_correlative.prop('disabled', true);
			parent_correlative.val("").trigger("change");
			$("input#parent_correlative").val("");
		}
		/** End parent correlative */

		set_correlative();

		$('table#pos_table tbody tr').each(function(){
			pos_each_row($(this));
		})

		pos_total_row();
		verifiedIfExistsTaxNumber();
	});

	/** Get parent correlatives */
	$("select#return_parent_id").select2({
		ajax: {
			type: "get",
			url: "/sells/get-parent-correlative",
			dataType: "json",
			data: function(params){
				let location = $("input#location_id").val();
				let customer = $("select#customer_id").val();
				
				return {
					q: params.term,
					location: location,
					customer: customer
				};
			},
			processResults: function(data) {
				return {
					results: data
				};
			}
		},
		minimumInputLength: 1,
		escapeMarkup: function (markup) {
			return markup;
		}
	});

	/** Set parent correlative */
	$("select#return_parent_id").on("select2:select", function (e) {
		let corr = e.params.data.correlative;
		$("input#parent_correlative").val(corr);
	});

	// On change of exchange_rate input
	$('#exchange_rate').change(function () {
		var curr_exchange_rate = 1;

		if ($(this).val()) {
			curr_exchange_rate = __read_number($(this));
		}

		var total_payable = __read_number($('input#final_total_input'));
		var shown_total = total_payable * curr_exchange_rate;

		$('span#total_payable').text(__currency_trans_from_en(shown_total, false, false, footer_precision));
	});

	// On change price_group select
	$('select#price_group').change(function () {
		var curr_val = $(this).val();
		var prev_value = $('input#hidden_price_group').val();

		$('input#hidden_price_group').val(curr_val);

		if (curr_val != prev_value && $("table#pos_table tbody tr").length > 0) {
			swal({
		        title: LANG.sure,
		        text: LANG.form_will_get_reset,
		        icon: "warning",
		        buttons: true,
		        dangerMode: true,
		    }).then((willDelete) => {
		    	if (willDelete) {
		    		if ($('form#edit_pos_sell_form').length > 0) {
		    			$('table#pos_table tbody').html('');
		    			pos_total_row();

		    		} else {
		    			reset_pos_form();
		    		}
		    		
		    		$('input#hidden_price_group').val(curr_val);
		    		$("select#price_group").val(curr_val).change();

		    	} else {
		    		$('input#hidden_price_group').val(prev_value);
		    		$("select#price_group").val(prev_value).change();
		    	}
		    });
		}
	});

	// On change of select_warehouse_id select or select_cashier_id select
	$("select#select_warehouse_id, select#select_cashier_id").on("change", function () {
		set_warehouse();
	});

	// Set final correlative field
	$(document).on('change', 'select#select_location_id, select#documents', function() {
		set_final_correlative();
	});

	/** CUSTOMERS */

	// Get customer
    $('#customer_id').select2({
    	ajax: {
      		url: '/customers/get_customers',
	        dataType: 'json',
	        delay: 250,
	        data: function (params) {
	            return {
	              q: params.term, // search term
	              page: params.page
	            };
	        },
	        processResults: function (data) {
				return {
					results: data
				};
	        }
      	},
      	minimumInputLength: 1,
      	language: {
		    noResults: function(){
		       	var name = $("#customer_id").data("select2").dropdown.$search.val();
		        return '<button type="button" data-name="' + name + '" class="btn btn-link add_new_customer"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>&nbsp; ' + __translate('add_name_as_new_customer', {'name': name}) +'</button>';
		    }
		},
	    escapeMarkup: function (markup) {
	        return markup;
	    }
    });

	// On select of customer_id select
    $('#customer_id').on('select2:select', function (e) {
		var data = e.params.data;

		if (data.tax_group_id) {
    		$('#tax_group_id').val(data.tax_group_id);
    	} else {
			$('#tax_group_id').val("");
		}
		
		if (data.tax_percent) {
    		$('#tax_group_percent').val(data.tax_percent).trigger("change");
    	} else {
			$('#tax_group_percent').val("").trigger("change");
		}

		if (data.min_amount) {
    		$('#min_amount').val(data.min_amount);
    	} else {
			$('#min_amount').val("");
		}
		
		if (data.max_amount) {
    		$('#max_amount').val(data.max_amount);
		} else {
			$('#max_amount').val("");
		}

		if (data.is_default) {
			$("div#customer_name_div").show();
			$("input#customer_name").val(data.text);
		} else{
			$("div#customer_name_div").hide();
			$("input#customer_name").val("");
		}

		if (data) {
			$('input#allowed_credit').val(data.allowed_credit);
			$('input#is_withholding_agent').val(data.is_withholding_agent);
		}

		// Fill select of customer vehicles
		if ($('#customer_vehicle_id').length > 0) {
			getCustomerVehicles(data.id, null);
		}
	});

	// On click of add_new_customer link
	$(document).on('click', '.add_new_customer', function () {
		$("#customer_id").select2("close");

		var name = $(this).data('name');
		var url = name ? '/customers/get_add_customer/' + name : '/customers/get_add_customer';

		if (app_business == 'optics') {
			var url = name ? '/customer-and-patient/create/' + name : '/customer-and-patient/create';
		}

		$.ajax({
			url: url,
			dataType: 'html',
			success: function (data) {
				$('div.customer_modal').html(data).modal('show');
			}
		});
	});

	// On show of customer_modal modal
	$('.customer_modal').on('shown.bs.modal', function(e) {
		// Shown customer and patient modal
		$(document).on('submit', 'form#form-add-customer-patient', function(e) {
			e.preventDefault();

			$(this).find('button[type="submit"]').prop('disabled', true);

			var data = $(this).serialize();

			$.ajax({
				method: "POST",
				url: $(this).attr("action"),
				dataType: "json",
				data: data,
				success: function(result) {
					if (result.success === true) {
						$(this).find('button[type="submit"]').prop('disabled', false);
						$('div.customer_modal').modal('hide');

						$('select#customer_id').empty()
							.append(new Option(result.customer_name, result.customer_id, true, true));

						if (result.is_patient) {
							$('select#select_patient_id').empty()
								.append(new Option(result.full_name, result.pat_id, true, true));
						}

						$('input#allowed_credit').val(result.allowed_credit);
						$('input#is_withholding_agent').val(result.is_withholding_agent);
						$('input#customer_name').val(result.customer_name);
						
						Swal.fire({
							title: result.msg,
							icon: "success",
							timer: 2000,
							showConfirmButton: false,
						});

					} else {
						$(this).find('button[type="submit"]').prop('disabled', false);
						Swal.fire({
							title: result.msg,
							icon: "error",
							timer: 2000,
							showConfirmButton: false,
						});
					}
				}
			});

			$(this).clear();

			return false;
		});

		// On change of name input
		$(document).on('change', 'form#form-add-customer-patient input#name', function () {
			let is_patient = 0;

			if ($('#is_patient').is(':checked')) {
				is_patient = 1;
			}

			$.ajax({
                method: 'post',
                url: '/pos/check-customer-patient-name',
                dataType: 'json',
                data: {
					is_customer: 1,
					is_patient: is_patient,
					term: $(this).val()
				},
                success: function (result) {
                    if (result.success === 0) {
                        Swal.fire({
							title: LANG.warning,
                            text: result.msg,
                            icon: 'warning',
                        });
                    }
                }
            });
		});
	});

	/** ORDERS */

	// Get orders
    $('select#orders').select2({
    	ajax: {
      		url: '/orders/get_orders',
	        dataType: 'json',
	        delay: 250,
	        data: function (params) {
	            return {
	              q: params.term, // search term
	            };
	        },
	        processResults: function (data) {
				return {
					results: data
				};
	        }
      	},
      	minimumInputLength: 1,
	    escapeMarkup: function (markup) {
	        return markup;
	    }
    });

	// On select of orders select
    $('#orders').on('select2:select', function (e) {
		var data = e.params.data;
		var customer = $("select#customer_id");

		$('input#product_row_count').val(0); // Reset product row count
		
		customer.empty()
			.append(new Option(data.c_name, data.customer_id, true, true))
			.change();
		
		$("input#order_id").val(data.id);
		
		if (data.is_default) {
			$("div#customer_name_div").show();
			$("input#customer_name").val(data.customer_name);
		} else {
			$("div#customer_name_div").hide();
			$("input#customer_name").val("");
		}

		$('input#tax_group_id').val(data.tax_group_id);
		$('input#tax_group_percent').val(data.tax_percent).trigger("change");
		$('input#min_amount').val(data.min_amount);
		$('input#max_amount').val(data.max_amount);

		$("input#allowed_credit").val(data.allowed_credit);
		$("input#is_withholding_agent").val(data.is_withholding_agent);
		$("input#is_exempt").val(data.is_exempt);
		
		$("input#quote-tax-detail").val(data.tax_detail);

		if (data.document_type_id) {
			$("select#documents").val(data.document_type_id).change();
		}

		if (data.selling_price_group_id) {
			$("input#hidden_price_group").val(data.selling_price_group_id);
			$("select#price_group").val(data.selling_price_group_id).change();
		}

		if (data.discount_amount) {
			$("span.total_discount").html(__currency_trans_from_en(data.discount_amount, false, false, footer_precision));
			$("input#discount_type").val(data.discount_type);
			__write_number($("input#discount_amount"), data.discount_amount, false, price_precision);
			$("select#discount_type_modal").val(data.discount_type).change();
			__write_number($("input#discount_amount_modal"), data.discount_amount, false, price_precision);
		}

		if (data.tax_amount) {
			$("span#order_tax").html(__currency_trans_from_en(data.tax_amount, false, false, footer_precision));
			__write_number($("input#original_tax_amount"), data.tax_amount, false, price_precision);
			__write_number($("input#tax_calculation_amount"), data.tax_amount, false, price_precision);
		}

		var location_id = $('input#location_id').val();
		var warehouse_id = $('input#warehouse_id').val();

		// Fill select of customer vehicles
		if ($('#customer_vehicle_id').length > 0) {
			getCustomerVehicles(data.customer_id, data.customer_vehicle_id);
		}

		$('table#pos_table tbody').empty();

		$.each(data.quote_lines, function (i, val) {
			if (app_business == 'workshop') {
				// Only quote lines with null service_parent_id
				if (val.service_parent_id !== null) {
					add_product_row_from_order(val.id, val.variation_id, location_id, warehouse_id);
				}

			} else {
				add_product_row_from_order(val.id, val.variation_id, location_id, warehouse_id);
			}
		});
	});

	/** PRODUCTS */

	// Add Product
	$("#search_product").autocomplete({
		source: function (request, response) {
			var price_group = '';
			if ($('#price_group').length > 0) {
				price_group = $('#price_group').val();
			}
    		$.getJSON("/products/list", { price_group: price_group, location_id: $('input#location_id').val(), warehouse_id: $('input#warehouse_id').val(), term: request.term }, response);
  		},
		minLength: 2,
		response: function(event,ui) {
			if (ui.content.length == 1) {
				ui.item = ui.content[0];
				if ((ui.item.qty_available - ui.item.qty_reserved) > 0) {
					$(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
					$(this).autocomplete('close');
				}
			} else if (ui.content.length == 0) {
            	swal(LANG.no_products_found)
					.then((value) => {
						$('input#search_product').select();
					});
            }
		},
		focus: function (event, ui) {
			if (ui.item.qty_available <= 0) {
				return false;
			}
		},
		select: function (event, ui) {
			if (ui.item.enable_stock != 1 || (ui.item.qty_available - ui.item.qty_reserved) > 0) {
				$(this).val(null);
                pos_product_row(ui.item.variation_id);
			} else{
				alert(LANG.out_of_stock);
			}
		}
	})
	.autocomplete( "instance" )._renderItem = function (ul, item) {
		if ((item.enable_stock == 1 && (item.qty_available - item.qty_reserved) <= 0 && item.clasification != 'kits') ||
			(item.clasification == 'kits' && item.state_disabled == 1)) {
			
			var string = '<li class="ui-state-disabled">&nbsp;'+ item.name;
			
			if (item.type == 'variable') {
        		string += '-' + item.variation;
        	}
        	
			var selling_price = item.selling_price;
        	
			if (item.variation_group_price) {
        		selling_price = item.variation_group_price;
        	}
			
			string += ' (' + item.sub_sku + ')' + '<br>&nbsp;' + LANG.price + ': ' + selling_price + ' (' + LANG.out_of_stock + ')</li>';
            
			return $(string).appendTo(ul);

        } else {
        	var string =  "<div>" + item.name;

        	if (item.type == 'variable') {
        		string += '-' + item.variation;
        	}

        	var selling_price = item.selling_price;

        	if (item.variation_group_price) {
        		selling_price = item.variation_group_price;
        	}

			if (item.clasification != 'product') {
				string += ' (' + item.sub_sku + ')' + '<br> ' + LANG.price + ': ' + selling_price + '</div>';
			} else {
				string += ' (' + item.sub_sku + ')' + '<br> ' + LANG.price + ': ' + selling_price + ' - ' + LANG.stock + ': ' + Number(item.qty_available).toFixed(2) + ' - ' + LANG.reserved + ': ' + Number(item.qty_reserved).toFixed(2) + '</div>';
			}

    		return $("<li>").append(string).appendTo(ul);
        }
    };

	// Press enter on search product to jump into last quantty and vice-versa
	$('#search_product').keydown(function (e) {
		var key = e.which;
		
		if(key == 9) { // The tab key code
			e.preventDefault();

			if ($('#pos_table tbody tr').length > 0) {
				$('#pos_table tbody tr:last').find('input.pos_quantity').focus().select();
			};
		}
	});

	// Quick add product 
	$(document).on('click', 'button.pos_add_quick_product', function () {
		var url = $(this).data('href');
		var container = $(this).data('container');

		$.ajax({
            url: url + '?product_for=pos',
            dataType: "html",
            success: function (result) {
                $(container).html(result).modal('show');
                $('.os_exp_date').datepicker({
			        autoclose: true,
			        format: 'dd-mm-yyyy',
			        clearBtn: true
			    });
            }
        });
	});

	// On change quick_add_product_form form or single_dpp input
	$(document).on('change', 'form#quick_add_product_form input#single_dpp', function () {
		var unit_price = __read_number($(this));

		$('table#quick_product_opening_stock_table tbody tr').each(function () {
			var input = $(this).find('input.unit_price');
			__write_number(input, unit_price, false, price_precision);
			input.change();
		});
	});

	$(document).on("quickProductAdded", function (e) {
		// Check if location is not set then show error message.
		if ($('input#location_id').val() == '') {
			toastr.warning(LANG.select_location);
		} else {
			pos_product_row(e.variation.id);
		}
	});

	/** RESERVATIONS */

	// Get reservations
    $('select#reservations').select2({
    	ajax: {
      		url: '/pos/reservations/get_reservations',
	        dataType: 'json',
	        delay: 250,
	        data: function(params) {
	            return {
	            	q: params.term, // Search term
					location_id: $('#location_id').val()
	            };
	        },
	        processResults: function(data) {
				return {
					results: data
				};
	        }
      	},
      	minimumInputLength: 1,
	    escapeMarkup: function (markup) {
	        return markup;
	    },
		templateSelection: function (data) {
			if (data.id === '') {
				reset_pos_form();
				$('table#pos_table tbody').empty();
				return LANG.pending_reservations;
			}
		
			return data.text;
		},
		placeholder: LANG.pending_reservations,
        allowClear: true
    });

	// On unselecting of reservations select
	$('select#reservations').on('select2:unselecting', function (e) {
		$('#modal_payment').find('.payment-id-input').each(function () {
			$(this).val('');
		});
	});

	// On select of reservations select
	$('#reservations').on('select2:select', function (e) {
		var data = e.params.data;
		var customer = $("select#customer_id");

		$('input#product_row_count').val(0); // Reset product row count
		customer.empty()
			.append(new Option(data.c_name, data.customer_id, true, true))
			.change();
		
		$("input#reservation_id").val(data.id);
		
		if (data.is_default) {
			$("div#customer_name_div").show();
			$("input#customer_name").val(data.customer_name);
		} else{
			$("div#customer_name_div").hide();
			$("input#customer_name").val("");
		}

		// $('input#tax_group_id').val(data.tax_group_id);
		// $('input#tax_group_percent').val(data.tax_percent).trigger("change");
		// $('input#min_amount').val(data.min_amount);
		// $('input#max_amount').val(data.max_amount);

		$("input#allowed_credit").val(data.allowed_credit);
		$("input#is_withholding_agent").val(data.is_withholding_agent);

		if (data.document_type_id) {
			$("select#documents").val(data.document_type_id).change();
		}

		if (data.selling_price_group_id) {
			$("input#hidden_price_group").val(data.selling_price_group_id);
			$("select#price_group").val(data.selling_price_group_id).change();
		}

		if (data.discount_amount) {
			$("span.total_discount").html(__currency_trans_from_en(data.discount_amount, false, false, footer_precision));
			$("input#discount_type").val(data.discount_type);
			__write_number($("input#discount_amount"), data.discount_amount, false, price_precision);
			$("select#discount_type_modal").val(data.discount_type).change();
			__write_number($("input#discount_amount_modal"), data.discount_amount, false, price_precision);
		}

		if (data.tax_amount) {
			$("span#order_tax").html(__currency_trans_from_en(data.tax_amount, false, false, footer_precision));
			__write_number($("input#original_tax_amount"), data.tax_amount, false, price_precision);
			__write_number($("input#tax_calculation_amount"), data.tax_amount, false, price_precision);
		}

		var location_id = $('input#location_id').val();
		var warehouse_id = $('input#warehouse_id').val();

		if (data.employee_id) {
			$('input#input-commission-agent').val(data.agent_code);
			$('input#txt-commission-agent').val(data.employee);
			$('input#commission_agent').val(data.employee_id);
		}

		$('table#pos_table tbody').empty();

		$.each(data.quote_lines, function(i, val){
			add_product_row_from_reservation(val.id, val.variation_id, location_id, warehouse_id);
		});

		$('div#payment_rows_div').empty();

		$('#payment_row_index').val('0');

		$.each(data.payment_lines, function(i, val) {
			add_payment_row_from_reservation(i, i, val.id);
		});

		// let payments_form = document.getElementById('div-payments-form');

		// $('#div-payments-form').find('input:text, textarea').each(function() {
		// 	$(this).val('');
		// 	$(this).prop('disabled', true);
		// });

		// $('#div-payments-form input').prop('disabled', true);

		// $('#btn-remove-payment').hide();
	});

	// Check if the correlative exists
	$(document).on('change', 'input#correlatives, select#documents, select#select_location_id', async function (e) {
		await sleep(500);

		var correlative = $('input#correlatives').val();
		var document = $('select#documents :selected').val();
		var location = $('select#select_location_id :selected').val();

		var _input = $('#correlatives');

		var route = "/pos/validateCorrelative/" + location + "/" + document + "/" + correlative;

		$('label#correlative-error').remove();
		$('input#flag-correlative').val(0);
        
		if (correlative.length > 0 && document.length > 0 && location.length > 0) {
			$.get(route, function (res) {
				if (res.flag) {
					$('input#flag-correlative').val(1);
					error = '<label id="correlative-error" class="error">' + LANG.correlative_alredy_exists + '</label>';
					$(error).insertAfter(_input.parent('div'));
	
				} else {
					$('input#flag-correlative').val(0);
				}
			});
		}
	});

	/** PATIENTS */

	if (app_business == 'optics') {
		// On click of add_new_patient button
		$(document).on('click', '.add_new_patient', function () {
			$("#select_patient_id").select2("close");
	
			var name = $(this).data('name');
			var url = name ? '/patients/create/' + name : '/patients/create';
	
			$.ajax({
				url: url,
				dataType: 'html',
				success: function (data) {
					$('div.patient_modal').html(data).modal('show');
				}
			});
		});

		// Get patients
		$('#select_patient_id').select2({
			ajax: {
				url: '/patients_lab/get_patients',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term, // search term
						page: params.page
					};
				},
				processResults: function (data) {
					return {
						results: data
					};
				}
			},
			minimumInputLength: 1,
			language: {
				noResults: function () {
					var name = $("#select_patient_id").data("select2").dropdown.$search.val();
					return '<button type="button" data-name="' + name + '" class="btn btn-link add_new_patient"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>&nbsp; ' + __translate('add_name_as_new_patient', { 'name': name }) + '</button>';
				}
			},
			escapeMarkup: function (markup) {
				return markup;
			}
		});

		$(document).on('change', 'select#reservations', function() {
			show_payment_note();
		});
	}

	/**
	 * --------------------------------------------------------------------------------
	 * FORM BODY
	 * --------------------------------------------------------------------------------
	 */

	sell_form_validator = sell_form.validate();

	// POS form validator
	pos_form_validator = pos_form_obj.validate({
		submitHandler: function(form) {
			var cnf = true;

			/** If negative quantity, parent correlative required */
			let doc_type = $('select#documents option:selected').text().trim();
			let parent_doc = $('select#return_parent_id');
			$('table#pos_table tbody tr').each(function() {
				let qty = $(this).find('input.pos_quantity').val();

				if (qty < 0 && doc_type == 'FCF' && !parent_doc.val()) {
					cnf = false;
					swal(
						LANG.notice,
						LANG.parent_correlative_required,
						"error"
					).then(function () {
						parent_doc.trigger('focus');
					});
				}
			});

			if ($('input#document_validate').val() == 0 && $.trim($('select[name="documents"] option:selected').text()) === 'CCF') {
				// Check if the client has nit and nrc
				cnf = false;
				swal(LANG.notice, LANG.customer_has_no_nit_nrc, "error");
			}

			// Ignore if the difference is less than 0.5
			if ($('input#in_balance_due').val() >= 0.1 && $('input#allowed_credit').val() == '1') {
				cnf = confirm(LANG.paid_amount_is_less_than_payable);

				if (cnf) {
					var total_paying = __read_number($("input#total_paying_input"));
					total_paying > 0 ? $("input#is_credit").val('2') : $("input#is_credit").val('1');
				}

			} else if (($('input#allowed_credit').val() == '0' && $("input#is_credit").val() == "1" && $('input#partial_payment_any_customer').val() == 0) ||
				($('input#in_balance_due').val() >= 0.1 && $('input#allowed_credit').val() == '0' && $('input#partial_payment_any_customer').val() == 0)) {
				cnf = false;
				swal(LANG.notice, LANG.customer_has_no_credit_allowed, "info");
			}

			// Check if the correlative exists
			if ($('input#flag-correlative').val() == 1) {
				cnf = false;

				var _input = $('input#correlatives');

				error = '<label id="correlative-error" class="error">' + LANG.correlative_alredy_exists + '</label>';
				$(error).insertAfter(_input.parent('div'));

				toastr.error(LANG.correlative_alredy_exists);
			}

			// Check if correlative is reaching its limit
			if ($('#flag-reservation').val() == 0) {
				let remaining_correlatives = parseInt($('#final-correlative').val()) - parseInt($('#correlatives').val());
	
				// Warning message
				if (remaining_correlatives <= 5 && remaining_correlatives >= 0) {
					Swal.fire({
						title: __translate('correlatives_warning', { 'number': remaining_correlatives }),
						icon: 'warning',
						showConfirmButton: true,
					});
	
				// Error message. Does not allow to register the sale.
				} else if (remaining_correlatives < 0) {
					cnf = false;
	
					Swal.fire({
						title: __translate('correlatives_error'),
						icon: 'error',
						showConfirmButton: true,
					});
				}
			}

			if (app_business == 'optics') {
				// Check if the payment note exists
				if ($('input#flag-payment-note').val() == 1) {
					cnf = false;
	
					var _input = $('input.validate-payment-note');
	
					$('input#flag-payment-note').val(1);
	
					error = '<label id="payment-note-error" class="error">' + LANG.payment_note_alredy_exists + '</label>';
					$(error).insertAfter(_input.parent('div'));
	
					toastr.error(LANG.payment_note_alredy_exists);
				}
			}
			
			if (cnf) {
				$('div.pos-processing').show();

			 	var data = $(form).serialize();
				data = data + '&status=final&is_quotation=0';

				var url = $(form).attr('action');

				// Choose reservation route
				if ($('#flag-reservation').val() == 1) {
					var url = $('#reservation-route').val();
				}

				$.ajax({
					method: 'POST',
					url: url,
					data: data,
					dataType: 'json',
					success: function(result){
						if (result.success == 1) {
							// Show lab order modal
							if (app_business == 'optics') {
								if (result.show_modal) {
									var patientid = $('select#select_patient_id').val();

									if (patientid != '') {
										Swal.fire({
											title: LANG.register_lab_order,
											text: LANG.register_lab_order_text,
											icon: 'question',
											showCancelButton: true,
											confirmButtonColor: '#3085d6',
											cancelButtonColor: '#d33',
											confirmButtonText: LANG.yes,
											cancelButtonText: LANG.not,
										}).then((resul) => {
											if (resul.isConfirmed) {
												$.ajax({
													type: "GET",
													url: "/pos/get_lab_order/" + result.transaction_id + "/" + patientid,
													dataType: "html",
													success: function(data){
														$("div.add_lab_order_modal").html(data).modal("show");
													}
												});
											}
										});
									}
								}
							}

							// Reset fields
							$('#flag-reservation').val(0);

							$('#modal_payment').find('.payment-id-input').each(function () {
								$(this).val('');
							});

							$('#modal_payment').modal('hide');
							
							toastr.success(result.msg);

							reset_pos_form();

							// Check if enabled or not
							if (app_business != 'optics') {
								if (result.receipt.is_enabled) {
									pos_print(result.receipt);
								}
							}

							get_recent_transactions('final', $('div#tab_final'));

						} else {
							toastr.error(result.msg);
						}

						$('div.pos-processing').hide();

						// Reset variables
						$('input#is_cash').val('0');
						$('input#is_credit').val('0');
					}
				});
			}

			return false;
		}
	});

	// Update line total and check for quantity not greater than max quantity
	$('table#pos_table tbody').on('change', 'input.pos_quantity', function () {
		if (sell_form_validator) {
			sell_form_validator.element($(this));
		}

		if (pos_form_validator) {
			pos_form_validator.element($(this));
		}

		// var max_qty = parseFloat($(this).data('rule-max'));
		var entered_qty = __read_number($(this));
		var doc_tax_inc = $('select#documents :selected').data('tax_inc');
		var doc_tax_exempt = $('select#documents :selected').data('tax_exempt');

		var tr = $(this).parents('tr');

		// var unit_price_inc_tax = __read_number(tr.find('input.pos_unit_price_inc_tax'));
		var unit_price_exc_tax = __read_number(tr.find('input#u_price_exc_tax'));
		var unit_price_inc_tax = __read_number(tr.find('input#u_price_inc_tax'));
		
		var line_exc_tax_total = entered_qty * unit_price_exc_tax;	
		var line_inc_tax_total = entered_qty * unit_price_inc_tax;

		var pos_line_total = doc_tax_inc || doc_tax_exempt ? line_exc_tax_total : line_inc_tax_total;

		__write_number(tr.find('input#unit_price_exc_tax'), line_exc_tax_total, false, price_precision);
		__write_number(tr.find('input#unit_price_inc_tax'), line_inc_tax_total, false, price_precision);

		__write_number(tr.find('input.pos_line_total'), pos_line_total, false, price_precision);
		tr.find('span.pos_line_total_text').text(__currency_trans_from_en(pos_line_total, true, false, decimals_in_sales));
		
		pos_total_row();
	});

	// If change in unit price update price including tax and line total
	$('table#pos_table tbody').on('change', 'input.pos_unit_price', function () {
		var unit_price = __read_number($(this));

		var tr = $(this).parents('tr');

		// Calculate discounted unit price
		var discounted_unit_price = calculate_discounted_unit_price(tr);

		var tax_rate = tr.find('select.tax_id').find(':selected').data('rate');
		var quantity = __read_number(tr.find('input.pos_quantity'));

		var unit_price_inc_tax = __add_percent(discounted_unit_price, tax_rate);
		var line_total = quantity * unit_price_inc_tax;

		__write_number(tr.find('input.pos_unit_price_inc_tax'), unit_price_inc_tax, false, price_precision);
		__write_number(tr.find('input.pos_line_total'), line_total, false, price_precision);

		tr.find('span.pos_line_total_text').text(__currency_trans_from_en(line_total, true, false, decimals_in_sales));

		pos_each_row(tr);
		pos_total_row();
		round_row_to_iraqi_dinnar(tr);
	});

	// If change in tax rate then update unit price according to it
	$('table#pos_table tbody').on('change', 'select.tax_id', function () {
		var tr = $(this).parents('tr');

		var tax_rate = tr.find('select.tax_id').find(':selected').data('rate');
		var unit_price_inc_tax = __read_number(tr.find('input.pos_unit_price_inc_tax'));

		var discounted_unit_price = __get_principle(unit_price_inc_tax, tax_rate);
		var unit_price = get_unit_price_from_discounted_unit_price(tr, discounted_unit_price);
		
		__write_number(tr.find('input.pos_unit_price'), unit_price, false, price_precision);
		
		pos_each_row(tr);
	});

	// If change in unit price including tax, update unit price
	$('table#pos_table tbody').on('change', 'input.pos_unit_price_inc_tax', function () {
		var unit_price_inc_tax = __read_number($(this));
		var doc_tax_inc = $('select#documents :selected').data('tax_inc');
		var doc_tax_exempt = $('select#documents :selected').data('tax_exempt');

		var tr = $(this).parents('tr');
		
		var quantity = __read_number(tr.find('input.pos_quantity'));

		var tax_percent = __read_number(tr.find('input#tax_percent')) + 1;
		var new_price_exc_tax = doc_tax_inc || doc_tax_exempt ? unit_price_inc_tax : unit_price_inc_tax / tax_percent;
		unit_price_inc_tax = doc_tax_inc || doc_tax_exempt ? unit_price_inc_tax * tax_percent : unit_price_inc_tax; ;

		/** Setting new values for unit prices */
		__write_number(tr.find('input#u_price_exc_tax'), new_price_exc_tax, false, price_precision);
		__write_number(tr.find('input#u_price_inc_tax'), unit_price_inc_tax, false, price_precision);
		// var pos_unit_price_inc_tax = doc_tax_inc || doc_tax_exempt ? new_price_exc_tax : unit_price_inc_tax

		var line_exc_tax_total = quantity * new_price_exc_tax;
		var line_inc_tax_total = doc_tax_inc || doc_tax_exempt ? line_exc_tax_total : quantity * unit_price_inc_tax;

		// var line_total = quantity * unit_price_inc_tax;
		// var discounted_unit_price = __get_principle(unit_price_inc_tax, tax_rate);
		// var unit_price = get_unit_price_from_discounted_unit_price(tr, new_price_exc_tax);

		var discounted = calculate_discounted(tr, line_inc_tax_total, line_exc_tax_total);

		/** Setting new values for total unit prices */
		__write_number(tr.find('input#unit_price_exc_tax'), discounted.row_discounted_unit_exc_price, false, price_precision);
		__write_number(tr.find('input#unit_price_inc_tax'), discounted.row_discounted_unit_inc_price, false, price_precision);

		/** Set values for edit price product modal */
		__write_number(tr.find('input.pos_unit_price'), unit_price_inc_tax, false, price_precision);
		__write_number(tr.find('input.pos_line_total'), discounted.row_discounted_unit_inc_price, false, price_precision);

		/** Set values for line total */
		tr.find('span.pos_line_total_text').text(__currency_trans_from_en(discounted.row_discounted_unit_inc_price, true, false, decimals_in_sales));

		pos_each_row(tr);
		pos_total_row();
	});

	// Change max quantity rule if lot number changes
	$('table#pos_table tbody').on('change', 'select.lot_number', function () {
		var qty_element = $(this).closest('tr').find('input.pos_quantity');

		if ($(this).val()) {
			var lot_qty = $('option:selected', $(this)).data('qty_available');
			var max_err_msg = $('option:selected', $(this)).data('msg-max');

			qty_element.attr("data-rule-max-value", lot_qty);
			qty_element.attr("data-msg-max-value", max_err_msg);

			qty_element.rules("add", {
				'max-value': lot_qty,
				messages: {
				    'max-value': max_err_msg,
				}
			});

		} else {
			var default_qty = qty_element.data('qty_available');
			var default_err_msg = qty_element.data('msg_max_default');

			qty_element.attr( "data-rule-max-value", );
			qty_element.attr( "data-msg-max-value", default_err_msg);

			qty_element.rules( "add", {
				'max-value': default_qty,
				messages: {
				    'max-value': default_err_msg,
				}
			});
		}

		qty_element.trigger('change');
	});

	// Change in row discount type or discount amount
	$('table#pos_table tbody').on('change', 'select.row_discount_type, input.row_discount_amount', function () {
		var tr = $(this).parents('tr');

		var doc_tax_inc = $('select#documents :selected').data('tax_inc');

		// Calculate discounted unit price
		var discounted_unit_price = calculate_discounted_unit_price(tr);

		// var tax_rate = tr.find('select.tax_id').find(':selected').data('rate');
		var quantity = __read_number(tr.find('input.pos_quantity'));

		// var unit_price_inc_tax = __add_percent(discounted_unit_price, tax_rate);
		// var line_total = quantity * unit_price_inc_tax;

		var unit_price_exc_tax = discounted_unit_price.unit_exc_price;
		var unit_price_inc_tax = discounted_unit_price.unit_inc_price;

		var price_exc_tax_total = quantity * unit_price_exc_tax;
		var price_inc_tax_total = doc_tax_inc ? quantity * unit_price_inc_tax : quantity * unit_price_exc_tax;

		// __write_number(tr.find('input.pos_unit_price_inc_tax'), unit_price_inc_tax, false, price_precision);
		__write_number(tr.find('input#unit_price_exc_tax'), price_exc_tax_total, false, price_precision);
		__write_number(tr.find('input#unit_price_inc_tax'), price_inc_tax_total, false, price_precision);
		__write_number(tr.find('input.pos_line_total'), price_inc_tax_total, false, price_precision);

		tr.find('span.pos_line_total_text').text(__currency_trans_from_en(price_inc_tax_total, true, false, decimals_in_sales));
		
		pos_each_row(tr);
		pos_total_row();
		round_row_to_iraqi_dinnar(tr);
	});

	// Remove row on click on remove row
	$('table#pos_table tbody').on('click', 'i.pos_remove_row', function () {
		swal({
          title: LANG.sure,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
            	$(this).parents('tr').remove();
				pos_total_row();
            }
        });
	});

	// On keypress of pos_quantity input
	$('#pos_table').on('keypress', 'input.pos_quantity', function (e) {
		var key = e.which;

		if(key == 13) { // the enter key code
			$('#search_product').focus();
		}
	});

	/**
	 * --------------------------------------------------------------------------------
	 * FORM FOOTER
	 * --------------------------------------------------------------------------------
	 */

	// Cancel the invoice
	$('button#pos-cancel').click(function () {
		swal({
          title: LANG.sure,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
            	reset_pos_form();
            }
        });
	});

	// Save invoice as draft
	$('button#pos-draft').click(function () {
		// Check if product is present or not
		if ($('table#pos_table tbody').find('.product_row').length <= 0) {
			toastr.warning(LANG.no_products_added);
			return false;
		}

		var is_valid = isValidPosForm();

		if (is_valid != true) {
			return;
		}

		var data = pos_form_obj.serialize();
		data = data + '&status=draft&is_quotation=0';
		
		var url = pos_form_obj.attr('action');

		$.ajax({
			method: "POST",
			url: url,
			data: data,
			dataType: "json",
			success: function (result) {
				if (result.success == 1) {
					reset_pos_form();
					toastr.success(result.msg);
					get_recent_transactions('draft', $('div#tab_draft'));
				} else {
					toastr.error(result.msg);
				}
			}
		});
	});

	// Save invoice as quotation
	$('button#pos-quotation').click(function () {
		// Check if product is present or not
		if ($('table#pos_table tbody').find('.product_row').length <= 0) {
			toastr.warning(LANG.no_products_added);
			return false;
		}

		var is_valid = isValidPosForm();

		if (is_valid != true) {
			return;
		}

		var data = pos_form_obj.serialize();
		data = data + '&status=draft&is_quotation=1';

		var url = pos_form_obj.attr('action');

		$.ajax({
			method: "POST",
			url: url,
			data: data,
			dataType: "json",
			success: function (result) {
				if (result.success == 1) {
					reset_pos_form();
					toastr.success(result.msg);

					// Check if enabled or not
					if (result.receipt.is_enabled) {
						pos_print(result.receipt);
					}

					get_recent_transactions('quotation', $('div#tab_quotation'));

				} else {
					toastr.error(result.msg);
				}
			}
		});
	});

	// Finalize invoice, open payment modal
	$('button#pos-finalize').click(function () {
		// Check if product is present or not
		if ($('table#pos_table tbody').find('.product_row').length <= 0) {
			toastr.warning(LANG.no_products_added);
			return false;
		}

		// Check if it's click on reservation button (0: No 1: Yes)
		$('#flag-reservation').val(0);

		$('#selected_payment_method').val('multiple');

		let document_id = $('#documents').val();

		if (
			// FCF validation
			(document_id == $('#fcf_document').val() && parseFloat($('#final_total_input').val().replace(',', '')) >= parseFloat($('#fcf_document').data('max-operation'))) ||
			// CCF validation
			(document_id == $('#ccf_document').val() && parseFloat($('#final_total_input').val().replace(',', '')) >= parseFloat($('#ccf_document').data('max-operation')))
		) {
			// Request DUIs for FCF or CCF equal to or greater than max operation
			$('#document_validation_modal').modal('show');

		} else {
			// Clear data
			$('#delivered_by').val('');
			$('#delivered_by_dui').val('');
			$('#delivered_by_passport').val('');
			$('#received_by').val('');
			$('#received_by_dui').val('');
			$('#check_foreign').prop('checked', false);

			$('#modal_payment').modal('show');
		}
	});

	// Finalize without showing payment options
	$('button.pos-express-finalize').click(function () {
		$(this).attr('disabled', true);

		if (app_business == 'workshop') {
			// Check if it's the default customer
			if ($('#default_customer_id').val() == $('#customer_id option:selected').val()) {
				$('#customer_vehicle_id').removeAttr('required');

			} else {
				$('#customer_vehicle_id').prop('required', true);
			}
		}

		// Check if product is present or not
		if ($('table#pos_table tbody').find('.product_row').length <= 0) {
			toastr.warning(LANG.no_products_added);
			$(this).removeAttr('disabled');
			return false;
		}

		var pay_method = $(this).data('pay_method');

		$('#selected_payment_method').val(pay_method);

		let document_id = $('#documents').val();

		if (
			// FCF validation
			(document_id == $('#fcf_document').val() && parseFloat($('#final_total_input').val().replace(',', '')) >= parseFloat($('#fcf_document').data('max-operation'))) ||
			// CCF validation
			(document_id == $('#ccf_document').val() && parseFloat($('#final_total_input').val().replace(',', '')) >= parseFloat($('#ccf_document').data('max-operation')))
		) {
			// Request DUIs for FCF or CCF equal to or greater than max operation
			$('#document_validation_modal').modal('show');

		} else {
			// Clear data
			$('#delivered_by').val('');
			$('#delivered_by_dui').val('');
			$('#delivered_by_passport').val('');
			$('#received_by').val('');
			$('#received_by_dui').val('');
			$('#check_foreign').prop('checked', false);

			// Check for remaining balance & add it in 1st payment row
			var total_payable = __read_number($('input#final_total_input'));
			var total_paying = __read_number($('input#total_paying_input'));

			if (total_payable > total_paying) {
				var bal_due = total_payable - total_paying;
	
				var first_row = $('#payment_rows_div').find('.payment-amount').first();
				var first_row_val = __read_number(first_row);
				first_row_val = first_row_val + bal_due;

				if ($('#flag-first-row').val() == 0) {
					__write_number(first_row, first_row_val);
				}

				first_row.trigger('change');
			}
	
			$("input#is_credit").val("0");
			
			// Change payment method
			$('#payment_rows_div').find('.payment_types_dropdown').first().val(pay_method);
			
			if (pay_method == 'card') {
				$('div#card_details_modal').modal('show');
	
			} else if (pay_method == 'suspend') {
				$('div#confirmSuspendModal').modal('show');
	
			} else if (pay_method == 'credit') {
				if ($("input#allowed_credit").val() == "1" || $("input#partial_payment_any_customer").val() == 0) {
					$("input#is_credit").val("1");
					// $("input#is_cash").val("0");
					// __write_number($('input#in_balance_due'), $("input#final_total_input").val(), false, price_precision);

					$.ajax({
						method: 'get',
						url: '/payment-terms/get-payment-terms',
						dataType: 'json',
						success: function(terminos){
							Swal.fire({
								title: LANG.payment_terms,
								input: 'select',
								inputOptions: {
									terminos
								},
								inputPlaceholder: LANG.select_payment_term,
								showCancelButton: true,
								inputValidator: (payment_term) => {
									if (payment_term) {
										$("input#pay_term_number").val(payment_term);
										pos_form_obj.submit();
									} else {
										Swal.fire(
											LANG.notice,
											LANG.payment_term_not_chosen,
											'warning'
										);
									}
								}
							});
						}
					});
	
				} else {
					swal(LANG.notice, LANG.customer_has_no_credit_allowed, "info");
				}

			} else {
				$("input#is_credit").val("0");
				// $("input#is_cash").val("1");
				pos_form_obj.submit();
			}
		}

		$('button.pos-express-finalize').removeAttr('disabled');
	});

	// On save card details
	$('button#pos-save-card').click(function () {
		$('input#card_number_0').val($('#card_number').val());
		$('input#card_holder_name_0').val($('#card_holder_name').val());
		$('input#card_transaction_number_0').val($('#card_transaction_number').val());
		$('select#card_type_0').val($('#card_type').val());
		$('input#card_month_0').val($('#card_month').val());
		$('input#card_year_0').val($('#card_year').val());
		$('input#card_security_0').val($('#card_security').val());

		// Avoid credit validation in direct card payment
		$('input#is_cash').val('1');

		$('div#card_details_modal').modal('hide');

		pos_form_obj.submit();
	});

	// Avoid credit validation in multiple payment
	$('button#pos-save').on('click', function(e) {
		if (app_business == 'optics') {
			$('input#is_cash').val('1');

		} else {
			e.preventDefault();

			$(this).prop('disabled', true);
	
			$('input#is_cash').val('1');
	
			setTimeout(() => {
				$(this).prop('disabled', false);
			}, 10000);
	
			$('form#add_pos_sell_form').trigger('submit');
		}
	});

	// On click of pos-suspend button
	$('button#pos-suspend').click(function () {
		$('input#is_suspend').val(1);
		$('div#confirmSuspendModal').modal('hide');
		pos_form_obj.submit();
		$('input#is_suspend').val(0);
	});

	// Updates for add sell
	$('select#discount_type, input#discount_amount, input#shipping_charges').on("change", function () {
		pos_total_row();
	});

	// On change of tax_rate_id select
	$('select#tax_rate_id').change(function () {
		var tax_rate = $(this).find(':selected').data('rate');
		__write_number($('input#tax_calculation_amount'), tax_rate, false, price_precision);
		pos_total_row();
	});

	// On click of submit-sell button
	$('button#submit-sell').click(function () {
		// Check if product is present or not
		if ($('table#pos_table tbody').find('.product_row').length <= 0) {
			toastr.warning(LANG.no_products_added);
			return false;
		}

		if (sell_form.valid()) {
			sell_form.submit();
		}
	});

	// On click of reservation-finalize button
	$('button#reservation-finalize').click(function () {
		// Check if product is present or not
		if ($('table#pos_table tbody').find('.product_row').length <= 0) {
			toastr.warning(LANG.no_products_added);
			return false;
		}

		// Check if it's click on reservation button (0: No 1: Yes)
		$('#flag-reservation').val(1);

		if ($('#is_edit').length <= 0) {
			$('#modal_payment').modal('show');
			
		} else {
			$('input#is_cash').val('1');
			pos_form_obj.submit();
		}
	});

	if (app_business == 'optics') {
		// On click of btn_inflow button
		$(document).on('click', 'button#btn_inflow', function(e){
			e.preventDefault();

			var cashier_id = $('input#cashier_id').val();
			var location_id = $('input#location_id').val();

			if (cashier_id > 0) {
				$.ajax({
					type: 'GET',
					url: '/inflow-outflow/create/input',
					dataType: 'html',
					data: { cashier_id: cashier_id, location_id: location_id },
					success: function (data) {
						$('div.inflow_outflow_modal').html(data).modal('show');
					}
				});

			} else {
				toastr.warning(LANG.must_select_cash_register_io);
			}	
		});

		// On click of btn_outflow button
		$(document).on('click', 'button#btn_outflow', function(e){
			e.preventDefault();

			var cashier_id = $('input#cashier_id').val();
			var location_id = $('input#location_id').val();

			if (cashier_id > 0) {
				$.ajax({
					type: 'GET',
					url: '/inflow-outflow/create/output',
					dataType: 'html',
					data: { cashier_id: cashier_id, location_id: location_id },
					success: function (data) {
						$('div.inflow_outflow_modal').html(data).modal('show');
					}
				});

			} else {
				toastr.warning(LANG.must_select_cash_register_io);
			}	
		});
	}

	/**
	 * --------------------------------------------------------------------------------
	 * PAYMENT MODAL
	 * --------------------------------------------------------------------------------
	 */

	// On show of modal_payment modal
	$('#modal_payment').on('shown.bs.modal', function () {
		$('#modal_payment').find('input').filter(':visible:first').focus().select();
	});

	// Fix select2 input issue on modal
	$('#modal_payment').find('.select2').each(function () {
		$(this).select2({
			dropdownParent: $('#modal_payment')
		});
	});

	// On click of add-payment-row button
	$('button#add-payment-row').click(function () {
		var row_index = $('#payment_row_index').val();

		$.ajax({
			method: 'POST',
			url: '/sells/pos/get_payment_row',
			data: { row_index: row_index },
			dataType: 'html',
			success: function (result) {
				if (result) {
					var appended = $('#payment_rows_div').append(result);

					var total_payable = __read_number($('input#final_total_input'));
					var total_paying = __read_number($('input#total_paying_input'));
					var b_due = total_payable - total_paying;

					$(appended).find('input.payment-amount').focus();
					$(appended).find('input.payment-amount').last().val(__currency_trans_from_en(b_due, false)).change().select();

					__select2($(appended).find('.select2'));

					$('#payment_row_index').val(parseInt(row_index) + 1);
				}
			}
		});
	});

	// On change of payment-amount
	$(document).on('change', '.payment-amount', function () {
		calculate_balance_due();
	});

	// On click of remove_payment_row button
	$(document).on('click', '.remove_payment_row', function () {
		swal({
			title: LANG.sure,
			icon: "warning",
			buttons: true,
			dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
            	$(this).closest('.payment_row').remove();
				calculate_balance_due();
            }
        });
	});

	if (app_business == 'optics') {
		// On change of validate-payment-note input
		$(document).on('change', 'input.validate-payment-note', function (e) {
			// Check if the payment note exists
			var payment_note = $(this).val();
			var _input = $(this);
			var route = "/pos/validatePaymentNote/" + payment_note;

			$('label#payment-note-error').remove();
			
			$.get(route, function(res) {
				if (res.flag) {
					$('input#flag-payment-note').val(1);

					error = '<label id="payment-note-error" class="error">' + LANG.payment_note_alredy_exists + '</label>';
					$(error).insertAfter(_input.parent('div'));

				} else {
					$('input#flag-payment-note').val(0);
				}
			});
		});
	}

	/**
	 * --------------------------------------------------------------------------------
	 * CUSTOMER MODAL
	 * --------------------------------------------------------------------------------
	 */

	// On show of customer_modal modal
	$('div.customer_modal').on('shown.bs.modal', function (e) {	
		var customer_modal = $(this);

		$(this).find('.select2').select2();

		// On click of btn-add-customer button
		$("#btn-add-customer").on("click", function () {
			$("#btn-add-customer").prop('disabled', true);
			$("#btn-close-modal-add-customer").prop('disabled', true);
			
			var data = $("#form-add-customer").serialize();
			route = "/customers";
			token = $("#token").val();
			
			$.ajax({
				url: route,
				headers: { 'X-CSRF-TOKEN': token },
				type: 'POST',
				dataType: 'json',
				data: data,
				success: function (result) {
					if (result.success == true) {
						$("#btn-add-customer").prop('disabled', false);
						$("#btn-close-modal-add-customer").prop('disabled', false);
						$('div.customer_modal').modal('hide');

						$('select#customer_id').empty()
							.append(new Option(result.customer_name, result.customer_id, true, true));
						
						$('input#allowed_credit').val(result.allowed_credit);
						$('input#is_withholding_agent').val(result.is_withholding_agent);

						if (result.is_default) {
							$("div#customer_name_div").show();
							$("input#customer_name").val(result.customer_name);
						} else {
							$("div#customer_name_div").hide();
							$("input#customer_name").val("");
						}

						Swal.fire({
							title: result.msg,
							icon: "success",
							timer: 3000,
							showConfirmButton: false,
						});

					} else {
						$("#btn-add-customer").prop('disabled', false);
						$("#btn-close-modal-add-customer").prop('disabled', false);
						
						Swal.fire({
							title: result.msg,
							icon: "error",
						});
					}
				},
				error: function (msj) {
					$("#btn-add-customer").prop('disabled', false);
					$("#btn-close-modal-add-customer").prop('disabled', false);
					
					var errormessages = "";
					
					$.each(msj.responseJSON.errors, function (i, field) {
						errormessages+="<li>" + field + "</li>";
					});

					Swal.fire({
						title: LANG.error,
						icon: "error",
						html: "<ul>" + errormessages + "</ul>",
					});
				}
			});
		});

		let dui = $("#dni");
		$(dui).mask("99999999-9");

		let nit = $("#tax_number");
		$(nit).mask('9999-999999-999-9');

		if (!($("#is_taxpayer").is(":checked"))) {
			$('#reg_number').prop('required', false);
			$("#dni").prop('required', true);
		}

		// On change of dni input
		$(document).on('change', '#dni', function (e) {
			let valor = $(this).val();
			let route = '/customers/verified_document/' + 'dni' + '/' + valor;

			$.get(route, function (data, status) {
				if (data.success == true) {
					$("#btn-add-customer").prop('disabled', true);

					Swal.fire({
						title: data.msg,
						icon: "error",
						timer: 3000,
						showConfirmButton: true,
					});

				} else {
					$("#btn-add-customer").prop('disabled', false);
				}
			});
		});

		// On change of reg_number input
		$(document).on('change','#reg_number', function () {
			let valor = $(this).val();
			let route = '/customers/verified_document/' + 'reg_number' + '/' + valor;

			$.get(route, function (data, status) {
				if (data.success == true) {
					$("#btn-add-customer").prop('disabled', true);
					
					Swal.fire({
						title: data.msg,
						icon: "error",
						timer: 3000,
						showConfirmButton: true,
					});

				} else {
					$("#btn-add-customer").prop('disabled', false);
				}
			});
		});

		// On change of business_type_id select
		$(document).on('change','select#business_type_id', function () {
			let valor = $(this).val();
			
			if (valor == 4) {
				$('#taxesP').show();
			}
			
			if (valor != 4) {
				$('#taxesP').hide();
			}
		});

		// On click of is_taxpayer input
		$(document).on('click', '#is_taxpayer', function () {
			if ($("#is_taxpayer").is(":checked")) {
				$('#div_taxpayer').show();
				$("#reg_number").val('');
				$("#tax_number").val('');
				$("#business_line").val('');
				$('#msgR').text("");
				$("#dni").prop('required', false);
				$("#btn-add-customer").prop('disabled', false);
				$('#reg_number').prop('required', true);
				setTimeout(function () {
					$('#reg_number').trigger('click');
				},
				800);

			} else {
				$('#div_taxpayer').hide();
				$("#btn-add-customer").prop('disabled', false);
				$("#reg_number").val('');
				$("#tax_number").val('');
				$("#business_line").val('');
				$('#msg').text("");
				$("#dni").prop('required', true);
			}
		});

		/**
		 * get list of countries.
		 * 
		 * @param  int  id
		 * @return void
		 */
		function getStatesByCountry(id) {
			$("#state_id").empty();

			var route = "/states/getStatesByCountry/" + id;

			$.get(route, function (res) {
				$("#state_id").append('<option value="0" disabled selected>' + LANG.select_please + '</option>');

				$(res).each(function (key,value) {
					$("#state_id").append('<option value="' + value.id + '">' + value.name + '</option>');
				});
			});
		}

		/**
		 * get list of cities by state.
		 * 
		 * @param  int  id
		 * @return void
		 */
		function getCitiesByState(id) {
			$("#city_id").empty();

			var route = "/cities/getCitiesByState/" + id;

			$.get(route, function (res) {
				$("#city_id").append('<option value="0" disabled selected>' + LANG.select_please + '</option>');

				$(res).each(function (key,value) {
					$("#city_id").append('<option value="' + value.id + '">' + value.name + '</option>');
				});
			});
		}

		// On change of country_id select
		$(document).on('change', '#country_id', function (e) {
			id = $("#country_id").val();

			if (id) {
				getStatesByCountry(id);

			} else {
				$("#state_id").empty();
				$("#state_id").append('<option value="0" disabled selected>' + LANG.select_please + '</option>');
	
				$("#city_id").empty();
				$("#city_id").append('<option value="0" disabled selected>' + LANG.select_please + '</option>');
			}
		});

		// On change of state_id select
		$(document).on('change', '#state_id', function (e) {
			id = $("#state_id").val();

			if (id) {
				getCitiesByState(id);

			} else {
				$("#city_id").empty();
				$("#city_id").append('<option value="0" disabled selected>' + LANG.select_please + '</option>');
			}
		});

		// On hide of modal
		$('.modal').on("hidden.bs.modal", function (e) {
			// Allow modal over other
			if ($('.modal:visible').length) {
				$('body').addClass('modal-open');
			}
		});

		// On click of add_reference button
		$(document).on('click','#add_reference', function(){
			var newtr1 = newtr1 + '<tr><input name="contactid[]" type="hidden" value="0">';
			newtr1 = newtr1 + '<td><input  class="form-control" name="contactname[]"  value="" required /></td>';
			newtr1 = newtr1 + '<td><input  class="form-control input_number" name="contactphone[]"  value="" required /></td>';  
			newtr1 = newtr1 + '<td><input  class="form-control input_number" name="contactlandline[]"  value="" required /></td>';
			newtr1 = newtr1 + '<td><input type="email" class="form-control" name="contactemail[]"  value="" required /></td>';
			newtr1 = newtr1 + '<td><input  class="form-control" name="contactcargo[]"  value="" required /></td>';
			newtr1 = newtr1 + '<td><button type="button" class="btn btn-danger btn-xs remove-item"><i class="fa fa-times"></i></button></td></tr>';

			$('#referencesItems').append(newtr1); // Agrego el contacto al tbody de la tabla con el id=ProSelected

			$('#dele').addClass("show");

			// On click of remove-item button
			$('.remove-item').on("click",function (e) {
				Swal.fire({
					title: LANG.sure,
					text: LANG.delete_content,
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: LANG.accept,
					cancelButtonText: LANG.cancel
				}).then((willDelete) => {
					if (willDelete.isConfirmed) {
						$(this).parent('td').parent('tr').slideDown(300, function() {
							$(this).remove(); // En acción elimino el contacto de la tabla
						});
					}
				});
			});
		});

		// On click of allowed_credit checkbox
		$(document).on('click', "#allowed_credit", function () {
			if ($(this).is(":checked")) {
				$('#div_credit').show();
				$("#opening_balance").val('');
				$("#credit_limit").val('');
				$("#payment_terms_id").val('').trigger('change');
				setTimeout(function() {
					$('#opening_balance').trigger('click');
				},
				800);

			} else {
				$('#div_credit').hide();
				$("#opening_balance").val('');
				$("#credit_limit").val('');
				$("#payment_terms_id").val('').trigger('change');
			}
		});

		// On click of btn-collapse-ci button
		$('#btn-collapse-ci').click(function () {
			if ($("#commercial-information-fields-box").hasClass("in")) {
				$("#create-icon-collapsed").removeClass("fa fa-minus");
				$("#create-icon-collapsed").addClass("fa fa-plus");

			} else {
				$("#create-icon-collapsed").removeClass("fa fa-plus");
				$("#create-icon-collapsed").addClass("fa fa-minus");
			}
		});

		// On click of btn-collapse-fi button
		$('#btn-collapse-fi').click(function () {
			if ($("#fiscal-information-fields-box").hasClass("in")) {
				$("#create-icon-collapsed-fi").removeClass("fa fa-minus");
				$("#create-icon-collapsed-fi").addClass("fa fa-plus");

			} else {
				$("#create-icon-collapsed-fi").removeClass("fa fa-plus");
				$("#create-icon-collapsed-fi").addClass("fa fa-minus");
			}
		});

		// On click of btn-collapse-gi button
		$('#btn-collapse-gi').click(function () {
			if ($("#general-information-fields-box").hasClass("in")) {
				$("#create-icon-collapsed-gi").removeClass("fa fa-minus");
				$("#create-icon-collapsed-gi").addClass("fa fa-plus");

			} else {
				$("#create-icon-collapsed-gi").removeClass("fa fa-plus");
				$("#create-icon-collapsed-gi").addClass("fa fa-minus");
			}
		});

		// Hidden input that contain the main_account code
		let main_account = $("#main_account").val();
		main_account = main_account ? main_account : null;

		/** Select accounting account to customer */
		$("select.select_account").select2({
			dropdownParent: customer_modal,
			ajax: {
				type: "post",
				url: "/catalogue/get_accounts_for_select2",
				dataType: "json",
				data: function (params) {
					return {
						q: params.term,
						main_account: main_account
					};
				},
				processResults: function (data) {
					return {
						results: data
					};
				}
			},
			minimumInputLength: 1,
			escapeMarkup: function (markup) {
				return markup;
			}
		});

		if (app_business == 'optics') {
			customer_modal.find("#is_patient").on('change', function(){
				if ($(this).is(":checked")) {
					customer_modal.find('#patient_form').show();
					customer_modal.find("#age").val('');
					customer_modal.find("#sex").val('').change();
					customer_modal.find("#chkhas_glasses").prop('checked', false);
					customer_modal.find("#glasses_graduation").val('');
					customer_modal.find("#graduation_box").hide();
					customer_modal.find("#location_id").val('').change();
					customer_modal.find("#txt-notes").val('');
					customer_modal.find("#employee_code").val('');
					customer_modal.find("#employee_name").val('');
					customer_modal.find("#glasses_graduation").prop('required', false);
					customer_modal.find("#code").prop('required', true);
					customer_modal.find("#age").prop('required', true);
					customer_modal.find("#sex").prop('required', true);
					customer_modal.find("#location_id").prop('required', true);
					setTimeout(function()
					{
						customer_modal.find('#age').focus();
					},
					800);
				} else {
					customer_modal.find('#patient_form').hide();
					customer_modal.find("#age").val('');
					customer_modal.find("#sex").val('').change();
					customer_modal.find("#chkhas_glasses").prop('checked', false);
					customer_modal.find("#glasses_graduation").val('');
					customer_modal.find("#graduation_box").hide();
					customer_modal.find("#location_id").val('').change();
					customer_modal.find("#txt-notes").val('');
					customer_modal.find("#employee_code").val('');
					customer_modal.find("#employee_name").val('');
					customer_modal.find("#glasses_graduation").prop('required', false);
					customer_modal.find("#code").prop('required', false);
					customer_modal.find("#age").prop('required', false);
					customer_modal.find("#sex").prop('required', false);
					customer_modal.find("#location_id").prop('required', false);
				}
			});
		}
	});

	// On hide of customer_modal modal
	$('.customer_modal').on('hidden.bs.modal', function (e) {
		$(this).empty();
	});

	/**
	 * --------------------------------------------------------------------------------
	 * MODALS
	 * --------------------------------------------------------------------------------
	 */

	// Modal for request DUIs for FCF or CCF equal to or greater than max operation
	$('#document_validation_modal').on('shown.bs.modal', function () {
		$(document).on('click', '#check_foreign', function () {
			check_foreign_customer()
			enable_button_accept();
		});

		$(document).on('change', '#delivered_by, #delivered_by_dui, #delivered_by_passport, #received_by, #received_by_dui', function () {
			enable_button_accept();
		});

		$(document).on('click', '#btn-accept', function () {
			$('#document_validation_modal').modal('hide');

			let pay_method = $('#selected_payment_method').val();

			if (pay_method == 'card') {
				$('div#card_details_modal').modal('show');

			} else if (pay_method == 'suspend') {
				$('div#confirmSuspendModal').modal('show');

			} else if (pay_method == 'credit') {
				if ($("input#allowed_credit").val() == "1") {
					$("input#is_credit").val("1");

					$.ajax({
						method: 'get',
						url: '/payment-terms/get-payment-terms',
						dataType: 'json',
						success: function(terminos){
							Swal.fire({
								title: LANG.payment_terms,
								input: 'select',
								inputOptions: {
									terminos
								},
								inputPlaceholder: LANG.select_payment_term,
								showCancelButton: true,
								inputValidator: (payment_term) => {
									if (payment_term) {
										$("input#pay_term_number").val(payment_term);
  
										pos_form_obj.submit();
									} else {
										Swal.fire(
											LANG.notice,
											LANG.payment_term_not_chosen,
											'warning'
										);
									}
								}
							});
						}
					});

				} else {
					swal(LANG.notice, LANG.customer_has_no_credit_allowed, "info");
				}

			} else if (pay_method == 'cash') {
				$("input#is_credit").val("0");
				pos_form_obj.submit();

			} else {
				$('#modal_payment').modal('show');
				$('#modal_payment').css('overflow', 'auto');
			}

			$('#document_validation_modal').clear();

			return false;
		});

		/**
		 * Check if the customer is a foreigner to request correct
		 * identification document.
		 * 
		 * @return void
		 */
		function check_foreign_customer() {
			if ($('#check_foreign').is(':checked')) {
				$('#delivered_by_passport').show();
				$('#delivered_by_dui').val('');
				$('#delivered_by_dui').hide();

			} else {
				$('#delivered_by_dui').show();
				$('#delivered_by_passport').val('');
				$('#delivered_by_passport').hide();
			}
		}

		/**
		 * Check which fields of the document_validation_modal are filled to
		 * enable the accept button.
		 * 
		 * @return void
		 */
		function enable_button_accept() {
			if ($('#check_foreign').is(':checked')) {
				if (
					$('#delivered_by').val() != '' &&
					$('#delivered_by_passport').val() != '' &&
					$('#received_by').val() != '' &&
					$('#received_by_dui').val() != ''
				) {
					$('#btn-accept').prop('disabled', false);

				} else {
					$('#btn-accept').prop('disabled', true);
				}

			} else {
				if (
					$('#delivered_by').val() != '' &&
					$('#delivered_by_dui').val() != '' &&
					$('#received_by').val() != '' &&
					$('#received_by_dui').val() != ''
				) {
					$('#btn-accept').prop('disabled', false);

				} else {
					$('#btn-accept').prop('disabled', true);
				}
			}
		}
	});

	// On show of card_details_modal modal.
	$('div#card_details_modal').on('shown.bs.modal', function (e) {
		$('input#card_number').focus();
	});

	// On show of confirmSuspendModal modal.
	$('div#confirmSuspendModal').on('shown.bs.modal', function (e) {
		$(this).find('textarea').focus();
	});

	// Update discount
	$('button#posEditDiscountModalUpdate').click(function () {
		// Validation
		var max_discount = parseFloat($('#max_sale_discount').val());
		var amount = parseFloat($('#discount_amount_modal').val());
		var discount_type = $('#discount_type_modal').val();
		var total = calculate_total();
		var msg_error = __translate('msg_max_discount_fixed', {'number': max_discount});

		

		if (amount > max_discount) {
			// Update values
			$('#discount_amount_modal').val(0);
			$('input#discount_type').val($('select#discount_type_modal').val());

			__write_number($('input#discount_amount'), __read_number($('input#discount_amount_modal')), false, price_precision);

			pos_total_row();

			Swal.fire({
				title: msg_error,
				icon: "error",
				timer: 2000,
				showConfirmButton: false,
			});

		} else {
			// Close modal
			$('div#posEditDiscountModal').modal('hide');

			// Update values
			$('input#discount_type').val($('select#discount_type_modal').val());

			__write_number($('input#discount_amount'), __read_number($('input#discount_amount_modal')), false, price_precision);

			pos_total_row();
		}
	});

	// Shipping
	$('button#posShippingModalUpdate').click(function () {
		// Close modal
		$('div#posShippingModal').modal('hide');

		// Update shipping details
		$('input#shipping_details').val($('#shipping_details_modal').val());

		// Update shipping charges
		__write_number($('input#shipping_charges'), __read_number($('input#shipping_charges_modal')), false, price_precision);

		// $('input#shipping_charges').val(__read_number($('input#shipping_charges_modal')));

		pos_total_row();
	});

	// On show of posShippingModal modal
	$('#posShippingModal').on('shown.bs.modal', function () {
		$('#posShippingModal').find('#shipping_details_modal').filter(':visible:first').focus().select();
	});

	// On show of row_edit_product_price_model modal
	$(document).on('shown.bs.modal', '.row_edit_product_price_model', function () {
		$('.row_edit_product_price_model').find('input').filter(':visible:first').focus().select();
	});

	// Update order tax
	$('button#posEditOrderTaxModalUpdate').click(function () {
		// Close modal
		$('div#posEditOrderTaxModal').modal('hide');

		var tax_obj = $('select#order_tax_modal');
		var tax_id = tax_obj.val();
		var tax_rate = tax_obj.find(':selected').data('rate');

		$('input#tax_rate_id').val(tax_id);

		__write_number($('input#tax_calculation_amount'), tax_rate, false, price_precision);

		pos_total_row();
	});

	// On change of discount_amount_modal input
	$(document).on('change', 'input#discount_amount_modal', function (e) {
		// Check if discount is negative
		let discount_amount = parseFloat($(this).val());
		if (discount_amount < 0) {
			$(this).val('0.00');
			Swal.fire({
				title: LANG.negative_discount_error,
				icon: "error",
				timer: 2000,
				showConfirmButton: false,
			});
		}
	});

	// On show of row_description_modal modal
	$(document).on('shown.bs.modal', '.row_description_modal', function () {
		$(this).find('textarea').first().focus();
	});

	// On show view_modal modal
	$('div.view_modal').on('show.bs.modal', function () {
		__currency_convert_recursively($(this));
	});

	if (app_business == 'optics') {
		// On show of graduation_cards_modal modal
		$('.graduation_cards_modal').on('shown.bs.modal', function () {
			$(this).find('.select2').select2();
			
			// Validation dnsp and di
			var _dnsp_od = $('input[name="dnsp_od"]');
			var _dnsp_os = $('input[name="dnsp_os"]');
			var _di = $('input[name="di"]');
		
			_dnsp_od.on('change', function(e) {
				if (_dnsp_od.val() != '') {
					_di.prop('readonly', true);
					_dnsp_os.prop('required', true);

				} else {
					if (_dnsp_os.val() == '') {
						_di.prop('readonly', false);
						_dnsp_os.prop('required', false);
					}
				}
			});
		
			_dnsp_os.on('change', function(e) {
				if (_dnsp_os.val() != '') {
					_di.prop('readonly', true);
					_dnsp_od.prop('required', true);

				} else {
					if (_dnsp_od.val() == '') {
						_di.prop('readonly', false);
						_dnsp_od.prop('required', false);
					}
				}
			});
		
			_di.on('change', function(e) {
				if (_di.val() != '') {
					_dnsp_od.prop('readonly', true);
					_dnsp_os.prop('readonly', true);

				} else {
					_dnsp_od.prop('readonly', false);
					_dnsp_os.prop('readonly', false);
				}
			});
		});

		// On show of patient_modal modal
		$('.patient_modal').on('shown.bs.modal', function (e) {
			$(document).on('submit', 'form#patient_add_form', function (e) {
				e.preventDefault();

				$(this).find('button[type="submit"]').prop('disabled', true);

				var data = $(this).serialize();

				$.ajax({
					method: "POST",
					url: $(this).attr("action"),
					dataType: "json",
					data: data,
					success: function(result) {
						if (result.success === true) {
							$(this).find('button[type="submit"]').prop('disabled', false);

							$('div.patient_modal').modal('hide');

							$('select#select_patient_id').empty()
								.append(new Option(result.full_name, result.pat_id, true, true));
							
							Swal.fire({
								title: result.msg,
								icon: "success",
								timer: 2000,
								showConfirmButton: false,
							});

						} else {
							$(this).find('button[type="submit"]').prop('disabled', false);

							Swal.fire({
								title: result.msg,
								icon: "error",
								timer: 2000,
								showConfirmButton: false,
							});
						}
					}
				});

				$(this).clear();

				return false;
			});

			// On change of name input
			$(document).on('change', 'form#patient_add_form input#full_name', function () {
				$.ajax({
					method: 'post',
					url: '/pos/check-customer-patient-name',
					dataType: 'json',
					data: {
						is_customer: 0,
						is_patient: 1,
						term: $(this).val()
					},
					success: function (result) {
						if (result.success === 0) {
							Swal.fire({
								title: LANG.warning,
								text: result.msg,
								icon: 'warning',
							});
						}
					}
				});
			});

			/**
			 * Show or hide graduation section.
			 * 
			 * @return void
			 */
			function showgraduationbox() {
				if ($("#chkhas_glasses").is(":checked")) {
					$("#graduation_box").show();
					$("#glasses_graduation").show();
					$("#glasses_graduation").val('');
					$("#glasses_graduation").prop('required', true);

				} else {
					$("#glasses_graduation").val('');
					$("#glasses_graduation").prop('required', false);
					$("#glasses_graduation").hide();
					$("#graduation_box").hide();
				}
			}

			/**
			 * Search employee by code.
			 * 
			 * @return void
			 */
			function SearchEmployee() {
				var code = $("#employee_code").val();

				if (code == '') {
					$('#employee_name').val('');

				} else {
					var route = "/patients/getEmployeeByCode/" + code;

					$.get(route, function(res) {
						if (res.success) {
							if (res.emp) {
								$('#employee_name').val(res.msg);

							} else {
								$('#employee_name').val('');
								$('#employee_code').val('');

								Swal.fire({
									title: res.msg,
									icon: "error",
									timer: 2000,
									showConfirmButton: false,
								});
							}

						} else {
							$('#employee_name').val('');
							$('#employee_code').val('');
							
							Swal.fire({
								title: ""+res.msg+"",
								icon: "error",
								timer: 2000,
								showConfirmButton: false,
							});
						}
					});
				}
			}
		});

		/** Hidden patient modal */
		$('.patient_modal').on('hidden.bs.modal', function (e) {
			$(this).empty();
		});
	}

	/**
	 * --------------------------------------------------------------------------------
	 * CASHIER CLOSURE
	 * --------------------------------------------------------------------------------
	 */

	// On show of close_register_modal div
	$('div.close_register_modal').on('show.bs.modal', function () {
		// Datetime picker for cash register close day
		$('input#close_day').datetimepicker({
			format: moment_date_format,
			ignoreReadonly: true
		})
	});

	/** Cash register script */
	$(document).on("click", "a#add_cash_detail", function (e) {
		e.preventDefault();

		$("div.cash_detail").modal("show");
		
		/*$.ajax({
			type: "GET",
			url: $(this).attr("href"),
			dataType: "html",
			success: function(data){
				$("div.cash_detail").html(data).modal("show");
			}
		});*/
	});

	/** Close cash detail modal */
	$(document).on("click", "button#close_cash_detail_modal", function () {
		$("div.cash_detail").modal("hide");
	});

	// On click of close_register button
	$(document).on("click", "button#close_register", function (e) {
		e.preventDefault();

		var cashier_id = $("input#cashier_id").val();

		var location_id = $("input#location_id").val();
		if (app_business == 'optics') {
			var close_date = $("input#transaction_date").val();

			if (cashier_id && location_id) {
				$.ajax({
					type: "GET",
					url: "/cash-register/close-register",
					dataType: "html",
					data: {
						close_date: close_date,
						cashier_id: cashier_id,
						location_id: location_id
					},
					success: function(data){
						$("div.close_register_modal").html(data).modal("show");
					}
				});
				
			} else {
				toastr.warning(LANG.must_select_cash_register);
			}

		} else {
			if (cashier_id > 0 && location_id > 0) {
				$.ajax({
					type: "GET",
					url: "/cashier-closure/get-cashier-closure",
					dataType: "html",
					data: { cashier_id: cashier_id },
					success: function (data) {
						$("div.close_register_modal").html(data).modal("show");
					}
				});
	
			} else {
				toastr.warning(LANG.must_select_cash_register);
			}
		}
	});

	if (app_business == 'optics') {
		// On show of close_register_modal modal
		$('.close_register_modal').on('shown.bs.modal', function (e) {
			$('#btn-cash-detail').on('click', function() {
				$('.cash_detail').modal('show');
			});

			$(".close_register_modal").modal({ backdrop: 'static' });
		});

		// On show of register_details_modal modal or close_register_modal modal
		$('.register_details_modal, .close_register_modal').on('shown.bs.modal', function () {
			__currency_convert_recursively($(this));
		});

	} else {
		/** Opening Cash register */
		let cashier_closure_id = $("input#cashier_closure_receipt").val();
		
		if (cashier_closure_id) {
			var tab = window.open('/cashier-closure/get-opening-cash-register/' + cashier_closure_id, '_blank');
	
			if (tab) { tab.focus(); }
			
			/** Reload */
			window.open('/pos/create', '_self');
		}
	
		/** Calculate differences on cashier closure */
		$(document).on("change", "form#cashier_closure_form input.closure_input", function () {
			var form = $("form#cashier_closure_form");
			var physical_amount = form.find("input#total_physical_amount");
			var system_amount = __read_number(form.find("input#total_system_amount"));
			var differences = form.find("input#differences");
			var cash_amount = __read_number(form.find("input#total_cash_amount"));
			var card_amount = __read_number(form.find("input#total_card_amount"));
			var check_amount = __read_number(form.find("input#total_check_amount"));
			var bank_transfer_amount = __read_number(form.find("input#total_bank_transfer_amount"));
			var credit_amount = __read_number(form.find("input#total_credit_amount"));
			var return_amount = __read_number(form.find("input#total_return_amount"));
			var cash_amount = __read_number(form.find("input#total_cash_amount"));
	
			var total = (cash_amount + card_amount + check_amount + bank_transfer_amount + credit_amount) - return_amount;
	
			__write_number(differences, (system_amount - total), false, price_precision);
			__write_number(physical_amount, total, false, price_precision)
		});
	
		// On click of cashier_closure_form submit button
		$(document).on("click", "form#cashier_closure_form button[type='submit']", function (e) {
			e.preventDefault();
			let btn = $(this);
			btn.attr('disabled', true);
	
			var form = $("form#cashier_closure_form");
			var differences = __read_number($(this).closest('form').find("input#differences"));
	
			if (differences > 0 || differences < 0) {
				swal({
					title: LANG.sure,
					text: LANG.cashier_closure_will_send_with_differences,
					icon: "warning",
					buttons: true,
					dangerMode: true,
				}).then((isConfirmed) => {
					if (isConfirmed) {
						form.submit();
					}
				});
	
			} else {
				form.submit();
			}
	
			/** Show daily z cut, but first wait 1 seg. */
			var location_id = $("input#location_id").val();
			var cashier_id = $("input#cashier_id").val();
	
			setTimeout(function () {
				var tab = window.open('/cashier-closure/get-daily-z-cut-report/' + location_id + '/' + cashier_id, '__blank');
	
				if (tab) {
					tab.focus();
				}
			}, 1000);
			
			/** enable btn after 5 seg */
			setTimeout(() => {
				btn.removeAttr('disabled');
			}, 5000);
		});
	}

	/**
	 * --------------------------------------------------------------------------------
	 * RIGHT DIV
	 * --------------------------------------------------------------------------------
	 */

	// Displays list of recent transactions
	get_recent_transactions('final', $('div#tab_final'));
	get_recent_transactions('quotation', $('div#tab_quotation'));
	get_recent_transactions('draft', $('div#tab_draft'));

	// Show product list
	get_product_suggestion_list(
		$("select#product_category").val(),
		$('select#product_brand').val(),
		$('input#location_id').val(),
		null
	);

	// On change of product_category select or product_brand select
	$("select#product_category, select#product_brand").on("change", function (e) {
		$('input#suggestion_page').val(1);

		var location_id = $('input#location_id').val();

		if (location_id != '' || location_id != undefined) {
			get_product_suggestion_list(
				$("select#product_category").val(),
				$('select#product_brand').val(),
				$('input#location_id').val(),
				null
			);
		}
	});

	// On click of product_box div
	$(document).on('click', 'div.product_box', function () {
		// Check if location is not set then show error message
		if ($('input#location_id').val() == '') {
			toastr.warning(LANG.select_location);

		} else {
			pos_product_row($(this).data('variation_id'));
		}
	});
});

// On click of label
$('body').on('click', 'label', function (e) {
    var field_id = $(this).attr('for');

    if (field_id) {
        if ($("#" + field_id).hasClass('select2')) {
            $("#" + field_id).select2("open");
            return false;
        }
    }
});

// On focus of select
$('body').on('focus', 'select', function (e) {
    var field_id = $(this).attr('id');

    if (field_id) {
        if ($("#" + field_id).hasClass('select2')) {
            $("#" + field_id).select2("open");
            return false;
        }
    }
});

// On change of customer_id select
$('select#customer_id').on('change', function () {
	let document_name = $('select[name="documents"] option:selected').text();
	
	if ($.trim(document_name) === "CCF") {
		verifiedIfExistsTaxNumber();
	}
});

// Update quantity if line subtotal changes
$('table#pos_table tbody').on('change', 'input.pos_line_total', function () {
	var subtotal = __read_number($(this));
	var tr = $(this).parents('tr');
	var quantity_element = tr.find('input.pos_quantity');
	var unit_price_inc_tax = __read_number(tr.find('input.pos_unit_price_inc_tax'));
	var quantity = subtotal / unit_price_inc_tax;

	__write_number(quantity_element, quantity);

	if (sell_form_validator) {
		sell_form_validator.element(quantity_element);
	}

	if (pos_form_validator) {
		pos_form_validator.element(quantity_element);
	}

	tr.find('span.pos_line_total_text').text(__currency_trans_from_en(subtotal, true, false, decimals_in_sales));
		
	pos_total_row();
});

// On scroll of product_list_body div
$('div#product_list_body').on('scroll', function () {
    if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
        var page = parseInt($('#suggestion_page').val());
        page += 1;
        $('#suggestion_page').val(page);
        var location_id = $('input#location_id').val();
		var category_id = $("select#product_category").val();
		var brand_id = $("select#product_brand").val();

		get_product_suggestion_list(category_id, brand_id, location_id);
    }
});

if (app_business == 'optics') {
	$(document).on('click', '#btn-commission-agent', function() {
		search_commission_agent(
			$('#input-commission-agent').val(),
			$('#input-commission-agent'),
			$('#txt-commission-agent'),
			$('#commission_agent'),
			'user'
		);
	});
}

/**
 * Verify if the customer has NIT and registration number if the document is a
 * tax credit.
 * 
 * @return void
 */
function verifiedIfExistsTaxNumber() {
	let customer = $('select#customer_id').val();
    let route = '/customer/verified_tax_number_sell_pos';
	let document_name = $('select[name="documents"] option:selected').text();

	if ($.trim(document_name) === "CCF") {
		if (customer != "") {
			$.ajax({
				method: "get",
				url: route,
				data: { 'customer_id': customer },
				dataType: "json",
				success: function(result) {
					if (result.success == false) {
						// Swal.fire({ title: result.msg, icon: "error", timer: 6000});
						
						swal(
							LANG.notice,
							LANG.customer_has_no_nit_nrc,
							"error"
						);

						$('input#document_validate').val(0);

					} else {
						$('input#document_validate').val(1);
					}
				}
			});
		}
	}
}

/** 
 * Set warehouses & cashiers.
 * 
 * @return void
 */
function set_warehouse() {
	var warehouse_id = $('select#select_warehouse_id :selected').val();
	var cashier_id = $('select#select_cashier_id :selected').val();

	if ($('#edit_pos_sell_form').length > 0) {
		warehouse_id = $('input#warehouse_id').val();
		cashier_id = $('input#cashier_id').val();
	}

	if (warehouse_id && cashier_id) {
		$('input#search_product').prop( "disabled", false).focus();
		$('select#price_group').prop('disabled', false);
		$('select#orders').prop('disabled', false);
		$('input#warehouse_id').val(warehouse_id);
		$('input#cashier_id').val(cashier_id);

	} else {
		$('input#search_product').prop( "disabled", true);
		$('select#price_group').prop('disabled', true);
		$('select#orders').prop('disabled', true);
		$('input#warehouse_id').val(null);

		if (cashier_id) {
			$('input#cashier_id').val(cashier_id);
		} else{
			$('input#cashier_id').val(null);
		}
	}
}

/**
 * Get product rows for orders only.
 * 
 * @param  int  $quote_id
 * @param  int  $variation_id
 * @param  int  $location_id
 * @param  int  $warehouse_id
 * @return void
 */
function add_product_row_from_order(quote_id, variation_id, location_id, warehouse_id){
	var row_count = $('input#product_row_count').val();

	$.ajax({
		method: "GET",
		url: '/orders/get_product_row/' + quote_id + '/' + variation_id + '/' + location_id + '/' + row_count,
		data: { warehouse_id: warehouse_id, check_qty_available: 1 },
		dataType: 'html',
		success: function(data){
			$('table#pos_table tbody').append(data).find('input.pos_quantity');
			
			var this_row = $('table#pos_table tbody').find("tr").last();
			
			pos_each_row(this_row);
			pos_total_row();
		}
	});

	$('input#product_row_count').val(parseInt(row_count) + 1); // Increment row count
}

/**
 * Get product suggestion list.
 * 
 * @param  int  category_id 
 * @param  int  brand_id 
 * @param  int  location_id 
 * @param  string  url 
 * @return void
 */
function get_product_suggestion_list(category_id, brand_id, location_id, url = null) {
	if (url == null) {
		url = "/sells/pos/get-product-suggestion";
	}

	$('#suggestion_page_loader').fadeIn(700);

	var page = $('input#suggestion_page').val();

	if (page == 1 ) {
		$('div#product_list_body').html('');
	}
	
	if ($('div#product_list_body').find('input#no_products_found').length > 0) {
		$('#suggestion_page_loader').fadeOut(700);
		return false;
	}

	$.ajax({
		method: "GET",
		url: url,
		data: {
			category_id: category_id,
			brand_id: brand_id, 
			location_id: location_id,
			page: page
		},
		dataType: "html",
		success: function (result) {
			$('div#product_list_body').append(result);
			$('#suggestion_page_loader').fadeOut(700);
		}
	});
}

/**
 * Get recent transactions.
 * 
 * @param  string  status
 * @param  HTMLElement  element_obj
 * @return void
 */
function get_recent_transactions(status, element_obj) {
	$.ajax({
		method: "GET",
		url: "/sells/pos/get-recent-transactions",
		data: {status: status},
		dataType: "html",
		success: function (result) {
			element_obj.html(result);
			__currency_convert_recursively(element_obj);
		}
	});
}

/**
 * Get product row.
 * 
 * @param  int  variation_id
 * @return void
 */
function pos_product_row(variation_id) {
	// Get item addition method
    var item_addtn_method = 0; 
    var add_via_ajax = true;
    
    if ($('#item_addition_method').length) {
        item_addtn_method = $('#item_addition_method').val();
    }

    if (item_addtn_method == 0) {
    	add_via_ajax = true;

    } else {
    	var is_added = false;

    	// Search for variation id in each row of pos table
        $('#pos_table tbody').find('tr').each(function() {
            var row_v_id = $(this).find('.row_variation_id').val();
            var enable_sr_no = $(this).find('.enable_sr_no').val();
            var modifiers_exist = false;

            if ($(this).find('input.modifiers_exist').length > 0) {
            	modifiers_exist = true;
            }

            if (row_v_id == variation_id && enable_sr_no !== '1' && !modifiers_exist && !is_added) {
            	add_via_ajax = false;
            	is_added = true;

            	// Increment product quantity
                qty_element = $(this).find('.pos_quantity');
                var qty = __read_number(qty_element);
                __write_number(qty_element, qty + 1);
                qty_element.change();

                round_row_to_iraqi_dinnar($(this));

                $('input#search_product').focus().select();
            }
        });
    }

	if (add_via_ajax) {
		var product_row = $('input#product_row_count').val();
		var location_id = $('input#location_id').val();
		var customer_id = $('select#customer_id').val();
		var warehouse_id = $('input#warehouse_id').val();
		var reservation_id = $('#reservation_id').val();
		var is_direct_sell = false;

		if ($('input[name="is_direct_sale"]').length > 0 && $('input[name="is_direct_sale"]').val() == 1) {
			is_direct_sell = true;
		}

		var price_group = '';

		if ($('#price_group').length > 0) {
			price_group = $('#price_group').val();
		}

		$.ajax({
			method: "GET",
			url: "/sells/pos/get_product_row/" + variation_id + '/' + location_id,
			async: false,
			data: {
				product_row: product_row, 
				customer_id: customer_id,
				warehouse_id: warehouse_id, 
				is_direct_sell: is_direct_sell,
				price_group: price_group,
				reservation_id: reservation_id
			},
			dataType: "json",
			success: function (result) {
				if (result.success) {
					$('table#pos_table tbody').append(result.html_content).find('input.pos_quantity');
					
					// Increment row count
					$('input#product_row_count').val(parseInt(product_row) + 1);
					
					var this_row = $('table#pos_table tbody').find("tr").last();
					
					pos_each_row(this_row);
					pos_total_row();

					if (result.enable_sr_no == '1') {
						var new_row = $('table#pos_table tbody').find("tr").last();
						new_row.find('.add-pos-row-description').trigger('click');
					}

					round_row_to_iraqi_dinnar(this_row);
					__currency_convert_recursively(this_row)

					$('input#search_product').focus().select();

					// Used in restaurant module
					if (result.html_modifier) {
						$('table#pos_table tbody').find("tr").last().find("td:first").append(result.html_modifier);
					}

				} else {
					swal(result.msg).then((value) => {
  						$('input#search_product').focus().select();
					});
				}
			}
		});
	}
}

/**
 * Update values for each row.
 * 
 * @param  HTMLElement  row_obj
 * @return void
 */
function pos_each_row(row_obj) {
	var discounted_unit_price = calculate_discounted_unit_price(row_obj);
	var quantity = __read_number(row_obj.find('input.pos_quantity'));
	var doc_tax_inc = $('select#documents :selected').data('tax_inc');
	var doc_tax_exempt = $('select#documents :selected').data('tax_exempt');
	var is_exempt = $('input#is_exempt').val();

	if ($('form#edit_pos_sell_form').length > 0) {
		doc_tax_inc = parseInt($('input#doc_tax_inc').val());
		doc_tax_exempt = parseInt($('input#doc_tax_exempt').val());
	}

	var u_price_exc_tax = __read_number(row_obj.find('input#u_price_exc_tax'));
	var u_price_inc_tax = __read_number(row_obj.find('input#u_price_inc_tax'));

	var unit_price_exc_tax = quantity * discounted_unit_price.unit_exc_price;
	var unit_price_inc_tax = quantity * discounted_unit_price.unit_inc_price;
	var pos_line_total = doc_tax_inc > 0 || (doc_tax_exempt > 0 || is_exempt > 0) ? unit_price_exc_tax : unit_price_inc_tax;
	var pos_unit_price = doc_tax_inc > 0 || (doc_tax_exempt > 0 || is_exempt > 0) ? u_price_exc_tax : u_price_inc_tax;

	__write_number(row_obj.find('input#unit_price_inc_tax'), unit_price_inc_tax, false, price_precision);
	__write_number(row_obj.find('input#unit_price_exc_tax'), unit_price_exc_tax, false, price_precision);
	__write_number(row_obj.find('input.pos_unit_price_inc_tax'), pos_unit_price, false, price_precision);

	row_obj.find('span.pos_line_total_text').text(__currency_trans_from_en(pos_line_total, true, false, decimals_in_sales));
}

function calculate_total() {
	var total_quantity = 0;
	var price_total_inc_tax = 0;
	var price_total_exc_tax = 0;
	var tax_percent = 0;
	var doc_tax_inc = $('select#documents :selected').data('tax_inc');
	var doc_tax_exempt = $('select#documents :selected').data('tax_exempt');
	var is_exempt = $('input#is_exempt').val();

	if ($('form#edit_pos_sell_form').length > 0) {
		doc_tax_inc = parseInt($('input#doc_tax_inc').val());
		doc_tax_exempt = parseInt($('input#doc_tax_exempt').val());
	}

	$('table#pos_table tbody tr').each(function( ) {
		total_quantity = total_quantity + __read_number($(this).find('input.pos_quantity'));
		price_total_exc_tax = price_total_exc_tax + __read_number($(this).find('input#unit_price_exc_tax'));
		price_total_inc_tax = price_total_inc_tax + __read_number($(this).find('input#unit_price_inc_tax'));
		tax_percent = __read_number($(this).find('input#tax_percent'));
	});

	// Go through the modifier prices.
	$('input.modifiers_price').each(function () {
		price_total_inc_tax = price_total_inc_tax + __read_number($(this));
	});

	// Updating shipping charges
	$('span#shipping_charges_amount').text(__currency_trans_from_en(__read_number($('input#shipping_charges_modal')), false, false, footer_precision));
	
	/** Sub total */
	$('span.total_quantity').each( function(){
		$(this).html(__number_f(total_quantity));
	});

	/** Calculate tax percent */
	var taxes = (doc_tax_exempt > 0 || is_exempt > 0) ? 0 : tax_percent;

	/** Subtotal */
	var subtotal = doc_tax_inc > 0 || (doc_tax_exempt > 0 || is_exempt > 0) ? price_total_exc_tax : price_total_inc_tax;
	__write_number($('input#subtotal'), subtotal, false, price_precision);
	$('span.price_total').html(__currency_trans_from_en(subtotal, false, false, footer_precision));

	return { subtotal: subtotal, taxes: taxes };
}

/**
 * Calculate row total.
 * 
 * @return void
 */
function pos_total_row() {
	let total = calculate_total();
	
	calculate_billing_details(total['subtotal'], total['taxes']);
}

/**
 * Calculate sale totals.
 * 
 * @param  float  subtotal
 * @param  float  tax_percent
 * @return void
 */
function calculate_billing_details(subtotal, tax_percent) {
	var discount = pos_discount(subtotal);

	var doc_tax_inc = $('select#documents :selected').data('tax_inc');
	var doc_tax_exempt = $('select#documents :selected').data('tax_exempt');
	var is_exempt = $('input#is_exempt').val();

	// Add shipping charges.
	var shipping_charges = __read_number($('input#shipping_charges'));

	if ($('form#edit_pos_sell_form').length > 0) {
		doc_tax_inc = parseInt($('input#doc_tax_inc').val());
		doc_tax_exempt = parseInt($('input#doc_tax_exempt').val());
	}

	/** Tax calculation */
	var tax_amount = doc_tax_inc > 0 ? ((doc_tax_exempt > 0 || is_exempt > 0) ? 0 : (subtotal - discount) * tax_percent) : 0;

	__write_number($('input#tax_calculation_amount'), tax_amount, false, price_precision);
	__write_number($('input#original_tax_amount'), tax_amount, false, price_precision);
	$('span#order_tax').text(__currency_trans_from_en(tax_amount, false, false, footer_precision));

	var total_payable = subtotal - discount + tax_amount + shipping_charges;
	
	/**
	 * Calculate global discount
	 */
	var tax_group_id = $('input#tax_group_id').val()

	if (tax_group_id) {
		var total_before_tax = total_payable / (tax_percent + 1);
		var withheld_amount = calc_contact_tax(total_before_tax);

		if (withheld_amount != 0) { // Retención
			__write_number($('input#withheld'), withheld_amount < 0 ? withheld_amount * -1 : withheld_amount, false, price_precision);
			$('span.withheld').html(__currency_trans_from_en(withheld_amount < 0 ? withheld_amount * -1 : withheld_amount, false, false, footer_precision));
			total_payable += withheld_amount;

		} else {
			__write_number($('input#withheld'), withheld_amount, false, price_precision);
			$('span.withheld').html(__currency_trans_from_en(withheld_amount, false, false, footer_precision));
		}

	} else {
		__write_number($('input#withheld'), 0, false, price_precision);
		$('span.withheld').html(__currency_trans_from_en(0, false, false, footer_precision));
	}

	/** Write total payable */
	__write_number($('input#final_total_input'), total_payable, false, price_precision);

	var curr_exchange_rate = 1;
	
	if ($('#exchange_rate').length > 0 && $('#exchange_rate').val()) {
		curr_exchange_rate = __read_number($('#exchange_rate'));
	}

	var shown_total = total_payable * curr_exchange_rate;
	$('span#total_payable').text(__currency_trans_from_en(shown_total, false, false, footer_precision));
	
	$('span.total_payable_span').text(__currency_trans_from_en(total_payable, true));

	// Check if edit form then don't update price
	if ($('form#edit_pos_sell_form').length == 0) {
		if ($('#flag-first-row').val() == 0) {
			__write_number($('.payment-amount').first(), total_payable);
		}
	}

	calculate_balance_due();
}

/** 
 * Calculate tax for contacts.
 * 
 * @param  float  amount
 * @return float
 */
function calc_contact_tax(amount) {
	var min_amount = __read_number($("input#min_amount"));
	var max_amount = __read_number($("input#max_amount"));
	var tax_percent = (__read_number($('input#tax_group_percent')) / 100);
	var tax_amount = 0;

	/** If has min o max amount */
	if (min_amount || max_amount) {
		/** if has min and max amount */
		if (min_amount && max_amount) {
			if (amount > min_amount && amount <= max_amount) {
				tax_amount = amount * tax_percent;
			}

		/** If has only min amount */
		} else if (min_amount && !max_amount) {
			if (amount > min_amount) {
				tax_amount = amount * tax_percent;
			}

		/** If has only max amount */
		} else if (!min_amount && max_amount) {
			if (amount <= max_amount) {
				tax_amount = amount * tax_percent;
			}
		}

	/** If has none tax */
	} else {
		tax_amount = amount * tax_percent;
	}

	return tax_amount;
}

/**
 * Calculate total discount.
 * 
 * @param  float  total_amount
 * @return float
 */
function pos_discount(total_amount) {
	var calculation_type = $('#discount_type').val();
	var calculation_amount = __read_number($('#discount_amount'));

	var discount = __calculate_amount(calculation_type, calculation_amount, total_amount);
	
	$('span#total_discount').text(__currency_trans_from_en(discount, false, false, footer_precision));

	return discount;
}

/**
 * Calculate order tax.
 * 
 * @param  float  price_total 
 * @param  float  discount 
 * @return float
 */
function pos_order_tax(price_total, discount) {
	var tax_rate_id = $('#tax_rate_id').val();
	var calculation_type = 'percentage';
	var calculation_amount = __read_number($('#tax_calculation_amount'));
	var total_amount = price_total - discount;

	if (tax_rate_id) {
		var order_tax = __calculate_amount(calculation_type, calculation_amount, total_amount);

	} else {
		var order_tax = 0;
	}

	// $('span#order_tax').text(__currency_trans_from_en(order_tax, false, false, footer_precision));

	return order_tax;
}

/**
 * Calculate balance due.
 * 
 * @return void
 */
function calculate_balance_due() {
	// var total_payable = __read_number($('#final_total_input'));
	var total_payable = parseFloat(__read_number($('#final_total_input'))).toFixed(2);
	var total_paying = 0;

	$('#payment_rows_div').find('.payment-amount').each(function () {
		if (parseFloat($(this).val())) {
			total_paying += __read_number($(this));
		}
	});

	total_paying = parseFloat(total_paying).toFixed(2);

	// var bal_due = total_payable - total_paying;
	var bal_due = parseFloat(total_payable - total_paying).toFixed(2);
	var change_return = 0;

	// Change_return
	if (bal_due < 0 || Math.abs(bal_due) < 0.01) {
		__write_number($('input#change_return'), bal_due*-1);
		$('span.change_return_span').text(__currency_trans_from_en(bal_due * -1, true));
		change_return = bal_due*-1;
		bal_due = 0;

	} else {
		__write_number($('input#change_return'), 0);
		$('span.change_return_span').text(__currency_trans_from_en(0, true));
		change_return = 0;
	}

	__write_number($('input#total_paying_input'), total_paying);
	$('span.total_paying').text(__currency_trans_from_en(total_paying, true));

	__write_number($('input#in_balance_due'), bal_due);
	$('span.balance_due').text(__currency_trans_from_en(bal_due, true));

	__highlight(bal_due * -1, $('span.balance_due'));
	__highlight(change_return * -1, $('span.change_return_span'));
}

/**
 * Validate form and show error messages.
 * 
 * @return  bool
 */
function isValidPosForm(){
	flag = true;

	$('span.error').remove();

	if ($('select#customer_id').val() == null) {
		flag = false;
		error = '<span class="error">' + LANG.required + '</span>';
		$(error).insertAfter($('select#customer_id').parent('div'));
	}

	if ($('tr.product_row').length == 0) {
		flag = false;
		error = '<span class="error">' + LANG.no_products + '</span>';
		$(error).insertAfter($('input#search_product').parent('div'));
	}

	return flag;
}

/**
 * Reset form.
 * 
 * @return bool
 */
function reset_pos_form() {
	// If on edit page then redirect to Add POS page
	if ($('form#edit_pos_sell_form').length > 0) {
		setTimeout(function() {
			window.location = '/pos/create/';
		}, 4000);

		return true;
	}
	
	if (pos_form_obj[0]) {
		pos_form_obj[0].reset();
	}

	if (sell_form[0]) {
		sell_form[0].reset();
	}

	set_default_customer();
	set_location();
	set_correlative();

	if ($('select#select_patient_id').length > 0) {
		$('select#select_patient_id').val('').trigger("change");
	}

	if ($('select#reservations').length > 0) {
		$('select#reservations').empty();
	}

	if ($('select#customer_vehicle_id').length > 0) {
		$('select#customer_vehicle_id').empty().append(new Option(LANG.customer_vehicle, 0, true, true));
	}

	let is_default = $('#default_doc').val(); 
	$("select#documents").val(is_default).trigger("change");

	/** Orders reset and show customer name*/
	$('select#orders').empty().append(new Option(LANG.pending_orders, 0, true, true));
	$("div#customer_name_div").show();

	$('tr.product_row').remove();
	$('span.total_quantity, span.price_total, span#total_discount, span#order_tax, span#total_payable, span#shipping_charges_amount').text(0);
	$('span.total_payable_span', 'span.total_paying', 'span.balance_due').text(0);

	$('#modal_payment').find('.remove_payment_row').each(function () {
		$(this).closest('.payment_row').remove();
	});

	$("input#is_credit").val("0");
	
	// Reset discount
	__write_number($('input#discount_amount'), $('input#discount_amount').data('default'), false, price_precision);
	$('input#discount_type').val($('input#discount_type').data('default'));

	// Reset tax rate
	$('input#tax_rate_id').val($('input#tax_rate_id').data('default'));
	__write_number($('input#tax_calculation_amount'), $('input#tax_calculation_amount').data('default'), false, price_precision);

	$('select.payment_types_dropdown').val('cash').trigger('change');
	$('#price_group').trigger('change');

	// Reset shipping
	__write_number($('input#shipping_charges'), $('input#shipping_charges').data('default'));
	$('input#shipping_details').val($('input#shipping_details').data('default'));

	$('#check_foreign').prop('checked', false);

	$('input#quote-tax-detail').val('');
}

/**
 * Reset customer form.
 * 
 * @return void
 */
function set_default_customer() {
	var default_customer_id = $('#default_customer_id').val();
	var default_customer_name = $('#default_customer_name').val();
	var exists  = $('select#customer_id option[value=' + default_customer_id + ']').length;

	if (exists == 0) {
		$("select#customer_id").append($('<option>', { value: default_customer_id, text: default_customer_name }));
	}
	
	$('select#customer_id').val(default_customer_id).trigger("change");

	// Reset customer fields
	$("input#allowed_credit").val($("input#default_allowed_credit").val());
	$("input#is_withholding_agent").val($("input#default_is_withholding_agent").val());
	$("input#order_id").val("");
	$("input#tax_group_id").val($("input#default_tax_group_id").val());
	$("input#tax_group_percent").val($("input#default_tax_percent").val());
	$("input#min_amount").val($("input#default_min_amount").val());
	$("input#max_amount").val($("input#default_max_amount").val());
}

/**
 * Get correlative of the document.
 * 
 * @return void
 */
function set_correlative() {
	var idDoc = $('select#documents').val();
	var idSuc = $('select#select_location_id').val();

	$.ajax({
		method: "POST",
		url: '/pos/getCorrelatives',
		data: { 'location_id': idSuc, 'document_type': idDoc },
		dataType: 'text',
		success: function (result) {
			if (result != null) {
				$('input#correlatives').val(result);
				$('input#correlatives').prop('disabled', false);

			} else {
				$('input#correlatives').val(0);
			}
		}
	});

	if (app_business == 'optics') {
		// Correlative to payment note
		idDoc = $('input#payment_note_id').val();
	
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
	
		show_payment_note();
	}
}

/**
 * Set the location and initialize printer.
 * 
 * @return void
 */
function set_location() {
	if ($('select#select_location_id').length == 1) {
		$('input#location_id').val($('select#select_location_id').val());
		$('input#location_id').data('receipt_printer_type', $('select#select_location_id').find(':selected').data('receipt_printer_type'));
	}
	
	if ($('input#location_id').val()) {
		$('input#search_product').prop( "disabled", false ).focus();
		$('select#price_group').prop('disabled', false);
		$('select#documents').prop('disabled', false);
		$('select#customer_id').prop('disabled', false);
		$('input#customer_name').prop('disabled', false);
		$('select#orders').prop('disabled', false);
		$('button.add_new_customer').prop('disabled', false);
		$('select#reservations').prop('disabled', false);
		// $('input#correlatives').prop('disabled', false);
		// $("select#documents").prop('selectedIndex', 0).trigger("change");

		if (app_business == 'optics') {
			$('select#select_patient_id').prop('disabled', false);
			$('button.add_new_patient').prop('disabled', false);
		}

		// Mostrar Correlativo
		set_correlative();

	} else {
		$('input#search_product').prop("disabled", true);
		$('select#price_group').prop('disabled', true);
		$('select#documents').prop('disabled', true);
		$('select#customer_id').prop('disabled', true);
		$('input#customer_name').prop('disabled', true);
		$('select#orders').prop('disabled', true);
		$('button.add_new_customer').prop('disabled', true);
		// $('input#correlatives').prop('disabled', true).val("");
		$("select#documents").prop('selectedIndex', 0).trigger("change");
		$('select#reservations').prop('disabled', true);

		if (app_business == 'optics') {
			$('select#select_patient_id').prop('disabled', true);
			$('button.add_new_patient').prop('disabled', true);
		}
		
		// Mostrar Correlativo
		// set_correlative();
	}

	initialize_printer();
	set_warehouse();
}

/**
 * Initialize printer.
 * 
 * @return void
 */
function initialize_printer() {
	if ($('input#location_id').data('receipt_printer_type') == 'printer') {
		initializeSocket();
	}
}

/**
 * Round unit price.
 * 
 * @param  HTMLElement  row
 * @return void
 */
function round_row_to_iraqi_dinnar(row) {
	if (iraqi_selling_price_adjustment) {
		var element = row.find('input.pos_unit_price_inc_tax');
		var unit_price = round_to_iraqi_dinnar(__read_number(element));
		__write_number(element, unit_price, false, price_precision);
		element.change();
	}
}

/**
 * Print receipt.
 * 
 * @param  string  receipt
 * @return void
 */
function pos_print(receipt) {
	// If printer type then connect with websocket
	if (receipt.print_type == 'printer') {
		var content = receipt;
		content.type = 'print-receipt';

		// Check if ready or not, then print.
		if (socket.readyState != 1) {
			initializeSocket();
			setTimeout(function() {
				socket.send(JSON.stringify(content));
			}, 700);

		} else {
			socket.send(JSON.stringify(content));
		}

	} else if(receipt.html_content != '') {
		// If printer type browser then print content
		$('#receipt_section').html(receipt.html_content);
		__currency_convert_recursively($('#receipt_section'));
		setTimeout(function () { window.print(); }, 1000);
	}
}

/**
 * Calculate discounted unit price.
 * 
 * @param  HTMLElement  row
 * @return json
 */
function calculate_discounted_unit_price(row){
	var this_unit_inc_price = __read_number(row.find('input#u_price_inc_tax'));
	var this_unit_exc_price = __read_number(row.find('input#u_price_exc_tax'));

	var row_discounted_unit_inc_price = this_unit_inc_price;
	var row_discounted_unit_exc_price = this_unit_exc_price;

	var row_discount_type = row.find('select.row_discount_type').val();
	var row_discount_amount = __read_number(row.find('input.row_discount_amount'));

	if (app_business == 'workshop') {
		let quote_tax_detail = $('input#quote-tax-detail').val();

		if (quote_tax_detail == 1) {
			let tax_percent = (this_unit_inc_price / this_unit_exc_price) - 1;
			row_discount_amount = row_discount_amount * (1 + tax_percent);
		}
	}

	if (row_discount_amount) {
		if (row_discount_type == 'fixed') {
			row_discounted_unit_inc_price = this_unit_inc_price - row_discount_amount;
			var percent = row_discount_amount / this_unit_inc_price;
			row_discounted_unit_exc_price = this_unit_exc_price - (this_unit_exc_price * percent);

		} else {
			row_discounted_unit_inc_price = __substract_percent(this_unit_inc_price, row_discount_amount);
			row_discounted_unit_exc_price = __substract_percent(this_unit_exc_price, row_discount_amount);
		}
	}

	return {
		'unit_inc_price' : row_discounted_unit_inc_price,
		'unit_exc_price' : row_discounted_unit_exc_price,
	}
}

/**
 * Get unit price from discounted unit price.
 * 
 * @param  HTMLElement  row 
 * @param  float  discounted_unit_price 
 * @returns 
 */
function get_unit_price_from_discounted_unit_price(row, discounted_unit_price) {
	var this_unit_price = discounted_unit_price;
	var row_discount_type = row.find('select.row_discount_type').val();
	var row_discount_amount = __read_number(row.find('input.row_discount_amount'));

	if (row_discount_amount) {
		if (row_discount_type == 'fixed') {
			this_unit_price = discounted_unit_price + row_discount_amount;

		} else {
			this_unit_price = __get_principle(discounted_unit_price, row_discount_amount, true);
		}
	}

	return this_unit_price;
}

/**
 * Calculate discounted.
 * 
 * @param  HTMLElement  row 
 * @param  float  tax_inc_price 
 * @param  float  tax_exc_price 
 * @return json
 */
function calculate_discounted(row, tax_inc_price, tax_exc_price) {
	var price_tax_inc = tax_inc_price;
	var price_tax_exc = tax_exc_price;

	var row_discount_type = row.find('select.row_discount_type').val();
	var row_discount_amount = __read_number(row.find('input.row_discount_amount'));

	if (row_discount_amount) {
		if (row_discount_type == 'fixed') {
			price_tax_inc = tax_inc_price - row_discount_amount;
			
			if (tax_exc_price != tax_inc_price) {
				var percent = row_discount_amount / tax_inc_price;
				price_tax_exc = tax_exc_price - (tax_exc_price * percent);
			
			} else {
				price_tax_exc = tax_exc_price - row_discount_amount;
			}

		} else {
			price_tax_inc = __substract_percent(tax_inc_price, row_discount_amount);
			price_tax_exc = __substract_percent(tax_exc_price, row_discount_amount);
		}
	}

	return {
		'unit_inc_price' : price_tax_inc,
		'unit_exc_price' : price_tax_exc
	}
}

/**
 * Stop execution for defined time.
 * 
 * @param  int  ms
 * @return Promise
 */
function sleep(ms) {
	return new Promise(
		resolve => setTimeout(resolve, ms)
	);
}

/**
 * Get product rows for reservations only.
 * 
 * @param  int  quote_id
 * @param  int  variation_id
 * @param  int  location_id
 * @param  int  warehouse_id
 * @return void
 */
function add_product_row_from_reservation(quote_id, variation_id, location_id, warehouse_id) {
	var row_count = $('input#product_row_count').val();
	var reservation_id = $('#reservation_id').val();

	$.ajax({
		method: "GET",
		url: '/reservations/get_product_row/' + quote_id + '/' + variation_id + '/' + location_id + '/' + row_count,
		data: { warehouse_id: warehouse_id, check_qty_available: 1, reservation_id: reservation_id },
		dataType: 'html',
		success: function(data) {
			$('table#pos_table tbody').append(data).find('input.pos_quantity');
			var this_row = $('table#pos_table tbody').find("tr").last();
			pos_each_row(this_row);
			pos_total_row();
		}
	});

	$('input#product_row_count').val(parseInt(row_count) + 1); // Increment row count
}

/**
 * Add payment rows in form from reservation.
 * 
 * @param  int  removable
 * @param  int  row_index
 * @param  int  payment_id
 * @return void
 */
function add_payment_row_from_reservation(removable, row_index, payment_id) {
	$.ajax({
		method: "GET",
		url: '/reservations/get_payment_row/' + removable + '/' + row_index + '/' + payment_id,
		dataType: 'html',
		success: function(data) {
			$('div#payment_rows_div').prepend(data);

			if (app_business == 'optics') {
				show_payment_note();
			}
		}
	});
}

/**
 * Reset patient select.
 * 
 * @return void
 */
function set_default_patient() {
	var default_patient_id = $('#default_patient_id').val();
	var default_patient_name = $('#default_patient_name').val();
	var exists  = $('select#select_patient_id option[value=' + default_patient_id + ']').length;

	if (exists == 0) {
		$("select#select_patient_id").append($('<option>', { value: default_patient_id, text: default_patient_name }));
	}
	
	$('select#select_patient_id').val(default_patient_id).trigger("change");
}

/**
 * Search commission agent by code.
 * 
 * @param  string  code 
 * @param  HTMLElement  code_input 
 * @param  HTMLElement  name_input 
 * @param  HTMLElement  send_input 
 * @param  string  type 
 */
function search_commission_agent(code, code_input, name_input, send_input, type) {
	if (code == '') {
		code_input.val('');
		$('#txt-commission-agent').val('');

	} else {
		let route = '/patients/getEmployeeByCode/' + code;

		$.get(route, function(res) {
			if (res.success) {
				if (res.emp) {
					name_input.val(res.msg);
					send_input.val(res.emp_id);
					// if (type === 'user') {
					// 	send_input.val(res.user_id);
					// } else {
					// 	send_input.val(res.emp_id);
					// }

				} else {
					name_input.val('');
					code_input.val('');

					Swal.fire({
						title: res.msg,
						icon: 'error',
						timer: 2000,
						showConfirmButton: false,
					});
				}

			} else {
				name_input.val('');
				code_input.val('');

				Swal.fire({
					title: res.msg,
					icon: 'error',
					timer: 2000,
					showConfirmButton: false,
				});
			}
		});
	}
}

/**
 * Show single or multiple payment note.
 * 
 * @return void
 */
function show_payment_note() {
	let select_reservations = $('#reservations').val();

	if (select_reservations === '' || select_reservations === 0 || select_reservations === null || select_reservations === undefined) {
		$('.show-note').each(function () {
			$(this).show();
		});

		$('.show-mult-note').each(function () {
			$(this).hide();
		});

		$('#flag-first-row').val(0);

		$('#reservation-finalize').removeAttr('disabled');

		$('#add-payment-row').removeAttr('disabled');

		$("input#reservation_id").val(0);

		$('.payment-form').removeClass('disabled-input');

	} else {
		$('.show-note').each(function() {
			$(this).hide();
		});

		$('.show-mult-note').each(function() {
			$(this).show();
		});

		$('#flag-first-row').val(1);

		$('#reservation-finalize').attr('disabled', 'disabled');

		$('#add-payment-row').prop('disabled', true);

		$("input#reservation_id").val($('#reservations').val());

		$('.payment-form').addClass('disabled-input');
	}
}

/**
 * Set final correlative field.
 * 
 * @return void
 */
function set_final_correlative() {
	let location_id = $('#select_location_id').val();
	let document_id = $('#documents').val();

	let route = $('#final-correlative').data('route');

	if (location_id.length > 0 && document_id.length > 0) {
		$.ajax({
			url: route,
			type: 'GET',
			dataType: 'json',
			data: {
				location_id: location_id,
				document_id: document_id
			},
			success: function(res) {
				if (res.success == true) {
					$('#final-correlative').val(res.final_correlative);
				}
			}
		});
	}
}

/**
 * Get customer vehicles.
 * 
 * @param  int  $id
 * @return void
 */
 function getCustomerVehicles(id, customer_vehicle_id) {
    $('#customer_vehicle_id').empty();

    let route = '/quotes/get-customer-vehicles/' + id;

    $.get(route, function (res) {
        $('#customer_vehicle_id').append('<option value="0" disabled selected>' + LANG.customer_vehicle + '</option>');

        $(res).each(function(key, value) {
            $('#customer_vehicle_id').append('<option value="' + value.id + '">' + value.name + '</option>');
        });

		if (customer_vehicle_id != null) {
			$('#customer_vehicle_id option[value=' + customer_vehicle_id + ']').attr('selected', true);
		}
    });
}
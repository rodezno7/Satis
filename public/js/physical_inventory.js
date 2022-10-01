$(document).ready(function () {
	$.fn.modal.Constructor.prototype.enforceFocus = function() {};

	// Modal
	$('.physical_inventory_modal').on('shown.bs.modal', function () {
		// Select2
		$(this).find('.select2').select2();

		// Datetime picker
		$('#start_date').datetimepicker({
			format: moment_date_format,
			ignoreReadonly: true
	
		});

		// Get warehouses by locations
		$("#location_id").on('change', function(){
			var location_id = $(this).val();
			var warehouse_id = $("#warehouse_id");

			if ($("#location_id").val() > 0) {	
				$.ajax({
					method: "GET",
					url: "/warehouses/get_warehouses/" + location_id,
					dataType: "json",
					success: function(warehouses){
						$("#warehouse_id").attr('disabled', false);
						warehouse_id.empty().append(new Option(LANG.select_please, 0, true, true))
						$.each(warehouses, function(i, w) {
							warehouse_id.append(new Option(w.name, w.id, false, false));
						});
					}
				});

			} else {
				warehouse_id.empty().append(new Option(LANG.select_please, 0, true, true));
				warehouse_id.attr('disabled', 'disabled');
			}
		});

		// On click of autoload input
		$(document).on('click', 'input#autoload', function () {
			if ($('input#autoload').is(':checked')) {
				$('input#autoload_rotation').prop('disabled', true);
			} else {
				$('input#autoload_rotation').prop('disabled', false);
			}
		});

		// On click of autoload input
		$(document).on('click', 'input#autoload_rotation', function () {
			if ($('input#autoload_rotation').is(':checked')) {
				$('input#autoload').prop('disabled', true);
			} else {
				$('input#autoload').prop('disabled', false);
			}
		});
	});

	// Datatable (index view)
    physical_inventory_table = $('#physical_inventory_table').DataTable({
        processing: true,
        serverSide: true,
        "ajax": {
            "url": "/physical-inventory",
        },
        columns: [
            { data: 'code', name: 'code' },
			{ data: 'name', name: 'name' },
			{ data: 'start_date', name: 'start_date' },
			{ data: 'location', name: 'business_locations.name' },
			{ data: 'warehouse', name: 'warehouses.name' },
			{ data: 'status', name: 'status' },
			{ data: 'responsible', name: 'responsible' },
			{ data: 'action', name: 'action', searchable: false, orderable: false },
        ],
    });

	// Datatable (edit view)
	if ($('#is_editable').val() == 1) {
		var url_pil = "/physical-inventory/" + $('#physical_inventory_id').val() + "/edit";
		var visible_pil = true;
	} else {
		var url_pil = "/physical-inventory/" + $('#physical_inventory_id').val();
		var visible_pil = false;
	}

    physical_inventory_lines_table = $('#physical_inventory_lines_table').DataTable({
        processing: true,
        serverSide: true,
		ordering: false,
        "ajax": {
            "url": url_pil,
        },
        columns: [
            { data: 'sub_sku', name: 'variations.sub_sku' },
			{ data: 'product_name', name: 'product_name' },
			{ data: 'quantity', name: 'quantity', searchable: false, orderable: false },
			{ data: 'qty_available', name: 'qty_available', searchable: false, orderable: false },
			{ data: 'difference', name: 'difference', searchable: false, orderable: false },
			{ data: 'last_purchased_price', name: 'last_purchased_price', searchable: false, orderable: false },
			{ data: 'action', name: 'action', searchable: false, orderable: false, className: 'text-center', visible: visible_pil },
        ],
    });

	// Add physical inventory
	$(document).on('submit', 'form#physical_inventory_add_form', function(e) {
		e.preventDefault();
		$(this).find('button[type="submit"]').attr('disabled', true);
		var data = $(this).serialize();
		$.ajax({
			method: "POST",
			url: $(this).attr("action"),
			dataType: "json",
			data: data,
			success: function(result) {
				if (result.success === true) {
					$('div.physical_inventory_modal').modal('hide');
					Swal.fire({
						title: result.msg,
						icon: "success",
					});
					physical_inventory_table.ajax.reload(null, false);
				} else {
					Swal.fire({
						title: result.msg,
						icon: "error",
					});
				}
				$(this).find('button[type="submit"]').prop('disabled', false);
			}
		});
	});

    // Search bar
    if ($("#search_product_for_physical_inventory").length > 0) {
		$( "#search_product_for_physical_inventory" ).autocomplete({
			source: function(request, response) {
	    		$.getJSON(
                    "/physical-inventory/products/list",
                    { term: request.term, category: $('#category').val() },
                    response
                );
	  		},
			minLength: 1,
			response: function(event, ui) {
				if (ui.content.length == 1) {
					ui.item = ui.content[0];
					if (ui.item.enable_stock == 1) {
						$(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
						$(this).autocomplete('close');
					}
				} else if (ui.content.length == 0) {
		            swal(LANG.no_products_found)
		        }
			},
			select: function( event, ui ) {
				$(this).val(null);
				add_physical_inventory_line(ui.item.variation_id);
			}
		})
		.autocomplete( "instance" )._renderItem = function( ul, item ) {
	        if (item.enable_stock != 1) {
	        	return ul;
	        } 
	        else {
	        	var string =  "<div>" + item.name;
	        	if (item.type == 'variable') {
	        		string += '-' + item.variation;
	        	}
	        	string += ' (' + item.sub_sku + ') </div>';
	    		return $("<li>").append(string).appendTo( ul );
	        }
	    }
    }

	// Update physical inventory line
	$(document).on('change', 'input.quantity_pil', function() {
		var quantity = $(this).val();
		var original_quantity = $(this).attr('data-line-quantity');
		var pil_id = $(this).attr('data-line-id');
		var allow_decimal = $(this).attr('data-rule-allow-decimal');

		if (! (quantity && pil_id)) {
			$(this).val(original_quantity);
			return;
		}

		if (allow_decimal == 0) {
			if (quantity % 1 != 0) {
				Swal.fire({
					title: LANG.decimal_value_not_allowed,
					icon: 'error',
				});
				$(this).val(original_quantity);
				return;
			}
		}

		if (quantity < 0) {
			quantity = Number.parseFloat(Math.abs(quantity)).toFixed(2);
			$(this).val(quantity);
		}

		$.ajax({
			method: 'post',
			url: '/physical-inventory-line/update-line',
			dataType: 'json',
			data: { id: pil_id, quantity: quantity },
			success: function(res) {
				if (res.success) {
					physical_inventory_lines_table.ajax.reload(null, false);
				}
			}
		});
	});

	// Delete physical inventory line
	$(document).on('click', 'i.delete_pil', function() {
		swal({
			title: LANG.sure,
			text: LANG.delete_content,
			icon: "warning",
			buttons: true,
			dangerMode: true
		}).then((willDelete) => {
			if (willDelete) {
				$.ajax({
					method: 'delete',
					url: '/physical-inventory-line/' + $(this).attr('data-line-id'),
					dataType: 'json',
					data: { physical_inventory_line: $(this).attr('data-line-id') },
					success: function(res) {
						if (res.success === true) {
							Swal.fire({
								title: res.msg,
								icon: 'success',
							});
							physical_inventory_lines_table.ajax.reload(null, false);
						} else {
							Swal.fire({
								title: res.msg,
								icon: 'error',
							});
						}
					}
				});
			}
		});
	});

	// Datetime picker
	$('input#end_date').datetimepicker({
		format: moment_date_format,
		ignoreReadonly: true
	});

	// On change of end_date input
	$('input#end_date').datetimepicker().on('dp.change', function (event) {
		if ($('#is_editable').val() == 1) {
			update_execution_date();
		}
	});

	// On change of code input
	$(document).on('change', 'input#code', function () {
		if ($('#is_editable').val() == 1) {
			update_code();
		}
	});

	// On click of delete_movement_types_button button
	$(document).on('click', 'a.delete_physical_inventory_button', function () {
		swal({
			title: LANG.sure,
			text: LANG.delete_content,
			icon: 'warning',
			buttons: true,
			dangerMode: true,
		}).then((willDelete) => {
			if (willDelete) {
				var href = $(this).data('href');
				var data = $(this).serialize();

				$.ajax({
					method: 'delete',
					url: href,
					dataType: 'json',
					data: data,
					success: function (result) {
						if (result.success === 1) {
							Swal.fire({
								title: result.msg,
								icon: 'success',
							});

							physical_inventory_lines_table.ajax.reload();

						} else {
							Swal.fire({
								title: result.msg,
								icon: 'error',
							});
						}
					}
				});
			}
		});
	});
});

function setLocation(warehouse_id) {
	if (isNaN(warehouse_id)) {
		return null;
	}

	$.ajax({
		type: "get",
		url: "/warehouses/get-location/" + warehouse_id,
		dataType: "text",
		success: function(data) {
			$("input#location_id").val(data);
		}
	});
}

function add_physical_inventory_line(variation_id) {
	var physical_inventory_id = $('#physical_inventory_id').val();
	
	$.ajax({
		method: "post",
		url: "/physical-inventory-line",
		dataType: "json",
		data: {
			physical_inventory_id: physical_inventory_id,
			variation_id: variation_id
		},
		success: function(res) {
			if (res.success) {
				physical_inventory_lines_table.ajax.reload(null, false);
			} else {
				Swal.fire({
					title: res.msg,
					icon: 'error',
				});
			}
		}
	});
}

/**
 * Update end_date field.
 * 
 * @return void
 */
function update_execution_date() {
	let physical_inventory_id = $('#physical_inventory_id').val();
	let end_date = $('#end_date').val();
	
	$.ajax({
		method: "post",
		url: "/physical-inventory/update-execution-date",
		dataType: "json",
		data: {
			physical_inventory_id: physical_inventory_id,
			end_date: end_date
		},
		success: function(res) {
			if (res.success) {
				Swal.fire({
					title: res.msg,
					icon: 'success',
				});
			} else {
				Swal.fire({
					title: res.msg,
					icon: 'error',
				});
			}
		}
	});
}

/**
 * Update code field.
 * 
 * @return void
 */
 function update_code() {
	let physical_inventory_id = $('#physical_inventory_id').val();
	let code = $('input#code').val();
	
	$.ajax({
		method: "post",
		url: "/physical-inventory/update-code",
		dataType: "json",
		data: {
			physical_inventory_id: physical_inventory_id,
			code: code
		},
		success: function(res) {
			if (res.success) {
				Swal.fire({
					title: res.msg,
					icon: 'success',
				});
			} else {
				Swal.fire({
					title: res.msg,
					icon: 'error',
				});
			}
		}
	});
}
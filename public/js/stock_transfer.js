// Number of decimal places to store and use in calculations
price_precision = $('#price_precision').length > 0 ? $('#price_precision').val() : 6;

// Number of decimal places to show
inventory_precision = $('#inventory_precision').length > 0 ? $('#inventory_precision').val() : 2;

$(document).ready(function() {
	// Add products
    if ($( "#search_product_for_stock_adjustment" ).length > 0) {
        // Add Product
		$( "#search_product_for_stock_adjustment" ).autocomplete({
			source: function(request, response) {
	    		$.getJSON("/products/list_stock_transfer", { warehouse_id: $('select#from_warehouse_id').val(), term: request.term }, response);
	  		},
			minLength: 2,
			response: function(event, ui) {
				if (ui.content.length == 1) {
					ui.item = ui.content[0];
					if ((ui.item.qty_available - ui.item.qty_reserved) > 0 && ui.item.enable_stock == 1) {
						$(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
						$(this).autocomplete('close');
					}
				} else if (ui.content.length == 0) {
		            swal(LANG.no_products_found)
		        }
			},
			focus: function(event, ui) {
				if ((ui.item.qty_available - ui.item.qty_reserved) <= 0) {
					return false;
				}
			},
			select: function(event, ui) {
				if ((ui.item.qty_available - ui.item.qty_reserved) > 0) {
					$(this).val(null);
	    			stock_transfer_product_row(ui.item.variation_id);
				} else {
					alert(LANG.out_of_stock);
				}
			}
		})
		.autocomplete( "instance" )._renderItem = function(ul, item) {
			if ((item.qty_available - item.qty_reserved) <= 0) {
				var string = '<li class="ui-state-disabled">&nbsp;' + item.name;
				if (item.type == 'variable') {
	        		string += '-' + item.variation;
	        	}
				string += ' (' + item.sub_sku + ')<br>&nbsp;(' + LANG.out_of_stock + ')</li>';
	            return $(string).appendTo(ul);
	        } else if (item.enable_stock != 1) {
	        	return ul;
	        } 
	        else {
	        	var string =  "<div>" + item.name;
	        	if (item.type == 'variable') {
	        		string += '-' + item.variation;
	        	}
				string += ' (' + item.sub_sku + ')<br>' + LANG.stock + ': ' + Number(item.qty_available).toFixed(2) + ' - ' + LANG.reserved + ': ' + Number(item.qty_reserved).toFixed(2) +  '</div>';
	    		return $("<li>")
	        		.append(string)
	        		.appendTo( ul );
	        }
	    }
    }

	if ($('input#transaction_id').val() > 0) {
		$('#search_product_for_stock_adjustment').removeAttr('disabled');
	}

    $('select#from_warehouse_id, select#to_warehouse_id').on('change', function() {
		if ($(this).val()) {
			$("#search_product_for_stock_adjustment").removeAttr('disabled');
		} else {
			$("#search_product_for_stock_adjustment").attr('disabled', 'disabled');
		}
		$('table#stock_adjustment_product_table tbody').html('');
		$('#product_row_index').val(0);

		var warehouse_id = $(this).val();
		var hidde_location = $(this).closest('div').find('input[type=hidden]');
		get_location_id(warehouse_id, hidde_location);
	});

	$(document).on('change', 'input.product_quantity', function() {
		update_table_row($(this).closest('tr'));
	});

	$(document).on('change', 'input.product_unit_price', function() {
		update_table_row($(this).closest('tr'));
	});

	$(document).on('click', '.remove_product_row', function() {
		swal({
          title: LANG.sure,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
            	$(this).closest('tr').remove();
				update_table_total();
            }
        });
	});

	// Date picker
    $('#transaction_date').datepicker({
        autoclose: true,
        format:datepicker_date_format
    });

    jQuery.validator.addMethod("notEqual", function(value, element, param) {
		return this.optional(element) || value != param;
	}, "Please select different location");

    $('form#stock_transfer_form').validate({
    	rules: {
		    from_warehouse_id: { 
		    	notEqual: function() {
		    		return $('select#to_warehouse_id').val();
		    	} 
		    }
		}
    });
    
    stock_transfer_table = $('#stock_transfer_table').DataTable({
		processing: true,
		serverSide: true,
		aaSorting: [[0, 'desc']],
		ajax: {
			url: '/stock-transfers',
			data: function (d) {
				d.warehouse_id = $("#warehouse").val();
			}
		},
		columnDefs: [{
			"targets": 9,
			"orderable": false,
			"searchable": false
		}],
		columns: [
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'ref_no', name: 'ref_no' },
            { data: 'warehouse_from', name: 'w1.name' },
            { data: 'warehouse_to', name: 'w2.name' },
            { data: 'quantity', name: 'tsl.quantity' },
            { data: 'final_total', name: 'final_total' },
			{ data: 'responsable', name: 'responsable' },
			{ data: 'transfer_state', name: 'transfer_state' },
            { data: 'additional_notes', name: 'additional_notes' },
            { data: 'action', name: 'action' }
        ],
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('#stock_transfer_table'));
        }
    });

	// Warehouse filter
	$('select#select_warehouse').on('change', function() {
		$("input#warehouse").val($("select#select_warehouse").val());
		stock_transfer_table.ajax.reload();
	});

    var detailRows = [];

    $('#stock_transfer_table tbody').on('click', '.view_stock_transfer', function() {
        var tr = $(this).closest('tr');
        var row = stock_transfer_table.row( tr );
        var idx = $.inArray( tr.attr('id'), detailRows );
 
        if (row.child.isShown()) {
            $(this).find('i').removeClass( 'fa-eye' ).addClass('fa-eye-slash');
            row.child.hide();
 
            // Remove from the 'open' array
            detailRows.splice( idx, 1 );
        } else {
            $(this).find('i').removeClass( 'fa-eye-slash' ).addClass('fa-eye');

            row.child( get_stock_transfer_details( row.data() ) ).show();
 
            // Add to the 'open' array
            if ( idx === -1 ) {
                detailRows.push( tr.attr('id') );
            }
        }
    } );

    // On each draw, loop over the `detailRows` array and show any child rows
    stock_transfer_table.on('draw', function() {
        $.each( detailRows, function(i, id) {
            $('#' + id + ' .view_stock_transfer').trigger('click');
        } );
    } );

    // Delete stock transfer
    $(document).on('click', '.delete_stock_transfer', function() {
    	swal({
          title: LANG.sure,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
            	var href = $(this).data('href');
            	$.ajax({
					method: "DELETE",
					url: href,
					dataType: "json",
					success: function(result){
						if (result.success) {
							toastr.success(result.msg);
							stock_transfer_table.ajax.reload();
						} else {
							toastr.error(result.msg);
						}
					}
				});
            }
        });
    });

	// Receive stock transfer
    $(document).on('click', '.receive_stock_transfer', function() {
		let href = $(this).data('href');

		Swal.fire({
			title: LANG.sure,
			text: LANG.receive_transfer_msg,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: LANG.yes,
			cancelButtonText: LANG.not,

		}).then((resul) => {
			if (resul.isConfirmed) {
				$.ajax({
					method: 'post',
					url: href,
					dataType: 'json',
					success: function(result) {
						if (result.success == true) {
							toastr.success(result.msg);
							stock_transfer_table.ajax.reload();
						} else {
							toastr.error(result.msg);
						}
					}
				});
			}
		});
    });

	// Count stock transfer
    $(document).on('click', '.count_stock_transfer', function() {
		let href = $(this).data('href');

		Swal.fire({
			title: LANG.sure,
			text: LANG.count_transfer_msg,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: LANG.yes,
			cancelButtonText: LANG.not,

		}).then((resul) => {
			if (resul.isConfirmed) {
				$.ajax({
					method: 'post',
					url: href,
					dataType: 'json',
					success: function(result) {
						if (result.success == true) {
							toastr.success(result.msg);
							stock_transfer_table.ajax.reload();
						} else {
							toastr.error(result.msg);
						}
					}
				});
			}
		});
    });

	$('form').on('keyup keypress', function(e) {
		var keyCode = e.keyCode || e.which;
		if (keyCode === 13) { 
			e.preventDefault();
			return false;
		}
	});
});

function stock_transfer_product_row(variation_id) {
	var row_index = parseInt($('#product_row_index').val());
	var warehouse_id = $('select#from_warehouse_id').val();

	var add_row = true;

	$('#stock_adjustment_product_table tbody').find('tr').each(function() {
		var row_variation_id = $(this).find('.row_variation_id').val();

		if (row_variation_id == variation_id) {
			add_row = false;
		}
	});

	if (add_row) {
		$.ajax({
			method: "POST",
			url: "/stock-transfers/get_product_row_transfer",
			data: {row_index: row_index, variation_id: variation_id, warehouse_id: warehouse_id, check_qty_available : 1},
			dataType: "html",
			success: function(result) {
				$('table#stock_adjustment_product_table tbody').append(result);
				update_table_total();
				$('#product_row_index').val( row_index + 1);
			}
		});

	} else {
		Swal.fire({
			title: LANG.warning_product_added,
			icon: 'warning'
		});
	}
}

/** Gets location_id for warehouse_id */
function get_location_id(warehouse_id, hidde_location) {
	$.ajax({
		type: "GET",
		url: "/warehouses/get-location/" + warehouse_id,
		dataType: "text",
		success: function(location_id){
			hidde_location.val(location_id);
		}
	});
}

function update_table_total() {
	var table_total = 0;
	var table_total_show = 0;
	$('table#stock_adjustment_product_table tbody tr').each(function() {
		var this_total = parseFloat(__read_number($(this).find('input.product_line_total_hidden')));
		var this_total_show = parseFloat(__read_number($(this).find('input.product_line_total')));
		if (this_total) {
			table_total += this_total;
		}
		if(this_total_show){
			table_total_show += this_total_show;
		}
	});
	$('input#total_amount').val(table_total);
	$('span#total_adjustment').text(__number_f(table_total_show, false, false, inventory_precision));
}

function update_table_row(tr) {
	var quantity = parseFloat( __read_number(tr.find('input.product_quantity')));
	var unit_price = parseFloat( __read_number(tr.find('input.product_unit_price_hidden')));
	var unit_price_show = parseFloat( __read_number(tr.find('input.product_unit_price')));
	var row_total = 0;
	if (quantity && unit_price) {
		row_total = quantity * unit_price;
	}
	var row_total_show = 0;
	if( quantity && unit_price_show){
		row_total_show = quantity * unit_price_show;
	}
	tr.find('input.product_line_total').val(__number_f(row_total_show, false, false, inventory_precision));
	__write_number(tr.find('input.product_line_total_hidden'), row_total, false, price_precision);
	update_table_total();
}

function get_stock_transfer_details(rowData) {
	var div = $('<div/>')
        .addClass( 'loading' )
        .text( 'Loading...' );
    $.ajax({
        url: '/stock-transfers/' + rowData.DT_RowId,
        dataType: 'html',
        success: function (data) {
            div
                .html(data)
                .removeClass('loading');
			
			__currency_convert_recursively($('.details-table'));
        }
    });
 
    return div;
}

function disabled_buttons() {
	$('button#save_stock_transfer').attr('disabled', 'true');
	$('button#send_stock_transfer').attr('disabled', 'true');
	$('button#cancel_stock_transfer').attr('disabled', 'true');
}

function enabled_buttons() {
	$('button#save_stock_transfer').removeAttr('disabled');
	$('button#send_stock_transfer').removeAttr('disabled');
	$('button#cancel_stock_transfer').removeAttr('disabled');
}

// Save button
$(document).on('click', 'button#save_stock_transfer', function(e) {
	e.preventDefault();

	disabled_buttons();

	let data = $('form#stock_transfer_form').serializeArray();
	data.push({ name: 'download_product', value: 0 });

	let url = $('form#stock_transfer_form').attr('action');
	let redirect_url = $('#redirect_url').val();
	
	if ($('table#stock_adjustment_product_table tbody').find('.product_row').length <= 0) {
		toastr.warning(LANG.no_products_added);
		enabled_buttons();
		return false;
	}

	if ($('form#stock_transfer_form').valid()) {
		Swal.fire({
			title: LANG.sure,
			text: LANG.save_transfer_msg,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: LANG.yes,
			cancelButtonText: LANG.not,
		}).then((resul) => {
			if (resul.isConfirmed) {
				$.ajax({
					method: "post",
					url: url,
					data: $.param(data),
					dataType: "json",
					success: function(result) {
						if (result.success == true) {
							toastr.success(result.msg);
							setTimeout(function() { window.location = redirect_url; }, 2000);
						} else {
							toastr.error(result.msg);
							enabled_buttons();
						}
					}
				});

			} else {
				enabled_buttons();
				return false;
			}
		});

	} else {
		$('button#save_stock_transfer').removeAttr('disabled');
		return false;
	}
});

// Send button
$(document).on('click', 'button#send_stock_transfer', function(e) {
	e.preventDefault();

	disabled_buttons();

	let data = $('form#stock_transfer_form').serializeArray();
	data.push({ name: 'download_product', value: 1 });
	let url = $('form#stock_transfer_form').attr('action');
	let redirect_url = $('#redirect_url').val();
	
	if ($('table#stock_adjustment_product_table tbody').find('.product_row').length <= 0) {
		toastr.warning(LANG.no_products_added);
		enabled_buttons();
		return false;
	}

	if ($('form#stock_transfer_form').valid()) {
		Swal.fire({
			title: LANG.sure,
			text: LANG.send_transfer_msg,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: LANG.yes,
			cancelButtonText: LANG.not,
		}).then((resul) => {
			if (resul.isConfirmed) {
				$.ajax({
					method: "post",
					url: url,
					data: $.param(data),
					dataType: "json",
					success: function(result) {
						if (result.success == true) {
							toastr.success(result.msg);

							enable_remission_note = $('#enable_remission_note').val();

							if (enable_remission_note == 1) {
								let url_note = `/reports/remission_note/${result.sell_transfer_id}`;
								setTimeout(function() { window.open(url_note, '_blank'); }, 1000);
								setTimeout(function() { window.location = redirect_url; }, 2000);

							} else {
								redirect_index();
								stock_transfer_print(result.receipt);
							}

						} else {
							toastr.error(result.msg);
							enabled_buttons();
						}
					}
				});

			} else {
				enabled_buttons();
				return false;
			}
		});

	} else {
		enabled_buttons();
		return false;
	}
});

// Cancel button
$(document).on('click', 'button#cancel_stock_transfer', function(e) {
	e.preventDefault();

	disabled_buttons();

	let redirect_url = $('#redirect_url').val();

	Swal.fire({
		title: LANG.sure,
		text: LANG.cancel_transfer_msg,
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: LANG.yes,
		cancelButtonText: LANG.not,
	}).then((resul) => {
		if (resul.isConfirmed) {
			setTimeout(function() { window.location = redirect_url; }, 1000);
			
		} else {
			enabled_buttons();
			return false;
		}
	});
});

function redirect_index() {
	var redirect_url = $('#redirect_url').val();
	setTimeout(function() { window.location = redirect_url; }, 4000);
	return true;
}

function stock_transfer_print(receipt) {
	$('#receipt_section').html(receipt.html_content);
	__currency_convert_recursively($('#receipt_section'));
	setTimeout(function() { window.print(); }, 1000);
}
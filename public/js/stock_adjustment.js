// Number of decimal places to store and use in calculations
price_precision = $('#price_precision').length > 0 ? $('#price_precision').val() : 6;

// Number of decimal places to show
inventory_precision = $('#inventory_precision').length > 0 ? $('#inventory_precision').val() : 2;

$(function () {

	$("select#warehouse_id").attr('disabled', true);
	//Add products
    if($( "#search_product_for_srock_adjustment" ).length > 0){
        //Add Product
		$( "#search_product_for_srock_adjustment" ).autocomplete({
			source: function(request, response) {
	    		$.getJSON("/products/list", {
					location_id: $('#location_id').val(),
					warehouse_id: $("select#adjustment_type").val() == 'normal' ? 0 : $('#warehouse_id').val(),
					check_qty: $("select#adjustment_type").val() == 'normal' ? 0 : 1,
					term: request.term
				}, response);
			},
			minLength: 2,
			response: function(event,ui) {
				if (ui.content.length == 1)
				{
					ui.item = ui.content[0];
					if((ui.item.qty_available - ui.item.qty_reserved) > 0 && ui.item.enable_stock == 1){
						$(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
						$(this).autocomplete('close');
					}
				} else if (ui.content.length == 0)
		            {
		                swal(LANG.no_products_found)
		            }
			},
			focus: function( event, ui ) {
				if(ui.item.qty_available <= 0){
					return false;
				}
			},
			select: function( event, ui ) {
				if((ui.item.qty_available - ui.item.qty_reserved) > 0 || $("select#adjustment_type").val() == "normal"){
					$(this).val(null);
	    			stock_adjustment_product_row(ui.item.variation_id);
				} else{
					alert(LANG.out_of_stock);
				}
			}
		})
		.autocomplete( "instance" )._renderItem = function( ul, item ) {
			if((item.qty_available - item.qty_reserved) <= 0 && $("select#adjustment_type").val() == "abnormal"){
				
				var string = '<li class="ui-state-disabled">&nbsp;'+ item.name;
				if(item.type == 'variable'){
	        		string += '-' + item.variation;
	        	}
				string += ' (' + item.sub_sku + ')<br>&nbsp;(' + LANG.out_of_stock + ')</li>';
	            return $(string).appendTo(ul);
	        } else if(item.enable_stock != 1){
	        	return ul;
	        } 
	        else {
	        	var string =  "<div>" + item.name;
	        	if(item.type == 'variable'){
	        		string += '-' + item.variation;
	        	}
				string += ' (' + item.sub_sku + ')<br>&nbsp;' + LANG.stock + ': ' + Number(item.qty_available).toFixed(2) + ' - ' + LANG.reserved + ': ' + Number(item.qty_reserved).toFixed(2) +  '</div>';
	    		return $( "<li>" )
	        		.append(string)
	        		.appendTo( ul );
	        }
	    }
    }

	// Disabled/enabled search product bar
	$(document).on('change', 'select#warehouse_id, select#adjustment_type', function() {
		if ($('select#warehouse_id').val() && $('select#adjustment_type').val()) {
			$( "#search_product_for_srock_adjustment" ).removeAttr('disabled');
			setLocation($(this).val());
		} else {
			$( "#search_product_for_srock_adjustment" ).attr('disabled', 'disabled');
			$("input#location_id").val(null);
		}
		// $('table#stock_adjustment_product_table tbody').html('');
		// $('#product_row_index').val(0);
	});

	$(document).on('change', 'select#adjustment_type', function() {
		var ref_count = $('#ref_count').val();
		var type = $('select#adjustment_type').val();
		
		if($(this).val() != ""){
            $("select#warehouse_id").attr('disabled', false);
        }else{
            $("select#warehouse_id").attr('disabled', true);
        }

		if (type == 'normal') {
			type = 'stock_adjustment';
			
		} else if (type == 'abnormal') {
			type = 'stock_adjustment_out';
		}

		if ($('select#adjustment_type').val()) {
			var route = `/stock-adjustments/create/${ref_count}/${type}/get-reference`;

			$.get(route, function(res) {
				$('#ref_no').val(res.reference);
			});

		} else {
			$('#ref_no').val('');
		}
	});

	$(document).on( 'change', 'input.product_quantity', function(){
		update_table_row( $(this).closest('tr') );
	});
	$(document).on( 'change', 'input.product_unit_price', function(){
		update_table_row( $(this).closest('tr') );
	});

	$(document).on( 'click', '.remove_product_row', function(){
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

	//Date picker
    $('#transaction_date').datepicker({
        autoclose: true,
        format: datepicker_date_format
    });

    $('form#stock_adjustment_form').validate();

    stock_adjustment_table = $('#stock_adjustment_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: '/stock-adjustments',
		aaSorting: [[0, 'desc']],
		columnDefs: [
			{
				targets: 6,
				orderable: false,
				searchable: false
			}
		],
		columns: [
            { data: 'transaction_date', name: 'transaction_date'  },
            { data: 'adjustment_type', name: 'adjustment_type' },
            { data: 'ref_no', name: 'ref_no' },
            { data: 'warehouse_name', name: 'w.name' },
			{ data: 'responsable', name: 'responsable' },
            { data: 'additional_notes', name: 'additional_notes' },
            { data: 'final_total', name: 'final_total' },
            { data: 'action', name: 'action' }
        ],
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('#stock_adjustment_table'));
        }
    });
    var detailRows = [];

    $('#stock_adjustment_table tbody').on( 'click', '.view_stock_adjustment', function () {
        var tr = $(this).closest('tr');
        var row = stock_adjustment_table.row( tr );
        var idx = $.inArray( tr.attr('id'), detailRows );
 
        if ( row.child.isShown() ) {
            $(this).find('i').removeClass( 'fa-eye' ).addClass('fa-eye-slash');
            row.child.hide();
 
            // Remove from the 'open' array
            detailRows.splice( idx, 1 );
        }
        else {
            $(this).find('i').removeClass( 'fa-eye-slash' ).addClass('fa-eye');

            row.child( get_stock_adjustment_details( row.data() ) ).show();
 
            // Add to the 'open' array
            if ( idx === -1 ) {
                detailRows.push( tr.attr('id') );
            }
        }
    } );

    // On each draw, loop over the `detailRows` array and show any child rows
    stock_adjustment_table.on( 'draw', function () {
        $.each( detailRows, function ( i, id ) {
            $('#'+id+' .view_stock_adjustment').trigger( 'click' );
        } );
    } );

    $(document).on('click', '.delete_stock_adjustment', function(){
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
						if(result.success){
							toastr.success(result.msg);
							stock_adjustment_table.ajax.reload();
						} else {
							toastr.error(result.msg);
						}
					}
				});
            }
        });
    })
});

function setLocation(warehouse_id){
	if(isNaN(warehouse_id)){
		return null;
	}

	$.ajax({
		type: "get",
		url: "/warehouses/get-location/" + warehouse_id,
		dataType: "text",
		success: function(data){
			$("input#location_id").val(data);
		}
	});
}

function stock_adjustment_product_row(variation_id){
	var row_index = parseInt($('#product_row_index').val());
	
	var adjustment_type = $("select#adjustment_type").val();
	var location_id = 0;
	var warehouse_id = 0;
	var check_qty_available = 0;
	
	if(adjustment_type == "abnormal"){
		check_qty_available = 1;
		var location_id = $('select#location_id').val();
		var warehouse_id = $('select#warehouse_id').val();
	}

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
			url: "/stock-adjustments/get_product_row",
			data: {
				row_index: row_index,
				variation_id: variation_id,
				location_id: location_id,
				warehouse_id: warehouse_id,
				check_qty_available: check_qty_available
			},
			dataType: "html",
			success: function(result){
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

function update_table_total(){
	var table_total = 0;
	var table_total_show = 0;
	$('table#stock_adjustment_product_table tbody tr').each( function(){
		var this_total = parseFloat(__read_number($(this).find('input.product_line_total_hidden')));
		var this_total_show = parseFloat(__read_number($(this).find('input.product_line_total')));
		if(this_total){
			table_total += this_total;
		}
		if(this_total_show){
			table_total_show += this_total_show;
		}
	});
	__write_number($('input#total_amount'), table_total, false, price_precision);
	$('span#total_adjustment').text(__number_f(table_total_show, false, false, inventory_precision));
}

function update_table_row( tr ){
	var quantity = parseFloat( __read_number(tr.find('input.product_quantity')));
	var unit_price = parseFloat( __read_number(tr.find('input.product_unit_price_hidden')));
	var unit_price_show = parseFloat( __read_number(tr.find('input.product_unit_price')));
	var row_total = 0;
	if( quantity && unit_price){
		row_total = quantity * unit_price;
	}
	var row_total_show = 0;
	if( quantity && unit_price_show){
		row_total_show = quantity * unit_price_show;
	}
	__write_number(tr.find('input.product_line_total'), row_total_show, false, inventory_precision);
	__write_number(tr.find('input.product_line_total_hidden'), row_total, false, price_precision);
	update_table_total();
}

function get_stock_adjustment_details(rowData){
	var div = $('<div/>')
        .addClass( 'loading' )
        .text( 'Loading...' );
    $.ajax( {
        url: '/stock-adjustments/' + rowData.DT_RowId,
        dataType: 'html',
        success: function ( data ) {
            div
                .html( data )
                .removeClass( 'loading' );

			__currency_convert_recursively($('.details-table'));
        }
    } );
 
    return div;
}

$(document).on('click', 'button#submit_stock_adjustment_form', function(e) {
	e.preventDefault();

	$('button#submit_stock_adjustment_form').attr('disabled', 'disabled');

	var data = $('form#stock_adjustment_form').serialize();
	var url = $('form#stock_adjustment_form').attr('action');

	var is_valid = is_valid_form();
	
	if (is_valid != true) {
		$('button#submit_stock_adjustment_form').removeAttr('disabled');
		return;
	}

	$.ajax({
		method: "post",
		url: url,
		data: data,
		dataType: "json",
		success: function(result) {
			if (result.success == 1) {
				toastr.success(result.msg);
				redirect_index();
				stock_adjustment_print(result.receipt);

			} else {
				toastr.error(result.msg);
				$('button#submit_stock_adjustment_form').removeAttr('disabled');
			}
		}
	});
});

function stock_adjustment_print(receipt) {
	$('#receipt_section').html(receipt.html_content);
	__currency_convert_recursively($('#receipt_section'));
	setTimeout(function() { window.print(); }, 1000);
}

function redirect_index() {
	var redirect_url = $('#redirect_url').val();
	setTimeout(function() { window.location = redirect_url; }, 4000);
	return true;
}

function is_valid_form(){
	flag = true;

	$('span.error').remove();

	console.log($('#adjustment_type').val());

	if ($('#adjustment_type').val() == null || $('#adjustment_type').val() == '') {
		flag = false;
		error = '<span class="error"><strong>' + LANG.required_field + '</strong></span>';
		$(error).insertAfter($('select#adjustment_type').parent('div'));
	}

	if ($('#warehouse_id').val() == null || $('#warehouse_id').val() == '') {
		flag = false;
		error = '<span class="error"><strong>' + LANG.required_field + '</strong></span>';
		$(error).insertAfter($('select#warehouse_id').parent('div'));
	}

	if ($('#transaction_date').val() == null || $('#transaction_date').val() == '') {
		flag = false;
		error = '<span class="error"><strong>' + LANG.required_field + '</strong></span>';
		$(error).insertAfter($('input#transaction_date').parent('div'));
	}

	if ($('#additional_notes').val() == null || $('#additional_notes').val() == '') {
		flag = false;
		error = '<span class="error"><strong>' + LANG.required_field + '</strong></span>';
		$(error).insertAfter($('textarea#additional_notes').parent('div'));
	}

	return flag;
}
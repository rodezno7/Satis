//This file contains all functions used products tab

// Number of decimal places to store and use in calculations
price_precision = $('#price_precision').length > 0 ? $('#price_precision').val() : 6;

$(document).ready(function(){
	$(document).on('ifChecked', 'input#enable_stock', function(){
		$('div#alert_quantity_div').show();
		$('div#quick_product_opening_stock_div').show();

			//Enable expiry selection
			if($('#expiry_period_type').length){
				$('#expiry_period_type').removeAttr("disabled");
			}

			if($('#opening_stock_button').length){
				$('#opening_stock_button').removeAttr("disabled");
			}
		}); 
	$(document).on('ifUnchecked', 'input#enable_stock', function(){
		$('div#alert_quantity_div').hide();
		$('div#quick_product_opening_stock_div').hide();
		$('input#alert_quantity').val(0);

			//Disable expiry selection
			if($('#expiry_period_type').length){
				$('#expiry_period_type').val('').change();
				$('#expiry_period_type').attr("disabled", true);
			}
			if($('#opening_stock_button').length){
				$('#opening_stock_button').attr("disabled", true);
			}
		});

	//Start For product type single

	//If tax rate is changed
	$(document).on( 'change', 'select#tax', function(){
		get_tax_percent();
	});

	// If there is default product tax
	if ($('select#tax').length > 0) {
		if ($('select#tax').val() > 0) {
			get_tax_percent();
		}
	}

	//If purchase price exc tax is changed
	$(document).on( 'change', 'input#single_dpp', function(e){
		var purchase_exc_tax = __read_number($('input#single_dpp'));
		purchase_exc_tax = (purchase_exc_tax == undefined) ? 0 : purchase_exc_tax;

		//var tax_rate = $('select#tax').find(':selected').data('rate');
		var tax_rate = $('input#tax_percent').val();
		tax_rate = (tax_rate == undefined) ? 0 : tax_rate;

		var purchase_inc_tax = __add_percent(purchase_exc_tax, tax_rate);
		__write_number($('input#single_dpp_inc_tax'), purchase_inc_tax, false, price_precision);

		var profit_percent = __read_number($('#profit_percent'));
		var selling_price = purchase_exc_tax / (1 - (profit_percent / 100)); //__add_percent(purchase_exc_tax, profit_percent);
		__write_number($('input#single_dsp'), selling_price, false, price_precision);

		var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
		__write_number($('input#single_dsp_inc_tax'), selling_price_inc_tax, false, price_precision);
	});

	//If purchase price inc tax is changed
	$(document).on( 'change', 'input#single_dpp_inc_tax', function(e){
		var purchase_inc_tax = __read_number($('input#single_dpp_inc_tax'));
		purchase_inc_tax = (purchase_inc_tax == undefined) ? 0 : purchase_inc_tax;

		//var tax_rate = $('select#tax').find(':selected').data('rate');
		var tax_rate = $('input#tax_percent').val();
		tax_rate = (tax_rate == undefined) ? 0 : tax_rate;

		var purchase_exc_tax = __get_principle(purchase_inc_tax, tax_rate);
		__write_number($('input#single_dpp'), purchase_exc_tax, false, price_precision);
		$('input#single_dpp').change();

		var profit_percent = __read_number($('#profit_percent'));
		profit_percent = profit_percent == undefined ? 0 : profit_percent;
		var selling_price = __read_number($('input#single_dsp')); //__add_percent(purchase_exc_tax, profit_percent);
		selling_price = ((selling_price - purchase_exc_tax) / selling_price) * 100;
		__write_number($('input#single_dsp'), selling_price, false, price_precision);

		var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
		__write_number($('input#single_dsp_inc_tax'), selling_price_inc_tax, false, price_precision);
	});

	$(document).on( 'change', 'input#profit_percent', function(e){
		//var tax_rate = $('select#tax').find(':selected').data('rate');
		var tax_rate = $('input#tax_percent').val();
		tax_rate = (tax_rate == undefined) ? 0 : tax_rate;

		var purchase_inc_tax = __read_number($('input#single_dpp_inc_tax'));
		purchase_inc_tax = (purchase_inc_tax == undefined) ? 0 : purchase_inc_tax;

		var purchase_exc_tax = __read_number($('input#single_dpp'));
		purchase_exc_tax = (purchase_exc_tax == undefined) ? 0 : purchase_exc_tax;

		var profit_percent = __read_number($('input#profit_percent'));
		// var selling_price = purchase_exc_tax / (1 - (profit_percent / 100)); //__add_percent(purchase_exc_tax, profit_percent);
		var selling_price = (purchase_exc_tax + (purchase_exc_tax * (profit_percent / 100)));
		__write_number($('input#single_dsp'), selling_price, false, price_precision);

		var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
		__write_number($('input#single_dsp_inc_tax'), selling_price_inc_tax, false, price_precision);
	});

	$(document).on( 'change', 'input#single_dsp', function(e){
		//var tax_rate = $('select#tax').find(':selected').data('rate');
		var tax_rate = $('input#tax_percent').val();
		tax_rate = (tax_rate == undefined) ? 0 : tax_rate;

		var selling_price = __read_number($('input#single_dsp'));
		var purchase_exc_tax = __read_number($('input#single_dpp'));

		// var profit_percent = ((selling_price - purchase_exc_tax) / selling_price) * 100; // __get_rate(purchase_exc_tax, selling_price);
		var profit_percent = ((selling_price - purchase_exc_tax) / purchase_exc_tax) * 100;
		__write_number($('input#profit_percent'), profit_percent, false, price_precision);

		var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
		__write_number($('input#single_dsp_inc_tax'), selling_price_inc_tax, false, price_precision);
	});

	$(document).on( 'change', 'input#single_dsp_inc_tax', function(e){
		//var tax_rate = $('select#tax').find(':selected').data('rate');
		var tax_rate = $('input#tax_percent').val();
		tax_rate = (tax_rate == undefined) ? 0 : tax_rate;

		var selling_price_inc_tax = __read_number($('input#single_dsp_inc_tax'));

		var selling_price = __get_principle( selling_price_inc_tax, tax_rate );
		__write_number($('input#single_dsp'), selling_price, false, price_precision);

		var purchase_exc_tax = __read_number($('input#single_dpp'));
		var profit_percent = ((selling_price - purchase_exc_tax) / selling_price) * 100; //__get_rate(purchase_exc_tax, selling_price);
		__write_number($('input#profit_percent'), profit_percent, false, price_precision);
	});

	/** Product accounts by location */
	$(document).on('click', 'a.accounting_account', function(e) {
		e.preventDefault();
		var url = $(this).attr('href');

		$("div#product_accounts_modal").load(url, function() {
			var modal = $(this);
			modal.modal('show');

			var creditor_account_code = modal.find("input#creditor_account_code").val();
			creditor_account_code ? creditor_account_code : null;

			modal.find('button.btn-panel-tool').on('click', function() {
				let icon = $(this).find('i');

				if(icon.hasClass('fa-minus')) {
					icon.removeClass('fa-minus');
					icon.addClass('fa-plus');

				} else if (icon.hasClass('fa-plus')) {
					icon.removeClass('fa-plus');
					icon.addClass('fa-minus');
				}
			});

			modal.find('select.location_account').select2({
                dropdownParent: modal, // fix select2 bug
				ajax: {
					type: "post",
					url: "/catalogue/get_accounts_for_select2",
					dataType: "json",
					data: function(params){
						return {
							q: params.term,
							main_account: creditor_account_code
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

			modal.find('form').on('submit', function(e) {
				e.preventDefault();
				var form = $(this);
				form.find('button[type="submit"]').attr('disabled');

				$.ajax({
                    method: "POST",
                    url: form.attr("action"),
                    dataType: "json",
                    data: $(this).serialize(),
                    success: function(res){
                        if (res.success == true){
                            modal.modal("hide");
							toastr.success(res.msg);

                        } else {
							toastr.error(res.msg);
                        }
                        form.find('button[type="submit"]').removeAttr('disabled');
                    }
                })
			});
		});
	});

	$(document).on( 'click', '.submit_product_form', function(e){
		e.preventDefault();
		var submit_type = $(this).attr('value');
		$('#submit_type').val(submit_type);
		$("form#product_add_form").validate({
			rules: {
				sku: {
					remote: {
						url: "/products/check_product_sku",
						type: "post",
						data: {
							sku: function() {
								return $( "#sku" ).val();
							},
							product_id: function() {
								if($('#product_id').length > 0 ){
									return $('#product_id').val();
								} else {
									return '';
								}
							}
						}
					}
				},
				expiry_period:{
					required: {
						depends: function(element) {
							return ($('#expiry_period_type').val().trim() != '');
						}
					}
				}
			},
			messages: {
				sku: {
					remote: LANG.sku_already_exists
				}
			}
		});
		if($("form#product_add_form").valid()) {
			$("form#product_add_form").submit();
		}
	});
    //End for product type single
	$(document).on( 'click', 'button.submit_product_edit_form', function(e){
		e.preventDefault();
		var submit_type = $(this).attr('value');
		$('#submit_type').val(submit_type);
		$("form#product_edit_form").validate({
			rules: {
				expiry_period:{
					required: {
						depends: function(element) {
							return ($('#expiry_period_type').val().trim() != '');
						}
					}
				}
			}
		});
		if($("form#product_edit_form").valid()) {
			$("form#product_edit_form").submit();
		}
	});
    //End for product type single

    //Start for product type Variable
    //If purchase price exc tax is changed
    $(document).on( 'change', 'input.variable_dpp', function(e){

    	var tr_obj = $(this).closest('tr');

    	var purchase_exc_tax = __read_number($(this));
    	purchase_exc_tax = (purchase_exc_tax == undefined) ? 0 : purchase_exc_tax;

		//var tax_rate = $('select#tax').find(':selected').data('rate');
		var tax_rate = $('input#tax_percent').val();
		tax_rate = (tax_rate == undefined) ? 0 : tax_rate;

    	var purchase_inc_tax = __add_percent(purchase_exc_tax, tax_rate);
    	__write_number(tr_obj.find('input.variable_dpp_inc_tax'), purchase_inc_tax, false, price_precision);

    	var profit_percent = __read_number(tr_obj.find('input.variable_profit_percent'));
    	var selling_price = __add_percent(purchase_exc_tax, profit_percent);
    	__write_number(tr_obj.find('input.variable_dsp'), selling_price, false, price_precision);

    	var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
    	__write_number(tr_obj.find('input.variable_dsp_inc_tax'), selling_price_inc_tax, false, price_precision);
    });

	//If purchase price inc tax is changed
	$(document).on( 'change', 'input.variable_dpp_inc_tax', function(e){

		var tr_obj = $(this).closest('tr');

		var purchase_inc_tax = __read_number($(this));
		purchase_inc_tax = (purchase_inc_tax == undefined) ? 0 : purchase_inc_tax;

		//var tax_rate = $('select#tax').find(':selected').data('rate');
		var tax_rate = $('input#tax_percent').val();
		tax_rate = (tax_rate == undefined) ? 0 : tax_rate;

		var purchase_exc_tax = __get_principle(purchase_inc_tax, tax_rate);
		__write_number(tr_obj.find('input.variable_dpp'), purchase_exc_tax, false, price_precision);

		var profit_percent = __read_number(tr_obj.find('input.variable_profit_percent'));
		var selling_price = __add_percent(purchase_exc_tax, profit_percent);
		__write_number(tr_obj.find('input.variable_dsp'), selling_price, false, price_precision);

		var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
		__write_number(tr_obj.find('input.variable_dsp_inc_tax'), selling_price_inc_tax, false, price_precision);
	});

	$(document).on( 'change', 'input.variable_profit_percent', function(e){
		//var tax_rate = $('select#tax').find(':selected').data('rate');
		var tax_rate = $('input#tax_percent').val();
		tax_rate = (tax_rate == undefined) ? 0 : tax_rate;

		var tr_obj = $(this).closest('tr');
		var profit_percent = __read_number($(this));

		var purchase_exc_tax = __read_number(tr_obj.find('input.variable_dpp'));
		purchase_exc_tax = (purchase_exc_tax == undefined) ? 0 : purchase_exc_tax;
		
		var selling_price = __add_percent(purchase_exc_tax, profit_percent);
		__write_number(tr_obj.find('input.variable_dsp'), selling_price, false, price_precision);

		var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
		__write_number(tr_obj.find('input.variable_dsp_inc_tax'), selling_price_inc_tax, false, price_precision);
	});

	$(document).on( 'change', 'input.variable_dsp', function(e){
		//var tax_rate = $('select#tax').find(':selected').data('rate');
		var tax_rate = $('input#tax_percent').val();
		tax_rate = (tax_rate == undefined) ? 0 : tax_rate;

		var tr_obj = $(this).closest('tr');
		var selling_price = __read_number($(this));
		var purchase_exc_tax = __read_number(tr_obj.find('input.variable_dpp'));

		var profit_percent = __get_rate(purchase_exc_tax, selling_price);
		__write_number(tr_obj.find('input.variable_profit_percent'), profit_percent, false, price_precision);

		var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
		__write_number(tr_obj.find('input.variable_dsp_inc_tax'), selling_price_inc_tax, false, price_precision);
	});
	$(document).on( 'change', 'input.variable_dsp_inc_tax', function(e){
		var tr_obj = $(this).closest('tr');
		var selling_price_inc_tax = __read_number($(this));

		//var tax_rate = $('select#tax').find(':selected').data('rate');
		var tax_rate = $('input#tax_percent').val();
		tax_rate = (tax_rate == undefined) ? 0 : tax_rate;

		var selling_price = __get_principle( selling_price_inc_tax, tax_rate);
		__write_number(tr_obj.find('input.variable_dsp'), selling_price, false, price_precision);

		var purchase_exc_tax = __read_number(tr_obj.find('input.variable_dpp'));
		var profit_percent = __get_rate(purchase_exc_tax, selling_price);
		__write_number(tr_obj.find('input.variable_profit_percent'), profit_percent, false, price_precision);
	});

	$(document).on( 'click', '.add_variation_value_row', function(){
		var variation_row_index = $(this).closest('.variation_row').find('.row_index').val();
		var variation_value_row_index = $(this).closest('table').find('tr:last .variation_row_index').val();

		if($(this).closest('.variation_row').find('.row_edit').length >=1){
			var row_type = 'edit';
		}else{
			var row_type = 'add';
		}

		var table = $(this).closest('table');

		$.ajax({
			method: "GET",
			url: '/products/get_variation_value_row',
			data: { 'variation_row_index': variation_row_index, 'value_index': variation_value_row_index, 'row_type': row_type},
			dataType: "html",
			success: function(result){
				if(result){
					table.append(result);
					toggle_dsp_input();
				}
			}
		});
	});

	$(document).on( 'change', '.variation_template', function(){
		tr_obj = $(this).closest('tr');

		if( $(this).val() !== ""){

			tr_obj.find('input.variation_name').val($(this).find("option:selected" ).text());

			var template_id = $(this).val();
			var row_index = $(this).closest('tr').find('.row_index').val();
			$.ajax({
				method: "POST",
				url: '/products/get_variation_template',
				dataType: "html",
				data: { 'template_id': template_id, 'row_index': row_index},
				success: function(result){
					if(result){
						tr_obj.find('table.variation_value_table').find('tbody').html(result);
						toggle_dsp_input();
					}
				}
			});
		}
	});

	$(document).on( 'click', '.remove_variation_value_row', function(){
		swal({
			title: LANG.sure,
			icon: "warning",
			buttons: true,
			dangerMode: true,
		}).then((willDelete) => {
			if (willDelete) {
				var count = $(this).closest('table').find('.remove_variation_value_row').length;
				if( count === 1){
					$(this).closest('.variation_row').remove();
				} else {
					$(this).closest('tr').remove();
				}
			}
		});
	});

	//End for product type Variable
	$(document).on( 'change', '#tax_type', function(e){
		toggle_dsp_input();
	});
	toggle_dsp_input();

	$(document).on( 'change', '#expiry_period_type', function(e){
		if($(this).val()){
			$('input#expiry_period').prop('disabled', false)
		} else {
			$('input#expiry_period').val('');
			$('input#expiry_period').prop('disabled', true)
		}
	});

	$(document).on('click', 'a.view-product', function(e){
		e.preventDefault();
		$.ajax({
			url: $(this).attr("href"),
			dataType: "html",
			success: function(result){
				$('#view_product_modal').html(result).modal('show');
				__currency_convert_recursively($('#view_product_modal'));
			}
		});

	});
	
	$(document).on('click', 'a.view-supplier', function(e){
		e.preventDefault();
		$.ajax({
			url: $(this).attr("href"),
			dataType: "html",
			success: function(result){
				$('#modalSupplier').html(result).modal('show');
			}
		});

	});

	$(document).on('click', 'a.view-kit', function(e){
		e.preventDefault();
		$.ajax({
			url: $(this).attr("href"),
			dataType: "html",
			success: function(result){
				$('#modalKit').html(result).modal('show');
			}
		});

	});

	var img_fileinput_setting = {
		'showUpload': false,
		'showPreview': true,
		'browseLabel': LANG.file_browse_label,
		'removeLabel': LANG.remove,
		'previewSettings': {
			image: {
				width: "auto",
				height: "auto",
				'max-width': "100%",
				'max-height': "100%"
			}
		}
	};

	$("#upload_image").fileinput(img_fileinput_setting);

	if($('textarea#product_description').length > 0) {
		CKEDITOR.config.height = 120;
		CKEDITOR.replace('product_description');
	}

	// On click of btn-plus-category button
	$('#btn-plus-category').on('click', function () {
		$('#flag-category').val('category');
	});

	// On click of btn-plus-category button
	$('#btn-plus-sub-category').on('click', function () {
		$('#flag-category').val('sub-category');
	});

	// On show of view_modal modal
	$('.view_modal').on('shown.bs.modal', function () {
		$(this).find('.select2').select2();

		if ($('form#quick_add_category_form').length > 0) {
			if ($('#flag-category').val() == 'category') {
				if ($('#div_add_as_sub_cat').length > 0) {
					$('#div_add_as_sub_cat').hide();
				}

			} else {
				if ($('#div_add_as_sub_cat').length > 0) {
					$('#add_as_sub_cat').prop('checked', true);
					$('#div_add_as_sub_cat').hide();
					$('#parent_cat_div').removeClass('hide');
					$('#parent_id').prop('required', true);
				}
			}
		}
	});

    // On click of btn-recalculate button
    $(document).on('click', 'button#btn-recalculate', function () {
        let icon = $(this).find('i');
        let variation_id = $(this).data('variation-id');
        let tr = $(this).closest('tr');

        Swal.fire({
            title: LANG.sure,
            text: LANG.recalculate_cost_text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelmButtonText: LANG.cancel,
            confirmButtonText: LANG.yes
        }).then((result) => {
            if (result.isConfirmed) {
                icon.removeClass('fa-refresh').addClass('fa-spinner fa-pulse');

                $.ajax({
                    type: 'get',
                    url: '/products/recalculate-product-cost/' + variation_id,
                    dataType: 'json',
                    success: function (res) {
                        icon.removeClass('fa-spinner fa-pulse').addClass('fa-refresh');

                        if (res.success === 1) {
                            tr.find('span.default-purchase-price').text(__currency_trans_from_en(res.default_purchase_price, true, false, price_precision));
                            tr.find('span.dpp-inc-tax').text(__currency_trans_from_en(res.dpp_inc_tax, true, false, price_precision));
                            tr.find('span.profit-percent').text(__currency_trans_from_en(res.profit_percent, false, false, price_precision));

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
                })
            }
        })
    });

	// On change of sku input
	$(document).on('change', '#sku', function () {
        let sku = $('#sku').val();
        let product_id = $('#product_id').length > 0 ? $('#product_id').val() : 0;
		let route = $('#check-sku-url').length > 0 ? $('#check-sku-url').val() : 0;

		if (route != 0 && sku != '') {
			$.ajax({
				method: 'post',
				url: route,
				dataType: 'json',
				data: {
					sku: sku,
					product_id: product_id
				},
				success: function (result) {
					if (result.success == 0) {
						$('button.submit_product_form').prop('disabled', true);

						Swal.fire({
							title: result.msg,
							icon: 'error',
						});

					} else {
						$('button.submit_product_form').prop('disabled', false);
					}
				}
			});

		} else {
			$('button.submit_product_form').prop('disabled', false);
		}
    });
});

function toggle_dsp_input(){
	var tax_type = $('#tax_type').val();
	if( tax_type == 'inclusive'){
		$('.dsp_label').each( function(){
			$(this).text(LANG.inc_tax);
		});
		$('#single_dsp').addClass('hide');
		$('#single_dsp_inc_tax').removeClass('hide');

		$('.add-product-price-table').find('.variable_dsp_inc_tax').each( function(){
			$(this).removeClass('hide');
		});
		$('.add-product-price-table').find('.variable_dsp').each( function(){
			$(this).addClass('hide');
		});
	} else if( tax_type == 'exclusive' ){
		$('.dsp_label').each( function(){
			$(this).text(LANG.exc_tax);
		});
		$('#single_dsp').removeClass('hide');
		$('#single_dsp_inc_tax').addClass('hide');

		$('.add-product-price-table').find('.variable_dsp_inc_tax').each( function(){
			$(this).addClass('hide');
		});
		$('.add-product-price-table').find('.variable_dsp').each( function(){
			$(this).removeClass('hide');
		});
	}
}

/** get tax group percent */
function get_tax_percent(){
	var tax_group_id = $('select#tax').val();
	var tax_percent = $('#tax_percent');

	$.ajax({
		type: 'POST',
		url: '/tax_groups/get_tax_percent',
		dataType: 'text',
		data: { 'tax_group_id' : tax_group_id },
		success: function(data){
			__write_number(tax_percent, data * 100, false, price_precision);

			if($('select#type').val() == 'single'){
				var purchase_exc_tax = __read_number($('input#single_dpp'));
				purchase_exc_tax = (purchase_exc_tax == undefined) ? 0 : purchase_exc_tax;
	
				//var tax_rate = $('select#tax').find(':selected').data('rate');
				var tax_rate = $('input#tax_percent').val();
				tax_rate = (tax_rate == undefined) ? 0 : tax_rate;
	
				var purchase_inc_tax = __add_percent(purchase_exc_tax, tax_rate);
				__write_number($('input#single_dpp_inc_tax'), purchase_inc_tax, false, price_precision);
	
				var selling_price = __read_number($('input#single_dsp'));
				var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
				__write_number($('input#single_dsp_inc_tax'), selling_price_inc_tax, false, price_precision);

			} else if($('select#type').val() == 'variable'){

				//var tax_rate = $('select#tax').find(':selected').data('rate');
				var tax_rate = $('input#tax_percent').val();
				tax_rate = (tax_rate == undefined) ? 0 : tax_rate;
	
				$('table.variation_value_table > tbody').each(function(){
					$(this).find('tr').each(function(){
	
						var purchase_exc_tax = __read_number($(this).find('input.variable_dpp'));
						purchase_exc_tax = (purchase_exc_tax == undefined) ? 0 : purchase_exc_tax;
	
						var purchase_inc_tax = __add_percent(purchase_exc_tax, tax_rate);
						__write_number($(this).find('input.variable_dpp_inc_tax'), purchase_inc_tax, false, price_precision);
	
						var selling_price = __read_number($(this).find('input.variable_dsp'));
						var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
						__write_number($(this).find('input.variable_dsp_inc_tax'), selling_price_inc_tax, false, price_precision);
					});
				});
			}
		}
	});
}

function get_product_details(rowData){
	var div = $('<div/>')
	.addClass( 'loading' )
	.text( 'Loading...' );

	$.ajax( {
		url: '/products/' + rowData.id,
		dataType: 'html',
		success: function ( data ) {
			div
			.html( data )
			.removeClass( 'loading' );
		}
	} );

	return div;
}

//Quick add unit
$(document).on('submit', 'form#quick_add_unit_form', function(e){
	e.preventDefault();
	$(this).find('button[type="submit"]').attr('disabled', true);
	var data = $(this).serialize();

	$.ajax({
		method: "POST",
		url: $(this).attr("action"),
		dataType: "json",
		data: data,
		success: function(result){
			if(result.success == true){
				var newOption = new Option(result.data.actual_name, result.data.id, true, true);
				// Append it to the select
				$('#unit_id').append(newOption).trigger('change');
				$('div.view_modal').modal('hide');
				Swal.fire({
					title: ""+result.msg+"",
					icon: "success",
				});
			} else {
				Swal.fire
				({
					title: ""+result.msg+"",
					icon: "error",
				});
			}
		}
	});
});

//Quick add brand
$(document).on('submit', 'form#quick_add_brand_form', function(e){
	e.preventDefault();
	$(this).find('button[type="submit"]').attr('disabled', true);
	var data = $(this).serialize();

	$.ajax({
		method: "POST",
		url: $(this).attr("action"),
		dataType: "json",
		data: data,
		success: function(result){
			if(result.success == true){
				var newOption = new Option(result.data.name, result.data.id, true, true);
				// Append it to the select
				$('#brand_id').append(newOption).trigger('change');
				$('div.view_modal').modal('hide');
				Swal.fire({
					title: ""+result.msg+"",
					icon: "success",
				});
			} else {
				Swal.fire
				({
					title: ""+result.msg+"",
					icon: "error",
				});
			}
		}
	});
});

//agregando un nuevo elemento
//If purchase price exc tax is changed
$(document).on( 'change', 'input#single_dpp', function(e){
	var purchase_exc_tax = __read_number($('input#single_dpp'));
	purchase_exc_tax = (purchase_exc_tax == undefined) ? 0 : purchase_exc_tax;

	//var tax_rate = $('select#tax').find(':selected').data('rate');
	var tax_rate = $('input#tax_percent').val();
	tax_rate = (tax_rate == undefined) ? 0 : tax_rate;

	var purchase_inc_tax = __add_percent(purchase_exc_tax, tax_rate);
	__write_number($('input#single_dpp_inc_tax'), purchase_inc_tax, false, price_precision);

	var profit_percent = __read_number($('#profit_percent'));
	var selling_price = __add_percent(purchase_exc_tax, profit_percent);
	__write_number($('input#single_dsp'), selling_price, false, price_precision);

	var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
	__write_number($('input#single_dsp_inc_tax'), selling_price_inc_tax, false, price_precision);
});

//get history purchase for unique product
$(document).on('click', 'a.view_history_purchase', function(e){
	e.preventDefault();
	$.ajax({
		url: $(this).attr("href"),
		dataType: "html",
		success: function(result){
			$('#modal_history_purchase').html(result).modal('show');
			__currency_convert_recursively($('#modal_history_purchase'));
		}
	});

});

// On submit of quick_add_category_form category
$(document).on('submit', 'form#quick_add_category_form', function (e) {
	e.preventDefault();

	$(this).find('button[type="submit"]').attr('disabled', true);

	var data = $(this).serialize();

	$.ajax({
		method: 'post',
		url: $(this).attr('action'),
		dataType: 'json',
		data: data,
		success: function (result) {
			if (result.success == true) {
				var newOption = new Option(result.data.name, result.data.id, true, true);
				
				// Append it to the select
				if (result.data.parent_id) {
					$('#sub_category_id').append(newOption).trigger('change');
				} else {
					$('#category_id').append(newOption).trigger('change');
				}

				$('div.view_modal').modal('hide');

				Swal.fire({
					title: result.msg,
					icon: 'success',
				});

			} else {
				Swal.fire({
					title: result.msg,
					icon: 'error',
				});
			}
		}
	});
});
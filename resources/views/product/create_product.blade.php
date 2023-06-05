@extends('layouts.app')
@section('title', __('product.add_product'))
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>@lang('product.add_product')</h1>
</section>

<!-- Main content -->
<section class="content">
	@include('product.create')
</section>
<!-- /.content -->

@endsection
@section('javascript')
@php $asset_v = env('APP_VERSION'); @endphp
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
	var cont = 0;
	var supplier_ids = [];

	$(document).on('change', '#has_warranty', function ()
    {
        if ($("#has_warranty").is(":checked")) {
            $('#has_warranty').val('1');
            $('#hasW').show();
        } else {
            $('#hasW').hide();
            $("#warranty").val('');
            $('#has_warranty').val('0');
        }
    });

	function addSupplier()
	{
		var route = "/contacts/showSupplier/"+id;
		$.get(route, function(res){
			supplier_id = res.id;
			name = res.name;
			count = parseInt(jQuery.inArray(supplier_id, supplier_ids));
			if (count >= 0)
			{
				Swal.fire
				({
					title: "{{__('contact.supplier_already_added')}}",
					icon: "error",
				});
			}
			else
			{
				supplier_ids.push(supplier_id);
				var fila = '<tr class="selected" id="fila'+cont+'" style="height: 10px"><td><button id="bitem'+cont+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteSupplier('+cont+', '+supplier_id+');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="supplier_ids[]" value="'+supplier_id+'">'+res.supplier_business_name+'</td><td>'+name+'</td><td>'+res.mobile+'</td><td><input type="text" id="catalogue'+cont+'" name="catalogue['+cont+']" class="form-control inpuy-sm" required></td><td><input type="number" name="uxc['+cont+']" class="form-control input-sm" required></td><td><input type="number" name="weight_product['+cont+']" class="form-control input-sm" required></td><td><input type="number" name="dimensions['+cont+']" class="form-control input-sm" required></td><td><input type="text" name="custom_field['+cont+']" class="form-control input-sm" required></td></tr>';
				$("#lista").append(fila);
				$("#catalogue"+cont+"").focus();
				cont++;
			}
		});
	}

	$("#supplier_id").change(function(event){
		id = $("#supplier_id").val();
		if(id.length > 0)
		{
			addSupplier();
		}
	});

	function deleteSupplier(index, id){ 
		$("#fila" + index).remove();
		supplier_ids.removeItem(id);
	}

	Array.prototype.removeItem = function (a) {
		for (var i = 0; i < this.length; i++) {
			if (this[i] == a) {
				for (var i2 = i; i2 < this.length - 1; i2++) {
					this[i2] = this[i2 + 1];
				}
				this.length = this.length - 1;
				return;
			}
		}
	};

	$("#clasification").change(function(event){
		type = $("#clasification").val();
		if(type == "service")
		{
			$("#type").val("single").change();
			$('#alert_quantity').val(0);
			$("#unit_id").prop('required', false);
			$('#divExcludeService').hide();
			$('#divExcludeService2').hide();
			//$('#divExcludeService3').hide();
			$('#divExcludeService4').hide();
			$("#opening_stock_button").hide();
			$("#submit_n_add_selling_prices").hide();
			$("#imei").prop('checked', false);
			$("#enable_stock").prop('checked', false);
			$("#div_type").hide();
			$("#div_imei").hide();
			var product_type = 'single';
			var action = $('#type').attr('data-action');
			var product_id = $('#type').attr('data-product_id');
			$.ajax({
				method: "POST",
				url: '/products/product_form_part',
				dataType: "html",
				data: { 'type': product_type, 'product_id': product_id, 'action': action },
				success: function(result) {
					if (result) {
						$('#product_form_part').html(result);
						toggle_dsp_input();
					}
				}
			});
		}
		if(type == "kits"){
			$("#type").val("single").change();
			$('#alert_quantity').val(0);
			$("#unit_id").prop('required', false);
			$('#divExcludeService').show();

			$("#div_brand").hide();
			$("#div_unit").hide();

			$('#divExcludeService2').hide();
			//$('#divExcludeService3').hide();
			$('#divExcludeService4').show();
			$("#opening_stock_button").hide();
			$("#submit_n_add_selling_prices").hide();
			$("#imei").prop('checked', false);
			$("#enable_stock").prop('checked', false);
			$("#div_type").hide();
			$("#div_imei").hide();
			var product_type = 'single';
			var action = $('#type').attr('data-action');
			var product_id = $('#type').attr('data-product_id');
			$.ajax({
				method: "POST",
				url: '/products/product_form_part',
				dataType: "html",
				data: { 'type': product_type, 'product_id': product_id, 'action': action },
				success: function(result) {
					if (result) {
						$('#product_form_part').html(result);
						toggle_dsp_input();
					}
				}
			});
		}

		if(type == 'product'){
			$("#div_brand").show();
			$("#div_unit").show();
			$("#div_imei").show();
			$('#alert_quantity').val('');
			$("#unit_id").prop('required', true);
			$('#divExcludeService').show();
			$('#divExcludeService2').show();
			//$('#divExcludeService3').show();
			$('#divExcludeService4').hide();
			$("#opening_stock_button").show();
			$("#submit_n_add_selling_prices").show();
			$("#div_type").show();
			$("#enable_stock").prop('checked', true);
		}
	});

	// $(document).on('submit', 'form#product_add_form', function(e) {
	// 	$(this).find('button[type="submit"]').attr('disabled', true);
	// 	$("#btnUndo").attr('disabled', true);
	// });

	function cls(){
		$("#lista").empty();
		$("#listak").empty();
		cont = 0;
		supplier_ids = [];

		contk = 0;
		kit_idsk = [];

		$("#clasification").val("product").change();
		$("#type").val("single").change();
		$('#name').val('');
		$('#sku').val('');
		$('#alert_quantity').val('');
		$('#dai').val('');
		$('#weight').val('');
		$('#is_active').prop('checked', true);

		$("#div_imei").show();    
		$("#unit_id").prop('required', true);
		$('#divExcludeService').show();
		$('#divExcludeService2').show();
		//$('#divExcludeService3').show();
		$("#opening_stock_button").show();
		$("#div_type").show();

		var product_type = 'single';
		var action = $('#type').attr('data-action');
		var product_id = $('#type').attr('data-product_id');
		$.ajax({
			method: "POST",
			url: '/products/product_form_part',
			dataType: "html",
			data: { 'type': product_type, 'product_id': product_id, 'action': action },
			success: function(result) {
				if (result) {
					$('#product_form_part').html(result);
					toggle_dsp_input();
				}
			}
		});
	}

	var contk = 0;
	var kit_idsk = [];
	var valor=[];
	var total = 0;

	$(document).ready( function(){
		$('#kit_children').select2({
			dropdownParent: $('#product_add_form'),
            ajax: {
                url: "/products/get_only_products",
                dataType: "json",
                delay: 25,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                    };
                },
                processResults: function (data) {
                    return {
                        results: data,
                    };
                },
            },
            minimumInputLength: 2,
            escapeMarkup: function (m) {
                return m;
            },
            templateResult: function (data) {
                if (!data.id) {
                    return data.text;
                }
                let html = data.sku +" - "+ data.text;
                return html;
            },
        });
	});
	
	function addChildren()
	{
		var route = "/products/showProduct/"+id;
		$.get(route, function(res){
			variation_id = res.variation_id;
			product_id = res.product_id;
			if(res.sku == res.sub_sku){
				name = res.name_product;
			}
			else{
				name = ""+res.name_product+" "+res.name_variation+"";
			}
			if(res.brand != null){
				brand = res.brand;
			}
			else{
				brand = 'N/A';
			}
			if(res.default_purchase_price != null){
				price = res.default_purchase_price;
			}
			else{
				price = 'N/A';
			}
			if(res.sub_sku != null){
				sku = res.sub_sku;
			}
			else{
				sku = 'N/A';
			}
			count = $('input#product_id-'+variation_id);
			//count = parseInt(jQuery.inArray(variation_id, kit_idsk));
			if (count.length > 0)
			{
				Swal.fire
				({
					title: "{{__('product.product_already_added')}}",
					icon: "warning",
				});
			}
			else
			{
				if(res.clasification == 'service'){
					unit = 'N/A'
					kit_idsk.push(variation_id);
					valor.push(contk);
					var fila = '<tr class="selected" id="filak'+contk+'" style="height: 10px"><td><input type="hidden" name="product_ids[]" value="'+variation_id+'" id="product_id-'+variation_id+'">'+name+'</td><td>'+sku+'</td><td>'+brand+'</td><td><input type="hidden" name="clas_product[]" value="service">'+unit+'</td><td><input type="hidden" name=price[] id="price'+contk+'" value="'+price+'">'+price+'</td><td><input type="number" id="quantity'+contk+'" name="quantity[]" class="form-control form-control-sm" min="0.01" value="1" onchange="getTotalKit()" required></td><td><button id="bitem'+contk+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteChildren('+contk+', '+variation_id+');"><i class="fa fa-times"></i></button></td></tr>';
					$("#listak").append(fila);
					contk++;
					getTotalKit();
				}
				else{
					var route = "/products/getUnitPlan/"+id;
					$.get(route, function(res){
						if(res.plan == 'group'){
							var route = "/products/getUnitsFromGroup/"+res.unit_group_id;
							$.get(route, function(res){
								content = "";
								$(res).each(function(key,value){
									content = content + '<option value="'+value.id+'">'+value.actual_name+'</option>';
								});
								kit_idsk.push(variation_id);
								valor.push(contk);
								var fila = '<tr class="selected" id="filak'+contk+'" style="height: 10px"><td><input type="hidden" name="product_ids[]" value="'+variation_id+'" id="product_id-'+variation_id+'">'+name+'</td><td>'+sku+'</td><td>'+brand+'</td><td><input type="hidden" name="clas_product[]" value="product"><select name="kit_child[]" id="kit_child[]" class="form-control select2">'+content+'</select></td><td><input type="hidden" name=price[] id="price'+contk+'" value="'+price+'">'+price+'</td><td><input type="number" id="quantity'+contk+'" name="quantity[]" class="form-control form-control-sm" min="0.01" value="1" onchange="getTotalKit()" required></td><td><button id="bitem'+contk+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteChildren('+contk+', '+variation_id+');"><i class="fa fa-times"></i></button></td></tr>';
								$("#listak").append(fila);
								contk++;
								getTotalKit();
							});
						}
						else{
							unit = '<input type="hidden" value="'+res.unit_id+'" name="kit_child[]">'+res.name+'';

							kit_idsk.push(variation_id);
							valor.push(contk);
							var fila = '<tr class="selected" id="filak'+contk+'" style="height: 10px"><td><input type="hidden" name="product_ids[]" value="'+variation_id+'" id="product_id-'+variation_id+'">'+name+'</td><td>'+sku+'</td><td>'+brand+'</td><td><input type="hidden" name="clas_product[]" value="product">'+unit+'</td><td><input type="hidden" name=price[] id="price'+contk+'" value="'+price+'">'+price+'</td><td><input type="number" id="quantity'+contk+'" name="quantity[]" class="form-control form-control-sm" min="0.01" value="1" onchange="getTotalKit()" required></td><td><button id="bitem'+contk+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteChildren('+contk+', '+variation_id+');"><i class="fa fa-times"></i></button></td></tr>';
							$("#listak").append(fila);
							contk++;
							getTotalKit();
						}
					});
				}
			}
		});
	}

	function deleteChildren(index, id){
		Swal.fire({
			title: LANG.sure,
			text: LANG.cancel_product_msg,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: LANG.yes,
			cancelButtonText: LANG.not,
		}).then((resul) => {
			if (resul.isConfirmed) {
				$("#filak" + index).remove();
				kit_idsk.removeItem(id);
				if(kit_idsk.length == 0)
				{
					total=0;
					contk = 0;
					kit_idsk=[];
					valor=[];
				}
				getTotalKit();
				
			} else {
				return false;
			}
		}); 
	}

	// $(document).on( 'change', 'select#kit_children', function(event){
	// 	id = $("#kit_children").val();
	// 	if(id != 0){
	// 		addChildren();
	// 		$("#kit_children").val(0).change();
	// 	}
	// });
	$("#kit_children").change(function(event){
        id = $("#kit_children").val();
        if(id.length > 0)
        {
            addChildren();
        }
    });

	function getTotalKit()
	{
		// Number of decimal places to store and use in calculations
		let price_precision = $('#price_precision').length > 0 ? $('#price_precision').val() : 6;

		quantity = 0.00;
		price = 0.00;
		total = 0.00;
		$.each(valor, function(value){
			quantityg = $("#quantity"+value+"").val();
			priceg = $("#price"+value+"").val();
			if(quantityg)
			{
				if(isNaN(quantityg))
				{
					quantity = parseFloat(0.00);
				}
				else
				{
					quantity = parseFloat($("#quantity"+value+"").val());
				}
			}
			else
			{
				quantity = parseFloat(0.00);
			}
			if(priceg)
			{
				if(isNaN(priceg))
				{
					price = 0.00;
				}
				else
				{
					price = parseFloat($("#price"+value+"").val());
				}

			}
			else
			{
				price = 0.00;
			}
			subtotal = quantity * price;
			if(isNaN(subtotal))
			{
				subtotal = 0.00;
			}
			if(isInfinite(total))
			{
				subtotal = 0.00;
			}
			total = total + subtotal;
		});
		$("#single_dpp").val(total);
		var purchase_exc_tax = __read_number($('input#single_dpp'));
		purchase_exc_tax = (purchase_exc_tax == undefined) ? 0 : purchase_exc_tax;

		var tax_rate = $('select#tax').find(':selected').data('rate');
		tax_rate = (tax_rate == undefined) ? 0 : tax_rate;

		var purchase_inc_tax = __add_percent(purchase_exc_tax, tax_rate);
		__write_number($('input#single_dpp_inc_tax'), purchase_inc_tax, false, price_precision);

		var profit_percent = __read_number($('#profit_percent'));
		var selling_price = __add_percent(purchase_exc_tax, profit_percent);
		__write_number($('input#single_dsp'), selling_price, false, price_precision);

		var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
		__write_number($('input#single_dsp_inc_tax'), selling_price_inc_tax, false, price_precision);
	}

	function isInfinite(n)
	{
		return n === n/0;
	}

</script>
@endsection

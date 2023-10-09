$(function(){
	$.fn.modal.Constructor.prototype.enforceFocus = function() {};

	$('#carouselHacked').carousel();

	// Select2
	$('.choose_month_modal').on('shown.bs.modal', function() {
		$(this).find('.select2').select2();
	});

	var start = $('input[name="date-filter"]:checked').data('start');
	var end = $('input[name="date-filter"]:checked').data('end');
	var location_id = $('select#business_location_id').val() == 0 ? 1 : $('select#business_location_id').val();;
	update_statistics(start, end, location_id);
	$(document).on('change', 'input[name="date-filter"]', function(){
		var start = $('input[name="date-filter"]:checked').data('start');
		var end = $('input[name="date-filter"]:checked').data('end');
		var location_id = $('select#business_location_id').val() == 0 ? 1 : $('select#business_location_id').val();	
		update_statistics(start, end, location_id);
	});

	// Choose month form
	$(document).on('submit', 'form#choose_month_form', function(e) {
		e.preventDefault();

		$('input[name="date-filter"]:checked').parent('label').removeClass('active');
		$('input[name="date-filter"]:checked').attr('checked', false);
		var location_id = $("#business_location_id").val();

		$.ajax({
			method: 'post',
			url: $(this).attr('action'),
			dataType: 'json',
			data: $(this).serialize(),
			success: function(result) {
				if (result.success === true) {
					$('div#choose_month_modal').modal('hide');
					update_statistics(result.start, result.end, location_id);
					
				} else {
					Swal.fire({
						title: result.msg,
						icon: 'error',
					});
				}
			}
		});
	});

	$(document).on("change", "#business_location_id", function () {
		var start = $('input[name="date-filter"]:checked').data('start');
		var end = $('input[name="date-filter"]:checked').data('end');
		var location_id = $("#business_location_id").val();
		update_statistics(start, end, location_id);
	});
});

function update_statistics( start, end, location_id){
	var data = { start: start, end: end, location_id: location_id };
	//get purchase details
	var loader = '<i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i>';
	$('.total_purchase').html(loader);
	$('.purchase_due').html(loader);
	$('.total_sell').html(loader);
	$('.invoice_due').html(loader);
	$('.total_stock').html(loader);
	$('.gross_profit').html(loader);
	$('.net_earnings').html(loader);
	$('.average_sales').html(loader);
	$.ajax({
		method: "POST",
		url: '/home/get-purchase-details',
		dataType: "json",
		data: data,
		success: function (data) {
			if (data.box_exc_tax) {
				$('.total_purchase').html(__currency_trans_from_en(data.total_purchase_exc_tax, true ));
				$('.purchase_due').html( __currency_trans_from_en(data.purchase_due, true));
			} else {
				$('.total_purchase').html(__currency_trans_from_en(data.total_purchase_inc_tax, true ));
				$('.purchase_due').html( __currency_trans_from_en(data.purchase_due, true));
			}
		}
	});
	//get sell details
	$.ajax({
		method: "POST",
		url: '/home/get-sell-details',
		dataType: "json",
		data: data,
		success: function(data){
			if (data.box_exc_tax) {
				$('.total_sell').html(__currency_trans_from_en(data.total_sell_exc_tax, true ));
				$('.invoice_due').html( __currency_trans_from_en(data.invoice_due, true));
				$('.average_sales').html(__currency_trans_from_en(data.average_exc_tax, true ));
			} else {
				$('.total_sell').html(__currency_trans_from_en(data.total_sell_inc_tax, true ));
				$('.invoice_due').html( __currency_trans_from_en(data.invoice_due, true));
				$('.average_sales').html(__currency_trans_from_en(data.average_inc_tax, true ));
			}
		}
	});

	//get alerts in case of low stock
	$.ajax({
		method: "GET",
		url: '/home/product-stock-alert',		
		data: data,
		success: function (data) {
			$('.low_stock_products').text(data);			
		}
	});
	//get purchase payment dues
	$.ajax({
		method: "GET",
		url: '/home/purchase-payment-dues',
		data: data,
		success: function (data) {
			$('.purchase_payment_dues').text(data);
		}
	});
	//get sales payment dues
	$.ajax({
		method: "GET",
		url: '/home/sales-payment-dues',
		data: data,
		success: function (data) {
			$('.sales_payment_dues').text(data);
		}
	});
	//get the amount of expired products and the ones that are close to expire
	// $.ajax({
	// 	method: "GET",
	// 	url: '/home/get-stock-expiry-products',
	// 	data: data,
	// 	success: function (data) {
	// 		$('.expire_products').text(data);
	// 	}
	// });

	// Get the monetary value of the total stock
	$.ajax({
		method: 'get',
		url: '/home/get-total-stock',
		data: data,
		success: function (data) {
			$('.total_stock').html(__currency_trans_from_en(data, true));
		}
	});

	$.ajax({
		method: 'get',
		url: '/home/get-profits',
		data: data,
		success: function (data) {
			$('.gross_profit').html(__currency_trans_from_en(data.gross_profit, true));
			$('.net_earnings').html(__currency_trans_from_en(data.net_earnings, true));
			$('.total_expense').html(__currency_trans_from_en(data.total_expense, true));
		}
	});

	$.ajax({
		method: 'get',
		url: '/home/get-trending-products',
		data: data,
		success: function (data) {
			$('#trendingProducts-table').find('tr').remove();
			if(data.length === 0){
				$('#trendingProducts-table').append('<tr><td class="text-center" colspan="4">No hay productos para ese rango de fechas</td></tr>');
			}else{
				data.forEach(element => {
					$('#trendingProducts-table').append('<tr><td>'+element.product+'</td><td>'+element.total_unit_sold+'</td><td>'+element.total_sells+'</td><td>'+element.last_sells+'</td></tr>');
				});
			}
			
		}
	});
}
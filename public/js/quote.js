$(document).ready(function(){
    //Date picker
    $('input#quote_date').datepicker({
        autoclose: true,
        format: datepicker_date_format
    });

    //get customer
    $('#customer_id').select2({
    	ajax: {
      		url: '/contacts/customers',
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
    $('#customer_id').on('select2:select', function (e) {
		var data = e.params.data;
		
		if(data.payment_condition){
			$('input#payment_condition').val(data.payment_condition);
		} else { $('input#payment_condition').val(""); }

    	if(data.pay_term_number){
    		$('input#pay_term_number').val(data.pay_term_number);
		} else { $('input#pay_term_number').val(""); }
		
    	if(data.pay_term_type){
			$('#pay_term_type').val(data.pay_term_type);
    	} else { $('#pay_term_type').val(""); }
		
		if(data.tax_group_id){
    		$('#tax_group_id').val(data.tax_group_id);
    	} else { $('#tax_group_id').val(""); }
		
		if(data.tax_percent){
    		$('#tax_percent').val(data.tax_percent).trigger("change");;
    	} else { $('#tax_percent').val("").trigger("change");; }
		
		if(data.min_amount){
    		$('#min_amount').val(data.min_amount);
    	} else { $('#min_amount').val(""); }
		
		if(data.max_amount){
    		$('#max_amount').val(data.max_amount);
    	} else { $('#max_amount').val(""); }
	});
});
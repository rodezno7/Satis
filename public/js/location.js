$.fn.modal.Constructor.prototype.enforceFocus = function() {};

$(document).ready(function(){
    $('div.add_update_accounting_account_modal').on('shown.bs.modal', function() {
        $("select.account_select").select2({
			ajax: {
				type: "post",
				url: "/catalogue/get_accounts_for_select2",
				dataType: "json",
				data: function(params){
					return {
						q: params.term
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

		$('form#account_business_location_form').on('submit', function(e){
			e.preventDefault();
	
			$.ajax({
				method: 'post',
				url: $(this).attr('action'),
				data: $(this).serialize(),
				dataType: 'json',
				success: function(result){
					if(result.success){
						$('div.add_update_accounting_account_modal').modal('hide');
						toastr.success(result.msg);
					} else{
						toastr.error(result.msg);
					}
				}
			});
		});
    });
});
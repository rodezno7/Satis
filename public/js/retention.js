$(document).ready(function () {
	$.fn.modal.Constructor.prototype.enforceFocus = function() {};

	// Modal
	$('.retention_modal').on('shown.bs.modal', function () {
		// Select2
		$(this).find('.select2').select2();

		// Datetime picker
		$('.datepicker1').datetimepicker({
			format: moment_date_format,
			ignoreReadonly: true
		});

		// Get customers
		$('#customer_id').select2({
			ajax: {
				  url: '/customers/get_customers',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
					  q: params.term,
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
			escapeMarkup: function (markup) {
				return markup;
			}
		});

		// On change for final_total input
		$('input#final_total').on('change', function () {
			let amount = parseFloat($(this).val());

			if (amount) {
				let retention = parseFloat(amount * 0.01).toFixed(4);
				$('input#retention').val(retention);

			} else {
				$('input#retention').val('');
			}
		});
	});

	// Datatable
    retentions_table = $('#retentions_table').DataTable({
        processing: true,
        serverSide: true,
        'ajax': {
            'url': '/retentions',
        },
        columns: [
            { data: 'transaction_date', name: 'transaction_date', className: 'text-center' },
			{ data: 'retention_type', name: 'retention_type', className: 'text-center' },
			{ data: 'ref_no', name: 'ref_no', className: 'text-center' },
			{ data: 'name', name: 'customers.name' },
			{ data: 'additional_notes', name: 'additional_notes' },
			{ data: 'final_total', name: 'final_total', className: 'text-right' },
			{ data: 'action', name: 'action', searchable: false, orderable: false, className: 'text-center' },
        ],
		fnDrawCallback: function (oSettings) {
            __currency_convert_recursively($('#retentions_table'));
        },
    });

	// On submit for retention_add_form form
	$(document).on('submit', 'form#retention_add_form', function (e) {
		e.preventDefault();

		$(this).find('button[type="submit"]').attr('disabled', true);
		
		$.ajax({
			method: 'post',
			url: $(this).attr("action"),
			dataType: 'json',
			data: $(this).serialize(),
			success: function (result) {
				if (result.success === true) {
					$('div.retention_modal').modal('hide');
					
					Swal.fire({
						title: result.msg,
						icon: 'success',
					});

					retentions_table.ajax.reload();

				} else {
					Swal.fire({
						title: result.msg,
						icon: 'error',
					});
				}
			}
		});
	});

	// On click for update_retention_button link
	$(document).on('click', '.update_retention_button', function (e) {
        e.preventDefault();

        var container = $(this).data("container");

        $.ajax({
            url: $(this).data('href'),
            dataType: 'html',
            success: function (result) {
                $(container).html(result).modal('show');

				$('form#retention_edit_form').submit(function (e) {
					e.preventDefault();
					
					$(this).find('button[type="submit"]').attr('disabled', true);

					$.ajax({
						method: 'put',
						url: $(this).attr('action'),
						dataType: 'json',
						data: $(this).serialize(),
						success: function (result) {
							if (result.success == true) {
								$('div.retention_modal').modal('hide');

								Swal.fire({
									title: result.msg,
									icon: 'success',
								});

								retentions_table.ajax.reload(null, false);

							} else {
								Swal.fire({
									title: result.msg,
									icon: 'error',
								});
							}
						}
					});
            	});
			}
    	});
	});

	// On click for delete_retention_button link
	$(document).on('click', '.delete_retention_button', function () {
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
					success: function(result) {
						if (result.success === true) {
							Swal.fire({
								title: result.msg,
								icon: 'success',
							});

							retentions_table.ajax.reload(null, false);

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
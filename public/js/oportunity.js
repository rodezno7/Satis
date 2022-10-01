$(function () {
	$("select.select2").off().select2();
	let type = $('input#type').val();
	$("#customer_id").select2({
		ajax: {
			url: "/contacts/customers",
			dataType: "json",
			delay: 250,
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
		minimumInputLength: 1,
		language: {
			noResults: function () {
				var name = $("#customer_id").data("select2").dropdown.$search.val();
				return (
					'<button type="button" data-name="' +
					name +
					'" class="btn btn-link add_new_customer"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>&nbsp; ' +
					__translate("add_name_as_new_customer", {
						name: name,
					}) +
					"</button>"
				);
			},
		},
		escapeMarkup: function (markup) {
			return markup;
		},
	});

	$("div.oportunity_modal").on("shown.bs.modal", function () {
		//** Get customers for refered by */
		$("select#refered_id").select2({
			ajax: {
				url: "/customers/get_customers",
				dataType: "json",
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
					};
				},
				processResults: function (data) {
					return {
						results: data,
					};
				},
			},
			minimumInputLength: 1,
			escapeMarkup: function (markup) {
				return markup;
			},
		});
	});
	$("div.oportunities_modal").on("shown.bs.modal", function () {
		$(this).modal({
			backdrop: "static",
			keyboard: false,
		});
		$("select.select2").off().select2();
	});
	$("#oportunity_table").DataTable({
		order: [
			[0, "desc"]
		],
		processing: true,
		serverSide: true,
		ajax: "/oportunities/getOportunityData?type=" + type + "",
		columns: [
			{data: "contact_date",},
			{data: "contact_type",},
			{data: "reason",},
			{data: "name",},
			{data: "full_name_user",},
			{
				data: "actions",
				orderable: false,
				searchable: false,
			},
		],
		columnDefs: [{
			targets: "_all",
			className: "text-center",
		}, ],
	});

	$("#oportunity_table").on("dblclick", "tr", function () {
		var data = oportunity_table.row(this).data();
		if (typeof data.id != "undefined") {
			$(location).attr("href", `/follow-oportunities/${data.id}`);
		}
	});
	//Date range as a button
	$('#daterange-btn').daterangepicker(
		dateRangeSettings,
		function (start, end) {
			$('#daterange-btn span').html(start.format(moment_date_format) + ' ~ ' + end.format(
				moment_date_format));
			$('#oportunity_table').DataTable().ajax.
				url('/oportunities/getOportunityData?type='+type+'&start_date=' + start
				.format('YYYY-MM-DD') +
				'&end_date=' + end.format('YYYY-MM-DD')).load();
		}
	);
});

var cont = 0;
var product_ids = [];
var rowCont = [];

var econt = 0;
var eproduct_ids = [];
var erowCont = [];

function eshowNotFoundDesc() {
	if ($("#echk_not_found").is(":checked")) {
		$('#eproducts_not_found_desc').show();
		$("#eproduct_cat_id").val('').trigger('change');
		$("#eproduct_cat_id").prop('disabled', true);
	} else {
		$('#eproducts_not_found_desc').hide();
		$('#eproducts_not_found_desc').val('');
		$("#eproduct_cat_id").prop('disabled', false);
	}
}

function showNotFoundDesc() {
	if ($("#chk_not_found").is(":checked")) {
		$('#products_not_found_desc').show();
		$("#product_cat_id").val('').trigger('change');
		$("#product_cat_id").prop('disabled', true);
	} else {
		$('#products_not_found_desc').hide();
		$('#products_not_found_desc').val('');
		$("#product_cat_id").prop('disabled', false);
	}
}
//funcion para agregar una nueva oportunidad 
$(document).on('submit', 'form#follow_oportunity_add_form', function (e) {
	// debugger;
	e.preventDefault();
	$(this).find('button[type="submit"]').attr('disabled', false);
	var data = $(this).serialize();
	$.ajax({
		method: "POST",
		url: $(this).attr("action"),
		dataType: "json",
		data: data,
		success: function (result) {
			if (result.success == true) {
				$("#follow_oportunities_table").DataTable().ajax.reload();
				$('div.oportunities_modal').modal('hide');
				Swal.fire({
					title: result.msg,
					icon: "success",
					timer: 2000,
					showConfirmButton: false,
				});
				$("#content").hide();
			} else {
				Swal.fire({
					title: result.msg,
					icon: "error",
				});
			}
		}
	});
});
$(document).on('submit', 'form#oportunity_add_form', function (e) {
	// debugger;
	e.preventDefault();
	$(this).find('button[type="submit"]').attr('disabled', false);
	var data = $(this).serialize();
	$.ajax({
		method: "POST",
		url: $(this).attr("action"),
		dataType: "json",
		data: data,
		success: function (result) {
			if (result.success == true) {
				$("#oportunity_table").DataTable().ajax.reload();
				$('div.oportunity_modal').modal('hide');
				Swal.fire({
					title: result.msg,
					icon: "success",
					timer: 2000,
					showConfirmButton: false,
				});
				$("#content").hide();
			} else {
				Swal.fire({
					title: result.msg,
					icon: "error",
				});
			}
		}
	});
});


$('#daterange-btn').on('cancel.daterangepicker', function (ev, picker) {
	$('#oportunity_table').DataTable().ajax.url('/oportunities/getOportunityData').load();
	$('#daterange-btn span').html('<i class="fa fa-calendar"></i> ' + LANG.filter_by_date + '');
});

function clear() {
	$('#list').empty();
	cont = 0;
	product_ids = [];
	rowCont = [];
	$("#contact_type").val('entrante').trigger('change');
	$("#contact_reason_id").val('').trigger('change');
	$("#contact_mode_id").val('').trigger('change');
	$("#product_cat_id").val('').trigger('change');
	$("#chk_not_found").prop('checked', false);
	$("#chk_not_stock").prop('checked', false);
	$("#products_not_found_desc").val('');
	$("#products_not_found_desc").hide();
	$("#div_products").hide();
	$("#product_cat_id").prop('disabled', false);
	$("#locations").val(0).trigger('change');
	$("#products").val(0).trigger('change');
	$("#notes").val('');
	$("#oportunity_id").val('');
}

function viewOportunity(id) {
	$("#div_oportunities").hide();
	$("#header_oportunity").hide();
	$("#date-filter").hide();
	$("#header_follow").show();
	$("#div_follows").show();

	var route = "/oportunities/" + id;
	$.get(route, function (res) {
		$("#c_id").val(res.id);
		$("#c_name").val(res.name);
		let contact_type = res.contact_type ?? "N/A";
		let contact_date = res.contact_date ?? "N/A";
		let reason = res.reason ?? "N/A";
		let name = res.name ?? "N/A";
		let company = res.company ?? "N/A";
		let charge = res.charge ?? "N/A";
		let email = res.email ?? "N/A";
		let contacts = res.contacts ?? "N/A";
		let mode = res.mode ?? "N/A";
		let knowned = res.knowned ?? "N/A";
		let customer = res.customer ?? "N/A";
		let social_user = res.social_user ?? "N/A";
		let country = res.country ?? "N/A";
		let state = res.state ?? "N/A";
		let city = res.city ?? "N/A";
		let interest = res.category ?? res.products_not_found_desc ?? "N/A";

		$("#lbl_contact_type").text("" + contact_type + "");
		$("#lbl_contact_date").text("" + contact_date + "");
		$("#lbl_contact_reason").text("" + reason + "");
		$("#lbl_name").text("" + name + "");
		$("#lbl_company").text("" + company + "");
		$("#lbl_position").text("" + charge + "");

		$("#lbl_email").text("" + email + "");
		$("#lbl_contacts").text("" + contacts + "");
		$("#lbl_contact_mode").text("" + mode + "");
		$("#lbl_known_by").text("" + knowned + "");
		$("#lbl_refered_id").text("" + customer + "");

		$("#lbl_social_user").text("" + social_user + "");
		$("#lbl_country").text("" + country + "");
		$("#lbl_state").text("" + state + "");
		$("#lbl_city").text("" + city + "");
		$("#lbl_interest").text("" + interest + "");
		loadFollows(id);
	});
}

$(document).on('submit', 'form#oportunity_edit_form', function (e) {
	e.preventDefault();
	$("#btn-edit-oportunity").prop('disabled', true);
	$("#btn-close-modal-edit-oportunity").prop('disabled', true);
	var data = $(this).serialize();
	$.ajax({
		method: "POST",
		url: $(this).attr("action"),
		datatype: "json",
		data: data,
		success: function (result) {
			if (result.success == true) {
				$("#btn-edit-oportunity").prop('disabled', false);
				$("#btn-close-modal-edit-oportunity").prop('disabled', false);
				$("#oportunity_table").DataTable().ajax.reload();
				$('div.oportunity_modal').modal('hide');
				Swal.fire({
					title: result.msg,
					icon: "success",
					timer: 3000,
					showConfirmButton: false,
				});
			} else {
				$("#btn-edit-oportunity").prop('disabled', false);
				$("#btn-close-modal-edit-oportunity").prop('disabled', false);
				Swal.fire({
					title: result.msg,
					icon: "error",
				});
			}
		}
	});
});

$("#btn-edit-follow-oportunity").on("click", function () {
	$("#btn-edit-follow-oportunity").prop('disabled', true);
	$("#btn-close-modal-edit-follow").prop('disabled', true);
	var data = $("#follow_oportunity_edit_form").serialize();
	var id = $("#follow_oportunity_id").val();
	route = "/follow-oportunities/" + id;
	token = $("#token").val();

	$.ajax({
		url: route,
		headers: {
			'X-CSRF-TOKEN': token
		},
		type: 'PUT',
		dataType: 'json',
		data: data,
		success: function (result) {
			if (result.success == true) {
				$("#btn-edit-follow-oportunity").prop('disabled', false);
				$("#btn-close-modal-edit-follow").prop('disabled', false);
				$("#follow_oportunities_table").DataTable().ajax.reload();
				Swal.fire({
					title: result.msg,
					icon: "success",
					timer: 3000,
					showConfirmButton: false,
				});
				$("#modal_edit_follow").modal('hide');
			} else {
				$("#btn-edit-follow-oportunity").prop('disabled', false);
				$("#btn-close-modal-edit-follow").prop('disabled', false);
				Swal.fire({
					title: result.msg,
					icon: "error",
				});
			}
		}
	});
});

$(document).on('click', 'a.edit_oportunity_button', function () {
	$("div.oportunity_modal").load($(this).data('href'), function () {
		$(this).modal({
			backdrop: "static",
			keyboard: false
		});
	});
});


$('.oportunity_modal').on('shown.bs.modal', function () {
	$('select.select2').off().select2();
	$('#products_not_found_desc').hide();
	var valors = $('select[name="known_by"] option:selected').text();

	if (valors.includes("cliente")) {
		$("#refered_id").prop("disabled", false);
	} else {
		$("#refered_id").prop("disabled", true);
	}
	let dui = document.getElementById("dni");
	$(dui).mask("00000000-0");

	let nit = document.getElementById('tax_number');
	$(nit).mask('0000-000000-000-0');

	if (!($("#is_taxpayer").is(":checked"))) {
		$('#reg_number').prop('required', false);
		$("#dni").prop('required', true);
	}
});



function getValSel(sel) {

	var valors = $('select[name="known_by"] option:selected').text();

	if (valors.includes("cliente")) {
		$("#refered_id").prop("disabled", false);
	} else {
		$("#refered_id").val('').trigger('change');
		$("#refered_id").prop("disabled", true);
	}
}

function getStatesByCountry(id) {
	$("#state_id").empty();
	var route = "/states/getStatesByCountry/" + id;
	$.get(route, function (res) {
		$("#state_id").append(
			'<option value="0" disabled selected>' + LANG.please_select + '</option>');
		$(res).each(function (key, value) {
			$("#state_id").append('<option value="' + value.id + '">' + value.name + '</option>');
		});
	});
}

function getCitiesByState(id) {
	$("#city_id").empty();
	var route = "/cities/getCitiesByState/" + id;
	$.get(route, function (res) {
		$("#city_id").append(
			'<option value="0" disabled selected>' + LANG.please_select + '</option>');
		$(res).each(function (key, value) {
			$("#city_id").append('<option value="' + value.id + '">' + value.name + '</option>');
		});
	});
}

$(document).on('change', '#country_id', function (e) {
	id = $("#country_id").val();
	if (id) {
		getStatesByCountry(id);
	} else {
		$("#state_id").empty();
		$("#state_id").append(
			'<option value="0" disabled selected>' + LANG.please_select + '</option>');

		$("#city_id").empty();
		$("#city_id").append(
			'<option value="0" disabled selected>' + LANG.please_select + '</option>');
	}
});

$(document).on('change', '#state_id', function (e) {
	id = $("#state_id").val();
	if (id) {
		getCitiesByState(id);
	} else {
		$("#city_id").empty();
		$("#city_id").append(
			'<option value="0" disabled selected>' + LANG.please_select + '</option>');
	}
})

function deleteOportunity(id) {
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
		if (willDelete.value) {
			route = '/oportunities/' + id;
			$.ajax({
				url: route,
				type: 'DELETE',
				dataType: 'json',
				success: function (result) {
					if (result.success == true) {
						Swal.fire({
							title: result.msg,
							icon: "success",
							timer: 3000,
							showConfirmButton: false,
						});
						$("#oportunity_table").DataTable().ajax.reload(null, false);
					} else {
						Swal.fire({
							title: result.msg,
							icon: "error",
						});
					}
				}
			});
		}
	});
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

$("#back").on("click", function () {
	$("#div_follows").hide();
	$("#header_follow").hide();
	$("#header_oportunity").show();
	$("#div_oportunities").show();
	if ($("#opportunity_details_box").hasClass("collapsed-box")) {
		$("#opportunity_details_box").removeClass("collapsed-box");
		$("#opportunity_details_box_body").css("display", "block");
		$("#icon-collapsed").removeClass("fa fa-plus");
		$("#icon-collapsed").addClass("fa fa-minus");
	}
});

/* CONVERT TO CUSTOMER */
$(document).on('click', 'a.convert_customer_button', function () {
	$("div.oportunity_modal").load($(this).data('href'), function () {
		$(this).modal({
			backdrop: 'static'
		});
	});
});

function showTaxPayer() {
	if ($("#is_taxpayer").is(":checked")) {
		$('#div_taxpayer').show();
		$("#reg_number").val('');
		$("#tax_number").val('');
		$("#business_line").val('');
		setTimeout(function () {
				$('#reg_number').focus();
			},
			800);
	} else {
		$('#div_taxpayer').hide();
		$("#reg_number").val('');
		$("#tax_number").val('');
		$("#business_line").val('');
	}
}

function showCredit() {
	if ($("#allowed_credit").is(":checked")) {
		$('#div_credit').show();
		$("#opening_balance").val('');
		$("#credit_limit").val('');
		$("#payment_terms_id").val('').trigger('change');
		setTimeout(function () {
				$('#opening_balance').focus();
			},
			800);
	} else {
		$('#div_credit').hide();
		$("#opening_balance").val('');
		$("#credit_limit").val('');
		$("#payment_terms_id").val('').trigger('change');
	}
}

$(document).on('submit', 'form#form-add-customer', function (e) {
	e.preventDefault();
	$("#btn-add-customer").prop('disabled', true);
	$("#btn-close-modal-add-customer").prop('disabled', true);
	var data = $("#form-add-customer").serialize();
	route = "/oportunities/convert-to-customer";
	token = $("#token").val();
	$.ajax({
		url: route,
		headers: {
			'X-CSRF-TOKEN': token
		},
		type: 'POST',
		dataType: 'json',
		data: data,
		success: function (result) {
			if (result.success == true) {
				$("#btn-add-customer").prop('disabled', false);
				$("#btn-close-modal-add-customer").prop('disabled', false);
				$("#oportunity_table").DataTable().ajax.reload();
				$('div.oportunity_modal').modal('hide');
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
				errormessages += "<li>" + field + "</li>";
			});
			Swal.fire({
				title: LANG.errors,
				icon: "error",
				html: "<ul>" + errormessages + "</ul>",
			});
		}
	});
});
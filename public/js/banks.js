$(document).ready(function(){
    $(document).on("click", "button#add_expense", function(){
        var add_expense_modal = $("div.add_expense_modal");
        $.ajax({
            url: "/expenses/get_add_expense",
            dataType: "html",
            success: function(result) {
                add_expense_modal.html(result).modal('show');
            }
        });
    });

    $("input#bank_reconciliation_xlsx").fileinput({
        'showUpload': false,
        'showPreview': false,
        'browseLabel': LANG.select_please,
        'removeLabel': ""
    });

    $(document).on('change', 'select#bank-reconc-bank', function(){
        let bank_id = $(this).val();
        let bank_account = $('select#bank-reconc-bank-accounts');

        if(bank_id){
            $.ajax({
                url: '/banks/get-bank-accounts/' + bank_id,
                type: 'GET',
                dataType: 'json',
                success: function(data){
                    if(data){
                        bank_account.empty();
                        bank_account.append("<option value=''>"+ LANG.select_bank_account +"</option>");

                        data.forEach(d => {
                            let option = new Option(d.name, d.id, false, false);
                            bank_account.append(option);
                        });
                    } else {
                        bank_account.empty();
                        bank_account.append("<option value=''>"+ LANG.select_bank_account +"</option>");
                    }
                }
            });
        } else {
            bank_account.empty();
            bank_account.append("<option value=''>"+ LANG.select_bank_account +"</option>");
        }
    });

    $('div.add_expenses_modal').on('shown.bs.modal', function() {
        var modal = $(this);

        modal.find('select#purchase_and_expenses_due').select2({
            ajax: {
                type: "get",
                url: "/expenses/get-purchases-expenses",
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
    });

    $('div.add_expense_modal').on('shown.bs.modal', function(e) {
        modal = $(this);
        modal.find("select.select2").select2();

        //Date picker
        modal.find('input#transaction_date').datepicker({
            autoclose: true,
            format: datepicker_date_format
        });

        modal.find("input#document").fileinput({
            'showUpload': false,
            'showPreview': false,
            'browseLabel': "",
            'removeLabel': ""
        });

        modal.on("change", "select#supplier", function(){
            var perception = modal.find("div#perception_div");
            var tax_percent = modal.find("select#supplier :selected").data("tax_percent");
    
            if(tax_percent == "0"){
                perception.hide();
                modal.find("input#perception_amount").val(0);
            } else{
                perception.show();
            }
        });

        modal.find("input#enable_exempt_amount").change(function(){
            var exempt_amount = modal.find("input#exempt_amount");
            if($(this).prop("checked")){
                exempt_amount.prop('disabled', false);
            } else{
                exempt_amount.prop('disabled', true);
                exempt_amount.val(null).change();
            }
        });

        modal.on('change', 'input#sub_total, select#taxes, input#exempt_amount, select#supplier', function(){

            var tax = modal.find('select#taxes').val();
            var sub_total = __read_number(modal.find('input#sub_total'));
            var tax_amount = modal.find('input#tax_amount');
            var exempt_amount = modal.find("input#enable_exempt_amount").prop("checked") ?
                (__read_number(modal.find("input#exempt_amount")) > 0 ? __read_number(modal.find("input#exempt_amount")) : 0) : 0;
            var tax_supplier_percent = modal.find("select#supplier :selected") ? modal.find("select#supplier :selected").data("tax_percent") : 0;
            var perception = modal.find("input#perception_amount");
            var final_total = modal.find('input#final_total');
            var amount_0 = modal.find('input#amount_0');
    
            var tax_supplier = 0;
            if(tax_supplier_percent != "0"){
                if(sub_total > 0){
                    var min_amount = modal.find("select#supplier :selected").val() ? parseFloat(modal.find("select#supplier :selected").data("tax_min_amount")) : 0;
                    var max_amount = modal.find("select#supplier :selected").val() ? parseFloat(modal.find("select#supplier :selected").data("tax_max_amount")) : 0;
                    tax_supplier_percent = parseFloat(tax_supplier_percent);
    
                    tax_supplier = calc_contact_tax(sub_total, min_amount, max_amount, tax_supplier_percent);
                    __write_number(perception, tax_supplier);
                }
    
            } else{
                __write_number(perception, tax_supplier);
            }
    
            if(tax != 0){
                var tax_percent = parseFloat(modal.find('select#taxes :selected').data('tax_percent'));
                var tax_amount_value = sub_total * (tax_percent / 100);
    
                var total = sub_total + tax_amount_value + exempt_amount + tax_supplier;
    
                __write_number(tax_amount, tax_amount_value);
                __write_number(final_total, total);
                __write_number(amount_0, total);
    
            } else if(tax == 0){
                __write_number(tax_amount, 0);
                __write_number(final_total, (sub_total + exempt_amount + tax_supplier));
                __write_number(amount_0, sub_total);
            }
        });
    
        modal.on("click", "button#add_single_expense", function(e){
            e.preventDefault();
            modal_ = $("div.add_expense_modal");
            
            add_single_expense_row(modal_);
            modal_.modal("hide").empty();
    
            update_expenses_index();
        });
    });

    $("div.expenses_modal, div.list_expenses_modal, div.add_expenses_modal").on("click", "button#rm_item", function(){
        Swal.fire({
            title: LANG.sure,
            text: LANG.wont_be_able_revert,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: LANG.cancel,
            confirmButtonText: LANG.yes
          }).then((result) => {
            if (result.isConfirmed) {
                var tr = $(this).closest("tr");
                var ref_no = tr.find("span#ref_no").text();

                $("table#hidden_table tbody tr").each(function(){
                    if ($(this).find("input#_ref_no").val() == ref_no) {
                        $(this).closest("tr").remove();
                        tr.remove();
                    }
                });

                Swal.fire({
                    icon: 'success',
                    title: LANG.record_deleted,
                    showConfirmButton: false,
                    timer: 1500
                })
            }
          });
    });
});

function update_expenses_index() {
    $("table#hidden_table tbody tr").each(function(i, val) {
        $(this).find("input[type='hidden']").each(function() {
            $(this).attr("name", "expenses[" + i + "][" + $(this).attr("id") + "]");
        });
    });
}

function add_single_expense_row_from_due(expense_id) {
    $.ajax({
        type: "GET",
        url: "/expenses/get_expense_details/" + expense_id,
        dataType: "json",
        success: function(expense) {
            var hidden_table = $("table#hidden_table tbody");
            var showed_table = $("table#showed_table tbody");

            var hidden_tr = "<tr>";
            hidden_tr += "<td>";
            hidden_tr += "<input type='hidden' id='_contact_id' value='" + expense.contact_id + "'>";
            hidden_tr += "<input type='hidden' id='_expense_id' value='" + expense.id + "'>";
            hidden_tr += "</td>";
            hidden_tr += "<td><input type='hidden' id='_expense_category_id' value='" + expense.expense_category_id + "'></td>";
            hidden_tr += "<td><input type='hidden' id='_transaction_date' value='" + expense.transaction_date + "'></td>";
            hidden_tr += "<td><input type='hidden' id='_document_types_id' value='" + expense.document_types_id + "'></td>";
            hidden_tr += "<td><input type='hidden' id='_ref_no' value='" + expense.ref_no + "'></td>";
            hidden_tr += "<td><input type='hidden' id='_payment_condition' value='" + expense.payment_condition + "'></td>";
            hidden_tr += "<td><input type='hidden' id='_payment_term_id' value='" + expense.payment_term_id + "'></td>";
            hidden_tr += "<td><input type='hidden' id='_total_before_tax' value='" + expense.total_before_tax + "'></td>";
            hidden_tr += "<td><input type='hidden' id='_tax_group_id' value='" + expense.tax_group_id + "'></td>";
            hidden_tr += "<td><input type='hidden' id='_tax_amount' value='" + expense.tax_amount + "'></td>";
            hidden_tr += "<td><input type='hidden' id='_final_total' value='" + expense.final_total + "'></td>";
            hidden_tr += "<td><input type='hidden' id='_additional_notes' value='" + expense.additional_notes + "'></td>";
            hidden_tr += "<td><input type='hidden' id='_document' value='" + expense.document + "'></td>";
            hidden_tr += "</tr>";

            var showed_tr = "<tr>";
            showed_tr += "<td><span>" + expense.supplier_name + "</span>";
            showed_tr += "<td><span id='ref_no'>" + expense.ref_no + "</span>";
            showed_tr += "<td><span>" + expense.transaction_date + "</span>";
            showed_tr += "<td><span>$ " + parseFloat(expense.total_before_tax).toFixed(2) + "</span>";
            showed_tr += "<td><span>$ " + parseFloat(expense.tax_amount).toFixed(2) + "</span>";
            showed_tr += "<td><span>$ " + parseFloat(expense.final_total).toFixed(2) + "</span>";
            showed_tr += "<td><button type='button' id='rm_item' class='btn btn-danger btn-xs'><i class='fa fa-times'></i></button></td>"
            showed_tr += "</tr>";

            hidden_table.append(hidden_tr);
            showed_table.append(showed_tr);
        }
    }).done(function() {
        update_expenses_index();
    });
}

function add_single_expense_row(modal) {
    let hidden_table = $("table#hidden_table tbody");
    let showed_table = $("table#showed_table tbody");
    
    let expense_category_id = modal.find("select#expense_search").val();
    let contact_id = modal.find("select#supplier_id").val();
    let supplier_name = modal.find("input#supplier_name").val();
    let transaction_date = modal.find("input#expense_transaction_date").val();
    let document_types_id = modal.find("select#document_types_id").val();
    let ref_no = modal.find("input#ref_no").val();
    let payment_condition = modal.find("select#payment_condition").val();
    let payment_term_id = modal.find("select#payment_term_id").val();
    let total_before_tax = modal.find("input#ammount").val();
    let tax_group_id = modal.find("select#tax_percent_group").val();
    let tax_amount = modal.find("input#iva").val();
    let final_total = modal.find("input#final_total").val();
    let additional_notes = modal.find("textarea#additional_notes").val();
    let document = modal.find("input#upload_document").val();

    let hidden_tr = "<tr>";
    hidden_tr += "<td>";
    hidden_tr += "<input type='hidden' id='_contact_id' value='" + contact_id + "'>";
    hidden_tr += "<input type='hidden' id='_expense_id' value='0'>";
    hidden_tr += "</td>";
    hidden_tr += "<td><input type='hidden' id='_expense_category_id' value='" + expense_category_id + "'></td>";
    hidden_tr += "<td><input type='hidden' id='_transaction_date' value='" + transaction_date + "'></td>";
    hidden_tr += "<td><input type='hidden' id='_document_types_id' value='" + document_types_id + "'></td>";
    hidden_tr += "<td><input type='hidden' id='_ref_no' value='" + ref_no + "'></td>";
    hidden_tr += "<td><input type='hidden' id='_payment_condition' value='" + payment_condition + "'></td>";
    hidden_tr += "<td><input type='hidden' id='_payment_term_id' value='" + payment_term_id + "'></td>";
    hidden_tr += "<td><input type='hidden' id='_total_before_tax' value='" + total_before_tax + "'></td>";
    hidden_tr += "<td><input type='hidden' id='_tax_group_id' value='" + tax_group_id + "'></td>";
    hidden_tr += "<td><input type='hidden' id='_tax_amount' value='" + tax_amount + "'></td>";
    hidden_tr += "<td><input type='hidden' id='_final_total' value='" + final_total + "'></td>";
    hidden_tr += "<td><input type='hidden' id='_additional_notes' value='" + additional_notes + "'></td>";
    hidden_tr += "<td><input type='hidden' id='_document' value='" + document + "'></td>";
    hidden_tr += "</tr>";

    var showed_tr = "<tr>";
    showed_tr += "<td><span>" + supplier_name + "</span>";
    showed_tr += "<td><span id='ref_no'>" + ref_no + "</span>";
    showed_tr += "<td><span>" + transaction_date + "</span>";
    showed_tr += "<td><span>$ " + parseFloat(total_before_tax).toFixed(2) + "</span>";
    showed_tr += "<td><span>$ " + parseFloat(tax_amount).toFixed(2) + "</span>";
    showed_tr += "<td><span>$ " + parseFloat(final_total).toFixed(2) + "</span>";
    showed_tr += "<td><button type='button' id='rm_item' class='btn btn-danger btn-xs'><i class='fa fa-times'></i></button></td>"
    showed_tr += "</tr>";

    hidden_table.append(hidden_tr);
    showed_table.append(showed_tr);
}

function calc_contact_tax(amount, min_amount, max_amount, tax_percent) {
	var tax_amount = 0;

	/** If has min o max amount */
	if (min_amount || max_amount) {
		/** if has min and max amount */
		if (min_amount && max_amount) {
			if (amount >= min_amount && amount <= max_amount) {
				tax_amount = amount * tax_percent;
			}

		/** If has only min amount */
		} else if (min_amount && !max_amount) {
			if (amount >= min_amount) {
				tax_amount = amount * tax_percent;
			}

		/** If has only max amount */
		} else if (!min_amount && max_amount) {
			if (amount <= max_amount) {
				tax_amount = amount * tax_percent;
			}
		}

	/** If has none tax */
	} else {
		tax_amount = amount * tax_percent;
	}

	return tax_amount;
}
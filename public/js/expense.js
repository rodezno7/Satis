$(function () {
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

    $('button#btn_accounting').on('click', function () {
        let start_date = $('input#expense_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
        let end_date = $('input#expense_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
        let start_date_formatted = moment(start_date).format(moment_date_format);
        let end_date_formatted = moment(end_date).format(moment_date_format);

        swal({
            title: LANG.sure,
            text: LANG.expenses_from +' '+ start_date_formatted +' '+ LANG.to +' '+ end_date_formatted +' '+ LANG.will_be_accounted,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((confirm) => {
            if (confirm) {
                toastr.success(LANG.success);
                $.ajax({
                    method: "get",
                    url: '/expenses/accounting-by-range/'+ start_date +'/'+ end_date,
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            
                        } else{
                            toastr.error(response.msg);
                        }
                    }
                });
            }
        });
    });

    //Expense table
    expense_table = $('#expense_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [
        [0, 'desc']
        ],
        "ajax": {
            "url": "/expenses",
            "data": function(d) {
                d.location_id = $('select#location_id').val();
                d.expense_category_id = $('select#expense_category_id').val();
                d.start_date = $('input#expense_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                d.end_date = $('input#expense_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
        },
        columns: [
        { data: 'transaction_date', name: 'transaction_date' },
        { data: 'location_name', name: 'bl.name' },
        { data: 'supplier', name: 'contacts.name' },
        { data: 'short_name', name: 'document_types.short_name'},
        { data: 'ref_no', name: 'ref_no' },
        { data: 'payment_status', name: 'payment_status' },
        { data: 'final_total', name: 'final_total' },
        { data: 'action', name: 'action' }
        ],
        "fnDrawCallback": function(oSettings) {
            var expense_total = sum_table_col($('#expense_table'), 'final-total');
            $('#footer_expense_total').text(expense_total);
            $('#footer_payment_status_count').html(__sum_status_html($('#expense_table'), 'payment-status'));
            __currency_convert_recursively($('#expense_table'));
        },
        createdRow: function(row, data, dataIndex) {
            $(row).find('td:eq(4)').attr('class', 'clickable_td');
        }
    });

    /** On change location or expense category */
    $('select#location_id, select#expense_category_id').on('change', function() {
        expense_table.ajax.reload();
    });

    /** On submit expense store form */
    $(document).on('submit', 'form#expense_add_form', function(e) {
        e.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', false);
        var data = $(this).serialize();
        $.ajax({
            method: "POST",
            url: $(this).attr("action"),
            dataType: "json",
            data: data,
            success: function(result) {
                if (result.success == true) {
                    $("table#expense_table").DataTable().ajax.reload();
                    $('div.expenses_modal').modal('hide');
                    Swal.fire({
                        title: LANG.success,
                        text: result.msg,
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    $("#content").hide();
                } else {
                    Swal.fire({
                        title: LANG.error,
                        text: result.msg,
                        icon: "error",
                    });
                }
            },
            error: function(msj) {
                var errormessages = "";
                $.each(msj.responseJSON.errors, function(i, field) {
                    errormessages += "<li>" + field + "</li>";
                });
                Swal.fire({
                    title: LANG.error,
                    text: "{{ __('customer.errors') }}",
                    icon: "error",
                    html: "<ul>" + errormessages + "</ul>",
                });
            }
        });
    });

    /** On submit expense update form */
    $(document).on('click', 'a.edit_expense_button', function() {
        $("div.expenses_modal").load($(this).data('href'), function() {
            $(this).modal('show');

            $('form#edit_expense_form').submit(function(e) {
                e.preventDefault();
                $(this).find('button[type="submit"]').attr('disabled', false);
                var data = $(this).serialize();

                $.ajax({
                    method: "POST",
                    url: $(this).attr("action"),
                    dataType: "json",
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            $("table#expense_table").DataTable().ajax.reload();
                            $('div.expenses_modal').modal('hide');
                            Swal.fire({
                                title: LANG.success,
                                text: result.msg,
                                icon: "success",
                                timer: 2000,
                                showConfirmButton: false,
                            });
                            $('#content').hide();
                        } else {
                            Swal.fire({
                                title: LANG.error,
                                text: result.msg,
                                icon: "error",
                            });
                        }
                    }
                });
            });
        });
    });

    /** On delete expense */
    $(document).on('click', 'a.delete_expense', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_expense,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();

                $.ajax({
                    method: "DELETE",
                    url: href,
                    dataType: "json",
                    data: data,
                    success: function(result) {
                        if (result.success === true) {
                            Swal.fire({
                                title: ""+result.msg+"",
                                icon: "success",
                            });
                            expense_table.ajax.reload();
                        } else {
                            Swal.fire
                            ({
                                title: ""+result.msg+"",
                                icon: "error",
                            });
                        }
                    }
                });
            }
        });
    });

    /** Delete expense line */
    $(document).on('click', 'i.del-exp', function () {
        let tr = $(this).closest('tr');
        let form = $(this).closest('form');

        swal({
            title: LANG.sure,
            text: LANG.wont_be_able_revert,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((confirm) => {
            if (confirm) {
                tr.remove();
                sum_lines(form.find('table#expense_lines tbody tr'), form.find('input#amount'));
			}
		});
    });    

    /** On shown expense modal */
    $("div.expenses_modal").on("shown.bs.modal", function () {
        let modal = $(this);

        modal.find("input#upload_document").fileinput({
            'showUpload': false,
            'showPreview': true,
            'browseLabel': LANG.file_browse_label,
            'removeLabel': LANG.remove
        });

        // Get suppliers
        modal.find('select#supplier_id').select2({
            ajax: {
                url: '/expenses/get_suppliers',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            },
            minimumInputLength: 1,
            escapeMarkup: function(m) {
                return m;
            },
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }
                var html = data.text + ' (<b>' + LANG.code + ': </b>' + data.contact_id + ' - <b>' + LANG
                    .business + ': </b>' + data.business_name + ')';
                return html;
            },
            templateSelection: function(data) {
                if (!data.id) {
                    modal.find('input#supplier_name').val('');
                    return data.text;
                }
                // If it's a new supplier
                if (!data.contact_id) {
                    return data.text;
                    // If a provider has been selected
                } else {
                    modal.find('input#supplier_name').val(data.text);
                    modal.find('input#is_exempt').val(data.is_exempt);
                    modal.find('input#is_excluded_subject').val(data.is_excluded_subject);
                    modal.find('input#tax_percent').val(data.tax_percent);
                    modal.find('input#tax_min_amount').val(data.tax_min_amount);
                    modal.find('input#tax_max_amount').val(data.tax_max_amount);
                    setTimeout(() => {
                        recalculate(modal); 
                    }, 500);
                    return data.contact_id || data.text;
                }
            },
        });

        // Get expense categories
        modal.find('select#expense_search').select2({
            ajax: {
                url: '/expenses/get_categories',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            },
            minimumInputLength: 1,
            escapeMarkup: function(m) {
                return m;
            },
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }
                var html = data.text + ' (<b>' + LANG.code + ': </b>' + data.cat_id + ' - <b>' + LANG
                    .account + ': </b>' + data.account_name + ')';
                return html;
            },
            templateSelection: function(data) {
                if (!data.id) {
                    modal.find('input#account_name').val('');
                    return data.text;
                }

                if (!data.cat_id) {
                    return data.text;
                } else {
                    let p = modal.find('input#account_name').val(data.code + " " + data.account_name);
                    return data.text;
                }
            },
        });

        /** On select expense category, added to expense lines table */
        modal.find('select#expense_search').on('select2:select', function (d) {
            let cat = d.params.data;
            let table = modal.find("table#expense_lines tbody");

            let tr = `
                <tr>
                    <td>
                        <input type="hidden" data-name="id" value="0">
                        <input type="hidden" data-name="category_id" value="${cat.cat_id}"/>
                        ${cat.text}
                    </td>
                    <td>${cat.code +' '+ cat.account_name}</td>
                    <td>
                        <input type="text" data-name="line_total" class="form-control input-sm input_number" />
                    </td>
                    <td class="text-center">
                        <i class="fa fa-times del-exp text-danger cursor-pointer"></i>
                    </td>
                </tr>
            `;

            table.append(tr);
            modal.find('select#expense_search').val("").trigger('change');
        });

        /** On input amount change */
        $(document).on('change', 'table#expense_lines input.input_number', function () {
            sum_lines(modal.find('table#expense_lines tbody tr'), modal.find('input#amount'));
        });

        /** Apply datetimepicker to expense date and validate it's closed */
        modal.find('input#expense_transaction_date').datetimepicker({
            format: moment_date_format,
            ignoreReadonly: true
        }).on("dp.change", function (e) {
            if (e.oldDate !== e.date) {
                var date = moment(e.date).format('DD/MM/YYYY');
                $.ajax({
                    type: 'post',
                    url: '/purchases/is-closed',
                    data: {date: date},
                    success: function(data){
                        if(parseInt(data) > 0){
                            swal(LANG.notice, LANG.month_closed, "error");
                        }
                    }
                });
            };
        });

        /** Apply datetimepicker to expense document date */
        modal.find('input#expense_document_date').datetimepicker({
            format: moment_date_format,
            ignoreReadonly: true
        });

        /** On change payment condition  */
        modal.find("#payment_condition").on('change', function() {
            let val = $(this).val();
            let payment_term = modal.find('select#payment_term_id');

            if (val == "credit") {
                payment_term.attr('disabled', false);
            } else {
                payment_term.attr('disabled', true);
                payment_term.val('').trigger('change');
            }
        });

        /** On change enable exempt amount */
        modal.find('input#enable_exempt_amount').on('change', function () {
            let exempt_amount = modal.find('input#exempt_amount');

            if ($(this).prop('checked')) {
                exempt_amount.prop('readonly', false);
            } else {
                exempt_amount.prop('readonly', true);
                exempt_amount.val(null).change();
            }
        });

        /** On change supplier */
        modal.find('select#supplier_id').on('change', function () {
            var perception = modal.find('div#perception_div');
            var tax_percent = modal.find('input#tax_percent').val();

            if (tax_percent == 0) {
                perception.hide();
            } else {
                perception.show();
            }
        });

        modal.find('select#tax_percent_group, input#amount, input#exempt_amount, select#supplier_id').on('change', function() {
            let amount = __read_number(modal.find("input#amount"));
            console.log(amount);
            let exempt_amount = modal.find("input#enable_exempt_amount").prop("checked") ? (__read_number(modal.find("input#exempt_amount")) > 0 ? __read_number(modal.find("input#exempt_amount")) : 0) : 0;
            let tax_supplier_percent = modal.find("select#supplier_id :selected") && modal.find("input#tax_percent").val() != "" ? parseFloat(modal.find("input#tax_percent").val()) : 0;
            let perception = modal.find("input#perception_amount");
            let is_excluded_subject = modal.find('input#is_excluded_subject').val();

            let tax_supplier = 0;

            if (tax_supplier_percent != "0") {
                if (amount > 0) {
                    let min_amount = modal.find("select#supplier_id :selected").val() ? parseFloat(modal.find("input#tax_min_amount").val()) : 0;
                    let max_amount = modal.find("select#supplier_id :selected").val() ? parseFloat(modal.find("input#tax_max_amount").val()) : 0;

                    tax_supplier_percent = parseFloat(tax_supplier_percent);

                    tax_supplier = calc_contact_tax(amount, min_amount, max_amount, tax_supplier_percent);
                    __write_number(perception, tax_supplier, false, 4);
                }

            } else{
                __write_number(perception, tax_supplier, false, 4);
            }

            if (modal.find('select#tax_percent_group').val() != "nulled") {
                let percent = modal.find('select#tax_percent_group :selected').data('tax_percent');
                let total = (amount * ((percent / 100) + 1)) + exempt_amount + tax_supplier;
                let impuesto = total - amount - exempt_amount - tax_supplier;

                __write_number(modal.find("input#final_total"), total, false, 4);
                __write_number(modal.find("input#iva"), impuesto, false, 4);

            } else if (modal.find('input#amount') != "" || modal.find('input#exempt_amount') != "") {
                __write_number(modal.find("input#final_total"), (amount + exempt_amount + tax_supplier), false, 4);
                modal.find("input#iva").val('0.0');

            } else {
                modal.find("input#final_total").val('0.0');
                modal.find("input#iva").val('0.0');
            }

            if(is_excluded_subject == 1){
                if(modal.find('input#amount') != ""){
                    modal.find("input#enable_exempt_amount").attr('disabled', true);
                    __write_number($("#excluded_subject_amount"), amount * 0.10);
                    __write_number($("#final_total"), amount + amount * 0.10);
                } else {
                    modal.find("input#final_total").val('0.0');
                    modal.find("input#exempt_amount").val(null);
                    modal.find("input#excluded_subject_amount").val(null);
                    modal.find("input#enable_exempt_amount").attr('disabled', false);
                }
            }else{
                modal.find("input#excluded_subject_amount").val(null);
                modal.find("input#exempt_amount").val(null);
                modal.find("input#enable_exempt_amount").attr('disabled', false);
            }
        });
    });

    /**
     * Sum expense lines
     * 
     * @param {*} lines row from expense lines table
     * @param {*} input subtotal input to set total rows value
     * 
     * @return void
     */
    function sum_lines(lines, input) {
        let amount = 0;
        $.each(lines, function (index, tr) {
            amount += __read_number($(tr).find('input.input_number'));

            update_index(index, tr);
        });

        __write_number(input, amount, false, 4);
        input.trigger('change');
    }

    /**
     * Update name indexes for inputs
     * 
     * @param {number} integer row index
     * @param {*} tr row
     * @return void
     */
    function update_index(index, tr) {
        let inputs = $(tr).find('input');

        $.each(inputs, function (i, input) {
            $(input).attr('name', 'expense_lines['+ index +']['+ $(input).data('name') +']');
        });
    }

    function recalculate(modal){
        let is_exempt = modal.find('input#is_exempt').val();
        let is_excluded_subject = modal.find('input#is_excluded_subject').val();
        let amount = __read_number(modal.find("input#amount"));
        let exempt_amount = modal.find('input#enable_exempt_amount').prop('checked') ? (__read_number(modal.find('input#exempt_amount')) > 0 ? __read_number(modal.find('input#exempt_amount')) : 0) : 0;

        if(is_exempt == 0){
            modal.find('select#tax_percent_group').attr('disabled', false);
            if(modal.find('input#amount') != "" || modal.find('input#exempt_amount') != ""){
                __write_number($("#final_total"), amount + exempt_amount);
                modal.find("input#iva").val('0.0');
            } else {
                modal.find("input#final_total").val('0.0');
                modal.find("input#iva").val('0.0');
            }
        }else{
            modal.find('select#tax_percent_group').attr('disabled', true);
            modal.find('select#tax_percent_group').val('nulled').change();
            modal.find("input#iva").val('0.0');
        }

        if(is_excluded_subject == 1){
            modal.find('select#tax_percent_group').attr('disabled', true);
            modal.find('select#tax_percent_group').val('nulled').change();
            modal.find("input#iva").val('0.0');

            if(modal.find('input#amount') != ""){
                __write_number($("#excluded_subject_amount"), amount * 0.10);
                let excluded_subject_amount = __read_number(modal.find('input#excluded_subject_amount'));
                __write_number($("#final_total"), amount + excluded_subject_amount);
            } else {
                modal.find("input#final_total").val('0.0');
                modal.find("input#excluded_subject_amount").val(null);
                modal.find("input#exempt_amount").val(null);
                modal.find("input#enable_exempt_amount").attr('disabled', true);
            }
        }else{
            modal.find("input#exempt_amount").val(null);
            modal.find("input#enable_exempt_amount").attr('disabled', false);
            modal.find("input#excluded_subject_amount").val(null);
        }
    }

    /**
     * Calc contact taxes
     * 
     * @param {*} amount 
     * @param {*} min_amount 
     * @param {*} max_amount 
     * @param {*} tax_percent 
     * @returns number
     */
    function calc_contact_tax(amount, min_amount, max_amount, tax_percent){
        var tax_amount = 0;

        // If has min o max amount
        if (min_amount || max_amount) {
            // if has min and max amount
            if (min_amount && max_amount) {
                if (amount >= min_amount && amount <= max_amount) {
                    tax_amount = amount * tax_percent;
                }
            // If has only min amount
            } else if (min_amount && ! max_amount) {
                if (amount >= min_amount) {
                    tax_amount = amount * tax_percent;
                }
            // If has only max amount
            } else if (! min_amount && max_amount) {
                if (amount <= max_amount) {
                    tax_amount = amount * tax_percent;
                }
            }
        // If has none tax
        } else {
            tax_amount = amount * tax_percent;
        }

        return tax_amount;
    }
});
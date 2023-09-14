$(function () {
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

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

        //get expense categories
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
            console.log(tr);

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
});
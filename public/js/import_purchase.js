$(function () {
    //Date picker
    $("#transaction_date").datepicker({
        autoclose: true,
        format: datepicker_date_format,
    });

    //get suppliers
    $("#supplier_id").select2({
        ajax: {
            url: "/purchases/get_suppliers",
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
        escapeMarkup: function (m) {
            return m;
        },
        templateResult: function (data) {
            if (!data.id) {
                return data.text;
            }
            let html = data.text + " (<b>Business: </b>" + data.business_name + ")";
            return html;
        },
    });

    /** Validate purchase date */
    $(document).on('change', 'input#transaction_date', function(){
        var date = $(this).val();

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
    });

    //Quick add supplier
    $(document).on("click", ".add_new_supplier", function () {
        $("#supplier_id").select2("close");
        let name = $(this).data("name");
        $(".contact_modal").find("input#name").val(name);
        $(".contact_modal")
            .find("select#contact_type")
            .val("supplier")
            .closest("div.contact_type_div")
            .addClass("hide");
        $(".contact_modal").modal("show");
    });

    $("form#quick_add_contact")
        .submit(function (e) {
            e.preventDefault();
        })
        .validate({
            rules: {
                contact_id: {
                    remote: {
                        url: "/contacts/check-contact-id",
                        type: "post",
                        data: {
                            contact_id: function () {
                                return $("#contact_id").val();
                            },
                            hidden_id: function () {
                                if ($("#hidden_id").length) {
                                    return $("#hidden_id").val();
                                } else {
                                    return "";
                                }
                            },
                        },
                    },
                },
            },
            messages: {
                contact_id: {
                    remote: LANG.contact_id_already_exists,
                },
            },
            submitHandler: function (form) {
                $(form).find('button[type="submit"]').attr("disabled", true);
                let data = $(form).serialize();
                $.ajax({
                    method: "POST",
                    url: $(form).attr("action"),
                    dataType: "json",
                    data: data,
                    success: function (result) {
                        if (result.success == true) {
                            $("select#supplier_id").append(
                                $("<option>", {
                                    value: result.data.id,
                                    text: result.data.name,
                                })
                            );
                            $("select#supplier_id").val(result.data.id).trigger("change");
                            $("div.contact_modal").modal("hide");
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            },
        });
    $(".contact_modal").on("hidden.bs.modal", function () {
        $("form#quick_add_contact")
            .find('button[type="submit"]')
            .removeAttr("disabled");
        $("form#quick_add_contact")[0].reset();
    });

    //Add products
    if ($("#search_product").length > 0) {
        $("#search_product")
            .autocomplete({
                source: "/purchases/get_products",
                minLength: 2,
                response: function (event, ui) {
                    if (ui.content.length == 1) {
                        ui.item = ui.content[0];
                        $(this)
                            .data("ui-autocomplete")
                            ._trigger("select", "autocompleteselect", ui);
                        $(this).autocomplete("close");
                    }
                },
                select: function (event, ui) {
                    $(this).val(null);
                    get_purchase_entry_row(ui.item.product_id, ui.item.variation_id);
                },
            })
            .autocomplete("instance")._renderItem = function (ul, item) {
                return $("<li>")
                    .append("<div>" + item.text + "</div>")
                    .appendTo(ul);
            };
    }

    $(document).on("click", ".remove_purchase_entry_row", function () {
        swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((value) => {
            if (value) {
                $(this).closest("tr").remove();
                update_line_total();
                update_row_index();
            }
        });
    });

    $("table#purchase_table tbody").on(
        "click",
        "a.delete-purchase",
        function (e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    let href = $(this).attr("href");
                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        success: function (result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                purchase_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        }
    );

    $(document).on("click", "button#submit_purchase_form", function (e) {
        e.preventDefault();

        //Check if product is present or not.
        if ($("table#purchase_entry_table tbody tr").length <= 0) {
            toastr.warning(LANG.no_products_added);
            $("input#search_product").select();
            return false;
        }

        // Verify that the client selected has nit or nrc
        if ($("input#verify_tax_reg").val() == 0) {
            // Check if the client has nit and nrc
            swal(LANG.notice, LANG.contact_has_no_nit_nrc, "error");
            return false;
        }

        $("form#add_purchase_form").validate({
            rules: {
                ref_no: {
                    remote: {
                        url: "/purchases/check_ref_number",
                        type: "post",
                        data: {
                            ref_no: function () {
                                return $("#ref_no").val();
                            },
                            contact_id: function () {
                                return $("#supplier_id").val();
                            },
                            purchase_id: function () {
                                if ($("#purchase_id").length > 0) {
                                    return $("#purchase_id").val();
                                } else {
                                    return '';
                                }
                            },
                        },
                    },
                },
            },
            messages: {
                ref_no: {
                    remote: LANG.ref_no_already_exists,
                },
            },
        });

        if ($("form#add_purchase_form").valid()) {
            $("form#add_purchase_form").submit();
        }
    });

    $(
        "input#deconsolidation_amount, input#dai_amount, input#vat_amount, input#freight_amount, select#freight, input#external_storage, input#internal_storage , input#local_freight_amount, input#customs_procedure_amount, input#subtotal"
    ).on("change", function () {
        update_line_total();
    });

    /** Payment due */
    $("input#amount_0, input#final_total").on("change", function () {
        update_payment_due();
    });

    /* GET Warehouses by Locations */
    $("#location_id").on("change", function () {
        if ($("#location_id").val() > 0) {
            let location_id = $(this).val();
            let warehouse_id = $("select#warehouse_id");

            $.ajax({
                method: "GET",
                url: "/warehouses/get_warehouses/" + location_id,
                dataType: "json",
                success: function (warehouses) {
                    $("select#warehouse_id").attr("disabled", false);
                    warehouse_id
                        .empty()
                        .append(new Option(LANG.select_please, 0, true, true));
                    $.each(warehouses, function (i, w) {
                        warehouse_id.append(new Option(w.name, w.id, false, false));
                    });
                },
            });
        }
    });
});

function update_table_sr_number() {
    let sr_number = 1;
    $("table#purchase_entry_table tbody")
        .find(".sr_number")
        .each(function () {
            $(this).text(sr_number);
            sr_number++;
        });
}

function get_purchase_entry_row(product_id, variation_id) {
    let purchase_type = $("input#purchase_type").val();
    if (product_id) {
        let row_count = $("#row_count").val();
        $.ajax({
            method: "POST",
            url: "/purchases/get_purchase_entry_row",
            dataType: "html",
            data: {
                product_id: product_id,
                row_count: row_count,
                variation_id: variation_id,
                purchase_type: purchase_type,
            },
            success: function (result) {
                $(result)
                    .find(".purchase_quantity")
                    .each(function () {
                        row = $(this).closest("tr");
                        $("#purchase_entry_table tbody").append(row);
                    });
                if ($(result).find(".purchase_quantity").length) {
                    $("#row_count").val(
                        $(result).find(".purchase_quantity").length + parseInt(row_count)
                    );
                }

                $("table#purchase_entry_table tbody tr").each(function () {
                    $(this)
                        .find("input#quantity")
                        .on("change", function () {
                            update_line_total();
                        });

                    $(this)
                        .find("input#weight_kg")
                        .on("change", function () {
                            update_line_total();
                        });

                    $(this)
                        .find("input#price")
                        .on("change", function () {
                            update_line_total();
                        });

                    $(this)
                        .find("input#line_transfer_fee")
                        .on("change", function () {
                            update_line_total();
                        });

                    $(this)
                        .find("select#line_freight_inc")
                        .on("change", function () {
                            update_line_total();
                        });
                });

                update_table_sr_number();
                update_row_index();
            },
        });
    }
}

function update_line_total() {
    let freight_inc = $("select#freight").val();
    let freight = __read_number($("input#freight_amount"), false);
    let deconsolidation = __read_number($("input#deconsolidation_amount"), false);
    let dai = __read_number($("input#dai_amount"), false);
    let tax = __read_number($("input#vat_amount"), false);
    let external_storage = __read_number($("input#external_storage"), false);
    let internal_storage = __read_number($("input#internal_storage"), false);
    let local_freight = __read_number($("input#local_freight_amount"), false);

    let custom_procedure = __read_number(
        $("input#customs_procedure_amount"),
        false
    );
    let total_weight = get_total_weight();
    let row_freight_inc = get_row_freight_inc();
    let decons_per_weight = deconsolidation / total_weight;
    let dai_per_weight = dai / total_weight;
    let tax_per_weight = tax / total_weight;
    let freight_per_weight = freight / row_freight_inc;
    let local_freight_per_weight = local_freight / total_weight;
    let external_storage_per_weigth = external_storage / total_weight;
    let internal_percent = internal_storage / 100 + 1;
    let custom_procedure_per_weight = custom_procedure / total_weight;

    //** Calculate purchase row values */
    $("#purchase_entry_table tbody")
        .find("tr")
        .each(function () {
            let qty = __read_number($(this).find("input#quantity"), false);
            let price = __read_number($(this).find("input#price"), false);
            let subtotal = qty * price;
            let line_weight_kg = __read_number(
                $(this).find("input#weight_kg"),
                false
            );
            let line_freight_inc = $(this).find("select#line_freight_inc").val();

            /** Transfer fee */
            let transfer_fee = __read_number(
                $(this).find("input#line_transfer_fee"),
                false
            );

            /** Freight */
            let line_freight = 0;
            if (freight_inc == "included") {
                if (line_freight_inc == "yes") {
                    let line_freight = line_weight_kg * freight_per_weight;

                    $(this)
                        .find("span#line_freight_amount_text")
                        .text(__currency_trans_from_en(line_freight, true, true));
                    __write_number(
                        $(this).find("input#line_freight_amount"),
                        line_freight,
                        false,
                        4
                    );
                } else {
                    $(this)
                        .find("span#line_freight_amount_text")
                        .text(__currency_trans_from_en(0, true, true));
                    __write_number(
                        $(this).find("input#line_freight_amount"),
                        0,
                        false,
                        2
                    );
                }
            } else {
                $(this)
                    .find("span#line_freight_amount_text")
                    .text(__currency_trans_from_en(0, true, true));
                __write_number($(this).find("input#line_freight_amount"), 0, false, 2);
            }

            /** Deconsolidation */
            let line_decons = line_weight_kg * decons_per_weight;
            $(this)
                .find("span#line_deconsolidation_amount_text")
                .text(__currency_trans_from_en(line_decons, true, true));
            __write_number(
                $(this).find("input#line_deconsolidation_amount"),
                line_decons,
                false,
                4
            );

            /** DAI */
            let line_dai = line_weight_kg * dai_per_weight;
            $(this)
                .find("span#line_dai_amount_text")
                .text(__currency_trans_from_en(line_dai, true, true));
            __write_number($(this).find("input#line_dai_amount"), line_dai, false, 4);

            /** Tax */
            let line_tax = line_weight_kg * tax_per_weight;
            $(this)
                .find("span#line_tax_amount_text")
                .text(__currency_trans_from_en(line_tax, true, true));
            __write_number($(this).find("input#line_tax_amount"), line_tax, false, 4);

            /**Storage external */
            let line_external_storage = line_weight_kg * external_storage_per_weigth;
            $(this)
                .find("span#line_external_storage_text")
                .text(__currency_trans_from_en(line_external_storage, true, true));
            __write_number(
                $(this).find("input#line_external_storage"),
                line_external_storage,
                false,
                4
            );

            /** Local freight */
            let line_local_freight = line_weight_kg * local_freight_per_weight;
            $(this)
                .find("span#line_local_freight_amount_text")
                .text(__currency_trans_from_en(line_local_freight, true, true));
            __write_number(
                $(this).find("input#line_local_freight_amount"),
                line_local_freight,
                false,
                4
            );

            /** Customs procedure */
            let line_custom_procedure = line_weight_kg * custom_procedure_per_weight;
            $(this)
                .find("span#line_customs_procedure_amount_text")
                .text(__currency_trans_from_en(line_custom_procedure, true, true));
            __write_number(
                $(this).find("input#line_customs_procedure_amount"),
                line_custom_procedure,
                false,
                4
            );

            /** Subtotal */
            let line_total =
                subtotal +
                transfer_fee +
                line_freight +
                line_decons +
                line_dai +
                line_tax +
                line_external_storage +
                line_local_freight +
                line_custom_procedure;
            let unit_cost = line_total / qty;
            unit_cost =
                internal_percent > 0 ? internal_percent * unit_cost : unit_cost;
            $(this)
                .find("span#purchase_price_text")
                .text(__currency_trans_from_en(unit_cost, true, true));
            __write_number($(this).find("input#purchase_price"), unit_cost, false, 4);
        });

    sum_total();
}

/** Calculate tfoot totals */
function sum_total() {
    let total_transfer_fee = 0;
    let total_quantity = 0;
    let total_weight_kg = 0;
    let total_fob_price = 0;
    let total_freight = 0;
    let total_deconsolidation = 0;
    let total_dai = 0;
    let total_vat = 0;
    // let total_storage = 0;
    let total_external_storage = 0;
    let total_local_freight = 0;
    let total_customs_procedure = 0;
    let final_total = 0;

    /** Sum each value from columns */
    $("#purchase_entry_table tbody")
        .find("tr")
        .each(function () {
            let qty = __read_number($(this).find("input#quantity"), false);
            let price = __read_number($(this).find("input#price"), false);
            let subtotal = qty * price;

            total_transfer_fee += __read_number(
                $(this).find("input#line_transfer_fee"),
                false
            );
            total_quantity += __read_number($(this).find("input#quantity"), false);
            total_weight_kg += __read_number($(this).find("input#weight_kg"), false);
            total_fob_price += __read_number($(this).find("input#price"), false);
            total_freight += __read_number(
                $(this).find("input#line_freight_amount"),
                false
            );
            total_deconsolidation += __read_number(
                $(this).find("input#line_deconsolidation_amount"),
                false
            );
            total_dai += __read_number($(this).find("input#line_dai_amount"), false);
            total_vat += __read_number($(this).find("input#line_tax_amount"), false);
            // total_storage += __calculate_amount("percentage", __read_number($(this).find("input#storage_percentage"), false), subtotal);
            total_external_storage += __read_number(
                $(this).find("input#line_external_storage")
            );
            total_local_freight += __read_number(
                $(this).find("input#line_local_freight_amount"),
                false
            );
            total_customs_procedure += __read_number(
                $(this).find("input#line_customs_procedure_amount"),
                false
            );
        });

    /** Print each column value on tfoot */
    $("span#total_transfer_fee_text").text(
        __currency_trans_from_en(total_transfer_fee, true, true)
    );
    $("span#total_quantity_text").text(
        __currency_trans_from_en(total_quantity, false, false)
    );
    $("span#total_weight_kg_text").text(
        __currency_trans_from_en(total_weight_kg, false, false)
    );
    $("span#total_fob_price_text").text(
        __currency_trans_from_en(total_fob_price, true, true)
    );
    $("span#total_freight_text").text(
        __currency_trans_from_en(total_freight, true, true)
    );
    $("span#total_deconsolidation_text").text(
        __currency_trans_from_en(total_deconsolidation, true, true)
    );
    $("span#total_dai_text").text(
        __currency_trans_from_en(total_dai, true, true)
    );
    $("span#total_vat_text").text(
        __currency_trans_from_en(total_vat, true, true)
    );
    // $("span#total_storage_text").text(__currency_trans_from_en(total_storage, true, true));
    $("span#total_external_storage").text(
        __currency_trans_from_en(total_external_storage, true, true)
    );
    $("span#total_local_freight_text").text(
        __currency_trans_from_en(total_local_freight, true, true)
    );
    $("span#total_customs_procedure_text").text(
        __currency_trans_from_en(total_customs_procedure, true, true)
    );

    /** Totals */
    let total_before_tax = __read_number($("input#subtotal"), false);
    let vat_amount = __read_number($("input#vat_amount"), false);
    final_total = total_before_tax + vat_amount;

    __write_number($("input#final_total"), final_total, false, 4);
    $("span#final_total_text").text(
        __currency_trans_from_en(final_total, true, true)
    );
    $("input#final_total").trigger("change");
}

/** Update payment due */
function update_payment_due() {
    let final_total = __read_number($("input#final_total"), false);
    let paid_amount = __read_number($("input#amount_0"), false);
    let payment_due = final_total - paid_amount;

    $("span#payment_due").text(__currency_trans_from_en(payment_due, true, true));
}

function get_total_weight() {
    let total_weight = 0;

    $("table#purchase_entry_table tbody tr").each(function () {
        let line_weight = $(this).find("input#weight_kg").val();
        line_weight = parseFloat(line_weight);

        total_weight += isNaN(line_weight) ? 0 : line_weight;
    });

    return total_weight;
}

function get_row_freight_inc() {
    let row_weight_freight_inc = 0;

    $("table#purchase_entry_table tbody tr").each(function () {
        let line_freight = $(this).find("select#line_freight_inc").val();
        let line_weight = $(this).find("input#weight_kg").val();
        line_weight = parseFloat(line_weight);

        if (line_freight == "yes") {
            row_weight_freight_inc += isNaN(line_weight) ? 0 : line_weight;
        }
    });

    return row_weight_freight_inc;
}

function update_row_index() {
    $("table#purchase_entry_table tbody tr").each(function (index) {
        $(this)
            .find("input")
            .each(function () {
                let id = $(this).attr("id");
                $(this).attr("name", "purchases[" + index + "][" + id + "]");
            });

        $(this)
            .find("select")
            .each(function () {
                let id = $(this).attr("id");
                $(this).attr("name", "purchases[" + index + "][" + id + "]");
            });
    });
}

//add events to inputs generated by a foreach PHP
function update_inputs_index() {
    $("input#quantity").on("change", function () {
        update_line_total();
    });

    $("input#weight_kg").on("change", function () {
        update_line_total();
    });

    $("input#price").on("change", function () {
        update_line_total();
    });

    $("input#line_transfer_fee").on("change", function () {
        update_line_total();
    });

    $("select#line_freight_inc").on("change", function () {
        update_line_total();
    });
}

/** Check if the contact has tax number and registration number*/
$("select#supplier_id").on("change", function () {
    verify_t_r();
});

function verify_t_r() {
    let contact_id = $("select#supplier_id").val();
    let route = "/contact/verify-tax-number-reg-number";
    if (contact_id != "") {
        $.ajax({
            method: "get",
            url: route,
            data: {
                contact_id: contact_id,
            },
            dataType: "json",
            success: function (result) {
                if (result.success == false) {
                    swal(LANG.notice, LANG.contact_has_no_nit_nrc, "error");
                    $("input#verify_tax_reg").val(0);
                } else {
                    $("input#verify_tax_reg").val(1);
                }
            },
        });
    }
}
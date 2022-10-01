// Number of decimals in POS
decimals_in_purchases = $('#decimals_in_purchases').length > 0 ? $('#decimals_in_purchases').val() : 6;

// Number of decimal places to store and use in calculations
price_precision = $('#price_precision').length > 0 ? $('#price_precision').val() : 6;

$(document).ready(function() {

    if($('input#iraqi_selling_price_adjustment').length > 0){
        iraqi_selling_price_adjustment = true;
    } else {
        iraqi_selling_price_adjustment = false;
    }

    /**
     * --------------------------------------------------------------------------------
     * PURCHASE TABLE
     * --------------------------------------------------------------------------------
     */
    
    // Purchase datatable.
    purchase_table = $('#purchase_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '/purchases',
            data: function(d) {
                d.purchase_type = $('#purchase-type').val();
                d.payment_status = $('#payment-status').val();
            }
        },
        columnDefs: [ {
            "targets": [7,8],
            "orderable": false,
            "searchable": false
        } ],
        columns: [
            { data: 'transaction_date', name: 'transaction_date'  },
            { data: 'ref_no', name: 'ref_no'},
            { data: 'location_name', name: 'BS.name'},
            { data: 'name', name: 'contacts.name'},
            { data: 'purchase_type', name: 'purchase_type'},
            { data: 'status', name: 'status'},
            { data: 'payment_status', name: 'payment_status'},
            { data: 'final_total', name: 'final_total'},
            { data: 'payment_due', name: 'payment_due'},
            { data: 'action', name: 'action'}
        ],
        "fnDrawCallback": function (oSettings) {
            var total_purchase = sum_table_col($('#purchase_table'), 'final_total');
            $('#footer_purchase_total').text(total_purchase);

            var total_due = sum_table_col($('#purchase_table'), 'payment_due');
            $('#footer_total_due').text(total_due);

            var total_purchase_return_due = sum_table_col($('#purchase_table'), 'purchase_return');
            $('#footer_total_purchase_return_due').text(total_purchase_return_due);

            $('#footer_status_count').html(__sum_status_html($('#purchase_table'), 'status-label'));
            
            $('#footer_payment_status_count').html(__sum_status_html($('#purchase_table'), 'payment-status-label'));
            
            __currency_convert_recursively($('#purchase_table'));
        },
        createdRow: function( row, data, dataIndex ) {
            $( row ).find('td:eq(5)').attr('class', 'clickable_td');
        }
    });

    // On change of purchase-type select
    $('select#purchase-type').on('change', function () {
        purchase_table.ajax.reload();
    });

    // On change of payment-status select
    $('select#payment-status').on('change', function () {
        purchase_table.ajax.reload();
    });

    // On click of delete-purchase button.
    $('table#purchase_table tbody').on('click', 'a.delete-purchase', function(e){
        e.preventDefault();
        swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).attr('href');
                $.ajax({
                    method: "DELETE",
                    url: href,
                    dataType: "json",
                    success: function(result){
                        if(result.success == true){
                            toastr.success(result.msg);
                            purchase_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            }
        });
    });

    /**
     * --------------------------------------------------------------------------------
     * FORM FIELDS
     * --------------------------------------------------------------------------------
     */

    // Date picker
    $('#transaction_date').datepicker({
        autoclose: true,
        format: datepicker_date_format
    });

    // Date picker
    $('#apportionment_date').datepicker({
        autoclose: true,
        format: datepicker_date_format
    });

    // Date picker
    $('#document_date').datepicker({
        autoclose: true,
        format: datepicker_date_format
    });
    
    // Validate purchase date on change of transaction_date input.
    $(document).on('change', 'input#transaction_date', function() {
        var date = $(this).val();
        $("input#document_date").val(date);

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

    // On change of payment_condition select.
    $("#payment_condition").on('change', function(){
        if($("#payment_condition").val() == "credit"){
            $('#payment_terms').attr('disabled', false);
            $('#amount_0').attr('readonly', true);
        }else{
            $('#payment_terms').attr('disabled', true);
            $("select#payment_terms").prop('selectedIndex', 0).change();
            $('#amount_0').attr('readonly', false);
        }

        calcTaxes();
    });

    // Get warehouses by locations.
    $("#location_id").on('change', function(){
        if($("#location_id").val() > 0){
            var location_id = $(this).val();
            var warehouse_id = $("select#warehouse_id");
            
            $.ajax({
                method: "GET",
                url: "/warehouses/get_warehouses/" + location_id,
                dataType: "json",
                success: function(warehouses){
                    $("select#warehouse_id").attr('disabled', false);
                    warehouse_id.empty()
                    .append(new Option(LANG.select_please, '', true, true))
                    $.each(warehouses, function(i, w){
                        warehouse_id.append(new Option(w.name, w.id, false, false));
                    });
                }
            });
        }
    });

    /**
     * Events for purchase return for discount
     * @author Arquímides Martínez
     */
    $(document).on('click', 'a.return_discount', function(e){
        e.preventDefault();
        
        /** show modal */
        $("div.purchase_return_discount").load($(this).attr('href'), function(){
            var modal = $(this);
            modal.modal('show');

            modal.find('input.date').datetimepicker({
                format: moment_date_format,
                ignoreReadonly: true
            });

            $(document).on('change', 'input#subtotal, select#taxes', function(){
                let subtotal = __read_number(modal.find('input#subtotal'));
                let tax_percent = parseFloat(modal.find('select#taxes :selected').data('tax_percent'));
                let vat_amount = modal.find('input#vat_amount');
                let final_total = modal.find('input#final_total');

                if (subtotal) {
                    if (tax_percent) {
                        let tax_amount = subtotal * (tax_percent/100);
                        __write_number(vat_amount, tax_amount, false, price_precision);
                        __write_number(final_total, (subtotal + tax_amount), false, price_precision);

                    } else {
                        __write_number(vat_amount, '0.00', false, price_precision);
                        __write_number(final_total, subtotal, false, price_precision);
                    }
                } else {
                    __write_number(vat_amount, '0.00', false, price_precision);
                    __write_number(final_total, '0.00', false, price_precision);
                }
            });

            $(document).on('change', 'input#final_total', function(){
                let subtotal = modal.find('input#subtotal');
                let tax_percent = parseFloat(modal.find('select#taxes :selected').data('tax_percent'));
                let vat_amount = modal.find('input#vat_amount');
                let final_total = __read_number(modal.find('input#final_total'));
                
                if ($(subtotal).val()) {
                    if (tax_percent) {
                        let subtotal_amount = (final_total / (1 + (tax_percent/100)));
                        let tax_amount = final_total - subtotal_amount;

                        __write_number(vat_amount, tax_amount, false, price_precision);
                        __write_number(subtotal, subtotal_amount, false, price_precision);

                    } else {
                        __write_number(vat_amount, '0.00', false, price_precision);
                        __write_number(subtotal, final_total, false, price_precision);
                    }
                } else {
                    __write_number(vat_amount, '0.00', false, price_precision);
                    __write_number(subtotal, final_total, false, price_precision);
                }
            });

            modal.find('form').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                $(this).find('button[type="submit"]').attr('disabled', false);
                var data = form.serialize();

                $.ajax({
                    method: "POST",
                    url: form.attr("action"),
                    dataType: "json",
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            purchase_table.ajax.reload();
                            modal.modal('hide');
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        form.find('button[type="submit"]').removeAttr('disabled');
                    }
                });
            });
        });
    });

    // On change of chk_import_expense checkbox.
    $(document).on('change', '#chk_import_expense', function() {
        if ($('#chk_import_expense').is(':checked')) {
            $('.import_expense_box').show();
            $('.col-import-expenses').show();

            if ($("#purchase_entry_table").length > 0) {
                calculate_import_expenses_purchases();
                calculate_sub_total_rows();
            }

        } else {
            $('.import_expense_box').hide();
            $('input.product_import_expenses').val('0.0000');
            $('.col-import-expenses').hide();
            
            if ($("#purchase_entry_table").length > 0) {
                calculate_import_expenses_purchases();
                calculate_sub_total_rows();
            }
        }
    });

    // On change of base select.
    $(document).on('change', '#base', function() {
        if ($("#purchase_entry_table").length > 0) {
            calculate_import_expenses_purchases();
            calculate_sub_total_rows();
        }

        if ($("#apportionment_table").length > 0) {
            calculate_totals();
            calculate_expenses_taxes(false);
        }
    });

    /**
     * --------------------------------------------------------------------------------
     * SUPPLIER FIELD
     * --------------------------------------------------------------------------------
     */
    
    // Get suppliers.
    $('#supplier_id').select2({
        ajax: {
        url: '/purchases/get_suppliers',
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
        escapeMarkup: function(m) {
            return m;
        },
        templateResult: function(data){
        if (!data.id) {
            return data.text;
        }
        var html = data.text + ' (<b>' + LANG.code + ': </b>' + data.contact_id + ' - <b>' + LANG.business + ': </b>' + data.business_name + ')';
        return html;
        },
        language: {
                noResults: function(){
                    var name = $("#supplier_id").data("select2").dropdown.$search.val();
                    return '<button type="button" data-name="' + name + '" class="btn btn-link add_new_supplier"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>&nbsp; ' + __translate('add_name_as_new_supplier', {'name': name}) +'</button>';
                }
        },
        templateSelection: function(data) {
            if (!data.id) {
                $('#supplier_name').val('');
                return data.text;
            }
            // If it's a new supplier
            if (!data.contact_id) {
                return data.text;
            // If a provider has been selected
            } else {
                $('#supplier_name').val(data.text);
                return data.contact_id || data.text;
            }
        },
    });

    // On select of supplier_id select.
    $('select#supplier_id').on('select2:select', function (e) {
        var data = e.params.data;
        $("input#contact_tax_id").val(data.tax_group_id);
        $("input#perception_percent").val(data.perception_percent);
        $("input#tax_min_amount").val(data.min_amount);
        $("input#tax_max_amount").val(data.max_amount);

        calcTaxes();
        calcTaxProducts();
    });
    
    // Quick add supplier on click of add_new_supplier class button.
    $(document).on('click', '.add_new_supplier', function(){
        $("#supplier_id").select2("close");
        var name = $(this).data('name');
        $('.contact_modal').find('input#name').val(name);
        $('.contact_modal').find('select#contact_type').val('supplier').closest('div.contact_type_div').addClass('hide');
        $('.contact_modal').modal('show');
    });

    // On submit of quick_add_contact form.
    $("form#quick_add_contact").submit(function(e){
        e.preventDefault();
    }).validate({
        rules: {
            contact_id: {
                remote: {
                    url: "/contacts/check-contact-id",
                    type: "post",
                    data: {
                        contact_id: function() {
                            return $( "#contact_id" ).val();
                        },
                        hidden_id: function() {
                            if($('#hidden_id').length){
                                return $('#hidden_id').val();
                            } else {
                                return '';
                            }
                        }

                    }
                }
            }
        },
        messages:{
            contact_id: {
                remote: LANG.contact_id_already_exists
            }
        },
        submitHandler: function(form) {
            $(form).find('button[type="submit"]').attr('disabled', true);
            var data = $(form).serialize();
            $.ajax({
                method: "POST",
                url: $(form).attr("action"),
                dataType: "json",
                data: data,
                success: function(result){
                    if(result.success == true){
                        /*
                        var newOption = new Option(result.data.contact_id, result.data.id, true, true);
                        $('select#supplier_id').append(newOption).trigger('change.select2');
                        */
                        $("select#supplier_id").append($('<option>', { value: result.data.id, text: result.data.contact_id }));
                        $('select#supplier_id').val(result.data.id).trigger("change");
                        $('#supplier_name').val(result.data.name);
                        $('div.contact_modal').modal('hide');
                        toastr.success(result.msg);
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        }
    });

    // On hidden of contact_modal class modal.
    $('.contact_modal').on('hidden.bs.modal', function () {
        $('form#quick_add_contact').find('button[type="submit"]').removeAttr('disabled');
        $('form#quick_add_contact')[0].reset();
    });

    /**
     * --------------------------------------------------------------------------------
     * PRODUCT BAR
     * --------------------------------------------------------------------------------
     */
    
    // Add product from search bar.
    if ($("#search_product").length > 0) {
        $("#search_product").autocomplete({
            source: "/purchases/get_products",
            minLength: 2,           
            response: function(event,ui) {
                if (ui.content.length == 1)
                {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                } else if (ui.content.length == 0)
                {
                    var term = $(this).data('ui-autocomplete').term;
                    swal({ 
                        title: LANG.no_products_found,
                        text: __translate('add_name_as_new_product', { 'term': term}),
                        buttons: [LANG.cancel, LANG.ok]
                    }).then((value) => {
                        if(value){
                            var container = $(".quick_add_product_modal");
                            $.ajax({
                                url: '/products/quick_add?product_name=' + term,
                                dataType: "html",
                                success: function(result){
                                    $(container).html(result).modal('show');
                                }
                            });
                        }
                    }); 
                }
            },
            select: function( event, ui ) {
                $(this).val(null);
                get_purchase_entry_row( ui.item.product_id, ui.item.variation_id );
            }
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" ).append( "<div>" + item.text + "</div>" ).appendTo( ul );
        };
    }

    /**
     * --------------------------------------------------------------------------------
     * PURCHASE ENTRY TABLE
     * --------------------------------------------------------------------------------
     */

    // On click of remove_purchase_entry_row class icon.
    $(document).on('click', '.remove_purchase_entry_row', function(){
        swal({ 
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((value) => {
            if(value){
                $(this).closest('tr').remove();
                update_table_total();
                update_table_sr_number();
                calcTaxes();
                calcTaxProducts();
            }
        });
    });

    // On change of purchase_unit_cost_without_discount input.
    $(document).on('change', '.purchase_unit_cost_without_discount', function() {
        var purchase_before_discount = __read_number($(this), true);

        var row = $(this).closest('tr');
        var discount_percent = __read_number(row.find('input.inline_discounts'), true);
        var quantity = __read_number(row.find('input.purchase_quantity'), true);

        //Calculations.
        var purchase_before_tax = parseFloat(purchase_before_discount) - __calculate_amount('percentage', discount_percent, purchase_before_discount);

        __write_number(row.find('input.purchase_unit_cost'), purchase_before_tax, false, price_precision);

        var sub_total_before_tax = quantity * purchase_before_tax;

        //Tax
        var tax_rate = parseFloat(row.find('select.purchase_line_tax_id').find(':selected').data('tax_amount'));
        var tax = __calculate_amount('percentage', tax_rate, purchase_before_tax);

        var purchase_after_tax = purchase_before_tax + tax;
        var sub_total_after_tax = quantity * purchase_after_tax;

        row.find('.row_subtotal_before_tax').text(__currency_trans_from_en(sub_total_before_tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.row_subtotal_before_tax_hidden'), sub_total_before_tax, false, price_precision);

        __write_number(row.find('input.purchase_unit_cost_after_tax'), purchase_after_tax, false, price_precision)
        row.find('.row_subtotal_after_tax').text(__currency_trans_from_en(sub_total_after_tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, false, price_precision);

        row.find('.purchase_product_unit_tax_text').text(__currency_trans_from_en(tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.purchase_product_unit_tax'), tax, false, price_precision);

        __write_number(row.find('input.row_purchase_total_amount'), sub_total_before_tax, false, price_precision);
        update_inline_profit_percentage(row);
        update_table_total();
    });

    // On change of inline_discounts input.
    $(document).on('change', '.inline_discounts', function() {
        var row = $(this).closest('tr');

        var discount_percent = __read_number($(this), true);

        var quantity = __read_number(row.find('input.purchase_quantity'), true);
        var purchase_before_discount = __read_number(row.find('input.purchase_unit_cost_without_discount'), true);

        //Calculations.
        var purchase_before_tax = parseFloat(purchase_before_discount) - __calculate_amount('percentage', discount_percent, purchase_before_discount);

        __write_number(row.find('input.purchase_unit_cost'), purchase_before_tax, false, price_precision);

        var sub_total_before_tax = quantity * purchase_before_tax;

        //Tax
        var tax_rate = parseFloat(row.find('select.purchase_line_tax_id').find(':selected').data('tax_amount'));
        var tax = __calculate_amount('percentage', tax_rate, purchase_before_tax);

        var purchase_after_tax = purchase_before_tax + tax;
        var sub_total_after_tax = quantity * purchase_after_tax;

        row.find('.row_subtotal_before_tax').text(__currency_trans_from_en(sub_total_before_tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.row_subtotal_before_tax_hidden'), sub_total_before_tax, false, price_precision);

        __write_number(row.find('input.purchase_unit_cost_after_tax'), purchase_after_tax, false, price_precision)
        row.find('.row_subtotal_after_tax').text(__currency_trans_from_en(sub_total_after_tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, false, price_precision);
        row.find('.purchase_product_unit_tax_text').text(__currency_trans_from_en(tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.purchase_product_unit_tax'), tax, false, price_precision);
        __write_number(row.find('input.row_purchase_total_amount'), sub_total_before_tax, false, price_precision);

        update_inline_profit_percentage(row);
        update_table_total();
    });

    // On change of purchase_line_tax_id select.
    $(document).on('change', 'select.purchase_line_tax_id', function() {
        var row = $(this).closest('tr');
        var purchase_before_tax = __read_number(row.find('.purchase_unit_cost'), true);
        var quantity = __read_number(row.find('input.purchase_quantity'), true);

        //Tax
        var tax_rate = parseFloat($(this).find(':selected').data('tax_amount'));
        var tax = __calculate_amount('percentage', tax_rate, purchase_before_tax);

        //Purchase price
        var purchase_after_tax = purchase_before_tax + tax;
        var sub_total_after_tax = quantity * purchase_after_tax;

        row.find('.purchase_product_unit_tax_text').text(__currency_trans_from_en(tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.purchase_product_unit_tax'), tax, false, price_precision);

        __write_number(row.find('input.purchase_unit_cost_after_tax'), purchase_after_tax, false, price_precision);

        row.find('.row_subtotal_after_tax').text(__currency_trans_from_en(sub_total_after_tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, false, price_precision);
        __write_number(row.find('input.row_purchase_total_amount'), sub_total_before_tax, false, price_precision);
        update_table_total();
    });

    // On change of purchase_unit_cost_after_tax input.
    $(document).on( 'change', '.purchase_unit_cost_after_tax', function(){
        var row = $(this).closest('tr');
        var purchase_after_tax = __read_number($(this), true);
        var quantity = __read_number(row.find('input.purchase_quantity'), true);

        var sub_total_after_tax = purchase_after_tax * quantity;

        //Tax
        var tax_rate = parseFloat(row.find('select.purchase_line_tax_id').find(':selected').data('tax_amount'));
        var purchase_before_tax = __get_principle(purchase_after_tax, tax_rate);
        var sub_total_before_tax = quantity * purchase_before_tax;
        var tax = __calculate_amount('percentage', tax_rate, purchase_before_tax);

        //Update unit cost price before discount
        var discount_percent = __read_number(row.find('input.inline_discounts'), true);
        var purchase_before_discount = __get_principle(purchase_before_tax, discount_percent, true);
        __write_number(row.find('input.purchase_unit_cost_without_discount'), purchase_before_discount, false, price_precision);
        
        row.find('.row_subtotal_after_tax').text(__currency_trans_from_en(sub_total_after_tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, false, price_precision);

        __write_number(row.find('.purchase_unit_cost'), purchase_before_tax, true, price_precision);

        row.find('.row_subtotal_before_tax').text(__currency_trans_from_en(sub_total_before_tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.row_subtotal_before_tax_hidden'), sub_total_before_tax, false, price_precision);

        row.find('.purchase_product_unit_tax_text').text(__currency_trans_from_en(tax, true, true, decimals_in_purchases));
        __write_number(row.find('input.purchase_product_unit_tax'), tax, false, price_precision);

        __write_number(row.find('input.row_purchase_total_amount'), sub_total_before_tax, false, price_precision);
        update_table_total();
    });

    // Calculate taxes on change of discount_type, discount_amount or shipping_charges inputs.
    $('#discount_type, #discount_amount, input#shipping_charges').change( function(){
        calcTaxes();
    });

    // Update correlative of purchase_entry_table.
    update_table_sr_number();
    
    // On change of mfg_date input.
    $(document).on('change', '.mfg_date', function() {
        var this_date = $(this).val();
        var this_moment = moment(this_date, moment_date_format);
        var expiry_period = parseFloat($(this).closest('td').find('.row_product_expiry').val());
        var expiry_period_type = $(this).closest('td').find('.row_product_expiry_type').val();
        if(this_date){
            if(expiry_period && expiry_period_type){
                exp_date = this_moment.add(expiry_period, expiry_period_type).format(moment_date_format);
                $(this).closest('td').find('.exp_date').datepicker('update', exp_date);
            } else {
                $(this).closest('td').find('.exp_date').datepicker('update', '');
            }
        } else {
            $(this).closest('td').find('.exp_date').datepicker('update', '');
        }
    });
    
    // Datepicker
    $('#purchase_entry_table tbody').find('.expiry_datepicker').each(function() {
        $(this).datepicker({
            autoclose: true,
            format:datepicker_date_format
        });
    });

    // On change of profit_percent input.
    $(document).on('change', '.profit_percent', function() {
        var row = $(this).closest('tr');
        var profit_percent = __read_number($(this), true);

        var purchase_unit_cost = __read_number(row.find('input.purchase_unit_cost'), true);
        var default_sell_price = parseFloat(purchase_unit_cost) + __calculate_amount('percentage', profit_percent, purchase_unit_cost);
        var exchange_rate = $('input#exchange_rate').val();
        __write_number(row.find('input.default_sell_price'), default_sell_price * exchange_rate, false, price_precision);
    });

    // On change of default_sell_price input.
    $(document).on('change', '.default_sell_price', function() {
        var row = $(this).closest('tr');
        update_inline_profit_percentage(row);
    });

    // On change of purchase_quantity input.
    $(document).on('change', '.purchase_quantity', function() {
        var row = $(this).closest('tr');
        var quantity = __read_number($(this), true);
        var purchase_before_tax = __read_number(row.find('input.purchase_unit_cost'), true);
        var purchase_after_tax = __read_number(row.find('input.purchase_unit_cost_after_tax'), true);
        let unit_cost_price = __read_number(row.find('.purchase_unit_cost'), true);

        let unit_cost_col = { total_input_id: '#purchase_total_unit_cost', new_value: quantity * unit_cost_price }
        add_sub_column_total(unit_cost_col);

        // Calculate import expense
        var import_expense = calculate_purchase_expenses(row);
        
        // Calculate DAI
        var dai_percent = __read_number(row.find('.purchase_dai_percent'), true);
        var dai_amount = dai_percent > 0 ? (quantity * purchase_before_tax + import_expense) * (dai_percent / 100) : 0;
        // var dai_amount_at = dai_percent > 0 ? (quantity * purchase_after_tax) * (dai_percent / 100) : 0;

        // Calculate sub totals
        var sub_total_before_tax = quantity * purchase_before_tax + import_expense;
        var sub_total_after_tax = quantity * purchase_after_tax + dai_amount + import_expense;

        row.find('.row_subtotal_before_tax').text(__currency_trans_from_en(sub_total_before_tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.row_subtotal_before_tax_hidden'), sub_total_before_tax, false, price_precision);

        row.find('.row_subtotal_after_tax').text(__currency_trans_from_en(sub_total_after_tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, false, price_precision);

        __write_number(row.find('input.row_purchase_total_amount'), sub_total_before_tax, false, price_precision);

        __write_number(row.find('input.purchase_import_expenses'), import_expense, false, price_precision);

        __write_number(row.find('input.purchase_dai_amount'), dai_amount, false, price_precision);

        calculate_total_bases();
        calculate_import_expenses_purchases();
        calculate_sub_total_rows();
        update_table_total();
        calcTaxes();
        calcTaxProducts()
    });

    // On change of purchase_unit_cost input.
    $(document).on( 'change', '.purchase_unit_cost', function(){
        var row = $(this).closest('tr');
        var quantity = __read_number(row.find('input.purchase_quantity'), true);
        var purchase_before_tax = __read_number($(this), true);
        let unit_cost_price = __read_number(row.find('.purchase_unit_cost'), true);

        let unit_cost_col = { total_input_id: '#purchase_total_unit_cost', new_value: quantity * unit_cost_price }
        add_sub_column_total(unit_cost_col);

        // Calculate import expense
        var import_expense = calculate_purchase_expenses(row);
        
        // Calculate DAI
        var dai_percent = __read_number(row.find('.purchase_dai_percent'), true);
        var dai_amount = dai_percent > 0 ? (quantity * purchase_before_tax + import_expense) * (dai_percent / 100) : 0;

        var sub_total_before_tax = quantity * purchase_before_tax + import_expense;

        //Update unit cost price before discount
        var discount_percent = __read_number(row.find('input.inline_discounts'), true);
        var purchase_before_discount = __get_principle(purchase_before_tax, discount_percent, true);
        __write_number(row.find('input.purchase_unit_cost_without_discount'), purchase_before_discount, false, price_precision);

        //Tax
        var tax_rate = parseFloat(row.find('select.purchase_line_tax_id').find(':selected').data('tax_amount'));
        var tax = __calculate_amount('percentage', tax_rate, purchase_before_tax);

        var purchase_after_tax = purchase_before_tax + tax;
        // dai_amount_at = dai_percent > 0 ? (quantity * purchase_after_tax) * (dai_percent / 100) : 0;
        var sub_total_after_tax = quantity * purchase_after_tax + dai_amount + import_expense;

        row.find('.row_subtotal_before_tax').text(__currency_trans_from_en(sub_total_before_tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.row_subtotal_before_tax_hidden'), sub_total_before_tax, false, price_precision);

        row.find('.purchase_product_unit_tax_text').text(__currency_trans_from_en(tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.purchase_product_unit_tax'), tax, false, price_precision);

        //row.find('.purchase_product_unit_tax_text').text( tax );
        __write_number(row.find('input.purchase_unit_cost_after_tax'), purchase_after_tax, false, price_precision)
        row.find('.row_subtotal_after_tax').text(__currency_trans_from_en(sub_total_after_tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, false, price_precision);

        __write_number(row.find('input.row_purchase_total_amount'), sub_total_before_tax, false, price_precision);

        __write_number(row.find('input.purchase_import_expenses'), import_expense, false, price_precision);

        __write_number(row.find('input.purchase_dai_amount'), dai_amount, false, price_precision);

        calculate_total_bases();
        calculate_import_expenses_purchases();
        calculate_sub_total_rows();
        update_inline_profit_percentage(row);
        update_table_total();
        calcTaxes();
        calcTaxProducts()
    });

    // On change of purchase_dai_percent input.
    $(document).on('change', '.purchase_dai_percent', function() {
        var row = $(this).closest('tr');
        var quantity = __read_number(row.find('input.purchase_quantity'), true);
        var purchase_before_tax = __read_number(row.find('input.purchase_unit_cost'), true);
        var purchase_after_tax = __read_number(row.find('input.purchase_unit_cost_after_tax'), true);
        
        // Calculate DAI
        var dai_percent = __read_number($(this), true);

        if (dai_percent < 0 || dai_percent > 100) {
            $(this).val('0.0000');
            dai_percent = 0;
        }

        // Calculate import expense
        var import_expense = calculate_purchase_expenses(row);

        var dai_amount = dai_percent > 0 ? (quantity * purchase_before_tax + import_expense) * (dai_percent / 100) : 0;
        // var dai_amount_at = dai_percent > 0 ? (quantity * purchase_after_tax) * (dai_percent / 100) : 0;

        // Calculate sub totals
        var sub_total_before_tax = quantity * purchase_before_tax + import_expense;
        var sub_total_after_tax = quantity * purchase_after_tax + dai_amount + import_expense;

        row.find('.row_subtotal_before_tax').text(__currency_trans_from_en(sub_total_before_tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.row_subtotal_before_tax_hidden'), sub_total_before_tax, false, price_precision);

        row.find('.row_subtotal_after_tax').text(__currency_trans_from_en(sub_total_after_tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, false, price_precision);

        __write_number(row.find('input.row_purchase_total_amount'), sub_total_before_tax, false, price_precision);

        __write_number(row.find('input.purchase_import_expenses'), import_expense, false, price_precision);

        __write_number(row.find('input.purchase_dai_amount'), dai_amount, false, price_precision);

        update_table_total();
        calcTaxes();
        calcTaxProducts()
    });

    // On change of import_expense_total input.
    $(document).on('change', '.purchase_product_weight', function() {
        var row = $(this).closest('tr');
        var quantity = __read_number(row.find('input.purchase_quantity'), true);
        var purchase_before_tax = __read_number(row.find('input.purchase_unit_cost'), true);
        var purchase_after_tax = __read_number(row.find('input.purchase_unit_cost_after_tax'), true);
        let weight = __read_number(row.find('.purchase_product_weight'), true);

        let weight_col = { total_input_id: '#purchase_total_weight', new_value: weight }
        add_sub_column_total(weight_col);

        // Calculate import expense
        var import_expense = calculate_purchase_expenses(row);
        
        // Calculate DAI
        var dai_percent = __read_number(row.find('.purchase_dai_percent'), true);
        var dai_amount = dai_percent > 0 ? (quantity * purchase_before_tax + import_expense) * (dai_percent / 100) : 0;
        // var dai_amount_at = dai_percent > 0 ? (quantity * purchase_after_tax) * (dai_percent / 100) : 0;

        // Calculate sub totals
        var sub_total_before_tax = quantity * purchase_before_tax + import_expense;
        var sub_total_after_tax = quantity * purchase_after_tax + dai_amount + import_expense;

        row.find('.row_subtotal_before_tax').text(__currency_trans_from_en(sub_total_before_tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.row_subtotal_before_tax_hidden'), sub_total_before_tax, false, price_precision);

        row.find('.row_subtotal_after_tax').text(__currency_trans_from_en(sub_total_after_tax, false, true, decimals_in_purchases));
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, false, price_precision);

        __write_number(row.find('input.row_purchase_total_amount'), sub_total_before_tax, false, price_precision);

        __write_number(row.find('input.purchase_import_expenses'), import_expense, false, price_precision);

        __write_number(row.find('input.purchase_dai_amount'), dai_amount, false, price_precision);

        calculate_total_bases();
        calculate_import_expenses_purchases();
        calculate_sub_total_rows();
        update_table_total();
        calcTaxes();
        calcTaxProducts();
    });

    /**
     * --------------------------------------------------------------------------------
     * IMPORT EXPENSE BAR (APPORTIONMENT)
     * --------------------------------------------------------------------------------
     */

    // Add import expense from search bar.
    if ($("#search_import_expense").length > 0) {
        let type = $("#expense_type").val();
        $("#search_import_expense").autocomplete({
            source: "/get_import_expenses?type=" + type,
            minLength: 2,
            response: function(event, ui) {
                if (ui.content.length == 1) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                } else if (ui.content.length == 0) {
                    swal(
                        LANG.no_import_expense_found
                    ).then((value) => {
                        $('#search_import_expense').select();
                    });
                }
            },
            select: function(event, ui) {
                $(this).val(null);
                get_import_expense_row(ui.item.id);
            }
        })
        .autocomplete("instance")._renderItem = function(ul, item) {
            return $("<li>").append("<div>" + item.text + "</div>").appendTo(ul);
        };
    }

    /**
     * --------------------------------------------------------------------------------
     * IMPORT EXPENSES TABLE (APPORTIONMENT)
     * --------------------------------------------------------------------------------
     */

    // On change of import_expense_amount input.
    $(document).on('change', '.import_expense_amount', function() {
        update_import_expense_total();

        if ($("#apportionment_table").length > 0) {
            calculate_totals();
            calculate_expenses_taxes(false);
        }
    });

    // On click of remove_import_expense_row icon.
    $(document).on('click', '.remove_import_expense_row', function() {
        swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((value) => {
            if (value) {
                $(this).closest('tr').remove();
                update_import_expense_total();

                if ($("#apportionment_table").length > 0) {
                    calculate_totals();
                    calculate_expenses_taxes(false);
                }
            }
        });
    });

    // Actions to be taken when loading the purchase edition form.
    if ($("#purchase_id").length > 0) {
        calculate_total_bases();
        update_import_expense_total();
        calculate_import_expenses_purchases();
        calculate_sub_total_rows();
    }

    /**
     * --------------------------------------------------------------------------------
     * PURCHASE BAR (APPORTIONMENT)
     * --------------------------------------------------------------------------------
     */

    // Add puchase from search bar.
    if ($("#search_purchase").length > 0) {
        $("#search_purchase").autocomplete({
            source: "/get_purchases",
            minLength: 2,
            response: function(event, ui) {
                if (ui.content.length == 1) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                } else if (ui.content.length == 0) {
                    swal(
                        LANG.no_purchase_found
                    ).then((value) => {
                        $('#search_purchase').select();
                    });
                }
            },
            select: function(event, ui) {
                $(this).val(null);
                get_purchase_row(ui.item.id);
            }
        })
        .autocomplete("instance")._renderItem = function(ul, item) {
            return $("<li>").append("<div>" + item.text + "</div>").appendTo(ul);
        };
    }

    /**
     * --------------------------------------------------------------------------------
     * PURCHASES TABLE (APPORTIONMENT)
     * --------------------------------------------------------------------------------
     */

    // On change of purchase_amount input.
    $(document).on('change', '.purchase_amount', function() {
        update_purchase_total();
    });

    // On click of remove_purchase_row icon.
    $(document).on('click', '.remove_purchase_row', function() {
        swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((value) => {
            if (value) {
                let row = $(this).closest('tr');
                remove_purchase_lines(row);
                row.remove();
                update_purchase_total();
                calculate_totals();
                calculate_expenses_taxes(false);
                update_product_number();
            }
        });
    });

    /**
     * --------------------------------------------------------------------------------
     * APPORTIONMENT TABLE (APPORTIONMENT)
     * --------------------------------------------------------------------------------
     */

    // On click of remove_product_row icon.
    $(document).on('click', '.remove_product_row', function() {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((value) => {
            if (value) {
                $(this).closest('tr').remove();
                // update_import_expense_total();
                update_product_number();
                calculate_totals();
                calculate_expenses_taxes();
            }
        });
    });

    // On change of general_vat input.
    $(document).on('change', 'input#general_vat', function() {
        calculate_expenses_taxes(false);
    });

    // On click of submit-apportionment buttons.
    $(document).on('click', '.submit-apportionment', function(e) {
        e.preventDefault();

		let submit_type = $(this).attr('value');
		$('#submit_type').val(submit_type);

        $('form').first().submit();
    });

    // Actions to be taken when loading the apportionment edition form.
    if ($("#edit_apportionment_form").length > 0) {
        update_purchase_total();
        update_import_expense_total();
        calculate_totals();
        calculate_expenses_taxes(false);
    }

    // Update correlative of apportionment_table.
    update_product_number();

    //recipeHelper();
});

/**
 * --------------------------------------------------------------------------------
 * FORM FIELDS
 * --------------------------------------------------------------------------------
 */

// On change of payment-amount input.
$(document).on('change', 'input.payment-amount', function() {
    var payment = __read_number($(this), true);
    var dai_amount = __read_number($('input#dai_amount_pur'), true);
    var iva_amount = __read_number($('input#iva_amount_pur'), true);
    var grand_total = __read_number($('input#grand_total_hidden'), true);
    var bal = grand_total - payment;
    $('#payment_due').text(__number_f(bal, true, false, decimals_in_purchases));
});

// On change of tax_id select.
$(document).on("change", "#tax_id", function(){
    var tax_group_id = $(this).val();

    if(tax_group_id != 0) {
        $.post('/tax_groups/get_taxes',
            { tax_group_id: tax_group_id },
                function(tax) {
                    tax = parseFloat(tax);
                    $("input#tax_percent_products").val(tax);
                    calcTaxes();
                    calcTaxProducts()
        });

    } else {
        $("#tax_percent_products").val(null);
        calcTaxes();
        calcTaxProducts();
    }
});

/** prevent doble click purchase submit button */
$(document).on('dblclick', 'button#submit_purchase_form', function(e){
    e.preventDefault();
    return false;
});

// On click of submit_purchase_form button.
$(document).on('click', 'button#submit_purchase_form', function(e){
    var btn_submit = $(this);

    e.preventDefault();

    // Check if product is present or not.
    if ($('table#purchase_entry_table tbody tr').length <= 0) {
        toastr.warning(LANG.no_products_added);
        $('input#search_product').select();
        return false;
    }

    if ($('input#flag').val() == 0) {
        // Check if the client has nit and nrc
        swal(LANG.notice, LANG.contact_has_no_nit_nrc, "error");
        return false;
    }

    $('form#add_purchase_form').validate({
        rules: {
            ref_no: {
                remote: {
                    url: "/purchases/check_ref_number",
                    type: "post",
                    data: {
                        ref_no: function() {
                            return $( "#ref_no" ).val();
                        },
                        document_type_id: function() {
                            return $( "#document_type_id" ).val();
                        },
                        contact_id: function(){
                            return $( "#supplier_id" ).val();
                        },
                        purchase_id: function() {
                            if ($('#purchase_id').length > 0) {
                                return $('#purchase_id').val();
                            } else {
                                return '';
                            }
                        },
                    }
                }
            }
        },
        messages: {
            ref_no: {
                remote: LANG.ref_no_already_exists
            }
        }
    });

    let flag_warehouse = 0;

    if ($('select#warehouse_id').is(':disabled') && $('input#purchase_id').length == 0) {
        $('select#warehouse_id').removeAttr('disabled');
        flag_warehouse = 1;
    }

    if ($('form#add_purchase_form').valid()) {
        btn_submit.attr('disabled', true);
        $('form#add_purchase_form').submit();

    } else {
        btn_submit.removeAttr('disabled');

        if (flag_warehouse) {
            $('select#warehouse_id').attr('disabled', true).change();
        }
    }

    setTimeout(function() {
        btn_submit.removeAttr('disabled');
        $("input#ref_no").attr('aria-invalid', false);
    }, 15000);
});

// On change of document_type_id select.
$("select#document_type_id").on('change', function() {
    if($(this).val() != ""){
        if($(this).val() == 3){
            $('select#tax_id').attr('disabled', true).val(0).change();
            $('input#tax_amount').val('0.00');
            $('span#tax_calculated_amount').text('0.0000');
        }else{
            $('select#tax_id').attr('disabled', false);
        }
    }
});

// On change of supplier_id select.
$('select#supplier_id').on('change', function () {
    if ($('#no-verified-supplier').length <= 0) {
	    verifiedIfExistsTaxNumber();
    }
});

// Select type of sale on click of btn_add button.
$(document).on('click', 'a#btn_add', function() {
    Swal.fire({
        title: LANG.add_purchase,
        html: 
            `<select id="select-purchase" style="width:50%;"> 
                <option value="1">${LANG.national_purchase}</option>
                <option value="2">${LANG.import_purchase}</option>
            </select>`,
        onOpen: () => {
            $('#select-purchase').select2();
        },
        preConfirm: () => {
            return $('#select-purchase').val();
        }
    }).then(result => {
        if (result.isConfirmed) {
            let url = '/purchases/create?type=' + result.value;
            setTimeout(() => {
                $(location).attr('href', url);
            }, 300);
        }
    });
});

/**
 * Assign option to supplier_id select.
 * 
 * @return void
 */
 function recipeHelper(){
    var recipeObject = $("#supplier_id").select2("data");
    //for(i=0; i<recipeList.length; i++){
    //    var recipeObject = recipeList[i];
        recipeObject.name = recipeObject.text;
    //}
    $("#supplier_id").trigger('change');
}

/**
 * --------------------------------------------------------------------------------
 * PURCHASE ENTRY TABLE
 * --------------------------------------------------------------------------------
 */

/**
 * Get and put row in purchase_entry_table table. Update table data.
 * 
 * @param  int  product_id
 * @param  int  variation_id
 * @return void
 */
function get_purchase_entry_row(product_id, variation_id) {
    if (product_id) {
        var row_count = $('#row_count').val();
        let purchase_type = $('#purchase_type').val();

        $.ajax({
            method: "POST",
            url: '/purchases/get_purchase_entry_row',
            dataType: "html",
            data: {
                'product_id': product_id,
                'row_count': row_count,
                'variation_id': variation_id,
                'purchase_type': purchase_type
            },
            success: function(result) {
                $(result).find('.purchase_quantity').each( function(){
                    row = $(this).closest('tr');

                    $('#purchase_entry_table tbody').append(update_purchase_entry_row_values(row));

                    if ($('#chk_import_expense').is(':checked')) {
                        $('.col-import-expenses').show();
                    }
                    
                    update_row_price_for_exchange_rate(row);

                    update_inline_profit_percentage(row);
                                        
                    update_table_total();
                    calcTaxes();
                    calcTaxProducts();
                    update_table_sr_number();
                });

                calculate_total_bases();
                calculate_import_expenses_purchases();
                calculate_sub_total_rows();

                if ($(result).find('.purchase_quantity').length) {
                    $('#row_count').val($(result).find('.purchase_quantity').length + parseInt(row_count) );
                }
            }
        });
    }
}

/**
 * Update data in the row of the purchase_entry_table table.
 * 
 * @param  tr  row
 * @return tr
 */
function update_purchase_entry_row_values(row) {
    if (typeof row != 'undefined') {
        let quantity = __read_number(row.find('.purchase_quantity'), true);
        let unit_cost_price = __read_number(row.find('.purchase_unit_cost'), true);
        let weight = __read_number(row.find('.purchase_product_weight'), true);

        let unit_cost_col = { total_input_id: '#purchase_total_unit_cost', new_value: quantity * unit_cost_price }
        add_sub_column_total(unit_cost_col);
        
        let weight_col = { total_input_id: '#purchase_total_weight', new_value: weight }
        add_sub_column_total(weight_col);

        // Calculate import expense
        var import_expense = calculate_purchase_expenses(row);

        // Calculate DAI
        var dai_percent = __read_number(row.find('.purchase_dai_percent'), true);
        var dai_amount = dai_percent > 0 ? (quantity * purchase_before_tax + import_expense) * (dai_percent / 100) : 0;

        var row_subtotal_before_tax = quantity * unit_cost_price + import_expense;

        var tax_rate = parseFloat( $('option:selected', row.find('.purchase_line_tax_id')).attr('data-tax_amount') );

        var unit_product_tax = __calculate_amount('percentage', tax_rate, unit_cost_price);

        var unit_cost_price_after_tax = unit_cost_price + unit_product_tax;
        // var dai_amount_at = dai_percent > 0 ? (quantity * unit_cost_price_after_tax) * (dai_percent / 100) : 0;
        var row_subtotal_after_tax = quantity * unit_cost_price_after_tax + dai_amount + import_expense;

        row.find('.row_subtotal_before_tax').text(__currency_trans_from_en(row_subtotal_before_tax, false, true, decimals_in_purchases));
        __write_number(row.find('.row_subtotal_before_tax_hidden'), row_subtotal_before_tax, false, price_precision);
        __write_number(row.find('.purchase_product_unit_tax'), unit_product_tax, false, price_precision);
        row.find('.purchase_product_unit_tax_text').text(__currency_trans_from_en(unit_product_tax, false, true, decimals_in_purchases));
        row.find('.purchase_unit_cost_after_tax').text( __currency_trans_from_en(unit_cost_price_after_tax, true, true, decimals_in_purchases));
        row.find('.row_subtotal_after_tax').text(__currency_trans_from_en(row_subtotal_after_tax, false, true, decimals_in_purchases));
        __write_number(row.find('.row_subtotal_after_tax_hidden'), row_subtotal_after_tax, false, price_precision);
        __write_number(row.find('.row_purchase_total_amount'), row_subtotal_before_tax, false, price_precision);
        __write_number(row.find('input.purchase_import_expenses'), import_expense, false, price_precision);
        __write_number(row.find('input.purchase_dai_amount'), dai_amount, false, price_precision);
        row.find('.expiry_datepicker').each( function(){
            $(this).datepicker({
                autoclose: true,
                format:datepicker_date_format
            });
        });

        calculate_total_bases();

        return row;
    }
}

/**
 * Update row price for exchange rate.
 * 
 * @param  tr  row
 * @return void
 */
function update_row_price_for_exchange_rate(row) {
    var exchange_rate = $('input#exchange_rate').val();

    if(exchange_rate == 1) {
        return true;
    }

    var purchase_unit_cost_without_discount = __read_number(row.find('.purchase_unit_cost_without_discount'), true) / exchange_rate;
    __write_number(row.find('.purchase_unit_cost_without_discount'), purchase_unit_cost_without_discount, false, price_precision);

    var purchase_unit_cost = __read_number(row.find('.purchase_unit_cost'), true) / exchange_rate;
    __write_number(row.find('.purchase_unit_cost'), purchase_unit_cost, false, price_precision);


    var row_subtotal_before_tax_hidden = __read_number(row.find('.row_subtotal_before_tax_hidden'), true) / exchange_rate;
    row.find('.row_subtotal_before_tax').text(__currency_trans_from_en(row_subtotal_before_tax_hidden, false, true, decimals_in_purchases));
    __write_number(row.find('input.row_subtotal_before_tax_hidden'), row_subtotal_before_tax_hidden, false, price_precision);

    var purchase_product_unit_tax = __read_number(row.find('.purchase_product_unit_tax'), true) / exchange_rate;
    __write_number(row.find('input.purchase_product_unit_tax'), purchase_product_unit_tax, false, price_precision);
    row.find('.purchase_product_unit_tax_text').text(__currency_trans_from_en(purchase_product_unit_tax, false, true, decimals_in_purchases));

    var purchase_unit_cost_after_tax = __read_number(row.find('.purchase_unit_cost_after_tax'), true) / exchange_rate;
    __write_number(row.find('input.purchase_unit_cost_after_tax'), purchase_unit_cost_after_tax, false, price_precision);

    var row_subtotal_after_tax_hidden = __read_number(row.find('.row_subtotal_after_tax_hidden'), true) / exchange_rate;
    __write_number(row.find('input.row_subtotal_after_tax_hidden'), row_subtotal_after_tax_hidden, false, price_precision);
    row.find('.row_subtotal_after_tax').text(__currency_trans_from_en(row_subtotal_after_tax_hidden, false, true, decimals_in_purchases));
}

/**
 * Iraqi dinnar selling price adjustment.
 * 
 * @param  tr  row
 * @return void
 */
function iraqi_dinnar_selling_price_adjustment(row) {
    var default_sell_price = __read_number(row.find('input.default_sell_price'), true);

    //Adjsustment
    var remaining = default_sell_price % 250;
    if(remaining >= 125 ){
        default_sell_price += (250-remaining);
    } else {
        default_sell_price -= remaining;
    }

    __write_number(row.find('input.default_sell_price'), default_sell_price, false, price_precision);

    update_inline_profit_percentage(row);
}

/**
 * Update inline profit percentage.
 * 
 * @param  tr  row
 * @return void
 */
function update_inline_profit_percentage(row) {
    //Update Profit percentage
    var default_sell_price = __read_number(row.find('input.default_sell_price'), true);
    var exchange_rate = $('input#exchange_rate').val();
    default_sell_price_in_base_currency = default_sell_price / parseFloat(exchange_rate);

    var purchase_before_tax = __read_number(row.find('input.purchase_unit_cost'), true);
    var profit_percent = __get_rate(purchase_before_tax, default_sell_price_in_base_currency);
    __write_number(row.find('input.profit_percent'), profit_percent, false, price_precision);
}

/**
 * Update totals from purchase_entry_table table.
 * 
 * @return void
 */
function update_table_total() {
    var total_quantity = 0;
    var total_st_before_tax = 0;
    var total_subtotal = 0;
    var total_purchase_expense_amount = 0;

    $('#purchase_entry_table tbody').find('tr').each( function(){
        total_quantity += __read_number($(this).find('.purchase_quantity'), true);
        total_st_before_tax += __read_number($(this).find('.row_subtotal_before_tax_hidden'), true);
        total_subtotal += __read_number($(this).find('.row_subtotal_after_tax_hidden'), true);
        total_purchase_expense_amount += __read_number($(this).find('.product_import_expenses'), true);
    });

    let total_before_expense = total_st_before_tax - total_purchase_expense_amount;
    let purchase_expense_amount = total_purchase_expense_amount;
    let total_after_expense = total_before_expense + purchase_expense_amount;
    let other_import_expenses = __read_number($("#other_import_expenses"), true);
    let total_before_tax = total_after_expense + other_import_expenses;
    let dai = __read_number($("#dai_amount_pur"), true);
    let vat = __read_number($("#iva_amount_pur"), true);
    let final_total = total_before_tax + dai + vat;

    // Total quantity
    $('#total_quantity').text(__number_f(total_quantity, true, false, decimals_in_purchases));

    // Total before purchase expenses
    $('#span_total_before_expense').text(__number_f(total_before_expense, true, false, decimals_in_purchases));
    __write_number($('input#total_before_expense'), total_before_expense, false, price_precision);

    // Purchase expenses
    $('#span_purchase_expense_amount').text(__number_f(purchase_expense_amount, true, false, decimals_in_purchases));
    __write_number($('input#purchase_expense_amount'), purchase_expense_amount, false, price_precision);

    // Total after purchase expenses
    $('#span_total_after_expense').text(__number_f(total_after_expense, true, false, decimals_in_purchases));
    __write_number($('input#total_after_expense'), total_after_expense, false, price_precision);

    // $('#total_st_before_tax').text(__currency_trans_from_en(total_st_before_tax, true, true, decimals_in_purchases));
    // __write_number($('input#st_before_tax_input'), total_st_before_tax, false, price_precision);

    // Total before tax
    $('#total_subtotal').text(__number_f(total_before_tax, true, false, decimals_in_purchases));
    __write_number($('input#total_subtotal_input'), total_before_tax, false, price_precision);

    // Purchase total
    // __write_number($('input.row_purchase_total_amount'), total_st_before_tax, true, price_precision);
    $('#grand_total').text(__number_f(final_total, true, false, decimals_in_purchases));
    __write_number($('input#grand_total_hidden'), final_total, false, price_precision);
}

/**
 * Update correlative of purchase_entry_table table.
 * 
 * @return void
 */
function update_table_sr_number() {
    var sr_number = 1;
    $('table#purchase_entry_table tbody').find('.sr_number').each( function(){
        $(this).text(sr_number);
        sr_number++;
    });
}

/**
 * Calculate taxes from purchase_entry_table table.
 * 
 * @return void
 */
function calcTaxes() {
    if(!$("table#purchase_entry_table tbody tr").length > 0){ return false; }
    let tax_products = $("#tax_id").val();
    let total_before_tax = __read_number($("#total_subtotal_input"), true);
    let tax_percent_products = $("#tax_percent_products").val();
    var payment_condition = $("select#payment_condition").val();
    var payment_amount = 0;

    //Calculate Discount
    let discount_type = $('select#discount_type').val();
    let discount_amount = __read_number($('input#discount_amount'), true);
    let discount = __calculate_amount(discount_type, discount_amount, total_before_tax);
    $('#discount_calculated_amount').text(__number_f(discount, true, false, decimals_in_purchases));
    __write_number($('input#discount_am'), discount, false, price_precision);

    var perception_amount = calc_contact_tax(total_before_tax - discount);
    __write_number($("input#perception_amount"), perception_amount, false, price_precision);
    $('span#perception_amount_text').text(__number_f(perception_amount, true, false, decimals_in_purchases));

    let dai_amount = $('#purchase_type').val() == 'international' ? __read_number($('input#dai_amount_pur'), true) : 0;
    let iva_amount = $('#purchase_type').val() == 'international' ? __read_number($('input#iva_amount_pur'), true) : 0;

    let tax = (total_before_tax - discount) * tax_percent_products;
    let final_total = total_before_tax + tax - discount + perception_amount + dai_amount + iva_amount;

    if((tax_products != null || tax_products != 0 ) && total_before_tax != "") {
        __write_number($('input#tax_amount'), tax, false, price_precision);
        $('#tax_calculated_amount').text(__number_f(tax, true, false, decimals_in_purchases));

        __write_number($('input#grand_total_hidden'), final_total, false, price_precision);
        $('#grand_total').text(__number_f(final_total, true, false, decimals_in_purchases));

        payment_amount = payment_condition == "credit" ? 0 : final_total;
        __write_number($("input#amount_0"), payment_amount, false, price_precision);

        $("input.payment-amount").trigger("change");
    } else {
        __write_number($('input#grand_total_hidden'), total_before_tax + perception_amount + dai_amount + iva_amount, false, price_precision);
        $('#grand_total').text(__number_f(total_before_tax + perception_amount + dai_amount + iva_amount, true, false, decimals_in_purchases));

        payment_amount = payment_condition == "credit" ? 0 : final_total;
        __write_number($("input#amount_0"), payment_amount, false, price_precision);
    }
}

/**
 * Calculate tax for contacts (perception).
 * 
 * @return void
 */
function calc_contact_tax(amount) {
	var min_amount = __read_number($("input#tax_min_amount"));
	var max_amount = __read_number($("input#tax_max_amount"));
	var tax_percent = (__read_number($('input#perception_percent')));
	var tax_amount = 0;

    if ($('#purchase_type').val() == 'national') {
        /** If has min o max amount */
        if(min_amount || max_amount){
            /** if has min and max amount */
            if(min_amount && max_amount){
                if(amount > min_amount && amount <= max_amount){
                    tax_amount = amount * tax_percent;
                }
            /** If has only min amount */
            } else if(min_amount && !max_amount){
                if(amount > min_amount){
                    tax_amount = amount * tax_percent;
                }
            /** If has only max amount */
            } else if(!min_amount && max_amount){
                if(amount <= max_amount){
                    tax_amount = amount * tax_percent;
                }
            }
        /** If has none tax */
        } else{
            tax_amount = amount * tax_percent;
        }
    }

	return tax_amount;
}

/**
 * Calculate taxes from purchase_entry_table table.
 * 
 * @return void
 */
function calcTaxProducts(){ 
    let table = $("table#purchase_entry_table tbody tr");

    if(table.length > 0){
        table.each(function(){
            let tax_percent_product = __read_number($("input#tax_percent_products"));
            let total_amount = __read_number($(this).find("input.row_purchase_total_amount"));
            var purchase_unit_cost = __read_number($(this).find("input.purchase_unit_cost"));
            var purchase_price_inc_tax = $(this).find("input.purchase_price_inc_tax");
            let tax_amount = $(this).find("input.tax_line_amount");

            if(tax_percent_product){
                let tax = total_amount * tax_percent_product;
                __write_number(tax_amount, tax, false, price_precision);
                __write_number(purchase_price_inc_tax, (purchase_unit_cost * (tax_percent_product + 1)), false, price_precision);
            } else{
                __write_number(tax_amount, 0, false, price_precision);
                __write_number(purchase_price_inc_tax, purchase_unit_cost, false, price_precision);
            }
        });
    }
}

/**
 * Check if the contact has NIT and registration number.
 * 
 * @return void
 */
function verifiedIfExistsTaxNumber() {
	let contact = $('select#supplier_id').val();
    let route = '/contact/verified_tax_number_purchase';
    if(contact != ""){
        $.ajax({
            method: "get",
            url: route,
            data: {'contact_id': contact},
            dataType: "json",
            success: function(result) {
                if (result.success == false) {
                    swal(LANG.notice, LANG.contact_has_no_nit_nrc, "error");
                    $('input#flag').val(0);
                }else{
                    $('input#flag').val(1);
                }
            }
        });
    }
}

/**
 * Calculate total from the unit cost and weight columns of the
 * purchase_entry_table table.
 * 
 * @return float
 */
function calculate_total_bases() {
    let total_unit_cost = 0;
    let total_weight = 0;

    $("#purchase_entry_table tbody").find("tr").each(function () {
        total_unit_cost += __read_number($(this).find('.purchase_unit_cost'), true) * __read_number($(this).find('.purchase_quantity'), true);
        total_weight += __read_number($(this).find('.purchase_product_weight'), true);
    });

    __write_number($("#purchase_total_unit_cost"), total_unit_cost, false, price_precision);
    __write_number($("#purchase_total_weight"), total_weight, false, price_precision);
}

/**
 * Calculate import expense of a row from purchase_entry_table table.
 * 
 * @param  tr  row
 * @return float
 */
function calculate_purchase_expenses(row) {
    let purchase_expenses = 0;

    if ($("#chk_import_expense").is(":checked")) {
        let base = $('#base').val();
        let import_expense_total = __read_number($('#import_expense_total'), false);
        let quantity = __read_number(row.find('input.purchase_quantity'), false);
        let value = base == 'weight' ? __read_number(row.find('input.purchase_product_weight'), false) : __read_number(row.find('input.purchase_unit_cost'), false) * quantity;
        let total = base == 'weight' ? __read_number($('#purchase_total_weight'), false) : __read_number($('#purchase_total_unit_cost'), false);

        if (total != 0) {
            purchase_expenses = value * import_expense_total / total;
        }

        row.find('.spn_product_import_expenses').text(__number_f(purchase_expenses, false, false, decimals_in_purchases));
        __write_number(row.find('input.product_import_expenses'), purchase_expenses, false, price_precision);
    }

    return purchase_expenses;
}

/**
 * Calculate import expense per row from purchase_entry_table table.
 * 
 * @return void
 */
function calculate_import_expenses_purchases() {
    $("#purchase_entry_table tbody").find("tr").each(function () {
        calculate_purchase_expenses($(this));
    });
}

/**
 * Calculate the subtotal of a row.
 * 
 * @param  tr  row
 * @return void
 */
function calculate_sub_total_per_row(row) {
    let quantity = __read_number(row.find('.purchase_quantity'), true);
    let unit_cost_price = __read_number(row.find('.purchase_unit_cost'), true);
    let purchase_before_tax = __read_number(row.find('input.purchase_unit_cost'), true);

    // Calculate import expense
    let import_expense = calculate_purchase_expenses(row);

    // Calculate DAI
    let dai_percent = __read_number(row.find('.purchase_dai_percent'), true);
    let dai_amount = dai_percent > 0 ? (quantity * purchase_before_tax + import_expense) * (dai_percent / 100) : 0;

    let row_subtotal_before_tax = quantity * unit_cost_price + import_expense;

    let tax_rate = parseFloat($('option:selected', row.find('.purchase_line_tax_id')).attr('data-tax_amount'));
    let unit_product_tax = __calculate_amount('percentage', tax_rate, unit_cost_price);
    let unit_cost_price_after_tax = unit_cost_price + unit_product_tax;

    let row_subtotal_after_tax = quantity * unit_cost_price_after_tax + dai_amount + import_expense;

    __write_number(row.find('.row_purchase_total_amount'), row_subtotal_before_tax, false, price_precision);
    __write_number(row.find('.row_subtotal_after_tax_hidden'), row_subtotal_after_tax, false, price_precision);
    __write_number(row.find('input.row_subtotal_before_tax_hidden'), row_subtotal_before_tax, false, price_precision);
}

/**
 * Calculate the subtotal of all rows.
 * 
 * @return void
 */
function calculate_sub_total_rows() {
    $("#purchase_entry_table tbody").find("tr").each(function () {
        calculate_sub_total_per_row($(this));

        update_table_total();
        calcTaxes();
        calcTaxProducts();
    });
}

/**
 * Add or subtract to the total of the column.
 * 
 * @param tr row
 * @param  string  operation
 * @param  bool  has_span
 * @return void
 */
 function add_sub_column_total(column_data, operation = 'add', has_span = false) {
    let total_input = $(column_data['total_input_id']);
    let total = __read_number(total_input, true);
    let new_value = parseFloat(column_data['new_value']);

    if (operation == 'add') {
        total += new_value;
    } else {
        total -= new_value;
    }

    __write_number(total_input, total, false, price_precision);

    if (has_span) {
        let span = $(column_data['span_id']);
        span.text(__currency_trans_from_en(total, false, true, decimals_in_purchases));
    }

    return total;
}

/**
 * --------------------------------------------------------------------------------
 * IMPORT EXPENSES TABLE (APPORTIONMENT)
 * --------------------------------------------------------------------------------
 */

/**
 * Get row for import_expenses_table table.
 * 
 * @param  int  id
 * @return void
 */
function get_import_expense_row(id) {
    if (id) {
        let row_count = $('#row_count_ie').val();

        let add_row = true;

        $('#import_expenses_table tbody').find('tr').each(function() {
            let row_import_expense_id = $(this).find('.import_expense_id').val();

            if (row_import_expense_id == id) {
                add_row = false;
            }
        });

        if (add_row) {
            $.ajax({
                method: 'post',
                url: '/get_import_expense_row',
                dataType: 'html',
                data: {
                    'id': id,
                    'row_count_ie': row_count,
                },
                success: function(result){
                    $(result).find('.import_expense_amount').each(function() {
                        let row = $(this).closest('tr');
                        $('#import_expenses_table tbody').append(row);
                        update_import_expense_total();
                    });

                    if ($(result).find('.import_expense_amount').length) {
                        $('#row_count_ie').val($(result).find('.import_expense_amount').length + parseInt(row_count));
                    }
                }
            });

        } else {
            Swal.fire({
                title: LANG.warning_import_expense_added,
                icon: 'warning'
            });
        }
    }
}

/**
 * Update total of import_expenses_table table.
 * 
 * @return void
 */
function update_import_expense_total() {
    let total = 0;

    $('#import_expenses_table tbody').find('tr').each(function() {
        total += __read_number($(this).find('.import_expense_amount'), true);
    });

    $('#spn_import_expense_total').text(__number_f(total, false, false, decimals_in_purchases));
    __write_number($('input#import_expense_total'), total, false, price_precision);

    if ($("#purchase_entry_table").length > 0) {
        calculate_import_expenses_purchases();
        calculate_sub_total_rows();
    }
}

/**
 * --------------------------------------------------------------------------------
 * PURCHASES TABLE (APPORTIONMENT)
 * --------------------------------------------------------------------------------
 */

/**
 * Get row for purchases_table table.
 * 
 * @param  int  id
 * @return void
 */
function get_purchase_row(id) {
    if (id) {
        let row_count = $('#row_count_p').val();

        let add_row = true;

        $('#purchases_table tbody').find('tr').each(function() {
            let row_purchse_id = $(this).find('.purchase_id').val();

            if (row_purchse_id == id) {
                add_row = false;
            }
        });

        if (add_row) {
            $.ajax({
                method: 'post',
                url: '/get_purchase_row',
                dataType: 'html',
                data: {
                    'id': id,
                    'row_count_p': row_count,
                },
                success: function(result) {
                    $(result).find('.purchase_amount').each(function() {
                        let row = $(this).closest('tr');
                        $('#purchases_table tbody').append(row);
                        update_purchase_total();
                    });

                    if ($(result).find('.purchase_amount').length) {
                        $('#row_count_p').val($(result).find('.purchase_amount').length + parseInt(row_count));
                    }

                    get_product_list(id);
                }
            });

        } else {
            Swal.fire({
                title: LANG.warning_purchase_added,
                icon: 'warning'
            });
        }
    }
}

/**
 * Update total of purchases_table table.
 * 
 * @return void
 */
function update_purchase_total() {
    let total = 0;

    $('#purchases_table tbody').find('tr').each(function() {
        total += __read_number($(this).find('.purchase_amount'), true);
    });

    $('#spn_purchase_total').text(__number_f(total, false, false, decimals_in_purchases));
    __write_number($('input#purchase_total'), total, false, price_precision);
}

/**
 * --------------------------------------------------------------------------------
 * APPORTIONMENT TABLE (APPORTIONMENT)
 * --------------------------------------------------------------------------------
 */

// On change of product_dai_percent input.
$(document).on('change', '.product_dai_percent', function() {
    calculate_expenses_taxes(true, $(this));
});

// On change of product_dai_amount input.
$(document).on('change', '.product_dai_amount', function() {
    calculate_expenses_taxes(false);
});

/**
 * Get ids from the purchase lines.
 * 
 * @param  int  id
 * @return void
 */
function get_product_list(id) {
    if (id) {
        $.ajax({
            method: 'post',
            url: '/get_product_list',
            dataType: 'json',
            data: { 'id': id },
            success: function(res) {
                $.each(res, function(i, val) {
                    add_product_row(val.id);
                    $('#row_count_pr').val(parseInt($('#row_count_pr').val()) + 1);
                });
            }
        });
    }
}

/**
 * Add row to apportionment_table table.
 * 
 * @param  int  id
 * @return void
 */
function add_product_row(id) {
    let row_count_pr = $('#row_count_pr').val();

    $.ajax({
        method: 'post',
        url: '/add_product_row',
        dataType: 'html',
        data: {
            'id': id,
            'row_count_pr': row_count_pr,
        },
        success: function(result) {
            $(result).find('.product_quantity').each(function() {
                let row = $(this).closest('tr');
                $('#apportionment_table tbody').append(row);
                update_product_number();
            });

            calculate_totals();
            calculate_expenses_taxes(false);

            if ($(result).find('.product_quantity').length) {
                $('#row_count_pr').val($(result).find('.product_quantity').length + parseInt(row_count_pr));
            }
        }
    });
}

/**
 * Update correlative of apportionment_table table.
 * 
 * @param  int  id
 * @return void
 */
function update_product_number() {
    let product_number = 1;

    $('table#apportionment_table tbody').find('.product_number').each(function() {
        $(this).text(product_number);
        product_number++;
    });
}

/**
 * Calculate totals from quantity, weight, FOB, and total columns from
 * apportionment_table table.
 * 
 * @return void
 */
function calculate_totals() {
    let total_quantity = 0;
    let total_weight = 0;
    let total_fob = 0;
    let total_total = 0;

    $("#apportionment_table tbody").find("tr").each(function () {
        total_quantity += __read_number($(this).find('.product_quantity'), true);
        total_weight += __read_number($(this).find('.product_weight'), true);
        total_fob += __read_number($(this).find('.product_fob'), true);
        total_total += __read_number($(this).find('.product_total'), true);
    });

    $("#product_total_quantity").text(__number_f(total_quantity, false, false, 4));
    $("#spn_product_total_weight").text(__number_f(total_weight, false, false, 4));
    $("#product_total_fob").text(__number_f(total_fob, false, false, decimals_in_purchases));
    $("#spn_product_total_total").text(__number_f(total_total, false, false, decimals_in_purchases));

    __write_number($('input#product_total_weight'), total_weight, false, price_precision);
    __write_number($('input#product_total_total'), total_total, false, price_precision);
}

/**
 * Calculate import expenses by row from apportionment_table table.
 * 
 * @param  tr  row
 * @return float
 */
function calculate_import_expenses(row) {
    let base = $('#base').val();
    let import_expense_total = __read_number($('#import_expense_total'), false);
    let value = base == 'weight' ? __read_number(row.find('input.product_weight'), false) : __read_number(row.find('input.product_total'), false);
    let total = base == 'weight' ? __read_number($('#product_total_weight'), false) : __read_number($('#product_total_total'), false);

    let import_expense = value * import_expense_total / total;
    import_expense = parseFloat((import_expense).toFixed(decimals_in_purchases));
    
    row.find('.spn_product_import_expenses').text(__number_f(import_expense, false, false, decimals_in_purchases));
    __write_number(row.find('input.product_import_expenses'), import_expense, false, price_precision);

    return import_expense;
}

/**
 * Calculate other expenses by row from apportionment_table table.
 * 
 * @param  tr  row
 * @return float
 */
function calculate_other_expenses(row) {
    let other_expenses_total = __read_number(row.find("input.product_total_import_expenses"), false);
    let apply_other_expenses_total = __read_number(row.find("input.apply_purchase_expense_amount"), false);
    let value = __read_number(row.find("input.product_total"), false);
    let total = __read_number(row.find("input.product_total_purchase"), false) - apply_other_expenses_total;
    
    if ($("#edit_apportionment_form").length > 0) {
        total = __read_number(row.find("input.product_total_purchase"), false);
    }

    let other_expenses = value * other_expenses_total / total;
    other_expenses = parseFloat((other_expenses).toFixed(decimals_in_purchases));
    
    row.find('.spn_product_other_expenses').text(__number_f(other_expenses, false, false, decimals_in_purchases));
    __write_number(row.find('input.product_other_expenses'), other_expenses, false, price_precision);

    return other_expenses;
}

/**
 * Calculate DAI by row from apportionment_table table.
 * 
 * @param  tr  row
 * @return float
 */
 function calculate_dai(row, cif) {
    let dai = __read_number(row.find("input.product_dai_percent"), false);
    let dai_percent = dai / 100;

    let dai_amount = cif * dai_percent;
    dai_amount = parseFloat((dai_amount).toFixed(decimals_in_purchases));
    
    row.find('.spn_product_dai_amount').text(__number_f(dai_amount, false, false, decimals_in_purchases));
    __write_number(row.find('input.product_dai_amount'), dai_amount, false, price_precision);

    return dai_amount;
}

/**
 * Calculate VAT by row from apportionment_table table.
 * 
 * @param  tr  row
 * @return float
 */
 function calculate_vat(row) {
    let base = $('#base').val();
    let total_vat = __read_number($('#general_vat'), false);
    let value = base == 'weight' ? __read_number(row.find('input.product_weight'), false) : __read_number(row.find('input.product_total'), false);
    let total = base == 'weight' ? __read_number($('#product_total_weight'), false) : __read_number($('#product_total_total'), false);

    let vat_amount = value * total_vat / total;
    vat_amount = parseFloat((vat_amount).toFixed(decimals_in_purchases));
    
    row.find('.spn_product_vat').text(__number_f(vat_amount, false, false, decimals_in_purchases));
    __write_number(row.find('input.product_vat'), vat_amount, false, price_precision);

    return vat_amount;
}

/**
 * Calculate unit cost by row from apportionment_table table.
 * 
 * @param  tr  row
 * @param  float  total_cost
 * @param  float  total_cost_exc_tax
 * @return void
 */
function calculate_unit_cost(row, total_cost, total_cost_exc_tax) {
    let quantity = __read_number(row.find("input.product_quantity"), false);

    let unit_cost = total_cost / quantity;
    let unit_cost_exc_tax = total_cost_exc_tax / quantity;
    
    row.find('.spn_product_unit_cost').text(__number_f(unit_cost, false, false, decimals_in_purchases));
    __write_number(row.find('input.product_unit_cost'), unit_cost, false, price_precision);

    row.find('.spn_product_unit_cost_exc_tax').text(__number_f(unit_cost_exc_tax, false, false, decimals_in_purchases));
    __write_number(row.find('input.product_unit_cost_exc_tax'), unit_cost_exc_tax, false, price_precision);
}

/**
 * Calculate totals from import expenses, other expenses, CIF, DAI, VAT and
 * total cost columns from apportionment_table table.
 * 
 * @return void
 */
function calculate_expenses_taxes(calculate_dai_flag = true, input_percent = null) {
    let total_import_expenses = 0;
    let total_other_expenses = 0;
    let total_cif = 0;
    let total_dai = 0;
    let total_vat = 0;
    let total_total_cost = 0;

    $('#apportionment_table tbody').find('tr').each(function() {
        let total = __read_number($(this).find('input.product_total'), false);
        let import_expenses = calculate_import_expenses($(this));
        let other_expenses = calculate_other_expenses($(this));
        let cif = parseFloat((total + import_expenses + other_expenses).toFixed(decimals_in_purchases));

        let dai_value;

        if (input_percent === null) {
            dai_value = calculate_dai_flag ? calculate_dai($(this), cif) : __read_number($(this).find('input.product_dai_amount'), false);
        } else {
            if (input_percent.data('line-id') == $(this).find('input.product_dai_percent').data('line-id')) {
                dai_value = calculate_dai($(this), cif);
            } else {
                dai_value = __read_number($(this).find('input.product_dai_amount'), false);
            }
        }

        let dai = dai_value;
        
        let vat = calculate_vat($(this));
        let total_cost = parseFloat((cif + dai + vat).toFixed(decimals_in_purchases));

        calculate_unit_cost($(this), total_cost, cif);

        $(this).find('.spn_product_cif').text(__number_f(cif, false, false, decimals_in_purchases));
        __write_number($(this).find('input.product_cif'), cif, false, price_precision);

        $(this).find('.spn_product_total_cost').text(__number_f(total_cost, false, false, decimals_in_purchases));
        __write_number($(this).find('input.product_total_cost'), total_cost, false, price_precision);

        total_import_expenses += import_expenses;
        total_other_expenses += other_expenses;
        total_cif += cif;
        total_dai += dai;
        total_vat += vat;
        total_total_cost += total_cost;
    });

    $("#product_total_import_expenses").text(__number_f(total_import_expenses, false, false, decimals_in_purchases));
    $("#product_total_other_expenses").text(__number_f(total_other_expenses, false, false, decimals_in_purchases));
    $("#product_total_cif").text(__number_f(total_cif, false, false, decimals_in_purchases));
    $("#product_total_dai_amount").text(__number_f(total_dai, false, false, decimals_in_purchases));
    $("#product_total_vat").text(__number_f(total_vat, false, false, decimals_in_purchases));
    $("#product_total_total_cost").text(__number_f(total_total_cost, false, false, decimals_in_purchases));
}

/**
 * Remove product lines belonging to the removed purchase.
 * 
 * @param  tr  row
 * @return void
 */
function remove_purchase_lines(row) {
    let purchase_id = __read_number(row.find('input.purchase_id'), true);

    $('#apportionment_table tbody').find('tr').each(function() {
        let product_purchase_id = __read_number($(this).find('input.product_purchase_id'), true);

        if (purchase_id == product_purchase_id) {
            $(this).remove();
        }
    });
}

/** 
 * Stop execution for defined time.
 * 
 * @param  int  ms
 * @return Promise
 */
function sleep(ms) {
	return new Promise(
		resolve => setTimeout(resolve, ms)
	);
}
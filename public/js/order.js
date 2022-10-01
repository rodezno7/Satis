// Identify type of business
app_business = $('#app_business').val();

$(document).ready(function(){
    //Date picker
    $('input#order_date, input#delivery_date').datepicker({
        autoclose: true,
        format: datepicker_date_format
    });

    set_default_customer();

    if (app_business == 'workshop') {
        update_rows_workshop();
        
    } else {
        update_rows();
    }

    calculate_discount();

    showHideFields();

    $(document).on("change", $("select#delivery_type"), function(){
        showHideFields();
    });

    /** Show qty alert */
    $(document).on("change", "input#quantity", function () {
        let tr = $(this).closest("tr");
        let qty = __read_number($(this));
        let qty_available = __read_number(tr.find("input.qty_available"));

        if (qty > qty_available) {
            Swal.fire({
                title: LANG.notice,
                text: LANG.qty_higher_than_stock_available,
                icon: "warning",
                showConfirmButton: true
            });
        }
    });

    $('div.show_order_modal').on('shown.bs.modal', function () {
        update_rows();
        calculate_discount();
    });

    /** disable save button on submit form */
    // $(document).on('submit', 'form#add_order_form', function(e){
    //     e.preventDefault();

    //     let btn_submit = $(this).find('button#btn_submit');
    //     btn_submit.attr('disabled', true);

    //     setTimeout(function() {
    //         btn_submit.removeAttr('disabled');
    //     }, 10000);
    // });

    //Orders table
    var orders_table = $('table#orders_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/orders',
            data: function(data){
                data.location_id = $("select#location").val();
            },
        },
        aaSorting: [
            [1, 'desc'],
            [3, 'asc']
        ],
        columnDefs: [{
            "targets": 7,
            "orderable": false,
            "searchable": false
        }],
        columns: [
            {data: 'quote_ref_no', name: 'quote_ref_no'},
            {data: 'quote_date', name: 'quote_date'},
            {data: 'customer_name', name: 'customer_name'},
            {data: 'invoiced', name: 'invoiced'},
            {data: 'final_total', name: 'transactions.final_total'},
            {data: 'delivery_type', name: 'delivery_type'},
            {data: 'employee_name', name: 'employee_name'},
            {data: 'action', name: 'action'}
        ]
    });

    $(document).on("change", "select#location", function(){
        orders_table.ajax.reload();
    });

    /** Show order */
    $(document).on("click", "a.show_order", function(e){
        e.preventDefault();

        $.ajax({
            url: $(this).attr("href"),
            dataType: "html",
            success: function(data){
                $('div.show_order_modal').html(data).modal('show');
            }
        });
    });

    /** Get cities by state */
    $(document).on("change", "select#state_id", function(){
        var state_id = $(this).val();
        var city_id = $("select#city_id");

        $.ajax({
            method: "GET",
            url: "/cities/getCitiesByState/" + state_id,
            dataType: "json",
            success: function(cities){
                city_id.empty()
                    .append(new Option(LANG.city, 0, true, true))
                $.each(cities, function(i, c){
                    city_id.
                        append(new Option(c.name, c.id, false, false));
                });
            }
        });
    });

    /** Delete order */
    $(document).on('click', 'a.delete_order', function(e) {
        e.preventDefault();

        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_order,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).attr('href');
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
                            orders_table.ajax.reload();
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

    // On click of discount_row button
    if (app_business == 'workshop') {
        $(document).on('click', 'button#discount_row', function (e) {
            e.preventDefault();

            let tr = $(this).closest('tr');
            let variation_id = tr.find('input#variation_id').val();
            let service_block_index = tr.find('input.service_block_index').val();
    
            $('div#discount_line_modal-' + service_block_index + '-' + variation_id).modal("show");
        });

    } else {
        $(document).on("click", "button#discount_row", function(e){
            e.preventDefault();

            var tr = $(this).closest("tr");
            var variation_id = tr.find("input#variation_id").val();
    
            $("div#discount_line_modal_" + variation_id).modal("show");
        });
    }

    $(document).on("click", "button#delete_row", function(e){
        e.preventDefault();
        var tr = $(this).closest("tr");

        Swal.fire({
            title: LANG.sure,
            text: LANG.wont_be_able_revert,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelmButtonText: 'LANG.cancel',
            confirmButtonText: LANG.yes_delete
          }).then((result) => {
            if (result.isConfirmed) {
                tr.remove()

                if (app_business == 'workshop') {
                    update_rows_workshop();
                } else {
                    update_rows();
                }

                calculate_discount();
            }
          })
    });

    $(document).on("change", "select#tax_detail", function(){
        var tax_detail = $(this).val();

        if (app_business == 'workshop') {
            $('div#service-blocks div.panel').each(function () {
                $(this).find('table.table-spares tbody tr').each(function () {
                    let unit_price_text = 0;
                    let unit_price_exc_tax = __read_number($(this).find('input#unit_price_exc_tax'));
                    let unit_price_inc_tax = __read_number($(this).find('input#unit_price_inc_tax'));

                    let discount_amount = 0;
                    let line_discount_amount = __read_number($(this).find('input#discount_line_amount'));

                    let tax_percent = (unit_price_inc_tax / unit_price_exc_tax) - 1;

                    if (tax_detail == 'yes') {
                        unit_price_text = unit_price_exc_tax;
                        discount_amount = line_discount_amount / (1 + tax_percent);

                    } else {
                        unit_price_text = unit_price_inc_tax;
                        discount_amount = line_discount_amount * (1 + tax_percent);
                    }

                    __write_number($(this).find('input.unit_price_text'), unit_price_text, false, 4);

                    __write_number($(this).find('input#discount_line_amount'), discount_amount, false, 4);

                    calculate_line_discount($(this));
                });
            });

        } else {
            $("table#order_table tbody tr").each(function(){
                var unit_price_text = tax_detail == "yes" ?
                    __read_number($(this).find("input#unit_price_exc_tax")) :
                        __read_number($(this).find("input#unit_price_inc_tax"));
                __write_number($(this).find("input.unit_price_text"), unit_price_text, false, 4);

                calculate_line_discount($(this));
            });
        }
    });

    $(document).on("change", "input#quantity", function(){
        var tr = $(this).closest("tr");
        calculate_line_discount(tr);
    });

    $(document).on("change", "select#discount_line_type, input#discount_line_amount", function(){
        var tr = $(this).closest("tr");
        calculate_line_discount(tr);
    });

    $(document).on("change", "select#discount_type, input#discount_amount", function(){
        calculate_discount();
    });

    $(document).on("change", "input.unit_price_text", function(){
        var tr = $(this).closest("tr");
        update_unit_price(tr);
    });

    // Get customer
    $('select#customer_id').select2({
    	ajax: {
      		url: '/customers/get_customers',
	        dataType: 'json',
	        delay: 250,
	        data: function (params) {
	            return {
	              q: params.term
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

    // On select of customer_id select
    $('select#customer_id').on('select2:select', function (e) {
        var data = e.params.data;

        $('input#customer_name').val(data.text);

        if ($('#customer_vehicle_id').length > 0) {
            getCustomerVehicles(data.id);
        }

        /** autocomplete customer info */
        $('input#contact_name').val(data.contact);
        $('input#mobile').val(data.telphone);
        $('input#email').val(data.email);
        
        if (data.is_taxpayer == 1) {
            $('select#document_type_id').val(2).trigger('change');
        } else {
            $('select#document_type_id').val(1).trigger('change');
        }

        if (data.allowed_credit == 1) {
            $('select#payment_condition').val('credit').trigger('change');
            $('input#validity').val(data.payment_term);
        } else {
            $('select#payment_condition').val('cash').trigger('change');
        }

        if(data.state) {
            $('select#state_id').val(data.state).trigger('change');
            
            setTimeout(() => {
                $('select#city_id').val(data.city).trigger('change');
            }, 1500);

        }

        $('input#delivery_address').val(data.address);
        $('select#employee_id').val(data.seller_id).trigger('change');
    });
    
    // Get quotes
    $('select#search_quote').select2({
    	ajax: {
      		url: '/quotes/get_quotes',
	        dataType: 'json',
	        delay: 250,
	        data: function (params) {
	            return {
	              q: params.term
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

    // On select of search_quote select
    $('select#search_quote').on('select2:select', function (e) {
        var data = e.params.data;
        
        set_quote_info(data);

        $(this).empty().append(new Option(LANG.search_quote, 0, true, true));
    });
   
    $(document).on("change", "select#warehouse_id", function () {
        if ($(this).val()) {
            $("input#order_search_product").prop("readonly", false);
            $("select#search_service").prop("disabled", false);

        } else {
            $("input#order_search_product").prop("readonly", true);
            $("select#search_service").prop("disabled", true);
        }
    });

    // Add Product
    if ($('#order_search_product').length > 0) {
        $("input#order_search_product").autocomplete({
            source: function(request, response) {
                $.getJSON("/products/list_for_quotes", {
                    warehouse_id: $('select#warehouse_id').val(),
                    term: request.term }, response
                    );
            },
            minLength: 2,
            response: function(event,ui) {
                if (ui.content.length == 1)
                {
                    ui.item = ui.content[0];
                } else if (ui.content.length == 0) {
                    swal(LANG.no_products_found)
                    .then((value) => {
                        $('input#search_product').select();
                    });
                }
            },
            focus: function( event, ui ) {
                if(ui.item.qty_available <= 0){
                    return false;
                }
            },
            select: function( event, ui ) {
                if(ui.item.enable_stock != 1 || ui.item.qty_available > 0){
                    $(this).val(null);
                    tax_detail = $('select#tax_detail').val()
                    warehouse_id = $('select#warehouse_id').val()
                    var ids = get_variation_ids();

                    if(jQuery.inArray(ui.item.variation_id, ids) >= 0){
                        Swal.fire(LANG.notice, LANG.product_already_added, 'warning');
                    } else{
                        add_product(ui.item.variation_id, warehouse_id, tax_detail);
                    }
                } else{
                    alert(LANG.out_of_stock);
                }
            }
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            if(item.enable_stock == 1 && item.qty_available <= 0){

                var string = '<li class="ui-state-disabled"> '+ item.name;
                if(item.type == 'variable'){
                    string += '-' + item.variation;
                }
                var selling_price = item.selling_price;
                if(item.variation_group_price){
                    selling_price = item.variation_group_price;
                }

                string += ' (' + item.sub_sku + ')' + "<br>" + LANG.price + ":$" + selling_price
                + ' (' + LANG.out_of_stock + ') </li>';
                return $(string).appendTo(ul);
            } else {

                var string =  "<div>" + item.name;
                if(item.type == 'variable'){
                    string += '-' + item.variation;
                }

                var selling_price = item.selling_price;
                if(item.variation_group_price){
                    selling_price = item.variation_group_price;
                }

                string += ' (' + item.sub_sku + ')' + "<br> <b>" + LANG.price + ":</b> $" + selling_price + " <b>"
                + LANG.stock + ":</b> " + Math.round(item.qty_available, 0)
                +" <b>"+ LANG.reserved + ':</b> ' + Math.round(item.qty_reserved, 0);
                
                if(item.variations){
                    string += '<br>';
                    $.each(item.variations, function(k, v){
                        string += '<b>' + v.price_group + ':</b> $' + v.price_inc_tax + ' ';
                    });
                }

                string += " </div>";
                return $( "<li>" )
                .append(string)
                .appendTo( ul );
            }
        };
    }

    let blockCont = [];
    let rowCont = [];

    // Get services
    if ($('#search_service').length > 0) {
        $('#search_service').select2({
            ajax: {
                url: '/products/get-services',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term,
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
            },
        });

        // On select of search_service select
        $('#search_service').on('select2:select', function (e) {
            let data = e.params.data;
            let warehouse_id = $('select#warehouse_id').val();
            let tax_detail = $("#tax_detail").val();
            let service_block_index = $('#service-block-index').val();

            let service_ids = [];
            let count_service_ids = 0;

            $('#service-blocks').find('input.service-id').each(function () {
                service_ids.push(parseInt($(this).val()));
            });

            count_service_ids = parseInt(jQuery.inArray(data.id, service_ids));

            if (count_service_ids >= 0) {
                // Clear select2
                $('#search_service').val(null).trigger('change');

                Swal.fire(
                    LANG.notice,
                    LANG.product_already_added,
                    'warning'
                );
                
            } else {
                $.ajax({
                    method: 'post',
                    url: '/quotes/add-service-block/' + data.id,
                    data: {
                        warehouse_id: warehouse_id,
                        tax_detail: tax_detail,
                        service_block_index: service_block_index,
                        view: 'order'
                    },
                    dataType: 'json',
                    success: function (result) {
                        // Clear select2
                        $('#search_service').val(null).trigger('change');

                        if (result.success == 1) {
                            // Add service block
                            let appended = $('#service-blocks').append(result.html_content);

                            __select2($(appended).find('.select2'));

                            let current_service_block = $('#service-block-index').val();

                            // blockCont.push(result.service_block_index);
                            blockCont.push(parseInt($('#service-block-index').val()));

                            // Update service block index
                            $('#service-block-index').val(parseInt($('#service-block-index').val()) + 1);

                            // Add service in table
                            add_spare(
                                result.service_id,
                                current_service_block,
                                result.service_id
                            );

                            if ($("#search_product-" + service_block_index).length > 0) {
                                $("#search_product-" + service_block_index).autocomplete({
                                    source: function (request, response) {
                                        $.getJSON(
                                            "/products/list_for_quotes",
                                            {
                                                warehouse_id: $('select#warehouse_id').val(),
                                                term: request.term
                                            },
                                            response
                                        );
                                    },
                                    minLength: 1,
                                    delay: 250,
                                    response: function (event,ui) {
                                        if (ui.content.length == 1) {
                                            ui.item = ui.content[0];

                                        } else if (ui.content.length == 0) {
                                            $('input#search_product').select();
                                        }
                                    },
                                    focus: function (event, ui) {
                                        if (ui.item.qty_available <= 0) {
                                            return false;
                                        }
                                    },
                                    select: function (event, ui) {
                                        if (ui.item.enable_stock != 1 || ui.item.qty_available > 0) {
                                            $(this).val(null);

                                            warehouse_id = $('select#warehouse_id').val();
                                            tax_detail = $('select#tax_detail').val();

                                            let ids = get_spare_ids($(this).data('service-block-index'));

                                            if (jQuery.inArray(ui.item.variation_id, ids) >= 0) {
                                                Swal.fire(LANG.notice, LANG.product_already_added, 'warning');

                                            } else {
                                                add_spare(
                                                    ui.item.variation_id,
                                                    $(this).data('service-block-index'),
                                                    $(this).data('service-parent-id')
                                                );
                                            }
                                            
                                        } else {
                                            alert(LANG.out_of_stock);
                                        }
                                    }
                                })
                                .autocomplete('instance')._renderItem = function (ul, item) {
                                    if (item.enable_stock == 1 && item.qty_available <= 0) {
                                        let string = '<li class="ui-state-disabled"> ' + item.name;

                                        if (item.type == 'variable') {
                                            string += '-' + item.variation;
                                        }

                                        let selling_price = item.selling_price;

                                        if (item.variation_group_price) {
                                            selling_price = item.variation_group_price;
                                        }

                                        string += ' (' + item.sub_sku + ')' + '<br>' + LANG.price + ':$' + selling_price
                                            + ' (' + LANG.out_of_stock + ') </li>';

                                        return $(string).appendTo(ul);

                                    } else {
                                        let string =  '<div>' + item.name;

                                        if (item.type == 'variable') {
                                            string += '-' + item.variation;
                                        }

                                        let selling_price = item.selling_price;

                                        if (item.variation_group_price) {
                                            selling_price = item.variation_group_price;
                                        }

                                        string += ' (' + item.sub_sku + ')' + '<br> <b>' + LANG.price + ':</b>$' + selling_price + ' <b>'
                                            + LANG.stock + ':</b>' + Math.round(item.qty_available, 0);
                                        
                                        if (item.variations) {
                                            string += '<br>';

                                            $.each(item.variations, function (k, v) {
                                                string += '<b>' + v.price_group + ':</b> $' + v.price_inc_tax + ' ';
                                            });
                                        }

                                        string += ' </div>';

                                        return $('<li>').append(string).appendTo(ul);
                                    }
                                };
                            }
                        }
                    }
                });
            }
        });
    }
    
    $(document).on("click", "button#go_back", function(){
        location.href = "/orders";
    });

    $(document).on("change", "select.group_price_line", function(){
        var price_group = __read_number($(this));
        var tr = $(this).closest("tr");
        var price = tr.find("input.unit_price_text");

        if($(this).val() > 0){
            var tax_detail = $("select#tax_detail").val();
            
            if(tax_detail == "yes"){
                var tax_percent = __read_number(tr.find("input#tax_percent"));
                price_group = price_group / (tax_percent + 1);
            }
            __write_number(price, price_group, false, 4);
            price.change();
        }
    });

    // On click of remove-service-block button
    $(document).on('click', 'button.remove-service-block', function () {
        swal({
            title: LANG.sure,
            text: LANG.delete_content,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $('#panel-' + $(this).data('panel-id')).remove();
                calculate_discount();
            }
        });
    });

    // FIll service blocks
    if ($('#edit_order_form').length > 0) {
        let quote_id = $('#quote_id').val();
        set_edit_form(quote_id);
    }
});

function add_product(variation_id, warehouse_id, tax_detail){
    $.ajax({
        type: "POST",
        url: "/orders/get_quote_lines",
        data: {
            variation_id : variation_id,
            warehouse_id : warehouse_id,
            tax_detail
        },
        dataType: "html",
        success: function(tr){
            $("table#order_table tbody").append(tr);
            update_rows();
        }
    }).done(function(){
        calculate_discount();
    });
}

function set_default_customer(){
    var default_customer_id = $("input#default_customer_id").val();
    var default_customer_name = $("input#default_customer_name").val();
    var customer_id = $("select#customer_id");

    if(default_customer_id && default_customer_name){
        customer_id.append(new Option(default_customer_name, default_customer_id, true, true));
    }
}

/** Update elements row & row number */
function update_rows(){
    var table_rows = $("table#order_table tr");

    $.each(table_rows, function(i, row){
        var row_no = $(this).find("span#row_no");
        row_no.text(parseInt(i));

        var selects = $(this).find("select");
        var inputs = $(this).find("input");

        update_selects(i, selects);
        update_inputs(i, inputs);

    });

    function update_selects(i, selects){
        var id = 'N/A';
        selects.each(function(){
            id = $(this).attr('id') == undefined ? 'N/A' : $(this).attr('id') ;
            $(this).attr("name", "order_lines["+ i +"]["+ id +"]");
        });
    }

    function update_inputs(i, inputs){
        var id = 'N/A';
        inputs.each(function(){
            id = $(this).attr('id') == undefined ? 'N/A' : $(this).attr('id') ;
            $(this).attr("name", "order_lines["+ i +"]["+ id +"]");
        });
    }
}

function get_variation_ids(){
    var table_rows = $("table#order_table tr");
    var ids = [];

    $.each(table_rows, function(i, row){
        var id = $(this).find("input#variation_id").val();
        id = parseInt(id);

        ids.push(id);
    });

    return ids;
}

/**
 * Get variation ids from spare tables.
 * 
 * @param  int  service_block_index
 * @return array
 */
function get_spare_ids(service_block_index) {
    let table_rows = $('table#table-spares-' + service_block_index + ' tr');
    let ids = [];

    $.each(table_rows, function(i, row) {
        let id = $(this).find('input#variation_id').val();
        id = parseInt(id);

        ids.push(id);
    });

    return ids;
}

/**
 * Fill workshop fields of the edition form.
 * 
 * @param  int  quote_id
 * @return void
 */
function set_edit_form(quote_id) {
    $.ajax({
        type: 'post',
        url: '/quote/workshop-data/' + quote_id,
        dataType: 'json',
        success: function (data) {
            blockCont = [];

            // Fill customer_vehicle_id select
            $('#customer_vehicle_id').find('option').remove();

            $("#customer_vehicle_id").append('<option value="0" selected>' + LANG.select_please + '</option>');
            
            $(data.customer_vehicles).each(function (i, v) {
                if (v.id == data.customer_vehicle_id) {
                    $('#customer_vehicle_id').append('<option value="' + v.id + '" selected>' + v.name + '</option>');
                } else {
                    $('#customer_vehicle_id').append('<option value="' + v.id + '">' + v.name + '</option>');
                }
            });

            // Add service block
            $('#service-blocks').empty();

            $(data.service_blocks).each(function (i, v) {
                fillServiceBlocks(data, i, v);
            });
        }
    });
}

function set_quote_info(data) {
    blockCont = [];

    data.id ? $("input#quote_id").val(data.id) : $("input#quote_id").val(0);

    if (data.customer_id) {
        $("select#customer_id")
            .empty()
            .append(new Option(data.c_name, data.customer_id, true, true));
    }

    data.customer_name ? $("input#customer_name").val(data.customer_name) : $("input#customer_name").val("");
    
    if (data.quote_date) {
        var quote_date = moment(data.quote_date);
        $("input#order_date").val(quote_date.format(moment_date_format));
    }

    data.quote_ref_no ? $("input#ref_no").val(data.quote_ref_no) : $("input#ref_no").val("");
    data.quote_ref_no ? $("#ref_no_2").text("# " + data.quote_ref_no) : $("#ref_no_2").text("");
    data.contact_name ? $("input#contact_name").val(data.contact_name) : $("input#contact_name").val("");
    data.document_type_id ? $("select#document_type_id").val(data.document_type_id).change() : $("select#document_type_id").prop('selectedIndex', 0).change();
    data.mobile ? $("input#mobile").val(data.mobile) : $("input#mobile").val("");
    data.email ? $("input#email").val(data.email) : $("input#email").val("");
    data.payment_condition ? $("select#payment_condition").val(data.payment_condition).change() : $("select#payment_condition").prop('selectedIndex', 0).change();
    data.validity ? $("input#validity").val(data.validity) : $("input#validity").val(""); 
    data.delivery_time ? $("input#delivery_time").val(data.delivery_time) : $("input#delivery_time").val("");
    // data.delivery_type ? $("select#delivery_type").val(data.delivery_type).change() : $("select#delivery_type").val("at_home").change();
    data.tax_detail ? $("select#tax_detail").val("yes").change() : $("select#tax_detail").val("no").change();
    data.employee_id ? $("select#employee_id").val(data.employee_id).change() : null; // $("select#tax_detail").val("no").change();
    data.address ? $("input#delivery_address").val(data.address) : $("input#delivery_address").val("");

    if (data.tax_amount) {
        __write_number($("input#tax_amount"), data.tax_amount, false, 4);
        $("span#tax_amount_text").text(__currency_trans_from_en(parseFloat(data.tax_amount).toFixed(2)));

    } else {
        __write_number($("input#tax_amount"), 0.00);
        $("span#tax_amount_text").text(__currency_trans_from_en(0.00));
    }

    if (data.total_before_tax) {
        $("input#total_before_tax").val(data.total_before_tax);
        $("span#total_before_tax_text").text(__currency_trans_from_en(parseFloat(data.total_before_tax).toFixed(2)));
    }

    if (data.total_final) {
        $("input#total_final").val(data.total_final);
        $("span#total_final_text").text(__currency_trans_from_en(parseFloat(data.total_final).toFixed(2)));
    }

    data.note ? $("textarea#note").val(data.note) : $("textarea#note").val("");

    data.terms_conditions ? $("textarea#terms_conditions").val(data.terms_conditions) : $("textarea#terms_conditions").val("");

    if (app_business == 'workshop') {
        // Fill customer_vehicle_id select
        $('#customer_vehicle_id').find('option').remove();

        $("#customer_vehicle_id").append('<option value="0" selected>' + LANG.select_please + '</option>');
        
        $(data.customer_vehicles).each(function (i, v) {
            if (v.id == data.customer_vehicle_id) {
                $('#customer_vehicle_id').append('<option value="' + v.id + '" selected>' + v.name + '</option>');
            } else {
                $('#customer_vehicle_id').append('<option value="' + v.id + '">' + v.name + '</option>');
            }
        });

        // Add service block
        $('#service-blocks').empty();

        $(data.service_blocks).each(function (i, v) {
            fillServiceBlocks(data, i, v);
        });

    } else {
        if (data.id) {
            $.ajax({
                type: "POST",
                url: "/orders/get_quote_lines",
                data: { quote_id : data.id },
                dataType: "html",
                success: function(tr){
                    $("table#order_table tbody").empty().append(tr);
                    update_rows();
                }
            }).done(function(){
                $("input#subtotal").val(calculate_subtotal());
    
                data.discount_type ? $("select#discount_type").val(data.discount_type).change() : $("select#discount_type").val("fixed").change();
    
                if (data.discount_amount) {
                    var subtotal = $("input#subtotal").val();
    
                    $("input#discount_amount").val(parseFloat(data.discount_amount).toFixed(4));
    
                    var discount_amount = $("input#discount_amount").val();
                    var discount_type = $("select#discount_type").val();
                    var discount = __calculate_amount(discount_type, discount_amount, subtotal);
    
                    $("span#discount_calculated_amount_text").text(__currency_trans_from_en(discount.toFixed(2)));
                    __write_number($("input#discount_calculated_amount"), discount, false, 4);
    
                } else{
                    $("span#discount_calculated_amount_text").text(__currency_trans_from_en(0.00));
                    __write_number($("input#discount_calculated_amount"), 0.00);
                }
            });
        }
    }
}

/**
 * Fill service blocks.
 * 
 * @param  json  data
 * @param  int  i
 * @param  json  v
 * @return void
 */
function fillServiceBlocks(data, i, v) {
    let tax_detail = data.tax_detail ? 'yes' : 'no';
    let service_block_index = $('#service-block-index').val();

    let service_ids = [];
    let count_service_ids = 0;

    $('#service-blocks').find('input.service-id').each(function () {
        service_ids.push(parseInt($(this).val()));
    });

    count_service_ids = parseInt(jQuery.inArray(data.id, service_ids));

    if (count_service_ids >= 0) {
        // Clear select2
        $('#search_service').val(null).trigger('change');

        Swal.fire(
            LANG.notice,
            LANG.product_already_added,
            'warning'
        );
        
    } else {
        $.ajax({
            method: 'post',
            url: '/quotes/add-service-block/' + v.variation_id,
            data: {
                warehouse_id: v.warehouse_id,
                tax_detail: tax_detail,
                service_block_index: i,
                view: 'order',
                quote_line_id: v.id
            },
            dataType: 'json',
            success: function (result) {
                if (result.success == 1) {
                    // Add service block
                    let appended = $('#service-blocks').append(result.html_content);

                    __select2($(appended).find('.select2'));

                    blockCont.push(parseInt($('#service-block-index').val()));

                    // Update service block index
                    $('#service-block-index').val(parseInt($('#service-block-index').val()) + 1);

                    if ($("#search_product-" + result.service_block_index).length > 0) {
                        $("#search_product-" + result.service_block_index).autocomplete({
                            source: function (request, response) {
                                $.getJSON(
                                    "/products/list_for_quotes",
                                    {
                                        warehouse_id: $('select#warehouse_id').val(),
                                        term: request.term
                                    },
                                    response
                                );
                            },
                            minLength: 1,
                            delay: 250,
                            response: function (event,ui) {
                                if (ui.content.length == 1) {
                                    ui.item = ui.content[0];

                                } else if (ui.content.length == 0) {
                                    $('input#search_product').select();
                                }
                            },
                            focus: function (event, ui) {
                                if (ui.item.qty_available <= 0) {
                                    return false;
                                }
                            },
                            select: function (event, ui) {
                                if (ui.item.enable_stock != 1 || ui.item.qty_available > 0) {
                                    $(this).val(null);
                                    
                                    warehouse_id = $('select#warehouse_id').val();
                                    tax_detail = $('select#tax_detail').val();
                                    
                                    let ids = get_spare_ids($(this).data('service-block-index'));

                                    if (jQuery.inArray(ui.item.variation_id, ids) >= 0) {
                                        Swal.fire(LANG.notice, LANG.product_already_added, 'warning');

                                    } else {
                                        add_spare(
                                            ui.item.variation_id,
                                            $(this).data('service-block-index'),
                                            $(this).data('service-parent-id')
                                        );
                                    }
                                    
                                } else {
                                    alert(LANG.out_of_stock);
                                }
                            }
                        })
                        .autocomplete('instance')._renderItem = function (ul, item) {
                            if (item.enable_stock == 1 && item.qty_available <= 0) {
                                let string = '<li class="ui-state-disabled"> ' + item.name;

                                if (item.type == 'variable') {
                                    string += '-' + item.variation;
                                }

                                let selling_price = item.selling_price;

                                if (item.variation_group_price) {
                                    selling_price = item.variation_group_price;
                                }

                                string += ' (' + item.sub_sku + ')' + '<br>' + LANG.price + ':$' + selling_price
                                    + ' (' + LANG.out_of_stock + ') </li>';

                                return $(string).appendTo(ul);

                            } else {
                                let string =  '<div>' + item.name;

                                if (item.type == 'variable') {
                                    string += '-' + item.variation;
                                }

                                let selling_price = item.selling_price;

                                if (item.variation_group_price) {
                                    selling_price = item.variation_group_price;
                                }

                                string += ' (' + item.sub_sku + ')' + '<br> <b>' + LANG.price + ':</b>$' + selling_price + ' <b>'
                                    + LANG.stock + ':</b>' + Math.round(item.qty_available, 0);
                                
                                if (item.variations) {
                                    string += '<br>';

                                    $.each(item.variations, function (k, v) {
                                        string += '<b>' + v.price_group + ':</b> $' + v.price_inc_tax + ' ';
                                    });
                                }

                                string += ' </div>';

                                return $('<li>').append(string).appendTo(ul);
                            }
                        };
                    }

                    $.ajax({
                        type: 'post',
                        url: '/orders/get-spare-lines',
                        data: {
                            quote_id : data.id,
                            service_block_index: result.service_block_index,
                            service_parent_id: v.variation_id,
                        },
                        dataType: 'html',
                        success: function (tr) {
                            let location = '#list-' + result.service_block_index;

                            $(location).append(tr);

                            update_rows_workshop(location);
                        }
                    }).done(function(){
                        $("input#subtotal").val(calculate_subtotal_workshop());
            
                        data.discount_type ? $("select#discount_type").val(data.discount_type).change() : $("select#discount_type").val("fixed").change();
            
                        if (data.discount_amount) {
                            var subtotal = $("input#subtotal").val();
            
                            $("input#discount_amount").val(parseFloat(data.discount_amount).toFixed(4));
            
                            var discount_amount = $("input#discount_amount").val();
                            var discount_type = $("select#discount_type").val();
                            var discount = __calculate_amount(discount_type, discount_amount, subtotal);
            
                            $("span#discount_calculated_amount_text").text(__currency_trans_from_en(discount.toFixed(2)));
                            __write_number($("input#discount_calculated_amount"), discount, false, 4);
            
                        } else{
                            $("span#discount_calculated_amount_text").text(__currency_trans_from_en(0.00));
                            __write_number($("input#discount_calculated_amount"), 0.00);
                        }
                    });
                }
            }
        });
    }
}

function calculate_line_discount(tr){
    var tax_detail = $("select#tax_detail").val();
    var quantity_line = __read_number(tr.find("input#quantity"));
    var unit_price = tax_detail == "yes" ? __read_number(tr.find("input#unit_price_exc_tax")) : __read_number(tr.find("input#unit_price_inc_tax"));
    var discount_line_type = tr.find("select#discount_line_type").val();
    var discount_line_amount = __read_number(tr.find("input#discount_line_amount"));
    var discount_calculated_line_amount_text = tr.find("span#discount_calculated_line_amount_text");
    var discount_calculated_line_amount = tr.find("input#discount_calculated_line_amount");


    var discount = __calculate_amount(discount_line_type, discount_line_amount, unit_price) * quantity_line;
    discount_calculated_line_amount_text.text(__currency_trans_from_en(discount.toFixed(2)));
    __write_number(discount_calculated_line_amount, discount, false, 4);

    tr = discount_calculated_line_amount.closest("tr");
    calculate_row_line_total(tr);
    calculate_tax_line_amount(tr, discount)
}

function calculate_tax_line_amount(tr, discount){
    var tax_detail = $("select#tax_detail").val();
    var tax_line_amount = tr.find("input#tax_line_amount");
    var tax_percent = tr.find("input#tax_percent");
    var quantity_line = __read_number(tr.find("input#quantity"));
    var unit_price_inc_tax = __read_number(tr.find("input#unit_price_inc_tax"));
    var tax_line_amount_value = 0.00;
    
    if(tax_detail == "no"){
        var line_total_inc_tax = (quantity_line * unit_price_inc_tax) - discount;
        tax_line_amount_value = (line_total_inc_tax / (tax_percent + 1) * tax_percent)
    }
    __write_number(tax_line_amount, tax_line_amount_value, false, 4);
}

function calculate_row_line_total(tr){
    var tax_detail = $("select#tax_detail").val();
    var quantity_line = __read_number(tr.find("input#quantity"));
    var unit_price = tax_detail == "yes" ? __read_number(tr.find("input#unit_price_exc_tax")) : __read_number(tr.find("input#unit_price_inc_tax"));
    var discount_calculated_line_amount = __read_number(tr.find("input#discount_calculated_line_amount"));
    var line_total_text = tr.find("span#line_total_text");
    var line_total = tr.find("input#line_total");

    var row_line_total = ((quantity_line * unit_price) - discount_calculated_line_amount);
    __write_number(line_total, row_line_total, false, 4);
    line_total_text.text(__currency_trans_from_en(row_line_total.toFixed(2)));

    calculate_discount();
}

function calculate_subtotal(){
    var subtotal_amount = 0;
    
    $("table#order_table tbody tr").each(function(){
        var line_total = __read_number($(this).find("input#line_total"));
        subtotal_amount += line_total;
    });

    return subtotal_amount.toFixed(4);
}

function get_tax_percent(){
    var tax_percent = 0;
    
    $("table#order_table tbody tr").each(function(){
        tax_percent = __read_number($(this).find("input#tax_percent"));
    });

    return tax_percent;
}

function calculate_discount() {
    if (app_business == 'workshop') {
        var subtotal = calculate_subtotal_workshop();
    } else {
        var subtotal = calculate_subtotal();
    }

    var discount_type = $("select#discount_type").val();
    var discount_amount = __read_number($("input#discount_amount"));
    var discount_calculated_amount_text = $("span#discount_calculated_amount_text");
    var discount_calculated_amount = $("input#discount_calculated_amount");

    var discount = __calculate_amount(discount_type, discount_amount, subtotal);
    discount_calculated_amount_text.text(__currency_trans_from_en(discount.toFixed(2)));
    __write_number(discount_calculated_amount, discount, false, 4);

    calculate_taxes(discount);
}

function calculate_taxes(discount){
    var tax_detail = $("select#tax_detail").val();
    var tax_amount = $("input#tax_amount");
    var tax_amount_text = $("span#tax_amount_text");
    var tax_amount_value = 0;

    if (tax_detail == "yes") {
        if (app_business == 'workshop') {
            var tax_percent = get_tax_percent_workshop();
            var subtotal = calculate_subtotal_workshop();

        } else {
            var tax_percent = get_tax_percent();
            var subtotal = calculate_subtotal();
        }

        var subtotal_amount = subtotal - discount;

        tax_amount_value = subtotal_amount * tax_percent;
        tax_amount_text.text(__currency_trans_from_en(tax_amount_value.toFixed(2)));
        __write_number(tax_amount, tax_amount_value, false, 4);

    } else {
        tax_amount_text.text(__currency_trans_from_en(tax_amount_value.toFixed(2)));
        __write_number(tax_amount, tax_amount_value, false, 4);
    }

    calculate_total_final();
}

function calculate_total_final() {
    var discount_calculated_amount = __read_number($("input#discount_calculated_amount"));
    var tax_detail = $("select#tax_detail").val();
    var subtotal_text = $("span#subtotal_text");
    var subtotal = $("input#subtotal");
    var total_final_text = $("span#total_final_text");
    var total_final = $("input#total_final");

    if (app_business == 'workshop') {
        var subtotal_value = calculate_subtotal_workshop();
    } else {
        var subtotal_value = calculate_subtotal();
    }

    var total_total_value = 0;

    if(tax_detail == "yes"){
        var tax_amount = __read_number($("input#tax_amount"));
        total_total_value = (subtotal_value - discount_calculated_amount) + tax_amount;
    } else{
        total_total_value = subtotal_value - discount_calculated_amount;
    }

    subtotal_text.text(__currency_trans_from_en(subtotal_value));
    total_final_text.text(__currency_trans_from_en(total_total_value));
    __write_number(subtotal, subtotal_value, false, 4);
    __write_number(total_final, total_total_value, false, 4);
}

function update_unit_price(tr){
    var unit_price = __read_number(tr.find("input.unit_price_text"));
    var tax_detail = $("select#tax_detail").val();
    var unit_price_exc_tax = tr.find("input#unit_price_exc_tax");
    var unit_price_inc_tax = tr.find("input#unit_price_inc_tax");
    var tax_percent = __read_number(tr.find("input#tax_percent")) + 1;

    if(tax_detail == "yes"){
        __write_number(unit_price_exc_tax, unit_price, false, 4);
        __write_number(unit_price_inc_tax, (unit_price * tax_percent), false, 4);
    } else{
        __write_number(unit_price_exc_tax, (unit_price / tax_percent), false, 4);
        __write_number(unit_price_inc_tax, unit_price, false, 4);
    }

    tr = unit_price_exc_tax.closest("tr");
    calculate_row_line_total(tr);
}

function showHideFields(){
    var other_delivery_type_div = $("div#other_delivery_type_div");

    if($("select#delivery_type").val() == "other"){
        other_delivery_type_div.show();
    } else{
        other_delivery_type_div.hide();
        other_delivery_type_div.find("input#other_delivery_type").val("");
    }
}

/**
 * Get customer vehicles.
 * @param  int  id
 * @return void
 */
function getCustomerVehicles(id) {
    $('#customer_vehicle_id').empty();

    let route = '/quotes/get-customer-vehicles/' + id;

    $.get(route, function (res) {
        $('#customer_vehicle_id').append('<option value="0" disabled selected>' + LANG.select_please + '</option>');

        $(res).each(function(key, value) {
            $('#customer_vehicle_id').append('<option value="' + value.id + '">' + value.name + '</option>');
        });
    });
}

/**
 * Add spares to service block.
 * 
 * @param  int  $variation_id
 * @param  int  $service_block_index
 * @param  int  $row_index
 * @param  int  $service_parent_id
 * @return void
 */
 function add_spare(variation_id, service_block_index, service_parent_id) {
    let tax_detail = $("#tax_detail").val();
    let warehouse_id = $('select#warehouse_id').val();

    $.ajax({
        type: 'post',
        url: '/orders/get-spare-lines',
        data: {
            variation_id: variation_id,
            warehouse_id: warehouse_id,
            tax_detail: tax_detail,
            service_block_index: service_block_index,
            service_parent_id: service_parent_id
        },
        dataType: 'html',
        success: function (tr) {
            let location = '#list-' + service_block_index;

            $(location).append(tr);

            update_rows_workshop(location);
        }
    }).done(function () {
        calculate_discount();
    });
}

/**
 * Update elements row and row number.
 * 
 * @param  string  location
 * @return void
 **/
function update_rows_workshop(location) {
    let service_blocks = $('#service-blocks');
    let index = 0;

    service_blocks.find('div.panel').each(function (i, v) {
        let selects = $(this).find('table.table-service tbody tr:first select');
        let inputs = $(this).find('table.table-service tbody tr:first input');
        let textarea = $(this).find('textarea:first');

        update_selects_workshop(index, selects);
        update_inputs_workshop(index, inputs);
        update_textareas_workshop(index, textarea);

        $(this).find('table.table-spares tr').each(function (i2, v2) {
            let selects = $(this).find('select');
            let inputs = $(this).find('input');

            update_selects_workshop(index, selects);
            update_inputs_workshop(index, inputs);

            index++;
        });
    });

    var table_rows = $(location + ' tr');

    $.each(table_rows, function(i, row){
        var row_no = $(this).find("span#row_no");
        row_no.text(parseInt(i) + 1);
    });

    function update_selects_workshop(i, selects) {
        var id = 'N/A';

        selects.each(function () {
            id = $(this).attr('id') == undefined ? 'N/A' : $(this).attr('id') ;
            $(this).attr("name", "order_lines["+ i +"]["+ id +"]");
        });
    }

    function update_inputs_workshop(i, inputs) {
        var id = 'N/A';

        inputs.each(function () {
            id = $(this).attr('id') == undefined ? 'N/A' : $(this).attr('id') ;
            $(this).attr("name", "order_lines["+ i +"]["+ id +"]");
        });
    }

    function update_textareas_workshop(i, textareas) {
        var id = 'N/A';

        textareas.each(function () {
            id = $(this).attr('id') == undefined ? 'N/A' : $(this).attr('id') ;
            $(this).attr("name", "order_lines["+ i +"]["+ id +"]");
        });
    }
}

/**
 * Calculate subtotal.
 * 
 * @return float
 */
function calculate_subtotal_workshop() {
    let subtotal_amount = 0;

    $('div#service-blocks div.panel').each(function () {
        let subtotal_block = 0;

        $(this).find('table.table-spares tbody tr').each(function () {
            let line_total = __read_number($(this).find('input#line_total'));

            subtotal_amount += line_total;
            subtotal_block += line_total;
        });

        $(this).find('input.line_total_block').val(subtotal_block.toFixed(4));
    });

    return subtotal_amount.toFixed(4);
}

/**
 * Get tax percentage.
 * 
 * @return float
 */
function get_tax_percent_workshop() {
    var tax_percent = 0;

    $('div#service-blocks div.panel').each(function () {
        $(this).find('table.table-spares tbody tr').each(function () {
            tax_percent = __read_number($(this).find('input#tax_percent'));
        });
    });

    return tax_percent;
}
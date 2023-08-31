$(document).ready( function(){

    //Purchase & Sell report
    //Date range as a button
    if($('#purchase_sell_date_filter').length == 1){
        $('#purchase_sell_date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#purchase_sell_date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                updatePurchaseSell();
            }
        );
        $('#purchase_sell_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#purchase_sell_date_filter').html('<i class="fa fa-calendar"></i> ' + LANG.filter_by_date);
        });
        updatePurchaseSell();
    }

    //Supplier report
    supplier_report_tbl = $('#supplier_report_tbl').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: '/reports/customer-supplier',
                            columnDefs: [ 
                                {"targets": [6], "orderable": false, "searchable": false},
                                {"targets": [2, 3, 4, 5], "searchable": false},
                            ],
                            columns: [
                                {data: 'contact_id', name: 'contact_id'},
                                {data: 'name', name: 'name'},
                                {data: 'total_purchase', name: 'total_purchase'},
                                {data: 'total_purchase_return', name: 'total_purchase_return'},
                                {data: 'total_invoice', name: 'total_invoice'},
                                {data: 'total_sell_return', name: 'total_sell_return'},
                                {data: 'opening_balance_due', name: 'opening_balance_due'},
                                {data: 'due', name: 'due'}
                            ],
                            "fnDrawCallback": function (oSettings) {
                                __currency_convert_recursively($('#supplier_report_tbl'));
                            }
                        });

    //Stock report table
    stock_report_table = $('#stock_report_table').DataTable({
                            processing: true,
                            serverSide: true,
                            "ajax": {
                                "url": "/reports/stock-report",
                                "data": function ( d ) {
                                    d.location_id = $('#location_id').val();
                                    d.category_id = $('#category_id').val();
                                    d.sub_category_id = $('#sub_category_id').val();
                                    d.brand_id = $('#brand').val();
                                    d.unit_id = $('#unit').val();
                                }
                            },
                            columns: [
                                {data: 'sku', name: 'variations.sub_sku'},
                                {data: 'product', name: 'p.name'},
                                {data: 'unit_price', name: 'variations.sell_price_inc_tax'},
                                {data: 'stock', name: 'stock', searchable: false},
                                {data: 'total_sold', name: 'total_sold', searchable: false},
                                {data: 'total_transfered', name: 'total_transfered', searchable: false},
                                {data: 'total_adjusted', name: 'total_adjusted', searchable: false}
                            ],
                            "fnDrawCallback": function (oSettings) {
                                $('#footer_total_stock').html(__sum_stock($('#stock_report_table'), 'current_stock'));
                                $('#footer_total_sold').html(__sum_stock($('#stock_report_table'), 'total_sold'));
                                $('#footer_total_transfered').html(__sum_stock($('#stock_report_table'), 'total_transfered'));
                                $('#footer_total_adjusted').html(__sum_stock($('#stock_report_table'), 'total_adjusted'));
                                __currency_convert_recursively($('#stock_report_table'));
                            }
                        });

    // New stock report table
    show_stock_report_table = $('#show_stock_report_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[1, 'asc']],
        "ajax": {
            "url": "/product-reports/show-stock-report",
            "data": function (d) {
                d.location_id = $('#location_id').val();
                d.warehouse_id = $('select#warehouse_id').val();
                d.category_id = $('#category_id').val();
                d.sub_category_id = $('#sub_category_id').val();
                d.brand_id = $('#brand_id').val();
                d.unit_id = $('#unit_id').val();
                d.contact_id = $('#contact_id').val();
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.stock_filter = $('#stock_filter').is(':checked') ? 1 : 0;
            }
        },
        columns: [
            { data: 'sku', name: 'sku' },
            { data: 'product', name: 'product' },
            { data: 'category', name: 'category' },
            { data: 'sub_category', name: 'sub_category' },
            { data: 'brand', name: 'brand' },
            { data: 'unit_cost', name: 'unit_cost', searchable: false },
            { data: 'unit_price', name: 'unit_price', searchable: false },
            { data: 'stock', name: 'stock', searchable: false },
            { data: 'vld_stock', name: 'vld_stock', searchable: false },
            { data: 'total_value', name: 'total_value', searchable: false },
            { data: 'total_sold', name: 'total_sold', searchable: false },
        ],
        "fnDrawCallback": function (oSettings) {
            $('#footer_total_stock').html(__sum_column($('#show_stock_report_table'), 'stock'));
            $('#footer_total_vld_stock').html(__sum_column($('#show_stock_report_table'), 'vld_stock'));
            $('#footer_total_value').html(__sum_column($('#show_stock_report_table'), 'total_value'));
            $('#footer_total_sold').html(__sum_column($('#show_stock_report_table'), 'total_sold'));
            __currency_convert_recursively($('#show_stock_report_table'));
        }
    });

    $('#form_stock_report #location_id, form#form_stock_report select#warehouse_id, #form_stock_report #category_id, #form_stock_report #sub_category_id, #form_stock_report #brand_id, #form_stock_report #unit_id, #form_stock_report #view_stock_filter, #form_stock_report #contact_id').change(function() {
        show_stock_report_table.ajax.reload();
    });

    $('#form_stock_report #stock_filter').on('click', function() {
        show_stock_report_table.ajax.reload();
    });

    if($('#tax_report_date_filter').length == 1){
        $('#tax_report_date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#tax_report_date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                updateTaxReport();
            }
        );
        $('#tax_report_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#tax_report_date_filter').html('<i class="fa fa-calendar"></i> ' + LANG.filter_by_date);
        });
        updateTaxReport();
    }

    if($('#trending_product_date_range').length == 1){
        get_sub_categories();
        $('#trending_product_date_range').daterangepicker({
            ranges: ranges,
            autoUpdateInput: false,
            locale: {
                format: moment_date_format,
                cancelLabel: LANG.clear,
                applyLabel: LANG.apply,
                customRangeLabel: LANG.custom_range
            }
        });
        $('#trending_product_date_range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format(moment_date_format) + ' ~ ' + picker.endDate.format(moment_date_format));
        });

        $('#trending_product_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    }

    $('#stock_report_filter_form #location_id, #stock_report_filter_form #category_id, #stock_report_filter_form #sub_category_id, #stock_report_filter_form #brand, #stock_report_filter_form #unit,#stock_report_filter_form #view_stock_filter').change( function(){
        stock_report_table.ajax.reload();
        stock_expiry_report_table.ajax.reload();
    });

    $('#purchase_sell_location_filter').change( function(){
        updatePurchaseSell();
    });
    $('#tax_report_location_filter').change( function(){
        updateTaxReport();
    });

    //Stock Adjustment Report
    if($('#stock_adjustment_date_filter').length == 1){
        $('#stock_adjustment_date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#stock_adjustment_date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                updateStockAdjustmentReport();
            }
        );
        $('#stock_adjustment_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#stock_adjustment_date_filter').html('<i class="fa fa-calendar"></i> ' + LANG.filter_by_date);
        });
        updateStockAdjustmentReport();
    }

    $('#stock_adjustment_location_filter').change( function(){
        updateStockAdjustmentReport();
    });

    //Register report
    register_report_table = $('#register_report_table').DataTable({
                            processing: true,
                            serverSide: true,
                            aaSorting: [[2, 'desc']],
                            ajax: '/reports/register-report',
                            columnDefs: [ 
                                {"targets": [8], "orderable": false, "searchable": false},
                            ],
                            columns: [
                                {data: 'cash_register_name', name: 'cashiers.name'},
                                {data: 'status', name: 'status'},
                                {data: 'date', name: 'date'},
                                {data: 'total_amount_cash', name: 'total_amount_cash'},
                                {data: 'total_amount_card', name: 'total_amount_card'},
                                {data: 'total_amount_check', name: 'total_amount_check'},
                                {data: 'total_amount_transfer', name: 'total_amount_transfer'},
                                {data: 'total_amount_credit', name: 'total_amount_credit'},
                                {data: 'action', name: 'action'}
                            ],
                            "fnDrawCallback": function (oSettings) {
                                __currency_convert_recursively($('#register_report_table'));
                            }
                        });
    $('.view_register').on('shown.bs.modal', function () {
        __currency_convert_recursively($(this));
    });
    $(document).on( 'submit', '#register_report_filter_form', function(e){
        e.preventDefault();
        updateRegisterReport();
    });

    $('#register_cachier_id, #register_status').change( function(){
        updateRegisterReport();
    });

    //Daily z cut report
    var daily_z_cut_report_table = $('table#daily_z_cut_report_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[1, 'desc']],
        ajax: {
            url: '/reports/daily-z-cut-report',
            data: function(d){
                d.cashier_id = $('select#cashier').val()
            }
        },
        columnDefs: [ 
            {
                "targets": [9],
                "orderable": false,
                "searchable": false
            },
        ],
        columns: [
            { data: 'cashier_name', name: 'c.name' },
            { data: 'close_date', name: 'close_date' },
            { data: 'close_correlative', name: 'close_correlative' },
            { data: 'total_cash_amount', name: 'total_cash_amount' },
            { data: 'total_card_amount', name: 'total_card_amount' },
            { data: 'total_check_amount', name: 'total_check_amount' },
            { data: 'total_bank_transfer_amount', name: 'total_bank_transfer_amount' },
            { data: 'total_return_amount', name: 'total_return_amount' },
            { data: 'total_physical_amount', name: 'total_physical_amount' },
            { data: 'action', name: 'action' }
        ],
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('#daily_z_cut_report_table'));
        }
    });

    /** Filter by cashier */
    $(document).on("change", "select#cashier", function(){
        daily_z_cut_report_table.ajax.reload();
    });
    
    /** Show daily z cut modal */
    $(document).on('click', 'a.view_daily_z_cut', function(e){
        e.preventDefault();

        $('div.daily_z_cut_modal').load($(this).attr('href'), function(){
            $(this).modal('show');
            __currency_convert_recursively($('table#closure_details'));
        });
    });

    /** Recalculate cashier closure */
    $(document).on('click', 'a.recalc_cc', function (e) {
        e.preventDefault();
        let btn = $(this);
        btn.addClass("disabled");

        swal({
			title: LANG.sure,
            text: LANG.cashier_closure_update,
			icon: "warning",
			buttons: true,
			dangerMode: true,
		}).then((confirm) => {
			if (confirm) {
                $.ajax({
                    method: "get",
                    url: btn.attr('href'),
                    success: function(response){
                        if(response.success){
                            toastr.success(response.msg);
                            daily_z_cut_report_table.ajax.reload();
                            
                        } else{
                            toastr.error(response.msg);
                        }

                        btn.removeClass('disabled');
                    }
                });
			} else {
                btn.removeClass('disabled');
            }
		});
    });

    /** Generate accounting entry */
    $(document).on("click", "a.create_acc_entry", function(e){
        e.preventDefault();
        var btn = $(this);

        swal({
            title: LANG.sure,
			text: LANG.generate_accounting_entry,
			icon: "warning",
			buttons: true,
			dangerMode: true,
		}).then((confirm) => {
			if (confirm) {
                $.ajax({
                    method: "get",
                    url: btn.attr("href"),
                    success: function(response){
                        if(response.success){
                            toastr.success(response.msg);
                            btn.prop("disabled", "disabled");
                        } else{
                            toastr.error(response.msg);
                        }
                    }
                });
			}
		});
    });
    /*
    $('.view_register').on('shown.bs.modal', function () {
    __currency_convert_recursively($(this));
    });*/

    /** Sales summary by seller */

    var sales_summary_report = $("table#sales_summary_by_seller").DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[1, 'desc']],
        ajax: {
            method: "get",
            url: "/reports/sales-summary-report",
            data: function(d){
                d.start_date = $('form#form_sales_summary input.start_date').val();
                d.end_date = $('form#form_sales_summary input.end_date').val();
                d.location_id = $('form#form_sales_summary select.location_id').val();
            },
        },
        columns: [
            { data: 'sku', name: 'sku' },
            { data: 'product_name', name: 'product_name' },
            { data: 'category', name: 'category' },
            { data: 'quantity', name: 'quantity' },
            { data: 'total_sale', name: 'total_sale' },
            { data: 'employee_name', name: 'employee_name' }
        ],
        "fnDrawCallback": function (oSettings) {
            //$('#footer_sale_total').text(sum_table_col($('#all_sales_report_table'), 'final-total'));
            __currency_convert_recursively($('form#form_sales_summary table#sales_summary_by_seller'));
        }
    });

    $(document).on("change", "form#form_sales_summary select.location_id", function(){
        sales_summary_report.ajax.reload();
    });

    //Datetime picker
    $('form#form_sales_summary input.start_date, form#form_sales_summary input.end_date').datetimepicker({
        format: moment_date_format,
        ignoreReadonly: true
    }).on("dp.change", function(){
        sales_summary_report.ajax.reload();
    });

    /** Sales summary by seller */
    var sales_by_seller = $("table#sales_by_seller").DataTable({
        processing: true,
        serverSide: true,
        //aaSorting: [[1, 'desc']],
        ajax: {
            method: "get",
            url: "/reports/sales-by-seller-report",
            data: function(d){
                d.start_date = $('form#form_sales_by_seller input.start_date').val();
                d.end_date = $('form#form_sales_by_seller input.end_date').val();
                d.location_id = $('form#form_sales_by_seller select.location_id').val();
            },
        },
        columns: [
            { data: 'location_name', name: 'location_name' },
            { data: 'seller_code', name: 'seller_code' },
            { data: 'seller_name', name: 'seller_name' },
            { data: 'total_before_tax', name: 'total_before_tax', class: 'align-right' },
            { data: 'total_amount', name: 'total_amount', class: 'align-right' }
        ],
        "fnDrawCallback": function (oSettings) {
            $('#footer_total_before_tax').text(sum_table_col($('table#sales_by_seller'), 'total-before-tax'));
            $('#footer_total_amount').text(sum_table_col($('table#sales_by_seller'), 'total-amount'));
            __currency_convert_recursively($('table#sales_by_seller'));
        }
    });

    $(document).on("change", "form#form_sales_by_seller select.location_id", function(){
        sales_by_seller.ajax.reload();
    });

    //Datetime picker
    $('form#form_sales_by_seller input.start_date, form#form_sales_by_seller input.end_date').datetimepicker({
        format: moment_date_format,
        ignoreReadonly: true
    }).on("dp.change", function(){
        sales_by_seller.ajax.reload();
    });

    /** daily inventory */
    if($('#input_output_form button#input_output_date_filter').length == 1){
        dateRangeSettings['startDate'] = moment();
        dateRangeSettings['endDate'] = moment();
        //Date range as a button
        $('#input_output_form button#input_output_date_filter').daterangepicker(
            dateRangeSettings,
            function(start, end) {
                $('#input_output_form button#input_output_date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                
                let start_date = $('#input_output_form button#input_output_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                let end_date = $('#input_output_form button#input_output_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                $("#input_output_form input#start_date").val(start_date);
                $("#input_output_form input#end_date").val(end_date);

                input_output.ajax.reload();
            }
        );
        $('#input_output_form button#input_output_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#input_output_form button#input_output_date_filter').html('<i class="fa fa-calendar"></i>'+ LANG.filter_by_day);
            
            let start_date = $('#input_output_form button#input_output_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
            let end_date = $('#input_output_form button#input_output_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
            $("#input_output_form input#start_date").val(start_date);
            $("#input_output_form input#end_date").val(end_date);

            input_output.ajax.reload();
        });
    }

    var input_output = $("table#input_output").DataTable({
        processing: true,
        serverSide: true,
        //aaSorting: [[1, 'desc']],
        ajax: {
            method: "get",
            url: "/product-reports/input-output-report",
            data: function(d){
                d.start_date = $('form#input_output_form input#start_date').val();
                d.end_date = $('form#input_output_form input#end_date').val();
                d.location = $('form#input_output_form select.location').val();
                d.brand = $('form#input_output_form select.brand').val();
                d.category = $('form#input_output_form select.category').val();
                d.transactions = $('form#input_output_form input#transactions').is(':checked') ? 1 : 0;
                d.stock = $('form#input_output_form input#stock').is(':checked') ? 1 : 0;
            },
        },
        columns: [
            { data: 'sku', name: 'sku' },
            { data: 'product_name', name: 'product_name' },
            { data: 'initial_inventory', name: 'initial_inventory', class: 'align-right' },
            { data: 'purchases', name: 'purchases', class: 'align-right' },
            { data: 'purchase_transfers', name: 'purchase_transfers', class: 'align-right' },
            { data: 'input_stock_adjustments', name: 'input_stock_adjustments', class: 'align-right' },
            { data: 'sell_returns', name: 'sell_returns', class: 'align-right' },
            { data: 'sales', name: 'sales', class: 'align-right' },
            { data: 'sell_transfers', name: 'sell_transfers', class: 'align-right' },
            { data: 'output_stock_adjustments', name: 'output_stock_adjustments', class: 'align-right' },
            { data: 'purchase_returns', name: 'purchase_returns', class: 'align-right' },
            { data: 'stock', name: 'stock', class: 'align-right' },
        ],
        "fnDrawCallback": function (oSettings) {
            $('span#footer_total_initial_inventory').text(sum_table_col($('table#input_output'), 'initial_inventory'));
            $('span#footer_total_purchases').text(sum_table_col($('table#input_output'), 'purchases'));
            $('span#footer_total_in_transfers').text(sum_table_col($('table#input_output'), 'purchase_transfers'));
            $('span#footer_total_in_adjustments').text(sum_table_col($('table#input_output'), 'input_stock_adjustments'));
            $('span#footer_total_sell_returns').text(sum_table_col($('table#input_output'), 'sell_returns'));
            $('span#footer_total_sells').text(sum_table_col($('table#input_output'), 'sales'));
            $('span#footer_total_out_transfers').text(sum_table_col($('table#input_output'), 'sell_transfers'));
            $('span#footer_total_out_adjustments').text(sum_table_col($('table#input_output'), 'output_stock_adjustments'));
            $('span#footer_total_purchase_returns').text(sum_table_col($('table#input_output'), 'purchase_returns'));
            $('span#footer_total_stock').text(sum_table_col($('table#input_output'), 'stock'));
            __currency_convert_recursively($('table#input_output'));
        }
    });

    $(document).on("change", "form#input_output_form select.location, form#input_output_form select.brand, form#input_output_form select.category",
        function(){
            input_output.ajax.reload();
    });

    $(document).on("change", "form#input_output_form input#stock, form#input_output_form input#transactions",
        function(){
            input_output.ajax.reload();
    });

    /** Dispatched products */
    if($('#dispatched_products_form button#dispatched_products_date_filter').length == 1){
        dateRangeSettings['startDate'] = moment();
        dateRangeSettings['endDate'] = moment();
        //Date range as a button
        $('#dispatched_products_form button#dispatched_products_date_filter').daterangepicker(
            dateRangeSettings,
            function(start, end) {
                $('#dispatched_products_form button#dispatched_products_date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                
                let start_date = $('#dispatched_products_form button#dispatched_products_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                let end_date = $('#dispatched_products_form button#dispatched_products_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                $("#dispatched_products_form input#start_date").val(start_date);
                $("#dispatched_products_form input#end_date").val(end_date);

                update_product_counts();
                dispatched_products.ajax.reload();
            }
        );
        $('#dispatched_products_form button#dispatched_products_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#dispatched_products_form button#dispatched_products_date_filter').html('<i class="fa fa-calendar"></i>'+ LANG.filter_by_day);
            
            let start_date = $('#dispatched_products_form button#dispatched_products_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
            let end_date = $('#dispatched_products_form button#dispatched_products_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
            $("#dispatched_products_form input#start_date").val(start_date);
            $("#dispatched_products_form input#end_date").val(end_date);
            
            update_product_counts();
            dispatched_products.ajax.reload();
        });
    }

    $(document).on('submit', 'form#dispatched_products_form', function(e){
        let count = $('form#dispatched_products_form input#product_counts').val();
        let format = $('form#dispatched_products_form select#report_format :selected').val();

        if(count > 6 && format == 'pdf') {
            e.preventDefault();

            Swal.fire
            ({
                title: LANG.notice,
                text: LANG.product_higher_than_n,
                icon: "info",
            });
        }
    });
    var dispatched_products = $("table#dispatched_products").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            method: "get",
            url: "/reports/dispatched-products-report",
            data: function(d){
                d.start_date = $('form#dispatched_products_form input#start_date').val();
                d.end_date = $('form#dispatched_products_form input#end_date').val();
                d.location = $('form#dispatched_products_form select.location').val();
                d.seller = $('form#dispatched_products_form select.seller').val();
            },
        }, columns: [
            { data: 'customer_name', name: 'customer_name' },
            { data: 'seller_name', name: 'seller_name' },
            { data: 'doc', name: 'doc' },
            { data: 'qty_total', name: 'qty_total', class: 'align-right' },
            { data: 'weight_total', name: 'weight_total', class: 'align-right' },
            { data: 'final_total', name: 'final_total', class: 'align-right' }
        ],
        "fnDrawCallback": function (oSettings) {
            $('#footer_total_qty').text(sum_table_col($('table#dispatched_products'), 'qty_total'));
            $('#footer_total_weight').text(sum_table_col($('table#dispatched_products'), 'weight_total'));
            $('#footer_total_final').text(sum_table_col($('table#dispatched_products'), 'final_total'));
            __currency_convert_recursively($('table#dispatched_products'));
        }
    });

    $(document).on("change", "form#dispatched_products_form select.location, form#dispatched_products_form select.seller", function(){
        update_product_counts();
        dispatched_products.ajax.reload();
    });

    function update_product_counts () {
        let start_date = $('form#dispatched_products_form input#start_date').val();
        let end_date = $('form#dispatched_products_form input#end_date').val();
        let location = $('form#dispatched_products_form select.location').val();
        let seller = $('form#dispatched_products_form select.seller').val();

        $.ajax({
            type: "get",
            url: '/reports/dispatched-products-count',
            data: {
                start_date: start_date,
                end_date: end_date,
                location: location,
                seller: seller
            },
            success: function(data) {
                $('form#dispatched_products_form input#product_counts').val(data);
            }
        });
    }

    /** Connect report for Disproci */
    if($('form#connect_report_form button#connect_report_date_filter').length == 1){
        dateRangeSettings['startDate'] = moment();
        dateRangeSettings['endDate'] = moment();
        //Date range as a button
        $('form#connect_report_form button#connect_report_date_filter').daterangepicker(
            dateRangeSettings,
            function(start, end) {
                $('form#connect_report_form button#connect_report_date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                
                let start_date = $('form#connect_report_form button#connect_report_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                let end_date = $('form#connect_report_form button#connect_report_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                $("form#connect_report_form input#start_date").val(start_date);
                $("form#connect_report_form input#end_date").val(end_date);

                connect_report.ajax.reload();
            }
        );
        $('form#connect_report_form button#connect_report_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('form#connect_report_form button#connect_report_date_filter').html('<i class="fa fa-calendar"></i>'+ LANG.filter_by_day);
            
            let start_date = $('form#connect_report_form button#connect_report_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
            let end_date = $('form#connect_report_form button#connect_report_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
            $("form#connect_report_form input#start_date").val(start_date);
            $("form#connect_report_form input#end_date").val(end_date);

            connect_report.ajax.reload();
        });
    }

    var connect_report = $("table#connect_report").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            method: "get",
            url: "/reports/connect-report",
            data: function(d){
                d.start_date = $('form#connect_report_form input#start_date').val();
                d.end_date = $('form#connect_report_form input#end_date').val();
                d.location = $('form#connect_report_form select.location').val();
                d.seller = $('form#connect_report_form select.seller').val();
            },
        }, columns: [
            { data: 'customer_name', name: 'customer_name' },
            { data: 'latitude', name: 'latitude', class: 'align-right' },
            { data: 'length', name: 'length', class: 'align-right' },
            { data: 'from', name: 'from', class: 'align-right' },
            { data: 'to', name: 'to', class: 'align-right' },
            { data: 'cost', name: 'latitude', class: 'align-right' },
            { data: 'weight', name: 'weight', class: 'align-right' },
            { data: 'volume', name: 'volume', class: 'align-right' },
            { data: 'download_time', name: 'download_time', class: 'align-right' }
        ],
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('table#connect_report'));
        }
    });

    $(document).on("change", "form#connect_report_form select.location, form#connect_report_form select.seller", function(){
        connect_report.ajax.reload();
    });

    $(document).on('submit', 'form#list_price_report_form', function(e){
        let count = $('form#list_price_report_form input#count').val();
        let format = $('form#list_price_report_form select#report_format :selected').val();
        
        if(count > 5 && format == 'pdf') {
            e.preventDefault();

            Swal.fire
            ({
                title: LANG.notice,
                text: LANG.list_price_higher_than_n,
                icon: "info",
            });
        }
    });

    /** List price report */
    var list_price_report = $("table#list_price_report").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            method: "get",
            url: "/product-reports/list-price-report",
            data: function(d){
                d.brand = $('form#list_price_report_form select.brand').val();
                d.category = $('form#list_price_report_form select.category').val();
            },
        }, columns: [
            { data: 'sku', name: 'v.sub_sku' },
            { data: 'product_name', name: 'product_name' },
            { data: 'brand_name', name: 'b.name' },
            { data: 'category_name', name: 'c.name' },
            { data: 'default_price', name: 'v.sell_price_inc_tax', class: 'align-right' }
        ],
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('table#list_price_report'));
        }
    });

    $(document).on("change", "form#list_price_report_form select.brand, form#list_price_report_form select.category", function(){
        list_price_report.ajax.reload();
    });
    
    /** expense summary report */
    var expense_summary = $("table#expense_summary_report_table").DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'asc']],
        ajax: {
            method: "get",
            url: "/reports/expense-purchase-report",
            data: function(d){
                d.year = $('select#year').val();
                d.location = $('select#location').val();
            },
        },
        columns: [
            { data: 'description', name: 'description' },
            { data: 'jan', name: 'jan' },
            { data: 'feb', name: 'feb' },
            { data: 'mar', name: 'mar' },
            { data: 'apr', name: 'apr' },
            { data: 'may', name: 'may' },
            { data: 'jun', name: 'jun' },
            { data: 'jul', name: 'jul' },
            { data: 'aug', name: 'aug' },
            { data: 'sep', name: 'sep' },
            { data: 'oct', name: 'oct' },
            { data: 'nov', name: 'nov' },
            { data: 'dec', name: 'dec' },
            { data: 'total', name: 'total' }
        ],
        "fnDrawCallback": function (oSettings) {
            $('table#expense_summary_report_table span#footer_jan').text(sum_table_col($('#expense_summary_report_table'), 'jan'));
            $('table#expense_summary_report_table span#footer_feb').text(sum_table_col($('#expense_summary_report_table'), 'feb'));
            $('table#expense_summary_report_table span#footer_mar').text(sum_table_col($('#expense_summary_report_table'), 'mar'));
            $('table#expense_summary_report_table span#footer_apr').text(sum_table_col($('#expense_summary_report_table'), 'apr'));
            $('table#expense_summary_report_table span#footer_may').text(sum_table_col($('#expense_summary_report_table'), 'may'));
            $('table#expense_summary_report_table span#footer_jun').text(sum_table_col($('#expense_summary_report_table'), 'jun'));
            $('table#expense_summary_report_table span#footer_jul').text(sum_table_col($('#expense_summary_report_table'), 'jul'));
            $('table#expense_summary_report_table span#footer_aug').text(sum_table_col($('#expense_summary_report_table'), 'aug'));
            $('table#expense_summary_report_table span#footer_sep').text(sum_table_col($('#expense_summary_report_table'), 'sep'));
            $('table#expense_summary_report_table span#footer_oct').text(sum_table_col($('#expense_summary_report_table'), 'oct'));
            $('table#expense_summary_report_table span#footer_nov').text(sum_table_col($('#expense_summary_report_table'), 'nov'));
            $('table#expense_summary_report_table span#footer_dev').text(sum_table_col($('#expense_summary_report_table'), 'dev'));
            $('table#expense_summary_report_table span#footer_total').text(sum_table_col($('#expense_summary_report_table'), 'total'));
            __currency_convert_recursively($('table#expense_summary_report_table'));
        }
    });

    /** on change year or location */
    $(document).on("change", "select#year, select#location", function(){
        expense_summary.ajax.reload();
    });

    //Sales representative report
    if($('#sr_date_filter').length == 1){
        
        //date range setting
        $('input#sr_date_filter').daterangepicker(dateRangeSettings, 
            function (start, end) {
               $('input#sr_date_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                updateSalesRepresentativeReport();
            }
        );
        $('input#sr_date_filter').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format(moment_date_format) + ' ~ ' + picker.endDate.format(moment_date_format));
        });

        $('input#sr_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        //Sales representative report -> Total expense
        if($('span#sr_total_expenses').length > 0){
            salesRepresentativeTotalExpense();
        }
        //Sales representative report -> Total sales
        if($('span#sr_total_sales').length > 0){
            salesRepresentativeTotalSales();
        }

        //Sales representative report -> Sales
        sr_sales_report = $('table#sr_sales_report').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/sells",
                "data": function ( d ) {
                    var start = $('input#sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('input#sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                    d.created_by = $('select#sr_id').val(),
                    d.location_id = $('select#sr_business_id').val(),
                    d.start_date = start,
                    d.end_date = end
                }
            },
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'customer_name', name: 'customer_name'},
                { data: 'location', name: 'location'},
                { data: 'payment_status', name: 'payment_status'},
                { data: 'final_total', name: 'final_total'},
                { data: 'total_paid', name: 'total_paid'},
                { data: 'total_remaining', name: 'total_remaining'}
            ],
            columnDefs: [
                    {
                        'searchable'    : false, 
                        'targets'       : [6] 
                    },
                ],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#sr_sales_report'));
            }
        });

        //Sales representative report -> Expenses
        sr_expenses_report = $('table#sr_expenses_report').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/expenses",
                "data": function ( d ) {
                    var start = $('input#sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('input#sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                    d.expense_for = $('select#sr_id').val(),
                    d.location_id = $('select#sr_business_id').val(),
                    d.start_date = start,
                    d.end_date = end
                }
            },
            columnDefs: [ {
                "targets": 7,
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'ref_no', name: 'ref_no'},
                { data: 'category', name: 'ec.name'},
                { data: 'location_name', name: 'bl.name'},
                { data: 'payment_status', name: 'payment_status'},
                { data: 'final_total', name: 'final_total'},
                { data: 'expense_for', name: 'expense_for'},
                { data: 'additional_notes', name: 'additional_notes'}
            ],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#sr_expenses_report'));
            }
        });

        //Sales representative report -> Sales with commission
        sr_sales_commission_report = $('table#sr_sales_with_commission_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/sells",
                "data": function ( d ) {
                    var start = $('input#sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('input#sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                    d.commission_agent = $('select#sr_id').val(),
                    d.location_id = $('select#sr_business_id').val(),
                    d.start_date = start,
                    d.end_date = end
                }
            },
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'customer_name', name: 'customer_name'},
                { data: 'location', name: 'location'},
                { data: 'payment_status', name: 'payment_status'},
                { data: 'final_total', name: 'final_total'},
                { data: 'total_paid', name: 'total_paid'},
                { data: 'total_remaining', name: 'total_remaining'}
            ],
            columnDefs: [
                    {
                        'searchable'    : false, 
                        'targets'       : [6] 
                    },
                ],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#sr_sales_with_commission_table'));
                __currency_convert_recursively($('#sr_sales_with_commission'));
            }
        });

        //Sales representive filter
        $('select#sr_id, select#sr_business_id').change( function(){
            updateSalesRepresentativeReport();
        });
    }

    //Stock expiry report table
    stock_expiry_report_table = $('table#stock_expiry_report_table').DataTable({
                    processing: true,
                    serverSide: true,
                    "ajax": {
                        "url": "/reports/stock-expiry",
                        "data": function ( d ) {
                            d.location_id = $('#location_id').val();
                            d.category_id = $('#category_id').val();
                            d.sub_category_id = $('#sub_category_id').val();
                            d.brand_id = $('#brand').val();
                            d.unit_id = $('#unit').val();
                            d.exp_date_filter = $('#view_stock_filter').val();
                        }
                    },
                    "order": [[ 5, "asc" ]],
                    columnDefs: [ 
                        {"targets": [7], "orderable": false, "searchable": false}
                    ],
                    columns: [
                        {data: 'product', name: 'p.name'},
                        {data: 'sku', name: 'p.sku'},
                        {data: 'ref_no', name: 't.ref_no'},
                        {data: 'location', name: 'l.name'},
                        {data: 'stock_left', name: 'stock_left', searchable: false},
                        {data: 'lot_number', name: 'lot_number'},
                        {data: 'exp_date', name: 'exp_date'},
                        {data: 'mfg_date', name: 'mfg_date'},
                        {data: 'edit', name: 'edit'},
                    ],
                    "fnDrawCallback": function (oSettings) {
                        __show_date_diff_for_human($('#stock_expiry_report_table'));
                        $('button.stock_expiry_edit_btn').click(function(){
                            var purchase_line_id = $(this).data('purchase_line_id');

                            $.ajax({
                                method: "GET",
                                url: '/reports/stock-expiry-edit-modal/' + purchase_line_id,
                                dataType: "html",
                                success: function(data){
                                    $('div.exp_update_modal').html(data).modal('show');
                                    $('input#exp_date_expiry_modal').datepicker({
                                        autoclose: true,
                                        format:datepicker_date_format
                                    });

                                    $('form#stock_exp_modal_form').submit(function(e){
                                        e.preventDefault();
                                        
                                        $.ajax({
                                            method: "POST",
                                            url:$('form#stock_exp_modal_form').attr('action'),
                                            dataType: "json",
                                            data: $('form#stock_exp_modal_form').serialize(),
                                            success: function(data){
                                                if(data.success == 1){
                                                    $('div.exp_update_modal').modal('hide');
                                                    toastr.success(data.msg);
                                                    stock_expiry_report_table.ajax.reload();
                                                } else {
                                                    toastr.error(data.msg);
                                                }
                                            }
                                        });
                                    })
                                }
                            });
                        });
                        $('#footer_total_stock_left').html(__sum_stock($('#stock_expiry_report_table'), 'stock_left'));
                         __currency_convert_recursively($('#stock_expiry_report_table'));
                    }
                });

    //Profit / Loss
    if($('#profit_loss_date_filter').length == 1){
        $('#profit_loss_date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#profit_loss_date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                updateProfitLoss();
            }
        );
        $('#profit_loss_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#profit_loss_date_filter').html('<i class="fa fa-calendar"></i> ' + LANG.filter_by_date);
        });
        updateProfitLoss();
    }
    $('#profit_loss_location_filter').change( function(){
        updateProfitLoss();
    });

    //Product Purchase Report
    if($('#product_pr_date_filter').length == 1){
        $('#product_pr_date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#product_pr_date_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                product_purchase_report.ajax.reload();
            }
        );
        $('#product_pr_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_pr_date_filter').val('');
            product_purchase_report.ajax.reload();
        });
        $('#product_pr_date_filter').data('daterangepicker').setStartDate(moment());
        $('#product_pr_date_filter').data('daterangepicker').setEndDate(moment());
    }
    $('#product_purchase_report_form #variation_id, #product_purchase_report_form #location_id, #product_purchase_report_form #supplier_id, #product_purchase_report_form #product_pr_date_filter').change( function(){
        product_purchase_report.ajax.reload();
    });
    product_purchase_report = $('table#product_purchase_report_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[3, 'desc']],
        "ajax": {
            "url": "/reports/product-purchase-report",
            "data": function ( d ) {
                var start = '';
                var end = '';
                if($('#product_pr_date_filter').val()){
                    start = $('input#product_pr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    end = $('input#product_pr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
                d.start_date = start;
                d.end_date = end;
                d.variation_id = $('#variation_id').val();
                d.supplier_id = $('select#supplier_id').val();
                d.location_id = $('select#location_id').val();
            }
        },
        columns: [
            { data: 'product_name', name: 'p.name'  },
            { data: 'supplier', name: 'c.name'  },
            { data: 'ref_no', name: 't.ref_no'  },
            { data: 'transaction_date', name: 't.transaction_date'},
            { data: 'purchase_qty', name: 'purchase_lines.quantity'},
            { data: 'unit_purchase_price', name: 'purchase_lines.purchase_price_inc_tax' },
            { data: 'subtotal', name: 'subtotal', searchable: false}
        
        ],
        "fnDrawCallback": function (oSettings) {
            $('#footer_subtotal').text(sum_table_col($('#product_purchase_report_table'), 'row_subtotal'));
            $('#footer_total_purchase').html(__sum_stock($('#product_purchase_report_table'), 'purchase_qty'));
            __currency_convert_recursively($('#product_purchase_report_table'));
        }
    });

    if($( "#search_product" ).length > 0){
        $( "#search_product" ).autocomplete({
            source: function( request, response ) {
                $.ajax( {
                  url: "/purchases/get_products",
                  dataType: "json",
                  data: {
                    term: request.term
                  },
                  success: function( data ) {
                    response( $.map(data, function(v,i) { 
                        if(v.variation_id){
                            return { label: v.text, value:v.variation_id }; 
                        } 
                        return false;
                     }));
                  }
                });
            },
            minLength: 2,
            select: function( event, ui ) {
                $('#variation_id').val(ui.item.value).change()
                event.preventDefault(); 
                $(this).val(ui.item.label);
            },
            focus: function(event, ui) {
                event.preventDefault();
                $(this).val(ui.item.label);
            }
        });
    }

    if($( "#search_product" ).length > 0){
        $( "#search_product" ).autocomplete({
            source: function( request, response ) {
                $.ajax( {
                  url: "/purchases/get_products",
                  dataType: "json",
                  data: {
                    term: request.term
                  },
                  success: function( data ) {
                    response( $.map(data, function(v,i) { 
                        if(v.variation_id){
                            return { label: v.text, value:v.variation_id }; 
                        } 
                        return false;
                     }));
                  }
                });
            },
            minLength: 2,
            select: function( event, ui ) {
                $('#variation_id1').val(ui.item.value).change()
                event.preventDefault(); 
                $(this).val(ui.item.label);
            },
            focus: function(event, ui) {
                event.preventDefault();
                $(this).val(ui.item.label);
            }
        });
    }
    //Product Sell Report
    if($('#product_sr_date_filter').length == 1){
        $('#product_sr_date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#product_sr_date_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                product_sell_report.ajax.reload();
                product_sell_grouped_report.ajax.reload();
            }
        );
        $('#product_sr_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
            product_sell_report.ajax.reload();
            product_sell_grouped_report.ajax.reload();
        });
        $('#product_sr_date_filter').data('daterangepicker').setStartDate(moment());
        $('#product_sr_date_filter').data('daterangepicker').setEndDate(moment());
    }
    product_sell_report = $('table#product_sell_report_table').DataTable({

        processing: true,
        serverSide: true,
        aaSorting: [[3, 'desc']],
        "ajax": {
            "url": "/reports/product-sell-report",
            "data": function ( d ) {
                var start = '';
                var end = '';
                if($('#product_sr_date_filter').val()){
                    start = $('input#product_sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    end = $('input#product_sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
                d.start_date = start;
                d.end_date = end;

                d.variation_id = $('#variation_id').val();
                d.customer_id = $('select#customer_id').val();
                d.location_id = $('select#location_id').val();
                d.brand_id = $('select#brand_id').val();
            }
        },
        columns: [
            { data: 'product_name', name: 'p.name'  },
            { data: 'customer', name: 'c.name'  },
            { data: 'invoice_no', name: 't.invoice_no'  },
            { data: 'transaction_date', name: 't.transaction_date'},
            { data: 'sell_qty', name: 'transaction_sell_lines.quantity'},
            { data: 'unit_price', name: 'transaction_sell_lines.unit_price_before_discount' },
            { data: 'discount_amount', name: 'transaction_sell_lines.line_discount_amount'},
            { data: 'tax', name: 'tax_rates.name'},
            { data: 'unit_sale_price', name: 'transaction_sell_lines.unit_price_inc_tax' },
            { data: 'subtotal', name: 'subtotal', searchable: false}
        
        ],
        "fnDrawCallback": function (oSettings) {
            $('#footer_subtotal').text(sum_table_col($('#product_sell_report_table'), 'row_subtotal'));
            $('#footer_total_sold').html(__sum_stock($('#product_sell_report_table'), 'sell_qty'));
            $('#footer_tax').html(__sum_stock($('#product_sell_report_table'), 'tax', 'left'));
            __currency_convert_recursively($('#product_sell_report_table'));
        }
    });

    product_sell_grouped_report = $('table#product_sell_grouped_report_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[1, 'desc']],
        "ajax": {
            "url": "/reports/product-sell-grouped-report",
            "data": function ( d ) {
                var start = '';
                var end = '';
                if($('#product_sr_date_filter').val()){
                    start = $('input#product_sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    end = $('input#product_sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
                d.start_date = start;
                d.end_date = end;

                d.variation_id = $('#variation_id').val();
                d.customer_id = $('select#customer_id').val();
                d.location_id = $('select#location_id').val();
            }
        },
        columns: [
            { data: 'product_name', name: 'p.name'  },
            { data: 'transaction_date', name: 't.transaction_date'},
            { data: 'current_stock', name: 'current_stock', 
                searchable: false, orderable: false},
            { data: 'total_qty_sold', name: 'total_qty_sold', searchable: false},
            { data: 'subtotal', name: 'subtotal', searchable: false}
        
        ],
        "fnDrawCallback": function (oSettings) {
            $('#footer_grouped_subtotal').text(sum_table_col($('#product_sell_grouped_report_table'), 'row_subtotal'));
            $('#footer_total_grouped_sold').html(__sum_stock($('#product_sell_grouped_report_table'), 'sell_qty'));
            __currency_convert_recursively($('#product_sell_grouped_report_table'));
        }
    });

    $('#product_sell_report_form,#variation_id, #product_sell_report_form, #location_id, #product_sell_report_form, #customer_id, #brand_id').change( function(){
        product_sell_report.ajax.reload();
        product_sell_grouped_report.ajax.reload();
    });


    $('#product_sell_report_form #search_product').keyup( function () {
        if($(this).val().trim() == '') {
            $('#product_sell_report_form #variation_id').val('').change();
        }
    });
    
    $(document).on('click', '.remove_from_stock_btn', function(){
        swal({
          title: LANG.sure,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    method: "GET",
                    url: $(this).data('href'),
                    dataType: "json",
                    success: function(result){
                        if(result.success == true){
                            toastr.success(result.msg);
                            stock_expiry_report_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            }
        });
    });

    
    //*********************************************

    //Product lot Report
    lot_report = $('table#lot_report').DataTable({
        processing: true,
        serverSide: true,
        // aaSorting: [[3, 'desc']],

        "ajax": {
            "url": "/reports/lot-report",
            "data": function ( d ) {
                d.location_id = $('#location_id').val();
                d.category_id = $('#category_id').val();
                d.sub_category_id = $('#sub_category_id').val();
                d.brand_id = $('#brand').val();
                d.unit_id = $('#unit').val();
            }
        },
        columns: [
            {data: 'sub_sku', name: 'v.sub_sku'},
            {data: 'product', name: 'products.name'},
            {data: 'lot_number', name: 'pl.lot_number'},
            {data: 'exp_date', name: 'pl.exp_date'},
            {data: 'stock', name: 'stock', searchable: false},
            {data: 'total_sold', name: 'total_sold', searchable: false},
            {data: 'total_adjusted', name: 'total_adjusted', searchable: false},
        ],

        "fnDrawCallback": function (oSettings) {
            $('#footer_total_stock').html(__sum_stock($('#lot_report'), 'total_stock'));
            $('#footer_total_sold').html(__sum_stock($('#lot_report'), 'total_sold'));
            $('#footer_total_adjusted').html(__sum_stock($('#lot_report'), 'total_adjusted'));
                         
            __currency_convert_recursively($('#lot_report'));
            __show_date_diff_for_human($('#lot_report'));
        }
    });

    if($('table#lot_report').length == 1){
        $('#location_id, #category_id, #sub_category_id, #unit, #brand').change( function(){
        lot_report.ajax.reload();
        });
    }

    //Purchase Payment Report
    purchase_payment_report = $('table#purchase_payment_report_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[2, 'desc']],
        "ajax": {
            "url": "/reports/purchase-payment-report",
            "data": function ( d ) {
                d.supplier_id = $('select#supplier_id').val();
                d.location_id = $('select#location_id').val();
                var start = '';
                var end = '';
                if($('input#ppr_date_filter').val()){
                    start = $('input#ppr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    end = $('input#ppr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
                d.start_date = start;
                d.end_date = end;
            }
        },
        columns: [
            {
                "orderable": false,
                "searchable": false,
                "data": null,
                "defaultContent": ""
            },
            { data: 'payment_ref_no', name: 'payment_ref_no'  },
            { data: 'paid_on', name: 'paid_on'  },
            { data: 'amount', name: 'transaction_payments.amount'  },
            { data: 'supplier', "orderable": false, "searchable": false},
            { data: 'method', name: 'method' },
            { data: 'ref_no', name: 't.ref_no' },
            { data: 'action', "orderable": false, "searchable": false },
        
        ],
        "fnDrawCallback": function (oSettings) {
            var total_amount = sum_table_col($('#purchase_payment_report_table'), 'paid-amount');
            $('#footer_total_amount').text(total_amount);
            __currency_convert_recursively($('#purchase_payment_report_table'));
        },
        createdRow: function( row, data, dataIndex ) {
            if( !data.transaction_id){
                $( row ).find('td:eq(0)').addClass('details-control');
            }
        },
    });

    // Array to track the ids of the details displayed rows
    var ppr_detail_rows = [];
 
    $('#purchase_payment_report_table tbody').on( 'click', 'tr td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = purchase_payment_report.row( tr );
        var idx = $.inArray( tr.attr('id'), ppr_detail_rows );
 
        if ( row.child.isShown() ) {
            tr.removeClass( 'details' );
            row.child.hide();
 
            // Remove from the 'open' array
            ppr_detail_rows.splice( idx, 1 );
        }
        else {
            tr.addClass( 'details' );

            row.child( show_child_payments( row.data() ) ).show();
 
            // Add to the 'open' array
            if ( idx === -1 ) {
                ppr_detail_rows.push( tr.attr('id') );
            }
        }
    } );
 
    // On each draw, loop over the `detailRows` array and show any child rows
    purchase_payment_report.on( 'draw', function () {
        $.each( ppr_detail_rows, function ( i, id ) {
            $('#'+id+' td.details-control').trigger( 'click' );
        } );
    } );

    if($('#ppr_date_filter').length == 1){
        $('#ppr_date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#ppr_date_filter span').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                purchase_payment_report.ajax.reload();
            }
        );
        $('#ppr_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#ppr_date_filter').val('');
            purchase_payment_report.ajax.reload();
        });
    }

    $('#purchase_payment_report_form #location_id, #purchase_payment_report_form #supplier_id').change( function(){
        purchase_payment_report.ajax.reload();
    });

    //Sell Payment Report
    sell_payment_report = $('table#sell_payment_report_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[2, 'desc']],
        "ajax": {
            "url": "/reports/sell-payment-report",
            "data": function ( d ) {
                d.supplier_id = $('select#customer_id').val();
                d.location_id = $('select#location_id').val();

                var start = '';
                var end = '';
                if($('input#spr_date_filter').val()){
                    start = $('input#spr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    end = $('input#spr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
                d.start_date = start;
                d.end_date = end;
            }
        },
        columns: [
            {
                "orderable": false,
                "searchable": false,
                "data": null,
                "defaultContent": ""
            },
            { data: 'payment_ref_no', name: 'payment_ref_no'  },
            { data: 'paid_on', name: 'paid_on'  },
            { data: 'amount', name: 'transaction_payments.amount'  },
            { data: 'customer', "orderable": false, "searchable": false},
            { data: 'method', name: 'method' },
            { data: 'invoice_no', name: 't.invoice_no' },
            { data: 'action', "orderable": false, "searchable": false },
        ],
        "fnDrawCallback": function (oSettings) {
            var total_amount = sum_table_col($('#sell_payment_report_table'), 'paid-amount');
            $('#footer_total_amount').text(total_amount);
            __currency_convert_recursively($('#sell_payment_report_table'));
        },
        createdRow: function( row, data, dataIndex ) {
            if( !data.transaction_id){
                $( row ).find('td:eq(0)').addClass('details-control');
            }
        }
    });
    // Array to track the ids of the details displayed rows
    var spr_detail_rows = [];
 
    $('#sell_payment_report_table tbody').on( 'click', 'tr td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = sell_payment_report.row( tr );
        var idx = $.inArray( tr.attr('id'), spr_detail_rows );
 
        if ( row.child.isShown() ) {
            tr.removeClass( 'details' );
            row.child.hide();
 
            // Remove from the 'open' array
            spr_detail_rows.splice( idx, 1 );
        }
        else {
            tr.addClass( 'details' );

            row.child( show_child_payments( row.data() ) ).show();
 
            // Add to the 'open' array
            if ( idx === -1 ) {
                spr_detail_rows.push( tr.attr('id') );
            }
        }
    } );

    // On each draw, loop over the `detailRows` array and show any child rows
    sell_payment_report.on( 'draw', function () {
        $.each( spr_detail_rows, function ( i, id ) {
            $('#'+id+' td.details-control').trigger( 'click' );
        } );
    } );

    if($('#spr_date_filter').length == 1){
        $('#spr_date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#spr_date_filter span').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                sell_payment_report.ajax.reload();
            }
        );
        $('#spr_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#spr_date_filter').val('');
            sell_payment_report.ajax.reload();
        });
    }

    $('#sell_payment_report_form #location_id, #sell_payment_report_form #customer_id').change( function(){
        sell_payment_report.ajax.reload();
    });

    // ----- Sales and stock adjustments report -----

    // Datatable
    sales_n_adjustments_report_table = $('#sales_n_adjustments_report_table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        "ajax": {
            "url": "/reports/sales-n-adjustments-report",
            "data": function(d) {
                d.location_id = $('#location_id').val();
                d.month = $('#month').val();
            }
        },
        columns: [
            { data: 'sku', name: 'sku' },
            { data: 'product', name: 'product'},
            { data: 'unit_price', name: 'unit_price' },
            { data: 'unit_cost', name: 'unit_cost' },
            { data: 'total_sold', name: 'total_sold', searchable: false} ,
            { data: 'input_adjustment', name: 'input_adjustment', searchable: false },
            { data: 'output_adjustment', name: 'output_adjustment', searchable: false }
        ],
        "fnDrawCallback": function(oSettings) {
            $('#footer_total_sold').html(__sum_stock($('#sales_n_adjustments_report_table'), 'total_sold'));
            $('#footer_total_input_adjustment').html(__sum_stock($('#sales_n_adjustments_report_table'), 'input_adjustment'));
            $('#footer_total_output_adjustment').html(__sum_stock($('#sales_n_adjustments_report_table'), 'output_adjustment'));
            __currency_convert_recursively($('#sales_n_adjustments_report_table'));
        }
    });

    // Reload table when changing params
    $('#sales_n_adjustments_report_filter_form #location_id, #sales_n_adjustments_report_filter_form #month').change(function() {
        sales_n_adjustments_report_table.ajax.reload();
    });

    // ----- Cost of sale detail report -----

    // Date filter for cost_of_sale_detail_report_table table
    if ($('#cost_of_sale_detail_report_filter_form #date_range').length > 0) {
        $('#cost_of_sale_detail_report_filter_form #date_range').daterangepicker(
            dateRangeSettings,
            function(start, end) {
                $('#date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));

                var start_date = $('#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end_date = $('#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');

                $('#start_date').val(start_date);
                $('#end_date').val(end_date);

                cost_of_sale_detail_report_table.ajax.reload();
            }
        );

        $('#cost_of_sale_detail_report_filter_form #date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_range').val('');
            cost_of_sale_detail_report_table.ajax.reload();
        });

        $('#date_range').data('daterangepicker').setStartDate(moment().startOf('month'));
        $('#date_range').data('daterangepicker').setEndDate(moment().endOf('month'));
    }

    // Datatable
    cost_of_sale_detail_report_table = $('#cost_of_sale_detail_report_table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        aaSorting: [[1, 'asc']],
        "ajax": {
            "url": "/product-reports/warehouse-closure-report",
            "data": function(d) {
                d.warehouse_id = $('#warehouse_id').val();
                d.document_type_id = $('#document_type_id').val();
                d.start_date = $('#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                d.end_date = $('#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                d.type = $('#type').val();
            }
        },
        columns: [
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'code', name: 'code'},
            { data: 'description', name: 'description' },
            { data: 'observation', name: 'observation' },
            { data: 'document_type', name: 'document_type' },
            { data: 'reference', name: 'reference' },
            { data: 'input', name: 'input', searchable: false },
            { data: 'output', name: 'output', searchable: false },
            { data: 'annulled', name: 'annulled', searchable: false }
        ],
        "fnDrawCallback": function(oSettings) {
            $('#footer_input').html(__sum_column($('#cost_of_sale_detail_report_table'), 'input'));
            $('#footer_output').html(__sum_column($('#cost_of_sale_detail_report_table'), 'output'));
            $('#footer_annulled').html(__sum_column($('#cost_of_sale_detail_report_table'), 'annulled'));
            __currency_convert_recursively($('#cost_of_sale_detail_report_table'));
        }
    });

    // Reload table when changing params
    $('#cost_of_sale_detail_report_filter_form #warehouse_id').change(function () {
        cost_of_sale_detail_report_table.ajax.reload();
    });

    $('#cost_of_sale_detail_report_filter_form #document_type_id').change(function () {
        cost_of_sale_detail_report_table.ajax.reload();
    });

    $('#cost_of_sale_detail_report_filter_form #type').change(function () {
        cost_of_sale_detail_report_table.ajax.reload();
    });

    // Get suppliers
    $('#contact_id').select2({
        ajax: {
            url: '/reports/get_suppliers',
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
        templateResult: function(data) {
            if (! data.id) {
                return data.text;
            }
            var html = data.text + ' (<b>' + LANG.code + ': </b>' + data.contact_id + ' - <b>' + LANG.business + ': </b>' + data.business_name + ')';
            return html;
        },
        templateSelection: function(data) {
            return data.text;
        },
        placeholder: LANG.all,
        allowClear: true
    });

    // ----- Sales tracking report -----

    // Datatable
    sales_tracking_report_table = $('#sales_tracking_report_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[1, 'asc']],
        "ajax": {
            "url": "/reports/sales-tracking-report",
            "data": function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.customer_id = $('#customer_id').val();
                d.invoiced = $('#invoiced').val();
                d.delivery_type = $('#delivery_type').val();
                d.employee_id = $('#employee_id').val();
            }
        },
        columns: [
            {data: 'code', name: 'code'},
            {data: 'quote_date', name: 'quote_date' },
            {data: 'customer_id', name: 'customer_id'},
            {data: 'customer', name: 'customer'},
            {data: 'delivery_type', name: 'delivery_type'},
            {data: 'invoiced', name: 'invoiced'},
            {data: 'quoted_amount', name: 'quoted_amount'},
            {data: 'invoiced_amount', name: 'invoiced_amount'},
            {data: 'seller', name: 'seller'},
        ],
    });

    // Reload table when changing params
    $('#form_sales_tracking_report #customer_id, #form_sales_tracking_report #delivery_type, #form_sales_tracking_report #invoiced, #form_sales_tracking_report #employee_id')
        .change(function() {
        sales_tracking_report_table.ajax.reload();
    });

    // Get customers
    $('#customer_id').select2({
        ajax: {
            url: '/customers/get_only_customers',
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
            if (! data.id) {
                return data.text;
            }
            var html = (data.text || data.business_name);
            return html;
        },
        templateSelection: function(data) {
            if (!data.id) {
                return data.text;
            }
            // If it's a new supplier
            if (!data.id) {
                return data.text;
            } else {
                return data.business_name || data.text;
            }
        },
        placeholder: LANG.all,
        allowClear: true
    });

    // Get employees
    $('#employee_id').select2({
        ajax: {
            url: '/reports/get_employees',
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
        templateResult: function(data) {
            return data.text;
        },
        templateSelection: function(data) {
            return data.text;
        },
        placeholder: LANG.all,
        allowClear: true
    });
});

function updatePurchaseSell(){

    var start = $('#purchase_sell_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end = $('#purchase_sell_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
    var location_id = $('#purchase_sell_location_filter').val();

    var data = { start_date: start, end_date: end, location_id: location_id  };
    
    var loader = __fa_awesome();
    $('.total_purchase').html(loader);
    $('.purchase_due').html(loader);
    $('.total_sell').html(loader);
    $('.invoice_due').html(loader);
    $('.purchase_return_inc_tax').html(loader);
    $('.total_sell_return').html(loader);

    $.ajax({
        method: "GET",
        url: '/reports/purchase-sell',
        dataType: "json",
        data: data,
        success: function(data){

            $('.total_purchase').html(__currency_trans_from_en( data.purchase.total_purchase_exc_tax, true ));
            $('.purchase_inc_tax').html(__currency_trans_from_en( data.purchase.total_purchase_inc_tax, true ));
            $('.purchase_due').html(__currency_trans_from_en( data.purchase.purchase_due, true ));

            $('.total_sell').html(__currency_trans_from_en( data.sell.total_sell_exc_tax, true ));
            $('.sell_inc_tax').html(__currency_trans_from_en( data.sell.total_sell_inc_tax, true ));
            $('.sell_due').html(__currency_trans_from_en( data.sell.invoice_due, true ));
            $('.purchase_return_inc_tax').html(__currency_trans_from_en( data.total_purchase_return, true ));
            $('.total_sell_return').html(__currency_trans_from_en( data.total_sell_return, true ));

            $('.sell_minus_purchase').html(__currency_trans_from_en( data.difference.total, true ));
            __highlight(data.difference.total, $('.sell_minus_purchase'));

            $('.difference_due').html(__currency_trans_from_en( data.difference.due, true ));
            __highlight(data.difference.due, $('.difference_due'));

            // $('.purchase_due').html( __currency_trans_from_en(data.purchase_due, true));
        }
    });
}

function get_stock_details ( rowData ) {
    var div = $('<div/>')
        .addClass( 'loading' )
        .text( 'Loading...' );
    var location_id = $('#location_id').val();
    $.ajax( {
        url: '/reports/stock-details?location_id=' + location_id,
        data: {
            product_id: rowData.DT_RowId
        },
        dataType: 'html',
        success: function ( data ) {
            div
                .html( data )
                .removeClass( 'loading' );
            __currency_convert_recursively(div);
        }
    } );
 
    return div;
}

function updateTaxReport(){
    var start = $('#tax_report_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end = $('#tax_report_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
    var location_id = $('#tax_report_location_filter').val();
    var data = { start_date: start, end_date: end, location_id: location_id };
    
    var loader = '<i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i>';
    $('.input_tax').html(loader);
    $('.output_tax').html(loader);
    $('.tax_diff').html(loader);
    $.ajax({
        method: "GET",
        url: '/reports/tax-report',
        dataType: "json",
        data: data,
        success: function(data){
            $('.input_tax').html(data.input_tax);
            __currency_convert_recursively($('.input_tax'));
            $('.output_tax').html(data.output_tax);
             __currency_convert_recursively($('.output_tax'));
            $('.tax_diff').html(__currency_trans_from_en( data.tax_diff, true ));
            __highlight(data.tax_diff, $('.tax_diff'));
        }
    });
}

function updateStockAdjustmentReport(){

    var location_id = $('#stock_adjustment_location_filter').val();
    var start = $('#stock_adjustment_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end = $('#stock_adjustment_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

    var data = { start_date: start, end_date: end, location_id: location_id  };
    
    var loader = __fa_awesome();
    $('.total_amount').html(loader);
    $('.total_recovered').html(loader);
    $('.total_normal').html(loader);
    $('.total_abnormal').html(loader);

    $.ajax({
        method: "GET",
        url: '/reports/stock-adjustment-report',
        dataType: "json",
        data: data,
        success: function(data){
            $('.total_amount').html(__currency_trans_from_en( data.total_amount, true ));
            $('.total_recovered').html(__currency_trans_from_en( data.total_recovered, true ));
            $('.total_normal').html(__currency_trans_from_en( data.total_normal, true ));
            $('.total_abnormal').html(__currency_trans_from_en( data.total_abnormal, true ));
        }
    });

    stock_adjustment_table.ajax.url( '/stock-adjustments?location_id=' + location_id + '&start_date=' + start +
                '&end_date=' + end ).load();
}

function updateRegisterReport(){
    var data = {
        cashier_id: $('#register_cashier_id').val(),
        status: $('#register_status').val(),
    }
    var out = [];

    for (var key in data) {
        out.push(key + '=' + encodeURIComponent(data[key]));
    }
    url_data = out.join('&');
    register_report_table.ajax.url( '/reports/register-report?' + url_data).load();
}

function updateSalesRepresentativeReport(){
    //Update total expenses and total sales
    salesRepresentativeTotalExpense();
    salesRepresentativeTotalSales();
    salesRepresentativeTotalCommission();

    //Expense and expense table refresh
    sr_expenses_report.ajax.reload();
    sr_sales_report.ajax.reload();
    sr_sales_commission_report.ajax.reload();
}

function salesRepresentativeTotalExpense(){

    var start = $('input#sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end = $('input#sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

    var data_expense = {
        expense_for: $('select#sr_id').val(),
        location_id: $('select#sr_business_id').val(),
        start_date: start,
        end_date: end
    }

    $('span#sr_total_expenses').html(__fa_awesome());

    $.ajax({
        method: "GET",
        url: '/reports/sales-representative-total-expense',
        dataType: "json",
        data: data_expense,
        success: function(data){
            $('span#sr_total_expenses').html(__currency_trans_from_en(data.total_expense, true));
        }
    });
}

function salesRepresentativeTotalSales(){

    var start = $('input#sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end = $('input#sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

    var data_expense = {
        created_by: $('select#sr_id').val(),
        location_id: $('select#sr_business_id').val(),
        start_date: start,
        end_date: end
    }

    $('span#sr_total_sales').html(__fa_awesome());

    $.ajax({
        method: "GET",
        url: '/reports/sales-representative-total-sell',
        dataType: "json",
        data: data_expense,
        success: function(data){
            $('span#sr_total_sales').html(__currency_trans_from_en(data.total_sell_exc_tax, true));
        }
    });
}

function salesRepresentativeTotalCommission(){

    var start = $('input#sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end = $('input#sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

    var data_sell = {
        commission_agent: $('select#sr_id').val(),
        location_id: $('select#sr_business_id').val(),
        start_date: start,
        end_date: end
    }

    $('span#sr_total_commission').html(__fa_awesome());
    if(data_sell.commission_agent){
        $('div#total_commission_div').removeClass('hide');
        $.ajax({
            method: "GET",
            url: '/reports/sales-representative-total-commission',
            dataType: "json",
            data: data_sell,
            success: function(data){
                var str = '<div style="padding-right:15px; display: inline-block">' + __currency_trans_from_en(data.total_commission, true) + '</div>';
                if(data.commission_percentage != 0){
                    str += ' <small>(' + data.commission_percentage + '% of ' + __currency_trans_from_en(data.total_sales_with_commission) + ')</small>';
                }
                
                $('span#sr_total_commission').html(str);
            }
        });
    } else {
        $('div#total_commission_div').addClass('hide');
    }
}

function updateProfitLoss(){

    var start = $('#profit_loss_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end = $('#profit_loss_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
    var location_id = $('#profit_loss_location_filter').val();

    var data = { start_date: start, end_date: end, location_id: location_id  };
    
    var loader = __fa_awesome();
    $('.opening_stock, .total_transfer_shipping_charges, .closing_stock, .total_sell, .total_purchase, .total_expense, .net_profit, .total_adjustment, .total_recovered, .total_sell_discount, .total_purchase_discount, .total_purchase_return, .total_sell_return').html(loader);

    $.ajax({
        method: "GET",
        url: '/reports/profit-loss',
        dataType: "json",
        data: data,
        success: function(data){
            $('.opening_stock').html(__currency_trans_from_en( data.opening_stock, true ));
            $('.closing_stock').html(__currency_trans_from_en( data.closing_stock, true ));
            $('.total_sell').html(__currency_trans_from_en( data.total_sell, true ));
            $('.total_purchase').html(__currency_trans_from_en( data.total_purchase, true ));
            $('.total_expense').html(__currency_trans_from_en( data.total_expense, true ));
            $('.net_profit').html(__currency_trans_from_en( data.net_profit, true ));
            $('.total_adjustment').html(__currency_trans_from_en( data.total_adjustment, true ));
            $('.total_recovered').html(__currency_trans_from_en( data.total_recovered, true ));
            $('.total_purchase_return').html(__currency_trans_from_en( data.total_purchase_return, true ));
            $('.total_transfer_shipping_charges').html(__currency_trans_from_en( data.total_transfer_shipping_charges, true ));
            $('.total_purchase_discount').html(__currency_trans_from_en( data.total_purchase_discount, true ));
            $('.total_sell_discount').html(__currency_trans_from_en( data.total_sell_discount, true ));
            $('.total_sell_return').html(__currency_trans_from_en( data.total_sell_return, true ));
            __highlight(data.net_profit, $('.net_profit'));
        }
    });
}

function show_child_payments ( rowData ) {
    var div = $('<div/>')
        .addClass( 'loading' )
        .text( 'Loading...' );
    $.ajax( {
        url: '/payments/show-child-payments/' + rowData.DT_RowId,
        dataType: 'html',
        success: function ( data ) {
            div
                .html( data )
                .removeClass( 'loading' );
            __currency_convert_recursively(div);
        }
    } );
 
    return div;
}
@extends('layouts.app')
@section('title', __('quote.quotes'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        @lang('quote.quotes')
        <small>@lang('quote.manage_your_quotes')</small>
    </h1>

    <input type="hidden" id="app-business" value="{{ config('app.business') }}">
</section>

<!-- Main content -->
<section class="content">
	<div class="boxform_u box-solid_u" id="div_list">
        <div class="box-header">
        	<h3 class="box-title">@lang( 'quote.all_your_quotes')</h3>
            @if(auth()->user()->can('quotes.create'))
            <div class="box-tools">
                <button class="btn btn-block btn-primary" id="btn-new-quote">
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </button>
            </div>
            @endif
        </div>
        <div class="box-body">
            @if(auth()->user()->can('quotes.view'))
            <div class="table-responsive-sm">
            	<table class="table table-bordered table-striped" id="quote_table" width="100%">
            		<thead>
            			<tr>
                            <th>@lang('messages.date')</th>
                            <th>@lang('quote.due_date')</th>
                            <th>@lang('purchase.ref_no')</th>
                            <th>@lang('sale.customer_name')</th>
                            <th>@lang('quote.employee')</th>
                            <th>@lang('quote.amount')</th>
                            <th>@lang('Venta perdida')</th>
                            <th>@lang('messages.actions')</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            @if(auth()->user()->can('quotes.add_not_stock'))
            <input type="hidden" name="enable_not_stock" id="enable_not_stock" value="1">
            @else
            <input type="hidden" name="enable_not_stock" id="enable_not_stock" value="0">
            @endif
            @endif
        </div>
    </div>

    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">

    </div>
    <div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">

    </div>
    <div class="modal fade lost_sale_modal" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade edit_lost_sale_modal" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div id="div_add" style="display: none;">
    </div>

</section>
<!-- /.content -->
@endsection
@section('javascript')
<script type="text/javascript">
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

    $(document).ready(function() {

        loadQuotesData();
        $.fn.dataTable.ext.errMode = 'none';


    });

    $(document).on('click', '#btn-new-quote', function(e) {
        $("#div_list").hide();
        $.get("/quotes/create", function(data){
            $("#div_add").html(data);
            cont = 0;
            product_ids = [];
            rowCont = [];
            $("#div_add").show();
            loadDiv();
        });
    })

    function loadDiv() {
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
        $('input#_date').datetimepicker({
            format: moment_date_format,
            ignoreReadonly: true
        });
        $('#search_customer').select2();
        $('#document_type_id').select2();
        $('#payment_condition').select2();
        $('#tax_detail').select2();
        $('#warehouse_id').select2();
        $('#discount_type').select2();
        $('#employee_id').select2();
        $('#selling_price_group_id').select2();
        $('#customer_vehicle_id').select2();

        if ($("#search_product").length > 0) {
            $("#search_product").autocomplete({
                source: function(request, response) {
                    $.getJSON("/products/list_for_quotes", { warehouse_id: $('select#warehouse_id').val(), term: request.term }, response);
                },
                minLength: 2,
                response: function(event,ui) {
                    if (ui.content.length == 1)
                    {
                        ui.item = ui.content[0];
                    } else if (ui.content.length == 0) {
                        /*swal(LANG.no_products_found)
                        .then((value) => {
                            $('input#search_product').select();
                        });*/
                        $('input#search_product').select();
                    }
                },
                focus: function( event, ui ) {
                    if(ui.item.qty_available <= 0){
                        return false;
                    }
                },
                select: function( event, ui ) {
                    if(ui.item.enable_stock != 1 || ui.item.qty_available > 0){
                        if(ui.item.qty_available != null){
                            $(this).val(null);
                            warehouse_id = $('select#warehouse_id').val();
                            selling_price_group_id = $('select#selling_price_group_id').val();
                            addProduct(ui.item.variation_id, warehouse_id, selling_price_group_id);
                        } else {
                            enable_not_stock = $("#enable_not_stock").val();
                            if (enable_not_stock == 1) {
                                Swal.fire({
                                    title: LANG.sure,
                                    text: "{{ __('quote.not_stock_content') }}",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: "{{ __('messages.accept') }}",
                                    cancelButtonText: "{{ __('messages.cancel') }}"
                                }).then((willDelete) => {
                                    if (willDelete.value) {
                                        addProductNotStock(ui.item.variation_id);
                                    }
                                });
                            } else {
                                Swal.fire
                                ({
                                    title: LANG.out_of_stock,
                                    icon: "error",
                                });
                            }
                        }
                        
                    } else{
                        enable_not_stock = $("#enable_not_stock").val();
                        if (enable_not_stock == 1) {
                            Swal.fire({
                                title: LANG.sure,
                                text: "{{ __('quote.not_stock_content') }}",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: "{{ __('messages.accept') }}",
                                cancelButtonText: "{{ __('messages.cancel') }}"
                            }).then((willDelete) => {
                                if (willDelete.value) {
                                    addProductNotStock(ui.item.variation_id);
                                }
                            });
                        } else {
                            Swal.fire
                            ({
                                title: LANG.out_of_stock,
                                icon: "error",
                            });
                        }
                    }
                }
            })
            .autocomplete( "instance" )._renderItem = function( ul, item ) {
                if(item.enable_stock == 1 && item.qty_available <= 0){
    
                    enable_not_stock = $("#enable_not_stock").val();
                    if (enable_not_stock == 1) {
    
                        var string = '<div> '+ item.name;
                        if(item.type == 'variable'){
                            string += '-' + item.variation;
                        }
                        var selling_price = item.selling_price;
                        if(item.variation_group_price){
                            selling_price = item.variation_group_price;
                        }
    
                        string += ' (' + item.sub_sku + ')' + "<br> <b>" + LANG.price + ":</b>$" + selling_price
                        + " <b>E:</b> " + item.rack + "<b>F:</b> " + item.row + "<b>P:</b> "+ item.position
                        + ' (' + LANG.out_of_stock + ') </div>';
                        return $( "<li>" )
                        .append(string)
                        .appendTo( ul );
    
                    } else {
                        var string = '<li class="ui-state-disabled"> '+ item.name;
                        if(item.type == 'variable'){
                            string += '-' + item.variation;
                        }
                        var selling_price = item.selling_price;
                        if(item.variation_group_price){
                            selling_price = item.variation_group_price;
                        }
    
                        string += ' (' + item.sub_sku + ')' + "<br> <b>" + LANG.price + ":</b>$" + selling_price
                        + ' (' + LANG.out_of_stock + ') </li>';
                        return $(string).appendTo(ul);
    
                    }
    
                    
                } else {
    
                    var string =  "<div>" + item.name;
                    if(item.type == 'variable'){
                        string += '-' + item.variation;
                    }
    
                    var selling_price = item.selling_price;
                    if(item.variation_group_price){
                        selling_price = item.variation_group_price;
                    }
    
                    string += ' (' + item.sub_sku + ')' + "<br> <b>" + LANG.price + ":</b>$" + selling_price + " <b>"
                    + LANG.stock + ":</b>" + Math.round(item.qty_available, 0) + " </div>";
                    return $( "<li>" )
                    .append(string)
                    .appendTo( ul );
                }
            };
        }

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
                let selling_price_group_id = $('select#selling_price_group_id').val();
                let warehouse_id = $('select#warehouse_id').val();
                let tax_detail = $("#tax_detail").val();
                let service_block_index = $('#service-block-index').val();
                let row_index = $('#row-index').val();

                let service_ids = [];
                let count_service_ids = 0;

                $('#service-blocks').find('input.service-id').each(function () {
                    service_ids.push(parseInt($(this).val()));
                });

                count_service_ids = parseInt(jQuery.inArray(data.id, service_ids));

                if (count_service_ids >= 0) {
                    // Clear select2
                    $('#search_service').val(null).trigger('change');

                    Swal.fire({
                        title: "{{ __('product.product_already_added') }}",
                        icon: "error",
                    });
                    
                } else {
                    $.ajax({
                        method: 'post',
                        url: '/quotes/add-service-block/' + data.id,
                        data: {
                            selling_price_group_id: selling_price_group_id,
                            warehouse_id: warehouse_id,
                            tax_detail: tax_detail,
                            service_block_index: service_block_index,
                            row_index: row_index,
                            view: 'quote'
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
    
                                rowCont.push(parseInt($('#row-index').val()));
    
                                // Update row index
                                $('#row-index').val(parseInt($('#row-index').val()) + 1);
    
                                // Add service in table
                                addSpare(
                                    result.service_id,
                                    current_service_block,
                                    $('#row-index').val(),
                                    result.service_id,
                                    null,
                                    null
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
                                                if (ui.item.qty_available != null) {
                                                    $(this).val(null);
                                                    warehouse_id = $('select#warehouse_id').val();
                                                    selling_price_group_id = $('select#selling_price_group_id').val();
    
                                                    addSpare(
                                                        ui.item.variation_id,
                                                        $(this).data('service-block-index'),
                                                        $('#row-index').val(),
                                                        $(this).data('service-parent-id'),
                                                        null,
                                                        null
                                                    );
    
                                                } else {
                                                    enable_not_stock = $("#enable_not_stock").val();
    
                                                    if (enable_not_stock == 1) {
                                                        Swal.fire({
                                                            title: LANG.sure,
                                                            text: "{{ __('quote.not_stock_content') }}",
                                                            icon: 'warning',
                                                            showCancelButton: true,
                                                            confirmButtonColor: '#3085d6',
                                                            cancelButtonColor: '#d33',
                                                            confirmButtonText: "{{ __('messages.accept') }}",
                                                            cancelButtonText: "{{ __('messages.cancel') }}"
                                                        }).then((willDelete) => {
                                                            if (willDelete.value) {
                                                                addSpareNotStock(
                                                                    ui.item.variation_id,
                                                                    $(this).data('service-block-index'),
                                                                    $('#row-index').val(),
                                                                    $(this).data('service-parent-id'),
                                                                    null,
                                                                    null
                                                                );
                                                            }
                                                        });
    
                                                    } else {
                                                        Swal.fire({
                                                            title: LANG.out_of_stock,
                                                            icon: "error",
                                                        });
                                                    }
                                                }
                                                
                                            } else {
                                                enable_not_stock = $("#enable_not_stock").val();
    
                                                if (enable_not_stock == 1) {
                                                    Swal.fire({
                                                        title: LANG.sure,
                                                        text: "{{ __('quote.not_stock_content') }}",
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#3085d6',
                                                        cancelButtonColor: '#d33',
                                                        confirmButtonText: "{{ __('messages.accept') }}",
                                                        cancelButtonText: "{{ __('messages.cancel') }}"
                                                    }).then((willDelete) => {
                                                        if (willDelete.value) {
                                                            addSpareNotStock(
                                                                ui.item.variation_id,
                                                                $(this).data('service-block-index'),
                                                                $('#row-index').val(),
                                                                $(this).data('service-parent-id'),
                                                                null,
                                                                null
                                                            );
                                                        }
                                                    });
    
                                                } else {
                                                    Swal.fire({
                                                        title: LANG.out_of_stock,
                                                        icon: "error",
                                                    });
                                                }
                                            }
                                        }
                                    })
                                    .autocomplete('instance')._renderItem = function (ul, item) {
                                        if (item.enable_stock == 1 && item.qty_available <= 0) {
                                            enable_not_stock = $('#enable_not_stock').val();
    
                                            if (enable_not_stock == 1) {
                                                var string = '<div> ' + item.name;
    
                                                if (item.type == 'variable') {
                                                    string += '-' + item.variation;
                                                }
    
                                                var selling_price = item.selling_price;
    
                                                if (item.variation_group_price) {
                                                    selling_price = item.variation_group_price;
                                                }
    
                                                string += ' (' + item.sub_sku + ')' + '<br> <b>' + LANG.price + ':</b>$' + selling_price
                                                    + ' <b>E:</b> ' + item.rack + '<b>F:</b> ' + item.row + '<b>P:</b> ' + item.position
                                                    + ' (' + LANG.out_of_stock + ') </div>';
    
                                                return $("<li>" ).append(string).appendTo( ul );
    
                                            } else {
                                                var string = '<li class="ui-state-disabled"> '+ item.name;
    
                                                if (item.type == 'variable') {
                                                    string += '-' + item.variation;
                                                }
    
                                                var selling_price = item.selling_price;
    
                                                if (item.variation_group_price) {
                                                    selling_price = item.variation_group_price;
                                                }
    
                                                string += ' (' + item.sub_sku + ')' + '<br> <b>' + LANG.price + ':</b>$' + selling_price
                                                    + ' (' + LANG.out_of_stock + ') </li>';
    
                                                return $(string).appendTo(ul);
                                            }
                                            
                                        } else {
                                            var string =  '<div>' + item.name;
    
                                            if (item.type == 'variable') {
                                                string += '-' + item.variation;
                                            }
    
                                            var selling_price = item.selling_price;
    
                                            if (item.variation_group_price) {
                                                selling_price = item.variation_group_price;
                                            }
    
                                            string += ' (' + item.sub_sku + ')' + '<br> <b>' + LANG.price + ':</b>$' + selling_price + ' <b>'
                                                + LANG.stock + ':</b>' + Math.round(item.qty_available, 0) + ' </div>';
    
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
    }

    $(document).on('click', '#btn-cancel-add-quote', function(e) {
        $("#div_add").html('');
        $("#div_add").hide();
        $("#div_list").show();
    })

    // On change of search_customer select
    $(document).on('change', '#search_customer', function (e) {
        id = $(this).val();

        if (id != null) {
            selection = $(this).find('option:selected');
            name = selection.data('name');
            is_default = selection.data('default');
            address = selection.data('address');
            email = selection.data('email');
            mobile = selection.data('mobile');
            employee = selection.data('employee');
            $("#customer_id").val(id);

            if (is_default == 1) {
                $("#customer_name").val('');
                $("#address").val('');
                $("#email").val('');
                $("#mobile").val('');
                $("#employee_id").val('').change();
                $("#customer_name").focus();

            } else {
                $("#customer_name").val(name);
                $("#address").val(address);
                $("#email").val(email);
                $("#mobile").val(mobile);
                $("#employee_id").val(employee).change();
            }

            if ($('#customer_vehicle_id').length > 0) {
                getCustomerVehicles(id);
            }

        } else {
            $("#customer_id").val('');
            $("#customer_name").val('');
            $("#address").val('');
            $("#email").val('');
            $("#mobile").val('');
            $("#employee_id").val('').change();

            if ($('#customer_vehicle_id').length > 0) {
                $("#customer_vehicle_id").empty();
                $("#customer_vehicle_id").append('<option value="0" disabled selected>@lang('messages.please_select')</option>');
            }
        }
    });

    $(document).on('change', '#warehouse_id', function(e) {
        id = $(this).val();
        if(id != "") {
            $("#search_product").prop('readonly', false);
            $("#search_product").val('');

            $("#search_service").prop('disabled', false);
            $("#search_service").val('');

            $(".search-spare").prop('readonly', false);
            $(".search-spare").val('');

        } else {
            $("#search_product").prop('readonly', true);

            $("#search_service").prop('disabled', true);

            $(".search-spare").prop('readonly', true);
        }
    });
    

    var cont = 0;
    var product_ids = [];
    var rowCont=[];
    var blockCont = [];

    function addProduct(variation_id, warehouse_id, selling_price_group_id = null) {
        let route = `/quotes/addProduct/${variation_id}/${warehouse_id}/${selling_price_group_id}`;

        $.get(route, function (res) {
            variation_id = res.variation_id;

            if (res.type_product = 'single') {
                name = res.name_product;
            } else {
                name = "" + res.name_product + " " + res.name_variation + "";
            }

            count = parseInt(jQuery.inArray(variation_id, product_ids));

            if (count >= 0) {
                Swal.fire({
                    title: "{{__('product.product_already_added')}}",
                    icon: "error",
                });

            } else {
                product_ids.push(variation_id);
                rowCont.push(cont);
                warehouse_id = $("#warehouse_id").val();
                tax_detail = $("#tax_detail").val();

                if (tax_detail == 1) {
                    unit_price = res.price;
                } else {
                    unit_price = res.price_inc_tax;
                }

                qty_available = parseInt(res.qty_available);
                tax_amount = parseFloat(res.price_inc_tax - res.price);
                tax = parseFloat(res.tax_percent + 1);

                // var row = '<tr class="selected" id="row'+cont+'" style="height: 10px"><td><button id="bitem'+cont+'" type="button" class="btn btn-danger btn-xs" onclick="deleteProduct('+cont+', '+variation_id+');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="variation_id[]" value="'+variation_id+'"><input type="hidden" name="line_warehouse_id[]" value="'+warehouse_id+'">'+name+'</td><td><input type="text" name="quantity[]" id="quantity'+cont+'" class="form-control form-control-sm input_number" value="1" onchange="calculate()" required></td><td><input class="four_decimals" type="hidden" id="tax_percent'+cont+'" name="tax_percent[]" value="'+tax+'"><input type="hidden" class="four_decimals" id="unit_price_exc_tax'+cont+'" name="unit_price_exc_tax[]" value="'+res.price+'"><input type="hidden" class="four_decimals" id="line_tax_amount'+cont+'" name="line_tax_amount[]" value="'+tax_amount+'"><input type="hidden" class="four_decimals" id="unit_price_inc_tax'+cont+'" name="unit_price_inc_tax[]" value="'+res.price_inc_tax+'"><input type="text" id="unit_price'+cont+'" name="unit_price[]" class="form-control form-control-sm input_number four_decimals price_editable" value="'+unit_price+'" onchange="changePrice('+cont+')"></td><td><select name="line_discount_type[]" id="line_discount_type'+cont+'" class="form-control select_discount" style="width: 100%;"><option value="fixed">@lang('quote.fixed')</option><option value="percentage">@lang('quote.percentage')</option></select></td><td><input type="text" id="line_discount_amount'+cont+'" name="line_discount_amount[]" class="form-control form-control-sm input_number four_decimals" onchange="calculate()"></td><td><input type="text" id="subtotal'+cont+'" name="subtotal[]" class="form-control form-control-sm input_number four_decimals" readonly required></td></tr>';
                var row = 
                    `<tr class="selected" id="row${cont}" style="height: 10px">
                        <td>
                            <button
                                id="bitem${cont}"
                                type="button"
                                class="btn btn-danger btn-xs"
                                onclick="deleteProduct(${cont}, ${variation_id});">
                                <i class="fa fa-times"></i>
                            </button>
                        </td>
                        <td>
                            <input
                                type="hidden"
                                name="variation_id[]"
                                value="${variation_id}">
                            <input
                                type="hidden"
                                name="line_warehouse_id[]"
                                value="${warehouse_id}">
                            ${name}
                        </td>
                        <td>
                            <input
                                type="text"
                                name="quantity[]"
                                id="quantity${cont}"
                                class="form-control form-control-sm input_number"
                                value="1"
                                onchange="calculate()"
                                required>
                        </td>
                        <td>
                            <input
                                class="four_decimals"
                                type="hidden"
                                id="tax_percent${cont}"
                                name="tax_percent[]"
                                value="${tax}">
                            <input
                                type="hidden"
                                class="four_decimals"
                                id="unit_price_exc_tax${cont}"
                                name="unit_price_exc_tax[]"
                                value="${res.price}">
                            <input
                                type="hidden"
                                class="four_decimals"
                                id="line_tax_amount${cont}"
                                name="line_tax_amount[]"
                                value="${tax_amount}">
                            <input
                                type="hidden"
                                class="four_decimals"
                                id="unit_price_inc_tax${cont}"
                                name="unit_price_inc_tax[]"
                                value="${res.price_inc_tax}">
                            <input
                                type="text"
                                id="unit_price${cont}"
                                name="unit_price[]"
                                class="form-control form-control-sm input_number four_decimals price_editable"
                                value="${unit_price}"
                                onchange="changePrice(${cont})">
                        </td>
                        <td>
                            <select
                                name="line_discount_type[]"
                                id="line_discount_type${cont}"
                                class="form-control select_discount"
                                style="width: 100%;">
                                <option value="fixed">
                                    @lang('quote.fixed')
                                </option>
                                <option value="percentage">
                                    @lang('quote.percentage')
                                </option>
                            </select>
                        </td>
                        <td>
                            <input
                                type="text"
                                id="line_discount_amount${cont}"
                                name="line_discount_amount[]"
                                class="form-control form-control-sm input_number four_decimals"
                                onchange="calculate()">
                        </td>
                        <td>
                            <input
                                type="text"
                                id="subtotal${cont}"
                                name="subtotal[]"
                                class="form-control form-control-sm input_number four_decimals"
                                readonly
                                required>
                        </td>
                    </tr>`;

                $("#list").append(row);
                
                $(".select_discount").select2();
                
                cont++;
                
                calculate();
            }
        });
    }

    function changePrice(cont) {
        new_price = __read_number($('#unit_price'+cont+''));
        new_price = new_price.toFixed(4);

        /*old_price_exc_tax = parseFloat($('#unit_price_exc_tax'+cont+'').val());
        old_price_inc_tax = parseFloat($('#unit_price_inc_tax'+cont+'').val());
        tax_percent = old_price_inc_tax / old_price_exc_tax;*/

        tax_percent = __read_number($('#tax_percent'+cont+''));

        tax_detail = $("#tax_detail").val();

        if (tax_detail == 1) {

            $('#unit_price_exc_tax'+cont+'').val(new_price).change();

            new_price_inc_tax = new_price * tax_percent;
            new_tax_amount = new_price_inc_tax - new_price;

            $('#unit_price_inc_tax'+cont+'').val(new_price_inc_tax).change();
            $('#line_tax_amount'+cont+'').val(new_tax_amount).change();

        } else {

            $('#unit_price_inc_tax'+cont+'').val(new_price).change();

            new_price_exc_tax = new_price / tax_percent;
            new_tax_amount = new_price - new_price_exc_tax;

            $('#unit_price_exc_tax'+cont+'').val(new_price_exc_tax).change();
            $('#line_tax_amount'+cont+'').val(new_tax_amount).change();
        }

        calculate();
    }

    function addProductNotStock(variation_id) {
        var route = "/quotes/addProductNotStock/" + variation_id;

        $.get(route, function (res) {
            variation_id = res.variation_id;
            
            if (res.sku == res.sub_sku) {
                name = res.name_product;
            } else {
                name = "" + res.name_product + " " + res.name_variation + "";
            }

            count = parseInt(jQuery.inArray(variation_id, product_ids));

            if (count >= 0) {
                Swal.fire({
                    title: "{{__('product.product_already_added')}}",
                    icon: "error",
                });

            } else {
                product_ids.push(variation_id);
                rowCont.push(cont);
                tax_detail = $("#tax_detail").val();

                if (tax_detail == 1) {
                    unit_price = res.price;
                } else {
                    unit_price = res.price_inc_tax;
                }

                //qty_available = parseInt(res.qty_available);

                tax_amount = parseFloat(res.price_inc_tax - res.price);
                tax = parseFloat(res.tax_percent + 1);

                // var row = '<tr class="selected" id="row'+cont+'" style="height: 10px"><td><button id="bitem'+cont+'" type="button" class="btn btn-danger btn-xs" onclick="deleteProduct('+cont+', '+variation_id+');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="variation_id[]" value="'+variation_id+'"><input type="hidden" name="line_warehouse_id[]">'+name+'</td><td><input type="text" name="quantity[]" id="quantity'+cont+'" class="form-control form-control-sm input_number" value="1" onchange="calculate()" required></td><td><input type="hidden" id="tax_percent'+cont+'" name="tax_percent[]" value="'+tax+'"><input type="hidden" class="four_decimals" id="unit_price_exc_tax'+cont+'" name="unit_price_exc_tax[]" value="'+res.price+'"><input type="hidden" id="line_tax_amount'+cont+'" name="line_tax_amount[]" value="'+tax_amount+'"><input type="hidden" id="unit_price_inc_tax'+cont+'" class="four_decimals" name="unit_price_inc_tax[]" value="'+res.price_inc_tax+'"><input type="text" id="unit_price'+cont+'" name="unit_price[]" class="form-control form-control-sm input_number price_editable four_decimals" value="'+unit_price+'" onchange="changePrice('+cont+')"></td><td><select name="line_discount_type[]" id="line_discount_type'+cont+'" class="form-control select_discount" style="width: 100%;"><option value="fixed">@lang('quote.fixed')</option><option value="percentage">@lang('quote.percentage')</option></select></td><td><input type="text" id="line_discount_amount'+cont+'" name="line_discount_amount[]" class="form-control form-control-sm input_number" onchange="calculate()"></td><td><input type="text" id="subtotal'+cont+'" name="subtotal[]" class="form-control form-control-sm input_number four_decimals" readonly required></td></tr>';

                var row =
                    `<tr class="selected" id="row${cont}" style="height: 10px">
                        <td>
                            <button
                                id="bitem${cont}"
                                type="button"
                                class="btn btn-danger btn-xs"
                                onclick="deleteProduct(${cont}, ${variation_id});">
                                <i class="fa fa-times"></i>
                            </button>
                        </td>
                        <td>
                            <input
                                type="hidden"
                                name="variation_id[]
                                value="${variation_id}">
                            <input
                                type="hidden"
                                name="line_warehouse_id[]">
                            ${name}
                        </td>
                        <td>
                            <input
                                type="text"
                                name="quantity[]"
                                id="quantity${cont}"
                                class="form-control form-control-sm input_number"
                                value="1"
                                onchange="calculate()"
                                required>
                        </td>
                        <td>
                            <input
                                type="hidden"
                                id="tax_percent${cont}"
                                name="tax_percent[]"
                                value="${tax}">
                            <input
                                type="hidden"
                                class="four_decimals"
                                id="unit_price_exc_tax${cont}"
                                name="unit_price_exc_tax[]"
                                value="${res.price}">
                            <input
                                type="hidden"
                                id="line_tax_amount${cont}"
                                name="line_tax_amount[]"
                                value="${tax_amount}">
                            <input
                                type="hidden"
                                id="unit_price_inc_tax${cont}"
                                class="four_decimals"
                                name="unit_price_inc_tax[]"
                                value="${res.price_inc_tax}">
                            <input
                                type="text"
                                id="unit_price${cont}"
                                name="unit_price[]"
                                class="form-control form-control-sm input_number price_editable four_decimals"
                                value="${unit_price}"
                                onchange="changePrice(${cont})">
                        </td>
                        <td>
                            <select
                                name="line_discount_type[]"
                                id="line_discount_type${cont}"
                                class="form-control select_discount"
                                style="width: 100%;">
                                <option value="fixed">
                                    @lang('quote.fixed')
                                </option>
                                <option value="percentage">
                                    @lang('quote.percentage')
                                </option>
                            </select>
                        </td>
                        <td>
                            <input
                                type="text"
                                id="line_discount_amount${cont}"
                                name="line_discount_amount[]"
                                class="form-control form-control-sm input_number"
                                onchange="calculate()">
                        </td>
                        <td>
                            <input
                                type="text"
                                id="subtotal${cont}"
                                name="subtotal[]"
                                class="form-control form-control-sm input_number four_decimals"
                                readonly
                                required>
                        </td>
                    </tr>`;
                
                $("#list").append(row);
                
                $(".select_discount").select2();
                
                cont++;
                
                calculate();
            }
        });
    }

    function deleteProduct(index, id){ 
        $("#row" + index).remove();
        product_ids.removeItem(id);
        if(product_ids.length == 0)
        {
            cont = 0;
            product_ids=[];
            rowCont=[];
        }
        calculate();
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

    $(document).on('change', '.select_discount', function(e) {
        if ($('#app-business').val() == 'workshop') {
            workshopCalculate();
        } else {
            calculate();
        }
    });

    function calculateTax()
    {

        tax_detail = $("#tax_detail").val();

        $.each(rowCont, function(value){
            price_exc_tax = __read_number($("#unit_price_exc_tax"+value+""));
            price_inc_tax = __read_number($("#unit_price_inc_tax"+value+""));

            if (tax_detail == 1) {
                $("#unit_price"+value+"").val(price_exc_tax).change();
            } else {
                $("#unit_price"+value+"").val(price_inc_tax).change();
            }
        });
        calculate();
    }

    function isInfinite(n)
    {
        return n === n/0;
    }

    $(document).on('change', '#discount_amount_s', function(e) {
        new_value = __read_number($('#discount_amount_s'));
        __write_number($('#discount_amount'), new_value, false, 4);

        if ($('#app-business').val() == 'workshop') {
            workshopCalculate();
        } else {
            calculate();
        }
    });

    $(document).on('change', '#discount_type', function(e) {
        if ($('#app-business').val() == 'workshop') {
            workshopCalculate();
        } else {
            calculate();
        }
    });

    $(document).on('change', '#tax_detail', function(e) {
        if ($('#app-business').val() == 'workshop') {
            workshopCalculateTax();
        } else {
            calculateTax();
        }
    });

    function loadQuotesData() {
        var quote_table = $("#quote_table").DataTable({
            pageLength: 25,
            order: [0, "desc"],
            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/quotes/getQuotesData",
            columns: [
            { data: 'quote_date', name: 'quote.quote_date'},
            { data: 'due_date', name: 'quote.due_date'},
            { data: 'quote_ref_no', name: 'quote.quote_ref_no'},
            { data: 'customer_name', name: 'quote.customer_name'},
            { data: 'short_name', name: 'employee.short_name'},
            { data: 'total_final', name: 'quote.total_final'},
            { data: 'lost_sale_id', orderable:false, searchable: false},
            { data: 'actions', orderable: false, searchable: false }
            ],
            columnDefs: [{
              "targets": '_all',
              "className": "text-center"
          }]
      });
    }

    $(document).on('submit', 'form#add_quote_form', function(e) {
        e.preventDefault();
        $("#btn-add-quote").prop('disabled', true);
        $("#btn-cancel-add-quote").prop('disabled', true);
        var data = $("#add_quote_form").serialize();
        route = "/quotes";
        token = $("#token").val();
        $.ajax({
            url: route,
            headers: {
                'X-CSRF-TOKEN': token
            },
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(result) {
                if (result.success == true) {
                    $("#btn-add-quote").prop('disabled', false);
                    $("#btn-cancel-add-quote").prop('disabled', false);
                    $("#div_add").html('');
                    $("#div_add").hide();
                    $("#div_list").show();            
                    $("#quote_table").DataTable().ajax.reload(null, false);
                    Swal.fire({
                      title: result.msg,
                      icon: "success",
                      timer: 2000,
                      showConfirmButton: false,
                  });
                } else {
                    $("#btn-add-quote").prop('disabled', false);
                    $("#btn-cancel-add-quote").prop('disabled', false);
                    Swal.fire({
                      title: result.msg,
                      icon: "error",
                  });
                }
            },
            error: function(msj) {
                $("#btn-add-quote").prop('disabled', false);
                $("#btn-cancel-add-quote").prop('disabled', false);
                var errormessages = "";
                $.each(msj.responseJSON.errors, function(i, field) {
                    errormessages += "<li>" + field + "</li>";
                });
                Swal.fire({
                    title: "{{ __('quote.errors') }}",
                    icon: "error",
                    html: "<ul>" + errormessages + "</ul>",
                });
            }
        });
    });

    $(document).on('submit', 'form#edit_quote_form', function(e) {
        e.preventDefault();
        $("#btn-edit-quote").prop('disabled', true);
        $("#btn-cancel-add-quote").prop('disabled', true);
        var data = $("#edit_quote_form").serialize();
        id = $("#quote_id").val()
        route = "/quotes/"+id;
        token = $("#token").val();
        $.ajax({
            url: route,
            headers: {
                'X-CSRF-TOKEN': token
            },
            type: 'PUT',
            dataType: 'json',
            data: data,
            success: function(result) {
                if (result.success == true) {
                    $("#btn-edit-quote").prop('disabled', false);
                    $("#btn-cancel-add-quote").prop('disabled', false);
                    $("#div_add").html('');
                    $("#div_add").hide();
                    $("#div_list").show();            
                    $("#quote_table").DataTable().ajax.reload(null, false);
                    Swal.fire({
                      title: result.msg,
                      icon: "success",
                      timer: 2000,
                      showConfirmButton: false,
                  });
                } else {
                    $("#btn-edit-quote").prop('disabled', false);
                    $("#btn-cancel-add-quote").prop('disabled', false);
                    Swal.fire({
                      title: result.msg,
                      icon: "error",
                  });
                }
            },
            error: function(msj) {
                $("#btn-edit-quote").prop('disabled', false);
                $("#btn-cancel-add-quote").prop('disabled', false);
                var errormessages = "";
                $.each(msj.responseJSON.errors, function(i, field) {
                    errormessages += "<li>" + field + "</li>";
                });
                Swal.fire({
                    title: "{{ __('quote.errors') }}",
                    icon: "error",
                    html: "<ul>" + errormessages + "</ul>",
                });
            }
        });
    });

    function viewQuote(id) {

        var url = $('#app-business').val() == 'workshop' ? '{!!URL::to('/quotes/viewQuoteWorkshop/:id')!!}' : '{!!URL::to('/quotes/viewQuote/:id')!!}';
        url = url.replace(':id', id);
        window.open(url, '_blank');

    }

    function deleteQuote(id) {
        Swal.fire({
            title: LANG.sure,
            text: "{{ __('messages.delete_content') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('messages.accept') }}",
            cancelButtonText: "{{ __('messages.cancel') }}"
        }).then((willDelete) => {
            if (willDelete.value) {
                route = '/quotes/' + id;
                $.ajax({
                    url: route,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function(result) {
                        if (result.success == true) {
                            Swal.fire({
                                title: result.msg,
                                icon: "success",
                                timer: 3000,
                                showConfirmButton: false,
                            });
                            $("#quote_table").DataTable().ajax.reload(null, false);
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

    $(document).on('click', 'a.edit_quote_button', function () {
        $("#div_list").hide();

        id = $(this).data('id');
        cont = 0;
        product_ids = [];
        rowCont = [];
        blockCont = [];

        $.get("/quotes/" + id + "/edit", function (data) {
            $("#div_add").html(data);
            $("#div_add").show();

            loadDiv();

            if ($('#app-business').val() == 'workshop') {
                $(".select_discount").select2();

                $.ajax({
                    type: 'post',
                    url: '/quote/workshop-data/' + id,
                    dataType: 'json',
                    success: function (res) {
                        // Add service block
                        $('#service-blocks').empty();

                        $(res.service_blocks).each(function (i, v) {
                            fillServiceBlocks(res, i, v);
                        });
                    }
                });
                
            } else {
                $("#list").empty();
    
                var route = "/quotes/getLinesByQuote/" + id;
    
                $.get(route, function (res) {
                    $(res).each(function (key, value) {
                        variation_id = value.variation_id;
    
                        if (value.sku == value.sub_sku) {
                            name = value.name_product;
                        } else {
                            name = "" + value.name_product + " " + value.name_variation + "";
                        }
    
                        count = parseInt(jQuery.inArray(variation_id, product_ids));
    
                        if (count >= 0) {
                            Swal.fire({
                                title: "{{__('product.product_already_added')}}",
                                icon: "error",
                            });
                        } else {
                            product_ids.push(variation_id);
                            rowCont.push(cont);
    
                            warehouse_id = value.warehouse_id;
    
                            tax_detail = $("#tax_detail").val();
    
                            if (tax_detail == 1) {
                                unit_price = value.unit_price_exc_tax;
                            } else {
                                unit_price = value.unit_price_inc_tax;
                            }
    
                            tax = parseFloat(value.tax_percent + 1);
    
                            var row =
                                `<tr class="selected" id="row${cont}" style="height: 10px">
                                    <td>
                                        <button id="bitem${cont}" type="button" class="btn btn-danger btn-xs" onclick="deleteProduct(${cont}, ${variation_id});">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <input type="hidden" name="variation_id[]" value="${variation_id}">
                                        <input type="hidden" name="line_warehouse_id[]" value="${warehouse_id}">
                                        ${name}
                                    </td>
                                    <td>
                                        <input type="text" name="quantity[]" id="quantity${cont}" class="form-control form-control-sm input_number" value="${value.quantity}" onchange="calculate()" required>
                                    </td>
                                    <td>
                                        <input type="hidden" id="tax_percent${cont}" name="tax_percent[]" value="${tax}">
                                        <input type="hidden" class="four_decimals" id="unit_price_exc_tax${cont}" name="unit_price_exc_tax[]" value="${value.unit_price_exc_tax}">
                                        <input type="hidden" class="four_decimals" id="line_tax_amount${cont}" name="line_tax_amount[]" value="${value.tax_amount}">
                                        <input type="hidden" class="four_decimals" id="unit_price_inc_tax${cont}" name="unit_price_inc_tax[]" value="${value.unit_price_inc_tax}">
                                        <input type="text" name="unit_price[]" id="unit_price${cont}" class="form-control form-control-sm input_number price_editable four_decimals" value="${unit_price}" onchange="changePrice(${cont})">
                                    </td>
                                    <td>
                                        <select name="line_discount_type[]" id="line_discount_type${cont}" class="form-control select_discount" style="width: 100%;">
                                            <option value="fixed">@lang('quote.fixed')</option>
                                            <option value="percentage">@lang('quote.percentage')</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" id="line_discount_amount${cont}" name="line_discount_amount[]" class="form-control form-control-sm input_number four_decimals" onchange="calculate()" value="${value.discount_amount}" required>
                                    </td>
                                    <td>
                                        <input type="text" id="subtotal${cont}" name="subtotal[]" class="form-control form-control-sm input_number four_decimals" readonly required>
                                    </td>
                                </tr>`;
    
                            $("#list").append(row);
    
                            $(".select_discount").select2();
    
                            $('#line_discount_type'+cont+'').val(value.discount_type).change();
    
                            cont++;
    
                            calculate();
                        }
                    });
                });
            }
        });
    });

    function calculate()
    {
        total_price_exc_tax = 0.00;
        total_price_inc_tax = 0.00;
        total_tax = 0.00
        total_discount = 0.00;
        total = 0.00;

        $.each(rowCont, function(value) {

            var quantityg = __read_number($("#quantity"+value));
            var unit_price_exc_taxg = __read_number($("#unit_price_exc_tax"+value));
            var line_tax_amountg = __read_number($("#line_tax_amount"+value));
            var unit_price_inc_taxg = __read_number($("#unit_price_inc_tax"+value));
            var line_discount_typeg = $("#line_discount_type"+value).val();
            var line_discount_amountg = __read_number($("#line_discount_amount"+value));
            var unit_priceg = __read_number($("#unit_price"+value));

            if(quantityg)
            {
                if(isNaN(quantityg))
                {
                    quantity = 0.00;
                }
                else
                {
                    quantity = __read_number($("#quantity"+value));
                }
            }
            else
            {
                quantity = parseInt(0);
            }

            if(unit_price_exc_taxg)
            {
                if(isNaN(unit_price_exc_taxg))
                {
                    unit_price_exc_tax = 0.00;
                }
                else
                {
                    unit_price_exc_tax = __read_number($("#unit_price_exc_tax"+value));
                }
            }
            else
            {
                unit_price_exc_tax = 0.00;
            }

            if(line_tax_amountg)
            {
                if(isNaN(line_tax_amountg))
                {
                    line_tax_amount = 0.00;
                }
                else
                {
                    line_tax_amount = __read_number($("#line_tax_amount"+value));
                }
            }
            else
            {
                line_tax_amount = 0.00;
            }

            if(unit_price_inc_taxg)
            {
                if(isNaN(unit_price_inc_taxg))
                {
                    unit_price_inc_tax = 0.00;
                }
                else
                {
                    unit_price_inc_tax = __read_number($("#unit_price_inc_tax"+value));
                }
            }
            else
            {
                unit_price_inc_tax = 0.00;
            }

            if(unit_priceg)
            {
                if(isNaN(unit_priceg))
                {
                    unit_price = 0.00;
                }
                else
                {
                    unit_price = __read_number($("#unit_price_exc_tax"+value));
                }
            }
            else
            {
                unit_price = 0.00;
            }

            if(line_discount_typeg)
            {
                line_discount_type = __read_number($("#line_discount_type"+value));

            }

            if(line_discount_amountg)
            {
                if(isNaN(line_discount_amountg))
                {
                    line_discount_amount = 0.00;
                }
                else
                {
                    line_discount_amount = __read_number($("#line_discount_amount"+value));
                }
            }
            else
            {
                line_discount_amount = 0.00;
            }


            
            subtotal_price_exc_tax = unit_price_exc_tax * quantity;
            subtotal_price_inc_tax = unit_price_inc_tax * quantity;
            subtotal_tax = subtotal_price_inc_tax - subtotal_price_exc_tax;
            //subtotal_tax = unit_price_inc_tax - unit_price_exc_tax;
            
            if (line_discount_type == 'fixed') {
                subtotal_discount = line_discount_amount * quantity;
            } else {
                subtotal_discount = (unit_price * (line_discount_amount / 100)) * quantity;
            }

            tax_detail = $("#tax_detail").val();

            if (tax_detail == 1) {
                subtotal = subtotal_price_exc_tax - subtotal_discount;
            } else {
                subtotal = subtotal_price_inc_tax - subtotal_discount;
            }

            


            if(isNaN(subtotal))
            {
                subtotal = 0.00;
            }

            if(isInfinite(subtotal))
            {
                subtotal = 0.00;
            }
            

            __write_number($("#line_tax_amount"+value), subtotal_tax, false, 4);
            $("#line_tax_amount"+value).change();

            __write_number($("#subtotal"+value), subtotal, false, 4);
            $("#subtotal"+value).change();

            total_price_exc_tax = total_price_exc_tax + subtotal_price_exc_tax;
            total_price_inc_tax = total_price_inc_tax + subtotal_price_inc_tax;
            total_discount = total_discount + subtotal_discount;
            total = total + subtotal;


        });

discount_type = $("#discount_type").val();
discount_amountg = $("#discount_amount").val();

if(discount_amountg)
{
    if(isNaN(discount_amountg))
    {
        discount_amount = 0.00;
    }
    else
    {
        discount_amount = parseFloat($("#discount_amount").val());
    }
}
else
{
    discount_amount = 0.00;
}

if (discount_type == 'fixed') {
    discount = discount_amount;
} else {
    discount = total * (discount_amount / 100);
}

subtotal = total - discount;

$("#sums").val(total).change();
$("#sums_s").val(total).change();


tax_detail = $("#tax_detail").val();

if (tax_detail == 1) {
    total_tax = subtotal * 0.13;

    $("#total_before_tax").val(subtotal).change();
    $("#total_before_tax_s").val(subtotal).change();


    $("#tax_amount").val(total_tax).change();
    $("#tax_amount_s").val(total_tax).change();
    total_final = subtotal + total_tax;
    $("#total_final").val(total_final).change();
    $("#total_final_s").val(total_final).change();
    $("#div_tax").show();
    $("#div_subtotal").show();

} else {

    new_total_before_tax = subtotal / 1.13;

    total_tax = subtotal - new_total_before_tax;

    $("#tax_amount").val(total_tax).change();
    $("#tax_amount_s").val(total_tax).change();

    

    $("#total_before_tax").val(new_total_before_tax).change();
    $("#total_before_tax_s").val(new_total_before_tax).change();


    total_final = subtotal;
    $("#total_final").val(total_final).change();
    $("#total_final_s").val(total_final).change();
    $("#div_tax").hide();
    $("#div_subtotal").hide();
}


}

function excelQuote(id)
{
    var url = '{!!URL::to('/quotes/excel/:id')!!}';
    url = url.replace(':id', id);
    window.open(url, '_blank');
}

//funcion para crear una venta perdida 
$(document).on('click', 'a.add_lost_sale', function() {
    $("div.lost_sale_modal").load($(this).data('href'), function() {
        $(this).modal('show');

        $('form#lost_sale_add_form').submit(function(e) {
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
                        $("#quote_table").DataTable().ajax.reload();
                        $('div.lost_sale_modal').modal('hide');
                        Swal.fire({
                            title: result.msg,
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        $('#content').hide();
                    } else {
                        Swal.fire({
                            title: result.msg,
                            icon: "error",
                        });
                    }
                },
                error: function(msj) {
                    // $("#btn-add-customer").prop('disabled', false);
                    // $("#btn-close-modal-add-customer").prop('disabled', false);
                    var errormessages = "";
                    $.each(msj.responseJSON.errors, function(i, field) {
                        errormessages += "<li>" + field + "</li>";
                    });
                    Swal.fire({
                        title: "{{ __('customer.errors') }}",
                        icon: "error",
                        html: "<ul>" + errormessages + "</ul>",
                    });
                }
            });
        });
    });
});

//funcion para editar una venta perdida
$(document).on('click', 'a.edit_lost_sale', function() {
    $("div.edit_lost_sale_modal").load($(this).data('href'), function() {
        $(this).modal('show');

        $('form#lost_sale_edit_form').submit(function(e) {
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', false);
            var data = $(this).serialize();

            $.ajax({
                method: "PUT",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        $("#quote_table").DataTable().ajax.reload();
                        $('div.lost_sale_edit_form').modal('hide');
                        Swal.fire({
                            title: result.msg,
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        $('#content').hide();
                    } else {
                        Swal.fire({
                            title: result.msg,
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
                        title: "{{ __('customer.errors') }}",
                        icon: "error",
                        html: "<ul>" + errormessages + "</ul>",
                    });
                }
            });
        });
    });
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
            workshopCalculate();
        }
    });
});

/**
 * Get customer vehicles.
 * 
 * @param  int  $id
 * @return void
 */
function getCustomerVehicles(id) {
    $('#customer_vehicle_id').empty();

    let route = '/quotes/get-customer-vehicles/' + id;

    $.get(route, function (res) {
        $('#customer_vehicle_id').append('<option value="0" disabled selected>@lang('messages.please_select')</option>');

        $(res).each(function(key, value) {
            $('#customer_vehicle_id').append('<option value="' + value.id + '">' + value.name + '</option>');
        });
    });
}

/**
 * Add spares to service block.
 * 
 * @param  int  variation_id
 * @param  int  service_block_index
 * @param  int  row_index
 * @param  int  service_parent_id
 * @param  int  warehouse
 * @param  int  quote_id
 * @return void
 */
function addSpare(variation_id, service_block_index, row_index, service_parent_id, warehouse, quote_id) {
    let tax_detail = $("#tax_detail").val();
    let warehouse_id = warehouse != null ? warehouse : $('select#warehouse_id').val();
    let selling_price_group_id = $('select#selling_price_group_id').val();

    $.ajax({
        method: 'post',
        url: '/quotes/add-spare/' + variation_id,
        data: {
            selling_price_group_id: selling_price_group_id,
            warehouse_id: warehouse_id,
            tax_detail: tax_detail,
            service_block_index: service_block_index,
            row_index: row_index,
            service_parent_id: service_parent_id,
            quote_id: quote_id
        },
        dataType: 'json',
        success: function (result) {
            if (result.success == 1) {
                let location = '#list-' + service_block_index;
                let variation_ids = [];
                let count_ids = 0;

                $(location + ' tr').each(function (index) {
                    variation_ids.push(parseInt($(this).find('#variation_id-' + service_block_index).val()));
                });

                count_ids = parseInt(jQuery.inArray(result.product.variation_id, variation_ids));

                if (count_ids >= 0) {
                    Swal.fire({
                        title: "{{ __('product.product_already_added') }}",
                        icon: "error",
                    });

                } else {
                    rowCont.push(parseInt($('#row-index').val()));

                    $('#list-' + service_block_index).append(result.html_content);

                    $(".select_discount").select2();

                    // Update row index
                    $('#row-index').val(parseInt($('#row-index').val()) + 1);

                    workshopCalculate();
                }
            }
        }
    });
}

/**
 * Add spares to service block.
 * 
 * @param  int  variation_id
 * @param  int  service_block_index
 * @param  int  row_index
 * @param  int  service_parent_id
 * @param  int  warehouse
 * @param  int  quote_id
 * @return void
 */
function addSpareNotStock(variation_id, service_block_index, row_index, service_parent_id, warehouse, quote_id) {
    let tax_detail = $("#tax_detail").val();
    let warehouse_id = warehouse != null ? warehouse : $('select#warehouse_id').val();
    let selling_price_group_id = $('select#selling_price_group_id').val();

    $.ajax({
        method: 'post',
        url: '/quotes/add-spare-not-stock/' + variation_id,
        data: {
            selling_price_group_id: selling_price_group_id,
            warehouse_id: warehouse_id,
            tax_detail: tax_detail,
            service_block_index: service_block_index,
            row_index: row_index,
            service_parent_id: service_parent_id,
            quote_id: quote_id
        },
        dataType: 'json',
        success: function (result) {
            if (result.success == 1) {
                let location = '#list-' + service_block_index;
                let variation_ids = [];
                let count_ids = 0;

                $(location + ' tr').each(function (index) {
                    variation_ids.push(parseInt($(this).find('#variation_id-' + service_block_index).val()));
                });

                count_ids = parseInt(jQuery.inArray(result.product.variation_id, variation_ids));

                if (count_ids >= 0) {
                    Swal.fire({
                        title: "{{ __('product.product_already_added') }}",
                        icon: "error",
                    });

                } else {
                    rowCont.push(parseInt($('#row-index').val()));

                    $('#list-' + service_block_index).append(result.html_content);

                    $(".select_discount").select2();

                    // Update row index
                    $('#row-index').val(parseInt($('#row-index').val()) + 1);

                    workshopCalculate();
                }
            }
        }
    });
}

/**
 * Calculate subtotals and totals in workshop form.
 * 
 * @return void
 */
function workshopCalculate() {
    let total_price_exc_tax = 0.00;
    let total_price_inc_tax = 0.00;
    let total_tax = 0.00
    let total_discount = 0.00;
    let total = 0.00;

    $.each(blockCont, function (block_value) {
        let block_subtotal = 0;
        let block_unit_price_exc_tax = 0;
        let block_line_tax_amount = 0;
        let block_unit_price_inc_tax = 0;
        let block_unit_price = 0;
        let block_line_discount_amount = 0;

        $.each(rowCont, function (value) {
            let identifier = '-'  + block_value + '-' + value;

            if ($('#quantity' + identifier).length > 0) {
                let quantityg = __read_number($('#quantity' + identifier));
                let unit_price_exc_taxg = __read_number($('#unit_price_exc_tax' + identifier));
                let line_tax_amountg = __read_number($('#line_tax_amount' + identifier));
                let unit_price_inc_taxg = __read_number($('#unit_price_inc_tax' + identifier));
                let line_discount_typeg = $('#line_discount_type' + identifier).val();
                let line_discount_amountg = __read_number($('#line_discount_amount' + identifier));
                let unit_priceg = __read_number($('#unit_price' + identifier));
        
                if (quantityg) {
                    if (isNaN(quantityg)) {
                        quantity = 0.00;
                    } else {
                        quantity = __read_number($('#quantity' + identifier));
                    }
                } else {
                    quantity = parseInt(0);
                }
        
                if (unit_price_exc_taxg) {
                    if (isNaN(unit_price_exc_taxg)) {
                        unit_price_exc_tax = 0.00;
                    } else {
                        unit_price_exc_tax = __read_number($('#unit_price_exc_tax' + identifier));
                    }
                } else {
                    unit_price_exc_tax = 0.00;
                }
        
                if (line_tax_amountg) {
                    if (isNaN(line_tax_amountg)) {
                        line_tax_amount = 0.00;
                    } else {
                        line_tax_amount = __read_number($('#line_tax_amount' + identifier));
                    }
                } else {
                    line_tax_amount = 0.00;
                }

                if (unit_price_inc_taxg) {
                    if (isNaN(unit_price_inc_taxg)) {
                        unit_price_inc_tax = 0.00;
                    } else {
                        unit_price_inc_tax = __read_number($('#unit_price_inc_tax' + identifier));
                    }
                } else {
                    unit_price_inc_tax = 0.00;
                }

                if (unit_priceg) {
                    if (isNaN(unit_priceg)) {
                        unit_price = 0.00;
                    } else {
                        unit_price = __read_number($('#unit_price_exc_tax' + identifier));
                    }
                } else {
                    unit_price = 0.00;
                }
        
                if (line_discount_typeg) {
                    line_discount_type = __read_number($('#line_discount_type' + identifier));
                }
        
                if (line_discount_amountg) {
                    if (isNaN(line_discount_amountg)) {
                        line_discount_amount = 0.00;
                    } else {
                        line_discount_amount = __read_number($('#line_discount_amount' + identifier));
                    }
                } else {
                    line_discount_amount = 0.00;
                }

                subtotal_price_exc_tax = unit_price_exc_tax * quantity;
                subtotal_price_inc_tax = unit_price_inc_tax * quantity;
                subtotal_tax = subtotal_price_inc_tax - subtotal_price_exc_tax;

                tax_detail = $('#tax_detail').val();

                if (line_discount_typeg == 'fixed') {
                    subtotal_discount = line_discount_amount * quantity;
                } else {
                    if (tax_detail == 1) {
                        subtotal_discount = (unit_price * (line_discount_amount / 100)) * quantity;
                    } else {
                        subtotal_discount = (unit_price_inc_tax * (line_discount_amount / 100)) * quantity;
                    }
                }
        
                if (tax_detail == 1) {
                    subtotal = subtotal_price_exc_tax - subtotal_discount;
                } else {
                    subtotal = subtotal_price_inc_tax - subtotal_discount;
                }

                if (isNaN(subtotal)) {
                    subtotal = 0.00;
                }

                if (isInfinite(subtotal)) {
                    subtotal = 0.00;
                }

                __write_number($('#line_tax_amount' + identifier), subtotal_tax, false, 4);
                $('#line_tax_amount' + identifier).change();
        
                __write_number($('#subtotal' + identifier), subtotal, false, 4);
                $('#subtotal' + identifier).change();

                total_price_exc_tax = total_price_exc_tax + subtotal_price_exc_tax;
                total_price_inc_tax = total_price_inc_tax + subtotal_price_inc_tax;
                total_discount = total_discount + subtotal_discount;
                total = total + subtotal;

                block_subtotal += subtotal;
                block_unit_price_exc_tax += unit_price_exc_tax;
                block_line_tax_amount += subtotal_tax;
                block_unit_price_inc_tax += unit_price_inc_tax;
                block_unit_price += unit_price;
                block_line_discount_amount += line_discount_amount;
            }
        });

        __write_number($('#subtotal-' + block_value), block_subtotal, false, 4);
        $('#unit_price_exc_tax-' + block_value).val(block_unit_price_exc_tax);
        $('#line_tax_amount-' + block_value).val(block_line_tax_amount);
        $('#unit_price_inc_tax-' + block_value).val(block_unit_price_inc_tax);
        $('#unit_price-' + block_value).val(block_unit_price);
        $('#line_discount_amount-' + block_value).val(block_line_discount_amount);
    });

    discount_type = $('#discount_type').val();
    discount_amountg = $('#discount_amount').val();

    if (discount_amountg) {
        if (isNaN(discount_amountg)) {
            discount_amount = 0.00;
        } else {
            discount_amount = parseFloat($('#discount_amount').val());
        }
    } else {
        discount_amount = 0.00;
    }

    if (discount_type == 'fixed') {
        discount = discount_amount;
    } else {
        discount = total * (discount_amount / 100);
    }

    subtotal = total - discount;

    $('#sums').val(total).change();
    $('#sums_s').val(total).change();

    tax_detail = $('#tax_detail').val();

    if (tax_detail == 1) {
        total_tax = subtotal * 0.13;

        $('#total_before_tax').val(subtotal).change();
        $('#total_before_tax_s').val(subtotal).change();

        $('#tax_amount').val(total_tax).change();
        $('#tax_amount_s').val(total_tax).change();

        total_final = subtotal + total_tax;

        $('#total_final').val(total_final).change();
        $('#total_final_s').val(total_final).change();

        $('#div_tax').show();
        $('#div_subtotal').show();

    } else {
        new_total_before_tax = subtotal / 1.13;

        total_tax = subtotal - new_total_before_tax;

        $('#tax_amount').val(total_tax).change();
        $('#tax_amount_s').val(total_tax).change();

        $('#total_before_tax').val(new_total_before_tax).change();
        $('#total_before_tax_s').val(new_total_before_tax).change();

        total_final = subtotal;

        $('#total_final').val(total_final).change();
        $('#total_final_s').val(total_final).change();

        $('#div_tax').hide();
        $('#div_subtotal').hide();
    }
}

/**
 * Change price.
 * 
 * @param  int  service_block_index
 * @param  int  row_index
 * @return void
 */
function workshopChangePrice(service_block_index, row_index) {
    let identifier = '-' + service_block_index + '-' + row_index;

    let new_price = __read_number($('#unit_price' + identifier));
    new_price = new_price.toFixed(4);

    tax_percent = __read_number($('#tax_percent' + identifier));

    tax_detail = $('#tax_detail').val();

    if (tax_detail == 1) {
        $('#unit_price_exc_tax' + identifier).val(new_price).change();

        new_price_inc_tax = new_price * tax_percent;
        new_tax_amount = new_price_inc_tax - new_price;

        $('#unit_price_inc_tax' + identifier).val(new_price_inc_tax).change();
        $('#line_tax_amount' + identifier).val(new_tax_amount).change();

    } else {
        $('#unit_price_inc_tax' + identifier).val(new_price).change();

        new_price_exc_tax = new_price / tax_percent;
        new_tax_amount = new_price - new_price_exc_tax;

        $('#unit_price_exc_tax' + identifier).val(new_price_exc_tax).change();
        $('#line_tax_amount' + identifier).val(new_tax_amount).change();
    }

    workshopCalculate();
}

/**
 * Delete spare line.
 * 
 * @param  int  service_block_index
 * @param  int  row_index
 * @param  int  variation_id
 * @return void
 */
function deleteSpare(service_block_index, row_index, variation_id) { 
    $('#row-' + service_block_index + '-' + row_index).remove();
    workshopCalculate();
}

/**
 * Calculate taxes.
 * 
 * @return void
 */
function workshopCalculateTax() {
    let tax_detail = $("#tax_detail").val();

    $.each(blockCont, function (block_value) {
        $.each(rowCont, function (value) {
            let identifier = '-'  + block_value + '-' + value;

            price_exc_tax = __read_number($('#unit_price_exc_tax' + identifier));
            price_inc_tax = __read_number($('#unit_price_inc_tax' + identifier));

            let line_discount_amount = __read_number($('#line_discount_amount' + identifier));

            let tax_percent = (price_inc_tax / price_exc_tax) - 1;
    
            if (tax_detail == 1) {
                $('#unit_price' + identifier).val(price_exc_tax).change();
                $('#line_discount_amount' + identifier).val(line_discount_amount / (1 + tax_percent)).change();

            } else {
                $('#unit_price' + identifier).val(price_inc_tax).change();
                $('#line_discount_amount' + identifier).val(line_discount_amount * (1 + tax_percent)).change();
            }
        });
    });

    workshopCalculate();
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
    // let selling_price_group_id = $('select#selling_price_group_id').val();
    let tax_detail = $("#tax_detail").val();
    let service_block_index = i;
    let row_index = $('#row-index').val();
    let warehouse_id = v.warehouse_id;

    $.ajax({
        method: 'post',
        url: '/quotes/add-service-block/' + v.variation_id,
        data: {
            // selling_price_group_id: selling_price_group_id,
            warehouse_id: warehouse_id,
            tax_detail: tax_detail,
            service_block_index: service_block_index,
            row_index: row_index,
            view: 'quote',
            quote_line_id: v.id
        },
        dataType: 'json',
        success: function (result) {
            if (result.success == 1) {
                // Add service block
                let appended = $('#service-blocks').append(result.html_content);

                __select2($(appended).find('.select2'));

                blockCont.push(i);

                // Update service block index
                $('#service-block-index').val(i);

                rowCont.push(parseInt($('#row-index').val()));

                // Update row index
                $('#row-index').val(parseInt($('#row-index').val()) + 1);

                if ($("#search_product-" + result.service_block_index).length > 0) {
                    $("#search_product-" + result.service_block_index).autocomplete({
                        source: function (request, response) {
                            $.getJSON(
                                "/products/list_for_quotes",
                                {
                                    warehouse_id: $('#warehouse_id').val(),
                                    term: request.term
                                },
                                response
                            );
                        },
                        minLength: 1,
                        delay: 250,
                        response: function (event, ui) {
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
                                if (ui.item.qty_available != null) {
                                    $(this).val(null);
                                    warehouse_id = $('select#warehouse_id').val();
                                    selling_price_group_id = $('select#selling_price_group_id').val();

                                    addSpare(
                                        ui.item.variation_id,
                                        $(this).data('service-block-index'),
                                        $('#row-index').val(),
                                        $(this).data('service-parent-id'),
                                        null,
                                        null
                                    );

                                } else {
                                    enable_not_stock = $("#enable_not_stock").val();

                                    if (enable_not_stock == 1) {
                                        Swal.fire({
                                            title: LANG.sure,
                                            text: "{{ __('quote.not_stock_content') }}",
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: "{{ __('messages.accept') }}",
                                            cancelButtonText: "{{ __('messages.cancel') }}"
                                        }).then((willDelete) => {
                                            if (willDelete.value) {
                                                addSpareNotStock(
                                                    ui.item.variation_id,
                                                    $(this).data('service-block-index'),
                                                    $('#row-index').val(),
                                                    $(this).data('service-parent-id'),
                                                    null,
                                                    null
                                                );
                                            }
                                        });

                                    } else {
                                        Swal.fire({
                                            title: LANG.out_of_stock,
                                            icon: "error",
                                        });
                                    }
                                }
                                
                            } else {
                                enable_not_stock = $("#enable_not_stock").val();

                                if (enable_not_stock == 1) {
                                    Swal.fire({
                                        title: LANG.sure,
                                        text: "{{ __('quote.not_stock_content') }}",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: "{{ __('messages.accept') }}",
                                        cancelButtonText: "{{ __('messages.cancel') }}"
                                    }).then((willDelete) => {
                                        if (willDelete.value) {
                                            addSpareNotStock(
                                                ui.item.variation_id,
                                                $(this).data('service-block-index'),
                                                $('#row-index').val(),
                                                $(this).data('service-parent-id'),
                                                null,
                                                null
                                            );
                                        }
                                    });

                                } else {
                                    Swal.fire({
                                        title: LANG.out_of_stock,
                                        icon: "error",
                                    });
                                }
                            }
                        }
                    })
                    .autocomplete('instance')._renderItem = function (ul, item) {
                        if (item.enable_stock == 1 && item.qty_available <= 0) {
                            enable_not_stock = $('#enable_not_stock').val();

                            if (enable_not_stock == 1) {
                                var string = '<div> ' + item.name;

                                if (item.type == 'variable') {
                                    string += '-' + item.variation;
                                }

                                var selling_price = item.selling_price;

                                if (item.variation_group_price) {
                                    selling_price = item.variation_group_price;
                                }

                                string += ' (' + item.sub_sku + ')' + '<br> <b>' + LANG.price + ':</b>$' + selling_price
                                    + ' <b>E:</b> ' + item.rack + '<b>F:</b> ' + item.row + '<b>P:</b> ' + item.position
                                    + ' (' + LANG.out_of_stock + ') </div>';

                                return $("<li>" ).append(string).appendTo( ul );

                            } else {
                                var string = '<li class="ui-state-disabled"> '+ item.name;

                                if (item.type == 'variable') {
                                    string += '-' + item.variation;
                                }

                                var selling_price = item.selling_price;

                                if (item.variation_group_price) {
                                    selling_price = item.variation_group_price;
                                }

                                string += ' (' + item.sub_sku + ')' + '<br> <b>' + LANG.price + ':</b>$' + selling_price
                                    + ' (' + LANG.out_of_stock + ') </li>';

                                return $(string).appendTo(ul);
                            }
                            
                        } else {
                            var string =  '<div>' + item.name;

                            if (item.type == 'variable') {
                                string += '-' + item.variation;
                            }

                            var selling_price = item.selling_price;

                            if (item.variation_group_price) {
                                selling_price = item.variation_group_price;
                            }

                            string += ' (' + item.sub_sku + ')' + '<br> <b>' + LANG.price + ':</b>$' + selling_price + ' <b>'
                                + LANG.stock + ':</b>' + Math.round(item.qty_available, 0) + ' </div>';

                            return $('<li>').append(string).appendTo(ul);
                        }
                    };
                }

                $.ajax({
                    type: 'post',
                    url: '/quote/get-spare-lines',
                    data: {
                        quote_id : data.id,
                        // service_block_index: result.service_block_index,
                        service_parent_id: v.variation_id,
                    },
                    dataType: 'json',
                    success: function (response) {
                        $(response).each(function (key, value) {
                            if (value.validate_stock == 1) {
                                addSpare(
                                    value.variation_id,
                                    result.service_block_index,
                                    key,
                                    value.service_parent_id,
                                    value.warehouse_id,
                                    value.quote_id
                                )
                            } else {
                                addSpareNotStock(
                                    value.variation_id,
                                    result.service_block_index,
                                    key,
                                    value.service_parent_id,
                                    value.warehouse_id,
                                    value.quote_id
                                )
                            }
                        });
                    }
                });

                $(".search-spare").prop('readonly', true);
            }
        }
    });
}
</script>
@endsection
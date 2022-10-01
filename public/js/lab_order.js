$(document).ready(function() {
    // Masks
    $('.di-mask').mask('00/00');
    $('.size-mask').mask('00-00-00');

    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

    // File input
    fileinput_setting = {
        'showUpload': false,
        'showPreview': false,
        'browseLabel': LANG.file_browse_label,
        'removeLabel': LANG.remove
    };

    // Lab orders modal
    $('.lab_orders_modal').on('shown.bs.modal', function () {
        // Select2
        $(this).find('.select2').select2();

        // File input
        $("#upload_document").fileinput(fileinput_setting);
    });

    // Add order modal
    $('#modal-add-order').on('shown.bs.modal', function () {
        // Select2
        $(this).find('.select2').select2();

        // File input
        $("#upload_document").fileinput(fileinput_setting);
    });

    // Edit order modal
    $('#modal-edit-order').on('shown.bs.modal', function () {
        // File input
        $("#eupload_document").fileinput(fileinput_setting);

        $("#esearch_product").focus();

        $("#esearch_product").autocomplete({
            source: function(request, response) {
                $.getJSON("/lab-orders/products/list_for_orders", {
                    term: request.term,
                    warehouse_id: $('#ewarehouse_id').val()
                }, response);
            },
            minLength: 2,
            response: function(event,ui) {
                if (ui.content.length == 1) {
                    ui.item = ui.content[0];
                } else if (ui.content.length == 0) {
                    swal(LANG.no_products_found)
                    .then((value) => {
                        $('input#esearch_product').select();
                    });
                }
            },
            focus: function( event, ui ) {
                if(ui.item.qty_available <= 0) {
                    return false;
                }
            },
            select: function( event, ui ) {
                if(ui.item.enable_stock != 1 || ui.item.qty_available > 0){
                    $(this).val(null);
                    warehouse_id = $("#ewarehouse_id").val();
                    eaddProduct(ui.item.variation_id, warehouse_id);
                } else{
                    alert(LANG.out_of_stock);
                }
            }
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            ul.css('overflow-y', 'scroll');
            ul.css('overflow-x', 'hidden');
            ul.css('height', '10em');
            if (item.enable_stock == 1 && item.qty_available <= 0) {
                var string = '<div><li class="ui-state-disabled""> '+ item.name;
                if(item.type == 'variable'){
                    string += '-' + item.variation;
                }
                string += ' (' + item.sub_sku + ') <br> <b>' + LANG.stock + ':</b> (' + LANG.out_of_stock + ')';
                string += ' </li></div>';
                return $(string).appendTo(ul);
            } else {
                var string =  "<div>" + item.name;
                if(item.type == 'variable'){
                    string += '-' + item.variation;
                }
                string += ' (' + item.sub_sku + ') <br> <b>' + LANG.stock + ':</b>' + Math.round(item.qty_available, 0);
                string += '</div>';
                return $("<li>").append(string).appendTo( ul );
            }
        };

        // Get patients
		$('#epatient_id').select2({
			ajax: {
				url: '/patients_lab/get_patients',
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

        // Get customers
        $('#ecustomer_id').select2({
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
    });

    // Add lab order modal
    $('div.add_lab_order_modal').on('shown.bs.modal', function () {
        // Save order
        $("input#btn-add-order").click(function(){
            $("#btn-add-order").prop('disabled', true);
            $("#btn-close-modal-add-order").prop('disabled', true);
            // Convert to FormData
            var formData = document.getElementById('lab_order_add_form');
            var data = new FormData(formData);
            route = "/lab-orders";
            token = $("#token").val();
            $.ajax({
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'POST',
                data: data,
                processData: false,   // Tell jQuery not to process the data
                contentType: false,   // Tell jQuery not to set contentType
                success: function(result){
                    if (result.success == true) {
                        $("#btn-add-order").prop('disabled', false);
                        $("#btn-close-modal-add-order").prop('disabled', false);
                        $('#modal-add-order').modal('hide');
                        $("#lab_orders_table").DataTable().ajax.reload(null, false);
                        Swal.fire({
                            title: result.msg,
                            icon: "success",
                            timer: 1000,
                            showConfirmButton: false,
                        });
                    } else {
                        $("#btn-add-order").prop('disabled', false);
                        $("#btn-close-modal-add-order").prop('disabled', false);
                        Swal.fire({
                            title: result.msg,
                            icon: "error",
                        });
                    }
                },
                error: function(msj) {
                    $("#btn-add-order").prop('disabled', false);
                    $("#btn-close-modal-add-order").prop('disabled', false);
                    var errormessages = "";
                    $.each(msj.responseJSON.errors, function(i, field) {
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

        // Save order and add another
        $("#btn-save-n-add-another").click(function() {
            $("#btn-save-n-add-another").prop('disabled', true);
            $("#btn-close-modal-add-order").prop('disabled', true);
            var data = $("#lab_order_add_form").serialize();
            route = "/lab-orders";
            token = $("#token").val();
            $.ajax({
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(result){
                    if (result.success == true) {
                        $("#btn-save-n-add-another").prop('disabled', false);
                        $("#btn-close-modal-add-order").prop('disabled', false);
                        $('#modal-add-order').modal('hide');
                        $("#lab_orders_table").DataTable().ajax.reload(null, false);
                        Swal.fire({
                            title: result.msg,
                            icon: "success",
                            timer: 1000,
                            showConfirmButton: false,
                        });
                        // Show lab order modal
                        var patientid = $('select#patient_id').val();
                        $.ajax({
                            type: "GET",
                            url: "/pos/get_lab_order/" + result.data.transaction_id + "/" + patientid,
                            dataType: "html",
                            success: function(data2){
                                $("div.add_lab_order_modal").html(data2).modal("show");
                            }
                        });
                    } else {
                        $("#btn-save-n-add-another").prop('disabled', false);
                        $("#btn-close-modal-add-order").prop('disabled', false);
                        Swal.fire({
                            title: result.msg,
                            icon: "error",
                        });
                    }
                },
                error:function(msj){
                    $("#btn-save-n-add-another").prop('disabled', false);
                    $("#btn-close-modal-add-order").prop('disabled', false);
                    var errormessages = "";
                    $.each(msj.responseJSON.errors, function(i, field){
                        errormessages+="<li>"+field+"</li>";
                    });
                    Swal.fire
                    ({
                        title: LANG.errors,
                        icon: "error",
                        html: "<ul>"+ errormessages+ "</ul>",
                    });
                }
            });
        });

        // Datetimepicker
        $(function () {
            $('#datetimepicker1').datetimepicker({
                locale: 'es'
            });

            $('#delivery').datetimepicker({
                locale: 'es'
            });

            $('#edatetimepicker1').datetimepicker({
                locale: 'es'
            });

            $('#edelivery').datetimepicker({
                locale: 'es'
            });
        });

        // Validation dnsp and di
        var _dnsp_od = $('input[name="dnsp_od"]');
        var _dnsp_os = $('input[name="dnsp_os"]');
        var _di = $('input[name="di"]');

        _dnsp_od.on('change', function(e) {
            if(_dnsp_od.val() != '') {
                _di.prop('readonly', true);
                _dnsp_os.prop('required', true);
            } else {
                if (_dnsp_os.val() == '') {
                    _di.prop('readonly', false);
                    _dnsp_os.prop('required', false);
                }
            }
        });

        _dnsp_os.on('change', function(e) {
            if(_dnsp_os.val() != '') {
                _di.prop('readonly', true);
                _dnsp_od.prop('required', true);
            } else {
                if (_dnsp_od.val() == '') {
                    _di.prop('readonly', false);
                    _dnsp_od.prop('required', false);
                }
            }
        });

        _di.on('change', function(e) {
            if(_di.val() != '') {
                _dnsp_od.prop('readonly', true);
                _dnsp_os.prop('readonly', true);
            } else {
                _dnsp_od.prop('readonly', false);
                _dnsp_os.prop('readonly', false);
            }
        });

        // Fill hoop fields (create after billing)
        $('#hoop').on('change', function(e) {
            var variation_id = $("#hoop option:selected").val();
            var transaction_id = $("#transaction_id").val();
            var route = "/lab-orders/fillHoopFields/"+variation_id+"/"+transaction_id;
            $.get(route, function(res){
                $('#size').val(res.size);
                $('#color').val(res.color);
            });
        });

        // Fill hoop fields (create without billing before)
        $('#hoop_c').on('change', function(e) {
            var variation_id = $("#hoop_c option:selected").val();
            var route = "/lab-orders/fillHoopFields2/"+variation_id;
            $.get(route, function(res){
                $('.size_c').val(res.size);
                $('.color_c').val(res.color);
            });
        });

        // Select first option (glasses)
        $("#glass").prop("selectedIndex", 1).change();
        $("#glass_od").prop("selectedIndex", 1).change();
        $("#glass_os").prop("selectedIndex", 1).change();

        // Select first option (hoop)
        $("#hoop").prop("selectedIndex", 1).change();

        // Select first option (ar)
        $("#ar").prop("selectedIndex", 1).change();

        // Search optometrist
        $(document).on('click', '#btn-optometrist', function() {
            search_optometrist(
                $('#input-optometrist').val(),
                $('#input-optometrist'),
                $('#txt-optometrist'),
                $('#optometrist'),
                'employee'
            );
        });

        // Get patients
		$('#patient_id').select2({
			ajax: {
				url: '/patients_lab/get_patients',
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

        // Get customers
        $('#lab_customer_id').select2({
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

        // On click of select-glass-hoop div
        $('.select-glass-hoop').on('click', function () {
            $('#product-type').val($(this).find('select').data('product'));
        });

        // Get products
        $('.lab-order-products').select2({
            ajax: {
                url: '/lab_orders/products/list_for_lab_orders',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term,
                        page: params.page,
                        glass: $('#product-type').val()
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (obj) {
                            return {
                                id: obj.id,
                                text: obj.name
                            }
                        })
                    };
                }
            },
            minimumInputLength: 1,
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        if ($('#default_location').val() === null || $('#default_location').val() == '') {
            $('#location_lo').select2('open');
        } else {
            $('#invoice_lo').focus();
        }
    });

    // Change lab order status
    $(document).on('click', '.status-lab-order-change', function (e) {
        e.preventDefault();

        let order_id = $(this).data('order-id');
        let status_id = $(this).data('status-id');

        change_status(order_id, status_id);
    });

    // Delete order
    $(document).on('click', 'button.delete_lab_orders_button', function() {
        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_lab_order,
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
                            lab_orders_table.ajax.reload(null, false);
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

    // 
    $('.modal').on("hidden.bs.modal", function (e) {
        if ($('.modal:visible').length) {
            $('body').addClass('modal-open');
        }
    });

    // Load actions button options
    $(document).on('click', '.btn-actions', function() {
        let id = $(this).data('lab-order-id');
        add_toggle_dropdown($(this), id);
    });

    function add_toggle_dropdown(btn, id) {
        if (btn.closest('.btn-group').hasClass('open')) {
            let loading = `
                <div class="text-center loading" style="margin: 5px 0;">
                    <i class="fa fa-circle-o-notch fa-spin fa-2x" style="color: #777;"></i>
                </div>`;
    
            btn.closest('.btn-group').find('ul').empty();
            btn.closest('.btn-group').find('ul').html(loading);
    
            $.ajax({
                method: "GET",
                url: '/lab_order/get_toggle_dropdown/' + id,
                dataType: 'html',
                success: function(data) {
                    btn.closest('.btn-group').find('ul').empty();
                    btn.closest('.btn-group').find('ul').html(data);
                }
            });
        }
    }

    function print_lab_order(lab_order_id) {
        $.ajax({
            method: "GET",
            url: '/lab-orders/print-change-status/' + lab_order_id + '/6',
            dataType: "json",
            success: function(result) {
                if (result.success == 1 && result.order.html_content != '') {
                    $('#lab_orders_table').DataTable().ajax.reload(null, false);
                    $('#order_section').html(result.order.html_content);
                    __currency_convert_recursively($('#order_section'));
                    setTimeout(function() { window.print(); }, 1000);
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

    // Edit lab order
    $("#btn-save-print-order").click(function(){
        $("#btn-save-print-order").prop('disabled', true);
        $("#btn-close-modal-edit-order").prop('disabled', true);
        var data = $("#form-edit-order").serialize();
        var id = $("#order_id").val();
        route = "/lab-orders/"+id;
        $.ajax({
            url: route,
            type: 'PUT',            
            dataType: 'json',
            data: data,
            success:function(result) {
                if (result.success == true) {
                    $("#btn-save-print-order").prop('disabled', false);
                    $("#btn-close-modal-edit-order").prop('disabled', false);
                    $("#modal-edit-order").modal('hide');
                    print_lab_order(result.lab_order_id);
                } else {
                    $("#btn-save-print-order").prop('disabled', false);
                    $("#btn-close-modal-edit-order").prop('disabled', false);
                    Swal.fire({
                        title: result.msg,
                        icon: "error",
                    });
                }
            },
            error: function(msj) {
                $("#btn-save-print-order").prop('disabled', false);
                $("#btn-close-modal-edit-order").prop('disabled', false);
                var errormessages = "";
                $.each(msj.responseJSON.errors, function(i, field){
                    errormessages+="<li>"+field+"</li>";
                });
                Swal.fire
                ({
                    title: LANG.errors,
                    icon: "error",
                    html: "<ul>"+ errormessages+ "</ul>",
                });
            }
        });
    });
});

// Check own hoop
function checkOwnHoop() {
    if ($('#is_own_hoop').is(':checked')) {
        $('#hoop_name').show();
        $('#div_hoop').hide();
    } else {
        $('#div_hoop').show();
        $('#hoop_name').hide();
    }
}

// Show/hide graduation card fields
function repairCheck() {
    if ($('#is_repair').is(':checked')) {
        $('.graduation_card_fields').hide();
    } else {
        $('.graduation_card_fields').show();
    }
}

// Show/hide graduation card fields
function erepairCheck() {
    if ($('#eis_repair').is(':checked')) {
        $('.graduation_card_fields').hide();
    } else {
        $('.graduation_card_fields').show();
    }
}

// Show/hide external labs
function extLabCheck() {
    if ($('#check_ext_lab').is(':checked')) {
        $('#lab_extern_box').show();
    } else {
        $('#lab_extern_box').hide();
    }

    if ($('#echeck_ext_lab').is(':checked')) {
        $('#elab_extern_box').show();
    } else {
        $('#elab_extern_box').hide();
    }
}

// Balance for right eye
function balanceOD() {
    if ($('#balance_od').is(':checked')) {
        $('input[name="sphere_od"]').val('');
        $('input[name="sphere_od"]').prop('readonly', true);

        $('input[name="cylindir_od"]').val('');
        $('input[name="cylindir_od"]').prop('readonly', true);

        $('input[name="axis_od"]').val('');
        $('input[name="axis_od"]').prop('readonly', true);

        $('input[name="base_od"]').val('');
        $('input[name="base_od"]').prop('readonly', true);

        $('input[name="addition_od"]').val('');
        $('input[name="addition_od"]').prop('readonly', true);
    } else {
        $('input[name="sphere_od"]').val('');
        $('input[name="sphere_od"]').prop('readonly', false);

        $('input[name="cylindir_od"]').val('');
        $('input[name="cylindir_od"]').prop('readonly', false);

        $('input[name="axis_od"]').val('');
        $('input[name="axis_od"]').prop('readonly', false);

        $('input[name="base_od"]').val('');
        $('input[name="base_od"]').prop('readonly', false);

        $('input[name="addition_od"]').val('');
        $('input[name="addition_od"]').prop('readonly', false);
    }

    if ($('#ebalance_od').is(':checked')) {
        $('input[name="esphere_od"]').val('');
        $('input[name="esphere_od"]').prop('readonly', true);

        $('input[name="ecylindir_od"]').val('');
        $('input[name="ecylindir_od"]').prop('readonly', true);

        $('input[name="eaxis_od"]').val('');
        $('input[name="eaxis_od"]').prop('readonly', true);

        $('input[name="ebase_od"]').val('');
        $('input[name="ebase_od"]').prop('readonly', true);

        $('input[name="eaddition_od"]').val('');
        $('input[name="eaddition_od"]').prop('readonly', true);
    } else {
        $('input[name="esphere_od"]').val('');
        $('input[name="esphere_od"]').prop('readonly', false);

        $('input[name="ecylindir_od"]').val('');
        $('input[name="ecylindir_od"]').prop('readonly', false);

        $('input[name="eaxis_od"]').val('');
        $('input[name="eaxis_od"]').prop('readonly', false);

        $('input[name="ebase_od"]').val('');
        $('input[name="ebase_od"]').prop('readonly', false);

        $('input[name="eaddition_od"]').val('');
        $('input[name="eaddition_od"]').prop('readonly', false);
    }
}

function ebalanceOD() {
    if (! $('#ebalance_od').is(':checked')) {
        $('input[name="esphere_od"]').prop('readonly', false);
        $('input[name="ecylindir_od"]').prop('readonly', false);
        $('input[name="eaxis_od"]').prop('readonly', false);
        $('input[name="ebase_od"]').prop('readonly', false);
        $('input[name="eaddition_od"]').prop('readonly', false);
    }
}

// Balance for left eye
function balanceOS() {
    if ($('#balance_os').is(':checked')) {
        $('input[name="sphere_os"]').val('');
        $('input[name="sphere_os"]').prop('readonly', true);

        $('input[name="cylindir_os"]').val('');
        $('input[name="cylindir_os"]').prop('readonly', true);

        $('input[name="axis_os"]').val('');
        $('input[name="axis_os"]').prop('readonly', true);

        $('input[name="base_os"]').val('');
        $('input[name="base_os"]').prop('readonly', true);

        $('input[name="addition_os"]').val('');
        $('input[name="addition_os"]').prop('readonly', true);
    } else {
        $('input[name="sphere_os"]').val('');
        $('input[name="sphere_os"]').prop('readonly', false);

        $('input[name="cylindir_os"]').val('');
        $('input[name="cylindir_os"]').prop('readonly', false);

        $('input[name="axis_os"]').val('');
        $('input[name="axis_os"]').prop('readonly', false);

        $('input[name="base_os"]').val('');
        $('input[name="base_os"]').prop('readonly', false);

        $('input[name="addition_os"]').val('');
        $('input[name="addition_os"]').prop('readonly', false);
    }

    if ($('#ebalance_os').is(':checked')) {
        $('input[name="esphere_os"]').val('');
        $('input[name="esphere_os"]').prop('readonly', true);

        $('input[name="ecylindir_os"]').val('');
        $('input[name="ecylindir_os"]').prop('readonly', true);

        $('input[name="eaxis_os"]').val('');
        $('input[name="eaxis_os"]').prop('readonly', true);

        $('input[name="ebase_os"]').val('');
        $('input[name="ebase_os"]').prop('readonly', true);

        $('input[name="eaddition_os"]').val('');
        $('input[name="eaddition_os"]').prop('readonly', true);
    } else {
        $('input[name="esphere_os"]').val('');
        $('input[name="esphere_os"]').prop('readonly', false);

        $('input[name="ecylindir_os"]').val('');
        $('input[name="ecylindir_os"]').prop('readonly', false);

        $('input[name="eaxis_os"]').val('');
        $('input[name="eaxis_os"]').prop('readonly', false);

        $('input[name="ebase_os"]').val('');
        $('input[name="ebase_os"]').prop('readonly', false);

        $('input[name="eaddition_os"]').val('');
        $('input[name="eaddition_os"]').prop('readonly', false);
    }
}

function ebalanceOS() {
    if (! $('#ebalance_os').is(':checked')) {
        $('input[name="esphere_os"]').prop('readonly', false);
        $('input[name="ecylindir_os"]').prop('readonly', false);
        $('input[name="eaxis_os"]').prop('readonly', false);
        $('input[name="ebase_os"]').prop('readonly', false);
        $('input[name="eaddition_os"]').prop('readonly', false);
    }
}

// Loading materials on the table
$("#products").change(function(event) {
    id = $("#products").val();
    if(id != 0){
        addProduct(id);
        $("#products").val(0).change();
    }
});

$("#eproducts").change(function(event) {
    id = $("#eproducts").val();
    if(id != 0){
        eaddProduct(id);
        $("#eproducts").val(0).change();
    }
});

var cont = 0;
var product_ids = [];
var rowCont=[];

var econt = 0;
var eproduct_ids=[];
var erowCont=[];

/** 
 * Add material in table.
 * 
 * @param  int  variation_id
 * @param  int  warehouse_id
 * @return void
 */
function addProduct(variation_id, warehouse_id) {
    var route = "/lab-orders/addProduct/"+variation_id+"/"+warehouse_id;
    $.get(route, function(res) {
        variation_id = res.variation_id;
        if(res.sku == res.sub_sku){
            name = res.name_product;
        } else {
            name = ""+res.name_product+" "+res.name_variation+"";
        }
        count = parseInt(jQuery.inArray(variation_id, product_ids));
        if (count >= 0) {
            Swal.fire({
                title: LANG.product_already_added,
                icon: "error",
            });
        } else {
            product_ids.push(variation_id);
            rowCont.push(cont);
            location_id = $("#lab_location_id").val(); // $("#location_id").val();
            warehouse_id = $("#warehouse_id").val();
            qty_available = parseFloat(res.qty_available).toFixed(2);
            var row = '<tr class="selected" id="row'+cont+'" style="height: 10px">'+
                '<td><button id="bitem'+cont+'" type="button" class="btn btn-danger btn-xs" onclick="deleteProduct('+cont+', '+variation_id+');"><i class="fa fa-times"></i></button></td>'+
                '<td><input type="hidden" name="variation_id[]" value="'+variation_id+'">'+
                    '<input type="hidden" name="location_id[]" value="'+location_id+'">'+
                    '<input type="hidden" name="warehouse_id[]" value="'+warehouse_id+'">'+res.sub_sku+'</td>'+
                '<td>'+name+'</td>'+
                '<td><input type="text" name="qty_available[]" id="qty_available'+cont+'" value="'+qty_available+'" class="form-control form-control-sm" readonly></td>'+
                '<td><input type="number" name="quantity[]" id="quantity'+cont+'" class="form-control form-control-sm input_number" value="1" max="'+res.qty_available+'"></td></tr>';
            $("#list").append(row);
            cont++;
        }
    });
}

function eaddProduct(variation_id, warehouse_id) {
    var route = "/lab-orders/addProduct/"+variation_id+"/"+warehouse_id;
    $.get(route, function(res) {
        variation_id = res.variation_id;
        if(res.sku == res.sub_sku) {
            name = res.name_product;
        } else {
            name = ""+res.name_product+" "+res.name_variation+"";
        }
        count = parseInt(jQuery.inArray(variation_id, eproduct_ids));
        if (count >= 0) {
            Swal.fire
            ({
                title: LANG.product_already_added,
                icon: "error",
            });
        } else {
            eproduct_ids.push(variation_id);
            erowCont.push(econt);
            location_id = $("#elocation_id").val();
            warehouse_id = $("#ewarehouse_id").val();
            qty_available = parseFloat(res.qty_available).toFixed(2);
            var erow = '<tr class="selected" id="erow'+econt+'" style="height: 10px">'+
                '<td><button id="ebitem'+econt+'" type="button" class="btn btn-danger btn-xs" onclick="edeleteProduct('+econt+', '+variation_id+');"><i class="fa fa-times"></i></button></td>'+
                '<td><input type="hidden" name="item_id[]" value="0">'+
                    '<input type="hidden" name="evariation_id[]" value="'+variation_id+'">'+
                    '<input type="hidden" name="elocation_id[]" value="'+location_id+'">'+
                    '<input type="hidden" name="ewarehouse_id[]" id="ew_id" value="'+warehouse_id+'">'+res.sub_sku+'</td>'+
                '<td>'+name+'</td>'+
                '<td><input type="text" name="eqty_available[]" id="eqty_available'+econt+'" value="'+res.qty_available+'" class="form-control form-control-sm" readonly></td>'+
                '<td><input type="number" name="equantity[]" id="equantity'+econt+'" class="form-control form-control-sm input_number" value="1" max="'+res.qty_available+'"></td></tr>';
            $("#elist").append(erow);
            econt++;
        }
    });
}

/**
 * Remove material from table.
 */
 function deleteProduct(index, id) { 
    $("#row" + index).remove();
    product_ids.removeItem(id);
    if(product_ids.length == 0) {
        cont = 0;
        product_ids=[];
        rowCont=[];
    }
}

function edeleteProduct(index, id) { 
    $("#erow" + index).remove();
    eproduct_ids.removeItem(id);
    if(eproduct_ids.length == 0) {
        econt = 0;
        eproduct_ids=[];
        erowCont=[];
    }
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

$('select#lab_select_location_id').change(function(){
    lab_reset_pos_form();
});

function lab_reset_pos_form(){
    lab_set_location();
}

function lab_set_location(){
    if($('select#lab_select_location_id').length == 1){
        $('input#lab_location_id').val($('select#lab_select_location_id').val());
    }

    if($('input#lab_location_id').val()){
        $('input#lab_search_product').prop( "disabled", false ).focus();
    } else {
        $('input#lab_search_product').prop( "disabled", true );
    }
}

$("#btn-new-order").click(function(event) {
    $("#lab_search_product").prop('disabled', true);
    $("#lab_select_location_id").val('').change();
    //clear();

    // $("#search_product").prop('disabled', true);
    // $("#select_location_id").val('').change();
    // clear();
});

function search_optometrist(code, code_input, name_input, send_input, type) {
	if (code == '') {
		code_input.val('');
        $('#txt-optometrist').val('');
	} else {
		let route = '/patients/getEmployeeByCode/' + code;
		$.get(route, function(res) {
			if (res.success) {
				if (res.emp) {
					name_input.val(res.msg);
					if (type === 'user') {
						send_input.val(res.user_id);
					} else {
						send_input.val(res.emp_id);
					}
				} else {
					name_input.val('');
					code_input.val('');
					Swal.fire
					({
						title: ''+res.msg+'',
						icon: 'error',
						timer: 1000,
						showConfirmButton: false,
					});
				}
			} else {
				name_input.val('');
				code_input.val('');
				Swal.fire
				({
					title: ''+res.msg+'',
					icon: 'error',
					timer: 1000,
					showConfirmButton: false,
				});
			}
		});
	}
}

/**
 * Change lab order status.
 * 
 * @param  int  order_id
 * @param  int  status_id
 * @return void
 */
 function change_status(order_id, status_id) {
    $.ajax({
        url: '/lab-orders/change-status/' + order_id + '/' + status_id,
        type: 'GET',
        dataType: 'json',
        success: function(result) {
            if (result.success == true) {
                $('#lab_orders_table').DataTable().ajax.reload(null, false);

                Swal.fire({
                    title: result.msg,
                    icon: 'success',
                    timer: 1000,
                    showConfirmButton: false,
                });

            } else {
                Swal.fire({
                    title: result.msg,
                    icon: 'error',
                });
            }
        }
    });
}

// Block optemetrist field
function optometristBlock() {
    if ($('#is_prescription').is(':checked')) {
        $('#optometrist').val('0');
        $('#input-optometrist').prop('disabled', true);
        $('#input-optometrist').val('');
        $('#txt-optometrist').val('');
    } else {
        $('#optometrist').val('');
        $('#input-optometrist').prop('disabled', false);
        $('#input-optometrist').val('');
        $('#txt-optometrist').val('');
    }

    if ($('#eis_prescription').is(':checked')) {
        $('#eoptometrist').val('').change();
        $('#eoptometrist').prop('disabled', true);
    } else {
        $('#eoptometrist').prop('disabled', false);
        $('#eoptometrist').val('').change();
    }
}

$('select#eselect_warehouse_id').change(function(){
    ereset_pos_form();
});

function ereset_pos_form() {
    eset_location();
}

function eset_location() {
    if ($('select#eselect_warehouse_id').length == 1) {
        $('input#elocation_id').val($('select#eselect_warehouse_id').val());
    }

    if ($('input#elocation_id').val()){
        $('input#esearch_product').prop( "disabled", false ).focus();
    } else {
        $('input#esearch_product').prop( "disabled", true );
    }
}

// Edit lab order
$("#btn-edit-order").click(function(){
    $("#btn-edit-order").prop('disabled', true);
    $("#btn-close-modal-edit-order").prop('disabled', true);
    var data = $("#form-edit-order").serialize();
    var id = $("#order_id").val();
    route = "/lab-orders/"+id;
    $.ajax({
        url: route,
        type: 'PUT',            
        dataType: 'json',
        data: data,
        success:function(result) {
            if (result.success == true) {
                $("#btn-edit-order").prop('disabled', false);
                $("#btn-close-modal-edit-order").prop('disabled', false);
                $("#lab_orders_table").DataTable().ajax.reload(null, false);
                Swal.fire({
                    title: result.msg,
                    icon: "success",
                    timer: 1000,
                    showConfirmButton: false,
                });
                $("#modal-edit-order").modal('hide');
            } else {
                $("#btn-edit-order").prop('disabled', false);
                $("#btn-close-modal-edit-order").prop('disabled', false);
                Swal.fire({
                    title: result.msg,
                    icon: "error",
                });
            }
        },
        error: function(msj) {
            $("#btn-edit-order").prop('disabled', false);
            $("#btn-close-modal-edit-order").prop('disabled', false);
            var errormessages = "";
            $.each(msj.responseJSON.errors, function(i, field){
                errormessages+="<li>"+field+"</li>";
            });
            Swal.fire
            ({
                title: LANG.errors,
                icon: "error",
                html: "<ul>"+ errormessages+ "</ul>",
            });
        }
    });
});

function viewOrder(id) {
    var route = '/lab-orders/' + id;
    $.ajax({
        url: route,
        dataType: "html",
        success: function(result) {
            $('.view_lab_order_modal').html(result).modal('show');
        }
    });
}

function editOrder(id) {
    var route = "/lab-orders/"+id+"/edit";
    $("#esearch_product").prop('disabled', true);
    $("#eselect_location_id").val('').change();
    $('#eupload_document').fileinput('clear');

    // Select2
    $(this).find('.select2').select2();

    $.get(route, function(res) {
        $("#elist").empty();
        econt = 0;
        eproduct_ids=[];
        erowCont=[];

        $('#order_id').val(res.loid);

        $('#final_total').val('$ ' + res.final_total);

        $('#epatient_id').empty();
        let patient_option = new Option(res.patient_name, res.patient_id, true, true);
        $('#epatient_id').append(patient_option).trigger('change');

        $('input[name="eno_order"]').val(res.no_order);

        $('#ecustomer_id').empty();
        let customer_option = new Option(res.customer_name, res.customer_id, true, true);
        $('#ecustomer_id').append(customer_option).trigger('change');

        if (res.is_reparation == 1) {
            $('input[name="eis_reparation"]').prop('checked', true);
            $('.graduation_card_fields').hide();
        } else {
            $('input[name="eis_reparation"]').prop('checked', false);
            $('.graduation_card_fields').show();
        }

        if (res.is_prescription == 1) {
            $('input[name="eis_prescription"]').prop('checked', true);
            optometristBlock()
        } else {
            $('input[name="eis_prescription"]').prop('checked', false);
            $('#eoptometrist').prop('disabled', false);
        }

        $('input[name="esphere_od"]').val(res.sphere_od);
        $('input[name="esphere_os"]').val(res.sphere_os);

        $('input[name="ecylindir_od"]').val(res.cylindir_od);
        $('input[name="ecylindir_os"]').val(res.cylindir_os);

        $('input[name="eaxis_od"]').val(res.axis_od);
        $('input[name="eaxis_os"]').val(res.axis_os);

        $('input[name="ebase_od"]').val(res.base_od);
        $('input[name="ebase_os"]').val(res.base_os);

        $('input[name="eaddition_od"]').val(res.addition_od);
        $('input[name="eaddition_os"]').val(res.addition_os);

        $('input[name="ednsp_od"]').val(res.dnsp_od);
        $('input[name="ednsp_os"]').val(res.dnsp_os);

        $('input[name="edi"]').val(res.di);

        $('input[name="eao"]').val(res.ao);

        $('input[name="eap"]').val(res.ap);

        if (res.is_own_hoop == 1) {
            $('input[name="eis_own_hoop"]').prop('checked', true);
            $("#ehoop").hide();
            $('input[name="ehoop_name"]').show();
            $('input[name="ehoop_name"]').val(res.hoop_name);

        } else {
            $('input[name="eis_own_hoop"]').prop('checked', false);
            $('input[name="ehoop_name"]').hide();
            $("#ehoop").show();

            $('#ehoop').empty();
            let hoop_option = new Option(res.hoop_value, res.hoop_id, true, true);
            $('#ehoop').append(hoop_option).trigger('change');
        }
        
        $('input[name="esize"]').val(res.size);

        $('input[name="ecolor"]').val(res.color);

        $('.ht_rb').each(function() {
            if ($(this).val() == res.hoop_type) {
                $(this).prop('checked', true);
            }
        });

        // $('#eglass option[value=' + res.glass_value + ']').attr('selected', true);

        $("#div_glass_empty").hide();

        if (res.glass_value) {
            $("#div_glass").show();
            $("#eglass").val(res.glass_value).change();
        } else {
            $("#div_glass").hide();
        }

        if (res.glass_os_value) {
            $("#div_glass_os").show();
            $("#eglass_os").val(res.glass_os_value).change();
        } else {
            $("#div_glass_os").hide();
        }

        if (res.glass_od_value) {
            $("#div_glass_od").show();
            $("#eglass_od").val(res.glass_od_value).change();
        } else {
            $("#div_glass_od").hide();
        }

        if (!res.glass_value && !res.glass_os_value && !res.glass_od_value) {
            $("#div_glass_empty").show();
        }

        $('#ejob_type').val(res.job_type);

        if (res.check_ext_lab == 1) {
            $('input[name="echeck_ext_lab"]').prop('checked', true);
            $('#elab_extern_box').show();
        } else {
            $('input[name="echeck_ext_lab"]').prop('checked', false);
            $('#elab_extern_box').hide();
        }

        $("#eexternal_lab_id").val(res.external_lab_id).change();

        $('.ar_rb').each(function() {
            if ($(this).val() == res.ar) {
                $(this).prop('checked', true);
            } else {
                $(this).prop('checked', false);
            }
        });

        $('.status_rb').each(function() {
            if ($(this).val() == res.status_lab_order_id) {
                $(this).prop('checked', true);
            }
        });

        $('input[name="edelivery"]').val(res.delivery_value);

        $("#eemployee_id").val(res.employee_id).change();

        $("#eoptometrist").val(res.optometrist).change();

        $('#ereason').val(res.reason);

        if (res.transaction_id) {
            $("#elocation_lo").val(res.location_id).change();
            $("#einvoice_lo").val(res.correlative);
        } else {
            $("#elocation_lo").val(res.business_location_id).change();
        }

        if (res.balance_os == 1) {
            $('input[name="ebalance_os"]').prop('checked', true);
            balanceOS();
        } else {
            $('input[name="ebalance_os"]').prop('checked', false);
            ebalanceOS();
        }
        
        if (res.balance_od == 1) {
            $('input[name="ebalance_od"]').prop('checked', true);
            balanceOD();
        } else {
            $('input[name="ebalance_od"]').prop('checked', false);
            ebalanceOD();
        }

        $('#div_second_time').hide();

        if (res.show_fields) {
            $('#div_second_time').show();
            $('#eemployee_id').val(res.employee_id).change();
            $('#ereason').val(res.reason);
        }

        $('#btn-save-print-order').css('display', 'none');

        if (res.save_and_print == 1) {
            $('#btn-save-print-order').css('display', '');
        }

        $('#ewarehouse_id').val($('#default_warehouse').val()).change();
        $('#eselect_warehouse_id').val($('#default_warehouse').val()).change();

        $('input[name="select_location_id"]').val($('#default_warehouse').val()).change();

        $('input#esearch_product').prop('disabled', false).focus();

        $('#esearch_product').val('').change();

        var route = "/lab-orders/getProductsByOrder/"+id;
        $.get(route, function(res) {
            $(res).each(function(key, value){
                let name = '';

                if (value.sku == value.sub_sku) {
                    name = value.product_name;
                }
                else{
                    name = ""+value.product_name+" "+value.variation_name+"";
                }

                eproduct_ids.push(value.variation_id);
                erowCont.push(econt);
                var erow = '<tr class="selected" id="erow'+econt+'" style="height: 10px">'+
                    '<td><button id="ebitem'+econt+'" type="button" class="btn btn-danger btn-xs" onclick="edeleteProduct('+econt+', '+value.variation_id+');"><i class="fa fa-times"></i></button></td>'+
                    '<td><input type="hidden" name="item_id[]" value="'+value.id+'">'+
                        '<input type="hidden" name="evariation_id[]" value="'+value.variation_id+'">'+
                        '<input type="hidden" name="elocation_id[]" value="'+value.location_id+'">'+
                        '<input type="hidden" name="ewarehouse_id[]" id="ew_id" value="'+value.warehouse_id+'">'+value.sub_sku+'</td>'+
                    '<td>'+name+'</td>'+
                    '<td><input type="text" name="eqty_available[]" id="eqty_available'+econt+'" value="'+value.qty_available+'" class="form-control form-control-sm" readonly></td>'+
                    '<td><input type="number" name="equantity[]" id="equantity'+econt+'" class="form-control form-control-sm input_number" value="'+value.quantity+'" onchange="ecalculate()"></td></tr>';
                $("#elist").append(erow);
                econt++;
            });
        });
        $("#modal-edit-order").modal({backdrop: 'static'});
    });
}

function deleteOrder(id) {
    swal({
        title: LANG.sure,
        text: LANG.confirm_delete_lab_order,
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            /* var href = $(this).data('href');
            var data = $(this).serialize(); */

            var href = '/lab-orders/' + id;

            $.ajax({
                method: "DELETE",
                url: href,
                dataType: "json",
                //data: data,
                success: function(result) {
                    if (result.success === true) {
                        Swal.fire({
                            title: ""+result.msg+"",
                            icon: "success",
                        });
                        $('#lab_orders_table').DataTable().ajax.reload(null, false);
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
}

function clear() {
    $('input[name="patient_id"]').val('').change();

    $('input[name="no_order"]').val(''); // obtener nuevo

    $('input[name="customer_id"]').val('').change();

    $('input[name="is_reparation"]').prop('checked', false);

    $('.graduation_card_fields').show();

    $('input[name="balance_od"]').prop('checked', false);
    $('input[name="balance_os"]').prop('checked', false);

    $('input[name="sphere_od"]').val('');
    $('input[name="sphere_od"]').prop('readonly', false);
    $('input[name="sphere_os"]').val('');
    $('input[name="sphere_os"]').prop('readonly', false);

    $('input[name="cylindir_od"]').val('');
    $('input[name="cylindir_od"]').prop('readonly', false);
    $('input[name="cylindir_os"]').val('');
    $('input[name="cylindir_os"]').prop('readonly', false);

    $('input[name="axis_od"]').val('');
    $('input[name="axis_od"]').prop('readonly', false);
    $('input[name="axis_os"]').val('');
    $('input[name="axis_os"]').prop('readonly', false);

    $('input[name="base_od"]').val('');
    $('input[name="base_od"]').prop('readonly', false);
    $('input[name="base_os"]').val('');
    $('input[name="base_os"]').prop('readonly', false);

    $('input[name="addition_od"]').val('');
    $('input[name="addition_od"]').prop('readonly', false);
    $('input[name="addition_os"]').val('');
    $('input[name="addition_os"]').prop('readonly', false);

    $('input[name="dnsp_od"]').val('');
    $('input[name="dnsp_os"]').val('');

    $('input[name="di"]').val('');

    $('input[name="ao"]').val('');

    $('input[name="ap"]').val('');

    $('input[name="hoop"]').val('').change();

    $('input[name="is_own_hoop"]').prop('checked', false);
    
    $('input[name="size"]').val('');

    $('input[name="color"]').val('');

    $('input[name="hoop_type"]').prop('checked', false);

    $('input[name="glass"]').val('').change();

    $('input[name="job_type"]').val('');

    $('input[name="check_ext_lab"]').prop('checked', false);

    $('#lab_extern_box').hide();

    $('input[name="external_lab_id"]').val('').change();

    $('input[name="ar"]').prop('checked', false);

    $('input[name="status_lab_order_id"]').prop('checked', false);

    $('input[name="delivery"]').val('');

    $('input[name="select_location_id"]').val('').change();

    $('input[name="warehouse_id"]').val('').change();

    $('input[name="search_product"]').val('');

    //actual_date = '{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}';
    //$('input[name="delivery"]').val(actual_date);

    code = '{{ $code }}';
    $('input[name="no_order"]').val(code);

    $("#list").empty();

    cont = 0;
    product_ids=[];
    rowCont=[];
}

$('.second_time').on('click', function() {
    if($(this).is(':checked')) {
        $('#modal_second_time').modal('show');
    }
});

$('.re_stock').on('click', function() {
    if($(this).is(':checked')) {
        $('#modal_return_stock').modal('show');
    }
});

// Mark printed status
function markPrinted(id) {
    var route = "/lab-orders/markPrinted/" + id;
    $.get(route, function(res) {
        if (res.success) {
            $('#lab_orders_table').DataTable().ajax.reload(null, false);
        }
    });
}

/** Mark second status */
function markSecondTime(id) {
    var route = "/lab-orders/second-time/" + id;
    $.get(route, function(res) {
        if (res.success) {
            $('#lab_orders_table').DataTable().ajax.reload();
            editOrder(res.id);
        }
    });
}

// Validation dnsp and di
var _ednsp_od = $('input[name="ednsp_od"]');
var _ednsp_os = $('input[name="ednsp_os"]');
var _edi = $('input[name="edi"]');

_ednsp_od.on('change', function(e) {
    if(_ednsp_od.val() != '') {
        _edi.prop('readonly', true);
        _ednsp_os.prop('required', true);
    } else {
        if (_ednsp_os.val() == '') {
            _edi.prop('readonly', false);
            _ednsp_os.prop('required', false);
        }
    }
});

_ednsp_os.on('change', function(e) {
    if(_ednsp_os.val() != '') {
        _edi.prop('readonly', true);
        _ednsp_od.prop('required', true);
    } else {
        if (_ednsp_od.val() == '') {
            _edi.prop('readonly', false);
            _ednsp_od.prop('required', false);
        }
    }
});

_edi.on('change', function(e) {
    if(_edi.val() != '') {
        _ednsp_od.prop('readonly', true);
        _ednsp_os.prop('readonly', true);
    } else {
        _ednsp_od.prop('readonly', false);
        _ednsp_os.prop('readonly', false);
    }
});

// Print lab order
$(document).on('click', 'a.print-order', function(e) {
    e.preventDefault();
    var href = $(this).data('href');
    $.ajax({
        method: "GET",
        url: href,
        dataType: "json",
        success: function(result) {
            if (result.success == 1 && result.order.html_content != '') {
                $('#lab_orders_table').DataTable().ajax.reload(null, false);
                $('#order_section').html(result.order.html_content);
                __currency_convert_recursively($('#order_section'));
                setTimeout(function() { window.print(); }, 1000);
            } else {
                Swal.fire
                ({
                    title: ""+result.msg+"",
                    icon: "error",
                });
            }
        }
    });
});

// Include lab order in transfer sheet
$(document).on('click', 'a.transfer-order', function(e) {
    e.preventDefault();
    
    var href = $(this).data('href');

    $.ajax({
        method: 'GET',
        url: href,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                $('#lab_orders_table').DataTable().ajax.reload(null, false);

                Swal.fire({
                    title: result.msg,
                    icon: 'success',
                    timer: 1000,
                    showConfirmButton: false,
                });

            } else {
                Swal.fire({
                    title: result.msg,
                    icon: 'error',
                });
            }
        }
    });
});

// Change status lab order and copy lab order
$(document).on('click', 'a.copy-order', function(e) {
    e.preventDefault();
    
    var href = $(this).data('href');

    $.ajax({
        method: 'GET',
        url: href,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                $('#lab_orders_table').DataTable().ajax.reload(null, false);
                editOrder(result.id);

            } else {
                Swal.fire({
                    title: result.msg,
                    icon: 'error',
                });
            }
        }
    });
});

// Change status lab order and edit lab order
$(document).on('click', 'a.edit-order', function(e) {
    e.preventDefault();
    
    var href = $(this).data('href');

    $.ajax({
        method: 'GET',
        url: href,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                $('#lab_orders_table').DataTable().ajax.reload(null, false);
                editOrder(result.id);

            } else {
                Swal.fire({
                    title: result.msg,
                    icon: 'error',
                });
            }
        }
    });
});

function showgraduationbox() {
    if ($("#chkhas_glasses").is(":checked")) {
        $("#graduation_box").show();
        $("#glasses_graduation").show();
        $("#glasses_graduation").val('');
        $("#glasses_graduation").prop('required', true);
    } else {
        $("#glasses_graduation").val('');
        $("#glasses_graduation").prop('required', false);
        $("#glasses_graduation").hide();
        $("#graduation_box").hide();
    }
}

// On click of edit-lab-order link
$(document).on('click', 'a.edit-lab-order', function (e) {
    e.preventDefault();
    let id = $(this).data('lab-order-id');
    editOrder(id);
});

// On click of edit-lab-order link
$(document).on('click', 'a.view-lab-order', function (e) {
    e.preventDefault();
    let id = $(this).data('lab-order-id');
    viewOrder(id);
});
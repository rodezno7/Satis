$(document).ready(function(){
    /** fix select2 bug */
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

    $("div.payment_commitments_modal").on("shown.bs.modal", function(){
        var modal = $(this);
        //Date picker
        modal.find('input.date').datetimepicker({
            format: moment_date_format
        });

        /** get suppliers */
        modal.find("select#supplier").select2({
            ajax: {
                type: "get",
                url: "/contacts/suppliers",
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

        //search debt purchases
        modal.find("select#add_purchase").select2({
            ajax: {
                type: "get",
                url: "/purchases/get-debt-purchases",
                dataType: "json",
                data: function(params){
                    return {
                        q: params.term,
                        supplier_id: $("select#supplier").val(),
                        location_id: $("select#location_id").val()
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

        modal.find("select#add_purchase").on("change", function(){
            add_payment_commitment_row("automatic", $(this).val());
        });

        modal.find("select#type").on("change", function(){
            var type = $(this).val();
            var manual = modal.find("div.manual");
            var automatic = modal.find("div.automatic");

            if(type == "manual"){
                manual.show();
                automatic.hide()
            } else {
                manual.hide();
                automatic.show();
            }
        });

        modal.find("button#add_manual").on("click", function(e){
            e.preventDefault();

            add_payment_commitment_row("manual", 0);
        });  
    });

    /** calculate total for payment commitment table */
    $(document).on("change", "input.row_amount", function(){
        calc_totals();
    });

    /** delete payment commitment table row */
    $(document).on("click", "button.btn-delete-row", function(){
        var row = $(this).closest("tr");

        swal({
            title: LANG.sure,
            text: LANG.wont_be_able_revert,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                row.remove();
                calc_totals();
            }
        });
    });

    /** payment commitments datatable */
    var payment_commitments = $('table#payment_commitments_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/payment-commitments',
        order: [1, 'desc'],
        columns: [
            { data: 'date', name: 'date' },
            { data: 'reference', name: 'reference' },
            { data: 'type', name: 'type' },
            { data: 'supplier_name', name: 'c.name' },
            { data: 'location_name', name: 'bl.name' },
            { data: 'total', name: 'total' },
            { data: 'action', name: 'action' }
        ],
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('table#payment_commitments_table'));
        }
    });

    /** create payment commitments */
    $(document).on('click', 'a.add-payment-commitment', function(e){
        e.preventDefault();

        $('div.payment_commitments_modal').load($(this).attr('href'), function(){
            let modal = $(this);
            modal.modal('show');

            $('form#payment_commitment_add_form').off('submit').on('submit', function(e){
                e.preventDefault();
                let btn = $(this).find('button[type="submit"]');
                btn.attr('disabled', true);

                $.ajax({
                    method: "POST",
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(data){
                        if(data.success){
                            modal.modal('hide');
                            payment_commitments.ajax.reload();
                            toastr.success(data.msg);

                            var tab = window.open('/payment-commitments/print/'+ data.transaction_id, '_blank');
		                    if(tab){ tab.focus(); }
                        } else{
                            btn.attr('disabled', false);
                            toastr.error(data.msg);
                        }
                    }
                });
            });
        });
    });

    /** edit payment commitment */
    $(document).on("click", "a.edit_payment_commitment", function(e){
        e.preventDefault();

        $('div.payment_commitments_modal').load($(this).attr('href'), function(){
            let modal = $(this);
            table = modal.find("table#payment_commitments");
            modal.modal('show');
            
            __currency_convert_recursively(table);

            $('form#payment_commitment_edit_form').off('submit').on('submit', function(e){
                e.preventDefault();
                let btn = $(this).find('button[type="submit"]');
                btn.attr('disabled', true);

                $.ajax({
                    method: "POST",
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(data){
                        if(data.success){
                            modal.modal('hide');
                            payment_commitments.ajax.reload();
                            toastr.success(data.msg);
                        } else{
                            btn.attr('disabled', false);
                            toastr.error(data.msg);
                        }
                    }
                });
            });
        }); 
    });

    /** delete payment_commitment */
    $(document).on("click", "a.delete_payment_commitment", function(e){
        e.preventDefault();
        
        swal({
            title: LANG.sure,
            text: LANG.wont_be_able_revert,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    method: "DELETE",
                    url: $(this).attr('href'),
                    dataType: "json",
                    data: $(this).serialize(),
                    success: function(result) {
                        if (result.success) {
                            payment_commitments.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            }
        });
    });

    /** annul payment_commitment */
    $(document).on("click", "a.annul_payment_commitment", function(e){
        e.preventDefault();
        
        swal({
            title: LANG.sure,
            text: LANG.wont_be_able_revert,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    method: "GET",
                    url: $(this).attr('href'),
                    dataType: "json",
                    data: $(this).serialize(),
                    success: function(result) {
                        if (result.success) {
                            payment_commitments.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            }
        });
    });
});

/** add a new row on payment commitment table */
function add_payment_commitment_row(type, id){
    if(id == null) return false;

    var table = $(document).find("table#payment_commitments tbody");
    
    $.ajax({
        type: "GET",
        url: "/payment-commitments/add-payment-commitment-row",
        data: { transaction_id: id, type: type },
        dataType: "html",
        success: function(data){
            table.append(data);
            __currency_convert_recursively(table);
            calc_totals();
        }
    });
}

/** calculate total on payment commitment table */
function calc_totals(){
    modal = $(document).find("div.payment_commitments_modal");
    var table = modal.find("table#payment_commitments tbody tr");
    var total_amount = modal.find("input#total_amount");
    var total_amount_text = modal.find("span#total_amount_text");
    var sum = 0;

    $.each(table, function(i, row){
        sum += __read_number($(this).find("input.row_amount"));

        var inputs = $(this).find("input");
        update_inputs(i, inputs);

    });

    __write_number(total_amount, sum, false, 4);
    total_amount_text.html(__currency_trans_from_en(sum, true));

    /** update name index for payment commitment table */
    function update_inputs(i, inputs){
        var name = 'N/A';
        inputs.each(function(){
            name = $(this).data('name') == undefined ? 'N/A' : $(this).data('name') ;
            $(this).attr("name", "payment_commitment_lines["+ i +"]["+ name +"]");
        });
    }
}
$(document).ready(function(){
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

    var cost_centers_table =
        $("table#cost_centers_table").DataTable({
            processing: true,
            serverSide: true,
            ajax: '/cost_centers'
        });

    /** Add Cost Center */
    $(document).on("click", "button#btn_add_cost_center", function(){
        $("div.cost_center_modal").load($(this).data("href"), function(){
            $(this).modal("show");

            $(document).on("submit", "form#add_cost_center_form", function(e){
                e.preventDefault();
                
                $.ajax({
                    method: "POST",
                    url: $(this).attr("action"),
                    dataType: "json",
                    data: $(this).serialize(),
                    success: function(data){
                        if (data.success == true){
                            $("div.cost_center_modal").modal("hide");
                            toastr.success(data.msg);
                            cost_centers_table.ajax.reload();
                        } else {
                            toastr.error(data.msg);
                        }
                    }
                })
            });
        });
    });

    /** Edit Cost Center */
    $(document).on("click", "a.btn_edit_cost_center", function(e){
        e.preventDefault();

        $("div.cost_center_modal").load($(this).attr("href"), function(){
            $(this).modal("show");

            $(document).on("submit", "form#edit_cost_center_form", function(o){
                o.preventDefault();
                
                $.ajax({
                    method: "POST",
                    url: $(this).attr("action"),
                    dataType: "json",
                    data: $(this).serialize(),
                    success: function(data){
                        if (data.success == true){
                            $("div.cost_center_modal").modal("hide");
                            toastr.success(data.msg);
                            cost_centers_table.ajax.reload();
                        } else {
                            toastr.error(data.msg);
                        }
                    }
                })
            });
        });
    });

    /** Add main accounts */
    $(document).on("click", "a.add_main_accounts", function(e){
        e.preventDefault();

        $("div.cost_center_accounts_modal").load($(this).attr("href"), function(){
            $(this).modal("show");

            $(document).off("submit").on("submit", "form#add_main_accounts_form", function(o){
                o.preventDefault();
                $(this).find('button[type="submit"]').attr('disabled', true);

                $.ajax({
                    method: "POST",
                    url: $(this).attr("action"),
                    dataType: "json",
                    data: $(this).serialize(),
                    success: function(data){
                        if (data.success == true){
                            $("div.cost_center_accounts_modal").modal("hide");
                            toastr.success(data.msg);
                        } else {
                            toastr.error(data.msg);
                        }
                    }
                })
            });
        });
    });

    /** Add operation accounts */
    $(document).on("click", "a.add_operation_accounts", function(e){
        e.preventDefault();

        $("div.cost_center_accounts_modal").load($(this).attr("href"), function(){
            $(this).modal("show");

            $(document).off("submit").on("submit", "form#create_operation_accounts_form", function(o){
                o.preventDefault();
                $(this).find('button[type="submit"]').attr('disabled', true);

                $.ajax({
                    method: "POST",
                    url: $(this).attr("action"),
                    dataType: "json",
                    data: $(this).serialize(),
                    success: function(data){
                        if (data.success == true){
                            $("div.cost_center_accounts_modal").modal("hide");
                            toastr.success(data.msg);
                        } else {
                            toastr.error(data.msg);
                        }
                    }
                })
            });
        });
    });

    /** Select main and operation accounts */
    $("div.cost_center_accounts_modal").on("shown.bs.modal", function(){
        /** Main accounts */
        $("select#expense_account").select2({
            ajax: {
                type: "post",
                url: "/catalogue/get_accounts_for_select2",
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

        /** Opetation accounts */
        var expenses = $(this).find("div.expenses");
        var expense_main_account = expenses ? expenses.find("input.main_account").val() : null;
        $("div.expenses select#sell_expense_account, div.expenses select#admin_expense_account, div.expenses select#finantial_expense_account, div.expenses select#non_dedu_expense_account").select2({
            ajax: {
                type: "post",
                url: "/catalogue/get_accounts_for_select2",
                dataType: "json",
                data: function(params){
                    return {
                        q: params.term,
                        main_account: expense_main_account
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
});
$(document).ready(function () {
    $.fn.modal.Constructor.prototype.enforceFocus = function () { };

    /** Payment conditions */
    $(document).on("change", "#payment_condition", function () {
        var row = $(this).closest(".row");
        toggle_payment(row);
    });

    $("div.contact_modal").on("shown.bs.modal", function () {
        let main_account = $(this).find("input#main_account").val();
        main_account = main_account ? main_account : null;

        $('#country_id').select2();
        $('#state_id').select2();
        $('#city_id').select2();
        $('#payment_terms_id').select2();
        $('#business_type').select2();
        /** select supplier and provider account */
        $("select.select_account").select2({
            ajax: {
                type: "post",
                url: "/catalogue/get_accounts_for_select2",
                dataType: "json",
                data: function (params) {
                    return {
                        q: params.term,
                        main_account: main_account
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

        /** validate is_supplier and is_provider */
        $("input.is_supplier").on("change ifChanged", function () {
            let supplier_account = $("div.supplier_account");

            if ($(this).prop("checked")) {
                supplier_account.show();
            } else {
                supplier_account.hide();
                supplier_account.find("select#supplier_catalogue_id").val(null).change();
            }
        });

        $("input.is_provider").on("change ifChanged", function () {
            let provider_account = $("div.provider_account");

            if ($(this).prop("checked")) {
                provider_account.show();
            } else {
                provider_account.hide();
                provider_account.find("select#provider_catalogue_id").val(null).change();
            }
        });
    });
});

/* To select tax_group in case that business type is a large one */
$(document).on("change", "#business_type", function () {
    var b_type_select = $(this);
    if (b_type_select.val() == 4) {
        $("#tax_group_id option:contains(PERCEPCIÓN)").prop('selected', true);
        $("#tax_group_id").prop("style", "pointer-events: none;");

        $("#is_exempt option:contains(No)").prop('selected', true)
        $("#is_exempt").prop("style", "pointer-events: none;");

    } else {
        $("#is_exempt").prop("style", "pointer-events: auto;");
        $("#tax_group_id").prop("style", "pointer-events: auto;");
    }
});

$(document).on("change", "#is_exempt", function () {
    var b_type_select = $(this);
    if (b_type_select.val() == 1) {
        $("#tax_group_id option:first-child").prop('selected', true)

        $("#tax_group_id").prop("style", "pointer-events: none;");

    } else {
        $("#tax_group_id option:first").prop('selected', true)

        $("#tax_group_id").prop("style", "pointer-events: auto;");
    }
});

function toggle_payment(row) {
    var payment_condition = row.find("#payment_condition option:selected").val();

    if (payment_condition == "cash") {
        row.find(".payment").hide();

    } else if (payment_condition == "credit") {
        row.find(".payment").show();
    }
}

function getStatesByCountry(id) {
    $("#state_id").empty();
    var route = "/states/getStatesByCountry/" + id;
    $.get(route, function (res) {
        $("#state_id").append('<option value="0" disabled selected>' + LANG.select_please + '</option>');

        $(res).each(function (key, value) {
            $("#state_id").append('<option value="' + value.id + '">' + value.name + '</option>');
        });
    });
}

function getCitiesByState(id) {
    $("#city_id").empty();
    var route = "/cities/getCitiesByState/" + id;
    $.get(route, function (res) {
        $("#city_id").append('<option value="0" disabled selected>' + LANG.select_please + '</option>');
        $(res).each(function (key, value) {
            $("#city_id").append('<option value="' + value.id + '">' + value.name + '</option>');
        });
    });
}

$(document).on('change', '#country_id', function (e) {
    id = $("#country_id").val();
    if (id) {
        getStatesByCountry(id);
    } else {
        $("#state_id").empty();
        $("#state_id").append('<option value="0" disabled selected>' + LANG.select_please + '</option > ');
        $("#city_id").empty();
        $("#city_id").append('<option value="0" disabled selected>' + LANG.select_please + '</option>');
    }
});

$(document).on('change', '#state_id', function (e) {
    id = $("#state_id").val();
    if (id) {
        getCitiesByState(id);
    } else {
        $("#city_id").empty();
        $("#city_id").append('<option value="0" disabled selected>' + LANG.select_please + '</option>');
    }
});
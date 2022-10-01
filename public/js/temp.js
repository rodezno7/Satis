function editBankTransaction(id) {
    $('#content2').show();
    $("#btn-edit-transaction").prop('disabled', true);
    $("#btn-close-modal-edit-transaction").prop('disabled', true);
    var route = "/bank-transactions/"+id;
    $.get(route, function(res){
        $("#select-type-etransaction").val(res.type).change();
        $("#select-bank-account-id-etransaction").val(res.bank_account_id).change();
        $("#txt-bank-account-id-etransaction").val(res.bank_account_id);
        $("#txt-date-etransaction").val(res.date);
        $("#txt-description-etransaction").val(res.description);
        $("#txt-epayment-to").val(res.headline);
        $("#txt-reference-etransaction").val(res.reference);
        $("#period_id2").val(res.period_value);
        $("#transaction_id").val(res.id);
        $("#etype_entrie_id").val(res.type_entrie_id).change();
        $("#ebusiness_location_id").val(res.business_location_id).change();

        type = res.type;
        if(type == "consignment"){
            $("#div_econtact").hide();
            $("#div_eaccounts").show();
            $("#label-etype").text("{{__('accounting.account_to_credit')}}");
        }
        if(type == "check"){
            $("#div_econtact").show();
            $("#div_eaccounts").show();
            $("#label-etype").text("{{__('accounting.account_to_debit')}}");
        }
        if(type == "send_transfer"){
            $("#div_econtact").hide();
            $("#div_eaccounts").show();
            $("#label-etype").text("{{__('accounting.account_to_debit')}}");
        }
        if(type == "receive_transfer"){
            $("#div_econtact").hide();
            $("#div_eaccounts").show();
            $("#label-etype").text("{{__('accounting.account_to_credit')}}");
        }

        entrie_id = res.accounting_entrie_id;
        $("#pie2").show();
        var route2 = "/entries/getTotalEntrie/"+entrie_id;
        $.get(route2, function(res2){
            $('#total_debe2').val(res2.debe);
            $('#total_haber2').val(res2.haber);
        });
        var route3 = "/entries/getEntrieDetailsDebe/"+entrie_id;
        $.get(route3, function(res3){
            $(res3).each(function(key,value) {
                id_c2 = value.account_id;
                code2 = value.code;
                name2 = value.name;
                existe2 = parseInt(jQuery.inArray(id_c2, id_a2));
                id_a2.push(id_c2);
                valor2.push(cont2);
                $("#vacio2").remove();
                if(value.debit > 0 || value.debit < 0)
                {
                    var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar2('+cont2+', '+id_c2+');" disabled>X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="'+value.debit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control-sm" step="0.01" onchange="deshabilitar12('+cont2+')"></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" value="0" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control-sm" onchange="deshabilitar22('+cont2+')" readonly></td></tr>'
                }
                else
                {
                    var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar2('+cont2+', '+id_c2+');" disabled>X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="0" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control-sm" step="0.01" onchange="deshabilitar12('+cont2+')" readonly></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" value="'+value.credit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control-sm" onchange="deshabilitar22('+cont+')"></td></tr>'
                }
                $("#lista2").append(fila2);
                cont2++;
                var route4 = "/entries/getEntrieDetailsHaber/"+entrie_id;
                $.get(route4, function(res4){
                    $(res4).each(function(key,value) {
                        id_c2 = value.account_id;
                        code2 = value.code;
                        name2 = value.name;
                        existe2 = parseInt(jQuery.inArray(id_c2, id_a2));
                        id_a2.push(id_c2);
                        valor2.push(cont2);
                        if((value.debe > 0) || (value.debe < 0)){

                        }
                        else{
                            //
                        }
                        

                        $("#lista2").append(fila2);
                        $("#pie2").show();
                        cont2++;
                    });
                    $('html, body').animate( {scrollTop: $(document).height() }, 1000);
                    $("#btn-edit-transaction").prop('disabled', false);
                    $("#btn-close-modal-edit-transaction").prop('disabled', false);
                    $.each(valor2, function(value2){
                        $("#bitem2"+value2+"").prop("disabled", false);
                    });
                    $('#content2').hide();

                });
            });

        });
        $("#div-list-transaction").hide();
        $("#div-edit-transaction").show();
        $("#flag-edit").val(1);
    });
}



///
function editBankTransaction(id) {
    $('#content2').show();
    $("#btn-edit-transaction").prop('disabled', true);
    $("#btn-close-modal-edit-transaction").prop('disabled', true);
    var route = "/bank-transactions/"+id;
    $.get(route, function(res){
        $("#select-type-etransaction").val(res.type).change();
        $("#select-bank-account-id-etransaction").val(res.bank_account_id).change();
        $("#txt-bank-account-id-etransaction").val(res.bank_account_id);
        $("#txt-date-etransaction").val(res.date);
        $("#txt-description-etransaction").val(res.description);
        $("#txt-epayment-to").val(res.headline);
        $("#txt-reference-etransaction").val(res.reference);
        $("#period_id2").val(res.period_value);
        $("#transaction_id").val(res.id);
        $("#etype_entrie_id").val(res.type_entrie_id).change();
        $("#ebusiness_location_id").val(res.business_location_id).change();

        type = res.type;
        if(type == "consignment"){
            $("#div_econtact").hide();
            $("#div_eaccounts").show();
            $("#label-etype").text("{{__('accounting.account_to_credit')}}");
        }
        if(type == "check"){
            $("#div_econtact").show();
            $("#div_eaccounts").show();
            $("#label-etype").text("{{__('accounting.account_to_debit')}}");
        }
        if(type == "send_transfer"){
            $("#div_econtact").hide();
            $("#div_eaccounts").show();
            $("#label-etype").text("{{__('accounting.account_to_debit')}}");
        }
        if(type == "receive_transfer"){
            $("#div_econtact").hide();
            $("#div_eaccounts").show();
            $("#label-etype").text("{{__('accounting.account_to_credit')}}");
        }

        entrie_id = res.accounting_entrie_id;
        $("#pie2").show();
        var route2 = "/entries/getTotalEntrie/"+entrie_id;
        $.get(route2, function(res2){
            $('#total_debe2').val(res2.debe);
            $('#total_haber2').val(res2.haber);
        });
        var route3 = "/entries/getEntrieDetailsDebe/"+entrie_id;
        $.get(route3, function(res3){
            $(res3).each(function(key,value) {
                id_c2 = value.account_id;
                code2 = value.code;
                name2 = value.name;
                existe2 = parseInt(jQuery.inArray(id_c2, id_a2));
                id_a2.push(id_c2);
                valor2.push(cont2);
                $("#vacio2").remove();
                if(value.debit > 0 || value.debit < 0)
                {
                    var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar2('+cont2+', '+id_c2+');" disabled>X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="'+value.debit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control-sm" step="0.01" onchange="deshabilitar12('+cont2+')"></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" value="0" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control-sm" onchange="deshabilitar22('+cont2+')" readonly></td></tr>';
                }
                else
                {
                    var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar2('+cont2+', '+id_c2+');" disabled>X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="0" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control-sm" step="0.01" onchange="deshabilitar12('+cont2+')" readonly></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" value="'+value.credit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control-sm" onchange="deshabilitar22('+cont+')"></td></tr>';
                }
                $("#lista2").append(fila2);
                cont2++;
                var route4 = "/entries/getEntrieDetailsHaber/"+entrie_id;
                $.get(route4, function(res4){
                    $(res4).each(function(key,value)
                    {
                        id_c2 = value.account_id;
                        code2 = value.code;
                        name2 = value.name;
                        existe2 = parseInt(jQuery.inArray(id_c2, id_a2));
                        id_a2.push(id_c2);
                        valor2.push(cont2);
                        if(value.debe > 0 || value.debe < 0)
                        {
                            var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar2('+cont2+', '+id_c2+');" disabled>X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="'+value.debit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control-sm" step="0.01" onchange="deshabilitar12('+cont2+')"></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" value="0" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control-sm" onchange="deshabilitar22('+cont2+')" readonly></td></tr>'
                        }
                        else
                        {
                            var fila2='<tr class="selected" id="fila2'+cont2+'" style="height: 10px"><td style="width: 5%"><button id="bitem2'+cont2+'" type="button" class="btn btn-warning btn-sm" onclick="eliminar2('+cont2+', '+id_c2+');" disabled>X</button></td><td style="width: 15%"><input type="hidden" name="account_id2[]" value="'+id_c2+'">'+code2+'</td><td style="width: 50%">'+name2+'</td><td style="width: 15%"><input type="text" name="debe2[]" id="debe2'+cont2+'" value="0" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control-sm" step="0.01" onchange="deshabilitar12('+cont2+')" readonly></td><td style="width: 15%"><input type="text" name="haber2[]" id="haber2'+cont2+'" value="'+value.credit+'" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control-sm" onchange="deshabilitar22('+cont2+')"></td></tr>'

                        }
                        $("#lista2").append(fila2);
                        $("#pie2").show()
                        cont2++;
                    });
                    $('html, body').animate( {scrollTop: $(document).height() }, 1000);
                    $("#btn-edit-transaction").prop('disabled', false);
                    $("#btn-close-modal-edit-transaction").prop('disabled', false);
                    $.each(valor2, function(value2){
                        $("#bitem2"+value2+"").prop("disabled", false);
                    });
                    $('#content2').hide();
                });
            });
});
$("#div-list-transaction").hide();
$("#div-edit-transaction").show();
$("#flag-edit").val(1);
});
}

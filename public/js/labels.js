$(document).ready( function(){

    //Add products
    if($( "#search_product_for_label" ).length > 0){
        $( "#search_product_for_label" ).autocomplete({
            source: "/purchases/get_products?check_enable_stock=false",
            minLength: 2,
            response: function(event,ui) {
                if (ui.content.length == 1)
                {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                } else if (ui.content.length == 0)
                {
                    swal(LANG.no_products_found)
                }
            },
            select: function( event, ui ) {
                $(this).val(null);
                get_label_product_row( ui.item.product_id, ui.item.variation_id );
            }
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" ).append( "<div>" + item.text + "</div>" ).appendTo( ul );
        };
    }

    $('input#is_show_price').change(function(){
        if($(this).is(":checked")){
            $('div#price_type_div').show();
        } else {
            $('div#price_type_div').hide();
        }
    });

    $('button#labels_preview').click(function(){
        if($('form#preview_setting_form table#product_table tbody tr').length > 0){
            $.ajax({
                method: "POST",
                url: '/labels/preview',
                dataType: "json",
                data: $('form#preview_setting_form').serialize(),
                success: function(result){
                    if(result.success){
                        $('div.display_label_div').removeClass('hide');
                        $('div#preview_box').html(result.html);
                        __currency_convert_recursively($('div#preview_box'));
                    } else {
                        toastr.error(result.msg);
                    }
                    
                }
            });
        } else {
            swal(LANG.label_no_product_error).then((value) => {
                $('#search_product_for_label').focus();
            });
        }
        
    });

    $(document).on('click', 'button#print_label', function(){
        window.print();
    });

    // On change of chk-logo checkbox
    $(document).on('change', '.chk-logo', function () {
        if ($('#chk-business-logo').is(':checked') || $('#chk-brand-logo').is(':checked')) {
            fill_barcode_setting(1);
        } else {
            fill_barcode_setting(0);
        }
    });
});

/**
 * Fill select from barcode_setting.
 * 
 * @param  int  has_logo 
 * @return void
 */
function fill_barcode_setting(has_logo) {
    let barcode_setting = $('select#barcode_setting');

    $.ajax({
        method: 'get',
        url: '/labels/show/barcode-setting/' + has_logo,
        dataType: 'json',
        success: function (data) {
            if (data) {
                $('#barcode_setting').empty();

                data.forEach(d => {
                    let option = new Option(d.name, d.id, false, false);
                    barcode_setting.append(option);
                });

            } else {
                $('#barcode_setting').empty();
            }
        }
    });
}

function get_label_product_row( product_id, variation_id){

    if(product_id ){

        var row_count = $('table#product_table tbody tr').length;
        $.ajax({
            method: "GET",
            url: '/labels/add-product-row',
            dataType: "html",
            data: { 'product_id' : product_id, 'row_count': row_count, 'variation_id': variation_id},
            success: function(result){
                $('table#product_table tbody').append(result);
            }
        });
    }
}
@extends('layouts.app')
@section('title', __('sale.products'))
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('sale.products')
        <small>@lang('lang_v1.manage_products')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    {{-- Number of decimal places to store and use in calculations --}}
    <input type="hidden" id="price_precision" value="{{ config('app.price_precision') }}">

   <div class="boxform_u box-solid_u">
    <div class="box-header">
        <span  id="header_index">
            <h3 class="box-title">@lang('lang_v1.all_products')</h3>
        </span>
        <span  id="header_add" style="display: none;">
            <h3 class="box-title">@lang('product.add_new_product')</h3>
        </span>
        @can('product.create')
        <div class="box-tools"  id="btn_add">
            <button type="button" id="btnAdd" class="btn btn-primary">@lang('messages.add')</button>
        </div>
        <div class="box-tools" id="btn_cancel" style="display: none;">
            <button type="button" id="btnUndo" class="btn btn-danger">@lang('messages.cancel')</button>
        </div>
        @endcan
    </div>
    <div class="box-body">
        <div id="div_index">
            @can('product.view')
            <div class="table-responsive">
             <table class="table table-bordered table-striped ajax_view table-text-center" id="product_table" width="100%">
                <thead>
                   <tr id="div_datatable">
                    <th>@lang('sale.product')</th>
                    <th>@lang('product.sku')</th>
                    <th>@lang('product.clasification')</th>
                    <th>@lang('product.category')</th>
                    <th>@lang('product.status')</th>
                    <th>@lang('product.brand')</th>
                    <th>@lang('messages.actions')</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcan
</div>
<div id="div_add" style="display: none;">
    @include('product.create')
</div>
</div>
</div>


<input type="hidden" id="is_rack_enabled" value="{{$rack_enabled}}">

<div class="modal fade product_modal" tabindex="-1" role="dialog" 
aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade" id="view_product_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade" id="opening_stock_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade" id="modalSupplier" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade" id="modalKit" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
{{-- Modal history purchases --}}
<div class="modal fade" id="modal_history_purchase" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade" id="product_accounts_modal" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</section>
<!-- /.content -->
@endsection
@section('javascript')
@php $asset_v = env('APP_VERSION'); @endphp
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">

function showWarranty() {
        if ($("#has_warranty").is(":checked")) {
            $('#has_warranty').val('1');
            $('#hasW').show();
        } else {
            $('#hasW').hide();
            $("#warranty").val('');
            $('#has_warranty').val('0');
        }
    }

    var cont = 0;
    var supplier_ids = [];
$(document).ready( function(){
    showWarranty();
    var product_table = $('#product_table').DataTable({
        processing: true,
        serverSide: true,
        deferRender: true,
        ajax: '/products/getProductsData',
        columnDefs: [
            { "searchable": false, "targets": [2, 3, 4, 5, 6] }
        ],
        columns: [
        { data: 'product', name: 'product.name'  },
        { data: 'sku', name: 'product.sku'},
        { data: null, render: function(data){
            clasification = '';
            if (data.clasification == 'product') {
                clasification = '@lang("product.clasification_product")';
            }
            if (data.clasification == 'service') {
                clasification = '@lang("product.clasification_service")';
            }
            if (data.clasification == 'kits') {
                clasification = '@lang("product.clasification_kits")';
            }
            return clasification;
        }, orderable: false, searchable: false},
        { data: 'category', name: 'c1.name'},
        { data: 'status', name: 'status'},
        { data: 'brand', name: 'brands.name'},
        { data: null, render: function(data) {

            html = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> @lang("messages.actions") <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu">';

            if(data.clasification == 'product')
            {
                html += '<li><a href="labels/show?product_id='+data.id+'" data-toggle="tooltip" title="Print Barcode/Label"><i class="fa fa-barcode"></i>@lang('barcode.labels')</a></li>';
            }

            @can('product.view')

            html += '<li><a href="/products/view/'+data.id+'" class="view-product"><i class="fa fa-eye"></i>@lang("messages.view")</a></li>';

            if (data.clasification == "product") {
                html += '<li><a href="/products/viewSupplier/'+data.id+'" class="view-supplier" ><i class="fa fa-eye"></i>@lang("product.view_suppliers")</a></li>';
            }

            if (data.clasification == "kits") {
                html += '<li><a href="/products/viewKit/'+data.id+'" class="view-kit" ><i class="fa fa-eye"></i>@lang("product.view_kit")</a></li>';
            }

            html += '<li><a href="/products/purchase_history/'+data.id+'" class="view_history_purchase"><i class="fa fa-history"></i>@lang("Historial de compra")</a></li>';
            @endcan


            @can('product.update')

            html += '<li><a href="/products/'+data.id+'/edit"><i class="glyphicon glyphicon-edit"></i>@lang("messages.edit")</a></li>';
            html += '<li><a href="/products/get-product-accounts/'+ data.id +'" class="accounting_account"><i class="fa fa-book"></i>'+ LANG.accounting_accounts +'</a></li>';

            @endcan

            @can('product.delete')

            html += '<li><a href="/products/'+data.id+'" class="delete-product"><i class="fa fa-trash"></i>@lang("messages.delete")</a></li>';

            @endcan

            html += '<li class="divider"></li>';

            @can('product.create')

            if(data.clasification != 'service')
            {
                html += '<li><a href="#" data-href="/opening-stock/add/'+data.id+'" class="add-opening-stock"><i class="fa fa-database"></i>@lang("lang_v1.add_edit_opening_stock")</a></li>';
            }

            @if($selling_price_group_count > 0)

            html += '<li><a href="/products/add-selling-prices/'+data.id+'"><i class="fa fa-money"></i>@lang("lang_v1.add_selling_price_group_prices")</a></li>';

            @endif

            @endcan

            html += '</ul></div>';

            return html;

        }, orderable: false, searchable: false }
        ],
        createdRow: function( row, data, dataIndex ) {
            if($('input#is_rack_enabled').val() == 1) {

                $(row).find('td:eq(0)').prepend('<i style="margin:auto;" class="fa fa-plus-circle text-success cursor-pointer no-print rack-details" title="' + LANG.details + '"></i>&nbsp;&nbsp;');
            }
        }

    });

    $('table#product_table tbody').on('click', 'a.delete-product', function(e){
        e.preventDefault();
        href = $(this).attr("href");
        swal({
            title: LANG.sure,
            text: '{{__('messages.delete_content')}}',
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete){
                $.ajax({
                    method: "DELETE",
                    url: href,
                    dataType: "json",
                    success: function(result){
                        if(result.success == true){
                            Swal.fire
                            ({
                                title: result.msg,
                                icon: "success",
                            });
                            product_table.ajax.reload(null, false);
                        } else {
                            Swal.fire
                            ({
                                title: result.msg,
                                icon: "error",
                            });
                        }
                    }
                });
            }
        });
    })

    var detailRows = [];
    $('#product_table tbody').on( 'click', 'tr i.rack-details', function () {
        var i = $(this);
        var tr = $(this).closest('tr');
        var row = product_table.row( tr );
        var idx = $.inArray( tr.attr('id'), detailRows );

        if ( row.child.isShown() ) {
            i.addClass( 'fa-plus-circle text-success' );
            i.removeClass( 'fa-minus-circle text-danger' );

            row.child.hide();


            detailRows.splice( idx, 1 );
        } else {
            i.removeClass( 'fa-plus-circle text-success' );
            i.addClass( 'fa-minus-circle text-danger' );

            row.child( get_product_details( row.data() ) ).show();


            if ( idx === -1 ) {
                detailRows.push( tr.attr('id') );
            }
        }
    });        
});

$("#btnAdd").click(function(){

    $("#div_index").hide();
    $("#div_datatable").hide();
    $("#div_add").show();

    $("#btn_add").hide();
    $("#btn_cancel").show();

    $("#header_index").hide();
    $("#header_add").show();
});

$("#btnUndo").click(function(){

    $("#div_add").hide();
    $("#div_index").show();
    $("#div_datatable").show();

    $("#btn_cancel").hide();
    $("#btn_add").show();

    $("#header_add").hide();
    $("#header_index").show();
});

function addSupplier()
{
    var route = "/contacts/showSupplier/"+id;
    $.get(route, function(res){
        supplier_id = res.id;
        name = res.name;
        count = parseInt(jQuery.inArray(supplier_id, supplier_ids));
        if (count >= 0)
        {
           Swal.fire
           ({
            title: "{{__('contact.supplier_already_added')}}",
            icon: "error",
        });
       }
       else
       {
        supplier_ids.push(supplier_id);
        var fila = '<tr class="selected" id="fila'+cont+'" style="height: 10px"><td><button id="bitem'+cont+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteSupplier('+cont+', '+supplier_id+');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="supplier_ids[]" value="'+supplier_id+'">'+res.supplier_business_name+'</td><td>'+name+'</td><td>'+res.mobile+'</td><td><input type="text" id="catalogue'+cont+'" name="catalogue['+cont+']" class="form-control inpuy-sm" required></td><td><input type="number" name="uxc['+cont+']" class="form-control input-sm" required></td><td><input type="number" name="weight_product['+cont+']" class="form-control input-sm" required></td><td><input type="number" name="dimensions['+cont+']" class="form-control input-sm" required></td><td><input type="text" name="custom_field['+cont+']" class="form-control input-sm" required></td></tr>';
        $("#lista").append(fila);
        $("#catalogue"+cont+"").focus();
        cont++;
    }
});
}
$("#supplier_id").change(function(event){
    id = $("#supplier_id").val();
    if(id.length > 0)
    {
      addSupplier();
  }
});
function deleteSupplier(index, id){ 
    $("#fila" + index).remove();
    supplier_ids.removeItem(id);
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
$("#clasification").change(function(event){
    type = $("#clasification").val();
    if(type == "service")
    {
        $("#type").val("single").change();
        $('#alert_quantity').val(0);
        $("#unit_id").prop('required', false);
        $('#divExcludeService').hide();
        $('#divExcludeService2').hide();
        $('#divExcludeService3').hide();
        $('#divExcludeService4').hide();
        $("#opening_stock_button").hide();
        $("#submit_n_add_selling_prices").hide();
        $("#imei").prop('checked', false);
        $("#enable_stock").prop('checked', false);
        $("#div_type").hide();
        $("#div_imei").hide();
        var product_type = 'single';
        var action = $('#type').attr('data-action');
        var product_id = $('#type').attr('data-product_id');
        $.ajax({
            method: "POST",
            url: '/products/product_form_part',
            dataType: "html",
            data: { 'type': product_type, 'product_id': product_id, 'action': action },
            success: function(result) {
                if (result) {
                    $('#product_form_part').html(result);
                    toggle_dsp_input();
                }
            }
        });
    }
    if(type == "kits"){
        $("#type").val("single").change();
        $('#alert_quantity').val(0);
        $("#unit_id").prop('required', false);
        $('#divExcludeService').show();

        $("#div_brand").hide();
        $("#div_unit").hide();

        $('#divExcludeService2').hide();
        $('#divExcludeService3').hide();
        $('#divExcludeService4').show();
        $("#opening_stock_button").hide();
        $("#submit_n_add_selling_prices").hide();
        $("#imei").prop('checked', false);
        $("#enable_stock").prop('checked', false);
        $("#div_type").hide();
        $("#div_imei").hide();
        var product_type = 'single';
        var action = $('#type').attr('data-action');
        var product_id = $('#type').attr('data-product_id');
        $.ajax({
            method: "POST",
            url: '/products/product_form_part',
            dataType: "html",
            data: { 'type': product_type, 'product_id': product_id, 'action': action },
            success: function(result) {
                if (result) {
                    $('#product_form_part').html(result);
                    toggle_dsp_input();
                }
            }
        });
    }

    if(type == 'product'){
        $("#div_brand").show();
        $("#div_unit").show();
        $("#div_imei").show();
        $('#alert_quantity').val('');
        $("#unit_id").prop('required', true);
        $('#divExcludeService').show();
        $('#divExcludeService2').show();
        $('#divExcludeService3').show();
        $('#divExcludeService4').hide();
        $("#opening_stock_button").show();
        $("#submit_n_add_selling_prices").show();
        $("#div_type").show();
        $("#enable_stock").prop('checked', true);
    }
});

$(document).on('submit', 'form#product_add_form', function(e) {
    $(this).find('button[type="submit"]').attr('disabled', true);
    $("#btnUndo").attr('disabled', true);
});

function cls(){
    $("#lista").empty();
    $("#listak").empty();
    cont = 0;
    supplier_ids = [];

    contk = 0;
    kit_idsk = [];

    $("#clasification").val("product").change();
    $("#type").val("single").change();
    $('#name').val('');
    $('#sku').val('');
    $('#alert_quantity').val('');
    $('#dai').val('');
    $('#weight').val('');
    $('#is_active').prop('checked', true);

    $("#div_imei").show();    
    $("#unit_id").prop('required', true);
    $('#divExcludeService').show();
    $('#divExcludeService2').show();
    $('#divExcludeService3').show();
    $("#opening_stock_button").show();
    $("#div_type").show();

    var product_type = 'single';
    var action = $('#type').attr('data-action');
    var product_id = $('#type').attr('data-product_id');
    $.ajax({
        method: "POST",
        url: '/products/product_form_part',
        dataType: "html",
        data: { 'type': product_type, 'product_id': product_id, 'action': action },
        success: function(result) {
            if (result) {
                $('#product_form_part').html(result);
                toggle_dsp_input();
            }
        }
    });

}

var contk = 0;
var kit_idsk = [];
var valor=[];
var total = 0;

function addChildren()
{
    var route = "/products/showProduct/"+id;
    $.get(route, function(res){
        variation_id = res.variation_id;
        product_id = res.product_id;
        if(res.sku == res.sub_sku){
            name = res.name_product;
        }
        else{
            name = ""+res.name_product+" "+res.name_variation+"";
        }
        if(res.brand != null){
            brand = res.brand;
        }
        else{
            brand = 'N/A';
        }
        if(res.default_purchase_price != null){
            price = res.default_purchase_price;
        }
        else{
            price = 'N/A';
        }
        if(res.sub_sku != null){
            sku = res.sub_sku;
        }
        else{
            sku = 'N/A';
        }

        count = parseInt(jQuery.inArray(variation_id, kit_idsk));
        if (count >= 0)
        {
            Swal.fire
            ({
                title: "{{__('product.product_already_added')}}",
                icon: "error",
            });
        }
        else
        {
            if(res.clasification == 'service'){
                unit = 'N/A'
                kit_idsk.push(variation_id);
                valor.push(contk);
                var fila = '<tr class="selected" id="filak'+contk+'" style="height: 10px"><td><button id="bitem'+contk+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteChildren('+contk+', '+variation_id+');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="product_ids[]" value="'+variation_id+'">'+name+'</td><td>'+sku+'</td><td>'+brand+'</td><td><input type="hidden" name="clas_product['+contk+']" value="service">'+unit+'</td><td><input type="hidden" name=price['+contk+'] id="price'+contk+'" value="'+price+'">'+price+'</td><td><input type="number" id="quantity'+contk+'" name="quantity['+contk+']" class="form-control form-control-sm" min="0.01" value="1" onchange="getTotalKit()" required></td></tr>';
                $("#listak").append(fila);
                contk++;
                getTotalKit();
            }
            else{
                var route = "/products/getUnitPlan/"+product_id;
                $.get(route, function(res){
                    if(res.plan == 'group'){
                        var route = "/products/getUnitsFromGroup/"+res.unit_group_id;
                        $.get(route, function(res){
                            content = "";
                            $(res).each(function(key,value){
                                content = content + '<option value="'+value.id+'">'+value.actual_name+'</option>';
                            });
                            kit_idsk.push(variation_id);
                            valor.push(contk);
                            var fila = '<tr class="selected" id="filak'+contk+'" style="height: 10px"><td><button id="bitem'+contk+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteChildren('+contk+', '+variation_id+');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="product_ids[]" value="'+variation_id+'">'+name+'</td><td>'+sku+'</td><td>'+brand+'</td><td><input type="hidden" name="clas_product['+contk+']" value="product"><select name="kit_child['+contk+']" id="kit_child['+contk+']" class="form-control select2">'+content+'</select></td><td><input type="hidden" name=price['+contk+'] id="price'+contk+'" value="'+price+'">'+price+'</td><td><input type="number" id="quantity'+contk+'" name="quantity['+contk+']" class="form-control form-control-sm" min="0.01" value="1" onchange="getTotalKit()" required></td></tr>';
                            $("#listak").append(fila);
                            contk++;
                            getTotalKit();
                        });
                    }
                    else{
                        unit = '<input type="hidden" value="'+res.unit_id+'" name="kit_child['+contk+']">'+res.name+'';

                        kit_idsk.push(variation_id);
                        valor.push(contk);
                        var fila = '<tr class="selected" id="filak'+contk+'" style="height: 10px"><td><button id="bitem'+contk+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteChildren('+contk+', '+variation_id+');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="product_ids[]" value="'+variation_id+'">'+name+'</td><td>'+sku+'</td><td>'+brand+'</td><td><input type="hidden" name="clas_product['+contk+']" value="product">'+unit+'</td><td><input type="hidden" name=price['+contk+'] id="price'+contk+'" value="'+price+'">'+price+'</td><td><input type="number" id="quantity'+contk+'" name="quantity['+contk+']" class="form-control form-control-sm" min="0.01" value="1" onchange="getTotalKit()" required></td></tr>';
                        $("#listak").append(fila);
                        contk++;
                        getTotalKit();
                    }
                });
            }
        }
    });
}

function deleteChildren(index, id){ 
    $("#filak" + index).remove();
    kit_idsk.removeItem(id);
    if(kit_idsk.length == 0)
    {
        total=0;
        contk = 0;
        kit_idsk=[];
        valor=[];
    }
    getTotalKit();
}

$(document).on( 'change', 'select#kit_children', function(event){
    id = $("#kit_children").val();
    if(id != 0){
        addChildren();
        $("#kit_children").val(0).change();
    }
});

function getTotalKit()
{
    quantity = 0.00;
    price = 0.00;
    total = 0.00;
    $.each(valor, function(value){
        quantityg = $("#quantity"+value+"").val();
        priceg = $("#price"+value+"").val();
        if(quantityg)
        {
            if(isNaN(quantityg))
            {
                quantity = parseFloat(0.00);
            }
            else
            {
                quantity = parseFloat($("#quantity"+value+"").val());
            }
        }
        else
        {
            quantity = parseFloat(0.00);
        }
        if(priceg)
        {
            if(isNaN(priceg))
            {
                price = 0.00;
            }
            else
            {
                price = parseFloat($("#price"+value+"").val());
            }

        }
        else
        {
            price = 0.00;
        }
        subtotal = quantity * price;
        if(isNaN(subtotal))
        {
            subtotal = 0.00;
        }
        if(isInfinite(total))
        {
            subtotal = 0.00;
        }
        total = total + subtotal;
    });
    $("#single_dpp").val(total);
    var purchase_exc_tax = __read_number($('input#single_dpp'));
    purchase_exc_tax = (purchase_exc_tax == undefined) ? 0 : purchase_exc_tax;

    var tax_rate = $('select#tax').find(':selected').data('rate');
    tax_rate = (tax_rate == undefined) ? 0 : tax_rate;

    var price_precision = $('#price_precision').val();

    var purchase_inc_tax = __add_percent(purchase_exc_tax, tax_rate);
    __write_number($('input#single_dpp_inc_tax'), purchase_inc_tax, false, price_precision);

    var profit_percent = __read_number($('#profit_percent'));
    var selling_price = __add_percent(purchase_exc_tax, profit_percent);
    __write_number($('input#single_dsp'), selling_price, false, price_precision);

    var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
    __write_number($('input#single_dsp_inc_tax'), selling_price_inc_tax, false, price_precision);
}

function isInfinite(n)
{
    return n === n/0;
}
</script>
@endsection
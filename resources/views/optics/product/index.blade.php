@extends('layouts.app')
@section('title', $type_ != "material" ? __('sale.products') : __('material.materials'))
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@if ($type_ != "material") @lang('sale.products') @else @lang('material.materials') @endif
        <small>@if ($type_ != "material") @lang('lang_v1.manage_products') @else @lang('material.manage_materials') @endif</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    {{-- Number of decimal places to store and use in calculations --}}
    <input type="hidden" id="price_precision" value="{{ config('app.price_precision') }}">

   <div class="boxform_u box-solid_u">
    <div class="box-header">
        <span  id="header_index">
            <h3 class="box-title">@if ($type_ != "material") @lang('lang_v1.all_products') @else @lang('material.all_materials') @endif</h3>
        </span>
        <span  id="header_add" style="display: none;">
            <h3 class="box-title">@if ($type_ != "material") @lang('product.add_new_product') @else @lang('material.add_new_material') @endif</h3>
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
        <div id="div_form">
            {!! Form::open(['id'=>'form_products_report', 'action' => 'ReportController@postProductsReport', 'method' => 'post', 'target' => '_blank']) !!}
            <div class="row">
                {{-- Clasification --}}
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('select_clasification', __('product.clasification')) !!}

                        @if ($type_ == 'material')
                            {!! Form::select('select_clasification', $clasifications, 'material', [
                                'id' => 'select_clasification',
                                'class' => 'form-control select2 product-filters',
                                'disabled'
                            ]) !!}

                            {!! Form::hidden('clasification_report', 'material', ['id' => 'clasification_report']) !!}
                        @else
                            {!! Form::select('clasification_report', $clasifications, null, [
                                'id' => 'clasification_report',
                                'class' => 'form-control select2 product-filters',
                                'placeholder' => __('kardex.all_2')
                            ]) !!}
                        @endif
                    </div>
                </div>

                {{-- Category --}}
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('category', __('category.category')) !!}

                        {!! Form::select('category', $categories, null, [
                            'id' => 'category',
                            'class' => 'form-control select2 product-filters',
                            'placeholder' => __('kardex.all_2')
                        ]) !!}
                    </div>
                </div>

                {{-- Subcategory --}}
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('sub_category', __('product.sub_category')) !!}

                        {!! Form::select('sub_category', [], null, [
                            'id' => 'sub_category',
                            'class' => 'form-control select2 product-filters',
                            'placeholder' => __('kardex.all_2')
                        ]) !!}
                    </div>
                </div>

                {{-- Brand --}}
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('brand', __('brand.brand')) !!}

                        {!! Form::select('brand', $brands, null, [
                            'id' => 'brand',
                            'class' => 'form-control select2 product-filters',
                            'placeholder' => __('kardex.all_2')
                        ]) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- report_tyoe --}}
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>@lang('accounting.format')</label>
                        <select name="report_type" id="report_type" class="form-control select2" style="width: 100%" required>
                            <option value="pdf" selected>PDF</option>
                            <option value="excel">Excel</option>
                        </select>                       
                    </div>
                </div>

                {{-- size --}}
                <div class="col-sm-3">
                    <label>@lang('accounting.size_font')</label>
                    <select name="size" id="size" class="form-control select2" style="width: 100%;" required>
                        <option value="7">7</option>
                        <option value="8" selected>8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                </div>

                {{-- button_report --}}
                <div class="col-sm-3" style="margin-top: 25px;">
                    <div class="form-group">
                        <input type="submit" class="btn btn-success" value="@lang('accounting.generate')" id="button_report">
                    </div>
                </div>
            </div>
            
            {!! Form::close() !!}
        </div>

        <div id="div_index">
            @can('product.view')
            <div class="table-responsive">
             <table class="table table-striped table-text-center ajax_view" id="product_table" width="100%">
                <thead>
                   <tr id="div_datatable">
                    <th>@lang('product.sku')</th>
                    <th>@lang('sale.product')</th>
                    <th>@lang('product.clasification')</th>
                    <th>@lang('product.category')</th>
                    <th>@lang('product.sub_category')</th>
                    <th>@lang('product.unit')</th>
                    <th>@lang('product.brand')</th>
                    <th>@lang('messages.action')</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcan
</div>
<div id="div_add" style="display: none;">
    @include('optics.product.create')
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


</section>
<!-- /.content -->
@endsection
@section('javascript')
@php $asset_v = env('APP_VERSION'); @endphp
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
    var cont = 0;
    var supplier_ids = [];
    $(document).ready( function(){

        $.fn.dataTable.ext.errMode = 'none';
        
        $('.size-mask').mask('00-00-00');

        var col_sort = [1, 'asc'];

        var product_table = $('#product_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/products/getProductsData?type={{ $type_ }}',
                data: function (d) {
                    d.clasification = $('#clasification_report').val();
                    d.category = $('#category').val();
                    d.sub_category = $('#sub_category').val();
                    d.brand = $('#brand').val();
                }
            },
            pageLength: 25,
            aaSorting: [col_sort],
            columns: [
                { data: 'sku', name: 'sku' },
                { data: 'name', name: 'name' },
                { data: 'clasification', name: 'clasification', orderable: false, searchable: false },
                { data: 'category', name: 'category', orderable: false, searchable: false },
                { data: 'sub_category', name: 'sub_category', orderable: false, searchable: false },
                { data: 'unit', name: 'unit', orderable: false, searchable: false },
                { data: 'brand', name: 'brand', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            createdRow: function( row, data, dataIndex ) {
                if($('input#is_rack_enabled').val() == 1){
                    var target_col = 0;
                    $( row ).find('td:eq('+target_col+') div').prepend('<i style="margin:auto;" class="fa fa-plus-circle text-success cursor-pointer no-print rack-details" title="' + LANG.details + '"></i>&nbsp;&nbsp;');
                }
                $( row ).find('td:eq(0)').attr('class', 'selectable_td');
            }
        });

        // On change of product-filters selects
        $('select.product-filters').on('change', function () {
            product_table.ajax.reload();
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

        // Array to track the ids of the details displayed rows
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

                // Remove from the 'open' array
                detailRows.splice( idx, 1 );
            } else {
                i.removeClass( 'fa-plus-circle text-success' );
                i.addClass( 'fa-minus-circle text-danger' );

                row.child( get_product_details( row.data() ) ).show();

                // Add to the 'open' array
                if ( idx === -1 ) {
                    detailRows.push( tr.attr('id') );
                }
            }
        });

        $(document).on('click', '#delete-selected', function(e){
            e.preventDefault();
            var selected_rows = [];
            var i = 0;
            $('.row-select:checked').each(function () {
                selected_rows[i++] = $(this).val();
            }); 

            if(selected_rows.length > 0){
                $('input#selected_rows').val(selected_rows);
                //
                $.confirm({
                    title: LANG.sure,
                    content: '{{__('messages.delete_content')}}',
                    icon: 'fa fa-warning',
                    theme: 'modern',
                    closeIcon: true,
                    animation: 'scale',
                    type: 'red',
                    buttons: {
                        confirm:{
                            text: '{{__('messages.delete_button_delete')}}',
                            action: function()
                            {
                                $('form#mass_delete_form').submit();
                            }
                        },
                        cancel:{
                            text: '{{__('messages.delete_button_cancel')}}',
                        },
                    }
                });
            }
            else{
                $('input#selected_rows').val('');
                swal('@lang("lang_v1.no_row_selected")');
            }    
        })

        // Hide product fields and show material fields
        type = $("#clasification").val();
        if (type == "material") {
            $("div.product_fields").hide();
            $("div.product_exc_fields").hide();
            $("div.material_fields").show();
        } else {
            $("div.material_fields").hide();
        }

        // Actions button
        $(document).on('click', '.btn-actions', function() {
            let id = $(this).data('product-id');
            add_toggle_dropdown($(this), id);
        });

        function add_toggle_dropdown(btn, id) {
            $.ajax({
                method: "GET",
                url: '/products/get_toggle_dropdown/' + id,
                dataType: 'html',
                success: function(data) {
                    btn.closest('.btn-group').find('ul').html(data);
                }
            });
        }
    });

$("#btnAdd").click(function(){
    $("#div_index").hide();
    $("#div_datatable").hide();
    $("#div_add").show();

    $("#btn_add").hide();
    $("#btn_cancel").show();

    $("#header_index").hide();
    $("#header_add").show();

    $("#div_form").hide();
});

$("#btnUndo").click(function(){
    $("#div_add").hide();
    $("#div_index").show();
    $("#div_datatable").show();

    $("#btn_cancel").hide();
    $("#btn_add").show();

    $("#header_add").hide();
    $("#header_index").show();

    $("#div_form").show();
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
        if ('{{ $type_ }}' == 'product') {
            var fila = '<tr class="selected" id="fila'+cont+'" style="height: 10px"><td><button id="bitem'+cont+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteSupplier('+cont+', '+supplier_id+');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="supplier_ids[]" value="'+supplier_id+'">'+res.supplier_business_name+'</td><td>'+name+'</td><td>'+res.mobile+'</td><td><input type="text" id="catalogue'+cont+'" name="catalogue[]" class="form-control inpuy-sm" required></td><td><input type="number" name="uxc[]" class="form-control input-sm" required></td><td><input type="number" name="weight_product[]" class="form-control input-sm" required></td><td><input type="number" name="dimensions[]" class="form-control input-sm" required></td><td><input type="text" name="custom_field[]" class="form-control input-sm" required></td></tr>';
        } else if ('{{ $type_ }}' == 'material') {
            var fila = '<tr class="selected" id="fila'+cont+'" style="height: 10px"><td><button id="bitem'+cont+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteSupplier('+cont+', '+supplier_id+');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="supplier_ids[]" value="'+supplier_id+'">'+res.supplier_business_name+'</td><td>'+name+'</td><td>'+res.mobile+'</td></tr>';
        }
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
        $("div#ar_div").show();
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

        $("div.product_exc_fields").hide();
        $("div.material_fields").hide();

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
        $("div#ar_div").hide();
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

        $("div.product_exc_fields").hide();
        $("div.material_fields").hide();

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
        $("div#ar_div").hide();
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

        $("div.product_fields").show();
        $("div.product_exc_fields").show();
        $("div.material_fields").hide();
    }

    if (type == "material") {
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
        
        $("div.product_fields").hide();
        $("div.product_exc_fields").hide();
        $("div.material_fields").show();
    }
});
$(document).on('submit', 'form#product_add_form', function(e) {
    $(this).find('button[type="submit"]').attr('disabled', true);
    $("#btnUndo").attr('disabled', true);
   /* submit_type = $('#submit_type').val();
    data = new FormData($(this)[0]);
    data.append("product_descript", $('#product_description').val());
    $.ajax({
        url: $(this).attr("action"),
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        success: function(result) {
            if (result.success == true){

                if(submit_type == 'save_n_add_another'){
                    cls();
                    $("#product_add_form").find('button[type="submit"]').attr('disabled', false);
                    $("#btnUndo").attr('disabled', false);
                    $("#product_table").DataTable().ajax.reload();
                    Swal.fire({
                        title: ""+result.msg+"",
                        icon: "success",});
                }

                if(submit_type == 'submit'){
                    cls();
                    $("#product_add_form").find('button[type="submit"]').attr('disabled', false);
                    $("#btnUndo").attr('disabled', false);
                    $("#product_table").DataTable().ajax.reload();
                    $("#btn_cancel").hide();
                    $("#btn_add").show();
                    $('#div_add').hide();
                    $('#div_index').show();
                    $("#header_add").hide();
                    $("#header_index").show();
                    Swal.fire({
                        title: ""+result.msg+"",
                        icon: "success",});
                }

                if(submit_type == 'submit_n_add_opening_stock'){
                    Swal.fire({
                        title: ""+result.msg+"",
                        icon: "success",});
                    id = result.product_id;
                    var url = '{!!URL::to('/opening-stock/add/:id')!!}';
                    url = url.replace(':id', id);
                    window.location.href = url;
                }
                
                if(submit_type == 'submit_n_add_selling_prices'){
                    Swal.fire({
                        title: ""+result.msg+"",
                        icon: "success",});
                    id = result.product_id;
                    var url = '{!!URL::to('/products/add-selling-prices/:id')!!}';
                    url = url.replace(':id', id);
                    window.location.href = url;
                }
            }
            else{
                $("#product_add_form").find('button[type="submit"]').attr('disabled', false);
                $("#btnUndo").attr('disabled', false);
                Swal.fire({
                    title: ""+result.msg+"",
                    icon: "error",});
            }
        }
    });*/
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
            var fila = '<tr class="selected" id="filak'+contk+'" style="height: 10px"><td><button id="bitem'+contk+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteChildren('+contk+', '+variation_id+');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="product_ids[]" value="'+variation_id+'">'+name+'</td><td>'+sku+'</td><td>'+brand+'</td><td><input type="hidden" name="clas_product[]" value="service">'+unit+'</td><td><input type="hidden" name=price[] id="price'+contk+'" value="'+price+'">'+price+'</td><td><input type="number" id="quantity'+contk+'" name="quantity[]" class="form-control form-control-sm" min=1 value="1" onchange="getTotalKit()" required></td></tr>';
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
                        var fila = '<tr class="selected" id="filak'+contk+'" style="height: 10px"><td><button id="bitem'+contk+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteChildren('+contk+', '+variation_id+');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="product_ids[]" value="'+variation_id+'">'+name+'</td><td>'+sku+'</td><td>'+brand+'</td><td><input type="hidden" name="clas_product[]" value="product"><select name="kit_child[]" id="kit_child[]" class="form-control select2">'+content+'</select></td><td><input type="hidden" name=price[] id="price'+contk+'" value="'+price+'">'+price+'</td><td><input type="number" id="quantity'+contk+'" name="quantity[]" class="form-control form-control-sm" min=1 value="1" onchange="getTotalKit()" required></td></tr>';
                        $("#listak").append(fila);
                        contk++;
                        getTotalKit();
                    });
                }
                else{
                    unit = '<input type="hidden" value="'+res.unit_id+'" name="kit_child[]">'+res.name+'';

                    kit_idsk.push(variation_id);
                    valor.push(contk);
                    var fila = '<tr class="selected" id="filak'+contk+'" style="height: 10px"><td><button id="bitem'+contk+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteChildren('+contk+', '+variation_id+');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="product_ids[]" value="'+variation_id+'">'+name+'</td><td>'+sku+'</td><td>'+brand+'</td><td><input type="hidden" name="clas_product[]" value="product">'+unit+'</td><td><input type="hidden" name=price[] id="price'+contk+'" value="'+price+'">'+price+'</td><td><input type="number" id="quantity'+contk+'" name="quantity[]" class="form-control form-control-sm" min=1 value="1" onchange="getTotalKit()" required></td></tr>';
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

// On change of category select
$('#category').change(function() {
	get_sub_categories_from_filter();
});

/**
 * Get subcategories of a category.
 * 
 * @return void
 */
function get_sub_categories_from_filter() {
    let cat = $('#category').val();

    if (cat == '') {
        $('#sub_category').empty();
        let placeholder = new Option(LANG.all_2, '', true, true);
        $('#sub_category').append(placeholder).trigger('change');

    } else {
        $.ajax({
            method: 'post',
            url: '/products/get_sub_categories',
            dataType: 'html',
            data: { 'cat_id': cat },
            success: function (result) {
                if (result) {
                    $('#sub_category').html(result);
                }
            }
        });
    }
}
</script>
@endsection
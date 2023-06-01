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
            <span id="header_index">
                <h3 class="box-title">@lang('lang_v1.all_products')</h3>
            </span>
            <span id="header_add" style="display: none;">
                <h3 class="box-title">@lang('product.add_new_product')</h3>
            </span>
            @can('product.create')
            <div class="box-tools">
                <a href="{{ url('products/create') }}" type="button" class="btn btn-primary">
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </a>
            </div>
            {{-- <div class="box-tools" id="btn_add">
                <button type="button" id="btnAdd" class="btn btn-primary">@lang('messages.add')</button>
            </div>
            <div class="box-tools" id="btn_cancel" style="display: none;">
                <button type="button" id="btnUndo" class="btn btn-danger">@lang('messages.cancel')</button>
            </div> --}}
            @endcan
        </div>
        <div class="box-body">
            <div id="div_index">
                @can('product.view')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped ajax_view table-text-center" id="product_table"
                        width="100%">
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

        </div>
    </div>


    <input type="hidden" id="is_rack_enabled" value="{{$rack_enabled}}">

    <div class="modal fade product_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
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
    <div class="modal fade" id="modal_history_purchase" data-backdrop="false" tabindex="-1" role="dialog"
        aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade" id="product_accounts_modal" data-backdrop="false" tabindex="-1" role="dialog"
        aria-labelledby="gridSystemModalLabel"></div>
</section>
<!-- /.content -->
@endsection
@section('javascript')
@php $asset_v = env('APP_VERSION'); @endphp
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/import_purchase.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
    var supplier_ids = [];
    var cont = 0;
    $(document).ready( function(){
        $('#modalSupplier').on('shown.bs.modal', function () {
		    $(this).find('#supplier_id').select2({
                dropdownParent: $(this),
                ajax: {
                    url: "/purchases/get_suppliers",
                    dataType: "json",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page,
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data,
                        };
                    },
                },
                minimumInputLength: 1,
                escapeMarkup: function (m) {
                    return m;
                },
                templateResult: function (data) {
                    if (!data.id) {
                        return data.text;
                    }
                    let html = data.text + " (<b>Business: </b>" + data.business_name + ")";
                    return html;
                },
            });

            function addSupplier()
            {
                var route = "/contacts/showSupplier/"+id;
                $.get(route, function(res){
                    supplier_id = res.id;
                    name = res.name;
                    var count = $('input#supplier_id-'+supplier_id);
                    //count = parseInt(jQuery.inArray(supplier_id, supplier_ids));
                    if (count.length > 0)
                    {
                        Swal.fire
                        ({
                            title: "{{__('contact.supplier_already_added')}}",
                            icon: "error",
                        });
                    }
                    else
                    {
                        cont = $('#supplier_table >tbody >tr').length;
                        supplier_ids.push(supplier_id);
                        var fila = '<tr class="selected" id="fila'+cont+'" style="height: 10px"><td><input type="hidden" name="supplier_ids[]" value="'+supplier_id+'">'+res.supplier_business_name+'</td><td>'+name+'</td><td>'+res.mobile+'</td><td><button id="bitem'+cont+'" type="button" class="btn btn-danger btn-xs remove-item" onClick="deleteSupplierTr('+cont+', '+supplier_id+');"><i class="fa fa-times"></i></button></td></tr>';
                        $("#listaSupplier").append(fila);
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
        });

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

                html = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> @lang("messages.actions") <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-right" role="menu">';

                if(data.clasification == 'product')
                {
                    html += '<li><a href="labels/show?product_id='+data.id+'" data-toggle="tooltip" title="Print Barcode/Label"><i class="fa fa-barcode"></i>@lang('barcode.labels')</a></li>';
                }

                @can('product.view')

                html += '<li><a href="/products/view/'+data.id+'" class="view-product"><i class="fa fa-eye"></i>@lang("messages.view")</a></li>';

                if (data.clasification == "product") {
                    html += '<li><a href="/products/viewSupplier/'+data.id+'" class="view-supplier"><i class="fa fa-eye"></i>@lang("product.view_suppliers")</a></li>';
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

    function deleteSupplierTr(index, supplierId){
        Swal.fire({
			title: LANG.sure,
			text: LANG.cancel_supplier_msg,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: LANG.yes,
			cancelButtonText: LANG.not,
		}).then((resul) => {
			if (resul.isConfirmed) {
				var id =  $("input#product_id").val();
                var route = "/products/deleteSupplier/"+id+"/"+supplierId;
                $.get(route, function(res){
                    $("#fila" + index).remove();
                    supplier_ids.removeItem(supplierId);
                });
			} else {
				return false;
			}
		});
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
</script>
@endsection
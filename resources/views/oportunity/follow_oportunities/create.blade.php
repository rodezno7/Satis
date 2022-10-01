<div class="modal-dialog modal-lg" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('FollowOportunitiesController@store'), 'method' => 'post', 'id' => 'follow_oportunity_add_form']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title"><span id="customer_name"></span> @lang('crm.add_follow_oportunity') <b>{{ $oportunity->name }}</b></h4> 
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('name', __('crm.contact_type') . ' : ') !!}

                        <select name="contact_type" id="contact_type" class="form-control select2 required"
                            style="width: 100%">
                            <option value='entrante'>@lang('crm.option_in')</option>
                            <option value='saliente'>@lang('crm.option_out')</option>
                            <option value='no_aplica'>@lang('crm.option_none')</option>
                        </select>
                        <input type="hidden" name="oportunity_id" value="{{ $id }}" id="oportunity_id">

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('name', __('crm.contactreason') . ' : ') !!}

                        {!! Form::select('contact_reason_id', $contactreason, '', ['class' => 'form-control select2', 'required', 'id' => 'contact_reason_id', 'style' => 'width: 100%;']) !!}

                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('contact_mode', __('crm.conctact_mode')) !!}
                        <div class="wrap-inputform">
                            {!! Form::select('contact_mode_id', $contactmode, '', ['class' => 'form-control select2', 'id' => 'contact_mode_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                        </div>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('product_cat_id', __('crm.interest') . ' : ') !!}
                        {!! Form::select('product_cat_id', $categories, '', ['class' => 'form-control select2', 'id' => 'product_cat_id', 'style' => 'width: 100%;']) !!}
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-check-input">
                            {{ __('crm.not_found') }} {!! Form::checkbox('chk_not_found', '1', false, ['id' => 'chk_not_found', 'onclick' => 'showNotFoundDesc();']) !!}
                        </label>

                        {!! Form::textarea('products_not_found_desc', null, ['class' => 'form-control', 'id' => 'products_not_found_desc','rows'=>2, 'cols'=>'2', 'style' => 'display: none;']) !!}

                    </div>
                </div>

                <div class="col-md-6">

                    <div class="form-group">
                        <label>@lang('crm.notes')</label>
                        {!! Form::textarea('notes', null, ['class' => 'form-control', 'id' => 'notes', 'rows' => 2]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-check-input">
                            {{ __('crm.not_stock') }} {!! Form::checkbox('chk_not_stock', '1', false, ['id' => 'chk_not_stock', 'onClick' => 'showNotStockDesc()']) !!}
                        </label>
                    </div>
                </div>

                <div class="col-md-6">
                    <label>@lang('crm.date')</label>
                    <div class="wrap-inputform">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        <input type="text" id="date" name="date" readonly class="form-control"
                            value="{{ @format_date('now') }}" required>
                    </div>
                </div>
            </div>
            <div class="row" id="div_products" style="display: none;">

                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                    <div class="form-group">

                        <label>@lang('accounting.location')</label>
                        {!! Form::select('locations', $locations, null, ['class' => 'form-control select2', 'style' => 'width: 100%', 'id' => 'locations']) !!}
                    </div>

                </div>
                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">

                    <div class="form-group">
                        <label>@lang('product.products')</label>
                        <select name="products" id="products" class="form-control select2" style="width: 100%">
                            <option value="0">@lang('messages.please_select')</option>
                            @foreach ($products as $item)
                                @if ($item->sku != $item->sub_sku)
                                    <option value="{{ $item->id }}">{{ $item->name_product }}
                                        {{ $item->name_variation }}</option>
                                @else
                                    <option value="{{ $item->id }}">{{ $item->name_product }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group col-sm-12 col-md-12 col-lg-12 col-xs-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <th style="width: 10%;">Op</th>
                                <th style="width: 45%;">@lang('product.name')</th>
                                <th style="width: 15%;">@lang('product.sku')</th>
                                <th style="width: 15%;">@lang('product.actual_stock')</th>
                                <th style="width: 15%;">@lang('product.required_quantity')</th>
                            </thead>
                            <tbody id="list">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="btn-add-follow">@lang('messages.save')</button>
            <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default"
                id="btn-close-modal-add-follow">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>
<script>
    $(document).ready(function(){
        $('select.select2').select2();
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    });
    $(document).on('change', 'select#products', function(event) {
        id = $("#products").val();
        if (id != 0) {
            addProduct();

            $("#products").val(0).change();
        }
    });

    var cont = 0;
    var product_ids = [];
    var rowCont = [];


    function addProduct() {
        location_id = $("#locations").val();
        if (location_id != null) {
            var route = "/products/showStock/" + id + "/" + location_id;
            $.get(route, function(res) {
                variation_id = res.variation_id;
                product_id = res.product_id;
                if (res.sku == res.sub_sku) {
                    name = res.name_product;
                } else {
                    name = "" + res.name_product + " " + res.name_variation + "";
                }

                if (res.quantity != null) {
                    quantity = res.quantity;
                } else {
                    quantity = 0;
                }

                count = parseInt(jQuery.inArray(variation_id, product_ids));
                if (count >= 0) {
                    Swal.fire({
                        title: "{{ __('product.product_already_added') }}",
                        icon: "error",
                    });
                } else {
                    product_ids.push(variation_id);
                    rowCont.push(cont);
                    var row = '<tr class="selected" id="row' + cont +
                        '" style="height: 10px"><td><button id="bitem' + cont +
                        '" type="button" class="btn btn-danger btn-xs" onclick="deleteProduct(' + cont + ', ' +
                        variation_id +
                        ');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="variation_id[]" value="' +
                        variation_id + '">' + name + '</td><td>' + res.sku +
                        '</td><td><input type="text" name="quantity[]" id="quantity' + cont +
                        '" class="form-control form-control-sm" value="' + quantity +
                        '" readonly></td><td><input type="number" id="required_quantity' + cont +
                        '" name="required_quantity[' + cont +
                        ']" class="form-control form-control-sm" min=1 value="1" required></td></tr>';
                    $("#list").append(row);
                    cont++;
                }
            });
        } else {
            Swal.fire({
                title: "{{ __('crm.select_location') }}",
                icon: "error",
            });
        }
    }

    function deleteProduct(index, id) {
        $("#row" + index).remove();
        product_ids.removeItem(id);
        if (product_ids.length == 0) {
            cont = 0;
            product_ids = [];
            rowCont = [];
        }
    }

    Array.prototype.removeItem = function(a) {
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

    function showTaxPayer() {
        if ($("#is_taxpayer").is(":checked")) {
            $('#div_taxpayer').show();
            $("#reg_number").val('');
            $("#tax_number").val('');
            $("#business_line").val('');
            setTimeout(function() {
                    $('#reg_number').focus();
                },
                800);
        } else {
            $('#div_taxpayer').hide();
            $("#reg_number").val('');
            $("#tax_number").val('');
            $("#business_line").val('');
        }
    }

    function showCredit() {
        if ($("#allowed_credit").is(":checked")) {
            $('#div_credit').show();
            $("#opening_balance").val('');
            $("#credit_limit").val('');
            $("#payment_terms_id").val('').change();
            setTimeout(function() {
                    $('#opening_balance').focus();
                },
                800);
        } else {
            $('#div_credit').hide();
            $("#opening_balance").val('');
            $("#credit_limit").val('');
            $("#payment_terms_id").val('').change();
        }
    }


    function showNotFoundDesc() {
        if ($("#chk_not_found").is(":checked")) {
            $('#products_not_found_desc').show();
            $("#product_cat_id").val('').change();
            $("#product_cat_id").prop('disabled', true);
        } else {
            $('#products_not_found_desc').hide();
            $('#products_not_found_desc').val('');
            $("#product_cat_id").prop('disabled', false);
        }
    }


    function showNotStockDesc() {
        if ($("#chk_not_stock").is(":checked")) {
            $('#div_products').show();
        } else {
            $('#div_products').hide();
            $('#list').empty();
            cont = 0;
            product_ids = [];
            rowCont = [];
        }
    }

    function getValSel(sel) {

        var valors = $('select[name="known_by"] option:selected').text();

        if (valors.includes("cliente")) {
            $("#refered_id").prop("disabled", false);
        } else {
            $("#refered_id").val('').change();
            $("#refered_id").prop("disabled", true);
        }
    }
</script>

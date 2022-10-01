<div class="modal-dialog modal-lg" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('FollowOportunitiesController@update', [$followOportunitie->id]), 'method' => 'PUT', 'id' => 'follow_oportunity_edit_form']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('crm.edit_follow')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('name', __('crm.contact_type') . ' : ') !!}

                        <select name="eecontact_type" id="eecontact_type" class="select2 required" style="width: 100%">
                            <option value='entrante'>@lang('crm.option_in')</option>
                            <option value='saliente'>@lang('crm.option_out')</option>
                            <option value='no_aplica'>@lang('crm.option_none')</option>
                        </select>
                        <input type="hidden" name="follow_oportunity_id"
                            value="{{ $followOportunitie->oportunity_id }}" id="follow_oportunity_id">

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('name', __('crm.contactreason') . ' : ') !!}
                        {!! Form::select('eecontact_reason_id', $contactreason, $followOportunitie->contact_reason_id, ['class' => 'select2', 'required', 'id' => 'eecontact_reason_id', 'style' => 'width: 100%;']) !!}

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('econtact_mode', __('crm.conctact_mode')) !!}
                        <div class="wrap-inputform">
                            {!! Form::select('econtact_mode_id', $contactmode, $followOportunitie->contact_mode_id, ['class' => 'form-control select2', 'id' => 'econtact_mode_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('eproduct_cat_id', __('crm.interest') . ' : ') !!}
                        {!! Form::select('eeproduct_cat_id', $categories, $followOportunitie->product_cat_id, ['class' => 'select2', 'id' => 'eeproduct_cat_id', 'style' => 'width: 100%;']) !!}
                    </div>
                </div>

            </div>
            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-check-input">
                            @php
                                $show = $followOportunitie->product_not_found == 1 ?: 'none';
                            @endphp
                            {{ __('crm.not_found') }}
                            {!! Form::checkbox('eechk_not_found', '1', $followOportunitie->product_not_found, ['id' => 'eechk_not_found', 'onClick' => 'eeshowNotFoundDesc()']) !!}
                        </label>
                        {{-- {!! Form::textarea('eeproducts_not_found_desc', $followOportunitie->products_not_found_desc, ['class' => 'form-control', 'id' => 'eeproducts_not_found_desc', 'style' => 'display: {{ $show }}']) !!} --}}
                        <textarea name="eeproducts_not_found_desc" class="form-control"
                            id="eeproducts_not_found_desc" cols="2" rows="2" style="display: {{ $show }}">
                            {{ $followOportunitie->products_not_found_desc }}
                        </textarea>
                    </div>
                </div>

                <div class="col-md-6">

                    <div class="form-group">
                        <label>@lang('crm.notes')</label>
                        {!! Form::textarea('eenotes', $followOportunitie->notes, ['class' => 'form-control', 'id' => 'eenotes', 'rows' => 2]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-check-input">
                            {{ __('crm.not_stock') }} {!! Form::checkbox('eechk_not_stock', '1', $followOportunitie->product_not_stock, ['id' => 'eechk_not_stock', 'onClick' => 'eshowNotStockDesc()']) !!}
                        </label>
                    </div>
                </div>

                <div class="col-md-6">
                    <label>@lang('crm.date')</label>
                        <input type="text" readonly id="edate" name="edate" class="form-control"
                            value="{{ @format_date($followOportunitie->date) }}" readonly>
                </div>


            </div>
            @php
                $display = $followOportunitie->product_not_stock == 1 ? '' : 'none';
            @endphp
            <div class="row" id="eediv_products" style="display: {{ $display }}">
                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                    <div class="form-group">

                        <label>@lang('accounting.location')</label>
                        {!! Form::select('locations', $locations, null, ['class' => 'form-control', 'id' => 'eelocations']) !!}
                    </div>

                </div>
                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">

                    <div class="form-group">
                        <label>@lang('product.products')</label>
                        <select name="eeproducts" id="eeproducts" class="form-control select2" style="width: 100%">
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
                    <table class="table">
                        <thead>
                            <th>Op</th>
                            <th>@lang('product.name')</th>
                            <th>@lang('product.sku')</th>
                            <th>@lang('product.actual_stock')</th>
                            <th>@lang('product.required_quantity')</th>
                        </thead>
                        <tbody id="eelist">
                            @foreach ($followOportunitie_product as $item)
                                {{-- Se recorren todos los contactos pertenecientes a el cliente seleccionado --}}
                                <tr class="selected" id="erow" style="height: 10px;">
                                    {!! Form::hidden('oporid[]', $item->idf) !!}
                                    <td><button type="button" class="btn btn-danger btn-xs remove-item"><i
                                                class="fa fa-times"></i></button></td>
                                    <td><input type="hidden" name="variation_id[]" value="{{ $item->id }}">
                                        {{ $item->name }}
                                    </td>
                                    <td>{{ $item->sku }}</td>
                                    <td>
                                        <input type="text" name="quantity[]" id="quantity"
                                            class="form-control form-control-sm" value="{{ $item->quantity }}"
                                            readonly="">
                                    </td>
                                    <td>
                                        <input type="number" id="required_quantity" name="required_quantity[]"
                                            class="form-control form-control-sm" min="1"
                                            value="{{ $item->required_quantity }}" required>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" id="btn-edit-follow-oportunity"
                class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default"
                id="btn-close-modal-edit-follow">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>
<script>
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    $(document).ready(function() {
        $('select.select2').select2();

    });

    $(document).on('click', '.remove-item', function() {
        Swal.fire({
            title: LANG.sure,
            text: '{{ __('messages.delete_content') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('messages.accept') }}",
            cancelButtonText: "{{ __('messages.cancel') }}"
        }).then((willDelete) => {
            if (willDelete.isConfirmed) {
                $(this).parent('td').parent('tr').slideDown(300, function() {
                    $(this).remove(); //En accion elimino el contacto de la Tabla
                });
                console.log("eliminado");
            } else {
                console.log('fue salvador');
            }
        });
    });

    $(document).on('change', 'select#eeproducts', function(event) {
        id = $("#eeproducts").val();
        if (id != 0) {
            eeaddProduct();

            $("#eeproducts").val(0).change();
        }
    });
    var econt = 0;
    var eproduct_ids = [];
    var erowCont = [];

    function eeaddProduct() {
        location_id = $("#eelocations").val();
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

                count = parseInt(jQuery.inArray(variation_id, eproduct_ids));
                if (count >= 0) {
                    Swal.fire({
                        title: "{{ __('product.product_already_added') }}",
                        icon: "error",
                    });
                } else {
                    eproduct_ids.push(variation_id);
                    erowCont.push(econt);
                    var erow = `
                            <tr class="selected" id="erow ${econt}"  style="height: 10px;">
                                <tr><input name="oporid[]" type="hidden" value="0">
                                <td><button type="button" class="btn btn-danger btn-xs remove-item"><i
                                    class="fa fa-times"></i></button></td>
                                <td><input type="hidden" name="variation_id[]" value="${variation_id}">${name}</td>
                                <td>${res.sku}</td>
                                <td>
                                    <input type="text" name="quantity[]" id="quantity${econt}"
                                        class="form-control form-control-sm" value="${quantity}" readonly="">
                                </td>
                                <td>
                                    <input type="number" id="required_quantity${econt}" name="required_quantity[]"
                                        class="form-control form-control-sm" min="1" value="1" required="">
                                </td>
                            </tr>
                    `
                    $("#eelist").append(erow);
                    econt++;
                }
            });
        } else {
            Swal.fire({
                title: "{{ __('crm.select_location') }}",
                icon: "error",
            });
        }
    }

    function eshowNotStockDesc() {
        if ($("#eechk_not_stock").is(":checked")) {
            $('#eediv_products').show();
        } else {
            $('#eediv_products').hide();
            $('#eelist').empty();
            econt = 0;
            eproduct_ids = [];
            erowCont = [];
        }
    }

    function eeshowNotFoundDesc() {
        if ($("#eechk_not_found").is(":checked")) {
            $('#eeproducts_not_found_desc').show();
            $("#eeproduct_cat_id").val('').change();
            $("#eeproduct_cat_id").prop('disabled', true);
        } else {
            $('#eeproducts_not_found_desc').hide();
            $('#eeproducts_not_found_desc').val('');
            $("#eeproduct_cat_id").prop('disabled', false);
        }
    }

    function eshowNotFoundDesc() {
        if ($("#echk_not_found").is(":checked")) {
            $('#eproducts_not_found_desc').show();
            $("#eproduct_cat_id").val('').change();
            $("#eproduct_cat_id").prop('disabled', true);
        } else {
            $('#eproducts_not_found_desc').hide();
            $('#eproducts_not_found_desc').val('');
            $("#eproduct_cat_id").prop('disabled', false);
        }
    }

</script>

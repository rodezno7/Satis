<div class="modal-dialog modal-dialog-centered" role="document" style="width: 80%">
    <form id="form-add-customer">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3>@lang('customer.add_customer')</h3>
                <div class="panel panel-default">
                    <div class="panel-heading">Información General</div>
                    <div class="panel-body">
                        <div class="row">
                            <input type="hidden" name="oportunity_id" value="{{ $oportunity->id }}">
                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.name')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        <input type="text" name="name" id="name" class="form-control"
                                            value="{{ $oportunity->name }}" aria-label="Left Align"
                                            placeholder="@lang('customer.name')">
                                    </div>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.dui')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-list-alt"></i></span>
                                        <input type="text" name="dni" id="dni" class="form-control"
                                            placeholder="@lang('customer.dui')">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.email')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-envelope"></i></span>
                                        <input type="text" name="email" id="email" class="form-control"
                                            value="{{ $oportunity->email }}" placeholder="@lang('customer.email')">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- direccion y Pais --}}
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.phone')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-earphone"></i></span>
                                        <input type="text" name="telphone" id="telphone"
                                            value="{{ $oportunity->contacts }}" class="form-control input_number"
                                            placeholder="@lang('customer.phone')">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                <div class="form-group">
                                    <label>@lang('customer.address')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-tags"></i></span>
                                        <input type="text" name="address" id="address" class="form-control"
                                            placeholder="@lang('customer.address')">
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.country')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class=" glyphicon glyphicon-flag"></i></span>
                                        {!! Form::select('country_id', $countries, $oportunity->country_id, ['class' => 'select2', 'id' => 'country_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- latitude, etc --}}
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.state')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-link"></i></span>
                                        {!! Form::select('state_id', $states, $oportunity->state_id, ['class' => 'select2', 'id' => 'state_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.city')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-link"></i></span>
                                        {!! Form::select('city_id', $cities, $oportunity->city_id, ['class' => 'select2', 'id' => 'city_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.latitude')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-map-marker"></i></span>
                                        <input type="text" name="latitude" id="latitude"
                                            class="form-control input_number" placeholder="@lang('customer.latitude')">
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.length')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-map-marker"></i></span>
                                        <input type="text" name="length" id="length" class="form-control input_number"
                                            placeholder="@lang('customer.length')">
                                    </div>

                                </div>
                            </div>



                        </div>
                        {{-- Contactos multiples --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <h4><b>@lang('customer.contactMult')</b></h4>
                                <table class="table table-responsive">
                                    <thead>
                                        <button type="button" id="add_reference" class="btn btn-info btn-xs"
                                            title='@lang(' contact.add_contact')'><i class="fa fa-plus"></i>
                                        </button>
                                        <tr>
                                            <th>
                                                @lang('contact.name')
                                            </th>
                                            <th>@lang('contact.mobile')</th>
                                            <th>@lang('contact.landline')</th>
                                            <th>@lang('lang_v1.email_address')</th>
                                            <th>@lang('contact.cargo')</th>
                                            <th id="dele" class="hidden">@lang('contact.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody id="referencesItems">
                                        <!--Ingreso un id al tbody-->
                                        <tr>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Información Fiscal</div>
                    <div class="panel-body">
                        {{-- Es contrinuyente? --}}
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.is_taxpayer')</label>
                                    {!! Form::checkbox('is_taxpayer', '1', false, ['id' => 'is_taxpayer', 'onClick' => 'showTaxPayer()']) !!}
                                </div>
                            </div>
                        </div>

                        <div id="div_taxpayer" style="display: none;">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('customer.business_name')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i
                                                    class="glyphicon glyphicon-user"></i></span>
                                            <input type="text" name="business_name" id="business_name"
                                                class="form-control" placeholder="@lang('customer.business_name')">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('customer.tax_number')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i
                                                    class="glyphicon glyphicon-list-alt"></i></span>
                                            <input type="text" name="tax_number" id="tax_number" class="form-control"
                                                placeholder="@lang('customer.tax_number')">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('customer.reg_number')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i
                                                    class="glyphicon glyphicon-list-alt"></i></span>
                                            <input type="text" name="reg_number" id="reg_number" class="form-control"
                                                placeholder="@lang('customer.reg_number')">
                                        </div>
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('customer.business_line')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i
                                                    class="glyphicon glyphicon-cog"></i></span>
                                            <input type="text" name="business_line" id="business_line"
                                                class="form-control" placeholder="@lang('customer.business_line')">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <div class="form-group">
                                        <label>@lang('customer.business_type')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i
                                                    class="glyphicon glyphicon-home"></i></span>
                                            {!! Form::select('business_type_id', $business_types, '', ['class' => 'select2', 'id' => 'business_type_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12" id="taxesP" style="display:none;">
                                    <div class="form-group">
                                        {!! Form::label('tax_group', __('tax_rate.taxes') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa">T</i>
                                            </span>
                                            <select name="tax_group_id" class="form-control select2">
                                                <option value="0">{{ __('lang_v1.none') }}</option>
                                                @foreach ($tax_groups as $tg)
                                                    <option value="{{ $tg->id }}">{{ $tg->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Información Comercial</div>
                    <div class="panel-body">
                        {{-- posee credito? --}}
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.allowed_credit')</label>
                                    {!! Form::checkbox('allowed_credit', '1', false, ['id' => 'allowed_credit', 'onClick' => 'showCredit()']) !!}
                                </div>
                            </div>

                        </div>
                        <div class="row" id="div_credit" style="display: none;">

                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.opening_balance')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></span>
                                        <input type="text" name="opening_balance" id="opening_balance"
                                            class="form-control input_number"
                                            placeholder="@lang('customer.opening_balance')">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.credit_limit')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></span>
                                        <input type="text" name="credit_limit" id="credit_limit"
                                            class="form-control input_number"
                                            placeholder="@lang('customer.credit_limit')">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.payment_terms')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-align-justify"></i></span>
                                        {!! Form::select('payment_terms_id', $payment_terms, '', ['class' => 'select2', 'id' => 'payment_terms_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                                    </div>

                                </div>
                            </div>
                        </div>
                        {{-- contact_mode_id --}}
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>@lang('customer.contact_mode')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-ok"></i></span>
                                        {!! Form::select('contact_mode_id', $contact_modes, '', ['class' => 'select2', 'id' => 'contact_mode_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                                    </div>

                                </div>
                            </div>

                            {{-- first_purchase_location --}}
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>@lang('customer.first_purchase_location')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-shopping-cart"></i></span>
                                        {!! Form::select('first_purchase_location', $business_locations, '', ['class' => 'select2', 'id' => 'first_purchase_location', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.customer_portfolio')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i
                                                class="glyphicon glyphicon-blackboard"></i></span>
                                        {!! Form::select('customer_portfolio_id', $customer_portfolios, '', ['class' => 'select2', 'id' => 'customer_portfolio_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.customer_group')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        <select name="customer_group_id" id="customer_group_id" class="select2"
                                            style="width: 100%;">
                                            <option value="">@lang('messages.please_select')</option>
                                            @foreach ($customer_groups as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            {{-- Lista de precios --}}
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label>@lang('customer.price_group')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        <select name="selling_price_group_id" id="selling_price_group_id"
                                            class="select2" style="width: 100%;">
                                            <option value="">@lang('messages.please_select')</option>
                                            @foreach ($prices_group as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <input type="submit" class="btn btn-primary" value="@lang('messages.save')" id="btn-add-customer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"
                    id="btn-close-modal-add-customer">@lang('messages.close')</button>
            </div>
        </div>
    </form>
</div>

<script>
    $('#dni').on('change', function() {
        let valor = $(this).val();
        let route = '/customers/verified_document/' + 'dni' + '/' + valor;
        console.log(route);
        $.get(route, function(data, status) {
            console.log(status);
            if (data.success == true) {
                $("#btn-add-customer").prop('disabled', true);
                // $('#msg').css('color', 'red');
                // $('#msg').text(data.msg);
                Swal.fire({
                    title: data.msg,
                    icon: "error",
                    timer: 3000,
                    showConfirmButton: true,
                });
            } else {
                $("#btn-add-customer").prop('disabled', false);
                // $('#msg').css('color', 'green');
                // $('#msg').text(data.msg);
                Swal.fire({
                    title: data.msg,
                    timer: 3000,
                    icon: "success",
                });
            }
        });
    });

    $('#reg_number').on('change', function() {
        let valor = $(this).val();
        let route = '/customers/verified_document/' + 'reg_number' + '/' + valor;
        console.log(route);
        $.get(route, function(data, status) {
            console.log(status);
            if (data.success == true) {
                $("#btn-add-customer").prop('disabled', true);
                // $('#msgR').css('color', 'red');
                // $('#msgR').text(data.msg);
                Swal.fire({
                    title: data.msg,
                    icon: "error",
                    timer: 3000,
                    showConfirmButton: true,
                });
            } else {
                $("#btn-add-customer").prop('disabled', false);
                // $('#msgR').css('color', 'green');
                // $('#msgR').text(data.msg);
                Swal.fire({
                    title: data.msg,
                    timer: 3000,
                    icon: "success",
                });
            }
        });
    });

    $('select#business_type_id').on('change', function() {
        let valor = $(this).val();
        if (valor == 4) {
            $('#taxesP').show();
        }
        if (valor != 4) {
            $('#taxesP').hide();
        }
    });

    function showTaxPayer() {
        if ($("#is_taxpayer").is(":checked")) {
            $('#div_taxpayer').show();
            $("#reg_number").val('');
            $("#tax_number").val('');
            $("#business_line").val('');
            $('#msgR').text("");
            $('#business_name').val('');
            $("#dni").prop('required', false);
            $("#btn-add-customer").prop('disabled', false);
            $('#reg_number').prop('required', true);
            setTimeout(function() {
                    $('#reg_number').focus();
                },
                800);
        } else {
            $('#div_taxpayer').hide();
            $("#btn-add-customer").prop('disabled', false);
            $("#reg_number").val('');
            $("#tax_number").val('');
            $("#business_line").val('');
            $('#msg').text("");
            $('#business_name').val('');
            $("#dni").prop('required', true);
        }
    }

    $("button#add_reference").on('click', function() {
        console.log('di click :(');
        let newtr1 = `
                                <tr>
                                    <input name="contactid[]" type="hidden" value="0">
                                    <td><input  class="form-control" name="contactname[]"  value="" required /></td>
                                    <td><input  class="form-control input_number" name="contactphone[]"  value="" required /></td>
                                    <td><input  class="form-control input_number" name="contactlandline[]"  value="" required /></td>
                                    <td><input type="email" class="form-control" name="contactemail[]"  value="" required /></td>
                                    <td><input  class="form-control" name="contactcargo[]"  value="" required /></td>
                                    <td><button type="button" class="btn btn-danger btn-xs remove-item"><i class="fa fa-times"></i></button></td>
                                </tr>
                            `;
        $('#referencesItems').append(newtr1);
        $('#dele').addClass("show");
        $('.remove-item').click(function(e) {
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
                        $(this)
                            .remove(); //En accion elimino el contacto de la Tabla
                    });
                    console.log("eliminado");
                } else {
                    console.log('fue salvador');
                }
            });
        });
    });

</script>

<div class="modal-dialog modal-xl" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('ExpenseController@update', [$expense->id]), 'method' => 'PUT', 'id' => 'edit_expense_form', 'files' => true]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('expense.edit_expense')</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                {{-- expense_category_id --}}
                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('expense_categories', __('expense.expense_type') . ':') !!}<span style="color: red">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-check-square-o"></i>
                            </span>
                            {!! Form::select('expense_category_id',[$expense->expense_category_id => $expense->expense_category->name], $expense->expense_category_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'style' => 'width:100%', 'required', 'id' => 'expense_search']) !!}
                        </div>
                    </div>
                </div>

                {{-- name --}}
                <div class="col-sm-6 col-md-6 col-lg-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('name', __('expense.expense_account')) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-cc"></i>
                            </span>
                            <input type="text" value="{{ $expense->expense_category->catalogue->code . ' ' . $expense->expense_category->catalogue->name }}" name="account_name" readonly id="account_name" placeholder="@lang('expense.expense_account')" class="form-control">
                        </div>
                    </div>
                </div>

                {{-- location_id --}}
                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('location_id', __('business.location')) !!}
                        <span style="color: red">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user-secret"></i>
                            </span>
                            {!! Form::select("location_id", $business_locations, $expense->location_id, ["class" => 'form-control', 'placeholder' => __('messages.please_select'), 'required']) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- contact_id --}}
                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('proveedor_id', __('expense.expense_provider') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user-circle"></i>
                            </span>
                            {!! Form::select('contact_id',[$expense->contact_id => $expense->contact->contact_id], $expense->contact_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'style' => 'width:100%', 'id' => 'supplier_id']) !!}
                            <input type="hidden" id="is_exempt" value="{{ $expense->contact->is_exempt }}">
                        </div>
                    </div>
                </div>

                {{-- supplier_name --}}
                <div class="col-sm-6 col-md-6 col-lg-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('name', __('expense.expense_provider_name')) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user-secret"></i>
                            </span>
                            <input type="text" name="supplier_name" value="{{ $expense->contact->name }}" readonly id="supplier_name" placeholder="@lang('expense.expense_provider_name')" class="form-control">
                            <input type="hidden" id="tax_percent" value="{{ $tax_percent }}">
                            <input type="hidden" id="tax_min_amount" value="{{ $tax_min_amount }}">
                            <input type="hidden" id="tax_max_amount" value="{{ $tax_max_amount }}">
                        </div>
                    </div>
                </div>

                {{-- transaction_date --}}
                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('transaction_date', __('messages.date') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input type="text" value="{{ @format_date($expense->transaction_date) }}" name="transaction_date" required class="form-control text-center" id="expense_transaction_date" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- document_date --}}
                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('document_date', __('retention.document_date') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input type="text" value="{{ @format_date($expense->document_date) }}" name="document_date" required class="form-control text-center" id="expense_document_date" readonly>
                        </div>
                    </div>
                </div>

                {{-- document_types_id --}}
                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('document_types_id', __('expense.document_type') . ':') !!}<span style="color: red">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-file-text-o"></i>
                            </span>
                            {!! Form::select('document_types_id', $document, $expense->document_types_id, ['class' => 'form-control select2', 'required', 'style' => 'width:100%', 'placeholder' => __('messages.please_select')]) !!}
                        </div>
                    </div>
                </div>

                {{-- ref_no --}}
                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('ref_no', __('expense.document_n') . ':') !!}<span style="color: red">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-hashtag"></i>
                            </span>
                            {!! Form::text('ref_no',$expense->ref_no, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                </div>

                {{-- serie --}}
                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('serie', __('accounting.serie') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-hashtag"></i>
                            </span>
                            {!! Form::text('serie',$expense->serie, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                {{-- payment_condition --}}
                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                    <div class="form-group">
                        {!! Form::label("payment_condition", __("lang_v1.payment_condition")) !!} <span style="color: red">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-credit-card-alt"></i>
                            </span>
                            <select name="payment_condition" id="payment_condition" class="form-control" required>
                                @if ($expense->payment_condition == 'cash')
                                    <option value="{{ $expense->payment_condition }}">{{ $expense->payment_condition }}
                                    </option>
                                    <option value="credit">@lang('sale.credit')</option>
                                @else
                                    <option value="{{ $expense->payment_condition }}">{{ $expense->payment_condition }}
                                    </option>
                                    <option value="cash">@lang('sale.cash')</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                {{-- payment_term_id --}}
                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                    <div class="form-group">
                        {!! Form::label("payment_term_id", __("purchase.credit_terms")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-list-ol"></i>
                            </span>
                            {!! Form::select('payment_term_id', $payment_term, $expense->payment_term_id, ['class' => 'form-control select2', 'required', 'disabled','style' => 'width:100%', 'placeholder' => __('messages.please_select')]) !!}
                        </div>
                    </div>
                </div>

                {{-- total_before_tax --}}
                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                    <div class="form-group">
                        <label for="">@lang('tax_rate.amount') <small>(@lang('expense.less_taxes'))</small></label><span style="color: red">*</span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-usd"></i>
                            </span>
                            {!! Form::text('total_before_tax', $expense->total_before_tax, ['class' => 'form-control input_number', 'id' => 'ammount', 'placeholder' => __('sale.total_amount'), 'required']) !!}
                        </div>
                    </div>
                </div>

                {{-- tax_group_id --}}
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('tax_group', __('tax_rate.tax_type') . ':') !!}
                        <div class="input-group">
                            @php
                                $dis = $expense->contact->is_exempt == 1 ? 'disabled' : '';
                            @endphp
                            <span class="input-group-addon">
                                <i class="fa fa-percent"></i>
                            </span>
                            <select name="tax_group_id" id="tax_percent_group" class="form-control select2" style="width: 100%;" {{ $dis }}>
                                <option value="nulled">@lang('messages.please_select')</option>
                                @foreach ($tax_groups as $tg)
                                <option data-tax_percent="{{ $tg['percent'] }}"
                                    value="{{ $tg['id'] }}" {{ $tg['id'] == $expense->tax_id ? 'selected' : '' }}> {{ $tg['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- exempt_amount --}}
                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                    <div class="form-group">
                        <label for="">@lang('tax_rate.exempt_amount')</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" id="enable_exempt_amount" @if ($expense->exempt_amount > 0) checked="true" @endif>
                            </span>
                            {!! Form::text('exempt_amount', $expense->exempt_amount > 0 ? $expense->exempt_amount : null, [
                                'class' => 'form-control input_number',
                                'id' => 'exempt_amount',
                                'placeholder' => __('tax_rate.exempt_amount'),
                                $expense->exempt_amount > 0 ? '' : 'readonly'
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- perception_amount --}}
                <div class="col-sm-3" style="display: {{ $tax_percent > 0 ? 'block' : 'none' }};" id="perception_div">
                    <div class="form-group">
                        <label for="perception_amount">@lang('tax_rate.perception')</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-usd"></i>
                            </span>
                            {!! Form::text("perception_amount", $expense->tax_amount ? $expense->tax_amount : null, ["class" => "form-control input_number", "id" => "perception_amount", "placeholder" => __('tax_rate.perception'), "readonly"]) !!}
                        </div>
                    </div>
                </div>

                {{-- tax_amount --}}
                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('iva', __('expense.taxes')) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-usd"></i>
                            </span>
                            {!! Form::text('tax_amount', $expense->tax_group_amount, ['class' => 'form-control', 'id' => 'iva', 'readonly', 'required']) !!}
                        </div>
                    </div>
                </div>

                {{-- final_total --}}
                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('final_total', __('sale.total_amount_expense')) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-usd"></i>
                            </span>
                            {!! Form::text('final_total', $expense->final_total, ['class' => 'form-control', 'id' => 'final_total', 'readonly', 'required']) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- document --}}
                <div class="col-sm-3 col-md-6 col-lg-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                        {!! Form::file('document', ['id' => 'upload_document', 'size' => 1]) !!}
                        <p style="font-size: 10px;" class="help-block">@lang('purchase.max_file_size', ['size' =>
                            (config('constants.document_size_limit') / 1000000)])</p>
                    </div>
                </div>

                {{-- additional_notes --}}
                <div class="col-sm-6 col-md-6 col-lg-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('additional_notes', __('expense.expense_note') . ':') !!}
                        {!! Form::textarea("additional_notes", $expense->additional_notes, ["class" => "form-control", "rows" => "3", "style" => "resize: none;"]) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" data-dismiss="modal" aria-label="Close"
                class="btn btn-default">@lang('messages.close')</button>
        </div>

        {!! Form::close() !!}
    </div>
</div>

<script>
    $(document).ready(function() {
        fileinput_setting = {
            'showUpload': false,
            'showPreview': false,
            'browseLabel': LANG.file_browse_label,
            'removeLabel': LANG.remove
        };
        $("#upload_document").fileinput(fileinput_setting);
        $('select.select2').select2();
        
        $('select#tax_percent_group, input#ammount, input#exempt_amount, select#supplier_id').on('change', function () {
            let ammount = __read_number($("input#ammount"));
            let exempt_amount = $("input#enable_exempt_amount").prop("checked") ? (__read_number($("input#exempt_amount")) > 0 ? __read_number($("input#exempt_amount")) : 0) : 0;
            let tax_supplier_percent = ("select#supplier_id :selected") && $("input#tax_percent").val() != "" ? parseFloat($("input#tax_percent").val()) : 0;
            let perception = $("input#perception_amount");

            let tax_supplier = 0;

            if (tax_supplier_percent != "0") {
                if (ammount > 0) {
                    let min_amount = $("select#supplier_id :selected").val() ? parseFloat($("input#tax_min_amount").val()) : 0;
                    let max_amount = $("select#supplier_id :selected").val() ? parseFloat($("input#tax_max_amount").val()) : 0;

                    tax_supplier_percent = parseFloat(tax_supplier_percent);

                    tax_supplier = calc_contact_tax(ammount, min_amount, max_amount, tax_supplier_percent);
                    __write_number(perception, tax_supplier, false, 4);
                }

            } else{
                __write_number(perception, tax_supplier, false, 4);
            }

            if ($('select#tax_percent_group').val() != "nulled") {
                let percent = $('select#tax_percent_group :selected').data('tax_percent');
                let total = (ammount * ((percent / 100) + 1)) + exempt_amount + tax_supplier;
                let impuesto = total - ammount - exempt_amount - tax_supplier;

                __write_number($("input#final_total"), total, false, 4);
                __write_number($("input#iva"), impuesto, false, 4);

            }else if($('input#ammount') != "" || $('input#exempt_amount') != ""){
                __write_number($("input#final_total"), (ammount + exempt_amount + tax_supplier), false, 4);
                $("input#iva").val('0.0');

            } else {
                $("input#final_total").val('0.0');
                $("input#iva").val('0.0');
            }
        });

        $("#payment_condition").on('change', function() {
            if ($("#payment_condition").val() == "credit") {
                $('#payment_term_id').attr('disabled', false);
            } else {
                $('#payment_term_id').attr('disabled', true);
            }
        });
        
        $('input#expense_transaction_date').datetimepicker({
            format: moment_date_format,
            ignoreReadonly: true
            
        }).on("dp.change", function (e) {
            if (e.oldDate !== e.date) {
                var date = moment(e.date).format('DD/MM/YYYY');
                $.ajax({
                    type: 'post',
                    url: '/purchases/is-closed',
                    data: {date: date},
                    success: function(data){
                        if(parseInt(data) > 0){
                            swal(LANG.notice, LANG.month_closed, "error");
                        }
                    }
                });
            };
        });

        $('input#expense_document_date').datetimepicker({
            format: moment_date_format,
            ignoreReadonly: true
            
        });

        $(document).on('change', 'input#enable_exempt_amount', function () {
            let exempt_amount = $('input#exempt_amount');

            if ($(this).prop('checked')) {
                exempt_amount.prop('readonly', false);

            } else {
                exempt_amount.prop('readonly', true);
                exempt_amount.val(null).change();
            }
        });

        $(document).on('change', $(this).find('select#supplier_id'), function () {
            var perception = $('div#perception_div');
            var tax_percent = $(this).find('input#tax_percent').val();

            if (tax_percent == 0) {
                perception.hide();
            } else {
                perception.show();
            }
        });
    });

    // get suppliers
    $('#supplier_id').select2({
        ajax: {
            url: '/expenses/get_suppliers',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        },
        minimumInputLength: 1,
        escapeMarkup: function(m) {
            return m;
        },
        templateResult: function(data) {
            if (!data.id) {
                return data.text;
            }
            var html = data.text + ' (<b>' + LANG.code + ': </b>' + data.contact_id + ' - <b>' + LANG
                .business + ': </b>' + data.business_name + ')';
            return html;
        },
        templateSelection: function(data) {
            if (!data.id) {
                $('#supplier_name').val('');
                return data.text;
            }
            // If it's a new supplier
            if (!data.contact_id) {
                return data.text;
                // If a provider has been selected
            } else {
                $('#supplier_name').val(data.text);
                $("input#is_exempt").val(data.is_exempt);
                $("input#tax_percent").val(data.tax_percent);
                $("input#tax_min_amount").val(data.tax_min_amount);
                $("input#tax_max_amount").val(data.tax_max_amount);
                setTimeout(() => {
                    recalculate(); 
                }, 500);
                return data.contact_id || data.text;
            }
        },
    });

    //get account
    $('#expense_search').select2({
        ajax: {
            url: '/expenses/get_contacts',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        },
        minimumInputLength: 1,
        escapeMarkup: function(m) {
            return m;
        },
        templateResult: function(data) {
            if (!data.id) {
                return data.text;
            }
            var html = data.text + ' (<b>' + LANG.code + ': </b>' + data.ide + ' - <b>' + LANG
                .account + ': </b>' + data.business_name + ')';
            return html;
        },
        templateSelection: function(data) {
            if (!data.id) {
                $('#account_name').val('');
                return data.text;
            }
            // If it's a new supplier
            if (!data.ide) {
                return data.text;
                // If a provider has been selected
            } else {
                let p = $('#account_name').val(data.coc + " " + data.business_name);
                return data.text;
            }
        },
    });

    function recalculate(){
        let is_exempt = $('input#is_exempt').val();
        let ammount = $("#ammount").val();
        let exempt_amount = $('input#enable_exempt_amount').prop('checked') ? (__read_number($('input#exempt_amount')) > 0 ? __read_number($('input#exempt_amount')) : 0) : 0;

        if(is_exempt == 0){
            $('select#tax_percent_group').attr('disabled', false);
            if($('input#ammount') != "" || $('input#exempt_amount') != ""){
                __write_number($("#final_total"), ammount + exempt_amount);
                $("#iva").val('0.0');
            } else {
                $("#final_total").val('0.0');
                $("#iva").val('0.0');
            }
        }else{
            $('select#tax_percent_group').attr('disabled', true);
            $('select#tax_percent_group').val('nulled').change();
            $("#iva").val('0.0');
        }
    }

    function calc_contact_tax(amount, min_amount, max_amount, tax_percent){
        var tax_amount = 0;

        // If has min o max amount
        if (min_amount || max_amount) {
            // if has min and max amount
            if (min_amount && max_amount) {
                if (amount >= min_amount && amount <= max_amount) {
                    tax_amount = amount * tax_percent;
                }
            // If has only min amount
            } else if (min_amount && ! max_amount) {
                if (amount >= min_amount) {
                    tax_amount = amount * tax_percent;
                }
            // If has only max amount
            } else if (! min_amount && max_amount) {
                if (amount <= max_amount) {
                    tax_amount = amount * tax_percent;
                }
            }
        // If has none tax
        } else {
            tax_amount = amount * tax_percent;
        }

        return tax_amount;
    }
</script>

<div class="modal-dialog modal-xl" role="dialog">
  <div class="modal-content" style="border-radius: 10px;">

      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">@lang('expense.add_expense')</h4>
      </div>

      <div class="modal-body">
          <input type="hidden" name="location_id" value="{{ $business_locations->id }}">

          <div class="row">
              <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                  <div class="form-group">
                      {!! Form::label('expense_categories', __('expense.expense_type') . ':') !!}<span style="color: red">*</span>
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-check-square-o"></i>
                          </span>
                          {!! Form::select('expense_category_id', [], null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'style' => 'width:100%', 'required', 'id' => 'expense_search']) !!}
                      </div>
                  </div>
              </div>
              <div class="col-sm-6 col-md-6 col-lg-6 col-xs-12">
                  <div class="form-group">
                      {!! Form::label('name', __('expense.expense_account')) !!}
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-cc"></i>
                          </span>
                          <input type="text" name="account_name" readonly id="account_name" placeholder="@lang('expense.expense_account')" class="form-control">
                      </div>
                  </div>
              </div>
          </div>

          <div class="row">
              <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                  <div class="form-group">
                      {!! Form::label('proveedor_id', __('expense.expense_provider') . ':') !!}
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-user-circle"></i>
                          </span>
                          {!! Form::select('contact_id', [], null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'style' => 'width:100%', 'id' => 'supplier_id']) !!}
                      </div>
                  </div>
              </div>

              <div class="col-sm-6 col-md-6 col-lg-6 col-xs-12">
                  <div class="form-group">
                      {!! Form::label('name', __('expense.expense_provider_name')) !!}
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-user-secret"></i>
                          </span>
                          <input type="text" name="supplier_name" readonly id="supplier_name" placeholder="@lang('expense.expense_provider_name')" class="form-control">
                          <input type="hidden" id="is_exempt" value="0">
                      </div>
                  </div>
              </div>

              <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                  <div class="form-group">
                      {!! Form::label('transaction_date', __('messages.date') . ':') !!}
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                          </span>
                          <input type="text" value="{{ @format_date('now') }}" name="transaction_date" readonly id="expense_transaction_date" required class="form-control text-center">
                      </div>
                  </div>
              </div>
          </div>
          <div class="row">
              <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                  <div class="form-group">
                      {!! Form::label('document_types_id', __('expense.document_type') . ':') !!}<span style="color: red">*</span>
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-file-text-o"></i>
                          </span>
                          {!! Form::select('document_types_id', $document, null, ['class' => 'form-control select2', 'required', 'style' => 'width:100%', 'placeholder' => __('messages.please_select')]) !!}
                      </div>
                  </div>
              </div>
              <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                  <div class="form-group">
                      {!! Form::label('ref_no', __('expense.document_n') . ':') !!}<span style="color: red">*</span>
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-hashtag"></i>
                          </span>
                          {!! Form::text('ref_no', null, ['class' => 'form-control', 'required']) !!}
                      </div>
                  </div>
              </div>

              <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                  <div class="form-group">
                      {!! Form::label("payment_condition", __("lang_v1.payment_condition")) !!} <span style="color: red">*</span>
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-credit-card-alt"></i>
                          </span>
                          {!! Form::select("payment_condition", $payment_condition, null, ["class" => "form-control select2", "id" => "payment_condition",
                          'required', "placeholder" => __("lang_v1.payment_condition"), "style" => "width: 100%;"]) !!}
                      </div>
                  </div>
              </div>
              <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                  <div class="form-group">
                      {!! Form::label("payment_term_id", __("purchase.credit_terms")) !!}
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-list-ol"></i>
                          </span>
                          {!! Form::select("payment_term_id", $payment_terms, null, ["class" => "form-control select2", "id" => "payment_term_id", "disabled", "style" => "width: 100%;"]) !!}
                      </div>
                  </div>
              </div>
          </div>
          <div class="row">
              <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                  <div class="form-group">
                      <label for="">@lang('tax_rate.amount') <small>(@lang('expense.less_taxes'))</small></label><span style="color: red">*</span>
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-usd"></i>
                          </span>
                          {!! Form::text('total_before_tax', null, ['class' => 'form-control input_number', 'id' => 'ammount', 'placeholder' => __('sale.total_amount'), 'required']) !!}
                      </div>
                  </div>
              </div>

              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                  <div class="form-group">
                      {!! Form::label('tax_group', __('tax_rate.tax_type') . ':') !!}
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-percent"></i>
                          </span>
                          <select name="tax_group_id" id="tax_percent_group" class="form-control select2" style="width: 100%;">
                              <option value="nulled">@lang('messages.please_select')</option>
                              @foreach ($tax_groups as $tg)
                              <option data-tax_percent="{{ $tg['percent'] }}" value="{{ $tg['id'] }}">{{ $tg['name'] }}</option>
                              @endforeach
                          </select>
                      </div>
                  </div>
              </div>
              <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                  <div class="form-group">
                      {!! Form::label('iva', __('expense.taxes')) !!}
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-usd"></i>
                          </span>
                          {!! Form::text('tax_amount', '0.0', ['class' => 'form-control', 'id' => 'iva', 'readonly', 'required']) !!}
                      </div>
                  </div>
              </div>
              <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
                  <div class="form-group">
                      {!! Form::label('final_total', __('sale.total_amount_expense')) !!}
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-usd"></i>
                          </span>
                          {!! Form::text('final_total', '0.0', ['class' => 'form-control', 'id' => 'final_total', 'readonly', 'required']) !!}
                      </div>
                  </div>
              </div>
          </div>

          <div class="row">
              <div class="col-sm-6 col-md-6 col-lg-6 col-xs-12">
                  <div class="form-group">
                      {!! Form::label('additional_notes', __('expense.expense_note') . ':') !!}
                      <textarea name="additional_notes" id="additional_notes" class="form-control" style="resize: none;" cols="20" rows="3"></textarea>
                  </div>
              </div>
              <div class="col-sm-3 col-md-6 col-lg-6 col-xs-12">
                  <div class="form-group">
                      {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                      {!! Form::file('document', ['id' => 'upload_document', 'size' => 1]) !!}
                      <p style="font-size: 10px;" class="help-block">@lang('purchase.max_file_size', ['size' =>
                          (config('constants.document_size_limit') / 1000000)])</p>
                  </div>
              </div>
          </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="add_single_expense">@lang('expense.add')</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
      </div>

  </div>
</div>

<script src="{{ asset('plugins/bootstrap-fileinput/fileinput.min.js?v=' . $asset_v) }}"></script>

<script>
  $(document).ready(function() {
      fileinput_setting = {
          'showUpload': false,
          'showPreview': true,
          'browseLabel': LANG.file_browse_label,
          'removeLabel': LANG.remove
      };
      $("#location_id").hide();
      $('select.select2').select2();

      $('select#tax_percent_group, input#ammount').on('change', function() {
          let ammount = $("#ammount").val();
          if ($('select#tax_percent_group').val() != "nulled") {
              let percent = $('select#tax_percent_group :selected').data('tax_percent');
              let total = ammount * ((percent / 100) + 1);
              let impuesto = total - ammount;
              __write_number($("input#final_total"), total, false, 4);
              __write_number($("input#iva"), impuesto, false, 4);
          }else if($('input#ammount') != ""){
              __write_number($("input#final_total"), ammount, false, 4);
              $("input#iva").val('0.0');
          } else {
              $("input#final_total").val('0.0');
              $("input#iva").val('0.0');
          }
      });

        /** Validate purchase date */
        $(document).on('change', 'input#expense_transaction_date', function(){
            var date = $(this).val();

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
        });

      $('input#expense_transaction_date').datetimepicker({
          format: moment_date_format,
          ignoreReadonly: true
      });

      $("#upload_document").fileinput(fileinput_setting);

      //enable and disabled Credit Terms
      $("#payment_condition").on('change', function() {
          if ($("#payment_condition").val() == "credit") {
              $('#payment_term_id').attr('disabled', false);
          } else {
              $('#payment_term_id').attr('disabled', true);
              $('#payment_term_id').val("").change();
          }
      });

  });

  // Get suppliers
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
              setTimeout(() => {
                  recalculate(); 
              }, 500);
              return data.contact_id || data.text;
          }
      },
  });

  // Get expense categories
  $('#expense_search').select2({
      ajax: {
          url: '/expenses/get_categories',
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
          var html = data.text + ' (<b>' + LANG.code + ': </b>' + data.cat_id + ' - <b>' + LANG
              .account + ': </b>' + data.account_name + ')';
          return html;
      },
      templateSelection: function(data) {
          if (!data.id) {
              $('#account_name').val('');
              return data.text;
          }
          
          if (!data.cat_id) {
            return data.text;

          } else {
              let p = $('#account_name').val(data.code + " " + data.account_name);
              return data.text;
          }
      },
  });

  function recalculate() {
      let is_exempt = $('input#is_exempt').val();
      let ammount = $("#ammount").val();
      if(is_exempt == 0){
          $('select#tax_percent_group').attr('disabled', false);
          if($('input#ammount') != ""){
              __write_number($("#final_total"), ammount);
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
</script>
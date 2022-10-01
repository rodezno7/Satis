<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang('expense.purchases_and_expenses')</h4>
      </div>

      <div class="modal-body">
        @if(empty($bank_transaction_id))
          <div class="box-tools pull-right" style="margin-bottom: 5px;">
            <button type="button" class="btn btn-primary" id="add_expense"><i class="fa fa-plus"></i> @lang('messages.add')</button>
          </div>
        @endif
        @if(!empty($bank_transaction_id))
          <div class="col-lg-6 col-md-8 col-sm-12">
            <div class="form-group">
              {!! Form::select("due_expenses", [], null,
                ["class" => "form-control", "id" => "purchase_and_expenses_due",
                "placeholder" => __("expense.purchases_and_expenses_due")]) !!}
            </div>
          </div>
          {!! Form::hidden("bank_transaction_id", $bank_transaction_id, ["id" => "bank_transaction_id"]) !!}
        @endif
        <table class="table" id="showed_table">
          <thead>
            <tr>
              <th style="width: 25%;">@lang('purchase.supplier')</th>
              <th style="width: 15%;">@lang('expense.ref_no')</th>
              <th style="width: 15%;">@lang('messages.date')</th>
              <th>@lang('accounting.subtotal')</th>
              <th style="width: 10%;">@lang('tax_rate.taxes')</th>
              <th style="width: 15%;">@lang('expense.total')</th>
              <th>@lang('messages.action')</th>
            </tr>
          </thead>
          <tbody>
            
          </tbody>
        </table>
        <table id="hidden_table" style="display: none;">
          <thead>
            <th>@lang('purchase.supplier')</th>
            <th>@lang('expense.ref_no')</th>
            <th>@lang('messages.date')</th>
            <th>@lang('lang_v1.sub_total')</th>
            <th>@lang('tax_rate.taxes')</th>
            <th>@lang('tax_rate.tax_amount')</th>
            <th>@lang('tax_rate.exempt_amount')</th>
            <th>@lang('tax_rate.perception')</th>
            <th>@lang('expense.total')</th>
            <th>@lang('purchase.attach_document')</th>
            <th>@lang('expense.expense_note')</th>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="save_expenses" data-dismiss="modal">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
    
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  
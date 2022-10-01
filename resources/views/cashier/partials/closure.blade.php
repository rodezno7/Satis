<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      {!! Form::open(['url' => action('CashierClosureController@postCashierClosure'), 'method' => 'post', 'id' => 'cashier_closure_form']) !!}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">@lang( 'cash_register.cashier_closure' ) ( {{ \Carbon::createFromFormat('Y-m-d H:i:s', $cashier_closure->open_date)->format('d/m/Y H:i:s') }} - {{ \Carbon::now()->format('d/m/Y H:i:s') }} )</h3>
      </div>
  
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
            {!! Form::hidden("cashier_closure_id", $cashier_closure->id) !!}
            {!! Form::hidden("cashier_id", $cashier_id) !!}
            <table class="table">
              <tr>
                <td>@lang('cash_register.initial_cash')</td>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ @num_format($closure_details->initial_cash_amount) }}</span>
                </td>
                <td>@lang('cash_register.cash_payment'): </th>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ @num_format($closure_details->cash_amount) }}</span>
                </td>
              </tr>
              <tr>
                <td>@lang('cash_register.check_payment'): </td>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ @num_format($closure_details->check_amount) }}</span>
                </td>
                <td>@lang('cash_register.card_payment'): </td>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ @num_format($closure_details->card_amount) }}</span>
                </td>
              </tr>
              <tr>
                <td>@lang('cash_register.bank_transfers'): </td>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ @num_format($closure_details->bank_transfer_amount) }}</span>
                </td>
                <td>@lang('cash_register.credits')</td>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ @num_format($closure_details->credit_amount) }}</span>
                </td>
              </tr>
              <tr class="success">
                <th>@lang('cash_register.returns')</th>
                <td>
                  <b><span class="display_currency" data-currency_symbol="true">{{ @num_format($closure_details->return_amount) }}</span></b>
                </td>
                <th>@lang('cash_register.total_sales'): </th>
                <th>
                  <span class="display_currency" data-currency_symbol="true">{{ @num_format($closure_details->final_total - $closure_details->return_amount) }}</span>
                </th>
              </tr>
            </table>
          </div>
        </div>
  
        <div class="row">
          <div class="col-sm-3">
            <div class="form-group">
                {!! Form::label('total_amount_cash', __( 'cash_register.total_cash' ) . ':*') !!}
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa">$</i>
                  </span>
                  {!! Form::text('total_cash_amount', round($closure_details->cash_amount, 4), ['class' => 'form-control closure_input input_number',
                    'required', 'id' => 'total_cash_amount', 'placeholder' => __( 'cash_register.total_cash' ) ]); !!}
                </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              {!! Form::label('total_amount_card', __( 'cash_register.total_card' ) . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa">$</i>
                </span>
                {!! Form::text('total_card_amount', round($closure_details->card_amount, 4), ['class' => 'form-control closure_input input_number',
                  'required', 'id' => 'total_card_amount', 'placeholder' => __( 'cash_register.total_card' ) ]); !!}
              </div>
            </div>
          </div> 
          <div class="col-sm-3">
            <div class="form-group">
              {!! Form::label('total_check', __( 'cash_register.total_check' ) . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa">$</i>
                </span>
                {!! Form::text('total_check_amount', round($closure_details->check_amount, 4), ['class' => 'form-control closure_input input_number',
                  'required', 'id' => 'total_check_amount', 'placeholder' => __( 'cash_register.total_check' ) ]); !!}
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              {!! Form::label('total_tranfer', __( 'cash_register.total_transfer' ) . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa">$</i>
                </span>
                {!! Form::text('total_bank_transfer_amount', round($closure_details->bank_transfer_amount, 4), ['class' => 'form-control closure_input input_number',
                  'required', 'id' => 'total_bank_transfer_amount', 'placeholder' => __( 'cash_register.total_transfer' ) ]); !!}
              </div>  
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              {!! Form::label('total_credit', __( 'cash_register.total_credit' ) . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa">$</i>
                </span>
                {!! Form::text('total_credit_amount', round($closure_details->credit_amount, 4), ['class' => 'form-control closure_input input_number',
                  'required', 'id' => 'total_credit_amount', 'placeholder' => __( 'cash_register.total_credit' ) ]); !!}
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              {!! Form::label('total_return', __( 'cash_register.total_return' ) . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa">$</i>
                </span>
                {!! Form::text('total_return_amount', round($closure_details->return_amount, 4), ['class' => 'form-control closure_input input_number',
                  'required', 'id' => 'total_return_amount', 'placeholder' => __( 'cash_register.total_return' ) ]); !!}
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              {!! Form::label('differences', __( 'cash_register.differences' ) . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa">$</i>
                </span>
                {!! Form::text('differences', 0, ['class' => 'form-control input_number', 'required',
                  'id' => 'differences', 'readonly', 'placeholder' => __( 'cash_register.differences' ) ]); !!}
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              {!! Form::label('total_sales', __( 'cash_register.total_sales' ) . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa">$</i>
                </span>
                {!! Form::text('total_physical_amount', round($closure_details->final_total - $closure_details->return_amount, 4),
                  ['class' => 'form-control input_number', 'id' => 'total_physical_amount', 'required', 'readonly', 'placeholder' => __( 'cash_register.total_sales' ) ]); !!}
              </div>
              {!! Form::hidden('total_system_amount', round($closure_details->final_total - $closure_details->return_amount, 4),
                ["id" => "total_system_amount"]) !!}
            </div>
          </div>
          <div class="col-sm-9">
            <div class="form-group">
              {!! Form::label('closing_note', __( 'cash_register.closing_note' ) . ':') !!}
                {!! Form::textarea('closing_note', null, ['class' => 'form-control', 'placeholder' => __( 'cash_register.closing_note' ), 'rows' => 2 ]); !!}
            </div>
          </div>
        </div> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.cancel' )</button>
        <button type="submit" class="btn btn-primary">@lang( 'cash_register.close_register' )</button>
      </div>
      {!! Form::close() !!}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
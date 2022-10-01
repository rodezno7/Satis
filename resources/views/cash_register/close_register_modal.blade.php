<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    
    {!! Form::open(['url' => action('CashRegisterController@postCloseRegister'), 'method' => 'post' ]) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h3 class="modal-title">@lang( 'cash_register.current_register' ) ({{ @format_date($close_date) }})</h3>
    </div>

    <div class="modal-body">
      {!! Form::hidden("close_date", $close_date) !!}
      {!! Form::hidden("cashier_id", $cashier_id) !!}
      {!! Form::hidden("location_id", $location_id) !!}
      <div class="row">
        <div class="col-sm-12">
          <table class="table">
            {{-- Cash in hand --}}
            <tr>
              <td style="border-top-color: #fff"></td>
              <td style="border-top-color: #fff"></td>
              <td style="border-top-color: #fff">@lang('cash_register.cash_in_hand')</td>
              <td style="border-top-color: #fff">
                <span class="display_currency" data-currency_symbol="true">{{ $initial }}</span>
              </td>
            </tr>

            {{-- Table --}}
            <tr class="success">
              <th colspan="2" class="text-center">@lang('cash_register.income_sales_day')</th>
              <th colspan="2" class="text-center">@lang('cash_register.cancel_pay_reserv')</th>
            </tr>

            {{-- Cash (Row 1) --}}
            <tr>
              <td>
                @lang('cash_register.cash_payment')
              </td>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_cash }}</span>
              </td>
              
              <td>
                @lang('cash_register.cash_payment')
              </td>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $payment_details->total_cash + $reservations->total_cash + $reservation_pays->total_cash }}</span>
              </td>
            </tr>

            {{-- Check (Row 2) --}}
            <tr>
              <td>
                @lang('cash_register.check_payment')
              </td>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_check }}</span>
              </td>

              <td>
                @lang('cash_register.check_payment')
              </td>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $payment_details->total_check + $reservations->total_check + $reservation_pays->total_check }}</span>
              </td>
            </tr>

            {{-- Card (Row 3) --}}
            <tr>
              <td>
                @lang('cash_register.card_payment')
              </td>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_card }}</span>
              </td>

              <td>
                @lang('cash_register.card_payment')
              </td>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $payment_details->total_card + $reservations->total_card + $reservation_pays->total_card }}</span>
              </td>
            </tr>

            {{-- Bank transfer (Row 4) --}}
            <tr>
              <td>
                @lang('cash_register.bank_transfer')
              </td>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_bank_transfer }}</span>
              </td>

              <td>
                @lang('cash_register.bank_transfer')
              </td>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $payment_details->total_bank_transfer + $reservations->total_bank_transfer + $reservation_pays->total_bank_transfer }}</span>
              </td>
            </tr>

            {{-- Credit (Row 5) --}}
            <tr>
              <td>
                @lang('cash_register.credits')
              </td>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $total_credit }}</span>
              </td>

              <td>
                @lang('cash_register.credits')
              </td>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $reservations->total_credit + $reservations->total_reservation - $reservations->total_sale }}</span>
              </td>
            </tr>

            {{-- Refund (Row 6) --}}
            <tr>
              {{-- Sales --}}
              <td>
                @lang('cash_register.total_refund')
              </td>

              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_refund }}</span><br>

                <small>
                  @if ($register_details->total_cash_refund != 0)
                    @lang('cash_register.cash'):
                    <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_cash_refund }}</span><br>
                  @endif

                  @if ($register_details->total_credit_refund != 0)
                    @lang('cash_register.credit'):
                    <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_credit_refund }}</span><br>
                  @endif

                  @if ($register_details->total_check_refund != 0) 
                    @lang('cash_register.check'):
                    <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_check_refund }}</span><br>
                  @endif

                  @if($register_details->total_card_refund != 0) 
                    @lang('cash_register.card'):
                    <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_card_refund }}</span><br> 
                  @endif

                  @if ($register_details->total_bank_transfer_refund != 0)
                    @lang('cash_register.bank_transfer'):
                    <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_bank_transfer_refund }}</span><br>
                  @endif
                </small>
              </td>

              {{-- Reservations --}}
              <td>
                @lang('cash_register.total_refund')
              </td>

              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $reservations->total_refund }}</span><br>

                <small>
                  @if ($reservations->total_cash_refund != 0)
                    @lang('cash_register.cash'):
                    <span class="display_currency" data-currency_symbol="true">{{ $reservations->total_cash_refund }}</span><br>
                  @endif

                  @if ($reservations->total_credit_refund != 0)
                    @lang('cash_register.credit'):
                    <span class="display_currency" data-currency_symbol="true">{{ $reservations->total_credit_refund }}</span><br>
                  @endif

                  @if ($reservations->total_check_refund != 0) 
                    @lang('cash_register.check'):
                    <span class="display_currency" data-currency_symbol="true">{{ $reservations->total_check_refund }}</span><br>
                  @endif

                  @if ($reservations->total_card_refund != 0) 
                    @lang('cash_register.card'):
                    <span class="display_currency" data-currency_symbol="true">{{ $reservations->total_card_refund }}</span><br> 
                  @endif

                  @if ($reservations->total_bank_transfer_refund != 0)
                    @lang('cash_register.bank_transfer'):
                    <span class="display_currency" data-currency_symbol="true">{{ $reservations->total_bank_transfer_refund }}</span><br>
                  @endif
                </small>
              </td>
            </tr>

            {{-- Others (Row 7) --}}
            <tr>
              {{-- Reservation to sale --}}
              <td>
                @lang('cash_register.reservation_to_sale')
              </td>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $reservation_to_sale->total_sale != 0 ? $sum_sales - $total_sell : 0 }}</span><br>

                <small>
                  @if ($reservation_to_sale->total_cash != 0)
                    @lang('cash_register.cash'):
                    <span class="display_currency" data-currency_symbol="true">{{ $reservation_to_sale->total_cash }}</span><br>
                  @endif

                  @if ($reservation_to_sale->total_credit != 0)
                    @lang('cash_register.credit'):
                    <span class="display_currency" data-currency_symbol="true">{{ $reservation_to_sale->total_credit }}</span><br>
                  @endif

                  @if ($reservation_to_sale->total_check != 0) 
                    @lang('cash_register.check'):
                    <span class="display_currency" data-currency_symbol="true">{{ $reservation_to_sale->total_check }}</span><br>
                  @endif

                  @if ($reservation_to_sale->total_card != 0) 
                    @lang('cash_register.card'):
                    <span class="display_currency" data-currency_symbol="true">{{ $reservation_to_sale->total_card }}</span><br> 
                  @endif

                  @if ($reservation_to_sale->total_bank_transfer != 0)
                    @lang('cash_register.bank_transfer'):
                    <span class="display_currency" data-currency_symbol="true">{{ $reservation_to_sale->total_bank_transfer }}</span><br>
                  @endif

                  @if ($reservation_to_sale->total_sale != 0)
                    @lang('accounting.total'):
                    <span class="display_currency" data-currency_symbol="true">{{ $reservation_to_sale->total_sale }}</span><br>
                  @endif
                </small>
              </td>

              {{-- Inflows and Outflows --}}
              <td>
                @lang('cash_register.cash_in') <br>
                @lang('cash_register.cash_out')
              </td>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $inflow_outflow->inflow }}</span> <br>
                <span class="display_currency" data-currency_symbol="true">{{ $inflow_outflow->outflow }}</span>
              </td>
            </tr>

            {{-- Totals --}}
            <tr class="success">
              <th>
                @lang('cash_register.total'):
              </th>
              <th>
                <span class="display_currency" data-currency_symbol="true">{{ $total_sell + ($sum_sales - $total_sell) }}</span>
              </th>

              <th>
                @lang('cash_register.total'):
              </th>
              <th>
                <span class="display_currency" data-currency_symbol="true">{{ $payment_details->total_sale + $reservations->total_reservation + $reservation_pays->total_sale + $inflow_outflow->inflow - $inflow_outflow->outflow }}</span>
              </th>
            </tr>
          </table>
        </div>
      </div>

      {{-- @include('cash_register.register_product_details') --}}

      {{-- Totals --}}
      <div class="row">
        {{-- Total cash --}}
        <div class="col-sm-3">
          <div class="form-group">
              {!! Form::label('total_amount_cash', __( 'cash_register.total_cash' ) . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa">$</i>
                </span>
                {!! Form::text('total_amount_cash',
                  @num_format(
                    $initial +
                    $register_details->total_cash +
                    $payment_details->total_cash +
                    $reservations->total_cash +
                    $reservation_pays->total_cash +
                    $inflow_outflow->inflow -
                    $register_details->total_cash_refund -
                    $payment_details->total_cash_refund -
                    $reservations->total_cash_refund -
                    $inflow_outflow->outflow
                  ),
                  ['class' => 'form-control input_number', 'required', 'placeholder' => __( 'cash_register.total_cash' ) ]); !!}
              </div>
          </div>
        </div>

        {{-- Total card --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('total_amount_card', __( 'cash_register.total_card' ) . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa">$</i>
              </span>
              {!! Form::text('total_amount_card',
                @num_format(
                  $register_details->total_card +
                  $payment_details->total_card +
                  $reservations->total_card +
                  $reservation_pays->total_card -
                  $register_details->total_card_refund -
                  $payment_details->total_card_refund -
                  $reservations->total_card_refund
                ),
                ['class' => 'form-control input_number', 'required', 'placeholder' => __( 'cash_register.total_card' ) ]); !!}
            </div>
          </div>
        </div>

        {{-- Total check --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('total_check', __( 'cash_register.total_check' ) . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa">$</i>
              </span>
              {!! Form::text('total_amount_check',
                @num_format(
                  $register_details->total_check +
                  $payment_details->total_check +
                  $reservations->total_check +
                  $reservation_pays->total_check -
                  $register_details->total_check_refund -
                  $payment_details->total_check_refund -
                  $reservations->total_check_refund
                ),
                ['class' => 'form-control input_number', 'required', 'placeholder' => __( 'cash_register.total_check' ) ]); !!}
            </div>
          </div>
        </div>

        {{-- Total transfer --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('total_tranfer', __( 'cash_register.total_transfer' ) . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa">$</i>
              </span>
              {!! Form::text('total_amount_transfer',
                @num_format(
                  $register_details->total_bank_transfer +
                  $payment_details->total_bank_transfer +
                  $reservations->total_bank_transfer +
                  $reservation_pays->total_bank_transfer -
                  $register_details->total_bank_transfer_refund -
                  $payment_details->total_bank_transfer_refund -
                  $reservations->total_bank_transfer_refund
                ),
                ['class' => 'form-control input_number', 'required', 'placeholder' => __( 'cash_register.total_transfer' ) ]); !!}
            </div>  
          </div>
        </div>

        {{-- Total credit --}}
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('total_credit', __( 'cash_register.total_credit' ) . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa">$</i>
              </span>
              {!! Form::text('total_amount_credit',
                @num_format(
                  $total_credit +
                  ($reservations->total_credit + $reservations->total_reservation - $reservations->total_sale) -
                  $register_details->total_credit_refund -
                  $reservations->total_credit_refund
                ),
                ['class' => 'form-control input_number', 'required', 'placeholder' => __( 'cash_register.total_credit' ) ]); !!}
            </div>
          </div>
        </div>

        {{-- Closing note --}}
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

      {{-- Cash detail button --}}
      <button type="button" class="btn btn-success" @if($is_closed > 0) disabled="disabled" @endif id="btn-cash-detail">
        @lang('cash_register.cash_detail')
      </button>

      <button type="submit" class="btn btn-primary" @if($is_closed > 0) disabled="disabled" @endif>@lang( 'cash_register.close_register' )</button>
    </div>
    
    {{-- Cash detail modal --}}
    <div class="modal fade cash_detail" tabindex="-1" role="dialog" 
      data-backdrop="static" aria-labelledby="gridSystemModalLabel">
      <div class="modal-dialog" role="document" style="width: 80%">
        <div class="modal-content">

        <div class="modal-header">
            {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> --}}
            <h4 class="modal-title">@lang( 'cash_register.add_cash_detail' )</h4>
        </div>
    
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label("one_cent", __("cash_register.one_cent")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::text("one_cent", null, ["class" => "form-control", "id" => "one_cent",
                                "placeholder" => "$0.01"]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label("five_cents", __("cash_register.five_cents")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::text("five_cents", null, ["class" => "form-control", "id" => "five_cents",
                                "placeholder" => "$0.05"]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label("ten_cents", __("cash_register.ten_cents")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::text("ten_cents", null, ["class" => "form-control", "id" => "ten_cents",
                                "placeholder" => "$0.1"]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label("twenty_five_cents", __("cash_register.twenty_five_cents")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::text("twenty_five_cents", null, ["class" => "form-control", "id" => "twenty_five_cents",
                                "placeholder" => "$0.25"]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label("one_dollar", __("cash_register.one_dollar")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::text("one_dollar", null, ["class" => "form-control", "id" => "one_dollar",
                                "placeholder" => "$1"]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label("five_dollars", __("cash_register.five_dollars")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::text("five_dollars", null, ["class" => "form-control", "id" => "five_dollars",
                                "placeholder" => "$5"]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label("ten_dollars", __("cash_register.ten_dollars")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::text("ten_dollars", null, ["class" => "form-control", "id" => "ten_dollars",
                                "placeholder" => "$10"]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label("twenty_dollars", __("cash_register.twenty_dollars")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::text("twenty_dollars", null, ["class" => "form-control", "id" => "twenty_dollars",
                                "placeholder" => "$20"]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label("fifty_dollars", __("cash_register.fifty_dollars")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::text("fifty_dollars", null, ["class" => "form-control", "id" => "fifty_dollars",
                                "placeholder" => "$50"]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label("one_hundred_dollars", __("cash_register.one_hundred_dollars")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::text("one_hundred_dollars", null, ["class" => "form-control", "id" => "one_hundred_dollars",
                                "placeholder" => "$50"]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <div class="modal-footer">
            <button type="button" class="btn btn-default" id="close_cash_detail_modal">@lang( 'messages.close' )</button>
        </div>
    
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>
    {!! Form::close() !!}
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
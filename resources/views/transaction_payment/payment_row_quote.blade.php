<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('TransactionPaymentController@storeToQuote'), 'method' => 'post',
        'id' => 'transaction_payment_add_form', 'files' => true ]) !!}
      
      {{-- quote_id --}}
      {!! Form::hidden('quote_id', $reservation->id); !!}
  
      {{-- Header --}}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">@lang('purchase.add_payment')</h4>
      </div>
  
      <div class="modal-body">
        <div class="row">
          {{-- Reservation info --}}
          <div class="col-md-6">
            <div class="well">
              <strong>@lang('purchase.ref_no'): </strong>{{ $reservation->ref_no }}
              <br>
              <strong>@lang('purchase.location'): </strong>{{ ! empty($reservation->location) ? $reservation->location->name : '' }}
            </div>
          </div>

          {{-- Payment info --}}
          <div class="col-md-6">
            <div class="well">
              <strong>@lang('sale.total_amount'): </strong>
              <span class="display_currency" data-currency_symbol="true">{{ $reservation->total_final }}</span>
              <br>
              <strong>@lang('purchase.payment_note'): </strong>
              @if(!empty($reservation->additional_notes))
              {{ $reservation->additional_notes }}
              @else
                --
              @endif
            </div>
          </div>
        </div>

        <div class="row payment_row">
          {{-- amount --}}
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label("amount", __('accounting.amount') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-money"></i>
                </span>
                {!! Form::text("amount", @num_format($payment_line->amount), [
                    'class' => 'form-control input_number',
                    'required',
                    'placeholder' => 'Amount',
                    'data-rule-max-value' => $payment_line->amount,
                    'data-msg-max-value' => __('lang_v1.max_amount_to_be_paid_is', ['amount' => $amount_formated])
                ]); !!}
              </div>
            </div>
          </div>

          {{-- paid_on --}}
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label("paid_on", __('lang_v1.paid_on') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </span>
                {!! Form::text('paid_on', date('m/d/Y', strtotime($payment_line->paid_on) ),
                    ['class' => 'form-control', 'readonly', 'required']); !!}
              </div>
            </div>
          </div>

          {{-- pay_method --}}
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label("method", __('lang_v1.pay_method') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-money"></i>
                </span>
                {!! Form::select("method", $payment_types, $payment_line->method,
                    ['class' => 'form-control select2 payment_types_dropdown', 'required', 'style' => 'width:100%;']); !!}
              </div>
            </div>
          </div>

          {{-- accounts --}}
          @if(!empty($accounts))
            <div class="col-md-6">
              <div class="form-group">
                {!! Form::label("account_id" , __('lang_v1.payment_account') . ':') !!}
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-money"></i>
                  </span>
                  {!! Form::select("account_id", $accounts, !empty($payment_line->account_id) ? $payment_line->account_id : '' ,
                    ['class' => 'form-control select2', 'id' => "account_id", 'style' => 'width:100%;']); !!}
                </div>
              </div>
            </div>
          @endif

          {{-- document --}}
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('document', __('purchase.attach_document') . ':') !!}
              {!! Form::file('document'); !!}
            </div>
          </div>

          <div class="clearfix"></div>

          {{-- payment_type_details --}}
          @include('transaction_payment.payment_type_details')

          {{-- cashier_id --}}
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label("cashier_id", __('cashier.cashier') . ':*') !!}

              @if (is_null($default_cashier))
              {{-- Some or all cashiers --}}
              {!! Form::select("cashier_id", $cashiers, $payment_line->cashier_id,
                ['class' => 'form-control select2', 'required', 'style' => 'width:100%;']); !!}

              @else
              {{-- Only one cashier --}}
              {!! Form::select("cashier_id", $cashiers, $default_cashier,
                ['class' => 'form-control select2', 'required', 'style' => 'width:100%;', 'disabled']); !!}
              {!! Form::hidden('cashier_id', $default_cashier, ['id' => 'cashier_id']) !!}
              @endif
            </div>
          </div>

          {{-- note --}}
          <div class="col-md-6">
            <div class="form-group">
              <div class="input-group">
                {!! Form::label("note", __('lang_v1.payment_note') . ':*') !!}

                @if (auth()->user()->can('transaction_payment.edit_payment_note'))
                  {!! Form::text("note", empty($payment_line->note) ? $correlative : $payment_line->note, [
                    'class' => 'form-control input_number validate-payment-note',
                    'required',
                    'placeholder' => __('sale.payment_note')
                  ]) !!}
                @else
                  {!! Form::text("note", empty($payment_line->note) ? $correlative : $payment_line->note, [
                    'class' => 'form-control input_number validate-payment-note',
                    'required',
                    'readonly',
                    'placeholder' => __('lab_order.payment_note')
                  ]) !!}
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- buttons --}}
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="btn-payment-form">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
      
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  
  <script>
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
  
    $('.payment_modal').on('shown.bs.modal', function () {
        $(this).find('.select2').select2();
    });
  
    $('.edit_payment_modal').on('shown.bs.modal', function () {
        $(this).find('.select2').select2();
    });
  </script>
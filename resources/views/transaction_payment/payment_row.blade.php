<div class="modal-dialog" role="document">
  <div class="modal-content">
    {!! Form::open([
      'url' => action('TransactionPaymentController@store'),
      'method' => 'post',
      'id' => 'transaction_payment_add_form',
      'files' => true
    ]) !!}

    {{-- transaction_id --}}
    {!! Form::hidden('transaction_id', $transaction->id) !!}

    @if (config('app.business') == 'optics')
      <input type="hidden" id="payment_note_id" value="{{ $payment_note_id }}">
    @endif

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>

      <h4 class="modal-title">
        @lang('purchase.add_payment')
      </h4>
    </div>

    <div class="modal-body">
      <div class="row">
      @if (!empty($transaction->contact))
        <div class="col-md-4">
          <div class="well">
            <strong>
              @if (in_array($transaction->type, ['purchase', 'purchase_return']))
                @lang('purchase.supplier'):
              @elseif(in_array($transaction->type, ['sell', 'sell_return']))
                @lang('contact.customer'):
              @endif
            </strong>
            {{ $transaction->contact->name }}
            <br>

            @if ($transaction->type == 'purchase')
              <strong>@lang('business.business'): </strong>{{ $transaction->contact->supplier_business_name }}
            @endif
          </div>
        </div>
        @endif

        <div class="col-md-4">
          <div class="well">
            @if (in_array($transaction->type, ['sell', 'sell_return']))
              <strong>@lang('sale.invoice_no'): </strong>{{ $transaction->correlative }}
            @else
              <strong>@lang('purchase.ref_no'): </strong>{{ $transaction->ref_no }}
            @endif
            <br>

            <strong>@lang('purchase.location'): </strong>{{ $transaction->location->name }}
          </div>
        </div>

        <div class="col-md-4">
          <div class="well">
            <strong>@lang('sale.total_amount'): </strong>
            <span class="display_currency" data-currency_symbol="true">
              {{ $transaction->final_total }}
            </span>
            <br>

            <strong>@lang('purchase.payment_note'): </strong>
            @if(!empty($transaction->additional_notes))
              {{ $transaction->additional_notes }}
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
            {!! Form::label("amount", __('payment.amount') . ': *') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-money"></i>
              </span>
              {!! Form::text("amount", @num_format($payment_line->amount), [
                'class' => 'form-control input_number',
                'required',
                'placeholder' => __('payment.amount'),
                'data-rule-max-value' => $amount_formated,
                'data-msg-max-value' => __('lang_v1.max_amount_to_be_paid_is', ['amount' => $amount_formated])
              ]) !!}
            </div>
          </div>
        </div>

        {{-- paid_on --}}
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("paid_on", __('payment.paid_on') . ': *') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!! Form::text('paid_on', date('m/d/Y', strtotime($payment_line->paid_on)), [
                'class' => 'form-control',
                'readonly',
                'required'
              ]) !!}
            </div>
          </div>
        </div>

        {{-- method --}}
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("method", __('payment.payment_method') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-money"></i>
              </span>
              {!! Form::select("method", $payment_types, $payment_line->method, [
                'class' => 'form-control select2 payment_types_dropdown',
                'required',
                'style' => 'width: 100%;'
              ]) !!}
            </div>
          </div>
        </div>

        {{-- account_id --}}
        @if (!empty($accounts))
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label("account_id", __('lang_v1.payment_account') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-money"></i>
                </span>
                {!! Form::select("account_id", $accounts, !empty($payment_line->account_id) ? $payment_line->account_id : '' , [
                  'class' => 'form-control select2', 
                  'id' => 'account_id',
                  'style' => 'width: 100%;'
                ]) !!}
              </div>
            </div>
          </div>
        @endif

        {{-- document --}}
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('document', __('purchase.attach_document') . ':') !!}
            {!! Form::file('document') !!}
          </div>
        </div>

        <div class="clearfix"></div>

        @include('transaction_payment.payment_type_details')

        @if (config('app.business') == 'optics')
          @if ($transaction->type != 'expense')
            {{-- location_id --}}
            <div class="col-md-6">
              <div class="form-group">
                {!! Form::label("location_id", __('accounting.location')) !!}

                @if (is_null($default_location))
                  {!! Form::select("location_id", $locations, $transaction->location_id, [
                    'class' => 'form-control select2',
                    'required',
                    'style' => 'width: 100%;'
                  ]) !!}
                @else
                  {!! Form::select("select_location_id", $locations, $default_location, [
                    'class' => 'form-control select2',
                    'style' => 'width: 100%;',
                    'disabled'
                  ]) !!}

                  {{-- {!! Form::hidden('location_id', $default_location, ['id' => 'location_id']) !!} --}}
                @endif
              </div>
            </div>

            {{-- note --}}
            <div class="col-md-6">
              <div class="form-group">
                <div class="input-group">
                  {!! Form::label("note", __('lab_order.payment_note') . ': *') !!}

                  @if (auth()->user()->can('transaction_payment.edit_payment_note'))
                    {!! Form::text("note", empty($payment_line->note) ? $correlative : $payment_line->note, [
                      'class' => 'form-control input_number validate-payment-note',
                      'required',
                      'placeholder' => __('lab_order.payment_note')
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

            {{-- cashier_id --}}
            <div class="col-md-6">
              <div class="form-group">
                {!! Form::label("cashier_id", __('cashier.cashier') . ': *') !!}

                @if (is_null($default_cashier))
                  {!! Form::select("cashier_id", $cashiers, $payment_line->cashier_id, [
                    'class' => 'form-control select2',
                    'required',
                    'style' => 'width: 100%;'
                  ]) !!}
                @else
                  {!! Form::select("select_cashier_id", $cashiers, $default_cashier, [
                    'class' => 'form-control select2',
                    'required',
                    'style' => 'width: 100%;',
                    'disabled'
                  ]) !!}

                  {!! Form::hidden('cashier_id', $default_cashier, ['id' => 'cashier_id']) !!}
                @endif
              </div>
            </div>
          @endif
        @else
          <div class="col-md-12">
            <div class="form-group">
              {!! Form::label("note", __('sale.payment_note') . ':') !!}
              {!! Form::textarea("note", $payment_line->note, ['class' => 'form-control', 'rows' => 3]) !!}
            </div>
          </div>
        @endif
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}
  </div>
</div>

<script>
  $.fn.modal.Constructor.prototype.enforceFocus = function() {};

  $('.payment_modal').on('shown.bs.modal', function () {
      $(this).find('.select2').select2();
  });

  $('.edit_payment_modal').on('shown.bs.modal', function () {
      $(this).find('.select2').select2();
  });
</script>
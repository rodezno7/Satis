<div class="modal-dialog" role="document">
  <div class="modal-content">
    {!! Form::open([
      'url' => action('TransactionPaymentController@update', [$payment_line->id]),
      'method' => 'put',
      'id' => 'transaction_payment_add_form',
      'files' => true
    ]) !!}

    {!! Form::hidden('entity_type', $entity_type) !!}

    @if (config('app.business') == 'optics')
      <input type="hidden" id="payment_note_id" value="{{ $payment_note_id }}">
    @endif

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      
      <h4 class="modal-title">
        @lang( 'purchase.edit_payment' )
      </h4>
    </div>

    <div class="modal-body">
      <div class="row">
        @if (!empty($transaction->contact))
          <div class="col-md-4">
            <div class="well">
              <strong>@lang('purchase.supplier'): </strong>{{ $transaction->contact->name }}<br>
              <strong>@lang('business.business'): </strong>{{ $transaction->contact->supplier_business_name }}
            </div>
          </div>
        @endif

        <div class="col-md-4">
          <div class="well">
            <strong>@lang('purchase.ref_no'): </strong>
            {{ isset($transaction->ref_no) ? $transaction->ref_no : $transaction->quote_ref_no }}
            <br>
            <strong>@lang('purchase.location'): </strong>
            {{ ! empty($transaction->location) ? $transaction->location->name : '' }}
          </div>
        </div>

        <div class="col-md-4">
          <div class="well">
            <strong>@lang('purchase.purchase_total'): </strong>
            <span class="display_currency" data-currency_symbol="true">
              {{ $transaction->final_total }}
            </span>
            <br>

            <strong>@lang('purchase.payment_note'): </strong>
            @if (!empty($transaction->additional_notes))
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
                'placeholder' => __('payment.amount')
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
                'readonly', 'required'
              ]) !!}
            </div>
          </div>
        </div>

        {{-- method --}}
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("method",  __('payment.payment_method') . ': *') !!}
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

        {{-- document --}}
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('document', __('purchase.attach_document') . ':') !!}
            {!! Form::file('document') !!}
            <p class="help-block">
              @lang('lang_v1.previous_file_will_be_replaced')
            </p>
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
                  'id' => "account_id",
                  'style' => 'width: 100%;'
                ]) !!}
              </div>
            </div>
          </div>
        @endif
        
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
            <div class="col-md-4">
              <div class="form-group">
                <div class="input-group">
                  {!! Form::label("note", __('lab_order.payment_note') . ': *') !!}

                  @if (auth()->user()->can('transaction_payment.edit_payment_note'))
                    {!! Form::text("note", $payment_line->note, [
                      'class' => 'form-control input_number validate-payment-note',
                      'required',
                      'placeholder' => __('lab_order.payment_note')
                    ]) !!}
                  @else
                    {!! Form::text("note", $payment_line->note, [
                      'class' => 'form-control input_number validate-payment-note',
                      'required',
                      'readonly',
                      'placeholder' => __('lab_order.payment_note')
                    ]) !!}
                  @endif

                  {!! Form::hidden('payment-note-value', $payment_line->note, ['id' => 'payment-note-value']) !!}
                </div>
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
      <button type="submit" class="btn btn-primary">
        @lang('messages.update')
      </button>

      <button type="button" class="btn btn-default" data-dismiss="modal">
        @lang('messages.close')
      </button>
    </div>

    {!! Form::close() !!}
  </div>
</div>
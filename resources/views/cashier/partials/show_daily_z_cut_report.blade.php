<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">@lang( 'cash_register.daily_z_cut' ) {{ "#" . $closure_details->close_correlative }} - ( {{ \Carbon::createFromFormat('Y-m-d H:i:s', $closure_details->open_date)->format('d/m/Y H:i:s') }} - {{ \Carbon::createFromFormat('Y-m-d H:i:s', $closure_details->close_date)->format('d/m/Y H:i:s') }} )</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
            <table class="table" id="closure_details">
              <tr>
                <td>@lang('cash_register.initial_cash')</td>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ @num_format($closure_details->initial_cash_amount) }}</span>
                </td>
                <td>@lang('cash_register.cash_payment'): </th>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ @num_format($closure_details->total_cash_amount) }}</span>
                </td>
              </tr>
              <tr>
                <td>@lang('cash_register.check_payment'): </td>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ @num_format($closure_details->total_check_amount) }}</span>
                </td>
                <td>@lang('cash_register.card_payment'): </td>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ @num_format($closure_details->total_card_amount) }}</span>
                </td>
              </tr>
              <tr>
                <td>@lang('cash_register.bank_transfers'): </td>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ @num_format($closure_details->total_bank_transfer_amount) }}</span>
                </td>
                <td>@lang('cash_register.credits')</td>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ @num_format($closure_details->total_credit_amount) }}</span>
                </td>
              </tr>
              <tr>
                <td>@lang('cash_register.returns')</td>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ @num_format($closure_details->total_return_amount) }}</span>
                </td>
                <td>@lang('cash_register.differences')</td>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ @num_format($closure_details->differences) }}</span>
                </td>
              </tr>
              <tr class="success">
                <th>@lang('cash_register.total_system'): </th>
                <th>
                  <span class="display_currency" data-currency_symbol="true">{{ @num_format($closure_details->total_system_amount) }}</span>
                </th>
                <th>@lang('cash_register.total_physical'): </th>
                <th>
                  <span class="display_currency" data-currency_symbol="true">{{ @num_format($closure_details->total_physical_amount) }}</span>
                </th>
              </tr>
            </table>
            <b>@lang('cash_register.closing_note'):</b> {{ $closure_details->closing_note }}
          </div>
        </div>
      </div>
      <div class="modal-footer">
        @if (auth()->user()->can('cash_register_report.view'))
          <a class="btn btn-primary no-print" aria-label="Print" target="_blank"
          href="/reports/new_cash_register_report?cashier_closure_id={{ $closure_details->id }}">
          <i class="fa fa-file-pdf-o"></i> @lang('report.cash_register_report')</a>
        @endif
        @if (auth()->user()->can('audit_tape.view'))
          <a class="btn btn-primary no-print" aria-label="Print" target="_blank"
            href="/reports/audit-tape-report/{{ $closure_details->id }}">
            <i class="fa fa-eye"></i> @lang('report.audit_tape')</a>            
        @endif
        @if ($closure_details->open_correlative)
          <a class="btn btn-primary no-print" aria-label="Print" target="_blank" 
          href="/cashier-closure/get-opening-cash-register/{{ $closure_details->id }}" }}>
            <i class="fa fa-print"></i> @lang( 'cash_register.opening_cash_register' )
        @endif
        <a class="btn btn-primary no-print" aria-label="Print" target="_blank" 
          href="/cashier-closure/get-daily-z-cut-report/{{ $closure_details->location_id }}/{{ $closure_details->cashier_id }}/{{ $closure_details->id }}" }}>
            <i class="fa fa-print"></i> @lang( 'messages.print' )
        </a>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">

    <div class="modal-header">
      <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h3 class="modal-title">@lang( 'cash_register.current_register' ) ( {{ @format_date($close_date) }} )</h3>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-sm-12">
          <table class="table">
            <tr>
              <td>
                @lang('cash_register.cash_payment'):
              </th>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_cash }}</span>
              </td>
              <td>
                @lang('cash_register.check_payment'):
              </td>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_check }}</span>
              </td>
            </tr>
            <tr>
              <td>
                @lang('cash_register.card_payment'):
              </td>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_card }}</span>
              </td>
              <td>
                @lang('cash_register.bank_transfer'):
              </td>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_bank_transfer }}</span>
              </td>
            </tr>
            <tr>
              <td>
                @lang('cash_register.credits'):
              </td>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_credit }}</span>
              </td>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr class="success">
              <th>
                @lang('cash_register.total_refund')
              </th>
              <td>
                <b><span class="display_currency" data-currency_symbol="true">{{ $register_details->total_refund }}</span></b><br>
                <small>
                @if($register_details->total_cash_refund != 0)
                  @lang('cash_register.cash')
                  <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_cash_refund }}</span><br>
                @endif
                @if($register_details->total_credit_refund != 0)
                  @lang('cash_register.credit')
                  <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_credit_refund }}</span><br>
                @endif
                @if($register_details->total_check_refund != 0) 
                  @lang('cash_register.check')
                  <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_check_refund }}</span><br>
                @endif
                @if($register_details->total_card_refund != 0) 
                  @lang('cash_register.card')
                  <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_card_refund }}</span><br> 
                @endif
                @if($register_details->total_bank_transfer_refund != 0)
                  @lang('cash_register.bank_transfer')
                  <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_bank_transfer_refund }}</span><br>
                @endif
                </small>
              </td>
              {{--<th>
                @lang('cash_register.total_cash')
              </th>
              <td>
                <b><span class="display_currency" data-currency_symbol="true">{{ $register_details->cash_in_hand + $register_details->total_cash - $register_details->total_cash_refund }}</span></b>
              </td>--}}
              <th>
                @lang('cash_register.total_sales'):
              </th>
              <th>
                <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_sale }}</span>
              </th>
            </tr>
          </table>
        </div>
        <div class="col-sm-6">
          {{ $closing_note }}
        </div>
      </div>

      {{--@include('cash_register.register_product_details')--}}

    <div class="modal-footer">
      <a type="button" class="btn btn-primary no-print" 
        aria-label="Print" target="__blank" 
          href="/reports/cash_register_report?cashier_id={{ $cashier_id }}&trans_date={{ @format_date($close_date) }}">
        <i class="fa fa-print"></i> @lang( 'messages.print' )
    </a>

      <button type="button" class="btn btn-default no-print" 
        data-dismiss="modal">@lang( 'messages.cancel' )
      </button>
    </div>

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->